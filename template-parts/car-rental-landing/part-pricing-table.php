<?php
/**
 * Bảng giá thuê xe — desktop table + mobile cards.
 *
 * @package GeneratePress_Child
 *
 * @var array $args Template args.
 */

defined( 'ABSPATH' ) || exit;

$routes         = isset( $args['routes'] ) ? (array) $args['routes'] : array();
$vehicle_label  = isset( $args['vehicle_label'] ) ? (string) $args['vehicle_label'] : '';
$note           = isset( $args['note'] ) ? (array) $args['note'] : array();
$config         = annam_car_rental_get_landing_config();
$pricing_title  = isset( $args['pricing_title'] ) ? (string) $args['pricing_title'] : ( $config['pricing_title'] ?? __( 'Bảng giá tham khảo', 'generatepress_child' ) );

if ( empty( $routes ) ) {
	return;
}

$vehicle_meta = $vehicle_label
	? sprintf(
		/* translators: %s: vehicle label e.g. Limousine 9–11 chỗ */
		__( 'Xe %s, xuất phát từ Hà Nội', 'generatepress_child' ),
		$vehicle_label
	)
	: __( 'Xuất phát từ Hà Nội', 'generatepress_child' );

$price_col_label = $vehicle_label
	? sprintf(
		/* translators: %s: vehicle label e.g. Limousine 9–11 chỗ */
		__( 'Giá %s', 'generatepress_child' ),
		$vehicle_label
	)
	: __( 'Giá (2 chiều)', 'generatepress_child' );
