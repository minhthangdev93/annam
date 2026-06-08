<?php
/**
 * Cài đặt trang Giới thiệu (option annam_about_settings) + helper đọc dữ liệu.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ANNAM_ABOUT_SETTINGS_OPTION' ) ) {
	define( 'ANNAM_ABOUT_SETTINGS_OPTION', 'annam_about_settings' );
}

/**
 * Lấy một key trong option cài đặt Giới thiệu.
 *
 * @param string $key     Key (vd: about_who_we_are_image).
 * @param mixed  $default Giá trị mặc định nếu không có.
 * @return mixed
 */
function annam_get_about_setting( $key, $default = '' ) {
	$key = is_string( $key ) ? $key : '';
	if ( '' === $key ) {
		return $default;
	}

	$opt = get_option( ANNAM_ABOUT_SETTINGS_OPTION, array() );
	if ( ! is_array( $opt ) || ! array_key_exists( $key, $opt ) ) {
		return $default;
	}

	return $opt[ $key ];
}

/**
 * URL ảnh từ attachment ID lưu trong cài đặt Giới thiệu.
 *
 * @param string $key    Key (vd: about_who_we_are_image).
 * @param string $size   Size đăng ký trong WordPress.
 * @return string URL hoặc chuỗi rỗng nếu không hợp lệ.
 */
function annam_get_about_image_url( $key, $size = 'full' ) {
	$id = absint( annam_get_about_setting( $key, 0 ) );
	if ( $id <= 0 || ! wp_attachment_is_image( $id ) ) {
		return '';
	}

	$size = is_string( $size ) && '' !== $size ? $size : 'full';
	$url  = wp_get_attachment_image_url( $id, $size );

	return $url ? esc_url_raw( $url ) : '';
}

/**
 * Danh sách URL ảnh gallery (theo thứ tự đã lưu).
 *
 * @param string $size Size đăng ký.
 * @return string[]
 */
function annam_get_about_gallery_images( $size = 'full' ) {
	$raw = annam_get_about_setting( 'about_gallery_images', array() );
	if ( ! is_array( $raw ) ) {
		return array();
	}

	$size = is_string( $size ) && '' !== $size ? $size : 'full';
	$out  = array();

	foreach ( array_map( 'absint', $raw ) as $id ) {
		if ( $id <= 0 || ! wp_attachment_is_image( $id ) ) {
			continue;
		}
		$url = wp_get_attachment_image_url( $id, $size );
		if ( $url ) {
			$out[] = esc_url_raw( $url );
		}
	}

	return $out;
}
