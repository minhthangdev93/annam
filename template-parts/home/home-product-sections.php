<?php
/**
 * Trang chủ: các section sản phẩm theo product_cat bật “Hiển thị trên trang chủ”.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'annam_get_product_categories_for_home_sections' ) || ! class_exists( 'WooCommerce' ) ) {
	return;
}

$terms = annam_get_product_categories_for_home_sections();
if ( empty( $terms ) ) {
	return;
}

$sections_out = 0;
ob_start();
foreach ( $terms as $term ) :
	if ( ! $term instanceof WP_Term ) {
		continue;
	}

	$per_page = (int) apply_filters( 'annam_home_section_posts_per_page', 8 );
	if ( $per_page < 1 ) {
		$per_page = 8;
	}
	if ( $per_page > 24 ) {
		$per_page = 24;
	}

	$term_id = (int) $term->term_id;
	$gen     = (int) get_option( 'annam_home_sections_cache_gen', 1 );
	$ckey    = 'annam_hsec_' . $term_id . '_' . $gen;
	$pids    = get_transient( $ckey );

	if ( false === $pids ) {
		$q_ids = new WP_Query(
			array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'posts_per_page'      => $per_page,
				'fields'              => 'ids',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'orderby'             => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
				'tax_query'           => array(
					array(
						'taxonomy'         => 'product_cat',
						'field'            => 'term_id',
						'terms'            => $term_id,
						'include_children' => true,
					),
				),
			)
		);
		$pids = is_array( $q_ids->posts ) ? array_map( 'absint', $q_ids->posts ) : array();
		wp_reset_postdata();
		if ( ! empty( $pids ) ) {
			set_transient( $ckey, $pids, 15 * MINUTE_IN_SECONDS );
		}
	} else {
		$pids = is_array( $pids ) ? array_map( 'absint', $pids ) : array();
	}

	if ( empty( $pids ) ) {
		continue;
	}

	$q = new WP_Query(
		array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'post__in'            => $pids,
			'orderby'             => 'post__in',
			'posts_per_page'      => count( $pids ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	if ( ! $q->have_posts() ) {
		wp_reset_postdata();
		continue;
	}

	$count        = (int) $q->post_count;
	$is_carousel = $count > 4;
	$term_link    = get_term_link( $term );
	if ( is_wp_error( $term_link ) ) {
		continue;
	}

	$title_id = 'annam-home-section-' . (int) $term->term_id;
	$desc     = function_exists( 'annam_get_product_cat_home_section_excerpt' )
		? annam_get_product_cat_home_section_excerpt( $term )
		: '';

	$section_class = 'annam-home-product-section annam-woo-category-page';
	if ( $is_carousel ) {
		$section_class .= ' annam-home-product-section--carousel annam-home-product-section--feature-carousel';
	} else {
		$section_class .= ' annam-home-product-section--grid';
	}

	++$sections_out;
	?>
	<section class="<?php echo esc_attr( $section_class ); ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
		<div class="annam-home-product-section__inner">
			<header class="annam-home-product-section__header">
				<h2 id="<?php echo esc_attr( $title_id ); ?>" class="annam-home-product-section__title"><?php echo esc_html( $term->name ); ?></h2>
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
								'columns'       => 4,
								'name'          => 'annam-home-section',
								'is_paginated'  => false,
								'total'         => $count,
								'total_pages'   => 1,
								'per_page'      => $count,
								'current_page'  => 1,
								'current_loop'  => 0,
							)
						);
						wc_set_loop_prop( 'annam_home_section', true );

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
					<div class="annam-home-product-section__progress" aria-hidden="true">
						<span class="annam-home-product-section__progress-bar"></span>
					</div>
				<?php endif; ?>
				<?php if ( $is_carousel ) : ?>
					<button type="button" class="annam-home-product-section__nav annam-home-product-section__nav--next" aria-label="<?php esc_attr_e( 'Cuộn phải', 'generatepress_child' ); ?>">
						<span aria-hidden="true">›</span>
					</button>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
endforeach;

$html = ob_get_clean();
if ( $sections_out < 1 ) {
	return;
}
?>
<div class="annam-home-product-sections">
	<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sections built with escaped output above ?>
</div>
