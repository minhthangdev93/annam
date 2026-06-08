<?php
/**
 * Admin: ảnh Landing Cabin VIP (thư viện media).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ANNAM_CABIN_LANDING_IMAGES_OPTION' ) ) {
	define( 'ANNAM_CABIN_LANDING_IMAGES_OPTION', 'annam_cabin_landing_images' );
}

/**
 * Danh sách slot ảnh (key khớp config landing).
 *
 * @return array<string,array{label:string,section:string,placement:string,recommended:string,ratio:string,formats:string,wp_size:string}>
 */
function annam_cabin_landing_get_image_slots() {
	$slots = array(
		'cabin-single-2'  => array(
			'label'       => __( 'Cabin đơn tầng 2', 'generatepress_child' ),
			'section'     => 'cabins',
			'placement'   => __( 'Thẻ loại cabin — Cabin đơn tầng 2', 'generatepress_child' ),
			'recommended' => '800 × 600 px',
			'ratio'       => '4 : 3 (ngang)',
			'formats'     => 'JPG, WebP',
			'wp_size'     => 'annam-cabin-card',
		),
		'cabin-single-1'  => array(
			'label'       => __( 'Cabin đơn tầng 1', 'generatepress_child' ),
			'section'     => 'cabins',
			'placement'   => __( 'Thẻ loại cabin — Cabin đơn tầng 1', 'generatepress_child' ),
			'recommended' => '800 × 600 px',
			'ratio'       => '4 : 3 (ngang)',
			'formats'     => 'JPG, WebP',
			'wp_size'     => 'large',
		),
		'cabin-double'    => array(
			'label'       => __( 'Cabin đôi (nổi bật)', 'generatepress_child' ),
			'section'     => 'cabins',
			'placement'   => __( 'Thẻ loại cabin — Cabin đôi (card lớn, giữa)', 'generatepress_child' ),
			'recommended' => '900 × 675 px',
			'ratio'       => '4 : 3 (ngang)',
			'formats'     => 'JPG, WebP',
			'wp_size'     => 'annam-cabin-card',
		),
		'gallery-hero'    => array(
			'label'       => __( 'Gallery — ảnh lớn (mosaic)', 'generatepress_child' ),
			'section'     => 'gallery',
			'placement'   => __( 'Ảnh chính bên trái (lightbox)', 'generatepress_child' ),
			'recommended' => '1200 × 750 px',
			'ratio'       => '16 : 10 (ngang)',
			'formats'     => 'JPG, WebP',
			'wp_size'     => 'annam-cabin-gallery-main',
		),
		'gallery-bed'     => array(
			'label'       => __( 'Gallery — Giường nằm', 'generatepress_child' ),
			'section'     => 'gallery',
			'placement'   => __( 'Ảnh phụ (mosaic + lightbox)', 'generatepress_child' ),
			'recommended' => '800 × 600 px',
			'ratio'       => '4 : 3 (ngang)',
			'formats'     => 'JPG, WebP',
			'wp_size'     => 'annam-cabin-gallery-thumb',
		),
		'gallery-double'  => array(
			'label'       => __( 'Gallery — Cabin đôi', 'generatepress_child' ),
			'section'     => 'gallery',
			'placement'   => __( 'Ảnh phụ (mosaic + lightbox)', 'generatepress_child' ),
			'recommended' => '800 × 600 px',
			'ratio'       => '4 : 3 (ngang)',
			'formats'     => 'JPG, WebP',
			'wp_size'     => 'annam-cabin-gallery-thumb',
		),
		'gallery-amenity' => array(
			'label'       => __( 'Gallery — Tiện ích cabin', 'generatepress_child' ),
			'section'     => 'gallery',
			'placement'   => __( 'Ảnh phụ (mosaic + lightbox)', 'generatepress_child' ),
			'recommended' => '800 × 600 px',
			'ratio'       => '4 : 3 (ngang)',
			'formats'     => 'JPG, WebP',
			'wp_size'     => 'annam-cabin-gallery-thumb',
		),
		'gallery-clean'   => array(
			'label'       => __( 'Gallery — Ngoại thất xe', 'generatepress_child' ),
			'section'     => 'gallery',
			'placement'   => __( 'Ảnh phụ 3 — ngoại thất (grid 2×2)', 'generatepress_child' ),
			'recommended' => '800 × 600 px',
			'ratio'       => '4 : 3 (ngang)',
			'formats'     => 'JPG, WebP',
			'wp_size'     => 'annam-cabin-gallery-thumb',
		),
	);

	return apply_filters( 'annam_cabin_landing_image_slots', $slots );
}

