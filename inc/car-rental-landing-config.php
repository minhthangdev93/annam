<?php
/**
 * Cấu hình landing thuê xe hợp đồng theo loại xe.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/car-rental-pricing.php';

/**
 * CTA liên hệ.
 *
 * @return array<string,string>
 */
function annam_car_rental_get_cta() {
	$d = function_exists( 'annam_contact_get_details' ) ? annam_contact_get_details() : array();

	return apply_filters(
		'annam_car_rental_cta',
		array(
			'hotline_display' => isset( $d['hotline_display'] ) ? $d['hotline_display'] : '1900 8164',
			'hotline_tel'     => isset( $d['hotline_tel'] ) ? $d['hotline_tel'] : 'tel:19008164',
			'mobile_display'  => isset( $d['mobile_display'] ) ? $d['mobile_display'] : '0942471111',
			'zalo_url'        => isset( $d['zalo_url'] ) ? $d['zalo_url'] : 'http://zalo.me/2127942034358673568',
			'email'           => isset( $d['email'] ) ? $d['email'] : 'annamdiscoveryvn@gmail.com',
		)
	);
}

/**
 * @param string $vehicle_type Vehicle key or hub.
 * @return array<string,mixed>
 */
function annam_car_rental_get_landing_config( $vehicle_type = '' ) {
	if ( '' === $vehicle_type ) {
		$vehicle_type = annam_car_rental_get_current_vehicle_type();
	}

	$types = annam_car_rental_get_vehicle_types();
	$base  = array(
		'vehicle_type' => $vehicle_type,
		'is_hub'       => 'hub' === $vehicle_type,
		'pricing_note' => array(
			'included'    => __( 'lái xe, xăng dầu, cầu đường.', 'generatepress_child' ),
			'excluded'    => __( 'VAT, phát sinh ngoài lịch trình.', 'generatepress_child' ),
			'disclaimer'  => __( 'Giá trên là giá tham khảo cho hành trình 2 chiều, xuất phát từ Hà Nội. Giá thực tế có thể thay đổi theo điểm đón/trả cụ thể, thời gian thuê, thời gian chờ, số ngày đi và các phát sinh ngoài lịch trình.', 'generatepress_child' ),
		),
		'form'         => array(
			'title'           => __( 'Nhận báo giá nhanh', 'generatepress_child' ),
			'submit_label'    => __( 'Nhận báo giá', 'generatepress_child' ),
			'success_message' => __( 'Cảm ơn quý khách. An Nam Discovery đã nhận yêu cầu và sẽ liên hệ báo giá sớm.', 'generatepress_child' ),
		),
		'use_cases'    => array(
			array( 'icon' => 'groups', 'title' => __( 'Đoàn du lịch', 'generatepress_child' ), 'text' => __( 'Không gian rộng rãi, thoải mái cho mọi thành viên.', 'generatepress_child' ) ),
			array( 'icon' => 'home', 'title' => __( 'Gia đình đông người', 'generatepress_child' ), 'text' => __( 'Gắn kết tình thân trên suốt hành trình.', 'generatepress_child' ) ),
			array( 'icon' => 'briefcase', 'title' => __( 'Doanh nghiệp công tác', 'generatepress_child' ), 'text' => __( 'Chuyên nghiệp cho chuyến công vụ, sự kiện.', 'generatepress_child' ) ),
			array( 'icon' => 'landmark', 'title' => __( 'Đi lễ hội / Chùa chiền', 'generatepress_child' ), 'text' => __( 'Thuận tiện cho các đoàn hành hương.', 'generatepress_child' ) ),
			array( 'icon' => 'map', 'title' => __( 'Nhóm bạn du lịch', 'generatepress_child' ), 'text' => __( 'Tự do khám phá cùng nhau.', 'generatepress_child' ) ),
			array( 'icon' => 'luggage', 'title' => __( 'Đưa đón sân bay', 'generatepress_child' ), 'text' => __( 'Đúng giờ, chuyên nghiệp, nhiều hành lý.', 'generatepress_child' ) ),
		),
		'services'     => array(
			array( 'icon' => 'sun', 'title' => __( 'Thuê xe du lịch', 'generatepress_child' ) ),
			array( 'icon' => 'landmark', 'title' => __( 'Lễ hội / Hành hương', 'generatepress_child' ) ),
			array( 'icon' => 'briefcase', 'title' => __( 'Công tác / Sự kiện', 'generatepress_child' ) ),
			array( 'icon' => 'bus', 'title' => __( 'Đón tiễn sân bay', 'generatepress_child' ) ),
		),
		'why'          => array(
			array(
				'title' => __( 'Báo giá minh bạch', 'generatepress_child' ),
				'text'  => __( 'Không phí ẩn, báo giá đã bao gồm xăng xe, tài xế và phí BOT.', 'generatepress_child' ),
			),
			array(
				'title' => __( 'Tài xế chuyên nghiệp', 'generatepress_child' ),
				'text'  => __( 'Đội ngũ lái xe nhiệt tình, am hiểu cung đường.', 'generatepress_child' ),
			),
			array(
				'title' => __( 'Đội xe đời mới', 'generatepress_child' ),
				'text'  => __( 'Xe đời mới, sạch sẽ, tiện nghi.', 'generatepress_child' ),
			),
			array(
				'title' => __( 'Linh hoạt lịch trình', 'generatepress_child' ),
				'text'  => __( 'Dễ dàng điều chỉnh điểm dừng theo nhu cầu đoàn.', 'generatepress_child' ),
			),
		),
		'steps'        => array(
			array( 'title' => __( 'Gửi lộ trình', 'generatepress_child' ), 'text' => __( 'Cung cấp điểm đón, điểm đến qua Form hoặc Zalo.', 'generatepress_child' ) ),
			array( 'title' => __( 'Nhận báo giá', 'generatepress_child' ), 'text' => __( 'Nhân viên tư vấn loại xe và gửi báo giá tốt nhất.', 'generatepress_child' ) ),
			array( 'title' => __( 'Xác nhận cọc', 'generatepress_child' ), 'text' => __( 'Chốt lịch và đặt cọc để giữ xe chắc chắn.', 'generatepress_child' ) ),
			array( 'title' => __( 'Khởi hành', 'generatepress_child' ), 'text' => __( 'Tài xế đón khách đúng giờ theo lịch hẹn.', 'generatepress_child' ), 'featured' => true ),
		),
		'faq'          => array(),
		'hero_badges'  => array(),
		'trust'        => annam_car_rental_get_trust_section_config(),
	);

	if ( 'hub' === $vehicle_type ) {
		$hub_min_price = PHP_INT_MAX;
		foreach ( array_keys( $types ) as $vkey ) {
			$p = annam_car_rental_get_price_from( $vkey );
			if ( $p > 0 ) {
				$hub_min_price = min( $hub_min_price, $p );
			}
		}
		if ( PHP_INT_MAX === $hub_min_price ) {
			$hub_min_price = 0;
		}

		$config = array_merge(
			$base,
			array(
				'hero' => array(
					'eyebrow'    => __( 'Dịch vụ thuê xe hợp đồng', 'generatepress_child' ),
					'title'      => __( 'Thuê xe có lái', 'generatepress_child' ),
					'title_accent' => __( 'uy tín, minh bạch giá', 'generatepress_child' ),
					'subtitle'   => __( 'An Nam Discovery cung cấp đội xe từ 7 đến 45 chỗ, phục vụ du lịch, công tác và sự kiện trên toàn miền Bắc.', 'generatepress_child' ),
					'price_from' => $hub_min_price > 0 ? annam_car_rental_format_price_from( $hub_min_price ) : '',
					'price_unit' => __( '/ hành trình 2 chiều', 'generatepress_child' ),
				),
				'hero_badges' => array(
					array( 'icon' => 'directions_bus', 'label' => __( 'Xe 7 đến 45 chỗ', 'generatepress_child' ) ),
					array( 'icon' => 'map', 'label' => __( 'Hơn 19 tuyến từ Hà Nội', 'generatepress_child' ) ),
					array( 'icon' => 'check_circle', 'label' => __( 'Báo giá minh bạch, không phí ẩn', 'generatepress_child' ) ),
				),
				'cta_final' => array(
					'title' => __( 'Cần tư vấn thêm?', 'generatepress_child' ),
					'desc'  => __( 'Chưa chọn được loại xe? Để lại lộ trình — nhân viên tư vấn loại xe và báo giá phù hợp.', 'generatepress_child' ),
				),
				'faq'  => annam_car_rental_get_hub_faq(),
			)
		);
		foreach ( $types as $key => $type ) {
			$min = annam_car_rental_get_price_from( $key );
			$config['vehicles'][] = array_merge(
				$type,
				array(
					'key'         => $key,
					'price_from'  => $min,
					'price_label' => annam_car_rental_format_price_from( $min ),
					'url'         => annam_car_rental_get_vehicle_page_url( $key ),
				)
			);
		}
		return apply_filters( 'annam_car_rental_landing_config', $config, $vehicle_type );
	}

	if ( ! annam_car_rental_is_valid_vehicle_type( $vehicle_type ) ) {
		return apply_filters( 'annam_car_rental_landing_config', $base, $vehicle_type );
	}

	$type_meta = $types[ $vehicle_type ];
	$min_price = annam_car_rental_get_price_from( $vehicle_type );
	$preset    = annam_car_rental_get_vehicle_hero_preset( $vehicle_type );

	$config = array_merge(
		$base,
		array(
			'vehicle_label' => $type_meta['label'],
			'passengers'    => $type_meta['passengers'],
			'routes'        => annam_car_rental_get_routes_for_vehicle( $vehicle_type ),
			'featured'      => annam_car_rental_get_featured_journeys( $vehicle_type ),
			'hot_routes'    => annam_car_rental_get_hot_routes( $vehicle_type ),
			'hero'          => array(
				'eyebrow'      => sprintf(
					/* translators: %s: vehicle label */
					__( 'Thuê xe %s có lái', 'generatepress_child' ),
					$type_meta['label']
				),
				'title'        => $preset['title'],
				'title_accent' => $preset['title_accent'],
				'subtitle'     => $preset['subtitle'],
				'price_from'   => annam_car_rental_format_price_from( $min_price ),
				'price_unit'   => __( '(2 chiều, từ Hà Nội)', 'generatepress_child' ),
			),
			'hero_badges'   => $preset['badges'],
			'pricing_title' => sprintf(
				/* translators: %s: vehicle label */
				__( 'Bảng giá thuê xe %s tham khảo', 'generatepress_child' ),
				$type_meta['label']
			),
			'use_cases_title' => sprintf(
				/* translators: %s: vehicle label */
				__( 'Xe %s phù hợp với ai?', 'generatepress_child' ),
				$type_meta['label']
			),
			'cta_final'     => array(
				'title' => sprintf(
					/* translators: %s: vehicle label */
					__( 'Cần thuê xe %s cho lịch trình sắp tới?', 'generatepress_child' ),
					$type_meta['label']
				),
				'desc'  => __( 'Chỉ mất 2 phút để nhận báo giá chi tiết và cam kết loại xe tốt nhất cho đoàn của bạn.', 'generatepress_child' ),
			),
			'form'          => array_merge(
				$base['form'],
				array(
					'title' => sprintf(
						/* translators: %s: vehicle label */
						__( 'Nhận báo giá xe %s', 'generatepress_child' ),
						$type_meta['label']
					),
				)
			),
			'faq'           => annam_car_rental_get_vehicle_faq( $vehicle_type ),
			'related'       => annam_car_rental_get_related_vehicles( $vehicle_type ),
		)
	);

	return apply_filters( 'annam_car_rental_landing_config', $config, $vehicle_type );
}

