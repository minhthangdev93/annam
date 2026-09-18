<?php
/**
 * Cấu hình landing vé Limousine 10 chỗ Hà Nội ⇄ Sapa (giá niêm yết công khai).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Giờ chạy cố định mỗi chiều.
 *
 * @return string[]
 */
function annam_limo_landing_departure_times() {
	return array( '07:00', '14:00' );
}

/**
 * Map giờ cho JS (key: from_to).
 *
 * @return array<string,string[]>
 */
function annam_limo_landing_get_schedule_times_map() {
	$times = annam_limo_landing_departure_times();
	return array(
		'hanoi_sapa' => $times,
		'sapa_hanoi' => $times,
	);
}

/**
 * CTA landing Limousine — S trip Việt Nam (không lấy hotline site An Nam).
 *
 * @return array<string,string>
 */
function annam_limo_landing_get_cta() {
	return apply_filters(
		'annam_limo_landing_cta',
		array(
			'brand'             => 'S trip Việt Nam',
			'hotline_display'   => '1900 888 828',
			'hotline_tel'       => 'tel:1900888828',
			'hotline2_display'  => '0523 031 111',
			'hotline2_tel'      => 'tel:0523031111',
			'zalo_url'          => 'https://zalo.me/0523031111',
		)
	);
}

/**
 * Config đầy đủ landing Limousine HN–Sapa.
 *
 * @param int $page_id Page ID.
 * @return array<string,mixed>
 */
