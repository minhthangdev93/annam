<?php
/**
 * Hệ sinh thái — option annam_ecosystem_items + helper đọc cho frontend.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ANNAM_ECOSYSTEM_ITEMS_OPTION' ) ) {
	define( 'ANNAM_ECOSYSTEM_ITEMS_OPTION', 'annam_ecosystem_items' );
}

/**
 * Danh sách thương hiệu bật hiển thị, có logo hợp lệ, sắp xếp theo order.
 *
 * Mỗi phần tử: name, logo_id, logo_url, url (link website, có thể rỗng).
 *
 * @return array<int, array{name:string,logo_id:int,logo_url:string,url:string}>
 */
function annam_get_ecosystem_items() {
	$raw = get_option( ANNAM_ECOSYSTEM_ITEMS_OPTION, array() );
	if ( ! is_array( $raw ) ) {
		return array();
	}

	$out = array();

	foreach ( $raw as $row ) {
		if ( ! is_array( $row ) || empty( $row['enabled'] ) ) {
			continue;
		}

		$logo_id = isset( $row['logo_id'] ) ? absint( $row['logo_id'] ) : 0;
		if ( $logo_id <= 0 || ! wp_attachment_is_image( $logo_id ) ) {
			continue;
		}

		$logo_url = wp_get_attachment_image_url( $logo_id, 'medium' );
		if ( ! $logo_url ) {
			continue;
		}

		$name = isset( $row['name'] ) ? sanitize_text_field( (string) $row['name'] ) : '';
		if ( '' === $name ) {
			continue;
		}

		$link = isset( $row['url'] ) ? esc_url_raw( trim( (string) $row['url'] ) ) : '';

		$out[] = array(
			'name'     => $name,
			'logo_id'  => $logo_id,
			'logo_url' => esc_url_raw( $logo_url ),
			'url'      => $link,
			'order'    => isset( $row['order'] ) ? absint( $row['order'] ) : 0,
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
