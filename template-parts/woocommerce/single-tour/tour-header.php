<?php
/**
 * Single product: breadcrumb, title, gallery (tour header block).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) ) {
	return;
}

$pid         = $product->get_id();
$image_id    = (int) $product->get_image_id();
$gallery_ids = array_map( 'intval', $product->get_gallery_image_ids() );
$gallery_ids = array_values( array_unique( array_filter( $gallery_ids ) ) );

$side_ids = array();
foreach ( $gallery_ids as $gid ) {
	if ( $gid && $gid !== $image_id ) {
		$side_ids[] = $gid;
	}
	if ( count( $side_ids ) >= 2 ) {
		break;
	}
}

$main_id = $image_id;
if ( ! $main_id && ! empty( $side_ids ) ) {
	$main_id  = (int) array_shift( $side_ids );
	$side_ids = array_values( $side_ids );
}

$has_main   = $main_id > 0;
$side_count = count( $side_ids );
$has_side   = $side_count > 0;

$all_image_ids = array();
if ( $main_id ) {
	$all_image_ids[] = $main_id;
}
foreach ( $gallery_ids as $gid ) {
	if ( $gid && ! in_array( $gid, $all_image_ids, true ) ) {
		$all_image_ids[] = $gid;
	}
}
$shown_ids     = array_merge( array( $main_id ), $side_ids );
$shown_ids     = array_values( array_filter( array_unique( $shown_ids ) ) );
$remaining_ids = array_values(
	array_filter(
		$all_image_ids,
		function ( $id ) use ( $shown_ids ) {
			return ! in_array( (int) $id, $shown_ids, true );
		}
	)
);

$show_more_btn = $has_side && count( $remaining_ids ) > 0;

$title      = $product->get_name();
$gallery_sz = function_exists( 'annam_get_tour_gallery_image_size' ) ? annam_get_tour_gallery_image_size() : 'woocommerce_single';

$crumbs   = array();
$crumbs[] = array(
	'label' => __( 'Trang chủ', 'woocommerce' ),
	'url'   => home_url( '/' ),
);
$cats = wp_get_post_terms(
	$pid,
	'product_cat',
	array(
		'orderby' => 'menu_order',
		'order'   => 'ASC',
		'number'  => 1,
	)
);
if ( $cats && ! is_wp_error( $cats ) && ! empty( $cats[0] ) ) {
	$primary = $cats[0];
	$link    = get_term_link( $primary );
	if ( ! is_wp_error( $link ) ) {
		$crumbs[] = array(
			'label' => $primary->name,
			'url'   => $link,
		);
	}
}
$crumbs[] = array(
	'label' => $title,
	'url'   => '',
);
?>
<section class="annam-single-tour" aria-label="<?php echo esc_attr( 'Đầu trang tour' ); ?>">
	<div class="annam-single-tour__container annam-container grid-container grid-parent">
		<nav class="annam-tour-breadcrumb" aria-label="<?php echo esc_attr( 'Breadcrumb' ); ?>">
			<div class="annam-tour-breadcrumb__list">
				<?php foreach ( $crumbs as $i => $c ) : ?>
					<?php if ( $i > 0 ) : ?>
						<span class="annam-tour-breadcrumb__sep" aria-hidden="true">/</span>
					<?php endif; ?>
					<?php if ( '' !== $c['url'] ) : ?>
						<a class="annam-tour-breadcrumb__link" href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['label'] ); ?></a>
					<?php else : ?>
						<span class="annam-tour-breadcrumb__current" aria-current="page"><?php echo esc_html( $c['label'] ); ?></span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</nav>

		<h1 class="annam-tour-title"><?php echo esc_html( $title ); ?></h1>

		<?php
		$review_count = (int) $product->get_review_count();
		if ( $review_count > 0 ) :
			$avg           = (float) $product->get_average_rating();
			$rating_total  = (int) $product->get_rating_count();
			$ratings_on    = ! function_exists( 'wc_review_ratings_enabled' ) || wc_review_ratings_enabled();
			$avg_display   = wc_format_decimal( $avg > 0 ? $avg : 0, 1 );
			$pct           = $ratings_on ? min( 100, max( 0, ( $avg / 5 ) * 100 ) ) : 0;
			$reviews_open  = comments_open( $pid );
			/* translators: 1: average, 2: review count */
			$aria_rating = sprintf(
				__( 'Điểm trung bình %1$s trên 5. %2$s đánh giá.', 'generatepress_child' ),
				$avg_display,
				number_format_i18n( $review_count )
			);
			$count_label = sprintf(
				/* translators: %s: formatted review count */
				__( '%s đánh giá', 'generatepress_child' ),
				number_format_i18n( $review_count )
			);
			?>
			<div class="annam-tour-single-rating" data-rating-total="<?php echo esc_attr( (string) $rating_total ); ?>" data-review-count="<?php echo esc_attr( (string) $review_count ); ?>">
				<div class="annam-tour-single-rating__row" role="group" aria-label="<?php echo esc_attr( $aria_rating ); ?>">
					<?php if ( $ratings_on ) : ?>
						<span class="annam-tour-single-rating__stars annam-review-stars" aria-hidden="true">
							<span class="annam-tour-single-rating__stars-bg">★★★★★</span>
							<span class="annam-tour-single-rating__stars-fg" style="width: <?php echo esc_attr( (string) round( $pct, 2 ) ); ?>%;">★★★★★</span>
						</span>
					<?php endif; ?>
					<span class="annam-tour-single-rating__text">
						<span class="annam-tour-single-rating__score"><?php echo esc_html( $avg_display ); ?>/5</span>
						<span class="annam-tour-single-rating__sep" aria-hidden="true"> · </span>
						<?php if ( $reviews_open ) : ?>
							<a class="annam-tour-single-rating__count annam-tour-single-rating__count--link" href="#annam-product-reviews"><?php echo esc_html( $count_label ); ?></a>
						<?php else : ?>
							<span class="annam-tour-single-rating__count"><?php echo esc_html( $count_label ); ?></span>
						<?php endif; ?>
					</span>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $has_main ) : ?>
			<div class="annam-tour-gallery" id="annam-tour-gallery">
				<?php if ( $has_side ) : ?>
					<div class="annam-tour-gallery__grid annam-tour-gallery__grid--split annam-tour-gallery__grid--side-<?php echo (int) $side_count; ?>">
						<div class="annam-tour-gallery__main annam-tour-gallery__item">
							<button type="button" class="annam-tour-gallery__trigger" aria-label="<?php echo esc_attr__( 'Xem ảnh lớn', 'woocommerce' ); ?>">
								<?php
								echo wp_get_attachment_image(
									$main_id,
									$gallery_sz,
									false,
									annam_single_tour_gallery_img_attrs( $main_id, $title, 'eager', $gallery_sz )
								);
								?>
							</button>
						</div>
						<div class="annam-tour-gallery__side">
							<?php foreach ( $side_ids as $idx => $sid ) : ?>
								<?php
								$is_last    = ( $idx === $side_count - 1 );
								$item_class = 'annam-tour-gallery__item annam-tour-gallery__side-item';
								if ( $is_last ) {
									$item_class .= ' annam-tour-gallery__side-item--last';
								}
								?>
								<div class="<?php echo esc_attr( $item_class ); ?>">
									<button type="button" class="annam-tour-gallery__trigger" aria-label="<?php echo esc_attr__( 'Xem ảnh lớn', 'woocommerce' ); ?>">
										<?php
										echo wp_get_attachment_image(
											$sid,
											$gallery_sz,
											false,
											annam_single_tour_gallery_img_attrs( $sid, $title, 'lazy', $gallery_sz )
										);
										?>
									</button>
									<?php if ( $is_last && $show_more_btn ) : ?>
										<button type="button" class="annam-tour-gallery__more" data-annam-lightbox-more="1" aria-label="<?php echo esc_attr__( 'Xem thêm hình trong thư viện', 'woocommerce' ); ?>">
											<?php echo esc_html__( 'Xem thêm hình', 'woocommerce' ); ?>
										</button>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php else : ?>
					<div class="annam-tour-gallery__grid annam-tour-gallery__grid--single">
						<div class="annam-tour-gallery__item annam-tour-gallery__item--full">
							<button type="button" class="annam-tour-gallery__trigger" aria-label="<?php echo esc_attr__( 'Xem ảnh lớn', 'woocommerce' ); ?>">
								<?php
								echo wp_get_attachment_image(
									$main_id,
									$gallery_sz,
									false,
									annam_single_tour_gallery_img_attrs( $main_id, $title, 'eager', $gallery_sz )
								);
								?>
							</button>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $remaining_ids ) ) : ?>
					<div class="annam-tour-gallery__extras" id="annam-tour-gallery-extras">
						<?php foreach ( $remaining_ids as $rid ) : ?>
							<div class="annam-tour-gallery__extra-item annam-tour-gallery__item">
								<button type="button" class="annam-tour-gallery__trigger" aria-label="<?php echo esc_attr__( 'Xem ảnh lớn', 'woocommerce' ); ?>">
									<?php
									echo wp_get_attachment_image(
										(int) $rid,
										$gallery_sz,
										false,
										annam_single_tour_gallery_img_attrs( (int) $rid, $title, 'lazy', $gallery_sz )
									);
									?>
								</button>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
