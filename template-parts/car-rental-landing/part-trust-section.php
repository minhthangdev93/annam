<?php
/**
 * Section uy tín: gallery thực tế + thông tin thanh toán công ty.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$config = function_exists( 'annam_car_rental_get_landing_config' ) ? annam_car_rental_get_landing_config() : array();
$cta    = function_exists( 'annam_car_rental_get_cta' ) ? annam_car_rental_get_cta() : array();
$trust  = isset( $config['trust'] ) ? $config['trust'] : array();
$gallery = isset( $trust['gallery'] ) ? $trust['gallery'] : array();
$payment = isset( $trust['payment'] ) ? $trust['payment'] : array();
$images  = isset( $gallery['images'] ) ? array_slice( array_values( (array) $gallery['images'] ), 0, 5 ) : array();

if ( empty( $trust ) ) {
	return;
}

$account_number = trim( (string) ( $payment['account_number'] ?? '' ) );
$bank_name      = trim( (string) ( $payment['bank_name'] ?? '' ) );
$account_display = $account_number !== '' ? $account_number : (string) ( $payment['account_number_placeholder'] ?? '' );
$bank_display    = $bank_name !== '' ? $bank_name : (string) ( $payment['bank_name_placeholder'] ?? '' );
$qr_image        = trim( (string) ( $payment['qr_image'] ?? '' ) );

$gallery_id = 'annam-cr-trust-gallery-' . wp_unique_id();
?>
<section class="annam-cr-section annam-cr-section--muted annam-cr-section--trust" id="uy-tin">
	<div class="annam-cr-container">
		<header class="annam-cr-section__head annam-cr-section__head--center">
			<h2 class="annam-cr-section__title"><?php echo esc_html( $trust['section_title'] ?? '' ); ?></h2>
			<span class="annam-cr-section__accent" aria-hidden="true"></span>
		</header>

		<div class="annam-cr-trust">
			<div class="annam-cr-trust__gallery-col">
				<div class="annam-cr-trust-gallery" id="<?php echo esc_attr( $gallery_id ); ?>" data-annam-cr-trust-gallery>
					<div class="annam-cr-trust-gallery__head">
						<h3 class="annam-cr-trust-gallery__title"><?php echo esc_html( $gallery['title'] ?? '' ); ?></h3>
					</div>

					<?php if ( ! empty( $images ) ) : ?>
						<div class="annam-cr-trust-gallery__main">
							<button
								type="button"
								class="annam-cr-trust-gallery__main-btn"
								data-annam-cr-trust-open
								aria-label="<?php esc_attr_e( 'Xem ảnh lớn', 'generatepress_child' ); ?>"
							>
								<?php
								$first = $images[0];
								$first_url = trim( (string) ( $first['url'] ?? '' ) );
								$first_alt = (string) ( $first['alt'] ?? '' );
								$first_label = (string) ( $first['label'] ?? '' );
								if ( $first_url ) :
									?>
									<img
										class="annam-cr-trust-gallery__main-img"
										src="<?php echo esc_url( $first_url ); ?>"
										alt="<?php echo esc_attr( $first_alt ); ?>"
										width="960"
										height="600"
										loading="lazy"
										decoding="async"
										data-annam-cr-trust-main-img
									/>
								<?php else : ?>
									<span class="annam-cr-trust-gallery__placeholder" data-annam-cr-trust-main-placeholder>
										<span class="annam-cr-trust-gallery__placeholder-label"><?php echo esc_html( $first_label ); ?></span>
									</span>
								<?php endif; ?>
							</button>
						</div>

						<div class="annam-cr-trust-gallery__thumbs" role="tablist" aria-label="<?php esc_attr_e( 'Ảnh thực tế', 'generatepress_child' ); ?>">
							<?php foreach ( $images as $index => $image ) : ?>
								<?php
								$img_url   = trim( (string) ( $image['url'] ?? '' ) );
								$img_alt   = (string) ( $image['alt'] ?? '' );
								$img_label = (string) ( $image['label'] ?? '' );
								$is_active = 0 === $index;
								?>
								<button
									type="button"
									class="annam-cr-trust-gallery__thumb<?php echo $is_active ? ' is-active' : ''; ?>"
									role="tab"
									<?php echo $is_active ? ' hidden' : ''; ?>
									aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
									data-annam-cr-trust-thumb
									data-index="<?php echo esc_attr( (string) $index ); ?>"
									data-url="<?php echo esc_attr( $img_url ); ?>"
									data-alt="<?php echo esc_attr( $img_alt ); ?>"
									data-label="<?php echo esc_attr( $img_label ); ?>"
									aria-label="<?php echo esc_attr( $img_alt ?: $img_label ); ?>"
								>
									<?php if ( $img_url ) : ?>
										<img src="<?php echo esc_url( $img_url ); ?>" alt="" width="160" height="100" loading="lazy" decoding="async" />
									<?php else : ?>
										<span class="annam-cr-trust-gallery__thumb-placeholder"><?php echo esc_html( $img_label ); ?></span>
									<?php endif; ?>
								</button>
							<?php endforeach; ?>
						</div>

						<script type="application/json" data-annam-cr-trust-data>
							<?php
							echo wp_json_encode(
								array_map(
									static function ( $image ) {
										return array(
											'url'   => trim( (string) ( $image['url'] ?? '' ) ),
											'alt'   => (string) ( $image['alt'] ?? '' ),
											'label' => (string) ( $image['label'] ?? '' ),
										);
									},
									$images
								)
							);
							?>
						</script>
					<?php endif; ?>
				</div>
			</div>

			<div class="annam-cr-trust__payment-col">
				<div class="annam-cr-trust-payment">
					<?php if ( ! empty( $payment['eyebrow'] ) ) : ?>
						<p class="annam-cr-trust-payment__eyebrow">
							<?php echo annam_car_rental_icon( 'shield_check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $payment['eyebrow'] ); ?></span>
						</p>
					<?php endif; ?>

					<div class="annam-cr-trust-payment__card">
						<h3 class="annam-cr-trust-payment__title"><?php echo esc_html( $payment['title'] ?? '' ); ?></h3>
						<?php if ( ! empty( $payment['description'] ) ) : ?>
							<p class="annam-cr-trust-payment__desc"><?php echo esc_html( $payment['description'] ); ?></p>
						<?php endif; ?>

						<div class="annam-cr-trust-payment__body">
							<dl class="annam-cr-trust-payment__fields">
								<div class="annam-cr-trust-payment__field">
									<dt><?php esc_html_e( 'Tên tài khoản', 'generatepress_child' ); ?></dt>
									<dd>
										<span class="annam-cr-trust-payment__value" data-annam-cr-copy-value><?php echo esc_html( $payment['account_name'] ?? '' ); ?></span>
										<button type="button" class="annam-cr-trust-payment__copy" data-annam-cr-copy="<?php echo esc_attr( $payment['account_name'] ?? '' ); ?>">
											<?php echo annam_car_rental_icon( 'copy' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											<span><?php esc_html_e( 'Copy', 'generatepress_child' ); ?></span>
										</button>
									</dd>
								</div>
								<div class="annam-cr-trust-payment__field">
									<dt><?php esc_html_e( 'Số tài khoản', 'generatepress_child' ); ?></dt>
									<dd>
										<span class="annam-cr-trust-payment__value<?php echo $account_number === '' ? ' annam-cr-trust-payment__value--placeholder' : ''; ?>" data-annam-cr-copy-value><?php echo esc_html( $account_display ); ?></span>
										<?php if ( $account_number !== '' ) : ?>
											<button type="button" class="annam-cr-trust-payment__copy" data-annam-cr-copy="<?php echo esc_attr( $account_number ); ?>">
												<?php echo annam_car_rental_icon( 'copy' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
												<span><?php esc_html_e( 'Copy', 'generatepress_child' ); ?></span>
											</button>
										<?php endif; ?>
									</dd>
								</div>
								<div class="annam-cr-trust-payment__field">
									<dt><?php esc_html_e( 'Ngân hàng', 'generatepress_child' ); ?></dt>
									<dd>
										<span class="annam-cr-trust-payment__value<?php echo $bank_name === '' ? ' annam-cr-trust-payment__value--placeholder' : ''; ?>"><?php echo esc_html( $bank_display ); ?></span>
									</dd>
								</div>
								<?php if ( ! empty( $payment['bank_branch'] ) ) : ?>
									<div class="annam-cr-trust-payment__field">
										<dt><?php esc_html_e( 'Chi nhánh', 'generatepress_child' ); ?></dt>
										<dd>
											<span class="annam-cr-trust-payment__value"><?php echo esc_html( $payment['bank_branch'] ); ?></span>
											<button type="button" class="annam-cr-trust-payment__copy" data-annam-cr-copy="<?php echo esc_attr( $payment['bank_branch'] ); ?>">
												<?php echo annam_car_rental_icon( 'copy' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
												<span><?php esc_html_e( 'Copy', 'generatepress_child' ); ?></span>
											</button>
										</dd>
									</div>
								<?php endif; ?>
							</dl>

							<div class="annam-cr-trust-payment__qr">
								<?php if ( $qr_image ) : ?>
									<div class="annam-cr-trust-payment__qr-wrap">
										<img src="<?php echo esc_url( $qr_image ); ?>" alt="<?php esc_attr_e( 'Mã QR chuyển khoản', 'generatepress_child' ); ?>" width="180" height="180" loading="lazy" decoding="async" />
										<a class="annam-cr-trust-payment__qr-download" href="<?php echo esc_url( $qr_image ); ?>" download="QR-An-Nam-Discovery.jpg">
											<?php echo annam_car_rental_icon( 'download' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											<span><?php esc_html_e( 'Tải QR', 'generatepress_child' ); ?></span>
										</a>
									</div>
								<?php else : ?>
									<div class="annam-cr-trust-payment__qr-placeholder" aria-hidden="true">
										<span><?php esc_html_e( 'QR chuyển khoản', 'generatepress_child' ); ?></span>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<?php if ( ! empty( $payment['alert_fraud'] ) ) : ?>
						<div class="annam-cr-trust-payment__alert annam-cr-trust-payment__alert--fraud" role="note">
							<strong><?php esc_html_e( 'Cảnh báo chống lừa đảo:', 'generatepress_child' ); ?></strong>
							<?php
							$fraud_text = (string) $payment['alert_fraud'];
							$fraud_text = preg_replace( '/^Cảnh báo chống lừa đảo:\s*/u', '', $fraud_text );
							echo esc_html( $fraud_text );
							?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $payment['office_address'] ) ) : ?>
						<p class="annam-cr-trust-payment__office">
							<strong><?php echo esc_html( $payment['office_label'] ?? __( 'Trụ sở chính', 'generatepress_child' ) ); ?>:</strong>
							<?php echo esc_html( $payment['office_address'] ); ?>
						</p>
					<?php endif; ?>

					<div class="annam-cr-trust-payment__actions">
						<a class="annam-cr-btn annam-cr-btn--ghost annam-cr-btn--block" href="<?php echo esc_url( $cta['hotline_tel'] ?? 'tel:19008164' ); ?>">
							<?php esc_html_e( 'Gọi xác minh', 'generatepress_child' ); ?>
						</a>
						<a class="annam-cr-btn annam-cr-btn--zalo annam-cr-btn--block" href="<?php echo esc_url( $cta['zalo_url'] ?? '#' ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Chat Zalo', 'generatepress_child' ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<dialog class="annam-cr-trust-lightbox" data-annam-cr-trust-lightbox aria-label="<?php esc_attr_e( 'Thư viện ảnh thực tế', 'generatepress_child' ); ?>">
		<div class="annam-cr-trust-lightbox__inner">
			<button type="button" class="annam-cr-trust-lightbox__close" data-annam-cr-trust-close aria-label="<?php esc_attr_e( 'Đóng', 'generatepress_child' ); ?>">&times;</button>
			<button type="button" class="annam-cr-trust-lightbox__nav annam-cr-trust-lightbox__nav--prev" data-annam-cr-trust-prev aria-label="<?php esc_attr_e( 'Ảnh trước', 'generatepress_child' ); ?>">&#10094;</button>
			<figure class="annam-cr-trust-lightbox__figure">
				<img class="annam-cr-trust-lightbox__img" src="" alt="" width="1200" height="800" decoding="async" data-annam-cr-trust-lightbox-img />
				<figcaption class="annam-cr-trust-lightbox__caption" data-annam-cr-trust-lightbox-caption></figcaption>
			</figure>
			<button type="button" class="annam-cr-trust-lightbox__nav annam-cr-trust-lightbox__nav--next" data-annam-cr-trust-next aria-label="<?php esc_attr_e( 'Ảnh sau', 'generatepress_child' ); ?>">&#10095;</button>
		</div>
	</dialog>
</section>
