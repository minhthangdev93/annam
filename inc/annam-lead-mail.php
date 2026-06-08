<?php
/**
 * Gửi email lead form landing (Cabin VIP, thuê xe…) — dùng chung.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return string[]
 */
function annam_lead_get_recipient_emails() {
	if ( function_exists( 'annam_cabin_landing_get_lead_recipient_emails' ) ) {
		$emails = annam_cabin_landing_get_lead_recipient_emails();
		if ( ! empty( $emails ) ) {
			return $emails;
		}
	}

	$admin = get_option( 'admin_email' );
	return is_email( $admin ) ? array( $admin ) : array();
}

/**
 * @param string $reply_to Optional Reply-To (SĐT khách dạng email placeholder không dùng — để trống).
 * @return string[]
 */
function annam_lead_build_mail_headers( $reply_to = '' ) {
	$site_name  = wp_specialchars_decode( (string) get_bloginfo( 'name' ), ENT_QUOTES );
	$from_email = get_option( 'admin_email' );

	if ( ! is_email( $from_email ) ) {
		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		$from_email = is_string( $host ) && '' !== $host ? 'noreply@' . $host : '';
	}

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

	if ( is_email( $from_email ) ) {
		$from_label = '' !== $site_name ? $site_name : 'WordPress';
		$headers[]  = sprintf( 'From: %s <%s>', $from_label, $from_email );
	}

	if ( '' !== $reply_to && is_email( $reply_to ) ) {
		$headers[] = sprintf( 'Reply-To: %s', $reply_to );
	}

	return $headers;
}

/**
 * Gửi email lead tới danh sách người nhận.
 *
 * @param string[]            $recipients Địa chỉ nhận; rỗng thì lấy mặc định.
 * @param string              $subject    Tiêu đề (nên có prefix phân loại lead).
 * @param array<int,string>   $body_lines Các dòng nội dung (chỉ field form).
 * @return bool
 */
function annam_lead_send_notification( array $recipients, $subject, array $body_lines ) {
	if ( empty( $recipients ) ) {
		$recipients = annam_lead_get_recipient_emails();
	}

	$recipients = array_values(
		array_filter(
			array_unique( array_map( 'sanitize_email', $recipients ) ),
			'is_email'
		)
	);

	if ( empty( $recipients ) ) {
		return false;
	}

	$lines = array();
	foreach ( $body_lines as $line ) {
		$line = is_string( $line ) ? trim( $line ) : '';
		if ( '' !== $line ) {
			$lines[] = $line;
		}
	}

	if ( empty( $lines ) ) {
		return false;
	}

	$subject = trim( (string) $subject );
	if ( '' === $subject ) {
		return false;
	}

	$body    = implode( "\n", $lines );
	$headers = annam_lead_build_mail_headers();
	$sent    = false;

	foreach ( $recipients as $to ) {
		if ( wp_mail( $to, $subject, $body, $headers ) ) {
			$sent = true;
		}
	}

	return $sent;
}
