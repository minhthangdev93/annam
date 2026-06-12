<?php
/**
 * Admin: tất cả ảnh landing thuê xe theo từng page.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta key thống nhất.
 */
function annam_car_rental_landing_images_meta_key() {
	return '_annam_cr_landing_images';
}

/** @deprecated Use annam_car_rental_landing_images_meta_key(). */
function annam_car_rental_trust_gallery_meta_key() {
	return '_annam_cr_trust_gallery';
}

/**
 * @param int $post_id Post ID.
 * @return bool
 */
function annam_car_rental_is_vehicle_landing_page( $post_id ) {
	return 'page-template-thue-xe-landing.php' === get_page_template_slug( (int) $post_id );
}

/**
 * @param int $post_id Post ID.
 * @return bool
 */
function annam_car_rental_is_hub_landing_page( $post_id ) {
	return 'page-template-thue-xe-hub.php' === get_page_template_slug( (int) $post_id );
}

/**
 * @param int $post_id Post ID.
 * @return bool
 */
function annam_car_rental_is_car_rental_landing_page( $post_id ) {
	return annam_car_rental_is_vehicle_landing_page( $post_id ) || annam_car_rental_is_hub_landing_page( $post_id );
}

/**
 * Số ảnh gallery uy tín tối đa.
 */
function annam_car_rental_trust_gallery_max_items() {
	return 5;
}

/**
 * @return array<string,array{label:string,placement:string,recommended:string,ratio:string,formats:string,filesize:string}>
 */
function annam_car_rental_landing_get_single_image_slots( $post_id ) {
	$slots = array(
		'hero' => array(
			'label'       => __( 'Ảnh nền Hero', 'generatepress_child' ),
			'placement'   => __( 'Nền banner đầu trang (full width, phía sau form báo giá)', 'generatepress_child' ),
			'recommended' => '1920 × 800 px',
			'ratio'       => '≈ 12 : 5 (ngang, cover)',
			'formats'     => 'JPG, WebP',
			'filesize'    => __( '< 400 KB', 'generatepress_child' ),
		),
		'why'  => array(
			'label'       => __( 'Ảnh section « Vì sao nên chọn »', 'generatepress_child' ),
			'placement'   => __( 'Cột ảnh bên trái khối lý do chọn An Nam Discovery', 'generatepress_child' ),
			'recommended' => '1280 × 800 px',
			'ratio'       => '16 : 10 (ngang)',
			'formats'     => 'JPG, WebP',
			'filesize'    => __( '< 300 KB', 'generatepress_child' ),
		),
	);

	if ( annam_car_rental_is_car_rental_landing_page( $post_id ) ) {
		$slots['cta_final'] = array(
			'label'       => __( 'Ảnh nền CTA cuối trang', 'generatepress_child' ),
			'placement'   => __( 'Nền section form « Nhận báo giá » ở cuối landing', 'generatepress_child' ),
			'recommended' => '1920 × 720 px',
			'ratio'       => '≈ 8 : 3 (ngang, cover)',
			'formats'     => 'JPG, WebP',
			'filesize'    => __( '< 400 KB', 'generatepress_child' ),
		);
	}

	if ( annam_car_rental_is_vehicle_landing_page( $post_id ) ) {
		$slots['qr'] = array(
			'label'       => __( 'Ảnh QR chuyển khoản', 'generatepress_child' ),
			'placement'   => __( 'Card thanh toán — section uy tín', 'generatepress_child' ),
			'recommended' => '600 × 600 px',
			'ratio'       => '1 : 1 (vuông)',
			'formats'     => 'JPG, PNG',
			'filesize'    => __( '< 200 KB', 'generatepress_child' ),
		);
	}

	if ( annam_car_rental_is_hub_landing_page( $post_id ) && function_exists( 'annam_car_rental_get_vehicle_types' ) ) {
		foreach ( annam_car_rental_get_vehicle_types() as $vkey => $type ) {
			$slots[ 'vehicle_' . $vkey ] = array(
				'label'       => sprintf(
					/* translators: %s: vehicle label */
					__( 'Ảnh loại xe — %s', 'generatepress_child' ),
					$type['label'] ?? $vkey
				),
				'placement'   => __( 'Thẻ chọn loại xe trên trang hub', 'generatepress_child' ),
				'recommended' => '800 × 600 px',
				'ratio'       => '4 : 3 (ngang)',
				'formats'     => 'JPG, WebP',
				'filesize'    => __( '< 250 KB', 'generatepress_child' ),
			);
		}
	}

	return apply_filters( 'annam_car_rental_landing_image_slots', $slots, $post_id );
}

