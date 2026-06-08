<?php
/**
 * Custom product review summary + modal submission (WooCommerce native reviews).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nonce action for a product.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function annam_review_nonce_action( $product_id ) {
	return 'annam_submit_review_' . (int) $product_id;
}

/**
 * Validate phone characters (digits, space, +, -, parentheses).
 *
 * @param string $phone Raw phone.
 * @return bool
 */
function annam_review_phone_valid_chars( $phone ) {
	$phone = is_string( $phone ) ? trim( $phone ) : '';
	if ( '' === $phone ) {
		return false;
	}
	return (bool) preg_match( '/^[\d\s+\-().]+$/u', $phone );
}

/**
 * Placeholder email for WordPress comment row (not shown on frontend).
 *
 * @return string
 */
function annam_review_placeholder_email() {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	$host  = is_string( $host ) && '' !== $host ? preg_replace( '/[^\w.-]+/', '', $host ) : 'site.local';
	$hash  = substr( md5( (string) microtime( true ) . wp_rand() ), 0, 14 );
	return 'review+' . $hash . '@noreply.' . $host;
}

/**
 * Allowed image mimes for review uploads.
 *
 * @return array<string, string>
 */
function annam_review_allowed_image_mimes() {
	return array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'webp'         => 'image/webp',
	);
}

/**
 * First administrator user ID for attachment author (guest uploads).
 *
 * @return int
 */
function annam_review_attachment_author_id() {
	$uid = get_current_user_id();
	if ( $uid > 0 ) {
		return $uid;
	}
	$admins = get_users(
		array(
			'role'   => 'administrator',
			'number' => 1,
			'fields' => array( 'ID' ),
		)
	);
	if ( ! empty( $admins[0]->ID ) ) {
		return (int) $admins[0]->ID;
	}
	return 1;
}

/**
 * Process uploaded review images; returns attachment IDs or WP_Error.
 *
 * @param int   $product_id Product ID (attachment parent).
 * @param array $files      $_FILES['review_images'] style multi array.
 * @return array<int>|WP_Error
 */
