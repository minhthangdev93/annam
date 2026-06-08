<?php
/**
 * Trang Liên hệ (template): enqueue, Customizer (bản đồ), xử lý form, rate limit.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/** @var int Số lần gửi form tối đa mỗi IP (cửa sổ phút — dùng annam_check_rate_limit). */
const ANNAM_CONTACT_RATE_LIMIT_MAX = 5;

/** @var int Cửa sổ rate limit (phút). */
const ANNAM_CONTACT_RATE_LIMIT_WINDOW_MINUTES = 10;

/**
 * Trang hiện tại có dùng template Liên hệ không?
 *
 * @return bool
 */
function annam_contact_is_contact_template() {
	if ( ! is_singular( 'page' ) ) {
		return false;
	}
	$page_id = get_queried_object_id();
	return $page_id && 'page-template-lien-he.php' === get_page_template_slug( $page_id );
}

/**
 * Thông tin liên hệ & mạng xã hội (có thể ghi đè qua filter `annam_contact_details` hoặc Customizer địa chỉ).
 *
 * @return array<string,string>
 */
function annam_contact_get_details() {
	$defaults = array(
		'brand'                 => 'An Nam Discovery',
		'hotline_display'       => '1900 8164',
		'hotline_tel'           => 'tel:19008164',
		'mobile_display'        => '0942471111',
		'zalo_url'              => 'http://zalo.me/2127942034358673568',
		'whatsapp_url'          => 'https://wa.me/+84942471111',
		'address'               => '214 Đ. Trần Quang Khải, Hoàn Kiếm, Hà Nội',
		'maps_directions_url' => 'https://maps.app.goo.gl/3DmmAdysHTtu7pPu9',
		'hours'                 => '08:00 - 22:00 hằng ngày',
		'email'                 => 'annamdiscoveryvn@gmail.com',
		'facebook_url'          => 'https://web.facebook.com/AnNamDiscovery/',
		'twitter_url'           => 'https://x.com/annamdiscovery',
		'youtube_url'           => 'https://www.youtube.com/@AnNamDiscovery',
		'instagram_url'         => 'https://www.instagram.com/annamdiscovery/',
	);

	$mod = function_exists( 'get_theme_mod' ) ? trim( (string) get_theme_mod( 'annam_contact_map_address', '' ) ) : '';
	if ( $mod !== '' ) {
		$defaults['address'] = $mod;
	}

	return apply_filters( 'annam_contact_details', $defaults );
}

/**
 * Danh sách văn phòng (section “Văn phòng & khu vực phục vụ”).
 *
 * Mỗi phần tử: title, address, maps_url.
 *
 * @return array<int, array{title:string,address:string,maps_url:string}>
 */
function annam_contact_get_offices() {
	$offices = array(
		array(
			'title'    => __( 'Văn phòng Hoàn Kiếm', 'generatepress_child' ),
			'address'  => '214 Đ. Trần Quang Khải, Hoàn Kiếm, Hà Nội',
			'maps_url' => 'https://maps.app.goo.gl/3DmmAdysHTtu7pPu9',
		),
		array(
			'title'    => __( 'Văn phòng Tú Mỡ', 'generatepress_child' ),
			'address'  => '23 Tú Mỡ, Phường Yên Hòa, Tp Hà Nội',
			'maps_url' => 'https://maps.app.goo.gl/dLFtc2s8LSYchXQa6',
		),
	);

	return apply_filters( 'annam_contact_offices', $offices );
}

/**
 * FAQ trang Liên hệ (dùng cho HTML + JSON-LD FAQPage).
 *
 * @return array<int, array{question:string,answer:string}>
 */
