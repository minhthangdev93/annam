<?php
/**
 * Landing vé Limousine HN–Sapa: enqueue, form hooks.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/limo-landing-route-profiles.php';
require_once get_stylesheet_directory() . '/inc/limo-landing-config.php';
require_once get_stylesheet_directory() . '/inc/limo-landing-config-halong.php';
require_once get_stylesheet_directory() . '/inc/limo-landing-config-ninhbinh.php';
require_once get_stylesheet_directory() . '/inc/limo-landing-routes.php';
require_once get_stylesheet_directory() . '/inc/limo-landing-booking.php';

const ANNAM_LIMO_LANDING_RATE_MAX     = 8;
const ANNAM_LIMO_LANDING_RATE_MINUTES = 10;

/**
 * @return bool
 */
function annam_limo_landing_is_template() {
	return '' !== annam_limo_landing_current_route();
}

/**
 * @param int $post_id Post ID.
 * @return bool
 */
function annam_limo_landing_page_has_editor_content( $post_id = 0 ) {
	if ( $post_id <= 0 ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id ) {
		return false;
	}
	$raw = get_post_field( 'post_content', $post_id );
	return is_string( $raw ) && '' !== trim( wp_strip_all_tags( $raw ) );
}

/**
 * @param int $post_id Post ID.
 * @return string
 */
function annam_limo_landing_get_page_content_html( $post_id = 0 ) {
	if ( $post_id <= 0 ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id || ! annam_limo_landing_page_has_editor_content( $post_id ) ) {
		return '';
	}
	$raw = get_post_field( 'post_content', $post_id );
	if ( ! is_string( $raw ) ) {
		return '';
	}
	return (string) apply_filters( 'the_content', $raw );
}

/**
 * @param int $page_id Page ID.
 * @return array<string,mixed>
 */
function annam_limo_landing_get_config( $page_id = 0 ) {
	if ( $page_id <= 0 ) {
		$page_id = get_queried_object_id();
	}
	return annam_limo_landing_get_default_config( (int) $page_id );
}

/**
 * @return array{type:string,message:string}|null
 */
function annam_limo_landing_get_notice() {
	if ( ! isset( $_GET['annam_limo'] ) ) {
		return null;
	}
	$code = sanitize_key( wp_unslash( $_GET['annam_limo'] ) );
	if ( 'sent' === $code ) {
		$config = annam_limo_landing_get_config();
		$msg    = isset( $config['form']['success_message'] ) ? $config['form']['success_message'] : '';
		return array(
			'type'    => 'success',
			'message' => $msg,
		);
	}
	if ( 'error' === $code ) {
		return array(
			'type'    => 'error',
			'message' => __( 'Không gửi được yêu cầu. Vui lòng gọi hotline hoặc nhắn Zalo để được hỗ trợ.', 'generatepress_child' ),
		);
	}
	return null;
}

/**
 * @param string               $slot_key Slot key.
 * @param array<string,string> $attrs    Img attrs.
 * @param string               $route    Route key (halong/ninhbinh/sapa); empty = detect.
 * @return string
 */
