<?php
/**
 * Trang chủ: khối “Khách hàng nói gì về An Nam Discovery” (Trustindex Google).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title = apply_filters(
	'annam_home_reviews_title',
	__( 'Khách hàng nói gì về An Nam Discovery', 'generatepress_child' )
);
$lead  = apply_filters(
	'annam_home_reviews_lead',
	__( 'Đánh giá thật từ Google — cảm ơn quý khách đã tin tưởng đồng hành.', 'generatepress_child' )
);

/**
 * Shortcode Trustindex (Widgets for Google Reviews).
 *
 * @var string
 */
$shortcode = (string) apply_filters( 'annam_home_reviews_shortcode', '[trustindex no-registration=google]' );

if ( '' === trim( $shortcode ) ) {
	return;
}

$widget = do_shortcode( $shortcode );
// Plugin chưa active / shortcode không chạy → không in khối trống.
if ( trim( $widget ) === trim( $shortcode ) || '' === trim( $widget ) ) {
	return;
}
?>
<section class="annam-home-reviews" aria-labelledby="annam-home-reviews-title">
	<div class="annam-home-reviews__inner">
		<header class="annam-home-reviews__header">
			<h2 id="annam-home-reviews-title" class="annam-home-reviews__title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( '' !== trim( (string) $lead ) ) : ?>
				<p class="annam-home-reviews__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>
		<div class="annam-home-reviews__widget">
			<?php echo $widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trustindex shortcode HTML. ?>
		</div>
	</div>
</section>
