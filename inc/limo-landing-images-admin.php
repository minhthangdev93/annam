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
function annam_limo_landing_get_image_slots( $route = '' ) {
	$route = function_exists( 'annam_limo_landing_storage_route' )
		? annam_limo_landing_storage_route( $route )
		: 'sapa';
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
			'label'           => 'halong' === $route
				? __( 'Trên đường Hà Nội ⇄ Hạ Long', 'generatepress_child' )
				: ( 'ninhbinh' === $route
					? __( 'Trên đường Hà Nội ⇄ Ninh Bình', 'generatepress_child' )
					: __( 'Trên đường Hà Nội ⇄ Sapa', 'generatepress_child' ) ),
			'section'         => 'gallery',
			'placement'       => __( 'Gallery — ảnh phụ (mobile)', 'generatepress_child' ),
			'recommended'     => '800 × 600 px',
			'ratio'           => '4 : 3',
			'formats'         => 'JPG, WebP',
			'wp_size'         => 'large',
			'fallback'        => 'https://images.unsplash.com/photo-1485291571150-772bcfc10da5?auto=format&fit=crop&w=800&q=80',
			'default_caption' => 'halong' === $route
				? 'Trên đường Hà Nội ⇄ Hạ Long'
				: ( 'ninhbinh' === $route ? 'Trên đường Hà Nội ⇄ Ninh Bình' : 'Trên đường Hà Nội ⇄ Sapa' ),
		),
	);

	/* Đội ngũ — chỉ HL / Ninh Bình (không caption). */
	if ( in_array( $route, array( 'halong', 'ninhbinh' ), true ) ) {
		$team_fallbacks = array(
			'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=800&q=80',
			'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=800&q=80',
			'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=800&q=80',
			'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=800&q=80',
			'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=800&q=80',
			'https://images.unsplash.com/photo-1607746882042-944635dfe10e?auto=format&fit=crop&w=800&q=80',
		);
		for ( $i = 1; $i <= 6; $i++ ) {
			$slots[ 'team-' . $i ] = array(
				'label'       => sprintf(
					/* translators: %d: team photo number 1–6 */
					__( 'Đội ngũ — ảnh %d', 'generatepress_child' ),
					$i
				),
				'section'     => 'team',
				'placement'   => __( 'Khối Đội ngũ An Nam Discovery', 'generatepress_child' ),
				'recommended' => '800 × 800 px',
				'ratio'       => '1 : 1',
				'formats'     => 'JPG, WebP',
				'wp_size'     => 'full',
				'fallback'    => $team_fallbacks[ $i - 1 ],
			);
		}
	}

	return apply_filters( 'annam_limo_landing_image_slots', $slots );
}

/**
 * @param string $key Slot key.
 * @return string
 */
function annam_limo_landing_get_image_caption( $key, $route = '' ) {
	$key   = sanitize_key( $key );
	$route = function_exists( 'annam_limo_landing_storage_route' )
		? annam_limo_landing_storage_route( $route )
		: 'sapa';
	$slots = annam_limo_landing_get_image_slots( $route );
	if ( ! isset( $slots[ $key ] ) ) {
		return '';
	}

	$option = function_exists( 'annam_limo_landing_option_name' )
		? annam_limo_landing_option_name( 'annam_limo_landing_captions', $route )
		: ANNAM_LIMO_LANDING_CAPTIONS_OPTION;
	$saved = get_option( $option, array() );
	if ( is_array( $saved ) && isset( $saved[ $key ] ) && is_string( $saved[ $key ] ) && '' !== trim( $saved[ $key ] ) ) {
		return sanitize_text_field( $saved[ $key ] );
	}

	return isset( $slots[ $key ]['default_caption'] ) ? (string) $slots[ $key ]['default_caption'] : '';
}

/**
 * @param string $key Slot key.
 * @return int
 */