function annam_limo_landing_print_image( $slot_key, array $attrs = array(), $route = '' ) {
	$slot_key = sanitize_key( (string) $slot_key );
	if ( '' === $slot_key || ! function_exists( 'annam_limo_landing_get_image_attachment_id' ) ) {
		return '';
	}

	$route = function_exists( 'annam_limo_landing_storage_route' )
		? annam_limo_landing_storage_route( $route )
		: sanitize_key( (string) $route );

	$attachment_id = annam_limo_landing_get_image_attachment_id( $slot_key, $route );
	if ( $attachment_id <= 0 ) {
		$url = function_exists( 'annam_limo_landing_image_url' ) ? annam_limo_landing_image_url( $slot_key, $route ) : '';
		if ( '' === $url ) {
			return '';
		}
		$alt     = isset( $attrs['alt'] ) ? (string) $attrs['alt'] : '';
		$loading = isset( $attrs['loading'] ) ? (string) $attrs['loading'] : 'lazy';
		unset( $attrs['alt'], $attrs['loading'] );
		$html_attrs = '';
		foreach ( $attrs as $name => $value ) {
			$html_attrs .= sprintf( ' %s="%s"', esc_attr( (string) $name ), esc_attr( (string) $value ) );
		}
		return sprintf(
			'<img src="%s" alt="%s" loading="%s" decoding="async"%s />',
			esc_url( $url ),
			esc_attr( $alt ),
			esc_attr( $loading ),
			$html_attrs
		);
	}

	$size = 'large';
	if ( function_exists( 'annam_limo_landing_get_image_slots' ) ) {
		$slots = annam_limo_landing_get_image_slots( $route );
		if ( isset( $slots[ $slot_key ]['wp_size'] ) ) {
			$size = (string) $slots[ $slot_key ]['wp_size'];
		}
	}

	/*
	 * Luôn 1 URL + ?ver= (id-mtime-save) — tránh srcset mờ và CDN giữ file trùng tên.
	 */
	$url = '';
	$w   = 0;
	$h   = 0;
	if ( 'hero-banner' === $slot_key && function_exists( 'wp_get_original_image_url' ) ) {
		$orig = wp_get_original_image_url( $attachment_id );
		if ( is_string( $orig ) && '' !== $orig ) {
			$url = $orig;
		}
	}
	if ( '' === $url ) {
		$prefer = $size ? $size : 'large';
		if ( 'hero-banner' === $slot_key ) {
			$prefer = 'full';
		}
		$src = wp_get_attachment_image_src( $attachment_id, $prefer );
		if ( ( ! is_array( $src ) || empty( $src[0] ) ) && 'large' === $prefer ) {
			$src = wp_get_attachment_image_src( $attachment_id, 'full' );
		}
		if ( 0 === strpos( $slot_key, 'team-' ) && is_array( $src ) && ! empty( $src[1] ) && (int) $src[1] < 1200 ) {
			$full = wp_get_attachment_image_src( $attachment_id, 'full' );
			if ( is_array( $full ) && ! empty( $full[0] ) ) {
				$src = $full;
			}
		}
		$url = ( is_array( $src ) && ! empty( $src[0] ) ) ? (string) $src[0] : '';
		$w   = isset( $src[1] ) ? (int) $src[1] : 0;
		$h   = isset( $src[2] ) ? (int) $src[2] : 0;
	}
	if ( '' === $url ) {
		return '';
	}
	if ( function_exists( 'annam_limo_landing_cache_bust_url' ) ) {
		$url = annam_limo_landing_cache_bust_url( $url, $attachment_id, $route );
	}

	$alt     = isset( $attrs['alt'] ) ? (string) $attrs['alt'] : (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
	$class   = isset( $attrs['class'] ) ? (string) $attrs['class'] : '';
	$loading = isset( $attrs['loading'] ) ? (string) $attrs['loading'] : ( 'hero-banner' === $slot_key ? 'eager' : 'lazy' );
	if ( 'hero-banner' === $slot_key ) {
		$w = $w ? $w : 2048;
		$h = $h ? $h : 751;
	}
	$fetch = '';
	if ( 'hero-banner' === $slot_key ) {
		$fetch_val = isset( $attrs['fetchpriority'] ) ? (string) $attrs['fetchpriority'] : 'high';
		$fetch     = ' fetchpriority="' . esc_attr( $fetch_val ) . '"';
	}
	$dim = ( $w > 0 && $h > 0 ) ? sprintf( ' width="%d" height="%d"', $w, $h ) : '';
	return sprintf(
		'<img src="%s" alt="%s" class="%s" loading="%s" decoding="async"%s%s />',
		esc_url( $url ),
		esc_attr( $alt ),
		esc_attr( $class ),
		esc_attr( $loading ),
		$fetch,
		$dim
	);
}

/**
 * Fallback POST (no-JS).
 */
function annam_limo_landing_handle_form() {
	if ( ! annam_limo_landing_is_template() ) {
		return;
	}
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : '' ) ) {
		return;
	}
	if ( empty( $_POST['annam_limo_submit'] ) ) {
		return;
	}

	$redirect = get_permalink();
	if ( ! $redirect ) {
		return;
	}

	$input                         = wp_unslash( $_POST );
	$input['annam_limo_page_url']  = $redirect;
	$result                        = annam_limo_landing_process_booking( $input );

	if ( ! empty( $result['success'] ) ) {
		wp_safe_redirect( add_query_arg( 'annam_limo', 'sent', $redirect ) );
		exit;
	}

	wp_safe_redirect( add_query_arg( 'annam_limo', 'error', $redirect ) );
	exit;
}
add_action( 'template_redirect', 'annam_limo_landing_handle_form', 2 );

