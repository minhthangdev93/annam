<?php
/**
 * Landing thuê limo HN–Sapa: enqueue, form hooks, page seed.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/annam-lead-mail.php';
require_once get_stylesheet_directory() . '/inc/limo-charter-landing-config.php';
require_once get_stylesheet_directory() . '/inc/limo-charter-landing-booking.php';

const ANNAM_LIMO_CHARTER_LANDING_RATE_MAX     = 8;
const ANNAM_LIMO_CHARTER_LANDING_RATE_MINUTES = 10;
const ANNAM_LIMO_CHARTER_TEMPLATE             = 'page-template-thue-xe-limousine-hn-sapa-landing.php';
const ANNAM_LIMO_CHARTER_SLUG                 = 'thue-xe-limousine-ha-noi-sapa';

/**
 * @return bool
 */
function annam_limo_charter_landing_is_template() {
	if ( ! is_singular( 'page' ) ) {
		return false;
	}
	$page_id = get_queried_object_id();
	return $page_id && ANNAM_LIMO_CHARTER_TEMPLATE === get_page_template_slug( $page_id );
}

/**
 * @param int $page_id Page ID.
 * @return array<string,mixed>
 */
function annam_limo_charter_landing_get_config( $page_id = 0 ) {
	if ( $page_id <= 0 ) {
		$page_id = get_queried_object_id();
	}
	return annam_limo_charter_landing_get_default_config( (int) $page_id );
}

/**
 * @return array{type:string,message:string}|null
 */
