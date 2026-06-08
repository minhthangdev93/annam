<?php
/**
 * Template Name: Trang chủ (tĩnh)
 * Template Post Type: page
 * Description: Gán cho trang tĩnh làm trang chủ trong Cài đặt → Đọc, hoặc chọn template này trong thuộc tính trang.
 *
 * @package GeneratePress_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'annam-page-trang-chu';
		return $classes;
	}
);

add_filter(
	'generate_show_entry_header',
	static function ( $show ) {
		if ( ! is_singular( 'page' ) ) {
			return $show;
		}
		$page_id = get_queried_object_id();
		if ( ! $page_id ) {
			return $show;
		}
		return 'page-template-trang-chu.php' === get_page_template_slug( $page_id ) ? false : $show;
	},
	5
);

if ( function_exists( 'annam_enqueue_trang_chu_template_assets' ) ) {
	annam_enqueue_trang_chu_template_assets();
}

get_header();
?>

	<div <?php generate_do_attr( 'content' ); ?>>
		<main <?php generate_do_attr( 'main' ); ?>>
			<?php
			/**
			 * generate_before_main_content hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_before_main_content' );

			if ( generate_has_default_loop() ) {
				while ( have_posts() ) :

					the_post();

					get_template_part( 'template-parts/home/home', 'top-hero-cats' );

					get_template_part( 'template-parts/home/home', 'product-sections' );

					if ( function_exists( 'annam_recently_viewed_render_section' ) ) {
						annam_recently_viewed_render_section( 0 );
					}

					generate_do_template_part( 'page' );

				endwhile;
			}

			/**
			 * generate_after_main_content hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_after_main_content' );
			?>
		</main>
	</div>

	<?php
	/**
	 * generate_after_primary_content_area hook.
	 *
	 * @since 2.0
	 */
	do_action( 'generate_after_primary_content_area' );

	generate_construct_sidebars();

	get_footer();
