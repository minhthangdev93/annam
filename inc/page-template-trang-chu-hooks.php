<?php
/**
 * Hooks cho template trang chủ tĩnh (GeneratePress).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trang dùng template này không cần ảnh nổi bật sau header (đã có hero tùy chỉnh).
 */
add_action(
	'template_redirect',
	static function () {
		if ( ! is_singular( 'page' ) ) {
			return;
		}
		$page_id = (int) get_queried_object_id();
		if ( ! $page_id || 'page-template-trang-chu.php' !== get_page_template_slug( $page_id ) ) {
			return;
		}
		remove_action( 'generate_after_header', 'generate_featured_page_header', 10 );
	},
	1
);
