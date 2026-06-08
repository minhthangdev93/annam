<?php
/**
 * Single post (cẩm nang): assets, helpers, tránh trùng hero GP.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL trang danh sách “Cẩm nang du lịch” (Posts page hoặc trang chủ).
 *
 * @return string
 */
function annam_single_cam_nang_list_url() {
	$page_for_posts = (int) get_option( 'page_for_posts' );
	$url = home_url( '/' );
	if ( $page_for_posts > 0 ) {
		$url = get_permalink( $page_for_posts );
	}

	return apply_filters( 'annam_single_cam_nang_list_url', $url, $page_for_posts );
}

/**
 * Lấy các term product_cat cho block dưới bài viết.
 * Dùng cùng resolver động với home/category showcase để tránh lệch dữ liệu khi slug bị đổi ở admin.
 *
 * @return WP_Term[]
 */
function annam_single_get_tour_category_terms() {
	if ( ! taxonomy_exists( 'product_cat' ) || ! function_exists( 'annam_resolve_category_showcase_terms' ) ) {
		return array();
	}

	$terms = annam_resolve_category_showcase_terms();

	/**
	 * Cho phép tùy biến danh sách term riêng cho block single post,
	 * nhưng mặc định vẫn dùng cùng nguồn động với các showcase khác.
	 *
	 * @param WP_Term[] $terms Resolved terms.
	 */
	$terms = apply_filters( 'annam_single_tour_category_terms', $terms );

	if ( ! is_array( $terms ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$terms,
			static function ( $term ) {
				return $term instanceof WP_Term && 'product_cat' === $term->taxonomy;
			}
		)
	);
}

/**
 * Ảnh hero khi bài không có featured image.
 *
 * @return string URL.
 */
function annam_single_post_hero_fallback_image_url() {
	if ( function_exists( 'annam_blog_hero_fallback_background_url' ) ) {
		return annam_blog_hero_fallback_background_url();
	}
	$uri = get_stylesheet_directory_uri();
	return $uri . '/assets/images/cam-nang-hero-bg.svg';
}

/**
 * Chèn box CTA sau khoảng 40% nội dung HTML (sau thẻ </p> gần vị trí đó).
 *
 * @param string $html Nội dung đã qua filter the_content.
 * @return string
 */
function annam_single_insert_cta_into_html( $html ) {
	$html = (string) $html;
	if ( '' === trim( $html ) ) {
		return $html;
	}

	$cta = annam_single_get_cta_box_markup();
	$len = strlen( $html );
	if ( $len < 350 ) {
		return $html . $cta;
	}

	$target = (int) floor( $len * 0.4 );
	$pos    = strpos( $html, '</p>', $target );
	if ( false === $pos ) {
		$pos = strrpos( substr( $html, 0, $target ), '</p>' );
	}
	if ( false === $pos ) {
		return $html . $cta;
	}
	$insert = $pos + 4;
	return substr( $html, 0, $insert ) . $cta . substr( $html, $insert );
}

/**
 * Markup CTA (HTML an toàn, link đã escape).
 *
 * @return string
 */