/**
 * @param string $layout Layout.
 * @return string
 */
function annam_limo_landing_sidebar_layout( $layout ) {
	return annam_limo_landing_is_template() ? 'no-sidebar' : $layout;
}
add_filter( 'generate_sidebar_layout', 'annam_limo_landing_sidebar_layout', 20 );

/**
 * @param bool $show Show entry header.
 * @return bool
 */
function annam_limo_landing_hide_entry_header( $show ) {
	return annam_limo_landing_is_template() ? false : $show;
}
add_filter( 'generate_show_entry_header', 'annam_limo_landing_hide_entry_header', 12 );

/**
 * @param string[] $classes Body classes.
 * @return string[]
 */
function annam_limo_landing_body_class( $classes ) {
	if ( ! annam_limo_landing_is_template() ) {
		return $classes;
	}
	$classes[] = 'annam-limo-landing-page';
	$route     = annam_limo_landing_current_route();
	if ( '' !== $route ) {
		$classes[] = 'annam-limo-landing--' . sanitize_html_class( $route );
	}
	// Brand chrome CSS targets --halong; Ninh Bình reuses the same skin.
	if ( 'ninhbinh' === $route ) {
		$classes[] = 'annam-limo-landing--halong';
	}
	return $classes;
}
add_filter( 'body_class', 'annam_limo_landing_body_class', 12 );

function annam_limo_landing_remove_gp_featured() {
	if ( ! annam_limo_landing_is_template() ) {
		return;
	}
	remove_action( 'generate_after_header', 'generate_featured_page_header', 10 );
	remove_action( 'generate_before_content', 'generate_featured_page_header_inside_single', 10 );
	remove_action( 'generate_after_entry_header', 'generate_post_image', 10 );
}
add_action( 'wp', 'annam_limo_landing_remove_gp_featured', 9 );

/**
 * Enqueue assets.
 */
