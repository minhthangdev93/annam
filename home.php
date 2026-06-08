<?php
/**
 * Trang danh sách bài viết (Posts page) — layout card ngang.
 *
 * @package GeneratePress_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

	<div <?php generate_do_attr( 'content' ); ?>>
		<main <?php generate_do_attr( 'main' ); ?>>
			<?php
			do_action( 'generate_before_main_content' );

			if ( function_exists( 'generate_has_default_loop' ) && generate_has_default_loop() ) {
				if ( have_posts() ) {
					if ( function_exists( 'annam_is_blog_card_listing' ) && annam_is_blog_card_listing() ) {
						get_template_part(
							'template-parts/blog/blog',
							'archive-main',
							array(
								'loop_context' => 'index',
							)
						);
					} else {
						do_action( 'generate_before_loop', 'index' );
						while ( have_posts() ) :
							the_post();
							generate_do_template_part( 'index' );
						endwhile;
						do_action( 'generate_after_loop', 'index' );
					}
				} else {
					generate_do_template_part( 'none' );
				}
			}

			do_action( 'generate_after_main_content' );
			?>
		</main>
	</div>

	<?php
	do_action( 'generate_after_primary_content_area' );

	generate_construct_sidebars();

	get_footer();
