<?php
/**
 * JSON-LD @graph cho trang chủ — An Nam Discovery (TravelAgency + WebSite).
 * Dữ liệu đồng bộ footer / liên hệ / giới thiệu trên site.
 *
 * Không dùng AggregateRating / Review / số liệu chưa xác minh.
 * Tắt output theme: add_filter( 'annam_homepage_schema_print', '__return_false' );
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trang chủ (front page hoặc template Trang chủ tĩnh).
 *
 * @return bool
 */
function annam_is_homepage_for_schema() {
	if ( is_front_page() ) {
		return true;
	}
	if ( is_page_template( 'page-template-trang-chu.php' ) ) {
		return true;
	}
	$page_on_front = (int) get_option( 'page_on_front' );
	if ( $page_on_front > 0 && is_page( $page_on_front ) && 'page-template-trang-chu.php' === get_page_template_slug( $page_on_front ) ) {
		return true;
	}
	return false;
}

/**
 * URL gốc thương hiệu.
 *
 * @return string
 */
function annam_schema_brand_url() {
	return trailingslashit( apply_filters( 'annam_schema_brand_url', 'https://annamdiscovery.vn' ) );
}

/**
 * Logo (uploads trên site).
 *
 * @return string
 */
function annam_schema_logo_url() {
	$default = annam_schema_brand_url() . 'wp-content/uploads/2026/05/logo.png';

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id > 0 ) {
		$url = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( is_string( $url ) && '' !== $url ) {
			return apply_filters( 'annam_schema_logo_url', $url );
		}
	}

	return apply_filters( 'annam_schema_logo_url', $default );
}

/**
 * Mạng xã hội chính thức (theo site).
 *
 * @return string[]
 */
function annam_schema_brand_same_as_urls() {
	$urls = array(
		'https://web.facebook.com/AnNamDiscovery/',
		'https://x.com/annamdiscovery',
		'https://www.youtube.com/@AnNamDiscovery',
		'https://www.instagram.com/annamdiscovery/',
	);

	return array_values( array_filter( array_unique( apply_filters( 'annam_schema_brand_same_as_urls', $urls ) ) ) );
}

/**
 * Địa chỉ văn phòng (có liên kết Google Maps).
 *
 * @return array<int, array<string, string>>
 */
function annam_schema_brand_postal_addresses() {
	$addresses = array(
		array(
			'@type'           => 'PostalAddress',
			'name'            => 'Văn phòng Tú Mỡ',
			'streetAddress'   => '23 Tú Mỡ',
			'addressLocality' => 'Phường Yên Hòa',
			'addressRegion'   => 'Hà Nội',
			'addressCountry'  => 'VN',
			'hasMap'          => 'https://maps.app.goo.gl/dLFtc2s8LSYchXQa6',
		),
		array(
			'@type'           => 'PostalAddress',
			'name'            => 'Văn phòng Trần Quang Khải',
			'streetAddress'   => '214 Đ. Trần Quang Khải',
			'addressLocality' => 'Hoàn Kiếm',
			'addressRegion'   => 'Hà Nội',
			'addressCountry'  => 'VN',
			'hasMap'          => 'https://maps.app.goo.gl/3DmmAdysHTtu7pPu9',
		),
	);

	return apply_filters( 'annam_schema_postal_addresses', $addresses );
}

/**
 * Điểm liên hệ (tổng đài, hotline, email).
 *
 * @return array<int, array<string, mixed>>
 */
function annam_schema_brand_contact_points() {
	$points = array(
		array(
			'@type'             => 'ContactPoint',
			'contactType'       => 'customer service',
			'name'              => 'Tổng đài',
			'telephone'         => '+841908164',
			'availableLanguage' => array( 'Vietnamese', 'English' ),
			'areaServed'        => 'VN',
		),
		array(
			'@type'             => 'ContactPoint',
			'contactType'       => 'customer service',
			'name'              => 'Hotline',
			'telephone'         => '+84942471111',
			'availableLanguage' => array( 'Vietnamese', 'English' ),
			'areaServed'        => 'VN',
		),
		array(
			'@type'             => 'ContactPoint',
			'contactType'       => 'customer service',
			'email'             => 'annamdiscoveryvn@gmail.com',
			'availableLanguage' => array( 'Vietnamese', 'English' ),
			'areaServed'        => 'VN',
		),
	);

	return apply_filters( 'annam_schema_brand_contact_points', $points );
}

