<?php
/**
 * Form báo giá thuê xe hợp đồng.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

const ANNAM_CAR_RENTAL_RATE_MAX     = 8;
const ANNAM_CAR_RENTAL_RATE_MINUTES = 10;

/**
 * @return string[]
 */
function annam_car_rental_get_lead_recipient_emails() {
	return function_exists( 'annam_lead_get_recipient_emails' )
		? annam_lead_get_recipient_emails()
		: array( get_option( 'admin_email' ) );
}

/**
 * @param array<string,mixed> $input Raw POST.
 * @return array{success:bool,message:string,code?:string}
 */
function annam_car_rental_process_booking( array $input ) {
	$fail = static function ( $message, $code = 'error' ) {
		return array(
			'success' => false,
			'message' => $message,
			'code'    => $code,
		);
	};

	if ( empty( $input['annam_cr_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( (string) $input['annam_cr_nonce'] ), 'annam_car_rental_booking' ) ) {
		return $fail( __( 'Phiên không hợp lệ. Vui lòng tải lại trang và thử lại.', 'generatepress_child' ), 'nonce' );
	}

	if ( ! empty( $input['annam_cr_website'] ) ) {
		return $fail( __( 'Không gửi được yêu cầu.', 'generatepress_child' ), 'spam' );
	}

	$ts = isset( $input['annam_cr_ts'] ) ? absint( $input['annam_cr_ts'] ) : 0;
	if ( ! $ts || ( time() - $ts ) < 3 || ( time() - $ts ) > 7200 ) {
		return $fail( __( 'Yêu cầu không hợp lệ. Vui lòng thử lại.', 'generatepress_child' ), 'ts' );
	}

	if ( function_exists( 'annam_check_rate_limit' ) && ! annam_check_rate_limit( 'annam_car_rental_booking', ANNAM_CAR_RENTAL_RATE_MAX, ANNAM_CAR_RENTAL_RATE_MINUTES ) ) {
		return $fail( __( 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng gọi hotline hoặc nhắn Zalo.', 'generatepress_child' ), 'rate' );
	}

	$pickup      = isset( $input['annam_cr_pickup'] ) ? sanitize_text_field( (string) $input['annam_cr_pickup'] ) : '';
	$dropoff     = isset( $input['annam_cr_dropoff'] ) ? sanitize_text_field( (string) $input['annam_cr_dropoff'] ) : '';
	$route_label  = isset( $input['annam_cr_route'] ) ? sanitize_text_field( (string) $input['annam_cr_route'] ) : '';
	$source_note  = isset( $input['annam_cr_source_note'] ) ? sanitize_text_field( (string) $input['annam_cr_source_note'] ) : '';
	$round_trip  = ! empty( $input['annam_cr_round_trip'] );
	$travel_date = isset( $input['annam_cr_date'] ) ? sanitize_text_field( (string) $input['annam_cr_date'] ) : '';
	$vehicle     = isset( $input['annam_cr_vehicle'] ) ? sanitize_key( (string) $input['annam_cr_vehicle'] ) : '';
	$phone       = isset( $input['annam_cr_phone'] ) ? sanitize_text_field( (string) $input['annam_cr_phone'] ) : '';
	$today       = wp_date( 'Y-m-d' );

	if ( '' === trim( $pickup ) && '' === trim( $route_label ) ) {
		return $fail( __( 'Vui lòng nhập điểm đón hoặc hành trình.', 'generatepress_child' ), 'pickup' );
	}

	if ( '' === trim( $phone ) ) {
		return $fail( __( 'Vui lòng nhập số điện thoại hoặc Zalo.', 'generatepress_child' ), 'phone' );
	}

	if ( ! function_exists( 'annam_contact_validate_phone' ) || ! annam_contact_validate_phone( $phone ) ) {
		return $fail( __( 'Số điện thoại không hợp lệ.', 'generatepress_child' ), 'phone' );
	}

	if ( '' !== $travel_date ) {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $travel_date ) ) {
			return $fail( __( 'Ngày đi không hợp lệ.', 'generatepress_child' ), 'date' );
		}
		if ( $travel_date < $today ) {
			return $fail( __( 'Không thể chọn ngày trong quá khứ.', 'generatepress_child' ), 'date' );
		}
	}

	if ( '' !== $vehicle && 'unknown' !== $vehicle && ! annam_car_rental_is_valid_vehicle_type( $vehicle ) ) {
		return $fail( __( 'Loại xe không hợp lệ.', 'generatepress_child' ), 'vehicle' );
	}

	$types         = annam_car_rental_get_vehicle_types();
	$vehicle_label = 'unknown' === $vehicle ? __( 'Chưa biết, cần tư vấn', 'generatepress_child' ) : ( $types[ $vehicle ]['label'] ?? $vehicle );

	$is_compact_form = ! array_key_exists( 'annam_cr_dropoff', $input )
		&& ! array_key_exists( 'annam_cr_round_trip', $input )
		&& ! array_key_exists( 'annam_cr_date', $input )
		&& ! array_key_exists( 'annam_cr_vehicle', $input );

	$body_lines = array();

	if ( $is_compact_form ) {
		$body_lines[] = __( 'Điểm đón & Điểm đến:', 'generatepress_child' ) . ' ' . $pickup;
		if ( '' !== trim( $route_label ) ) {
			$body_lines[] = __( 'Hành trình:', 'generatepress_child' ) . ' ' . $route_label;
		}
		$body_lines[] = __( 'Số điện thoại / Zalo:', 'generatepress_child' ) . ' ' . $phone;

		$subject = sprintf(
			'[THUÊ XE] Báo giá nhanh — %s — %s',
			$pickup,
			$phone
		);
	} else {
		$body_lines = array(
			__( 'Điểm đón:', 'generatepress_child' ) . ' ' . $pickup,
			__( 'Điểm đến:', 'generatepress_child' ) . ' ' . ( '' !== trim( $dropoff ) ? $dropoff : '—' ),
			__( 'Thuê xe 2 chiều:', 'generatepress_child' ) . ' ' . ( $round_trip ? __( 'Có', 'generatepress_child' ) : __( 'Không / 1 chiều', 'generatepress_child' ) ),
			__( 'Ngày đi:', 'generatepress_child' ) . ' ' . ( '' !== $travel_date ? $travel_date : '—' ),
			__( 'Loại xe:', 'generatepress_child' ) . ' ' . $vehicle_label,
			__( 'Số điện thoại / Zalo:', 'generatepress_child' ) . ' ' . $phone,
		);

		if ( '' !== trim( $route_label ) ) {
			$body_lines[] = __( 'Hành trình:', 'generatepress_child' ) . ' ' . $route_label;
		}
		if ( '' !== trim( $source_note ) ) {
			$body_lines[] = __( 'Ghi chú:', 'generatepress_child' ) . ' ' . $source_note;
		}

		$subject = sprintf(
			'[THUÊ XE] Báo giá — %s — %s → %s — %s',
			$vehicle_label,
			$pickup,
			'' !== trim( $dropoff ) ? $dropoff : '—',
			$phone
		);
	}

	$sent = annam_lead_send_notification( annam_car_rental_get_lead_recipient_emails(), $subject, $body_lines );

	if ( function_exists( 'annam_rate_limit_increment' ) ) {
		annam_rate_limit_increment( 'annam_car_rental_booking', ANNAM_CAR_RENTAL_RATE_MINUTES );
	}

	if ( ! $sent ) {
		return $fail( __( 'Không gửi được email. Vui lòng gọi hotline hoặc nhắn Zalo.', 'generatepress_child' ), 'mail' );
	}

	$config = annam_car_rental_get_landing_config();
	$msg    = isset( $config['form']['success_message'] ) ? $config['form']['success_message'] : __( 'Cảm ơn quý khách. Chúng tôi sẽ liên hệ sớm.', 'generatepress_child' );

	return array(
		'success' => true,
		'message' => $msg,
	);
}

/**
 * AJAX handler.
 */
function annam_car_rental_ajax_booking() {
	$result = annam_car_rental_process_booking( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( ! empty( $result['success'] ) ) {
		wp_send_json_success( array( 'message' => $result['message'] ) );
	}

	wp_send_json_error(
		array(
			'message' => isset( $result['message'] ) ? $result['message'] : __( 'Có lỗi xảy ra.', 'generatepress_child' ),
			'code'    => isset( $result['code'] ) ? $result['code'] : 'error',
		),
		400
	);
}
add_action( 'wp_ajax_annam_car_rental_booking', 'annam_car_rental_ajax_booking' );
add_action( 'wp_ajax_nopriv_annam_car_rental_booking', 'annam_car_rental_ajax_booking' );