/**
 * Vehicle type gắn với page landing loại xe.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function annam_car_rental_get_page_vehicle_type( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return '';
	}

	$meta = get_post_meta( $post_id, '_annam_car_rental_vehicle_type', true );
	if ( is_string( $meta ) && function_exists( 'annam_car_rental_is_valid_vehicle_type' ) && annam_car_rental_is_valid_vehicle_type( $meta ) ) {
		return $meta;
	}

	$post = get_post( $post_id );
	if ( $post && function_exists( 'annam_car_rental_vehicle_type_from_slug' ) ) {
		$from_slug = annam_car_rental_vehicle_type_from_slug( $post->post_name );
		if ( function_exists( 'annam_car_rental_is_valid_vehicle_type' ) && annam_car_rental_is_valid_vehicle_type( $from_slug ) ) {
			return $from_slug;
		}
	}

	return '';
}

/**
 * Slot ảnh nền từng thẻ hành trình phổ biến (tuyến hot của loại xe).
 *
 * @param int $post_id Post ID.
 * @return array<string,array{label:string,placement:string,recommended:string,ratio:string,formats:string,filesize:string}>
 */
function annam_car_rental_get_journey_image_slots_for_page( $post_id ) {
	if ( ! annam_car_rental_is_vehicle_landing_page( $post_id ) ) {
		return array();
	}

	$vehicle_type = annam_car_rental_get_page_vehicle_type( $post_id );
	if ( '' === $vehicle_type || ! function_exists( 'annam_car_rental_get_hot_routes' ) ) {
		return array();
	}

	$slots = array();
	foreach ( annam_car_rental_get_hot_routes( $vehicle_type ) as $route ) {
		$route_id = sanitize_key( (string) ( $route['id'] ?? '' ) );
		if ( '' === $route_id ) {
			continue;
		}
		$label_display = (string) ( $route['label_display'] ?? $route['label'] ?? $route_id );
		$slots[ 'journey_' . $route_id ] = array(
			'label'       => sprintf(
				/* translators: %s: route label */
				__( 'Hành trình — %s', 'generatepress_child' ),
				$label_display
			),
			'placement'   => __( 'Ảnh nền thẻ « Hành trình phổ biến »', 'generatepress_child' ),
			'recommended' => '960 × 576 px',
			'ratio'       => '5 : 3 (ngang, cover)',
			'formats'     => 'JPG, WebP',
			'filesize'    => __( '< 250 KB', 'generatepress_child' ),
		);
	}

	return apply_filters( 'annam_car_rental_journey_image_slots', $slots, $post_id, $vehicle_type );
}

/**
 * Tất cả slot ảnh đơn (hero, why, qr, vehicle, journey…).
 *
 * @param int $post_id Post ID.
 * @return array<string,array<string,string>>
 */
function annam_car_rental_get_all_landing_image_slots( $post_id ) {
	return array_merge(
		annam_car_rental_landing_get_single_image_slots( $post_id ),
		annam_car_rental_get_journey_image_slots_for_page( $post_id )
	);
}

/**
 * @return array<string,string>
 */
function annam_car_rental_trust_gallery_image_specs() {
	return array(
		'recommended' => '1200 × 750 px',
		'ratio'       => '16 : 10 (ngang)',
		'display'     => __( '1 ảnh chính + 4 thumbnail; tự chuyển 3 giây', 'generatepress_child' ),
		'formats'     => 'JPG, WebP',
		'filesize'    => __( '< 300 KB/ảnh', 'generatepress_child' ),
		'max'         => __( 'Tối đa 5 ảnh (gồm ảnh chính)', 'generatepress_child' ),
	);
}