/**
 * Giấy phép lữ hành + mã số thuế (đã công bố trên site).
 *
 * @return array<string, mixed>
 */
function annam_schema_brand_legal_identifiers() {
	return apply_filters(
		'annam_schema_brand_legal_identifiers',
		array(
			'taxID'         => '0111205475',
			'license_label' => 'Giấy phép kinh doanh dịch vụ lữ hành quốc tế',
			'license_number'=> '01-3006/2025/CDL-GVN-GP LHQT',
		)
	);
}

/**
 * Mô tả thương hiệu (trang chủ).
 *
 * @return string
 */
function annam_schema_brand_description() {
	$legal = annam_schema_brand_legal_identifiers();
	$base  = 'An Nam Discovery cung cấp tour du lịch, combo nghỉ dưỡng, du thuyền, vé xe, khách sạn và dịch vụ tư vấn lịch trình trọn gói cho khách cá nhân, gia đình, nhóm bạn và khách đoàn. Đơn vị tập trung tư vấn hành trình phù hợp, thông tin rõ ràng, hỗ trợ đặt dịch vụ nhanh và tối ưu chi phí cho từng nhu cầu du lịch.';
	$base .= ' Đơn vị có kinh nghiệm tư vấn và tổ chức nhiều loại hình dịch vụ du lịch trong nước và định hướng mở rộng tour quốc tế.';
	if ( ! empty( $legal['license_label'] ) && ! empty( $legal['license_number'] ) ) {
		$base .= ' ' . $legal['license_label'] . ' (số ' . $legal['license_number'] . ').';
	}

	return apply_filters( 'annam_schema_brand_description', $base );
}

/**
 * Danh mục dịch vụ cho OfferCatalog.
 *
 * @return array<int, array{name:string,description:string}>
 */
function annam_schema_service_catalog_items() {
	$items = array(
		array(
			'name'        => 'Tour du lịch',
			'description' => 'Tư vấn và đặt tour trong nước, lựa chọn lịch trình phù hợp từng nhóm khách. Giá linh hoạt theo dịch vụ và lịch trình.',
		),
		array(
			'name'        => 'Combo nghỉ dưỡng',
			'description' => 'Gói nghỉ dưỡng kết hợp lưu trú và trải nghiệm, tối ưu theo ngân sách và thời gian lưu trú. Giá linh hoạt theo dịch vụ và lịch trình.',
		),
		array(
			'name'        => 'Du thuyền',
			'description' => 'Tư vấn đặt du thuyền, hành trình và dịch vụ trên tàu theo nhu cầu nhóm hoặc đoàn. Giá linh hoạt theo dịch vụ và lịch trình.',
		),
		array(
			'name'        => 'Vé xe du lịch / limousine',
			'description' => 'Hỗ trợ đặt vé xe, limousine và di chuyển liên tỉnh phục vụ hành trình du lịch. Giá linh hoạt theo dịch vụ và lịch trình.',
		),
		array(
			'name'        => 'Khách sạn và lưu trú',
			'description' => 'Gợi ý và đặt phòng khách sạn, resort phù hợp hành trình và số lượng khách. Giá linh hoạt theo dịch vụ và lịch trình.',
		),
		array(
			'name'        => 'Tư vấn lịch trình cho khách đoàn',
			'description' => 'Thiết kế lịch trình trọn gói cho đoàn công ty, trường học và nhóm đông người; phối hợp dịch vụ theo yêu cầu. Giá linh hoạt theo dịch vụ và lịch trình.',
		),
	);
	return apply_filters( 'annam_schema_service_catalog_items', $items );
}

/**
 * @return array<int, array<string, mixed>>
 */
function annam_schema_build_offer_catalog_parts() {
	$parts = array();
	foreach ( annam_schema_service_catalog_items() as $item ) {
		$parts[] = array(
			'@type'           => 'OfferCatalog',
			'name'            => $item['name'],
			'itemListElement' => array(
				array(
					'@type'       => 'Offer',
					'itemOffered' => array(
						'@type'       => 'Service',
						'name'        => $item['name'],
						'description' => $item['description'],
						'provider'    => array(
							'@id' => annam_schema_brand_url() . '#travel-agency',
						),
						'areaServed'  => array(
							array(
								'@type' => 'Country',
								'name'  => 'Vietnam',
							),
						),
					),
				),
			),
		);
	}
	return $parts;
}

