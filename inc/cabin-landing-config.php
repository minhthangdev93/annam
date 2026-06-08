<?php
/**
 * Cấu hình mặc định landing xe Cabin VIP (có thể ghi đè qua filter / post meta).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tạo danh sách giờ cách đều 1 tiếng.
 *
 * @param int $start_h Giờ bắt đầu.
 * @param int $start_m Phút bắt đầu.
 * @param int $end_h   Giờ kết thúc.
 * @param int $end_m   Phút kết thúc.
 * @return string[]
 */
function annam_cabin_landing_build_hourly_times( $start_h, $start_m, $end_h, $end_m ) {
	$times   = array();
	$current = ( (int) $start_h * 60 ) + (int) $start_m;
	$end     = ( (int) $end_h * 60 ) + (int) $end_m;

	while ( $current <= $end ) {
		$h       = (int) floor( $current / 60 );
		$m       = $current % 60;
		$times[] = sprintf( '%02d:%02d', $h, $m );
		$current += 60;
	}

	return $times;
}

/**
 * Hà Nội → Sapa / Lào Cai: 06:30–23:30, mỗi tiếng một chuyến.
 *
 * @return string[]
 */
function annam_cabin_landing_departure_times_hanoi_outbound() {
	return annam_cabin_landing_build_hourly_times( 6, 30, 23, 30 );
}

/**
 * Sapa / Lào Cai → Hà Nội: 06:00–24:00, mỗi tiếng một chuyến.
 *
 * @return string[]
 */
function annam_cabin_landing_departure_times_hanoi_return() {
	return annam_cabin_landing_build_hourly_times( 6, 0, 24, 0 );
}

/**
 * Giờ chạy theo điểm đi / điểm đến.
 *
 * @param string $from hanoi|sapa|laocai.
 * @param string $to   hanoi|sapa|laocai.
 * @return string[]
 */
function annam_cabin_landing_get_departure_times_for_places( $from, $to ) {
	$from = sanitize_key( (string) $from );
	$to   = sanitize_key( (string) $to );

	if ( 'hanoi' === $from && in_array( $to, array( 'sapa', 'laocai' ), true ) ) {
		return annam_cabin_landing_departure_times_hanoi_outbound();
	}
	if ( 'hanoi' === $to && in_array( $from, array( 'sapa', 'laocai' ), true ) ) {
		return annam_cabin_landing_departure_times_hanoi_return();
	}

	return annam_cabin_landing_departure_times_hanoi_outbound();
}

/**
 * Lịch cố định cho section “Lịch Xe Cabin VIP” (xem tổng quan trong ngày).
 * Không dùng cho validation form / lọc “còn chỗ”.
 *
 * @return string[]
 */
function annam_cabin_landing_public_timetable_hanoi_outbound() {
	return array(
		'06:30', '07:30', '08:30', '09:30', '10:30', '11:30', '12:30', '13:30', '14:30', '15:30',
		'16:30', '17:30', '18:30', '19:30', '20:30', '21:30', '22:30', '23:30',
	);
}

/**
 * @return string[]
 */
function annam_cabin_landing_public_timetable_return() {
	return array(
		'06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00',
		'16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00', '24:00',
	);
}

/**
 * Giờ hiển thị trên bảng lịch theo điểm đi/đến.
 *
 * @param string $from hanoi|sapa|laocai.
 * @param string $to   hanoi|sapa|laocai.
 * @return string[]
 */
function annam_cabin_landing_get_public_timetable_times_for_places( $from, $to ) {
	$from = sanitize_key( (string) $from );
	$to   = sanitize_key( (string) $to );

	if ( 'hanoi' === $from && in_array( $to, array( 'sapa', 'laocai' ), true ) ) {
		return annam_cabin_landing_public_timetable_hanoi_outbound();
	}
	if ( 'hanoi' === $to && in_array( $from, array( 'sapa', 'laocai' ), true ) ) {
		return annam_cabin_landing_public_timetable_return();
	}

	return annam_cabin_landing_public_timetable_hanoi_outbound();
}