function annam_contact_get_faq_items() {
	$items = array(
		array(
			'question' => __( 'Tôi gửi yêu cầu thì bao lâu được phản hồi?', 'generatepress_child' ),
			'answer'   => __( 'Trong giờ làm việc, đội ngũ thường phản hồi trong vòng 30–60 phút qua điện thoại hoặc Zalo. Email có thể mất vài giờ tùy khối lượng yêu cầu.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Có thể tư vấn tour theo ngân sách không?', 'generatepress_child' ),
			'answer'   => __( 'Có. Bạn chỉ cần cho biết mức ngân sách dự kiến và số ngày, chúng tôi sẽ gợi ý các phương án phù hợp.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Có hỗ trợ đoàn công ty/gia đình không?', 'generatepress_child' ),
			'answer'   => __( 'Có. Chúng tôi hỗ trợ đoàn gia đình, nhóm bạn và doanh nghiệp với lịch trình và báo giá riêng.', 'generatepress_child' ),
		),
		array(
			'question' => __( 'Có thể đặt combo xe + khách sạn không?', 'generatepress_child' ),
			'answer'   => __( 'Có. An Nam Discovery kết hợp vé xe limousine, lưu trú và dịch vụ tại điểm đến theo nhu cầu của bạn.', 'generatepress_child' ),
		),
	);

	return apply_filters( 'annam_contact_faq_items', $items );
}

/**
 * Gỡ ảnh nổi bật mặc định của GeneratePress (chỉ dùng ảnh đại diện làm nền hero).
 */
function annam_contact_remove_gp_featured_image() {
	if ( ! annam_contact_is_contact_template() ) {
		return;
	}
	remove_action( 'generate_after_header', 'generate_featured_page_header', 10 );
	remove_action( 'generate_before_content', 'generate_featured_page_header_inside_single', 10 );
	remove_action( 'generate_after_entry_header', 'generate_post_image', 10 );
}
add_action( 'wp', 'annam_contact_remove_gp_featured_image', 9 );

/**
 * Ẩn HTML ảnh nổi bật trong excerpt/archive (dự phòng, không ảnh hưởng singular).
 *
 * @param string $html HTML.
 * @return string
 */
function annam_contact_blank_featured_image_output( $html ) {
	return annam_contact_is_contact_template() ? '' : $html;
}
add_filter( 'generate_featured_image_output', 'annam_contact_blank_featured_image_output', 10, 1 );

/**
 * URL ảnh nền hero Liên hệ (featured trang hoặc fallback).
 *
 * @param int $page_id ID trang.
 * @return string
 */
function annam_contact_hero_background_url( $page_id ) {
	$page_id = (int) $page_id;
	if ( $page_id && has_post_thumbnail( $page_id ) ) {
		$u = get_the_post_thumbnail_url( $page_id, 'large' );
		if ( $u ) {
			return $u;
		}
	}
	if ( function_exists( 'annam_blog_hero_fallback_background_url' ) ) {
		return annam_blog_hero_fallback_background_url();
	}
	$uri = get_stylesheet_directory_uri();
	return $uri . '/assets/images/cam-nang-hero-bg.svg';
}

/**
 * Lấy IP client (đơn giản).
 *
 * @return string
 */
function annam_contact_get_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return preg_match( '/^[\d.:a-fA-F]+$/', $ip ) ? $ip : '0.0.0.0';
}

/**
 * Đã vượt rate limit form liên hệ chưa.
 *
 * @return bool
 */
function annam_contact_rate_limit_exceeded() {
	if ( function_exists( 'annam_check_rate_limit' ) ) {
		return ! annam_check_rate_limit( 'annam_contact_form', ANNAM_CONTACT_RATE_LIMIT_MAX, ANNAM_CONTACT_RATE_LIMIT_WINDOW_MINUTES );
	}
	return false;
}

/**
 * Ghi nhận một lần gửi thành công (sau khi gửi mail OK).
 */
function annam_contact_rate_limit_record() {
	if ( function_exists( 'annam_rate_limit_increment' ) ) {
		annam_rate_limit_increment( 'annam_contact_form', ANNAM_CONTACT_RATE_LIMIT_WINDOW_MINUTES );
	}
}

/**
 * Chuẩn hóa SĐT (bỏ khoảng trắng, dấu chấm, gạch ngang) để hiển thị / lưu.
 *
 * @param string $phone Raw input.
 * @return string
 */
function annam_contact_normalize_phone( $phone ) {
	$phone = trim( (string) $phone );
	if ( '' === $phone ) {
		return '';
	}
	$phone = preg_replace( '/[\s.\-]+/', '', $phone );
	return $phone;
}

