<?php
/**
 * Template Name: Liên hệ An Nam Discovery
 * Template Post Type: page
 * Description: Trang liên hệ — hero, thẻ liên hệ nhanh, form tư vấn, hỗ trợ, bản đồ/online, FAQ.
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
				get_template_part( 'template-parts/contact/contact', 'page' );
			endwhile;

			do_action( 'generate_after_main_content' );
			?>
		</main>
	</div>

	<?php
	do_action( 'generate_after_primary_content_area' );

	generate_construct_sidebars();

	get_footer();
