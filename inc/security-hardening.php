<?php
/**
 * Cứng hóa bảo mật (XML-RPC, enumeration, header, rate limit chung, đăng nhập).
 * Form cụ thể: contact / tour lead / review — xử lý trong file tương ứng + helper dưới đây.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * IP cho rate limit (hash trong key transient, không lưu IP thô trong option).
 *
 * @return string Chuỗi hợp lệ cho hash (có thể rỗng).
 */
function annam_security_ip_for_ratelimit() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return preg_match( '/^[\d.:a-fA-F]+$/', $ip ) ? $ip : '';
}

/**
 * Kiểm tra rate limit theo IP (transient sliding window).
 *
 * @param string $action   Khóa logic, ví dụ `annam_contact_form`.
 * @param int    $limit    Số lần tối đa trong cửa sổ.
 * @param int    $minutes  Độ dài cửa sổ (phút).
 * @return bool True nếu còn được phép (chưa vượt), false nếu đã chặn.
 */
function annam_check_rate_limit( $action, $limit, $minutes ) {
	$action = sanitize_key( (string) $action );
	$limit  = (int) $limit;
	$minutes = (int) $minutes;
	if ( '' === $action || $limit < 1 || $minutes < 1 ) {
		return true;
	}
	$ip = annam_security_ip_for_ratelimit();
	$key = 'annam_rl_' . md5( $action . '|' . $ip );
	$now = time();
	$win = $minutes * MINUTE_IN_SECONDS;
	$raw = get_transient( $key );
	$hits = is_array( $raw ) ? $raw : array();
	$hits = array_values(
		array_filter(
			array_map( 'intval', $hits ),
			static function ( $t ) use ( $now, $win ) {
				return $t > ( $now - $win );
			}
		)
	);
	return count( $hits ) < $limit;
}

/**
 * Ghi nhận một lần thao tác vào rate limit (gọi sau khi xử lý hợp lệ / gửi thành công tùy luồng).
 *
 * @param string $action   Khóa logic.
 * @param int    $minutes  Độ dài cửa sổ (phút), dùng cho TTL transient.
 * @return void
 */
function annam_rate_limit_increment( $action, $minutes ) {
	$action = sanitize_key( (string) $action );
	$minutes = (int) $minutes;
	if ( '' === $action || $minutes < 1 ) {
		return;
	}
	$ip = annam_security_ip_for_ratelimit();
	$key = 'annam_rl_' . md5( $action . '|' . $ip );
	$now = time();
	$win = $minutes * MINUTE_IN_SECONDS;
	$raw = get_transient( $key );
	$hits = is_array( $raw ) ? $raw : array();
	$hits = array_values(
		array_filter(
			array_map( 'intval', $hits ),
			static function ( $t ) use ( $now, $win ) {
				return $t > ( $now - $win );
			}
		)
	);
	$hits[] = $now;
	set_transient( $key, $hits, $win + 120 );
}

/**
 * Chặn liệt kê user qua ?author=N (không ảnh hưởng permalink bài viết).
 */
