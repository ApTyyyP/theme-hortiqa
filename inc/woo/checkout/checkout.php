<?php

add_filter('woocommerce_checkout_fields', 'custom_remove_checkout_fields');

function custom_remove_checkout_fields($fields)
{
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_city']);

    $fields['billing']['billing_phone']['required'] = true;
    $fields['billing']['billing_phone']['label'] = 'Телефон';

    return $fields;
}

remove_action(
    'woocommerce_checkout_billing',
    array(WC()->checkout(), 'checkout_form_billing')
);

add_action('woocommerce_checkout_billing', function () {
    $checkout = WC()->checkout();
    $fields = $checkout->get_checkout_fields();

    echo '<div class="checkout__wrapper">';
    echo '<h2 class="checkout__title title">Оформлення замовлення</h2>';
    echo '<ul class="checkout__items ">';

    foreach ($fields['billing'] as $key => $field) {
        echo '<li class="checkout__item">';

        $field['input_class'][] = 'checkout__input';
        $field['class'][] = 'checkout__box';

        woocommerce_form_field(
            $key,
            $field,
            $checkout->get_value($key)
        );

        echo '</li>';
    }

    if (!empty($fields['account'])) {
        echo '<h3 class="title">Доставка</h3>';

        foreach ($fields['account'] as $key => $field) {
            echo '<li class="checkout__item">';

            $field['input_class'][] = 'checkout__input';
            $field['class'][] = 'checkout__box';

            woocommerce_form_field(
                $key,
                $field,
                $checkout->get_value($key)
            );

            echo '</li>';
        }
    }

    echo '</ul>';
    echo '<h3 class="checkout__comment">Доставка нова пошта</h3>';
    echo '</div>';
}, 10);

add_action('woocommerce_after_order_notes', function () {
    echo '<div class="after-np-comment">';

    $checkout = WC()->checkout();
    $field = $checkout->get_checkout_fields()['order']['order_comments'];

    $field['input_class'][] = 'checkout__input';
    $field['class'][] = 'checkout__box';

    woocommerce_form_field(
        'order_comments',
        $field,
        $checkout->get_value('order_comments')
    );

    echo '</div>';
}, 20);

add_filter('woocommerce_cart_needs_shipping_address', '__return_false');

remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);

add_action('woocommerce_checkout_order_review', function () {
    echo '<h3 class="checkout__comment">Оберіть зручний для вас спосіб оплати</h3>';
    woocommerce_checkout_payment();
}, 20);

add_filter('woocommerce_checkout_fields', function ($fields) {
    if (!empty($fields['shipping'])) {
        foreach ($fields['shipping'] as $key => $field) {
            $fields['shipping'][$key]['required'] = false;
        }
    }

    return $fields;
}, 9999);

/**
 * Keep WC Ukr Shipping shipping context mount point in custom checkout markup.
 * Plugin mounts both #wcus-billing-fields and #wcus-shipping-fields via JS.
 */
add_action('woocommerce_checkout_shipping', function () {
    echo '<div id="wcus-shipping-fields"></div>';
}, 30);

/**
 * Resolve fallback city ref for WC Ukr Shipping Nova Poshta requests.
 *
 * @return string
 */
function hortiqa_wcus_city_ref_fallback()
{
    $request_keys = array(
        'wcus_np_billing_city',
        'wcus_np_shipping_city',
        'wcus_city_ref',
        'city_ref',
    );

    foreach ($request_keys as $key) {
        if (empty($_REQUEST[$key])) {
            continue;
        }

        $value = wp_unslash($_REQUEST[$key]);
        if (is_string($value) && $value !== '') {
            return sanitize_text_field($value);
        }
    }

    if (function_exists('WC') && WC()->session) {
        $session_keys = array('wcus_last_city_ref', 'wcus_city_ref');
        foreach ($session_keys as $session_key) {
            $session_value = WC()->session->get($session_key);
            if (is_string($session_value) && $session_value !== '') {
                return sanitize_text_field($session_value);
            }
        }
    }

    return '8d5a980d-391c-11dd-90d9-001a92567626';
}

/**
 * WC Ukr Shipping checks city_ref before its own filters.
 * Fill missing city_ref early for warehouses search request.
 */
add_action('init', function () {
    if (!wp_doing_ajax()) {
        return;
    }

    $action = isset($_REQUEST['action']) ? sanitize_key(wp_unslash($_REQUEST['action'])) : '';
    if ($action !== 'wcus_search_warehouses') {
        return;
    }

    if (!empty($_REQUEST['city_ref'])) {
        return;
    }

    $fallback = hortiqa_wcus_city_ref_fallback();
    if ($fallback === '') {
        return;
    }

    $_REQUEST['city_ref'] = $fallback;
    $_POST['city_ref'] = $fallback;
}, 1);

/**
 * Fallback city ref for Nova Poshta pickup points search.
 * Prevents empty "Nothing found" when checkout JS loses city_ref state.
 */
add_filter('wcus_pudo_points_request', function ($request, $carrier) {
    if ($carrier !== 'nova_poshta' || !is_array($request)) {
        return $request;
    }

    if (!empty($request['cityId'])) {
        return $request;
    }

    $request['cityId'] = hortiqa_wcus_city_ref_fallback();

    return $request;
}, 10, 2);

/**
 * Frontend guard: ensure checkout state has city ref before WCUS app bootstrap.
 */
add_action('wp_enqueue_scripts', function () {
    if (!function_exists('is_checkout') || !is_checkout()) {
        return;
    }

    if (
        !wp_script_is('wcus_checkout_js', 'registered')
        && !wp_script_is('wcus_checkout_js', 'enqueued')
    ) {
        return;
    }

    $script = <<<'JS'
(function () {
  function applyCityFallback() {
    if (!window.WCUS_APP_STATE || !window.WCUS_APP_STATE.checkout || !window.wc_ukr_shipping_globals) {
      return;
    }

    var defaults = window.wc_ukr_shipping_globals.default_cities || [];
    if (!Array.isArray(defaults) || defaults.length === 0 || !defaults[0] || !defaults[0].value) {
      return;
    }

    var fallback = defaults[0];
    var checkout = window.WCUS_APP_STATE.checkout;

    if (!checkout.city || !checkout.city.value) {
      checkout.city = {
        value: fallback.value,
        name: fallback.name || ""
      };
    }

    var inputNames = ["wcus_np_billing_city", "wcus_np_shipping_city"];
    inputNames.forEach(function (name) {
      var input = document.querySelector('input[name="' + name + '"]');
      if (input && !input.value) {
        input.value = checkout.city.value;
      }
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", applyCityFallback);
  } else {
    applyCityFallback();
  }

  if (window.jQuery) {
    window.jQuery(document.body).on("updated_checkout", applyCityFallback);
  }
})();
JS;

    wp_add_inline_script('wcus_checkout_js', $script, 'before');
}, 200);
