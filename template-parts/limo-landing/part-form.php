<?php
/**
 * Form giữ chỗ Limousine HN–Sapa.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$config   = annam_limo_landing_get_config();
$form     = isset( $config['form'] ) ? $config['form'] : array();
$defaults = isset( $config['form_defaults'] ) ? $config['form_defaults'] : array();
$notice   = annam_limo_landing_get_notice();

$default_from = isset( $defaults['from'] ) ? sanitize_key( (string) $defaults['from'] ) : 'hanoi';
$default_to   = isset( $defaults['to'] ) ? sanitize_key( (string) $defaults['to'] ) : 'sapa';
$default_seat = isset( $defaults['seat'] ) ? sanitize_key( (string) $defaults['seat'] ) : 'seat_a';
$default_time = isset( $defaults['time'] ) ? (string) $defaults['time'] : '07:00';
$today        = wp_date( 'Y-m-d' );
$route_map    = annam_limo_landing_get_route_destinations_map();
$time_options = function_exists( 'annam_limo_landing_departure_times' )
	? annam_limo_landing_departure_times( $default_from, $default_to )
	: array( '07:00', '14:30' );
if ( empty( $time_options ) ) {
	$time_options = array( '07:00', '14:30' );
}
if ( ! in_array( $default_time, $time_options, true ) ) {
	$default_time = $time_options[0];
}
$place_labels = array(
	'hanoi' => 'Hà Nội',
	'sapa'  => 'Sapa',
);
?>
<div class="annam-limo-form-wrap" id="annam-limo-booking">
	<?php if ( ! empty( $form['title'] ) ) : ?>
		<h2 class="annam-limo-form__title"><?php echo esc_html( $form['title'] ); ?></h2>
	<?php endif; ?>
	<?php if ( ! empty( $form['subtitle'] ) ) : ?>
		<p class="annam-limo-form__subtitle"><?php echo esc_html( $form['subtitle'] ); ?></p>
	<?php endif; ?>

	<form class="annam-limo-form" id="annam-limo-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate data-annam-limo-form>
		<input type="hidden" name="annam_limo_submit" value="1" />
		<input type="hidden" name="annam_limo_ts" id="annam-limo-ts" value="<?php echo esc_attr( (string) time() ); ?>" />
		<input type="hidden" name="annam_limo_nonce" id="annam-limo-nonce" value="<?php echo esc_attr( wp_create_nonce( 'annam_limo_booking' ) ); ?>" />
		<p class="annam-limo-form__hp" aria-hidden="true">
			<label for="annam-limo-website">Website</label>
			<input type="text" name="annam_limo_website" id="annam-limo-website" tabindex="-1" autocomplete="off" />
		</p>

		<p class="annam-limo-form__hint" id="annam-limo-time-hint" hidden></p>

		<div class="annam-limo-form__row annam-limo-form__row--2">
			<div class="annam-limo-form__field">
				<label for="annam-limo-from"><?php esc_html_e( 'Điểm đón', 'generatepress_child' ); ?> <span class="annam-limo-req">*</span></label>
				<select name="annam_limo_from" id="annam-limo-from" required data-annam-field="from">
					<?php foreach ( array_keys( $route_map ) as $place ) : ?>
						<option value="<?php echo esc_attr( $place ); ?>" <?php selected( $default_from, $place ); ?>><?php echo esc_html( $place_labels[ $place ] ?? $place ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="annam-limo-form__field">
				<label for="annam-limo-to"><?php esc_html_e( 'Điểm trả', 'generatepress_child' ); ?> <span class="annam-limo-req">*</span></label>
				<select name="annam_limo_to" id="annam-limo-to" required data-annam-field="to">
					<?php
					$destinations = isset( $route_map[ $default_from ] ) ? $route_map[ $default_from ] : array();
					foreach ( $destinations as $place ) :
						?>
						<option value="<?php echo esc_attr( $place ); ?>" <?php selected( $default_to, $place ); ?>><?php echo esc_html( $place_labels[ $place ] ?? $place ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="annam-limo-form__row annam-limo-form__row--2">
			<div class="annam-limo-form__field">
				<label for="annam-limo-date"><?php esc_html_e( 'Ngày đi', 'generatepress_child' ); ?> <span class="annam-limo-req">*</span></label>
				<input type="date" name="annam_limo_date" id="annam-limo-date" value="<?php echo esc_attr( $today ); ?>" min="<?php echo esc_attr( $today ); ?>" required data-annam-field="date" />
			</div>
			<div class="annam-limo-form__field">
				<label for="annam-limo-time"><?php esc_html_e( 'Giờ đi', 'generatepress_child' ); ?> <span class="annam-limo-req">*</span></label>
				<select name="annam_limo_time" id="annam-limo-time" required data-annam-field="time">
					<?php foreach ( $time_options as $time_opt ) : ?>
						<option value="<?php echo esc_attr( $time_opt ); ?>" <?php selected( $default_time, $time_opt ); ?>><?php echo esc_html( $time_opt ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="annam-limo-form__row annam-limo-form__row--2">
			<div class="annam-limo-form__field">
				<label for="annam-limo-seat"><?php esc_html_e( 'Hạng ghế / dịch vụ', 'generatepress_child' ); ?> <span class="annam-limo-req">*</span></label>
				<select name="annam_limo_seat" id="annam-limo-seat" required data-annam-field="seat">
					<?php foreach ( $config['seat_types'] as $st ) : ?>
						<option value="<?php echo esc_attr( $st['value'] ); ?>" <?php selected( $default_seat, $st['value'] ); ?>><?php echo esc_html( $st['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="annam-limo-form__field">
				<label for="annam-limo-guests"><?php esc_html_e( 'Số lượng khách', 'generatepress_child' ); ?></label>
				<input type="number" name="annam_limo_guests" id="annam-limo-guests" min="1" max="20" value="1" />
			</div>
		</div>

		<div class="annam-limo-form__row annam-limo-form__row--2">
			<div class="annam-limo-form__field">
				<label for="annam-limo-name"><?php esc_html_e( 'Họ tên', 'generatepress_child' ); ?> <span class="annam-limo-req">*</span></label>
				<input type="text" name="annam_limo_name" id="annam-limo-name" required maxlength="100" autocomplete="name" placeholder="<?php esc_attr_e( 'Nhập họ và tên', 'generatepress_child' ); ?>" />
			</div>
			<div class="annam-limo-form__field">
				<label for="annam-limo-phone"><?php esc_html_e( 'Số điện thoại / Zalo', 'generatepress_child' ); ?> <span class="annam-limo-req">*</span></label>
				<input type="tel" name="annam_limo_phone" id="annam-limo-phone" required maxlength="25" inputmode="tel" autocomplete="tel" placeholder="<?php esc_attr_e( 'Nhập SĐT hoặc Zalo', 'generatepress_child' ); ?>" />
			</div>
		</div>

		<div class="annam-limo-form__row annam-limo-form__row--2">
			<div class="annam-limo-form__field">
				<label for="annam-limo-pickup"><?php esc_html_e( 'Điểm đón mong muốn (tuỳ chọn)', 'generatepress_child' ); ?></label>
				<input type="text" name="annam_limo_pickup" id="annam-limo-pickup" maxlength="200" placeholder="<?php esc_attr_e( 'VD: Royal City / Nội Bài / 23 Tú Mỡ…', 'generatepress_child' ); ?>" />
			</div>
			<div class="annam-limo-form__field">
				<label for="annam-limo-dropoff"><?php esc_html_e( 'Điểm trả mong muốn (tuỳ chọn)', 'generatepress_child' ); ?></label>
				<input type="text" name="annam_limo_dropoff" id="annam-limo-dropoff" maxlength="200" placeholder="<?php esc_attr_e( 'VD: khách sạn Fansipan / gần chợ Sapa…', 'generatepress_child' ); ?>" />
			</div>
		</div>

		<div class="annam-limo-form__actions">
			<button type="submit" class="annam-limo-btn annam-limo-btn--primary" id="annam-limo-submit" data-track="submit_limo_booking">
				<?php echo esc_html( ! empty( $form['submit_label'] ) ? $form['submit_label'] : __( 'Gửi Yêu Cầu Giữ Chỗ', 'generatepress_child' ) ); ?>
			</button>
		</div>

		<div class="annam-limo-form__ajax-notice" id="annam-limo-form-notice" role="alert" hidden></div>

		<?php if ( $notice ) : ?>
			<div class="annam-limo-notice annam-limo-notice--<?php echo esc_attr( $notice['type'] ); ?>" role="alert">
				<?php echo esc_html( $notice['message'] ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $form['footer_note'] ) ) : ?>
			<p class="annam-limo-form__note"><?php echo esc_html( $form['footer_note'] ); ?></p>
		<?php endif; ?>
	</form>
</div>
