<?php
/**
 * Booking Tour Sapa 3N2Đ (POST + AJAX).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param array<string,mixed> $input Raw POST.
 * @return array{success:bool,message:string,code?:string}
 */
function annam_tour_sapa_landing_process_booking( array $input ) {
	$config = annam_tour_sapa_landing_get_config();

	$fail = static function ( $message, $code = 'error' ) {
		return array(
			'success' => false,
			'message' => $message,
			'code'    => $code,
		);
	};

	if ( empty( $input['annam_tour_sapa_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( (string) $input['annam_tour_sapa_nonce'] ), 'annam_tour_sapa_booking' ) ) {
		return $fail( __( 'Phiên không hợp lệ. Vui lòng tải lại trang và thử lại.', 'generatepress_child' ), 'nonce' );
	}

	if ( ! empty( $input['annam_tour_sapa_website'] ) ) {
		return $fail( __( 'Không gửi được yêu cầu.', 'generatepress_child' ), 'spam' );
	}

	$ts = isset( $input['annam_tour_sapa_ts'] ) ? absint( $input['annam_tour_sapa_ts'] ) : 0;
	if ( ! $ts || ( time() - $ts ) < 3 || ( time() - $ts ) > 7200 ) {
		return $fail( __( 'Yêu cầu không hợp lệ. Vui lòng thử lại.', 'generatepress_child' ), 'ts' );
	}

	if ( function_exists( 'annam_check_rate_limit' ) && ! annam_check_rate_limit( 'annam_tour_sapa_booking', ANNAM_TOUR_SAPA_LANDING_RATE_MAX, ANNAM_TOUR_SAPA_LANDING_RATE_MINUTES ) ) {
		return $fail( __( 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng gọi hotline hoặc nhắn Zalo.', 'generatepress_child' ), 'rate' );
	}

	$travel_date = isset( $input['annam_tour_sapa_date'] ) ? sanitize_text_field( (string) $input['annam_tour_sapa_date'] ) : '';
	$hotel       = isset( $input['annam_tour_sapa_hotel'] ) ? sanitize_key( (string) $input['annam_tour_sapa_hotel'] ) : '';
	$guests      = isset( $input['annam_tour_sapa_guests'] ) ? max( 1, min( 30, (int) $input['annam_tour_sapa_guests'] ) ) : 1;
	$name        = isset( $input['annam_tour_sapa_name'] ) ? sanitize_text_field( (string) $input['annam_tour_sapa_name'] ) : '';
	$phone       = isset( $input['annam_tour_sapa_phone'] ) ? sanitize_text_field( (string) $input['annam_tour_sapa_phone'] ) : '';
	$note        = isset( $input['annam_tour_sapa_note'] ) ? sanitize_textarea_field( (string) $input['annam_tour_sapa_note'] ) : '';

	$valid_hotels = array( '3star', '4star' );
	$today        = wp_date( 'Y-m-d' );

	if ( ! in_array( $hotel, $valid_hotels, true ) ) {
		return $fail( __( 'Vui lòng chọn hạng khách sạn.', 'generatepress_child' ), 'hotel' );
	}

	if ( '' === trim( $name ) ) {
		return $fail( __( 'Vui lòng nhập họ và tên.', 'generatepress_child' ), 'name' );
	}

	if ( '' === trim( $phone ) ) {
		return $fail( __( 'Vui lòng nhập số điện thoại, Zalo hoặc WhatsApp.', 'generatepress_child' ), 'phone' );
	}

	if ( ! function_exists( 'annam_contact_validate_phone' ) || ! annam_contact_validate_phone( $phone ) ) {
		return $fail( __( 'Số điện thoại không hợp lệ.', 'generatepress_child' ), 'phone' );
	}

	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $travel_date ) ) {
		return $fail( __( 'Vui lòng chọn ngày khởi hành.', 'generatepress_child' ), 'date' );
	}

	if ( $travel_date < $today ) {
		return $fail( __( 'Không thể chọn ngày trong quá khứ.', 'generatepress_child' ), 'date' );
	}

	$hotel_labels = array(
		'3star' => 'Khách sạn 3 sao (2.990.000đ)',
		'4star' => 'Khách sạn 4 sao (3.990.000đ)',
	);
	$hotel_label = isset( $hotel_labels[ $hotel ] ) ? $hotel_labels[ $hotel ] : $hotel;

	$cta   = annam_tour_sapa_landing_get_cta();
	$brand = isset( $cta['brand'] ) ? $cta['brand'] : 'An Nam Discovery';

	$body_lines = array(
		__( 'Tour:', 'generatepress_child' ) . ' ' . ( isset( $config['product_name'] ) ? $config['product_name'] : 'Tour Sapa 3N2Đ' ),
		__( 'Họ tên:', 'generatepress_child' ) . ' ' . $name,
		__( 'SĐT/Zalo/WhatsApp:', 'generatepress_child' ) . ' ' . $phone,
		__( 'Ngày khởi hành:', 'generatepress_child' ) . ' ' . $travel_date,
		__( 'Số khách:', 'generatepress_child' ) . ' ' . $guests,
		__( 'Hạng KS:', 'generatepress_child' ) . ' ' . $hotel_label,
	);
	if ( '' !== trim( $note ) ) {
		$body_lines[] = __( 'Ghi chú:', 'generatepress_child' ) . ' ' . $note;
	}
	$page_url = isset( $input['annam_tour_sapa_page_url'] ) ? esc_url_raw( (string) $input['annam_tour_sapa_page_url'] ) : '';
	if ( $page_url ) {
		$body_lines[] = __( 'Trang:', 'generatepress_child' ) . ' ' . $page_url;
	}

	$recipients = array();
	if ( function_exists( 'annam_tour_sapa_landing_get_lead_emails' ) ) {
		$recipients = annam_tour_sapa_landing_get_lead_emails();
	}
	if ( empty( $recipients ) ) {
		$recipients = array( get_option( 'admin_email' ) );
	}

	$subject = sprintf(
		'[TOUR SAPA 3N2D] Giữ chỗ — %s — %s — %s',
		$travel_date,
		$name,
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
		annam_rate_limit_increment( 'annam_tour_sapa_booking', ANNAM_TOUR_SAPA_LANDING_RATE_MINUTES );
	}

	$msg = isset( $config['form']['success_message'] ) ? $config['form']['success_message'] : __( 'Đã nhận yêu cầu.', 'generatepress_child' );

	return array(
		'success' => true,
		'message' => $msg,
		'code'    => 'ok',
	);
}

/**
 * Fresh nonce for LiteSpeed / cache.
 */
function annam_tour_sapa_landing_ajax_fresh_nonce() {
	wp_send_json_success(
		array(
			'nonce' => wp_create_nonce( 'annam_tour_sapa_booking' ),
			'ts'    => (string) ( time() - 5 ),
		)
	);
}
add_action( 'wp_ajax_annam_tour_sapa_booking_nonce', 'annam_tour_sapa_landing_ajax_fresh_nonce' );
add_action( 'wp_ajax_nopriv_annam_tour_sapa_booking_nonce', 'annam_tour_sapa_landing_ajax_fresh_nonce' );

/**
 * AJAX booking.
 */
function annam_tour_sapa_landing_ajax_booking() {
	$result = annam_tour_sapa_landing_process_booking( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
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
add_action( 'wp_ajax_annam_tour_sapa_booking', 'annam_tour_sapa_landing_ajax_booking' );
add_action( 'wp_ajax_nopriv_annam_tour_sapa_booking', 'annam_tour_sapa_landing_ajax_booking' );