function annam_limo_landing_get_default_config( $page_id = 0 ) {
	$times = annam_limo_landing_departure_times();

	$config = array(
		'product_name' => 'Vé Limousine 10 chỗ Hà Nội ⇄ Sapa',
		'hero'         => array(
			'title'      => 'Vé Limousine 10 Chỗ Hà Nội ⇄ Sapa',
			'subtitle'   => '2 chuyến mỗi chiều mỗi ngày (07:00 & 14:00), khoảng 6 giờ, đón trả theo phạm vi tiêu chuẩn.',
			'price_from' => '450.000đ',
			'badges'     => array(
				'Limousine 10 chỗ',
				'07:00 & 14:00 hằng ngày',
				'Nhiều điểm đón trả HN ⇄ Sapa',
				'Giữ chỗ nhanh qua form / Zalo',
			),
		),
		'form'          => array(
			'title'           => 'Giữ Chỗ Limousine Trong 1 Phút',
			'subtitle'        => 'Điền thông tin — nhân viên gọi hoặc Zalo xác nhận trong giờ làm việc.',
			'submit_label'    => 'Gửi Yêu Cầu Giữ Chỗ',
			'footer_note'     => 'Không cần thanh toán online ngay. Nhân viên sẽ gọi/Zalo xác nhận trước.',
			'success_message' => 'Cảm ơn quý khách. S trip Việt Nam đã nhận thông tin và sẽ liên hệ xác nhận vé sớm.',
		),
		'form_defaults' => array(
			'from' => 'hanoi',
			'to'   => 'sapa',
			'seat' => 'seat_a',
			'time' => '07:00',
		),
		'seat_types'    => array(
			array(
				'value' => 'seat_a',
				'label' => 'Ghế giữa',
			),
			array(
				'value' => 'seat_b',
				'label' => 'Ghế đầu / cuối',
			),
			array(
				'value' => 'charter',
				'label' => 'Bao nguyên xe 10 chỗ',
			),
		),
		'pricing'       => array(
			'rows'       => array(
				array(
					'type'  => 'seat_a',
					'label' => 'Ghế giữa',
					'desc'  => '06 ghế giữa xe — thoải mái, vị trí trung tâm.',
					'price' => '500.000đ/ghế/chiều',
					'badge' => 'Phổ biến',
				),
				array(
					'type'  => 'seat_b',
					'label' => 'Ghế đầu / cuối',
					'desc'  => '01 ghế đầu + 03 ghế cuối xe.',
					'price' => '450.000đ/ghế/chiều',
					'badge' => '',
				),
				array(
					'type'  => 'charter',
					'label' => 'Bao nguyên xe',
					'desc'  => 'Toàn bộ xe 10 chỗ — phù hợp nhóm / gia đình.',
					'price' => '4.200.000đ/xe/chiều',
					'badge' => 'Nhóm',
				),
			),
			'price_note' => 'Giá niêm yết áp dụng cả hai chiều Hà Nội → Sapa và Sapa → Hà Nội trong phạm vi đón/trả tiêu chuẩn. Điểm ngoài phạm vi có thể phát sinh phụ thu (thông báo trước khi xác nhận).',
		),
		'schedules'     => array(
			array(
				'id'    => 'hanoi-sapa',
				'label' => 'Hà Nội → Sapa',
				'from'  => 'hanoi',
				'to'    => 'sapa',
				'times' => $times,
			),
			array(
				'id'    => 'sapa-hanoi',
				'label' => 'Sapa → Hà Nội',
				'from'  => 'sapa',
				'to'    => 'hanoi',
				'times' => $times,
			),
		),
		'timelines'     => array(
			'hanoi-sapa' => array(
				'heading' => 'Lộ trình Hà Nội → Sapa (mốc dự kiến)',
				'steps'   => array(
					array( 'place' => 'Đón nội thành Hà Nội', 'note' => 'Trước giờ xuất phát tùy điểm' ),
					array( 'place' => 'Cầu vượt Vĩnh Ngọc', 'note' => 'Đúng giờ chuyến 07:00 hoặc 14:00' ),
					array( 'place' => 'Sân bay Nội Bài', 'note' => '~20 phút sau giờ xuất phát' ),
					array( 'place' => 'TP. Lào Cai (VP IC19 Cốc San)', 'note' => '~05 giờ sau giờ xuất phát' ),
					array( 'place' => 'Thị trấn Sapa', 'note' => '~06 giờ sau giờ xuất phát' ),
				),
			),
			'sapa-hanoi' => array(
				'heading' => 'Lộ trình Sapa → Hà Nội (mốc dự kiến)',
				'steps'   => array(
					array( 'place' => 'Đón thị trấn Sapa / VP 697 Điện Biên Phủ', 'note' => 'Bắt đầu đón ~30–45 phút trước giờ' ),
					array( 'place' => 'Xuất phát đúng giờ', 'note' => '07:00 hoặc 14:00' ),
					array( 'place' => 'TP. Lào Cai', 'note' => '~01 giờ sau giờ xuất phát' ),
					array( 'place' => 'Sân bay Nội Bài', 'note' => '~05 giờ sau giờ xuất phát' ),
					array( 'place' => 'Nội thành Hà Nội', 'note' => '~06 giờ sau giờ xuất phát' ),
				),
			),
		),
		'pickup_tabs'   => array(
			array(
				'id'      => 'hanoi',
				'label'   => 'Hà Nội',
				'heading' => 'Đón/trả miễn phí trong phạm vi tiêu chuẩn (giờ đón là dự kiến)',
				'items'   => array(
					array( 'name' => 'Khách sạn khu vực Phố Cổ', 'time' => '~30–45 phút trước giờ xuất phát' ),
					array( 'name' => 'VP 51 Minh Khai', 'time' => '~30–60 phút trước giờ xuất phát' ),
					array( 'name' => 'VP 56 Phố Vọng', 'time' => '~30–45 phút trước giờ xuất phát' ),
					array( 'name' => 'Rạp Xiếc Trung Ương', 'time' => '~30–45 phút trước giờ xuất phát' ),
					array( 'name' => 'VP 214 Trần Quang Khải', 'time' => '~25–30 phút trước giờ xuất phát' ),
					array( 'name' => 'VP 80 Hồng Tiến', 'time' => '~15–20 phút trước giờ xuất phát' ),
					array( 'name' => '72 Trường Chinh', 'time' => '~30–45 phút trước giờ xuất phát' ),
					array( 'name' => 'VP 23 Tú Mỡ', 'time' => '~30 phút trước giờ xuất phát' ),
					array( 'name' => 'Công viên Hòa Bình', 'time' => '~20 phút trước giờ xuất phát' ),
					array( 'name' => 'Lotte Mall Tây Hồ – 96 Võ Chí Công', 'time' => '~10 phút trước giờ xuất phát' ),
					array( 'name' => 'Cầu vượt Vĩnh Ngọc', 'time' => 'Đúng giờ xuất phát' ),
				),
				'note'    => 'Giờ đón thực tế được xác nhận trước chuyến. Có thể dùng xe trung chuyển nếu điểm đón ghép ngược hướng (đặc biệt khu Phố Cổ).',
			),
			array(
				'id'      => 'sapa',
				'label'   => 'Sapa',
				'heading' => 'Đón/trả tận nơi trong phạm vi thị trấn',
				'items'   => array(
					array( 'name' => 'Khách sạn trong bán kính 5 km thị trấn Sapa', 'time' => 'Hỗ trợ đón/trả tận nơi' ),
					array( 'name' => 'VP 697 Điện Biên Phủ (chiều về Hà Nội)', 'time' => 'Điểm tập kết / bắt đầu đón' ),
				),
				'note'    => 'Ngoài phạm vi tiêu chuẩn có thể phát sinh phụ thu — báo trước khi xác nhận.',
			),
		),
		'gallery'       => array(
			array(
				'slot'    => 'gallery-exterior',
				'caption' => 'Ngoại thất Limousine 10 chỗ',
			),
			array(
				'slot'    => 'gallery-interior',
				'caption' => 'Không gian ghế Limousine',
			),
			array(
				'slot'    => 'gallery-seat',
				'caption' => 'Ghế ngồi thoải mái',
			),
			array(
				'slot'    => 'gallery-detail',
				'caption' => 'Chi tiết tiện nghi trên xe',
			),
			array(
				'slot'    => 'gallery-ready',
				'caption' => 'Xe sẵn sàng xuất phát',
			),
			array(
				'slot'    => 'gallery-road',
				'caption' => 'Trên đường Hà Nội ⇄ Sapa',
			),
		),
		'video'         => array(
			'title' => 'Xem Xe & Hành Trình Thật',
			'lead'  => 'Video giúp quý khách hình dung rõ hơn về xe Limousine và trải nghiệm trên tuyến Hà Nội ⇄ Sapa.',
		),
		'why_cards'     => array(
			array(
				'icon'  => 'van',
				'title' => 'Limousine 10 chỗ',
				'text'  => 'Ít chỗ hơn xe khách lớn — không gian riêng tư, ghế ngồi êm cho hành trình ~6 giờ.',
			),
			array(
				'icon'  => 'clock',
				'title' => 'Lịch rõ ràng mỗi ngày',
				'text'  => 'Hai khung giờ cố định 07:00 và 14:00 cho cả chiều đi và chiều về.',
			),
			array(
				'icon'  => 'pin',
				'title' => 'Đón trả tiện lợi',
				'text'  => 'Phố Cổ & nhiều điểm Hà Nội; Sapa đón trả khách sạn trong 5 km thị trấn.',
			),
			array(
				'icon'  => 'zap',
				'title' => 'Giữ chỗ nhanh',
				'text'  => 'Gửi form hoặc Zalo — nhân viên xác nhận ghế/chuyến trước khi thanh toán.',
			),
		),
		'steps'         => array(
			array(
				'title' => 'Chọn tuyến & giờ',
				'text'  => 'Hà Nội ⇄ Sapa, chuyến 07:00 hoặc 14:00, chọn ghế giữa / ghế đầu–cuối hoặc bao xe.',
			),
			array(
				'title' => 'Gửi yêu cầu giữ chỗ',
				'text'  => 'Điền họ tên, SĐT/Zalo và điểm đón mong muốn.',
			),
			array(
				'title' => 'Xác nhận & lên xe',
				'text'  => 'Nhân viên gọi/Zalo báo giờ đón dự kiến và hướng dẫn thanh toán.',
			),
		),
		'faq'           => array(
			array(
				'question' => 'Vé Limousine Hà Nội Sapa giá bao nhiêu?',
				'answer'   => 'Giá niêm yết: ghế giữa 500.000đ/ghế/chiều, ghế đầu/cuối 450.000đ/ghế/chiều, bao nguyên xe 4.200.000đ/xe/chiều. Áp dụng cả hai chiều trong phạm vi đón/trả tiêu chuẩn.',
			),
			array(
				'question' => 'Ghế giữa và ghế đầu/cuối khác nhau thế nào?',
				'answer'   => 'Ghế giữa gồm 06 ghế ở giữa xe. Ghế đầu/cuối gồm 01 ghế đầu và 03 ghế cuối. Nhân viên sẽ xác nhận vị trí còn trống khi giữ chỗ.',
			),
			array(
				'question' => 'Xe chạy những giờ nào?',
				'answer'   => 'Mỗi chiều 02 chuyến mỗi ngày: 07:00 và 14:00. Thời gian hành trình dự kiến khoảng 06 giờ (đã gồm nghỉ dọc đường).',
			),
			array(
				'question' => 'Đón trả ở đâu tại Hà Nội?',
				'answer'   => 'Hỗ trợ đón/trả miễn phí tại Phố Cổ và các điểm như Minh Khai, Phố Vọng, Rạp Xiếc, Trường Chinh, Tú Mỡ, CV Hòa Bình, Lotte Tây Hồ; mốc đúng giờ tại Cầu vượt Vĩnh Ngọc. Giờ đón là dự kiến và được xác nhận trước chuyến.',
			),
			array(
				'question' => 'Ở Sapa có đón tận khách sạn không?',
				'answer'   => 'Có hỗ trợ đón/trả tận nơi tại khách sạn trong phạm vi khoảng 5 km khu vực thị trấn Sapa.',
			),
			array(
				'question' => 'Có dừng sân bay Nội Bài không?',
				'answer'   => 'Trên lộ trình có mốc qua Nội Bài (chiều đi khoảng 20 phút sau giờ xuất phát; chiều về khoảng 5 giờ sau giờ xuất phát). Liên hệ để xác nhận nhu cầu lên/xuống cụ thể.',
			),
			array(
				'question' => 'Bao nguyên xe tính thế nào?',
				'answer'   => 'Giá bao nguyên xe theo một chiều Hà Nội → Sapa hoặc Sapa → Hà Nội. Phù hợp nhóm cần riêng tư toàn xe.',
			),
			array(
				'question' => 'Đón ngoài phạm vi có phụ thu không?',
				'answer'   => 'Có thể phát sinh phụ thu nếu điểm đón/trả ngoài khu vực tiêu chuẩn. Mức phụ thu báo trước khi xác nhận dịch vụ.',
			),
		),
		'final_cta'     => array(
			'title'    => 'Sẵn sàng giữ chỗ Limousine Hà Nội ⇄ Sapa?',
			'subtitle' => 'Chọn giờ 07:00 hoặc 14:00 — nhân viên xác nhận ghế và điểm đón nhanh.',
		),
		'anchors'       => array(
			array( 'id' => 'gia-ve', 'label' => 'Bảng giá' ),
			array( 'id' => 'lich-xe', 'label' => 'Lịch xe' ),
			array( 'id' => 'diem-don', 'label' => 'Đón trả' ),
			array( 'id' => 'anh-xe', 'label' => 'Ảnh xe' ),
			array( 'id' => 'faq', 'label' => 'FAQ' ),
		),
		'sections'      => array(
			'hero'     => true,
			'pricing'  => true,
			'schedule' => true,
			'pickup'   => true,
			'gallery'  => true,
			'video'    => true,
			'why'      => true,
			'steps'    => true,
			'faq'      => true,
			'final_cta'=> true,
			'seo'      => true,
		),
	);

	if ( $page_id > 0 ) {
		$custom_title = get_post_meta( $page_id, '_annam_limo_hero_title', true );
		if ( is_string( $custom_title ) && '' !== trim( $custom_title ) ) {
			$config['hero']['title'] = sanitize_text_field( $custom_title );
		}
		if ( '1' === (string) get_post_meta( $page_id, '_annam_limo_seo_disabled', true ) ) {
			$config['sections']['seo'] = false;
		}
	}

	if ( ! empty( $config['gallery'] ) && is_array( $config['gallery'] ) && function_exists( 'annam_limo_landing_get_image_caption' ) ) {
		foreach ( $config['gallery'] as $gi => $gitem ) {
			$slot = isset( $gitem['slot'] ) ? (string) $gitem['slot'] : '';
			if ( '' === $slot ) {
				continue;
			}
			$cap = annam_limo_landing_get_image_caption( $slot );
			if ( '' !== $cap ) {
				$config['gallery'][ $gi ]['caption'] = $cap;
			}
		}
	}

	return apply_filters( 'annam_limo_landing_config', $config, $page_id );
}
