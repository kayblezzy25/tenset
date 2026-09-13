<?php
/**
 * Fallback template (blog index / search / 404).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( null, array( 'style' => 'static' ) );
?>

<main class="mr-content-page mr-container">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<h1><?php esc_html_e( 'Nothing found', 'maggie-rowan' ); ?></h1>
		<p><?php esc_html_e( 'Try heading back to the homepage.', 'maggie-rowan' ); ?></p>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
