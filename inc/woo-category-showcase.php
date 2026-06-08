<?php
/**
 * Featured tour categories strip (product_cat) below category archives.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Slugs tùy chọn cho showcase (chỉ khi filter trả về mảng — không hardcode mặc định).
 * Ưu tiên dùng {@see annam_resolve_category_showcase_terms()} với get_terms().
 *
 * @return string[]
 */
function annam_get_category_showcase_slugs() {
	$slugs = array();

	/**
	 * Danh sách slug product_cat cố định (tùy chọn). Nếu rỗng, showcase lấy 6 danh mục cha động theo thứ tự WooCommerce.
	 *
	 * @param string[] $slugs Term slugs.
	 */
	return apply_filters( 'annam_category_showcase_slugs', $slugs );
}

/**
 * Danh sách product_cat cho showcase / trang chủ: tối đa 6 danh mục cha (pad_counts), thứ tự admin
 * (WooCommerce menu_order → meta `order`). Gồm cả danh mục mặc định (`default_product_cat`) nếu thỏa điều kiện;
 * muốn ẩn: thêm ID term vào filter `annam_category_showcase_exclude_term_ids`.
 *
 * @return WP_Term[]
 */
function annam_resolve_category_showcase_terms() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$limit = (int) apply_filters( 'annam_category_showcase_limit', 6 );
	$limit = max( 1, min( 24, $limit ) );

	$manual = apply_filters( 'annam_category_showcase_terms', null );
	if ( is_array( $manual ) ) {
		$out = array_values(
			array_filter(
				$manual,
				static function ( $t ) {
					return $t instanceof WP_Term && 'product_cat' === $t->taxonomy;
				}
			)
		);
		return array_slice( $out, 0, $limit );
	}

	$slugs = annam_get_category_showcase_slugs();
	if ( ! empty( $slugs ) && is_array( $slugs ) ) {
		$terms = array();
		foreach ( $slugs as $slug ) {
			$slug = is_string( $slug ) ? sanitize_title( $slug ) : '';
			if ( '' === $slug ) {
				continue;
			}
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( $term instanceof WP_Term && ! is_wp_error( $term ) ) {
				$terms[] = $term;
			}
			if ( count( $terms ) >= $limit ) {
				break;
			}
		}
		if ( ! empty( $terms ) ) {
			return $terms;
		}
	}

	$exclude = array_filter(
		array_map( 'absint', (array) apply_filters( 'annam_category_showcase_exclude_term_ids', array() ) )
	);
	$exclude = array_unique( $exclude );

	$args = array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => (bool) apply_filters( 'annam_category_showcase_hide_empty', true ),
		'pad_counts' => true,
		'parent'     => (int) apply_filters( 'annam_category_showcase_parent', 0 ),
		'number'     => $limit,
		'exclude'    => $exclude,
	);

	// Không truyền orderby: WooCommerce get_terms_defaults + pre_get_terms sắp xếp product_cat theo meta order (kéo thả admin).
	$terms = get_terms( $args );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	return array_slice(
		array_values(
			array_filter(
				$terms,
				static function ( $t ) {
					return $t instanceof WP_Term;
				}
			)
		),
		0,
		$limit
	);
}

/**
 * Render shared category navigation cards with a single normalized data flow.
 *
 * @param WP_Term[] $terms    Category terms to render.
 * @param string    $context  Optional rendering context, e.g. `home` or `single_post`.
 */
function annam_render_category_nav_cards( array $terms, $context = '' ) {
	$terms = array_values(
		array_filter(
			$terms,
			static function ( $term ) {
				return $term instanceof WP_Term;
			}
		)
	);

	if ( empty( $terms ) ) {
		return;
	}

	get_template_part(
		'template-parts/annam-category-nav-cards',
		null,
		array(
			'annam_category_nav_terms'   => $terms,
			'annam_category_nav_context' => is_string( $context ) ? $context : '',
		)
	);
}

/**
 * Số tour publish thực tế trong product_cat, gồm cả danh mục con.
 * Không tin hoàn toàn vào $term->count vì term cha có thể là 0 khi sản phẩm nằm ở child terms.
 *
 * @param WP_Term|int $term Term object hoặc term_id.
 * @return int
 */
function annam_get_product_category_effective_count( $term ) {
	static $cache = array();

	if ( $term instanceof WP_Term ) {
		$term_id = (int) $term->term_id;
	} else {
		$term_id = absint( $term );
	}

	if ( $term_id <= 0 || ! taxonomy_exists( 'product_cat' ) ) {
		return 0;
	}

	if ( isset( $cache[ $term_id ] ) ) {
		return $cache[ $term_id ];
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => array(
				array(
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => array( $term_id ),
					'include_children' => true,
					'operator'         => 'IN',
				),
			),
		)
	);

	$cache[ $term_id ] = max( 0, (int) $query->found_posts );

	wp_reset_postdata();

	return $cache[ $term_id ];
}

