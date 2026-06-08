<?php
/**
 * Landing Cabin VIP — tour/combo Sapa từ danh mục product_cat.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Slug danh mục tour Sapa (mặc định tour-sapa).
 *
 * @return string
 */
function annam_cabin_landing_tour_sapa_slug() {
	return (string) apply_filters( 'annam_cabin_landing_tour_sapa_category_slug', 'tour-sapa' );
}

/**
 * @return WP_Term|null
 */
function annam_cabin_landing_get_tour_sapa_term() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return null;
	}

	$term = get_term_by( 'slug', annam_cabin_landing_tour_sapa_slug(), 'product_cat' );
	if ( ! $term instanceof WP_Term || is_wp_error( $term ) ) {
		return null;
	}

	return $term;
}

/**
 * Sản phẩm tour/combo trong danh mục Sapa — giá thấp → cao.
 *
 * @param int $limit Số sản phẩm.
 * @return WP_Query|null
 */
function annam_cabin_landing_query_tour_sapa_products( $limit = 8 ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return null;
	}

	$term = annam_cabin_landing_get_tour_sapa_term();
	if ( ! $term ) {
		return null;
	}

	$limit = max( 1, min( 24, (int) apply_filters( 'annam_cabin_landing_tour_sapa_limit', $limit ) ) );

	$q = new WP_Query(
		array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array(
				array(
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => (int) $term->term_id,
					'include_children' => true,
				),
			),
			'meta_key'            => '_price',
			'orderby'             => 'meta_value_num',
			'order'               => 'ASC',
		)
	);

	if ( ! $q->have_posts() ) {
		wp_reset_postdata();
		return null;
	}

	return $q;
}

/**
 * Có section tour Sapa và ít nhất một sản phẩm.
 *
 * @return bool
 */
function annam_cabin_landing_should_enqueue_tour_sapa_assets() {
	if ( ! function_exists( 'annam_cabin_landing_is_template' ) || ! annam_cabin_landing_is_template() || ! class_exists( 'WooCommerce' ) ) {
		return false;
	}
	$config = function_exists( 'annam_cabin_landing_get_config' ) ? annam_cabin_landing_get_config() : array();
	$secs   = isset( $config['sections'] ) && is_array( $config['sections'] ) ? $config['sections'] : array();
	if ( empty( $secs['related_tours'] ) ) {
		return false;
	}
	$limit = 8;
	if ( isset( $config['related_tours']['limit'] ) ) {
		$limit = (int) $config['related_tours']['limit'];
	}
	$q = annam_cabin_landing_query_tour_sapa_products( $limit );
	return $q instanceof WP_Query && $q->have_posts();
}

/**
 * CSS/JS card tour + slider section (cùng trang chủ).
 */
function annam_cabin_landing_enqueue_tour_sapa_assets() {
	if ( ! annam_cabin_landing_should_enqueue_tour_sapa_assets() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	if ( function_exists( 'annam_enqueue_home_product_sections_assets' ) ) {
		annam_enqueue_home_product_sections_assets( $dir, $uri );
		return;
	}

	$price_path      = $dir . '/assets/css/woo-tour-price.css';
	$tour_deps_extra = array();
	if ( file_exists( $price_path ) && ! wp_style_is( 'annam-tour-price', 'enqueued' ) ) {
		wp_enqueue_style(
			'annam-tour-price',
			$uri . '/assets/css/woo-tour-price.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $price_path )
		);
		$tour_deps_extra[] = 'annam-tour-price';
	}

	if ( function_exists( 'annam_get_woo_category_tour_style_dependencies' ) ) {
		$tour_css = $dir . '/assets/css/woo-category-tour.css';
		if ( file_exists( $tour_css ) && ! wp_style_is( 'annam-woo-category-tour', 'enqueued' ) ) {
			wp_enqueue_style(
				'annam-woo-category-tour',
				$uri . '/assets/css/woo-category-tour.css',
				annam_get_woo_category_tour_style_dependencies( $tour_deps_extra ),
				(string) filemtime( $tour_css )
			);
		}
	}

	$sec_css = $dir . '/assets/css/home-product-sections.css';
	if ( file_exists( $sec_css ) && ! wp_style_is( 'annam-home-product-sections', 'enqueued' ) ) {
		$sec_deps = array( 'annam-design-tokens' );
		if ( wp_style_is( 'annam-woo-category-tour', 'enqueued' ) || wp_style_is( 'annam-woo-category-tour', 'done' ) ) {
			$sec_deps[] = 'annam-woo-category-tour';
		}
		wp_enqueue_style(
			'annam-home-product-sections',
			$uri . '/assets/css/home-product-sections.css',
			$sec_deps,
			(string) filemtime( $sec_css )
		);
	}

	$sec_js = $dir . '/assets/js/annam-home-product-sections.js';
	if ( file_exists( $sec_js ) && ! wp_script_is( 'annam-home-product-sections', 'enqueued' ) ) {
		wp_enqueue_script(
			'annam-home-product-sections',
			$uri . '/assets/js/annam-home-product-sections.js',
			array(),
			(string) filemtime( $sec_js ),
			true
		);
	}
}
