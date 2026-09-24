<?php
/**
 * Admin ảnh + email lead Tour Sapa 3N2Đ.
 * Thư viện đầu trang (động, >6 ảnh) + ảnh lịch trình 3 ngày.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ANNAM_TOUR_SAPA_IMAGES_OPTION' ) ) {
	define( 'ANNAM_TOUR_SAPA_IMAGES_OPTION', 'annam_tour_sapa_landing_images' );
}

if ( ! defined( 'ANNAM_TOUR_SAPA_GALLERY_OPTION' ) ) {
	define( 'ANNAM_TOUR_SAPA_GALLERY_OPTION', 'annam_tour_sapa_landing_gallery' );
}

if ( ! defined( 'ANNAM_TOUR_SAPA_SETTINGS_OPTION' ) ) {
	define( 'ANNAM_TOUR_SAPA_SETTINGS_OPTION', 'annam_tour_sapa_landing_settings' );
}

/**
 * @return array<string,array{label:string,section:string,fallback:string,default_caption:string}>
 */
function annam_tour_sapa_landing_get_itinerary_image_slots() {
	return array(
		'itinerary-day-1' => array(
			'label'           => __( 'Ngày 01 — Hà Nội → Sapa → Cát Cát', 'generatepress_child' ),
			'section'         => 'itinerary',
			'fallback'        => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=1200&q=80',
			'default_caption' => 'Ngày 01 — Bản Cát Cát',
		),
		'itinerary-day-2' => array(
			'label'           => __( 'Ngày 02 — Fansipan / Moana', 'generatepress_child' ),
			'section'         => 'itinerary',
			'fallback'        => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=80',
			'default_caption' => 'Ngày 02 — Fansipan & Moana',
		),
		'itinerary-day-3' => array(
			'label'           => __( 'Ngày 03 — Sapa → Hà Nội', 'generatepress_child' ),
			'section'         => 'itinerary',
			'fallback'        => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=1200&q=80',
			'default_caption' => 'Ngày 03 — Sapa về Hà Nội',
		),
	);
}

/**
 * Legacy slots (experience + itinerary) for fallbacks / migration.
 *
 * @return array<string,array{label:string,fallback:string,default_caption:string}>
 */
function annam_tour_sapa_landing_get_image_slots() {
	$legacy = array(
		'experience-1' => array(
			'label'           => __( 'Trải nghiệm 1 (ảnh lớn)', 'generatepress_child' ),
			'fallback'        => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=1400&q=80',
			'default_caption' => 'Ruộng bậc thang & trải nghiệm Sapa',
		),
		'experience-2' => array(
			'label'           => __( 'Trải nghiệm 2', 'generatepress_child' ),
			'fallback'        => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=800&q=80',
			'default_caption' => 'Thác và bản làng Cát Cát',
		),
		'experience-3' => array(
			'label'           => __( 'Trải nghiệm 3', 'generatepress_child' ),
			'fallback'        => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=800&q=80',
			'default_caption' => 'Kiến trúc núi & làng bản',
		),
		'experience-4' => array(
			'label'           => __( 'Trải nghiệm 4', 'generatepress_child' ),
			'fallback'        => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=800&q=80',
			'default_caption' => 'Moana Sapa – Bàn tay vàng',
		),
		'experience-5' => array(
			'label'           => __( 'Trải nghiệm 5', 'generatepress_child' ),
			'fallback'        => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
			'default_caption' => 'Săn mây Fansipan / Hoàng Liên Sơn',
		),
		'experience-6' => array(
			'label'           => __( 'Trải nghiệm 6', 'generatepress_child' ),
			'fallback'        => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=80',
			'default_caption' => 'Không khí Sapa về đêm',
		),
	);

	return array_merge( $legacy, annam_tour_sapa_landing_get_itinerary_image_slots() );
}

/**
 * @return array<int,array{id:int,caption:string,fallback:string}>
 */
function annam_tour_sapa_landing_get_gallery_defaults() {
	$slots = annam_tour_sapa_landing_get_image_slots();
	$out   = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$key = 'experience-' . $i;
		if ( ! isset( $slots[ $key ] ) ) {
			continue;
		}
		$out[] = array(
			'id'       => 0,
			'caption'  => (string) $slots[ $key ]['default_caption'],
			'fallback' => (string) $slots[ $key ]['fallback'],
		);
	}
	return $out;
}