/**
 * Map giờ chạy cho JS (key: from_to).
 *
 * @return array<string,string[]>
 */
function annam_cabin_landing_get_schedule_times_map() {
	$pairs = array(
		array( 'hanoi', 'sapa' ),
		array( 'hanoi', 'laocai' ),
		array( 'sapa', 'hanoi' ),
		array( 'laocai', 'hanoi' ),
	);
	$map   = array();

	foreach ( $pairs as $pair ) {
		$key         = $pair[0] . '_' . $pair[1];
		$map[ $key ] = annam_cabin_landing_get_departure_times_for_places( $pair[0], $pair[1] );
	}

	return $map;
}

/**
 * CTA toàn site (hotline, Zalo, WhatsApp).
 *
 * @return array<string,string>
 */
function annam_cabin_landing_get_cta() {
	$d = function_exists( 'annam_contact_get_details' ) ? annam_contact_get_details() : array();

	return apply_filters(
		'annam_cabin_landing_cta',
		array(
			'hotline_display' => isset( $d['hotline_display'] ) ? $d['hotline_display'] : '1900 8164',
			'hotline_tel'     => isset( $d['hotline_tel'] ) ? $d['hotline_tel'] : 'tel:19008164',
			'zalo_url'        => isset( $d['zalo_url'] ) ? $d['zalo_url'] : 'http://zalo.me/2127942034358673568',
			'whatsapp_url'    => isset( $d['whatsapp_url'] ) ? $d['whatsapp_url'] : 'https://wa.me/+84942471111',
			'email'           => isset( $d['email'] ) ? $d['email'] : 'annamdiscoveryvn@gmail.com',
		)
	);
}

/**
 * Config đầy đủ cho landing Cabin VIP 22 phòng.
 *
 * @param int $page_id ID trang (0 = chỉ default).
 * @return array<string,mixed>
 */
