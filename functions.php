<?php

/* inc */
require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/image.php';
require get_template_directory() . '/inc/underscores.php';
require get_template_directory() . '/inc/global-acf.php';
require get_template_directory() . '/inc/arrow.php';
require get_template_directory() . '/inc/blog_filter.php';
require get_template_directory() . '/inc/wishlist.php';


require get_template_directory() . '/inc/header.php';
require get_template_directory() . '/inc/footer.php';
require get_template_directory() . '/inc/woocommerce.php';
require get_template_directory() . '/inc/breadcrumbs.php';







function project_scripts()
{
	/* fancybox */
	wp_enqueue_style(
		'fancybox-css',
		'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css',
		array(),
		'5.0.36'
	);

	wp_enqueue_script(
		'fancybox-js',
		'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.umd.js',
		array(),
		'5.0.36',
		true
	);

	wp_enqueue_style(
		'style',
		get_template_directory_uri() . '/assets/css/style.css',
		array(),
		'1.0',
		'all'
	);



	wp_enqueue_script(
		'swiper',
		get_template_directory_uri() . '/assets/js/libs/swiper.js',
		array('jquery'),
		'1.0',
		true
	);

	wp_enqueue_script(
		'mask',
		get_template_directory_uri() . '/assets/js/libs/mask.js',
		array('jquery'),
		'1.0',
		true
	);



	wp_enqueue_script(
		'script',
		get_template_directory_uri() . '/assets/js/main.js',
		array('jquery'),
		'1.0',
		true
	);

	/* appi */
	wp_localize_script('script', 'wpApiSettings', [
		'root' => esc_url(rest_url()),
		'nonce' => wp_create_nonce('wp_rest'),
	]);


	/* admin-ajax */
	wp_localize_script('script', 'my_ajax_obj', [
		'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('nonceToken')
	]);

	/* валюта сайта */
	wp_localize_script('script', 'wcData', [
		'currencySymbol' => get_woocommerce_currency_symbol(),
		'currencyCode'   => get_woocommerce_currency(),
		'shopUrl'        => wc_get_page_permalink('shop'),
		'cartUrl'        => wc_get_cart_url(),
		'checkoutUrl'    => wc_get_checkout_url(),
	]);
}
add_action('wp_enqueue_scripts', 'project_scripts');









add_action('acf/init', 'register_acf_blocks');
function register_acf_blocks()
{

	if (function_exists('acf_register_block_type')) {

		acf_register_block_type([
			'name'              => 'Flexible Block',
			'title'             => __('Flexible Block poduct'),
			'description'       => __('Блок с Flexible Content внутри'),
			'render_template' => get_template_directory() . '/inc/template/flexible-blog-product.php',
			'category'          => 'formatting',
			'icon'              => 'admin-comments',
			'mode'              => 'edit',
		]);
	}
}










/* setcookie('site_guard', '1', 0, '/');

add_action('init', function () {

	// если куки нет → считаем сессию сломанной
	if (!isset($_COOKIE['site_guard'])) {

		// чистим куки (только для текущего домена/пути)
		foreach ($_COOKIE as $name => $value) {
			setcookie($name, '', 0, '/');
			unset($_COOKIE[$name]);
		}

		wp_logout();
	}

	// всегда восстанавливаем маячок
	setcookie('site_guard', '1', 0, '/');
}); */









add_filter('woocommerce_registration_generate_password', '__return_false');
add_filter('woocommerce_registration_generate_username', '__return_false');

/**
 * Checks whether URL host points to local machine.
 *
 * @param string $url URL to check.
 * @return bool
 */
function hortiqa_is_local_url($url)
{
	$host = wp_parse_url($url, PHP_URL_HOST);
	if (!is_string($host) || $host === '') {
		return false;
	}

	$host = strtolower($host);
	return in_array($host, array('localhost', '127.0.0.1', '::1'), true);
}

/**
 * Returns public base URL for payment callbacks (if configured).
 * Use env/constant LIQPAY_PUBLIC_BASE_URL, e.g. https://my-test-domain.ngrok-free.app
 *
 * @return string
 */
function hortiqa_get_liqpay_public_base_url()
{
	$candidates = array();

	if (defined('LIQPAY_PUBLIC_BASE_URL')) {
		$candidates[] = LIQPAY_PUBLIC_BASE_URL;
	}

	$env_url = getenv('LIQPAY_PUBLIC_BASE_URL');
	if (is_string($env_url) && $env_url !== '') {
		$candidates[] = $env_url;
	}

	foreach ($candidates as $candidate) {
		$candidate = trim((string) $candidate);
		if ($candidate === '') {
			continue;
		}

		if (!preg_match('#^https?://#i', $candidate)) {
			$candidate = 'https://' . ltrim($candidate, '/');
		}

		$normalized = esc_url_raw($candidate);
		if ($normalized) {
			return untrailingslashit($normalized);
		}
	}

	return '';
}