/**
 * @return array<int,array{id:int,caption:string,fallback:string}>
 */
function annam_tour_sapa_landing_migrate_gallery_from_legacy() {
	$images   = get_option( ANNAM_TOUR_SAPA_IMAGES_OPTION, array() );
	$images   = is_array( $images ) ? $images : array();
	$slots    = annam_tour_sapa_landing_get_image_slots();
	$defaults = annam_tour_sapa_landing_get_gallery_defaults();
	$out      = array();

	for ( $i = 1; $i <= 6; $i++ ) {
		$key   = 'experience-' . $i;
		$aid   = isset( $images[ $key ] ) ? absint( $images[ $key ] ) : 0;
		$cap   = isset( $slots[ $key ]['default_caption'] ) ? (string) $slots[ $key ]['default_caption'] : '';
		$fb    = isset( $defaults[ $i - 1 ]['fallback'] ) ? (string) $defaults[ $i - 1 ]['fallback'] : '';
		$out[] = array(
			'id'       => $aid,
			'caption'  => $cap,
			'fallback' => $fb,
		);
	}

	return $out;
}

/**
 * @return array<int,array{id:int,caption:string,fallback:string,url:string}>
 */
function annam_tour_sapa_landing_get_gallery_items() {
	$saved = get_option( ANNAM_TOUR_SAPA_GALLERY_OPTION, null );

	if ( null === $saved ) {
		$items = annam_tour_sapa_landing_migrate_gallery_from_legacy();
	} elseif ( is_array( $saved ) && ! empty( $saved ) ) {
		$items = array();
		foreach ( $saved as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$items[] = array(
				'id'       => isset( $row['id'] ) ? absint( $row['id'] ) : 0,
				'caption'  => isset( $row['caption'] ) ? sanitize_text_field( (string) $row['caption'] ) : '',
				'fallback' => isset( $row['fallback'] ) ? esc_url_raw( (string) $row['fallback'] ) : '',
			);
		}
	} else {
		$items = annam_tour_sapa_landing_get_gallery_defaults();
	}

	if ( empty( $items ) ) {
		$items = annam_tour_sapa_landing_get_gallery_defaults();
	}

	$out = array();
	foreach ( $items as $row ) {
		$id  = isset( $row['id'] ) ? absint( $row['id'] ) : 0;
		$url = '';
		if ( $id > 0 ) {
			$url = (string) wp_get_attachment_image_url( $id, 'large' );
		}
		if ( '' === $url && ! empty( $row['fallback'] ) ) {
			$url = (string) $row['fallback'];
		}
		if ( '' === $url ) {
			continue;
		}
		$out[] = array(
			'id'       => $id,
			'caption'  => isset( $row['caption'] ) ? (string) $row['caption'] : '',
			'fallback' => isset( $row['fallback'] ) ? (string) $row['fallback'] : '',
			'url'      => $url,
		);
	}

	return apply_filters( 'annam_tour_sapa_landing_gallery_items', $out );
}

/**
 * @return array<int,array{slot:string,caption:string,url:string}>
 */
function annam_tour_sapa_landing_get_experience_from_gallery() {
	$items = annam_tour_sapa_landing_get_gallery_items();
	$out   = array();
	foreach ( $items as $i => $item ) {
		$out[] = array(
			'slot'    => 'gallery-' . (int) $i,
			'caption' => isset( $item['caption'] ) ? (string) $item['caption'] : '',
			'url'     => isset( $item['url'] ) ? (string) $item['url'] : '',
		);
	}
	return $out;
}

/**
 * @return array<string,int>
 */
function annam_tour_sapa_landing_get_images() {
	$opt = get_option( ANNAM_TOUR_SAPA_IMAGES_OPTION, array() );
	return is_array( $opt ) ? $opt : array();
}

/**
 * @param string $slot Slot key.
 * @return int
 */
function annam_tour_sapa_landing_get_image_attachment_id( $slot ) {
	$slot = sanitize_key( (string) $slot );

	if ( preg_match( '/^gallery-(\d+)$/', $slot, $m ) ) {
		$items = annam_tour_sapa_landing_get_gallery_items();
		$idx   = (int) $m[1];
		return isset( $items[ $idx ]['id'] ) ? absint( $items[ $idx ]['id'] ) : 0;
	}

	$images = annam_tour_sapa_landing_get_images();
	return isset( $images[ $slot ] ) ? absint( $images[ $slot ] ) : 0;
}

