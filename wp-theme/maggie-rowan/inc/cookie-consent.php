<?php
/**
 * Lightweight, dependency-free cookie consent banner.
 * Stores the visitor's choice in localStorage (see assets/js/main.js).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mr_render_cookie_banner() {
	$privacy_url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
	if ( ! $privacy_url ) {
		$privacy_url = home_url( '/privacy-policy/' );
	}
	?>
	<div class="mr-cookie-banner" id="mr-cookie-banner" role="dialog" aria-live="polite" aria-label="<?php esc_attr_e( 'Cookie notice', 'maggie-rowan' ); ?>">
		<p><?php esc_html_e( 'We use cookies to ensure you get the best experience on our website. By continuing to browse, you agree to our use of cookies.', 'maggie-rowan' ); ?></p>
		<div class="mr-cookie-actions">
			<button type="button" class="mr-cookie-accept" id="mr-cookie-accept"><?php esc_html_e( 'Accept', 'maggie-rowan' ); ?></button>
			<a href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Learn more', 'maggie-rowan' ); ?></a>
		</div>
	</div>
	<?php
}