/**
 * Adjust LiqPay request in local Docker setups.
 */
add_filter('wc_liqpay_request_filter', function ($params, $order) {
	$public_base_url = hortiqa_get_liqpay_public_base_url();

	if ($public_base_url !== '') {
		$params['result_url'] = add_query_arg(
			array(
				'wc-api'   => 'liqpay_return',
				'order_id' => $order->get_id(),
			),
			trailingslashit($public_base_url)
		);
		$params['server_url'] = trailingslashit($public_base_url) . 'wc-api/wc_gateway_kmnd_liqpay/';
	}

	$liqpay_settings = get_option('woocommerce_liqpay_settings', array());
	$public_key = is_array($liqpay_settings) && !empty($liqpay_settings['public_key'])
		? (string) $liqpay_settings['public_key']
		: '';

	if (
		empty($params['sandbox']) &&
		$public_key !== '' &&
		strpos($public_key, 'sandbox_') === 0
	) {
		$params['sandbox'] = 1;
	}

	if (
		defined('WP_DEBUG') &&
		WP_DEBUG &&
		(
			(isset($params['result_url']) && hortiqa_is_local_url($params['result_url'])) ||
			(isset($params['server_url']) && hortiqa_is_local_url($params['server_url']))
		)
	) {
		error_log('LiqPay: result_url/server_url point to localhost. Set LIQPAY_PUBLIC_BASE_URL to a public HTTPS domain.');
	}

	return $params;
}, 10, 2);

/**
 * Get runtime config value from constant or environment.
 *
 * @param string $key Config key.
 * @param string $default Default value.
 * @return string
 */
function hortiqa_runtime_value($key, $default = '')
{
	if (defined($key)) {
		$value = constant($key);
		return is_scalar($value) ? (string) $value : $default;
	}

	$value = getenv($key);
	if ($value === false || $value === null) {
		return $default;
	}

	$value = trim((string) $value);
	return $value !== '' ? $value : $default;
}

/**
 * Parse bool-like runtime value.
 *
 * @param string $key Config key.
 * @param bool $default Default value.
 * @return bool
 */
function hortiqa_runtime_bool($key, $default = false)
{
	$value = strtolower(hortiqa_runtime_value($key, $default ? '1' : '0'));
	return in_array($value, array('1', 'true', 'yes', 'on'), true);
}

/**
 * Configure wp_mail transport via runtime SMTP settings.
 * Useful when PHP mail() is disabled by hosting.
 */
add_action('phpmailer_init', function ($phpmailer) {
	$smtp_host = hortiqa_runtime_value('WP_SMTP_HOST');

	if ($smtp_host !== '') {
		$phpmailer->isSMTP();
		$phpmailer->Host = $smtp_host;

		$port = (int) hortiqa_runtime_value('WP_SMTP_PORT', '587');
		$phpmailer->Port = $port > 0 ? $port : 587;

		$smtp_auth = hortiqa_runtime_bool('WP_SMTP_AUTH', true);
		$smtp_user = hortiqa_runtime_value('WP_SMTP_USER');
		$smtp_pass = hortiqa_runtime_value('WP_SMTP_PASS');

		$phpmailer->SMTPAuth = $smtp_auth;
		$phpmailer->Username = $smtp_user;
		$phpmailer->Password = $smtp_pass;

		$secure = strtolower(hortiqa_runtime_value('WP_SMTP_SECURE', 'tls'));
		if ($secure === 'ssl' || $secure === 'tls') {
			$phpmailer->SMTPSecure = $secure;
		} else {
			$phpmailer->SMTPSecure = '';
			$phpmailer->SMTPAutoTLS = false;
		}

		$timeout = (int) hortiqa_runtime_value('WP_SMTP_TIMEOUT', '15');
		if ($timeout > 0) {
			$phpmailer->Timeout = $timeout;
		}

		$from = hortiqa_runtime_value('WP_SMTP_FROM');
		if ($from !== '' && is_email($from)) {
			$from_name = hortiqa_runtime_value('WP_SMTP_FROM_NAME', get_bloginfo('name'));
			$phpmailer->setFrom($from, $from_name, false);
		}
	}
}, 10, 1);

/**
 * Last-resort guard: do not call wp_mail() through PHP mail() if mail() is unavailable.
 * Prevents checkout/profile fatal errors on hosts with disabled mail() function.
 */
add_filter('pre_wp_mail', function ($pre_wp_mail) {
	if ($pre_wp_mail !== null) {
		return $pre_wp_mail;
	}

	$smtp_host = hortiqa_runtime_value('WP_SMTP_HOST');
	if ($smtp_host === '' && !function_exists('mail')) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('wp_mail skipped: mail() is unavailable and WP_SMTP_HOST is not configured.');
		}
		return false;
	}

	return null;
}, 10, 1);
