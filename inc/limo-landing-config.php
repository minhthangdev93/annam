<?php
/**
 * Cấu hình landing vé Limousine 11 chỗ Hà Nội ⇄ Sapa (giá niêm yết công khai).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Map giờ xuất phát theo chiều (key: from_to).
 *
 * @return array<string,string[]>
 */
function annam_limo_landing_get_schedule_times_map() {
	return array(
		'hanoi_sapa' => array( '07:00', '14:30' ),
		'sapa_hanoi' => array( '07:30', '14:30' ),
	);
}

/**
 * Giờ chạy theo chiều; không truyền from/to thì trả về toàn bộ giờ unique.
 *
 * @param string $from hanoi|sapa|''.
 * @param string $to   hanoi|sapa|''.
 * @return string[]
 */
function annam_limo_landing_departure_times( $from = '', $to = '' ) {
	$map  = annam_limo_landing_get_schedule_times_map();
	$from = sanitize_key( (string) $from );
	$to   = sanitize_key( (string) $to );

	if ( '' !== $from && '' !== $to ) {
		$key = $from . '_' . $to;
		return isset( $map[ $key ] ) ? $map[ $key ] : array();
	}

	$all = array();
	foreach ( $map as $times ) {
		foreach ( $times as $time ) {
			$all[ $time ] = $time;
		}
	}
	return array_values( $all );
}

/**
 * CTA landing Limousine — lấy hotline/Zalo An Nam Discovery (site-wide).
 *
 * @return array<string,string>
 */