/**
 * @param string $slot Slot key.
 * @return string
 */
function annam_tour_sapa_landing_image_url( $slot ) {
	$slot = sanitize_key( (string) $slot );

	if ( preg_match( '/^gallery-(\d+)$/', $slot, $m ) ) {
		$items = annam_tour_sapa_landing_get_gallery_items();
		$idx   = (int) $m[1];
		return isset( $items[ $idx ]['url'] ) ? (string) $items[ $idx ]['url'] : '';
	}

	$aid = annam_tour_sapa_landing_get_image_attachment_id( $slot );
	if ( $aid > 0 ) {
		$url = wp_get_attachment_image_url( $aid, 'large' );
		if ( $url ) {
			return $url;
		}
	}
	$slots = annam_tour_sapa_landing_get_image_slots();
	return isset( $slots[ $slot ]['fallback'] ) ? (string) $slots[ $slot ]['fallback'] : '';
}

/**
 * @return string[]
 */
function annam_tour_sapa_landing_get_lead_emails() {
	$settings = get_option( ANNAM_TOUR_SAPA_SETTINGS_OPTION, array() );
	$raw      = isset( $settings['lead_emails'] ) ? (string) $settings['lead_emails'] : '';
	if ( '' === trim( $raw ) ) {
		return array();
	}
	$parts = preg_split( '/[\s,;]+/', $raw );
	$out   = array();
	foreach ( (array) $parts as $email ) {
		$email = sanitize_email( $email );
		if ( $email ) {
			$out[] = $email;
		}
	}
	return array_values( array_unique( $out ) );
}

/**
 * Admin menu.
 */
function annam_tour_sapa_landing_images_admin_menu() {
	add_theme_page(
		__( 'Tour Sapa 3N2Đ — Ảnh & Email', 'generatepress_child' ),
		__( 'Tour Sapa Landing', 'generatepress_child' ),
		'edit_theme_options',
		'annam-tour-sapa-landing',
		'annam_tour_sapa_landing_images_admin_render'
	);
}
add_action( 'admin_menu', 'annam_tour_sapa_landing_images_admin_menu' );

/**
 * @param mixed $raw Raw POST gallery.
 * @return array<int,array{id:int,caption:string,fallback:string}>
 */
function annam_tour_sapa_landing_sanitize_gallery_post( $raw ) {
	$defaults = annam_tour_sapa_landing_get_gallery_defaults();
	$out      = array();
	if ( ! is_array( $raw ) ) {
		return $defaults;
	}
	foreach ( $raw as $i => $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$id  = isset( $row['id'] ) ? absint( $row['id'] ) : 0;
		$cap = isset( $row['caption'] ) ? sanitize_text_field( wp_unslash( (string) $row['caption'] ) ) : '';
		$fb  = isset( $defaults[ min( (int) $i, 5 ) ]['fallback'] ) ? (string) $defaults[ min( (int) $i, 5 ) ]['fallback'] : '';
		if ( isset( $row['fallback'] ) && is_string( $row['fallback'] ) && '' !== trim( $row['fallback'] ) ) {
			$fb = esc_url_raw( wp_unslash( $row['fallback'] ) );
		}
		if ( $id <= 0 && '' === $cap && (int) $i >= 6 ) {
			continue;
		}
		if ( '' === $cap && isset( $defaults[ min( (int) $i, 5 ) ]['caption'] ) ) {
			$cap = (string) $defaults[ min( (int) $i, 5 ) ]['caption'];
		}
		$out[] = array(
			'id'       => $id,
			'caption'  => $cap,
			'fallback' => $fb ? $fb : ( isset( $defaults[0]['fallback'] ) ? (string) $defaults[0]['fallback'] : '' ),
		);
	}
	while ( count( $out ) < 6 ) {
		$out[] = $defaults[ count( $out ) ] ?? $defaults[0];
	}
	return array_values( $out );
}

/**
 * Save + render.
 */
