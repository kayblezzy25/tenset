<?php
/**
 * Default page template -- used for Privacy Policy and any other plain page
 * that isn't assigned one of the dedicated templates in /page-templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( null, array( 'style' => 'transparent' ) );
?>

<main>
	<div class="mr-page-hero" style="background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/hero-photo.jpg' ) ); ?>');">
		<div class="mr-container mr-page-hero-content">
			<span class="mr-eyebrow"><?php esc_html_e( 'Maggie Rowan', 'maggie-rowan' ); ?></span>
			<h1 class="mr-h2"><?php the_title(); ?></h1>
		</div>
	</div>

	<div class="mr-content-page mr-container">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
</main>

<?php get_footer(); ?>
