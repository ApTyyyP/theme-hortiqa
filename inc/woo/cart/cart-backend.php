<?php

function hortiqa_verify_cart_ajax_nonce()
{
    $is_valid_nonce = check_ajax_referer('nonceToken', 'nonce', false);

    // Cart actions should continue to work even if nonce is stale.
    if (false === $is_valid_nonce && defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Cart AJAX: stale or missing nonce');
    }

    return $is_valid_nonce;
}

function hortiqa_guest_cart_cookie_name()
{
    return 'hortiqa_guest_cart';
}

function hortiqa_set_cookie_value($name, $value, $expires)
{
    if (headers_sent()) {
        return;
    }

    setcookie(
        $name,
        $value,
        [
            'expires'  => $expires,
            'path'     => '/',
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]
    );
}

function hortiqa_clear_guest_cart_cookie()
{
    $cookie_name = hortiqa_guest_cart_cookie_name();
    hortiqa_set_cookie_value($cookie_name, '', time() - HOUR_IN_SECONDS);
    unset($_COOKIE[$cookie_name]);
}

function hortiqa_collect_cart_items_for_merge()
{
    if (null === WC()->cart) {
        return [];
    }

    $items = [];

    foreach (WC()->cart->get_cart() as $cart_item) {
        $items[] = [
            'product_id'   => isset($cart_item['product_id']) ? intval($cart_item['product_id']) : 0,
            'variation_id' => isset($cart_item['variation_id']) ? intval($cart_item['variation_id']) : 0,
            'quantity'     => isset($cart_item['quantity']) ? max(1, intval($cart_item['quantity'])) : 1,
            'variation'    => isset($cart_item['variation']) && is_array($cart_item['variation']) ? $cart_item['variation'] : [],
        ];
    }

    return $items;
}

function hortiqa_store_guest_cart_for_login_merge()
{
    if (is_user_logged_in()) {
        return;
    }

    if (null === WC()->cart) {
        return;
    }

    $payload = hortiqa_collect_cart_items_for_merge();

    if (empty($payload)) {
        hortiqa_clear_guest_cart_cookie();
        return;
    }

    $json = wp_json_encode($payload);

    if (! is_string($json) || '' === $json) {
        return;
    }

    $encoded = rawurlencode(base64_encode($json));

    // Keep under common browser cookie limits.
    if (strlen($encoded) > 3500) {
        return;
    }

    $cookie_name = hortiqa_guest_cart_cookie_name();
    hortiqa_set_cookie_value($cookie_name, $encoded, time() + DAY_IN_SECONDS);
    $_COOKIE[$cookie_name] = $encoded;
}

function hortiqa_read_guest_cart_from_cookie()
{
    $cookie_name = hortiqa_guest_cart_cookie_name();

    if (empty($_COOKIE[$cookie_name])) {
        return [];
    }

    $decoded = base64_decode(rawurldecode((string) $_COOKIE[$cookie_name]), true);

    if (false === $decoded || '' === $decoded) {
        return [];
    }

    $payload = json_decode($decoded, true);

    if (! is_array($payload)) {
        return [];
    }

    return $payload;
}

function hortiqa_merge_guest_cart_into_logged_user_cart()
{
    if (! is_user_logged_in()) {
        return;
    }

    if (null === WC()->cart) {
        wc_load_cart();
    }

    $guest_items = hortiqa_read_guest_cart_from_cookie();

    if (empty($guest_items)) {
        return;
    }

    $existing_qty = [];

    foreach (WC()->cart->get_cart() as $current_item) {
        $product_id   = isset($current_item['product_id']) ? intval($current_item['product_id']) : 0;
        $variation_id = isset($current_item['variation_id']) ? intval($current_item['variation_id']) : 0;
        $variation    = isset($current_item['variation']) && is_array($current_item['variation']) ? $current_item['variation'] : [];
        $quantity     = isset($current_item['quantity']) ? max(1, intval($current_item['quantity'])) : 1;

        if ($product_id <= 0) {
            continue;
        }

        $cart_id = WC()->cart->generate_cart_id($product_id, $variation_id, $variation, []);
        $existing_qty[$cart_id] = isset($existing_qty[$cart_id]) ? $existing_qty[$cart_id] + $quantity : $quantity;
    }

    foreach ($guest_items as $guest_item) {
        if (! is_array($guest_item)) {
            continue;
        }

        $product_id   = isset($guest_item['product_id']) ? intval($guest_item['product_id']) : 0;
        $variation_id = isset($guest_item['variation_id']) ? intval($guest_item['variation_id']) : 0;
        $variation    = isset($guest_item['variation']) && is_array($guest_item['variation']) ? $guest_item['variation'] : [];
        $quantity     = isset($guest_item['quantity']) ? max(1, intval($guest_item['quantity'])) : 1;

        if ($product_id <= 0) {
            continue;
        }

        $cart_id = WC()->cart->generate_cart_id($product_id, $variation_id, $variation, []);
        $already_in_cart = isset($existing_qty[$cart_id]) ? intval($existing_qty[$cart_id]) : 0;
        $qty_to_add = $quantity - $already_in_cart;

        if ($qty_to_add <= 0) {
            continue;
        }

        $added = WC()->cart->add_to_cart($product_id, $qty_to_add, $variation_id, $variation, []);

        if ($added) {
            $existing_qty[$cart_id] = $already_in_cart + $qty_to_add;
        }
    }

    hortiqa_clear_guest_cart_cookie();
}

