<?php
/**
 * Tuyến Limousine HN ⇄ Sapa, lọc giờ, email lead.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/annam-lead-mail.php';

if ( ! defined( 'ANNAM_LIMO_LANDING_SETTINGS_OPTION' ) ) {
	define( 'ANNAM_LIMO_LANDING_SETTINGS_OPTION', 'annam_limo_landing_settings' );
}

/**
 * @return array<string,string[]>
 */
function annam_limo_landing_get_route_destinations_map( $route = 'sapa' ) {
	$profile = function_exists( 'annam_limo_landing_get_route_profile' )
		? annam_limo_landing_get_route_profile( $route )
		: array();
	if ( ! empty( $profile['destinations'] ) && is_array( $profile['destinations'] ) ) {
		return $profile['destinations'];
	}
	return array(
		'hanoi' => array( 'sapa' ),
		'sapa'  => array( 'hanoi' ),
	);
}

/**
 * @param string $from Place key.
 * @param string $to   Place key.
 * @return bool
 */
function annam_limo_landing_is_valid_route( $from, $to, $route = 'sapa' ) {
	$from = sanitize_key( (string) $from );
	$to   = sanitize_key( (string) $to );
	if ( '' === $from || '' === $to || $from === $to ) {
		return false;
	}
	$map = annam_limo_landing_get_route_destinations_map( $route );
	return isset( $map[ $from ] ) && in_array( $to, $map[ $from ], true );
}

/**
 * @param string $time HH:MM.
 * @return int
 */
function annam_limo_landing_time_to_minutes( $time ) {
	$time = trim( (string) $time );
	if ( ! preg_match( '/^(\d{1,2}):(\d{2})$/', $time, $m ) ) {
		return -1;
	}
	$h   = (int) $m[1];
	$min = (int) $m[2];
	if ( $h < 0 || $h > 23 || $min < 0 || $min > 59 ) {
		return -1;
	}
	return $h * 60 + $min;
}

/**
 * Lọc giờ theo ngày (hôm nay: cộng số phút đặt trước của tuyến).
 *
 * @param string $from     Place key.
 * @param string $to       Place key.
 * @param string $date_ymd Y-m-d.
 * @param string $route    Route key.
 * @return string[]
 */
function annam_limo_landing_filter_times_for_date( $from, $to, $date_ymd, $route = 'sapa' ) {
	$route = function_exists( 'annam_limo_landing_storage_route' )
		? annam_limo_landing_storage_route( $route )
		: 'sapa';
	if ( ! annam_limo_landing_is_valid_route( $from, $to, $route ) ) {
		return array();
	}

	$all      = annam_limo_landing_departure_times( $from, $to, $route );
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

	$profile = function_exists( 'annam_limo_landing_get_route_profile' )
		? annam_limo_landing_get_route_profile( $route )
		: array();
	$lead    = isset( $profile['min_lead_minutes'] ) ? (int) $profile['min_lead_minutes'] : 120;
	$lead    = (int) apply_filters( 'annam_limo_landing_min_lead_minutes', $lead, $route );

	$tz      = wp_timezone();
	$now     = new DateTimeImmutable( 'now', $tz );
	$now_min = ( (int) $now->format( 'G' ) * 60 ) + (int) $now->format( 'i' );
	$threshold_minutes = $now_min + max( 0, $lead );

	if ( $threshold_minutes >= 24 * 60 ) {
		return array();
	}

	$filtered = array();

	foreach ( $all as $time ) {
		$mins = annam_limo_landing_time_to_minutes( $time );
		if ( $mins >= 0 && $mins >= $threshold_minutes ) {
			$filtered[] = $time;
		}
	}

	return $filtered;
}

/**
 * @return array<string,mixed>
 */
function annam_limo_landing_get_settings( $route = '' ) {
	$route = function_exists( 'annam_limo_landing_storage_route' )
		? annam_limo_landing_storage_route( $route )
		: 'sapa';
	$option = function_exists( 'annam_limo_landing_option_name' )
		? annam_limo_landing_option_name( 'annam_limo_landing_settings', $route )
		: ANNAM_LIMO_LANDING_SETTINGS_OPTION;
	$saved = get_option( $option, array() );
	return is_array( $saved ) ? $saved : array();
}

/**
 * URL YouTube trust video (admin hoặc placeholder).
 *
 * @return string
 */
