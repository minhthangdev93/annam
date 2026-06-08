<?php
/**
 * GeneratePress child theme functions and definitions.
 *
 * Add your custom PHP in this file.
 * Only edit this file if you have direct access to it on your server (to fix errors if they happen).
 */

require_once get_stylesheet_directory() . '/inc/home-hero-slides.php';
require_once get_stylesheet_directory() . '/inc/annam-about-settings.php';
require_once get_stylesheet_directory() . '/inc/annam-ecosystem-settings.php';
require_once get_stylesheet_directory() . '/inc/annam-settings.php';
require_once get_stylesheet_directory() . '/inc/security-hardening.php';
require_once get_stylesheet_directory() . '/inc/annam-homepage-schema.php';
require_once get_stylesheet_directory() . '/inc/seo-rank-math-annam.php';
require_once get_stylesheet_directory() . '/inc/annam-design-system.php';
require_once get_stylesheet_directory() . '/inc/performance-hooks.php';
require_once get_stylesheet_directory() . '/inc/blog-archive.php';
require_once get_stylesheet_directory() . '/inc/single-post.php';
require_once get_stylesheet_directory() . '/inc/contact-page.php';
require_once get_stylesheet_directory() . '/inc/cabin-landing-page.php';
require_once get_stylesheet_directory() . '/inc/cabin-landing-images-admin.php';
require_once get_stylesheet_directory() . '/inc/annam-cabin-landing-schema.php';
require_once get_stylesheet_directory() . '/inc/car-rental-landing-page.php';
require_once get_stylesheet_directory() . '/inc/annam-car-rental-landing-schema.php';
require_once get_stylesheet_directory() . '/inc/annam-contact-faq-schema.php';
require_once get_stylesheet_directory() . '/inc/annam-llms-txt.php';
require_once get_stylesheet_directory() . '/inc/about-page.php';
require_once get_stylesheet_directory() . '/inc/header-hooks.php'; // Header tùy chỉnh: hook + enqueue (filemtime trong inc/header-hooks.php).
require_once get_stylesheet_directory() . '/inc/gtranslate-shortcode.php';
require_once get_stylesheet_directory() . '/inc/footer-hooks.php';
require_once get_stylesheet_directory() . '/inc/page-template-trang-chu-hooks.php';
require_once get_stylesheet_directory() . '/inc/woo-category-helpers.php';
require_once get_stylesheet_directory() . '/inc/woo-category-hooks.php';
require_once get_stylesheet_directory() . '/inc/woo-category-sort-ajax.php';
require_once get_stylesheet_directory() . '/inc/woo-category-card-image-term.php';
require_once get_stylesheet_directory() . '/inc/woo-category-home-display-term.php';
require_once get_stylesheet_directory() . '/inc/woo-category-showcase.php';
require_once get_stylesheet_directory() . '/inc/home-ecosystem-section.php';
require_once get_stylesheet_directory() . '/inc/woo-tour-product-fields.php';
require_once get_stylesheet_directory() . '/inc/woo-tour-price-display.php';
require_once get_stylesheet_directory() . '/inc/woo-category-hero-term-fields.php';
require_once get_stylesheet_directory() . '/inc/woo-category-seo-term-fields.php';
require_once get_stylesheet_directory() . '/inc/woo-single-tour-header-hooks.php';
require_once get_stylesheet_directory() . '/inc/woo-single-tour-detail-section.php';
require_once get_stylesheet_directory() . '/inc/woo-single-tour-related.php';
require_once get_stylesheet_directory() . '/inc/woo-recently-viewed-tours.php';
require_once get_stylesheet_directory() . '/inc/woo-product-reviews.php';

/**
 * Load an inline SVG icon from the child theme assets directory.
 *
 * @param string $icon_name SVG file name without extension.
 * @return string
 */
function strip_theme_get_svg( $icon_name ) {
	static $cache = [];

	if ( isset( $cache[ $icon_name ] ) ) {
		return $cache[ $icon_name ];
	}

	$file_path = get_stylesheet_directory() . '/assets/icons/' . $icon_name . '.svg';

	if ( file_exists( $file_path ) ) {
		$cache[ $icon_name ] = file_get_contents( $file_path );
	} else {
		$cache[ $icon_name ] = '';
	}

	return $cache[ $icon_name ];
}

/**
 * Disable the Gutenberg block editor (use the classic editor for posts and pages).
 */
add_filter( 'use_block_editor_for_post', '__return_false', 10 );
add_filter( 'use_block_editor_for_post_type', '__return_false', 10, 2 );

/**
 * Disable the block-based widgets screen (Appearance → Widgets uses classic widgets).
 */
add_action(
	'after_setup_theme',
	function () {
		remove_theme_support( 'widgets-block-editor' );
	},
	11
);
