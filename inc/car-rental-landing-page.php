<?php
/**
 * Landing thuê xe hợp đồng: enqueue, layout, form POST.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/car-rental-landing-config.php';
require_once get_stylesheet_directory() . '/inc/car-rental-landing-booking.php';
require_once get_stylesheet_directory() . '/inc/car-rental-landing-images-admin.php';

/**
 * @return bool
 */
function annam_car_rental_is_landing_template() {
	if ( ! is_singular( 'page' ) ) {
		return false;
	}
	$slug = get_page_template_slug( get_queried_object_id() );
	return in_array( $slug, array( 'page-template-thue-xe-hub.php', 'page-template-thue-xe-landing.php' ), true );
}

/**
 * @return array{type:string,message:string}|null
 */
function annam_car_rental_get_notice() {
	if ( ! isset( $_GET['annam_cr'] ) ) {
		return null;
	}
	$code = sanitize_key( wp_unslash( $_GET['annam_cr'] ) );
	if ( 'sent' === $code ) {
		$config = annam_car_rental_get_landing_config();
		return array(
			'type'    => 'success',
			'message' => $config['form']['success_message'] ?? '',
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
 * Tiêu đề section quy trình: Quy trình {tên trang} đơn giản.
 *
 * @param int $page_id Page ID; 0 = trang hiện tại.
 * @return string
 */
function annam_car_rental_get_steps_section_title( $page_id = 0 ) {
	$page_id = $page_id ? (int) $page_id : (int) get_the_ID();
	$name    = $page_id ? get_the_title( $page_id ) : '';

	if ( '' === $name ) {
		$name = __( 'thuê xe', 'generatepress_child' );
	}

	return sprintf(
		/* translators: %s: page title e.g. Thuê xe Limousine 9–11 chỗ */
		__( 'Quy trình %s đơn giản', 'generatepress_child' ),
		$name
	);
}

/**
 * Page editor có nội dung (không rỗng sau khi strip tag).
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function annam_car_rental_page_has_editor_content( $post_id = 0 ) {
	if ( $post_id <= 0 ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id ) {
		return false;
	}
	$raw = get_post_field( 'post_content', $post_id );
	return is_string( $raw ) && '' !== trim( wp_strip_all_tags( $raw ) );
}

/**
 * HTML nội dung page sau filter the_content.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function annam_car_rental_get_page_content_html( $post_id = 0 ) {
	if ( $post_id <= 0 ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id || ! annam_car_rental_page_has_editor_content( $post_id ) ) {
		return '';
	}
	$raw = get_post_field( 'post_content', $post_id );
	if ( ! is_string( $raw ) ) {
		return '';
	}
	return (string) apply_filters( 'the_content', $raw );
}

/**
 * Inline SVG icons (stroke for UI, fill for decorative cards).
 *
 * @param string $name Icon name.
 * @return string
 */
function annam_car_rental_icon( $name ) {
	if ( 'call' === $name && function_exists( 'strip_theme_get_svg' ) ) {
		$svg = strip_theme_get_svg( 'phone-incoming' );
		if ( '' !== $svg ) {
			$svg = preg_replace( '/\sclass="[^"]*"/', '', $svg );
			$svg = preg_replace( '/<svg/', '<svg class="annam-cr-icon annam-cr-icon--stroke"', $svg, 1 );
			$svg = preg_replace( '/\s(width|height)="[^"]*"/', '', $svg );
			return $svg;
		}
	}

	$stroke_icons = array(
		'call'              => '<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>',
		'phone'             => '<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>',
		'chat'              => '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>',
		'location_on'       => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0116 0z"/><circle cx="12" cy="10" r="3"/>',
		'calendar_today'    => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
		'route'             => '<circle cx="6" cy="19" r="3"/><circle cx="18" cy="5" r="3"/><path d="M8.5 17.5L15.5 7.5"/>',
		'person_pin_circle' => '<circle cx="12" cy="10" r="3"/><path d="M12 21a7 7 0 007-7c0-2.5-1.5-4.5-3.5-5.5S12 7 12 7s-1 .5-3.5 1.5S5 12.5 5 14a7 7 0 007 7z"/>',
		'request_quote'     => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
		'directions'        => '<path d="M3 11l19-9-9 19-2-8-8-2z"/>',
		'check_circle'      => '<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
		'arrow_forward'     => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
		'groups'            => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
		'local_gas_station' => '<path d="M6 20V10l6-3 6 3v10"/><path d="M6 10h12"/><path d="M9 20v-4h6v4"/>',
		'flight_takeoff'    => '<path d="M2 22h20"/><path d="M6 18h8l4-9V4H6l4 9"/>',
		'home'              => '<path d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1v-9.5z"/><path d="M9 21V12h6v9"/>',
		'briefcase'         => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>',
		'landmark'          => '<line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><path d="M12 2l8 5H4l8-5z"/>',
		'map'               => '<polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/>',
		'plane'             => '<path d="M17.8 19.2L16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>',
		'luggage'           => '<path d="M6 20h12"/><path d="M8 20V8a2 2 0 012-2h4a2 2 0 012 2v12"/><path d="M10 6V4a2 2 0 014 0v2"/><path d="M8 12h8"/>',
		'sun'               => '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>',
		'bus'               => '<path d="M4 6h16v10H4z"/><path d="M4 10h16"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/><path d="M4 16h16"/><path d="M8 6V4"/><path d="M16 6V4"/>',
		'van_limo'          => '<path d="M3 16V12l4.5-4h10.5l3 3.5V16H3z"/><line x1="8.5" y1="12" x2="20" y2="12"/><line x1="11.5" y1="8" x2="11.5" y2="12"/><line x1="15.5" y1="8" x2="15.5" y2="12"/><circle cx="7" cy="17" r="1.75"/><circle cx="17" cy="17" r="1.75"/>',
		'copy'              => '<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>',
		'download'          => '<path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>',
		'shield_check'      => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
	);

	$fill_icons = array(
		'expand_more'       => '<path d="M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6-1.41-1.41z"/>',
		'directions_car'    => '<path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8a1 1 0 001 1h1a1 1 0 001-1v-1h12v1a1 1 0 001 1h1a1 1 0 001-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>',
		'directions_bus'    => '<path d="M4 16c0 .88.39 1.67 1 2.22V20a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-1.78c.61-.55 1-1.34 1-2.22V6c0-3.5-3.58-4-8-4s-8 .5-8 4v10zm3.5 1c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm1.5-6H6V6h12v5z"/>',
		'airport_shuttle'   => '<path d="M17 5H3a2 2 0 00-2 2v9h2a3 3 0 106 0h6a3 3 0 106 0h2v-5l-4-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5S5.17 15.5 6 15.5 7.5 16.17 7.5 17 6.83 18.5 6 18.5zm10 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM15 9V6.5l2.5 2.5H15z"/>',
		'groups'            => '<path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>',
		'family_restroom'   => '<path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A2.01 2.01 0 0018.06 7h-.12a2 2 0 00-1.9 1.37L13.5 16H16v6h4zM12.5 11.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5S11 9.17 11 10s.67 1.5 1.5 1.5zM5.5 6c1.11 0 2-.89 2-2s-.89-2-2-2-2 .89-2 2 .89 2 2 2zm2 16v-7H9V9.5C9 8.12 7.88 7 6.5 7S4 8.12 4 9.5V15h1.5v7h2z"/>',
		'business_center'   => '<path d="M10 16v-1H3.01L3 19c0 1.11.89 2 2 2h14c1.11 0 2-.89 2-2V3c0-1.11-.89-2-2-2H5c-1.11 0-2 .89-2 2v6h7v2H5v2h5v2H5v2h5z"/>',
		'temple_buddhist'   => '<path d="M21 10h-4V4h-2v6H9.83L12 3.41 14.17 10H9v4h2v8h2v-8h2v-4h2v4h2v-4h2z"/>',
		'celebration'       => '<path d="M2 22l14-5-9-9-5 14zm12.53-9.47L21 3l-3.53 8.47L13 12l4.53 1.53L19 22l-4.47-8.47z"/>',
		'flight_takeoff'    => '<path d="M2.5 19h19v2h-19v-2zm19.57-9.36c-.21-.8-1.04-1.28-1.84-1.06L14.92 10l-6.9-6.43-1.93.51 4.14 7.17-4.97 1.33-1.97-1.54-1.45.39 2.59 4.49s7.12-1.9 16.57-4.43c.81-.23 1.28-1.05 1.07-1.85z"/>',
		'beach_access'      => '<path d="M13.127 14.56l1.43-1.43 6.44 6.44L21.97 22l-8.87-7.44zm4.95-5.08l-1.43 1.43-3.15-3.15 1.43-1.43 3.15 3.15zM4.05 5.61L3 6.66l2.83 2.83 1.05-1.05L4.05 5.61zM20 2l-8.87 7.44 1.43 1.43L21.97 4 20 2zM6.66 3 5.61 4.05l2.83 2.83 1.05-1.05L6.66 3z"/>',
		'temple_hindu'      => '<path d="M20 7h-1V5c0-1.1-.9-2-2-2H7c-1.1 0-2 .9-2 2v2H4c-1.1 0-2 .9-2 2v11h20V9c0-1.1-.9-2-2-2zM7 5h10v2H7V5zm12 13H5V9h14v9z"/>',
		'work'              => '<path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>',
		'local_gas_station' => '<path d="M3 21h3v-8h6v8h3V8l4-4v13h3V3l-8 6H3v12z"/><rect x="7" y="13" width="4" height="4"/>',
	);

	$name = sanitize_key( (string) $name );

	if ( isset( $stroke_icons[ $name ] ) ) {
		return sprintf(
			'<svg class="annam-cr-icon annam-cr-icon--stroke" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
			$stroke_icons[ $name ]
		);
	}

	if ( ! isset( $fill_icons[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="annam-cr-icon annam-cr-icon--fill" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">%s</svg>',
		$fill_icons[ $name ]
	);
}

function annam_car_rental_handle_form_post() {
	if ( ! annam_car_rental_is_landing_template() ) {
		return;
	}
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : '' ) ) {
		return;
	}
	if ( empty( $_POST['annam_cr_submit'] ) ) {
		return;
	}
	$redirect = get_permalink();
	if ( ! $redirect ) {
		return;
	}
	$input                    = wp_unslash( $_POST );
	$input['annam_cr_page_url'] = $redirect;
	$result                   = annam_car_rental_process_booking( $input );
	wp_safe_redirect( add_query_arg( 'annam_cr', ! empty( $result['success'] ) ? 'sent' : 'error', $redirect ) );
	exit;
}
add_action( 'template_redirect', 'annam_car_rental_handle_form_post', 2 );

function annam_car_rental_sidebar_layout( $layout ) {
	return annam_car_rental_is_landing_template() ? 'no-sidebar' : $layout;
}
add_filter( 'generate_sidebar_layout', 'annam_car_rental_sidebar_layout', 20 );

function annam_car_rental_hide_entry_header( $show ) {
	return annam_car_rental_is_landing_template() ? false : $show;
}
add_filter( 'generate_show_entry_header', 'annam_car_rental_hide_entry_header', 12 );

function annam_car_rental_body_class( $classes ) {
	if ( annam_car_rental_is_landing_template() ) {
		$classes[] = 'annam-car-rental-landing-page';
	}
	return $classes;
}
add_filter( 'body_class', 'annam_car_rental_body_class' );

function annam_car_rental_enqueue_assets() {
	if ( ! annam_car_rental_is_landing_template() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$css = $dir . '/assets/css/car-rental-landing.css';
	$js  = $dir . '/assets/js/car-rental-landing.js';

	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-car-rental-landing',
			$uri . '/assets/css/car-rental-landing.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $css )
		);
	}

	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-car-rental-landing',
			$uri . '/assets/js/car-rental-landing.js',
			array(),
			(string) filemtime( $js ),
			true
		);

		$vehicle = annam_car_rental_get_current_vehicle_type();
		wp_localize_script(
			'annam-car-rental-landing',
			'annamCarRental',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'action'       => 'annam_car_rental_booking',
				'nonce'        => wp_create_nonce( 'annam_car_rental_booking' ),
				'pageUrl'      => get_permalink() ? get_permalink() : home_url( '/' ),
				'vehicleType'  => $vehicle,
				'dateToday'    => wp_date( 'Y-m-d' ),
				'i18n'         => array(
					'sending'     => __( 'Đang gửi...', 'generatepress_child' ),
					'submitError' => __( 'Không gửi được. Vui lòng thử lại hoặc gọi hotline.', 'generatepress_child' ),
					'copied'      => __( 'Đã sao chép', 'generatepress_child' ),
				),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_car_rental_enqueue_assets', 30 );

/**
 * Tạo 6 trang landing nếu chưa có (chạy một lần).
 */
function annam_car_rental_maybe_create_pages() {
	if ( get_option( 'annam_car_rental_pages_created' ) ) {
		return;
	}

	$pages = array(
		array(
			'title'    => 'Thuê xe hợp đồng',
			'slug'     => 'thue-xe-hop-dong',
			'template' => 'page-template-thue-xe-hub.php',
			'meta'     => '',
		),
		array(
			'title'    => 'Thuê xe 7 chỗ',
			'slug'     => 'thue-xe-7-cho',
			'template' => 'page-template-thue-xe-landing.php',
			'meta'     => '7-cho',
		),
		array(
			'title'    => 'Thuê xe Limousine 9–11 chỗ',
			'slug'     => 'thue-xe-limousine-9-11-cho',
			'template' => 'page-template-thue-xe-landing.php',
			'meta'     => 'limousine-9-11',
		),
		array(
			'title'    => 'Thuê xe 16 chỗ',
			'slug'     => 'thue-xe-16-cho',
			'template' => 'page-template-thue-xe-landing.php',
			'meta'     => '16-cho',
		),
		array(
			'title'    => 'Thuê xe 29 chỗ',
			'slug'     => 'thue-xe-29-cho',
			'template' => 'page-template-thue-xe-landing.php',
			'meta'     => '29-cho',
		),
		array(
			'title'    => 'Thuê xe 45 chỗ',
			'slug'     => 'thue-xe-45-cho',
			'template' => 'page-template-thue-xe-landing.php',
			'meta'     => '45-cho',
		),
	);

	foreach ( $pages as $page_def ) {
		$existing = get_page_by_path( $page_def['slug'] );
		if ( $existing instanceof WP_Post ) {
			if ( $page_def['meta'] ) {
				update_post_meta( $existing->ID, '_annam_car_rental_vehicle_type', $page_def['meta'] );
			}
			update_post_meta( $existing->ID, '_wp_page_template', $page_def['template'] );
			continue;
		}

		$id = wp_insert_post(
			array(
				'post_title'   => $page_def['title'],
				'post_name'    => $page_def['slug'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);

		if ( ! is_wp_error( $id ) && $id ) {
			update_post_meta( $id, '_wp_page_template', $page_def['template'] );
			if ( $page_def['meta'] ) {
				update_post_meta( $id, '_annam_car_rental_vehicle_type', $page_def['meta'] );
			}
		}
	}

	update_option( 'annam_car_rental_pages_created', 1, false );
}
add_action( 'after_setup_theme', 'annam_car_rental_maybe_create_pages', 30 );