/**
 * @param int $post_id Post ID.
 * @return array<string,mixed>
 */
function annam_car_rental_get_landing_images_meta( $post_id ) {
	$post_id = (int) $post_id;
	$saved   = get_post_meta( $post_id, annam_car_rental_landing_images_meta_key(), true );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	if ( empty( $saved['trust_gallery'] ) ) {
		$legacy = get_post_meta( $post_id, annam_car_rental_trust_gallery_meta_key(), true );
		if ( is_array( $legacy ) && ! empty( $legacy ) ) {
			$saved['trust_gallery'] = $legacy;
		}
	}

	return $saved;
}

/**
 * @param int $post_id Post ID.
 * @return array<int,array{attachment_id:int,alt:string,label:string}>
 */
function annam_car_rental_get_trust_gallery_meta( $post_id ) {
	$meta = annam_car_rental_get_landing_images_meta( $post_id );
	$raw  = isset( $meta['trust_gallery'] ) ? $meta['trust_gallery'] : array();
	if ( ! is_array( $raw ) ) {
		return array();
	}

	$out = array();
	foreach ( $raw as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}
		$id = absint( $item['attachment_id'] ?? 0 );
		if ( $id <= 0 ) {
			continue;
		}
		$out[] = array(
			'attachment_id' => $id,
			'alt'           => sanitize_text_field( (string) ( $item['alt'] ?? '' ) ),
			'label'         => sanitize_text_field( (string) ( $item['label'] ?? '' ) ),
		);
		if ( count( $out ) >= annam_car_rental_trust_gallery_max_items() ) {
			break;
		}
	}

	return $out;
}

/**
 * @param int    $post_id Post ID.
 * @param string $slot    Slot key (hero, why, qr, vehicle_7-cho, …).
 * @return int
 */
function annam_car_rental_get_landing_image_attachment_id( $post_id, $slot ) {
	$post_id = (int) $post_id;
	$slot    = sanitize_key( (string) $slot );
	if ( $post_id <= 0 || '' === $slot ) {
		return 0;
	}

	$meta = annam_car_rental_get_landing_images_meta( $post_id );
	$id   = absint( $meta[ $slot ] ?? 0 );
	if ( $id > 0 && wp_attachment_is_image( $id ) ) {
		return $id;
	}

	// Đổi route id lao-cai → sapa: vẫn đọc ảnh hành trình đã gán trước đó.
	if ( 'journey_sapa' === $slot ) {
		$legacy_id = absint( $meta['journey_lao-cai'] ?? 0 );
		if ( $legacy_id > 0 && wp_attachment_is_image( $legacy_id ) ) {
			return $legacy_id;
		}
	}

	return 0;
}

/**
 * @param int    $post_id Post ID.
 * @param string $slot    Slot key.
 * @param string $size    Image size.
 * @return string
 */
function annam_car_rental_get_landing_image_url( $post_id, $slot, $size = 'large' ) {
	$id = annam_car_rental_get_landing_image_attachment_id( $post_id, $slot );
	if ( $id <= 0 ) {
		return '';
	}
	$url = wp_get_attachment_image_url( $id, $size );
	return is_string( $url ) ? $url : '';
}

/**
 * @param int $post_id Post ID.
 * @return array<int,array{url:string,alt:string,label:string}>
 */
function annam_car_rental_get_trust_gallery_images_for_page( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( $post_id <= 0 ) {
		return array();
	}

	$meta = annam_car_rental_get_trust_gallery_meta( $post_id );
	if ( empty( $meta ) ) {
		return array();
	}

	$out = array();
	foreach ( $meta as $item ) {
		$id = (int) $item['attachment_id'];
		if ( ! wp_attachment_is_image( $id ) ) {
			continue;
		}
		$url = wp_get_attachment_image_url( $id, 'large' );
		if ( ! $url ) {
			continue;
		}
		$alt = $item['alt'];
		if ( '' === $alt ) {
			$alt_meta = get_post_meta( $id, '_wp_attachment_image_alt', true );
			$alt      = is_string( $alt_meta ) ? $alt_meta : '';
		}
		$label = $item['label'];
		if ( '' === $label ) {
			$label = $alt;
		}
		$out[] = array(
			'url'   => $url,
			'alt'   => $alt,
			'label' => $label,
		);
	}

	return $out;
}

