<?php
/**
 * Form báo giá thuê xe.
 *
 * @package GeneratePress_Child
 *
 * @var array $args Template args.
 */

defined( 'ABSPATH' ) || exit;

$variant      = isset( $args['variant'] ) ? (string) $args['variant'] : 'hero';
$config       = annam_car_rental_get_landing_config();
$form         = isset( $config['form'] ) ? $config['form'] : array();
$vehicle_type = isset( $config['vehicle_type'] ) ? (string) $config['vehicle_type'] : '';
if ( ! annam_car_rental_is_valid_vehicle_type( $vehicle_type ) ) {
	$vehicle_type = '';
}
$notice       = annam_car_rental_get_notice();
$types        = annam_car_rental_get_vehicle_types();
$today        = wp_date( 'Y-m-d' );
$form_id      = 'hero' === $variant ? 'annam-cr-booking' : 'annam-cr-booking-final';
$is_compact   = 'final' === $variant;
?>
<div class="annam-cr-form-wrap annam-cr-form-wrap--<?php echo esc_attr( $variant ); ?>" id="<?php echo esc_attr( $form_id ); ?>">
	<div class="annam-cr-form__ajax-notice" id="annam-cr-form-notice-<?php echo esc_attr( $variant ); ?>" role="alert" hidden></div>

	<?php if ( $notice && 'hero' === $variant ) : ?>
		<div class="annam-cr-notice annam-cr-notice--<?php echo esc_attr( $notice['type'] ); ?>" role="alert">
			<?php echo esc_html( $notice['message'] ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $form['title'] ) && ! $is_compact ) : ?>
		<h2 class="annam-cr-form__title"><?php echo esc_html( $form['title'] ); ?></h2>
	<?php endif; ?>

	<form class="annam-cr-form" id="annam-cr-form-<?php echo esc_attr( $variant ); ?>" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate data-annam-cr-form data-variant="<?php echo esc_attr( $variant ); ?>">
		<input type="hidden" name="annam_cr_submit" value="1" />
		<input type="hidden" name="annam_cr_ts" value="<?php echo esc_attr( (string) time() ); ?>" />
		<input type="hidden" name="annam_cr_nonce" value="<?php echo esc_attr( wp_create_nonce( 'annam_car_rental_booking' ) ); ?>" />
		<input type="hidden" name="annam_cr_route" id="annam-cr-route-<?php echo esc_attr( $variant ); ?>" value="" />
		<input type="hidden" name="annam_cr_source_note" id="annam-cr-source-note-<?php echo esc_attr( $variant ); ?>" value="" />
		<p class="annam-cr-form__hp" aria-hidden="true">
			<label for="annam-cr-website-<?php echo esc_attr( $variant ); ?>">Website</label>
			<input type="text" name="annam_cr_website" id="annam-cr-website-<?php echo esc_attr( $variant ); ?>" tabindex="-1" autocomplete="off" />
		</p>

		<?php if ( $is_compact ) : ?>
			<div class="annam-cr-form__field">
				<label class="annam-cr-form__label annam-cr-form__label--upper" for="annam-cr-route-text-<?php echo esc_attr( $variant ); ?>"><?php esc_html_e( 'Điểm đón & Điểm đến', 'generatepress_child' ); ?></label>
				<div class="annam-cr-input-icon">
					<?php echo annam_car_rental_icon( 'route' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<input type="text" name="annam_cr_pickup" id="annam-cr-route-text-<?php echo esc_attr( $variant ); ?>" placeholder="<?php esc_attr_e( 'Hà Nội - Hạ Long...', 'generatepress_child' ); ?>" required />
				</div>
			</div>
			<div class="annam-cr-form__field">
				<label class="annam-cr-form__label annam-cr-form__label--upper" for="annam-cr-phone-<?php echo esc_attr( $variant ); ?>"><?php esc_html_e( 'Số điện thoại / Zalo', 'generatepress_child' ); ?></label>
				<div class="annam-cr-input-icon">
					<?php echo annam_car_rental_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<input type="tel" name="annam_cr_phone" id="annam-cr-phone-<?php echo esc_attr( $variant ); ?>" placeholder="<?php esc_attr_e( 'Nhập số điện thoại', 'generatepress_child' ); ?>" required autocomplete="tel" />
				</div>
			</div>
		<?php else : ?>
			<div class="annam-cr-form__field">
				<label class="annam-cr-form__label" for="annam-cr-pickup-<?php echo esc_attr( $variant ); ?>"><?php esc_html_e( 'Điểm đón', 'generatepress_child' ); ?></label>
				<div class="annam-cr-input-icon">
					<?php echo annam_car_rental_icon( 'location_on' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<input type="text" name="annam_cr_pickup" id="annam-cr-pickup-<?php echo esc_attr( $variant ); ?>" placeholder="<?php esc_attr_e( 'Nhập điểm đón', 'generatepress_child' ); ?>" required />
				</div>
			</div>
			<div class="annam-cr-form__field">
				<label class="annam-cr-form__label" for="annam-cr-dropoff-<?php echo esc_attr( $variant ); ?>"><?php esc_html_e( 'Điểm đến', 'generatepress_child' ); ?></label>
				<div class="annam-cr-input-icon">
					<?php echo annam_car_rental_icon( 'location_on' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<input type="text" name="annam_cr_dropoff" id="annam-cr-dropoff-<?php echo esc_attr( $variant ); ?>" placeholder="<?php esc_attr_e( 'Nhập điểm đến', 'generatepress_child' ); ?>" />
				</div>
			</div>
			<div class="annam-cr-form__toggle-row">
				<span class="annam-cr-form__label"><?php esc_html_e( 'Thuê xe 2 chiều', 'generatepress_child' ); ?></span>
				<label class="annam-cr-switch">
					<input type="checkbox" name="annam_cr_round_trip" value="1" checked />
					<span class="annam-cr-switch__slider" aria-hidden="true"></span>
				</label>
			</div>
			<div class="annam-cr-form__row annam-cr-form__row--2">
				<div class="annam-cr-form__field">
					<label class="annam-cr-form__label" for="annam-cr-date-<?php echo esc_attr( $variant ); ?>"><?php esc_html_e( 'Ngày đi', 'generatepress_child' ); ?></label>
					<div class="annam-cr-input-icon">
						<?php echo annam_car_rental_icon( 'calendar_today' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<input type="date" class="annam-cr-input-date" name="annam_cr_date" id="annam-cr-date-<?php echo esc_attr( $variant ); ?>" value="<?php echo esc_attr( $today ); ?>" min="<?php echo esc_attr( $today ); ?>" autocomplete="off" />
					</div>
				</div>
				<div class="annam-cr-form__field">
					<label class="annam-cr-form__label" for="annam-cr-vehicle-<?php echo esc_attr( $variant ); ?>"><?php esc_html_e( 'Loại xe', 'generatepress_child' ); ?></label>
					<select name="annam_cr_vehicle" id="annam-cr-vehicle-<?php echo esc_attr( $variant ); ?>">
						<?php foreach ( $types as $key => $type ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $vehicle_type, $key ); ?>><?php echo esc_html( $type['label'] ); ?></option>
						<?php endforeach; ?>
						<option value="unknown" <?php selected( $vehicle_type, '' ); ?>><?php esc_html_e( 'Chưa biết, cần tư vấn', 'generatepress_child' ); ?></option>
					</select>
				</div>
			</div>
			<div class="annam-cr-form__field">
				<label class="annam-cr-form__label" for="annam-cr-phone-<?php echo esc_attr( $variant ); ?>"><?php esc_html_e( 'SĐT / Zalo (Bắt buộc)', 'generatepress_child' ); ?></label>
				<div class="annam-cr-input-icon">
					<?php echo annam_car_rental_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<input type="tel" name="annam_cr_phone" id="annam-cr-phone-<?php echo esc_attr( $variant ); ?>" placeholder="<?php esc_attr_e( 'Nhập số điện thoại', 'generatepress_child' ); ?>" required autocomplete="tel" />
				</div>
			</div>
		<?php endif; ?>

		<button type="submit" class="annam-cr-btn annam-cr-btn--cta annam-cr-btn--block">
			<?php echo esc_html( $form['submit_label'] ?? __( 'Nhận báo giá', 'generatepress_child' ) ); ?>
		</button>
	</form>
</div>
