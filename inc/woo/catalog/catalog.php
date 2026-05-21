<?php


/********************************** !!!catalog!!! **********************************/

/********************************** wrapper продуктов **********************************/
/* Для замены класса ul карточёк товара ( обертки items) */
add_filter('woocommerce_product_loop_start', 'my_custom_products_ul', 10);
function my_custom_products_ul($html)
{
    // Заменяем стандартный класс на свой
    $html = str_replace('class="products columns-4"', 'class="categories__items row"', $html);
    return $html;
}


/********************************** item продуктов **********************************/
/* !!!!!!!!!!   content-product туда ставлять стили     !!!!!!!!!!! */

/* add_filter('woocommerce_post_class', 'productItemWoo', 20, 2);
function productItemWoo($classes, $product)
{
    if (!is_product()) {
        $classes[] = 'categories__item';
        $classes[] = 'col-md-4';
        $classes[] = 'col-6';
    }

    return $classes;
} */

/********************************** обертка нутри item **********************************/
add_action('woocommerce_before_shop_loop_item', 'open_product_wrapper', 5);
function open_product_wrapper()
{
    echo '<div class="categories__item-box">';
}

add_action('woocommerce_after_shop_loop_item', 'close_product_wrapper', 20);
function close_product_wrapper()
{
    echo '</div>';
}


/********************************** image продуктов, кастомный размер image **********************************/

add_filter('single_product_archive_thumbnail_size', function () {
    return 'best_sellers';
});




/********************************** продукты, карточка товара **********************************/


remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);

// 1. wishlist
add_action('woocommerce_before_shop_loop_item', function () {
    echo '<div class="categories__wishlist product-item__link-heart">';
    echo do_shortcode('[ti_wishlists_addtowishlist]');
    echo '</div>';
}, 5);


/********************************** продукты, лейбел акция **********************************/
add_filter('woocommerce_sale_flash', function ($html, $post, $product) {
    return '<span class="onsale"> АКЦІЯ </span>';
}, 10, 3);


// 2. ссылка на товар
add_action('woocommerce_before_shop_loop_item', function () {
    echo '<a href="' . get_the_permalink() . '" class="categories__cart">';
}, 10);

// 3. закрытие ссылки
add_action('woocommerce_after_shop_loop_item', function () {
    echo '</a>';
}, 5);






/********************************** продукты, Рейтинг **********************************/

remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);

add_action('woocommerce_shop_loop_item_title', function () {
    global $product;

    echo '<div class="categories__contnet">';


    echo '<div class="review-content">';
    echo 'Отзывов :';
    echo '</div>';

    echo '<div class="review-count">';
    echo '' . $product->get_review_count();
    echo '</div>';


    echo '</div>';


    /********************************** продукты, title **********************************/
    echo '<h2 class="categories__name">' . get_the_title() . '</h2>';


    /********************************** категория товара **********************************/
    echo '<div class="categories__desc">';

    $terms = get_the_terms(get_the_ID(), 'product_cat');

    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            // пропускаем главные категории (оставляем только подкатегории)
            if ($term->parent != 0) {
                echo '<span class="category-item">' . esc_html($term->name) . '</span> ';
            }
        }
    }

    echo '</div>';
}, 9);









/********************************** продукты, prace  **********************************/
add_filter('woocommerce_variable_price_html', 'custom_variable_min_price_only', 10, 2);


function custom_variable_min_price_only($price, $product)
{
    $min_regular = $product->get_variation_regular_price('min', true);
    $min_sale    = $product->get_variation_sale_price('min', true);

    // если есть скидка
    if ($min_sale && $min_sale < $min_regular) {
        return wc_format_sale_price(
            wc_price($min_regular),
            wc_price($min_sale)
        ) . $product->get_price_suffix();
    }

    // без скидки — просто минимальная цена
    return wc_price($min_regular) . $product->get_price_suffix();
}



// меняем разделитель копеек (запятая -> точка)
add_filter('wc_get_price_decimal_separator', function () {
    return '.';
});

// пробел между тысячами
add_filter('wc_get_price_thousand_separator', function () {
    return '';
});


