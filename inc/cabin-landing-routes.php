<?php
/**
 * Tuyến hợp lệ, lọc giờ theo ngày, cài đặt email lead.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ANNAM_CABIN_LANDING_SETTINGS_OPTION' ) ) {
	define( 'ANNAM_CABIN_LANDING_SETTINGS_OPTION', 'annam_cabin_landing_settings' );
}

/**
 * Map điểm đi → điểm đến được phép.
 *
 * @return array<string,string[]>
 */
function annam_cabin_landing_get_route_destinations_map() {
	return array(
		'hanoi'  => array( 'sapa', 'laocai' ),
		'sapa'   => array( 'hanoi' ),
		'laocai' => array( 'hanoi' ),
	);
}

/**
 * @param string $from Place key.
 * @param string $to   Place key.
 * @return bool
 */
function annam_cabin_landing_is_valid_route( $from, $to ) {
	$from = sanitize_key( (string) $from );
	$to   = sanitize_key( (string) $to );
	if ( '' === $from || '' === $to || $from === $to ) {
		return false;
	}
	$map = annam_cabin_landing_get_route_destinations_map();
	return isset( $map[ $from ] ) && in_array( $to, $map[ $from ], true );
}

/**
 * @param string $time HH:MM.
 * @return int Minutes since midnight (24:00 = 1440).
 */
function annam_cabin_landing_time_to_minutes( $time ) {
	$time = trim( (string) $time );
	if ( ! preg_match( '/^(\d{1,2}):(\d{2})$/', $time, $m ) ) {
		return -1;
	}
	$h = (int) $m[1];
	$min = (int) $m[2];
	if ( $h === 24 && 0 === $min ) {
		return 24 * 60;
	}
	if ( $h < 0 || $h > 23 || $min < 0 || $min > 59 ) {
		return -1;
	}
	return $h * 60 + $min;
}

/**
 * Lọc giờ chạy theo ngày (hôm nay: giờ hiện tại theo calendar, cộng 2 tiếng, bỏ phút; giữ chuyến >= mốc đó).
 *
 * @param string $from      hanoi|sapa|laocai.
 * @param string $to        hanoi|sapa|laocai.
 * @param string $date_ymd  Y-m-d.
 * @return string[]
 */
function annam_cabin_landing_filter_times_for_date( $from, $to, $date_ymd ) {
	if ( ! annam_cabin_landing_is_valid_route( $from, $to ) ) {
		return array();
	}

	$all = annam_cabin_landing_get_departure_times_for_places( $from, $to );
	$date_ymd = preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $date_ymd ) ? (string) $date_ymd : '';
	if ( '' === $date_ymd ) {
		return $all;
	}

	$today = wp_date( 'Y-m-d' );
	if ( $date_ymd > $today ) {
		return $all;
	}
	if ( $date_ymd < $today ) {
		return array();
	}

	$tz             = wp_timezone();
	$now            = new DateTimeImmutable( 'now', $tz );
	$hour           = (int) $now->format( 'G' );
	$lead           = (int) apply_filters( 'annam_cabin_landing_min_lead_hours', 2 );
	$threshold_hour = $hour + max( 0, $lead );

	if ( $threshold_hour > 24 ) {
		return array();
	}

	$threshold_minutes = $threshold_hour * 60;
	$filtered          = array();

	foreach ( $all as $time ) {
		$mins = annam_cabin_landing_time_to_minutes( $time );
		if ( $mins < 0 ) {
			continue;
		}
		if ( $mins >= $threshold_minutes ) {
			$filtered[] = $time;
		}
	}

	return $filtered;
}

/**
 * @return array<string,mixed>
 */
function annam_cabin_landing_get_settings() {
	$raw = get_option( ANNAM_CABIN_LANDING_SETTINGS_OPTION, array() );
	return is_array( $raw ) ? $raw : array();
}

/**
 * Email nhận lead (mảng địa chỉ hợp lệ).
 *
 * @return string[]
 */
function annam_cabin_landing_get_lead_recipient_emails() {
	$settings = annam_cabin_landing_get_settings();
	$raw      = isset( $settings['lead_emails'] ) ? (string) $settings['lead_emails'] : '';
	$parts    = array_map( 'trim', explode( ',', $raw ) );
	$emails   = array();

	foreach ( $parts as $part ) {
		if ( is_email( $part ) ) {
			$emails[] = $part;
		}
	}

	$emails = array_values( array_unique( $emails ) );
	if ( ! empty( $emails ) ) {
		return $emails;
	}

	$admin = get_option( 'admin_email' );
	return is_email( $admin ) ? array( $admin ) : array();
}

/**
 * Lưu cài đặt lead email (admin).
 */
function annam_cabin_landing_maybe_save_settings() {
	if ( ! is_admin() || empty( $_POST['annam_cabin_landing_settings_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}
	if ( empty( $_GET['page'] ) || 'annam-cabin-landing-images' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'annam_save_cabin_landing_settings', 'annam_cabin_landing_settings_nonce' );

	$raw = isset( $_POST['annam_cabin_lead_emails'] ) ? sanitize_text_field( wp_unslash( $_POST['annam_cabin_lead_emails'] ) ) : '';

	update_option(
		ANNAM_CABIN_LANDING_SETTINGS_OPTION,
		array(
			'lead_emails' => $raw,
		),
		false
	);

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'               => 'annam-cabin-landing-images',
				'annam_settings_saved' => '1',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_init', 'annam_cabin_landing_maybe_save_settings' );
