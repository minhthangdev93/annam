<?php
/**
 * Config landing thuê nguyên xe Limousine HN–Sapa.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * CTA từ contact site-wide.
 *
 * @return array<string,string>
 */
function annam_limo_charter_landing_get_cta() {
	$d             = function_exists( 'annam_contact_get_details' ) ? annam_contact_get_details() : array();
	$mobile        = isset( $d['mobile_display'] ) ? (string) $d['mobile_display'] : '0942471111';
	$mobile_digits = preg_replace( '/\D+/', '', $mobile );

	return apply_filters(
		'annam_limo_charter_landing_cta',
		array(
			'brand'            => isset( $d['brand'] ) ? (string) $d['brand'] : 'An Nam Discovery',
			'hotline_display'  => isset( $d['hotline_display'] ) ? (string) $d['hotline_display'] : '1900 8164',
			'hotline_tel'      => isset( $d['hotline_tel'] ) ? (string) $d['hotline_tel'] : 'tel:19008164',
			'hotline2_display' => $mobile,
			'hotline2_tel'     => 'tel:' . $mobile_digits,
			'zalo_url'         => isset( $d['zalo_url'] ) ? (string) $d['zalo_url'] : 'http://zalo.me/2127942034358673568',
			'address'          => isset( $d['address'] ) ? (string) $d['address'] : '214 Đ. Trần Quang Khải, Hoàn Kiếm, Hà Nội',
			'maps_url'         => 'https://maps.app.goo.gl/6mQkPgdUMFhRfRnK7',
		)
	);
}

/**
 * Link Google Maps mặc định (nút “Mở Google Maps”).
 *
 * @return string
 */
function annam_limo_charter_landing_default_maps_url() {
	return (string) apply_filters( 'annam_limo_charter_landing_default_maps_url', 'https://maps.app.goo.gl/6mQkPgdUMFhRfRnK7' );
}

/**
 * Query nhúng iframe (tọa độ / tên địa điểm) — short link không dùng được cho embed.
 *
 * @return string
 */
function annam_limo_charter_landing_default_maps_embed_query() {
	return (string) apply_filters( 'annam_limo_charter_landing_default_maps_embed_query', '21.026181,105.8588833' );
}

/**
 * Permalink helper by path.
 *
 * @param string $path Slug path.
 * @param string $fallback Fallback URL path.
 * @return string
 */
function annam_limo_charter_landing_page_url( $path, $fallback = '' ) {
	$page = get_page_by_path( $path );
	if ( $page instanceof WP_Post ) {
		$url = get_permalink( $page );
		if ( $url ) {
			return $url;
		}
	}
	return home_url( $fallback ? $fallback : '/' . trailingslashit( $path ) );
}

/**
 * @param int $page_id Page ID.
 * @return array<string,mixed>
 */
