<?php

/**
 * The Template for displaying wishlist if a current user is owner.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist.php.
 *
 * @version             2.3.3
 * @package           TInvWishlist\Template
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
wp_enqueue_script('tinvwl');
?>
<div class="tinv-wishlist woocommerce tinv-wishlist-clear">
    <?php do_action('tinvwl_before_wishlist', $wishlist); ?>
    <?php if (function_exists('wc_print_notices') && isset(WC()->session)) wc_print_notices(); ?>

    <?php $form_url = tinv_url_wishlist($wishlist['share_key'], $wl_paged, true); ?>
    <form action="<?php echo esc_url($form_url); ?>" method="post" autocomplete="off"
        data-tinvwl_paged="<?php echo $wl_paged; ?>"
        data-tinvwl_per_page="<?php echo $wl_per_page; ?>"
        data-tinvwl_sharekey="<?php echo $wishlist['share_key']; ?>"
        class="tinvwl-wishlist-form">

        <?php do_action('tinvwl_before_wishlist_table', $wishlist); ?>

        <div class="row wishlist-wrapper">
            <?php do_action('tinvwl_wishlist_contents_before'); ?>

            <?php
            global $product, $post;

            $_product_tmp = $product;
            $_post_tmp = $post; ?>



            <ul class="categories__items row">
                <? foreach ($products as $wl_product) {

                    if (empty($wl_product['data'])) continue;

                    $product = apply_filters('tinvwl_wishlist_item', $wl_product['data']);
                    $post = get_post($product->get_id());


                    $product = apply_filters('tinvwl_wishlist_item', $wl_product['data']);



                    $post = get_post($product->get_id());
                    unset($wl_product['data']);

                    if ($wl_product['quantity'] > 0 && apply_filters('tinvwl_wishlist_item_visible', true, $wl_product, $product)) {



                        $rating = (float) $product->get_average_rating();
                        $rating_int = floor($rating);

                        $product_url = apply_filters(
                            'tinvwl_wishlist_item_url',
                            $product->get_permalink(),
                            $wl_product,
                            $product
                        );

                        do_action('tinvwl_wishlist_row_before', $wl_product, $product);
                ?>



                        <li class="categories__item product type-product  col-sm-6 col-lg-4 col-xl-3">

                            <div class="categories__item-box">

                                <!-- REMOVE (wishlist) -->
                                <div class="categories__wishlist product-item__link-heart">
                                    <button type="submit"
                                        name="tinvwl-remove"
                                        value="<?php echo esc_attr($wl_product['ID']); ?>">
                                        <i class="ftinvwl ftinvwl-times"></i>
                                    </button>
                                </div>

                                <!-- IMAGE -->
                                <a href="<?php echo esc_url($product_url); ?>" class="categories__cart">

                                    <span class="onsale">АКЦІЯ</span>

                                    <?php echo $product->get_image('woocommerce_thumbnail'); ?>

                                </a>

                                <!-- INNER -->
                                <div class="categories__item-inner">

                                    <!-- NAME -->
                                    <a href="<?php echo esc_url($product_url); ?>" class="categories__cart">

                                        <div class="categories__contnet">
                                            <div class="review-content">Отзывов :</div>
                                            <div class="review-count">
                                                <?php echo $product->get_review_count(); ?>
                                            </div>
                                        </div>

                                        <h2 class="categories__name">
                                            <?php echo $product->get_name(); ?>
                                        </h2>




                                        <div class="categories__desc">
                                            <?php echo wc_get_product_category_list($product->get_id()); ?>
                                        </div>




                                        <?php if (!empty($rating) && $rating > 0) : ?>

                                            <div class="rating" data-rate-total="<?php echo esc_attr($rating); ?>">

                                                <?php for ($i = 5; $i >= 1; $i--) : ?>

                                                    <svg
                                                        class="rating__star <?php echo ($i <= $rating_int) ? 'active' : ''; ?>"
                                                        data-rate="<?php echo $i; ?>"
                                                        viewBox="0 0 26 25">

                                                        <path d="M11.5204 1.9421C11.986 0.508953 14.0135 0.508955 14.4792 1.94211L16.2677 7.44656C16.476 8.08748 17.0732 8.52142 17.7471 8.52142H23.5349C25.0418 8.52142 25.6683 10.4497 24.4492 11.3354L19.7668 14.7374C19.2216 15.1335 18.9935 15.8356 19.2017 16.4765L20.9902 21.981C21.4559 23.4142 19.8156 24.6059 18.5965 23.7202L13.9141 20.3182C13.3689 19.9221 12.6307 19.9221 12.0855 20.3182L7.40308 23.7202C6.18397 24.6059 4.54367 23.4141 5.00933 21.981L6.79784 16.4765C7.00608 15.8356 6.77795 15.1335 6.23275 14.7374L1.55038 11.3354C0.331269 10.4497 0.95781 8.52142 2.46471 8.52142H8.25243C8.92634 8.52142 9.52361 8.08748 9.73186 7.44656L11.5204 1.9421Z"></path>

                                                    </svg>

                                                <?php endfor; ?>

                                            </div>

                                        <?php endif; ?>
                                    </a>


                                    <!-- PRICE + BUTTON -->
                                    <div class="categories__wrapper-content">

                                        <a href="<?php echo esc_url($product_url); ?>" class="categories__cart categories__cart-price">
                                            <?php echo $product->get_price_html(); ?>
                                        </a>

                                        <a href="<?php echo esc_url($product_url); ?>" class="categories__link btn-green">
                                            <span>Вибрати</span>
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </li>



                <?php
                        do_action('tinvwl_wishlist_row_after', $wl_product, $product);
                    }
                } ?>
            </ul>


            <? $product = $_product_tmp;
            $post = $_post_tmp;
            ?>
            <?php do_action('tinvwl_wishlist_contents_after'); ?>
        </div>

        <div class="wishlist-footer">
            <?php do_action('tinvwl_after_wishlist_table', $wishlist); ?>
            <?php wp_nonce_field('tinvwl_wishlist_owner', 'wishlist_nonce'); ?>
        </div>
    </form>

    <?php do_action('tinvwl_after_wishlist', $wishlist); ?>
    <div class="tinv-lists-nav tinv-wishlist-clear">
        <?php do_action('tinvwl_pagenation_wishlist', $wishlist); ?>
    </div>
</div>