function annam_cabin_landing_get_default_config( $page_id = 0 ) {
	$schedule_times_out = annam_cabin_landing_public_timetable_hanoi_outbound();
	$schedule_times_in  = annam_cabin_landing_public_timetable_return();

	$config = array(
		'product_name' => 'Xe Cabin VIP 22 phòng',
		'hero'         => array(
			'title'       => 'Đặt Vé Xe Cabin VIP 22 Phòng Hà Nội ⇄ Sapa',
			'subtitle'    => 'Giá từ 420.000đ, cabin riêng tư, có nhân viên xác nhận vé nhanh',
			'price_from'  => '420.000đ',
			'badges'      => array(
				'Cabin đơn / cabin đôi',
				'Lịch chạy hằng ngày',
				'Hỗ trợ điểm đón/trả',
				'Giữ chỗ nhanh qua Zalo',
			),
		),
		'form'         => array(
			'title'            => 'Giữ Chỗ Cabin Trong 1 Phút',
			'subtitle'         => 'Điền thông tin — nhân viên gọi hoặc Zalo xác nhận trong giờ làm việc.',
			'submit_label'     => 'Gửi Yêu Cầu Giữ Chỗ',
			'footer_note'      => 'Không cần thanh toán online ngay. Nhân viên sẽ gọi/Zalo xác nhận trước.',
			'success_message'  => 'Cảm ơn quý khách. An Nam Discovery đã nhận thông tin và sẽ liên hệ xác nhận vé sớm.',
		),
		'form_defaults' => array(
			'from'       => 'hanoi',
			'to'         => 'sapa',
			'cabin_type' => 'single_floor2',
		),
		'routes'       => array(
			array(
				'id'    => 'hanoi-sapa',
				'label' => 'Hà Nội ⇄ Sapa',
			),
			array(
				'id'    => 'hanoi-laocai',
				'label' => 'Hà Nội ⇄ Lào Cai',
			),
		),
		'route_options' => array(
			array( 'value' => 'hanoi', 'label' => 'Hà Nội' ),
			array( 'value' => 'sapa', 'label' => 'Sapa' ),
			array( 'value' => 'laocai', 'label' => 'Lào Cai' ),
		),
		'cabin_types'  => array(
			array(
				'value'       => 'single_floor2',
				'label'       => 'Cabin đơn tầng 2',
				'short_label' => 'Cabin đơn tầng 2',
			),
			array(
				'value'       => 'single_floor1',
				'label'       => 'Cabin đơn tầng 1',
				'short_label' => 'Cabin đơn tầng 1',
			),
			array(
				'value'       => 'double',
				'label'       => 'Cabin đôi',
				'short_label' => 'Cabin đôi',
			),
		),
		'pricing'      => array(
			'hanoi-sapa'    => array(
				array(
					'type'  => 'single_floor2',
					'label' => 'Cabin đơn tầng 2',
					'price' => '420.000đ',
					'desc'  => 'Tiết kiệm, cabin riêng có rèm che — phù hợp đi một mình.',
				),
				array(
					'type'  => 'single_floor1',
					'label' => 'Cabin đơn tầng 1',
					'price' => '500.000đ',
					'desc'  => 'Dễ lên xuống, thoải mái hơn cho hành trình dài.',
				),
				array(
					'type'  => 'double',
					'label' => 'Cabin đôi',
					'price' => '720.000đ',
					'desc'  => 'Rộng rãi cho 2 khách — được chọn nhiều nhất.',
					'badge' => 'Phổ biến',
				),
			),
			'hanoi-laocai'  => array(
				array(
					'type'  => 'single_floor2',
					'label' => 'Cabin đơn tầng 2',
					'price' => '350.000đ',
					'desc'  => 'Giá tốt cho tuyến Hà Nội — Lào Cai.',
				),
				array(
					'type'  => 'single_floor1',
					'label' => 'Cabin đơn tầng 1',
					'price' => '400.000đ',
					'desc'  => 'Cabin riêng tầng 1, thuận tiện di chuyển.',
				),
				array(
					'type'  => 'double',
					'label' => 'Cabin đôi',
					'price' => '650.000đ',
					'desc'  => '2 khách cùng khoang — tiện cho cặp đôi.',
					'badge' => 'Phổ biến',
				),
			),
			'price_note'    => 'Giá vé có thể thay đổi theo ngày cao điểm, cuối tuần hoặc dịp lễ. Vui lòng liên hệ để kiểm tra cabin còn trống.',
		),
		'schedules'    => array(
			array( 'id' => 'hanoi-sapa', 'label' => 'Hà Nội → Sapa', 'from' => 'hanoi', 'to' => 'sapa', 'times' => $schedule_times_out ),
			array( 'id' => 'sapa-hanoi', 'label' => 'Sapa → Hà Nội', 'from' => 'sapa', 'to' => 'hanoi', 'times' => $schedule_times_in ),
			array( 'id' => 'hanoi-laocai', 'label' => 'Hà Nội → Lào Cai', 'from' => 'hanoi', 'to' => 'laocai', 'times' => $schedule_times_out ),
			array( 'id' => 'laocai-hanoi', 'label' => 'Lào Cai → Hà Nội', 'from' => 'laocai', 'to' => 'hanoi', 'times' => $schedule_times_in ),
		),
		'cabins'       => array(
			array(
				'type'        => 'single_floor2',
				'name'        => 'Cabin đơn tầng 2',
				'price_from'  => '420.000đ',
				'description' => 'Phù hợp khách đi một mình, muốn tiết kiệm chi phí nhưng vẫn có không gian nghỉ ngơi riêng tư.',
				'image'       => 'cabin-single-2',
				'tag'         => 'Tiết kiệm',
				'featured'    => false,
			),
			array(
				'type'        => 'single_floor1',
				'name'        => 'Cabin đơn tầng 1',
				'price_from'  => '500.000đ',
				'description' => 'Phù hợp khách muốn vị trí thuận tiện hơn, dễ lên xuống, ưu tiên sự thoải mái trong hành trình.',
				'image'       => 'cabin-single-1',
				'tag'         => 'Thuận tiện',
				'featured'    => false,
			),
			array(
				'type'        => 'double',
				'name'        => 'Cabin đôi',
				'price_from'  => '720.000đ',
				'description' => 'Phù hợp cặp đôi, gia đình có trẻ nhỏ hoặc 2 khách muốn nghỉ chung một khoang rộng rãi hơn.',
				'image'       => 'cabin-double',
				'tag'         => 'Được chọn nhiều',
				'featured'    => true,
			),
		),
		'pickup_tabs'  => array(
			array(
				'id'            => 'hanoi-sapa',
				'label'         => 'Hà Nội ⇄ Sapa',
				'heading'       => 'Điểm đón và điểm trả phổ biến tại Hà Nội và Sapa',
				'pickup_title'  => 'Điểm đón tại Hà Nội',
				'pickup'        => array(
					'80 Hồng Tiến, Bồ Đề, Long Biên',
					'214 Trần Quang Khải, phường Hoàn Kiếm',
					'23 P. Tú Mỡ, Phường Yên Hòa',
					'Công viên Hòa Bình',
					'Điểm dừng đỗ Thôn Bầu (Bus Bầu)',
					'Rạp Xiếc Trung Ương',
					'Sân bay Nội Bài (Ga Nội Địa)',
					'Sân bay Nội Bài (Ga Quốc Tế)',
					'Số 51 Minh Khai, Phường Bạch Mai',
					'Số 56 phố Vọng, Phường Bạch Mai',
					'Tận nơi Phố Cổ',
				),
				'dropoff_title' => 'Điểm trả tại Sapa',
				'dropoff'       => array(
					'697 Điện Biên Phủ, Sapa',
					'Tận Nơi Trung Tâm Thị Trấn Sapa',
				),
			),
			array(
				'id'            => 'hanoi-laocai',
				'label'         => 'Hà Nội ⇄ Lào Cai',
				'heading'       => 'Điểm đón và điểm trả phổ biến tại Hà Nội và Lào Cai',
				'pickup_title'  => 'Điểm đón tại Hà Nội',
				'pickup'        => array(
					'80 Hồng Tiến, Bồ Đề, Long Biên',
					'214 Trần Quang Khải, phường Hoàn Kiếm',
					'23 P. Tú Mỡ, Phường Yên Hòa',
					'Điểm dừng đỗ Thôn Bầu (Bus Bầu)',
					'Rạp Xiếc Trung Ương',
					'Sân bay Nội Bài (Ga Nội Địa)',
					'Sân bay Nội Bài (Ga Quốc Tế)',
					'Số 51 Minh Khai, Phường Bạch Mai',
					'Số 56 phố Vọng, Phường Bạch Mai',
					'Tận nơi Phố Cổ',
				),
				'dropoff_title' => 'Điểm trả tại Lào Cai',
				'dropoff'       => array(
					'Số 055A đường Nguyễn Huệ, Lào Cai',
					'Nút Giao IC19, cao tốc NB-LC, xã Cốc San, Lào Cai',
					'Tận nơi Lào Cai (BK 5km)',
				),
			),
		),
		'pickup_note'  => 'Nếu đi từ 3 khách trở lên, vui lòng liên hệ để được hỗ trợ đón, trả tận nơi tại Hà Nội theo khu vực được hỗ trợ.',
		'gallery'      => array(
			array( 'caption' => 'Nội thất cabin VIP — không gian riêng tư', 'image' => 'gallery-hero' ),
			array( 'caption' => 'Cabin đơn — giường nằm rộng', 'image' => 'gallery-bed' ),
			array( 'caption' => 'Cabin đôi — 2 khách thoải mái', 'image' => 'gallery-double' ),
			array( 'caption' => 'Ngoại thất xe cabin hiện đại', 'image' => 'gallery-clean' ),
			array( 'caption' => 'Tiện ích sạc & điều hòa trong cabin', 'image' => 'gallery-amenity' ),
		),
		'amenities'    => array(
			array( 'title' => 'Cabin riêng tư', 'description' => 'Không gian nghỉ ngơi tách biệt, phù hợp đi đêm hoặc hành trình dài.' ),
			array( 'title' => 'Rèm che kín đáo', 'description' => 'Tăng riêng tư khi nghỉ trên xe.' ),
			array( 'title' => 'Giường nằm êm ái', 'description' => 'Thoải mái hơn ghế ngồi thông thường.' ),
			array( 'title' => 'Chăn gối sạch sẽ', 'description' => 'Chuẩn bị cho hành trình qua đêm.' ),
			array( 'title' => 'Điều hòa mát', 'description' => 'Duy trì nhiệt độ dễ chịu trong cabin.' ),
			array( 'title' => 'Wifi', 'description' => 'Kết nối tùy tuyến và nhà xe (không cam kết tuyệt đối).' ),
			array( 'title' => 'Cổng sạc USB/Type C', 'description' => 'Sạc điện thoại trong hành trình.' ),
			array( 'title' => 'Nước uống', 'description' => 'Theo chính sách nhà xe từng chuyến.' ),
			array( 'title' => 'Khoang hành lý', 'description' => 'Hành lý theo quy định nhà xe.' ),
		),
		'why_cards'    => array(
			array(
				'title' => 'Có nhân viên xác nhận vé',
				'text'  => 'Không đặt mù — luôn có người kiểm tra cabin trống và gọi lại cho bạn.',
			),
			array(
				'title' => 'Báo giá rõ trước khi chốt',
				'text'  => 'Giá vé minh bạch theo loại cabin và ngày đi, không phát sinh bất ngờ.',
			),
			array(
				'title' => 'Hỗ trợ điểm đón/trả',
				'text'  => 'Tư vấn điểm gần nhất tại Hà Nội, Sapa, Lào Cai tùy chuyến.',
			),
			array(
				'title' => 'Hotline & Zalo nhanh',
				'text'  => 'Gọi 1900 8164 hoặc nhắn Zalo — phản hồi trong giờ làm việc.',
			),
			array(
				'title' => 'WhatsApp & đa kênh',
				'text'  => 'Hỗ trợ thêm qua WhatsApp khi bạn tiện nhắn tin.',
			),
			array(
				'title' => 'An Nam Discovery uy tín',
				'text'  => 'Đặt vé xe, tour, combo du lịch miền Bắc — thông tin công ty rõ ràng.',
			),
		),
		'why_note'     => 'An Nam Discovery hỗ trợ đặt vé xe, tour du lịch, combo nghỉ dưỡng và dịch vụ du lịch tại miền Bắc.',
		'steps'        => array(
			array(
				'title' => 'Chọn tuyến, ngày đi, giờ đi',
				'text'  => 'Điền thông tin chuyến đi dự kiến hoặc nhắn Zalo để được hỗ trợ nhanh.',
				'icon'  => 'route',
			),
			array(
				'title'    => 'Nhân viên kiểm tra cabin',
				'text'     => 'An Nam Discovery kiểm tra cabin còn trống và tư vấn khung giờ phù hợp.',
				'icon'     => 'cabin',
				'featured' => true,
			),
			array(
				'title' => 'Xác nhận vé và điểm đón',
				'text'  => 'Gửi lại thông tin vé, điểm đón/trả và hướng dẫn thanh toán nếu cần.',
				'icon'  => 'confirm',
			),
		),
		'promises'     => array(
			array(
				'title'    => 'Báo giá rõ ràng trước khi xác nhận',
				'text'     => 'Nhân viên báo đủ giá và điều kiện trước khi bạn xác nhận đặt vé.',
				'featured' => true,
				'icon'     => 'shield',
			),
			array(
				'title' => 'Không tự ý đổi loại cabin khi chưa thông báo',
				'text'  => 'Cam kết đúng loại cabin đã chọn, có thông báo nếu cần điều chỉnh.',
				'icon'  => 'check',
			),
			array(
				'title' => 'Hỗ trợ đổi giờ nếu còn chỗ (tùy chuyến)',
				'text'  => 'Hỗ trợ đổi khung giờ linh hoạt khi xe còn chỗ trống.',
				'icon'  => 'check',
			),
			array(
				'title' => 'Tư vấn điểm đón/trả phù hợp',
				'text'  => 'Gợi ý điểm đón, trả phù hợp tuyến và lịch trình của bạn.',
				'icon'  => 'check',
			),
			array(
				'title' => 'Có nhân viên hỗ trợ trong quá trình đặt vé',
				'text'  => 'Luôn có nhân viên An Nam đồng hành từ lúc đặt vé đến khi lên xe.',
				'icon'  => 'check',
			),
		),
		'faq'          => annam_cabin_landing_default_faq_items(),
		'anchors'      => array(
			array( 'id' => 'gia-ve', 'label' => 'Bảng giá' ),
			array( 'id' => 'lich-xe', 'label' => 'Lịch xe' ),
			array( 'id' => 'diem-don', 'label' => 'Điểm đón' ),
			array( 'id' => 'anh-xe', 'label' => 'Ảnh xe' ),
			array( 'id' => 'goi-y-tour-sapa', 'label' => 'Tour Sapa' ),
			array( 'id' => 'faq', 'label' => 'FAQ' ),
		),
		'sections'     => array(
			'hero'      => true,
			'form'      => true,
			'pricing'   => true,
			'schedule'  => true,
			'cabins'    => true,
			'pickup'    => true,
			'gallery'   => true,
			'amenities' => true,
			'why'       => true,
			'steps'     => true,
			'promises'      => true,
			'related_tours' => true,
			'faq'           => true,
			'cta_final' => true,
			'seo'       => true,
		),
		'related_tours' => array(
			'category_slug' => 'tour-sapa',
			'limit'         => 12,
			'title'         => 'Gợi ý Tour/Combo Sapa liên quan',
		),
		'seo'           => array(
			'enabled' => true,
		),
	);

	if ( $page_id > 0 ) {
		$hero_title = get_post_meta( $page_id, '_annam_cabin_hero_title', true );
		if ( is_string( $hero_title ) && '' !== trim( $hero_title ) ) {
			$config['hero']['title'] = trim( $hero_title );
		}
		$seo_off = get_post_meta( $page_id, '_annam_cabin_seo_disabled', true );
		if ( '1' === (string) $seo_off ) {
			$config['sections']['seo'] = false;
			$config['seo']['enabled']  = false;
		}
	}

	$config = annam_cabin_landing_apply_image_urls( $config );

	return apply_filters( 'annam_cabin_landing_config', $config, $page_id );
}

