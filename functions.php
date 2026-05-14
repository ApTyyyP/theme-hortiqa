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








/* if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Дебаг(аякс) categoryId
    var_dump($_POST['categoryId']); 
    exit; // останавливаем, чтобы сразу увидеть
} */
