<?php
/**
 * Single product tour: two-column layout below gallery + lead form + mail handler.
 * Product gallery (desktop + mobile) lives in template-parts/.../tour-header.php and
 * assets/css/woo-single-tour-header.css, not in this file.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Default hotline when _tour_hotline is empty.
 *
 * @return string
 */
function annam_tour_default_hotline() {
	return '19008164';
}

/**
 * @return array<int, array{label: string, code: string}>
 */
function annam_tour_get_phone_country_codes() {
	static $cached = null;
	if ( null !== $cached ) {
		return $cached;
	}
	$file = get_stylesheet_directory() . '/inc/phone-country-codes.php';
	if ( ! file_exists( $file ) ) {
		$cached = array();
		return $cached;
	}
	$data = require $file;
	$cached = is_array( $data ) ? $data : array();
	return $cached;
}

/**
 * @param string $code Dial code e.g. +84.
 * @return bool
 */
function annam_tour_is_allowed_dial_code( $code ) {
	$code = is_string( $code ) ? trim( $code ) : '';
	if ( '' === $code ) {
		return false;
	}
	foreach ( annam_tour_get_phone_country_codes() as $row ) {
		if ( isset( $row['code'] ) && $row['code'] === $code ) {
			return true;
		}
	}
	return false;
}

/**
 * Client IP for rate limiting (REMOTE_ADDR).
 *
 * @return string
 */
function annam_tour_lead_client_ip() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
}

/**
 * Validate local phone characters and combined digit length.
 *
 * @param string $dial     e.g. +84.
 * @param string $local_raw User input.
 * @return array{ ok: bool, message?: string, full_digits?: string }
 */
function annam_tour_lead_validate_phone( $dial, $local_raw ) {
	$local_raw = is_string( $local_raw ) ? trim( $local_raw ) : '';
	if ( '' === $local_raw ) {
		return array( 'ok' => false, 'message' => 'empty' );
	}
	if ( ! preg_match( '/^[\d\s+\-().]+$/u', $local_raw ) ) {
		return array( 'ok' => false, 'message' => 'chars' );
	}
	$dial_digits = preg_replace( '/\D/', '', $dial );
	$local_digits = preg_replace( '/\D/', '', $local_raw );
	if ( '' === $local_digits ) {
		return array( 'ok' => false, 'message' => 'nodigits' );
	}
	$all = $dial_digits . $local_digits;
	$len = strlen( $all );
	if ( $len < 9 || $len > 15 ) {
		return array( 'ok' => false, 'message' => 'len' );
	}
	return array( 'ok' => true, 'full_digits' => $all );
}

/**
 * @param string $code Dial code.
 * @return string
 */
function annam_tour_lead_get_country_label( $code ) {
	foreach ( annam_tour_get_phone_country_codes() as $row ) {
		if ( isset( $row['code'] ) && $row['code'] === $code ) {
			return isset( $row['label'] ) ? (string) $row['label'] : $code;
		}
	}
	return $code;
}

/**
 * @return string
 */
function annam_tour_lead_success_message() {
	return __( 'Cảm ơn quý khách. An Nam Discovery đã nhận yêu cầu tư vấn và sẽ liên hệ lại trong thời gian sớm nhất.', 'generatepress_child' );
}

/**
 * @return string
 */