function annam_limo_landing_get_cta() {
	$d = function_exists( 'annam_contact_get_details' ) ? annam_contact_get_details() : array();
	$mobile = isset( $d['mobile_display'] ) ? (string) $d['mobile_display'] : '0942471111';
	$mobile_digits = preg_replace( '/\D+/', '', $mobile );

	return apply_filters(
		'annam_limo_landing_cta',
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
 * URL trang cabin VIP (cross-sell từ limo).
 *
 * @return string
 */
function annam_limo_landing_get_cabin_upsell_base_url() {
	$page = get_page_by_path( 'dat-ve-xe-ha-noi-sapa' );
	$url  = ( $page instanceof WP_Post ) ? get_permalink( $page ) : home_url( '/dat-ve-xe-ha-noi-sapa/' );
	return (string) apply_filters( 'annam_limo_cabin_upsell_url', $url );
}

/**
 * Link giữ chỗ cabin kèm loại cabin (query) + hash form.
 *
 * @param string $cabin_type single_floor2|single_floor1|double|''.
 * @return string
 */
function annam_limo_landing_get_cabin_upsell_link( $cabin_type = '' ) {
	$url   = annam_limo_landing_get_cabin_upsell_base_url();
	$valid = array( 'single_floor2', 'single_floor1', 'double' );
	$type  = sanitize_key( (string) $cabin_type );
	if ( in_array( $type, $valid, true ) ) {
		$url = add_query_arg( 'cabin', $type, $url );
	}
	return $url . '#annam-cabin-booking';
}

/**
 * Danh sách cabin để upsell trên landing limo (từ config cabin VIP).
 *
 * @return array<int,array<string,mixed>>
 */
function annam_limo_landing_get_cabin_upsell_cards() {
	if ( ! function_exists( 'annam_cabin_landing_get_config' ) ) {
		return array();
	}
	$cabin_config = annam_cabin_landing_get_config();
	$cabins       = isset( $cabin_config['cabins'] ) && is_array( $cabin_config['cabins'] ) ? $cabin_config['cabins'] : array();
	return $cabins;
}

/**
 * Config đầy đủ landing Limousine HN–Sapa.
 *
 * @param int $page_id Page ID.
 * @return array<string,mixed>
 */
function annam_limo_landing_get_default_config( $page_id = 0 ) {
	$schedule_map  = annam_limo_landing_get_schedule_times_map();
	$times_hn_sapa = isset( $schedule_map['hanoi_sapa'] ) ? $schedule_map['hanoi_sapa'] : array( '07:00', '14:30' );
	$times_sapa_hn = isset( $schedule_map['sapa_hanoi'] ) ? $schedule_map['sapa_hanoi'] : array( '07:30', '14:30' );

	$config = array(
		'product_name'  => 'Vé Limousine 11 chỗ Hà Nội ⇄ Sapa',
		'hero'          => array(
			'title'      => 'Vé Limousine 11 Chỗ Hà Nội ⇄ Sapa',
			'subtitle'   => '2 chuyến mỗi chiều mỗi ngày (HN→Sapa 07:00 & 14:30 · Sapa→HN 07:30 & 14:30), khoảng 6 giờ, đón trả theo phạm vi tiêu chuẩn.',
			'price_from' => '450.000đ',
			'badges'     => array(
				'Limousine 11 chỗ (gồm ghế tài xế)',
				'HN→Sapa 07:00 & 14:30 · Sapa→HN 07:30 & 14:30',
				'Nhiều điểm đón trả HN ⇄ Sapa',
				'Giữ chỗ nhanh qua form / Zalo',
			),
		),
		'form'          => array(
			'title'           => 'Giữ Chỗ Limousine Trong 1 Phút',
			'subtitle'        => 'Điền thông tin — nhân viên gọi hoặc Zalo xác nhận trong giờ làm việc.',
			'submit_label'    => 'Gửi Yêu Cầu Giữ Chỗ',
			'footer_note'     => 'Không cần thanh toán online ngay. Nhân viên sẽ gọi/Zalo xác nhận trước.',
			'success_message' => 'Cảm ơn quý khách. An Nam Discovery đã nhận thông tin và sẽ liên hệ xác nhận vé sớm.',
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
				'label' => 'Bao nguyên xe (11 chỗ gồm tài xế)',
			),
		),
		'pricing'       => array(
			'rows'       => array(
				array(
					'type'  => 'seat_a',
					'label' => 'Ghế giữa',
					'desc'  => '06 ghế giữa xe — thoải mái, vị trí trung tâm (ghế khách).',
					'price' => '500.000đ/ghế/chiều',
					'badge' => 'Phổ biến',
				),
				array(
					'type'  => 'seat_b',
					'label' => 'Ghế đầu / cuối',
					'desc'  => '01 ghế đầu + 03 ghế cuối xe (ghế khách).',
					'price' => '450.000đ/ghế/chiều',
					'badge' => '',
				),
				array(
					'type'  => 'charter',
					'label' => 'Bao nguyên xe',
					'desc'  => 'Xe 11 chỗ gồm ghế tài xế — bao toàn bộ 10 ghế khách. Phù hợp nhóm / gia đình.',
					'price' => '4.500.000đ/xe/chiều',
					'badge' => 'Nhóm',
				),
			),
			'price_note' => 'Xe Limousine 11 chỗ đã bao gồm ghế tài xế; hành khách ngồi tối đa 10 ghế (06 ghế giữa + 01 ghế đầu + 03 ghế cuối). Giá niêm yết áp dụng cả hai chiều Hà Nội → Sapa và Sapa → Hà Nội trong phạm vi đón/trả tiêu chuẩn. Điểm ngoài phạm vi có thể phát sinh phụ thu (thông báo trước khi xác nhận).',
		),
		'schedules'     => array(
			array(
				'id'    => 'hanoi-sapa',
				'label' => 'Hà Nội → Sapa',
				'from'  => 'hanoi',
				'to'    => 'sapa',
				'times' => $times_hn_sapa,
			),
			array(
				'id'    => 'sapa-hanoi',
				'label' => 'Sapa → Hà Nội',
				'from'  => 'sapa',
				'to'    => 'hanoi',
				'times' => $times_sapa_hn,
			),
		),
		'timelines'     => array(
			'hanoi-sapa' => array(
				'heading' => 'Lộ trình Hà Nội → Sapa (mốc dự kiến)',
				'steps'   => array(
					array( 'place' => 'VP 214 Trần Quang Khải & khách sạn Phố Cổ', 'note' => 'Đón ~30–45 phút trước giờ xuất phát' ),
					array( 'place' => 'Nhà Hát Lớn', 'note' => 'Đón ~30–45 phút trước giờ xuất phát' ),
					array( 'place' => 'Rạp Xiếc Trung Ương', 'note' => 'Đón ~20 phút trước giờ xuất phát' ),
					array( 'place' => 'Mediamart 72 Trường Chinh', 'note' => 'Đón ~10 phút trước giờ xuất phát' ),
					array( 'place' => 'Sảnh Royal City', 'note' => 'Đúng giờ chuyến 07:00 hoặc 14:30' ),
					array( 'place' => 'VP 23 Tú Mỡ', 'note' => '~10 phút sau giờ xuất phát' ),
					array( 'place' => 'Lotte Mall Tây Hồ', 'note' => '~20 phút sau giờ xuất phát' ),
					array( 'place' => 'Sân bay Nội Bài', 'note' => '~40 phút sau giờ xuất phát' ),
					array( 'place' => 'TP. Lào Cai / VP IC19 Cốc San', 'note' => '~05 giờ sau giờ xuất phát' ),
					array( 'place' => 'Thị trấn Sapa', 'note' => '~06 giờ sau giờ xuất phát' ),
				),
			),
			'sapa-hanoi' => array(
				'heading' => 'Lộ trình Sapa → Hà Nội (mốc dự kiến)',
				'steps'   => array(
					array( 'place' => 'VP 697 Điện Biên Phủ & khu vực thị trấn Sapa', 'note' => 'KS trung tâm ~30–45 phút trước; VP ~15 phút trước giờ xuất phát' ),
					array( 'place' => 'Xuất phát đúng giờ', 'note' => '07:30 hoặc 14:30' ),
					array( 'place' => 'TP. Lào Cai & VP IC19 Cốc San', 'note' => '~30 phút sau giờ xuất phát' ),
					array( 'place' => 'Sân bay Nội Bài', 'note' => '~05 giờ hơn sau giờ xuất phát' ),
					array( 'place' => 'Nội thành Hà Nội', 'note' => '~06 giờ sau giờ xuất phát (các điểm đón/trả tiêu chuẩn)' ),
				),
			),
		),
		'pickup_tabs'   => array(
			array(
				'id'      => 'hanoi',
				'label'   => 'Hà Nội',
				'heading' => 'Đón/trả trong phạm vi tiêu chuẩn (giờ đón là dự kiến)',
				'items'   => array(
					array( 'name' => 'VP 214 Trần Quang Khải & khách sạn Phố Cổ', 'time' => '~30–45 phút trước giờ xuất phát' ),
					array( 'name' => 'Nhà Hát Lớn', 'time' => '~30–45 phút trước giờ xuất phát' ),
					array( 'name' => 'Rạp Xiếc Trung Ương', 'time' => '~20 phút trước giờ xuất phát' ),
					array( 'name' => 'Mediamart 72 Trường Chinh', 'time' => '~10 phút trước giờ xuất phát' ),
					array( 'name' => 'Sảnh Royal City', 'time' => 'Đúng giờ xuất phát' ),
					array( 'name' => 'VP 23 Tú Mỡ', 'time' => '~10 phút sau giờ xuất phát' ),
					array( 'name' => 'Lotte Mall Tây Hồ', 'time' => '~20 phút sau giờ xuất phát' ),
					array( 'name' => 'Sân bay Nội Bài', 'time' => '~40 phút sau giờ XP chiều đi · ~5 giờ hơn chiều về' ),
				),
				'note'    => 'Giờ đón thực tế được xác nhận trước chuyến. Có thể dùng xe trung chuyển nếu điểm đón ghép ngược hướng (đặc biệt khu Phố Cổ).',
			),
			array(
				'id'      => 'laocai',
				'label'   => 'Lào Cai',
				'heading' => 'Đón/trả khu vực Lào Cai',
				'items'   => array(
					array( 'name' => 'VP IC19 Cốc San (TP. Lào Cai)', 'time' => 'Chiều đi ~05 giờ sau XP · Chiều về ~30 phút sau XP' ),
				),
				'note'    => 'Xe trả/đón tại VP IC19 Cốc San theo lộ trình. Giờ thực tế xác nhận trước chuyến.',
			),
			array(
				'id'      => 'sapa',
				'label'   => 'Sapa',
				'heading' => 'Đón/trả tận nơi trong khu vực thị trấn',
				'items'   => array(
					array( 'name' => 'Khách sạn khu vực thị trấn Sapa', 'time' => 'Đón/trả tận nơi (~30–45 phút trước giờ XP chiều về)' ),
					array( 'name' => 'VP 697 Điện Biên Phủ (chiều về Hà Nội)', 'time' => '~15 phút trước giờ xuất phát' ),
				),
				'note'    => 'Ngoài phạm vi tiêu chuẩn có thể phát sinh phụ thu — báo trước khi xác nhận.',
			),
		),
		'gallery'       => array(
			array(
				'slot'    => 'gallery-exterior',
				'caption' => 'Ngoại thất Limousine 11 chỗ',
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
				'title' => 'Limousine 11 chỗ',
				'text'  => 'Xe 11 chỗ đã gồm ghế tài xế — tối đa 10 ghế khách, riêng tư hơn xe khách lớn cho hành trình ~6 giờ.',
			),
			array(
				'icon'  => 'clock',
				'title' => 'Lịch rõ ràng mỗi ngày',
				'text'  => 'HN→Sapa: 07:00 & 14:30 · Sapa→HN: 07:30 & 14:30 — hai chuyến mỗi chiều mỗi ngày.',
			),
			array(
				'icon'  => 'pin',
				'title' => 'Đón trả tiện lợi',
				'text'  => 'Nhiều điểm Hà Nội (Phố Cổ, Royal City, Nội Bài…); Lào Cai (IC19 Cốc San); Sapa đón trả khách sạn trong khu vực thị trấn.',
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
				'text'  => 'Hà Nội ⇄ Sapa: HN→Sapa 07:00/14:30, Sapa→HN 07:30/14:30 — chọn ghế giữa / ghế đầu–cuối hoặc bao xe.',
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
				'answer'   => 'Giá niêm yết: ghế giữa 500.000đ/ghế/chiều, ghế đầu/cuối 450.000đ/ghế/chiều, bao nguyên xe 4.500.000đ/xe/chiều (toàn bộ 10 ghế khách trên xe 11 chỗ gồm tài xế). Áp dụng cả hai chiều trong phạm vi đón/trả tiêu chuẩn.',
			),
			array(
				'question' => 'Ghế giữa và ghế đầu/cuối khác nhau thế nào?',
				'answer'   => 'Ghế giữa gồm 06 ghế ở giữa xe. Ghế đầu/cuối gồm 01 ghế đầu và 03 ghế cuối. Nhân viên sẽ xác nhận vị trí còn trống khi giữ chỗ.',
			),
			array(
				'question' => 'Xe chạy những giờ nào?',
				'answer'   => 'Hà Nội → Sapa: 07:00 và 14:30. Sapa → Hà Nội: 07:30 và 14:30. Mỗi chiều 02 chuyến mỗi ngày. Thời gian hành trình dự kiến khoảng 06 giờ (đã gồm nghỉ dọc đường).',
			),
			array(
				'question' => 'Đón trả ở đâu tại Hà Nội?',
				'answer'   => 'Hỗ trợ đón/trả tại VP 214 Trần Quang Khải & khách sạn Phố Cổ, Nhà Hát Lớn, Rạp Xiếc Trung Ương, Mediamart 72 Trường Chinh; mốc đúng giờ tại Sảnh Royal City; thêm VP 23 Tú Mỡ, Lotte Mall Tây Hồ và Sân bay Nội Bài theo lộ trình. Giờ đón là dự kiến và được xác nhận trước chuyến.',
			),
			array(
				'question' => 'Ở Sapa có đón tận khách sạn không?',
				'answer'   => 'Có hỗ trợ đón/trả tận nơi tại khách sạn trong khu vực thị trấn Sapa; chiều về có điểm tập kết VP 697 Điện Biên Phủ.',
			),
			array(
				'question' => 'Có dừng sân bay Nội Bài và Lào Cai không?',
				'answer'   => 'Có. Chiều Hà Nội → Sapa: Nội Bài khoảng 40 phút sau giờ xuất phát; TP. Lào Cai / VP IC19 Cốc San khoảng 05 giờ sau giờ xuất phát. Chiều Sapa → Hà Nội: Lào Cai khoảng 30 phút sau giờ xuất phát; Nội Bài khoảng 5 giờ hơn sau giờ xuất phát. Liên hệ để xác nhận nhu cầu lên/xuống cụ thể.',
			),
			array(
				'question' => 'Bao nguyên xe tính thế nào?',
				'answer'   => 'Bao nguyên xe theo một chiều Hà Nội → Sapa hoặc Sapa → Hà Nội, gồm toàn bộ 10 ghế khách trên xe Limousine 11 chỗ (đã gồm ghế tài xế). Phù hợp nhóm cần riêng tư toàn xe.',
			),
			array(
				'question' => 'Đón ngoài phạm vi có phụ thu không?',
				'answer'   => 'Có thể phát sinh phụ thu nếu điểm đón/trả ngoài khu vực tiêu chuẩn. Mức phụ thu báo trước khi xác nhận dịch vụ.',
			),
		),
		'final_cta'     => array(
			'title'    => 'Sẵn sàng giữ chỗ Limousine Hà Nội ⇄ Sapa?',
			'subtitle' => 'Chọn giờ theo chiều (HN→Sapa 07:00/14:30 · Sapa→HN 07:30/14:30) — nhân viên xác nhận ghế và điểm đón nhanh.',
		),
		'cabin_upsell'  => array(
			'title'        => 'Muốn nghỉ giường nằm? Chọn cabin VIP',
			'lead'         => 'Limousine 11 chỗ phù hợp ghế ngồi ban ngày. Nếu muốn ngủ trên đường, xem thêm xe cabin giường nằm Hà Nội ⇄ Sapa — giá từ trang đặt vé cabin.',
			'footer_label' => 'Xem đầy đủ lịch & điểm đón cabin',
		),
		'anchors'       => array(
			array( 'id' => 'gia-ve', 'label' => 'Bảng giá' ),
			array( 'id' => 'lich-xe', 'label' => 'Lịch xe' ),
			array( 'id' => 'diem-don', 'label' => 'Đón trả' ),
			array( 'id' => 'anh-xe', 'label' => 'Ảnh xe' ),
			array( 'id' => 'cabin-giuong-nam', 'label' => 'Cabin' ),
			array( 'id' => 'faq', 'label' => 'FAQ' ),
		),
		'sections'      => array(
			'hero'         => true,
			'pricing'      => true,
			'schedule'     => true,
			'pickup'       => true,
			'gallery'      => true,
			'video'        => true,
			'why'          => true,
			'cabin_upsell' => true,
			'steps'        => true,
			'faq'          => true,
			'final_cta'    => true,
			'seo'          => true,
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
