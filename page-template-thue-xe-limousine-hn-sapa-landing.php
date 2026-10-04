<?php
/**
 * Template Name: Landing Thuê Xe Limousine HN–Sapa
 * Template Post Type: page
 * Description: Landing Ads thuê nguyên xe limousine 9 & 11 chỗ Hà Nội ⇄ Sapa (có tài xế).
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
				get_template_part( 'template-parts/limo-charter-landing/landing', 'page' );
			endwhile;

			do_action( 'generate_after_main_content' );
			?>
		</main>
	</div>

	<?php
	do_action( 'generate_after_primary_content_area' );
	get_footer();
