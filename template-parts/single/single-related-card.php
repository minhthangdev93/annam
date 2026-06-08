<?php
/**
 * Card trong block bài viết liên quan.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$permalink = get_permalink();
$title     = get_the_title();
$thumb_id  = get_post_thumbnail_id();
$img_url   = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
if ( ! $img_url && function_exists( 'annam_blog_card_placeholder_image_url' ) ) {
	$img_url = annam_blog_card_placeholder_image_url();
}
$excerpt = get_the_excerpt();
if ( '' === trim( wp_strip_all_tags( $excerpt ) ) ) {
	$excerpt = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', get_the_ID() ) ), 22, '…' );
}
?>
<article <?php post_class( 'annam-single-related-card' ); ?> role="listitem">
	<a class="annam-single-related-card__image-link" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( $img_url ) : ?>
			<img class="annam-single-related-card__image" src="<?php echo esc_url( $img_url ); ?>" alt="" loading="lazy" width="640" height="400" />
		<?php endif; ?>
	</a>
	<div class="annam-single-related-card__body">
		<h3 class="annam-single-related-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( wp_strip_all_tags( $title ) ); ?></a>
		</h3>
		<?php if ( get_the_date() ) : ?>
			<time class="annam-single-related-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		<?php endif; ?>
		<p class="annam-single-related-card__excerpt"><?php echo esc_html( wp_strip_all_tags( $excerpt ) ); ?></p>
		<a class="annam-single-related-card__more" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html__( 'Đọc tiếp', 'generatepress_child' ); ?></a>
	</div>
</article>
