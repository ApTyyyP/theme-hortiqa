<?php

/**
 * Login / Register Form (custom UI safe WooCommerce template)
 */

if (! defined('ABSPATH')) {
	exit;
}

do_action('woocommerce_before_customer_login_form');

$registration_enabled = 'yes' === get_option('woocommerce_enable_myaccount_registration');
?>

<section class="account">
	<div class="wrapper">

		<div class="auth-wrapper">

			<!-- LOGIN -->
			<div class="auth-block auth-login active">

				<h2 class="title fw700 ts-40">Log into your account</h2>

				<form class="woocommerce-form woocommerce-form-login login" method="post">

					<?php do_action('woocommerce_login_form_start'); ?>

					<p class="form-row">
						<input
							type="text"
							name="username"
							id="username"
							placeholder="Вкажіть ваш логін"
							autocomplete="username"
							required />
					</p>

					<p class="form-row">
						<input
							type="password"
							name="password"
							id="password"
							placeholder="Your password"
							autocomplete="current-password"
							required />
					</p>

					<?php do_action('woocommerce_login_form'); ?>

					<p class="lost-password">
						<a href="<?php echo esc_url(wp_lostpassword_url()); ?>">
							Forgot Password?
						</a>
					</p>

					<?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>

					<button type="submit" name="login" class="button">
						Login
					</button>

					<?php do_action('woocommerce_login_form_end'); ?>

				</form>

				<?php if ($registration_enabled) : ?>
					<div class="switch">
						Don’t have an account?
						<a href="#" class="js-show-register">Create one</a>
					</div>
				<?php endif; ?>

				

			</div>

			<!-- REGISTER -->
			<?php if ($registration_enabled) : ?>

				<div class="auth-block auth-register">

					<h2 class="title fw700 ts-40">Create an account</h2>

					<form method="post" class="woocommerce-form woocommerce-form-register register">

						<?php do_action('woocommerce_register_form_start'); ?>




						<!-- EMAIL (ВСЕГДА) -->
						<p class="form-row">
							<input
								type="email"
								name="email"
								id="reg_email"
								placeholder="Email"
								required />
						</p>


						<!-- USERNAME (ВСЕГДА) -->
						<p class="form-row">
							<input
								type="text"
								name="username"
								id="reg_username"
								placeholder="Username"
								required />
						</p>

						<!-- PASSWORD (ВСЕГДА) -->
						<p class="form-row">
							<input
								type="password"
								name="password"
								id="reg_password"
								placeholder="Password"
								required />
						</p>

						<?php do_action('woocommerce_register_form'); ?>

						<!-- CHECKBOX -->
						<p class="checkbox">
							<label>
								<input type="checkbox" name="receive" value="1">
							</label>
						</p>

						<?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>

						<button type="submit" name="register" class="button">
							Register
						</button>

						<?php do_action('woocommerce_register_form_end'); ?>

					</form>

					<div class="switch">
						Already have an account?
						<a href="#" class="js-show-login">Log in</a>
					</div>

				</div>

			<?php endif; ?>

		</div>
	</div>
</section>

<?php do_action('woocommerce_after_customer_login_form'); ?>