/**
 * JSON-LD @graph cho trang chủ.
 *
 * @return array<string, mixed>
 */
function annam_get_homepage_schema_graph() {
	$base    = annam_schema_brand_url();
	$org_id  = $base . '#travel-agency';
	$site_id = $base . '#website';
	$logo    = annam_schema_logo_url();
	$legal   = annam_schema_brand_legal_identifiers();

	$travel_agency = array(
		'@type'           => array( 'TravelAgency', 'Organization', 'LocalBusiness' ),
		'@id'             => $org_id,
		'name'            => 'An Nam Discovery',
		'url'             => $base,
		'logo'            => array(
			'@type' => 'ImageObject',
			'url'   => $logo,
		),
		'image'           => array( $logo ),
		'description'     => annam_schema_brand_description(),
		'email'           => 'annamdiscoveryvn@gmail.com',
		'telephone'       => '+841908164',
		'contactPoint'    => annam_schema_brand_contact_points(),
		'address'         => annam_schema_brand_postal_addresses(),
		'areaServed'      => array(
			array(
				'@type' => 'Country',
				'name'  => 'Vietnam',
			),
			array(
				'@type' => 'AdministrativeArea',
				'name'  => 'International',
			),
		),
		'knowsAbout'      => array(
			'Tour du lịch',
			'Combo nghỉ dưỡng',
			'Du thuyền',
			'Vé xe du lịch',
			'Khách sạn',
			'Tư vấn lịch trình trọn gói',
			'Du lịch khách đoàn',
			'Du lịch gia đình',
		),
		'audience'        => array(
			array(
				'@type'        => 'Audience',
				'audienceType' => 'Khách cá nhân',
			),
			array(
				'@type'        => 'Audience',
				'audienceType' => 'Gia đình',
			),
			array(
				'@type'        => 'Audience',
				'audienceType' => 'Nhóm bạn',
			),
			array(
				'@type'        => 'Audience',
				'audienceType' => 'Khách đoàn',
			),
			array(
				'@type'        => 'Audience',
				'audienceType' => 'Doanh nghiệp',
			),
		),
		'slogan'          => 'Tư vấn du lịch trọn gói — linh hoạt, rõ ràng, tối ưu chi phí',
		'sameAs'          => annam_schema_brand_same_as_urls(),
		'hasOfferCatalog' => array(
			'@type'   => 'OfferCatalog',
			'name'    => 'Dịch vụ du lịch An Nam Discovery',
			'hasPart' => annam_schema_build_offer_catalog_parts(),
		),
	);

	if ( ! empty( $legal['taxID'] ) ) {
		$travel_agency['taxID'] = $legal['taxID'];
	}

	if ( ! empty( $legal['license_label'] ) && ! empty( $legal['license_number'] ) ) {
		$travel_agency['hasCredential'] = array(
			'@type'              => 'EducationalOccupationalCredential',
			'name'               => $legal['license_label'],
			'credentialCategory' => 'license',
			'identifier'         => $legal['license_number'],
		);
	}

	$website = array(
		'@type'       => 'WebSite',
		'@id'         => $site_id,
		'url'         => $base,
		'name'        => 'An Nam Discovery',
		'description' => annam_schema_brand_description(),
		'publisher'   => array( '@id' => $org_id ),
		'inLanguage'  => 'vi-VN',
	);

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			$travel_agency,
			$website,
		),
	);

	return apply_filters( 'annam_homepage_schema_graph', $graph );
}

/**
 * In JSON-LD trên trang chủ.
 */
function annam_print_homepage_json_ld() {
	if ( ! apply_filters( 'annam_homepage_schema_print', true ) ) {
		return;
	}
	if ( ! annam_is_homepage_for_schema() ) {
		return;
	}

	$graph = annam_get_homepage_schema_graph();
	if ( empty( $graph['@graph'] ) ) {
		return;
	}

	$json = wp_json_encode( $graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
	if ( ! is_string( $json ) || '' === $json ) {
		return;
	}

	echo "\n<script type=\"application/ld+json\">\n" . $json . "\n</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'annam_print_homepage_json_ld', 6 );
