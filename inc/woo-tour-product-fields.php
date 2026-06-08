<?php
/**
 * Tour meta fields on product edit screen (duration, schedule, transport, departure, hotline).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Output duration + schedule fields in General product data.
 */
function annam_tour_product_output_general_fields() {
	global $post;

	if ( ! $post instanceof WP_Post || 'product' !== $post->post_type ) {
		return;
	}

	echo '<div class="options_group show_if_simple show_if_variable">';

	woocommerce_wp_text_input(
		array(
			'id'          => '_tour_duration',
			'name'        => '_tour_duration',
			'label'       => esc_html__( 'Thời lượng tour', 'woocommerce' ),
			'placeholder' => esc_html__( 'Ví dụ: 2 ngày 1 đêm', 'woocommerce' ),
			'description' => esc_html__( 'Hiển thị trên card tour ở trang danh mục (để trống nếu không dùng).', 'woocommerce' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $post->ID, '_tour_duration', true ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_tour_schedule',
			'name'        => '_tour_schedule',
			'label'       => esc_html__( 'Lịch khởi hành', 'woocommerce' ),
			'placeholder' => esc_html__( 'Ví dụ: Đi buổi sáng hằng ngày', 'woocommerce' ),
			'description' => esc_html__( 'Hiển thị trên card tour ở trang danh mục (để trống nếu không dùng).', 'woocommerce' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $post->ID, '_tour_schedule', true ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_tour_transport',
			'name'        => '_tour_transport',
			'label'       => esc_html__( 'Phương tiện', 'woocommerce' ),
			'placeholder' => esc_html__( 'Ví dụ: Xe du lịch', 'woocommerce' ),
			'description' => esc_html__( 'Hiển thị trên trang chi tiết tour.', 'woocommerce' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $post->ID, '_tour_transport', true ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_tour_departure',
			'name'        => '_tour_departure',
			'label'       => esc_html__( 'Nơi khởi hành', 'woocommerce' ),
			'placeholder' => esc_html__( 'Ví dụ: Hà Nội', 'woocommerce' ),
			'description' => esc_html__( 'Hiển thị trên trang chi tiết tour.', 'woocommerce' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $post->ID, '_tour_departure', true ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_tour_hotline',
			'name'        => '_tour_hotline',
			'label'       => esc_html__( 'Hotline đặt tour (tuỳ chọn)', 'woocommerce' ),
			'placeholder' => esc_html__( 'Để trống dùng hotline mặc định của website', 'woocommerce' ),
			'description' => esc_html__( 'Số hotline riêng cho tour này.', 'woocommerce' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $post->ID, '_tour_hotline', true ),
		)
	);

	echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'annam_tour_product_output_general_fields', 12 );

/**
 * Field tuỳ chọn hỗ trợ Google Merchant / Product schema (không bắt buộc).
 */
function annam_merchant_product_output_admin_fields() {
	global $post;

	if ( ! $post instanceof WP_Post || 'product' !== $post->post_type ) {
		return;
	}

	$pid = (int) $post->ID;

	echo '<div class="options_group">';

	echo '<p class="form-field" style="padding:0 12px;"><strong>' . esc_html__( 'Google Merchant / dữ liệu schema', 'woocommerce' ) . '</strong><br /><span class="description">' . esc_html__( 'Điền khi cần; để trống vẫn dùng schema mặc định từ Rank Math + WooCommerce. Không nhập GTIN giả.', 'woocommerce' ) . '</span></p>';

	woocommerce_wp_text_input(
		array(
			'id'          => '_annam_google_product_category',
			'name'        => '_annam_google_product_category',
			'label'       => esc_html__( 'Google product category', 'woocommerce' ),
			'placeholder' => esc_html__( 'Ví dụ: Travel & Tourism > Tours', 'woocommerce' ),
			'description' => esc_html__( 'Mã hoặc chuỗi phân loại Google (tuỳ chọn). Lưu trong schema dạng PropertyValue.', 'woocommerce' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $pid, '_annam_google_product_category', true ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_annam_mpn',
			'name'        => '_annam_mpn',
			'label'       => esc_html__( 'MPN', 'woocommerce' ),
			'description' => esc_html__( 'Manufacturer Part Number thật nếu có (tuỳ chọn).', 'woocommerce' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $pid, '_annam_mpn', true ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_annam_brand_name',
			'name'        => '_annam_brand_name',
			'label'       => esc_html__( 'Brand (schema)', 'woocommerce' ),
			'placeholder' => 'An Nam Discovery',
			'description' => esc_html__( 'Để trống: dùng thương hiệu mặc định An Nam Discovery nếu Rank Math chưa có brand.', 'woocommerce' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $pid, '_annam_brand_name', true ),
		)
	);

	$condition_val = get_post_meta( $pid, '_annam_item_condition', true );
	$condition_val = is_string( $condition_val ) && in_array( $condition_val, array( 'new', 'used', 'refurbished' ), true ) ? $condition_val : 'new';

	woocommerce_wp_select(
		array(
			'id'          => '_annam_item_condition',
			'name'        => '_annam_item_condition',
			'label'       => esc_html__( 'Tình trạng hàng (schema)', 'woocommerce' ),
			'options'     => array(
				'new'         => esc_html__( 'Mới', 'woocommerce' ),
				'used'        => esc_html__( 'Đã qua sử dụng', 'woocommerce' ),
				'refurbished' => esc_html__( 'Tân trang', 'woocommerce' ),
			),
			'value'       => $condition_val,
			'description' => esc_html__( 'Dùng cho itemCondition trong Offer (schema.org).', 'woocommerce' ),
			'desc_tip'    => true,
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_annam_merchant_custom_label',
			'name'        => '_annam_merchant_custom_label',
			'label'       => esc_html__( 'Custom label 0', 'woocommerce' ),
			'description' => esc_html__( 'Nhãn tuỳ chọn (feed / Merchant), lưu trong schema additionalProperty.', 'woocommerce' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $pid, '_annam_merchant_custom_label', true ),
		)
	);

	echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'annam_merchant_product_output_admin_fields', 25 );

/**
 * Save tour meta.
 *
 * @param int $post_id Product post ID.
 */
function annam_tour_product_save_fields( $post_id ) {
	if ( ! isset( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['_tour_duration'] ) ) {
		update_post_meta( $post_id, '_tour_duration', sanitize_text_field( wp_unslash( $_POST['_tour_duration'] ) ) );
	}

	if ( isset( $_POST['_tour_schedule'] ) ) {
		update_post_meta( $post_id, '_tour_schedule', sanitize_text_field( wp_unslash( $_POST['_tour_schedule'] ) ) );
	}

	if ( isset( $_POST['_tour_transport'] ) ) {
		update_post_meta( $post_id, '_tour_transport', sanitize_text_field( wp_unslash( $_POST['_tour_transport'] ) ) );
	}

	if ( isset( $_POST['_tour_departure'] ) ) {
		update_post_meta( $post_id, '_tour_departure', sanitize_text_field( wp_unslash( $_POST['_tour_departure'] ) ) );
	}

	if ( isset( $_POST['_tour_hotline'] ) ) {
		update_post_meta( $post_id, '_tour_hotline', sanitize_text_field( wp_unslash( $_POST['_tour_hotline'] ) ) );
	}

	if ( isset( $_POST['_annam_google_product_category'] ) ) {
		update_post_meta( $post_id, '_annam_google_product_category', sanitize_text_field( wp_unslash( $_POST['_annam_google_product_category'] ) ) );
	}
	if ( isset( $_POST['_annam_mpn'] ) ) {
		update_post_meta( $post_id, '_annam_mpn', sanitize_text_field( wp_unslash( $_POST['_annam_mpn'] ) ) );
	}
	if ( isset( $_POST['_annam_brand_name'] ) ) {
		update_post_meta( $post_id, '_annam_brand_name', sanitize_text_field( wp_unslash( $_POST['_annam_brand_name'] ) ) );
	}
	if ( isset( $_POST['_annam_item_condition'] ) ) {
		$cond = sanitize_key( wp_unslash( $_POST['_annam_item_condition'] ) );
		if ( in_array( $cond, array( 'new', 'used', 'refurbished' ), true ) ) {
			update_post_meta( $post_id, '_annam_item_condition', $cond );
		}
	}
	if ( isset( $_POST['_annam_merchant_custom_label'] ) ) {
		update_post_meta( $post_id, '_annam_merchant_custom_label', sanitize_text_field( wp_unslash( $_POST['_annam_merchant_custom_label'] ) ) );
	}
}
add_action( 'woocommerce_process_product_meta', 'annam_tour_product_save_fields', 10, 1 );