function hortiqa_has_wc_session_cookie()
{
    $cookie_name = (string) apply_filters('woocommerce_cookie', 'wp_woocommerce_session_' . COOKIEHASH);

    return ! empty($_COOKIE[$cookie_name]);
}

function hortiqa_reset_stale_server_cart($cart_key = '')
{
    if (is_user_logged_in()) {
        return;
    }

    if (! empty($cart_key)) {
        return;
    }

    if (hortiqa_has_wc_session_cookie()) {
        return;
    }

    if (null === WC()->cart || WC()->cart->is_empty()) {
        return;
    }

    // Browser storage was reset, but Woo restored old server-side cart.
    WC()->cart->empty_cart(true);
}

function hortiqa_get_cart_snapshot()
{
    if (null === WC()->cart) {
        wc_load_cart();
    }

    $cart_items = [];

    foreach (WC()->cart->get_cart() as $key => $cart_item) {
        $product = $cart_item['data'];

        $cart_items[] = [
            'key'          => $key,
            'id'           => $product->get_id(),
            'name'         => $product->get_name(),
            'qty'          => $cart_item['quantity'],
            'price'        => wc_price($product->get_price()),
            'total'        => wc_price($product->get_price() * $cart_item['quantity']),
            'image'        => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
            'variation_id' => $cart_item['variation_id'],
            'variation'    => $cart_item['variation'],
        ];
    }

    return [
        'cart_items' => $cart_items,
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_total' => WC()->cart->get_total('edit'),
    ];
}

function ajaxBackendCart()
{
    hortiqa_verify_cart_ajax_nonce();

    if (null === WC()->cart) {
        wc_load_cart();
    }

    hortiqa_merge_guest_cart_into_logged_user_cart();

    $product_id   = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
    $quantity     = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $cart_key     = isset($_POST['cart_key']) ? sanitize_text_field($_POST['cart_key']) : '';

    hortiqa_reset_stale_server_cart($cart_key);

    if ($product_id <= 0) {
        wp_send_json_error('Invalid product ID');
    }

    $product = wc_get_product($product_id);
    $attributes = [];

    if ($product && $product->is_type('variable')) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'attribute_') === 0) {
                $attributes[$key] = sanitize_text_field($value);
            }
        }
    }

    if ($cart_key) {
        if ($quantity <= 0) {
            WC()->cart->remove_cart_item($cart_key);
        } else {
            WC()->cart->set_quantity($cart_key, $quantity, true);
        }
    } else {
        WC()->cart->add_to_cart(
            $product_id,
            $quantity,
            $variation_id,
            $attributes
        );
    }

    hortiqa_store_guest_cart_for_login_merge();

    wp_send_json_success(hortiqa_get_cart_snapshot());
}

add_action('wp_ajax_cartAdd', 'ajaxBackendCart');
add_action('wp_ajax_nopriv_cartAdd', 'ajaxBackendCart');

function cart_remove()
{
    hortiqa_verify_cart_ajax_nonce();

    if (null === WC()->cart) {
        wc_load_cart();
    }

    hortiqa_merge_guest_cart_into_logged_user_cart();

    $cart_key = isset($_POST['cart_key']) ? sanitize_text_field($_POST['cart_key']) : '';

    if (! $cart_key) {
        wp_send_json_error('Invalid cart key');
    }

    $removed = WC()->cart->remove_cart_item($cart_key);

    if (! $removed) {
        wp_send_json_error('Cart item not found or already removed');
    }

    hortiqa_store_guest_cart_for_login_merge();

    wp_send_json_success(hortiqa_get_cart_snapshot());
}

add_action('wp_ajax_cartRemove', 'cart_remove');
add_action('wp_ajax_nopriv_cartRemove', 'cart_remove');

function cart_state()
{
    hortiqa_verify_cart_ajax_nonce();

    if (null === WC()->cart) {
        wc_load_cart();
    }

    hortiqa_merge_guest_cart_into_logged_user_cart();
    hortiqa_reset_stale_server_cart('');
    hortiqa_store_guest_cart_for_login_merge();

    wp_send_json_success(hortiqa_get_cart_snapshot());
}

add_action('wp_ajax_cartState', 'cart_state');
add_action('wp_ajax_nopriv_cartState', 'cart_state');
