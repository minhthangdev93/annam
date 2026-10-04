<?php
/**
 * Admin ảnh + email lead Landing thuê limo HN–Sapa.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ANNAM_LIMO_CHARTER_IMAGES_OPTION' ) ) {
	define( 'ANNAM_LIMO_CHARTER_IMAGES_OPTION', 'annam_limo_charter_landing_images' );
}

if ( ! defined( 'ANNAM_LIMO_CHARTER_CAPTIONS_OPTION' ) ) {
	define( 'ANNAM_LIMO_CHARTER_CAPTIONS_OPTION', 'annam_limo_charter_landing_captions' );
}

if ( ! defined( 'ANNAM_LIMO_CHARTER_SETTINGS_OPTION' ) ) {
	define( 'ANNAM_LIMO_CHARTER_SETTINGS_OPTION', 'annam_limo_charter_landing_settings' );
}

/**
 * @return array<string,array<string,string>>
 */
function annam_limo_charter_landing_get_image_slots() {
	$fallbacks = array(
		'hero'        => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=80',
		'price-9'     => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?auto=format&fit=crop&w=1200&q=80',
		'price-11'    => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=1200&q=80',
		'gallery-1'   => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=80',
		'gallery-2'   => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=1200&q=80',
		'gallery-3'   => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?auto=format&fit=crop&w=1200&q=80',
		'gallery-4'   => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=1200&q=80',
		'gallery-5'   => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1200&q=80',
		'gallery-6'   => 'https://images.unsplash.com/photo-1485291571150-772bcfc10da5?auto=format&fit=crop&w=1200&q=80',
		'staff-1'     => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=800&q=80',
		'staff-2'     => 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=800&q=80',
		'staff-3'     => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=800&q=80',
		'staff-4'     => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=800&q=80',
		'staff-5'     => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=800&q=80',
		'staff-6'     => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=800&q=80',
		'cross-limo'  => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=800&q=80',
		'cross-cabin' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=800&q=80',
		'cross-tour'  => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
	);

	$slots = array(
		'hero'        => array(
			'label'           => __( 'Hero — ảnh xe limousine', 'generatepress_child' ),
			'section'         => 'hero',
			'placement'       => __( 'Cột ảnh hero (desktop cạnh form đặt xe)', 'generatepress_child' ),
			'recommended'     => '1400 × 933 px (tối thiểu 1200 × 800)',
			'ratio'           => '3:2',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh xe thật, sáng, ngang — crop đúng 3:2 để không bị cắt trên desktop.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['hero'],
			'default_caption' => 'Limousine thuê nguyên xe HN–Sapa',
		),
		'price-9'     => array(
			'label'           => __( 'Card giá — Limousine 9 chỗ', 'generatepress_child' ),
			'section'         => 'pricing',
			'placement'       => __( 'Ảnh trên card giá 9 chỗ', 'generatepress_child' ),
			'recommended'     => '1200 × 800 px',
			'ratio'           => '3:2',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Nên là ảnh khoang ghế / ngoại thất 9 chỗ rõ biển hoặc nội thất.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['price-9'],
			'default_caption' => 'Limousine 9 chỗ',
		),
		'price-11'    => array(
			'label'           => __( 'Card giá — Limousine 11 chỗ', 'generatepress_child' ),
			'section'         => 'pricing',
			'placement'       => __( 'Ảnh trên card giá 11 chỗ', 'generatepress_child' ),
			'recommended'     => '1200 × 800 px',
			'ratio'           => '3:2',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Nên là ảnh khoang ghế / ngoại thất 11 chỗ để phân biệt với card 9 chỗ.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['price-11'],
			'default_caption' => 'Limousine 11 chỗ',
		),
		'gallery-1'   => array(
			'label'           => __( 'Gallery 1', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Hình ảnh xe — ô 1 (hàng trên, trái)', 'generatepress_child' ),
			'recommended'     => '1200 × 800 px',
			'ratio'           => '3:2',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh ngang 3:2. Ghi chú hiển thị dưới ảnh trên trang.', 'generatepress_child' ),
			'has_caption'     => true,
			'fallback'        => $fallbacks['gallery-1'],
			'default_caption' => '9 chỗ — ngoại thất',
		),
		'gallery-2'   => array(
			'label'           => __( 'Gallery 2', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Hình ảnh xe — ô 2 (hàng trên, giữa)', 'generatepress_child' ),
			'recommended'     => '1200 × 800 px',
			'ratio'           => '3:2',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh ngang 3:2. Ghi chú hiển thị dưới ảnh trên trang.', 'generatepress_child' ),
			'has_caption'     => true,
			'fallback'        => $fallbacks['gallery-2'],
			'default_caption' => '11 chỗ — ngoại thất',
		),
		'gallery-3'   => array(
			'label'           => __( 'Gallery 3', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Hình ảnh xe — ô 3 (hàng trên, phải)', 'generatepress_child' ),
			'recommended'     => '1200 × 800 px',
			'ratio'           => '3:2',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh ngang 3:2. Ghi chú hiển thị dưới ảnh trên trang.', 'generatepress_child' ),
			'has_caption'     => true,
			'fallback'        => $fallbacks['gallery-3'],
			'default_caption' => '9 chỗ — khoang ghế',
		),
		'gallery-4'   => array(
			'label'           => __( 'Gallery 4', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Hình ảnh xe — ô 4 (hàng dưới, trái)', 'generatepress_child' ),
			'recommended'     => '1200 × 800 px',
			'ratio'           => '3:2',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh ngang 3:2. Ghi chú hiển thị dưới ảnh trên trang.', 'generatepress_child' ),
			'has_caption'     => true,
			'fallback'        => $fallbacks['gallery-4'],
			'default_caption' => '11 chỗ — khoang ghế',
		),
		'gallery-5'   => array(
			'label'           => __( 'Gallery 5', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Hình ảnh xe — ô 5 (hàng dưới, giữa)', 'generatepress_child' ),
			'recommended'     => '1200 × 800 px',
			'ratio'           => '3:2',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh ngang 3:2. Ghi chú hiển thị dưới ảnh trên trang.', 'generatepress_child' ),
			'has_caption'     => true,
			'fallback'        => $fallbacks['gallery-5'],
			'default_caption' => 'Hành lý / sẵn sàng xuất phát',
		),
		'gallery-6'   => array(
			'label'           => __( 'Gallery 6', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Hình ảnh xe — ô 6 (hàng dưới, phải)', 'generatepress_child' ),
			'recommended'     => '1200 × 800 px',
			'ratio'           => '3:2',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh ngang 3:2. Ghi chú hiển thị dưới ảnh trên trang.', 'generatepress_child' ),
			'has_caption'     => true,
			'fallback'        => $fallbacks['gallery-6'],
			'default_caption' => 'Trên đường HN ⇄ Sapa',
		),
		'staff-1'     => array(
			'label'           => __( 'Uy tín — ảnh 1', 'generatepress_child' ),
			'section'         => 'staff',
			'placement'       => __( 'Khối Uy tín — lưới 3×2 (ô 1)', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4:3',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh đội xe / nhân viên / điểm đón thật — ưu tiên mặt người hoặc xe rõ.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['staff-1'],
			'default_caption' => 'Đội vận hành An Nam',
		),
		'staff-2'     => array(
			'label'           => __( 'Uy tín — ảnh 2', 'generatepress_child' ),
			'section'         => 'staff',
			'placement'       => __( 'Khối Uy tín — lưới 3×2 (ô 2)', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4:3',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh đội xe / nhân viên / điểm đón thật.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['staff-2'],
			'default_caption' => 'Tài xế / phục vụ',
		),
		'staff-3'     => array(
			'label'           => __( 'Uy tín — ảnh 3', 'generatepress_child' ),
			'section'         => 'staff',
			'placement'       => __( 'Khối Uy tín — lưới 3×2 (ô 3)', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4:3',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh đội xe / nhân viên / điểm đón thật.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['staff-3'],
			'default_caption' => 'Đội xe sẵn sàng',
		),
		'staff-4'     => array(
			'label'           => __( 'Uy tín — ảnh 4', 'generatepress_child' ),
			'section'         => 'staff',
			'placement'       => __( 'Khối Uy tín — lưới 3×2 (ô 4)', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4:3',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh đội xe / nhân viên / điểm đón thật.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['staff-4'],
			'default_caption' => 'Xe limousine An Nam',
		),
		'staff-5'     => array(
			'label'           => __( 'Uy tín — ảnh 5', 'generatepress_child' ),
			'section'         => 'staff',
			'placement'       => __( 'Khối Uy tín — lưới 3×2 (ô 5)', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4:3',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh đội xe / nhân viên / điểm đón thật.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['staff-5'],
			'default_caption' => 'Đón khách / điểm đón',
		),
		'staff-6'     => array(
			'label'           => __( 'Uy tín — ảnh 6', 'generatepress_child' ),
			'section'         => 'staff',
			'placement'       => __( 'Khối Uy tín — lưới 3×2 (ô 6)', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4:3',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh đội xe / nhân viên / điểm đón thật.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['staff-6'],
			'default_caption' => 'Đội vận hành sẵn sàng',
		),
		'cross-limo'  => array(
			'label'           => __( 'Gợi ý khác — vé limousine ghế', 'generatepress_child' ),
			'section'         => 'cross',
			'placement'       => __( 'Card cross-sell vé ghế HN ⇄ Sapa', 'generatepress_child' ),
			'recommended'     => '1000 × 750 px',
			'ratio'           => '4:3',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh minh họa vé ghế limo — crop 4:3.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['cross-limo'],
			'default_caption' => 'Vé limousine ghế',
		),
		'cross-cabin' => array(
			'label'           => __( 'Gợi ý khác — cabin giường nằm', 'generatepress_child' ),
			'section'         => 'cross',
			'placement'       => __( 'Card cross-sell cabin VIP', 'generatepress_child' ),
			'recommended'     => '1000 × 750 px',
			'ratio'           => '4:3',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh nội thất cabin/giường nằm — crop 4:3.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['cross-cabin'],
			'default_caption' => 'Cabin giường nằm',
		),
		'cross-tour'  => array(
			'label'           => __( 'Gợi ý khác — tour Sapa', 'generatepress_child' ),
			'section'         => 'cross',
			'placement'       => __( 'Card cross-sell tour 3N2Đ', 'generatepress_child' ),
			'recommended'     => '1000 × 750 px',
			'ratio'           => '4:3',
			'formats'         => 'JPG / WebP',
			'tip'             => __( 'Ảnh Sapa / Fansipan / Cát Cát — crop 4:3.', 'generatepress_child' ),
			'has_caption'     => false,
			'fallback'        => $fallbacks['cross-tour'],
			'default_caption' => 'Tour Sapa 3N2Đ',
		),
	);

	return apply_filters( 'annam_limo_charter_landing_image_slots', $slots );
}

/**
 * @param string $key Slot.
 * @return string
 */
function annam_limo_charter_landing_get_image_caption( $key ) {
	$key   = sanitize_key( $key );
	$slots = annam_limo_charter_landing_get_image_slots();
	if ( ! isset( $slots[ $key ] ) ) {
		return '';
	}
	$saved = get_option( ANNAM_LIMO_CHARTER_CAPTIONS_OPTION, array() );
	if ( is_array( $saved ) && isset( $saved[ $key ] ) && is_string( $saved[ $key ] ) && '' !== trim( $saved[ $key ] ) ) {
		return sanitize_text_field( $saved[ $key ] );
	}
	return isset( $slots[ $key ]['default_caption'] ) ? (string) $slots[ $key ]['default_caption'] : '';
}

/**
 * @param string $key Slot.
 * @return int
 */
function annam_limo_charter_landing_get_image_attachment_id( $key ) {
	$key = sanitize_key( $key );
	if ( '' === $key ) {
		return 0;
	}
	$slots = annam_limo_charter_landing_get_image_slots();
	if ( ! isset( $slots[ $key ] ) ) {
		return 0;
	}
	$saved = get_option( ANNAM_LIMO_CHARTER_IMAGES_OPTION, array() );
	if ( ! is_array( $saved ) || empty( $saved[ $key ] ) ) {
		return 0;
	}
	$id = absint( $saved[ $key ] );
	return ( $id > 0 && wp_attachment_is_image( $id ) ) ? $id : 0;
}

/**
 * Danh sách ảnh lightbox toàn trang (hero → giá → gallery → uy tín).
 *
 * @return array<int,array{slot:string,src:string,caption:string}>
 */
function annam_limo_charter_landing_get_lightbox_items() {
	$config = function_exists( 'annam_limo_charter_landing_get_config' )
		? annam_limo_charter_landing_get_config()
		: array();

	$ordered = array( 'hero', 'price-9', 'price-11' );

	if ( ! empty( $config['gallery'] ) && is_array( $config['gallery'] ) ) {
		foreach ( $config['gallery'] as $item ) {
			if ( ! empty( $item['slot'] ) ) {
				$ordered[] = (string) $item['slot'];
			}
		}
	}

	foreach ( array( 'staff-1', 'staff-2', 'staff-3', 'staff-4', 'staff-5', 'staff-6' ) as $staff_slot ) {
		$ordered[] = $staff_slot;
	}

	$out = array();
	foreach ( $ordered as $slot ) {
		$url = annam_limo_charter_landing_image_url( $slot );
		if ( '' === $url ) {
			continue;
		}
		$out[] = array(
			'slot'    => $slot,
			'src'     => $url,
			'caption' => annam_limo_charter_landing_get_image_caption( $slot ),
		);
	}

	return apply_filters( 'annam_limo_charter_landing_lightbox_items', $out );
}

/**
 * Index lightbox theo slot (hoặc -1).
 *
 * @param string $slot Slot key.
 * @return int
 */
function annam_limo_charter_landing_lightbox_index( $slot ) {
	$slot = sanitize_key( (string) $slot );
	if ( '' === $slot ) {
		return -1;
	}
	foreach ( annam_limo_charter_landing_get_lightbox_items() as $i => $item ) {
		if ( isset( $item['slot'] ) && $slot === $item['slot'] ) {
			return (int) $i;
		}
	}
	return -1;
}

/**
 * @param string $key Slot.
 * @return string
 */
function annam_limo_charter_landing_image_url( $key ) {
	$key   = sanitize_key( $key );
	$slots = annam_limo_charter_landing_get_image_slots();
	if ( ! isset( $slots[ $key ] ) ) {
		return '';
	}
	$aid = annam_limo_charter_landing_get_image_attachment_id( $key );
	if ( $aid > 0 ) {
		$url = wp_get_attachment_image_url( $aid, 'large' );
		return is_string( $url ) ? $url : '';
	}
	return isset( $slots[ $key ]['fallback'] ) ? (string) $slots[ $key ]['fallback'] : '';
}

/**
 * @return array<string,mixed>
 */
function annam_limo_charter_landing_get_settings() {
	$raw = get_option( ANNAM_LIMO_CHARTER_SETTINGS_OPTION, array() );
	if ( ! is_array( $raw ) ) {
		$raw = array();
	}
	$default_maps = function_exists( 'annam_limo_charter_landing_default_maps_url' )
		? annam_limo_charter_landing_default_maps_url()
		: ( function_exists( 'annam_contact_default_maps_place_url' ) ? annam_contact_default_maps_place_url() : '' );
	$map_saved = isset( $raw['map_address'] ) ? trim( (string) $raw['map_address'] ) : '';
	// Short link cũ → place URL đầy đủ (để iframe lấy đúng nhãn An Nam Discovery).
	if ( '' !== $map_saved && false !== strpos( $map_saved, '6mQkPgdUMFhRfRnK7' ) ) {
		$map_saved = $default_maps;
	}

	$lead_saved = isset( $raw['lead_emails'] ) ? trim( (string) $raw['lead_emails'] ) : '';

	return array(
		// Mặc định luôn nhận lead tại annamdiscoveryvn@gmail.com nếu admin để trống.
		'lead_emails' => '' !== $lead_saved ? $lead_saved : 'annamdiscoveryvn@gmail.com',
		// Mặc định dùng link place Maps — không bắt buộc cấu hình trong admin.
		'map_address' => '' !== $map_saved ? $map_saved : $default_maps,
	);
}

/**
 * Email mặc định nhận lead landing thuê limo.
 *
 * @return string
 */
function annam_limo_charter_landing_default_lead_email() {
	return (string) apply_filters( 'annam_limo_charter_landing_default_lead_email', 'annamdiscoveryvn@gmail.com' );
}

/**
 * @return string[]
 */
function annam_limo_charter_landing_get_lead_emails() {
	$settings = annam_limo_charter_landing_get_settings();
	$raw      = isset( $settings['lead_emails'] ) ? (string) $settings['lead_emails'] : '';
	$parts    = preg_split( '/[,;\s]+/', $raw );
	$out      = array();
	foreach ( (array) $parts as $email ) {
		$email = sanitize_email( trim( (string) $email ) );
		if ( $email && is_email( $email ) ) {
			$out[] = $email;
		}
	}
	if ( empty( $out ) ) {
		$fallback = annam_limo_charter_landing_default_lead_email();
		return is_email( $fallback ) ? array( $fallback ) : array();
	}
	return array_values( array_unique( $out ) );
}

/**
 * Điền slot ảnh còn trống từ pool Media (dùng khi thêm slot mới sau lần seed đầu).
 */
function annam_limo_charter_landing_fill_missing_image_slots() {
	$slots = annam_limo_charter_landing_get_image_slots();
	$saved = get_option( ANNAM_LIMO_CHARTER_IMAGES_OPTION, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	$missing = array();
	foreach ( array_keys( $slots ) as $key ) {
		$id = isset( $saved[ $key ] ) ? absint( $saved[ $key ] ) : 0;
		if ( $id <= 0 || ! wp_attachment_is_image( $id ) ) {
			$missing[] = $key;
		}
	}
	if ( empty( $missing ) ) {
		return;
	}

	$pool = array();
	foreach ( $saved as $aid ) {
		$aid = absint( $aid );
		if ( $aid > 0 && wp_attachment_is_image( $aid ) ) {
			$pool[] = $aid;
		}
	}
	if ( empty( $pool ) ) {
		$query = new WP_Query(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post_mime_type' => 'image',
				'posts_per_page' => 40,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		if ( ! empty( $query->posts ) ) {
			foreach ( $query->posts as $aid ) {
				$aid  = absint( $aid );
				$meta = wp_get_attachment_metadata( $aid );
				$w    = isset( $meta['width'] ) ? (int) $meta['width'] : 0;
				$h    = isset( $meta['height'] ) ? (int) $meta['height'] : 0;
				if ( $w >= 800 && $h >= 500 ) {
					$pool[] = $aid;
				}
			}
		}
	}
	$pool = array_values( array_unique( array_filter( $pool ) ) );
	if ( empty( $pool ) ) {
		return;
	}

	$i = 0;
	foreach ( $missing as $key ) {
		$saved[ $key ] = $pool[ $i % count( $pool ) ];
		++$i;
	}
	update_option( ANNAM_LIMO_CHARTER_IMAGES_OPTION, $saved, false );
}

/**
 * Seed tạm ảnh từ Media Library (ưu tiên ảnh rộng ≥ 800px) + reuse slot limo seat nếu có.
 */
function annam_limo_charter_landing_maybe_seed_images() {
	$slots = annam_limo_charter_landing_get_image_slots();
	$saved = get_option( ANNAM_LIMO_CHARTER_IMAGES_OPTION, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	// Đã seed trước đó: chỉ bổ sung slot mới (vd. staff-4…6).
	if ( get_option( 'annam_limo_charter_landing_images_seeded' ) ) {
		annam_limo_charter_landing_fill_missing_image_slots();
		return;
	}

	$has_any = false;
	foreach ( array_keys( $slots ) as $key ) {
		if ( ! empty( $saved[ $key ] ) && absint( $saved[ $key ] ) > 0 ) {
			$has_any = true;
			break;
		}
	}
	if ( $has_any ) {
		annam_limo_charter_landing_fill_missing_image_slots();
		update_option( 'annam_limo_charter_landing_images_seeded', 1, false );
		return;
	}

	$pool = array();

	// Reuse limo seat gallery attachments if mapped.
	if ( defined( 'ANNAM_LIMO_LANDING_IMAGES_OPTION' ) ) {
		$limo = get_option( ANNAM_LIMO_LANDING_IMAGES_OPTION, array() );
		if ( is_array( $limo ) ) {
			foreach ( $limo as $aid ) {
				$aid = absint( $aid );
				if ( $aid > 0 && wp_attachment_is_image( $aid ) ) {
					$pool[] = $aid;
				}
			}
		}
	}

	// Pull wide-ish images from media library.
	$query = new WP_Query(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_mime_type' => 'image',
			'posts_per_page' => 40,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	if ( ! empty( $query->posts ) ) {
		foreach ( $query->posts as $aid ) {
			$aid = absint( $aid );
			$meta = wp_get_attachment_metadata( $aid );
			$w    = isset( $meta['width'] ) ? (int) $meta['width'] : 0;
			$h    = isset( $meta['height'] ) ? (int) $meta['height'] : 0;
			if ( $w >= 800 && $h >= 500 ) {
				$pool[] = $aid;
			}
		}
	}

	$pool = array_values( array_unique( array_filter( $pool ) ) );
	if ( empty( $pool ) ) {
		// Still mark seeded so we don't query forever; fallbacks (Unsplash) remain.
		update_option( 'annam_limo_charter_landing_images_seeded', 1, false );
		return;
	}

	$i     = 0;
	$clean = array();
	foreach ( array_keys( $slots ) as $key ) {
		$clean[ $key ] = $pool[ $i % count( $pool ) ];
		++$i;
	}
	update_option( ANNAM_LIMO_CHARTER_IMAGES_OPTION, $clean, false );
	update_option( 'annam_limo_charter_landing_images_seeded', 1, false );
}
add_action( 'init', 'annam_limo_charter_landing_maybe_seed_images', 35 );

/**
 * Register admin menu.
 */
function annam_limo_charter_landing_images_register_menu() {
	add_submenu_page(
		'annam-settings',
		__( 'Landing Thuê Limo 9–11 chỗ', 'generatepress_child' ),
		__( 'Landing Thuê Limo 9–11 chỗ', 'generatepress_child' ),
		'manage_options',
		'annam-limo-charter-landing-images',
		'annam_limo_charter_landing_images_render_admin_page'
	);
}
add_action( 'admin_menu', 'annam_limo_charter_landing_images_register_menu', 22 );

/**
 * Save images / captions.
 */
function annam_limo_charter_landing_images_maybe_save() {
	if ( ! is_admin() || empty( $_POST['annam_limo_charter_landing_images_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}
	if ( empty( $_GET['page'] ) || 'annam-limo-charter-landing-images' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'annam_save_limo_charter_landing_images', 'annam_limo_charter_landing_images_nonce' );

	$input = isset( $_POST['annam_limo_charter_landing_images'] ) && is_array( $_POST['annam_limo_charter_landing_images'] )
		? wp_unslash( $_POST['annam_limo_charter_landing_images'] )
		: array();

	$clean = array();
	foreach ( annam_limo_charter_landing_get_image_slots() as $key => $slot ) {
		$clean[ $key ] = isset( $input[ $key ] ) ? absint( $input[ $key ] ) : 0;
	}
	update_option( ANNAM_LIMO_CHARTER_IMAGES_OPTION, $clean, false );

	$cap_input = isset( $_POST['annam_limo_charter_landing_captions'] ) && is_array( $_POST['annam_limo_charter_landing_captions'] )
		? wp_unslash( $_POST['annam_limo_charter_landing_captions'] )
		: array();
	$cap_prev = get_option( ANNAM_LIMO_CHARTER_CAPTIONS_OPTION, array() );
	if ( ! is_array( $cap_prev ) ) {
		$cap_prev = array();
	}
	$cap_clean = $cap_prev;
	foreach ( annam_limo_charter_landing_get_image_slots() as $key => $slot ) {
		if ( empty( $slot['has_caption'] ) ) {
			continue;
		}
		$raw               = isset( $cap_input[ $key ] ) ? (string) $cap_input[ $key ] : '';
		$cap_clean[ $key ] = sanitize_text_field( $raw );
	}
	update_option( ANNAM_LIMO_CHARTER_CAPTIONS_OPTION, $cap_clean, false );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'        => 'annam-limo-charter-landing-images',
				'annam_saved' => '1',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_init', 'annam_limo_charter_landing_images_maybe_save' );

/**
 * Save settings.
 */
function annam_limo_charter_landing_settings_maybe_save() {
	if ( ! is_admin() || empty( $_POST['annam_limo_charter_landing_settings_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}
	if ( empty( $_GET['page'] ) || 'annam-limo-charter-landing-images' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'annam_save_limo_charter_landing_settings', 'annam_limo_charter_landing_settings_nonce' );

	$emails = isset( $_POST['annam_limo_charter_lead_emails'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['annam_limo_charter_lead_emails'] ) ) : '';
	$map    = isset( $_POST['annam_limo_charter_map_address'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['annam_limo_charter_map_address'] ) ) : '';

	update_option(
		ANNAM_LIMO_CHARTER_SETTINGS_OPTION,
		array(
			'lead_emails' => $emails,
			'map_address' => $map,
		),
		false
	);

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'        => 'annam-limo-charter-landing-images',
				'annam_saved' => '1',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_init', 'annam_limo_charter_landing_settings_maybe_save' );

/**
 * @param string $hook_suffix Hook.
 */
function annam_limo_charter_landing_images_admin_assets( $hook_suffix ) {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( false === strpos( (string) $hook_suffix, 'annam-limo-charter-landing-images' ) && 'annam-limo-charter-landing-images' !== $page ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	wp_enqueue_media();

	$css_path = $dir . '/assets/css/annam-admin.css';
	$js_path  = $dir . '/assets/js/annam-admin.js';

	if ( file_exists( $css_path ) ) {
		wp_enqueue_style( 'annam-admin', $uri . '/assets/css/annam-admin.css', array(), (string) filemtime( $css_path ) );
	}
	if ( file_exists( $js_path ) ) {
		wp_enqueue_script(
			'annam-admin',
			$uri . '/assets/js/annam-admin.js',
			array( 'jquery', 'media-upload', 'media-views', 'media-editor' ),
			(string) filemtime( $js_path ),
			true
		);
		wp_localize_script(
			'annam-admin',
			'annamAdminL10n',
			array(
				'pickTitle'        => __( 'Chọn ảnh', 'generatepress_child' ),
				'pickButton'       => __( 'Dùng ảnh này', 'generatepress_child' ),
				'placeholderImage' => __( 'Chưa chọn ảnh', 'generatepress_child' ),
				'mediaUnavailable' => __( 'Không mở được thư viện ảnh. Vui lòng tải lại trang.', 'generatepress_child' ),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'annam_limo_charter_landing_images_admin_assets' );

/**
 * @param string               $key      Slot.
 * @param array<string,mixed>  $slot     Meta.
 * @param int                  $value_id Attachment.
 * @param string               $caption  Caption đã lưu.
 */
function annam_limo_charter_landing_images_render_field( $key, array $slot, $value_id, $caption = '' ) {
	$value_id    = absint( $value_id );
	$name        = 'annam_limo_charter_landing_images[' . $key . ']';
	$preview     = $value_id && wp_attachment_is_image( $value_id )
		? wp_get_attachment_image_url( $value_id, 'medium' )
		: '';
	$has_caption = ! empty( $slot['has_caption'] );
	$cap_name    = 'annam_limo_charter_landing_captions[' . $key . ']';
	$cap_value   = '' !== trim( (string) $caption )
		? (string) $caption
		: ( isset( $slot['default_caption'] ) ? (string) $slot['default_caption'] : '' );

	$meta = array();
	if ( $value_id ) {
		$file = get_attached_file( $value_id );
		if ( $file && file_exists( $file ) ) {
			$size = @getimagesize( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( is_array( $size ) && ! empty( $size[0] ) && ! empty( $size[1] ) ) {
				/* translators: 1: width px, 2: height px */
				$meta[] = sprintf( __( 'File hiện tại: %1$d × %2$d px', 'generatepress_child' ), (int) $size[0], (int) $size[1] );
			}
		}
	}
	?>
	<div class="annam-about-image-field annam-cabin-image-field" data-annam-about-image>
		<label class="annam-about-image-field__label"><?php echo esc_html( $slot['label'] ); ?></label>
		<p class="annam-cabin-image-field__placement"><?php echo esc_html( $slot['placement'] ); ?></p>
		<ul class="annam-cabin-image-field__specs">
			<li>
				<strong><?php esc_html_e( 'Kích thước khuyến nghị:', 'generatepress_child' ); ?></strong>
				<?php echo esc_html( isset( $slot['recommended'] ) ? (string) $slot['recommended'] : '' ); ?>
			</li>
			<li>
				<strong><?php esc_html_e( 'Tỷ lệ:', 'generatepress_child' ); ?></strong>
				<?php echo esc_html( isset( $slot['ratio'] ) ? (string) $slot['ratio'] : '' ); ?>
			</li>
			<li>
				<strong><?php esc_html_e( 'Định dạng:', 'generatepress_child' ); ?></strong>
				<?php echo esc_html( isset( $slot['formats'] ) ? (string) $slot['formats'] : 'JPG / WebP' ); ?>
			</li>
		</ul>
		<?php if ( ! empty( $slot['tip'] ) ) : ?>
			<p class="description"><?php echo esc_html( (string) $slot['tip'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $meta ) ) : ?>
			<p class="annam-cabin-image-field__current"><?php echo esc_html( implode( ' · ', $meta ) ); ?></p>
		<?php endif; ?>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value_id ); ?>" class="annam-about-attachment-id" />
		<div class="annam-media-preview annam-about-image-field__preview annam-cabin-image-field__preview">
			<?php if ( $preview ) : ?>
				<img src="<?php echo esc_url( $preview ); ?>" alt="" width="160" height="120" />
			<?php else : ?>
				<span class="annam-media-placeholder"><?php esc_html_e( 'Chưa chọn ảnh — dùng ảnh mặc định', 'generatepress_child' ); ?></span>
			<?php endif; ?>
		</div>
		<p>
			<button type="button" class="button annam-about-pick-image"><?php esc_html_e( 'Chọn từ thư viện', 'generatepress_child' ); ?></button>
			<button type="button" class="button annam-about-clear-image"><?php esc_html_e( 'Xóa ảnh', 'generatepress_child' ); ?></button>
		</p>
		<?php if ( $has_caption ) : ?>
			<p>
				<label for="annam-limo-charter-cap-<?php echo esc_attr( $key ); ?>">
					<strong><?php esc_html_e( 'Ghi chú dưới ảnh', 'generatepress_child' ); ?></strong>
				</label><br />
				<input
					type="text"
					class="large-text"
					id="annam-limo-charter-cap-<?php echo esc_attr( $key ); ?>"
					name="<?php echo esc_attr( $cap_name ); ?>"
					value="<?php echo esc_attr( $cap_value ); ?>"
					maxlength="120"
					placeholder="<?php echo esc_attr( isset( $slot['default_caption'] ) ? (string) $slot['default_caption'] : '' ); ?>"
				/>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Admin page.
 */
function annam_limo_charter_landing_images_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$saved = get_option( ANNAM_LIMO_CHARTER_IMAGES_OPTION, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	$captions = get_option( ANNAM_LIMO_CHARTER_CAPTIONS_OPTION, array() );
	if ( ! is_array( $captions ) ) {
		$captions = array();
	}
	$slots    = annam_limo_charter_landing_get_image_slots();
	$settings = annam_limo_charter_landing_get_settings();
	$emails   = isset( $settings['lead_emails'] ) ? (string) $settings['lead_emails'] : '';
	$map      = isset( $settings['map_address'] ) ? (string) $settings['map_address'] : '';

	$groups = array(
		'hero'    => array(
			'title' => __( 'Hero', 'generatepress_child' ),
			'note'  => __( 'Ảnh cạnh form đặt xe. Khuyến nghị 1400×933 (3:2).', 'generatepress_child' ),
			'slots' => array(),
		),
		'pricing' => array(
			'title' => __( 'Card giá 9 chỗ & 11 chỗ', 'generatepress_child' ),
			'note'  => __( 'Hai ảnh trên card giá. Khuyến nghị 1200×800 (3:2).', 'generatepress_child' ),
			'slots' => array(),
		),
		'gallery' => array(
			'title' => __( 'Hình ảnh xe limousine (6 ảnh + ghi chú)', 'generatepress_child' ),
			'note'  => __( 'Lưới 3×2 trên trang. Mỗi ảnh có ghi chú riêng. Khuyến nghị 1200×800 (3:2).', 'generatepress_child' ),
			'slots' => array(),
		),
		'staff'   => array(
			'title' => __( 'Uy tín — lưới ảnh đội xe / NV (6 ảnh)', 'generatepress_child' ),
			'note'  => __( 'Lưới 3×2 cạnh Maps. Khuyến nghị 800×600 (4:3).', 'generatepress_child' ),
			'slots' => array(),
		),
		'cross'   => array(
			'title' => __( 'Gợi ý lựa chọn khác (3 ảnh)', 'generatepress_child' ),
			'note'  => __( 'Card vé limo / cabin / tour. Khuyến nghị 1000×750 (4:3).', 'generatepress_child' ),
			'slots' => array(),
		),
	);

	foreach ( $slots as $key => $slot ) {
		$sec = isset( $slot['section'] ) ? (string) $slot['section'] : '';
		if ( isset( $groups[ $sec ] ) ) {
			$groups[ $sec ]['slots'][ $key ] = $slot;
		}
	}

	$landing_pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-template-thue-xe-limousine-hn-sapa-landing.php',
		)
	);
	?>
	<div class="wrap annam-admin-wrap annam-cabin-images-wrap">
		<h1><?php esc_html_e( 'Landing Thuê Limo 9–11 chỗ', 'generatepress_child' ); ?></h1>

		<?php if ( ! empty( $_GET['annam_saved'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã lưu.', 'generatepress_child' ); ?></p></div>
		<?php endif; ?>

		<div class="notice notice-info inline annam-cabin-images-intro">
			<p>
				<?php esc_html_e( 'Quản lý toàn bộ ảnh trang Thuê limousine Hà Nội – Sapa (9 & 11 chỗ). Ảnh gallery có thêm ô ghi chú dưới ảnh. Để trống một mục → dùng ảnh mặc định.', 'generatepress_child' ); ?>
			</p>
			<p>
				<strong><?php esc_html_e( 'Gợi ý chung:', 'generatepress_child' ); ?></strong>
				<?php esc_html_e( 'ảnh xe thật, sáng rõ; JPG/WebP đã nén; không watermark; đúng tỷ lệ từng slot để tránh méo trên mobile Ads.', 'generatepress_child' ); ?>
			</p>
		</div>

		<?php if ( ! empty( $landing_pages ) ) : ?>
			<p class="description">
				<?php esc_html_e( 'Trang đang dùng template này:', 'generatepress_child' ); ?>
				<?php
				$links = array();
				foreach ( $landing_pages as $p ) {
					$view    = get_permalink( $p );
					$links[] = '<a href="' . esc_url( get_edit_post_link( $p->ID, 'raw' ) ) . '">' . esc_html( get_the_title( $p ) ) . '</a>'
						. ( $view ? ' (<a href="' . esc_url( $view ) . '" target="_blank" rel="noopener">' . esc_html__( 'Xem', 'generatepress_child' ) . '</a>)' : '' );
				}
				echo wp_kses_post( implode( ' · ', $links ) );
				?>
			</p>
		<?php else : ?>
			<p class="description">
				<?php esc_html_e( 'Chưa có trang gán template “Thuê xe Limousine HN–Sapa”. Tạo trang → Page Attributes → chọn template đó.', 'generatepress_child' ); ?>
			</p>
		<?php endif; ?>

		<h2 class="annam-admin-section-title"><?php esc_html_e( 'Email lead & địa chỉ Maps', 'generatepress_child' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=annam-limo-charter-landing-images' ) ); ?>" class="annam-cabin-settings-form">
			<?php wp_nonce_field( 'annam_save_limo_charter_landing_settings', 'annam_limo_charter_landing_settings_nonce' ); ?>
			<input type="hidden" name="annam_limo_charter_landing_settings_action" value="1" />
			<p>
				<label for="annam_limo_charter_lead_emails"><strong><?php esc_html_e( 'Email nhận lead (cách nhau bằng dấu phẩy)', 'generatepress_child' ); ?></strong></label><br />
				<input type="text" class="large-text" id="annam_limo_charter_lead_emails" name="annam_limo_charter_lead_emails" value="<?php echo esc_attr( $emails ); ?>" placeholder="annamdiscoveryvn@gmail.com" />
			</p>
			<p class="description"><?php esc_html_e( 'Mặc định: annamdiscoveryvn@gmail.com — chỉ đổi nếu cần thêm/đổi địa chỉ nhận lead.', 'generatepress_child' ); ?></p>
			<p>
				<label for="annam_limo_charter_map_address"><strong><?php esc_html_e( 'Địa chỉ / link nhúng Google Maps (tuỳ chọn)', 'generatepress_child' ); ?></strong></label><br />
				<input type="text" class="large-text" id="annam_limo_charter_map_address" name="annam_limo_charter_map_address" value="<?php echo esc_attr( $map ); ?>" placeholder="https://www.google.com/maps/place/An+Nam+Discovery/..." />
			</p>
			<p class="description">
				<?php
				esc_html_e(
					'Mặc định dùng link place An Nam Discovery (có nhãn trên bản đồ). Dán link /maps/place/... nếu muốn đổi.',
					'generatepress_child'
				);
				?>
			</p>
			<?php submit_button( __( 'Lưu email & Maps', 'generatepress_child' ), 'secondary' ); ?>
		</form>

		<hr />

		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=annam-limo-charter-landing-images' ) ); ?>" id="annam-limo-charter-landing-images-form">
			<?php wp_nonce_field( 'annam_save_limo_charter_landing_images', 'annam_limo_charter_landing_images_nonce' ); ?>
			<input type="hidden" name="annam_limo_charter_landing_images_action" value="1" />

			<?php foreach ( $groups as $group ) : ?>
				<?php if ( empty( $group['slots'] ) ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<h2 class="annam-admin-section-title"><?php echo esc_html( $group['title'] ); ?></h2>
				<p class="description annam-cabin-section-note"><?php echo esc_html( $group['note'] ); ?></p>
				<div class="annam-cabin-images-grid">
					<?php
					foreach ( $group['slots'] as $key => $slot ) {
						$cap = isset( $captions[ $key ] ) ? (string) $captions[ $key ] : '';
						annam_limo_charter_landing_images_render_field( $key, $slot, isset( $saved[ $key ] ) ? (int) $saved[ $key ] : 0, $cap );
					}
					?>
				</div>
			<?php endforeach; ?>

			<p class="submit">
				<button type="submit" class="button button-primary button-large"><?php esc_html_e( 'Lưu tất cả ảnh & ghi chú', 'generatepress_child' ); ?></button>
			</p>
		</form>
	</div>
	<?php
}
