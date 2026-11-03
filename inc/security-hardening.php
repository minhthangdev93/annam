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
 * Bỏ URL website trên form bình luận (spam hay nhét link vào đây).
 *
 * @param array<string,mixed> $commentdata Data.
 * @return array<string,mixed>
 */
function annam_security_strip_comment_author_url( $commentdata ) {
	$type = isset( $commentdata['comment_type'] ) ? (string) $commentdata['comment_type'] : '';
	if ( 'review' === $type ) {
		return $commentdata;
	}
	$commentdata['comment_author_url'] = '';
	return $commentdata;
}
add_filter( 'preprocess_comment', 'annam_security_strip_comment_author_url', 5 );

/**
 * Đóng bình luận bài viết / trang cũ hơn N ngày (review Woo giữ nguyên).
 *
 * @param bool $open    Open.
 * @param int  $post_id Post ID.
 * @return bool
 */
function annam_security_close_old_comments( $open, $post_id ) {
	if ( ! $open ) {
		return false;
	}
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return $open;
	}
	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post ) {
		return $open;
	}
	if ( 'product' === $post->post_type ) {
		return $open;
	}
	$days = (int) apply_filters( 'annam_security_close_comments_after_days', 45 );
	if ( $days <= 0 ) {
		return $open;
	}
	$created = strtotime( $post->post_date_gmt . ' UTC' );
	if ( ! $created ) {
		return $open;
	}
	if ( ( time() - $created ) > ( $days * DAY_IN_SECONDS ) ) {
		return false;
	}
	return $open;
}
add_filter( 'comments_open', 'annam_security_close_old_comments', 20, 2 );

/**
 * Heuristic: comment có vẻ spam?
 *
 * @param array<string,mixed> $commentdata Data.
 * @return bool
 */
function annam_security_comment_looks_like_spam( $commentdata ) {
	$type = isset( $commentdata['comment_type'] ) ? (string) $commentdata['comment_type'] : '';
	if ( 'review' === $type ) {
		return false;
	}

	$content = isset( $commentdata['comment_content'] ) ? (string) $commentdata['comment_content'] : '';
	$author  = isset( $commentdata['comment_author'] ) ? (string) $commentdata['comment_author'] : '';
	$email   = isset( $commentdata['comment_author_email'] ) ? (string) $commentdata['comment_author_email'] : '';
	$url     = isset( $commentdata['comment_author_url'] ) ? (string) $commentdata['comment_author_url'] : '';
	$plain   = strtolower( wp_strip_all_tags( $content . ' ' . $author . ' ' . $email . ' ' . $url ) );

	if ( '' !== trim( $url ) && ! in_array( $url, array( 'http://', 'https://' ), true ) ) {
		return true;
	}

	/* Link trong nội dung / author kiểu domain. */
	if ( preg_match( '/https?:\/\/|www\.\S+\.\S+/i', $content . ' ' . $author ) ) {
		return true;
	}
	if ( preg_match( '/\b[a-z0-9-]+\.(com|net|org|xyz|top|click|shop|app|info|biz)\b/i', $author ) ) {
		return true;
	}

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
			'jackpot',
			'betting',
			'gambling',
			'slot game',
			'forex',
			'binance',
			'my website',
			'check out my',
			'stop by my',
			'look at my web',
		)
	);
	foreach ( (array) $keywords as $kw ) {
		$kw = strtolower( trim( (string) $kw ) );
		if ( '' !== $kw && false !== strpos( $plain, $kw ) ) {
			return true;
		}
	}

	/* Toàn tiếng Anh dài + không dấu tiếng Việt trên site du lịch VN → nghi spam. */
	$has_vi = (bool) preg_match( '/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/iu', $content );
	$len    = function_exists( 'mb_strlen' ) ? mb_strlen( wp_strip_all_tags( $content ), 'UTF-8' ) : strlen( wp_strip_all_tags( $content ) );
	if ( ! $has_vi && $len >= 80 && preg_match( '/\b(the|and|you|your|website|app|game|money|view)\b/i', $content ) ) {
		return true;
	}

	return false;
}

