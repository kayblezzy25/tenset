<?php
/**
 * Helper functions shared by the Home and Music templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get every published album, oldest debut first (menu_order, then date).
 *
 * @return WP_Post[]
 */
function mr_get_albums() {
	return get_posts( array(
		'post_type'      => 'mr_album',
		'post_status'    => 'publish',
		'numberposts'    => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
	) );
}

/**
 * Get the album flagged "feature on homepage", falling back to the most
 * recent album so the homepage never renders empty.
 *
 * @return WP_Post|null
 */
function mr_get_featured_album() {
	$featured = get_posts( array(
		'post_type'      => 'mr_album',
		'post_status'    => 'publish',
		'numberposts'    => 1,
		'meta_key'       => '_mr_album_featured',
		'meta_value'     => '1',
	) );

	if ( ! empty( $featured ) ) {
		return $featured[0];
	}

	$latest = get_posts( array(
		'post_type'   => 'mr_album',
		'post_status' => 'publish',
		'numberposts' => 1,
		'orderby'     => 'date',
		'order'       => 'DESC',
	) );

	return ! empty( $latest ) ? $latest[0] : null;
}

/**
 * Parse the "Title | 3:12" textarea into a clean array of tracks.
 *
 * @param int $album_id
 * @return array<int, array{title:string,time:string}>
 */
function mr_get_album_tracks( $album_id ) {
	$raw    = get_post_meta( $album_id, '_mr_album_tracks', true );
	$lines  = array_filter( array_map( 'trim', explode( "\n", (string) $raw ) ) );
	$tracks = array();

	foreach ( $lines as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$tracks[] = array(
			'title' => $parts[0],
			'time'  => isset( $parts[1] ) ? $parts[1] : '',
		);
	}

	return $tracks;
}

/**
 * Render the play-icon SVG used next to each track row.
 */
function mr_track_play_icon() {
	return '<span class="mr-track-play" aria-hidden="true"><svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor"><path d="M1 0.5L11 6L1 11.5V0.5Z"/></svg></span>';
}

/**
 * Render Spotify + iTunes CTA buttons for a given album.
 */
function mr_album_link_buttons( $album_id, $spotify_label = 'Play on Spotify', $itunes_label = 'Listen on iTunes' ) {
	$spotify = get_post_meta( $album_id, '_mr_album_spotify', true );
	$itunes  = get_post_meta( $album_id, '_mr_album_itunes', true );
	?>
	<div class="mr-btn-row">
		<?php if ( $spotify ) : ?>
			<a class="mr-btn mr-btn-spotify" href="<?php echo esc_url( $spotify ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html( $spotify_label ); ?>
			</a>
		<?php endif; ?>
		<?php if ( $itunes ) : ?>
			<a class="mr-btn mr-btn-itunes" href="<?php echo esc_url( $itunes ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html( $itunes_label ); ?>
			</a>
		<?php endif; ?>
	</div>
	<?php
}