/**
 * Attachment ID đã lưu cho một slot.
 *
 * @param string $key Slot key.
 * @return int
 */
function annam_cabin_landing_get_image_attachment_id( $key ) {
	$key = sanitize_key( $key );
	if ( '' === $key ) {
		return 0;
	}

	$slots = annam_cabin_landing_get_image_slots();
	if ( ! isset( $slots[ $key ] ) ) {
		return 0;
	}

	$saved = get_option( ANNAM_CABIN_LANDING_IMAGES_OPTION, array() );
	if ( ! is_array( $saved ) || empty( $saved[ $key ] ) ) {
		return 0;
	}

	$id = absint( $saved[ $key ] );
	if ( $id > 0 && wp_attachment_is_image( $id ) ) {
		return $id;
	}

	return 0;
}

/**
 * Đăng submenu dưới An Nam Settings.
 */
function annam_cabin_landing_images_register_menu() {
	add_submenu_page(
		'annam-settings',
		__( 'Landing Cabin VIP', 'generatepress_child' ),
		__( 'Landing Cabin VIP', 'generatepress_child' ),
		'manage_options',
		'annam-cabin-landing-images',
		'annam_cabin_landing_images_render_admin_page'
	);
}
add_action( 'admin_menu', 'annam_cabin_landing_images_register_menu', 20 );

/**
 * Lưu ảnh.
 */