/**
 * URL ảnh mặc định (có thể thay bằng file trong assets/images/cabin-landing/).
 *
 * @param string $key Key ảnh.
 * @return string
 */
function annam_cabin_landing_image_url( $key ) {
	$key = sanitize_key( (string) $key );
	if ( '' === $key ) {
		return '';
	}

	if ( function_exists( 'annam_cabin_landing_get_image_attachment_id' ) ) {
		$attachment_id = annam_cabin_landing_get_image_attachment_id( $key );
		if ( $attachment_id > 0 ) {
			$size = 'large';
			if ( function_exists( 'annam_cabin_landing_get_image_slots' ) ) {
				$slots = annam_cabin_landing_get_image_slots();
				if ( isset( $slots[ $key ]['wp_size'] ) ) {
					$size = (string) $slots[ $key ]['wp_size'];
				}
			}
			$url = wp_get_attachment_image_url( $attachment_id, $size );
			if ( $url ) {
				return $url;
			}
		}
	}

	$dir  = get_stylesheet_directory() . '/assets/images/cabin-landing/';
	$uri  = get_stylesheet_directory_uri() . '/assets/images/cabin-landing/';
	$map  = array(
		'cabin-single-2' => 'cabin-single-2.jpg',
		'cabin-single-1' => 'cabin-single-1.jpg',
		'cabin-double'   => 'cabin-double.jpg',
		'gallery-hero'   => 'gallery-hero.jpg',
		'gallery-bed'    => 'gallery-bed.jpg',
		'gallery-double' => 'gallery-double.jpg',
		'gallery-amenity'=> 'gallery-amenity.jpg',
		'gallery-clean'  => 'gallery-clean.jpg',
	);
	$defaults = array(
		'cabin-single-2'  => 'https://images.unsplash.com/photo-1570125909232-e097fb8d8586?w=800&q=80',
		'cabin-single-1'  => 'https://images.unsplash.com/photo-1544620307-c4fd4a3d5f3e?w=800&q=80',
		'cabin-double'    => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&q=80',
		'gallery-hero'    => 'https://images.unsplash.com/photo-1570125909232-e097fb8d8586?w=1200&q=85',
		'gallery-bed'     => 'https://images.unsplash.com/photo-1544620307-c4fd4a3d5f3e?w=600&q=80',
		'gallery-double'  => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&q=80',
		'gallery-amenity' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=600&q=80',
		'gallery-clean'   => 'https://images.unsplash.com/photo-1557221212-2435b6a57335?w=800&q=80',
	);

	if ( isset( $map[ $key ] ) && file_exists( $dir . $map[ $key ] ) ) {
		return $uri . $map[ $key ];
	}

	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Gắn URL ảnh vào cabins + gallery.
 *
 * @param array<string,mixed> $config Config.
 * @return array<string,mixed>
 */
function annam_cabin_landing_apply_image_urls( array $config ) {
	if ( ! empty( $config['cabins'] ) ) {
		foreach ( $config['cabins'] as $i => $cabin ) {
			if ( ! empty( $cabin['image'] ) ) {
				$config['cabins'][ $i ]['image_url'] = annam_cabin_landing_image_url( (string) $cabin['image'] );
			}
		}
	}
	if ( ! empty( $config['gallery'] ) ) {
		foreach ( $config['gallery'] as $i => $item ) {
			if ( ! empty( $item['image'] ) ) {
				$config['gallery'][ $i ]['image_url'] = annam_cabin_landing_image_url( (string) $item['image'] );
			}
		}
	}
	return $config;
}

/**
 * @return array<int,array{question:string,answer:string}>
 */
function annam_cabin_landing_default_faq_items() {
	return array(
		array(
			'question' => 'Vé xe cabin Hà Nội Sapa giá bao nhiêu?',
			'answer'   => 'Giá tham khảo: cabin đơn tầng 2 từ 420.000đ, cabin đơn tầng 1 từ 500.000đ, cabin đôi từ 720.000đ (tuyến Hà Nội ⇄ Sapa). Giá có thể thay đổi theo ngày — liên hệ để kiểm tra chính xác.',
		),
		array(
			'question' => 'Cabin đơn tầng 1 và tầng 2 khác gì nhau?',
			'answer'   => 'Tầng 1 thường thuận tiện lên xuống hơn; tầng 2 thường có mức giá tiết kiệm hơn. Cả hai đều là cabin riêng có rèm che.',
		),
		array(
			'question' => 'Cabin đôi đi được mấy người?',
			'answer'   => 'Thường phù hợp 2 khách (cặp đôi hoặc gia đình nhỏ). Nếu đi 3–4 người, nhân viên sẽ tư vấn phương án cabin/ghế phù hợp.',
		),
		array(
			'question' => 'Xe có đón trả tận nơi không?',
			'answer'   => 'Có hỗ trợ điểm đón/trả tại một số khu vực Hà Nội, Sapa, Lào Cai tùy chuyến. Vui lòng ghi chú địa chỉ khi đặt vé để được tư vấn điểm gần nhất.',
		),
		array(
			'question' => 'Đi Hà Nội Sapa mất bao lâu?',
			'answer'   => 'Thường khoảng 5–6 giờ tùy giờ xuất bến, điều kiện đường và điểm đón/trả. Nhân viên sẽ báo cụ thể khi xác nhận vé.',
		),
		array(
			'question' => 'Có cần đặt vé trước không?',
			'answer'   => 'Nên đặt trước, đặc biệt cuối tuần và dịp lễ để giữ đúng loại cabin và khung giờ mong muốn.',
		),
		array(
			'question' => 'Có thể đổi giờ xe không?',
			'answer'   => 'Có thể hỗ trợ đổi giờ nếu còn chỗ trống trên chuyến tương ứng. Liên hệ hotline hoặc Zalo sớm nhất có thể.',
		),
		array(
			'question' => 'Thanh toán như thế nào?',
			'answer'   => 'Sau khi xác nhận cabin, nhân viên gửi hướng dẫn thanh toán (chuyển khoản / theo quy định từng chuyến). Không thanh toán nếu chưa được xác nhận rõ.',
		),
		array(
			'question' => 'Có hỗ trợ khách đi nhóm không?',
			'answer'   => 'Có. Ghi số lượng khách trong form, chúng tôi tư vấn số cabin và giờ phù hợp cho nhóm.',
		),
		array(
			'question' => 'Tôi đặt qua Zalo được không?',
			'answer'   => 'Được. Bạn có thể nhắn Zalo kèm tuyến, ngày giờ, loại cabin và số điện thoại — nhân viên phản hồi trong giờ làm việc.',
		),
	);
}

/**
 * @return array<int,array{heading:string,content:string}>
 */
function annam_cabin_landing_default_seo_blocks() {
	return array(
		array(
			'heading' => 'Xe cabin VIP Hà Nội Sapa là gì?',
			'content' => 'Xe cabin VIP 22 phòng là loại xe giường nằm có khoang cabin riêng (đơn hoặc đôi), rèm che, phù hợp hành trình đêm hoặc đường dài Hà Nội — Sapa / Lào Cai. An Nam Discovery hỗ trợ đặt vé và xác nhận thông tin trước khi khách thanh toán.',
		),
		array(
			'heading' => 'Khi nào nên chọn cabin đơn?',
			'content' => 'Cabin đơn phù hợp khách đi một mình hoặc muốn không gian riêng. Tầng 2 thường tiết kiệm hơn; tầng 1 thuận tiện di chuyển hơn.',
		),
		array(
			'heading' => 'Khi nào nên chọn cabin đôi?',
			'content' => 'Cabin đôi phù hợp 2 khách đi chung, cặp đôi hoặc gia đình nhỏ cần không gian rộng hơn cabin đơn.',
		),
		array(
			'heading' => 'Kinh nghiệm đặt vé xe Hà Nội Sapa',
			'content' => 'Nên đặt trước 1–3 ngày (hoặc sớm hơn dịp cao điểm), chốt rõ điểm đón/trả, loại cabin và giờ xuất bến. Giữ screenshot xác nhận từ nhân viên sau khi chốt vé.',
		),
		array(
			'heading' => 'Lưu ý khi đi xe đêm lên Sapa',
			'content' => 'Mang thêm áo khoác, sạc dự phòng, đồ dùng cá nhân gọn. Đến điểm đón trước 15–20 phút. Kiểm tra lại số điện thoại tài xế/điều hành khi được gửi.',
		),
		array(
			'heading' => 'Vì sao nên đặt vé trước vào cuối tuần?',
			'content' => 'Cuối tuần và lễ Tết nhu cầu đi Sapa tăng, cabin đôi và khung giờ đẹp hết nhanh. Đặt sớm giúp giữ đúng loại cabin và giờ mong muốn.',
		),
		array(
			'heading' => 'Các tuyến liên quan',
			'content' => 'Ngoài Hà Nội ⇄ Sapa, An Nam Discovery hỗ trợ đặt vé cabin Hà Nội ⇄ Lào Cai và chiều ngược Sapa/Lào Cai về Hà Nội với lịch chạy hằng ngày (tùy ngày).',
		),
	);
}