/**
 * @return string
 */
function annam_car_rental_get_current_vehicle_type() {
	if ( is_page_template( 'page-template-thue-xe-hub.php' ) ) {
		return 'hub';
	}
	if ( is_page_template( 'page-template-thue-xe-landing.php' ) ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$from_slug = annam_car_rental_vehicle_type_from_slug( $post->post_name );
			if ( $from_slug && 'hub' !== $from_slug ) {
				return $from_slug;
			}
		}
		$meta = get_post_meta( get_queried_object_id(), '_annam_car_rental_vehicle_type', true );
		if ( is_string( $meta ) && annam_car_rental_is_valid_vehicle_type( $meta ) ) {
			return sanitize_key( $meta );
		}
	}
	return '';
}

/**
 * @param array<int,array{icon:string,label:string}|string> $badges Raw badges.
 * @return array<int,array{icon:string,label:string}>
 */
function annam_car_rental_normalize_hero_badges( array $badges ) {
	$out = array();
	foreach ( $badges as $badge ) {
		if ( is_array( $badge ) && ! empty( $badge['label'] ) ) {
			$out[] = array(
				'icon'  => ! empty( $badge['icon'] ) ? sanitize_key( (string) $badge['icon'] ) : 'check_circle',
				'label' => (string) $badge['label'],
			);
			continue;
		}
		if ( is_string( $badge ) && '' !== trim( $badge ) ) {
			$out[] = array(
				'icon'  => 'check_circle',
				'label' => $badge,
			);
		}
	}
	return $out;
}

