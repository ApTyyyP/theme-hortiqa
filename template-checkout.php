<?php /*Template name: checkout*/ ?>



<? get_header();

if (WC()->cart->is_empty()) {
    wp_redirect(wc_get_page_permalink('shop'));
    exit;
}

$cart = WC()->cart;
$items = $cart->get_cart();  ?>

<body class="body-payment">
    <main>


        <section class="summary" style="background-image: url(<?= get_template_directory_uri(); ?>/assets/img/summary/summary.png)">

            <div class="wrapper">
                <div class="summary__wrapper-box">


                    <!--  summary/form-summary.php -->
                    <?php echo do_shortcode('[woocommerce_checkout]'); ?>



                    <?php if (! is_order_received_page()) : ?>

                        <!-- basket product -->
                        <div class="basket">
                            <div class="basket__title">Ваше замовлення:</div>
                            <ul class="basket__items ">
                                <?php foreach ($items as $cart_item_key => $cart_item) :
                                    $product = $cart_item['data'];
                                ?>
                                    <li class="basket__item cart__item" data-product-id="<?= esc_attr($product->get_id()); ?>">
                                        <div class="basket__box">
                                            <a href="<?= esc_url($product->get_permalink()); ?>">
                                                <img
                                                    class="basket__image"
                                                    src="<?= wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'); ?>"
                                                    alt="<?= esc_attr($product->get_name()); ?>" />
                                            </a>
                                            <div class="basket__wrapper">
                                                <div class="basket__sub-title"><?= esc_html($product->get_name()); ?></div>

                                                <!-- VARIATIONS -->
                                                <?php if (!empty($cart_item['variation'])) : ?>
                                                    <div class="mini-cart__variations">
                                                        <?php foreach ($cart_item['variation'] as $key => $value) : ?>
                                                            <div class="mini-cart__variation">
                                                                <span><?= esc_html(str_replace('attribute_pa_', '', $key)); ?>:</span>
                                                                <span><?= esc_html($value); ?></span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>



                                                <div class="basket__wrapper-inner">
                                                    <!-- Сумма за товар без валюты -->
                                                    <div class="basket__content-inner">
                                                        <div class="basket__quantity"><span>Кількість :</span> <?= esc_html($cart_item['quantity']); ?></div>
                                                        <div class="basket__prace__wrapper">
                                                            <div class="basket__prace "> <?php echo WC()->cart->get_cart_total(); ?></div>

                                                        </div>

                                                    </div>

                                                </div>
                                            </div>


                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="basket__price">
                                <p>Сума до сплати</p>
                                <div class="basket__price-box">

                                    <span class="summary__price-currency"> <?php echo number_format(WC()->cart->get_cart_contents_total(), 2, '.', ''); ?> </span>
                                    <span class="summary__currency"> <?php echo get_woocommerce_currency_symbol(); ?> </span>
                                </div>



                            </div>



                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
    <?php get_footer(); ?>
</body>














<!-- <form class="form summary__form" action="/payment/">
    <h2 class="summary__title title">Оформлення замовлення</h2>
    <div class="form__sub-title">Контактні дані</div>
    <ul class="summary__items summary__contact">
        <li class="summary__item form__box">
            <div class="summary__item-wrapper">
                <span class="form__input-name">Ваше Призвіще*</span>
                <input
                    class="form__input input__name"
                    type="text"
                    placeholder="Введіть Ваше Призвіще" required />
            </div>

            <div class="summary__item-wrapper">
                <span class="form__input-name">Ваше ім'я*</span>
                <input
                    class="form__input input__name"
                    type="text"
                    placeholder="Введіть Ваше ім'я" required />
            </div>
        </li>



        <li class="summary__item form__box">
            <div class="summary__item-wrapper">
                <span class="form__input-name">Ваш телефон*</span>
                <input
                    class="form__input phone__tell"
                    type="tel"
                    placeholder="+38" required />
            </div>
            <div class="summary__item-wrapper">
                <span class="form__input-name">Ваш e-mail*</span>
                <input
                    class="form__input input__mail"
                    type="email"
                    placeholder="Введіть вашу пошту" required />
            </div>
        </li>


        <li class="summary__item form__box">
            <div class="summary__item-wrapper summary__item-coment">
                <span class="form__input-name">Коментар до замовлення (якщо потрибно)</span>
                <input
                    class="form__input input__user-name"
                    type="text"
                    placeholder="Примітки до вашого замовлення, особливі побажання для відділу доставки." />
            </div>
        </li>
    </ul>
    <div class="form__sub-title">Доставка</div>
    <ul class="summary__items summary__delivery">
        <li class="summary__item summary__del">
            <label>
                <input
                    class="form__input-radio"
                    type="radio"
                    name="delivery"
                    value="pickup" required />

                <span class="form__radio-custom"></span>
                <span class="form__name-radio">Нова Пошта</span>
            </label>


        </li>





        <li class="summary__item form__box item__pickup">
            <div class="summary__item-wrapper">
                <span class="form__input-name">Місто*</span>
                <input
                    class="form__input input__city"
                    type="text"
                    placeholder="Введіть місто"
                    required
                    id="city-input"
                    value="Київ"
                    autocomplete="off" />
                <ul id="city-suggestions" class="suggestions-list"></ul>
            </div>
            <div class="summary__item-wrapper">
                <span class="form__input-name">Вулиця*</span>
                <input
                    class="form__input input__street"
                    type="text"
                    placeholder="Введіть вулицю" required />
            </div>
        </li>

    </ul>
    <div class="form__sub-title">Оплата</div>
    <ul class="summary__items summary__payment">
        <li class="summary__item">
            <label>
                <input
                    class="form__input-radio"
                    type="radio"
                    name="payment"
                    value="card" required />
                <span class="form__radio-custom"></span>
                <span class="form__name-radio">Банківська картка</span>
            </label>

            <label>
                <input
                    class="form__input-radio"
                    type="radio"
                    name="payment"
                    value="cash" required />
                <span class="form__radio-custom"></span>
                <span class="form__name-radio">Готівка</span>
            </label>

            <label>
                <input
                    class="form__input-radio"
                    type="radio"
                    name="payment"
                    value="applepay" required />
                <span class="form__radio-custom"></span>
                <span class="form__name-radio">Apple Pay</span>
            </label>

            <label>
                <input
                    class="form__input-radio"
                    type="radio"
                    name="payment"
                    value="googlepay" required />
                <span class="form__radio-custom"></span>
                <span class="form__name-radio">Google Pay</span>
            </label>

        </li>
    </ul>

    <ul class="summary__items summary__promo">
        <li class="summary__item">
            <div class="summary__inner">
                <div class="summary__total">
                    <span>Загальна сума замовлення</span>
                    <div class="summary__content-box">
                        <span class="summary__price-currency"> <?php echo number_format(WC()->cart->get_cart_contents_total(), 2, '.', ''); ?> </span>
                        <span class="summary__currency"> <?php echo get_woocommerce_currency_symbol(); ?> </span>




                    </div>

                </div>


        </li>
    </ul>

    <div class="summary__inner-box">
        <button class="summary__paymentbtn">До оплати</button>
    </div>
</form> -->