<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined('ABSPATH') || exit;

$order = wc_get_order($order_id);
if (!$order) {
    return;
}

$order_id = $order->get_id();
$order_key = $order->get_order_key();
$order_total = $order->get_total();
$order_currency = $order->get_currency();
$order_date = $order->get_date_created();
$customer_note = $order->get_customer_note();
$payment_method = $order->get_payment_method_title();
$billing_email = $order->get_billing_email();
$billing_phone = $order->get_billing_phone();
$billing_first_name = $order->get_billing_first_name();
$billing_last_name = $order->get_billing_last_name();
$shipping_address = $order->get_formatted_shipping_address();
$items = $order->get_items();
$item_count = $order->get_item_count();
$order_status = $order->get_status();

do_action('woocommerce_before_thankyou', $order_id);
?>

<div class="woocommerce-order woocommerce-thankyou order-received-page">

    <?php if ($order->has_status('failed')) : ?>
        <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
            <?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?>
        </p>

        <p class="woocommerce-notice woocommerce-notice--error">
            <?php esc_html_e('Your order was not successful and has not been completed.', 'woocommerce'); ?>
        </p>

        <?php
        // Pay form for failed orders
        if (is_user_logged_in()) {
            woocommerce_order_pay($order_id);
        }
        ?>

    <?php else : ?>

        <div class="order-received__container">
            <div class="order-received__header">
                <div class="order-received__icon">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="40" cy="40" r="40" fill="#4CAF50"/>
                        <path d="M25 40L35 50L55 30" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h1 class="order-received__title">Дякуємо за ваше замовлення!</h1>
                <p class="order-received__subtitle">Ваше замовлення №<?php echo esc_html($order_id); ?> успішно оформлено</p>
            </div>

            <div class="order-received__content">
