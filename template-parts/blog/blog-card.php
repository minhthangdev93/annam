<?php
/**
 * Một card bài viết (archive / listing).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$permalink = get_permalink();
$title     = get_the_title();
$excerpt   = get_the_excerpt();
if ( '' === trim( wp_strip_all_tags( $excerpt ) ) ) {
	$excerpt = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', get_the_ID() ) ), 36, '…' );
}
$thumb_id  = get_post_thumbnail_id();
$img_size  = function_exists( 'annam_get_blog_card_image_size' ) ? annam_get_blog_card_image_size() : 'annam-blog-card';
$img_html  = '';
if ( $thumb_id ) {
	$img_html = wp_get_attachment_image(
		$thumb_id,
		$img_size,
		false,
		array(
			'class'    => 'annam-blog-card__image',
			'alt'      => '',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'width'    => 1200,
			'height'   => 675,
		)
	);
}
$img_url      = ! $img_html && function_exists( 'annam_blog_card_placeholder_image_url' ) ? annam_blog_card_placeholder_image_url() : '';
$date_display = get_the_date();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'annam-blog-card' ); ?>>
	<a class="annam-blog-card__image-link" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( $img_html ) : ?>
			<?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image. ?>
		<?php elseif ( $img_url ) : ?>
			<img class="annam-blog-card__image" src="<?php echo esc_url( $img_url ); ?>" alt="" loading="lazy" width="1200" height="675" decoding="async" />
		<?php else : ?>
			<span class="annam-blog-card__image annam-blog-card__image--placeholder" role="img" aria-label="<?php echo esc_attr( $title ); ?>"></span>
		<?php endif; ?>
	</a>
	<div class="annam-blog-card__content">
		<h2 class="annam-blog-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
		</h2>
		<p class="annam-blog-card__excerpt"><?php echo esc_html( wp_strip_all_tags( $excerpt ) ); ?></p>
		<div class="annam-blog-card__footer">
			<?php if ( $date_display ) : ?>
				<div class="annam-blog-card__meta">
					<time class="annam-blog-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( $date_display ); ?></time>
				</div>
			<?php endif; ?>
			<a class="annam-blog-card__readmore" href="<?php echo esc_url( $permalink ); ?>">
				<span class="annam-blog-card__readmore-icon" aria-hidden="true">→</span>
				<?php echo esc_html__( 'Đọc chi tiết', 'generatepress_child' ); ?>
			</a>
		</div>
	</div>
</article>