/**
 * @param array<string,mixed> $config       Config.
 * @param string              $vehicle_type Vehicle key.
 * @return array<string,mixed>
 */
function annam_car_rental_apply_page_landing_images( $config, $vehicle_type ) {
	$post_id = get_queried_object_id();
	if ( ! $post_id || ! annam_car_rental_is_car_rental_landing_page( $post_id ) ) {
		return $config;
	}

	if ( ! empty( $config['is_hub'] ) && function_exists( 'annam_car_rental_get_vehicle_types' ) && ! empty( $config['vehicles'] ) ) {
		foreach ( $config['vehicles'] as $i => $vehicle ) {
			$vkey = (string) ( $vehicle['key'] ?? '' );
			if ( '' === $vkey ) {
				continue;
			}
			$url = annam_car_rental_get_landing_image_url( $post_id, 'vehicle_' . $vkey, 'medium_large' );
			if ( $url ) {
				$config['vehicles'][ $i ]['image_url'] = $url;
			}
		}
	}

	if ( empty( $config['is_hub'] ) ) {
		$images = annam_car_rental_get_trust_gallery_images_for_page( $post_id );
		if ( ! empty( $images ) ) {
			if ( ! isset( $config['trust'] ) || ! is_array( $config['trust'] ) ) {
				$config['trust'] = annam_car_rental_get_trust_section_config();
			}
			if ( ! isset( $config['trust']['gallery'] ) || ! is_array( $config['trust']['gallery'] ) ) {
				$config['trust']['gallery'] = array();
			}
			$config['trust']['gallery']['images'] = $images;
		}

		$qr_url = annam_car_rental_get_landing_image_url( $post_id, 'qr', 'full' );
		if ( $qr_url ) {
			if ( ! isset( $config['trust'] ) || ! is_array( $config['trust'] ) ) {
				$config['trust'] = annam_car_rental_get_trust_section_config();
			}
			if ( ! isset( $config['trust']['payment'] ) || ! is_array( $config['trust']['payment'] ) ) {
				$config['trust']['payment'] = array();
			}
			$config['trust']['payment']['qr_image'] = $qr_url;
		}

		if ( ! empty( $config['featured'] ) && is_array( $config['featured'] ) ) {
			foreach ( $config['featured'] as $i => $journey ) {
				$route_id = sanitize_key( (string) ( $journey['route_id'] ?? '' ) );
				if ( '' === $route_id ) {
					continue;
				}
				$bg_url = annam_car_rental_get_landing_image_url( $post_id, 'journey_' . $route_id, 'large' );
				if ( $bg_url ) {
					$config['featured'][ $i ]['bg_image'] = $bg_url;
				}
			}
		}
	}

	return $config;
}
add_filter( 'annam_car_rental_landing_config', 'annam_car_rental_apply_page_landing_images', 25, 2 );

/**
 * Meta box.
 */
