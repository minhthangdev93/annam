<?php
/**
 * Blog / archive bài viết: layout card ngang + phân trang.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Các trang dùng layout danh sách blog (post).
 */
function annam_is_blog_card_listing() {
	if ( is_admin() ) {
		return false;
	}
	if ( is_page_template( 'page-template-cam-nang-blog.php' ) ) {
		return true;
	}
	if ( is_home() ) {
		return true;
	}
	if ( is_singular() || is_404() || is_search() ) {
		return false;
	}
	if ( is_category() || is_tag() || is_author() || is_date() ) {
		return true;
	}
	if ( is_tax() ) {
		$obj = get_queried_object();
		if ( ! $obj || empty( $obj->taxonomy ) ) {
			return false;
		}
		$tax = get_taxonomy( $obj->taxonomy );
		return $tax && in_array( 'post', (array) $tax->object_type, true );
	}
	if ( is_post_type_archive( 'post' ) ) {
		return true;
	}
	return false;
}

/**
 * ID trang dùng để lấy ảnh đại diện cho hero (Posts page hoặc Page template Cẩm nang).
 *
 * @return int 0 nếu không áp dụng.
 */
function annam_blog_hero_featured_source_post_id() {
	$page_for_posts = (int) get_option( 'page_for_posts' );
	$id             = 0;
	$context        = 'none';

	if ( is_page_template( 'page-template-cam-nang-blog.php' ) ) {
		$id      = (int) get_queried_object_id();
		$context = 'page_template';
	} elseif ( is_home() && ! is_front_page() && $page_for_posts ) {
		$id      = $page_for_posts;
		$context = 'posts_page';
	} elseif ( $page_for_posts && ( is_category() || is_tag() || is_author() || is_date() || is_tax() || is_post_type_archive( 'post' ) ) ) {
		$id      = $page_for_posts;
		$context = 'archive_uses_posts_page';
	}

	return (int) apply_filters( 'annam_blog_hero_featured_source_post_id', $id, $context );
}

/**
 * URL ảnh nền fallback (child theme) khi không có featured image.
 *
 * @return string
 */
function annam_blog_hero_fallback_background_url() {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	if ( file_exists( $dir . '/assets/images/cam-nang-hero-bg.jpg' ) ) {
		return $uri . '/assets/images/cam-nang-hero-bg.jpg';
	}
	if ( file_exists( $dir . '/assets/images/cam-nang-hero-bg.webp' ) ) {
		return $uri . '/assets/images/cam-nang-hero-bg.webp';
	}
	return $uri . '/assets/images/cam-nang-hero-bg.svg';
}

/**
 * URL ảnh nền hero: featured image của trang nguồn, hoặc fallback.
 *
 * @return string
 */
function annam_blog_hero_background_url() {
	$source_id = annam_blog_hero_featured_source_post_id();
	$url       = '';

	if ( $source_id && has_post_thumbnail( $source_id ) ) {
		$thumb = get_the_post_thumbnail_url( $source_id, 'full' );
		if ( $thumb ) {
			$url = $thumb;
		}
	}

	if ( ! $url ) {
		$url = annam_blog_hero_fallback_background_url();
	}

	return apply_filters( 'annam_blog_hero_background_url', $url );
}

/**
 * Ảnh fallback khi bài không có featured image.
 *
 * @return string URL.
 */
function annam_blog_card_placeholder_image_url() {
	static $url = null;
	if ( null !== $url ) {
		return $url;
	}
	$path = get_stylesheet_directory() . '/assets/images/blog-card-placeholder.svg';
	$url  = file_exists( $path ) ? get_stylesheet_directory_uri() . '/assets/images/blog-card-placeholder.svg' : '';
	return $url;
}

/**
 * Phân trang (paginate_links).
 *
 * @param WP_Query|null $query Query tùy chọn (WP_Query custom).
 */
function annam_blog_pagination( $query = null, $pagination_args = array() ) {
	global $wp_query;
	$q = $query instanceof WP_Query ? $query : $wp_query;
	if ( ! $q || $q->max_num_pages < 2 ) {
		return;
	}

	$paged = (int) $q->get( 'paged' );
	if ( $paged < 1 ) {
		$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	}

	$pagination_args = wp_parse_args(
		$pagination_args,
		array(
			'base'   => '',
			'format' => '',
		)
	);

	$paginate_args = array(
		'current'   => max( 1, $paged ),
		'total'     => (int) $q->max_num_pages,
		'type'      => 'list',
		'prev_text' => '<span class="annam-blog-pagination__prev" aria-hidden="true">&larr;</span> ' . esc_html__( 'Trang trước', 'generatepress_child' ),
		'next_text' => esc_html__( 'Trang sau', 'generatepress_child' ) . ' <span class="annam-blog-pagination__next" aria-hidden="true">&rarr;</span>',
		'mid_size'  => 1,
		'end_size'  => 1,
	);

	if ( $pagination_args['base'] ) {
		$paginate_args['base']   = $pagination_args['base'];
		$paginate_args['format'] = $pagination_args['format'] ? $pagination_args['format'] : user_trailingslashit( 'page/%#%', 'paged' );
	} else {
		$big                    = 999999999;
		$paginate_args['base'] = str_replace( (string) $big, '%#%', esc_url( get_pagenum_link( $big ) ) );
	}

	$links = paginate_links( $paginate_args );

	if ( ! $links ) {
		return;
	}

	echo '<nav class="annam-blog-pagination" aria-label="' . esc_attr__( 'Phân trang bài viết', 'generatepress_child' ) . '">';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links returns safe HTML.
	echo $links;
	echo '</nav>';
}

/**
 * Tắt phân trang mặc GeneratePress trên trang blog card (tránh trùng).
 *
 * @param bool $show Hiển thị hay không.
 * @return bool
 */
function annam_blog_disable_generate_post_nav( $show ) {
	if ( annam_is_blog_card_listing() ) {
		return false;
	}
	return $show;
}
add_filter( 'generate_show_post_navigation', 'annam_blog_disable_generate_post_nav', 20 );

/**
 * Body class.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function annam_blog_archive_body_class( $classes ) {
	if ( ! is_admin() && annam_is_blog_card_listing() ) {
		$classes[] = 'annam-has-blog-archive-layout';
	}
	return $classes;
}
add_filter( 'body_class', 'annam_blog_archive_body_class' );

/**
 * Enqueue CSS blog archive.
 */
function annam_blog_archive_enqueue_assets() {
	if ( is_admin() || ! annam_is_blog_card_listing() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$css = $dir . '/assets/css/blog-archive.css';

	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-blog-archive',
			$uri . '/assets/css/blog-archive.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $css )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_blog_archive_enqueue_assets', 20 );
