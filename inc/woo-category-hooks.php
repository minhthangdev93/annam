<?php
/**
 * Hooks: assets and optional WooCommerce layout tweaks for tour archives.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Style deps for `annam-woo-category-tour` so it prints after WooCommerce grid rules (`woocommerce-general`).
 *
 * @param string[] $extra Handles after `annam-design-tokens`, e.g. `annam-tour-price`.
 * @return string[]
 */
function annam_get_woo_category_tour_style_dependencies( array $extra = array() ) {
	$deps = array_merge( array( 'annam-design-tokens' ), array_values( $extra ) );
	$skip_wc_general = function_exists( 'annam_cabin_landing_is_template' ) && annam_cabin_landing_is_template();
	$skip_wc_general = (bool) apply_filters( 'annam_skip_woocommerce_general_style_dep', $skip_wc_general );
	if ( ! $skip_wc_general && wp_style_is( 'woocommerce-general', 'registered' ) ) {
		$deps[] = 'woocommerce-general';
	}
	return $deps;
}

/**
 * Whether to load tour archive assets.
 *
 * @return bool
 */
function annam_should_enqueue_woo_category_tour_assets() {
	if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'is_shop' ) ) {
		return false;
	}
	return is_shop() || is_product_category() || is_product_tag();
}

/**
 * Enqueue scoped CSS/JS for shop, category, and tag archives.
 */
function annam_enqueue_woo_category_tour_assets() {
	if ( ! annam_should_enqueue_woo_category_tour_assets() ) {
		return;
	}

	$base_dir = get_stylesheet_directory();
	$base_uri = get_stylesheet_directory_uri();

	$price_path  = $base_dir . '/assets/css/woo-tour-price.css';
	$deps_extra  = array();
	if ( file_exists( $price_path ) ) {
		wp_enqueue_style(
			'annam-tour-price',
			$base_uri . '/assets/css/woo-tour-price.css',
			array(),
			(string) filemtime( $price_path )
		);
		$deps_extra[] = 'annam-tour-price';
	}

	$css_path = $base_dir . '/assets/css/woo-category-tour.css';
	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'annam-woo-category-tour',
			$base_uri . '/assets/css/woo-category-tour.css',
			annam_get_woo_category_tour_style_dependencies( $deps_extra ),
			(string) filemtime( $css_path )
		);
	}

	$js_path = $base_dir . '/assets/js/woo-category-tour.js';
	if ( file_exists( $js_path ) ) {
		wp_enqueue_script(
			'annam-woo-category-tour',
			$base_uri . '/assets/js/woo-category-tour.js',
			array(),
			(string) filemtime( $js_path ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_enqueue_woo_category_tour_assets', 20 );

/**
 * WooCommerce views that use full width without theme sidebar.
 *
 * @return bool
 */
function annam_is_woocommerce_no_sidebar_route() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}
	return is_product() || is_shop() || is_product_category() || is_product_tag();
}

/**
 * Do not output WooCommerce sidebar column on shop, archives, and single product.
 */
function annam_remove_woocommerce_sidebar_on_tour_archives() {
	if ( ! annam_is_woocommerce_no_sidebar_route() ) {
		return;
	}
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'wp', 'annam_remove_woocommerce_sidebar_on_tour_archives', 11 );

/**
 * GeneratePress: force no-sidebar on WooCommerce shop, taxonomy archives, and single product.
 *
 * @param string $layout Layout slug from generate_get_layout().
 * @return string
 */
function annam_generate_sidebar_layout_woocommerce( $layout ) {
	if ( annam_is_woocommerce_no_sidebar_route() ) {
		return 'no-sidebar';
	}
	return $layout;
}
add_filter( 'generate_sidebar_layout', 'annam_generate_sidebar_layout_woocommerce', 20 );

/**
 * Show breadcrumb only inside category hero (avoid duplicate under header).
 */
function annam_remove_default_wc_breadcrumb_on_product_category() {
	if ( function_exists( 'is_product_category' ) && is_product_category() ) {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	}
}
add_action( 'wp', 'annam_remove_default_wc_breadcrumb_on_product_category', 9 );
