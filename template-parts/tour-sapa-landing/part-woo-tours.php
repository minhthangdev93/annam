<?php
/**
 * Slider tất cả tour/combo Woo danh mục Tour Sapa — trước CTA cuối.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'annam_cabin_landing_query_tour_sapa_products' ) || ! function_exists( 'annam_cabin_landing_get_tour_sapa_term' ) ) {
	return;
}

$config      = function_exists( 'annam_tour_sapa_landing_get_config' ) ? annam_tour_sapa_landing_get_config() : array();
$related_cfg = isset( $config['related_tours'] ) && is_array( $config['related_tours'] ) ? $config['related_tours'] : array();
$limit       = array_key_exists( 'limit', $related_cfg ) ? (int) $related_cfg['limit'] : -1;
$title       = ! empty( $related_cfg['title'] ) ? (string) $related_cfg['title'] : __( 'Tour & Combo Sapa khác', 'generatepress_child' );

$term = annam_cabin_landing_get_tour_sapa_term();
$q    = annam_cabin_landing_query_tour_sapa_products( $limit );

if ( ! $term || ! $q ) {
	return;
}

$count       = (int) $q->post_count;
$is_carousel = $count > 1;
$term_link   = get_term_link( $term );
if ( is_wp_error( $term_link ) ) {
	$term_link = home_url( '/tour-sapa/' );
}

$title_id = 'annam-tour-sapa-woo-tours-title';
$desc     = function_exists( 'annam_get_product_cat_home_section_excerpt' )
	? annam_get_product_cat_home_section_excerpt( $term )
	: '';

$section_class = 'annam-home-product-section annam-woo-category-page annam-tour-sapa-woo-tours-section';
if ( $is_carousel ) {
	$section_class .= ' annam-home-product-section--carousel';
} else {
	$section_class .= ' annam-home-product-section--grid';
}
?>
<section class="annam-tour-sapa-section annam-tour-sapa-section--woo-tours" id="tour-sapa-woo" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
	<div class="annam-tour-sapa-container annam-tour-sapa-woo-tours-wrap">
		<section class="<?php echo esc_attr( $section_class ); ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
			<div class="annam-home-product-section__inner">
				<header class="annam-home-product-section__header">
					<h2 id="<?php echo esc_attr( $title_id ); ?>" class="annam-home-product-section__title"><?php echo esc_html( $title ); ?></h2>
					<a class="annam-home-product-section__view-all" href="<?php echo esc_url( $term_link ); ?>">
						<?php esc_html_e( 'Xem tất cả', 'generatepress_child' ); ?> <span aria-hidden="true">→</span>
					</a>
				</header>
				<?php if ( '' !== $desc ) : ?>
					<p class="annam-home-product-section__desc"><?php echo esc_html( $desc ); ?></p>
				<?php endif; ?>

				<div class="annam-home-product-section__slider" <?php echo $is_carousel ? ' data-annam-section-slider="1"' : ''; ?>>
					<?php if ( $is_carousel ) : ?>
						<button type="button" class="annam-home-product-section__nav annam-home-product-section__nav--prev" aria-label="<?php esc_attr_e( 'Cuộn trái', 'generatepress_child' ); ?>">
							<span aria-hidden="true">‹</span>
						</button>
					<?php endif; ?>
					<div class="annam-home-product-section__viewport">
						<ul class="products columns-4 annam-home-product-section__grid">
							<?php
							wc_setup_loop(
								array(
									'columns'      => 4,
									'name'         => 'annam-tour-sapa-landing-woo',
									'is_paginated' => false,
									'total'        => $count,
									'total_pages'  => 1,
									'per_page'     => $count,
									'current_page' => 1,
									'current_loop' => 0,
								)
							);
							wc_set_loop_prop( 'annam_home_section', true );
							wc_set_loop_prop( 'annam_tour_sapa_landing_woo', true );

							while ( $q->have_posts() ) :
								$q->the_post();
								wc_get_template_part( 'content', 'product' );
							endwhile;

							wc_reset_loop();
							wp_reset_postdata();
							?>
						</ul>
					</div>
					<?php if ( $is_carousel ) : ?>
						<button type="button" class="annam-home-product-section__nav annam-home-product-section__nav--next" aria-label="<?php esc_attr_e( 'Cuộn phải', 'generatepress_child' ); ?>">
							<span aria-hidden="true">›</span>
						</button>
					<?php endif; ?>
				</div>
			</div>
		</section>
	</div>
</section>