// убираем пробел между числом и валютой и меняем порядок валюты и числа %1$s, валюта %2$s
add_filter('woocommerce_price_format', function () {
    return '%2$s%1$s';
});






/********************************** продукты, кнопка товара **********************************/
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

add_action('woocommerce_after_shop_loop_item', function () {
    global $product;

    if ($product->is_type('variation')) {
        echo '<a href="?add-to-cart=' . $product->get_id() . '" class="categories__link btn-green">
        <span>Вибрати</span>
        </a>';
    } else {
        echo '<a href="' . get_permalink($product->get_id()) . '" class="categories__link btn-green">   <span>Вибрати</span></a>';
    }
}, 10);




/**************** общия обертка кнопки фильтра для адаптива, моб фильтр ****************/

remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);

add_action('woocommerce_before_shop_loop', function () {
    echo '<div class="categories__count-box">';
    echo '<button class="fillter-mob__btn btn-filter">
	<span>Фильтр</span>
	<img src="' . get_template_directory_uri() . '/assets/img/png/filter.png" alt="filter">
</button>';

    /* количество товаров */
    woocommerce_result_count();
    echo '</div>';
}, 20);





/* pagination  woocommers  catalog*/

/* ввыводим сколько карточек показывать */

add_filter('loop_shop_per_page', function () {
    return 3;  /* количества на одной странице */
}, 1);


/* pagination  кастомный ввывод*/
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
add_action('woocommerce_after_shop_loop', 'custom_wc_pagination', 10);


function custom_wc_pagination()
{

    global $wp_query;

    $total   = $wp_query->max_num_pages;
    $current = max(1, get_query_var('paged'));

    if ($total <= 1) return;
?>

    <div class="wrapper">
        <div class="pagination">
            <ul class="pagination__items">

                <!-- Prev -->
                <?php
                $prev_page = $current - 1;
                $prev_class = 'pagination__item pagination__item-prev';

                if ($current <= 1) {
                    $prev_class .= ' disabled';
                }
                ?>

                <li class="<?php echo $prev_class; ?>" data-pagination-id="<?php echo $prev_page; ?>">

                    <?php if ($current > 1) : ?>
                        <a class="pagination__link pagination__link-prev"
                            href="<?php echo esc_url(get_pagenum_link($prev_page)); ?>">
                            <span class="pagination__arrow">&lt;</span>
                        </a>
                    <?php else : ?>
                        <span class="pagination__link pagination__link-prev">
                            <span class="pagination__arrow">&lt;</span>
                        </span>
                    <?php endif; ?>

                </li>

                <!-- Pages -->
                <?php
                $links = paginate_links([
                    'total'     => $total,
                    'current'   => $current,
                    'type'      => 'array',
                    'prev_next' => false,
                    'end_size'  => 1,
                    'mid_size'  => 1,
                ]);

                if (!empty($links)) {
                    foreach ($links as $link) {

                        $class = 'pagination__item';

                        if (strpos($link, 'current') !== false) {
                            $class .= ' active';
                        }

                        if (strpos($link, 'dots') !== false) {
                            $class .= ' pagination__item-dots';
                        }

                        $link = str_replace('page-numbers', 'pagination__link', $link);

                        echo '<li class="' . $class . '">' . $link . '</li>';
                    }
                }
                ?>

                <!-- Next -->
                <?php
                $next_page = $current + 1;
                $next_class = 'pagination__item pagination__item-next';

                if ($current >= $total) {
                    $next_class .= ' disabled';
                }
                ?>

                <li class="<?php echo $next_class; ?>" data-pagination-id="<?php echo $next_page; ?>">

                    <?php if ($current < $total) : ?>
                        <a class="pagination__link pagination__link-next"
                            href="<?php echo esc_url(get_pagenum_link($next_page)); ?>">
                            <span class="pagination__arrow">&gt;</span>
                        </a>
                    <?php else : ?>
                        <span class="pagination__link pagination__link-next">
                            <span class="pagination__arrow">&gt;</span>
                        </span>
                    <?php endif; ?>

                </li>

            </ul>
        </div>
    </div>

<?php
}
