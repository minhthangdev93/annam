<?php
/**
 * Two-column layout: long description + booking card / lead form.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $product, $post;

if ( ! is_a( $product, WC_Product::class ) || ! $post instanceof WP_Post ) {
	return;
}

$pid = (int) $product->get_id();

$duration  = trim( (string) get_post_meta( $pid, '_tour_duration', true ) );
$transport = trim( (string) get_post_meta( $pid, '_tour_transport', true ) );
$departure = trim( (string) get_post_meta( $pid, '_tour_departure', true ) );
$schedule  = trim( (string) get_post_meta( $pid, '_tour_schedule', true ) );
$hotline_m = trim( (string) get_post_meta( $pid, '_tour_hotline', true ) );
$hotline   = '' !== $hotline_m ? $hotline_m : annam_tour_default_hotline();

$title = $product->get_name();

$tour_price_html = annam_tour_price_block_html( $product );

$lead_status = isset( $_GET['annam_lead'] ) ? sanitize_key( wp_unslash( $_GET['annam_lead'] ) ) : '';

$codes = annam_tour_get_phone_country_codes();

setup_postdata( $post );
$content = apply_filters( 'the_content', get_post_field( 'post_content', $pid ) );
wp_reset_postdata();
?>
<section class="annam-tour-detail-section" id="annam-tour-detail">
	<div class="annam-tour-detail-section__container annam-container grid-container grid-parent">
		<div class="annam-tour-detail-grid">
			<div class="annam-tour-detail-grid__main annam-tour-main-content">
				<div class="annam-tour-content entry-content">
					<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content filters apply wp_kses. ?>
				</div>
			</div>
			<aside class="annam-tour-detail-grid__sidebar annam-tour-sidebar" aria-label="<?php echo esc_attr__( 'Đặt tour & tư vấn', 'woocommerce' ); ?>">
				<div class="annam-tour-booking-card">
					<h2 class="annam-tour-booking-card__title"><?php echo esc_html( $title ); ?></h2>

					<?php if ( '' !== $tour_price_html ) : ?>
						<div class="annam-tour-booking-card__price">
							<?php echo wp_kses_post( $tour_price_html ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $duration || $transport || $departure || $schedule ) : ?>
						<ul class="annam-tour-booking-card__info">
							<?php if ( $duration ) : ?>
								<li class="annam-tour-booking-card__info-item">
									<span class="annam-tour-booking-card__info-icon" aria-hidden="true">⏱</span>
									<span class="annam-tour-booking-card__info-label"><?php echo esc_html__( 'Thời gian', 'woocommerce' ); ?></span>
									<span class="annam-tour-booking-card__info-value"><?php echo esc_html( $duration ); ?></span>
								</li>
							<?php endif; ?>
							<?php if ( $transport ) : ?>
								<li class="annam-tour-booking-card__info-item">
									<span class="annam-tour-booking-card__info-icon" aria-hidden="true">▸</span>
									<span class="annam-tour-booking-card__info-label"><?php echo esc_html__( 'Phương tiện', 'woocommerce' ); ?></span>
									<span class="annam-tour-booking-card__info-value"><?php echo esc_html( $transport ); ?></span>
								</li>
							<?php endif; ?>
							<?php if ( $departure ) : ?>
								<li class="annam-tour-booking-card__info-item">
									<span class="annam-tour-booking-card__info-icon" aria-hidden="true">◎</span>
									<span class="annam-tour-booking-card__info-label"><?php echo esc_html__( 'Nơi khởi hành', 'woocommerce' ); ?></span>
									<span class="annam-tour-booking-card__info-value"><?php echo esc_html( $departure ); ?></span>
								</li>
							<?php endif; ?>
							<?php if ( $schedule ) : ?>
								<li class="annam-tour-booking-card__info-item">
									<span class="annam-tour-booking-card__info-icon" aria-hidden="true">•</span>
									<span class="annam-tour-booking-card__info-label"><?php echo esc_html__( 'Lịch khởi hành', 'woocommerce' ); ?></span>
									<span class="annam-tour-booking-card__info-value"><?php echo esc_html( $schedule ); ?></span>
								</li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $hotline ) : ?>
						<?php
						$digits_only = preg_replace( '/\D+/', '', $hotline );
						$tel_href      = $digits_only ? 'tel:' . $digits_only : '#';
						?>
						<div class="annam-tour-booking-card__hotline">
							<span class="annam-tour-booking-card__hotline-label"><?php echo esc_html__( 'Hotline đặt tour', 'woocommerce' ); ?></span>
							<a class="annam-tour-booking-card__hotline-num" href="<?php echo esc_url( $tel_href ); ?>"><?php echo esc_html( $hotline ); ?></a>
						</div>
					<?php endif; ?>

					<p class="annam-tour-booking-card__note"><?php echo esc_html__( 'Đặt giữ chỗ giá tốt - Thanh toán sau.', 'woocommerce' ); ?></p>

					<?php if ( 'success' === $lead_status ) : ?>
						<p class="annam-tour-form-message annam-tour-form-message--success" role="status" id="annam-tour-lead-notice"><?php echo esc_html( annam_tour_lead_success_message() ); ?></p>
					<?php elseif ( 'error' === $lead_status ) : ?>
						<p class="annam-tour-form-message annam-tour-form-message--error" role="alert" id="annam-tour-lead-notice"><?php echo esc_html( annam_tour_lead_error_message() ); ?></p>
					<?php else : ?>
						<p class="annam-tour-form-message annam-tour-form-message--js" id="annam-tour-lead-notice" role="alert" hidden></p>
					<?php endif; ?>

					<form class="annam-tour-lead-form" method="post" action="<?php echo esc_url( get_permalink( $pid ) ); ?>#annam-tour-detail" id="annam-tour-lead-form" novalidate data-annam-tour-lead-form>
						<?php wp_nonce_field( 'annam_tour_lead', 'annam_tour_lead_nonce' ); ?>
						<input type="hidden" name="annam_product_id" value="<?php echo esc_attr( (string) $pid ); ?>" />
						<input type="hidden" name="annam_form_ts" id="annam-tour-form-ts" value="<?php echo esc_attr( (string) time() ); ?>" />

						<div class="annam-tour-lead-form__hp" aria-hidden="true">
							<label for="annam_hp_website"><?php echo esc_html__( 'Website', 'woocommerce' ); ?></label>
							<input type="text" name="annam_hp_website" id="annam_hp_website" value="" tabindex="-1" autocomplete="off" />
						</div>

						<div class="annam-tour-phone-row">
							<div class="annam-tour-phone-code">
								<label class="screen-reader-text" for="annam_phone_country"><?php echo esc_html__( 'Mã vùng', 'woocommerce' ); ?></label>
								<select class="annam-tour-phone-code__select" name="annam_phone_country" id="annam_phone_country" required>
									<?php foreach ( $codes as $row ) : ?>
										<?php
										if ( empty( $row['code'] ) || empty( $row['label'] ) ) {
											continue;
										}
										?>
										<option value="<?php echo esc_attr( $row['code'] ); ?>"<?php selected( '+84', $row['code'] ); ?>><?php echo esc_html( $row['label'] . ' ' . $row['code'] ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="annam-tour-phone-input">
								<label class="screen-reader-text" for="annam_phone_local"><?php echo esc_html__( 'Số điện thoại', 'woocommerce' ); ?></label>
								<input
									type="text"
									name="annam_phone_local"
									id="annam_phone_local"
									class="annam-tour-phone-input__field"
									inputmode="tel"
									autocomplete="tel-national"
									placeholder="<?php echo esc_attr__( 'Nhập số điện thoại', 'woocommerce' ); ?>"
									required
								/>
							</div>
						</div>

						<button type="submit" name="annam_tour_lead_submit" class="annam-tour-lead-form__submit" id="annam-tour-lead-submit" value="1">
							<?php echo esc_html__( 'Gửi', 'woocommerce' ); ?>
						</button>
					</form>
				</div>
			</aside>
		</div>
	</div>
</section>
