<?php
/**
 * Minimal theme settings screen.
 *
 * Lets the client point the Contact page at a WPForms form (recommended,
 * see README.md) without touching template code. If no WPForms form ID is
 * set, the Contact page falls back to the built-in styled form in
 * inc/contact-form.php, which emails the address below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'MR_CONTACT_EMAIL' ) ) {
	define( 'MR_CONTACT_EMAIL', 'ian@ianmaciver.com' );
}

function mr_add_settings_page() {
	add_options_page(
		__( 'Maggie Rowan Theme', 'maggie-rowan' ),
		__( 'Maggie Rowan Theme', 'maggie-rowan' ),
		'manage_options',
		'mr-theme-settings',
		'mr_render_settings_page'
	);
}
add_action( 'admin_menu', 'mr_add_settings_page' );

function mr_register_settings() {
	register_setting( 'mr_theme_settings', 'mr_wpforms_id', array( 'sanitize_callback' => 'sanitize_text_field' ) );
}
add_action( 'admin_init', 'mr_register_settings' );

function mr_render_settings_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Maggie Rowan Theme Settings', 'maggie-rowan' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'mr_theme_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="mr_wpforms_id"><?php esc_html_e( 'WPForms Form ID', 'maggie-rowan' ); ?></label></th>
					<td>
						<input type="text" id="mr_wpforms_id" name="mr_wpforms_id" class="regular-text" value="<?php echo esc_attr( get_option( 'mr_wpforms_id' ) ); ?>" placeholder="e.g. 123" />
						<p class="description">
							<?php esc_html_e( 'Optional. If WPForms is installed, create a form with Name / Email / Subject / Message and set its notification email to', 'maggie-rowan' ); ?>
							<code><?php echo esc_html( MR_CONTACT_EMAIL ); ?></code>.
							<?php esc_html_e( 'Paste that form\'s ID here and the Contact page will use it automatically. Leave blank to use the built-in contact form instead. See README.md for the full walkthrough.', 'maggie-rowan' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