/**
 * Validate số điện thoại: không rỗng, tối thiểu 9 chữ số sau khi chuẩn hóa.
 *
 * @param string $phone Số điện thoại.
 * @return bool
 */
function annam_contact_validate_phone( $phone ) {
	$normalized = annam_contact_normalize_phone( $phone );
	if ( '' === $normalized ) {
		return false;
	}
	$digits = preg_replace( '/\D/', '', $normalized );
	return strlen( $digits ) >= 9;
}

/**
 * Danh sách loại dịch vụ (value => label).
 *
 * @return array<string,string>
 */
function annam_contact_service_options() {
	return array(
		'tour'   => __( 'Tour du lịch', 'generatepress_child' ),
		'combo'  => __( 'Combo du lịch', 'generatepress_child' ),
		'bus'    => __( 'Vé xe', 'generatepress_child' ),
		'cruise' => __( 'Du thuyền', 'generatepress_child' ),
		'hotel'  => __( 'Khách sạn / Homestay', 'generatepress_child' ),
		'other'  => __( 'Dịch vụ khác', 'generatepress_child' ),
	);
}

/**
 * Email nhận lead (ưu tiên cài đặt Landing Cabin VIP, fallback admin).
 *
 * @return string[]
 */
function annam_contact_get_lead_recipient_emails() {
	if ( function_exists( 'annam_cabin_landing_get_lead_recipient_emails' ) ) {
		$emails = annam_cabin_landing_get_lead_recipient_emails();
		if ( ! empty( $emails ) ) {
			return $emails;
		}
	}

	$admin = get_option( 'admin_email' );
	return is_email( $admin ) ? array( $admin ) : array();
}

/**
 * Thông báo thành công.
 *
 * @return string
 */
function annam_contact_success_message() {
	return __( 'Cảm ơn quý khách. An Nam Discovery đã nhận yêu cầu tư vấn và sẽ liên hệ lại trong thời gian sớm nhất.', 'generatepress_child' );
}

/**
 * Thông báo lỗi chung.
 *
 * @return string
 */