function annam_limo_landing_enqueue_assets() {
	if ( ! annam_limo_landing_is_template() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$css = $dir . '/assets/css/limo-landing.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-limo-landing',
			$uri . '/assets/css/limo-landing.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $css )
		);
	}

	$route = annam_limo_landing_current_route();
	if ( in_array( $route, array( 'halong', 'ninhbinh' ), true ) ) {
		$brand_css = $dir . '/assets/css/limo-landing-halong.css';
		if ( file_exists( $brand_css ) ) {
			wp_enqueue_style(
				'annam-limo-landing-halong',
				$uri . '/assets/css/limo-landing-halong.css',
				array( 'annam-limo-landing' ),
				(string) filemtime( $brand_css )
			);
		}
	}

	$js = $dir . '/assets/js/limo-landing.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-limo-landing',
			$uri . '/assets/js/limo-landing.js',
			array(),
			(string) filemtime( $js ),
			true
		);

		$config        = annam_limo_landing_get_config();
		$profile       = annam_limo_landing_get_route_profile( $route );
		$form_defaults = isset( $config['form_defaults'] ) ? $config['form_defaults'] : array();
		$seat_prices   = array();
		if ( ! empty( $profile['seats'] ) && is_array( $profile['seats'] ) ) {
			foreach ( $profile['seats'] as $seat_key => $seat_row ) {
				$seat_prices[ $seat_key ] = isset( $seat_row['price'] ) ? (int) $seat_row['price'] : 0;
			}
		}
		$today         = wp_date( 'Y-m-d' );
		$gallery_js    = array();
		if ( ! empty( $config['gallery'] ) ) {
			foreach ( $config['gallery'] as $item ) {
				$slot = isset( $item['slot'] ) ? (string) $item['slot'] : '';
				$url  = '';
				$aid  = 0;
				if ( $slot && function_exists( 'annam_limo_landing_get_image_attachment_id' ) ) {
					$aid = annam_limo_landing_get_image_attachment_id( $slot, $route );
					if ( $aid > 0 ) {
						$src = wp_get_attachment_image_src( $aid, 'large' );
						$url = is_array( $src ) ? (string) $src[0] : '';
						if ( '' !== $url && function_exists( 'annam_limo_landing_cache_bust_url' ) ) {
							$url = annam_limo_landing_cache_bust_url( $url, $aid, $route );
						}
					}
				}
				if ( '' === $url && function_exists( 'annam_limo_landing_image_url' ) ) {
					$url = annam_limo_landing_image_url( $slot, $route );
				}
				if ( '' !== $url ) {
					$gallery_js[] = array(
						'src'     => $url,
						'caption' => isset( $item['caption'] ) ? (string) $item['caption'] : '',
					);
				}
			}
		}

		$team_gallery_js = array();
		$team_slots_js   = ( ! empty( $config['team']['slots'] ) && is_array( $config['team']['slots'] ) )
			? $config['team']['slots']
			: array();
		foreach ( $team_slots_js as $team_slot_key ) {
			$url = function_exists( 'annam_limo_landing_image_url' )
				? annam_limo_landing_image_url( (string) $team_slot_key, $route )
				: '';
			if ( '' === $url ) {
				continue;
			}
			$full = '';
			$aid  = function_exists( 'annam_limo_landing_get_image_attachment_id' )
				? annam_limo_landing_get_image_attachment_id( (string) $team_slot_key, $route )
				: 0;
			if ( $aid > 0 ) {
				$full_src = wp_get_attachment_image_src( $aid, 'full' );
				$full     = is_array( $full_src ) ? (string) $full_src[0] : '';
				if ( '' !== $full && function_exists( 'annam_limo_landing_cache_bust_url' ) ) {
					$full = annam_limo_landing_cache_bust_url( $full, $aid, $route );
				}
			}
			$team_gallery_js[] = array(
				'src'     => $full ? $full : $url,
				'caption' => '',
			);
		}

		wp_localize_script(
			'annam-limo-landing',
			'annamLimoLanding',
			array(
				'formId'            => 'annam-limo-booking',
				'gallery'           => $gallery_js,
				'teamGallery'       => $team_gallery_js,
				'scheduleTimes'     => annam_limo_landing_get_schedule_times_map( $route ),
				'routeDestinations' => annam_limo_landing_get_route_destinations_map( $route ),
				'formDefaults'      => array(
					'from' => isset( $form_defaults['from'] ) ? $form_defaults['from'] : 'hanoi',
					'to'   => isset( $form_defaults['to'] ) ? $form_defaults['to'] : 'sapa',
					'seat' => isset( $form_defaults['seat'] ) ? $form_defaults['seat'] : 'seat_a',
					'time' => isset( $form_defaults['time'] ) ? $form_defaults['time'] : '07:00',
					'date' => $today,
				),
				'booking'           => array(
					'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
					'action'          => 'annam_limo_booking',
					'nonceAction'     => 'annam_limo_booking_nonce',
					'nonce'           => wp_create_nonce( 'annam_limo_booking' ),
					'pageUrl'         => get_permalink() ? get_permalink() : home_url( '/' ),
					'dateToday'       => $today,
					'dateTomorrow'    => wp_date( 'Y-m-d', strtotime( '+1 day', current_time( 'timestamp' ) ) ),
					'minLeadMinutes'  => isset( $profile['min_lead_minutes'] ) ? (int) $profile['min_lead_minutes'] : 120,
					'timezone'        => wp_timezone_string(),
				),
				'routeLabels'       => isset( $profile['places'] ) ? $profile['places'] : array(),
				'placeNotePlaceholders' => isset( $profile['place_placeholders'] ) ? $profile['place_placeholders'] : array(),
				'landingKey'        => isset( $profile['landing_key'] ) ? (string) $profile['landing_key'] : '',
				'seatPrices'        => $seat_prices,
				'pageId'            => get_queried_object_id(),
				'directionQuery'    => isset( $profile['direction_query'] ) ? $profile['direction_query'] : array(),
				'maxGuests'         => isset( $profile['max_guests'] ) ? (int) $profile['max_guests'] : 20,
				'i18n'              => array(
					'sameRoute'        => __( 'Tuyến đi không hợp lệ.', 'generatepress_child' ),
					'pickTime'         => __( '— Chọn giờ —', 'generatepress_child' ),
					'noTimeToday'      => __( 'Hôm nay không còn chuyến phù hợp. Đã chuyển sang ngày mai.', 'generatepress_child' ),
					'noTimePickDate'   => __( 'Không còn chuyến phù hợp trong ngày này. Vui lòng chọn ngày khác.', 'generatepress_child' ),
					'sending'          => __( 'Đang gửi...', 'generatepress_child' ),
					'submitError'      => __( 'Không gửi được. Vui lòng thử lại hoặc gọi hotline.', 'generatepress_child' ),
					'pickTimeRequired' => __( 'Vui lòng chọn giờ đi.', 'generatepress_child' ),
				),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_limo_landing_enqueue_assets', 24 );

/**
 * Meta box.
 */
function annam_limo_landing_add_meta_box() {
	add_meta_box(
		'annam_limo_landing_opts',
		__( 'Landing Limousine (vé ghế)', 'generatepress_child' ),
		'annam_limo_landing_meta_box_render',
		'page',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'annam_limo_landing_add_meta_box' );

/**
 * @param WP_Post $post Post.
 */
function annam_limo_landing_meta_box_render( $post ) {
	if ( '' === annam_limo_landing_route_from_page( $post->ID ) ) {
		echo '<p>' . esc_html__( 'Chỉ áp dụng khi chọn template Landing Vé Limousine.', 'generatepress_child' ) . '</p>';
		return;
	}
	wp_nonce_field( 'annam_limo_landing_meta', 'annam_limo_landing_meta_nonce' );
	$title   = get_post_meta( $post->ID, '_annam_limo_hero_title', true );
	$seo_off = get_post_meta( $post->ID, '_annam_limo_seo_disabled', true );
	?>
	<p>
		<label for="annam_limo_hero_title"><strong><?php esc_html_e( 'Tiêu đề H1 (tùy chọn)', 'generatepress_child' ); ?></strong></label>
		<input type="text" class="widefat" id="annam_limo_hero_title" name="annam_limo_hero_title" value="<?php echo esc_attr( (string) $title ); ?>" placeholder="<?php esc_attr_e( 'Để trống = mặc định', 'generatepress_child' ); ?>" />
	</p>
	<p>
		<label>
			<input type="checkbox" name="annam_limo_seo_disabled" value="1" <?php checked( '1', (string) $seo_off ); ?> />
			<?php esc_html_e( 'Ẩn khối nội dung SEO (editor)', 'generatepress_child' ); ?>
		</label>
	</p>
	<?php
}

/**
 * @param int $post_id Post ID.
 */
function annam_limo_landing_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['annam_limo_landing_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['annam_limo_landing_meta_nonce'] ) ), 'annam_limo_landing_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['annam_limo_hero_title'] ) ) {
		update_post_meta( $post_id, '_annam_limo_hero_title', sanitize_text_field( wp_unslash( $_POST['annam_limo_hero_title'] ) ) );
	}
	if ( ! empty( $_POST['annam_limo_seo_disabled'] ) ) {
		update_post_meta( $post_id, '_annam_limo_seo_disabled', '1' );
	} else {
		delete_post_meta( $post_id, '_annam_limo_seo_disabled' );
	}
}
add_action( 'save_post_page', 'annam_limo_landing_save_meta_box' );

/**
 * Tạo / sửa template trang Hạ Long.
 */
function annam_limo_landing_maybe_create_halong_page() {
	if ( function_exists( 'annam_limo_landing_ensure_route_page_template' ) ) {
		$ensured = annam_limo_landing_ensure_route_page_template( 'halong' );
		if ( $ensured > 0 ) {
			update_option( 'annam_limo_halong_page_created', 1, false );
			return;
		}
	}
	if ( get_option( 'annam_limo_halong_page_created' ) ) {
		return;
	}
	$slug    = 'xe-limousine-ha-noi-ha-long';
	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Vé Limousine Hà Nội – Hạ Long',
			'post_name'    => $slug,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		),
		true
	);
	if ( ! is_wp_error( $page_id ) && $page_id ) {
		update_post_meta( (int) $page_id, '_wp_page_template', 'page-template-limousine-halong-landing.php' );
		update_option( 'annam_limo_halong_page_created', 1, false );
	}
}
add_action( 'init', 'annam_limo_landing_maybe_create_halong_page', 30 );