/**
 * Comment spam → đánh dấu spam (không publish).
 *
 * @param int|string|float    $approved    Approved.
 * @param array<string,mixed> $commentdata Data.
 * @return int|string|float
 */
function annam_security_comment_spam_gate( $approved, $commentdata ) {
	if ( 'spam' === $approved || 'trash' === $approved ) {
		return $approved;
	}
	if ( ! is_array( $commentdata ) ) {
		return $approved;
	}
	if ( annam_security_comment_looks_like_spam( $commentdata ) ) {
		return 'spam';
	}
	/* Mọi comment bài viết: chờ duyệt (review Woo không vào đây thường xuyên). */
	$type = isset( $commentdata['comment_type'] ) ? (string) $commentdata['comment_type'] : '';
	if ( 'review' !== $type && apply_filters( 'annam_security_force_comment_moderation', true ) ) {
		return 0;
	}
	return $approved;
}
add_filter( 'pre_comment_approved', 'annam_security_comment_spam_gate', 9, 2 );

/**
 * Rate limit bình luận theo IP.
 *
 * @param array<string,mixed> $commentdata Data.
 * @return array<string,mixed>
 */
function annam_security_comment_rate_limit( $commentdata ) {
	$type = isset( $commentdata['comment_type'] ) ? (string) $commentdata['comment_type'] : '';
	if ( 'review' === $type ) {
		return $commentdata;
	}
	if ( ! function_exists( 'annam_check_rate_limit' ) ) {
		return $commentdata;
	}
	$max = (int) apply_filters( 'annam_security_comment_rate_max', 3 );
	$win = (int) apply_filters( 'annam_security_comment_rate_minutes', 30 );
	if ( $max < 1 || $win < 1 ) {
		return $commentdata;
	}
	if ( ! annam_check_rate_limit( 'annam_blog_comment', $max, $win ) ) {
		wp_die(
			esc_html__( 'Bạn gửi bình luận quá nhanh. Vui lòng thử lại sau.', 'generatepress_child' ),
			esc_html__( 'Bị giới hạn', 'generatepress_child' ),
			array( 'response' => 429 )
		);
	}
	annam_rate_limit_increment( 'annam_blog_comment', $win );
	return $commentdata;
}
add_filter( 'preprocess_comment', 'annam_security_comment_rate_limit', 8 );

/**
 * Menu: dọn bình luận spam cũ.
 */
function annam_security_comment_spam_menu() {
	add_submenu_page(
		'annam-settings',
		__( 'Dọn comment spam', 'generatepress_child' ),
		__( 'Dọn comment spam', 'generatepress_child' ),
		'moderate_comments',
		'annam-comment-spam-cleanup',
		'annam_security_comment_spam_render_page'
	);
}
add_action( 'admin_menu', 'annam_security_comment_spam_menu', 40 );

/**
 * @return int Số comment đã đánh spam.
 */
function annam_security_purge_suspicious_comments() {
	$marked = 0;
	$page   = 1;
	do {
		$query = new WP_Comment_Query(
			array(
				'status'                       => 'all',
				'type__not_in'                 => array( 'review', 'order_note', 'webhook_delivery' ),
				'number'                       => 100,
				'paged'                        => $page,
				'orderby'                      => 'comment_ID',
				'order'                        => 'DESC',
				'no_found_rows'                => true,
				'update_comment_meta_cache'    => false,
			)
		);
		$comments = $query->comments;
		if ( empty( $comments ) ) {
			break;
		}
		foreach ( $comments as $c ) {
			if ( ! $c instanceof WP_Comment ) {
				continue;
			}
			if ( in_array( $c->comment_approved, array( 'spam', 'trash' ), true ) ) {
				continue;
			}
			$data = array(
				'comment_content'      => $c->comment_content,
				'comment_author'       => $c->comment_author,
				'comment_author_email' => $c->comment_author_email,
				'comment_author_url'   => $c->comment_author_url,
				'comment_type'         => $c->comment_type ? $c->comment_type : 'comment',
			);
			if ( annam_security_comment_looks_like_spam( $data ) ) {
				wp_spam_comment( (int) $c->comment_ID );
				++$marked;
			}
		}
		++$page;
		if ( $page > 50 ) {
			break;
		}
	} while ( ! empty( $comments ) );

	return $marked;
}

