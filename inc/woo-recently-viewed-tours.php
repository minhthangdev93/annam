<?php
/**
 * Recently viewed WooCommerce tours: cookie + section on single, archive, home.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

if ( ! defined( 'ANNAM_RV_MAX_ITEMS' ) ) {
	define( 'ANNAM_RV_MAX_ITEMS', 10 );
}
if ( ! defined( 'ANNAM_RV_COOKIE_NAME' ) ) {
	define( 'ANNAM_RV_COOKIE_NAME', 'annam_rv_tours' );
}

/**
 * Cookie lifetime (1 year).
 *
 * @return int
 */
function annam_recently_viewed_cookie_ttl() {
	return YEAR_IN_SECONDS;
}

/**
 * Raw ID list from cookie (may include invalid or non-publish).
 *
 * @return array<int, int>
 */
function annam_recently_viewed_read_cookie_ids() {
	if ( empty( $_COOKIE[ ANNAM_RV_COOKIE_NAME ] ) || ! is_string( $_COOKIE[ ANNAM_RV_COOKIE_NAME ] ) ) {
		return array();
	}
	$raw = sanitize_text_field( wp_unslash( $_COOKIE[ ANNAM_RV_COOKIE_NAME ] ) );
	if ( '' === $raw ) {
		return array();
	}
	$parts = preg_split( '/\s*,\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY );
	if ( ! is_array( $parts ) ) {
		return array();
	}
	$ids = array();
	foreach ( $parts as $p ) {
		$id = absint( $p );
		if ( $id > 0 ) {
			$ids[] = $id;
		}
	}
	return array_slice( $ids, 0, ANNAM_RV_MAX_ITEMS );
}

/**
 * Merge newest-first, dedupe, cap length.
 *
 * @param array<int, int> $existing Existing IDs (newest first).
 * @param int             $new_id   Product ID just viewed.
 * @return array<int, int>
 */
function annam_recently_viewed_merge_ids( array $existing, $new_id ) {
	$new_id = absint( $new_id );
	if ( $new_id <= 0 ) {
		return array_slice( array_values( array_unique( array_map( 'absint', $existing ) ) ), 0, ANNAM_RV_MAX_ITEMS );
	}
	$filtered = array();
	foreach ( $existing as $id ) {
		$id = absint( $id );
		if ( $id > 0 && $id !== $new_id ) {
			$filtered[] = $id;
		}
	}
	array_unshift( $filtered, $new_id );
	return array_slice( $filtered, 0, ANNAM_RV_MAX_ITEMS );
}

/**
 * Persist cookie (readable by PHP on next request; not HttpOnly so JS could mirror if needed).
 *
 * @param array<int, int> $ids Product IDs, newest first.
 */
function annam_recently_viewed_set_cookie_ids( array $ids ) {
	$ids = array_slice( array_values( array_filter( array_map( 'absint', $ids ) ) ), 0, ANNAM_RV_MAX_ITEMS );
	$value = implode( ',', $ids );

	$path   = ( defined( 'COOKIEPATH' ) && COOKIEPATH ) ? COOKIEPATH : '/';
	$domain = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '';

	$args = array(
		'expires'  => time() + annam_recently_viewed_cookie_ttl(),
		'path'     => $path,
		'domain'   => $domain,
		'secure'   => is_ssl(),
		'httponly' => false,
		'samesite' => 'Lax',
	);

	if ( PHP_VERSION_ID >= 70300 ) {
		setcookie( ANNAM_RV_COOKIE_NAME, $value, $args );
	} else {
		setcookie( ANNAM_RV_COOKIE_NAME, $value, $args['expires'], $args['path'], $args['domain'], $args['secure'], $args['httponly'] );
	}

	// Make current request see updated list for same-page edge cases.
	$_COOKIE[ ANNAM_RV_COOKIE_NAME ] = $value;
}

/**
 * On single product: append current publish product to history cookie.
 */
function annam_recently_viewed_record_visit() {
	if ( ! is_product() ) {
		return;
	}
	$pid = (int) get_queried_object_id();
	if ( $pid <= 0 ) {
		return;
	}
	$product = wc_get_product( $pid );
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	if ( 'publish' !== $product->get_status() ) {
		return;
	}

	$existing = annam_recently_viewed_read_cookie_ids();
	$merged   = annam_recently_viewed_merge_ids( $existing, $pid );
	annam_recently_viewed_set_cookie_ids( $merged );
}
add_action( 'template_redirect', 'annam_recently_viewed_record_visit', 22 );

/**
 * Whether product is valid for “recently viewed” list (publish + catalogue-visible).
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function annam_recently_viewed_is_listable_product( $product_id ) {
	$product_id = absint( $product_id );
	if ( $product_id <= 0 ) {
		return false;
	}
	$product = wc_get_product( $product_id );
	if ( ! $product instanceof WC_Product ) {
		return false;
	}
	if ( 'publish' !== $product->get_status() ) {
		return false;
	}
	return $product->is_visible();
}

/**
 * Ordered IDs from cookie, filtered to listable products; optional exclude (e.g. current single).
 *
 * @param int $exclude_product_id Exclude this ID (0 = none).
 * @return array<int, int>
 */
function annam_recently_viewed_get_display_ids( $exclude_product_id = 0 ) {
	$exclude_product_id = absint( $exclude_product_id );
	$raw                = annam_recently_viewed_read_cookie_ids();
	$out                = array();
	$seen               = array();
	foreach ( $raw as $id ) {
		$id = absint( $id );
		if ( $id <= 0 || $id === $exclude_product_id ) {
			continue;
		}
		if ( isset( $seen[ $id ] ) ) {
			continue;
		}
		if ( ! annam_recently_viewed_is_listable_product( $id ) ) {
			continue;
		}
		$seen[ $id ] = true;
		$out[]       = $id;
	}
	return $out;
}

/**
 * Enqueue section assets (cards reuse woo-category-tour.css).
 */
function annam_recently_viewed_enqueue_assets() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$on_tpl_home = function_exists( 'annam_is_page_template_trang_chu' ) && annam_is_page_template_trang_chu();
	$on_archive  = function_exists( 'annam_should_enqueue_woo_category_tour_assets' ) && annam_should_enqueue_woo_category_tour_assets();

	if ( ! is_product() && ! $on_archive && ! $on_tpl_home ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$deps       = array( 'annam-design-tokens' );
	$deps_extra = array();

	$price_path = $dir . '/assets/css/woo-tour-price.css';
	if ( file_exists( $price_path ) && ! wp_style_is( 'annam-tour-price', 'enqueued' ) && ! wp_style_is( 'annam-tour-price', 'done' ) ) {
		wp_enqueue_style(
			'annam-tour-price',
			$uri . '/assets/css/woo-tour-price.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $price_path )
		);
	}
	if ( wp_style_is( 'annam-tour-price', 'enqueued' ) || wp_style_is( 'annam-tour-price', 'done' ) ) {
		$deps[]       = 'annam-tour-price';
		$deps_extra[] = 'annam-tour-price';
	}

	$cat_css = $dir . '/assets/css/woo-category-tour.css';
	if ( file_exists( $cat_css ) && ! wp_style_is( 'annam-woo-category-tour', 'enqueued' ) && ! wp_style_is( 'annam-woo-category-tour', 'done' ) ) {
		wp_enqueue_style(
			'annam-woo-category-tour',
			$uri . '/assets/css/woo-category-tour.css',
			annam_get_woo_category_tour_style_dependencies( $deps_extra ),
			(string) filemtime( $cat_css )
		);
	}
	if ( wp_style_is( 'annam-woo-category-tour', 'enqueued' ) || wp_style_is( 'annam-woo-category-tour', 'done' ) ) {
		$deps[] = 'annam-woo-category-tour';
	}

	$css = $dir . '/assets/css/woo-recently-viewed-tours.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-recently-viewed-tours',
			$uri . '/assets/css/woo-recently-viewed-tours.css',
			$deps,
			(string) filemtime( $css )
		);
	}

	$js = $dir . '/assets/js/woo-recently-viewed-tours.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-recently-viewed-tours',
			$uri . '/assets/js/woo-recently-viewed-tours.js',
			array(),
			(string) filemtime( $js ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_recently_viewed_enqueue_assets', 27 );

/**
 * Output “Tour đã xem gần đây” if there is at least one other listable product.
 *
 * @param int $exclude_product_id Exclude from list (current product on single).
 */
function annam_recently_viewed_render_section( $exclude_product_id = 0 ) {
	$exclude_product_id = absint( $exclude_product_id );
	$ids                = annam_recently_viewed_get_display_ids( $exclude_product_id );
	if ( empty( $ids ) ) {
		return;
	}

	$q = new WP_Query(
		array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => count( $ids ),
			'post__in'            => $ids,
			'orderby'             => 'post__in',
			'no_found_rows'       => true,
		)
	);

	if ( ! $q->have_posts() ) {
		wp_reset_postdata();
		return;
	}

	$count    = (int) $q->post_count;
	$carousel = $count > 4;

	$section_classes = array(
		'annam-category-products',
		'annam-recently-viewed',
	);
	if ( $carousel ) {
		$section_classes[] = 'annam-recently-viewed--carousel';
	}

	wc_set_loop_prop( 'annam_recently_viewed', true );
	wc_set_loop_prop( 'columns', 4 );
	?>
	<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>" data-annam-recently-viewed>
		<div class="annam-recently-viewed__inner annam-container grid-container">
			<header class="annam-recently-viewed__header">
				<h2 class="annam-recently-viewed__title"><?php echo esc_html__( 'Tour đã xem gần đây', 'generatepress_child' ); ?></h2>
			</header>
			<div class="annam-recently-viewed__slider">
				<?php if ( $carousel ) : ?>
					<button type="button" class="annam-recently-viewed__nav annam-recently-viewed__nav--prev" aria-label="<?php echo esc_attr__( 'Trước', 'generatepress_child' ); ?>" data-annam-rv-prev></button>
				<?php endif; ?>
				<div class="annam-recently-viewed__viewport" data-annam-rv-viewport>
					<div class="annam-recently-viewed__grid">
						<?php woocommerce_product_loop_start(); ?>
						<?php
						while ( $q->have_posts() ) :
							$q->the_post();
							do_action( 'woocommerce_shop_loop' );
							wc_get_template_part( 'content', 'product' );
						endwhile;
						?>
						<?php woocommerce_product_loop_end(); ?>
					</div>
				</div>
				<?php if ( $carousel ) : ?>
					<button type="button" class="annam-recently-viewed__nav annam-recently-viewed__nav--next" aria-label="<?php echo esc_attr__( 'Tiếp', 'generatepress_child' ); ?>" data-annam-rv-next></button>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
	wp_reset_postdata();
	wc_reset_loop();
}

/**
 * Single product: after related tours, before reviews / rest of summary.
 */
function annam_recently_viewed_render_on_single() {
	if ( ! is_product() ) {
		return;
	}
	annam_recently_viewed_render_section( (int) get_queried_object_id() );
}
add_action( 'woocommerce_after_single_product_summary', 'annam_recently_viewed_render_on_single', 21 );