/**
 * Tạo / sửa template trang Ninh Bình (luôn ép lại đúng template nếu slug tồn tại).
 */
function annam_limo_landing_maybe_create_ninhbinh_page() {
	if ( function_exists( 'annam_limo_landing_ensure_route_page_template' ) ) {
		$ensured = annam_limo_landing_ensure_route_page_template( 'ninhbinh' );
		if ( $ensured > 0 ) {
			update_option( 'annam_limo_ninhbinh_page_created', 1, false );
			return;
		}
	}
	if ( get_option( 'annam_limo_ninhbinh_page_created' ) ) {
		return;
	}
	$slug    = 'xe-limousine-ha-noi-ninh-binh';
	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Vé Limousine Hà Nội – Ninh Bình',
			'post_name'    => $slug,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		),
		true
	);
	if ( ! is_wp_error( $page_id ) && $page_id ) {
		update_post_meta( (int) $page_id, '_wp_page_template', 'page-template-limousine-ninhbinh-landing.php' );
		update_option( 'annam_limo_ninhbinh_page_created', 1, false );
	}
}
add_action( 'init', 'annam_limo_landing_maybe_create_ninhbinh_page', 31 );

/**
 * URL ảnh banner landing — dùng khi share MXH (FB / Zalo / Twitter).
 *
 * @return string Absolute URL or empty.
 */
