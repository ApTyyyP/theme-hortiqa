<?php




/* Хук для удаляния ипутов личных данных и доставки    и можно задать порядок */
add_filter('woocommerce_checkout_fields', 'custom_remove_checkout_fields');

function custom_remove_checkout_fields($fields)
{

    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_address_2']);


    unset($fields['shipping']);

    return $fields;
}





/* хук для порядка вывода  импутов, Личные данные доставка оплата и добавления стилей */


remove_action(
    'woocommerce_checkout_billing',
    array(WC()->checkout(), 'checkout_form_billing')
);



add_action('woocommerce_checkout_billing', function () {

    $checkout = WC()->checkout();
    $fields = $checkout->get_checkout_fields();

    echo '<div class="my-billing-wrapper">';

    echo '<h2 class="title">Личные данные </h2>';

    // 👤 BILLING
    foreach ($fields['billing'] as $key => $field) {

        // добавляем свой класс к input
        $field['input_class'][] = 'my-input';
        $field['class'][] = 'my-field-wrapper';

        woocommerce_form_field(
            $key,
            $field,
            $checkout->get_value($key)
        );
    }


    // ⚙️ ACCOUNT (если включено)
    if (!empty($fields['account'])) {
        echo '<h2 class="title">Доствка  </h2>';
        foreach ($fields['account'] as $key => $field) {

            // добавляем свой класс к input
            $field['input_class'][] = 'my-input';
            $field['class'][] = 'my-field-wrapper';


            woocommerce_form_field(
                $key,
                $field,
                $checkout->get_value($key)
            );
        }
    }

    // 🧾 ORDER COMMENTS
    if (!empty($fields['order']['order_comments'])) {
        echo '<h2 class="title">Коментарий</h2>';


        // добавляем свой класс к input
        $field['input_class'][] = 'my-input';
        $field['class'][] = 'my-field-wrapper';


        woocommerce_form_field(
            'order_comments',
            $fields['order']['order_comments'],
            $checkout->get_value('order_comments')
        );
    }

    echo '</div>';
}, 10);




add_action('woocommerce_checkout_payment', function () {
    echo '<div class="my-payment">Мой блок оплаты</div>';
}, 10);
