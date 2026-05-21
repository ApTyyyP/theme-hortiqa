<?php
/* my-account */

/* delete title account */

add_filter('the_title', function ($title, $id) {
    if (is_account_page() && in_the_loop()) {
        return '';
    }
    return $title;
}, 10, 2);



add_filter('woocommerce_product_query', 'custom_filter_variations');

function custom_filter_variations($query)
{
    $color = $_GET['filter_pa_color'] ?? '';
    if (!empty($color)) {
        $query->set('post_type', 'product_variation');

        $query->set('meta_query', array(
            array(
                'key'     => '_variation_attribute_pa_color',
                'value'   => $color,
                'compare' => '='
            ),
        ));
    }

    return $query;
}
