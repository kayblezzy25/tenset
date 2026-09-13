<?php
/**
 * Maggie Rowan theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MR_THEME_VERSION', '1.0.0' );

/**
 * Theme setup: nav menus, thumbnails, title tag.
 */
function mr_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'maggie-rowan' ),
		'footer'  => __( 'Footer Navigation', 'maggie-rowan' ),
	) );
}
add_action( 'after_setup_theme', 'mr_theme_setup' );

/**
 * Enqueue fonts, styles and scripts.
 */
function mr_enqueue_assets() {
	wp_enqueue_style(
		'mr-google-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,500&family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'maggie-rowan-style', get_stylesheet_uri(), array(), MR_THEME_VERSION );

	wp_enqueue_script( 'maggie-rowan-main', get_theme_file_uri( '/assets/js/main.js' ), array(), MR_THEME_VERSION, true );

	wp_localize_script( 'maggie-rowan-main', 'mrTheme', array(
		'privacyUrl' => function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : home_url( '/privacy-policy/' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'mr_enqueue_assets' );

/** Register the default fallback menu items if no menu is assigned. */
function mr_default_menu() {
	echo '<ul class="mr-nav" id="mr-nav">';
	$pages = array(
		'Home'    => home_url( '/' ),
		'About'   => home_url( '/about/' ),
		'Music'   => home_url( '/music/' ),
		'Contact' => home_url( '/contact/' ),
	);
	foreach ( $pages as $label => $url ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/** Load includes. */
require get_theme_file_path( '/inc/custom-post-types.php' );
require get_theme_file_path( '/inc/album-helpers.php' );
require get_theme_file_path( '/inc/contact-form.php' );
require get_theme_file_path( '/inc/cookie-consent.php' );
require get_theme_file_path( '/inc/theme-options.php' );

/**
 * Excerpt-free content helper used across templates.
 */
function mr_strip_content( $content, $limit = 0 ) {
	$content = wp_strip_all_tags( $content );
	if ( $limit && mb_strlen( $content ) > $limit ) {
		$content = mb_substr( $content, 0, $limit ) . '…';
	}
	return $content;
}