function annam_tour_lead_error_message() {
	return __( 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại hoặc liên hệ hotline/Zalo.', 'generatepress_child' );
}

/**
 * Email nhận lead.
 *
 * @return string[]
 */
function annam_tour_lead_get_recipient_emails() {
	if ( function_exists( 'annam_contact_get_lead_recipient_emails' ) ) {
		$emails = annam_contact_get_lead_recipient_emails();
		if ( ! empty( $emails ) ) {
			return $emails;
		}
	}

	$admin = get_option( 'admin_email' );
	return is_email( $admin ) ? array( $admin ) : array();
}

/**
 * Xử lý gửi form sidebar (POST / AJAX).
 *
 * @param array<string,mixed> $input Raw input.
 * @return array{success:bool,message:string,code?:string}
 */
function annam_tour_lead_process_submission( array $input ) {
	$fail = static function ( $message, $code = 'error' ) {
		return array(
			'success' => false,
			'message' => $message,
			'code'    => $code,
		);
	};

	$product_id = isset( $input['annam_product_id'] ) ? absint( $input['annam_product_id'] ) : 0;
	$queried    = is_product() ? get_queried_object_id() : 0;

	if ( ! $product_id || ( $queried && $queried !== $product_id ) ) {
		return $fail( annam_tour_lead_error_message(), 'product' );
	}

	if ( empty( $input['annam_tour_lead_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( (string) $input['annam_tour_lead_nonce'] ), 'annam_tour_lead' ) ) {
		return $fail( __( 'Phiên không hợp lệ. Vui lòng tải lại trang và thử lại.', 'generatepress_child' ), 'nonce' );
	}

	$hp = isset( $input['annam_hp_website'] ) ? (string) $input['annam_hp_website'] : '';
	if ( '' !== trim( $hp ) ) {
		return $fail( annam_tour_lead_error_message(), 'spam' );
	}

	$ts = isset( $input['annam_form_ts'] ) ? absint( $input['annam_form_ts'] ) : 0;
	if ( ! $ts || ( time() - $ts ) < 3 || ( time() - $ts ) > 7200 ) {
		return $fail( annam_tour_lead_error_message(), 'spam' );
	}

	if ( ! function_exists( 'annam_check_rate_limit' ) || ! annam_check_rate_limit( 'annam_tour_lead', 5, 10 ) ) {
		return $fail( __( 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau ít phút.', 'generatepress_child' ), 'rate' );
	}

	$dial = isset( $input['annam_phone_country'] ) ? sanitize_text_field( (string) $input['annam_phone_country'] ) : '';
	if ( ! annam_tour_is_allowed_dial_code( $dial ) ) {
		return $fail( __( 'Vui lòng chọn quốc gia hợp lệ.', 'generatepress_child' ), 'country' );
	}

	$local = isset( $input['annam_phone_local'] ) ? wp_unslash( $input['annam_phone_local'] ) : '';
	$local = is_string( $local ) ? sanitize_text_field( $local ) : '';

	$check = annam_tour_lead_validate_phone( $dial, $local );
	if ( empty( $check['ok'] ) ) {
		return $fail( __( 'Số điện thoại không hợp lệ. Vui lòng nhập ít nhất 9 chữ số.', 'generatepress_child' ), 'phone' );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return $fail( annam_tour_lead_error_message(), 'product' );
	}

	$recipients = annam_tour_lead_get_recipient_emails();
	if ( empty( $recipients ) ) {
		return $fail( annam_tour_lead_error_message(), 'mail' );
	}

	$tour_name      = wp_strip_all_tags( $product->get_name() );
	$product_link   = get_permalink( $product_id );
	$country_label  = annam_tour_lead_get_country_label( $dial );
	$full_display   = trim( $dial . ' ' . $local );
	$page_url       = isset( $input['annam_tour_lead_page_url'] ) ? esc_url_raw( (string) $input['annam_tour_lead_page_url'] ) : '';
	if ( '' === $page_url ) {
		$page_url = $product_link ? $product_link : home_url( '/' );
	}

	$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
	if ( function_exists( 'mb_substr' ) && '' !== $user_agent ) {
		$user_agent = mb_substr( $user_agent, 0, 500, 'UTF-8' );
	} elseif ( '' !== $user_agent ) {
		$user_agent = substr( $user_agent, 0, 500 );
	}

	$submitted_at = wp_date( 'd/m/Y H:i' );
	$lead_source  = __( 'Sidebar trang chi tiết sản phẩm', 'generatepress_child' );

	$subject = sprintf(
		'Lead sidebar sản phẩm - %s - %s',
		$tour_name,
		$full_display
	);

	$body_lines = array(
		__( 'Yêu cầu tư vấn từ sidebar trang chi tiết sản phẩm', 'generatepress_child' ),
		'',
		__( 'Nguồn lead:', 'generatepress_child' ) . ' ' . $lead_source,
		__( 'Tên sản phẩm:', 'generatepress_child' ) . ' ' . $tour_name,
		__( 'Link sản phẩm:', 'generatepress_child' ) . ' ' . $product_link,
		__( 'Quốc gia:', 'generatepress_child' ) . ' ' . $country_label . ' (' . $dial . ')',
		__( 'Số điện thoại:', 'generatepress_child' ) . ' ' . $full_display,
		'',
		__( 'URL trang gửi form:', 'generatepress_child' ) . ' ' . $page_url,
		__( 'Thời gian gửi:', 'generatepress_child' ) . ' ' . $submitted_at,
		'IP: ' . annam_tour_lead_client_ip(),
		'User-Agent: ' . ( '' !== $user_agent ? $user_agent : '—' ),
	);

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	$sent    = false;

	foreach ( $recipients as $to ) {
		if ( wp_mail( $to, $subject, implode( "\n", $body_lines ), $headers ) ) {
			$sent = true;
		}
	}

	if ( ! $sent ) {
		return $fail( annam_tour_lead_error_message(), 'mail' );
	}

	if ( function_exists( 'annam_rate_limit_increment' ) ) {
		annam_rate_limit_increment( 'annam_tour_lead', 10 );
	}

	return array(
		'success' => true,
		'message' => annam_tour_lead_success_message(),
	);
}

/**
 * Fallback POST (no-JS).
 */
function annam_tour_lead_form_handle_post() {
	if ( ! isset( $_POST['annam_tour_lead_submit'] ) || ! is_product() ) {
		return;
	}

	$product_id = isset( $_POST['annam_product_id'] ) ? absint( $_POST['annam_product_id'] ) : 0;
	$url        = $product_id ? get_permalink( $product_id ) : ( get_permalink() ? get_permalink() : home_url( '/' ) );

	$input = wp_unslash( $_POST );
	$input['annam_tour_lead_page_url'] = $url;
	$result = annam_tour_lead_process_submission( $input );

	wp_safe_redirect( add_query_arg( 'annam_lead', ! empty( $result['success'] ) ? 'success' : 'error', $url ) );
	exit;
}
add_action( 'template_redirect', 'annam_tour_lead_form_handle_post', 5 );

/**
 * AJAX gửi form sidebar.
 */
function annam_tour_lead_ajax_submit() {
	$input = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$result = annam_tour_lead_process_submission( $input );

	if ( ! empty( $result['success'] ) ) {
		wp_send_json_success(
			array(
				'message' => $result['message'],
			)
		);
	}

	wp_send_json_error(
		array(
			'message' => isset( $result['message'] ) ? $result['message'] : annam_tour_lead_error_message(),
			'code'    => isset( $result['code'] ) ? $result['code'] : 'error',
		),
		400
	);
}
add_action( 'wp_ajax_annam_tour_lead', 'annam_tour_lead_ajax_submit' );
add_action( 'wp_ajax_nopriv_annam_tour_lead', 'annam_tour_lead_ajax_submit' );

/**
 * Enqueue detail section assets.
 */
function annam_tour_detail_enqueue_assets() {
	if ( ! is_product() ) {
		return;
	}
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$price_css = $dir . '/assets/css/woo-tour-price.css';
	$price_deps = array( 'annam-design-tokens' );
	if ( file_exists( $price_css ) ) {
		wp_enqueue_style(
			'annam-tour-price',
			$uri . '/assets/css/woo-tour-price.css',
			array(),
			(string) filemtime( $price_css )
		);
		$price_deps[] = 'annam-tour-price';
	}

	$css = $dir . '/assets/css/woo-single-tour-detail.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-single-tour-detail',
			$uri . '/assets/css/woo-single-tour-detail.css',
			$price_deps,
			(string) filemtime( $css )
		);
	}

	$js = $dir . '/assets/js/woo-single-tour-lead-form.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-tour-lead-form',
			$uri . '/assets/js/woo-single-tour-lead-form.js',
			array(),
			(string) filemtime( $js ),
			true
		);

		$product_id = get_queried_object_id();
		wp_localize_script(
			'annam-tour-lead-form',
			'annamTourLeadForm',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'action'    => 'annam_tour_lead',
				'nonce'     => wp_create_nonce( 'annam_tour_lead' ),
				'productId' => $product_id,
				'pageUrl'   => $product_id ? get_permalink( $product_id ) : home_url( '/' ),
				'i18n'      => array(
					'sending'       => __( 'Đang gửi...', 'generatepress_child' ),
					'success'       => annam_tour_lead_success_message(),
					'error'         => annam_tour_lead_error_message(),
					'phoneRequired' => __( 'Vui lòng nhập số điện thoại.', 'generatepress_child' ),
					'phoneInvalid'  => __( 'Số điện thoại không hợp lệ. Vui lòng nhập ít nhất 9 chữ số.', 'generatepress_child' ),
					'submitLabel'   => __( 'Gửi', 'generatepress_child' ),
				),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_tour_detail_enqueue_assets', 26 );

/**
 * Remove duplicate summary / description tab on single product tour layout.
 */
function annam_tour_detail_remove_default_summary() {
	if ( ! is_product() ) {
		return;
	}
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
}
add_action( 'wp', 'annam_tour_detail_remove_default_summary', 20 );

/**
 * Remove product description tab (long content shown in main column).
 *
 * @param array<string, array> $tabs Tabs.
 * @return array<string, array>
 */
function annam_tour_detail_remove_description_tab( $tabs ) {
	if ( ! is_product() ) {
		return $tabs;
	}
	unset( $tabs['description'] );
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'annam_tour_detail_remove_description_tab', 98 );

/**
 * Output two-column section below gallery.
 */
function annam_tour_detail_section_render() {
	if ( ! is_product() ) {
		return;
	}
	get_template_part( 'template-parts/woocommerce/single-tour/tour', 'detail-main' );
}
add_action( 'woocommerce_before_single_product_summary', 'annam_tour_detail_section_render', 25 );
