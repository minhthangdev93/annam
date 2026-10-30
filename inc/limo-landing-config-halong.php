<?php
/**
 * Cấu hình landing vé Limousine 11 chỗ Hà Nội ⇄ Hạ Long.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param int $page_id Page ID. Unused; wrapper applies meta.
 * @return array<string,mixed>
 */
function annam_limo_landing_get_halong_config( $page_id = 0 ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	$schedule_map = annam_limo_landing_get_schedule_times_map( 'halong' );
	$times_hn_hl  = isset( $schedule_map['hanoi_halong'] ) ? $schedule_map['hanoi_halong'] : array( '07:00', '08:00' );
	$times_hl_hn  = isset( $schedule_map['halong_hanoi'] ) ? $schedule_map['halong_hanoi'] : array( '12:00', '13:00' );

	return array(
		'product_name'  => 'Vé Limousine 11 chỗ Hà Nội ⇄ Hạ Long',
		'hero'          => array(
			'title'      => 'Limousine Hà Nội – Hạ Long từ 300.000đ · Đón tận khách sạn',
			'subtitle'   => 'Cao tốc 5B, khoảng 2 giờ đến 2 giờ 30 phút. Hà Nội đi Hạ Long lúc 07:00 và 08:00. Hạ Long về Hà Nội lúc 12:00 và 13:00. Đón và trả tận khách sạn Phố Cổ và thành phố Hạ Long.',
			'price_from' => '300.000đ',
			'price_unit' => '/khách/chiều',
			'image_alt'  => 'Limousine Hà Nội Hạ Long',
			'badges'     => array(
				'Đón/trả tận nơi, không trung chuyển',
				'2h–2h30 qua cao tốc',
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
			'seat_note'       => '',
			'success_message' => 'Cảm ơn quý khách. Sale sẽ gọi tư vấn và hướng dẫn thanh toán.',
		),
		'form_defaults' => array(
			'from' => 'hanoi',
			'to'   => 'halong',
			'seat' => 'seat_mid',
			'time' => '07:00',
		),
		'seat_types'    => array(
			array(
				'value' => 'seat_back',
				'label' => 'Ghế cuối — 300.000đ',
			),
			array(
				'value' => 'seat_front',
				'label' => 'Ghế đầu — 320.000đ',
			),
			array(
				'value' => 'seat_mid',
				'label' => 'Ghế giữa — 340.000đ',
			),
			array(
				'value' => 'charter',
				'label' => 'Thuê nguyên xe — 3.200.000đ/xe/chiều',
			),
		),
		'pricing'       => array(
			'lead'       => 'Giá/khách/chiều, đã gồm VAT, áp dụng cả hai chiều. Xe 11 chỗ: 10 ghế khách + 1 tài xế.',
			'rows'       => array(
				array(
					'type'  => 'seat_back',
					'label' => 'Ghế cuối',
					'desc'  => '03 ghế cuối xe.',
					'price' => '300.000đ',
					'badge' => 'Giá tốt',
				),
				array(
					'type'  => 'seat_front',
					'label' => 'Ghế đầu',
					'desc'  => '01 ghế đầu xe.',
					'price' => '320.000đ',
					'badge' => '',
				),
				array(
					'type'  => 'seat_mid',
					'label' => 'Ghế giữa',
					'desc'  => '06 ghế giữa xe.',
					'price' => '340.000đ',
					'badge' => 'Êm nhất',
				),
			),
			'price_note' => 'Giá/khách/chiều, đã gồm VAT, áp dụng cả hai chiều.',
			'charter'    => array(
				'type'   => 'charter',
				'kicker' => 'Đi nhóm',
				'label'  => 'Thuê nguyên xe',
				'price'  => '3.200.000đ',
				'unit'   => '/ xe / chiều',
				'points' => array(
					'Xe riêng 10 chỗ, không ghép khách',
					'Cả hai chiều Hà Nội và Hạ Long',
					'Có tài xế, đón và trả tận nơi',
				),
				'note'   => 'Thuê nguyên xe: giờ nào cũng có xe. Sale xác nhận giờ đi khi gọi.',
				'cta'    => 'Thuê xe này',
			),
		),
		'schedule_lead' => '',
		'schedules'     => array(
			array(
				'id'    => 'hanoi-halong',
				'label' => 'Hà Nội → Hạ Long',
				'from'  => 'hanoi',
				'to'    => 'halong',
				'times' => $times_hn_hl,
			),
			array(
				'id'    => 'halong-hanoi',
				'label' => 'Hạ Long → Hà Nội',
				'from'  => 'halong',
				'to'    => 'hanoi',
				'times' => $times_hl_hn,
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
						'name'    => 'Văn phòng 80 Hồng Tiến',
						'time'    => '',
						'map_url' => 'https://maps.app.goo.gl/guZVA1j6oPKPyANj9',
					),
					array(
						'name'    => 'Văn phòng 214 Trần Quang Khải',
						'time'    => '',
						'map_url' => 'https://maps.app.goo.gl/3DmmAdysHTtu7pPu9',
					),
				),
				'note'    => 'Đón/trả tận nơi khách sạn Phố Cổ trước/sau 15–30 phút. Điểm văn phòng: có mặt trước 10 phút. Ngoài Phố Cổ (Tây Hồ, Mỹ Đình, Nội Bài…) liên hệ 1900 8164.',
			),
			array(
				'id'      => 'halong',
				'label'   => 'Hạ Long',
				'heading' => 'Hạ Long',
				'items'   => array(
					array(
						'name'    => 'Khách sạn thành phố Hạ Long',
						'time'    => '',
						'map_url' => '',
					),
					array(
						'name'    => 'Văn phòng 505 Nguyễn Văn Cừ',
						'time'    => '',
						'map_url' => 'https://maps.app.goo.gl/kHDhzqudzA5j9Kv39',
					),
					array(
						'name'    => 'Vincom Plaza Hạ Long',
						'time'    => '',
						'map_url' => 'https://maps.app.goo.gl/prBoLUXQozsxsGd4A',
					),
					array(
						'name'    => 'Cảng tàu khách Quốc tế Hạ Long',
						'time'    => '',
						'map_url' => 'https://maps.app.goo.gl/FRNB3sEzS5YpxDrg6',
					),
					array(
						'name'    => 'Cảng Tuần Châu',
						'time'    => '',
						'map_url' => 'https://maps.app.goo.gl/8UoQBfcXa63W3dR48',
					),
				),
				'note'    => 'Đón/trả tận nơi khách sạn và cảng khu vực TP Hạ Long trước/sau 30–45 phút. Điểm văn phòng / Vincom: có mặt trước 10 phút.',
			),
		),
		'amenities'     => array(
			array( 'icon' => 'ac', 'title' => 'Điều hòa', 'text' => 'Xe mát suốt hành trình.' ),
			array( 'icon' => 'water', 'title' => 'Nước uống', 'text' => 'Phục vụ trên xe.' ),
			array( 'icon' => 'towel', 'title' => 'Khăn lạnh', 'text' => 'Phục vụ trên xe.' ),
			array( 'icon' => 'wifi', 'title' => 'Wi-Fi', 'text' => 'Kết nối trên đường.' ),
			array( 'icon' => 'usb', 'title' => 'Cổng sạc', 'text' => 'Sạc điện thoại trên xe.' ),
			array( 'icon' => 'seat', 'title' => 'Ghế ngả', 'text' => 'Ngả lưng trên cao tốc.' ),
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
				'text'  => 'Không giới hạn số lần. Báo trước ít nhất 6 tiếng và chuyến mới còn ghế.',
			),
			array(
				'label' => 'Đặt trước',
				'icon'  => 'clock',
				'text'  => 'Tối thiểu 30 phút trước giờ khởi hành. Dưới 30 phút thì gọi tổng đài 1900 8164.',
			),
			array(
				'label' => 'Có mặt',
				'icon'  => 'pin',
				'text'  => 'Điểm cố định: có mặt trước 10 phút. Phố Cổ Hà Nội: đón/trả trước/sau 15–30 phút. Khách sạn và cảng TP Hạ Long: trước/sau 30–45 phút.',
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
				'caption' => 'Trên đường Hà Nội ⇄ Hạ Long',
			),
		),
		'gallery_lead'  => 'Xe Limousine 11 chỗ (10 ghế khách + 1 tài xế) trên tuyến Hà Nội ⇄ Hạ Long.',
		'video'         => array(
			'title' => 'Xem Xe & Hành Trình Thật',
			'lead'  => 'Video giúp quý khách hình dung xe Limousine trên tuyến Hà Nội ⇄ Hạ Long.',
		),
		'why_cards'     => array(
			array(
				'icon'  => 'pin',
				'title' => 'Đón trả tận nơi',
				'text'  => 'Xe limousine đón và trả thẳng tại khách sạn, không trung chuyển. Phố Cổ Hà Nội và khách sạn trong thành phố Hạ Long. Ngoài Phố Cổ thì liên hệ tổng đài.',
			),
			array(
				'icon'  => 'clock',
				'title' => 'Nhanh nhờ cao tốc 5B',
				'text'  => 'Khoảng 2 giờ đến 2 giờ 30 phút qua cao tốc Hà Nội – Hải Phòng – Hạ Long.',
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
				'title' => 'Chọn chiều, giờ và ghế',
				'text'  => 'Hà Nội đi Hạ Long lúc 07:00 hoặc 08:00. Hạ Long về Hà Nội lúc 12:00 hoặc 13:00. Chọn một hạng ghế và số khách.',
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
				'question' => 'Vé Limousine Hà Nội Hạ Long giá bao nhiêu?',
				'answer'   => 'Giá niêm yết/khách/chiều, đã gồm VAT, áp dụng cả hai chiều: ghế cuối 300.000đ (03 ghế), ghế đầu 320.000đ (01 ghế), ghế giữa 340.000đ (06 ghế). Thuê nguyên xe 3.200.000đ/xe/một chiều, không ghép khách.',
			),
			array(
				'question' => 'Có thuê nguyên xe không?',
				'answer'   => 'Có. Thuê nguyên xe limousine 11 chỗ (10 khách và 1 tài xế) giá 3.200.000đ/xe/một chiều, cả hai chiều. Không ghép khách, có tài xế, đón và trả tận nơi. Sale gọi tư vấn giờ đi và hướng dẫn thanh toán.',
			),
			array(
				'question' => 'Xe chạy những giờ nào?',
				'answer'   => 'Hà Nội → Hạ Long: 07:00 và 08:00. Hạ Long → Hà Nội: 12:00 và 13:00. Mỗi khung giờ có thể có nhiều xe. Đón khách sạn phố cổ Hà Nội trước/sau 15–30 phút; đón khách sạn và cảng khu vực TP Hạ Long trước/sau 30–45 phút.',
			),
			array(
				'question' => 'Cần đặt trước bao lâu?',
				'answer'   => 'Tối thiểu 30 phút trước giờ khởi hành. Cần đi sát hơn 30 phút thì gọi tổng đài 1900 8164 để xác nhận.',
			),
			array(
				'question' => 'Đi mất bao lâu?',
				'answer'   => 'Khoảng 2 giờ đến 2 giờ 30 phút qua cao tốc 5B Hà Nội – Hải Phòng – Hạ Long.',
			),
			array(
				'question' => 'Có đón tận khách sạn không?',
				'answer'   => 'Có. Xe limousine đón và trả tận nơi, không trung chuyển. Khách sạn phố cổ Hà Nội: trước/sau 15–30 phút. Khách sạn và cảng khu vực TP Hạ Long: trước/sau 30–45 phút. Ngoài Phố Cổ (Tây Hồ, Mỹ Đình, Nội Bài…) vui lòng liên hệ tổng đài.',
			),
			array(
				'question' => 'Có ra Cảng Tuần Châu hoặc cảng du thuyền không?',
				'answer'   => 'Có đón/trả tại Cảng Tuần Châu và Cảng tàu khách Quốc tế Hạ Long trong khung trước/sau 30–45 phút. Ghi rõ điểm khi giữ chỗ.',
			),
			array(
				'question' => 'Hành lý được mang bao nhiêu?',
				'answer'   => 'Mỗi khách: 01 vali tối đa 20 kg và 01 túi xách nhỏ. Hành lý vượt quy định cần liên hệ trước và có thể phát sinh phụ phí.',
			),
			array(
				'question' => 'Đổi vé và hủy vé thế nào?',
				'answer'   => 'Đổi vé miễn phí, không giới hạn, báo trước ít nhất 6 tiếng và chuyến mới còn ghế. Hủy trước ít nhất 12 tiếng hoàn 100%; từ 12 tiếng đến 3 tiếng hoàn 50%; trong 3 tiếng cuối không hoàn tiền.',
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
			'title'    => 'Đặt Limousine Hà Nội ⇄ Hạ Long — xác nhận nhanh qua tổng đài 1900 8164',
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
