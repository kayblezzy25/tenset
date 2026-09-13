<?php
/**
 * Template Name: About
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( null, array( 'style' => 'transparent' ) );
?>

<main>

	<!-- PAGE HERO -->
	<section class="mr-page-hero" style="background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/page-hero.svg' ) ); ?>');">
		<div class="mr-container mr-page-hero-content">
			<span class="mr-eyebrow"><?php esc_html_e( 'About Maggie', 'maggie-rowan' ); ?></span>
			<h1 class="mr-h1-sm"><?php esc_html_e( 'The story behind the music', 'maggie-rowan' ); ?></h1>
			<hr class="mr-rule" />
			<p class="mr-lead" style="color:#e7e2d6;margin-top:22px;">
				<?php esc_html_e( 'Maggie Rowan is a singer-songwriter bringing together modern Americana, country and bluegrass, with a sound rooted in real life, honest storytelling and a love of the road.', 'maggie-rowan' ); ?>
			</p>
		</div>
	</section>

	<!-- MY JOURNEY -->
	<section class="mr-section mr-bg-ivory">
		<div class="mr-container mr-split">
			<div class="mr-split-media">
				<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/portrait-1.svg' ) ); ?>" alt="<?php esc_attr_e( 'Maggie Rowan — replace with final artist photography', 'maggie-rowan' ); ?>" />
			</div>
			<div class="mr-split-body">
				<span class="mr-eyebrow"><?php esc_html_e( 'My Journey', 'maggie-rowan' ); ?></span>
				<h2 class="mr-h2"><?php esc_html_e( 'Music, places and ordinary moments', 'maggie-rowan' ); ?></h2>
				<p><?php esc_html_e( 'Maggie Rowan brings together modern Americana, country and bluegrass with songs built around journeys, places and the ordinary moments that shape a life.', 'maggie-rowan' ); ?></p>
				<p><?php esc_html_e( 'Her debut album, Traveling Back, is a ten-song collection rooted in storytelling. Roads, railways, small towns, working lives, relationships and the pull of home provide the backdrop to songs that look both forward and back.', 'maggie-rowan' ); ?></p>
				<p><?php esc_html_e( "Drawing on the traditions of American folk and country music, Maggie Rowan's sound combines acoustic instrumentation with memorable melodies and a contemporary approach to the classic story-song.", 'maggie-rowan' ); ?></p>
				<blockquote class="mr-pull-quote">
					<?php esc_html_e( '"Every journey has a story, and sometimes travelling back is the best way to understand where you\'ve been."', 'maggie-rowan' ); ?>
				</blockquote>
				<hr class="mr-rule" />
			</div>
		</div>
	</section>

	<!-- GALLERY -->
	<section class="mr-section mr-bg-white">
		<div class="mr-container">
			<div class="mr-tracklist-head">
				<div>
					<span class="mr-eyebrow"><?php esc_html_e( 'Gallery', 'maggie-rowan' ); ?></span>
					<h2 class="mr-h2" style="margin-bottom:0;"><?php esc_html_e( 'Moments from the road', 'maggie-rowan' ); ?></h2>
					<hr class="mr-rule" />
				</div>
				<p class="mr-lead" style="margin:0;"><?php esc_html_e( 'From quiet backroads to open skies, these moments inspire the songs.', 'maggie-rowan' ); ?></p>
			</div>

			<div class="mr-gallery">
				<div class="mr-gallery-main">
					<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/portrait-2.svg' ) ); ?>" alt="<?php esc_attr_e( 'Maggie Rowan performing — replace with final photography', 'maggie-rowan' ); ?>" />
				</div>
				<div class="mr-gallery-grid">
					<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/gallery-2.svg' ) ); ?>" alt="" />
					<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/gallery-1.svg' ) ); ?>" alt="" />
					<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/gallery-3.svg' ) ); ?>" alt="" />
					<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/gallery-4.svg' ) ); ?>" alt="" />
				</div>
			</div>
		</div>
	</section>

	<!-- CONTACT CTA -->
	<section class="mr-section mr-cta" style="background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/cta-bg.svg' ) ); ?>');">
		<div class="mr-container mr-cta-inner">
			<div>
				<h2 class="mr-h2"><?php esc_html_e( "Let's stay in touch", 'maggie-rowan' ); ?></h2>
				<p><?php esc_html_e( 'For enquiries.', 'maggie-rowan' ); ?></p>
				<a class="mr-btn mr-btn-rust" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Maggie', 'maggie-rowan' ); ?></a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
