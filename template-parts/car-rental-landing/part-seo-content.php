<?php
/**
 * Bài SEO dài từ editor trang (sau FAQ).
 *
 * @package GeneratePress_Child
 *
 * @var array $args Template args.
 */

defined( 'ABSPATH' ) || exit;

$page_id = isset( $args['page_id'] ) ? (int) $args['page_id'] : get_the_ID();
if ( ! $page_id || ! function_exists( 'annam_car_rental_get_page_content_html' ) ) {
	return;
}

$content_html = annam_car_rental_get_page_content_html( $page_id );
if ( '' === trim( wp_strip_all_tags( $content_html ) ) ) {
	return;
}

$toggle_min = (int) apply_filters( 'annam_car_rental_page_content_toggle_min_chars', 400 );
$toggle_min = max( 200, $toggle_min );
$is_long    = strlen( wp_strip_all_tags( $content_html ) ) > $toggle_min;
$page_title = get_the_title( $page_id );
$heading    = $page_title
	? sprintf(
		/* translators: %s: page title e.g. Thuê xe 7 chỗ */
		__( 'Thông tin chi tiết về %s', 'generatepress_child' ),
		$page_title
	)
	: __( 'Thông tin chi tiết', 'generatepress_child' );
?>
<section
	class="annam-cr-section annam-cr-seo-content<?php echo $is_long ? '' : ' annam-cr-seo-content--no-toggle'; ?>"
	id="noi-dung-seo"
>
	<div class="annam-cr-container">
		<div class="annam-cr-seo-content__card">
			<h2 class="annam-cr-seo-content__heading"><?php echo esc_html( $heading ); ?></h2>
			<div class="annam-cr-seo-content__body-wrap">
				<div class="annam-cr-seo-content__body entry-content">
					<?php echo $content_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content filters. ?>
				</div>
			</div>
			<?php if ( $is_long ) : ?>
				<button
					type="button"
					class="annam-cr-seo-content__toggle"
					aria-expanded="false"
					data-label-more="<?php echo esc_attr__( 'Xem thêm', 'generatepress_child' ); ?>"
					data-label-less="<?php echo esc_attr__( 'Thu gọn', 'generatepress_child' ); ?>"
				>
					<?php esc_html_e( 'Xem thêm', 'generatepress_child' ); ?>
				</button>
			<?php endif; ?>
		</div>
	</div>
</section>
