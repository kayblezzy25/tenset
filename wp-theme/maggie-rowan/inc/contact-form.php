<?php
/**
 * Built-in fallback contact form.
 *
 * Used automatically on the Contact page whenever no WPForms form ID has
 * been set under Settings > Maggie Rowan Theme. Submits via admin-post.php,
 * emails MR_CONTACT_EMAIL (never printed on the front end), and redirects
 * back to the Contact page with a success flag.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mr_handle_contact_submit() {
	if ( ! isset( $_POST['mr_contact_nonce'] ) || ! wp_verify_nonce( $_POST['mr_contact_nonce'], 'mr_contact_submit' ) ) {
		wp_die( esc_html__( 'Security check failed. Please go back and try again.', 'maggie-rowan' ) );
	}

	// Honeypot: real visitors never fill this hidden field in.
	if ( ! empty( $_POST['mr_contact_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'mr_contact', 'sent', wp_get_referer() ?: home_url( '/contact/' ) ) );
		exit;
	}

	$name    = isset( $_POST['mr_contact_name'] ) ? sanitize_text_field( $_POST['mr_contact_name'] ) : '';
	$email   = isset( $_POST['mr_contact_email'] ) ? sanitize_email( $_POST['mr_contact_email'] ) : '';
	$subject = isset( $_POST['mr_contact_subject'] ) ? sanitize_text_field( $_POST['mr_contact_subject'] ) : '';
	$message = isset( $_POST['mr_contact_message'] ) ? sanitize_textarea_field( $_POST['mr_contact_message'] ) : '';

	$redirect_url = wp_get_referer() ?: home_url( '/contact/' );

	if ( ! $name || ! is_email( $email ) || ! $message ) {
		wp_safe_redirect( add_query_arg( 'mr_contact', 'error', $redirect_url ) );
		exit;
	}

	$to      = MR_CONTACT_EMAIL;
	$subject_line = sprintf( '[Maggie Rowan site] %s', $subject ? $subject : 'New enquiry' );
	$body    = "You have a new enquiry from the Maggie Rowan website:\n\n"
		. "Name: {$name}\n"
		. "Email: {$email}\n"
		. "Subject: {$subject}\n\n"
		. "Message:\n{$message}\n";
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject_line, $body, $headers );

	wp_safe_redirect( add_query_arg( 'mr_contact', $sent ? 'sent' : 'error', $redirect_url ) );
	exit;
}
add_action( 'admin_post_nopriv_mr_contact_submit', 'mr_handle_contact_submit' );
add_action( 'admin_post_mr_contact_submit', 'mr_handle_contact_submit' );

/**
 * Outputs either the configured WPForms shortcode or the built-in form.
 */
function mr_render_contact_form() {
	$wpforms_id = trim( (string) get_option( 'mr_wpforms_id' ) );

	if ( $wpforms_id && shortcode_exists( 'wpforms' ) ) {
		echo do_shortcode( '[wpforms id="' . esc_attr( $wpforms_id ) . '"]' );
		return;
	}

	$status = isset( $_GET['mr_contact'] ) ? sanitize_text_field( $_GET['mr_contact'] ) : '';
	?>
	<div class="mr-form-wrap">
		<?php if ( 'sent' === $status ) : ?>
			<div class="mr-form-success"><?php esc_html_e( 'Thanks -- your message has been sent. Maggie\'s team will be in touch soon.', 'maggie-rowan' ); ?></div>
		<?php elseif ( 'error' === $status ) : ?>
			<div class="mr-form-success" style="border-color:#8B5A3C;"><?php esc_html_e( 'Something went wrong sending your message. Please check the fields and try again.', 'maggie-rowan' ); ?></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mr_contact_submit" />
			<?php wp_nonce_field( 'mr_contact_submit', 'mr_contact_nonce' ); ?>
			<input type="text" name="mr_contact_website" value="" autocomplete="off" tabindex="-1" style="position:absolute;left:-9999px;" aria-hidden="true" />

			<div class="mr-form-row">
				<div class="mr-field">
					<label for="mr_contact_name"><?php esc_html_e( 'Name *', 'maggie-rowan' ); ?></label>
					<input type="text" id="mr_contact_name" name="mr_contact_name" placeholder="<?php esc_attr_e( 'Your name', 'maggie-rowan' ); ?>" required />
				</div>
				<div class="mr-field">
					<label for="mr_contact_email"><?php esc_html_e( 'Email *', 'maggie-rowan' ); ?></label>
					<input type="email" id="mr_contact_email" name="mr_contact_email" placeholder="<?php esc_attr_e( 'Your email', 'maggie-rowan' ); ?>" required />
				</div>
			</div>

			<div class="mr-field">
				<label for="mr_contact_subject"><?php esc_html_e( 'Subject *', 'maggie-rowan' ); ?></label>
				<select id="mr_contact_subject" name="mr_contact_subject" required>
					<option value=""><?php esc_html_e( 'Select a subject', 'maggie-rowan' ); ?></option>
					<option value="Booking enquiry"><?php esc_html_e( 'Booking enquiry', 'maggie-rowan' ); ?></option>
					<option value="Press / media"><?php esc_html_e( 'Press / media', 'maggie-rowan' ); ?></option>
					<option value="Collaboration"><?php esc_html_e( 'Collaboration', 'maggie-rowan' ); ?></option>
					<option value="General enquiry"><?php esc_html_e( 'General enquiry', 'maggie-rowan' ); ?></option>
				</select>
			</div>

			<div class="mr-field">
				<label for="mr_contact_message"><?php esc_html_e( 'Message *', 'maggie-rowan' ); ?></label>
				<textarea id="mr_contact_message" name="mr_contact_message" placeholder="<?php esc_attr_e( 'Your message', 'maggie-rowan' ); ?>" required></textarea>
			</div>

			<button type="submit" class="mr-btn mr-btn-rust mr-form-submit"><?php esc_html_e( 'Send Message', 'maggie-rowan' ); ?></button>
			<p class="mr-form-note"><?php esc_html_e( 'Your enquiry goes straight to Maggie\'s team -- we don\'t share your details with anyone else.', 'maggie-rowan' ); ?></p>
		</form>
	</div>
	<?php
}
