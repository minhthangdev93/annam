<?php
/**
 * Tối ưu hiệu năng (PageSpeed / Core Web Vitals) — chỉ child theme.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/** @var string Kích thước ảnh card tour / loop. */
const ANNAM_TOUR_CARD_IMAGE_SIZE = 'annam-tour-card';

/** @var string Kích thước ảnh card danh mục trên home/archive. */
const ANNAM_CAT_NAV_IMAGE_SIZE = 'annam-cat-nav';

/** @var string Ảnh gallery chính landing cabin (16:10). */
const ANNAM_CABIN_GALLERY_MAIN_SIZE = 'annam-cabin-gallery-main';

/** @var string Ảnh gallery phụ / thẻ cabin landing. */
const ANNAM_CABIN_GALLERY_THUMB_SIZE = 'annam-cabin-gallery-thumb';

/** @var string Ảnh thẻ loại cabin. */
const ANNAM_CABIN_CARD_IMAGE_SIZE = 'annam-cabin-card';

/** @var string Ảnh thumbnail card tin tức / cẩm nang (16:9). */
const ANNAM_BLOG_CARD_IMAGE_SIZE = 'annam-blog-card';

/**
 * Đăng ký kích thước ảnh phù hợp card (tránh load full/woocommerce_single quá lớn).
 */
function annam_performance_register_image_sizes() {
	/* 4:3 — khớp .annam-tour-card__image (aspect-ratio 4/3) & ảnh đại diện tour 800×600 */
	add_image_size( ANNAM_TOUR_CARD_IMAGE_SIZE, 800, 600, true );
	/* Vuông — khớp UI card; tránh bản crop 520×320 (ngang) gây hiểu nhầm khi ảnh gốc vuông */
	add_image_size( ANNAM_CAT_NAV_IMAGE_SIZE, 520, 520, true );
	/* Landing Cabin VIP */
	add_image_size( ANNAM_CABIN_GALLERY_MAIN_SIZE, 1200, 750, true );
	add_image_size( ANNAM_CABIN_GALLERY_THUMB_SIZE, 600, 450, true );
	add_image_size( ANNAM_CABIN_CARD_IMAGE_SIZE, 800, 600, true );
	/* 16:9 — khớp .annam-blog-card__image-link (archive cẩm nang) */
	add_image_size( ANNAM_BLOG_CARD_IMAGE_SIZE, 1200, 675, true );
}
add_action( 'after_setup_theme', 'annam_performance_register_image_sizes', 20 );

/**
 * Kích thước ảnh dùng trong loop tour card.
 *
 * @return string
 */
function annam_get_product_card_image_size() {
	return apply_filters( 'annam_product_card_image_size', ANNAM_TOUR_CARD_IMAGE_SIZE );
}

/**
 * Kích thước ảnh gallery chi tiết tour (800×600, 4:3).
 *
 * @return string
 */
function annam_get_tour_gallery_image_size() {
	return apply_filters( 'annam_tour_gallery_image_size', ANNAM_TOUR_CARD_IMAGE_SIZE );
}

/**
 * Kích thước ảnh card danh mục (home / nav).
 *
 * @return string
 */
function annam_get_category_nav_card_image_size() {
	return apply_filters( 'annam_category_nav_card_image_size', ANNAM_CAT_NAV_IMAGE_SIZE );
}

/**
 * Kích thước ảnh thumbnail card tin tức (1200×675, 16:9).
 *
 * @return string
 */
function annam_get_blog_card_image_size() {
	return apply_filters( 'annam_blog_card_image_size', ANNAM_BLOG_CARD_IMAGE_SIZE );
}

/**
 * Bump version cache section trang chủ (transient key dùng hậu tố _v{generation}).
 */
function annam_home_section_cache_bump_generation() {
	update_option( 'annam_home_sections_cache_gen', time(), false );
}

/**
 * Invalidate cache section trang chủ khi sản phẩm / danh mục thay đổi.
 */
function annam_home_section_cache_hooks() {
	add_action( 'save_post_product', 'annam_home_section_cache_bump_generation', 30 );
	add_action( 'woocommerce_update_product', 'annam_home_section_cache_bump_generation', 30 );
	add_action( 'edited_product_cat', 'annam_home_section_cache_bump_generation', 30 );
	add_action( 'created_product_cat', 'annam_home_section_cache_bump_generation', 30 );
	add_action( 'delete_product_cat', 'annam_home_section_cache_bump_generation', 30 );
}
add_action( 'init', 'annam_home_section_cache_hooks' );

/**
 * Script custom child theme: defer (không chặn parse; WP 6.3+ strategy).
 */