/**
 * Trang chủ tĩnh có đang dùng template Trang chủ (tĩnh) hay không (kể cả khi is_page_template chưa ổn định lúc enqueue).
 *
 * @return bool
 */
function annam_is_page_template_trang_chu() {
	if ( is_page_template( 'page-template-trang-chu.php' ) ) {
		return true;
	}
	$page_on_front = (int) get_option( 'page_on_front' );
	if ( $page_on_front > 0 && (int) get_queried_object_id() === $page_on_front ) {
		return 'page-template-trang-chu.php' === get_page_template_slug( $page_on_front );
	}
	if ( is_singular( 'page' ) ) {
		$page_id = (int) get_queried_object_id();
		return $page_id > 0 && 'page-template-trang-chu.php' === get_page_template_slug( $page_id );
	}
	return false;
}

/**
 * CSS/JS section sản phẩm trang chủ (card tour + layout section).
 *
 * @param string $dir Stylesheet directory path.
 * @param string $uri Stylesheet directory URI.
 */
function annam_enqueue_home_product_sections_assets( $dir, $uri ) {
	$price_path      = $dir . '/assets/css/woo-tour-price.css';
	$tour_deps_extra = array();
	if ( file_exists( $price_path ) && ! wp_style_is( 'annam-tour-price', 'enqueued' ) && ! wp_style_is( 'annam-tour-price', 'done' ) ) {
		wp_enqueue_style(
			'annam-tour-price',
			$uri . '/assets/css/woo-tour-price.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $price_path )
		);
	}
	if ( wp_style_is( 'annam-tour-price', 'enqueued' ) || wp_style_is( 'annam-tour-price', 'done' ) ) {
		$tour_deps_extra[] = 'annam-tour-price';
	}
	$tour_deps = annam_get_woo_category_tour_style_dependencies( $tour_deps_extra );

	$tour_css = $dir . '/assets/css/woo-category-tour.css';
	$tour_ok   = false;
	if ( file_exists( $tour_css ) && ! wp_style_is( 'annam-woo-category-tour', 'enqueued' ) && ! wp_style_is( 'annam-woo-category-tour', 'done' ) ) {
		wp_enqueue_style(
			'annam-woo-category-tour',
			$uri . '/assets/css/woo-category-tour.css',
			$tour_deps,
			(string) filemtime( $tour_css )
		);
		$tour_ok = true;
	} elseif ( file_exists( $tour_css ) ) {
		$tour_ok = true;
	}

	$sec_css = $dir . '/assets/css/home-product-sections.css';
	if ( file_exists( $sec_css ) && ! wp_style_is( 'annam-home-product-sections', 'enqueued' ) && ! wp_style_is( 'annam-home-product-sections', 'done' ) ) {
		$sec_deps = array( 'annam-design-tokens' );
		if ( $tour_ok ) {
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
	if ( file_exists( $sec_js ) && ! wp_script_is( 'annam-home-product-sections', 'enqueued' ) && ! wp_script_is( 'annam-home-product-sections', 'done' ) ) {
		wp_enqueue_script(
			'annam-home-product-sections',
			$uri . '/assets/js/annam-home-product-sections.js',
			array(),
			(string) filemtime( $sec_js ),
			true
		);
	}
}

/**
 * Nạp CSS/JS trang chủ tĩnh — gọi từ page-template-trang-chu.php trước get_header()
 * để tránh lỡ enqueue khi conditional tags chưa đúng trên wp_enqueue_scripts.
 */
function annam_enqueue_trang_chu_template_assets() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$css_showcase = $dir . '/assets/css/woo-category-showcase.css';
	if ( file_exists( $css_showcase ) && ! wp_style_is( 'annam-category-showcase', 'enqueued' ) && ! wp_style_is( 'annam-category-showcase', 'done' ) ) {
		wp_enqueue_style(
			'annam-category-showcase',
			$uri . '/assets/css/woo-category-showcase.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $css_showcase )
		);
	}

	/* Intro collapse (category-intro block) + card grid: cần cho mô tả dài trang chủ dù không có section tour */
	$tour_deps_extra = array();
	$price_path      = $dir . '/assets/css/woo-tour-price.css';
	if ( file_exists( $price_path ) && ! wp_style_is( 'annam-tour-price', 'enqueued' ) && ! wp_style_is( 'annam-tour-price', 'done' ) ) {
		wp_enqueue_style(
			'annam-tour-price',
			$uri . '/assets/css/woo-tour-price.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $price_path )
		);
	}
	if ( wp_style_is( 'annam-tour-price', 'enqueued' ) || wp_style_is( 'annam-tour-price', 'done' ) ) {
		$tour_deps_extra[] = 'annam-tour-price';
	}
	$tour_deps = annam_get_woo_category_tour_style_dependencies( $tour_deps_extra );

	$tour_css = $dir . '/assets/css/woo-category-tour.css';
	if ( file_exists( $tour_css ) && ! wp_style_is( 'annam-woo-category-tour', 'enqueued' ) && ! wp_style_is( 'annam-woo-category-tour', 'done' ) ) {
		wp_enqueue_style(
			'annam-woo-category-tour',
			$uri . '/assets/css/woo-category-tour.css',
			$tour_deps,
			(string) filemtime( $tour_css )
		);
	}

	$home_css_deps = array( 'annam-design-tokens', 'annam-category-showcase' );
	if ( wp_style_is( 'annam-woo-category-tour', 'enqueued' ) || wp_style_is( 'annam-woo-category-tour', 'done' ) ) {
		$home_css_deps[] = 'annam-woo-category-tour';
	}

	$css_home = $dir . '/assets/css/page-template-home.css';
	if ( file_exists( $css_home ) && ! wp_style_is( 'annam-page-template-home', 'enqueued' ) && ! wp_style_is( 'annam-page-template-home', 'done' ) ) {
		wp_enqueue_style(
			'annam-page-template-home',
			$uri . '/assets/css/page-template-home.css',
			$home_css_deps,
			(string) filemtime( $css_home )
		);
	}

	$js = $dir . '/assets/js/annam-home-hero-slider.js';
	if ( file_exists( $js ) && ! wp_script_is( 'annam-home-hero-slider', 'enqueued' ) && ! wp_script_is( 'annam-home-hero-slider', 'done' ) ) {
		wp_enqueue_script(
			'annam-home-hero-slider',
			$uri . '/assets/js/annam-home-hero-slider.js',
			array(),
			(string) filemtime( $js ),
			true
		);
	}

	annam_enqueue_home_product_sections_assets( $dir, $uri );

	if ( function_exists( 'annam_recently_viewed_enqueue_assets' ) ) {
		annam_recently_viewed_enqueue_assets();
	}

	if ( function_exists( 'annam_ecosystem_enqueue_trang_chu_assets' ) ) {
		annam_ecosystem_enqueue_trang_chu_assets();
	}

	$intro_js = $dir . '/assets/js/woo-category-tour.js';
	if ( file_exists( $intro_js ) && ! wp_script_is( 'annam-woo-category-tour', 'enqueued' ) && ! wp_script_is( 'annam-woo-category-tour', 'done' ) ) {
		wp_enqueue_script(
			'annam-woo-category-tour',
			$uri . '/assets/js/woo-category-tour.js',
			array(),
			(string) filemtime( $intro_js ),
			true
		);
	}
}

/**
 * Shorten category label for cards (drop leading/trailing “Tour”).
 *
 * @param WP_Term $term Product category term.
 * @return string
 */
function annam_showcase_category_short_name( WP_Term $term ) {
	$name = isset( $term->name ) ? trim( (string) $term->name ) : '';
	if ( '' === $name ) {
		return '';
	}
	$name = trim( preg_replace( '/^\s*tour\s+/iu', '', $name ) );
	$name = trim( preg_replace( '/\s+tour\s*$/iu', '', $name ) );
	return '' !== $name ? $name : (string) $term->name;
}

/**
 * Stable 0–5 index for distinct visual fallbacks when a category has no image.
 *
 * @param WP_Term $term Term.
 * @return int
 */
function annam_showcase_category_fallback_index( WP_Term $term ) {
	return absint( $term->term_id ) % 6;
}

/**
 * Enqueue showcase styles on product category archives.
 */
function annam_enqueue_category_showcase_assets() {
	$need_showcase = function_exists( 'is_product_category' ) && is_product_category();
	$need_home     = function_exists( 'annam_is_page_template_trang_chu' ) && annam_is_page_template_trang_chu();

	if ( ! $need_showcase && ! $need_home ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$css = $dir . '/assets/css/woo-category-showcase.css';

	if ( ! file_exists( $css ) ) {
		return;
	}

	wp_enqueue_style(
		'annam-category-showcase',
		$uri . '/assets/css/woo-category-showcase.css',
		array( 'annam-design-tokens' ),
		(string) filemtime( $css )
	);
}
add_action( 'wp_enqueue_scripts', 'annam_enqueue_category_showcase_assets', 21 );

/**
 * CSS riêng hero + lưới danh mục trên template trang chủ tĩnh.
 */
function annam_enqueue_page_template_home_assets() {
	if ( ! function_exists( 'annam_is_page_template_trang_chu' ) || ! annam_is_page_template_trang_chu() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$css = $dir . '/assets/css/page-template-home.css';

	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-page-template-home',
			$uri . '/assets/css/page-template-home.css',
			array( 'annam-category-showcase' ),
			(string) filemtime( $css )
		);
	}

	$js = $dir . '/assets/js/annam-home-hero-slider.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-home-hero-slider',
			$uri . '/assets/js/annam-home-hero-slider.js',
			array(),
			(string) filemtime( $js ),
			true
		);
	}

	annam_enqueue_home_product_sections_assets( $dir, $uri );
}
add_action( 'wp_enqueue_scripts', 'annam_enqueue_page_template_home_assets', 22 );
