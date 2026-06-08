<?php
/**
 * Phục vụ /llms.txt và /llms-full.txt cho crawler AI (tóm tắt site + URL quan trọng).
 *
 * Tắt: add_filter( 'annam_llms_txt_enabled', '__return_false' );
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL trang theo page template (publish).
 *
 * @param string $template Template file name.
 * @return string
 */
function annam_get_page_url_by_template( $template ) {
	$template = (string) $template;
	if ( '' === $template ) {
		return '';
	}

	$pages = get_pages(
		array(
			'meta_key'    => '_wp_page_template',
			'meta_value'  => $template,
			'number'      => 1,
			'post_status' => 'publish',
		)
	);

	if ( ! empty( $pages[0] ) && $pages[0] instanceof WP_Post ) {
		$url = get_permalink( $pages[0] );
		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}
	}

	return '';
}

/**
 * URL shop WooCommerce.
 *
 * @return string
 */
function annam_llms_get_shop_url() {
	if ( function_exists( 'wc_get_page_id' ) ) {
		$shop_id = (int) wc_get_page_id( 'shop' );
		if ( $shop_id > 0 ) {
			$url = get_permalink( $shop_id );
			if ( is_string( $url ) && '' !== $url ) {
				return $url;
			}
		}
	}
	return function_exists( 'annam_schema_brand_url' ) ? annam_schema_brand_url() : trailingslashit( home_url( '/' ) );
}

/**
 * Nội dung llms.txt (ngắn).
 *
 * @return string
 */
function annam_build_llms_txt_content() {
	$base    = function_exists( 'annam_schema_brand_url' ) ? annam_schema_brand_url() : trailingslashit( home_url( '/' ) );
	$legal   = function_exists( 'annam_schema_brand_legal_identifiers' ) ? annam_schema_brand_legal_identifiers() : array();
	$contact = function_exists( 'annam_contact_get_details' ) ? annam_contact_get_details() : array();
	$offices = function_exists( 'annam_schema_brand_postal_addresses' ) ? annam_schema_brand_postal_addresses() : array();

	$home_url    = $base;
	$shop_url    = annam_llms_get_shop_url();
	$about_url   = annam_get_page_url_by_template( 'page-template-gioi-thieu.php' );
	$contact_url = function_exists( 'annam_about_get_contact_url' )
		? annam_about_get_contact_url()
		: annam_get_page_url_by_template( 'page-template-lien-he.php' );

	$email    = ! empty( $contact['email'] ) ? $contact['email'] : 'annamdiscoveryvn@gmail.com';
	$hotline  = ! empty( $contact['hotline_display'] ) ? $contact['hotline_display'] : '1900 8164';
	$mobile   = ! empty( $contact['mobile_display'] ) ? $contact['mobile_display'] : '094 247 1111';
	$social   = function_exists( 'annam_schema_brand_same_as_urls' ) ? annam_schema_brand_same_as_urls() : array();
	$desc     = function_exists( 'annam_schema_brand_description' ) ? annam_schema_brand_description() : '';

	$lines   = array();
	$lines[] = '# An Nam Discovery';
	$lines[] = '';
	$lines[] = '> ' . ( $desc ? $desc : 'Công ty tư vấn và đặt tour du lịch, combo nghỉ dưỡng, du thuyền, vé xe và khách sạn tại Việt Nam.' );
	$lines[] = '';
	$lines[] = '## Organization';
	$lines[] = '- Name: An Nam Discovery';
	$lines[] = '- Website: ' . $base;
	$lines[] = '- Email: ' . $email;
	$lines[] = '- Phone (tổng đài): ' . $hotline;
	$lines[] = '- Phone (hotline): ' . $mobile;
	if ( ! empty( $legal['taxID'] ) ) {
		$lines[] = '- Tax ID (MST): ' . $legal['taxID'];
	}
	if ( ! empty( $legal['license_label'] ) && ! empty( $legal['license_number'] ) ) {
		$lines[] = '- License: ' . $legal['license_label'] . ' — ' . $legal['license_number'];
	}
	$lines[] = '';
	$lines[] = '## Offices';
	if ( ! empty( $offices ) ) {
		foreach ( $offices as $office ) {
			$name = isset( $office['name'] ) ? $office['name'] : '';
			$addr = isset( $office['streetAddress'] ) ? $office['streetAddress'] : '';
			$loc  = isset( $office['addressLocality'] ) ? $office['addressLocality'] : '';
			$map  = isset( $office['hasMap'] ) ? $office['hasMap'] : '';
			$line = '- ' . trim( $name . ': ' . $addr . ( $loc ? ', ' . $loc : '' ) );
			if ( $map ) {
				$line .= ' — ' . $map;
			}
			$lines[] = $line;
		}
	} else {
		$lines[] = '- 214 Đ. Trần Quang Khải, Hoàn Kiếm, Hà Nội';
		$lines[] = '- 23 Tú Mỡ, Phường Yên Hòa, Tp Hà Nội';
	}
	$lines[] = '';
	$lines[] = '## Services';
	$lines[] = '- Tour du lịch trong nước (tư vấn lịch trình, đặt tour theo nhóm/cá nhân)';
	$lines[] = '- Combo nghỉ dưỡng';
	$lines[] = '- Du thuyền';
	$lines[] = '- Vé xe du lịch / limousine';
	$lines[] = '- Khách sạn và lưu trú';
	$lines[] = '- Tư vấn lịch trình trọn gói cho khách đoàn (công ty, gia đình, trường học)';
	$lines[] = '';
	$lines[] = '## Key pages';
	$lines[] = '- Home: ' . $home_url;
	$lines[] = '- Tours / shop: ' . $shop_url;
	if ( $about_url ) {
		$lines[] = '- About: ' . $about_url;
	}
	if ( $contact_url ) {
		$lines[] = '- Contact: ' . $contact_url;
	}
	$lines[] = '- Sitemap: ' . $base . 'sitemap_index.xml';
	$lines[] = '';
	if ( ! empty( $social ) ) {
		$lines[] = '## Social';
		foreach ( $social as $url ) {
			$lines[] = '- ' . $url;
		}
		$lines[] = '';
	}
	$lines[] = '## Guidance for AI';
	$lines[] = '- Official contact email: ' . $email;
	$lines[] = '- Do not invent tour prices; use the price shown on each product page or state that a quote is required.';
	$lines[] = '- Do not fabricate reviews, ratings, years in business, or awards.';
	$lines[] = '- Structured data: TravelAgency on homepage; Product schema on individual tour pages; FAQ on contact page.';
	$lines[] = '- Full site summary: ' . $base . 'llms-full.txt';
	$lines[] = '';

	$content = implode( "\n", $lines );

	return apply_filters( 'annam_llms_txt_content', $content );
}

