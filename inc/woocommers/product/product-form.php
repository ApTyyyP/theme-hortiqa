<?php
/* variation */

/* select добавить класс */
add_filter('woocommerce_dropdown_variation_attribute_options_args', function ($args) {

    if (!empty($args['class'])) {
        $args['class'] .= ' variations__select';
    } else {
        $args['class'] = 'variations__select';
    }

    return $args;
});


/* price variation */
add_filter('woocommerce_available_variation', 'variation_price');

function variation_price($variation)
{

    $price = $variation['display_price'];
    $regular = $variation['display_regular_price'];

    if ($regular > $price) {

        $currency = get_woocommerce_currency_symbol();

        $variation['price_html'] =
            '<div class="my-price">
            <span class="old-price">
                ' . wc_price($regular) . '
                <span class="price-currency">' . $currency . '</span>
            </span>
            <span class="new-price price">
            ' . wc_price($price) . '
            <span class="price-currency">' . $currency . '</span>
            </span>
           
        </div>';
    } else {

        $currency = get_woocommerce_currency_symbol();

        $variation['price_html'] =
            '<div class="my-price">
            <span class="price">
                ' . wc_price($price) . '
                <span class="price-currency">' . $currency . '</span>
            </span>
        </div>';
    }

    return $variation;
}



/* costom: count,  btn */
add_action('woocommerce_before_single_product', function () {
    remove_action(
        'woocommerce_single_variation',
        'woocommerce_single_variation_add_to_cart_button',
        20
    );

    add_action(
        'woocommerce_single_variation',
        'couter_variation',
        20
    );
});

function couter_variation()
{
    global $product;



    if (!$product) return;
?>

    <div class="cout__sum">
        <span class="cout__product-sum">

        </span>
        <span class="cout__product-currency"> <?php echo get_woocommerce_currency_symbol(); ?></span>
    </div>

    <div class="woocommerce-variation-add-to-cart variations_button">

        <!-- COUNTER -->
        <div class="counter">
            <button type="button" class="counter__btn minus">-</button>

            <input type="number"
                class="counter__input qty"
                name="quantity"
                value="1"
                min="1"
                step="1">

            <button type="button" class="counter__btn plus">+</button>
        </div>

        <!-- BUTTON -->
        <button type="submit"
            class="single_add_to_cart_button button alt">
            В корзину
        </button>

        <!-- REQUIRED HIDDEN INPUTS -->
        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
        <input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>">
        <input type="hidden" name="variation_id" class="variation_id" value="0">

    </div>

<?php
}









/* простой товар */

remove_action('woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30);
add_action('woocommerce_simple_add_to_cart', 'couter_simple', 30);



function couter_simple()
{
    global $product;

    if (!$product || !$product->is_type('simple')) return;
?>

    <form class="cart" method="post" enctype="multipart/form-data">

        <div class="cout__sum">
            <span class="cout__product-sum cout__simple-sum">0</span>
            <span class="cout__product-currency">
                <?php echo get_woocommerce_currency_symbol(); ?>
            </span>
        </div>

        <div class="woocommerce-simple-add-to-cart">

            <!-- COUNTER -->
            <div class="counter">
                <button type="button" class="counter__btn minus">-</button>

                <input type="number"
                    class="counter__input qty"
                    name="quantity"
                    value="1"
                    min="1"
                    step="1">

                <button type="button" class="counter__btn plus">+</button>
            </div>

            <!-- BUTTON -->
            <button type="submit"
                class="single_add_to_cart_button button alt">
                В корзину
            </button>

            <!-- REQUIRED -->
            <input type="hidden"
                name="add-to-cart"
                value="<?php echo esc_attr($product->get_id()); ?>">

        </div>

    </form>

<?php
}
