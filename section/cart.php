<?php
function cart()
{
    // Форсуємо ініціалізацію сесії та корзини
    if (null === WC()->session) {
        WC()->session = new WC_Session_Handler();
        WC()->session->init();
    }

    if (null === WC()->cart) {
        WC()->cart = new WC_Cart();
    }

    // Форсуємо оновлення кошика
    if (function_exists('hortiqa_merge_guest_cart_into_logged_user_cart')) {
        hortiqa_merge_guest_cart_into_logged_user_cart();
    }

    WC()->cart->get_cart();

    $items = WC()->cart->get_cart();

?>

    <div class="cart mini-cart">
        <div class="cart__inner">
            <div class="cart__box">
                <h4 class="cart__title">Ваш кошик</h4>
                <div class="cart__clouse">X</div>
            </div>
            <div class="cart__box-wrapper">

                <?php if (! empty($items)) : ?>
                    <ul class="cart__items">
                        <?php foreach ($items as $cart_item_key => $cart_item) :
                            $product = $cart_item['data'];
                        ?>
                            <li class="cart__item"
                                data-cart-key="<?= esc_attr($cart_item_key); ?>"
                                data-product-id="<?= esc_attr($product->get_id()); ?>"
                                data-variation-id="<?= esc_attr($cart_item['variation_id'] ?? 0); ?>">
                                <div class="cart__box">
                                    <a href="<?= esc_url($product->get_permalink()); ?>">
                                    <img class="cart__image"
                                        src="<?= esc_url(wp_get_attachment_image_url($product->get_image_id(), 'thumbnail')); ?>"
                                        alt="<?= esc_attr($product->get_name()); ?>" />
                                    </a>
                                    <div class="cart__wrapper">
                                        <div class="cart__sub-title"><?= esc_html($product->get_name()); ?></div>

                                        <!-- variation -->

                                        <?php if (!empty($cart_item['variation'])) : ?>

                                            <div class="mini-cart__variations">

                                                <?php foreach ($cart_item['variation'] as $key => $value) : ?>

                                                    <div class="mini-cart__variation">

                                                        <span>
                                                            <?php echo esc_html(str_replace('attribute_pa_', '', $key)); ?>:
                                                        </span>

                                                        <span>
                                                            <?php echo esc_html($value); ?>
                                                        </span>

                                                    </div>

                                                <?php endforeach; ?>

                                            </div>

                                        <?php endif; ?>



                                        <div class="mini-cart__counter" data-qty="<?= esc_attr($cart_item['quantity']); ?>">
                                            <button class="mini-cart__counter-btn minus" type="button">-</button>
                                            <input class="mini-cart__counter-input" type="text" value="<?= esc_attr($cart_item['quantity']); ?>" maxlength="1">
                                            <button class="mini-cart__counter-btn plus" type="button">+</button>
                                        </div>
                                    </div>
                                    <div class="cart__wrapper-inner">
                                        <div class="cart__price">
                                            <?= wc_price($cart_item['line_total']); ?>
                                        </div>
                                        <div class="cart__delete">Видалити</div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="cart__btn">
                        <div class="cart__price">
                            Загальна ціна:
                            <span class="cart__price-currency"><?= wc_price(WC()->cart->get_cart_contents_total()); ?></span>
                        </div>
                        <a class="cart__link" href="<?php echo wc_get_checkout_url(); ?>">Оформити замовлення</a>
                    </div>

                <?php else : ?>
                    <p class="cart__empty">Поки що немає товару в магазині ...</p>
                <?php endif; ?>

            </div>
        </div>
    </div>

<?php
}
