<?php

if (class_exists('WooCommerce')) {

    /* !!!!woocommerce castom!!!  */
    function hortiqa_add_woocommerce_support()
    {
        add_theme_support('woocommerce');
    }
    add_action('after_setup_theme', 'hortiqa_add_woocommerce_support');



    /* catalog */
    require get_template_directory() . '/inc/woo/account.php';
    require get_template_directory() . '/inc/woo/catalog/catalog.php';
    require get_template_directory() . '/inc/woo/catalog/catalog-filter.php';

    /* product */
    require get_template_directory() . '/inc/woo/product/product.php';
    require get_template_directory() . '/inc/woo/product/product-form.php';
    require get_template_directory() . '/inc/woo/product/product-comment.php';


    /* rating */
    require get_template_directory() . '/inc/woo/rating.php';

    /* mini cart */
    require get_template_directory() . '/inc/woo/cart/cart-backend.php';
    require get_template_directory() . '/section/cart.php';


    /* checkout */
    require get_template_directory() . '/inc/woo/checkout/checkout.php';
}
