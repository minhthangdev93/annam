<?php
/**
 * Landing Tour Sapa 3N2Đ: enqueue, form hooks.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/tour-sapa-landing-config.php';
require_once get_stylesheet_directory() . '/inc/tour-sapa-landing-booking.php';

const ANNAM_TOUR_SAPA_LANDING_RATE_MAX     = 8;
const ANNAM_TOUR_SAPA_LANDING_RATE_MINUTES = 10;

/**
 * @return bool
 */
function annam_tour_sapa_landing_is_template() {
	if ( ! is_singular( 'page' ) ) {
		return false;
	}
	$page_id = get_queried_object_id();
	return $page_id && 'page-template-tour-sapa-3n2d-landing.php' === get_page_template_slug( $page_id );
}

/**
 * SVG icon cho điểm nổi bật (stroke, 24×24).
 *
 * @param string $icon Icon key.
 * @return string Safe SVG markup.
 */
function annam_tour_sapa_landing_highlight_icon_svg( $icon ) {
	$icon = sanitize_key( (string) $icon );
	$paths = array(
		'village' => '<path d="M3 21h18"/><path d="M5 21V10l7-5 7 5v11"/><path d="M9 21v-6h6v6"/>',
		'peak'    => '<path d="m3 20 6.5-11 3.5 5 2.5-4L21 20"/><path d="M12 4v3"/>',
		'sun'     => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
		'car'     => '<path d="M5 17h14v-5l-2-4H7l-2 4v5z"/><path d="M5 12h14"/><circle cx="7.5" cy="17" r="1.5"/><circle cx="16.5" cy="17" r="1.5"/>',
		'gift'    => '<path d="M20 12v8a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-8"/><path d="M4 8h16v4H4z"/><path d="M12 8v13"/><path d="M12 8c-1.5-3-4-3.5-5-2s0 3.5 2.5 3h2.5"/><path d="M12 8c1.5-3 4-3.5 5-2s0 3.5-2.5 3H12"/>',
		'stay'    => '<path d="M3 18V9a2 2 0 0 1 2-2h6v11"/><path d="M11 11h8a2 2 0 0 1 2 2v5"/><path d="M3 18h18"/><path d="M7 7V5"/>',
	);
	$inner = isset( $paths[ $icon ] ) ? $paths[ $icon ] : $paths['village'];

	return '<svg class="annam-tour-sapa-highlight__svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $inner . '</svg>';
}

/**
 * @param int $page_id Page ID.
 * @return array<string,mixed>
 */
function annam_tour_sapa_landing_get_config( $page_id = 0 ) {
	if ( $page_id <= 0 ) {
		$page_id = get_queried_object_id();
	}
	$config = annam_tour_sapa_landing_get_default_config( (int) $page_id );

	// Thư viện ảnh admin ghi đè experience (động, có thể >6).
	if ( function_exists( 'annam_tour_sapa_landing_get_experience_from_gallery' ) ) {
		$from_gallery = annam_tour_sapa_landing_get_experience_from_gallery();
		if ( ! empty( $from_gallery ) ) {
			$config['experience'] = $from_gallery;
		}
	}

	return $config;
}

/**
 * @return array{type:string,message:string}|null
 */
function annam_tour_sapa_landing_get_notice() {
	if ( ! isset( $_GET['annam_tour_sapa'] ) ) {
		return null;
	}
	$code = sanitize_key( wp_unslash( $_GET['annam_tour_sapa'] ) );
	if ( 'sent' === $code ) {
		$config = annam_tour_sapa_landing_get_config();
		$msg    = isset( $config['form']['success_message'] ) ? $config['form']['success_message'] : '';
		return array(
			'type'    => 'success',
			'message' => $msg,
		);
	}
	if ( 'error' === $code ) {
		return array(
			'type'    => 'error',
			'message' => __( 'Không gửi được yêu cầu. Vui lòng gọi hotline hoặc nhắn Zalo.', 'generatepress_child' ),
		);
	}
	return null;
}