function annam_security_block_author_enumeration() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}
	if ( ! isset( $_GET['author'] ) ) {
		return;
	}
	if ( apply_filters( 'annam_security_allow_author_query', false ) ) {
		return;
	}
	$author = absint( wp_unslash( $_GET['author'] ) );
	if ( $author < 1 ) {
		return;
	}
	wp_safe_redirect( home_url( '/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'annam_security_block_author_enumeration', 0 );

/**
 * Tắt XML-RPC (Jetpack / app cần thì filter `annam_security_disable_xmlrpc` = false).
 */
function annam_security_disable_xmlrpc() {
	if ( ! apply_filters( 'annam_security_disable_xmlrpc', true ) ) {
		return;
	}
	add_filter( 'xmlrpc_enabled', '__return_false' );
	add_filter( 'xmlrpc_methods', 'annam_security_strip_xmlrpc_pingback' );
}
add_action( 'init', 'annam_security_disable_xmlrpc', 1 );

/**
 * @param array<string,mixed> $methods Methods.
 * @return array<string,mixed>
 */
function annam_security_strip_xmlrpc_pingback( $methods ) {
	if ( ! is_array( $methods ) ) {
		return $methods;
	}
	unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );
	return $methods;
}

/**
 * Khách: hạn chế lộ danh sách user qua REST /wp/v2/users.
 *
 * @param WP_Error|null|bool $errors Errors.
 * @return WP_Error|null|bool
 */
function annam_security_rest_guest_users_forbidden( $errors ) {
	if ( ! empty( $errors ) ) {
		return $errors;
	}
	if ( is_user_logged_in() ) {
		return $errors;
	}
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	if ( preg_match( '#wp-json/wp/v2/users#', $uri ) ) {
		return new WP_Error(
			'rest_forbidden',
			__( 'Bạn không có quyền truy cập.', 'generatepress_child' ),
			array( 'status' => 401 )
		);
	}
	return $errors;
}
add_filter( 'rest_authentication_errors', 'annam_security_rest_guest_users_forbidden', 20 );

/**
 * Header bảo mật cơ bản (không bật CSP cứng).
 */
function annam_security_send_headers() {
	if ( is_admin() ) {
		return;
	}
	if ( ! headers_sent() ) {
		header( 'X-Content-Type-Options: nosniff', false );
		header( 'X-Frame-Options: SAMEORIGIN', false );
		header( 'Referrer-Policy: strict-origin-when-cross-origin', false );
		header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()', false );
	}
}
add_action( 'send_headers', 'annam_security_send_headers', 1 );

/**
 * Đếm đăng nhập sai theo IP (khóa tạm sau N lần).
 */
function annam_security_login_failed_count_key( $ip ) {
	return 'annam_lf_' . md5( $ip );
}

/**
 * @param string $username Username (unused).
 */
function annam_security_login_failed_track( $username ) {
	unset( $username );
	$ip = annam_security_ip_for_ratelimit();
	if ( '' === $ip ) {
		return;
	}
	$key  = annam_security_login_failed_count_key( $ip );
	$now  = time();
	$win_min = (int) apply_filters( 'annam_security_login_lockout_minutes', 15 );
	$win     = max( 1, $win_min ) * MINUTE_IN_SECONDS;
	$raw  = get_transient( $key );
	$hits = is_array( $raw ) ? $raw : array();
	$hits = array_values(
		array_filter(
			array_map( 'intval', $hits ),
			static function ( $t ) use ( $now, $win ) {
				return $t > ( $now - $win );
			}
		)
	);
	$hits[] = $now;
	set_transient( $key, $hits, $win + 60 );
}
add_action( 'wp_login_failed', 'annam_security_login_failed_track', 10, 1 );

/**
 * @param string $user_login Login.
 */
function annam_security_login_success_clear( $user_login ) {
	unset( $user_login );
	$ip = annam_security_ip_for_ratelimit();
	if ( '' === $ip ) {
		return;
	}
	delete_transient( annam_security_login_failed_count_key( $ip ) );
}
add_action( 'wp_login', 'annam_security_login_success_clear', 10, 1 );

/**
 * Khóa đăng nhập tạm nếu quá nhiều lần sai (theo IP) — trước khi kiểm tra mật khẩu.
 *
 * @param WP_User|WP_Error|null $user     User.
 * @param string                  $username Username.
 * @param string                  $password Password.
 * @return WP_User|WP_Error|null
 */
function annam_security_authenticate_lockout( $user, $username, $password ) {
	if ( $user instanceof WP_User ) {
		return $user;
	}
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		return $user;
	}
	$username = is_string( $username ) ? $username : '';
	$password = is_string( $password ) ? $password : '';
	if ( '' === $username && '' === $password ) {
		return $user;
	}
	$max = (int) apply_filters( 'annam_security_login_max_attempts', 5 );
	$win = (int) apply_filters( 'annam_security_login_lockout_minutes', 15 );
	if ( $max < 1 || $win < 1 ) {
		return $user;
	}
	$ip = annam_security_ip_for_ratelimit();
	if ( '' === $ip ) {
		return $user;
	}
	$key  = annam_security_login_failed_count_key( $ip );
	$now  = time();
	$secs = $win * MINUTE_IN_SECONDS;
	$raw  = get_transient( $key );
	$hits = is_array( $raw ) ? $raw : array();
	$hits = array_values(
		array_filter(
			array_map( 'intval', $hits ),
			static function ( $t ) use ( $now, $secs ) {
				return $t > ( $now - $secs );
			}
		)
	);
	if ( count( $hits ) >= $max ) {
		return new WP_Error(
			'annam_login_locked',
			__( 'Đăng nhập tạm thời bị khóa do quá nhiều lần thử. Vui lòng thử lại sau.', 'generatepress_child' )
		);
	}
	return $user;
}
add_filter( 'authenticate', 'annam_security_authenticate_lockout', 5, 3 );

