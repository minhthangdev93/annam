<?php
/**
 * Product archive template (shop, categories, tags) — tour layout.
 *
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 */
do_action( 'woocommerce_before_main_content' );
?>

<main class="annam-woo-category-page">
	<?php wc_print_notices(); ?>
	<?php get_template_part( 'template-parts/woocommerce/category', 'hero' ); ?>
	<?php get_template_part( 'template-parts/woocommerce/category', 'intro' ); ?>
	<?php get_template_part( 'template-parts/woocommerce/category', 'products' ); ?>
	<?php
	$annam_show_recently_viewed_archive = false;
	if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
		$annam_show_recently_viewed_archive = true;
	} elseif ( ( function_exists( 'is_product_category' ) && is_product_category() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() ) ) {
		$annam_show_recently_viewed_archive = true;
	}
	?>
	<?php if ( function_exists( 'annam_recently_viewed_render_section' ) && $annam_show_recently_viewed_archive ) : ?>
		<?php annam_recently_viewed_render_section( 0 ); ?>
	<?php endif; ?>
	<?php if ( function_exists( 'is_product_category' ) && is_product_category() ) : ?>
		<?php get_template_part( 'template-parts/woocommerce/category', 'showcase' ); ?>
	<?php endif; ?>
</main>

<?php
/**
 * Hook: woocommerce_after_main_content.
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
