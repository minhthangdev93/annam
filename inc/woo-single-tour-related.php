<?php
/**
 * Single product: related tours by primary product_cat, price ASC, custom layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Primary product_cat for a product (Yoast / Rank Math primary if set, else first ordered term, skip uncategorized).
 *
 * @param int $post_id Product post ID.
 * @return WP_Term|null
 */
function annam_tour_get_primary_product_cat_term( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return null;
	}

	$primary_id = (int) get_post_meta( $post_id, '_yoast_wpseo_primary_product_cat', true );
	if ( $primary_id > 0 ) {
		$term = get_term( $primary_id, 'product_cat' );
		if ( $term instanceof WP_Term && ! is_wp_error( $term ) ) {
			return $term;
		}
	}

	$rank_primary = (int) get_post_meta( $post_id, 'rank_math_primary_product_cat', true );
	if ( $rank_primary > 0 ) {
		$term = get_term( $rank_primary, 'product_cat' );
		if ( $term instanceof WP_Term && ! is_wp_error( $term ) ) {
			return $term;
		}
	}

	$terms = wp_get_post_terms(
		$post_id,
		'product_cat',
		array(
			'orderby' => 'menu_order',
			'order'   => 'ASC',
		)
	);

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return null;
	}

	foreach ( $terms as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}
		if ( 'uncategorized' === $term->slug ) {
			continue;
		}
		return $term;
	}

	$first = $terms[0];
	return $first instanceof WP_Term ? $first : null;
}

/**
 * Remove default WooCommerce related block (same hook priority as core).
 */
function annam_tour_related_remove_default() {
	if ( ! is_product() ) {
		return;
	}
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
}
add_action( 'wp', 'annam_tour_related_remove_default', 5 );

/**
 * Enqueue category tour card styles + related layout (single product only).
 */
function annam_tour_related_enqueue_assets() {
	if ( ! is_product() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$deps       = array( 'annam-design-tokens' );
	$deps_extra = array();

	$price_path = $dir . '/assets/css/woo-tour-price.css';
	if ( file_exists( $price_path ) ) {
		wp_enqueue_style(
			'annam-tour-price',
			$uri . '/assets/css/woo-tour-price.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $price_path )
		);
		$deps[]       = 'annam-tour-price';
		$deps_extra[] = 'annam-tour-price';
	}

	$cat_css = $dir . '/assets/css/woo-category-tour.css';
	if ( file_exists( $cat_css ) ) {
		wp_enqueue_style(
			'annam-woo-category-tour',
			$uri . '/assets/css/woo-category-tour.css',
			annam_get_woo_category_tour_style_dependencies( $deps_extra ),
			(string) filemtime( $cat_css )
		);
		$deps[] = 'annam-woo-category-tour';
	}

	$rel_css = $dir . '/assets/css/woo-single-tour-related.css';
	if ( file_exists( $rel_css ) ) {
		wp_enqueue_style(
			'annam-single-tour-related',
			$uri . '/assets/css/woo-single-tour-related.css',
			$deps,
			(string) filemtime( $rel_css )
		);
	}

	$rel_js = $dir . '/assets/js/woo-single-tour-related.js';
	if ( file_exists( $rel_js ) ) {
		wp_enqueue_script(
			'annam-single-tour-related',
			$uri . '/assets/js/woo-single-tour-related.js',
			array(),
			(string) filemtime( $rel_js ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_tour_related_enqueue_assets', 26 );

/**
 * Output related section after summary (replaces default related products).
 */
function annam_tour_related_products_render() {
	if ( ! is_product() ) {
		return;
	}

	get_template_part( 'template-parts/woocommerce/single-tour/tour', 'related' );
}
add_action( 'woocommerce_after_single_product_summary', 'annam_tour_related_products_render', 20 );
