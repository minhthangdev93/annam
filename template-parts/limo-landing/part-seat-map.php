<?php
/**
 * Sơ đồ ghế 11 chỗ: tài xế + 1 ghế đầu, 3 hàng × 2 ghế giữa, 3 ghế cuối.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="annam-limo-seatmap" aria-label="<?php esc_attr_e( 'Sơ đồ ghế', 'generatepress_child' ); ?>">
	<p class="annam-limo-seatmap__title"><?php esc_html_e( 'Sơ đồ ghế', 'generatepress_child' ); ?></p>
	<div class="annam-limo-seatmap__body">
		<div class="annam-limo-seatmap__row">
			<span class="annam-limo-seatmap__cell annam-limo-seatmap__cell--driver"><?php esc_html_e( 'Tài xế', 'generatepress_child' ); ?></span>
			<button type="button" class="annam-limo-seatmap__cell" data-annam-pick-seat="seat_front" data-annam-seat-group="seat_front"><?php esc_html_e( 'Đầu', 'generatepress_child' ); ?></button>
		</div>
		<?php for ( $row = 0; $row < 3; $row++ ) : ?>
			<div class="annam-limo-seatmap__row">
				<button type="button" class="annam-limo-seatmap__cell" data-annam-pick-seat="seat_mid" data-annam-seat-group="seat_mid"><?php esc_html_e( 'Giữa', 'generatepress_child' ); ?></button>
				<button type="button" class="annam-limo-seatmap__cell" data-annam-pick-seat="seat_mid" data-annam-seat-group="seat_mid"><?php esc_html_e( 'Giữa', 'generatepress_child' ); ?></button>
			</div>
		<?php endfor; ?>
		<div class="annam-limo-seatmap__row annam-limo-seatmap__row--back">
			<?php for ( $i = 0; $i < 3; $i++ ) : ?>
				<button type="button" class="annam-limo-seatmap__cell" data-annam-pick-seat="seat_back" data-annam-seat-group="seat_back"><?php esc_html_e( 'Cuối', 'generatepress_child' ); ?></button>
			<?php endfor; ?>
		</div>
	</div>
	<p class="annam-limo-seatmap__hint"><?php esc_html_e( 'Chạm một nhóm ghế để chọn hạng. Ghế giữa là lựa chọn mặc định.', 'generatepress_child' ); ?></p>
</div>
