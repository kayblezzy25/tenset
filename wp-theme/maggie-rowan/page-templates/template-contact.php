<?php
/**
 * Template Name: Contact
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( null, array( 'style' => 'transparent' ) );
?>

<main>

	<section class="mr-page-hero" style="min-height:44vh;background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/hero-photo.jpg' ) ); ?>');">
		<div class="mr-container mr-page-hero-content" style="padding-top:130px;">
			<span class="mr-eyebrow"><?php esc_html_e( 'Contact', 'maggie-rowan' ); ?></span>
			<h1 class="mr-h2"><?php esc_html_e( "Let's stay in touch", 'maggie-rowan' ); ?></h1>
		</div>
	</section>

	<section class="mr-section mr-bg-ivory">
		<div class="mr-container mr-contact-grid">
			<div>
				<span class="mr-eyebrow"><?php esc_html_e( 'Get In Touch', 'maggie-rowan' ); ?></span>
				<h2 class="mr-h2"><?php esc_html_e( 'For enquiries', 'maggie-rowan' ); ?></h2>
				<p class="mr-lead">
					<?php esc_html_e( 'For booking requests, press enquiries, collaborations or anything else, send Maggie a message using the form and her team will get back to you as soon as possible.', 'maggie-rowan' ); ?>
				</p>
			</div>
			<div>
				<?php mr_render_contact_form(); ?>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