/**
 * @param string $vehicle_type Vehicle key.
 * @return array{title:string,title_accent:string,subtitle:string,badges:array<int,array{icon:string,label:string}>}
 */
function annam_car_rental_get_vehicle_hero_preset( $vehicle_type ) {
	$presets = array(
		'7-cho' => array(
			'title'        => __( 'Thuê xe 7 chỗ có lái', 'generatepress_child' ),
			'title_accent' => __( 'linh hoạt mọi hành trình', 'generatepress_child' ),
			'subtitle'     => __( 'Phù hợp gia đình và công tác nhóm nhỏ. Xe đời mới, tài xế chuyên nghiệp, báo giá minh bạch theo lộ trình.', 'generatepress_child' ),
			'badges'       => array(
				array( 'icon' => 'person_pin_circle', 'label' => __( 'Xe có lái', 'generatepress_child' ) ),
				array( 'icon' => 'request_quote', 'label' => __( 'Báo giá theo điểm đến', 'generatepress_child' ) ),
				array( 'icon' => 'directions', 'label' => __( 'Hỗ trợ 1/2 chiều', 'generatepress_child' ) ),
				array( 'icon' => 'local_gas_station', 'label' => __( 'Bao gồm xăng/BOT', 'generatepress_child' ) ),
			),
		),
		'limousine-9-11' => array(
			'title'        => __( 'Thuê xe Limousine 9–11 chỗ', 'generatepress_child' ),
			'title_accent' => __( 'cao cấp, chỗ ngồi VIP', 'generatepress_child' ),
			'subtitle'     => __( 'Trải nghiệm sang trọng cho đoàn 7–9 khách. Phù hợp sự kiện, đón tiếp đối tác và du lịch cao cấp.', 'generatepress_child' ),
			'badges'       => array(
				array( 'icon' => 'airport_shuttle', 'label' => __( 'Ghế VIP', 'generatepress_child' ) ),
				array( 'icon' => 'flight_takeoff', 'label' => __( 'Đón sân bay', 'generatepress_child' ) ),
				array( 'icon' => 'celebration', 'label' => __( 'Sự kiện / cưới hỏi', 'generatepress_child' ) ),
				array( 'icon' => 'local_gas_station', 'label' => __( 'Bao gồm xăng/BOT', 'generatepress_child' ) ),
			),
		),
		'16-cho' => array(
			'title'        => __( 'Thuê xe 16 chỗ có lái', 'generatepress_child' ),
			'title_accent' => __( 'theo lịch trình riêng', 'generatepress_child' ),
			'subtitle'     => __( 'Giải pháp vận chuyển cho đoàn 8–15 khách. Cam kết xe đời mới, tài xế chuyên nghiệp và báo giá minh bạch.', 'generatepress_child' ),
			'badges'       => array(
				array( 'icon' => 'person_pin_circle', 'label' => __( 'Xe có lái chuyên nghiệp', 'generatepress_child' ) ),
				array( 'icon' => 'request_quote', 'label' => __( 'Báo giá theo điểm đến', 'generatepress_child' ) ),
				array( 'icon' => 'directions', 'label' => __( 'Hỗ trợ 1/2 chiều', 'generatepress_child' ) ),
				array( 'icon' => 'groups', 'label' => __( 'Đoàn 8–15 khách', 'generatepress_child' ) ),
				array( 'icon' => 'local_gas_station', 'label' => __( 'Bao gồm xăng/BOT', 'generatepress_child' ) ),
			),
		),
		'29-cho' => array(
			'title'        => __( 'Thuê xe 29 chỗ có lái', 'generatepress_child' ),
			'title_accent' => __( 'cho đoàn trung và lớn', 'generatepress_child' ),
			'subtitle'     => __( 'Phục vụ tour, trường học và doanh nghiệp với đội xe 29 chỗ hiện đại, an toàn.', 'generatepress_child' ),
			'badges'       => array(
				array( 'icon' => 'groups', 'label' => __( 'Tour / trường học', 'generatepress_child' ) ),
				array( 'icon' => 'calendar_today', 'label' => __( 'Hợp đồng dài ngày', 'generatepress_child' ) ),
				array( 'icon' => 'request_quote', 'label' => __( 'Báo giá minh bạch', 'generatepress_child' ) ),
				array( 'icon' => 'local_gas_station', 'label' => __( 'Bao gồm xăng/BOT', 'generatepress_child' ) ),
			),
		),
		'45-cho' => array(
			'title'        => __( 'Thuê xe 45 chỗ có lái', 'generatepress_child' ),
			'title_accent' => __( 'đoàn đông, sự kiện lớn', 'generatepress_child' ),
			'subtitle'     => __( 'Vận chuyển 30–40 khách cho tour dài ngày, hội nghị và sự kiện quy mô lớn.', 'generatepress_child' ),
			'badges'       => array(
				array( 'icon' => 'groups', 'label' => __( 'Đoàn 30–40 khách', 'generatepress_child' ) ),
				array( 'icon' => 'route', 'label' => __( 'Tour liên tỉnh', 'generatepress_child' ) ),
				array( 'icon' => 'directions_bus', 'label' => __( 'Ghế ngồi / giường nằm', 'generatepress_child' ) ),
				array( 'icon' => 'local_gas_station', 'label' => __( 'Bao gồm xăng/BOT', 'generatepress_child' ) ),
			),
		),
	);

	if ( ! isset( $presets[ $vehicle_type ] ) ) {
		return array(
			'title'        => __( 'Thuê xe có lái', 'generatepress_child' ),
			'title_accent' => '',
			'subtitle'     => '',
			'badges'       => array(),
		);
	}

	$presets[ $vehicle_type ]['badges'] = annam_car_rental_normalize_hero_badges( $presets[ $vehicle_type ]['badges'] );

	return $presets[ $vehicle_type ];
}