function annam_limo_landing_get_share_image_url() {
	if ( ! function_exists( 'annam_limo_landing_image_url' ) ) {
		return '';
	}
	$url = annam_limo_landing_image_url( 'hero-banner' );
	if ( ! is_string( $url ) || '' === $url ) {
		return '';
	}
	if ( 0 === strpos( $url, '//' ) ) {
		$url = ( is_ssl() ? 'https:' : 'http:' ) . $url;
	} elseif ( 0 !== strpos( $url, 'http://' ) && 0 !== strpos( $url, 'https://' ) ) {
		$url = home_url( $url );
	}
	return $url;
}

/**
 * Rank Math: ép og:image / twitter:image = banner limo theo từng route.
 *
 * @param string $attachment_url URL hiện tại.
 * @return string
 */
function annam_limo_landing_filter_rank_math_og_image( $attachment_url ) {
	if ( ! annam_limo_landing_is_template() ) {
		return $attachment_url;
	}
	$banner = annam_limo_landing_get_share_image_url();
	return '' !== $banner ? $banner : $attachment_url;
}
add_filter( 'rank_math/opengraph/facebook/image', 'annam_limo_landing_filter_rank_math_og_image', 20 );
add_filter( 'rank_math/opengraph/twitter/image', 'annam_limo_landing_filter_rank_math_og_image', 20 );

/**
 * Fallback meta OG khi không có Rank Math.
 */
function annam_limo_landing_output_social_banner_meta() {
	if ( ! annam_limo_landing_is_template() ) {
		return;
	}

	$banner = annam_limo_landing_get_share_image_url();
	if ( '' === $banner ) {
		return;
	}

	/* Khi có Rank Math vẫn bổ sung width/height nếu thiếu; ảnh chính do filter ở trên. */
	if ( ! defined( 'RANK_MATH_VERSION' ) ) {
		echo '<meta property="og:image" content="' . esc_url( $banner ) . '" />' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $banner ) . '" />' . "\n";
	}

	$aid = function_exists( 'annam_limo_landing_get_image_attachment_id' )
		? annam_limo_landing_get_image_attachment_id( 'hero-banner' )
		: 0;
	if ( $aid > 0 ) {
		$meta = wp_get_attachment_metadata( $aid );
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
			echo '<meta property="og:image:width" content="' . esc_attr( (string) (int) $meta['width'] ) . '" />' . "\n";
			echo '<meta property="og:image:height" content="' . esc_attr( (string) (int) $meta['height'] ) . '" />' . "\n";
		}
	}
}
add_action( 'wp_head', 'annam_limo_landing_output_social_banner_meta', 5 );