function annam_tour_sapa_landing_images_admin_render() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	if ( isset( $_POST['annam_tour_sapa_images_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['annam_tour_sapa_images_nonce'] ) ), 'annam_tour_sapa_images_save' ) ) {
		$itin_slots = annam_tour_sapa_landing_get_itinerary_image_slots();
		$saved      = annam_tour_sapa_landing_get_images();
		foreach ( array_keys( $itin_slots ) as $key ) {
			$field         = 'annam_tour_sapa_img_' . $key;
			$saved[ $key ] = isset( $_POST[ $field ] ) ? absint( $_POST[ $field ] ) : 0;
		}
		update_option( ANNAM_TOUR_SAPA_IMAGES_OPTION, $saved, false );

		$gallery_raw = isset( $_POST['annam_tour_sapa_gallery'] ) ? wp_unslash( $_POST['annam_tour_sapa_gallery'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_option( ANNAM_TOUR_SAPA_GALLERY_OPTION, annam_tour_sapa_landing_sanitize_gallery_post( $gallery_raw ), false );

		$emails = isset( $_POST['annam_tour_sapa_lead_emails'] ) ? sanitize_text_field( wp_unslash( $_POST['annam_tour_sapa_lead_emails'] ) ) : '';
		update_option( ANNAM_TOUR_SAPA_SETTINGS_OPTION, array( 'lead_emails' => $emails ), false );

		echo '<div class="notice notice-success"><p>' . esc_html__( 'Đã lưu ảnh thư viện, lịch trình và email lead.', 'generatepress_child' ) . '</p></div>';
	}

	$images     = annam_tour_sapa_landing_get_images();
	$itin_slots = annam_tour_sapa_landing_get_itinerary_image_slots();
	$gallery    = get_option( ANNAM_TOUR_SAPA_GALLERY_OPTION, null );
	if ( null === $gallery || ! is_array( $gallery ) || empty( $gallery ) ) {
		$gallery = annam_tour_sapa_landing_migrate_gallery_from_legacy();
	}
	$defaults = annam_tour_sapa_landing_get_gallery_defaults();
	$settings = get_option( ANNAM_TOUR_SAPA_SETTINGS_OPTION, array() );
	$emails   = isset( $settings['lead_emails'] ) ? (string) $settings['lead_emails'] : '';

	wp_enqueue_media();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Tour Sapa 3N2Đ — Ảnh & Email', 'generatepress_child' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Thư viện đầu trang: mosaic 6 ô + lightbox toàn bộ. Có thể thêm nhiều hơn 6 ảnh.', 'generatepress_child' ); ?></p>

		<form method="post" id="annam-tour-sapa-images-form">
			<?php wp_nonce_field( 'annam_tour_sapa_images_save', 'annam_tour_sapa_images_nonce' ); ?>

			<h2><?php esc_html_e( '1. Thư viện ảnh đầu trang', 'generatepress_child' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Ảnh 1 = ô lớn. Ảnh 6 trong mosaic = nút Gallery. Caption hiện trên ảnh / lightbox.', 'generatepress_child' ); ?></p>

			<table class="widefat striped" id="annam-tour-sapa-gallery-table" style="max-width:960px;margin:12px 0 24px;">
				<thead>
					<tr>
						<th style="width:56px;">#</th>
						<th style="width:140px;"><?php esc_html_e( 'Ảnh', 'generatepress_child' ); ?></th>
						<th><?php esc_html_e( 'Caption', 'generatepress_child' ); ?></th>
						<th style="width:220px;"><?php esc_html_e( 'Thao tác', 'generatepress_child' ); ?></th>
					</tr>
				</thead>
				<tbody id="annam-tour-sapa-gallery-body">
					<?php foreach ( $gallery as $i => $row ) :
						$aid     = isset( $row['id'] ) ? absint( $row['id'] ) : 0;
						$cap     = isset( $row['caption'] ) ? (string) $row['caption'] : '';
						$fb      = isset( $row['fallback'] ) ? (string) $row['fallback'] : ( isset( $defaults[ min( (int) $i, 5 ) ]['fallback'] ) ? (string) $defaults[ min( (int) $i, 5 ) ]['fallback'] : '' );
						$preview = $aid ? wp_get_attachment_image_url( $aid, 'medium' ) : $fb;
						?>
						<tr class="annam-tour-sapa-gallery-row" data-index="<?php echo esc_attr( (string) $i ); ?>">
							<td class="annam-tour-sapa-gallery-num"><?php echo esc_html( (string) ( (int) $i + 1 ) ); ?></td>
							<td>
								<img src="<?php echo esc_url( (string) $preview ); ?>" alt="" class="annam-tour-sapa-gallery-prev" style="width:120px;height:80px;object-fit:cover;border-radius:8px;background:#eee;" />
								<input type="hidden" name="annam_tour_sapa_gallery[<?php echo esc_attr( (string) $i ); ?>][id]" class="annam-tour-sapa-gallery-id" value="<?php echo esc_attr( (string) $aid ); ?>" />
								<input type="hidden" name="annam_tour_sapa_gallery[<?php echo esc_attr( (string) $i ); ?>][fallback]" value="<?php echo esc_attr( $fb ); ?>" />
							</td>
							<td>
								<input type="text" class="large-text annam-tour-sapa-gallery-caption" name="annam_tour_sapa_gallery[<?php echo esc_attr( (string) $i ); ?>][caption]" value="<?php echo esc_attr( $cap ); ?>" placeholder="<?php esc_attr_e( 'Mô tả ảnh', 'generatepress_child' ); ?>" />
							</td>
							<td>
								<button type="button" class="button annam-tour-sapa-gallery-pick"><?php esc_html_e( 'Chọn ảnh', 'generatepress_child' ); ?></button>
								<button type="button" class="button annam-tour-sapa-gallery-clear"><?php esc_html_e( 'Xóa ảnh', 'generatepress_child' ); ?></button>
								<button type="button" class="button link-delete annam-tour-sapa-gallery-remove" <?php disabled( count( $gallery ) <= 6 ); ?>><?php esc_html_e( 'Gỡ dòng', 'generatepress_child' ); ?></button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p>
				<button type="button" class="button button-secondary" id="annam-tour-sapa-gallery-add"><?php esc_html_e( '+ Thêm ảnh vào thư viện', 'generatepress_child' ); ?></button>
			</p>

			<h2><?php esc_html_e( '2. Ảnh lịch trình (3 ngày)', 'generatepress_child' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php foreach ( $itin_slots as $key => $slot ) :
					$aid     = isset( $images[ $key ] ) ? absint( $images[ $key ] ) : 0;
					$preview = $aid ? wp_get_attachment_image_url( $aid, 'medium' ) : $slot['fallback'];
					?>
					<tr>
						<th scope="row"><label for="annam-tour-sapa-img-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $slot['label'] ); ?></label></th>
						<td>
							<div style="display:flex;gap:12px;align-items:flex-start;">
								<img src="<?php echo esc_url( $preview ); ?>" alt="" style="width:140px;height:90px;object-fit:cover;border-radius:8px;" id="annam-tour-sapa-prev-<?php echo esc_attr( $key ); ?>" />
								<div>
									<input type="hidden" name="annam_tour_sapa_img_<?php echo esc_attr( $key ); ?>" id="annam-tour-sapa-img-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( (string) $aid ); ?>" />
									<button type="button" class="button annam-tour-sapa-pick" data-slot="<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Chọn ảnh', 'generatepress_child' ); ?></button>
									<button type="button" class="button annam-tour-sapa-clear" data-slot="<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Xóa', 'generatepress_child' ); ?></button>
									<p class="description"><?php echo esc_html( $slot['default_caption'] ); ?></p>
								</div>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>

			<h2><?php esc_html_e( '3. Email nhận lead', 'generatepress_child' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th><label for="annam_tour_sapa_lead_emails"><?php esc_html_e( 'Email', 'generatepress_child' ); ?></label></th>
					<td>
						<input type="text" class="large-text" id="annam_tour_sapa_lead_emails" name="annam_tour_sapa_lead_emails" value="<?php echo esc_attr( $emails ); ?>" placeholder="email1@example.com, email2@example.com" />
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Lưu thay đổi', 'generatepress_child' ) ); ?>
		</form>
	</div>

	<script>
	(function($){
		var defaultFallback = <?php echo wp_json_encode( isset( $defaults[0]['fallback'] ) ? $defaults[0]['fallback'] : '' ); ?>;

		function reindexGallery() {
			$('#annam-tour-sapa-gallery-body .annam-tour-sapa-gallery-row').each(function(i){
				var $row = $(this);
				$row.attr('data-index', i);
				$row.find('.annam-tour-sapa-gallery-num').text(i + 1);
				$row.find('.annam-tour-sapa-gallery-id').attr('name', 'annam_tour_sapa_gallery[' + i + '][id]');
				$row.find('input[name*="[fallback]"]').attr('name', 'annam_tour_sapa_gallery[' + i + '][fallback]');
				$row.find('.annam-tour-sapa-gallery-caption').attr('name', 'annam_tour_sapa_gallery[' + i + '][caption]');
			});
			var count = $('#annam-tour-sapa-gallery-body .annam-tour-sapa-gallery-row').length;
			$('#annam-tour-sapa-gallery-body .annam-tour-sapa-gallery-remove').prop('disabled', count <= 6);
		}

		$(document).on('click', '.annam-tour-sapa-pick', function(e){
			e.preventDefault();
			var slot = $(this).data('slot');
			var frame = wp.media({ title: 'Chọn ảnh', multiple: false });
			frame.on('select', function(){
				var att = frame.state().get('selection').first().toJSON();
				$('#annam-tour-sapa-img-' + slot).val(att.id);
				$('#annam-tour-sapa-prev-' + slot).attr('src', att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url);
			});
			frame.open();
		});

		$(document).on('click', '.annam-tour-sapa-clear', function(e){
			e.preventDefault();
			var slot = $(this).data('slot');
			$('#annam-tour-sapa-img-' + slot).val('0');
		});

		$(document).on('click', '.annam-tour-sapa-gallery-pick', function(e){
			e.preventDefault();
			var $row = $(this).closest('.annam-tour-sapa-gallery-row');
			var frame = wp.media({ title: 'Chọn ảnh thư viện', multiple: false });
			frame.on('select', function(){
				var att = frame.state().get('selection').first().toJSON();
				$row.find('.annam-tour-sapa-gallery-id').val(att.id);
				$row.find('.annam-tour-sapa-gallery-prev').attr('src', att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url);
				if (!$row.find('.annam-tour-sapa-gallery-caption').val() && att.alt) {
					$row.find('.annam-tour-sapa-gallery-caption').val(att.alt);
				}
			});
			frame.open();
		});

		$(document).on('click', '.annam-tour-sapa-gallery-clear', function(e){
			e.preventDefault();
			var $row = $(this).closest('.annam-tour-sapa-gallery-row');
			$row.find('.annam-tour-sapa-gallery-id').val('0');
			var fb = $row.find('input[name*="[fallback]"]').val() || defaultFallback;
			$row.find('.annam-tour-sapa-gallery-prev').attr('src', fb);
		});

		$(document).on('click', '.annam-tour-sapa-gallery-remove', function(e){
			e.preventDefault();
			if ($('#annam-tour-sapa-gallery-body .annam-tour-sapa-gallery-row').length <= 6) {
				return;
			}
			$(this).closest('.annam-tour-sapa-gallery-row').remove();
			reindexGallery();
		});

		$('#annam-tour-sapa-gallery-add').on('click', function(e){
			e.preventDefault();
			var i = $('#annam-tour-sapa-gallery-body .annam-tour-sapa-gallery-row').length;
			var $row = $('<tr class="annam-tour-sapa-gallery-row"/>');
			$row.append('<td class="annam-tour-sapa-gallery-num">' + (i + 1) + '</td>');
			$row.append(
				'<td><img src="' + defaultFallback + '" alt="" class="annam-tour-sapa-gallery-prev" style="width:120px;height:80px;object-fit:cover;border-radius:8px;background:#eee;" />' +
				'<input type="hidden" name="annam_tour_sapa_gallery[' + i + '][id]" class="annam-tour-sapa-gallery-id" value="0" />' +
				'<input type="hidden" name="annam_tour_sapa_gallery[' + i + '][fallback]" value="' + defaultFallback + '" /></td>'
			);
			$row.append('<td><input type="text" class="large-text annam-tour-sapa-gallery-caption" name="annam_tour_sapa_gallery[' + i + '][caption]" value="" placeholder="Mô tả ảnh" /></td>');
			$row.append(
				'<td><button type="button" class="button annam-tour-sapa-gallery-pick">Chọn ảnh</button> ' +
				'<button type="button" class="button annam-tour-sapa-gallery-clear">Xóa ảnh</button> ' +
				'<button type="button" class="button link-delete annam-tour-sapa-gallery-remove">Gỡ dòng</button></td>'
			);
			$('#annam-tour-sapa-gallery-body').append($row);
			reindexGallery();
		});
	})(jQuery);
	</script>
	<?php
}
