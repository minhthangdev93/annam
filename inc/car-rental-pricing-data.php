<?php
/**
 * Bảng giá thuê xe hợp đồng — Hà Nội ↔ tỉnh (2 chiều). Single source of truth.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Thứ tự hiển thị « Hành trình phổ biến » (tuyến hot).
 *
 * @return string[] Route ids.
 */
function annam_car_rental_get_hot_route_display_order() {
	return array(
		'quang-ninh',
		'sapa',
		'ha-giang',
		'ninh-binh',
		'thanh-hoa',
	);
}

/**
 * @return array<string,array{label:string,slug:string,path:string,passengers:string}>
 */
function annam_car_rental_get_vehicle_types() {
	return array(
		'7-cho'          => array(
			'label'       => '7 chỗ',
			'slug'        => 'thue-xe-7-cho',
			'path'        => '/thue-xe-7-cho/',
			'passengers'  => '4–6 khách',
			'icon'        => 'directions_car',
		),
		'limousine-9-11' => array(
			'label'       => 'Limousine 9–11 chỗ',
			'slug'        => 'thue-xe-limousine-9-11-cho',
			'path'        => '/thue-xe-limousine-9-11-cho/',
			'passengers'  => '7–9 khách',
			'icon'        => 'van_limo',
			'badge'       => 'VIP',
		),
		'16-cho'         => array(
			'label'       => '16 chỗ',
			'slug'        => 'thue-xe-16-cho',
			'path'        => '/thue-xe-16-cho/',
			'passengers'  => '8–15 khách',
			'icon'        => 'directions_bus',
		),
		'29-cho'         => array(
			'label'       => '29 chỗ',
			'slug'        => 'thue-xe-29-cho',
			'path'        => '/thue-xe-29-cho/',
			'passengers'  => '20–28 khách',
			'icon'        => 'directions_bus',
		),
		'45-cho'         => array(
			'label'       => '45 chỗ',
			'slug'        => 'thue-xe-45-cho',
			'path'        => '/thue-xe-45-cho/',
			'passengers'  => '30–40 khách',
			'icon'        => 'directions_bus',
		),
	);
}

/**
 * @return array<int,array<string,mixed>>
 */