function annam_contact_error_message() {
	return __( 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại hoặc liên hệ hotline/Zalo.', 'generatepress_child' );
}

/**
 * Xử lý gửi form Liên hệ (POST hoặc AJAX).
 *
 * @param array<string,mixed> $input Raw input.
 * @return array{success:bool,message:string,code?:string}
 */
function annam_contact_process_submission( array $input ) {
	$fail = static function ( $message, $code = 'error' ) {
		return array(
			'success' => false,
			'message' => $message,
			'code'    => $code,
		);
	};

	if ( empty( $input['annam_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( (string) $input['annam_contact_nonce'] ), 'annam_contact_form' ) ) {
		return $fail( __( 'Phiên không hợp lệ. Vui lòng tải lại trang và thử lại.', 'generatepress_child' ), 'nonce' );
	}

	if ( ! empty( $input['annam_contact_website'] ) ) {
		return $fail( annam_contact_error_message(), 'spam' );
	}

	$ts = isset( $input['annam_form_ts'] ) ? absint( $input['annam_form_ts'] ) : 0;
	if ( ! $ts || ( time() - $ts ) < 3 || ( time() - $ts ) > 7200 ) {
		return $fail( annam_contact_error_message(), 'spam' );
	}

	if ( annam_contact_rate_limit_exceeded() ) {
		return $fail( __( 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau ít phút.', 'generatepress_child' ), 'rate' );
	}

	$name        = isset( $input['annam_contact_name'] ) ? sanitize_text_field( (string) $input['annam_contact_name'] ) : '';
	$phone_raw   = isset( $input['annam_contact_phone'] ) ? sanitize_text_field( (string) $input['annam_contact_phone'] ) : '';
	$phone       = annam_contact_normalize_phone( $phone_raw );
	$service     = isset( $input['annam_contact_service'] ) ? sanitize_key( (string) $input['annam_contact_service'] ) : '';
	$travel_date = isset( $input['annam_contact_travel_date'] ) ? sanitize_text_field( (string) $input['annam_contact_travel_date'] ) : '';
	$guests_raw  = isset( $input['annam_contact_guests'] ) ? trim( (string) $input['annam_contact_guests'] ) : '';
	$message     = isset( $input['annam_contact_message'] ) ? sanitize_textarea_field( (string) $input['annam_contact_message'] ) : '';

	if ( function_exists( 'mb_substr' ) ) {
		$name    = mb_substr( $name, 0, 100, 'UTF-8' );
		$phone   = mb_substr( $phone, 0, 25, 'UTF-8' );
		$message = mb_substr( $message, 0, 1000, 'UTF-8' );
	} else {
		$name    = substr( $name, 0, 100 );
		$phone   = substr( $phone, 0, 25 );
		$message = substr( $message, 0, 1000 );
	}

	$options = annam_contact_service_options();

	if ( '' === trim( $name ) ) {
		return $fail( __( 'Vui lòng nhập họ và tên.', 'generatepress_child' ), 'name' );
	}

	if ( '' === trim( $phone_raw ) || ! annam_contact_validate_phone( $phone_raw ) ) {
		return $fail( __( 'Số điện thoại không hợp lệ. Vui lòng nhập ít nhất 9 chữ số.', 'generatepress_child' ), 'phone' );
	}

	if ( ! isset( $options[ $service ] ) ) {
		return $fail( __( 'Vui lòng chọn loại dịch vụ cần tư vấn.', 'generatepress_child' ), 'service' );
	}

	$guests = '' === $guests_raw ? 0 : (int) $guests_raw;
	if ( $guests < 1 && '' !== $guests_raw ) {
		$guests = 0;
	}
	if ( $guests > 500 ) {
		$guests = 500;
	}

	$today = wp_date( 'Y-m-d' );
	if ( '' === $travel_date ) {
		$travel_date = $today;
	} elseif ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $travel_date ) ) {
		$travel_date = $today;
	} elseif ( $travel_date < $today ) {
		return $fail( __( 'Ngày đi dự kiến không thể là ngày trong quá khứ.', 'generatepress_child' ), 'date' );
	}

	$recipients = annam_contact_get_lead_recipient_emails();
	if ( empty( $recipients ) ) {
		return $fail( annam_contact_error_message(), 'mail' );
	}

	$service_label = $options[ $service ];
	$subject       = sprintf(
		'Lead tư vấn từ trang Liên hệ - %s - %s',
		$service_label,
		$name
	);

	$page_url = isset( $input['annam_contact_page_url'] ) ? esc_url_raw( (string) $input['annam_contact_page_url'] ) : '';
	if ( '' === $page_url ) {
		$page_url = get_permalink() ? get_permalink() : home_url( '/' );
	}

	$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
	if ( function_exists( 'mb_substr' ) && '' !== $user_agent ) {
		$user_agent = mb_substr( $user_agent, 0, 500, 'UTF-8' );
	} elseif ( '' !== $user_agent ) {
		$user_agent = substr( $user_agent, 0, 500 );
	}

	$submitted_at = wp_date( 'd/m/Y H:i' );

	$body_lines = array(
		__( 'Yêu cầu tư vấn mới từ trang Liên hệ', 'generatepress_child' ),
		'',
		__( 'Họ và tên:', 'generatepress_child' ) . ' ' . $name,
		__( 'Số điện thoại:', 'generatepress_child' ) . ' ' . $phone,
		__( 'Loại dịch vụ cần tư vấn:', 'generatepress_child' ) . ' ' . $service_label,
		__( 'Ngày đi dự kiến:', 'generatepress_child' ) . ' ' . $travel_date,
		__( 'Số lượng khách:', 'generatepress_child' ) . ' ' . ( $guests > 0 ? (string) $guests : '—' ),
		'',
		__( 'Nội dung cần hỗ trợ:', 'generatepress_child' ),
		$message !== '' ? $message : '—',
		'',
		__( 'Thời gian gửi form:', 'generatepress_child' ) . ' ' . $submitted_at,
		__( 'URL trang gửi form:', 'generatepress_child' ) . ' ' . $page_url,
		'IP: ' . annam_contact_get_client_ip(),
		'User-Agent: ' . ( '' !== $user_agent ? $user_agent : '—' ),
	);

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	$sent    = false;

	foreach ( $recipients as $to ) {
		if ( wp_mail( $to, $subject, implode( "\n", $body_lines ), $headers ) ) {
			$sent = true;
		}
	}

	if ( ! $sent ) {
		return $fail( annam_contact_error_message(), 'mail' );
	}

	annam_contact_rate_limit_record();

	return array(
		'success' => true,
		'message' => annam_contact_success_message(),
	);
}

/**
 * Fallback POST (no-JS).
 */
function annam_contact_maybe_handle_form() {
	if ( ! annam_contact_is_contact_template() ) {
		return;
	}
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : '' ) ) {
		return;
	}
	if ( empty( $_POST['annam_contact_submit'] ) ) {
		return;
	}

	$redirect_base = get_permalink();
	if ( ! $redirect_base ) {
		return;
	}

	$input = wp_unslash( $_POST );
	$input['annam_contact_page_url'] = $redirect_base;
	$result = annam_contact_process_submission( $input );

	if ( ! empty( $result['success'] ) ) {
		wp_safe_redirect( add_query_arg( 'annam_contact', 'sent', $redirect_base ) );
		exit;
	}

	$code = isset( $result['code'] ) ? sanitize_key( (string) $result['code'] ) : 'error';
	wp_safe_redirect(
		add_query_arg(
			array(
				'annam_contact' => 'error',
				'err'           => rawurlencode( $code ),
			),
			$redirect_base
		)
	);
	exit;
}
add_action( 'template_redirect', 'annam_contact_maybe_handle_form', 1 );

/**
 * AJAX gửi form Liên hệ.
 */
function annam_contact_ajax_submit() {
	$input = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$input['annam_contact_page_url'] = isset( $input['annam_contact_page_url'] ) ? $input['annam_contact_page_url'] : ( get_permalink() ? get_permalink() : home_url( '/' ) );

	$result = annam_contact_process_submission( $input );

	if ( ! empty( $result['success'] ) ) {
		wp_send_json_success(
			array(
				'message' => $result['message'],
			)
		);
	}

	wp_send_json_error(
		array(
			'message' => isset( $result['message'] ) ? $result['message'] : annam_contact_error_message(),
			'code'    => isset( $result['code'] ) ? $result['code'] : 'error',
		),
		400
	);
}
add_action( 'wp_ajax_annam_contact_form', 'annam_contact_ajax_submit' );
add_action( 'wp_ajax_nopriv_annam_contact_form', 'annam_contact_ajax_submit' );

/**
 * Body class.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function annam_contact_body_class( $classes ) {
	if ( annam_contact_is_contact_template() ) {
		$classes[] = 'annam-contact-page';
	}
	return $classes;
}
add_filter( 'body_class', 'annam_contact_body_class', 12 );

/**
 * Ẩn entry header mặc định trên template Liên hệ.
 *
 * @param bool $show Hiển thị.
 * @return bool
 */
function annam_contact_hide_entry_header( $show ) {
	return annam_contact_is_contact_template() ? false : $show;
}
add_filter( 'generate_show_entry_header', 'annam_contact_hide_entry_header', 12 );

/**
 * Enqueue CSS/JS trang Liên hệ.
 */
function annam_contact_enqueue_assets() {
	if ( is_admin() || ! annam_contact_is_contact_template() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$deps = array( 'annam-design-tokens' );
	$css  = $dir . '/assets/css/contact-page.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-contact-page',
			$uri . '/assets/css/contact-page.css',
			$deps,
			(string) filemtime( $css )
		);
	}

	$js = $dir . '/assets/js/contact-page.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-contact-page',
			$uri . '/assets/js/contact-page.js',
			array(),
			(string) filemtime( $js ),
			true
		);

		wp_localize_script(
			'annam-contact-page',
			'annamContactForm',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'action'   => 'annam_contact_form',
				'nonce'    => wp_create_nonce( 'annam_contact_form' ),
				'pageUrl'  => get_permalink() ? get_permalink() : home_url( '/' ),
				'dateToday'=> wp_date( 'Y-m-d' ),
				'i18n'     => array(
					'sending'      => __( 'Đang gửi...', 'generatepress_child' ),
					'success'      => annam_contact_success_message(),
					'error'        => annam_contact_error_message(),
					'nameRequired' => __( 'Vui lòng nhập họ và tên.', 'generatepress_child' ),
					'phoneRequired'=> __( 'Vui lòng nhập số điện thoại.', 'generatepress_child' ),
					'phoneInvalid' => __( 'Số điện thoại không hợp lệ. Vui lòng nhập ít nhất 9 chữ số.', 'generatepress_child' ),
					'serviceRequired' => __( 'Vui lòng chọn loại dịch vụ cần tư vấn.', 'generatepress_child' ),
					'datePast'     => __( 'Ngày đi dự kiến không thể là ngày trong quá khứ.', 'generatepress_child' ),
					'submitLabel'  => __( 'Gửi yêu cầu tư vấn', 'generatepress_child' ),
				),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_contact_enqueue_assets', 22 );

/**
 * Customizer: địa chỉ hiển thị bản đồ (embed Google Maps đơn giản).
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function annam_contact_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'annam_contact_section',
		array(
			'title'    => __( 'Trang Liên hệ (An Nam)', 'generatepress_child' ),
			'priority' => 130,
		)
	);

	$wp_customize->add_setting(
		'annam_contact_map_address',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'annam_contact_map_address',
		array(
			'label'       => __( 'Địa chỉ văn phòng (tùy chọn)', 'generatepress_child' ),
			'description' => __( 'Nếu nhập, sẽ thay địa chỉ mặc định khi hiển thị và khi nhúng Google Maps. Để trống để dùng địa chỉ mặc định trong code.', 'generatepress_child' ),
			'section'     => 'annam_contact_section',
			'type'        => 'textarea',
		)
	);
}
add_action( 'customize_register', 'annam_contact_customize_register' );

/**
 * URL embed Google Maps từ địa chỉ (không cần API key, output=embed).
 *
 * @param string $address Địa chỉ.
 * @return string
 */
function annam_contact_maps_embed_url( $address ) {
	$address = trim( (string) $address );
	if ( '' === $address ) {
		return '';
	}
	return 'https://www.google.com/maps?q=' . rawurlencode( $address ) . '&hl=vi&z=16&output=embed';
}

/**
 * Thông báo sau redirect (GET).
 *
 * @return array{type:string,message:string}|null
 */
function annam_contact_get_notice() {
	if ( ! annam_contact_is_contact_template() ) {
		return null;
	}
	if ( ! isset( $_GET['annam_contact'] ) ) {
		return null;
	}
	$status = sanitize_key( wp_unslash( $_GET['annam_contact'] ) );
	if ( 'sent' === $status ) {
		return array(
			'type'    => 'success',
			'message' => annam_contact_success_message(),
		);
	}
	if ( 'error' !== $status ) {
		return null;
	}
	$code = isset( $_GET['err'] ) ? sanitize_key( wp_unslash( $_GET['err'] ) ) : 'unknown';
	$map  = array(
		'nonce'       => __( 'Phiên làm việc hết hạn. Vui lòng tải lại trang và gửi lại.', 'generatepress_child' ),
		'spam'        => __( 'Thông tin chưa hợp lệ, vui lòng kiểm tra lại.', 'generatepress_child' ),
		'rate'        => __( 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau ít phút.', 'generatepress_child' ),
		'invalid'     => __( 'Thông tin chưa hợp lệ, vui lòng kiểm tra lại.', 'generatepress_child' ),
		'validation'  => __( 'Thông tin chưa hợp lệ, vui lòng kiểm tra lại.', 'generatepress_child' ),
		'mail'        => annam_contact_error_message(),
		'unknown'     => annam_contact_error_message(),
		'name'        => __( 'Vui lòng nhập họ và tên.', 'generatepress_child' ),
		'phone'       => __( 'Số điện thoại không hợp lệ. Vui lòng nhập ít nhất 9 chữ số.', 'generatepress_child' ),
		'service'     => __( 'Vui lòng chọn loại dịch vụ cần tư vấn.', 'generatepress_child' ),
		'date'        => __( 'Ngày đi dự kiến không thể là ngày trong quá khứ.', 'generatepress_child' ),
	);
	return array(
		'type'    => 'error',
		'message' => isset( $map[ $code ] ) ? $map[ $code ] : $map['unknown'],
	);
}
