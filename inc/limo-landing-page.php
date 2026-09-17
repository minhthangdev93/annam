<?php
/**
 * Landing vé Limousine HN–Sapa: enqueue, form hooks.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/limo-landing-config.php';
require_once get_stylesheet_directory() . '/inc/limo-landing-routes.php';
require_once get_stylesheet_directory() . '/inc/limo-landing-booking.php';

const ANNAM_LIMO_LANDING_RATE_MAX     = 8;
const ANNAM_LIMO_LANDING_RATE_MINUTES = 10;

/**
 * @return bool
 */
function annam_limo_landing_is_template() {
	if ( ! is_singular( 'page' ) ) {
		return false;
	}
	$page_id = get_queried_object_id();
	return $page_id && 'page-template-limousine-sapa-landing.php' === get_page_template_slug( $page_id );
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
 * @return string
 */
function annam_limo_landing_print_image( $slot_key, array $attrs = array() ) {
	$slot_key = sanitize_key( (string) $slot_key );
	if ( '' === $slot_key || ! function_exists( 'annam_limo_landing_get_image_attachment_id' ) ) {
		return '';
	}

	$attachment_id = annam_limo_landing_get_image_attachment_id( $slot_key );
	if ( $attachment_id <= 0 ) {
		$url = function_exists( 'annam_limo_landing_image_url' ) ? annam_limo_landing_image_url( $slot_key ) : '';
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
		$slots = annam_limo_landing_get_image_slots();
		if ( isset( $slots[ $slot_key ]['wp_size'] ) ) {
			$size = (string) $slots[ $slot_key ]['wp_size'];
		}
	}

	return wp_get_attachment_image(
		$attachment_id,
		$size,
		false,
		array_merge(
			array(
				'loading'  => 'lazy',
				'decoding' => 'async',
			),
			$attrs
		)
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
	if ( annam_limo_landing_is_template() ) {
		$classes[] = 'annam-limo-landing-page';
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
		$form_defaults = isset( $config['form_defaults'] ) ? $config['form_defaults'] : array();
		$today         = wp_date( 'Y-m-d' );
		$gallery_js    = array();
		if ( ! empty( $config['gallery'] ) ) {
			foreach ( $config['gallery'] as $item ) {
				$slot = isset( $item['slot'] ) ? (string) $item['slot'] : '';
				$url  = '';
				if ( $slot && function_exists( 'annam_limo_landing_get_image_attachment_id' ) ) {
					$aid = annam_limo_landing_get_image_attachment_id( $slot );
					if ( $aid > 0 ) {
						$src = wp_get_attachment_image_src( $aid, 'large' );
						$url = is_array( $src ) ? (string) $src[0] : '';
					}
				}
				if ( '' === $url && function_exists( 'annam_limo_landing_image_url' ) ) {
					$url = annam_limo_landing_image_url( $slot );
				}
				if ( '' !== $url ) {
					$gallery_js[] = array(
						'src'     => $url,
						'caption' => isset( $item['caption'] ) ? (string) $item['caption'] : '',
					);
				}
			}
		}

		wp_localize_script(
			'annam-limo-landing',
			'annamLimoLanding',
			array(
				'formId'            => 'annam-limo-booking',
				'gallery'           => $gallery_js,
				'scheduleTimes'     => annam_limo_landing_get_schedule_times_map(),
				'routeDestinations' => annam_limo_landing_get_route_destinations_map(),
				'formDefaults'      => array(
					'from' => isset( $form_defaults['from'] ) ? $form_defaults['from'] : 'hanoi',
					'to'   => isset( $form_defaults['to'] ) ? $form_defaults['to'] : 'sapa',
					'seat' => isset( $form_defaults['seat'] ) ? $form_defaults['seat'] : 'seat_a',
					'time' => isset( $form_defaults['time'] ) ? $form_defaults['time'] : '07:00',
					'date' => $today,
				),
				'booking'           => array(
					'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
					'action'       => 'annam_limo_booking',
					'nonceAction'  => 'annam_limo_booking_nonce',
					'nonce'        => wp_create_nonce( 'annam_limo_booking' ),
					'pageUrl'      => get_permalink() ? get_permalink() : home_url( '/' ),
					'dateToday'    => $today,
					'dateTomorrow' => wp_date( 'Y-m-d', strtotime( '+1 day', current_time( 'timestamp' ) ) ),
					'minLeadHours' => (int) apply_filters( 'annam_limo_landing_min_lead_hours', 2 ),
					'timezone'     => wp_timezone_string(),
				),
				'routeLabels'       => array(
					'hanoi' => 'Hà Nội',
					'sapa'  => 'Sapa',
				),
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
		__( 'Landing Limousine HN–Sapa', 'generatepress_child' ),
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
	if ( 'page-template-limousine-sapa-landing.php' !== get_page_template_slug( $post ) ) {
		echo '<p>' . esc_html__( 'Chỉ áp dụng khi chọn template Landing Vé Limousine HN–Sapa.', 'generatepress_child' ) . '</p>';
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