function annam_limo_charter_landing_get_notice() {
	if ( ! isset( $_GET['annam_limo_charter'] ) ) {
		return null;
	}
	$code = sanitize_key( wp_unslash( $_GET['annam_limo_charter'] ) );
	if ( 'sent' === $code ) {
		$config = annam_limo_charter_landing_get_config();
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
function annam_limo_charter_landing_print_image( $slot_key, array $attrs = array() ) {
	$slot_key = sanitize_key( (string) $slot_key );
	if ( '' === $slot_key ) {
		return '';
	}

	$attachment_id = function_exists( 'annam_limo_charter_landing_get_image_attachment_id' )
		? annam_limo_charter_landing_get_image_attachment_id( $slot_key )
		: 0;

	if ( $attachment_id > 0 ) {
		$size = isset( $attrs['size'] ) ? (string) $attrs['size'] : 'large';
		unset( $attrs['size'] );
		$default = array(
			'class'   => 'annam-limo-charter-img',
			'loading' => 'lazy',
			'alt'     => '',
		);
		$attrs = array_merge( $default, $attrs );
		return wp_get_attachment_image( $attachment_id, $size, false, $attrs );
	}

	$url = function_exists( 'annam_limo_charter_landing_image_url' ) ? annam_limo_charter_landing_image_url( $slot_key ) : '';
	if ( '' === $url ) {
		return '';
	}
	$alt     = isset( $attrs['alt'] ) ? (string) $attrs['alt'] : '';
	$loading = isset( $attrs['loading'] ) ? (string) $attrs['loading'] : 'lazy';
	$class   = isset( $attrs['class'] ) ? (string) $attrs['class'] : 'annam-limo-charter-img';
	$w       = isset( $attrs['width'] ) ? (string) $attrs['width'] : '';
	$h       = isset( $attrs['height'] ) ? (string) $attrs['height'] : '';
	$fp      = isset( $attrs['fetchpriority'] ) ? (string) $attrs['fetchpriority'] : '';

	return sprintf(
		'<img src="%s" alt="%s" class="%s" loading="%s"%s%s%s decoding="async" />',
		esc_url( $url ),
		esc_attr( $alt ),
		esc_attr( $class ),
		esc_attr( $loading ),
		$w ? ' width="' . esc_attr( $w ) . '"' : '',
		$h ? ' height="' . esc_attr( $h ) . '"' : '',
		$fp ? ' fetchpriority="' . esc_attr( $fp ) . '"' : ''
	);
}

/**
 * Classic POST fallback.
 */
function annam_limo_charter_landing_handle_form() {
	if ( ! annam_limo_charter_landing_is_template() ) {
		return;
	}
	if ( empty( $_POST['annam_limo_charter_submit'] ) ) {
		return;
	}
	$redirect = get_permalink();
	if ( ! $redirect ) {
		$redirect = home_url( '/' );
	}
	$input = wp_unslash( $_POST );
	$input['annam_limo_charter_page_url'] = $redirect;
	$result = annam_limo_charter_landing_process_lead( $input );
	if ( ! empty( $result['success'] ) ) {
		wp_safe_redirect( add_query_arg( 'annam_limo_charter', 'sent', $redirect ) );
		exit;
	}
	wp_safe_redirect( add_query_arg( 'annam_limo_charter', 'error', $redirect ) );
	exit;
}
add_action( 'template_redirect', 'annam_limo_charter_landing_handle_form', 2 );

/**
 * @param string $layout Layout.
 * @return string
 */
function annam_limo_charter_landing_sidebar_layout( $layout ) {
	return annam_limo_charter_landing_is_template() ? 'no-sidebar' : $layout;
}
add_filter( 'generate_sidebar_layout', 'annam_limo_charter_landing_sidebar_layout', 20 );

/**
 * @param bool $show Show header.
 * @return bool
 */
function annam_limo_charter_landing_hide_entry_header( $show ) {
	return annam_limo_charter_landing_is_template() ? false : $show;
}
add_filter( 'generate_show_entry_header', 'annam_limo_charter_landing_hide_entry_header', 12 );

/**
 * @param string[] $classes Classes.
 * @return string[]
 */
function annam_limo_charter_landing_body_class( $classes ) {
	if ( annam_limo_charter_landing_is_template() ) {
		$classes[] = 'annam-limo-charter-landing-page';
	}
	return $classes;
}
add_filter( 'body_class', 'annam_limo_charter_landing_body_class', 12 );

/**
 * Enqueue.
 */
function annam_limo_charter_landing_enqueue_assets() {
	if ( ! annam_limo_charter_landing_is_template() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$css = $dir . '/assets/css/limo-charter-landing.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-limo-charter-landing',
			$uri . '/assets/css/limo-charter-landing.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $css )
		);
	}

	$config = annam_limo_charter_landing_get_config();
	$secs   = isset( $config['sections'] ) && is_array( $config['sections'] ) ? $config['sections'] : array();

	if ( ! empty( $secs['related_tours'] ) && class_exists( 'WooCommerce' ) && function_exists( 'annam_enqueue_home_product_sections_assets' ) ) {
		annam_enqueue_home_product_sections_assets( $dir, $uri );
	}

	$js = $dir . '/assets/js/limo-charter-landing.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-limo-charter-landing',
			$uri . '/assets/js/limo-charter-landing.js',
			array(),
			(string) filemtime( $js ),
			true
		);

		$gallery_js = array();
		if ( function_exists( 'annam_limo_charter_landing_get_lightbox_items' ) ) {
			foreach ( annam_limo_charter_landing_get_lightbox_items() as $item ) {
				$gallery_js[] = array(
					'src'     => isset( $item['src'] ) ? (string) $item['src'] : '',
					'caption' => isset( $item['caption'] ) ? (string) $item['caption'] : '',
					'slot'    => isset( $item['slot'] ) ? (string) $item['slot'] : '',
				);
			}
		}

		$defs = isset( $config['form_defaults'] ) && is_array( $config['form_defaults'] ) ? $config['form_defaults'] : array();

		wp_localize_script(
			'annam-limo-charter-landing',
			'annamLimoCharterLanding',
			array(
				'formId'       => 'annam-limo-charter-booking',
				'gallery'      => $gallery_js,
				'formDefaults' => $defs,
				'booking'      => array(
					'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
					'action'      => 'annam_limo_charter_lead',
					'nonceAction' => 'annam_limo_charter_lead_nonce',
					'nonce'       => wp_create_nonce( 'annam_limo_charter_lead' ),
					'pageUrl'     => get_permalink() ? get_permalink() : home_url( '/' ),
					'dateToday'   => wp_date( 'Y-m-d' ),
				),
				'i18n'         => array(
					'sending'     => __( 'Đang gửi...', 'generatepress_child' ),
					'submitError' => __( 'Không gửi được. Vui lòng thử lại hoặc gọi hotline.', 'generatepress_child' ),
				),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_limo_charter_landing_enqueue_assets', 24 );

/**
 * @return array{title:string,description:string}
 */
function annam_limo_charter_landing_get_seo_defaults() {
	$config = function_exists( 'annam_limo_charter_landing_get_default_config' )
		? annam_limo_charter_landing_get_default_config( 0 )
		: array();
	$seo    = isset( $config['seo'] ) && is_array( $config['seo'] ) ? $config['seo'] : array();

	return array(
		'title'       => isset( $seo['title'] ) ? (string) $seo['title'] : 'Thuê Xe Limousine Hà Nội Sapa | Từ 3.800.000đ',
		'description' => isset( $seo['description'] ) ? (string) $seo['description'] : 'Thuê nguyên xe limousine Hà Nội – Sapa có tài xế. Hà Nội → Sapa 3.800.000đ · Sapa → Hà Nội 4.200.000đ/chiều.',
	);
}

/**
 * @param int $page_id Page ID.
 */
function annam_limo_charter_landing_seed_rank_math_meta( $page_id ) {
	$page_id = (int) $page_id;
	if ( $page_id <= 0 ) {
		return;
	}
	$seo = annam_limo_charter_landing_get_seo_defaults();
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
function annam_limo_charter_landing_rank_math_title( $title ) {
	if ( ! annam_limo_charter_landing_is_template() ) {
		return $title;
	}
	$page_id = get_queried_object_id();
	$custom  = $page_id ? (string) get_post_meta( $page_id, 'rank_math_title', true ) : '';
	if ( '' !== trim( $custom ) ) {
		return $title;
	}
	return annam_limo_charter_landing_get_seo_defaults()['title'];
}
add_filter( 'rank_math/frontend/title', 'annam_limo_charter_landing_rank_math_title', 99 );

/**
 * @param string $desc Description.
 * @return string
 */
function annam_limo_charter_landing_rank_math_description( $desc ) {
	if ( ! annam_limo_charter_landing_is_template() ) {
		return $desc;
	}
	$page_id = get_queried_object_id();
	$custom  = $page_id ? (string) get_post_meta( $page_id, 'rank_math_description', true ) : '';
	if ( '' !== trim( $custom ) ) {
		return $desc;
	}
	return annam_limo_charter_landing_get_seo_defaults()['description'];
}
add_filter( 'rank_math/frontend/description', 'annam_limo_charter_landing_rank_math_description', 99 );

/**
 * @param string $title Title.
 * @return string
 */
function annam_limo_charter_landing_document_title( $title ) {
	if ( ! annam_limo_charter_landing_is_template() ) {
		return $title;
	}
	$page_id = get_queried_object_id();
	$custom  = $page_id ? (string) get_post_meta( $page_id, 'rank_math_title', true ) : '';
	if ( '' !== trim( $custom ) ) {
		return $title;
	}
	return annam_limo_charter_landing_get_seo_defaults()['title'];
}
add_filter( 'pre_get_document_title', 'annam_limo_charter_landing_document_title', 99 );

/**
 * Maybe create landing page once.
 */
function annam_limo_charter_landing_maybe_create_page() {
	if ( get_option( 'annam_limo_charter_landing_page_created' ) ) {
		$page = get_page_by_path( ANNAM_LIMO_CHARTER_SLUG );
		if ( $page && ! get_option( 'annam_limo_charter_landing_seo_seeded' ) ) {
			annam_limo_charter_landing_seed_rank_math_meta( (int) $page->ID );
			update_option( 'annam_limo_charter_landing_seo_seeded', 1, false );
		}
		return;
	}
	$existing = get_page_by_path( ANNAM_LIMO_CHARTER_SLUG );
	if ( $existing ) {
		update_post_meta( (int) $existing->ID, '_wp_page_template', ANNAM_LIMO_CHARTER_TEMPLATE );
		annam_limo_charter_landing_seed_rank_math_meta( (int) $existing->ID );
		update_option( 'annam_limo_charter_landing_page_created', 1, false );
		update_option( 'annam_limo_charter_landing_seo_seeded', 1, false );
		return;
	}
	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Thuê xe Limousine Hà Nội – Sapa',
			'post_name'    => ANNAM_LIMO_CHARTER_SLUG,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		),
		true
	);
	if ( ! is_wp_error( $page_id ) && $page_id ) {
		update_post_meta( $page_id, '_wp_page_template', ANNAM_LIMO_CHARTER_TEMPLATE );
		annam_limo_charter_landing_seed_rank_math_meta( (int) $page_id );
		update_option( 'annam_limo_charter_landing_page_created', 1, false );
		update_option( 'annam_limo_charter_landing_seo_seeded', 1, false );
	}
}
add_action( 'init', 'annam_limo_charter_landing_maybe_create_page', 30 );
