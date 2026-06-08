<?php
/**
 * Xử lý đặt vé Cabin VIP (validate, email) — dùng cho POST và AJAX.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param array<string,mixed> $input Raw POST.
 * @return array{success:bool,message:string,code?:string,data?:array<string,string>}
 */
function annam_cabin_landing_process_booking( array $input ) {
	$config = annam_cabin_landing_get_config();

	$fail = static function ( $message, $code = 'error' ) {
		return array(
			'success' => false,
			'message' => $message,
			'code'    => $code,
		);
	};

	if ( empty( $input['annam_cabin_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( (string) $input['annam_cabin_nonce'] ), 'annam_cabin_booking' ) ) {
		return $fail( __( 'Phiên không hợp lệ. Vui lòng tải lại trang và thử lại.', 'generatepress_child' ), 'nonce' );
	}

	if ( ! empty( $input['annam_cabin_website'] ) ) {
		return $fail( __( 'Không gửi được yêu cầu.', 'generatepress_child' ), 'spam' );
	}

	$ts = isset( $input['annam_cabin_ts'] ) ? absint( $input['annam_cabin_ts'] ) : 0;
	if ( ! $ts || ( time() - $ts ) < 3 || ( time() - $ts ) > 7200 ) {
		return $fail( __( 'Yêu cầu không hợp lệ. Vui lòng thử lại.', 'generatepress_child' ), 'ts' );
	}

	if ( function_exists( 'annam_check_rate_limit' ) && ! annam_check_rate_limit( 'annam_cabin_booking', ANNAM_CABIN_LANDING_RATE_MAX, ANNAM_CABIN_LANDING_RATE_MINUTES ) ) {
		return $fail( __( 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng gọi hotline hoặc nhắn Zalo.', 'generatepress_child' ), 'rate' );
	}

	$from_place  = isset( $input['annam_cabin_from'] ) ? sanitize_key( (string) $input['annam_cabin_from'] ) : '';
	$to_place    = isset( $input['annam_cabin_to'] ) ? sanitize_key( (string) $input['annam_cabin_to'] ) : '';
	$travel_date = isset( $input['annam_cabin_date'] ) ? sanitize_text_field( (string) $input['annam_cabin_date'] ) : '';
	$dep_time    = isset( $input['annam_cabin_time'] ) ? sanitize_text_field( (string) $input['annam_cabin_time'] ) : '';
	$cabin_type  = isset( $input['annam_cabin_type'] ) ? sanitize_key( (string) $input['annam_cabin_type'] ) : '';
	$guests      = isset( $input['annam_cabin_guests'] ) ? max( 1, min( 20, (int) $input['annam_cabin_guests'] ) ) : 1;
	$name        = isset( $input['annam_cabin_name'] ) ? sanitize_text_field( (string) $input['annam_cabin_name'] ) : '';
	$phone       = isset( $input['annam_cabin_phone'] ) ? sanitize_text_field( (string) $input['annam_cabin_phone'] ) : '';

	$valid_cabins = array( 'single_floor2', 'single_floor1', 'double' );
	$today        = wp_date( 'Y-m-d' );

	if ( ! annam_cabin_landing_is_valid_route( $from_place, $to_place ) ) {
		return $fail( __( 'Tuyến đi không hợp lệ. Chỉ hỗ trợ Hà Nội ⇄ Sapa và Hà Nội ⇄ Lào Cai.', 'generatepress_child' ), 'route' );
	}

	if ( ! in_array( $cabin_type, $valid_cabins, true ) ) {
		return $fail( __( 'Vui lòng chọn loại cabin.', 'generatepress_child' ), 'cabin' );
	}

	if ( '' === trim( $name ) ) {
		return $fail( __( 'Vui lòng nhập họ và tên.', 'generatepress_child' ), 'name' );
	}

	if ( '' === trim( $phone ) ) {
		return $fail( __( 'Vui lòng nhập số điện thoại hoặc Zalo.', 'generatepress_child' ), 'phone' );
	}

	if ( ! function_exists( 'annam_contact_validate_phone' ) || ! annam_contact_validate_phone( $phone ) ) {
		return $fail( __( 'Số điện thoại không hợp lệ.', 'generatepress_child' ), 'phone' );
	}

	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $travel_date ) ) {
		return $fail( __( 'Vui lòng chọn ngày đi.', 'generatepress_child' ), 'date' );
	}

	if ( $travel_date < $today ) {
		return $fail( __( 'Không thể chọn ngày trong quá khứ.', 'generatepress_child' ), 'date' );
	}

	$allowed_times = annam_cabin_landing_filter_times_for_date( $from_place, $to_place, $travel_date );
	if ( '' === $dep_time || ! in_array( $dep_time, $allowed_times, true ) ) {
		return $fail( __( 'Giờ đi không hợp lệ hoặc đã qua. Vui lòng chọn giờ khác.', 'generatepress_child' ), 'time' );
	}

	$labels = array(
		'hanoi'  => 'Hà Nội',
		'sapa'   => 'Sapa',
		'laocai' => 'Lào Cai',
	);
	$cabin_labels = array(
		'single_floor2' => 'Cabin đơn tầng 2',
		'single_floor1' => 'Cabin đơn tầng 1',
		'double'        => 'Cabin đôi',
	);

	$from_label  = isset( $labels[ $from_place ] ) ? $labels[ $from_place ] : $from_place;
	$to_label    = isset( $labels[ $to_place ] ) ? $labels[ $to_place ] : $to_place;
	$cabin_label = isset( $cabin_labels[ $cabin_type ] ) ? $cabin_labels[ $cabin_type ] : $cabin_type;

	$subject = sprintf(
		'[CABIN VIP] Giữ chỗ — %s → %s — %s — %s',
		$from_label,
		$to_label,
		$name,
		$phone
	);

	$body_lines = array(
		__( 'Điểm đón:', 'generatepress_child' ) . ' ' . $from_label,
		__( 'Điểm trả:', 'generatepress_child' ) . ' ' . $to_label,
		__( 'Ngày đi:', 'generatepress_child' ) . ' ' . $travel_date,
		__( 'Giờ đi:', 'generatepress_child' ) . ' ' . $dep_time,
		__( 'Loại cabin:', 'generatepress_child' ) . ' ' . $cabin_label,
		__( 'Số lượng khách:', 'generatepress_child' ) . ' ' . (string) $guests,
		__( 'Họ tên:', 'generatepress_child' ) . ' ' . $name,
		__( 'Số điện thoại / Zalo:', 'generatepress_child' ) . ' ' . $phone,
	);

	$sent = annam_lead_send_notification( annam_lead_get_recipient_emails(), $subject, $body_lines );

	if ( function_exists( 'annam_rate_limit_increment' ) ) {
		annam_rate_limit_increment( 'annam_cabin_booking', ANNAM_CABIN_LANDING_RATE_MINUTES );
	}

	if ( ! $sent ) {
		return $fail( __( 'Không gửi được email. Vui lòng gọi hotline hoặc nhắn Zalo.', 'generatepress_child' ), 'mail' );
	}

	$success_msg = isset( $config['form']['success_message'] ) ? $config['form']['success_message'] : __( 'Cảm ơn quý khách. Chúng tôi sẽ liên hệ xác nhận vé sớm.', 'generatepress_child' );

	return array(
		'success' => true,
		'message' => $success_msg,
		'data'    => array(
			'from'  => $from_label,
			'to'    => $to_label,
			'date'  => $travel_date,
			'time'  => $dep_time,
			'cabin' => $cabin_label,
		),
	);
}

/**
 * AJAX submit form.
 */
function annam_cabin_landing_ajax_booking() {
	$result = annam_cabin_landing_process_booking( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( ! empty( $result['success'] ) ) {
		wp_send_json_success(
			array(
				'message' => $result['message'],
			)
		);
	}

	wp_send_json_error(
		array(
			'message' => isset( $result['message'] ) ? $result['message'] : __( 'Có lỗi xảy ra.', 'generatepress_child' ),
			'code'    => isset( $result['code'] ) ? $result['code'] : 'error',
		),
		400
	);
}
add_action( 'wp_ajax_annam_cabin_booking', 'annam_cabin_landing_ajax_booking' );
add_action( 'wp_ajax_nopriv_annam_cabin_booking', 'annam_cabin_landing_ajax_booking' );