function annam_car_rental_landing_images_add_meta_box() {
	add_meta_box(
		'annam_cr_landing_images',
		__( 'Ảnh Landing thuê xe', 'generatepress_child' ),
		'annam_car_rental_landing_images_meta_box_render',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'annam_car_rental_landing_images_add_meta_box' );

/**
 * @param string               $name          Field name.
 * @param array<string,string> $slot          Slot meta.
 * @param int                  $attachment_id Attachment ID.
 */
function annam_car_rental_landing_render_image_field( $name, array $slot, $attachment_id ) {
	$attachment_id = absint( $attachment_id );
	$preview       = $attachment_id && wp_attachment_is_image( $attachment_id )
		? wp_get_attachment_image_url( $attachment_id, 'medium' )
		: '';

	$meta_note = '';
	if ( $attachment_id ) {
		$file = get_attached_file( $attachment_id );
		if ( $file && file_exists( $file ) ) {
			$size = @getimagesize( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( is_array( $size ) && ! empty( $size[0] ) && ! empty( $size[1] ) ) {
				$meta_note = sprintf(
					/* translators: 1: width px, 2: height px */
					__( 'File hiện tại: %1$d × %2$d px', 'generatepress_child' ),
					(int) $size[0],
					(int) $size[1]
				);
			}
		}
	}
	?>
	<div class="annam-about-image-field annam-cabin-image-field annam-cr-landing-image-field" data-annam-about-image>
		<label class="annam-about-image-field__label"><?php echo esc_html( $slot['label'] ?? '' ); ?></label>
		<?php if ( ! empty( $slot['placement'] ) ) : ?>
			<p class="annam-cabin-image-field__placement"><?php echo esc_html( $slot['placement'] ); ?></p>
		<?php endif; ?>
		<ul class="annam-cabin-image-field__specs">
			<li>
				<strong><?php esc_html_e( 'Kích thước khuyến nghị:', 'generatepress_child' ); ?></strong>
				<?php echo esc_html( $slot['recommended'] ?? '' ); ?>
			</li>
			<li>
				<strong><?php esc_html_e( 'Tỷ lệ:', 'generatepress_child' ); ?></strong>
				<?php echo esc_html( $slot['ratio'] ?? '' ); ?>
			</li>
			<li>
				<strong><?php esc_html_e( 'Định dạng:', 'generatepress_child' ); ?></strong>
				<?php echo esc_html( $slot['formats'] ?? '' ); ?>
				<?php if ( ! empty( $slot['filesize'] ) ) : ?>
					· <strong><?php esc_html_e( 'Dung lượng:', 'generatepress_child' ); ?></strong>
					<?php echo esc_html( $slot['filesize'] ); ?>
				<?php endif; ?>
			</li>
		</ul>
		<?php if ( $meta_note ) : ?>
			<p class="annam-cabin-image-field__current"><?php echo esc_html( $meta_note ); ?></p>
		<?php endif; ?>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $attachment_id ); ?>" class="annam-about-attachment-id" />
		<div class="annam-media-preview annam-about-image-field__preview annam-cabin-image-field__preview">
			<?php if ( $preview ) : ?>
				<img src="<?php echo esc_url( $preview ); ?>" alt="" width="160" height="120" />
			<?php else : ?>
				<span class="annam-media-placeholder"><?php esc_html_e( 'Chưa chọn ảnh', 'generatepress_child' ); ?></span>
			<?php endif; ?>
		</div>
		<p>
			<button type="button" class="button annam-about-pick-image"><?php esc_html_e( 'Chọn từ thư viện', 'generatepress_child' ); ?></button>
			<button type="button" class="button annam-about-clear-image"><?php esc_html_e( 'Xóa ảnh', 'generatepress_child' ); ?></button>
		</p>
	</div>
	<?php
}

/**
 * @param WP_Post $post Post.
 */
function annam_car_rental_landing_images_meta_box_render( $post ) {
	wp_nonce_field( 'annam_cr_landing_images_save', 'annam_cr_landing_images_nonce' );

	if ( ! annam_car_rental_is_car_rental_landing_page( $post->ID ) ) {
		echo '<p class="description">';
		esc_html_e( 'Chọn template « Thuê xe hợp đồng (Landing loại xe) » hoặc « Hub thuê xe » rồi lưu trang để quản lý ảnh tại đây.', 'generatepress_child' );
		echo '</p>';
		return;
	}

	$meta           = annam_car_rental_get_landing_images_meta( $post->ID );
	$slots          = annam_car_rental_landing_get_single_image_slots( $post->ID );
	$journey_slots  = annam_car_rental_get_journey_image_slots_for_page( $post->ID );
	$is_vehicle     = annam_car_rental_is_vehicle_landing_page( $post->ID );
	?>
	<div class="annam-cr-landing-images-admin">
		<p class="annam-cabin-images-intro">
			<?php esc_html_e( 'Chọn ảnh cho từng vị trí trên landing. Nếu chưa chọn ảnh Hero, hệ thống dùng Ảnh đại diện (Featured Image) của trang làm dự phòng.', 'generatepress_child' ); ?>
		</p>

		<div class="annam-cabin-images-grid">
			<?php
			foreach ( $slots as $key => $slot ) {
				if ( 0 === strpos( $key, 'vehicle_' ) || 0 === strpos( $key, 'journey_' ) ) {
					continue;
				}
				annam_car_rental_landing_render_image_field(
					'annam_cr_landing_images[' . $key . ']',
					$slot,
					absint( $meta[ $key ] ?? 0 )
				);
			}
			?>
		</div>

		<?php if ( $is_vehicle && ! empty( $journey_slots ) ) : ?>
			<hr class="annam-cr-landing-images-admin__sep" />
			<h3 class="annam-cr-landing-images-admin__heading"><?php esc_html_e( 'Ảnh nền — Hành trình phổ biến', 'generatepress_child' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Mỗi tuyến hot hiển thị một thẻ. Chưa chọn ảnh sẽ dùng nền gradient mặc định.', 'generatepress_child' ); ?>
			</p>
			<div class="annam-cabin-images-grid annam-cabin-images-grid--journeys">
				<?php
				foreach ( $journey_slots as $key => $slot ) {
					annam_car_rental_landing_render_image_field(
						'annam_cr_landing_images[' . $key . ']',
						$slot,
						absint( $meta[ $key ] ?? 0 )
					);
				}
				?>
			</div>
		<?php endif; ?>

		<?php if ( $is_vehicle ) : ?>
			<hr class="annam-cr-landing-images-admin__sep" />
			<h3 class="annam-cr-landing-images-admin__heading"><?php esc_html_e( 'Gallery uy tín — Hình ảnh thực tế', 'generatepress_child' ); ?></h3>
			<?php
			$specs = annam_car_rental_trust_gallery_image_specs();
			$items = annam_car_rental_get_trust_gallery_meta( $post->ID );
			$max   = annam_car_rental_trust_gallery_max_items();
			?>
			<p class="description">
				<?php esc_html_e( 'Kéo thả để sắp xếp — ảnh đầu tiên là ảnh chính khi mở trang.', 'generatepress_child' ); ?>
			</p>
			<ul class="annam-cabin-image-field__specs annam-cr-trust-gallery-admin__specs">
				<li><strong><?php esc_html_e( 'Kích thước khuyến nghị:', 'generatepress_child' ); ?></strong> <?php echo esc_html( $specs['recommended'] ); ?></li>
				<li><strong><?php esc_html_e( 'Tỷ lệ:', 'generatepress_child' ); ?></strong> <?php echo esc_html( $specs['ratio'] ); ?></li>
				<li><strong><?php esc_html_e( 'Hiển thị:', 'generatepress_child' ); ?></strong> <?php echo esc_html( $specs['display'] ); ?></li>
				<li><strong><?php esc_html_e( 'Số lượng:', 'generatepress_child' ); ?></strong> <?php echo esc_html( $specs['max'] ); ?></li>
			</ul>
			<ul id="annam-cr-trust-gallery-list" class="annam-cr-trust-gallery-admin__list" data-max="<?php echo esc_attr( (string) $max ); ?>">
				<?php
				if ( empty( $items ) ) {
					annam_car_rental_trust_gallery_render_admin_row( 0, array( 'attachment_id' => 0, 'alt' => '', 'label' => '' ) );
				} else {
					foreach ( $items as $index => $item ) {
						annam_car_rental_trust_gallery_render_admin_row( $index, $item );
					}
				}
				?>
			</ul>
			<p>
				<button type="button" class="button" id="annam-cr-trust-gallery-add"><?php esc_html_e( 'Thêm ảnh gallery', 'generatepress_child' ); ?></button>
				<span class="description annam-cr-trust-gallery-admin__count"></span>
			</p>
			<script type="text/html" id="annam-cr-trust-gallery-row-tpl">
				<?php annam_car_rental_trust_gallery_render_admin_row( '__INDEX__', array( 'attachment_id' => 0, 'alt' => '', 'label' => '' ) ); ?>
			</script>
		<?php endif; ?>

		<?php
		$vehicle_slots = array_filter(
			$slots,
			static function ( $key ) {
				return 0 === strpos( (string) $key, 'vehicle_' );
			},
			ARRAY_FILTER_USE_KEY
		);
		if ( ! empty( $vehicle_slots ) ) :
			?>
			<hr class="annam-cr-landing-images-admin__sep" />
			<h3 class="annam-cr-landing-images-admin__heading"><?php esc_html_e( 'Ảnh thẻ loại xe (Hub)', 'generatepress_child' ); ?></h3>
			<div class="annam-cabin-images-grid">
				<?php
				foreach ( $vehicle_slots as $key => $slot ) {
					annam_car_rental_landing_render_image_field(
						'annam_cr_landing_images[' . $key . ']',
						$slot,
						absint( $meta[ $key ] ?? 0 )
					);
				}
				?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * @param int|string                           $index Row index.
 * @param array{attachment_id:int,alt:string,label:string} $item Row.
 */
function annam_car_rental_trust_gallery_render_admin_row( $index, array $item ) {
	$attachment_id = absint( $item['attachment_id'] ?? 0 );
	$alt           = (string) ( $item['alt'] ?? '' );
	$preview       = $attachment_id && wp_attachment_is_image( $attachment_id )
		? wp_get_attachment_image_url( $attachment_id, 'medium' )
		: '';

	$meta_note = '';
	if ( $attachment_id ) {
		$file = get_attached_file( $attachment_id );
		if ( $file && file_exists( $file ) ) {
			$size = @getimagesize( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			if ( is_array( $size ) && ! empty( $size[0] ) && ! empty( $size[1] ) ) {
				$meta_note = sprintf(
					__( 'File: %1$d × %2$d px', 'generatepress_child' ),
					(int) $size[0],
					(int) $size[1]
				);
			}
		}
	}

	$name_prefix = 'annam_cr_trust_gallery[' . $index . ']';
	?>
	<li class="annam-cr-trust-gallery-admin__item annam-gallery-item" data-annam-about-image>
		<span class="annam-gallery-handle" title="<?php esc_attr_e( 'Kéo để sắp xếp', 'generatepress_child' ); ?>">⠿</span>
		<div class="annam-cr-trust-gallery-admin__item-body">
			<input type="hidden" name="<?php echo esc_attr( $name_prefix ); ?>[attachment_id]" value="<?php echo esc_attr( (string) $attachment_id ); ?>" class="annam-about-attachment-id" />
			<div class="annam-media-preview annam-about-image-field__preview annam-cabin-image-field__preview">
				<?php if ( $preview ) : ?>
					<img src="<?php echo esc_url( $preview ); ?>" alt="" width="160" height="100" />
				<?php else : ?>
					<span class="annam-media-placeholder"><?php esc_html_e( 'Chưa chọn ảnh', 'generatepress_child' ); ?></span>
				<?php endif; ?>
			</div>
			<?php if ( $meta_note ) : ?>
				<p class="annam-cabin-image-field__current"><?php echo esc_html( $meta_note ); ?></p>
			<?php endif; ?>
			<p class="annam-cr-trust-gallery-admin__actions">
				<button type="button" class="button annam-about-pick-image"><?php esc_html_e( 'Chọn ảnh', 'generatepress_child' ); ?></button>
				<button type="button" class="button annam-about-clear-image"><?php esc_html_e( 'Xóa ảnh', 'generatepress_child' ); ?></button>
				<button type="button" class="button-link-delete annam-cr-trust-gallery-remove"><?php esc_html_e( 'Xóa dòng', 'generatepress_child' ); ?></button>
			</p>
			<p>
				<input type="text" class="widefat" name="<?php echo esc_attr( $name_prefix ); ?>[alt]" value="<?php echo esc_attr( $alt ); ?>" placeholder="<?php esc_attr_e( 'Mô tả ảnh (alt)', 'generatepress_child' ); ?>" />
			</p>
		</div>
	</li>
	<?php
}

/**
 * @param int $post_id Post ID.
 */
function annam_car_rental_landing_images_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['annam_cr_landing_images_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['annam_cr_landing_images_nonce'] ) ), 'annam_cr_landing_images_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	if ( ! annam_car_rental_is_car_rental_landing_page( $post_id ) ) {
		return;
	}

	$input = isset( $_POST['annam_cr_landing_images'] ) && is_array( $_POST['annam_cr_landing_images'] )
		? wp_unslash( $_POST['annam_cr_landing_images'] )
		: array();

	$clean = array();
	$slots = annam_car_rental_get_all_landing_image_slots( $post_id );
	foreach ( $slots as $key => $slot ) {
		unset( $slot );
		$id = absint( $input[ $key ] ?? 0 );
		if ( $id > 0 && wp_attachment_is_image( $id ) ) {
			$clean[ $key ] = $id;
		}
	}

	if ( annam_car_rental_is_vehicle_landing_page( $post_id ) ) {
		$gallery_input = isset( $_POST['annam_cr_trust_gallery'] ) && is_array( $_POST['annam_cr_trust_gallery'] )
			? wp_unslash( $_POST['annam_cr_trust_gallery'] )
			: array();

		$gallery = array();
		foreach ( $gallery_input as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			if ( count( $gallery ) >= annam_car_rental_trust_gallery_max_items() ) {
				break;
			}
			$id = absint( $row['attachment_id'] ?? 0 );
			if ( $id <= 0 || ! wp_attachment_is_image( $id ) ) {
				continue;
			}
			$gallery[] = array(
				'attachment_id' => $id,
				'alt'           => sanitize_text_field( (string) ( $row['alt'] ?? '' ) ),
				'label'         => sanitize_text_field( (string) ( $row['label'] ?? '' ) ),
			);
		}
		if ( ! empty( $gallery ) ) {
			$clean['trust_gallery'] = $gallery;
		}
	}

	if ( empty( $clean ) ) {
		delete_post_meta( $post_id, annam_car_rental_landing_images_meta_key() );
	} else {
		update_post_meta( $post_id, annam_car_rental_landing_images_meta_key(), $clean );
	}

	delete_post_meta( $post_id, annam_car_rental_trust_gallery_meta_key() );
}
add_action( 'save_post_page', 'annam_car_rental_landing_images_save_meta_box' );

/**
 * @param string $hook_suffix Hook.
 */
function annam_car_rental_landing_images_admin_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );

	$css_admin = $dir . '/assets/css/annam-admin.css';
	$js_admin  = $dir . '/assets/js/annam-admin.js';
	$js_cr     = $dir . '/assets/js/car-rental-trust-gallery-admin.js';

	wp_enqueue_style(
		'annam-admin',
		$uri . '/assets/css/annam-admin.css',
		array(),
		file_exists( $css_admin ) ? (string) filemtime( $css_admin ) : '1.0.0'
	);

	wp_enqueue_script(
		'annam-admin',
		$uri . '/assets/js/annam-admin.js',
		array( 'jquery', 'media-upload', 'media-views', 'media-editor' ),
		file_exists( $js_admin ) ? (string) filemtime( $js_admin ) : '1.0.0',
		true
	);

	wp_enqueue_script(
		'annam-cr-trust-gallery-admin',
		$uri . '/assets/js/car-rental-trust-gallery-admin.js',
		array( 'jquery', 'jquery-ui-sortable', 'annam-admin' ),
		file_exists( $js_cr ) ? (string) filemtime( $js_cr ) : '1.0.0',
		true
	);

	wp_localize_script(
		'annam-cr-trust-gallery-admin',
		'annamCrTrustGalleryL10n',
		array(
			'maxItems'   => annam_car_rental_trust_gallery_max_items(),
			'addLimit'   => __( 'Đã đủ 5 ảnh.', 'generatepress_child' ),
			'countTpl'   => __( 'Đang có %1$d/%2$d ảnh gallery.', 'generatepress_child' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'annam_car_rental_landing_images_admin_assets' );