/**
 * @param string               $slot_key Slot.
 * @param array<string,string> $attrs    Attrs.
 * @return string
 */
function annam_tour_sapa_landing_print_image( $slot_key, array $attrs = array() ) {
	$slot_key = sanitize_key( (string) $slot_key );
	if ( '' === $slot_key ) {
		return '';
	}

	$attachment_id = function_exists( 'annam_tour_sapa_landing_get_image_attachment_id' )
		? annam_tour_sapa_landing_get_image_attachment_id( $slot_key )
		: 0;

	if ( $attachment_id > 0 ) {
		$default = array(
			'class'   => 'annam-tour-sapa-img',
			'loading' => 'lazy',
			'alt'     => '',
		);
		$attrs = array_merge( $default, $attrs );
		return wp_get_attachment_image( $attachment_id, 'large', false, $attrs );
	}

	$url = function_exists( 'annam_tour_sapa_landing_image_url' ) ? annam_tour_sapa_landing_image_url( $slot_key ) : '';
	if ( '' === $url ) {
		return '';
	}
	$alt     = isset( $attrs['alt'] ) ? (string) $attrs['alt'] : '';
	$loading = isset( $attrs['loading'] ) ? (string) $attrs['loading'] : 'lazy';
	$class   = isset( $attrs['class'] ) ? (string) $attrs['class'] : 'annam-tour-sapa-img';
	$w       = isset( $attrs['width'] ) ? (string) $attrs['width'] : '';
	$h       = isset( $attrs['height'] ) ? (string) $attrs['height'] : '';

	return sprintf(
		'<img src="%s" alt="%s" class="%s" loading="%s"%s%s decoding="async" />',
		esc_url( $url ),
		esc_attr( $alt ),
		esc_attr( $class ),
		esc_attr( $loading ),
		$w ? ' width="' . esc_attr( $w ) . '"' : '',
		$h ? ' height="' . esc_attr( $h ) . '"' : ''
	);
}

/**
 * Classic POST fallback.
 */
function annam_tour_sapa_landing_handle_form() {
	if ( ! annam_tour_sapa_landing_is_template() ) {
		return;
	}
	if ( empty( $_POST['annam_tour_sapa_submit'] ) ) {
		return;
	}
	$redirect = get_permalink();
	if ( ! $redirect ) {
		$redirect = home_url( '/' );
	}
	$input = wp_unslash( $_POST );
	$input['annam_tour_sapa_page_url'] = $redirect;
	$result = annam_tour_sapa_landing_process_booking( $input );
	if ( ! empty( $result['success'] ) ) {
		wp_safe_redirect( add_query_arg( 'annam_tour_sapa', 'sent', $redirect ) );
		exit;
	}
	wp_safe_redirect( add_query_arg( 'annam_tour_sapa', 'error', $redirect ) );
	exit;
}
add_action( 'template_redirect', 'annam_tour_sapa_landing_handle_form', 2 );

/**
 * @param string $layout Layout.
 * @return string
 */
function annam_tour_sapa_landing_sidebar_layout( $layout ) {
	return annam_tour_sapa_landing_is_template() ? 'no-sidebar' : $layout;
}
add_filter( 'generate_sidebar_layout', 'annam_tour_sapa_landing_sidebar_layout', 20 );

/**
 * @param bool $show Show header.
 * @return bool
 */
function annam_tour_sapa_landing_hide_entry_header( $show ) {
	return annam_tour_sapa_landing_is_template() ? false : $show;
}
add_filter( 'generate_show_entry_header', 'annam_tour_sapa_landing_hide_entry_header', 12 );

/**
 * @param string[] $classes Classes.
 * @return string[]
 */
function annam_tour_sapa_landing_body_class( $classes ) {
	if ( annam_tour_sapa_landing_is_template() ) {
		$classes[] = 'annam-tour-sapa-landing-page';
	}
	return $classes;
}
add_filter( 'body_class', 'annam_tour_sapa_landing_body_class', 12 );

/**
 * Enqueue.
 */
