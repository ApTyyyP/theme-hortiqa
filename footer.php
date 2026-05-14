<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package knives
 */

?>
<!-- Footer -->
<footer class="footer">



	<div class="wrapper">
		<div class="footer__content">

			<div class="footer__feedback">
				<div class="logo">

					<?php
					$footer_logo = get_theme_mod('footer_logo_setting');
					if ($footer_logo) {
						echo '<a href="' . esc_url(home_url('/')) . '"><img src="' . esc_url($footer_logo) . '" alt="' . get_bloginfo('name') . '"></a>';
					}
					?>
				</div>

				<h1 class="footer__title">Grow With Us</h1>
				<div class="footer__sub-title">Get exclusive offers, seasonal care tips & inspiration</div>
				<form class="email" action="#">
					<input class="email__input" placeholder="Enter your email here" type="text">
					<button class="email__btn">Join & Get 10% Off</button>
				</form>
				<a class="footer__club" href="#">Earn points with every purchase – Join our loyalty club</a>
			</div>



			<div class="footer__wrapper">


				<div class="footer__detals-box">
					<h4 class="footer__page">Shop</h4>

					<ul class="footer__items">
						<?php
						$categories = get_terms([
							'taxonomy'   => 'product_cat',
							'hide_empty' => false,
							'parent'     => 0,
						]);


						foreach ($categories as $category) { ?>
							<li class="footer__item">
								<a class="footer__link"
									href="<?php echo esc_url(get_term_link($category)); ?>">
									<?php echo esc_html($category->name); ?>
								</a>
							</li>
						<?php
						} ?>


					</ul>
				</div>



				<div class="footer__detals-box">
					<h4 class="footer__page">Services</h4>
					<ul class="footer__items">
						<li class="footer__item">
							<a class="footer__link" href="#">Rental</a>

						</li>
						<li class="footer__item">
							<a class="footer__link" href="#">Landscaping</a>

						</li>
						<li class="footer__item">
							<a class="footer__link" href="#">Corporate</a>

						</li>


					</ul>
				</div>


				<div class="footer__detals-box">
					<h4 class="footer__page">Inspiration</h4>
					<ul class="footer__items">
						<li class="footer__item">
							<a class="footer__link" href="#">Blog</a>
						</li>
						<li class="footer__item">
							<a class="footer__link" href="#">Video Guides</a>
						</li>
						<li class="footer__item">
							<a class="footer__link" href="#">Plant Finder</a>
						</li>





					</ul>
				</div>


				<div class="footer__detals-box">
					<h4 class="footer__page">Support</h4>
					<ul class="footer__items">

						<a class="footer__link" href="#">FAQ</a>
						</li>
						<li class="footer__item">
							<a class="footer__link" href="#">Contact</a>
						</li>
						<li class="footer__item">
							<a class="footer__link" href="#">Shipping & Returns</a>
						</li>
						<li class="footer__item">
							<a class="footer__link" href="#">Loyalty</a>
						</li>
					</ul>
				</div>




				<div class="footer__detals-box footer__social">
					<h4 class="footer__page">Social</h4>

					<ul class="footer__social-items">
						<li class="footer__social-item"><a class="footer__social-link" href="#"> <?= file_get_contents(get_template_directory() . '/assets/img/svg/telephone.svg');	?></a></li>
						<li class="footer__social-item"><a class="footer__social-link" href="#"> <?= file_get_contents(get_template_directory() . '/assets/img/svg/telegram.svg');	?></a></li>
						<li class="footer__social-item"><a class="footer__social-link" href="#"> <?= file_get_contents(get_template_directory() . '/assets/img/svg/instagram.svg');	?></a></li>





					</ul>
				</div>



			</div>
		</div>





	</div>

</footer>




<?php wp_footer(); ?>

<?php cart(); ?>

</body>

</html>