function annam_review_handle_uploads( $product_id, $files ) {
	$ids = array();

	if ( empty( $files['name'] ) || ! is_array( $files['name'] ) ) {
		return $ids;
	}

	$indices = array();
	foreach ( $files['name'] as $i => $n ) {
		if ( is_string( $n ) && '' !== trim( $n ) ) {
			$indices[] = (int) $i;
		}
	}

	if ( count( $indices ) > 3 ) {
		return new WP_Error( 'too_many', __( 'Tối đa 3 ảnh.', 'generatepress_child' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$GLOBALS['annam_review_allow_upload'] = true;
	$author_id                           = annam_review_attachment_author_id();

	foreach ( $indices as $i ) {
		if ( ! isset( $files['tmp_name'][ $i ], $files['error'][ $i ] ) || UPLOAD_ERR_OK !== (int) $files['error'][ $i ] ) {
			continue;
		}

		$tmp = $files['tmp_name'][ $i ];
		if ( ! is_string( $tmp ) || ! is_uploaded_file( $tmp ) ) {
			continue;
		}

		$file_array = array(
			'name'     => $files['name'][ $i ],
			'type'     => isset( $files['type'][ $i ] ) ? $files['type'][ $i ] : '',
			'tmp_name' => $tmp,
			'error'    => (int) $files['error'][ $i ],
			'size'     => isset( $files['size'][ $i ] ) ? (int) $files['size'][ $i ] : 0,
		);

		if ( $file_array['size'] > 3 * MB_IN_BYTES ) {
			$GLOBALS['annam_review_allow_upload'] = false;
			return new WP_Error( 'file_size', __( 'Mỗi ảnh tối đa 3MB.', 'generatepress_child' ) );
		}

		$overrides = array(
			'test_form' => false,
			'mimes'     => annam_review_allowed_image_mimes(),
		);

		$upload = wp_handle_upload( $file_array, $overrides );

		if ( isset( $upload['error'] ) ) {
			$GLOBALS['annam_review_allow_upload'] = false;
			return new WP_Error( 'upload', $upload['error'] );
		}

		$wp_filetype = wp_check_filetype_and_ext( $upload['file'], $upload['file'], annam_review_allowed_image_mimes() );
		if ( empty( $wp_filetype['type'] ) || empty( $wp_filetype['ext'] ) ) {
			if ( is_string( $upload['file'] ) && file_exists( $upload['file'] ) ) {
				unlink( $upload['file'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
			}
			$GLOBALS['annam_review_allow_upload'] = false;
			return new WP_Error( 'type', __( 'Định dạng ảnh không hợp lệ.', 'generatepress_child' ) );
		}

		$allowed_types = array( 'image/jpeg', 'image/png', 'image/webp' );
		if ( ! in_array( $wp_filetype['type'], $allowed_types, true ) ) {
			if ( is_string( $upload['file'] ) && file_exists( $upload['file'] ) ) {
				unlink( $upload['file'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
			}
			$GLOBALS['annam_review_allow_upload'] = false;
			return new WP_Error( 'mime', __( 'Chỉ chấp nhận JPEG, PNG hoặc WebP.', 'generatepress_child' ) );
		}

		$attach_id = wp_insert_attachment(
			array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name( wp_basename( (string) $files['name'][ $i ] ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
				'post_parent'    => (int) $product_id,
				'post_author'    => $author_id,
			),
			$upload['file']
		);

		if ( ! $attach_id || is_wp_error( $attach_id ) ) {
			if ( is_string( $upload['file'] ) && file_exists( $upload['file'] ) ) {
				unlink( $upload['file'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
			}
			$GLOBALS['annam_review_allow_upload'] = false;
			return new WP_Error( 'attach', __( 'Không lưu được ảnh.', 'generatepress_child' ) );
		}

		$meta = wp_generate_attachment_metadata( (int) $attach_id, $upload['file'] );
		wp_update_attachment_metadata( (int) $attach_id, $meta );

		$ids[] = (int) $attach_id;
	}

	$GLOBALS['annam_review_allow_upload'] = false;

	return $ids;
}

/**
 * Grant upload_files only while annam_review_handle_uploads runs.
 *
 * @param array $allcaps All caps.
 * @return array
 */
function annam_review_filter_user_has_cap_upload( $allcaps ) {
	if ( ! empty( $GLOBALS['annam_review_allow_upload'] ) ) {
		$allcaps['upload_files'] = true;
	}
	return $allcaps;
}
add_filter( 'user_has_cap', 'annam_review_filter_user_has_cap_upload', 999 );

/**
 * AJAX: submit product review.
 */
function annam_ajax_submit_product_review() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		wp_send_json_error( array( 'message' => __( 'WooCommerce không khả dụng.', 'generatepress_child' ) ), 400 );
	}

	check_ajax_referer( 'annam_review_ajax', 'security' );

	$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Sản phẩm không hợp lệ.', 'generatepress_child' ) ), 400 );
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ?? '' ) ), annam_review_nonce_action( $product_id ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		wp_send_json_error( array( 'message' => __( 'Phiên làm việc hết hạn, vui lòng tải lại trang.', 'generatepress_child' ) ), 403 );
	}

	$form_ts = isset( $_POST['annam_review_form_ts'] ) ? absint( wp_unslash( $_POST['annam_review_form_ts'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( ! $form_ts || ( time() - $form_ts ) < 3 || ( time() - $form_ts ) > 7200 ) {
		wp_send_json_error( array( 'message' => __( 'Thông tin chưa hợp lệ, vui lòng kiểm tra lại.', 'generatepress_child' ) ), 400 );
	}

	if ( ! empty( $_POST['annam_review_company'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		wp_send_json_error( array( 'message' => __( 'Thông tin chưa hợp lệ, vui lòng kiểm tra lại.', 'generatepress_child' ) ), 400 );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product instanceof WC_Product ) {
		wp_send_json_error( array( 'message' => __( 'Sản phẩm không tồn tại.', 'generatepress_child' ) ), 404 );
	}

	if ( function_exists( 'wc_review_ratings_enabled' ) && ! wc_review_ratings_enabled() ) {
		wp_send_json_error( array( 'message' => __( 'Đánh giá theo sao đang tắt.', 'generatepress_child' ) ), 400 );
	}

	if ( ! comments_open( $product_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Đánh giá đã đóng cho sản phẩm này.', 'generatepress_child' ) ), 400 );
	}

	if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'yes' && ! wc_customer_bought_product( '', get_current_user_id(), $product_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Chỉ khách đã mua sản phẩm mới được đánh giá.', 'generatepress_child' ) ), 403 );
	}

	$rating = isset( $_POST['rating'] ) ? absint( wp_unslash( $_POST['rating'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( $rating < 1 || $rating > 5 ) {
		wp_send_json_error( array( 'message' => __( 'Vui lòng chọn số sao từ 1 đến 5.', 'generatepress_child' ) ), 400 );
	}

	$name = isset( $_POST['author'] ) ? sanitize_text_field( wp_unslash( $_POST['author'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$name = wp_strip_all_tags( $name );
	if ( function_exists( 'mb_substr' ) ) {
		$name = mb_substr( $name, 0, 100, 'UTF-8' );
	} else {
		$name = substr( $name, 0, 100 );
	}
	if ( '' === $name ) {
		wp_send_json_error( array( 'message' => __( 'Thông tin chưa hợp lệ, vui lòng kiểm tra lại.', 'generatepress_child' ) ), 400 );
	}

	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( '' === $phone || ! annam_review_phone_valid_chars( $phone ) ) {
		wp_send_json_error( array( 'message' => __( 'Thông tin chưa hợp lệ, vui lòng kiểm tra lại.', 'generatepress_child' ) ), 400 );
	}
	if ( function_exists( 'mb_substr' ) ) {
		$phone = mb_substr( $phone, 0, 25, 'UTF-8' );
	} else {
		$phone = substr( $phone, 0, 25 );
	}

	$comment_raw = isset( $_POST['comment'] ) ? wp_unslash( $_POST['comment'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$comment_raw = is_string( $comment_raw ) ? $comment_raw : '';
	$comment     = wp_kses_post( trim( $comment_raw ) );
	if ( function_exists( 'mb_substr' ) ) {
		$comment = mb_substr( $comment, 0, 1000, 'UTF-8' );
	} else {
		$comment = substr( $comment, 0, 1000 );
	}

	$email = annam_review_placeholder_email();

	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	if ( function_exists( 'annam_check_rate_limit' ) ) {
		if ( ! annam_check_rate_limit( 'annam_product_review', 3, 10 ) ) {
			wp_send_json_error( array( 'message' => __( 'Bạn đã gửi quá nhiều đánh giá, vui lòng thử lại sau.', 'generatepress_child' ) ), 429 );
		}
		if ( ! annam_check_rate_limit( 'annam_review_prod_' . $product_id, 4, 10 ) ) {
			wp_send_json_error( array( 'message' => __( 'Bạn đã gửi quá nhiều đánh giá cho tour này, vui lòng thử lại sau.', 'generatepress_child' ) ), 429 );
		}
		$phone_digits = preg_replace( '/\D+/', '', $phone );
		if ( strlen( $phone_digits ) >= 8 ) {
			$ph_key = 'annam_rev_ph_' . md5( $phone_digits );
			if ( ! annam_check_rate_limit( $ph_key, 3, 10 ) ) {
				wp_send_json_error( array( 'message' => __( 'Bạn đã gửi quá nhiều đánh giá, vui lòng thử lại sau.', 'generatepress_child' ) ), 429 );
			}
		}
	}

	$attachment_ids = array();
	if ( ! empty( $_FILES['review_images'] ) && is_array( $_FILES['review_images'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$uploaded = annam_review_handle_uploads( $product_id, $_FILES['review_images'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_wp_error( $uploaded ) ) {
			wp_send_json_error( array( 'message' => $uploaded->get_error_message() ), 400 );
		}
		$attachment_ids = $uploaded;
	}

	$commentdata = array(
		'comment_post_ID'      => $product_id,
		'comment_author'       => $name,
		'comment_author_email' => $email,
		'comment_author_url'   => '',
		'comment_content'      => $comment,
		'comment_type'         => 'review',
		'comment_parent'       => 0,
		'user_id'              => get_current_user_id(),
		'comment_author_IP'    => $ip,
		'comment_agent'        => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
		'comment_date'         => current_time( 'mysql' ),
		'comment_date_gmt'     => current_time( 'mysql', 1 ),
		'comment_approved'     => 0,
	);

	$approved_result = wp_allow_comment( $commentdata, true );
	if ( is_wp_error( $approved_result ) ) {
		foreach ( $attachment_ids as $aid ) {
			wp_delete_attachment( $aid, true );
		}
		$edata  = $approved_result->get_error_data();
		$status = ( is_int( $edata ) && $edata >= 400 && $edata < 600 ) ? $edata : 400;
		wp_send_json_error(
			array(
				'message' => __( 'Thông tin chưa hợp lệ, vui lòng kiểm tra lại.', 'generatepress_child' ),
			),
			$status
		);
	}

	$commentdata['comment_approved'] = $approved_result;

	if ( in_array( $commentdata['comment_approved'], array( 'spam', 'trash' ), true ) ) {
		foreach ( $attachment_ids as $aid ) {
			wp_delete_attachment( $aid, true );
		}
		wp_send_json_error( array( 'message' => __( 'Đánh giá không được chấp nhận.', 'generatepress_child' ) ), 400 );
	}

	$comment_id = wp_insert_comment( $commentdata );

	if ( ! $comment_id || is_wp_error( $comment_id ) ) {
		foreach ( $attachment_ids as $aid ) {
			wp_delete_attachment( $aid, true );
		}
		wp_send_json_error( array( 'message' => __( 'Không lưu được đánh giá, vui lòng thử lại.', 'generatepress_child' ) ), 500 );
	}

	add_comment_meta( $comment_id, 'rating', $rating, true );
	add_comment_meta( $comment_id, '_review_phone', $phone, true );
	if ( ! empty( $attachment_ids ) ) {
		add_comment_meta( $comment_id, '_review_images', wp_json_encode( array_values( array_map( 'absint', $attachment_ids ) ) ), true );
	}

	if ( class_exists( 'WC_Comments', false ) ) {
		WC_Comments::clear_transients( $product_id );
	}

	if ( function_exists( 'annam_rate_limit_increment' ) ) {
		annam_rate_limit_increment( 'annam_product_review', 10 );
		annam_rate_limit_increment( 'annam_review_prod_' . $product_id, 10 );
		$phone_digits = preg_replace( '/\D+/', '', $phone );
		if ( strlen( $phone_digits ) >= 8 ) {
			annam_rate_limit_increment( 'annam_rev_ph_' . md5( $phone_digits ), 10 );
		}
	}

	wp_send_json_success(
		array(
			'message' => __( 'Cảm ơn bạn đã gửi đánh giá.', 'generatepress_child' ),
		)
	);
}
add_action( 'wp_ajax_annam_submit_product_review', 'annam_ajax_submit_product_review' );
add_action( 'wp_ajax_nopriv_annam_submit_product_review', 'annam_ajax_submit_product_review' );

/**
 * Enqueue review UI assets on single product.
 */
function annam_product_reviews_enqueue_assets() {
	if ( ! function_exists( 'is_product' ) || ! is_product() || ! comments_open() ) {
		return;
	}

	$pid = get_queried_object_id();
	if ( ! $pid ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$css = $dir . '/assets/css/woo-product-reviews.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-product-reviews',
			$uri . '/assets/css/woo-product-reviews.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $css )
		);
	}

	$js = $dir . '/assets/js/woo-product-reviews.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-product-reviews',
			$uri . '/assets/js/woo-product-reviews.js',
			array(),
			(string) filemtime( $js ),
			true
		);
		wp_localize_script(
			'annam-product-reviews',
			'annamProductReviews',
			array(
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'annam_review_ajax' ),
				'reviewNonce' => wp_create_nonce( annam_review_nonce_action( $pid ) ),
				'productId'   => (int) $pid,
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_product_reviews_enqueue_assets', 35 );

/**
 * Output review photos stored in comment meta _review_images (attachment IDs, JSON).
 *
 * @param WP_Comment $comment Comment.
 * @return void
 */
function annam_review_display_attached_images( $comment ) {
	if ( ! $comment instanceof WP_Comment || 'review' !== $comment->comment_type ) {
		return;
	}

	$raw = get_comment_meta( $comment->comment_ID, '_review_images', true );
	if ( ! is_string( $raw ) || '' === $raw ) {
		return;
	}

	$ids = json_decode( $raw, true );
	if ( ! is_array( $ids ) ) {
		return;
	}

	$ids = array_values(
		array_unique(
			array_filter(
				array_map( 'absint', $ids ),
				static function ( $id ) {
					return $id > 0;
				}
			)
		)
	);

	if ( empty( $ids ) ) {
		return;
	}

	$items = array();
	foreach ( $ids as $aid ) {
		if ( ! wp_attachment_is_image( $aid ) ) {
			continue;
		}
		$mime = get_post_mime_type( $aid );
		if ( ! is_string( $mime ) || 0 !== strpos( $mime, 'image/' ) ) {
			continue;
		}
		$items[] = $aid;
	}

	if ( empty( $items ) ) {
		return;
	}

	echo '<div class="annam-review-images" data-annam-review-gallery="' . esc_attr( (string) (int) $comment->comment_ID ) . '" role="group" aria-label="' . esc_attr__( 'Ảnh đính kèm đánh giá', 'generatepress_child' ) . '">';

	$idx = 0;
	foreach ( $items as $aid ) {
		$full = wp_get_attachment_image_src( $aid, 'full' );
		if ( empty( $full[0] ) ) {
			continue;
		}
		$full_url = $full[0];

		$alt = '';
		$post = get_post( $aid );
		if ( $post instanceof WP_Post && '' !== $post->post_excerpt ) {
			$alt = $post->post_excerpt;
		}
		$alt_text = $alt ? wp_strip_all_tags( $alt ) : esc_attr__( 'Ảnh đánh giá', 'generatepress_child' );

		echo '<figure class="annam-review-images__item">';
		echo '<button type="button" class="annam-review-images__thumb" data-full="' . esc_url( $full_url ) . '" data-index="' . esc_attr( (string) $idx ) . '" aria-label="' . esc_attr__( 'Xem ảnh lớn', 'generatepress_child' ) . '">';
		echo wp_get_attachment_image(
			$aid,
			'woocommerce_thumbnail',
			false,
			array(
				'class'    => 'annam-review-images__img',
				'loading'  => 'lazy',
				'decoding' => 'async',
				'alt'      => $alt_text,
			)
		);
		echo '</button>';
		echo '</figure>';
		++$idx;
	}
	echo '</div>';
}
add_action( 'woocommerce_review_after_comment_text', 'annam_review_display_attached_images', 15 );