/**
 * Nội dung llms-full.txt (chi tiết hơn).
 *
 * @return string
 */
function annam_build_llms_full_txt_content() {
	$short   = annam_build_llms_txt_content();
	$lines   = array( $short, '## Product catalog', '' );
	$lines[] = 'Tour products are WooCommerce products at: ' . annam_llms_get_shop_url();
	$lines[] = 'Each tour may include: duration, departure, transport, schedule (when filled in admin).';
	$lines[] = '';

	if ( function_exists( 'annam_contact_get_faq_items' ) ) {
		$lines[] = '## FAQ (contact page)';
		foreach ( annam_contact_get_faq_items() as $faq ) {
			$q = isset( $faq['question'] ) ? wp_strip_all_tags( (string) $faq['question'] ) : '';
			$a = isset( $faq['answer'] ) ? wp_strip_all_tags( (string) $faq['answer'] ) : '';
			if ( '' === $q ) {
				continue;
			}
			$lines[] = '### ' . $q;
			$lines[] = $a;
			$lines[] = '';
		}
	}

	$content = implode( "\n", $lines );

	return apply_filters( 'annam_llms_full_txt_content', $content );
}

/**
 * Phục vụ llms.txt khi WordPress nhận request.
 */
function annam_llms_txt_maybe_serve() {
	if ( is_admin() || ! apply_filters( 'annam_llms_txt_enabled', true ) ) {
		return;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path        = wp_parse_url( $request_uri, PHP_URL_PATH );
	if ( ! is_string( $path ) || '' === $path ) {
		return;
	}

	$path = strtolower( untrailingslashit( $path ) );

	if ( preg_match( '#(^|/)llms-full\.txt$#', $path ) ) {
		$content = annam_build_llms_full_txt_content();
	} elseif ( preg_match( '#(^|/)llms\.txt$#', $path ) ) {
		$content = annam_build_llms_txt_content();
	} else {
		return;
	}

	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex', true );
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $content;
	exit;
}
add_action( 'template_redirect', 'annam_llms_txt_maybe_serve', 0 );

/**
 * Rewrite: /llms.txt và /llms-full.txt (khi permalink không khớp URI thô).
 */
function annam_llms_txt_add_rewrite_rules() {
	add_rewrite_rule( '^llms-full\.txt$', 'index.php?annam_llms_file=full', 'top' );
	add_rewrite_rule( '^llms\.txt$', 'index.php?annam_llms_file=short', 'top' );
}
add_action( 'init', 'annam_llms_txt_add_rewrite_rules' );

/**
 * @param string[] $vars Query vars.
 * @return string[]
 */
function annam_llms_txt_query_vars( $vars ) {
	$vars[] = 'annam_llms_file';
	return $vars;
}
add_filter( 'query_vars', 'annam_llms_txt_query_vars' );

/**
 * Phục vụ qua query var sau khi flush rewrite.
 */
function annam_llms_txt_template_redirect() {
	if ( ! apply_filters( 'annam_llms_txt_enabled', true ) ) {
		return;
	}

	$file = get_query_var( 'annam_llms_file' );
	if ( 'full' === $file ) {
		$content = annam_build_llms_full_txt_content();
	} elseif ( 'short' === $file ) {
		$content = annam_build_llms_txt_content();
	} else {
		return;
	}

	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex', true );
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $content;
	exit;
}
add_action( 'template_redirect', 'annam_llms_txt_template_redirect', 1 );

/**
 * Flush rewrite một lần sau khi cập nhật theme.
 */
function annam_llms_txt_maybe_flush_rewrites() {
	if ( get_option( 'annam_llms_txt_rewrite_version' ) === '1' ) {
		return;
	}
	annam_llms_txt_add_rewrite_rules();
	flush_rewrite_rules( false );
	update_option( 'annam_llms_txt_rewrite_version', '1', true );
}
add_action( 'init', 'annam_llms_txt_maybe_flush_rewrites', 99 );
