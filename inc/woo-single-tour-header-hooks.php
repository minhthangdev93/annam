<?php
/**
 * Single product tour: header (breadcrumb, title, gallery) only.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Attachment alt or product title fallback.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $fallback      Product title.
 * @return string
 */
function annam_single_tour_img_alt( $attachment_id, $fallback ) {
	$alt = get_post_meta( (int) $attachment_id, '_wp_attachment_image_alt', true );
	$alt = is_string( $alt ) ? trim( wp_strip_all_tags( $alt ) ) : '';
	return '' !== $alt ? $alt : $fallback;
}

/**
 * Full-size image URL for lightbox data-full.
 *
 * @param int $attachment_id Attachment ID.
 * @return string
 */
function annam_single_tour_img_full_url( $attachment_id ) {
	$id = (int) $attachment_id;
	if ( $id <= 0 ) {
		return '';
	}
	$url = wp_get_attachment_image_url( $id, 'full' );
	if ( ! $url ) {
		$url = wp_get_attachment_url( $id );
	}
	return is_string( $url ) ? $url : '';
}

/**
 * Attributes for gallery <img> including data-full for lightbox JS.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $title         Product title for alt fallback.
 * @param string $loading       eager|lazy.
 * @param string $size          Registered image size (for width/height hints).
 * @return array<string, string>
 */
function annam_single_tour_gallery_img_attrs( $attachment_id, $title, $loading = 'lazy', $size = '' ) {
	if ( '' === $size && function_exists( 'annam_get_tour_gallery_image_size' ) ) {
		$size = annam_get_tour_gallery_image_size();
	}
	if ( '' === $size ) {
		$size = 'woocommerce_single';
	}
	$id   = (int) $attachment_id;
	$full = annam_single_tour_img_full_url( $id );

	$attrs = array(
		'class'      => 'annam-tour-gallery__img',
		'loading'    => 'eager' === $loading ? 'eager' : 'lazy',
		'decoding'   => 'eager' === $loading ? 'sync' : 'async',
		'alt'        => annam_single_tour_img_alt( $id, $title ),
		'data-full'  => esc_url( $full ),
	);

	if ( 'eager' === $loading ) {
		$attrs['fetchpriority'] = 'high';
	}

	$dims = wp_get_attachment_image_src( $id, $size );
	if ( is_array( $dims ) && isset( $dims[1], $dims[2] ) && (int) $dims[1] > 0 && (int) $dims[2] > 0 ) {
		$attrs['width']  = (string) (int) $dims[1];
		$attrs['height'] = (string) (int) $dims[2];
	}

	return $attrs;
}

/**
 * Enqueue header block assets on single product.
 */
function annam_enqueue_single_tour_header_assets() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$css = $dir . '/assets/css/woo-single-tour-header.css';

	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-single-tour-header',
			$uri . '/assets/css/woo-single-tour-header.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $css )
		);
	}

	$js = $dir . '/assets/js/woo-single-tour-lightbox.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-single-tour-lightbox',
			$uri . '/assets/js/woo-single-tour-lightbox.js',
			array(),
			(string) filemtime( $js ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_enqueue_single_tour_header_assets', 25 );

/**
 * Replace default product title + gallery with custom tour header.
 */
function annam_single_tour_header_remove_defaults() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
}
add_action( 'wp', 'annam_single_tour_header_remove_defaults', 19 );

/**
 * Breadcrumb only in tour header (avoid duplicate under header).
 */
function annam_remove_wc_breadcrumb_on_single_product() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
}
add_action( 'wp', 'annam_remove_wc_breadcrumb_on_single_product', 9 );

/**
 * Output tour header (breadcrumb, H1, gallery).
 */
function annam_single_tour_header_render() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	get_template_part( 'template-parts/woocommerce/single-tour/tour', 'header' );
}
add_action( 'woocommerce_before_single_product_summary', 'annam_single_tour_header_render', 5 );
