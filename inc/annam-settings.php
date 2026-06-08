<?php
/**
 * Trang cài đặt admin An Nam (child theme): tab Slider, Giới thiệu, Hệ sinh thái.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ANNAM_HOME_SLIDERS_OPTION' ) ) {
	define( 'ANNAM_HOME_SLIDERS_OPTION', 'annam_home_sliders' );
}

/**
 * Tab hiện tại trên trang cài đặt.
 *
 * @return string 'slider'|'about'|'ecosystem'
 */
function annam_settings_get_current_tab() {
	if ( empty( $_GET['tab'] ) || ! is_string( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return 'slider';
	}
	$t = sanitize_key( wp_unslash( $_GET['tab'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'about' === $t || 'ecosystem' === $t ) {
		return $t;
	}
	return 'slider';
}

/**
 * Đăng menu cấp cao và submenu đầu tiên.
 */
function annam_settings_register_menu() {
	add_menu_page(
		__( 'An Nam Settings', 'generatepress_child' ),
		__( 'An Nam Settings', 'generatepress_child' ),
		'manage_options',
		'annam-settings',
		'annam_settings_render_admin_page',
		'dashicons-admin-generic',
		61
	);

	add_submenu_page(
		'annam-settings',
		__( 'Slider trang chủ', 'generatepress_child' ),
		__( 'Slider trang chủ', 'generatepress_child' ),
		'manage_options',
		'annam-settings',
		'annam_settings_render_admin_page'
	);
}
add_action( 'admin_menu', 'annam_settings_register_menu' );

/**
 * Redirect sau lưu (giữ tab).
 *
 * @param string $tab 'slider'|'about'|'ecosystem'.
 */
function annam_settings_redirect_saved( $tab ) {
	$tab = in_array( $tab, array( 'slider', 'about', 'ecosystem' ), true ) ? $tab : 'slider';
	wp_safe_redirect(
		add_query_arg(
			array(
				'page'        => 'annam-settings',
				'tab'         => $tab,
				'annam_saved' => '1',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}

/**
 * Lưu slider.
 */
function annam_settings_maybe_save_home_sliders() {
	if ( ! is_admin() || empty( $_POST['annam_home_sliders_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	if ( empty( $_GET['page'] ) || 'annam-settings' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'annam_save_home_sliders', 'annam_home_sliders_nonce' );

	$input = isset( $_POST['annam_home_sliders'] ) && is_array( $_POST['annam_home_sliders'] )
		? wp_unslash( $_POST['annam_home_sliders'] )
		: array();

	$clean = array();

	foreach ( $input as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$desktop_id = isset( $row['desktop_id'] ) ? absint( $row['desktop_id'] ) : 0;
		$mobile_id  = isset( $row['mobile_id'] ) ? absint( $row['mobile_id'] ) : 0;

		$clean[] = array(
			'desktop_id'  => $desktop_id,
			'mobile_id'   => $mobile_id,
			'title'       => isset( $row['title'] ) ? sanitize_text_field( (string) $row['title'] ) : '',
			'description' => isset( $row['description'] ) ? sanitize_textarea_field( (string) $row['description'] ) : '',
			'button_text' => isset( $row['button_text'] ) ? sanitize_text_field( (string) $row['button_text'] ) : '',
			'button_url'  => isset( $row['button_url'] ) ? esc_url_raw( (string) $row['button_url'] ) : '',
			'enabled'     => ! empty( $row['enabled'] ) ? 1 : 0,
			'order'       => isset( $row['order'] ) ? absint( $row['order'] ) : 0,
		);
	}

	update_option( ANNAM_HOME_SLIDERS_OPTION, $clean, false );
	annam_settings_redirect_saved( 'slider' );
}
add_action( 'admin_init', 'annam_settings_maybe_save_home_sliders' );

/**
 * Lưu tab Giới thiệu (option annam_about_settings).
 */
function annam_settings_maybe_save_about() {
	if ( ! is_admin() || empty( $_POST['annam_about_settings_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	if ( empty( $_GET['page'] ) || 'annam-settings' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! defined( 'ANNAM_ABOUT_SETTINGS_OPTION' ) ) {
		return;
	}

	check_admin_referer( 'annam_save_about_settings', 'annam_about_settings_nonce' );

	$input = isset( $_POST['annam_about_settings'] ) && is_array( $_POST['annam_about_settings'] )
		? wp_unslash( $_POST['annam_about_settings'] )
		: array();

	$gallery_raw = isset( $input['about_gallery_images'] ) ? $input['about_gallery_images'] : array();
	if ( ! is_array( $gallery_raw ) ) {
		$gallery_raw = array();
	}
	$gallery_ids = array_values( array_filter( array_map( 'absint', $gallery_raw ) ) );

	$clean = array(
		'about_who_we_are_image'       => isset( $input['about_who_we_are_image'] ) ? absint( $input['about_who_we_are_image'] ) : 0,
		'about_service_image_1'        => isset( $input['about_service_image_1'] ) ? absint( $input['about_service_image_1'] ) : 0,
		'about_service_image_2'        => isset( $input['about_service_image_2'] ) ? absint( $input['about_service_image_2'] ) : 0,
		'about_service_image_3'        => isset( $input['about_service_image_3'] ) ? absint( $input['about_service_image_3'] ) : 0,
		'about_service_image_4'        => isset( $input['about_service_image_4'] ) ? absint( $input['about_service_image_4'] ) : 0,
		'about_service_image_5'        => isset( $input['about_service_image_5'] ) ? absint( $input['about_service_image_5'] ) : 0,
		'about_service_image_6'        => isset( $input['about_service_image_6'] ) ? absint( $input['about_service_image_6'] ) : 0,
		'about_gallery_images'         => $gallery_ids,
		'about_cta_background_image'   => isset( $input['about_cta_background_image'] ) ? absint( $input['about_cta_background_image'] ) : 0,
	);

	update_option( ANNAM_ABOUT_SETTINGS_OPTION, $clean, false );
	annam_settings_redirect_saved( 'about' );
}
add_action( 'admin_init', 'annam_settings_maybe_save_about' );

/**
 * Lưu tab Hệ sinh thái (option annam_ecosystem_items).
 */
function annam_settings_maybe_save_ecosystem_items() {
	if ( ! is_admin() || empty( $_POST['annam_ecosystem_items_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	if ( empty( $_GET['page'] ) || 'annam-settings' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! defined( 'ANNAM_ECOSYSTEM_ITEMS_OPTION' ) ) {
		return;
	}

	check_admin_referer( 'annam_save_ecosystem_items', 'annam_ecosystem_items_nonce' );

	$input = isset( $_POST['annam_ecosystem_items'] ) && is_array( $_POST['annam_ecosystem_items'] )
		? wp_unslash( $_POST['annam_ecosystem_items'] )
		: array();

	$clean = array();

	foreach ( $input as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$clean[] = array(
			'name'    => isset( $row['name'] ) ? sanitize_text_field( (string) $row['name'] ) : '',
			'logo_id' => isset( $row['logo_id'] ) ? absint( $row['logo_id'] ) : 0,
			'url'     => isset( $row['url'] ) ? esc_url_raw( trim( (string) $row['url'] ) ) : '',
			'enabled' => ! empty( $row['enabled'] ) ? 1 : 0,
			'order'   => isset( $row['order'] ) ? absint( $row['order'] ) : 0,
		);
	}

	update_option( ANNAM_ECOSYSTEM_ITEMS_OPTION, $clean, false );
	annam_settings_redirect_saved( 'ecosystem' );
}
add_action( 'admin_init', 'annam_settings_maybe_save_ecosystem_items' );

/**
 * Enqueue CSS/JS chỉ trên trang An Nam Settings.
 *
 * @param string $hook_suffix Current admin page.
 */
function annam_settings_admin_assets( $hook_suffix ) {
	if ( 'toplevel_page_annam-settings' !== $hook_suffix ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );

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
		array( 'jquery', 'jquery-ui-sortable', 'media-upload', 'media-views', 'media-editor' ),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : '1.0.0',
		true
	);

	wp_localize_script(
		'annam-admin',
		'annamAdminL10n',
		array(
			'pickTitle'            => __( 'Chọn ảnh', 'generatepress_child' ),
			'pickButton'           => __( 'Dùng ảnh này', 'generatepress_child' ),
			'pickManyTitle'        => __( 'Chọn nhiều ảnh cho slider', 'generatepress_child' ),
			'pickManyButton'       => __( 'Tạo slide từ ảnh đã chọn', 'generatepress_child' ),
			'placeholderDesktop'   => __( 'Chưa có ảnh', 'generatepress_child' ),
			'placeholderMobile'    => __( 'Tùy chọn — để trống dùng ảnh desktop', 'generatepress_child' ),
			'placeholderImage'     => __( 'Chưa chọn ảnh', 'generatepress_child' ),
			'galleryAdd'           => __( 'Thêm ảnh vào gallery', 'generatepress_child' ),
			'galleryRemove'        => __( 'Xóa', 'generatepress_child' ),
			'galleryDrag'          => __( 'Kéo để sắp xếp', 'generatepress_child' ),
			'pickLogo'             => __( 'Chọn logo', 'generatepress_child' ),
			'clearLogo'            => __( 'Xóa logo', 'generatepress_child' ),
			'placeholderLogo'      => __( 'Chưa chọn logo', 'generatepress_child' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'annam_settings_admin_assets' );

/**
 * Một dòng thương hiệu (tab Hệ sinh thái).
 *
 * @param int   $index Index trong form.
 * @param array $row   Dữ liệu đã lưu.
 */
function annam_settings_render_ecosystem_row( $index, array $row ) {
	$prefix = 'annam_ecosystem_items[' . $index . ']';

	$name     = isset( $row['name'] ) ? (string) $row['name'] : '';
	$logo_id  = isset( $row['logo_id'] ) ? absint( $row['logo_id'] ) : 0;
	$url      = isset( $row['url'] ) ? (string) $row['url'] : '';
	$enabled  = ! empty( $row['enabled'] ) ? 1 : 0;
	$order    = isset( $row['order'] ) ? absint( $row['order'] ) : 0;
	$logo_url = $logo_id && wp_attachment_is_image( $logo_id )
		? wp_get_attachment_image_url( $logo_id, 'medium' )
		: '';

	?>
	<div class="annam-ecosystem-card" data-annam-ecosystem-row>
		<div class="annam-ecosystem-card__head">
			<strong><?php echo esc_html__( 'Thương hiệu', 'generatepress_child' ); ?></strong>
			<button type="button" class="button-link-delete annam-remove-ecosystem-row" aria-label="<?php echo esc_attr__( 'Xóa thương hiệu', 'generatepress_child' ); ?>"><?php echo esc_html__( 'Xóa', 'generatepress_child' ); ?></button>
		</div>
		<div class="annam-ecosystem-card__grid">
			<div class="annam-ecosystem-card__field">
				<label for="<?php echo esc_attr( $prefix ); ?>-name"><?php echo esc_html__( 'Tên thương hiệu / đối tác', 'generatepress_child' ); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $prefix ); ?>-name" name="<?php echo esc_attr( $prefix ); ?>[name]" value="<?php echo esc_attr( $name ); ?>" />
			</div>
			<div class="annam-ecosystem-card__field annam-ecosystem-card__field--media">
				<label><?php echo esc_html__( 'Logo', 'generatepress_child' ); ?></label>
				<input type="hidden" name="<?php echo esc_attr( $prefix ); ?>[logo_id]" value="<?php echo esc_attr( (string) $logo_id ); ?>" class="annam-ecosystem-logo-id" />
				<div class="annam-media-preview annam-ecosystem-card__preview">
					<?php if ( $logo_url ) : ?>
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="" width="120" height="80" />
					<?php else : ?>
						<span class="annam-media-placeholder"><?php echo esc_html__( 'Chưa chọn logo', 'generatepress_child' ); ?></span>
					<?php endif; ?>
				</div>
				<p>
					<button type="button" class="button annam-ecosystem-pick-logo"><?php echo esc_html__( 'Chọn logo', 'generatepress_child' ); ?></button>
					<button type="button" class="button annam-ecosystem-clear-logo"><?php echo esc_html__( 'Xóa logo', 'generatepress_child' ); ?></button>
				</p>
			</div>
			<div class="annam-ecosystem-card__field">
				<label for="<?php echo esc_attr( $prefix ); ?>-url"><?php echo esc_html__( 'Link website', 'generatepress_child' ); ?></label>
				<input type="url" class="widefat" id="<?php echo esc_attr( $prefix ); ?>-url" name="<?php echo esc_attr( $prefix ); ?>[url]" value="<?php echo esc_attr( $url ); ?>" placeholder="https://" />
			</div>
			<div class="annam-ecosystem-card__field annam-ecosystem-card__field--inline">
				<label>
					<input type="checkbox" name="<?php echo esc_attr( $prefix ); ?>[enabled]" value="1" <?php checked( 1, $enabled ); ?> />
					<?php echo esc_html__( 'Hiển thị', 'generatepress_child' ); ?>
				</label>
			</div>
			<div class="annam-ecosystem-card__field">
				<label for="<?php echo esc_attr( $prefix ); ?>-order"><?php echo esc_html__( 'Thứ tự', 'generatepress_child' ); ?></label>
				<input type="number" class="small-text" id="<?php echo esc_attr( $prefix ); ?>-order" name="<?php echo esc_attr( $prefix ); ?>[order]" value="<?php echo esc_attr( (string) $order ); ?>" min="0" step="1" />
			</div>
		</div>
	</div>
	<?php
}

/**
 * Nội dung tab Hệ sinh thái.
 */
function annam_settings_render_ecosystem_tab() {
	$rows = get_option( ANNAM_ECOSYSTEM_ITEMS_OPTION, array() );
	if ( ! is_array( $rows ) ) {
		$rows = array();
	}

	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=annam-settings&tab=ecosystem' ) ); ?>" id="annam-ecosystem-items-form">
		<?php wp_nonce_field( 'annam_save_ecosystem_items', 'annam_ecosystem_items_nonce' ); ?>
		<input type="hidden" name="annam_ecosystem_items_action" value="1" />

		<p class="description"><?php echo esc_html__( 'Quản lý logo thương hiệu hiển thị trong section “Hệ sinh thái của An Nam Discovery” (footer). Chỉ mục bật Hiển thị và có logo hợp lệ mới xuất hiện trên website.', 'generatepress_child' ); ?></p>

		<div id="annam-ecosystem-rows" class="annam-ecosystem-rows">
			<?php
			if ( empty( $rows ) ) {
				annam_settings_render_ecosystem_row( 0, array() );
			} else {
				foreach ( array_values( $rows ) as $i => $row ) {
					annam_settings_render_ecosystem_row( (int) $i, is_array( $row ) ? $row : array() );
				}
			}
			?>
		</div>

		<p>
			<button type="button" class="button" id="annam-add-ecosystem-row"><?php echo esc_html__( 'Thêm thương hiệu', 'generatepress_child' ); ?></button>
		</p>
		<p>
			<button type="submit" class="button button-primary"><?php echo esc_html__( 'Lưu thay đổi', 'generatepress_child' ); ?></button>
		</p>
	</form>
	<?php
}

/**
 * Một field ảnh đơn (tab Giới thiệu).
 *
 * @param string $field_key Key trong option (vd about_who_we_are_image).
 * @param string $label     Nhãn hiển thị.
 * @param int    $value_id  Attachment ID.
 */
function annam_settings_render_about_image_field( $field_key, $label, $value_id ) {
	$value_id = absint( $value_id );
	$name     = 'annam_about_settings[' . $field_key . ']';
	$preview  = $value_id && wp_attachment_is_image( $value_id )
		? wp_get_attachment_image_url( $value_id, 'medium' )
		: '';

	?>
	<div class="annam-about-image-field" data-annam-about-image>
		<label class="annam-about-image-field__label"><?php echo esc_html( $label ); ?></label>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value_id ); ?>" class="annam-about-attachment-id" />
		<div class="annam-media-preview annam-about-image-field__preview">
			<?php if ( $preview ) : ?>
				<img src="<?php echo esc_url( $preview ); ?>" alt="" width="120" height="120" />
			<?php else : ?>
				<span class="annam-media-placeholder"><?php echo esc_html__( 'Chưa chọn ảnh', 'generatepress_child' ); ?></span>
			<?php endif; ?>
		</div>
		<p>
			<button type="button" class="button annam-about-pick-image"><?php echo esc_html__( 'Chọn ảnh', 'generatepress_child' ); ?></button>
			<button type="button" class="button annam-about-clear-image"><?php echo esc_html__( 'Xóa ảnh', 'generatepress_child' ); ?></button>
		</p>
	</div>
	<?php
}

/**
 * Nội dung tab Giới thiệu.
 */
function annam_settings_render_about_tab() {
	$settings = get_option( ANNAM_ABOUT_SETTINGS_OPTION, array() );
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	$gallery_ids = isset( $settings['about_gallery_images'] ) && is_array( $settings['about_gallery_images'] )
		? array_map( 'absint', $settings['about_gallery_images'] )
		: array();
	$gallery_ids = array_values( array_filter( $gallery_ids ) );

	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=annam-settings&tab=about' ) ); ?>" id="annam-about-settings-form" class="annam-about-settings-form">
		<?php wp_nonce_field( 'annam_save_about_settings', 'annam_about_settings_nonce' ); ?>
		<input type="hidden" name="annam_about_settings_action" value="1" />

		<h2 class="annam-admin-section-title"><?php echo esc_html__( 'Ảnh section “Chúng tôi là ai?”', 'generatepress_child' ); ?></h2>
		<?php
		annam_settings_render_about_image_field(
			'about_who_we_are_image',
			__( 'Ảnh Chúng tôi là ai?', 'generatepress_child' ),
			isset( $settings['about_who_we_are_image'] ) ? absint( $settings['about_who_we_are_image'] ) : 0
		);
		?>

		<h2 class="annam-admin-section-title"><?php echo esc_html__( 'Dịch vụ chính (6 ảnh)', 'generatepress_child' ); ?></h2>
		<div class="annam-about-service-grid">
			<?php
			for ( $i = 1; $i <= 6; $i++ ) {
				$key = 'about_service_image_' . $i;
				annam_settings_render_about_image_field(
					$key,
					/* translators: %d: service index 1–6 */
					sprintf( __( 'Ảnh dịch vụ chính %d', 'generatepress_child' ), $i ),
					isset( $settings[ $key ] ) ? absint( $settings[ $key ] ) : 0
				);
			}
			?>
		</div>

		<h2 class="annam-admin-section-title"><?php echo esc_html__( 'Hành trình & hình ảnh thực tế (gallery)', 'generatepress_child' ); ?></h2>
		<p class="description"><?php echo esc_html__( 'Thêm nhiều ảnh, kéo thả để đổi thứ tự. Thứ tự lưu sẽ khớp thứ tự hiển thị trên trang Giới thiệu.', 'generatepress_child' ); ?></p>
		<ul id="annam-about-gallery-list" class="annam-gallery-sortable">
			<?php foreach ( $gallery_ids as $gid ) : ?>
				<?php
				if ( ! wp_attachment_is_image( $gid ) ) {
					continue;
				}
				$thumb = wp_get_attachment_image_url( $gid, 'thumbnail' );
				if ( ! $thumb ) {
					continue;
				}
				?>
				<li class="annam-gallery-item" data-id="<?php echo esc_attr( (string) $gid ); ?>">
					<span class="annam-gallery-handle" title="<?php echo esc_attr__( 'Kéo để sắp xếp', 'generatepress_child' ); ?>">⠿</span>
					<span class="annam-gallery-thumb-wrap">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="" width="80" height="80" loading="lazy" decoding="async" />
					</span>
					<input type="hidden" name="annam_about_settings[about_gallery_images][]" value="<?php echo esc_attr( (string) $gid ); ?>" />
					<button type="button" class="button-link annam-gallery-remove"><?php echo esc_html__( 'Xóa', 'generatepress_child' ); ?></button>
				</li>
			<?php endforeach; ?>
		</ul>
		<p>
			<button type="button" class="button" id="annam-gallery-add"><?php echo esc_html__( 'Thêm ảnh vào gallery', 'generatepress_child' ); ?></button>
		</p>

		<h2 class="annam-admin-section-title"><?php echo esc_html__( 'Nền section CTA cuối trang', 'generatepress_child' ); ?></h2>
		<p class="description"><?php echo esc_html__( 'Section: “Bạn đang cần tư vấn một hành trình phù hợp?”', 'generatepress_child' ); ?></p>
		<?php
		annam_settings_render_about_image_field(
			'about_cta_background_image',
			__( 'Ảnh nền CTA cuối trang', 'generatepress_child' ),
			isset( $settings['about_cta_background_image'] ) ? absint( $settings['about_cta_background_image'] ) : 0
		);
		?>

		<p>
			<button type="submit" class="button button-primary"><?php echo esc_html__( 'Lưu thay đổi', 'generatepress_child' ); ?></button>
		</p>
	</form>
	<?php
}

/**
 * Trang cài đặt chính (tab).
 */
function annam_settings_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tab = annam_settings_get_current_tab();

	if ( isset( $_GET['annam_saved'] ) && '1' === $_GET['annam_saved'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Đã lưu cài đặt.', 'generatepress_child' ) . '</p></div>';
	}

	$base = admin_url( 'admin.php?page=annam-settings' );
	?>
	<div class="wrap annam-admin-wrap">
		<h1><?php echo esc_html__( 'An Nam Settings', 'generatepress_child' ); ?></h1>

		<h2 class="nav-tab-wrapper annam-admin-tabs">
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'slider', $base ) ); ?>" class="nav-tab<?php echo 'slider' === $tab ? ' nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Slider trang chủ', 'generatepress_child' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'about', $base ) ); ?>" class="nav-tab<?php echo 'about' === $tab ? ' nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Giới thiệu', 'generatepress_child' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'ecosystem', $base ) ); ?>" class="nav-tab<?php echo 'ecosystem' === $tab ? ' nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Hệ sinh thái', 'generatepress_child' ); ?></a>
		</h2>

		<div id="annam-tab-panel-slider" class="annam-admin-tab-panel"<?php echo 'slider' === $tab ? '' : ' hidden'; ?>>
			<p class="description"><?php echo esc_html__( 'Thêm ảnh và nội dung cho từng slide. Chỉ slide bật hiển thị và có ảnh desktop hợp lệ mới xuất hiện trên trang chủ.', 'generatepress_child' ); ?></p>
			<p class="description"><?php echo esc_html__( 'Để hero slider tự chuyển slide, cần ít nhất 2 slide đang bật và có ảnh desktop.', 'generatepress_child' ); ?></p>

			<form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'slider', $base ) ); ?>" id="annam-home-sliders-form">
				<?php wp_nonce_field( 'annam_save_home_sliders', 'annam_home_sliders_nonce' ); ?>
				<input type="hidden" name="annam_home_sliders_action" value="1" />

				<?php
				$rows = get_option( ANNAM_HOME_SLIDERS_OPTION, array() );
				if ( ! is_array( $rows ) ) {
					$rows = array();
				}
				?>
				<div id="annam-home-sliders-rows" class="annam-home-sliders-rows">
					<?php
					if ( empty( $rows ) ) {
						annam_settings_render_slider_row( 0, array() );
					} else {
						foreach ( array_values( $rows ) as $i => $row ) {
							annam_settings_render_slider_row( (int) $i, is_array( $row ) ? $row : array() );
						}
					}
					?>
				</div>

				<p>
					<button type="button" class="button" id="annam-add-slide"><?php echo esc_html__( 'Thêm slide', 'generatepress_child' ); ?></button>
					<button type="button" class="button" id="annam-add-slides-bulk"><?php echo esc_html__( 'Thêm nhiều ảnh', 'generatepress_child' ); ?></button>
				</p>
				<p class="description"><?php echo esc_html__( 'Trong thư viện ảnh, nếu cần chọn nhiều ảnh liên tiếp hãy giữ Ctrl/Cmd hoặc Shift khi chọn.', 'generatepress_child' ); ?></p>

				<p>
					<button type="submit" class="button button-primary"><?php echo esc_html__( 'Lưu thay đổi', 'generatepress_child' ); ?></button>
				</p>
			</form>

			<template id="annam-home-slider-row-template">
				<?php annam_settings_render_slider_row( '__INDEX__', array( 'enabled' => 1 ) ); ?>
			</template>
		</div>

		<div id="annam-tab-panel-about" class="annam-admin-tab-panel"<?php echo 'about' === $tab ? '' : ' hidden'; ?>>
			<?php annam_settings_render_about_tab(); ?>
		</div>

		<div id="annam-tab-panel-ecosystem" class="annam-admin-tab-panel"<?php echo 'ecosystem' === $tab ? '' : ' hidden'; ?>>
			<?php annam_settings_render_ecosystem_tab(); ?>
		</div>
	</div>
	<?php
}

/**
 * Một dòng cài đặt slide (admin).
 *
 * @param int|string $index Chỉ số.
 * @param array      $row   Dữ liệu đã lưu.
 */
function annam_settings_render_slider_row( $index, array $row ) {
	$prefix = 'annam_home_sliders[' . $index . ']';

	$desktop_id = isset( $row['desktop_id'] ) ? absint( $row['desktop_id'] ) : 0;
	$mobile_id  = isset( $row['mobile_id'] ) ? absint( $row['mobile_id'] ) : 0;

	$desk_url = $desktop_id && wp_attachment_is_image( $desktop_id )
		? wp_get_attachment_image_url( $desktop_id, 'medium' )
		: '';
	$mob_url  = $mobile_id && wp_attachment_is_image( $mobile_id )
		? wp_get_attachment_image_url( $mobile_id, 'medium' )
		: '';

	$title       = isset( $row['title'] ) ? (string) $row['title'] : '';
	$description = isset( $row['description'] ) ? (string) $row['description'] : '';
	$button_text = isset( $row['button_text'] ) ? (string) $row['button_text'] : '';
	$button_url  = isset( $row['button_url'] ) ? (string) $row['button_url'] : '';
	$enabled     = ! empty( $row['enabled'] ) ? 1 : 0;
	$order       = isset( $row['order'] ) ? absint( $row['order'] ) : 0;

	?>
	<div class="annam-slide-card" data-annam-slide-row>
		<div class="annam-slide-card__head">
			<strong><?php echo esc_html__( 'Slide', 'generatepress_child' ); ?></strong>
			<button type="button" class="button-link-delete annam-remove-slide" aria-label="<?php echo esc_attr__( 'Xóa slide', 'generatepress_child' ); ?>"><?php echo esc_html__( 'Xóa slide', 'generatepress_child' ); ?></button>
		</div>

		<div class="annam-slide-card__grid">
			<div class="annam-slide-card__field annam-slide-card__field--media">
				<label><?php echo esc_html__( 'Ảnh desktop', 'generatepress_child' ); ?></label>
				<input type="hidden" name="<?php echo esc_attr( $prefix ); ?>[desktop_id]" value="<?php echo esc_attr( (string) $desktop_id ); ?>" class="annam-desktop-id" />
				<div class="annam-media-preview">
					<?php if ( $desk_url ) : ?>
						<img src="<?php echo esc_url( $desk_url ); ?>" alt="" class="annam-preview-desktop" width="120" height="120" />
					<?php else : ?>
						<span class="annam-media-placeholder"><?php echo esc_html__( 'Chưa có ảnh', 'generatepress_child' ); ?></span>
					<?php endif; ?>
				</div>
				<p>
					<button type="button" class="button annam-pick-desktop"><?php echo esc_html__( 'Chọn ảnh', 'generatepress_child' ); ?></button>
					<button type="button" class="button annam-clear-desktop"><?php echo esc_html__( 'Xóa ảnh', 'generatepress_child' ); ?></button>
				</p>
			</div>

			<div class="annam-slide-card__field annam-slide-card__field--media">
				<label><?php echo esc_html__( 'Ảnh mobile', 'generatepress_child' ); ?></label>
				<input type="hidden" name="<?php echo esc_attr( $prefix ); ?>[mobile_id]" value="<?php echo esc_attr( (string) $mobile_id ); ?>" class="annam-mobile-id" />
				<div class="annam-media-preview">
					<?php if ( $mob_url ) : ?>
						<img src="<?php echo esc_url( $mob_url ); ?>" alt="" class="annam-preview-mobile" width="120" height="120" />
					<?php else : ?>
						<span class="annam-media-placeholder"><?php echo esc_html__( 'Tùy chọn — để trống dùng ảnh desktop', 'generatepress_child' ); ?></span>
					<?php endif; ?>
				</div>
				<p>
					<button type="button" class="button annam-pick-mobile"><?php echo esc_html__( 'Chọn ảnh', 'generatepress_child' ); ?></button>
					<button type="button" class="button annam-clear-mobile"><?php echo esc_html__( 'Xóa ảnh', 'generatepress_child' ); ?></button>
				</p>
			</div>

			<div class="annam-slide-card__field">
				<label for="<?php echo esc_attr( $prefix ); ?>-title"><?php echo esc_html__( 'Tiêu đề', 'generatepress_child' ); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $prefix ); ?>-title" name="<?php echo esc_attr( $prefix ); ?>[title]" value="<?php echo esc_attr( $title ); ?>" />
			</div>

			<div class="annam-slide-card__field">
				<label for="<?php echo esc_attr( $prefix ); ?>-desc"><?php echo esc_html__( 'Mô tả', 'generatepress_child' ); ?></label>
				<textarea class="widefat" rows="3" id="<?php echo esc_attr( $prefix ); ?>-desc" name="<?php echo esc_attr( $prefix ); ?>[description]"><?php echo esc_textarea( $description ); ?></textarea>
			</div>

			<div class="annam-slide-card__field">
				<label for="<?php echo esc_attr( $prefix ); ?>-btn"><?php echo esc_html__( 'Text nút', 'generatepress_child' ); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $prefix ); ?>-btn" name="<?php echo esc_attr( $prefix ); ?>[button_text]" value="<?php echo esc_attr( $button_text ); ?>" />
			</div>

			<div class="annam-slide-card__field">
				<label for="<?php echo esc_attr( $prefix ); ?>-url"><?php echo esc_html__( 'Link nút', 'generatepress_child' ); ?></label>
				<input type="url" class="widefat" id="<?php echo esc_attr( $prefix ); ?>-url" name="<?php echo esc_attr( $prefix ); ?>[button_url]" value="<?php echo esc_attr( $button_url ); ?>" placeholder="https://" />
			</div>

			<div class="annam-slide-card__field annam-slide-card__field--inline">
				<label>
					<input type="checkbox" name="<?php echo esc_attr( $prefix ); ?>[enabled]" value="1" <?php checked( 1, $enabled ); ?> />
					<?php echo esc_html__( 'Bật hiển thị', 'generatepress_child' ); ?>
				</label>
			</div>

			<div class="annam-slide-card__field">
				<label for="<?php echo esc_attr( $prefix ); ?>-order"><?php echo esc_html__( 'Thứ tự', 'generatepress_child' ); ?></label>
				<input type="number" class="small-text" id="<?php echo esc_attr( $prefix ); ?>-order" name="<?php echo esc_attr( $prefix ); ?>[order]" value="<?php echo esc_attr( (string) $order ); ?>" min="0" step="1" />
			</div>
		</div>
	</div>
	<?php
}
