<?php
/**
 * Form nhận báo giá thuê limo HN–Sapa.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$config   = annam_limo_charter_landing_get_config();
$form     = isset( $config['form'] ) ? $config['form'] : array();
$defaults = isset( $config['form_defaults'] ) ? $config['form_defaults'] : array();
$notice   = annam_limo_charter_landing_get_notice();

$default_route   = isset( $defaults['route'] ) ? sanitize_key( (string) $defaults['route'] ) : 'hn_sapa';
$default_vehicle = isset( $defaults['vehicle'] ) ? sanitize_key( (string) $defaults['vehicle'] ) : '9';
$default_time    = isset( $defaults['time'] ) ? (string) $defaults['time'] : '07:00';
$today           = wp_date( 'Y-m-d' );

// Thuê nguyên xe: chủ động giờ — đủ 24 khung (mỗi giờ).
$time_options = array();
for ( $h = 0; $h < 24; $h++ ) {
	$time_options[] = sprintf( '%02d:00', $h );
}
if ( ! in_array( $default_time, $time_options, true ) ) {
	$default_time = '07:00';
}
?>
<div class="annam-limo-charter-form-wrap" id="nhan-bao-gia">
	<div id="annam-limo-charter-booking">
		<?php if ( ! empty( $form['title'] ) ) : ?>
			<h2 class="annam-limo-charter-form__title"><?php echo esc_html( $form['title'] ); ?></h2>
		<?php endif; ?>
		<?php if ( ! empty( $form['subtitle'] ) ) : ?>
			<p class="annam-limo-charter-form__subtitle"><?php echo esc_html( $form['subtitle'] ); ?></p>
		<?php endif; ?>

		<form class="annam-limo-charter-form" id="annam-limo-charter-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate data-annam-limo-charter-form>
			<input type="hidden" name="annam_limo_charter_submit" value="1" />
			<input type="hidden" name="annam_limo_charter_ts" id="annam-limo-charter-ts" value="<?php echo esc_attr( (string) time() ); ?>" />
			<input type="hidden" name="annam_limo_charter_nonce" id="annam-limo-charter-nonce" value="<?php echo esc_attr( wp_create_nonce( 'annam_limo_charter_lead' ) ); ?>" />
			<p class="annam-limo-charter-form__hp" aria-hidden="true">
				<label for="annam-limo-charter-website">Website</label>
				<input type="text" name="annam_limo_charter_website" id="annam-limo-charter-website" tabindex="-1" autocomplete="off" />
			</p>

			<div class="annam-limo-charter-form__field">
				<label for="annam-limo-charter-route"><?php esc_html_e( 'Tuyến', 'generatepress_child' ); ?> <span class="annam-limo-charter-req">*</span></label>
				<select name="annam_limo_charter_route" id="annam-limo-charter-route" required data-annam-field="route">
					<option value="hn_sapa" <?php selected( $default_route, 'hn_sapa' ); ?>><?php esc_html_e( 'Hà Nội → Sapa', 'generatepress_child' ); ?></option>
					<option value="sapa_hn" <?php selected( $default_route, 'sapa_hn' ); ?>><?php esc_html_e( 'Sapa → Hà Nội', 'generatepress_child' ); ?></option>
				</select>
			</div>

			<div class="annam-limo-charter-form__row annam-limo-charter-form__row--2">
				<div class="annam-limo-charter-form__field">
					<label for="annam-limo-charter-date"><?php esc_html_e( 'Ngày đi', 'generatepress_child' ); ?> <span class="annam-limo-charter-req">*</span></label>
					<input type="date" name="annam_limo_charter_date" id="annam-limo-charter-date" value="<?php echo esc_attr( $today ); ?>" min="<?php echo esc_attr( $today ); ?>" required data-annam-field="date" />
				</div>
				<div class="annam-limo-charter-form__field">
					<label for="annam-limo-charter-time"><?php esc_html_e( 'Giờ dự kiến', 'generatepress_child' ); ?></label>
					<select name="annam_limo_charter_time" id="annam-limo-charter-time" data-annam-field="time">
						<?php foreach ( $time_options as $time_opt ) : ?>
							<option value="<?php echo esc_attr( $time_opt ); ?>" <?php selected( $default_time, $time_opt ); ?>><?php echo esc_html( $time_opt ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="annam-limo-charter-form__field">
				<label for="annam-limo-charter-vehicle"><?php esc_html_e( 'Loại xe', 'generatepress_child' ); ?> <span class="annam-limo-charter-req">*</span></label>
				<select name="annam_limo_charter_vehicle" id="annam-limo-charter-vehicle" required data-annam-field="vehicle">
					<option value="9" <?php selected( $default_vehicle, '9' ); ?>><?php esc_html_e( 'Limousine 9 chỗ — 4.200.000đ', 'generatepress_child' ); ?></option>
					<option value="11" <?php selected( $default_vehicle, '11' ); ?>><?php esc_html_e( 'Limousine 11 chỗ — 4.500.000đ', 'generatepress_child' ); ?></option>
					<option value="undecided" <?php selected( $default_vehicle, 'undecided' ); ?>><?php esc_html_e( 'Chưa quyết định', 'generatepress_child' ); ?></option>
				</select>
			</div>

			<div class="annam-limo-charter-form__field">
				<label for="annam-limo-charter-phone"><?php esc_html_e( 'Số điện thoại / Zalo', 'generatepress_child' ); ?> <span class="annam-limo-charter-req">*</span></label>
				<input type="tel" name="annam_limo_charter_phone" id="annam-limo-charter-phone" required maxlength="25" inputmode="tel" autocomplete="tel" placeholder="<?php esc_attr_e( 'Nhập SĐT hoặc Zalo', 'generatepress_child' ); ?>" data-annam-field="phone" />
			</div>

			<div class="annam-limo-charter-form__actions">
				<button type="submit" class="annam-limo-charter-btn annam-limo-charter-btn--primary" id="annam-limo-charter-submit">
					<?php echo esc_html( ! empty( $form['submit_label'] ) ? $form['submit_label'] : __( 'Nhận Báo Giá — Để Lại SĐT', 'generatepress_child' ) ); ?>
				</button>
			</div>

			<div class="annam-limo-charter-form__ajax-notice" id="annam-limo-charter-form-notice" role="alert" hidden></div>

			<?php if ( $notice ) : ?>
				<div class="annam-limo-charter-notice annam-limo-charter-notice--<?php echo esc_attr( $notice['type'] ); ?>" role="alert">
					<?php echo esc_html( $notice['message'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $form['footer_note'] ) ) : ?>
				<p class="annam-limo-charter-form__note"><?php echo esc_html( $form['footer_note'] ); ?></p>
			<?php endif; ?>
		</form>
	</div>
</div>
