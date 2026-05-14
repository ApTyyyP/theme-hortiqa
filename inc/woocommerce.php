<?php




if (class_exists('WooCommerce')) {

    /* !!!!woocommerce castom!!!  */
    function hortiqa_add_woocommerce_support()
    {
        add_theme_support('woocommerce');
    }
    add_action('after_setup_theme', 'hortiqa_add_woocommerce_support');



    /* catalog */
    require get_template_directory() . '/inc/woocommers/account.php';
    require get_template_directory() . '/inc/woocommers/catalog/catalog.php';
    require get_template_directory() . '/inc/woocommers/catalog/catalog-filter.php';

    /* product */
    require get_template_directory() . '/inc/woocommers/product/product.php';
    require get_template_directory() . '/inc/woocommers/product/product-form.php';
    require get_template_directory() . '/inc/woocommers/product/product-comment.php';


    /* rating */
    require get_template_directory() . '/inc/woocommers/rating.php';

    /* cart */
    require get_template_directory() . '/inc/woocommers/cart/cart-backend.php';
    require get_template_directory() . '/section/cart.php';

   

}
