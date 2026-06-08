<?php
/**
 * Product loop item: tour card on shop/category/tag; default hooks elsewhere.
 *
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

if ( function_exists( 'annam_is_tour_card_loop_context' ) && annam_is_tour_card_loop_context() ) {
	$permalink = get_permalink( $product->get_id() );
	$duration  = annam_get_tour_meta( $product->get_id(), '_tour_duration' );
	$schedule  = annam_get_tour_meta( $product->get_id(), '_tour_schedule' );
	?>
	<li <?php wc_product_class( 'annam-tour-card-wrap', $product ); ?>>
		<div class="annam-tour-card__surface">
			<article class="annam-tour-card">
				<a class="annam-tour-card__media-link" href="<?php echo esc_url( $permalink ); ?>">
					<div class="annam-tour-card__image">
						<?php
						$img_size = function_exists( 'annam_get_product_card_image_size' ) ? annam_get_product_card_image_size() : 'woocommerce_thumbnail';
						echo $product->get_image(
							$img_size,
							array(
								'class'    => 'annam-tour-card__product-img',
								'loading'  => 'lazy',
								'decoding' => 'async',
							)
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						if ( function_exists( 'annam_tour_product_has_card_promo_price' ) && annam_tour_product_has_card_promo_price( $product ) ) {
							$pct = function_exists( 'annam_tour_product_card_discount_percent' ) ? annam_tour_product_card_discount_percent( $product ) : null;
							if ( null !== $pct ) {
								/* translators: %d: discount percentage */
								$badge_text = sprintf( __( 'Giảm %d%%', 'generatepress_child' ), $pct );
								/* translators: %d: discount percentage */
								$badge_label = sprintf( __( 'Đang giảm giá %d%%', 'generatepress_child' ), $pct );
							} else {
								$badge_text  = __( 'Giảm giá', 'generatepress_child' );
								$badge_label = __( 'Đang giảm giá', 'generatepress_child' );
							}
							echo '<span class="annam-tour-card__sale-badge" aria-hidden="true">' . esc_html( $badge_text ) . '</span>';
							echo '<span class="screen-reader-text">' . esc_html( $badge_label ) . '</span>';
						}
						?>
					</div>
				</a>
				<div class="annam-tour-card__body">
					<h3 class="annam-tour-card__title">
						<a class="annam-tour-card__title-link" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
					</h3>
					<div class="annam-tour-card__rating"><?php echo annam_render_tour_card_rating_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php
					$annam_tour_price = annam_tour_price_block_html( $product );
					$annam_has_meta   = (bool) ( $duration || $schedule );
					$annam_has_price  = ( '' !== $annam_tour_price );
					?>
					<a class="annam-tour-card__foot-link" href="<?php echo esc_url( $permalink ); ?>">
						<?php if ( $annam_has_price ) : ?>
							<div class="annam-tour-card__price">
								<?php echo wp_kses_post( $annam_tour_price ); ?>
							</div>
						<?php endif; ?>
						<?php if ( $annam_has_meta ) : ?>
							<div class="annam-tour-card__meta">
								<?php if ( $duration ) : ?>
									<div class="annam-tour-card__meta-item annam-tour-card__meta-item--duration">
										<span class="annam-tour-card__meta-icon annam-tour-card__meta-icon--calendar" aria-hidden="true"></span>
										<span class="annam-tour-card__meta-text"><?php echo esc_html( $duration ); ?></span>
									</div>
								<?php endif; ?>
								<?php if ( $schedule ) : ?>
									<div class="annam-tour-card__meta-item annam-tour-card__meta-item--schedule">
										<span class="annam-tour-card__meta-icon annam-tour-card__meta-icon--clock" aria-hidden="true"></span>
										<span class="annam-tour-card__meta-text"><?php echo esc_html( $schedule ); ?></span>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?php if ( ! $annam_has_price && ! $annam_has_meta ) : ?>
							<span class="annam-tour-card__foot-placeholder"><?php echo esc_html__( 'Xem chi tiết', 'generatepress_child' ); ?></span>
						<?php endif; ?>
					</a>
				</div>
			</article>
		</div>
	</li>
	<?php
	return;
}
?>
<li <?php wc_product_class( '', $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>
