<?php
/**
 * Route profiles for shared limo seat landing (Sapa, Hạ Long, …).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string,array<string,mixed>>
 */
function annam_limo_landing_get_route_profiles() {
	$profiles = array(
		'sapa'   => array(
			'template'            => 'page-template-limousine-sapa-landing.php',
			'landing_key'         => 'limo_hn_sapa',
			'mail_prefix'         => '[LIMO HN-SAPA]',
			'places'              => array(
				'hanoi' => 'Hà Nội',
				'sapa'  => 'Sapa',
			),
			'destinations'        => array(
				'hanoi' => array( 'sapa' ),
				'sapa'  => array( 'hanoi' ),
			),
			'schedule'            => array(
				'hanoi_sapa' => array( '07:00', '14:30' ),
				'sapa_hanoi' => array( '07:30', '14:30' ),
			),
			'min_lead_minutes'    => 120,
			'max_guests'          => 20,
			'seats'               => array(
				'seat_a'  => array(
					'label' => 'Ghế giữa',
					'price' => 500000,
				),
				'seat_b'  => array(
					'label' => 'Ghế đầu / cuối',
					'price' => 450000,
				),
				'charter' => array(
					'label' => 'Bao nguyên xe (11 chỗ gồm tài xế)',
					'price' => 4500000,
				),
			),
			'place_placeholders'  => array(
				'hanoi' => 'VD: Royal City / Nội Bài / 23 Tú Mỡ…',
				'sapa'  => 'VD: khách sạn Fansipan / gần chợ Sapa…',
			),
			'option_suffix'       => '',
			'config_callback'     => 'annam_limo_landing_get_sapa_config',
			'whatsapp_url'        => 'https://wa.me/84942471111',
			'direction_query'     => array(),
			'extra_places'        => array(),
		),
		'halong' => array(
			'template'            => 'page-template-limousine-halong-landing.php',
			'landing_key'         => 'limo_hn_halong',
			'mail_prefix'         => '[LIMO HN-HALONG]',
			'places'              => array(
				'hanoi'  => 'Hà Nội',
				'halong' => 'Hạ Long',
			),
			'destinations'        => array(
				'hanoi'  => array( 'halong' ),
				'halong' => array( 'hanoi' ),
			),
			'schedule'            => array(
				'hanoi_halong' => array( '07:00', '08:00' ),
				'halong_hanoi' => array( '12:00', '13:00' ),
			),
			'min_lead_minutes'    => 30,
			'max_guests'          => 10,
			'seats'               => array(
				'seat_front' => array(
					'label' => 'Ghế đầu',
					'price' => 320000,
				),
				'seat_mid'   => array(
					'label' => 'Ghế giữa',
					'price' => 340000,
				),
				'seat_back'  => array(
					'label' => 'Ghế cuối',
					'price' => 300000,
				),
				'charter'    => array(
					'label' => 'Thuê nguyên xe',
					'price' => 3000000,
				),
			),
			'place_placeholders'  => array(
				'hanoi'  => 'VD: tên khách sạn ở Phố Cổ / 214 Trần Quang Khải',
				'halong' => 'VD: tên khách sạn / Cảng Tuần Châu / Cảng tàu khách Quốc tế',
			),
			'option_suffix'       => '_halong',
			'config_callback'     => 'annam_limo_landing_get_halong_config',
			'whatsapp_url'        => 'https://wa.me/84942471111',
			'direction_query'     => array(
				'hn-hl' => array(
					'from' => 'hanoi',
					'to'   => 'halong',
				),
				'hl-hn' => array(
					'from' => 'halong',
					'to'   => 'hanoi',
				),
			),
			'extra_places'        => array(
				array(
					'@type' => 'Place',
					'name'  => 'Cảng Tuần Châu',
				),
			),
		),
	);

	$filtered = apply_filters( 'annam_limo_landing_route_profiles', $profiles );
	return is_array( $filtered ) ? $filtered : $profiles;
}

/**
 * @param string $route Route key.
 * @return array<string,mixed>
 */
function annam_limo_landing_get_route_profile( $route ) {
	$profiles = annam_limo_landing_get_route_profiles();
	$route    = sanitize_key( (string) $route );
	if ( isset( $profiles[ $route ] ) && is_array( $profiles[ $route ] ) ) {
		return $profiles[ $route ];
	}
	return isset( $profiles['sapa'] ) && is_array( $profiles['sapa'] ) ? $profiles['sapa'] : array();
}

/**
 * @param int $page_id Page ID.
 * @return string Route key or ''.
 */
function annam_limo_landing_route_from_page( $page_id ) {
	$page_id = (int) $page_id;
	if ( $page_id <= 0 ) {
		return '';
	}
	$template = (string) get_page_template_slug( $page_id );
	if ( '' === $template ) {
		return '';
	}
	foreach ( annam_limo_landing_get_route_profiles() as $key => $profile ) {
		if ( isset( $profile['template'] ) && $template === (string) $profile['template'] ) {
			return (string) $key;
		}
	}
	return '';
}

/**
 * @return string
 */
function annam_limo_landing_current_route() {
	if ( ! is_singular( 'page' ) ) {
		return '';
	}
	return annam_limo_landing_route_from_page( get_queried_object_id() );
}

/**
 * Route used for options and images. Explicit profile key wins; else current page; else sapa.
 *
 * @param string $route Route key.
 * @return string
 */
function annam_limo_landing_storage_route( $route = '' ) {
	$profiles = annam_limo_landing_get_route_profiles();
	$route    = sanitize_key( (string) $route );
	if ( isset( $profiles[ $route ] ) ) {
		return $route;
	}
	$current = annam_limo_landing_current_route();
	if ( '' !== $current && isset( $profiles[ $current ] ) ) {
		return $current;
	}
	return 'sapa';
}

/**
 * @param string $base  Option base without suffix.
 * @param string $route Route key.
 * @return string
 */
function annam_limo_landing_option_name( $base, $route = '' ) {
	$profile = annam_limo_landing_get_route_profile( annam_limo_landing_storage_route( $route ) );
	$suffix  = isset( $profile['option_suffix'] ) ? (string) $profile['option_suffix'] : '';
	return (string) $base . $suffix;
}

/**
 * Admin route from POST (save) or GET tab. Defaults to sapa.
 *
 * @return string
 */
function annam_limo_landing_admin_route() {
	$route = '';
	if ( isset( $_POST['annam_limo_route'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$route = sanitize_key( wp_unslash( $_POST['annam_limo_route'] ) );
	} elseif ( isset( $_GET['route'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$route = sanitize_key( wp_unslash( $_GET['route'] ) );
	}
	if ( '' === $route ) {
		return 'sapa';
	}
	$profiles = annam_limo_landing_get_route_profiles();
	return isset( $profiles[ $route ] ) ? $route : 'sapa';
}
