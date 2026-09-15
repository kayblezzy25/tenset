<?php
/**
 * Template Name: Music
 *
 * Loops over every published "Album" post. Adding a second album later is
 * just: Albums > Add New -- no edits to this file are needed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( null, array( 'style' => 'transparent' ) );

$mr_albums = mr_get_albums();
?>

<main>

	<section class="mr-page-hero" style="background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/hero-photo.jpg' ) ); ?>');">
		<div class="mr-container mr-page-hero-content">
			<span class="mr-eyebrow"><?php esc_html_e( 'Music', 'maggie-rowan' ); ?></span>
			<h1 class="mr-h1-sm"><?php esc_html_e( 'Traveling Back and beyond', 'maggie-rowan' ); ?></h1>
			<p class="mr-lead" style="color:#e7e2d6;"><?php esc_html_e( 'Every release, one place -- roads, railways and the pull of home, told one album at a time.', 'maggie-rowan' ); ?></p>
		</div>
	</section>

	<?php if ( empty( $mr_albums ) ) : ?>

		<div class="mr-empty-state mr-container">
			<p><?php esc_html_e( 'No albums have been published yet. Add one under Albums > Add New in the WordPress admin.', 'maggie-rowan' ); ?></p>
		</div>

	<?php else : ?>

		<?php foreach ( $mr_albums as $i => $album ) : ?>
			<?php $tracks = mr_get_album_tracks( $album->ID ); ?>
			<section class="mr-section <?php echo 0 === $i % 2 ? 'mr-bg-ivory' : 'mr-bg-white'; ?>">
				<div class="mr-container">

					<div class="mr-album-feature" style="background-image:url('<?php echo esc_url( get_theme_file_uri( '/assets/images/hero-photo.jpg' ) ); ?>');">
						<div class="mr-album-feature-inner">
							<div class="mr-album-cover">
								<?php if ( has_post_thumbnail( $album ) ) : ?>
									<?php echo get_the_post_thumbnail( $album, 'large' ); ?>
								<?php else : ?>
									<img src="<?php echo esc_url( get_theme_file_uri( '/assets/images/album-cover.jpg' ) ); ?>" alt="<?php echo esc_attr( get_the_title( $album ) ); ?>" />
								<?php endif; ?>
							</div>
							<div>
								<span class="mr-eyebrow"><?php echo 0 === $i ? esc_html__( 'Latest Release', 'maggie-rowan' ) : esc_html__( 'Album', 'maggie-rowan' ); ?></span>
								<h2 class="mr-h2"><?php echo esc_html( get_the_title( $album ) ); ?></h2>
								<p><?php echo esc_html( mr_strip_content( $album->post_content, 320 ) ); ?></p>
								<div class="mr-album-badges">
									<span class="mr-badge"><?php echo esc_html( count( $tracks ) ); ?> <?php esc_html_e( 'Tracks', 'maggie-rowan' ); ?></span>
									<?php $badge = get_post_meta( $album->ID, '_mr_album_badge', true ); ?>
									<?php if ( $badge ) : ?>
										<span class="mr-badge"><?php echo esc_html( $badge ); ?></span>
									<?php endif; ?>
								</div>
								<?php mr_album_link_buttons( $album->ID ); ?>
							</div>
						</div>
					</div>

					<?php if ( ! empty( $tracks ) ) : ?>
						<div style="margin-top:56px;">
							<h3 class="mr-h3"><?php esc_html_e( 'Track List', 'maggie-rowan' ); ?></h3>
							<div class="mr-tracks mr-tracklist-full" style="grid-template-columns:1fr;">
								<?php
								$album_spotify = get_post_meta( $album->ID, '_mr_album_spotify', true );
								foreach ( $tracks as $t => $track ) :
									$track_spotify = $track['spotify_url'] ? $track['spotify_url'] : $album_spotify;
									?>
									<div class="mr-track">
										<span class="mr-track-num"><?php echo esc_html( sprintf( '%02d', $t + 1 ) ); ?></span>
										<div class="mr-track-title">
											<?php if ( $track_spotify ) : ?>
												<a href="<?php echo esc_url( $track_spotify ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $track['title'] ); ?></a>
											<?php else : ?>
												<?php echo esc_html( $track['title'] ); ?>
											<?php endif; ?>
										</div>
										<span class="mr-track-time"><?php echo esc_html( $track['time'] ); ?></span>
										<?php echo mr_track_play_icon(); ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

				</div>
			</section>
		<?php endforeach; ?>

	<?php endif; ?>

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
