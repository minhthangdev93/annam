<?php
/**
 * Form đặt vé cabin VIP.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$config   = annam_cabin_landing_get_config();
$form     = isset( $config['form'] ) ? $config['form'] : array();
$defaults = isset( $config['form_defaults'] ) ? $config['form_defaults'] : array();
$notice   = annam_cabin_landing_get_notice();

$default_from  = isset( $defaults['from'] ) ? sanitize_key( (string) $defaults['from'] ) : 'hanoi';
$default_to    = isset( $defaults['to'] ) ? sanitize_key( (string) $defaults['to'] ) : 'sapa';
$default_cabin = isset( $defaults['cabin_type'] ) ? sanitize_key( (string) $defaults['cabin_type'] ) : 'single_floor2';
$today         = wp_date( 'Y-m-d' );
$route_map     = annam_cabin_landing_get_route_destinations_map();
$place_labels  = array(
	'hanoi'  => 'Hà Nội',
	'sapa'   => 'Sapa',
	'laocai' => 'Lào Cai',
);
?>
<div class="annam-cabin-form-wrap annam-cabin-form-wrap--prominent" id="annam-cabin-booking">
	<div class="annam-cabin-form__ajax-notice" id="annam-cabin-form-notice" role="alert" hidden></div>

	<?php if ( $notice ) : ?>
		<div class="annam-cabin-notice annam-cabin-notice--<?php echo esc_attr( $notice['type'] ); ?>" role="alert">
			<?php echo esc_html( $notice['message'] ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $form['title'] ) ) : ?>
		<h2 class="annam-cabin-form__title"><?php echo esc_html( $form['title'] ); ?></h2>
	<?php endif; ?>
	<?php if ( ! empty( $form['subtitle'] ) ) : ?>
		<p class="annam-cabin-form__subtitle"><?php echo esc_html( $form['subtitle'] ); ?></p>
	<?php endif; ?>

	<form class="annam-cabin-form" id="annam-cabin-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate data-annam-cabin-form>
		<input type="hidden" name="annam_cabin_submit" value="1" />
		<input type="hidden" name="annam_cabin_ts" id="annam-cabin-ts" value="<?php echo esc_attr( (string) time() ); ?>" />
		<input type="hidden" name="annam_cabin_nonce" id="annam-cabin-nonce" value="<?php echo esc_attr( wp_create_nonce( 'annam_cabin_booking' ) ); ?>" />
		<p class="annam-cabin-form__hp" aria-hidden="true">
			<label for="annam-cabin-website">Website</label>
			<input type="text" name="annam_cabin_website" id="annam-cabin-website" tabindex="-1" autocomplete="off" />
		</p>

		<p class="annam-cabin-form__hint annam-cabin-form__hint--time" id="annam-cabin-time-hint" hidden></p>

		<div class="annam-cabin-form__row annam-cabin-form__row--2">
			<div class="annam-cabin-form__field">
				<label for="annam-cabin-from"><?php esc_html_e( 'Điểm đón', 'generatepress_child' ); ?> <span class="annam-cabin-req">*</span></label>
				<select name="annam_cabin_from" id="annam-cabin-from" required data-annam-field="from">
					<?php foreach ( array_keys( $route_map ) as $place ) : ?>
						<option value="<?php echo esc_attr( $place ); ?>" <?php selected( $default_from, $place ); ?>><?php echo esc_html( $place_labels[ $place ] ?? $place ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="annam-cabin-form__field">
				<label for="annam-cabin-to"><?php esc_html_e( 'Điểm trả', 'generatepress_child' ); ?> <span class="annam-cabin-req">*</span></label>
				<select name="annam_cabin_to" id="annam-cabin-to" required data-annam-field="to">
					<?php
					$destinations = isset( $route_map[ $default_from ] ) ? $route_map[ $default_from ] : array();
					foreach ( $destinations as $place ) :
						?>
						<option value="<?php echo esc_attr( $place ); ?>" <?php selected( $default_to, $place ); ?>><?php echo esc_html( $place_labels[ $place ] ?? $place ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="annam-cabin-form__row annam-cabin-form__row--2">
			<div class="annam-cabin-form__field">
				<label for="annam-cabin-date"><?php esc_html_e( 'Ngày đi', 'generatepress_child' ); ?> <span class="annam-cabin-req">*</span></label>
				<input type="date" name="annam_cabin_date" id="annam-cabin-date" value="<?php echo esc_attr( $today ); ?>" min="<?php echo esc_attr( $today ); ?>" required data-annam-field="date" />
			</div>
			<div class="annam-cabin-form__field">
				<label for="annam-cabin-time"><?php esc_html_e( 'Giờ đi', 'generatepress_child' ); ?> <span class="annam-cabin-req">*</span></label>
				<select name="annam_cabin_time" id="annam-cabin-time" required data-annam-field="time">
					<option value=""><?php esc_html_e( '— Chọn giờ —', 'generatepress_child' ); ?></option>
				</select>
			</div>
		</div>

		<div class="annam-cabin-form__row annam-cabin-form__row--2">
			<div class="annam-cabin-form__field">
				<label for="annam-cabin-type"><?php esc_html_e( 'Loại cabin', 'generatepress_child' ); ?> <span class="annam-cabin-req">*</span></label>
				<select name="annam_cabin_type" id="annam-cabin-type" required data-annam-field="cabin_type">
					<?php foreach ( $config['cabin_types'] as $ct ) : ?>
						<option value="<?php echo esc_attr( $ct['value'] ); ?>" <?php selected( $default_cabin, $ct['value'] ); ?>><?php echo esc_html( $ct['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="annam-cabin-form__field">
				<label for="annam-cabin-guests"><?php esc_html_e( 'Số lượng khách', 'generatepress_child' ); ?></label>
				<input type="number" name="annam_cabin_guests" id="annam-cabin-guests" min="1" max="20" value="1" />
			</div>
		</div>

		<div class="annam-cabin-form__row annam-cabin-form__row--2">
			<div class="annam-cabin-form__field">
				<label for="annam-cabin-name"><?php esc_html_e( 'Họ tên', 'generatepress_child' ); ?> <span class="annam-cabin-req">*</span></label>
				<input type="text" name="annam_cabin_name" id="annam-cabin-name" required maxlength="100" autocomplete="name" placeholder="<?php esc_attr_e( 'Nhập họ và tên của quý khách', 'generatepress_child' ); ?>" />
			</div>
			<div class="annam-cabin-form__field">
				<label for="annam-cabin-phone"><?php esc_html_e( 'Số điện thoại / Zalo', 'generatepress_child' ); ?> <span class="annam-cabin-req">*</span></label>
				<input type="tel" name="annam_cabin_phone" id="annam-cabin-phone" required maxlength="25" inputmode="tel" autocomplete="tel" placeholder="<?php esc_attr_e( 'Nhập số điện thoại hoặc Zalo', 'generatepress_child' ); ?>" />
			</div>
		</div>

		<div class="annam-cabin-form__actions">
			<button type="submit" class="annam-cabin-btn annam-cabin-btn--primary" id="annam-cabin-submit" data-track="submit_booking_form">
				<?php echo esc_html( ! empty( $form['submit_label'] ) ? $form['submit_label'] : __( 'Gửi Yêu Cầu Giữ Chỗ', 'generatepress_child' ) ); ?>
			</button>
		</div>
		<?php if ( ! empty( $form['footer_note'] ) ) : ?>
			<p class="annam-cabin-form__note"><?php echo esc_html( $form['footer_note'] ); ?></p>
		<?php endif; ?>
	</form>
</div>
