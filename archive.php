<?php
/**
 * Archive bài viết (category, tag, date, author, …) — layout card ngang.
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
								'loop_context' => 'archive',
							)
						);
					} else {
						do_action( 'generate_archive_title' );
						do_action( 'generate_before_loop', 'archive' );
						while ( have_posts() ) :
							the_post();
							generate_do_template_part( 'archive' );
						endwhile;
						do_action( 'generate_after_loop', 'archive' );
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
