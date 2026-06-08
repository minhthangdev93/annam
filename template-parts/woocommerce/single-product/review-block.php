<?php
/**
 * Review summary + modal trigger (single product).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$pid          = (int) $product->get_id();
$title        = $product->get_name();
$avg          = (float) $product->get_average_rating();
$review_count = (int) $product->get_review_count();
$counts       = $product->get_rating_counts();
if ( ! is_array( $counts ) ) {
	$counts = array();
}

$ratings_enabled = function_exists( 'wc_review_ratings_enabled' ) ? wc_review_ratings_enabled() : true;

$dist = array();
for ( $s = 5; $s >= 1; $s-- ) {
	$dist[ $s ] = isset( $counts[ $s ] ) ? (int) $counts[ $s ] : 0;
}

$total_rated = array_sum( $dist );
$pcts        = array();
foreach ( $dist as $s => $n ) {
	$pcts[ $s ] = $total_rated > 0 ? round( 100 * $n / $total_rated ) : 0;
}

$display_avg = $review_count > 0 ? wc_format_decimal( $avg, 1 ) : '—';

$can_submit = true;
$submit_msg = '';
if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'yes' && ! wc_customer_bought_product( '', get_current_user_id(), $pid ) ) {
	$can_submit = false;
	$submit_msg = __( 'Chỉ khách đã mua sản phẩm mới được đánh giá.', 'woocommerce' );
}

if ( $can_submit && function_exists( 'wc_review_ratings_enabled' ) && ! wc_review_ratings_enabled() ) {
	$can_submit = false;
	$submit_msg = __( 'Cửa hàng đang tắt đánh giá theo sao.', 'generatepress_child' );
}

$thumb = $product->get_image_id() ? wp_get_attachment_image_url( (int) $product->get_image_id(), 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );

$labels = array(
	1 => __( 'Rất tệ', 'generatepress_child' ),
	2 => __( 'Tệ', 'generatepress_child' ),
	3 => __( 'Tạm ổn', 'generatepress_child' ),
	4 => __( 'Tốt', 'generatepress_child' ),
	5 => __( 'Rất tốt', 'generatepress_child' ),
);
?>
<div class="annam-review-summary" id="annam-review-summary">
	<h2 class="annam-review-summary__title">
		<?php echo esc_html( sprintf( __( 'Đánh giá %s', 'generatepress_child' ), $title ) ); ?>
	</h2>

	<div class="annam-review-summary__grid<?php echo $ratings_enabled ? '' : ' annam-review-summary__grid--no-ratings'; ?>">
		<div class="annam-review-summary__score">
			<?php if ( $ratings_enabled ) : ?>
			<div class="annam-review-summary__score-row">
				<span class="annam-review-summary__star-big" aria-hidden="true">★</span>
				<div class="annam-review-summary__score-text">
					<span class="annam-review-summary__avg"><?php echo esc_html( (string) $display_avg ); ?></span><span class="annam-review-summary__avg-suffix">/5</span>
				</div>
			</div>
			<?php endif; ?>
			<?php if ( $review_count > 0 ) : ?>
				<?php if ( ! $ratings_enabled ) : ?>
				<p class="annam-review-summary__count annam-review-summary__count--norating">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: number of reviews */
							_n( '%d đánh giá', '%d đánh giá', $review_count, 'generatepress_child' ),
							$review_count
						)
					);
					?>
				</p>
				<?php endif; ?>
				<?php if ( $ratings_enabled ) : ?>
				<p class="annam-review-summary__count">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: number of reviews */
							_n( '%d khách hài lòng', '%d khách hài lòng', $review_count, 'generatepress_child' ),
							$review_count
						)
					);
					?>
					<span class="annam-review-summary__info" title="<?php esc_attr_e( 'Dựa trên đánh giá đã duyệt trên website.', 'generatepress_child' ); ?>">i</span>
				</p>
				<?php endif; ?>
			<?php else : ?>
				<p class="annam-review-summary__count annam-review-summary__count--empty"><?php esc_html_e( 'Chưa có đánh giá', 'generatepress_child' ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $ratings_enabled ) : ?>
		<div class="annam-review-summary__bars" role="list">
			<?php foreach ( range( 5, 1 ) as $star ) : ?>
				<div class="annam-review-summary__bar-row" role="listitem">
					<span class="annam-review-summary__bar-label"><?php echo (int) $star; ?> <span aria-hidden="true">★</span></span>
					<div class="annam-review-summary__bar-track" aria-hidden="true">
						<div class="annam-review-summary__bar-fill" style="width: <?php echo esc_attr( (string) (int) $pcts[ $star ] ); ?>%;"></div>
					</div>
					<span class="annam-review-summary__bar-pct"><?php echo esc_html( (string) (int) $pcts[ $star ] ); ?>%</span>
				</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>

	<?php if ( $can_submit ) : ?>
		<button type="button" class="annam-review-summary__cta annam-btn-primary" id="annam-open-review-modal">
			<?php esc_html_e( 'Viết đánh giá', 'generatepress_child' ); ?>
		</button>
	<?php else : ?>
		<p class="annam-review-summary__verify-msg"><?php echo esc_html( $submit_msg ); ?></p>
	<?php endif; ?>