function annam_limo_charter_landing_get_default_config( $page_id = 0 ) {
	$cta = annam_limo_charter_landing_get_cta();

	$limo_seat_url = annam_limo_charter_landing_page_url( 've-limousine-ha-noi-sapa', '/ve-limousine-ha-noi-sapa/' );
	// Fallback common slug if vé page uses another path.
	if ( home_url( '/ve-limousine-ha-noi-sapa/' ) === $limo_seat_url ) {
		$alt = get_page_by_path( 'limousine-ha-noi-sapa' );
		if ( $alt instanceof WP_Post ) {
			$limo_seat_url = get_permalink( $alt ) ?: $limo_seat_url;
		}
	}
	$cabin_url = annam_limo_charter_landing_page_url( 'dat-ve-xe-ha-noi-sapa', '/dat-ve-xe-ha-noi-sapa/' );
	$tour_url  = annam_limo_charter_landing_page_url( 'tour-sapa-3-ngay-2-dem', '/tour-sapa-3-ngay-2-dem/' );

	$config = array(
		'product_name' => 'Thuê xe Limousine Hà Nội – Sapa 9 & 11 chỗ (có tài xế)',
		'hero'         => array(
			'title'      => 'Thuê Xe Limousine Hà Nội – Sapa 9 & 11 Chỗ',
			'subtitle'   => 'Xe riêng có tài xế · Từ 4.200.000đ/chiều',
			'price_from' => '4.200.000đ',
			'badges'     => array(
				'Thuê nguyên xe – không ghép khách',
				'Đón trả tận nơi Hà Nội & Sapa',
				'Chủ động giờ khởi hành',
				'Giá trọn gói rõ ràng',
			),
			'note'       => 'Đây là thuê nguyên xe riêng có tài xế, không phải vé limousine tính theo ghế.',
		),
		'form'         => array(
			'title'           => 'Nhận Báo Giá Nhanh',
			'subtitle'        => 'Để lại SĐT — nhân viên gọi tư vấn & báo giá trong ngày.',
			'submit_label'    => 'Nhận Báo Giá — Để Lại SĐT',
			'footer_note'     => 'Sale gọi tư vấn, chốt giờ & điểm đón. Không thanh toán online ngay.',
			'success_message' => 'Cảm ơn quý khách. An Nam Discovery đã nhận SĐT và sẽ gọi tư vấn báo giá sớm.',
		),
		'form_defaults' => array(
			'route'   => 'hn_sapa',
			'vehicle' => '9',
			'time'    => '07:00',
		),
		'pricing'      => array(
			'rows' => array(
				array(
					'type'    => '9',
					'label'   => 'Limousine 9 chỗ',
					'price'   => '4.200.000đ',
					'unit'    => '/ xe / chiều',
					'image'   => 'price-9',
					'bullets' => array(
						'Phù hợp nhóm nhỏ / gia đình',
						'Không ghép khách',
						'Có tài xế',
					),
					'cta'     => 'Chọn xe 9 chỗ',
				),
				array(
					'type'    => '11',
					'label'   => 'Limousine 11 chỗ',
					'price'   => '4.500.000đ',
					'unit'    => '/ xe / chiều',
					'image'   => 'price-11',
					'bullets' => array(
						'Rộng hơn cho đoàn đông',
						'Không ghép khách',
						'Có tài xế',
					),
					'cta'     => 'Chọn xe 11 chỗ',
					'badge'   => 'Phổ biến',
				),
			),
			'included' => 'Giá đã gồm: xe + tài xế + nhiên liệu + phí cao tốc hành trình tiêu chuẩn HN–Sapa.',
			'extra'    => '',
			'note'     => 'Thuê nguyên xe riêng có tài xế — không phải vé limousine theo ghế.',
		),
		'trust_strip'  => array(
			array( 'icon' => 'driver', 'label' => 'Xe riêng có tài xế' ),
			array( 'icon' => 'check', 'label' => 'Giá xác nhận trước chuyến' ),
			array( 'icon' => 'pin', 'label' => 'Đón tận nơi' ),
			array( 'icon' => 'chat', 'label' => 'Hotline / Zalo' ),
		),
		'gallery'      => array(
			array( 'slot' => 'gallery-1', 'caption' => '9 chỗ — ngoại thất' ),
			array( 'slot' => 'gallery-2', 'caption' => '11 chỗ — ngoại thất' ),
			array( 'slot' => 'gallery-3', 'caption' => '9 chỗ — khoang ghế' ),
			array( 'slot' => 'gallery-4', 'caption' => '11 chỗ — khoang ghế' ),
			array( 'slot' => 'gallery-5', 'caption' => 'Hành lý / sẵn sàng xuất phát' ),
			array( 'slot' => 'gallery-6', 'caption' => 'Trên đường HN ⇄ Sapa' ),
		),
		'why'          => array(
			array(
				'icon'  => 'van',
				'title' => 'Xe riêng rõ loại',
				'text'  => 'Chọn limo 9 hoặc 11 chỗ — không ghép.',
			),
			array(
				'icon'  => 'tag',
				'title' => 'Giá trọn gói',
				'text'  => 'Đã gồm tài xế, nhiên liệu, cao tốc chuẩn.',
			),
			array(
				'icon'  => 'clock',
				'title' => 'Chủ động giờ',
				'text'  => 'Chốt giờ khởi hành theo lịch của bạn.',
			),
			array(
				'icon'  => 'pin',
				'title' => 'Đón tận nơi',
				'text'  => 'Đón trả 2 đầu Hà Nội & Sapa.',
			),
		),
		'steps'        => array(
			array( 'title' => 'Nhận báo giá', 'text' => 'Gọi, Zalo hoặc để lại SĐT trên form.' ),
			array( 'title' => 'Xác nhận lịch', 'text' => 'Sale chốt giờ, điểm đón và loại xe.' ),
			array( 'title' => 'Đón đúng giờ', 'text' => 'Tài xế đón tại điểm đã hẹn và khởi hành.' ),
		),
		'compare'      => array(
			'title' => 'Khác vé limousine theo ghế',
			'text'  => 'Trang này là thuê nguyên xe riêng có tài xế (4,2–4,5 triệu/chiều). Nếu cần 1–2 ghế theo lịch cố định, xem vé limousine ghế bên dưới.',
		),
		'faq'          => array(
			array(
				'question' => 'Giá thuê xe limousine HN–Sapa bao nhiêu?',
				'answer'   => 'Limousine 9 chỗ: 4.200.000đ/xe/chiều. Limousine 11 chỗ: 4.500.000đ/xe/chiều. Giá trọn gói có tài xế.',
			),
			array(
				'question' => 'Giá đã gồm những gì?',
				'answer'   => 'Đã gồm xe, tài xế, nhiên liệu và phí cao tốc hành trình tiêu chuẩn Hà Nội – Sapa. Điểm đón/trả ngoài phạm vi có thể phát sinh phụ phí — báo trước.',
			),
			array(
				'question' => 'Đây là thuê nguyên xe hay vé theo ghế?',
				'answer'   => 'Thuê nguyên xe riêng có tài xế, không ghép khách. Không phải vé limousine tính theo ghế (~450–500k/người).',
			),
			array(
				'question' => 'Có đón tận nhà / khách sạn không?',
				'answer'   => 'Có. Đón trả tận nơi Hà Nội và Sapa trong phạm vi thỏa thuận khi xác nhận lịch.',
			),
			array(
				'question' => 'Tôi có tự chọn giờ khởi hành không?',
				'answer'   => 'Có. Bạn chủ động giờ dự kiến; sale xác nhận khung giờ phù hợp vận hành trước chuyến.',
			),
			array(
				'question' => 'Nên chọn 9 chỗ hay 11 chỗ?',
				'answer'   => '9 chỗ phù hợp nhóm nhỏ / gia đình. 11 chỗ rộng hơn cho đoàn đông hoặc nhiều hành lý. Chưa chắc có thể chọn “Chưa quyết định” trên form.',
			),
			array(
				'question' => 'Phụ phí điểm đón thế nào?',
				'answer'   => 'Trong phạm vi tiêu chuẩn HN–Sapa không phát sinh. Ngoài phạm vi sẽ báo trước khi chốt lịch.',
			),
		),
		'cross_sell'   => array(
			array(
				'slot'     => 'cross-limo',
				'title'    => 'Vé limousine ghế HN ⇄ Sapa',
				'line'     => 'Từ ~450k/ghế · lịch cố định',
				'price'    => 'Từ 450.000đ',
				'url'      => $limo_seat_url,
				'cta'      => 'Xem lịch & giá vé',
				'track'    => 'cross_limo_seat',
			),
			array(
				'slot'     => 'cross-cabin',
				'title'    => 'Xe giường nằm cabin 24 phòng',
				'line'     => 'Giường nằm VIP · giữ chỗ theo lịch',
				'price'    => 'Từ 450.000đ',
				'url'      => $cabin_url,
				'cta'      => 'Xem cabin & giữ chỗ',
				'track'    => 'cross_cabin',
			),
			array(
				'slot'     => 'cross-tour',
				'title'    => 'Tour Sapa 3 ngày 2 đêm',
				'line'     => 'Tour trọn gói Cát Cát – Fansipan – Moana',
				'price'    => 'Từ 2.990.000đ',
				'url'      => $tour_url,
				'cta'      => 'Xem tour 3N2Đ',
				'track'    => 'cross_tour',
			),
		),
		'related_tours' => array(
			'category_slug' => 'tour-sapa',
			'limit'         => -1,
			'title'         => 'Tour & Combo Sapa khác',
		),
		'final_cta'    => array(
			'title'    => 'Cần thuê limousine 9–11 chỗ Hà Nội – Sapa ngay?',
			'subtitle' => 'Sale An Nam giữ xe & chốt lịch — Gọi, Zalo hoặc để lại SĐT.',
		),
		'seo'          => array(
			'title'       => 'Thuê Xe Limousine Hà Nội Sapa 9 & 11 Chỗ | Từ 4.200.000đ',
			'description' => 'Thuê nguyên xe limousine Hà Nội – Sapa có tài xế. 9 chỗ 4.200.000đ · 11 chỗ 4.500.000đ/chiều. Đón tận nơi, chủ động giờ — gọi/Zalo báo giá nhanh.',
		),
		'sections'     => array(
			'hero'          => true,
			'pricing'       => true,
			'trust'         => true,
			'gallery'       => true,
			'proof'         => true,
			'why'           => true,
			'steps'         => true,
			'compare'       => true,
			'faq'           => true,
			'cross_sell'    => true,
			'reviews'       => true,
			'related_tours' => true,
			'final_cta'     => true,
		),
		'brand'        => isset( $cta['brand'] ) ? $cta['brand'] : 'An Nam Discovery',
	);

	return apply_filters( 'annam_limo_charter_landing_config', $config, $page_id );
}
