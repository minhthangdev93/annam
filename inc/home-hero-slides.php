<?php
/**
 * Hero trang chủ: cùng logic URL slide cho template + preload LCP.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Option key đồng bộ với inc/annam-settings.php (ANNAM_HOME_SLIDERS_OPTION).
 *
 * @var string
 */
function annam_home_sliders_option_name() {
	return defined( 'ANNAM_HOME_SLIDERS_OPTION' ) ? ANNAM_HOME_SLIDERS_OPTION : 'annam_home_sliders';
}

/**
 * Slider trang chủ từ cài đặt admin: chỉ slide bật, có ảnh desktop hợp lệ, sắp xếp theo thứ tự.
 *
 * Mỗi phần tử gồm: desktop_id, mobile_id, desktop_src, mobile_src, alt, title, description, button_text, button_url, order.
 *
 * @return array<int, array<string, int|string>>
 */
function annam_get_home_sliders() {
	$raw = get_option( annam_home_sliders_option_name(), array() );
	if ( ! is_array( $raw ) ) {
		return array();
	}

	$default_alt = apply_filters(
		'annam_home_hero_image_alt',
		__( 'Tour miền Bắc — An Nam Discovery', 'generatepress_child' )
	);

	$out = array();

	foreach ( $raw as $row ) {
		if ( ! is_array( $row ) || empty( $row['enabled'] ) ) {
			continue;
		}

		$desktop_id = isset( $row['desktop_id'] ) ? absint( $row['desktop_id'] ) : 0;
		if ( $desktop_id <= 0 || ! wp_attachment_is_image( $desktop_id ) ) {
			continue;
		}

		$mobile_id = isset( $row['mobile_id'] ) ? absint( $row['mobile_id'] ) : 0;
		if ( $mobile_id > 0 && ! wp_attachment_is_image( $mobile_id ) ) {
			$mobile_id = 0;
		}

		$desk_url = wp_get_attachment_image_url( $desktop_id, 'full' );
		if ( ! $desk_url ) {
			continue;
		}

		$mob_url = ( $mobile_id > 0 ) ? wp_get_attachment_image_url( $mobile_id, 'full' ) : '';
		if ( ! $mob_url ) {
			$mob_url = $desk_url;
		}

		$title = isset( $row['title'] ) ? sanitize_text_field( (string) $row['title'] ) : '';
		$alt   = '' !== $title ? $title : $default_alt;

		$out[] = array(
			'desktop_id'  => $desktop_id,
			'mobile_id'   => $mobile_id,
			'desktop_src' => esc_url_raw( $desk_url ),
			'mobile_src'  => esc_url_raw( $mob_url ),
			'alt'         => $alt,
			'title'       => $title,
			'description' => isset( $row['description'] ) ? sanitize_textarea_field( (string) $row['description'] ) : '',
			'button_text' => isset( $row['button_text'] ) ? sanitize_text_field( (string) $row['button_text'] ) : '',
			'button_url'  => isset( $row['button_url'] ) ? esc_url_raw( (string) $row['button_url'] ) : '',
			'order'       => isset( $row['order'] ) ? absint( $row['order'] ) : 0,
		);
	}

	usort(
		$out,
		static function ( $a, $b ) {
			return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 );
		}
	);

	return $out;
}

/**
 * Nếu trong uploads có file .webp cùng tên (cùng thư mục với .jpg/.jpeg), trả về URL WebP — không cần plugin.
 *
 * @param string $image_url URL ảnh JPEG dưới thư mục uploads.
 * @return string URL WebP hoặc chuỗi rỗng.
 */
