<?php
/**
 * Admin: ảnh + email lead Landing Limousine HN–Sapa.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ANNAM_LIMO_LANDING_IMAGES_OPTION' ) ) {
	define( 'ANNAM_LIMO_LANDING_IMAGES_OPTION', 'annam_limo_landing_images' );
}

if ( ! defined( 'ANNAM_LIMO_LANDING_CAPTIONS_OPTION' ) ) {
	define( 'ANNAM_LIMO_LANDING_CAPTIONS_OPTION', 'annam_limo_landing_captions' );
}

/**
 * @return array<string,array{label:string,section:string,placement:string,recommended:string,ratio:string,formats:string,wp_size:string,fallback:string,default_caption?:string}>
 */
function annam_limo_landing_get_image_slots() {
	$banner_fallback = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/limo-landing-banner.png';

	$slots = array(
		'hero-banner'      => array(
			'label'       => __( 'Banner đầu trang', 'generatepress_child' ),
			'section'     => 'banner',
			'placement'   => __( 'Đầu landing — full width', 'generatepress_child' ),
			'recommended' => '2048 × 751 px',
			'ratio'       => '2048 : 751',
			'formats'     => 'JPG, WebP',
			'wp_size'     => 'full',
			'fallback'    => $banner_fallback,
		),
		'gallery-exterior' => array(
			'label'           => __( 'Ngoại thất xe', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Gallery — ảnh chính', 'generatepress_child' ),
			'recommended'     => '1200 × 750 px',
			'ratio'           => '16 : 10',
			'formats'         => 'JPG, WebP',
			'wp_size'         => 'large',
			'fallback'        => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=80',
			'default_caption' => 'Ngoại thất Limousine 11 chỗ',
		),
		'gallery-interior' => array(
			'label'           => __( 'Nội thất ghế', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Gallery — ảnh phụ', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4 : 3',
			'formats'         => 'JPG, WebP',
			'wp_size'         => 'large',
			'fallback'        => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?auto=format&fit=crop&w=800&q=80',
			'default_caption' => 'Không gian ghế Limousine',
		),
		'gallery-seat'     => array(
			'label'           => __( 'Chi tiết ghế', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Gallery — ảnh phụ', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4 : 3',
			'formats'         => 'JPG, WebP',
			'wp_size'         => 'large',
			'fallback'        => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=800&q=80',
			'default_caption' => 'Ghế ngồi thoải mái',
		),
		'gallery-detail'   => array(
			'label'           => __( 'Tiện nghi / chi tiết', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Gallery — ảnh phụ', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4 : 3',
			'formats'         => 'JPG, WebP',
			'wp_size'         => 'large',
			'fallback'        => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=800&q=80',
			'default_caption' => 'Chi tiết tiện nghi trên xe',
		),
		'gallery-ready'    => array(
			'label'           => __( 'Xe sẵn sàng xuất phát', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Gallery — ảnh phụ', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4 : 3',
			'formats'         => 'JPG, WebP',
			'wp_size'         => 'large',
			'fallback'        => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=800&q=80',
			'default_caption' => 'Xe sẵn sàng xuất phát',
		),
		'gallery-road'     => array(
			'label'           => __( 'Trên đường Hà Nội ⇄ Sapa', 'generatepress_child' ),
			'section'         => 'gallery',
			'placement'       => __( 'Gallery — ảnh phụ (mobile)', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4 : 3',
			'formats'         => 'JPG, WebP',
			'wp_size'         => 'large',
			'fallback'        => 'https://images.unsplash.com/photo-1485291571150-772bcfc10da5?auto=format&fit=crop&w=800&q=80',
			'default_caption' => 'Trên đường Hà Nội ⇄ Sapa',
		),
	);

	return apply_filters( 'annam_limo_landing_image_slots', $slots );
}

/**
 * @param string $key Slot key.
 * @return string
 */
function annam_limo_landing_get_image_caption( $key ) {
	$key   = sanitize_key( $key );
	$slots = annam_limo_landing_get_image_slots();
	if ( ! isset( $slots[ $key ] ) ) {
		return '';
	}

	$saved = get_option( ANNAM_LIMO_LANDING_CAPTIONS_OPTION, array() );
	if ( is_array( $saved ) && isset( $saved[ $key ] ) && is_string( $saved[ $key ] ) && '' !== trim( $saved[ $key ] ) ) {
		return sanitize_text_field( $saved[ $key ] );
	}

	return isset( $slots[ $key ]['default_caption'] ) ? (string) $slots[ $key ]['default_caption'] : '';
}

/**
 * @param string $key Slot key.
 * @return int
 */
function annam_limo_landing_get_image_attachment_id( $key ) {
	$key = sanitize_key( $key );
	if ( '' === $key ) {
		return 0;
	}
	$slots = annam_limo_landing_get_image_slots();
	if ( ! isset( $slots[ $key ] ) ) {
		return 0;
	}
	$saved = get_option( ANNAM_LIMO_LANDING_IMAGES_OPTION, array() );
	if ( ! is_array( $saved ) || empty( $saved[ $key ] ) ) {
		return 0;
	}
	$id = absint( $saved[ $key ] );
	return ( $id > 0 && wp_attachment_is_image( $id ) ) ? $id : 0;
}

/**
 * @param string $key Slot key.
 * @return string
 */
function annam_limo_landing_image_url( $key ) {
	$key   = sanitize_key( $key );
	$slots = annam_limo_landing_get_image_slots();
	if ( ! isset( $slots[ $key ] ) ) {
		return '';
	}
	$aid = annam_limo_landing_get_image_attachment_id( $key );
	if ( $aid > 0 ) {
		$url = wp_get_attachment_image_url( $aid, 'large' );
		return is_string( $url ) ? $url : '';
	}
	return isset( $slots[ $key ]['fallback'] ) ? (string) $slots[ $key ]['fallback'] : '';
}

function annam_limo_landing_images_register_menu() {
	add_submenu_page(
		'annam-settings',
		__( 'Landing Limousine HN–Sapa', 'generatepress_child' ),
		__( 'Landing Limousine HN–Sapa', 'generatepress_child' ),
		'manage_options',
		'annam-limo-landing-images',
		'annam_limo_landing_images_render_admin_page'
	);
}
add_action( 'admin_menu', 'annam_limo_landing_images_register_menu', 21 );

function annam_limo_landing_images_maybe_save() {
	if ( ! is_admin() || empty( $_POST['annam_limo_landing_images_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}
	if ( empty( $_GET['page'] ) || 'annam-limo-landing-images' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'annam_save_limo_landing_images', 'annam_limo_landing_images_nonce' );

	$input = isset( $_POST['annam_limo_landing_images'] ) && is_array( $_POST['annam_limo_landing_images'] )
		? wp_unslash( $_POST['annam_limo_landing_images'] )
		: array();

	$clean = array();
	foreach ( annam_limo_landing_get_image_slots() as $key => $slot ) {
		$clean[ $key ] = isset( $input[ $key ] ) ? absint( $input[ $key ] ) : 0;
	}
	update_option( ANNAM_LIMO_LANDING_IMAGES_OPTION, $clean, false );

	$cap_input = isset( $_POST['annam_limo_landing_captions'] ) && is_array( $_POST['annam_limo_landing_captions'] )
		? wp_unslash( $_POST['annam_limo_landing_captions'] )
		: array();
	$cap_clean = array();
	foreach ( annam_limo_landing_get_image_slots() as $key => $slot ) {
		if ( 'gallery' !== ( $slot['section'] ?? '' ) ) {
			continue;
		}
		$raw = isset( $cap_input[ $key ] ) ? (string) $cap_input[ $key ] : '';
		$cap_clean[ $key ] = sanitize_text_field( $raw );
	}
	update_option( ANNAM_LIMO_LANDING_CAPTIONS_OPTION, $cap_clean, false );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'        => 'annam-limo-landing-images',
				'annam_saved' => '1',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_init', 'annam_limo_landing_images_maybe_save' );

/**
 * @param string $hook_suffix Hook.
 */
/**
 * @param string $hook_suffix Hook.
 */
function annam_limo_landing_images_admin_assets( $hook_suffix ) {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( false === strpos( (string) $hook_suffix, 'annam-limo-landing-images' ) && 'annam-limo-landing-images' !== $page ) {
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
add_action( 'admin_enqueue_scripts', 'annam_limo_landing_images_admin_assets' );

/**
 * @param string               $key      Slot.
 * @param array<string,string> $slot     Meta.
 * @param int                  $value_id Attachment.
 * @param string               $caption  Caption (gallery only).
 */
function annam_limo_landing_images_render_field( $key, array $slot, $value_id, $caption = '' ) {
	$value_id = absint( $value_id );
	$name     = 'annam_limo_landing_images[' . $key . ']';
	$preview  = $value_id && wp_attachment_is_image( $value_id )
		? wp_get_attachment_image_url( $value_id, 'medium' )
		: '';
	$is_gallery = ( 'gallery' === ( $slot['section'] ?? '' ) );
	$cap_name   = 'annam_limo_landing_captions[' . $key . ']';
	$cap_value  = '' !== $caption ? $caption : ( isset( $slot['default_caption'] ) ? (string) $slot['default_caption'] : '' );
	?>
	<div class="annam-about-image-field" data-annam-about-image>
		<label class="annam-about-image-field__label"><?php echo esc_html( $slot['label'] ); ?></label>
		<p><?php echo esc_html( $slot['placement'] ); ?> — <?php echo esc_html( $slot['recommended'] ); ?></p>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value_id ); ?>" class="annam-about-attachment-id" />
		<div class="annam-media-preview annam-about-image-field__preview">
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
		<?php if ( $is_gallery ) : ?>
			<p>
				<label for="annam-limo-cap-<?php echo esc_attr( $key ); ?>"><strong><?php esc_html_e( 'Mô tả ảnh (caption)', 'generatepress_child' ); ?></strong></label><br />
				<input type="text" class="large-text" id="annam-limo-cap-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $cap_name ); ?>" value="<?php echo esc_attr( $cap_value ); ?>" maxlength="120" placeholder="<?php esc_attr_e( 'VD: Ngoại thất Limousine 11 chỗ', 'generatepress_child' ); ?>" />
			</p>
		<?php endif; ?>
	</div>
	<?php
}

function annam_limo_landing_images_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$saved = get_option( ANNAM_LIMO_LANDING_IMAGES_OPTION, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	$captions = get_option( ANNAM_LIMO_LANDING_CAPTIONS_OPTION, array() );
	if ( ! is_array( $captions ) ) {
		$captions = array();
	}
	$slots    = annam_limo_landing_get_image_slots();
	$settings = function_exists( 'annam_limo_landing_get_settings' ) ? annam_limo_landing_get_settings() : array();
	$emails   = isset( $settings['lead_emails'] ) ? (string) $settings['lead_emails'] : '';
	$youtube_url = isset( $settings['youtube_url'] ) ? (string) $settings['youtube_url'] : '';
	if ( '' === $youtube_url && function_exists( 'annam_limo_landing_get_youtube_url' ) ) {
		$youtube_url = annam_limo_landing_get_youtube_url();
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Landing Limousine HN–Sapa', 'generatepress_child' ); ?></h1>

		<?php if ( ! empty( $_GET['annam_saved'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã lưu.', 'generatepress_child' ); ?></p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Email & video YouTube', 'generatepress_child' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=annam-limo-landing-images' ) ); ?>">
			<?php wp_nonce_field( 'annam_save_limo_landing_settings', 'annam_limo_landing_settings_nonce' ); ?>
			<input type="hidden" name="annam_limo_landing_settings_action" value="1" />
			<p>
				<label for="annam_limo_lead_emails"><?php esc_html_e( 'Email nhận lead (cách nhau bằng dấu phẩy)', 'generatepress_child' ); ?></label>
				<input type="text" class="large-text" id="annam_limo_lead_emails" name="annam_limo_lead_emails" value="<?php echo esc_attr( $emails ); ?>" placeholder="email1@example.com, email2@example.com" />
			</p>
			<p>
				<label for="annam_limo_youtube_url"><strong><?php esc_html_e( 'Link video YouTube (trust)', 'generatepress_child' ); ?></strong></label><br />
				<input type="url" class="large-text" id="annam_limo_youtube_url" name="annam_limo_youtube_url" value="<?php echo esc_attr( $youtube_url ); ?>" placeholder="https://www.youtube.com/watch?v=..." />
			</p>
			<p class="description"><?php esc_html_e( 'Dán link watch / youtu.be / shorts. Để trống sẽ dùng video placeholder tạm. Video hiện sau khối Hình ảnh xe.', 'generatepress_child' ); ?></p>
			<?php submit_button( __( 'Lưu email & video', 'generatepress_child' ) ); ?>
		</form>

		<hr />

		<h2><?php esc_html_e( 'Ảnh banner & gallery xe', 'generatepress_child' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Đổi ảnh và mô tả (caption) cho từng ảnh gallery. Caption hiện dưới ảnh trên trang landing.', 'generatepress_child' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=annam-limo-landing-images' ) ); ?>">
			<?php wp_nonce_field( 'annam_save_limo_landing_images', 'annam_limo_landing_images_nonce' ); ?>
			<input type="hidden" name="annam_limo_landing_images_action" value="1" />
			<?php
			foreach ( $slots as $key => $slot ) {
				$cap = isset( $captions[ $key ] ) ? (string) $captions[ $key ] : '';
				annam_limo_landing_images_render_field( $key, $slot, isset( $saved[ $key ] ) ? (int) $saved[ $key ] : 0, $cap );
			}
			submit_button( __( 'Lưu ảnh & mô tả', 'generatepress_child' ) );
			?>
		</form>
	</div>
	<?php
}
