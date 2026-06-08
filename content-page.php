<?php
/**
 * Page content template — trang chủ tĩnh: mô tả dài có Xem thêm/Thu gọn (dùng chung block với danh mục).
 * Các trang khác: dùng template của theme cha.
 *
 * @package GeneratePress_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'annam_is_page_template_trang_chu' ) && annam_is_page_template_trang_chu() ) {
	get_template_part( 'template-parts/home/home', 'trang-chu-content-page' );
} else {
	require get_template_directory() . '/content-page.php';
}
