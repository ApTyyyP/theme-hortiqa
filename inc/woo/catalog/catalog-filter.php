<?php


/********************************** selector filter **********************************/
// удаляем сортировку по названию
add_filter('woocommerce_catalog_orderby', function ($sortby) {
    unset($sortby['title']);
    return $sortby;
});

/* переоприделяем */

add_filter('woocommerce_catalog_orderby', function ($sortby) {

    return [
        'popularity'  => 'По популярности',
        'rating'      => 'По рейтингу',
        'date'        => 'По новизне',
        'price'       => 'Сначала дешевые',
        'price-desc'  => 'Сначала дорогие',
    ];
});

// убираем стандартный вывод
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

// добавляем свой с обёрткой
add_action('woocommerce_before_shop_loop', function () {

    echo '<div class="orderby-wrapper">';

    woocommerce_catalog_ordering();

    echo '</div>';
}, 30);



