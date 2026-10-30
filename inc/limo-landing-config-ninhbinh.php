<?php
/**
 * Cấu hình landing vé Limousine 11 chỗ Hà Nội ⇄ Ninh Bình.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param int $page_id Page ID. Unused; wrapper applies meta.
 * @return array<string,mixed>
 */
function annam_limo_landing_get_ninhbinh_config( $page_id = 0 ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	$schedule_map = annam_limo_landing_get_schedule_times_map( 'ninhbinh' );
	$times_hn_nb  = isset( $schedule_map['hanoi_ninhbinh'] ) ? $schedule_map['hanoi_ninhbinh'] : array( '07:00', '08:00' );
	$times_nb_hn  = isset( $schedule_map['ninhbinh_hanoi'] ) ? $schedule_map['ninhbinh_hanoi'] : array( '12:00', '13:00' );

	return array(
		'product_name'  => 'Vé Limousine 11 chỗ Hà Nội ⇄ Ninh Bình',
		'hero'          => array(
			'eyebrow'    => 'Limousine 11 chỗ',
			'title'      => 'Limousine Hà Nội ⇄ Ninh Bình từ 220.000đ · Đón tận nơi',
			'subtitle'   => 'Khoảng 1 giờ 30 phút. Hà Nội đi Ninh Bình lúc 07:00 và 08:00. Ninh Bình về Hà Nội lúc 12:00 và 13:00. Đón và trả tận nơi, không trung chuyển.',
			'price_from' => '220.000đ',
			'price_unit' => '/khách/chiều',
			'image_alt'  => 'Limousine Hà Nội Ninh Bình',
			'badges'     => array(
				'Đón/trả tận nơi, không trung chuyển',
				'~1h30 mỗi chiều',
				'Đổi vé miễn phí',
				'Hủy trước 12h hoàn 100%',
			),
		),
		'form'          => array(
			'phone_first'     => true,
			'title'           => 'Để lại số điện thoại',
			'subtitle'        => 'Chỉ cần số điện thoại. Sale gọi lại để tư vấn và hướng dẫn thanh toán.',
			'submit_label'    => 'Gửi số — Sale gọi tư vấn',
			'footer_note'     => 'Không thanh toán trên trang. Sale gọi tư vấn và hướng dẫn thanh toán.',
			'seat_label'      => 'Khu đón/trả Ninh Bình',
			'seat_note'       => '',
			'success_message' => 'Cảm ơn quý khách. Sale sẽ gọi tư vấn và hướng dẫn thanh toán.',
		),
		'form_defaults' => array(
			'from' => 'hanoi',
			'to'   => 'ninhbinh',
			'seat' => 'zone_hoalu',
			'time' => '07:00',
		),
		'seat_types'    => array(
			array(
				'value' => 'zone_hoalu',
				'label' => 'Hoa Lư tận nơi — 220.000đ',
			),
			array(
				'value' => 'zone_trangan',
				'label' => 'Tràng An, Hang Múa, Tam Cốc — 220.000đ',
			),
			array(
				'value' => 'zone_baidinh',
				'label' => 'Bái Đính, Thung Nham — 250.000đ',
			),
			array(
				'value' => 'charter',
				'label' => 'Thuê nguyên xe — 2.200.000đ/xe/chiều',
			),
		),
		'pricing'       => array(
			'lead'       => 'Giá/khách/chiều theo khu đón–trả Ninh Bình, đã gồm VAT, áp dụng cả hai chiều. Xe 11 chỗ: 10 ghế khách + 1 tài xế.',
			'rows'       => array(
				array(
					'type'  => 'zone_hoalu',
					'label' => 'Hoa Lư tận nơi',
					'desc'  => 'Đón/trả tận nơi khu Hoa Lư (gồm Bích Động).',
					'price' => '220.000đ',
					'badge' => 'Giá tốt',
				),
				array(
					'type'  => 'zone_trangan',
					'label' => 'Tràng An, Hang Múa, Tam Cốc',
					'desc'  => 'Đón/trả theo khu điểm tham quan.',
					'price' => '220.000đ',
					'badge' => '',
				),
				array(
					'type'  => 'zone_baidinh',
					'label' => 'Bái Đính, Thung Nham',
					'desc'  => 'Đón/trả theo khu điểm tham quan.',
					'price' => '250.000đ',
					'badge' => '',
				),
			),
			'price_note' => 'Giá/khách/chiều, đã gồm VAT, áp dụng cả hai chiều. Không bán theo hạng ghế.',
			'charter'    => array(
				'type'   => 'charter',
				'kicker' => 'Đi nhóm',
				'label'  => 'Thuê nguyên xe',
				'price'  => '2.200.000đ',
				'unit'   => '/ xe / chiều',
				'points' => array(
					'Xe riêng 10 chỗ, không ghép khách',
					'Cả hai chiều Hà Nội và Ninh Bình',
					'Mọi khu đón/trả Ninh Bình trong bảng giá',
					'Có tài xế, đón và trả tận nơi',
				),
				'note'   => 'Thuê nguyên xe: giờ nào cũng có xe. Sale xác nhận giờ đi khi gọi.',
				'cta'    => 'Thuê xe này',
			),
		),
		'schedule_lead' => '',
		'schedules'     => array(
			array(
				'id'    => 'hanoi-ninhbinh',
				'label' => 'Hà Nội → Ninh Bình',
				'from'  => 'hanoi',
				'to'    => 'ninhbinh',
				'times' => $times_hn_nb,
			),
			array(
				'id'    => 'ninhbinh-hanoi',
				'label' => 'Ninh Bình → Hà Nội',
				'from'  => 'ninhbinh',
				'to'    => 'hanoi',
				'times' => $times_nb_hn,
			),
		),
		'timelines'     => array(),
		'pickup_lead'   => '',
		'pickup_layout' => 'split',
		'pickup_tabs'   => array(
			array(
				'id'      => 'hanoi',
				'label'   => 'Hà Nội',
				'heading' => 'Hà Nội',
				'items'   => array(
					array(
						'name'    => 'Khách sạn khu Phố Cổ',
						'time'    => '',
						'map_url' => '',
					),
					array(
						'name'    => 'Văn phòng 214 Trần Quang Khải',
						'time'    => '',
						'map_url' => 'https://maps.app.goo.gl/3DmmAdysHTtu7pPu9',
					),
				),
				'note'    => 'Đón/trả tận nơi khách sạn Phố Cổ trước/sau 15–30 phút. Điểm văn phòng: có mặt trước 10 phút. Ngoài Phố Cổ liên hệ 1900 8164.',
			),
			array(
				'id'      => 'ninhbinh',
				'label'   => 'Ninh Bình',
				'heading' => 'Ninh Bình',
				'items'   => array(
					array(
						'name'    => 'Hoa Lư tận nơi',
						'time'    => '',
						'map_url' => '',
					),
					array(
						'name'    => 'Tràng An, Hang Múa, Tam Cốc',
						'time'    => '',
						'map_url' => '',
					),
					array(
						'name'    => 'Bái Đính, Thung Nham',
						'time'    => '',
						'map_url' => '',
					),
					array(
						'name'    => 'Văn phòng 47 Nguyễn Huệ, Hoa Lư, Ninh Bình',
						'time'    => '',
						'map_url' => 'https://maps.app.goo.gl/heiWyjcJSsNCEsH16',
					),
				),
				'note'    => 'Đón/trả homestay & khách sạn khu du lịch trước/sau 30–45 phút. Điểm văn phòng: có mặt trước 10 phút. Ghi rõ khu/địa chỉ khi giữ chỗ. Giá theo khu xem Bảng giá.',
			),
		),
		'amenities'     => array(
			array( 'icon' => 'ac', 'title' => 'Điều hòa', 'text' => 'Xe mát suốt hành trình.' ),
			array( 'icon' => 'water', 'title' => 'Nước uống', 'text' => 'Phục vụ trên xe.' ),
			array( 'icon' => 'towel', 'title' => 'Khăn lạnh', 'text' => 'Phục vụ trên xe.' ),
			array( 'icon' => 'wifi', 'title' => 'Wi-Fi', 'text' => 'Kết nối trên đường.' ),
			array( 'icon' => 'usb', 'title' => 'Cổng sạc', 'text' => 'Sạc điện thoại trên xe.' ),
			array( 'icon' => 'seat', 'title' => 'Ghế ngả', 'text' => 'Ngả lưng trên đường.' ),
			array( 'icon' => 'massage', 'title' => 'Ghế massage', 'text' => 'Ghế massage trên xe limousine.' ),
		),
		'policies_lead' => 'Đổi vé miễn phí. Hủy càng sớm, hoàn càng nhiều.',
		'policies'      => array(
			array(
				'label' => 'Hủy vé',
				'text'  => 'Trước ít nhất 12 tiếng: hoàn 100%. Từ 12 tiếng đến 3 tiếng: hoàn 50%. Trong 3 tiếng cuối trước giờ khởi hành: không hoàn tiền.',
				'tiers' => array(
					array(
						'when'   => 'Trước 12 tiếng',
						'result' => 'Hoàn 100%',
						'tone'   => 'full',
					),
					array(
						'when'   => '12 tiếng đến 3 tiếng',
						'result' => 'Hoàn 50%',
						'tone'   => 'half',
					),
					array(
						'when'   => '3 tiếng cuối',
						'result' => 'Không hoàn',
						'tone'   => 'none',
					),
				),
			),
			array(
				'label' => 'Đổi vé',
				'icon'  => 'swap',
				'badge' => 'Miễn phí',
				'text'  => 'Không giới hạn số lần. Báo trước ít nhất 6 tiếng và chuyến mới còn chỗ.',
			),
			array(
				'label' => 'Đặt trước',
				'icon'  => 'clock',
				'text'  => 'Nên đặt trước ít nhất 30 phút trước giờ khởi hành. Cần đi sát hơn thì gọi tổng đài 1900 8164 để xác nhận.',
			),
			array(
				'label' => 'Có mặt',
				'icon'  => 'pin',
				'text'  => 'Điểm cố định: có mặt trước 10 phút. Phố Cổ Hà Nội: đón/trả trước/sau 15–30 phút. Homestay và khách sạn khu du lịch Ninh Bình: trước/sau 30–45 phút.',
			),
			array(
				'label' => 'Hành lý',
				'icon'  => 'bag',
				'text'  => '01 vali tối đa 20 kg và 01 túi xách nhỏ mỗi khách. Quá quy định thì liên hệ trước.',
			),
			array(
				'label' => 'Thú cưng',
				'icon'  => 'ban',
				'tone'  => 'limit',
				'text'  => 'Không nhận thú cưng lên xe.',
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
				'caption' => 'Trên đường Hà Nội ⇄ Ninh Bình',
			),
		),
		'gallery_lead'  => 'Xe Limousine 11 chỗ (10 ghế khách + 1 tài xế) trên tuyến Hà Nội ⇄ Ninh Bình.',
		'video'         => array(
			'title' => 'Xem Xe & Hành Trình Thật',
			'lead'  => 'Video giúp quý khách hình dung xe Limousine trên tuyến Hà Nội ⇄ Ninh Bình.',
		),
		'why_title'     => 'Vì Sao Chọn Limousine 11 Chỗ?',
		'why_lead'      => 'Limousine 11 chỗ, lịch cố định, đón trả tận nơi — giữ chỗ nhanh qua form hoặc Zalo.',
		'why_cards'     => array(
			array(
				'icon'  => 'pin',
				'title' => 'Đón trả tận nơi',
				'text'  => 'Xe limousine đón và trả thẳng tại điểm, không trung chuyển. Phố Cổ Hà Nội và các khu Ninh Bình theo bảng giá.',
			),
			array(
				'icon'  => 'clock',
				'title' => 'Khoảng 1 giờ 30 phút',
				'text'  => 'Hành trình Hà Nội ⇄ Ninh Bình khoảng 1 giờ 30 phút mỗi chiều.',
			),
			array(
				'icon'  => 'van',
				'title' => 'Ghế massage và tiện ích',
				'text'  => 'Điều hòa, nước, khăn lạnh, Wi-Fi, cổng sạc, ghế ngả và ghế massage.',
			),
			array(
				'icon'  => 'zap',
				'title' => 'Đổi/hủy linh hoạt',
				'text'  => 'Đổi vé miễn phí. Hủy trước 12 tiếng hoàn 100% tiền vé.',
			),
		),
		'steps'         => array(
			array(
				'title' => 'Chọn chiều, giờ và khu',
				'text'  => 'Hà Nội đi Ninh Bình lúc 07:00 hoặc 08:00. Ninh Bình về Hà Nội lúc 12:00 hoặc 13:00. Chọn khu đón/trả và số khách.',
			),
			array(
				'title' => 'Để lại số điện thoại',
				'text'  => 'Chỉ cần số điện thoại. Sale gọi lại để tư vấn và hướng dẫn thanh toán.',
			),
			array(
				'title' => 'Tổng đài xác nhận',
				'text'  => 'Sale gọi tư vấn chuyến đi và hướng dẫn thanh toán. Không thanh toán trên trang.',
			),
		),
		'faq'           => array(
			array(
				'question' => 'Vé Limousine Hà Nội Ninh Bình giá bao nhiêu?',
				'answer'   => 'Giá niêm yết/khách/chiều, đã gồm VAT, áp dụng cả hai chiều theo khu Ninh Bình: Hoa Lư tận nơi 220.000đ; Tràng An, Hang Múa, Tam Cốc 220.000đ; Bái Đính, Thung Nham 250.000đ. Thuê nguyên xe 2.200.000đ/xe/một chiều, mọi khu.',
			),
			array(
				'question' => 'Có thuê nguyên xe không?',
				'answer'   => 'Có. Thuê nguyên xe limousine 11 chỗ (10 khách và 1 tài xế) giá 2.200.000đ/xe/một chiều, cả hai chiều, mọi khu đón/trả trong bảng giá. Không ghép khách, có tài xế, đón và trả tận nơi. Sale gọi tư vấn giờ đi và hướng dẫn thanh toán.',
			),
			array(
				'question' => 'Xe chạy những giờ nào?',
				'answer'   => 'Hà Nội → Ninh Bình: 07:00 và 08:00. Ninh Bình → Hà Nội: 12:00 và 13:00. Mỗi khung giờ có thể có nhiều xe. Đón phố cổ Hà Nội trước/sau 15–30 phút; đón homestay và khách sạn khu du lịch Ninh Bình trước/sau 30–45 phút.',
			),
			array(
				'question' => 'Cần đặt trước bao lâu?',
				'answer'   => 'Nên đặt trước ít nhất 30 phút trước giờ khởi hành. Cần đi sát hơn thì gọi tổng đài 1900 8164 để xác nhận.',
			),
			array(
				'question' => 'Đi mất bao lâu?',
				'answer'   => 'Khoảng 1 giờ 30 phút mỗi chiều Hà Nội ⇄ Ninh Bình.',
			),
			array(
				'question' => 'Có đón tận nơi không?',
				'answer'   => 'Có. Xe limousine đón và trả tận nơi, không trung chuyển. Phố Cổ Hà Nội: trước/sau 15–30 phút. Homestay và khách sạn khu du lịch Ninh Bình: trước/sau 30–45 phút. Có thể đón/trả tại văn phòng cố định.',
			),
			array(
				'question' => 'Giá khác nhau theo khu thế nào?',
				'answer'   => 'Hoa Lư tận nơi và khu Tràng An / Hang Múa / Tam Cốc: 220.000đ/khách/chiều. Bái Đính / Thung Nham: 250.000đ/khách/chiều. Bích Động gom vào khu Hoa Lư 220.000đ.',
			),
			array(
				'question' => 'Hành lý được mang bao nhiêu?',
				'answer'   => 'Mỗi khách: 01 vali tối đa 20 kg và 01 túi xách nhỏ. Hành lý vượt quy định cần liên hệ trước và có thể phát sinh phụ phí.',
			),
			array(
				'question' => 'Đổi vé và hủy vé thế nào?',
				'answer'   => 'Đổi vé miễn phí, không giới hạn, báo trước ít nhất 6 tiếng và chuyến mới còn chỗ. Hủy trước ít nhất 12 tiếng hoàn 100%; từ 12 tiếng đến 3 tiếng hoàn 50%; trong 3 tiếng cuối không hoàn tiền.',
			),
			array(
				'question' => 'Có mang thú cưng được không?',
				'answer'   => 'Không nhận thú cưng lên xe.',
			),
			array(
				'question' => 'Khách nước ngoài có cần hộ chiếu không?',
				'answer'   => 'Không bắt buộc số hộ chiếu. Cần họ tên và thông tin liên hệ.',
			),
			array(
				'question' => 'Thanh toán thế nào?',
				'answer'   => 'Chuyển khoản vào tài khoản công ty CTCP AN NAM DISCOVERY — VietinBank số 119633611111 (Chi nhánh Đông Hà Nội). Sale gọi tư vấn và hướng dẫn nội dung chuyển khoản. Trang này không nhận thanh toán online.',
			),
		),
		'payment'       => function_exists( 'annam_limo_landing_get_company_payment' ) ? annam_limo_landing_get_company_payment() : array(),
		'final_cta'     => array(
			'title'    => 'Đặt Limousine Hà Nội ⇄ Ninh Bình — xác nhận nhanh qua tổng đài 1900 8164',
			'subtitle' => 'Gửi số điện thoại, Zalo hoặc WhatsApp. Sale gọi tư vấn và hướng dẫn thanh toán.',
		),
		'cabin_upsell'  => array(),
		'team'          => array(
			'title' => 'Đội ngũ An Nam Discovery',
			'lead'  => '',
			'slots' => array( 'team-1', 'team-2', 'team-3', 'team-4', 'team-5', 'team-6' ),
		),
		'anchors'       => array(
			array( 'id' => 'gia-ve', 'label' => 'Bảng giá' ),
			array( 'id' => 'lich-xe', 'label' => 'Lịch xe' ),
			array( 'id' => 'diem-don', 'label' => 'Đón trả' ),
			array( 'id' => 'chinh-sach', 'label' => 'Chính sách' ),
			array( 'id' => 'thanh-toan', 'label' => 'Thanh toán' ),
			array( 'id' => 'anh-xe', 'label' => 'Ảnh xe' ),
			array( 'id' => 'doi-ngu', 'label' => 'Đội ngũ' ),
			array( 'id' => 'faq', 'label' => 'FAQ' ),
		),
		'sections'      => array(
			'hero'         => true,
			'anchor_nav'   => true,
			'pricing'      => true,
			'schedule'     => true,
			'pickup'       => true,
			'amenities'    => true,
			'policies'     => true,
			'payment'      => true,
			'gallery'      => true,
			'video'        => true,
			'why'          => true,
			'team'         => true,
			'cabin_upsell' => false,
			'steps'        => true,
			'faq'          => true,
			'final_cta'    => true,
			'seo'          => true,
		),
	);
}
