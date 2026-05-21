<?php


// RELATED (похожие товары)
add_filter('woocommerce_post_class', 'custom_wc_product_grid_classes', 10, 2);

function custom_wc_product_grid_classes($classes, $product)
{

    if (is_admin()) return $classes;

    if (wc_get_loop_prop('name') === 'related') {
        $classes[] = 'col-lg-3';
        $classes[] = 'col-6';
    }


    return $classes;
}






/* передвинул выше single_meta */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

/* product_meta  кастом (выводит подкатегорию , подкатегорию-подкатегории) */
add_action('woocommerce_single_product_summary', 'custom_product_meta', 4);
function custom_product_meta()
{
    global $product;

    if (! $product) return;

    echo '<div class="product_meta">';

    // ===== SKU =====
    $sku = $product->get_sku();

    echo '<span class="sku_wrapper">Артикул: <span class="sku">' .
        ($sku ? esc_html($sku) : 'Н/Д') .
        '</span></span>';

    // ===== КАТЕГОРИИ =====
    $terms = get_the_terms($product->get_id(), 'product_cat');

    if ($terms && !is_wp_error($terms)) {

        echo '<span class="posted_in">Категории: ';

        $links = [];

        foreach ($terms as $term) {
            $links[] = '<a href="' . esc_url(get_term_link($term)) . '" rel="tag">' . esc_html($term->name) . '</a>';
        }

        echo implode(', ', $links);

        echo '</span>';
    }

    echo '</div>';
}


remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);

/* product__title, кастом есть наличии*/

add_action('woocommerce_single_product_summary', function () {
    global $product;


    echo '<h1 class="product__title title">' . get_the_title() . '</h1>';


    /*есть наличи*/
    if ($product->is_in_stock()) {
        echo '<div class="product__stock in">В наличии</div>';
    } else {
        echo '<div class="product__stock out">Нет в наличии</div>';
    }
}, 5);



// Убираем стандартный вывод цены
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);

// Добавляем свой вывод цены с условием
add_action('woocommerce_single_product_summary', function () {


    global $product;

    // если товар вариативный — ничего не показываем
    if ($product->is_type('variable')) {
        return;
    }

    // для простых товаров показываем цену
    echo $product->get_price_html();
}, 6);



/* Лебел акция   */
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