function annam_limo_landing_get_youtube_url( $route = '' ) {
	$route    = function_exists( 'annam_limo_landing_storage_route' )
		? annam_limo_landing_storage_route( $route )
		: 'sapa';
	$settings = annam_limo_landing_get_settings( $route );
	if ( ! empty( $settings['youtube_url'] ) && is_string( $settings['youtube_url'] ) ) {
		$url = esc_url_raw( trim( $settings['youtube_url'] ) );
		if ( '' !== $url ) {
			return $url;
		}
	}
	return 'https://www.youtube.com/watch?v=aqz-KE-bpKQ';
}

/**
 * Lấy YouTube video ID từ URL.
 *
 * @param string $url YouTube URL.
 * @return string
 */
function annam_limo_landing_youtube_video_id( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( preg_match( '/(?:youtube\.com\/(?:watch\?(?:[^#]*&)?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/', $url, $m ) ) {
		return $m[1];
	}
	if ( preg_match( '/^[A-Za-z0-9_-]{6,}$/', $url ) ) {
		return $url;
	}
	return '';
}

/**
 * @param string $route Route key. Unused placeholder kept for signature clarity in callers.
 * @return string
 */
function annam_limo_landing_youtube_url_or_empty( $route = '' ) {
	$route    = function_exists( 'annam_limo_landing_storage_route' )
		? annam_limo_landing_storage_route( $route )
		: 'sapa';
	$settings = annam_limo_landing_get_settings( $route );
	if ( ! empty( $settings['youtube_url'] ) && is_string( $settings['youtube_url'] ) ) {
		$url = esc_url_raw( trim( $settings['youtube_url'] ) );
		if ( '' !== $url ) {
			return $url;
		}
	}
	if ( 'halong' === $route ) {
		return '';
	}
	return annam_limo_landing_get_youtube_url( $route );
}

/**
 * src embed YouTube (privacy-enhanced).
 *
 * @param string $url Optional URL; empty = from settings.
 * @return string
 */
function annam_limo_landing_youtube_embed_src( $url = '' ) {
	if ( '' === $url ) {
		$url = annam_limo_landing_get_youtube_url();
	}
	$id = annam_limo_landing_youtube_video_id( $url );
	if ( '' === $id ) {
		return '';
	}
	return 'https://www.youtube-nocookie.com/embed/' . rawurlencode( $id );
}

/**
 * @return string[]
 */
function annam_limo_landing_get_lead_recipient_emails( $route = '' ) {
	$route    = function_exists( 'annam_limo_landing_storage_route' )
		? annam_limo_landing_storage_route( $route )
		: 'sapa';
	$settings = annam_limo_landing_get_settings( $route );
	$raw      = isset( $settings['lead_emails'] ) ? (string) $settings['lead_emails'] : '';
	$parts    = preg_split( '/[\s,;]+/', $raw ) ?: array();
	$emails   = array();
	foreach ( $parts as $part ) {
		$email = sanitize_email( $part );
		if ( is_email( $email ) ) {
			$emails[] = $email;
		}
	}
	if ( ! empty( $emails ) ) {
		return array_values( array_unique( $emails ) );
	}
	if ( 'halong' === $route && is_email( 'annamdiscoveryvn@gmail.com' ) ) {
		return array( 'annamdiscoveryvn@gmail.com' );
	}
	$admin = get_option( 'admin_email' );
	return is_email( $admin ) ? array( $admin ) : array();
}

/**
 * Lưu email lead từ admin.
 */
function annam_limo_landing_maybe_save_settings() {
	if ( ! is_admin() || empty( $_POST['annam_limo_landing_settings_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}
	if ( empty( $_GET['page'] ) || 'annam-limo-landing-images' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'annam_save_limo_landing_settings', 'annam_limo_landing_settings_nonce' );

	$route = function_exists( 'annam_limo_landing_admin_route' ) ? annam_limo_landing_admin_route() : 'sapa';
	$saved = annam_limo_landing_get_settings( $route );
	$saved['lead_emails'] = isset( $_POST['annam_limo_lead_emails'] ) ? sanitize_text_field( wp_unslash( $_POST['annam_limo_lead_emails'] ) ) : '';
	$saved['youtube_url'] = isset( $_POST['annam_limo_youtube_url'] ) ? esc_url_raw( trim( (string) wp_unslash( $_POST['annam_limo_youtube_url'] ) ) ) : '';

	$option = function_exists( 'annam_limo_landing_option_name' )
		? annam_limo_landing_option_name( 'annam_limo_landing_settings', $route )
		: ANNAM_LIMO_LANDING_SETTINGS_OPTION;
	update_option( $option, $saved, false );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'        => 'annam-limo-landing-images',
				'route'       => $route,
				'annam_saved' => '1',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_init', 'annam_limo_landing_maybe_save_settings' );
