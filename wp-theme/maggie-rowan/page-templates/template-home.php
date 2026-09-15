<?php
/**
 * Template Name: Home
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( null, array( 'style' => 'transparent' ) );

$mr_album = mr_get_featured_album();
?>

<main>

	<!-- HERO -->
	<section class="mr-hero" style="background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/hero-bg.svg' ) ); ?>');">
		<div class="mr-container mr-hero-content">
			<span class="mr-eyebrow"><?php esc_html_e( 'Modern Americana · Country · Bluegrass', 'maggie-rowan' ); ?></span>
			<h1><?php bloginfo( 'name' ); ?></h1>
			<p class="mr-hero-subtext"><?php esc_html_e( 'Stories, songs and a little bit of home — wherever the road leads.', 'maggie-rowan' ); ?></p>
			<div class="mr-btn-row">
				<a class="mr-btn mr-btn-spotify" href="https://open.spotify.com/artist/01Sv95DwkMF2DjDFXOxd6R" target="_blank" rel="noopener">
					<?php esc_html_e( 'Listen on Spotify', 'maggie-rowan' ); ?>
				</a>
				<a class="mr-btn mr-btn-outline" href="https://itunes.apple.com/us/artist/maggie-rowan/6808578070" target="_blank" rel="noopener">
					<?php esc_html_e( 'Listen on iTunes', 'maggie-rowan' ); ?>
				</a>
			</div>
		</div>
	</section>

	<!-- ABOUT -->
	<section class="mr-section mr-bg-ivory">
		<div class="mr-container mr-split">
			<div class="mr-split-media">
				<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/portrait-home.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Maggie Rowan', 'maggie-rowan' ); ?>" />
			</div>
			<div class="mr-split-body">
				<span class="mr-eyebrow"><?php esc_html_e( 'About Maggie', 'maggie-rowan' ); ?></span>
				<h2 class="mr-h2"><?php esc_html_e( 'Stories inspired by the road home', 'maggie-rowan' ); ?></h2>
				<p><?php esc_html_e( 'Maggie Rowan blends modern Americana, country and bluegrass into heartfelt songs shaped by journeys, places, working lives and the ordinary moments that define us.', 'maggie-rowan' ); ?></p>
				<p><?php esc_html_e( 'Her debut album, Traveling Back, is a ten-song collection rooted in storytelling and acoustic tradition.', 'maggie-rowan' ); ?></p>
				<a class="mr-btn mr-btn-outline-dark" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'Read More →', 'maggie-rowan' ); ?></a>
			</div>
		</div>
	</section>

	<!-- FEATURED ALBUM -->
	<?php if ( $mr_album ) : ?>
	<section class="mr-section mr-bg-ivory">
		<div class="mr-container">
			<div class="mr-album-feature" style="background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/page-hero.svg' ) ); ?>');">
				<div class="mr-album-feature-inner">
					<div class="mr-album-cover">
						<?php if ( has_post_thumbnail( $mr_album ) ) : ?>
							<?php echo get_the_post_thumbnail( $mr_album, 'large' ); ?>
						<?php else : ?>
							<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/album-cover.jpg' ) ); ?>" alt="<?php echo esc_attr( get_the_title( $mr_album ) ); ?>" />
						<?php endif; ?>
					</div>
					<div>
						<span class="mr-eyebrow"><?php esc_html_e( 'Featured Album', 'maggie-rowan' ); ?></span>
						<h2 class="mr-h2"><?php echo esc_html( get_the_title( $mr_album ) ); ?></h2>
						<p><?php echo esc_html( mr_strip_content( $mr_album->post_content, 220 ) ); ?></p>
						<div class="mr-album-badges">
							<span class="mr-badge"><?php echo esc_html( count( mr_get_album_tracks( $mr_album->ID ) ) ); ?> <?php esc_html_e( 'Tracks', 'maggie-rowan' ); ?></span>
							<?php $badge = get_post_meta( $mr_album->ID, '_mr_album_badge', true ); ?>
							<?php if ( $badge ) : ?>
								<span class="mr-badge"><?php echo esc_html( $badge ); ?></span>
							<?php endif; ?>
						</div>
						<?php mr_album_link_buttons( $mr_album->ID ); ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- TRACK LIST -->
	<section class="mr-section mr-bg-ivory">
		<div class="mr-container">
			<div class="mr-tracklist-head">
				<div>
					<span class="mr-eyebrow"><?php esc_html_e( 'Popular Songs', 'maggie-rowan' ); ?></span>
					<h2 class="mr-h3" style="margin-bottom:0;"><?php printf( esc_html__( 'From %s', 'maggie-rowan' ), esc_html( get_the_title( $mr_album ) ) ); ?></h2>
				</div>
				<a class="mr-view-all" href="<?php echo esc_url( home_url( '/music/' ) ); ?>"><?php esc_html_e( 'View All Songs →', 'maggie-rowan' ); ?></a>
			</div>
			<div class="mr-tracks">
				<?php
				$tracks = array_slice( mr_get_album_tracks( $mr_album->ID ), 0, 6 );
				$spotify = get_post_meta( $mr_album->ID, '_mr_album_spotify', true );
				foreach ( $tracks as $i => $track ) :
					?>
					<div class="mr-track">
						<span class="mr-track-num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
						<div>
							<div class="mr-track-title">
								<?php if ( $spotify ) : ?>
									<a href="<?php echo esc_url( $spotify ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $track['title'] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $track['title'] ); ?>
								<?php endif; ?>
							</div>
							<div class="mr-track-album"><?php echo esc_html( get_the_title( $mr_album ) ); ?></div>
						</div>
						<span class="mr-track-time"><?php echo esc_html( $track['time'] ); ?></span>
						<?php echo mr_track_play_icon(); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- CONTACT CTA -->
	<section class="mr-section mr-cta" style="background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/cta-bg.svg' ) ); ?>');">
		<div class="mr-container mr-cta-inner">
			<div>
				<span class="mr-eyebrow"><?php esc_html_e( 'Get In Touch', 'maggie-rowan' ); ?></span>
				<h2 class="mr-h2"><?php esc_html_e( "Let's stay in touch", 'maggie-rowan' ); ?></h2>
				<p><?php esc_html_e( 'For enquiries.', 'maggie-rowan' ); ?></p>
				<a class="mr-btn mr-btn-rust" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Maggie', 'maggie-rowan' ); ?></a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
