<?php
/**
 * Footer tùy chỉnh (child theme): thay footer widgets + site-info GeneratePress.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL logo footer (giữ filter cho tương thích; footer custom hiện không hiển thị logo).
 *
 * @return string
 */
function annam_footer_logo_url() {
	$default = content_url( 'uploads/2026/05/logo.png' );
	return apply_filters( 'annam_footer_logo_url', $default );
}

/**
 * URL ảnh kênh thanh toán.
 *
 * @return string
 */
function annam_footer_payment_image_url() {
	$default = content_url( 'uploads/2026/05/visa.png.webp' );
	return apply_filters( 'annam_footer_payment_image_url', $default );
}

/**
 * ảnh cuối cột 4 footer (thay GTranslate).
 *
 * @return string
 */
function annam_footer_sale_notice_image_url() {
	$default = content_url( 'uploads/2026/05/logoSaleNoti.png' );
	return apply_filters( 'annam_footer_sale_notice_image_url', $default );
}

/**
 * SVG icon tĩnh cho cột thông tin công ty (stroke currentColor).
 *
 * @param string $name location|phone|mail|document|tax
 * @return string
 */
function annam_footer_icon_svg( $name ) {
	$name = sanitize_key( $name );
	$open = '<svg class="annam-site-footer__inline-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">';
	$body = '';

	switch ( $name ) {
		case 'location':
			$body = '<path d="M12 21s-8-4.434-8-11a8 8 0 1 1 16 0c0 6.566-8 11-8 11z"/><circle cx="12" cy="10" r="2.5"/>';
			break;
		case 'phone':
			$body = '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>';
			break;
		case 'mail':
			$body = '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>';
			break;
		case 'document':
			$body = '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/>';
			break;
		case 'tax':
			$body = '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M6 5V3a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2"/><path d="M8 12h8"/><path d="M8 16h5"/>';
			break;
		default:
			return '';
	}

	return $open . $body . '</svg>';
}

/**
 * Link Google Maps cho từng văn phòng (filter `annam_footer_office_map_urls`).
 *
 * @return array<string,string>
 */
function annam_footer_office_map_urls() {
	return apply_filters(
		'annam_footer_office_map_urls',
		array(
			'tu_mo'    => 'https://maps.app.goo.gl/dLFtc2s8LSYchXQa6',
			'hoan_kiem' => 'https://maps.app.goo.gl/3DmmAdysHTtu7pPu9',
		)
	);
}

/**
 * Gắn link danh mục WooCommerce nếu slug tồn tại.
 *
 * @param string[] $slug_candidates Các slug thử lần lượt.
 * @param string   $fallback_path   Đường dẫn tương đối site, ví dụ `/tour-ha-long/`.
 * @return string
 */
function annam_footer_resolve_product_cat_url( array $slug_candidates, $fallback_path ) {
	$fallback_path = '/' . trim( (string) $fallback_path, '/' ) . '/';
	$fallback      = home_url( $fallback_path );

	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return $fallback;
	}

	foreach ( $slug_candidates as $slug ) {
		$slug = sanitize_title( (string) $slug );
		if ( '' === $slug ) {
			continue;
		}
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$url = get_term_link( $term );
			if ( ! is_wp_error( $url ) ) {
				return $url;
			}
		}
	}

	return $fallback;
}

/**
 * Transient cache key cho danh mục cha footer.
 *
 * @return string
 */
function annam_footer_tour_cat_cache_key() {
	return 'annam_footer_tour_cat_parent_rows_v3';
}

/**
 * Xóa cache danh mục tour footer.
 */
function annam_footer_flush_tour_cat_cache() {
	delete_transient( 'annam_footer_tour_cat_parent_rows_v1' );
	delete_transient( 'annam_footer_tour_cat_parent_rows_v2' );
	delete_transient( annam_footer_tour_cat_cache_key() );
}

/**
 * Khi sửa / tạo danh mục sản phẩm.
 *
 * @param int    $term_id  Term ID.
 * @param int    $tt_id    Term taxonomy ID.
 * @param string $taxonomy Taxonomy slug.
 */
function annam_footer_on_product_cat_term_changed( $term_id, $tt_id, $taxonomy ) {
	if ( 'product_cat' === $taxonomy ) {
		annam_footer_flush_tour_cat_cache();
	}
}
add_action( 'edited_term', 'annam_footer_on_product_cat_term_changed', 10, 3 );
add_action( 'created_term', 'annam_footer_on_product_cat_term_changed', 10, 3 );

/**
 * Khi xóa danh mục sản phẩm.
 *
 * @param int      $term_id       Term ID.
 * @param int      $tt_id         Term taxonomy ID.
 * @param string   $taxonomy      Taxonomy slug.
 * @param WP_Term  $deleted_term  Deleted term.
 * @param int[]    $object_ids    Object IDs.
 */
