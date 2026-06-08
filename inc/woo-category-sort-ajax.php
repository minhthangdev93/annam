<?php
/**
 * AJAX sort for tour list on shop / product_cat / product_tag archives.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return string[]
 */
function annam_category_sort_allowed_orderby_keys() {
	return array( 'date', 'price_asc', 'price_desc' );
}

/**
 * Sanitize orderby key from request.
 *
 * @param string $raw Raw value.
 * @return string
 */
function annam_category_sort_sanitize_orderby( $raw ) {
	$key = sanitize_key( (string) $raw );
	return in_array( $key, annam_category_sort_allowed_orderby_keys(), true ) ? $key : 'date';
}

/**
 * Hiển thị bộ lọc sắp xếp trên archive tour.
 *
 * @return bool
 */
function annam_category_sort_is_enabled_view() {
	if ( ! function_exists( 'annam_is_tour_archive_shop_context' ) || ! annam_is_tour_archive_shop_context() ) {
		return false;
	}
	return function_exists( 'is_product_category' ) && ( is_product_category() || is_product_tag() || is_shop() );
}

/**
 * Ngữ cảnh taxonomy cho query (category / tag / shop).
 *
 * @return array{taxonomy:string,term_id:int}
 */
function annam_category_sort_get_archive_context() {
	if ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term && 'product_cat' === $term->taxonomy ) {
			return array(
				'taxonomy' => 'product_cat',
				'term_id'  => (int) $term->term_id,
			);
		}
	}
	if ( function_exists( 'is_product_tag' ) && is_product_tag() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term && 'product_tag' === $term->taxonomy ) {
			return array(
				'taxonomy' => 'product_tag',
				'term_id'  => (int) $term->term_id,
			);
		}
	}
	return array(
		'taxonomy' => '',
		'term_id'  => 0,
	);
}

/**
 * @param array{taxonomy:string,term_id:int} $context Archive context.
 * @param string                            $orderby Sort key.
 * @param int                               $paged   Page.
 * @return array<string, mixed>
 */
function annam_category_sort_build_query_args( array $context, $orderby, $paged = 1 ) {
	$per_page = (int) apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );
	if ( $per_page < 1 ) {
		$per_page = 12;
	}

	$args = array(
		'post_type'              => 'product',
		'post_status'            => 'publish',
		'posts_per_page'         => $per_page,
		'paged'                  => max( 1, (int) $paged ),
		'ignore_sticky_posts'    => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	);

	$taxonomy = isset( $context['taxonomy'] ) ? (string) $context['taxonomy'] : '';
	$term_id  = isset( $context['term_id'] ) ? (int) $context['term_id'] : 0;

	if ( $term_id > 0 && in_array( $taxonomy, array( 'product_cat', 'product_tag' ), true ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy'         => $taxonomy,
				'field'            => 'term_id',
				'terms'            => $term_id,
				'include_children' => ( 'product_cat' === $taxonomy ),
			),
		);
	}

	$orderby = annam_category_sort_sanitize_orderby( $orderby );

	switch ( $orderby ) {
		case 'price_asc':
			$args['meta_key'] = '_price';
			$args['orderby']  = array(
				'meta_value_num' => 'ASC',
				'date'           => 'DESC',
				'ID'             => 'DESC',
			);
			break;
		case 'price_desc':
			$args['meta_key'] = '_price';
			$args['orderby']  = array(
				'meta_value_num' => 'DESC',
				'date'           => 'DESC',
				'ID'             => 'DESC',
			);
			break;
		case 'date':
		default:
			$args['orderby'] = array(
				'date' => 'DESC',
				'ID'   => 'DESC',
			);
			break;
	}

	return $args;
}

/**
 * HTML loop + pagination for a product query.
 *
 * @param WP_Query $query Product query.
 * @return string
 */
function annam_category_sort_render_loop_html( WP_Query $query ) {
	if ( ! $query instanceof WP_Query ) {
		return '';
	}

	$columns      = (int) apply_filters( 'loop_shop_columns', 4 );
	$current_page = max( 1, (int) $query->get( 'paged' ) );
	if ( $current_page < 1 ) {
		$current_page = 1;
	}

	ob_start();

	if ( $query->have_posts() ) {
		wc_set_loop_prop( 'annam_category_sort_ajax', true );

		wc_setup_loop(
			array(
				'columns'      => $columns,
				'name'         => 'products',
				'is_shortcode' => false,
				'is_search'    => false,
				'is_paginated' => $query->max_num_pages > 1,
				'total'        => (int) $query->found_posts,
				'total_pages'  => (int) $query->max_num_pages,
				'per_page'     => (int) $query->get( 'posts_per_page' ),
				'current_page' => $current_page,
			)
		);

		woocommerce_product_loop_start();

		while ( $query->have_posts() ) {
			$query->the_post();
			do_action( 'woocommerce_shop_loop' );
			wc_get_template_part( 'content', 'product' );
		}

		woocommerce_product_loop_end();

		wp_reset_postdata();

		global $wp_query;
		$prev_query = $wp_query;
		$wp_query   = $query;
		woocommerce_pagination();
		$wp_query = $prev_query;

		wc_reset_loop();
	} else {
		do_action( 'woocommerce_no_products_found' );
	}

	return (string) ob_get_clean();
}

