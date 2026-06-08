<?php
/**
 * Related products: same product_cat, price ASC, tour cards; hidden if none.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$current_id = (int) $product->get_id();
$term       = annam_tour_get_primary_product_cat_term( $current_id );

if ( ! $term instanceof WP_Term ) {
	return;
}

$q = new WP_Query(
	array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => 30,
		'post__not_in'        => array( $current_id ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'tax_query'           => array(
			array(
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => (int) $term->term_id,
				'include_children' => true,
			),
		),
		'meta_key'            => '_price',
		'orderby'             => 'meta_value_num',
		'order'               => 'ASC',
	)
);

if ( ! $q->have_posts() ) {
	return;
}

$count     = (int) $q->post_count;
$carousel  = $count > 4;
$section_classes = array(
	'annam-category-products',
	'annam-tour-related',
);
if ( $carousel ) {
	$section_classes[] = 'annam-tour-related--carousel';
}

wc_set_loop_prop( 'annam_tour_related', true );
wc_set_loop_prop( 'columns', 4 );
?>
<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>" data-annam-tour-related>
	<div class="annam-container grid-container grid-parent">
		<h2 class="annam-tour-related__title">
			<?php echo esc_html( sprintf( '%s đề xuất', $term->name ) ); ?>
		</h2>
		<div class="annam-tour-related__inner">
			<?php if ( $carousel ) : ?>
				<button type="button" class="annam-tour-related__nav annam-tour-related__nav--prev" aria-label="<?php echo esc_attr__( 'Trước', 'generatepress_child' ); ?>" data-annam-related-prev></button>
			<?php endif; ?>
			<div class="annam-tour-related__viewport" data-annam-related-viewport>
				<?php woocommerce_product_loop_start(); ?>
				<?php
				while ( $q->have_posts() ) :
					$q->the_post();
					do_action( 'woocommerce_shop_loop' );
					wc_get_template_part( 'content', 'product' );
				endwhile;
				?>
				<?php woocommerce_product_loop_end(); ?>
			</div>
			<?php if ( $carousel ) : ?>
				<button type="button" class="annam-tour-related__nav annam-tour-related__nav--next" aria-label="<?php echo esc_attr__( 'Tiếp', 'generatepress_child' ); ?>" data-annam-related-next></button>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
wc_reset_loop();
