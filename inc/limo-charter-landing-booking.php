<?php
/**
 * Lead form thuê limo HN–Sapa (POST + AJAX).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param array<string,mixed> $input Raw POST.
 * @return array{success:bool,message:string,code?:string}
 */
function annam_limo_charter_landing_process_lead( array $input ) {
	$config = annam_limo_charter_landing_get_config();

	$fail = static function ( $message, $code = 'error' ) {
		return array(
			'success' => false,
			'message' => $message,
			'code'    => $code,
		);
	};

	if ( empty( $input['annam_limo_charter_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( (string) $input['annam_limo_charter_nonce'] ), 'annam_limo_charter_lead' ) ) {
		return $fail( __( 'Phiên không hợp lệ. Vui lòng tải lại trang và thử lại.', 'generatepress_child' ), 'nonce' );
	}

	if ( ! empty( $input['annam_limo_charter_website'] ) ) {
		return $fail( __( 'Không gửi được yêu cầu.', 'generatepress_child' ), 'spam' );
	}

	$ts = isset( $input['annam_limo_charter_ts'] ) ? absint( $input['annam_limo_charter_ts'] ) : 0;
	if ( ! $ts || ( time() - $ts ) < 3 || ( time() - $ts ) > 7200 ) {
		return $fail( __( 'Yêu cầu không hợp lệ. Vui lòng thử lại.', 'generatepress_child' ), 'ts' );
	}

	if ( function_exists( 'annam_check_rate_limit' ) && ! annam_check_rate_limit( 'annam_limo_charter_lead', ANNAM_LIMO_CHARTER_LANDING_RATE_MAX, ANNAM_LIMO_CHARTER_LANDING_RATE_MINUTES ) ) {
		return $fail( __( 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng gọi hotline hoặc nhắn Zalo.', 'generatepress_child' ), 'rate' );
	}

	$route   = isset( $input['annam_limo_charter_route'] ) ? sanitize_key( (string) $input['annam_limo_charter_route'] ) : '';
	$date    = isset( $input['annam_limo_charter_date'] ) ? sanitize_text_field( (string) $input['annam_limo_charter_date'] ) : '';
	$vehicle = isset( $input['annam_limo_charter_vehicle'] ) ? sanitize_key( (string) $input['annam_limo_charter_vehicle'] ) : '';
	$time    = isset( $input['annam_limo_charter_time'] ) ? sanitize_text_field( (string) $input['annam_limo_charter_time'] ) : '';
	$phone   = isset( $input['annam_limo_charter_phone'] ) ? sanitize_text_field( (string) $input['annam_limo_charter_phone'] ) : '';

	$valid_routes   = array( 'hn_sapa', 'sapa_hn' );
	$valid_vehicles = array( '9', '11', 'undecided' );
	$today          = wp_date( 'Y-m-d' );

	if ( ! in_array( $route, $valid_routes, true ) ) {
		return $fail( __( 'Vui lòng chọn tuyến.', 'generatepress_child' ), 'route' );
	}

	if ( ! in_array( $vehicle, $valid_vehicles, true ) ) {
		return $fail( __( 'Vui lòng chọn loại xe.', 'generatepress_child' ), 'vehicle' );
	}

	if ( '' === trim( $phone ) ) {
		return $fail( __( 'Vui lòng nhập số điện thoại hoặc Zalo.', 'generatepress_child' ), 'phone' );
	}

	if ( ! function_exists( 'annam_contact_validate_phone' ) || ! annam_contact_validate_phone( $phone ) ) {
		return $fail( __( 'Số điện thoại không hợp lệ.', 'generatepress_child' ), 'phone' );
	}

	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
		return $fail( __( 'Vui lòng chọn ngày đi.', 'generatepress_child' ), 'date' );
	}

	if ( $date < $today ) {
		return $fail( __( 'Không thể chọn ngày trong quá khứ.', 'generatepress_child' ), 'date' );
	}

	$route_labels = array(
		'hn_sapa' => 'Hà Nội → Sapa',
		'sapa_hn' => 'Sapa → Hà Nội',
	);
	$vehicle_labels = array(
		'9'         => 'Limousine 9 chỗ (4.200.000đ)',
		'11'        => 'Limousine 11 chỗ (4.500.000đ)',
		'undecided' => 'Chưa quyết định',
	);

	$body_lines = array(
		__( 'Dịch vụ:', 'generatepress_child' ) . ' ' . ( isset( $config['product_name'] ) ? $config['product_name'] : 'Thuê limo HN–Sapa' ),
		__( 'SĐT/Zalo:', 'generatepress_child' ) . ' ' . $phone,
		__( 'Tuyến:', 'generatepress_child' ) . ' ' . ( $route_labels[ $route ] ?? $route ),
		__( 'Ngày đi:', 'generatepress_child' ) . ' ' . $date,
		__( 'Loại xe:', 'generatepress_child' ) . ' ' . ( $vehicle_labels[ $vehicle ] ?? $vehicle ),
	);
	if ( '' !== trim( $time ) ) {
		$body_lines[] = __( 'Giờ dự kiến:', 'generatepress_child' ) . ' ' . $time;
	}
	$page_url = isset( $input['annam_limo_charter_page_url'] ) ? esc_url_raw( (string) $input['annam_limo_charter_page_url'] ) : '';
	if ( $page_url ) {
		$body_lines[] = __( 'Trang:', 'generatepress_child' ) . ' ' . $page_url;
	}

	$recipients = array();
	if ( function_exists( 'annam_limo_charter_landing_get_lead_emails' ) ) {
		$recipients = annam_limo_charter_landing_get_lead_emails();
	}
	if ( empty( $recipients ) ) {
		$fallback = function_exists( 'annam_limo_charter_landing_default_lead_email' )
			? annam_limo_charter_landing_default_lead_email()
			: 'annamdiscoveryvn@gmail.com';
		$recipients = is_email( $fallback ) ? array( $fallback ) : array();
	}

	$subject = sprintf(
		'[THUE LIMO HN-SAPA] %s — %s — %s',
		$route_labels[ $route ] ?? $route,
		$date,
		$phone
	);

	$sent = false;
	if ( function_exists( 'annam_lead_send_notification' ) ) {
		$sent = annam_lead_send_notification( $recipients, $subject, $body_lines );
	}

	if ( ! $sent ) {
		return $fail( __( 'Không gửi được email. Vui lòng gọi hotline hoặc nhắn Zalo.', 'generatepress_child' ), 'mail' );
	}

	if ( function_exists( 'annam_rate_limit_increment' ) ) {
		annam_rate_limit_increment( 'annam_limo_charter_lead', ANNAM_LIMO_CHARTER_LANDING_RATE_MINUTES );
	}

	$msg = isset( $config['form']['success_message'] ) ? $config['form']['success_message'] : __( 'Đã nhận yêu cầu.', 'generatepress_child' );

	return array(
		'success' => true,
		'message' => $msg,
		'code'    => 'ok',
	);
}

/**
 * Fresh nonce for cache.
 */
function annam_limo_charter_landing_ajax_fresh_nonce() {
	wp_send_json_success(
		array(
			'nonce' => wp_create_nonce( 'annam_limo_charter_lead' ),
			'ts'    => (string) ( time() - 5 ),
		)
	);
}
add_action( 'wp_ajax_annam_limo_charter_lead_nonce', 'annam_limo_charter_landing_ajax_fresh_nonce' );
add_action( 'wp_ajax_nopriv_annam_limo_charter_lead_nonce', 'annam_limo_charter_landing_ajax_fresh_nonce' );

/**
 * AJAX lead.
 */
function annam_limo_charter_landing_ajax_lead() {
	$result = annam_limo_charter_landing_process_lead( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( ! empty( $result['success'] ) ) {
		wp_send_json_success(
			array(
				'message' => $result['message'],
			)
		);
	}
	wp_send_json_error(
		array(
			'message' => isset( $result['message'] ) ? $result['message'] : __( 'Không gửi được.', 'generatepress_child' ),
			'code'    => isset( $result['code'] ) ? $result['code'] : 'error',
		)
	);
}
add_action( 'wp_ajax_annam_limo_charter_lead', 'annam_limo_charter_landing_ajax_lead' );
add_action( 'wp_ajax_nopriv_annam_limo_charter_lead', 'annam_limo_charter_landing_ajax_lead' );