</div>

<?php if ( $can_submit ) : ?>
<div class="annam-review-modal" id="annam-review-modal" hidden aria-hidden="true">
	<div class="annam-review-modal__overlay" data-annam-review-close tabindex="-1"></div>
	<div class="annam-review-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="annam-review-modal-title">
		<button type="button" class="annam-review-modal__close" data-annam-review-close aria-label="<?php esc_attr_e( 'Đóng', 'generatepress_child' ); ?>">×</button>
		<h2 class="annam-review-modal__title" id="annam-review-modal-title"><?php echo esc_html( sprintf( __( 'đánh giá %s', 'generatepress_child' ), $title ) ); ?></h2>

		<div class="annam-review-modal__product">
			<img src="<?php echo esc_url( $thumb ); ?>" alt="" class="annam-review-modal__thumb" width="80" height="80" loading="lazy" />
			<p class="annam-review-modal__name"><?php echo esc_html( $title ); ?></p>
		</div>

		<div class="annam-review-modal__stars-wrap">
			<div class="annam-review-modal__stars" id="annam-review-stars" role="radiogroup" aria-label="<?php esc_attr_e( 'Chọn số sao', 'generatepress_child' ); ?>">
				<?php foreach ( range( 1, 5 ) as $s ) : ?>
					<button type="button" class="annam-review-modal__star-btn" data-rating="<?php echo (int) $s; ?>" aria-label="<?php echo esc_attr( sprintf( __( '%d sao', 'generatepress_child' ), $s ) ); ?>">
						<span class="annam-review-modal__star" aria-hidden="true">★</span>
						<span class="annam-review-modal__star-label"><?php echo esc_html( $labels[ $s ] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<form class="annam-review-modal__form" id="annam-review-form" hidden>
			<input type="hidden" name="rating" id="annam-review-rating-input" value="" />
			<input type="hidden" name="annam_review_form_ts" id="annam-review-form-ts" value="" />
			<label class="screen-reader-text" for="annam-review-company"><?php esc_html_e( 'Công ty', 'generatepress_child' ); ?></label>
			<input type="text" name="annam_review_company" id="annam-review-company" class="annam-review-modal__hp" value="" tabindex="-1" autocomplete="off" />

				<label class="annam-review-modal__field">
					<span class="annam-review-modal__label"><?php esc_html_e( 'Cảm nhận', 'generatepress_child' ); ?></span>
					<textarea name="comment" id="annam-review-comment" rows="4" maxlength="1000" placeholder="<?php esc_attr_e( 'Mời bạn chia sẻ thêm cảm nhận...', 'generatepress_child' ); ?>"></textarea>
				</label>

			<div class="annam-review-modal__upload-row">
				<div class="annam-review-modal__upload-block">
					<label class="annam-review-modal__upload">
						<input type="file" name="review_images[]" id="annam-review-images" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" multiple />
						<span class="annam-review-modal__upload-text"><?php esc_html_e( 'Gửi ảnh thực tế', 'generatepress_child' ); ?> <span class="annam-review-modal__upload-hint">(<?php esc_html_e( 'tối đa 3 ảnh', 'generatepress_child' ); ?>)</span></span>
					</label>
					<ul class="annam-review-modal__file-list" id="annam-review-file-list" hidden aria-live="polite"></ul>
				</div>
			</div>

			<div class="annam-review-modal__row2">
				<label class="annam-review-modal__field annam-review-modal__field--half">
					<span class="annam-review-modal__label"><?php esc_html_e( 'Họ tên', 'generatepress_child' ); ?> <abbr title="<?php esc_attr_e( 'bắt buộc', 'generatepress_child' ); ?>">*</abbr></span>
					<input type="text" name="author" id="annam-review-author" required maxlength="100" autocomplete="name" />
				</label>
				<label class="annam-review-modal__field annam-review-modal__field--half">
					<span class="annam-review-modal__label"><?php esc_html_e( 'Số điện thoại', 'generatepress_child' ); ?> <abbr title="<?php esc_attr_e( 'bắt buộc', 'generatepress_child' ); ?>">*</abbr></span>
					<input type="tel" name="phone" id="annam-review-phone" required maxlength="25" autocomplete="tel" />
				</label>
			</div>

			<p class="annam-review-modal__msg" id="annam-review-form-msg" hidden></p>

			<button type="submit" class="annam-review-modal__submit annam-btn-cta"><?php esc_html_e( 'Gửi đánh giá', 'generatepress_child' ); ?></button>
		</form>
	</div>
</div>
<?php endif; ?>