function annam_performance_defer_child_scripts() {
	$handles = array(
		'annam-header-custom',
		'annam-home-hero-slider',
		'annam-home-product-sections',
		'annam-woo-category-tour',
		'annam-recently-viewed-tours',
		'annam-product-reviews',
		'annam-contact-page',
		'annam-single-tour-lightbox',
		'annam-tour-lead-form',
		'annam-single-tour-related',
		'annam-cabin-landing',
		'annam-car-rental-landing',
		'generate-menu',
	);

	foreach ( $handles as $handle ) {
		if ( wp_script_is( $handle, 'enqueued' ) ) {
			wp_script_add_data( $handle, 'strategy', 'defer' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'annam_performance_defer_child_scripts', 101 );

/**
 * WooCommerce: giảm JS không cần trên trang không dùng shop (cart fragments).
 */
function annam_performance_dequeue_wc_cart_fragments() {
	if ( is_admin() || ! function_exists( 'is_woocommerce' ) ) {
		return;
	}
	if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		return;
	}
	wp_dequeue_script( 'wc-cart-fragments' );
}
add_action( 'wp_enqueue_scripts', 'annam_performance_dequeue_wc_cart_fragments', 100 );

/**
 * Landing Cabin VIP: bỏ CSS/JS WooCommerce shop (chỉ dùng card tour tùy biến).
 */
function annam_performance_optimize_cabin_landing_assets() {
	if ( ! function_exists( 'annam_cabin_landing_is_template' ) || ! annam_cabin_landing_is_template() ) {
		return;
	}

	$wc_styles = array(
		'wc-blocks-style',
		'woocommerce-layout',
		'woocommerce-smallscreen',
		'woocommerce-general',
	);
	foreach ( $wc_styles as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}

	$wc_scripts = array(
		'wc-add-to-cart',
		'woocommerce',
		'wc-jquery-blockui',
		'wc-js-cookie',
		'sourcebuster-js',
		'wc-order-attribution',
		'jquery-blockui',
	);
	foreach ( $wc_scripts as $handle ) {
		wp_dequeue_script( $handle );
		wp_deregister_script( $handle );
	}

	if ( ! is_customize_preview() ) {
		wp_dequeue_script( 'jquery-migrate' );
	}
}
add_action( 'wp_enqueue_scripts', 'annam_performance_optimize_cabin_landing_assets', 99 );

/**
 * Landing Ads: tắt slider hệ sinh thái footer (CSS/JS) — bật lại: add_filter( 'annam_footer_show_ecosystem_on_cabin_landing', '__return_true' );
 *
 * @param bool $show Show ecosystem block.
 * @return bool
 */
function annam_performance_cabin_landing_footer_ecosystem( $show ) {
	if ( ! $show ) {
		return false;
	}
	if ( ! function_exists( 'annam_cabin_landing_is_template' ) || ! annam_cabin_landing_is_template() ) {
		return $show;
	}
	return (bool) apply_filters( 'annam_footer_show_ecosystem_on_cabin_landing', false );
}
add_filter( 'annam_footer_show_ecosystem', 'annam_performance_cabin_landing_footer_ecosystem', 20 );

/**
 * Preload font 700 (H1 landing) — bổ sung preload 400 toàn site.
 */
function annam_performance_preload_cabin_landing_font_bold() {
	if ( ! function_exists( 'annam_cabin_landing_is_template' ) || ! annam_cabin_landing_is_template() ) {
		return;
	}
	$uri = get_stylesheet_directory_uri() . '/assets/font/be-vietnam-pro/QdVMSTAyLFyeg_IDWvOJmVES_HSMIG81Rb0.woff2';
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin />' . "\n",
		esc_url( $uri )
	);
}
add_action( 'wp_head', 'annam_performance_preload_cabin_landing_font_bold', 2 );

/**
 * LCP trang chủ: preload ảnh hero đúng viewport (tránh chỉ tải bản desktop trên mobile).
 */
function annam_performance_preload_home_hero_lcp() {
	if ( is_admin() ) {
		return;
	}
	if ( ! function_exists( 'annam_is_page_template_trang_chu' ) || ! annam_is_page_template_trang_chu() ) {
		return;
	}
	if ( function_exists( 'annam_get_home_sliders' ) ) {
		$custom = annam_get_home_sliders();
		if ( ! empty( $custom[0] ) && ! empty( $custom[0]['desktop_src'] ) ) {
			$desktop = (string) $custom[0]['desktop_src'];
			$mobile  = isset( $custom[0]['mobile_src'] ) ? (string) $custom[0]['mobile_src'] : $desktop;
			$two     = '' !== $desktop && '' !== $mobile && $desktop !== $mobile;

			$preload_mobile  = $mobile;
			$preload_desktop = $desktop;
			if ( function_exists( 'annam_home_hero_matching_webp_url' ) ) {
				$mw = annam_home_hero_matching_webp_url( $mobile );
				if ( '' !== $mw ) {
					$preload_mobile = $mw;
				}
				$dw = annam_home_hero_matching_webp_url( $desktop );
				if ( '' !== $dw ) {
					$preload_desktop = $dw;
				}
			}

			if ( $two ) {
				printf(
					'<link rel="preload" as="image" href="%s" media="(max-width: 768px)" />' . "\n",
					esc_url( $preload_mobile )
				);
				printf(
					'<link rel="preload" as="image" href="%s" media="(min-width: 769px)" />' . "\n",
					esc_url( $preload_desktop )
				);
			} elseif ( '' !== $desktop ) {
				$pw  = function_exists( 'annam_home_hero_matching_webp_url' ) ? annam_home_hero_matching_webp_url( $desktop ) : '';
				$one = '' !== $pw ? $pw : $desktop;
				printf(
					'<link rel="preload" as="image" href="%s" />' . "\n",
					esc_url( $one )
				);
			}
			return;
		}
	}

	if ( ! function_exists( 'annam_home_hero_get_slide_rows' ) ) {
		return;
	}
	$slides = annam_home_hero_get_slide_rows();
	if ( empty( $slides ) ) {
		return;
	}
	$desktop = isset( $slides[0]['src'] ) ? (string) $slides[0]['src'] : '';
	$mobile  = isset( $slides[1]['src'] ) ? (string) $slides[1]['src'] : $desktop;
	$two     = count( $slides ) >= 2 && '' !== $desktop && '' !== $mobile && $desktop !== $mobile;

	$preload_mobile = $mobile;
	$preload_desktop = $desktop;
	if ( function_exists( 'annam_home_hero_matching_webp_url' ) ) {
		$mw = annam_home_hero_matching_webp_url( $mobile );
		if ( '' !== $mw ) {
			$preload_mobile = $mw;
		}
		$dw = annam_home_hero_matching_webp_url( $desktop );
		if ( '' !== $dw ) {
			$preload_desktop = $dw;
		}
	}

	if ( $two ) {
		printf(
			'<link rel="preload" as="image" href="%s" media="(max-width: 768px)" />' . "\n",
			esc_url( $preload_mobile )
		);
		printf(
			'<link rel="preload" as="image" href="%s" media="(min-width: 769px)" />' . "\n",
			esc_url( $preload_desktop )
		);
	} elseif ( '' !== $desktop ) {
		$pw  = function_exists( 'annam_home_hero_matching_webp_url' ) ? annam_home_hero_matching_webp_url( $desktop ) : '';
		$one = '' !== $pw ? $pw : $desktop;
		printf(
			'<link rel="preload" as="image" href="%s" />' . "\n",
			esc_url( $one )
		);
	}
}
add_action( 'wp_head', 'annam_performance_preload_home_hero_lcp', 1 );

/**
 * Preload font UI chính (Be Vietnam Pro 400 latin subset — giảm chờ LCP text).
 */
function annam_performance_preload_primary_font() {
	if ( is_admin() ) {
		return;
	}
	$uri = get_stylesheet_directory_uri() . '/assets/font/be-vietnam-pro/QdVPSTAyLFyeg_IDWvOJmVES_Hw3BXo.woff2';
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin />' . "\n",
		esc_url( $uri )
	);
}
add_action( 'wp_head', 'annam_performance_preload_primary_font', 2 );

/**
 * LCP: preload ảnh hero danh mục (thumbnail term, size large).
 */
function annam_performance_preload_product_category_hero() {
	if ( is_admin() || ! function_exists( 'is_product_category' ) || ! is_product_category() ) {
		return;
	}
	$term = get_queried_object();
	if ( ! $term instanceof WP_Term || 'product_cat' !== $term->taxonomy ) {
		return;
	}
	$thumb_id = (int) get_term_meta( (int) $term->term_id, 'thumbnail_id', true );
	if ( $thumb_id <= 0 ) {
		return;
	}
	$size = apply_filters( 'annam_product_category_hero_image_size', 'large' );
	$href = wp_get_attachment_image_url( $thumb_id, $size );
	if ( ! $href ) {
		return;
	}
	printf(
		'<link rel="preload" as="image" href="%s" />' . "\n",
		esc_url( $href )
	);
}
add_action( 'wp_head', 'annam_performance_preload_product_category_hero', 3 );
