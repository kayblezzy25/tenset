<?php
/**
 * Site header. Templates call get_header( null, array( 'style' => '...' ) )
 * with 'transparent' (overlaid on a hero image, e.g. Home/About/Music) or
 * 'static' (solid background, used on Contact/Privacy). $args is extracted
 * by WordPress's load_template() (core behavior since 5.5), so $args is
 * always available here even though this file is included from inside a
 * different function's scope than the calling template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mr_header_style = isset( $args['style'] ) ? $args['style'] : 'static';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'mr-body' ); ?>>
<?php wp_body_open(); ?>

<header class="mr-header <?php echo 'transparent' === $mr_header_style ? '' : 'mr-header--static'; ?>" id="mr-site-header">
	<div class="mr-container mr-header-inner">
		<a class="mr-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php bloginfo( 'name' ); ?>
		</a>

		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'mr-nav',
				'menu_id'        => 'mr-nav',
			) );
		} else {
			mr_default_menu();
		}
		?>

		<button type="button" class="mr-nav-toggle" id="mr-nav-toggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'maggie-rowan' ); ?>" aria-expanded="false" aria-controls="mr-nav">
			<span></span><span></span><span></span>
		</button>
	</div>
</header>
