<?php
/**
 * Config landing Tour Sapa 3N2Đ (Cát Cát – Fansipan – Moana).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * CTA từ contact site-wide.
 *
 * @return array<string,string>
 */
function annam_tour_sapa_landing_get_cta() {
	$d             = function_exists( 'annam_contact_get_details' ) ? annam_contact_get_details() : array();
	$mobile        = isset( $d['mobile_display'] ) ? (string) $d['mobile_display'] : '0942471111';
	$mobile_digits = preg_replace( '/\D+/', '', $mobile );

	return apply_filters(
		'annam_tour_sapa_landing_cta',
		array(
			'brand'            => isset( $d['brand'] ) ? (string) $d['brand'] : 'An Nam Discovery',
			'hotline_display'  => isset( $d['hotline_display'] ) ? (string) $d['hotline_display'] : '1900 8164',
			'hotline_tel'      => isset( $d['hotline_tel'] ) ? (string) $d['hotline_tel'] : 'tel:19008164',
			'hotline2_display' => $mobile,
			'hotline2_tel'     => 'tel:' . $mobile_digits,
			'zalo_url'         => isset( $d['zalo_url'] ) ? (string) $d['zalo_url'] : 'http://zalo.me/2127942034358673568',
		)
	);
}

/**
 * @param int $page_id Page ID.
 * @return array<string,mixed>
 */