/**
 * Comment: từ khóa spam → chờ duyệt (không xóa nội dung).
 *
 * @param int|string|float $approved Approved.
 * @param array<string,mixed> $commentdata Data.
 * @return int|string|float
 */
function annam_security_comment_spam_keywords( $approved, $commentdata ) {
	if ( 1 === (int) $approved || 'spam' === $approved ) {
		return $approved;
	}
	$text = '';
	if ( isset( $commentdata['comment_content'] ) ) {
		$text .= ' ' . $commentdata['comment_content'];
	}
	if ( isset( $commentdata['comment_author'] ) ) {
		$text .= ' ' . $commentdata['comment_author'];
	}
	$text = strtolower( wp_strip_all_tags( (string) $text ) );
	$keywords = apply_filters(
		'annam_security_comment_spam_keywords',
		array(
			'viagra',
			'cialis',
			'casino',
			'porn',
			'seo service',
			'backlink',
			'crypto wallet',
			'telegram @',
		)
	);
	foreach ( (array) $keywords as $kw ) {
		$kw = strtolower( trim( (string) $kw ) );
		if ( '' !== $kw && false !== strpos( $text, $kw ) ) {
			return 0;
		}
	}
	return $approved;
}
add_filter( 'pre_comment_approved', 'annam_security_comment_spam_keywords', 9, 2 );

/**
 * Ghi .htaccess chặn PHP trong uploads (Apache); chỉ chạy một lần nếu ghi được.
 */
function annam_security_maybe_install_uploads_htaccess() {
	if ( ! apply_filters( 'annam_security_install_uploads_htaccess', true ) ) {
		return;
	}
	if ( get_option( 'annam_uploads_htaccess_done' ) ) {
		return;
	}
	$upload = wp_upload_dir();
	if ( ! empty( $upload['error'] ) || empty( $upload['basedir'] ) ) {
		return;
	}
	$dir = $upload['basedir'];
	if ( ! is_dir( $dir ) || ! is_writable( $dir ) ) {
		return;
	}
	$path = trailingslashit( $dir ) . '.htaccess';
	if ( file_exists( $path ) ) {
		update_option( 'annam_uploads_htaccess_done', 1, false );
		return;
	}
	$rules = "# Annam child theme — chặn thực thi PHP trong uploads (Apache 2.4+)\n<Files \"*.php\">\nRequire all denied\n</Files>\n<FilesMatch \"\\.(?i:phtml|phps|php[0-9]*)$\">\nRequire all denied\n</FilesMatch>\n";
	if ( false !== @file_put_contents( $path, $rules ) ) {
		update_option( 'annam_uploads_htaccess_done', 1, false );
	}
}
add_action( 'admin_init', 'annam_security_maybe_install_uploads_htaccess', 20 );
