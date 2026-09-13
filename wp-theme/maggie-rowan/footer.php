<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<footer class="mr-footer">
		<div class="mr-container">
			<div class="mr-footer-inner">
				<a class="mr-footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>

				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'mr-footer-nav',
					) );
					?>
				<?php else : ?>
					<ul class="mr-footer-nav">
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'maggie-rowan' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'maggie-rowan' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/music/' ) ); ?>"><?php esc_html_e( 'Music', 'maggie-rowan' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'maggie-rowan' ); ?></a></li>
					</ul>
				<?php endif; ?>
			</div>

			<div class="mr-footer-bottom">
				<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'maggie-rowan' ); ?></span>
				<div class="mr-footer-legal">
					<a href="<?php echo esc_url( function_exists( 'get_privacy_policy_url' ) && get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'maggie-rowan' ); ?></a>
					<a href="#" id="mr-manage-cookies"><?php esc_html_e( 'Cookies', 'maggie-rowan' ); ?></a>
				</div>
			</div>
		</div>
	</footer>

	<?php mr_render_cookie_banner(); ?>

	<?php wp_footer(); ?>
</body>
</html>