/**
 * @param string $current Current vehicle key.
 * @return array<int,array<string,mixed>>
 */
function annam_car_rental_get_related_vehicles( $current ) {
	$types = annam_car_rental_get_vehicle_types();
	$out   = array();
	foreach ( $types as $key => $type ) {
		$min = annam_car_rental_get_price_from( $key );
		$out[] = array_merge(
			$type,
			array(
				'key'         => $key,
				'price_from'  => $min,
				'price_label' => annam_car_rental_format_price_from( $min ),
				'url'         => annam_car_rental_get_vehicle_page_url( $key ),
				'current'     => $key === $current,
			)
		);
	}
	return $out;
}

/**
 * @param string $vehicle_type Vehicle key.
 * @return array<int,array{question:string,answer:string}>
 */
function annam_car_rental_get_vehicle_faq( $vehicle_type ) {
	$label = annam_car_rental_get_vehicle_types()[ $vehicle_type ]['label'] ?? '';

	return array(
		array(
			'question' => sprintf(
				/* translators: %s: vehicle label */
				__( 'Giá thuê xe %s đã bao gồm phí cao tốc chưa?', 'generatepress_child' ),
				$label
			),
			'answer'   => __( 'Báo giá thông thường đã bao gồm lái xe, xăng dầu và phí cao tốc (BOT). Phí bến bãi tại một số điểm tham quan có thể phát sinh theo thỏa thuận.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Báo giá đã bao gồm VAT chưa?', 'generatepress_child' ),
			'answer'   => __( 'Giá tham khảo trên website chưa bao gồm VAT. Khi nhận báo giá chính thức, nhân viên sẽ tư vấn rõ phần VAT (nếu quý khách cần xuất hóa đơn).', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Có thuê xe 1 chiều được không?', 'generatepress_child' ),
			'answer'   => __( 'Có. An Nam Discovery hỗ trợ thuê 1 chiều hoặc 2 chiều tùy lịch trình. Giá 1 chiều sẽ được báo riêng theo điểm đón, điểm trả và thời gian chờ.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Cần đặt xe trước bao lâu?', 'generatepress_child' ),
			'answer'   => __( 'Nên gửi yêu cầu trước 2–3 ngày cho ngày thường và 1–2 tuần cho dịp cao điểm, lễ tết hoặc cuối tuần để giữ xe tốt hơn.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Có cần đặt cọc trước không?', 'generatepress_child' ),
			'answer'   => __( 'Quý khách đặt cọc khoảng 30% giá trị hợp đồng để xác nhận giữ xe. Phần còn lại thanh toán theo thỏa thuận khi kết thúc hành trình.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Có được dừng chân tham quan trên đường không?', 'generatepress_child' ),
			'answer'   => __( 'Có thể dừng chân theo lịch trình đã thống nhất. Nếu dừng lâu hơn hoặc đổi lộ trình ngoài kế hoạch, chi phí phát sinh sẽ được báo trước khi tiếp tục.', 'generatepress_child' ),
		),
		array(
			'question' => sprintf(
				/* translators: %s: vehicle label */
				__( 'Xe %s có phù hợp đưa đón sân bay không?', 'generatepress_child' ),
				$label
			),
			'answer'   => __( 'Có, tùy số lượng khách và hành lý. Vui lòng ghi rõ giờ bay, điểm đón/trả và số người để nhân viên tư vấn loại xe và báo giá phù hợp.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Thanh toán như thế nào?', 'generatepress_child' ),
			'answer'   => __( 'Chuyển khoản đặt cọc khi chốt lịch; phần còn lại thanh toán cho tài xế hoặc theo hình thức đã thỏa thuận. Hỗ trợ chuyển khoản, tiền mặt và xuất hóa đơn theo yêu cầu.', 'generatepress_child' ),
		),
	);
}

/**
 * @return array<int,array{question:string,answer:string}>
 */
function annam_car_rental_get_hub_faq() {
	return array(
		array(
			'question' => __( 'Thuê xe hợp đồng khác gì thuê xe theo ngày?', 'generatepress_child' ),
			'answer'   => __( 'Thuê hợp đồng tính theo hành trình cụ thể (điểm đón – điểm đến), bao gồm tài xế và chi phí vận hành cơ bản. Phù hợp tour, công tác và đưa đón theo lịch trình riêng.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Tôi nên chọn loại xe nào?', 'generatepress_child' ),
			'answer'   => __( '7 chỗ cho 4–6 khách; Limousine 9–11 cho đoàn VIP; 16 chỗ cho 8–15 khách; 29–45 chỗ cho đoàn lớn, tour và sự kiện.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Giá trên bảng là giá chốt chưa?', 'generatepress_child' ),
			'answer'   => __( 'Bảng giá là mức tham khảo (2 chiều, xuất phát từ Hà Nội). Báo giá chính xác sẽ được tư vấn theo ngày đi, điểm đón/trả, loại xe và thời gian chờ.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Báo giá đã bao gồm những gì?', 'generatepress_child' ),
			'answer'   => __( 'Thông thường đã bao gồm lái xe, xăng dầu và phí cao tốc (BOT). Chưa bao gồm VAT và các phát sinh ngoài lịch trình (chờ lâu, đổi tuyến, phí bến bãi…).', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Có phục vụ tuyến ngoài miền Bắc không?', 'generatepress_child' ),
			'answer'   => __( 'Trọng tâm là các tuyến xuất phát từ Hà Nội đi các tỉnh miền Bắc. Tuyến dài hoặc liên miền vui lòng gửi lộ trình để được tư vấn riêng.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Cần đặt xe trước bao lâu?', 'generatepress_child' ),
			'answer'   => __( 'Nên gửi yêu cầu trước 2–3 ngày; dịp lễ tết hoặc cuối tuần nên đặt sớm hơn (1–2 tuần) để giữ xe và báo giá tốt.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Có cần đặt cọc không?', 'generatepress_child' ),
			'answer'   => __( 'Có. Đặt cọc khoảng 30% để xác nhận giữ xe sau khi chốt lịch và báo giá. Phần còn lại thanh toán theo thỏa thuận.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Làm sao để nhận báo giá nhanh nhất?', 'generatepress_child' ),
			'answer'   => __( 'Điền form báo giá trên trang (điểm đón, điểm đến, ngày đi, loại xe, SĐT) hoặc nhắn Zalo/gọi hotline. Nhân viên phản hồi sớm trong giờ hành chính.', 'generatepress_child' ),
		),
	);
}

/**
 * Section uy tín: gallery thực tế + thông tin thanh toán công ty.
 *
 * @return array<string,mixed>
 */
function annam_car_rental_get_trust_section_config() {
	return apply_filters(
		'annam_car_rental_trust_section_config',
		array(
			'section_title' => __( 'Hình ảnh thực tế & thông tin thanh toán chính thức', 'generatepress_child' ),
			'gallery'       => array(
				'title'       => __( 'Hình ảnh xe & dịch vụ thực tế', 'generatepress_child' ),
				'images'      => array(
					array(
						'url'   => '',
						'alt'   => __( 'Xe thuê có lái — ngoại thất', 'generatepress_child' ),
						'label' => __( 'Xe ngoại thất', 'generatepress_child' ),
					),
					array(
						'url'   => '',
						'alt'   => __( 'Nội thất xe thuê', 'generatepress_child' ),
						'label' => __( 'Nội thất xe', 'generatepress_child' ),
					),
					array(
						'url'   => '',
						'alt'   => __( 'Xe đón khách tại điểm hẹn', 'generatepress_child' ),
						'label' => __( 'Đón khách', 'generatepress_child' ),
					),
					array(
						'url'   => '',
						'alt'   => __( 'Đoàn khách trên hành trình', 'generatepress_child' ),
						'label' => __( 'Đoàn khách', 'generatepress_child' ),
					),
					array(
						'url'   => '',
						'alt'   => __( 'Xe trên hành trình thực tế', 'generatepress_child' ),
						'label' => __( 'Hành trình', 'generatepress_child' ),
					),
				),
			),
			'payment'       => array(
				'eyebrow'                    => __( 'Thanh toán an toàn qua tài khoản công ty', 'generatepress_child' ),
				'title'                      => __( 'Tài khoản công ty nhận cọc', 'generatepress_child' ),
				'description'                => __( 'An Nam Discovery chỉ nhận tiền cọc/thuê xe qua tài khoản công ty chính thức. Quý khách vui lòng kiểm tra đúng tên tài khoản trước khi chuyển khoản.', 'generatepress_child' ),
				'account_name'               => 'CTCP AN NAM DISCOVERY',
				'account_number'             => '119633611111',
				'bank_name'                  => 'Ngân hàng TMCP Công Thương Việt Nam (VietinBank)',
				'bank_branch'                => 'CN ĐÔNG HÀ NỘI - PGD PHÚ THỊNH',
				'qr_image'                   => content_url( 'uploads/2026/06/QR.jpg' ),
				'alert_fraud'                => __( 'Cảnh báo chống lừa đảo: Không chuyển khoản vào tài khoản cá nhân hoặc thông tin thanh toán không được công bố trên website. Nếu có nghi ngờ, vui lòng gọi hotline chính thức để xác minh trước khi chuyển cọc.', 'generatepress_child' ),
				'office_label'               => __( 'Trụ sở chính', 'generatepress_child' ),
				'office_address'             => '214 Đ. Trần Quang Khải, Hoàn Kiếm, Hà Nội',
				'account_number_placeholder' => __( '[Nhập số tài khoản công ty]', 'generatepress_child' ),
				'bank_name_placeholder'      => __( '[Nhập tên ngân hàng]', 'generatepress_child' ),
			),
		)
	);
}