function annam_tour_sapa_landing_enqueue_assets() {
	if ( ! annam_tour_sapa_landing_is_template() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$css = $dir . '/assets/css/tour-sapa-landing.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-tour-sapa-landing',
			$uri . '/assets/css/tour-sapa-landing.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $css )
		);
	}

	$config = annam_tour_sapa_landing_get_config();
	$secs   = isset( $config['sections'] ) && is_array( $config['sections'] ) ? $config['sections'] : array();

	if ( ! empty( $secs['related_tours'] ) && class_exists( 'WooCommerce' ) && function_exists( 'annam_enqueue_home_product_sections_assets' ) ) {
		annam_enqueue_home_product_sections_assets( $dir, $uri );
	}

	$js = $dir . '/assets/js/tour-sapa-landing.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-tour-sapa-landing',
			$uri . '/assets/js/tour-sapa-landing.js',
			array(),
			(string) filemtime( $js ),
			true
		);

		$gallery_js = array();
		if ( function_exists( 'annam_tour_sapa_landing_get_gallery_items' ) ) {
			foreach ( annam_tour_sapa_landing_get_gallery_items() as $item ) {
				if ( empty( $item['url'] ) ) {
					continue;
				}
				$gallery_js[] = array(
					'src'     => (string) $item['url'],
					'caption' => isset( $item['caption'] ) ? (string) $item['caption'] : '',
				);
			}
		} elseif ( ! empty( $config['experience'] ) ) {
			foreach ( $config['experience'] as $item ) {
				$slot = isset( $item['slot'] ) ? (string) $item['slot'] : '';
				$url  = $slot && function_exists( 'annam_tour_sapa_landing_image_url' )
					? annam_tour_sapa_landing_image_url( $slot )
					: '';
				if ( '' !== $url ) {
					$gallery_js[] = array(
						'src'     => $url,
						'caption' => isset( $item['caption'] ) ? (string) $item['caption'] : '',
					);
				}
			}
		}

		wp_localize_script(
			'annam-tour-sapa-landing',
			'annamTourSapaLanding',
			array(
				'formId'  => 'annam-tour-sapa-booking',
				'gallery' => $gallery_js,
				'booking' => array(
					'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
					'action'      => 'annam_tour_sapa_booking',
					'nonceAction' => 'annam_tour_sapa_booking_nonce',
					'nonce'       => wp_create_nonce( 'annam_tour_sapa_booking' ),
					'pageUrl'     => get_permalink() ? get_permalink() : home_url( '/' ),
					'dateToday'   => wp_date( 'Y-m-d' ),
				),
				'i18n'    => array(
					'sending'     => __( 'Đang gửi...', 'generatepress_child' ),
					'submitError' => __( 'Không gửi được. Vui lòng thử lại hoặc gọi hotline.', 'generatepress_child' ),
				),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_tour_sapa_landing_enqueue_assets', 24 );

/**
 * Default SEO strings for Ads keyword match.
 *
 * @return array{title:string,description:string}
 */
function annam_tour_sapa_landing_get_seo_defaults() {
	$config = function_exists( 'annam_tour_sapa_landing_get_default_config' )
		? annam_tour_sapa_landing_get_default_config( 0 )
		: array();
	$seo    = isset( $config['seo'] ) && is_array( $config['seo'] ) ? $config['seo'] : array();

	return array(
		'title'       => isset( $seo['title'] ) ? (string) $seo['title'] : 'Tour Sapa 3 Ngày 2 Đêm (3N2Đ) | Du Lịch Sapa Từ 2.990.000đ',
		'description' => isset( $seo['description'] ) ? (string) $seo['description'] : 'Du lịch Sapa 3 ngày 2 đêm: Cát Cát – Fansipan – Moana. Giá tour Sapa từ 2.990.000đ.',
	);
}

/**
 * Seed Rank Math title/description when empty (does not overwrite editor values).
 *
 * @param int $page_id Page ID.
 */
function annam_tour_sapa_landing_seed_rank_math_meta( $page_id ) {
	$page_id = (int) $page_id;
	if ( $page_id <= 0 ) {
		return;
	}
	$seo = annam_tour_sapa_landing_get_seo_defaults();
	if ( '' === (string) get_post_meta( $page_id, 'rank_math_title', true ) ) {
		update_post_meta( $page_id, 'rank_math_title', $seo['title'] );
	}
	if ( '' === (string) get_post_meta( $page_id, 'rank_math_description', true ) ) {
		update_post_meta( $page_id, 'rank_math_description', $seo['description'] );
	}
}

/**
 * @param string $title Title.
 * @return string
 */
function annam_tour_sapa_landing_rank_math_title( $title ) {
	if ( ! annam_tour_sapa_landing_is_template() ) {
		return $title;
	}
	$page_id = get_queried_object_id();
	$custom  = $page_id ? (string) get_post_meta( $page_id, 'rank_math_title', true ) : '';
	if ( '' !== trim( $custom ) ) {
		return $title;
	}
	return annam_tour_sapa_landing_get_seo_defaults()['title'];
}
add_filter( 'rank_math/frontend/title', 'annam_tour_sapa_landing_rank_math_title', 99 );

/**
 * @param string $desc Description.
 * @return string
 */
function annam_tour_sapa_landing_rank_math_description( $desc ) {
	if ( ! annam_tour_sapa_landing_is_template() ) {
		return $desc;
	}
	$page_id = get_queried_object_id();
	$custom  = $page_id ? (string) get_post_meta( $page_id, 'rank_math_description', true ) : '';
	if ( '' !== trim( $custom ) ) {
		return $desc;
	}
	return annam_tour_sapa_landing_get_seo_defaults()['description'];
}
add_filter( 'rank_math/frontend/description', 'annam_tour_sapa_landing_rank_math_description', 99 );

/**
 * Fallback document title when Rank Math inactive / empty.
 *
 * @param string $title Title.
 * @return string
 */
function annam_tour_sapa_landing_document_title( $title ) {
	if ( ! annam_tour_sapa_landing_is_template() ) {
		return $title;
	}
	$page_id = get_queried_object_id();
	$custom  = $page_id ? (string) get_post_meta( $page_id, 'rank_math_title', true ) : '';
	if ( '' !== trim( $custom ) ) {
		return $title;
	}
	return annam_tour_sapa_landing_get_seo_defaults()['title'];
}
add_filter( 'pre_get_document_title', 'annam_tour_sapa_landing_document_title', 99 );

/**
 * Maybe create landing page once.
 */
function annam_tour_sapa_landing_maybe_create_page() {
	if ( get_option( 'annam_tour_sapa_landing_page_created' ) ) {
		$page = get_page_by_path( 'tour-sapa-3-ngay-2-dem' );
		if ( $page && ! get_option( 'annam_tour_sapa_landing_seo_seeded' ) ) {
			annam_tour_sapa_landing_seed_rank_math_meta( (int) $page->ID );
			update_option( 'annam_tour_sapa_landing_seo_seeded', 1, false );
		}
		return;
	}
	$existing = get_page_by_path( 'tour-sapa-3-ngay-2-dem' );
	if ( $existing ) {
		annam_tour_sapa_landing_seed_rank_math_meta( (int) $existing->ID );
		update_option( 'annam_tour_sapa_landing_page_created', 1, false );
		update_option( 'annam_tour_sapa_landing_seo_seeded', 1, false );
		return;
	}
	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Tour Sapa 3 ngày 2 đêm',
			'post_name'    => 'tour-sapa-3-ngay-2-dem',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		),
		true
	);
	if ( ! is_wp_error( $page_id ) && $page_id ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-template-tour-sapa-3n2d-landing.php' );
		annam_tour_sapa_landing_seed_rank_math_meta( (int) $page_id );
		update_option( 'annam_tour_sapa_landing_page_created', 1, false );
		update_option( 'annam_tour_sapa_landing_seo_seeded', 1, false );
	}
}
add_action( 'init', 'annam_tour_sapa_landing_maybe_create_page', 30 );
