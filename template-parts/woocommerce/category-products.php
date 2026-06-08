<?php
/**
 * Product loop for tour archive layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$annam_sort_enabled = function_exists( 'annam_category_sort_is_enabled_view' ) && annam_category_sort_is_enabled_view();
$annam_sort_ctx     = function_exists( 'annam_category_sort_get_archive_context' ) ? annam_category_sort_get_archive_context() : array(
	'taxonomy' => '',
	'term_id'  => 0,
);
$annam_show_title   = is_product_category() || is_product_tag();
?>
<section
	id="annam-category-products"
	class="annam-category-products<?php echo $annam_sort_enabled ? ' annam-category-products--has-sort' : ''; ?>"
	<?php if ( $annam_sort_enabled ) : ?>
		data-annam-sort-taxonomy="<?php echo esc_attr( $annam_sort_ctx['taxonomy'] ); ?>"
		data-annam-sort-term-id="<?php echo esc_attr( (string) $annam_sort_ctx['term_id'] ); ?>"
	<?php endif; ?>
>
	<div class="annam-container grid-container grid-parent annam-category-products__container">
		<?php if ( $annam_show_title || $annam_sort_enabled ) : ?>
			<div class="annam-category-products__toolbar">
				<?php if ( $annam_show_title ) : ?>
					<h2 class="annam-category-products__section-title"><?php echo esc_html__( 'Danh sách tour', 'generatepress_child' ); ?></h2>
				<?php else : ?>
					<span class="annam-category-products__section-title annam-category-products__section-title--placeholder" aria-hidden="true"></span>
				<?php endif; ?>
				<?php if ( $annam_sort_enabled ) : ?>
					<div class="annam-category-products__sort-wrap">
						<select
							id="annam-category-sort"
							class="annam-category-products__sort"
							data-annam-category-sort
							aria-label="<?php echo esc_attr__( 'Sắp xếp danh sách tour', 'generatepress_child' ); ?>"
						>
							<option value="date"><?php echo esc_html__( 'Mới nhất', 'generatepress_child' ); ?></option>
							<option value="price_asc"><?php echo esc_html__( 'Giá: thấp đến cao', 'generatepress_child' ); ?></option>
							<option value="price_desc"><?php echo esc_html__( 'Giá: cao đến thấp', 'generatepress_child' ); ?></option>
						</select>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="annam-category-products__loop" data-annam-category-loop aria-live="polite" aria-busy="false">
			<?php if ( woocommerce_product_loop() ) : ?>
				<?php woocommerce_product_loop_start(); ?>
				<?php if ( wc_get_loop_prop( 'total' ) ) : ?>
					<?php
					while ( have_posts() ) :
						the_post();
						do_action( 'woocommerce_shop_loop' );
						wc_get_template_part( 'content', 'product' );
					endwhile;
					?>
				<?php endif; ?>
				<?php woocommerce_product_loop_end(); ?>
				<?php do_action( 'woocommerce_after_shop_loop' ); ?>
			<?php else : ?>
				<?php do_action( 'woocommerce_no_products_found' ); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
