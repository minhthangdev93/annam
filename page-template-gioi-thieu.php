<?php
/**
 * Template Name: Giới thiệu An Nam Discovery
 * Template Post Type: page
 * Description: Trang giới thiệu công ty — uy tín, pháp lý, dịch vụ, quy trình, CTA.
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

			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/page/about', 'page' );
			endwhile;

			do_action( 'generate_after_main_content' );
			?>
		</main>
	</div>

	<?php
	do_action( 'generate_after_primary_content_area' );

	generate_construct_sidebars();

	get_footer();
