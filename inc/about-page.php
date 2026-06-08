<?php
/**
 * Trang Giới thiệu (template): enqueue, hero ảnh nổi bật, gallery meta, testimonials filter.
 *
 * Custom Fields (tùy chọn):
 * - `_annam_about_gallery` — ID ảnh đính kèm, cách nhau bằng dấu phẩy hoặc khoảng trắng (tối đa 8).
 *
 * Filter `annam_about_gallery_items` — (items, page_id).
 * Filter `annam_about_testimonials` — mảng các phần tử: `name`, `role`, `quote` (và optional `rating` 1–5).
 * Nếu mảng rỗng, section đánh giá được ẩn.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trang hiện tại có dùng template Giới thiệu không?
 *
 * @return bool
 */
function annam_about_is_about_template() {
	if ( ! is_singular( 'page' ) ) {
		return false;
	}
	$page_id = get_queried_object_id();
	return $page_id && 'page-template-gioi-thieu.php' === get_page_template_slug( $page_id );
}

/**
 * Thông tin thương hiệu / liên hệ (có thể ghi đè qua filter `annam_about_brand`).
 *
 * @return array<string,string>
 */
function annam_about_get_brand() {
	$defaults = array(
		'brand'            => 'An Nam Discovery',
		'company'          => 'CÔNG TY CỔ PHẦN AN NAM DISCOVERY',
		'hotline_display'  => '1900 8164',
		'hotline_tel'      => 'tel:19008164',
		'mobile_display'   => '0942471111',
		'zalo_url'         => 'http://zalo.me/2127942034358673568',
		'whatsapp_url'     => 'https://wa.me/+84942471111',
		'email'            => 'annamdiscoveryvn@gmail.com',
		'license_label'    => __( 'Giấy phép kinh doanh dịch vụ lữ hành quốc tế', 'generatepress_child' ),
		'license_number'   => '01-3006/2025/CDL-GVN-GP LHQT',
		'tax_id'           => '0111205475',
		'office1_title'    => __( 'Văn phòng Hoàn Kiếm', 'generatepress_child' ),
		'office1_address'  => '214 Đ. Trần Quang Khải, Hoàn Kiếm, Hà Nội',
		'office1_maps'     => 'https://maps.app.goo.gl/3DmmAdysHTtu7pPu9',
		'office2_title'    => __( 'Văn phòng Tú Mỡ', 'generatepress_child' ),
		'office2_address'  => '23 Tú Mỡ, Phường Yên Hòa, Tp Hà Nội',
		'office2_maps'     => 'https://maps.app.goo.gl/dLFtc2s8LSYchXQa6',
	);
	return apply_filters( 'annam_about_brand', $defaults );
}

/**
 * URL shop (tour) cho CTA.
 *
 * @return string
 */
function annam_about_get_shop_url() {
	if ( function_exists( 'wc_get_page_id' ) ) {
		$shop_id = (int) wc_get_page_id( 'shop' );
		if ( $shop_id > 0 ) {
			$url = get_permalink( $shop_id );
			if ( $url ) {
				return $url;
			}
		}
	}
	return home_url( '/' );
}

/**
 * URL ảnh nền hero (featured hoặc fallback trong child theme).
 *
 * @param int $page_id ID trang.
 * @return string
 */
function annam_about_hero_background_url( $page_id ) {
	$page_id = (int) $page_id;
	if ( $page_id && has_post_thumbnail( $page_id ) ) {
		$u = get_the_post_thumbnail_url( $page_id, 'full' );
		if ( $u ) {
			return $u;
		}
	}
	if ( function_exists( 'annam_blog_hero_fallback_background_url' ) ) {
		return annam_blog_hero_fallback_background_url();
	}
	return get_stylesheet_directory_uri() . '/assets/images/cam-nang-hero-bg.svg';
}

/**
 * Ảnh minh họa cột phải section “Chúng tôi là ai?” (có thể ghi đè filter `annam_about_intro_side_image`).
 *
 * @return string
 */
function annam_about_intro_side_image_url() {
	$default = get_stylesheet_directory_uri() . '/assets/images/cam-nang-hero-bg.svg';
	if ( function_exists( 'annam_get_about_image_url' ) ) {
		$from = annam_get_about_image_url( 'about_who_we_are_image', 'large' );
		if ( '' !== $from ) {
			return apply_filters( 'annam_about_intro_side_image', $from );
		}
	}
	return apply_filters( 'annam_about_intro_side_image', $default );
}

/**
 * Mục gallery: url hiển thị + url full (lightbox).
 *
 * @param int $page_id ID trang.
 * @return array<int, array{url:string,full:string,alt:string}>
 */
