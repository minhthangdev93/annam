<?php
/**
 * Form giữ chỗ Tour Sapa 3N2Đ.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$config = isset( $args['config'] ) && is_array( $args['config'] )
	? $args['config']
	: ( function_exists( 'annam_tour_sapa_landing_get_config' ) ? annam_tour_sapa_landing_get_config() : array() );
$form   = isset( $config['form'] ) ? $config['form'] : array();
$defs   = isset( $config['form_defaults'] ) ? $config['form_defaults'] : array();
$today  = wp_date( 'Y-m-d' );
$hotel  = isset( $defs['hotel'] ) ? $defs['hotel'] : '3star';
?>
<div class="annam-tour-sapa-form-wrap" id="annam-tour-sapa-booking">
	<?php if ( ! empty( $form['title'] ) ) : ?>
		<h2 class="annam-tour-sapa-form__title"><?php echo esc_html( $form['title'] ); ?></h2>
	<?php endif; ?>
	<?php if ( ! empty( $form['subtitle'] ) ) : ?>
		<p class="annam-tour-sapa-form__subtitle"><?php echo esc_html( $form['subtitle'] ); ?></p>
	<?php endif; ?>

	<form class="annam-tour-sapa-form" id="annam-tour-sapa-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate data-annam-tour-sapa-form>
		<input type="hidden" name="annam_tour_sapa_ts" id="annam-tour-sapa-ts" value="<?php echo esc_attr( (string) time() ); ?>" />
		<input type="hidden" name="annam_tour_sapa_nonce" id="annam-tour-sapa-nonce" value="<?php echo esc_attr( wp_create_nonce( 'annam_tour_sapa_booking' ) ); ?>" />
		<p class="annam-tour-sapa-form__hp" aria-hidden="true">
			<label for="annam-tour-sapa-website"><?php esc_html_e( 'Website', 'generatepress_child' ); ?></label>
			<input type="text" name="annam_tour_sapa_website" id="annam-tour-sapa-website" tabindex="-1" autocomplete="off" />
		</p>

		<div class="annam-tour-sapa-form__row annam-tour-sapa-form__row--2">
			<div class="annam-tour-sapa-form__field">
				<label for="annam-tour-sapa-date"><?php esc_html_e( 'Ngày khởi hành', 'generatepress_child' ); ?> <span class="annam-tour-sapa-req">*</span></label>
				<input type="date" name="annam_tour_sapa_date" id="annam-tour-sapa-date" value="<?php echo esc_attr( $today ); ?>" min="<?php echo esc_attr( $today ); ?>" required />
			</div>
			<div class="annam-tour-sapa-form__field">
				<label for="annam-tour-sapa-hotel"><?php esc_html_e( 'Hạng khách sạn', 'generatepress_child' ); ?> <span class="annam-tour-sapa-req">*</span></label>
				<select name="annam_tour_sapa_hotel" id="annam-tour-sapa-hotel" required>
					<option value="3star" <?php selected( $hotel, '3star' ); ?>><?php esc_html_e( 'KS 3 sao — 2.990.000đ', 'generatepress_child' ); ?></option>
					<option value="4star" <?php selected( $hotel, '4star' ); ?>><?php esc_html_e( 'KS 4 sao — 3.990.000đ', 'generatepress_child' ); ?></option>
				</select>
			</div>
		</div>

		<div class="annam-tour-sapa-form__row annam-tour-sapa-form__row--2">
			<div class="annam-tour-sapa-form__field">
				<label for="annam-tour-sapa-guests"><?php esc_html_e( 'Số khách', 'generatepress_child' ); ?></label>
				<input type="number" name="annam_tour_sapa_guests" id="annam-tour-sapa-guests" min="1" max="30" value="1" />
			</div>
			<div class="annam-tour-sapa-form__field">
				<label for="annam-tour-sapa-name"><?php esc_html_e( 'Họ và tên', 'generatepress_child' ); ?> <span class="annam-tour-sapa-req">*</span></label>
				<input type="text" name="annam_tour_sapa_name" id="annam-tour-sapa-name" required maxlength="100" autocomplete="name" placeholder="<?php esc_attr_e( 'Nhập họ và tên', 'generatepress_child' ); ?>" />
			</div>
		</div>

		<div class="annam-tour-sapa-form__row">
			<div class="annam-tour-sapa-form__field">
				<label for="annam-tour-sapa-phone"><?php esc_html_e( 'SĐT / Zalo / WhatsApp', 'generatepress_child' ); ?> <span class="annam-tour-sapa-req">*</span></label>
				<input type="tel" name="annam_tour_sapa_phone" id="annam-tour-sapa-phone" required maxlength="25" inputmode="tel" autocomplete="tel" placeholder="<?php esc_attr_e( 'Nhập SĐT, Zalo hoặc WhatsApp', 'generatepress_child' ); ?>" />
			</div>
		</div>

		<div class="annam-tour-sapa-form__row">
			<div class="annam-tour-sapa-form__field">
				<label for="annam-tour-sapa-note"><?php esc_html_e( 'Ghi chú (tuỳ chọn)', 'generatepress_child' ); ?></label>
				<textarea name="annam_tour_sapa_note" id="annam-tour-sapa-note" rows="2" maxlength="500" placeholder="<?php esc_attr_e( 'Điểm đón mong muốn, trẻ em…', 'generatepress_child' ); ?>"></textarea>
			</div>
		</div>

		<div class="annam-tour-sapa-form__actions">
			<button type="submit" class="annam-tour-sapa-btn annam-tour-sapa-btn--gold" id="annam-tour-sapa-submit" name="annam_tour_sapa_submit" value="1">
				<?php echo esc_html( ! empty( $form['submit_label'] ) ? $form['submit_label'] : __( 'Gửi Yêu Cầu Giữ Chỗ', 'generatepress_child' ) ); ?>
			</button>
		</div>
		<div class="annam-tour-sapa-form__ajax-notice" id="annam-tour-sapa-form-notice" role="alert" hidden></div>
		<?php if ( ! empty( $form['footer_note'] ) ) : ?>
			<p class="annam-tour-sapa-form__note"><?php echo esc_html( $form['footer_note'] ); ?></p>
		<?php endif; ?>
	</form>
</div>