function annam_limo_landing_get_image_attachment_id( $key, $route = '' ) {
	$key = sanitize_key( $key );
	if ( '' === $key ) {
		return 0;
	}
	$route = function_exists( 'annam_limo_landing_storage_route' )
		? annam_limo_landing_storage_route( $route )
		: 'sapa';
	$slots = annam_limo_landing_get_image_slots( $route );
	if ( ! isset( $slots[ $key ] ) ) {
		return 0;
	}
	$option = function_exists( 'annam_limo_landing_option_name' )
		? annam_limo_landing_option_name( 'annam_limo_landing_images', $route )
		: ANNAM_LIMO_LANDING_IMAGES_OPTION;
	$saved = get_option( $option, array() );
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
function annam_limo_landing_image_url( $key, $route = '' ) {
	$key   = sanitize_key( $key );
	$route = function_exists( 'annam_limo_landing_storage_route' )
		? annam_limo_landing_storage_route( $route )
		: 'sapa';
	$slots = annam_limo_landing_get_image_slots( $route );
	if ( ! isset( $slots[ $key ] ) ) {
		return '';
	}
	$aid = annam_limo_landing_get_image_attachment_id( $key, $route );
	if ( $aid > 0 ) {
		$size = ( 'hero-banner' === $key ) ? 'full' : 'large';
		$url  = wp_get_attachment_image_url( $aid, $size );
		return is_string( $url ) ? $url : '';
	}
	return isset( $slots[ $key ]['fallback'] ) ? (string) $slots[ $key ]['fallback'] : '';
}

function annam_limo_landing_images_register_menu() {
	add_submenu_page(
		'annam-settings',
		__( 'Landing Limousine (vé ghế)', 'generatepress_child' ),
		__( 'Landing Limousine (vé ghế)', 'generatepress_child' ),
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

	$route = function_exists( 'annam_limo_landing_admin_route' ) ? annam_limo_landing_admin_route() : 'sapa';
	$input = isset( $_POST['annam_limo_landing_images'] ) && is_array( $_POST['annam_limo_landing_images'] )
		? wp_unslash( $_POST['annam_limo_landing_images'] )
		: array();

	$clean = array();
	foreach ( annam_limo_landing_get_image_slots( $route ) as $key => $slot ) {
		if ( 'team' === ( $slot['section'] ?? '' ) ) {
			$clean[ $key ] = 0;
			continue;
		}
		$clean[ $key ] = isset( $input[ $key ] ) ? absint( $input[ $key ] ) : 0;
	}

	/* Đội ngũ (HL/NB): chọn nhiều ảnh → map vào team-1…team-6 theo thứ tự. */
	if ( in_array( $route, array( 'halong', 'ninhbinh' ), true ) ) {
		$team_input = isset( $_POST['annam_limo_landing_team'] ) && is_array( $_POST['annam_limo_landing_team'] )
			? wp_unslash( $_POST['annam_limo_landing_team'] )
			: array();
		$team_ids = array();
		foreach ( $team_input as $raw_id ) {
			$id = absint( $raw_id );
			if ( $id > 0 && wp_attachment_is_image( $id ) && ! in_array( $id, $team_ids, true ) ) {
				$team_ids[] = $id;
			}
			if ( count( $team_ids ) >= 6 ) {
				break;
			}
		}
		for ( $i = 1; $i <= 6; $i++ ) {
			$slot_key           = 'team-' . $i;
			$clean[ $slot_key ] = isset( $team_ids[ $i - 1 ] ) ? (int) $team_ids[ $i - 1 ] : 0;
		}
	}

	$images_option = function_exists( 'annam_limo_landing_option_name' )
		? annam_limo_landing_option_name( 'annam_limo_landing_images', $route )
		: ANNAM_LIMO_LANDING_IMAGES_OPTION;
	update_option( $images_option, $clean, false );

	$cap_input = isset( $_POST['annam_limo_landing_captions'] ) && is_array( $_POST['annam_limo_landing_captions'] )
		? wp_unslash( $_POST['annam_limo_landing_captions'] )
		: array();
	$cap_clean = array();
	foreach ( annam_limo_landing_get_image_slots( $route ) as $key => $slot ) {
		if ( 'gallery' !== ( $slot['section'] ?? '' ) ) {
			continue;
		}
		$raw = isset( $cap_input[ $key ] ) ? (string) $cap_input[ $key ] : '';
		$cap_clean[ $key ] = sanitize_text_field( $raw );
	}
	$captions_option = function_exists( 'annam_limo_landing_option_name' )
		? annam_limo_landing_option_name( 'annam_limo_landing_captions', $route )
		: ANNAM_LIMO_LANDING_CAPTIONS_OPTION;
	update_option( $captions_option, $cap_clean, false );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'        => 'annam-limo-landing-images',
				'route'       => $route,
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
	wp_enqueue_script( 'jquery-ui-sortable' );

	$css_path = $dir . '/assets/css/annam-admin.css';
	$js_path  = $dir . '/assets/js/annam-admin.js';
	$team_js  = $dir . '/assets/js/limo-landing-team-admin.js';

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
	if ( file_exists( $team_js ) ) {
		wp_enqueue_script(
			'annam-limo-team-admin',
			$uri . '/assets/js/limo-landing-team-admin.js',
			array( 'jquery', 'jquery-ui-sortable', 'media-upload', 'media-views' ),
			(string) filemtime( $team_js ),
			true
		);
		wp_localize_script(
			'annam-limo-team-admin',
			'annamLimoTeamAdminL10n',
			array(
				'max'         => 6,
				'pickTitle'   => __( 'Chọn ảnh đội ngũ (tối đa 6)', 'generatepress_child' ),
				'pickButton'  => __( 'Dùng ảnh đã chọn', 'generatepress_child' ),
				'remove'      => __( 'Xóa', 'generatepress_child' ),
				'drag'        => __( 'Kéo để đổi thứ tự', 'generatepress_child' ),
				'countTpl'    => __( 'Đang có %1$d/%2$d ảnh.', 'generatepress_child' ),
				'limitAlert'  => __( 'Chỉ chọn tối đa 6 ảnh đội ngũ.', 'generatepress_child' ),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'annam_limo_landing_images_admin_assets' );

/**
 * Danh sách ID ảnh đội ngũ theo thứ tự (tối đa 6).
 *
 * @param string $route Route.
 * @return int[]
 */
function annam_limo_landing_get_team_attachment_ids( $route = '' ) {
	$ids = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$aid = function_exists( 'annam_limo_landing_get_image_attachment_id' )
			? annam_limo_landing_get_image_attachment_id( 'team-' . $i, $route )
			: 0;
		if ( $aid > 0 ) {
			$ids[] = $aid;
		}
	}
	return $ids;
}

/**
 * Render UI chọn nhiều ảnh đội ngũ + kéo sắp xếp.
 *
 * @param string $route Route.
 * @param array  $saved Saved image options.
 */
function annam_limo_landing_images_render_team_gallery( $route, array $saved ) {
	if ( ! in_array( $route, array( 'halong', 'ninhbinh' ), true ) ) {
		return;
	}
	$ids = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$key = 'team-' . $i;
		$id  = isset( $saved[ $key ] ) ? absint( $saved[ $key ] ) : 0;
		if ( $id > 0 && wp_attachment_is_image( $id ) ) {
			$ids[] = $id;
		}
	}
	?>
	<div class="annam-limo-team-admin" data-annam-limo-team-admin data-max="6">
		<h2><?php esc_html_e( 'Đội ngũ An Nam Discovery', 'generatepress_child' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Chọn tối đa 6 ảnh một lần. Kéo thả để đổi thứ tự hiển thị trên landing. Không cần caption.', 'generatepress_child' ); ?></p>
		<p class="description"><?php esc_html_e( 'Khuyến nghị: 800 × 800 px (JPG/WebP).', 'generatepress_child' ); ?></p>
		<ul id="annam-limo-team-list" class="annam-gallery-sortable annam-limo-team-admin__list">
			<?php foreach ( $ids as $id ) : ?>
				<?php
				$thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
				if ( ! $thumb ) {
					$thumb = wp_get_attachment_image_url( $id, 'medium' );
				}
				?>
				<li class="annam-gallery-item annam-limo-team-admin__item" data-id="<?php echo esc_attr( (string) $id ); ?>">
					<span class="annam-gallery-handle" title="<?php esc_attr_e( 'Kéo để đổi thứ tự', 'generatepress_child' ); ?>">⠿</span>
					<span class="annam-gallery-thumb-wrap">
						<?php if ( $thumb ) : ?>
							<img src="<?php echo esc_url( $thumb ); ?>" alt="" width="80" height="80" loading="lazy" decoding="async" />
						<?php endif; ?>
					</span>
					<input type="hidden" name="annam_limo_landing_team[]" value="<?php echo esc_attr( (string) $id ); ?>" />
					<button type="button" class="button-link annam-gallery-remove annam-limo-team-admin__remove"><?php esc_html_e( 'Xóa', 'generatepress_child' ); ?></button>
				</li>
			<?php endforeach; ?>
		</ul>
		<p>
			<button type="button" class="button button-primary" id="annam-limo-team-add"><?php esc_html_e( 'Chọn / thêm ảnh đội ngũ', 'generatepress_child' ); ?></button>
			<button type="button" class="button" id="annam-limo-team-clear" <?php disabled( empty( $ids ) ); ?>><?php esc_html_e( 'Xóa hết', 'generatepress_child' ); ?></button>
			<span class="description annam-limo-team-admin__count"></span>
		</p>
	</div>
	<hr />
	<?php
}

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

	$route = function_exists( 'annam_limo_landing_admin_route' ) ? annam_limo_landing_admin_route() : 'sapa';
	$images_option = function_exists( 'annam_limo_landing_option_name' )
		? annam_limo_landing_option_name( 'annam_limo_landing_images', $route )
		: ANNAM_LIMO_LANDING_IMAGES_OPTION;
	$captions_option = function_exists( 'annam_limo_landing_option_name' )
		? annam_limo_landing_option_name( 'annam_limo_landing_captions', $route )
		: ANNAM_LIMO_LANDING_CAPTIONS_OPTION;
	$saved = get_option( $images_option, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	$captions = get_option( $captions_option, array() );
	if ( ! is_array( $captions ) ) {
		$captions = array();
	}
	$slots    = annam_limo_landing_get_image_slots( $route );
	$settings = function_exists( 'annam_limo_landing_get_settings' ) ? annam_limo_landing_get_settings( $route ) : array();
	$emails   = isset( $settings['lead_emails'] ) ? (string) $settings['lead_emails'] : '';
	$youtube_url = isset( $settings['youtube_url'] ) ? (string) $settings['youtube_url'] : '';
	if ( '' === $youtube_url && 'sapa' === $route && function_exists( 'annam_limo_landing_get_youtube_url' ) ) {
		$youtube_url = annam_limo_landing_get_youtube_url( 'sapa' );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Landing Limousine (vé ghế)', 'generatepress_child' ); ?></h1>
		<h2 class="nav-tab-wrapper">
			<?php
			$route_tab_labels = array(
				'sapa'     => __( 'Hà Nội ⇄ Sapa', 'generatepress_child' ),
				'halong'   => __( 'Hà Nội ⇄ Hạ Long', 'generatepress_child' ),
				'ninhbinh' => __( 'Hà Nội ⇄ Ninh Bình', 'generatepress_child' ),
			);
			foreach ( annam_limo_landing_get_route_profiles() as $route_key => $route_profile ) :
				$tab_label = isset( $route_tab_labels[ $route_key ] ) ? $route_tab_labels[ $route_key ] : (string) $route_key;
				?>
				<a class="nav-tab<?php echo $route === $route_key ? ' nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=annam-limo-landing-images&route=' . rawurlencode( (string) $route_key ) ) ); ?>">
					<?php echo esc_html( $tab_label ); ?>
				</a>
			<?php endforeach; ?>
		</h2>

		<?php if ( ! empty( $_GET['annam_saved'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã lưu.', 'generatepress_child' ); ?></p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Email & video YouTube', 'generatepress_child' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=annam-limo-landing-images&route=' . rawurlencode( $route ) ) ); ?>">
			<?php wp_nonce_field( 'annam_save_limo_landing_settings', 'annam_limo_landing_settings_nonce' ); ?>
			<input type="hidden" name="annam_limo_landing_settings_action" value="1" />
			<input type="hidden" name="annam_limo_route" value="<?php echo esc_attr( $route ); ?>" />
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
		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=annam-limo-landing-images&route=' . rawurlencode( $route ) ) ); ?>">
			<?php wp_nonce_field( 'annam_save_limo_landing_images', 'annam_limo_landing_images_nonce' ); ?>
			<input type="hidden" name="annam_limo_landing_images_action" value="1" />
			<input type="hidden" name="annam_limo_route" value="<?php echo esc_attr( $route ); ?>" />
			<?php
			annam_limo_landing_images_render_team_gallery( $route, $saved );
			foreach ( $slots as $key => $slot ) {
				if ( 'team' === ( $slot['section'] ?? '' ) ) {
					continue;
				}
				$cap = isset( $captions[ $key ] ) ? (string) $captions[ $key ] : '';
				annam_limo_landing_images_render_field( $key, $slot, isset( $saved[ $key ] ) ? (int) $saved[ $key ] : 0, $cap );
			}
			submit_button( __( 'Lưu ảnh & mô tả', 'generatepress_child' ) );
			?>
		</form>
	</div>
	<?php
}
