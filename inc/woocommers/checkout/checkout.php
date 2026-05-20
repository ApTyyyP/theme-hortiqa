<?php




/* Хук удаляет из формы импуты,  делать обезательные импуты или нет, для заполения и можно управлять порядком вывода импутов  */
add_filter('woocommerce_checkout_fields', 'custom_remove_checkout_fields');

function custom_remove_checkout_fields($fields)
{
    // Улаление из личных данных импуты
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_city']);

    // PHONE обезательное к заполнению
    $fields['billing']['billing_phone']['required'] = true;
    $fields['billing']['billing_phone']['label'] = 'Телефон';

    // Доставку , дефотную отключил, тому что потключил новую почту.
    unset($fields['shipping']);

    return $fields;
}

add_filter('woocommerce_checkout_fields', 'custom_remove_checkout_fields');





/* хук для порядка вывода в форме блоков импуты , 1 личные данные 2 доставка 3 оплата*/

remove_action(
    'woocommerce_checkout_billing',
    array(WC()->checkout(), 'checkout_form_billing')
);



add_action('woocommerce_checkout_billing', function () {

    $checkout = WC()->checkout();
    $fields = $checkout->get_checkout_fields();

    echo '<div class="checkout__wrapper">';
    echo '<h2 class="checkout__title title">Оформлення замовлення</h2>';
    echo '<ul class="checkout__items ">';


    //  личные данные 
    foreach ($fields['billing'] as $key => $field) {

        echo '<li class="checkout__item">';
        // добавляем свой класс к input
        $field['input_class'][] = 'checkout__input';
        $field['class'][] = 'checkout__box';

        woocommerce_form_field(
            $key,
            $field,
            $checkout->get_value($key)
        );
        echo '</li>';
    }



    // ⚙️ ACCOUNT (если включено)
    if (!empty($fields['account'])) {
        echo '<h3 class="title">Доствка</h3>';

        foreach ($fields['account'] as $key => $field) {
            echo '<li class="checkout__item">';
            // добавляем свой класс к input
            $field['input_class'][] = 'checkout__input';
            $field['class'][] = 'checkout__box';


            woocommerce_form_field(
                $key,
                $field,
                $checkout->get_value($key)
            );
        }
        echo '</li>';
    }



    /* --------------------не нужна доствка базовая вокомерса убрать значит -------------- */


    /* Доствка  отключил стандартную потключил вокомерс */
    /*     if (!empty($fields['shipping'])) {

        echo '<h2 class="title">Доставка</h2>';

        foreach ($fields['shipping'] as $key => $field) {

            echo '<li class="checkout__item">';

            $field['input_class'][] = 'checkout__input';
            $field['class'][] = 'checkout__box';

            woocommerce_form_field(
                $key,
                $field,
                $checkout->get_value($key)
            );

            echo '</li>';
        }
    } */

    echo '</ul>';
    echo '<h3 class="checkout__comment">Доставка нова пошта</h3>';
    echo '</div>';
}, 10);





/* хук Коментарь из формы ,  Если нужно его можно поместить выше в хук*/
add_action('woocommerce_after_order_notes', function () {

    echo '<div class="after-np-comment">';

    $checkout = WC()->checkout();
    $field = $checkout->get_checkout_fields()['order']['order_comments'];

    $field['input_class'][] = 'checkout__input';
    $field['class'][] = 'checkout__box';

    woocommerce_form_field(
        'order_comments',
        $field,
        $checkout->get_value('order_comments')
    );

    echo '</div>';
}, 20);



/* убрать  Дополнительный адрис */
add_filter('woocommerce_cart_needs_shipping_address', '__return_false');









/* Блок опалата */

remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);

add_action('woocommerce_checkout_order_review', function () {

    echo '<h3 class="checkout__comment">Оберіть зручний для вас спосіб оплати</h3>';

    woocommerce_checkout_payment();
}, 20);
