<?php
/**
 * Product reviews: custom summary + modal; native WooCommerce comment list.
 *
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product || ! comments_open() ) {
	return;
}

?>
<div id="annam-product-reviews" class="annam-product-reviews-wrap">
<div id="reviews" class="woocommerce-Reviews annam-woocommerce-reviews">
	<?php get_template_part( 'template-parts/woocommerce/single-product/review', 'block' ); ?>

	<div id="comments" class="annam-woocommerce-reviews__list-wrap">
		<?php if ( have_comments() ) : ?>
			<ol class="commentlist annam-review-commentlist">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
			</ol>

			<?php
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination annam-review-pagination">';
				paginate_comments_links(
					apply_filters(
						'woocommerce_comment_pagination_args',
						array(
							'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
							'next_text' => is_rtl() ? '&larr;' : '&rarr;',
							'type'      => 'list',
						)
					)
				);
				echo '</nav>';
			endif;
			?>
		<?php endif; ?>
	</div>

	<div class="clear"></div>

	<div id="annam-review-image-lightbox" class="annam-review-lightbox" hidden aria-hidden="true">
		<div class="annam-review-lightbox__backdrop" data-annam-lightbox-close tabindex="-1"></div>
		<div class="annam-review-lightbox__inner" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Xem ảnh đánh giá', 'generatepress_child' ); ?>">
			<button type="button" class="annam-review-lightbox__close" data-annam-lightbox-close aria-label="<?php esc_attr_e( 'Đóng', 'generatepress_child' ); ?>">×</button>
			<button type="button" class="annam-review-lightbox__nav annam-review-lightbox__prev" data-annam-lightbox-prev aria-label="<?php esc_attr_e( 'Ảnh trước', 'generatepress_child' ); ?>">‹</button>
			<div class="annam-review-lightbox__stage">
				<img class="annam-review-lightbox__img" src="" alt="" decoding="async" />
			</div>
			<button type="button" class="annam-review-lightbox__nav annam-review-lightbox__next" data-annam-lightbox-next aria-label="<?php esc_attr_e( 'Ảnh sau', 'generatepress_child' ); ?>">›</button>
		</div>
	</div>
</div>
</div>
