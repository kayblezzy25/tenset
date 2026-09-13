<?php
/**
 * "Album" custom post type.
 *
 * Each Album post represents one release. This keeps the Music page and the
 * homepage "Featured Album" section structured so a second (or third) album
 * can be added later just by publishing a new Album post -- no template or
 * page-builder edits required.
 *
 * Fields:
 * - Title              -> album title
 * - Featured image     -> album artwork
 * - Content editor     -> album description
 * - _mr_album_badge    -> small label, e.g. "Debut Release" / "Studio Album"
 * - _mr_album_tracks   -> one track per line: "Track Title | 3:12"
 * - _mr_album_spotify  -> Spotify album URL
 * - _mr_album_itunes   -> iTunes/Apple Music album URL
 * - _mr_album_featured -> show this album in the homepage Featured Album section
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mr_register_album_cpt() {
	register_post_type( 'mr_album', array(
		'labels' => array(
			'name'               => __( 'Albums', 'maggie-rowan' ),
			'singular_name'      => __( 'Album', 'maggie-rowan' ),
			'add_new_item'       => __( 'Add New Album', 'maggie-rowan' ),
			'edit_item'          => __( 'Edit Album', 'maggie-rowan' ),
			'all_items'          => __( 'Albums', 'maggie-rowan' ),
		),
		'public'       => true,
		'has_archive'  => false,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-album',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'rewrite'      => array( 'slug' => 'album' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'mr_register_album_cpt' );

function mr_album_meta_box() {
	add_meta_box(
		'mr_album_details',
		__( 'Album Details', 'maggie-rowan' ),
		'mr_album_meta_box_html',
		'mr_album',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'mr_album_meta_box' );

function mr_album_meta_box_html( $post ) {
	wp_nonce_field( 'mr_album_save', 'mr_album_nonce' );

	$badge    = get_post_meta( $post->ID, '_mr_album_badge', true );
	$tracks   = get_post_meta( $post->ID, '_mr_album_tracks', true );
	$spotify  = get_post_meta( $post->ID, '_mr_album_spotify', true );
	$itunes   = get_post_meta( $post->ID, '_mr_album_itunes', true );
	$featured = get_post_meta( $post->ID, '_mr_album_featured', true );
	?>
	<p>
		<label for="mr_album_badge"><strong><?php esc_html_e( 'Badge label', 'maggie-rowan' ); ?></strong></label><br />
		<input type="text" id="mr_album_badge" name="mr_album_badge" class="widefat" value="<?php echo esc_attr( $badge ); ?>" placeholder="e.g. Debut Release" />
	</p>
	<p>
		<label for="mr_album_tracks"><strong><?php esc_html_e( 'Track list', 'maggie-rowan' ); ?></strong></label><br />
		<span class="description"><?php esc_html_e( 'One track per line, formatted as: Track Title | 3:12', 'maggie-rowan' ); ?></span><br />
		<textarea id="mr_album_tracks" name="mr_album_tracks" class="widefat" rows="12" placeholder="Delete My Number Now | 2:56"><?php echo esc_textarea( $tracks ); ?></textarea>
	</p>
	<p>
		<label for="mr_album_spotify"><strong><?php esc_html_e( 'Spotify URL', 'maggie-rowan' ); ?></strong></label><br />
		<input type="url" id="mr_album_spotify" name="mr_album_spotify" class="widefat" value="<?php echo esc_attr( $spotify ); ?>" placeholder="https://open.spotify.com/..." />
	</p>
	<p>
		<label for="mr_album_itunes"><strong><?php esc_html_e( 'iTunes / Apple Music URL', 'maggie-rowan' ); ?></strong></label><br />
		<input type="url" id="mr_album_itunes" name="mr_album_itunes" class="widefat" value="<?php echo esc_attr( $itunes ); ?>" placeholder="https://itunes.apple.com/..." />
	</p>
	<p>
		<label>
			<input type="checkbox" name="mr_album_featured" value="1" <?php checked( $featured, '1' ); ?> />
			<?php esc_html_e( 'Feature this album on the homepage', 'maggie-rowan' ); ?>
		</label>
	</p>
	<?php
}

function mr_album_save_meta( $post_id ) {
	if ( ! isset( $_POST['mr_album_nonce'] ) || ! wp_verify_nonce( $_POST['mr_album_nonce'], 'mr_album_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['mr_album_badge'] ) ) {
		update_post_meta( $post_id, '_mr_album_badge', sanitize_text_field( $_POST['mr_album_badge'] ) );
	}
	if ( isset( $_POST['mr_album_tracks'] ) ) {
		update_post_meta( $post_id, '_mr_album_tracks', sanitize_textarea_field( $_POST['mr_album_tracks'] ) );
	}
	if ( isset( $_POST['mr_album_spotify'] ) ) {
		update_post_meta( $post_id, '_mr_album_spotify', esc_url_raw( $_POST['mr_album_spotify'] ) );
	}
	if ( isset( $_POST['mr_album_itunes'] ) ) {
		update_post_meta( $post_id, '_mr_album_itunes', esc_url_raw( $_POST['mr_album_itunes'] ) );
	}
	update_post_meta( $post_id, '_mr_album_featured', isset( $_POST['mr_album_featured'] ) ? '1' : '' );
}
add_action( 'save_post_mr_album', 'mr_album_save_meta' );

/**
 * Seed the debut album on theme activation so the site is populated out of the box.
 */
function mr_seed_default_album() {
	if ( get_option( 'mr_seeded_album' ) ) {
		return;
	}

	$existing = get_posts( array( 'post_type' => 'mr_album', 'numberposts' => 1 ) );
	if ( empty( $existing ) ) {
		$tracks = implode( "\n", array(
			'Delete My Number Now | 2:56',
			'Cuddle Up | 3:12',
			'Cumberland River | 3:34',
			'Knoxville Bound | 3:08',
			'Rodeo Blues | 3:21',
			'Running the Woods | 3:47',
			'Traveling Back | 3:52',
			'Quiet Roads | 2:48',
			'Working Man\'s Town | 3:16',
			'Where the River Bends | 4:02',
		) );

		$post_id = wp_insert_post( array(
			'post_type'    => 'mr_album',
			'post_title'   => 'Traveling Back',
			'post_status'  => 'publish',
			'post_content' => "Ten original songs exploring roads, railways, relationships and the pull of home. Traveling Back is Maggie Rowan's debut collection -- rooted in storytelling, acoustic tradition and the ordinary moments that shape a life.",
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_mr_album_badge', 'Debut Release' );
			update_post_meta( $post_id, '_mr_album_tracks', $tracks );
			update_post_meta( $post_id, '_mr_album_spotify', 'https://open.spotify.com/artist/01Sv95DwkMF2DjDFXOxd6R' );
			update_post_meta( $post_id, '_mr_album_itunes', 'https://itunes.apple.com/us/artist/maggie-rowan/6808578070' );
			update_post_meta( $post_id, '_mr_album_featured', '1' );
		}
	}

	update_option( 'mr_seeded_album', 1 );
}
add_action( 'after_setup_theme', 'mr_seed_default_album' );
