<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>





<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<?php global $geniuscourses_options; ?>



	<header class="header">
		<!-- Navbar -->
		<div class="header__desktop">

			<div class="header__wrapper">
				<div class="wrapper">
					<div class="navbar">
						<div class="navbar__wrapper">
							<div class="logo">

								<?php if (function_exists('has_custom_logo')) { ?>
									<a href="<?php echo esc_url(home_url("/")); ?>"> <? the_custom_logo(); ?> </a>
								<? } ?>

							</div>




							<button class="burger">
								<div class="burger__box">
									<span></span>
								</div>
								<div class="burger__content">Catalog</div>
							</button>
						</div>
						<nav class="nav__menu">
							<ul class="menu">
								<?php
								wp_nav_menu(array(
									'theme_location'  => 'header_nav',
									'container'      => false,
									'items_wrap'     => '%3$s',
								));
								?>
							</ul>
						</nav>

						<div class="header__inner">

							<a class="header__tell" href="tel:+380631298869">
								<svg class="header__tell-icon">
									<use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/img/svg/tell.svg#tell"></use>
								</svg> <span>380 (63) 129-88-69</span></a>


							<div class="header__content">

								<a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="header__account account header__box">
									<svg class="user__svg">
										<use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/img/svg/login.svg#login"></use>
									</svg>

								</a>

								<a href="/wishlist" class="header__wishlist wishlist header__box">

									<svg class="wishlist__svg">
										<use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/img/svg/wishlist.svg#wishlist"></use>
									</svg>
									<span class="wishlist__number user-nav__like wishlist_products_counter_number">0</span>

								</a>

								<div class="header__cart cart header__box cart-user">
									<svg class="cart__svg">

										<use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/img/svg/basket.svg#basket"></use>
									</svg>
									<span class="cart__number cart__quantity-product"> <?php echo WC()->cart->get_cart_contents_count(); ?></span>
								</div>

							</div>


							<div class="header__languages languages">
								<a href="#" class="languages__link active">UA</a>
								<a href="#" class="languages__link">RU</a>
							</div>
						</div>
					</div>
				</div>

				<!-- catalog -->

				<nav class="catalog">
					<div class="wrapper">
						<div class="catalog__wrapper">
							<div class="catalog__content">
								<h3 class="catalog__title">Каталог товарів</h3>

								<div class="catalog__wrapper-conntent">

									<!-- megamenu -->
									<ul class="megamenu megamenu__one">
										<?php
										$categories = get_terms([
											'taxonomy' => 'product_cat',
											'hide_empty' => false,
											'parent' => 0,
										]);

										if (!empty($categories) && !is_wp_error($categories)) {
											foreach ($categories as $category) :
												$thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
												$image = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
										?>
												<li class="megamenu__item" data-cat-id="<?php echo esc_attr($category->term_id); ?>">
													<a class="megamenu__item-link lvl-1" href="<?php echo esc_url(get_term_link($category)); ?>">
														<?php if ($image): ?>
															<img class="megamenu__image" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($category->name); ?>">
														<?php endif; ?>
														<div class="megamenu__box-lvl-1">
															<div class="megamenu__item-name"><?php echo esc_html($category->name); ?></div>
															<span class="megamenu__arrow"></span>
														</div>
													</a>

													<!-- Подкатегории -->
													<?php
													$subcategories = get_terms([
														'taxonomy'   => 'product_cat',
														'parent'     => $category->term_id,
														'hide_empty' => false,
													]);

													if (!empty($subcategories) && !is_wp_error($subcategories)) :
													?>
														<ul class="megamenu megamenu__two">
															<?php foreach ($subcategories as $subcategory) : ?>
																<li class="megamenu__item" data-cat-id="<?php echo esc_attr($category->term_id); ?>" data-subcat-id="<?php echo esc_attr($subcategory->term_id); ?>">
																	<a class="megamenu__item-link lvl-2" href="<?php echo esc_url(get_term_link($subcategory)); ?>">
																		<div class="megamenu__item-name"><?php echo esc_html($subcategory->name); ?></div>
																		<span class="megamenu__arrow"></span>
																	</a>

																	<!-- Товары под подкатегорией -->
																	<?php
																	$products = wc_get_products([
																		'category' => [$subcategory->slug],
																		'limit' => -1,
																		'status' => 'publish',
																		'include_children' => false, // только товары текущей подкатегории
																	]);

																	if (!empty($products)) :
																	?>
																		<ul class="megamenu megamenu__three">
																			<?php foreach ($products as $product) : ?>
																				<li class="megamenu__item" data-cat-id="<?php echo esc_attr($category->term_id); ?>" data-subcat-id="<?php echo esc_attr($subcategory->term_id); ?>">
																					<a class="megamenu__item-link lvl-3" href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
																						<div class="megamenu__item-name"><?php echo esc_html($product->get_name()); ?></div>
																					</a>
																				</li>
																			<?php endforeach; ?>
																		</ul>
																	<?php endif; ?>
																</li>
															<?php endforeach; ?>
														</ul>
													<?php endif; ?>
												</li>
											<?php endforeach; ?>
									</ul>

								<? } ?>
								</div>
							</div>
						</div>
					</div>
				</nav>







			</div>








		</div>

		<div class="header__mobile">
			<div class="wrapper">
				<div class="navbar">
					<div class="header__wrapper">
						<div class="header__detals">
							<div class="logo">

								<?php if (function_exists('has_custom_logo')) { ?>
									<a href="<?php echo esc_url(home_url("/")); ?>"> <? the_custom_logo(); ?> </a>
								<? } ?>

							</div>
							<button class="burger burger__mobile-btn">
								<div class="burger__box">
									<span></span>
								</div>
								<div class="burger__content">Каталог товарів</div>
							</button>


						</div>

						<div class="header__detals">


							<div class="header__account account header__box">
								<svg class="user__svg">
									<use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/img/svg/login.svg#login"></use>
								</svg>
							</div>


							<div class="header__wishlist wishlist header__box">
								<a href="/wishlist">
									<svg class="wishlist__svg">
										<use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/img/svg/wishlist.svg#wishlist"></use>
									</svg>
									<span class="wishlist__number user-nav__like wishlist_products_counter_number">0</span>
								</a>
							</div>

							<div class="header__cart cart header__box cart-user">
								<svg class="cart__svg">
									<use xlink:href="<?php echo get_template_directory_uri(); ?>/assets/img/svg/basket.svg#basket"></use>
								</svg>
								<span class="cart__number cart__quantity-product"> <?php echo WC()->cart->get_cart_contents_count(); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="burger-mobile">
			<div class="burger-mobile__box">
				<div class="logo">

					<?php if (function_exists('has_custom_logo')) { ?>
						<a href="<?php echo esc_url(home_url("/")); ?>"> <? the_custom_logo(); ?> </a>
					<? } ?>

				</div>
				<div class="burger-mobile__close">
					<?php
					echo file_get_contents(
						get_template_directory() . '/assets/img/svg/close.svg'
					);
					?>

				</div>

			</div>






			<nav class="mobilemenu">
				<div class="wrapper">



					<ul class="mobilemenu__items">
						<?php
						$categories = get_terms([
							'taxonomy' => 'product_cat',
							'hide_empty' => false,
							'parent' => 0,
						]);

						if (!empty($categories) && !is_wp_error($categories)) {
							foreach ($categories as $category) :
								$thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
								$image = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
						?>
								<li class="mobilemenu__item mobilemenu__item-one" data-cat-id="<?php echo esc_attr($category->term_id); ?>">
									<a class="mobilemenu__link" href="<?php echo esc_url(get_term_link($category)); ?>">
										<?php if ($image): ?>
											<img class="mobilemenu__image" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($category->name); ?>">
										<?php endif; ?>
										<div class="mobilemenu__box">
											<div class="mobilemenu__name"><?php echo esc_html($category->name); ?></div>
											<span class="mobilemenu__arrow"></span>
										</div>
									</a>

									<!-- Подкатегории -->
									<?php
									$subcategories = get_terms([
										'taxonomy'   => 'product_cat',
										'parent'     => $category->term_id,
										'hide_empty' => false,
									]);

									if (!empty($subcategories) && !is_wp_error($subcategories)) :
									?>
										<ul class="mobilemenu__two">
											<?php foreach ($subcategories as $subcategory) : ?>
												<li class="mobilemenu__item" data-cat-id="<?php echo esc_attr($category->term_id); ?>" data-subcat-id="<?php echo esc_attr($subcategory->term_id); ?>">
													<a class="mobilemenu__link" href="<?php echo esc_url(get_term_link($subcategory)); ?>">
														<div class="mobilemenu__name"><?php echo esc_html($subcategory->name); ?></div>
														<span class="mobilemenu__arrow"></span>
													</a>

												</li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
					</ul>

				<? } ?>


				</div>
			</nav>




			<?php $locations = get_nav_menu_locations();
			$menu_id = $locations['header_nav'] ?? null;

			if ($menu_id):
				$menu_items = wp_get_nav_menu_items($menu_id);
			?>
				<ul class="burger-mobile__nav">
					<?php foreach ($menu_items as $item): ?>
						<?php if ($item->menu_item_parent == 0): ?>
							<li class="burger-mobile__item">
								<div class="burger-mobile__content">
									<a
										class="burger-mobile__link"
										href="<?php echo esc_url($item->url); ?>">
										<?php echo esc_html($item->title); ?>
									</a>
								</div>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="wrapper">
				<div class="burger-mobile__wrapper">
					<a class="header__tel" href="tel:+380631298869">380 (63) 129-88-69</a>

					<div class="header__languages languages">
						<a href="#" class="languages__link active">UA</a>
						<a href="#" class="languages__link">RU</a>
					</div>
				</div>


			</div>
		</div>
	</header>
