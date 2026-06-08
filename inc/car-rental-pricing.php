<?php
/**
 * Helpers bảng giá thuê xe hợp đồng.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/car-rental-pricing-data.php';

/**
 * @param int $amount Số tiền VNĐ.
 * @return string
 */
function annam_car_rental_format_price( $amount ) {
	$amount = max( 0, (int) $amount );
	return number_format( $amount, 0, ',', '.' ) . 'đ';
}

/**
 * @param int $amount Số tiền VNĐ.
 * @return string
 */
function annam_car_rental_format_price_from( $amount ) {
	return sprintf(
		/* translators: %s: formatted price */
		__( 'Từ %s', 'generatepress_child' ),
		annam_car_rental_format_price( $amount )
	);
}

/**
 * Nhãn tuyến hiển thị trong bảng giá (⇌ thay cho ↔).
 *
 * @param string $label Route label.
 * @return string
 */
function annam_car_rental_format_route_label_display( $label ) {
	return str_replace( '↔', '⇌', (string) $label );
}

/**
 * Nhãn tuyến hiển thị trong bảng giá (có tiền tố Thuê xe).
 *
 * @param string $label Route label.
 * @return string
 */
function annam_car_rental_format_route_pricing_label( $label ) {
	$display = annam_car_rental_format_route_label_display( $label );
	$prefix  = __( 'Thuê xe', 'generatepress_child' );

	if ( '' === $display ) {
		return $prefix;
	}

	if ( 0 === stripos( $display, $prefix ) ) {
		return $display;
	}

	return $prefix . ' ' . $display;
}

/**
 * Tách nhãn tuyến "Hà Nội ↔ Tỉnh" thành điểm đón / đến.
 *
 * @param string $label Route label.
 * @return array{pickup:string,destination:string}
 */
function annam_car_rental_parse_route_label( $label ) {
	$label = (string) $label;
	$parts = preg_split( '/\s*(?:↔|⇌)\s*/u', $label, 2 );
	return array(
		'pickup'      => isset( $parts[0] ) ? trim( $parts[0] ) : '',
		'destination' => isset( $parts[1] ) ? trim( $parts[1] ) : '',
	);
}

/**
 * @param string $vehicle_type Vehicle key.
 * @return bool
 */
function annam_car_rental_is_valid_vehicle_type( $vehicle_type ) {
	$types = annam_car_rental_get_vehicle_types();
	return isset( $types[ $vehicle_type ] );
}

/**
 * @param string $vehicle_type Vehicle key.
 * @return array<int,array<string,mixed>>
 */
function annam_car_rental_get_routes_for_vehicle( $vehicle_type ) {
	$vehicle_type = sanitize_key( (string) $vehicle_type );
	if ( ! annam_car_rental_is_valid_vehicle_type( $vehicle_type ) ) {
		return array();
	}

	$out = array();
	foreach ( annam_car_rental_get_routes_raw() as $route ) {
		$price = isset( $route['prices'][ $vehicle_type ] ) ? (int) $route['prices'][ $vehicle_type ] : 0;
		if ( $price <= 0 ) {
			continue;
		}
		$parsed = annam_car_rental_parse_route_label( $route['label'] );
		$out[]  = array(
			'id'           => $route['id'],
			'label'        => $route['label'],
			'label_display'=> annam_car_rental_format_route_label_display( $route['label'] ),
			'pickup'       => $parsed['pickup'] ?: 'Hà Nội',
			'destination'  => $parsed['destination'],
			'hot'          => ! empty( $route['hot'] ),
			'price'        => $price,
			'price_label'  => annam_car_rental_format_price_from( $price ),
			'price_plain'  => annam_car_rental_format_price( $price ),
			'search_key'   => mb_strtolower( $route['label'] . ' ' . $parsed['destination'], 'UTF-8' ),
		);
	}

	return apply_filters( 'annam_car_rental_routes_for_vehicle', $out, $vehicle_type );
}

/**
 * @param string $vehicle_type Vehicle key.
 * @return int
 */