function annam_car_rental_get_routes_raw() {
	return array(
		array(
			'id'     => 'son-la',
			'label'  => 'Hà Nội ↔ Sơn La',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 4200000,
				'16-cho'         => 5400000,
				'29-cho'         => 9300000,
				'45-cho'         => 11280000,
				'limousine-9-11' => 6840000,
			),
		),
		array(
			'id'     => 'dien-bien',
			'label'  => 'Hà Nội ↔ Điện Biên',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 6104000,
				'16-cho'         => 10464000,
				'29-cho'         => 14600000,
				'45-cho'         => 17350000,
				'limousine-9-11' => 12840000,
			),
		),
		array(
			'id'     => 'lai-chau',
			'label'  => 'Hà Nội ↔ Lai Châu',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 6258000,
				'16-cho'         => 10728000,
				'29-cho'         => 14900000,
				'45-cho'         => 17680000,
				'limousine-9-11' => 12960000,
			),
		),
		array(
			'id'     => 'sapa',
			'label'  => 'Hà Nội ↔ Sapa',
			'hot'    => true,
			'prices' => array(
				'7-cho'          => 4074000,
				'16-cho'         => 6984000,
				'29-cho'         => 11900000,
				'45-cho'         => 14280000,
				'limousine-9-11' => 7480000,
			),
		),
		array(
			'id'     => 'yen-bai',
			'label'  => 'Hà Nội ↔ Yên Bái',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 2240000,
				'16-cho'         => 3840000,
				'29-cho'         => 5930000,
				'45-cho'         => 8680000,
				'limousine-9-11' => 4980000,
			),
		),
		array(
			'id'     => 'phu-tho',
			'label'  => 'Hà Nội ↔ Phú Thọ',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 1428000,
				'16-cho'         => 2448000,
				'29-cho'         => 4330000,
				'45-cho'         => 6820000,
				'limousine-9-11' => 4560000,
			),
		),
		array(
			'id'     => 'ha-giang',
			'label'  => 'Hà Nội ↔ Hà Giang',
			'hot'    => true,
			'prices' => array(
				'7-cho'          => 4158000,
				'16-cho'         => 7128000,
				'29-cho'         => 10390000,
				'45-cho'         => 17890000,
				'limousine-9-11' => 9690000,
			),
		),
		array(
			'id'     => 'tuyen-quang',
			'label'  => 'Hà Nội ↔ Tuyên Quang',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 2044000,
				'16-cho'         => 3504000,
				'29-cho'         => 8839000,
				'45-cho'         => 10839000,
				'limousine-9-11' => 5420000,
			),
		),
		array(
			'id'     => 'cao-bang',
			'label'  => 'Hà Nội ↔ Cao Bằng',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 3990000,
				'16-cho'         => 6840000,
				'29-cho'         => 9960000,
				'45-cho'         => 13834000,
				'limousine-9-11' => 10840000,
			),
		),
		array(
			'id'     => 'thai-nguyen',
			'label'  => 'Hà Nội ↔ Thái Nguyên',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 1260000,
				'16-cho'         => 2160000,
				'29-cho'         => 4280000,
				'45-cho'         => 7370000,
				'limousine-9-11' => 5160000,
			),
		),
		array(
			'id'     => 'lang-son',
			'label'  => 'Hà Nội ↔ Lạng Sơn',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 2254000,
				'16-cho'         => 3864000,
				'29-cho'         => 5130000,
				'45-cho'         => 8280000,
				'limousine-9-11' => 5730000,
			),
		),
		array(
			'id'     => 'quang-ninh',
			'label'  => 'Hà Nội ↔ Quảng Ninh',
			'hot'    => true,
			'prices' => array(
				'7-cho'          => 2198000,
				'16-cho'         => 3768000,
				'29-cho'         => 5330000,
				'45-cho'         => 9340000,
				'limousine-9-11' => 5230000,
			),
		),
		array(
			'id'     => 'hai-phong',
			'label'  => 'Hà Nội ↔ Hải Phòng',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 1750000,
				'16-cho'         => 3000000,
				'29-cho'         => 5220000,
				'45-cho'         => 9280000,
				'limousine-9-11' => 5400000,
			),
		),
		array(
			'id'     => 'nam-dinh',
			'label'  => 'Hà Nội ↔ Nam Định',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 1190000,
				'16-cho'         => 2040000,
				'29-cho'         => 4800000,
				'45-cho'         => 8210000,
				'limousine-9-11' => 3400000,
			),
		),
		array(
			'id'     => 'thai-binh',
			'label'  => 'Hà Nội ↔ Thái Bình',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 1470000,
				'16-cho'         => 2520000,
				'29-cho'         => 5600000,
				'45-cho'         => 8910000,
				'limousine-9-11' => 3800000,
			),
		),
		array(
			'id'     => 'ninh-binh',
			'label'  => 'Hà Nội ↔ Ninh Bình',
			'hot'    => true,
			'prices' => array(
				'7-cho'          => 1330000,
				'16-cho'         => 2280000,
				'29-cho'         => 5200000,
				'45-cho'         => 8610000,
				'limousine-9-11' => 3800000,
			),
		),
		array(
			'id'     => 'thanh-hoa',
			'label'  => 'Hà Nội ↔ Thanh Hóa',
			'hot'    => true,
			'prices' => array(
				'7-cho'          => 2352000,
				'16-cho'         => 4032000,
				'29-cho'         => 7200000,
				'45-cho'         => 11610000,
				'limousine-9-11' => 6300000,
			),
		),
		array(
			'id'     => 'nghe-an',
			'label'  => 'Hà Nội ↔ Nghệ An',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 4830000,
				'16-cho'         => 8280000,
				'29-cho'         => 11600000,
				'45-cho'         => 17610000,
				'limousine-9-11' => 10800000,
			),
		),
		array(
			'id'     => 'ha-tinh',
			'label'  => 'Hà Nội ↔ Hà Tĩnh',
			'hot'    => false,
			'prices' => array(
				'7-cho'          => 4816000,
				'16-cho'         => 8256000,
				'29-cho'         => 11300000,
				'45-cho'         => 17680000,
				'limousine-9-11' => 10900000,
			),
		),
	);
}