function annam_footer_on_product_cat_term_deleted( $term_id, $tt_id, $taxonomy, $deleted_term = null, $object_ids = null ) {
	unset( $deleted_term, $object_ids );
	if ( 'product_cat' === $taxonomy ) {
		annam_footer_flush_tour_cat_cache();
	}
}
add_action( 'delete_term', 'annam_footer_on_product_cat_term_deleted', 10, 5 );

/**
 * Khi lưu sản phẩm (ảnh hưởng hide_empty / gán danh mục).
 *
 * @param int $post_id Post ID.
 */
function annam_footer_on_product_save_flush_tour_cache( $post_id ) {
	if ( 'product' === get_post_type( $post_id ) ) {
		annam_footer_flush_tour_cat_cache();
	}
}
add_action( 'save_post_product', 'annam_footer_on_product_save_flush_tour_cache', 20 );

/**
 * Truy vấn danh mục cha product_cat (không cache): có sản phẩm (kể cả qua danh mục con), gồm cả danh mục mặc định WC.
 * Thứ tự giống Sản phẩm → Danh mục: orderby menu_order (WooCommerce map sang meta `order` + LEFT JOIN).
 * Muốn loại một số term: dùng filter `annam_footer_tour_product_cat_query_args` (ví dụ `exclude`).
 *
 * @return array<int, array{label:string,url:string}>
 */
function annam_footer_query_parent_product_cat_rows() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$args = array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => true,
		'pad_counts' => true,
		'orderby'    => 'menu_order',
		'order'      => 'ASC',
	);

	$args = apply_filters( 'annam_footer_tour_product_cat_query_args', $args );

	$terms = get_terms( $args );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	$rows = array();
	foreach ( $terms as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}
		$url = get_term_link( $term );
		if ( is_wp_error( $url ) ) {
			continue;
		}
		$rows[] = array(
			'label' => $term->name,
			'url'   => $url,
		);
	}

	return $rows;
}

/**
 * Danh sách danh mục cha (có cache transient).
 *
 * @return array<int, array{label:string,url:string}>
 */