function annam_car_rental_get_price_from( $vehicle_type ) {
	$routes = annam_car_rental_get_routes_for_vehicle( $vehicle_type );
	if ( empty( $routes ) ) {
		return 0;
	}
	$min = PHP_INT_MAX;
	foreach ( $routes as $route ) {
		$min = min( $min, (int) $route['price'] );
	}
	return PHP_INT_MAX === $min ? 0 : $min;
}

/**
 * @param string $vehicle_type Vehicle key.
 * @param string $route_id     Route id.
 * @return int
 */
function annam_car_rental_get_route_price( $vehicle_type, $route_id ) {
	$vehicle_type = sanitize_key( (string) $vehicle_type );
	$route_id     = sanitize_key( (string) $route_id );
	foreach ( annam_car_rental_get_routes_raw() as $route ) {
		if ( $route['id'] !== $route_id ) {
			continue;
		}
		return isset( $route['prices'][ $vehicle_type ] ) ? (int) $route['prices'][ $vehicle_type ] : 0;
	}
	return 0;
}

/**
 * @param string $vehicle_type Vehicle key.
 * @param int    $limit        Max routes; 0 = all hot routes.
 * @return array<int,array<string,mixed>>
 */
function annam_car_rental_get_hot_routes( $vehicle_type, $limit = 0 ) {
	$routes = annam_car_rental_get_routes_for_vehicle( $vehicle_type );
	$hot    = array_values(
		array_filter(
			$routes,
			static function ( $r ) {
				return ! empty( $r['hot'] );
			}
		)
	);
	$limit = (int) $limit;
	if ( $limit > 0 ) {
		return array_slice( $hot, 0, $limit );
	}
	return $hot;
}

/**
 * Hành trình phổ biến — lấy từ toàn bộ tuyến hot của loại xe.
 *
 * @param string $vehicle_type Vehicle key.
 * @return array<int,array<string,mixed>>
 */
function annam_car_rental_get_featured_journeys( $vehicle_type ) {
	$out = array();
	foreach ( annam_car_rental_get_hot_routes( $vehicle_type ) as $route ) {
		$out[] = array(
			'route_id'      => $route['id'],
			'title'         => $route['label'],
			'title_display' => $route['label_display'],
			'subtitle'      => $route['destination'],
			'pickup'        => $route['pickup'],
			'destination'   => $route['destination'],
			'price'         => $route['price'],
			'price_label'   => $route['price_label'],
		);
	}

	return apply_filters( 'annam_car_rental_featured_journeys', $out, $vehicle_type );
}

/**
 * Resolve vehicle type from page slug.
 *
 * @param string $slug Page slug.
 * @return string
 */
function annam_car_rental_vehicle_type_from_slug( $slug ) {
	$slug = sanitize_title( (string) $slug );
	$map  = array(
		'thue-xe-7-cho'                 => '7-cho',
		'thue-xe-limousine-9-11-cho'    => 'limousine-9-11',
		'thue-xe-16-cho'                => '16-cho',
		'thue-xe-29-cho'                => '29-cho',
		'thue-xe-45-cho'                => '45-cho',
		'thue-xe-hop-dong'              => 'hub',
	);
	return isset( $map[ $slug ] ) ? $map[ $slug ] : '';
}

/**
 * @param string $vehicle_type Vehicle key.
 * @return string
 */
/**
 * @return string
 */
function annam_car_rental_get_hub_url() {
	$page = get_page_by_path( 'thue-xe-hop-dong' );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}
	return home_url( '/thue-xe-hop-dong/' );
}

/**
 * @param string $vehicle_type Vehicle key.
 * @return string
 */
function annam_car_rental_get_vehicle_page_url( $vehicle_type ) {
	$types = annam_car_rental_get_vehicle_types();
	if ( ! isset( $types[ $vehicle_type ] ) ) {
		return home_url( '/' );
	}
	$page = get_page_by_path( $types[ $vehicle_type ]['slug'] );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}
	return home_url( $types[ $vehicle_type ]['path'] );
}