function annam_cabin_landing_images_maybe_save() {
	if ( ! is_admin() || empty( $_POST['annam_cabin_landing_images_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	if ( empty( $_GET['page'] ) || 'annam-cabin-landing-images' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'annam_save_cabin_landing_images', 'annam_cabin_landing_images_nonce' );

	$input = isset( $_POST['annam_cabin_landing_images'] ) && is_array( $_POST['annam_cabin_landing_images'] )
		? wp_unslash( $_POST['annam_cabin_landing_images'] )
		: array();

	$clean = array();
	foreach ( annam_cabin_landing_get_image_slots() as $key => $slot ) {
		$clean[ $key ] = isset( $input[ $key ] ) ? absint( $input[ $key ] ) : 0;
	}

	update_option( ANNAM_CABIN_LANDING_IMAGES_OPTION, $clean, false );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'        => 'annam-cabin-landing-images',
				'annam_saved' => '1',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_init', 'annam_cabin_landing_images_maybe_save' );

/**
 * Enqueue media + annam-admin trên trang ảnh cabin.
 *
 * @param string $hook_suffix Hook.
 */
function annam_cabin_landing_images_admin_assets( $hook_suffix ) {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'annam-settings_page_annam-cabin-landing-images' !== $hook_suffix && 'annam-cabin-landing-images' !== $page ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	wp_enqueue_media();

	$css_path = $dir . '/assets/css/annam-admin.css';
	$js_path  = $dir . '/assets/js/annam-admin.js';

	wp_enqueue_style(
		'annam-admin',
		$uri . '/assets/css/annam-admin.css',
		array(),
		file_exists( $css_path ) ? (string) filemtime( $css_path ) : '1.0.0'
	);

	wp_enqueue_script(
		'annam-admin',
		$uri . '/assets/js/annam-admin.js',
		array( 'jquery', 'media-upload', 'media-views', 'media-editor' ),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : '1.0.0',
		true
	);

	wp_localize_script(
		'annam-admin',
		'annamAdminL10n',
		array(
			'pickTitle'        => __( 'Chọn ảnh', 'generatepress_child' ),
			'pickButton'       => __( 'Dùng ảnh này', 'generatepress_child' ),
			'placeholderImage' => __( 'Chưa chọn ảnh', 'generatepress_child' ),
			'mediaUnavailable' => __( 'Không mở được thư viện ảnh. Vui lòng tải lại trang hoặc thử trình duyệt khác.', 'generatepress_child' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'annam_cabin_landing_images_admin_assets' );

/**
 * Một field ảnh trên trang admin.
 *
 * @param string $key       Slot key.
 * @param array  $slot      Metadata slot.
 * @param int    $value_id  Attachment ID.
 */
function annam_cabin_landing_images_render_field( $key, array $slot, $value_id ) {
	$value_id = absint( $value_id );
	$name     = 'annam_cabin_landing_images[' . $key . ']';
	$preview  = $value_id && wp_attachment_is_image( $value_id )
		? wp_get_attachment_image_url( $value_id, 'medium' )
		: '';

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
				<?php echo esc_html( $slot['recommended'] ); ?>
			</li>
			<li>
				<strong><?php esc_html_e( 'Tỷ lệ:', 'generatepress_child' ); ?></strong>
				<?php echo esc_html( $slot['ratio'] ); ?>
			</li>
			<li>
				<strong><?php esc_html_e( 'Định dạng:', 'generatepress_child' ); ?></strong>
				<?php echo esc_html( $slot['formats'] ); ?>
			</li>
		</ul>
		<?php if ( ! empty( $meta ) ) : ?>
			<p class="annam-cabin-image-field__current"><?php echo esc_html( implode( ' · ', $meta ) ); ?></p>
		<?php endif; ?>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value_id ); ?>" class="annam-about-attachment-id" />
		<div class="annam-media-preview annam-about-image-field__preview annam-cabin-image-field__preview">
			<?php if ( $preview ) : ?>
				<img src="<?php echo esc_url( $preview ); ?>" alt="" width="160" height="120" />
			<?php else : ?>
				<span class="annam-media-placeholder"><?php esc_html_e( 'Chưa chọn ảnh — dùng ảnh mặc định theme', 'generatepress_child' ); ?></span>
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
 * Render trang admin.
 */
function annam_cabin_landing_images_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$saved = get_option( ANNAM_CABIN_LANDING_IMAGES_OPTION, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	$slots   = annam_cabin_landing_get_image_slots();
	$cabins  = array();
	$gallery = array();

	foreach ( $slots as $key => $slot ) {
		if ( 'gallery' === $slot['section'] ) {
			$gallery[ $key ] = $slot;
		} else {
			$cabins[ $key ] = $slot;
		}
	}

	$landing_pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-template-cabin-vip-landing.php',
		)
	);

	$settings      = function_exists( 'annam_cabin_landing_get_settings' ) ? annam_cabin_landing_get_settings() : array();
	$lead_emails   = isset( $settings['lead_emails'] ) ? (string) $settings['lead_emails'] : '';
	$admin_default = get_option( 'admin_email' );
	?>
	<div class="wrap annam-admin-wrap annam-cabin-images-wrap">
		<h1><?php esc_html_e( 'Landing Cabin VIP', 'generatepress_child' ); ?></h1>

		<?php if ( ! empty( $_GET['annam_settings_saved'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã lưu cài đặt email lead.', 'generatepress_child' ); ?></p></div>
		<?php endif; ?>

		<?php if ( ! empty( $_GET['annam_saved'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã lưu ảnh landing.', 'generatepress_child' ); ?></p></div>
		<?php endif; ?>

		<h2 class="annam-admin-section-title"><?php esc_html_e( 'Cài đặt form đặt vé', 'generatepress_child' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=annam-cabin-landing-images' ) ); ?>" class="annam-cabin-settings-form">
			<?php wp_nonce_field( 'annam_save_cabin_landing_settings', 'annam_cabin_landing_settings_nonce' ); ?>
			<input type="hidden" name="annam_cabin_landing_settings_action" value="1" />
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="annam_cabin_lead_emails"><?php esc_html_e( 'Email nhận lead', 'generatepress_child' ); ?></label></th>
					<td>
						<input type="text" class="large-text" id="annam_cabin_lead_emails" name="annam_cabin_lead_emails" value="<?php echo esc_attr( $lead_emails ); ?>" placeholder="email1@example.com, email2@example.com" />
						<p class="description">
							<?php
							printf(
								/* translators: %s: default admin email */
								esc_html__( 'Nhiều email cách nhau bằng dấu phẩy. Để trống sẽ gửi về email quản trị: %s', 'generatepress_child' ),
								esc_html( is_email( $admin_default ) ? $admin_default : '—' )
							);
							?>
						</p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Lưu cài đặt', 'generatepress_child' ); ?></button>
			</p>
		</form>

		<div class="notice notice-info inline annam-cabin-images-intro">
			<p>
				<?php esc_html_e( 'Chọn ảnh từ Thư viện Media cho landing “Xe Cabin VIP”. Ảnh nên nén nhẹ (JPG/WebP) để tải nhanh trên mobile (Google Ads). Nếu để trống một mục, website dùng ảnh mặc định trong theme hoặc ảnh placeholder.', 'generatepress_child' ); ?>
			</p>
			<p>
				<strong><?php esc_html_e( 'Gợi ý chung:', 'generatepress_child' ); ?></strong>
				<?php esc_html_e( 'ảnh sáng, rõ nội thất cabin/giường; tránh watermark; không kéo méo tỷ lệ.', 'generatepress_child' ); ?>
			</p>
		</div>

		<?php if ( ! empty( $landing_pages ) ) : ?>
			<p class="description">
				<?php esc_html_e( 'Trang đang dùng template Landing Cabin VIP:', 'generatepress_child' ); ?>
				<?php
				$links = array();
				foreach ( $landing_pages as $p ) {
					$links[] = '<a href="' . esc_url( get_edit_post_link( $p->ID, 'raw' ) ) . '">' . esc_html( get_the_title( $p ) ) . '</a>';
				}
				echo wp_kses_post( implode( ' · ', $links ) );
				?>
			</p>
		<?php else : ?>
			<p class="description">
				<?php esc_html_e( 'Chưa có trang nào gán template “Landing Cabin VIP (Google Ads)”. Tạo trang mới → Page Attributes → chọn template đó.', 'generatepress_child' ); ?>
			</p>
		<?php endif; ?>

		<h2 class="annam-admin-section-title"><?php esc_html_e( 'Ảnh landing', 'generatepress_child' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=annam-cabin-landing-images' ) ); ?>" id="annam-cabin-landing-images-form">
			<?php wp_nonce_field( 'annam_save_cabin_landing_images', 'annam_cabin_landing_images_nonce' ); ?>
			<input type="hidden" name="annam_cabin_landing_images_action" value="1" />

			<h2 class="annam-admin-section-title"><?php esc_html_e( 'Loại cabin (3 ảnh)', 'generatepress_child' ); ?></h2>
			<p class="description annam-cabin-section-note">
				<?php esc_html_e( 'Hiển thị trên section “Chọn loại cabin”. Card giữa (cabin đôi) có viền nổi bật — nên dùng ảnh đẹp nhất.', 'generatepress_child' ); ?>
			</p>
			<div class="annam-cabin-images-grid">
				<?php
				foreach ( $cabins as $key => $slot ) {
					annam_cabin_landing_images_render_field(
						$key,
						$slot,
						isset( $saved[ $key ] ) ? absint( $saved[ $key ] ) : 0
					);
				}
				?>
			</div>

			<h2 class="annam-admin-section-title"><?php esc_html_e( 'Gallery “Ảnh xe & cabin” (5 ảnh)', 'generatepress_child' ); ?></h2>
			<p class="description annam-cabin-section-note">
				<?php esc_html_e( 'Ảnh 1: nội thất lớn bên trái. Ảnh 2–5: cabin đơn, cabin đôi, ngoại thất, tiện ích (grid 2×2 bên phải) — click mở lightbox.', 'generatepress_child' ); ?>
			</p>
			<div class="annam-cabin-images-grid">
				<?php
				foreach ( $gallery as $key => $slot ) {
					annam_cabin_landing_images_render_field(
						$key,
						$slot,
						isset( $saved[ $key ] ) ? absint( $saved[ $key ] ) : 0
					);
				}
				?>
			</div>

			<p class="submit">
				<button type="submit" class="button button-primary button-large"><?php esc_html_e( 'Lưu tất cả ảnh', 'generatepress_child' ); ?></button>
			</p>
		</form>
	</div>
	<?php
}