function annam_footer_get_parent_product_cat_rows_cached() {
	$use_cache = (bool) apply_filters( 'annam_footer_tour_cat_cache_enabled', true );
	$key       = annam_footer_tour_cat_cache_key();

	if ( $use_cache ) {
		$cached = get_transient( $key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$rows = annam_footer_query_parent_product_cat_rows();

	if ( $use_cache ) {
		$ttl = empty( $rows ) ? 5 * MINUTE_IN_SECONDS : 30 * MINUTE_IN_SECONDS;
		set_transient( $key, $rows, $ttl );
	}

	return $rows;
}

/**
 * URL trang tổng hợp tour (mặc định /tours/).
 *
 * @return string
 */
function annam_footer_get_tours_archive_url() {
	$url = home_url( '/tours/' );
	return apply_filters( 'annam_footer_tours_archive_url', $url );
}

/**
 * Fallback khi không có WooCommerce hoặc không có danh mục hiển thị.
 *
 * @return array<int, array{label:string,url:string,variant?:string}>
 */
function annam_footer_get_tour_links_fallback() {
	return array(
		array(
			'label'   => __( 'Xem thêm tour', 'generatepress_child' ),
			'url'     => annam_footer_get_tours_archive_url(),
			'variant' => 'more',
		),
	);
}

/**
 * Danh sách tour phổ biến: tối đa 7 danh mục cha WooCommerce + "Xem thêm tour" nếu còn danh mục.
 *
 * @return array<int, array{label:string,url:string,variant?:string}>
 */
function annam_footer_get_tour_links() {
	if ( ! class_exists( 'WooCommerce' ) || ! taxonomy_exists( 'product_cat' ) ) {
		return apply_filters( 'annam_footer_tour_links', annam_footer_get_tour_links_fallback(), array(), annam_footer_get_tours_archive_url() );
	}

	$all_rows = annam_footer_get_parent_product_cat_rows_cached();
	$all_rows = apply_filters( 'annam_footer_tour_product_cat_rows', $all_rows );

	$more_url = annam_footer_get_tours_archive_url();
	$limit    = (int) apply_filters( 'annam_footer_tour_category_limit', 7 );
	$limit    = max( 1, min( 20, $limit ) );

	$total = count( $all_rows );
	$out   = array();

	for ( $i = 0; $i < min( $limit, $total ); $i++ ) {
		$out[] = array(
			'label' => $all_rows[ $i ]['label'],
			'url'   => $all_rows[ $i ]['url'],
		);
	}

	if ( $total > $limit ) {
		$out[] = array(
			'label'   => __( 'Xem thêm tour', 'generatepress_child' ),
			'url'     => $more_url,
			'variant' => 'more',
		);
	}

	if ( empty( $out ) ) {
		$out = annam_footer_get_tour_links_fallback();
	}

	return apply_filters( 'annam_footer_tour_links', $out, $all_rows, $more_url );
}

/**
 * Cột “Thông tin” (link nội bộ + filter).
 *
 * @return array<int, array{label:string,url:string}>
 */
function annam_footer_get_info_links() {
	$links = array(
		array(
			'label' => __( 'Về chúng tôi', 'generatepress_child' ),
			'url'   => home_url( '/gioi-thieu/' ),
		),
		array(
			'label' => __( 'Điều khoản dịch vụ', 'generatepress_child' ),
			'url'   => home_url( '/terms-of-service/' ),
		),
		array(
			'label' => __( 'Chính sách bảo mật', 'generatepress_child' ),
			'url'   => home_url( '/privacy-policy/' ),
		),
		array(
			'label' => __( 'Chính sách Đổi trả & Hoàn tiền', 'generatepress_child' ),
			'url'   => home_url( '/chinh-sach-hoan-huy/' ),
		),
		array(
			'label' => __( 'Câu chuyện & cảm hứng', 'generatepress_child' ),
			'url'   => home_url( '/tin-tuc/' ),
		),
		array(
			'label' => __( 'Liên hệ với chúng tôi', 'generatepress_child' ),
			'url'   => home_url( '/lien-he/' ),
		),
	);

	return apply_filters( 'annam_footer_info_links', $links );
}

/**
 * Liên kết mạng xã hội.
 *
 * @return array<int, array{label:string,url:string,key:string}>
 */
function annam_footer_get_social_links() {
	$links = array(
		array(
			'key'   => 'facebook',
			'label' => __( 'Facebook', 'generatepress_child' ),
			'url'   => 'https://web.facebook.com/AnNamDiscovery/',
		),
		array(
			'key'   => 'twitter',
			'label' => __( 'X / Twitter', 'generatepress_child' ),
			'url'   => 'https://x.com/annamdiscovery',
		),
		array(
			'key'   => 'youtube',
			'label' => __( 'YouTube', 'generatepress_child' ),
			'url'   => 'https://www.youtube.com/@AnNamDiscovery',
		),
		array(
			'key'   => 'instagram',
			'label' => __( 'Instagram', 'generatepress_child' ),
			'url'   => 'https://www.instagram.com/annamdiscovery/',
		),
	);

	return apply_filters( 'annam_footer_social_links', $links );
}

/**
 * Gỡ footer mặc định GeneratePress (widgets + copyright) để tránh trùng.
 */
function annam_footer_remove_generatepress_footer_parts() {
	remove_action( 'generate_footer', 'generate_construct_footer_widgets', 5 );
	remove_action( 'generate_footer', 'generate_construct_footer', 10 );
}
add_action( 'after_setup_theme', 'annam_footer_remove_generatepress_footer_parts', 20 );

/**
 * In footer HTML tùy chỉnh.
 */
function annam_footer_render_custom() {
	get_template_part( 'template-parts/footer/site-footer-custom' );
}
add_action( 'generate_footer', 'annam_footer_render_custom', 10 );

/**
 * Body class khi dùng footer custom.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function annam_footer_body_class( $classes ) {
	$classes[] = 'annam-custom-site-footer';
	return $classes;
}
add_filter( 'body_class', 'annam_footer_body_class', 12 );

/**
 * Enqueue CSS footer.
 */
function annam_footer_enqueue_assets() {
	if ( is_admin() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$css = $dir . '/assets/css/footer-custom.css';

	if ( ! file_exists( $css ) ) {
		return;
	}

	wp_enqueue_style(
		'annam-footer-custom',
		$uri . '/assets/css/footer-custom.css',
		array( 'annam-design-tokens' ),
		(string) filemtime( $css )
	);

	if (
		apply_filters( 'annam_footer_show_ecosystem', true )
		&& function_exists( 'annam_get_ecosystem_items' )
		&& ! empty( annam_get_ecosystem_items() )
		&& function_exists( 'annam_ecosystem_get_section_html' )
	) {
		$eco = $dir . '/assets/css/home-ecosystem.css';
		if ( file_exists( $eco ) && ! wp_style_is( 'annam-home-ecosystem', 'enqueued' ) && ! wp_style_is( 'annam-home-ecosystem', 'done' ) ) {
			wp_enqueue_style(
				'annam-home-ecosystem',
				$uri . '/assets/css/home-ecosystem.css',
				array( 'annam-design-tokens', 'annam-footer-custom' ),
				(string) filemtime( $eco )
			);
		}
		if ( function_exists( 'annam_ecosystem_enqueue_slider_script' ) ) {
			annam_ecosystem_enqueue_slider_script();
		}
	}
}
add_action( 'wp_enqueue_scripts', 'annam_footer_enqueue_assets', 25 );
