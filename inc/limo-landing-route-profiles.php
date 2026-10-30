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
			'min_lead_minutes'    => 0,
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
					'price' => 3200000,
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
		'ninhbinh' => array(
			'template'            => 'page-template-limousine-ninhbinh-landing.php',
			'landing_key'         => 'limo_hn_ninhbinh',
			'mail_prefix'         => '[LIMO HN-NINHBINH]',
			'places'              => array(
				'hanoi'    => 'Hà Nội',
				'ninhbinh' => 'Ninh Bình',
			),
			'destinations'        => array(
				'hanoi'    => array( 'ninhbinh' ),
				'ninhbinh' => array( 'hanoi' ),
			),
			'schedule'            => array(
				'hanoi_ninhbinh' => array( '07:00', '08:00' ),
				'ninhbinh_hanoi' => array( '12:00', '13:00' ),
			),
			'min_lead_minutes'    => 0,
			'max_guests'          => 10,
			'seats'               => array(
				'zone_hoalu'    => array(
					'label' => 'Hoa Lư tận nơi',
					'price' => 220000,
				),
				'zone_trangan'  => array(
					'label' => 'Tràng An, Hang Múa, Tam Cốc',
					'price' => 220000,
				),
				'zone_baidinh'  => array(
					'label' => 'Bái Đính, Thung Nham',
					'price' => 250000,
				),
				'charter'       => array(
					'label' => 'Thuê nguyên xe',
					'price' => 2200000,
				),
			),
			'place_placeholders'  => array(
				'hanoi'    => 'VD: tên khách sạn ở Phố Cổ / 214 Trần Quang Khải',
				'ninhbinh' => 'VD: homestay / khách sạn Hoa Lư, Tam Cốc, Bái Đính…',
			),
			'option_suffix'       => '_ninhbinh',
			'config_callback'     => 'annam_limo_landing_get_ninhbinh_config',
			'whatsapp_url'        => 'https://wa.me/84942471111',
			'direction_query'     => array(
				'hn-nb' => array(
					'from' => 'hanoi',
					'to'   => 'ninhbinh',
				),
				'nb-hn' => array(
					'from' => 'ninhbinh',
					'to'   => 'hanoi',
				),
			),
			'extra_places'        => array(
				array(
					'@type' => 'Place',
					'name'  => 'Hoa Lư',
				),
				array(
					'@type' => 'Place',
					'name'  => 'Tràng An',
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

/**
 * Slug trang landing mặc định theo tuyến (dùng để tự gán lại template nếu bị lệch).
 *
 * @param string $route Route key.
 * @return string
 */
function annam_limo_landing_default_page_slug( $route ) {
	$route = sanitize_key( (string) $route );
	$map   = array(
		'sapa'     => 'dat-ve-xe-ha-noi-sapa',
		'halong'   => 'xe-limousine-ha-noi-ha-long',
		'ninhbinh' => 'xe-limousine-ha-noi-ninh-binh',
	);
	return isset( $map[ $route ] ) ? $map[ $route ] : '';
}

/**
 * Các trang publish đang gán template của tuyến.
 *
 * @param string $route Route key.
 * @return WP_Post[]
 */
function annam_limo_landing_get_pages_for_route( $route ) {
	$route   = function_exists( 'annam_limo_landing_storage_route' )
		? annam_limo_landing_storage_route( $route )
		: sanitize_key( (string) $route );
	$profile = annam_limo_landing_get_route_profile( $route );
	$tpl     = isset( $profile['template'] ) ? (string) $profile['template'] : '';
	if ( '' === $tpl ) {
		return array();
	}
	$pages = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => array( 'publish', 'draft', 'private' ),
			'posts_per_page'         => 20,
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'meta_key'               => '_wp_page_template',
			'meta_value'             => $tpl,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	return is_array( $pages ) ? $pages : array();
}

/**
 * Đảm bảo trang slug mặc định của tuyến đang dùng đúng page template
 * (tránh admin lưu option _ninhbinh nhưng frontend vẫn đọc _halong / sapa).
 *
 * @param string $route Route key.
 * @return int Page ID đã sửa/đảm bảo, hoặc 0.
 */
function annam_limo_landing_ensure_route_page_template( $route ) {
	$route = sanitize_key( (string) $route );
	$slug  = annam_limo_landing_default_page_slug( $route );
	$profile = annam_limo_landing_get_route_profile( $route );
	$tpl     = isset( $profile['template'] ) ? (string) $profile['template'] : '';
	if ( '' === $slug || '' === $tpl ) {
		return 0;
	}
	$page = get_page_by_path( $slug );
	if ( ! ( $page instanceof WP_Post ) ) {
		return 0;
	}
	$current = (string) get_page_template_slug( (int) $page->ID );
	if ( $current !== $tpl ) {
		update_post_meta( (int) $page->ID, '_wp_page_template', $tpl );
		clean_post_cache( (int) $page->ID );
	}
	return (int) $page->ID;
}

/**
 * Xóa cache trang landing sau khi đổi ảnh (LiteSpeed / WP Rocket / Super Cache…).
 *
 * @param string $route Route key.
 */
function annam_limo_landing_purge_route_caches( $route ) {
	$route = sanitize_key( (string) $route );
	annam_limo_landing_ensure_route_page_template( $route );

	$pages = annam_limo_landing_get_pages_for_route( $route );
	$slug  = annam_limo_landing_default_page_slug( $route );
	if ( $slug ) {
		$by_slug = get_page_by_path( $slug );
		if ( $by_slug instanceof WP_Post ) {
			$found = false;
			foreach ( $pages as $p ) {
				if ( (int) $p->ID === (int) $by_slug->ID ) {
					$found = true;
					break;
				}
			}
			if ( ! $found ) {
				$pages[] = $by_slug;
			}
		}
	}

	foreach ( $pages as $page ) {
		if ( ! ( $page instanceof WP_Post ) ) {
			continue;
		}
		$page_id = (int) $page->ID;
		clean_post_cache( $page_id );
		$url = get_permalink( $page_id );
		if ( ! is_string( $url ) || '' === $url ) {
			continue;
		}

		if ( function_exists( 'rocket_clean_post' ) ) {
			rocket_clean_post( $page_id );
		}
		if ( function_exists( 'wp_cache_post_change' ) ) {
			wp_cache_post_change( $page_id );
		}
		if ( function_exists( 'w3tc_flush_post' ) ) {
			w3tc_flush_post( $page_id );
		}
		if ( has_action( 'litespeed_purge_post' ) ) {
			do_action( 'litespeed_purge_post', $page_id );
		}
		if ( has_action( 'litespeed_purge_url' ) ) {
			do_action( 'litespeed_purge_url', $url );
		}
		if ( function_exists( 'litespeed_purge_url' ) ) {
			litespeed_purge_url( $url );
		}
	}

	$option = function_exists( 'annam_limo_landing_option_name' )
		? annam_limo_landing_option_name( 'annam_limo_landing_images', $route )
		: '';
	if ( '' !== $option ) {
		wp_cache_delete( $option, 'options' );
		wp_cache_delete( 'alloptions', 'options' );
	}

	do_action( 'annam_limo_landing_purge_route_caches', $route, $pages );
}