function annam_about_get_gallery_items( $page_id ) {
	$page_id = (int) $page_id;
	$items    = array();

	if ( function_exists( 'annam_get_about_setting' ) ) {
		$setting_ids = annam_get_about_setting( 'about_gallery_images', array() );
		if ( is_array( $setting_ids ) && ! empty( $setting_ids ) ) {
			foreach ( array_map( 'absint', $setting_ids ) as $aid ) {
				if ( ! $aid || ! wp_attachment_is_image( $aid ) ) {
					continue;
				}
				$u    = wp_get_attachment_image_url( $aid, 'large' );
				$full = wp_get_attachment_image_url( $aid, 'full' );
				if ( ! $u ) {
					continue;
				}
				if ( ! $full ) {
					$full = $u;
				}
				$alt       = (string) get_post_meta( $aid, '_wp_attachment_image_alt', true );
				$items[] = array(
					'url'  => $u,
					'full' => $full,
					'alt'  => $alt,
				);
			}
			if ( ! empty( $items ) ) {
				return apply_filters( 'annam_about_gallery_items', $items, $page_id );
			}
		}
	}

	$raw = get_post_meta( $page_id, '_annam_about_gallery', true );
	if ( is_string( $raw ) && $raw !== '' ) {
		$parts = preg_split( '/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY );
		if ( is_array( $parts ) ) {
			foreach ( $parts as $p ) {
				$aid = absint( $p );
				if ( ! $aid || ! wp_attachment_is_image( $aid ) ) {
					continue;
				}
				$u    = wp_get_attachment_image_url( $aid, 'large' );
				$full = wp_get_attachment_image_url( $aid, 'full' );
				if ( ! $u ) {
					continue;
				}
				if ( ! $full ) {
					$full = $u;
				}
				$alt = (string) get_post_meta( $aid, '_wp_attachment_image_alt', true );
				$items[] = array(
					'url'  => $u,
					'full' => $full,
					'alt'  => $alt,
				);
				if ( count( $items ) >= 8 ) {
					break;
				}
			}
		}
	}
	$items = apply_filters( 'annam_about_gallery_items', $items, $page_id );
	$fb    = get_stylesheet_directory_uri() . '/assets/images/cam-nang-hero-bg.svg';
	while ( count( $items ) < 4 ) {
		$items[] = array(
			'url'  => $fb,
			'full' => $fb,
			'alt'  => '',
		);
	}
	return array_slice( $items, 0, 8 );
}

/**
 * @deprecated Dùng annam_about_get_gallery_items.
 *
 * @param int $page_id ID trang.
 * @return string[]
 */
function annam_about_get_gallery_urls( $page_id ) {
	$out = array();
	foreach ( annam_about_get_gallery_items( $page_id ) as $row ) {
		$out[] = $row['url'];
	}
	return $out;
}

/**
 * Testimonials (chỉ hiển thị khi filter / dữ liệu trả về không rỗng).
 *
 * @return array<int, array{name:string,role:string,quote:string,rating?:int}>
 */
function annam_about_get_testimonials() {
	$items = apply_filters( 'annam_about_testimonials', array() );
	if ( ! is_array( $items ) ) {
		return array();
	}
	$out = array();
	foreach ( $items as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$quote = isset( $row['quote'] ) ? (string) $row['quote'] : '';
		$name  = isset( $row['name'] ) ? (string) $row['name'] : '';
		if ( $quote === '' || $name === '' ) {
			continue;
		}
		$out[] = array(
			'name'   => $name,
			'role'   => isset( $row['role'] ) ? (string) $row['role'] : '',
			'quote'  => $quote,
			'rating' => isset( $row['rating'] ) ? min( 5, max( 0, (int) $row['rating'] ) ) : 0,
		);
	}
	return $out;
}

/**
 * Gỡ ảnh nổi bật mặc định của theme (chỉ dùng nền hero).
 */
function annam_about_remove_gp_featured_image() {
	if ( ! annam_about_is_about_template() ) {
		return;
	}
	remove_action( 'generate_after_header', 'generate_featured_page_header', 10 );
	remove_action( 'generate_before_content', 'generate_featured_page_header_inside_single', 10 );
	remove_action( 'generate_after_entry_header', 'generate_post_image', 10 );
}
add_action( 'wp', 'annam_about_remove_gp_featured_image', 9 );

/**
 * Ẩn output ảnh nổi bật trong luồng theme.
 *
 * @param string $html HTML.
 * @return string
 */
function annam_about_blank_featured_image_output( $html ) {
	return annam_about_is_about_template() ? '' : $html;
}
add_filter( 'generate_featured_image_output', 'annam_about_blank_featured_image_output', 10, 1 );

/**
 * Body class.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function annam_about_body_class( $classes ) {
	if ( annam_about_is_about_template() ) {
		$classes[] = 'annam-about-page';
	}
	return $classes;
}
add_filter( 'body_class', 'annam_about_body_class', 12 );

/**
 * Ẩn entry header mặc định.
 *
 * @param bool $show Hiển thị.
 * @return bool
 */
function annam_about_hide_entry_header( $show ) {
	return annam_about_is_about_template() ? false : $show;
}
add_filter( 'generate_show_entry_header', 'annam_about_hide_entry_header', 12 );

/**
 * Enqueue CSS/JS.
 */
function annam_about_enqueue_assets() {
	if ( is_admin() || ! annam_about_is_about_template() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$deps = array( 'annam-design-tokens' );
	$css  = $dir . '/assets/css/about-page.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-about-page',
			$uri . '/assets/css/about-page.css',
			$deps,
			(string) filemtime( $css )
		);
	}

	$js = $dir . '/assets/js/about-page.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-about-page',
			$uri . '/assets/js/about-page.js',
			array(),
			(string) filemtime( $js ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_about_enqueue_assets', 22 );

/**
 * URL trang Liên hệ (tìm theo template, hoặc filter / mặc định /lien-he/).
 *
 * @return string
 */
function annam_about_get_contact_url() {
	$pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'page-template-lien-he.php',
			'number'     => 1,
			'post_status'=> 'publish',
		)
	);
	if ( ! empty( $pages[0] ) && $pages[0] instanceof WP_Post ) {
		$url = get_permalink( $pages[0] );
		if ( $url ) {
			return $url;
		}
	}
	return apply_filters( 'annam_about_contact_url', home_url( '/lien-he/' ) );
}