function annam_tour_sapa_landing_get_default_config( $page_id = 0 ) {
	$config = array(
		'product_name'  => 'Tour Sapa Cát Cát – Fansipan – Moana 3 ngày 2 đêm',
		'hero'          => array(
			'title'      => 'Tour Sapa 3 Ngày 2 Đêm',
			'subtitle'   => 'Du lịch Sapa 3N2Đ: Cát Cát – Fansipan – Moana. Khởi hành hàng ngày từ Hà Nội. Giá tour đã gồm xe limousine hoặc cabin VIP khứ hồi.',
			'price_from' => '2.990.000đ',
			'badges'     => array(
				'Khởi hành hàng ngày',
				'Đã gồm xe HN ⇄ Sapa',
				'KS 3–4 sao trung tâm',
				'Tặng lẩu cá tầm, gà bản + ngâm chân thảo mộc',
			),
		),
		'form'          => array(
			'title'           => 'Giữ Chỗ Tour Trong 1 Phút',
			'subtitle'        => 'Điền thông tin — nhân viên gọi hoặc Zalo xác nhận trong giờ làm việc.',
			'submit_label'    => 'Gửi Yêu Cầu Giữ Chỗ',
			'footer_note'     => 'Không cần thanh toán online ngay. Nhân viên sẽ gọi/Zalo xác nhận trước.',
			'success_message' => 'Cảm ơn quý khách. An Nam Discovery đã nhận thông tin và sẽ liên hệ xác nhận tour sớm.',
		),
		'form_defaults' => array(
			'hotel' => '3star',
		),
		'highlights'    => array(
			array( 'title' => 'Bản Cát Cát', 'text' => 'Văn hóa H’Mông, thác Tiên Sa, check-in đặc trưng.' ),
			array( 'title' => 'Fansipan 3.143m', 'text' => 'Nóc nhà Đông Dương — vé cáp/tàu tự túc.' ),
			array( 'title' => 'Moana Sapa', 'text' => 'Cổng trời Bali, Bàn tay vàng, hồ vô cực.' ),
			array( 'title' => 'Xe limo / cabin VIP', 'text' => 'Hà Nội ⇄ Sapa đã gồm trong giá tour.' ),
			array( 'title' => 'Quà tặng', 'text' => 'Lẩu cá tầm, gà bản + ngâm chân thảo mộc.' ),
			array( 'title' => 'Ăn nghỉ đủ', 'text' => '02 sáng + 04 bữa chính · 02 đêm KS.' ),
		),
		'pricing'       => array(
			'lead' => 'Giá tour Sapa 3 ngày 2 đêm (3N2Đ) trọn gói / khách. Đã gồm xe limousine hoặc cabin VIP Hà Nội ⇄ Sapa.',
			'rows' => array(
				array(
					'type'   => '3star',
					'label'  => 'Khách sạn 3 sao',
					'price'  => '2.990.000đ',
					'desc'   => 'Lavender Sapa, H&T Hotel, Luxury Hotel (hoặc tương đương).',
					'badge'  => 'Phổ biến',
					'hotels' => 'Lavender Sapa · H&T · Luxury',
				),
				array(
					'type'   => '4star',
					'label'  => 'Khách sạn 4 sao',
					'price'  => '3.990.000đ',
					'desc'   => 'Charm Hotel, Bamboo Hotel, Amazing Hotel (hoặc tương đương).',
					'badge'  => '',
					'hotels' => 'Charm · Bamboo · Amazing',
				),
			),
			'note' => 'Tiêu chuẩn 02 khách/phòng. Phụ thu phòng đơn theo quy định khách sạn. Vé Fansipan và buffet Sapa Xưa (400.000đ) không gồm trong giá.',
		),
		'itinerary'     => array(
			array(
				'day'   => 'Ngày 01',
				'title' => 'Hà Nội – Sapa – Bản Cát Cát',
				'meals' => 'Ăn trưa, tối',
				'image' => 'itinerary-day-1',
				'steps' => array(
					array( 'time' => '06:00 – 06:30', 'text' => 'Xe đón tại điểm hẹn, khởi hành đi Sapa. Nghỉ dừng chân dọc đường.' ),
					array( 'time' => '12:40', 'text' => 'Đến Sapa, dùng bữa trưa tại nhà hàng.' ),
					array( 'time' => '14:00', 'text' => 'Nhận phòng khách sạn và nghỉ ngơi.' ),
					array( 'time' => '14:30', 'text' => 'Tham quan Bản Cát Cát — bản làng người H’Mông, thác Tiên Sa, nhà máy thủy điện cổ, check-in.' ),
					array( 'time' => '18:30', 'text' => 'Bữa tối lẩu tại nhà hàng.' ),
					array( 'time' => 'Buổi tối', 'text' => 'Tự do Nhà thờ đá, quảng trường – chợ đêm. Nghỉ đêm KS Sapa.' ),
				),
			),
			array(
				'day'   => 'Ngày 02',
				'title' => 'Fansipan – Moana Sapa – Sapa Xưa',
				'meals' => 'Ăn sáng, trưa',
				'image' => 'itinerary-day-2',
				'steps' => array(
					array( 'time' => '07:00', 'text' => 'Ăn sáng tại khách sạn.' ),
					array( 'time' => '08:00', 'text' => 'Ga Fansipan. Tự túc tàu hỏa Mường Hoa, cáp treo, tàu leo đỉnh (nếu dùng) — chinh phục 3.143m.' ),
					array( 'time' => '11:30', 'text' => 'Về trung tâm Sapa, ăn trưa.' ),
					array( 'time' => '14:00', 'text' => 'Moana Sapa: Cổng trời Bali, Bàn tay vàng, Hồ vô cực, tượng Moana, săn mây Hoàng Liên Sơn.' ),
					array( 'time' => '17:00', 'text' => 'Về khách sạn nghỉ ngơi.' ),
					array( 'time' => '18:30', 'text' => 'Buffet tối Sapa Xưa (400.000đ/người — tự túc) hoặc tự do ẩm thực. Dạo phố về đêm.' ),
				),
			),
			array(
				'day'   => 'Ngày 03',
				'title' => 'Tự do Sapa – Hà Nội',
				'meals' => 'Ăn sáng, trưa',
				'image' => 'itinerary-day-3',
				'steps' => array(
					array( 'time' => '07:00', 'text' => 'Ăn sáng tại khách sạn.' ),
					array( 'time' => 'Buổi sáng', 'text' => 'Tự do mua sắm, Nhà thờ đá, quảng trường, chợ Sapa, cà phê ngắm núi.' ),
					array( 'time' => '11:00', 'text' => 'Trả phòng khách sạn.' ),
					array( 'time' => '11:30', 'text' => 'Ăn trưa tại nhà hàng.' ),
					array( 'time' => '13:00 – 14:00', 'text' => 'Tự do mua sắm, chuẩn bị hành lý.' ),
					array( 'time' => '14:00 – 14:30', 'text' => 'Xe đón khởi hành về Hà Nội.' ),
					array( 'time' => '20:00 – 21:00', 'text' => 'Về Hà Nội. Kết thúc chương trình.' ),
				),
			),
		),
		'includes'      => array(
			'02 đêm khách sạn 3–4 sao trung tâm Sapa (02 khách/phòng).',
			'02 bữa sáng tại khách sạn và 04 bữa chính tại nhà hàng.',
			'Vé tham quan Bản Cát Cát.',
			'Vé tham quan Moana Sapa.',
			'Xe đưa đón tham quan Cát Cát, Moana, Fansipan theo lịch trình.',
			'Xe Hà Nội – Sapa – Hà Nội (limousine hoặc cabin VIP).',
			'Hướng dẫn viên nhiệt tình, kinh nghiệm.',
			'Nước uống trên xe.',
			'Tặng lẩu cá tầm, gà bản + ngâm chân thảo mộc (theo chương trình).',
		),
		'excludes'      => array(
			'Vé Fansipan: cáp treo, tàu hỏa Mường Hoa, tàu leo đỉnh.',
			'Bữa tối buffet Sapa Xưa (400.000đ/người).',
			'Đồ uống trong bữa ăn và chi phí cá nhân.',
			'Dịch vụ vui chơi / tham quan ngoài chương trình.',
			'Phụ thu phòng đơn.',
			'Thuế VAT nếu xuất hóa đơn.',
			'Tip HDV, lái xe và phát sinh khác.',
		),
		'children'      => array(
			'Trẻ em dưới 5 tuổi: miễn phí tour (mỗi gia đình 01 bé); bố mẹ tự lo ăn nghỉ. Bé thứ 2 tính 50% người lớn.',
			'Trẻ em 5 đến dưới 9 tuổi: 75% giá người lớn, ngủ chung giường bố mẹ.',
			'Trẻ em từ 9 tuổi: 100% giá người lớn.',
			'Vé Fansipan / tàu theo quy định khu du lịch (chiều cao/độ tuổi).',
		),
		'experience'    => array(
			array( 'slot' => 'experience-1', 'caption' => 'Ruộng bậc thang & trải nghiệm Sapa' ),
			array( 'slot' => 'experience-2', 'caption' => 'Thác và bản làng Cát Cát' ),
			array( 'slot' => 'experience-3', 'caption' => 'Kiến trúc núi & làng bản' ),
			array( 'slot' => 'experience-4', 'caption' => 'Moana Sapa – Bàn tay vàng' ),
			array( 'slot' => 'experience-5', 'caption' => 'Săn mây Fansipan / Hoàng Liên Sơn' ),
			array( 'slot' => 'experience-6', 'caption' => 'Không khí Sapa về đêm' ),
		),
		'faq'           => array(
			array(
				'question' => 'Tour Sapa 3N2Đ là gì?',
				'answer'   => '3N2Đ = 3 ngày 2 đêm. Tour / du lịch Sapa 3 ngày 2 đêm Cát Cát – Fansipan – Moana, khởi hành hàng ngày từ Hà Nội.',
			),
			array(
				'question' => 'Giá tour Sapa 3 ngày 2 đêm bao nhiêu?',
				'answer'   => 'Từ 2.990.000đ/khách (KS 3 sao) và 3.990.000đ/khách (KS 4 sao). Đã gồm xe HN ⇄ Sapa; chưa gồm vé Fansipan và buffet Sapa Xưa.',
			),
			array(
				'question' => 'Giá tour đã gồm xe Hà Nội – Sapa chưa?',
				'answer'   => 'Có. Giá trọn gói đã gồm xe limousine hoặc cabin VIP Hà Nội ⇄ Sapa và xe đưa đón tham quan theo lịch trình.',
			),
			array(
				'question' => 'Vé Fansipan có trong giá không?',
				'answer'   => 'Không. Vé cáp treo, tàu hỏa Mường Hoa và tàu leo đỉnh Fansipan là chi phí tự túc theo quy định khu du lịch.',
			),
			array(
				'question' => 'Tour khởi hành khi nào?',
				'answer'   => 'Khởi hành hàng ngày từ Hà Nội. Nhân viên xác nhận giờ đón cụ thể khi giữ chỗ.',
			),
			array(
				'question' => 'Khách sạn 3 sao và 4 sao khác gì?',
				'answer'   => 'Cùng lịch trình; khác hạng lưu trú và giá: 3 sao từ 2.990.000đ/khách, 4 sao từ 3.990.000đ/khách (02 khách/phòng).',
			),
			array(
				'question' => 'Chính sách trẻ em thế nào?',
				'answer'   => 'Dưới 5 tuổi: miễn phí 01 bé/gia đình (ăn nghỉ tự lo). 5–dưới 9 tuổi: 75% người lớn. Từ 9 tuổi: 100%.',
			),
			array(
				'question' => 'Thứ tự điểm tham quan có đổi không?',
				'answer'   => 'Có thể điều chỉnh theo thực tế nhưng vẫn đảm bảo các nội dung chính của chương trình.',
			),
		),
		'final_cta'     => array(
			'title'    => 'Sẵn sàng giữ chỗ Tour Sapa 3 ngày 2 đêm?',
			'subtitle' => 'Du lịch Sapa 3N2Đ từ 2.990.000đ — đã gồm xe limo/cabin VIP HN ⇄ Sapa. Nhân viên xác nhận nhanh qua điện thoại hoặc Zalo.',
		),
		'related_tours' => array(
			'category_slug' => 'tour-sapa',
			'limit'         => -1,
			'title'         => 'Tour & Combo Sapa khác',
		),
		'seo'           => array(
			'title'       => 'Tour Sapa 3 Ngày 2 Đêm (3N2Đ) | Du Lịch Sapa Từ 2.990.000đ',
			'description' => 'Du lịch Sapa 3 ngày 2 đêm: Cát Cát – Fansipan – Moana. Giá tour Sapa từ 2.990.000đ, đã gồm xe limo/cabin VIP HN ⇄ Sapa. Khởi hành hàng ngày — giữ chỗ nhanh.',
		),
		'sections'      => array(
			'experience'    => true,
			'hero'          => true,
			'highlights'    => true,
			'pricing'       => true,
			'itinerary'     => true,
			'includes'      => true,
			'faq'           => true,
			'reviews'       => true,
			'related_tours' => true,
			'final_cta'     => true,
			'seo'           => true,
		),
	);

	if ( $page_id > 0 && '1' === (string) get_post_meta( $page_id, '_annam_tour_sapa_seo_disabled', true ) ) {
		$config['sections']['seo'] = false;
	}

	return apply_filters( 'annam_tour_sapa_landing_config', $config, $page_id );
}
