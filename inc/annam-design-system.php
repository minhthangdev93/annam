<?php
/**
 * Global design system: self-hosted fonts + CSS variables (child theme).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue fonts-local.css then design-tokens.css on the front end.
 */
function annam_enqueue_design_system_assets() {
	if ( is_admin() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$deps = array();

	$fonts = $dir . '/assets/css/fonts-local.css';
	if ( file_exists( $fonts ) ) {
		wp_enqueue_style(
			'annam-fonts-local',
			$uri . '/assets/css/fonts-local.css',
			array(),
			(string) filemtime( $fonts )
		);
		$deps[] = 'annam-fonts-local';
	}

	$tokens = $dir . '/assets/css/design-tokens.css';
	if ( file_exists( $tokens ) ) {
		wp_enqueue_style(
			'annam-design-tokens',
			$uri . '/assets/css/design-tokens.css',
			$deps,
			(string) filemtime( $tokens )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_enqueue_design_system_assets', 3 );

/**
 * Không tải Google Fonts từ GeneratePress — dùng Be Vietnam Pro self-hosted.
 *
 * @param string $google_fonts Pipe-separated font list.
 * @return string
 */
function annam_disable_generatepress_google_fonts( $google_fonts ) {
	return '';
}
add_filter( 'generate_typography_google_fonts', 'annam_disable_generatepress_google_fonts' );

/**
 * Dequeue stylesheet Google Fonts nếu GP vẫn đăng ký handle.
 */
function annam_dequeue_generatepress_google_fonts() {
	wp_dequeue_style( 'generate-fonts' );
	wp_deregister_style( 'generate-fonts' );
}
add_action( 'wp_enqueue_scripts', 'annam_dequeue_generatepress_google_fonts', 50 );