?>
<section class="annam-cr-section" id="bang-gia">
	<div class="annam-cr-container">
		<header class="annam-cr-section__head annam-cr-section__head--center">
			<h2 class="annam-cr-section__title"><?php echo esc_html( $pricing_title ); ?></h2>
			<p class="annam-cr-section__desc"><?php esc_html_e( 'Giá 2 chiều, xuất phát từ Hà Nội', 'generatepress_child' ); ?></p>
			<span class="annam-cr-section__accent" aria-hidden="true"></span>
		</header>

		<div class="annam-cr-pricing" data-annam-cr-pricing>
			<label class="annam-cr-pricing__search-label" for="annam-cr-pricing-search">
				<span class="screen-reader-text"><?php esc_html_e( 'Tìm hành trình', 'generatepress_child' ); ?></span>
				<input
					type="search"
					class="annam-cr-pricing__search"
					id="annam-cr-pricing-search"
					placeholder="<?php esc_attr_e( 'Nhập tỉnh/thành muốn đi', 'generatepress_child' ); ?>"
					autocomplete="off"
					inputmode="search"
				/>
			</label>

			<div class="annam-cr-pricing-list annam-cr-pricing-list--sticky-head" id="annam-cr-pricing-list" role="list">
				<div class="annam-cr-pricing-list__header" role="row" aria-hidden="true">
					<span class="annam-cr-pricing-list__col annam-cr-pricing-list__col--route" role="columnheader">
						<?php esc_html_e( 'Hành trình từ Hà Nội', 'generatepress_child' ); ?>
					</span>
					<span class="annam-cr-pricing-list__col annam-cr-pricing-list__col--price" role="columnheader">
						<?php echo esc_html( $price_col_label ); ?>
					</span>
					<span class="annam-cr-pricing-list__col annam-cr-pricing-list__col--cta" role="columnheader">
						<?php esc_html_e( 'Nhận giá chính xác', 'generatepress_child' ); ?>
					</span>
				</div>

				<?php foreach ( $routes as $route ) : ?>
					<?php
					$route_label   = (string) ( $route['label'] ?? '' );
					$label_display = annam_car_rental_format_route_pricing_label( $route_label );
					$pickup        = (string) ( $route['pickup'] ?? 'Hà Nội' );
					$destination   = (string) ( $route['destination'] ?? '' );
					$price         = (int) ( $route['price'] ?? 0 );
					$is_hot        = ! empty( $route['hot'] );
					$source_note   = sprintf(
						/* translators: %s: route label */
						__( 'Khách bấm từ bảng giá hành trình %s', 'generatepress_child' ),
						$route_label
					);
					?>
					<article
						class="annam-cr-pricing-row<?php echo $is_hot ? ' annam-cr-pricing-row--hot' : ''; ?>"
						role="listitem"
						data-annam-cr-pricing-row
						data-search="<?php echo esc_attr( (string) ( $route['search_key'] ?? '' ) ); ?>"
					>
						<div class="annam-cr-pricing-row__meta">
							<div class="annam-cr-pricing-row__meta-start">
								<?php if ( $is_hot ) : ?>
									<span class="annam-cr-pricing-row__badge annam-cr-pricing-row__badge--hot"><?php esc_html_e( '🔥 Tuyến hot', 'generatepress_child' ); ?></span>
								<?php endif; ?>
							</div>
							<span class="annam-cr-pricing-row__badge annam-cr-pricing-row__badge--trip"><?php esc_html_e( '2 chiều', 'generatepress_child' ); ?></span>
						</div>

						<div class="annam-cr-pricing-row__route annam-cr-pricing-list__col annam-cr-pricing-list__col--route">
							<?php if ( $is_hot ) : ?>
								<span class="annam-cr-pricing-row__badge annam-cr-pricing-row__badge--hot annam-cr-pricing-row__badge--desktop"><?php esc_html_e( '🔥 Tuyến hot', 'generatepress_child' ); ?></span>
							<?php endif; ?>
							<span class="annam-cr-pricing-row__route-name"><?php echo esc_html( $label_display ); ?></span>
							<p class="annam-cr-pricing-row__desc"><?php echo esc_html( $vehicle_meta ); ?></p>
						</div>

						<div class="annam-cr-pricing-row__footer">
							<div class="annam-cr-pricing-row__price annam-cr-pricing-list__col annam-cr-pricing-list__col--price">
								<p class="annam-cr-price-display">
									<span class="annam-cr-price-display__prefix"><?php esc_html_e( 'Từ', 'generatepress_child' ); ?></span>
									<strong class="annam-cr-price-display__amount"><?php echo esc_html( annam_car_rental_format_price( $price ) ); ?></strong>
								</p>
							</div>

							<div class="annam-cr-pricing-row__cta annam-cr-pricing-list__col annam-cr-pricing-list__col--cta">
								<button
									type="button"
									class="annam-cr-btn annam-cr-btn--pill annam-cr-btn--quote"
									data-annam-cr-pick-route="<?php echo esc_attr( $route_label ); ?>"
									data-annam-cr-pickup="<?php echo esc_attr( $pickup ); ?>"
									data-annam-cr-destination="<?php echo esc_attr( $destination ); ?>"
									data-annam-cr-price="<?php echo esc_attr( (string) $price ); ?>"
									data-annam-cr-source-note="<?php echo esc_attr( $source_note ); ?>"
								>
									<?php esc_html_e( 'Nhận báo giá', 'generatepress_child' ); ?>
								</button>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<p class="annam-cr-pricing__empty" id="annam-cr-pricing-empty" hidden>
				<?php esc_html_e( 'Không tìm thấy hành trình phù hợp. Thử từ khóa khác hoặc gọi hotline để được tư vấn.', 'generatepress_child' ); ?>
			</p>
		</div>

		<?php if ( ! empty( $note ) ) : ?>
			<div class="annam-cr-pricing-note">
				<p class="annam-cr-pricing-note__line">
					<strong class="annam-cr-pricing-note__in"><?php esc_html_e( 'Báo giá đã bao gồm:', 'generatepress_child' ); ?></strong>
					<?php echo esc_html( $note['included'] ?? '' ); ?>
				</p>
				<p class="annam-cr-pricing-note__line">
					<strong class="annam-cr-pricing-note__out"><?php esc_html_e( 'Báo giá chưa bao gồm:', 'generatepress_child' ); ?></strong>
					<?php echo esc_html( $note['excluded'] ?? '' ); ?>
				</p>
				<?php if ( ! empty( $note['disclaimer'] ) ) : ?>
					<p class="annam-cr-pricing-note__disclaimer"><?php echo esc_html( $note['disclaimer'] ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