/**
 * AJAX: sorted product loop.
 */
function annam_ajax_category_products_sort() {
	check_ajax_referer( 'annam_category_sort', 'nonce' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		wp_send_json_error( array( 'message' => 'WooCommerce inactive.' ), 400 );
	}

	$orderby  = annam_category_sort_sanitize_orderby( isset( $_POST['orderby'] ) ? wp_unslash( $_POST['orderby'] ) : 'date' );
	$paged    = isset( $_POST['paged'] ) ? max( 1, absint( wp_unslash( $_POST['paged'] ) ) ) : 1;
	$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy'] ) ) : '';
	$term_id  = isset( $_POST['term_id'] ) ? absint( wp_unslash( $_POST['term_id'] ) ) : 0;

	$context = array(
		'taxonomy' => '',
		'term_id'  => 0,
	);

	if ( $term_id > 0 && in_array( $taxonomy, array( 'product_cat', 'product_tag' ), true ) ) {
		$term = get_term( $term_id, $taxonomy );
		if ( ! $term instanceof WP_Term || is_wp_error( $term ) ) {
			wp_send_json_error( array( 'message' => 'Invalid term.' ), 400 );
		}
		$context['taxonomy'] = $taxonomy;
		$context['term_id']  = $term_id;
	}

	$query = new WP_Query( annam_category_sort_build_query_args( $context, $orderby, $paged ) );

	wp_send_json_success(
		array(
			'html'       => annam_category_sort_render_loop_html( $query ),
			'found'      => (int) $query->found_posts,
			'max_pages'  => (int) $query->max_num_pages,
			'paged'      => $paged,
			'orderby'    => $orderby,
		)
	);
}
add_action( 'wp_ajax_annam_category_products_sort', 'annam_ajax_category_products_sort' );
add_action( 'wp_ajax_nopriv_annam_category_products_sort', 'annam_ajax_category_products_sort' );

/**
 * Keep the initial WooCommerce archive query aligned with the visible default sort.
 *
 * WooCommerce's default catalog ordering is menu_order/title, while this archive
 * labels the default option as "Mới nhất". Without this override the first page
 * loads A-Z until the visitor changes the select and triggers AJAX.
 *
 * @param array<string, mixed> $args    Catalog ordering args.
 * @param string               $orderby Requested orderby.
 * @return array<string, mixed>
 */
function annam_category_sort_default_catalog_ordering_args( $args, $orderby ) {
	if ( ! annam_category_sort_is_enabled_view() || '' !== (string) $orderby ) {
		return $args;
	}

	$args['orderby'] = array(
		'date' => 'DESC',
		'ID'   => 'DESC',
	);
	$args['order']   = 'DESC';
	unset( $args['meta_key'] );

	return $args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'annam_category_sort_default_catalog_ordering_args', 20, 2 );

/**
 * Apply the same default ordering to the main WooCommerce product archive query.
 *
 * @param WP_Query $query Main WooCommerce product query.
 */
function annam_category_sort_default_product_query( $query ) {
	if ( ! $query instanceof WP_Query || is_admin() || ! annam_category_sort_is_enabled_view() ) {
		return;
	}

	if ( isset( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$query->set(
		'orderby',
		array(
			'date' => 'DESC',
			'ID'   => 'DESC',
		)
	);
	$query->set( 'order', 'DESC' );
	$query->set( 'meta_key', '' );
}
add_action( 'woocommerce_product_query', 'annam_category_sort_default_product_query', 20 );

/**
 * Enqueue sort script on tour archives.
 */
function annam_enqueue_category_sort_assets() {
	if ( ! annam_category_sort_is_enabled_view() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$js  = $dir . '/assets/js/woo-category-sort.js';

	if ( ! file_exists( $js ) ) {
		return;
	}

	wp_enqueue_script(
		'annam-woo-category-sort',
		$uri . '/assets/js/woo-category-sort.js',
		array(),
		(string) filemtime( $js ),
		true
	);

	$ctx = annam_category_sort_get_archive_context();

	wp_localize_script(
		'annam-woo-category-sort',
		'annamCategorySort',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'annam_category_sort' ),
			'taxonomy' => $ctx['taxonomy'],
			'termId'   => $ctx['term_id'],
			'labels'   => array(
				'loading' => __( 'Đang tải…', 'generatepress_child' ),
				'error'   => __( 'Không tải được danh sách. Vui lòng thử lại.', 'generatepress_child' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'annam_enqueue_category_sort_assets', 25 );
