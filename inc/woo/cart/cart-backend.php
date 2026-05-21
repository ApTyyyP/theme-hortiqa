<?php


/* Добавления и изменения товара */
function ajaxBackendCart()
{
    check_ajax_referer('nonceToken', 'nonce');

    error_log(print_r($_POST, true));

    $product_id   = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
    $quantity     = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $cart_key     = isset($_POST['cart_key']) ? sanitize_text_field($_POST['cart_key']) : '';

    if ($product_id <= 0) {
        wp_send_json_error('Invalid product ID');
    }

    // 👇 ДОБАВИЛИ
    $product = wc_get_product($product_id);

    $attributes = [];

    // 👇 Если товар вариативный — собираем attributes
    if ($product && $product->is_type('variable')) {

        foreach ($_POST as $key => $value) {

            if (strpos($key, 'attribute_') === 0) {

                $attributes[$key] = sanitize_text_field($value);
            }
        }
    }

    // 🔥 UPDATE MODE (корзина)
    if ($cart_key) {

        if ($quantity <= 0) {

            WC()->cart->remove_cart_item($cart_key);
        } else {

            WC()->cart->set_quantity($cart_key, $quantity, true);
        }
    } else {

        // ➕ ADD MODE (каталог)
        WC()->cart->add_to_cart(
            $product_id,
            $quantity,
            $variation_id,
            $attributes
        );
    }

    // 📦 CART ITEMS
    $cart_items = [];

    foreach (WC()->cart->get_cart() as $key => $cart_item) {

        $product = $cart_item['data'];

        $cart_items[] = [
            'key'   => $key,
            'id'    => $product->get_id(),
            'name'  => $product->get_name(),
            'qty'   => $cart_item['quantity'],
            'price' => wc_price($product->get_price()),
            'total' => wc_price($product->get_price() * $cart_item['quantity']),
            'image' => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),

            // 👇 ДОБАВИЛИ variation данные
            'variation_id' => $cart_item['variation_id'],
            'variation'    => $cart_item['variation'],
        ];
    }

    wp_send_json_success([
        'cart_items' => $cart_items,
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_total' => WC()->cart->get_total('edit')
    ]);
}

add_action('wp_ajax_cartAdd', 'ajaxBackendCart');
add_action('wp_ajax_nopriv_cartAdd', 'ajaxBackendCart');



/* удаление товара при нажатии на delete */
function cart_remove()
{
    check_ajax_referer('nonceToken', 'nonce');







    if (null === WC()->cart) {
        wc_load_cart();
    }

    $cart_key = isset($_POST['cart_key']) ? sanitize_text_field($_POST['cart_key']) : '';

    if (!$cart_key) {
        wp_send_json_error('Invalid cart key');
    }

    $removed = WC()->cart->remove_cart_item($cart_key);

    if (!$removed) {
        wp_send_json_error('Cart item not found or already removed');
    }

    $cart_items = [];

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

        $product = $cart_item['data'];

        $cart_items[] = [
            'key'   => $cart_item_key,
            'id'    => $product->get_id(),
            'name'  => $product->get_name(),
            'qty'   => $cart_item['quantity'],
            'price' => wc_price($product->get_price()),
            'total' => wc_price($product->get_price() * $cart_item['quantity']),
            'image' => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail')
        ];
    }

    wp_send_json_success([
        'cart_items' => $cart_items,
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_total' => WC()->cart->get_total('edit')
    ]);
}

add_action('wp_ajax_cartRemove', 'cart_remove');
add_action('wp_ajax_nopriv_cartRemove', 'cart_remove');