function annam_home_hero_matching_webp_url( $image_url ) {
	$image_url = is_string( $image_url ) ? trim( $image_url ) : '';
	if ( '' === $image_url || ! preg_match( '/\.(?:jpe?g)$/i', $image_url ) ) {
		return '';
	}

	$upload = wp_upload_dir();
	if ( ! empty( $upload['error'] ) || empty( $upload['baseurl'] ) || empty( $upload['basedir'] ) ) {
		return '';
	}

	$baseurl = untrailingslashit( $upload['baseurl'] );
	$basedir  = wp_normalize_path( untrailingslashit( $upload['basedir'] ) );
	$bare     = strtok( $image_url, '?' );

	$paths_to_try = array( $bare );
	// Một số cấu hình dùng URL khác với baseurl trả về — thử khớp sau /uploads/.
	if ( 0 !== strpos( $bare, $baseurl ) && preg_match( '#/wp-content/uploads/(.+\.(?:jpe?g))$#i', $bare, $m ) ) {
		$paths_to_try[] = $baseurl . '/' . ltrim( $m[1], '/' );
	}

	foreach ( $paths_to_try as $candidate ) {
		if ( 0 !== strpos( $candidate, $baseurl ) ) {
			continue;
		}
		$rel = substr( $candidate, strlen( $baseurl ) );
		$rel = '/' . ltrim( str_replace( '\\', '/', $rel ), '/' );
		$jpeg_fs = wp_normalize_path( $basedir . str_replace( '/', DIRECTORY_SEPARATOR, $rel ) );
		if ( ! file_exists( $jpeg_fs ) ) {
			continue;
		}
		$webp_fs = preg_replace( '/\.(jpe?g)$/i', '.webp', $jpeg_fs );
		if ( $webp_fs && file_exists( $webp_fs ) ) {
			$webp_rel = preg_replace( '/\.(jpe?g)$/i', '.webp', $rel );
			return $baseurl . $webp_rel;
		}
	}

	return '';
}

/**
 * Danh sách slide hero (mỗi phần tử: src, alt), tối thiểu 2 phần tử nếu có ít nhất 1 ảnh.
 *
 * @return array<int, array{src:string, alt:string}>
 */
function annam_home_hero_get_slide_rows() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$hero_alt = apply_filters(
		'annam_home_hero_image_alt',
		__( 'Tour miền Bắc — An Nam Discovery', 'generatepress_child' )
	);

	$upload      = wp_upload_dir();
	$upload_base = '';
	if ( empty( $upload['error'] ) && ! empty( $upload['baseurl'] ) ) {
		$upload_base = untrailingslashit( $upload['baseurl'] );
	} else {
		$upload_base = untrailingslashit( home_url( '/wp-content/uploads' ) );
	}

	$slide_a = apply_filters(
		'annam_home_hero_image_desktop',
		$upload_base . '/2026/05/fsefafawe.jpg'
	);
	$slide_b = apply_filters(
		'annam_home_hero_image_mobile',
		$upload_base . '/2026/05/fsefafawe-Copy.jpg'
	);

	$slides_default = array(
		array(
			'src' => $slide_a,
			'alt' => $hero_alt,
		),
		array(
			'src' => $slide_b,
			'alt' => $hero_alt,
		),
	);

	$slides = apply_filters( 'annam_home_hero_slides', $slides_default );

	$slides_sanitized = array();
	if ( is_array( $slides ) ) {
		foreach ( $slides as $row ) {
			if ( ! is_array( $row ) || empty( $row['src'] ) ) {
				continue;
			}
			$slides_sanitized[] = array(
				'src' => esc_url_raw( $row['src'] ),
				'alt' => isset( $row['alt'] ) ? sanitize_text_field( (string) $row['alt'] ) : $hero_alt,
			);
		}
	}

	if ( count( $slides_sanitized ) < 1 ) {
		$slides_sanitized = array(
			array(
				'src' => esc_url_raw( $slide_a ),
				'alt' => $hero_alt,
			),
			array(
				'src' => esc_url_raw( $slide_b ),
				'alt' => $hero_alt,
			),
		);
	}

	$slides_sanitized = array_values(
		array_filter(
			$slides_sanitized,
			static function ( $s ) {
				return is_array( $s ) && ! empty( $s['src'] );
			}
		)
	);

	if ( count( $slides_sanitized ) === 1 ) {
		$slides_sanitized[] = $slides_sanitized[0];
	}

	return $slides_sanitized;
}