function annam_single_get_cta_box_markup() {
	$zalo = 'https://zalo.me/0942471111';
	$tel  = 'tel:19008164';

	ob_start();
	?>
	<div class="annam-single-cta" role="note">
		<p class="annam-single-cta__title"><?php echo esc_html__( 'Bạn cần tư vấn tour phù hợp?', 'generatepress_child' ); ?></p>
		<p class="annam-single-cta__text"><?php echo esc_html__( 'An Nam Discovery hỗ trợ chọn tour, du thuyền và combo theo nhu cầu.', 'generatepress_child' ); ?></p>
		<div class="annam-single-cta__actions">
			<a class="annam-single-cta__btn annam-single-cta__btn--primary" href="<?php echo esc_url( $zalo ); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html__( 'Tư vấn Zalo', 'generatepress_child' ); ?></a>
			<a class="annam-single-cta__btn annam-single-cta__btn--outline" href="<?php echo esc_url( $tel ); ?>"><?php echo esc_html__( 'Gọi ngay', 'generatepress_child' ); ?></a>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Tắt hero ảnh mặc định của GeneratePress trong single post (tránh trùng với hero tùy chỉnh).
 */
function annam_single_remove_gp_inside_featured() {
	if ( is_singular( 'post' ) ) {
		remove_action( 'generate_before_content', 'generate_featured_page_header_inside_single', 10 );
	}
}
add_action( 'wp', 'annam_single_remove_gp_inside_featured', 9 );

/**
 * Body class căn layout single post.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function annam_single_post_body_class( $classes ) {
	if ( ! is_admin() && is_singular( 'post' ) ) {
		$classes[] = 'annam-single-post-layout';
	}
	return $classes;
}
add_filter( 'body_class', 'annam_single_post_body_class' );

/**
 * Bỏ trường Website khỏi form bình luận (single bài viết).
 *
 * @param array<string,string> $fields Các field mặc định của comment_form.
 * @return array<string,string>
 */
function annam_single_post_comment_form_remove_url_field( $fields ) {
	if ( is_admin() || ! is_singular( 'post' ) ) {
		return $fields;
	}
	if ( isset( $fields['url'] ) ) {
		unset( $fields['url'] );
	}
	return $fields;
}
add_filter( 'comment_form_default_fields', 'annam_single_post_comment_form_remove_url_field', 20 );

/**
 * Tắt meta chuyên mục (GeneratePress footer entry-meta: icon + link category) trên single bài viết.
 *
 * @param bool $show Giá trị mặc định từ theme.
 * @return bool
 */
function annam_single_post_hide_gp_categories_meta( $show ) {
	if ( ! is_admin() && is_singular( 'post' ) ) {
		return false;
	}
	return $show;
}
add_filter( 'generate_show_categories', 'annam_single_post_hide_gp_categories_meta', 10, 1 );

/**
 * Ẩn điều hướng bài trước/sau của GeneratePress (footer entry meta) chỉ trên single post.
 * Dùng hook filter, không ảnh hưởng phân trang archive hay breadcrumb.
 *
 * @param bool $show Giá trị mặc định từ theme.
 * @return bool
 */
function annam_single_post_hide_gp_post_navigation( $show ) {
	if ( ! is_admin() && is_singular( 'post' ) ) {
		return false;
	}
	return $show;
}
add_filter( 'generate_show_post_navigation', 'annam_single_post_hide_gp_post_navigation', 25 );

/**
 * Ẩn breadcrumb Rank Math trên single bài viết (đã có breadcrumb trong annam-single-hero).
 *
 * @param string $html HTML breadcrumb.
 * @return string
 */
function annam_single_post_strip_rank_math_breadcrumb_html( $html ) {
	if ( ! is_admin() && is_singular( 'post' ) ) {
		return '';
	}
	return (string) $html;
}
add_filter( 'rank_math/frontend/breadcrumb/html', 'annam_single_post_strip_rank_math_breadcrumb_html', 20 );

/**
 * Không render shortcode [rank_math_breadcrumb] trên single post (Element / nội dung hook).
 *
 * @param false|string $return Chuỗi thay thế hoặc false để chạy shortcode gốc.
 * @param string       $tag    Tên shortcode.
 * @return false|string
 */
function annam_single_post_strip_rank_math_breadcrumb_shortcode( $return, $tag ) {
	if ( ! is_admin() && 'rank_math_breadcrumb' === $tag && is_singular( 'post' ) ) {
		return '';
	}
	return $return;
}
add_filter( 'pre_do_shortcode_tag', 'annam_single_post_strip_rank_math_breadcrumb_shortcode', 10, 2 );

/**
 * Enqueue CSS single post + card danh mục tour.
 */
function annam_single_post_enqueue_assets() {
	if ( is_admin() || ! is_singular( 'post' ) ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$deps = array( 'annam-design-tokens' );

	$showcase = $dir . '/assets/css/woo-category-showcase.css';
	if ( file_exists( $showcase ) ) {
		wp_enqueue_style(
			'annam-category-showcase',
			$uri . '/assets/css/woo-category-showcase.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $showcase )
		);
		$deps[] = 'annam-category-showcase';
	}

	$css = $dir . '/assets/css/single-post.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-single-post',
			$uri . '/assets/css/single-post.css',
			$deps,
			(string) filemtime( $css )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_single_post_enqueue_assets', 22 );