/**
 * @return int
 */
function annam_security_empty_spam_comments() {
	$deleted = 0;
	$spam    = get_comments(
		array(
			'status' => 'spam',
			'number' => 500,
			'fields' => 'ids',
		)
	);
	if ( ! is_array( $spam ) ) {
		return 0;
	}
	foreach ( $spam as $cid ) {
		if ( wp_delete_comment( (int) $cid, true ) ) {
			++$deleted;
		}
	}
	return $deleted;
}

/**
 * Admin page.
 */
function annam_security_comment_spam_render_page() {
	if ( ! current_user_can( 'moderate_comments' ) ) {
		return;
	}

	$notice = '';
	if ( isset( $_POST['annam_spam_action'] ) && check_admin_referer( 'annam_comment_spam_cleanup' ) ) {
		$action = sanitize_key( wp_unslash( (string) $_POST['annam_spam_action'] ) );
		if ( 'mark' === $action ) {
			$n      = annam_security_purge_suspicious_comments();
			$notice = sprintf(
				/* translators: %d: count */
				__( 'Đã đánh dấu spam %d bình luận nghi ngờ.', 'generatepress_child' ),
				$n
			);
		} elseif ( 'empty' === $action ) {
			$n      = annam_security_empty_spam_comments();
			$notice = sprintf(
				/* translators: %d: count */
				__( 'Đã xóa vĩnh viễn %d bình luận trong thùng spam.', 'generatepress_child' ),
				$n
			);
		}
	}

	$hold  = (int) get_comments( array( 'status' => 'hold', 'count' => true ) );
	$spam  = (int) get_comments( array( 'status' => 'spam', 'count' => true ) );
	$appr  = (int) get_comments( array( 'status' => 'approve', 'count' => true, 'type' => 'comment' ) );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Dọn comment spam', 'generatepress_child' ); ?></h1>
		<?php if ( $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
		<?php endif; ?>
		<p><?php esc_html_e( 'Theme tự đánh spam comment có link / từ khóa cờ bạc-SEO, đóng comment bài cũ >45 ngày, bắt buộc duyệt tay. Dùng nút dưới để dọn hàng loạt comment đã có.', 'generatepress_child' ); ?></p>
		<ul>
			<li><?php echo esc_html( sprintf( /* translators: %d */ __( 'Đang chờ duyệt: %d', 'generatepress_child' ), $hold ) ); ?></li>
			<li><?php echo esc_html( sprintf( /* translators: %d */ __( 'Đã đánh spam: %d', 'generatepress_child' ), $spam ) ); ?></li>
			<li><?php echo esc_html( sprintf( /* translators: %d */ __( 'Comment đã duyệt (không gồm review): %d', 'generatepress_child' ), $appr ) ); ?></li>
		</ul>
		<form method="post" action="">
			<?php wp_nonce_field( 'annam_comment_spam_cleanup' ); ?>
			<p>
				<button type="submit" class="button button-primary" name="annam_spam_action" value="mark" onclick="return confirm('Đánh spam mọi comment nghi ngờ (có link, casino, tiếng Anh dài…)?');">
					<?php esc_html_e( '1. Đánh dấu spam comment nghi ngờ', 'generatepress_child' ); ?>
				</button>
			</p>
			<p>
				<button type="submit" class="button" name="annam_spam_action" value="empty" onclick="return confirm('Xóa vĩnh viễn toàn bộ comment đang ở mục Spam?');">
					<?php esc_html_e( '2. Xóa vĩnh viễn mục Spam', 'generatepress_child' ); ?>
				</button>
			</p>
		</form>
		<p><a href="<?php echo esc_url( admin_url( 'edit-comments.php' ) ); ?>"><?php esc_html_e( 'Mở danh sách bình luận', 'generatepress_child' ); ?></a></p>
	</div>
	<?php
}

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
