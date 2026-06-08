<?php
/**
 * Template Name: Landing Cabin VIP (Google Ads)
 * Template Post Type: page
 * Description: Landing chuyển đổi cao — vé xe cabin VIP Hà Nội Sapa / Lào Cai.
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
				get_template_part( 'template-parts/cabin-landing/landing', 'page' );
			endwhile;

			do_action( 'generate_after_main_content' );
			?>
		</main>
	</div>

	<?php
	do_action( 'generate_after_primary_content_area' );
	get_footer();
