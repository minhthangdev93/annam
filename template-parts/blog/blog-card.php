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
$thumb_id = get_post_thumbnail_id();
$img_html = '';
if ( $thumb_id ) {
	$img_html = wp_get_attachment_image(
		$thumb_id,
		'medium_large',
		false,
		array(
			'class'    => 'annam-blog-card__image',
			'alt'      => '',
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);
}
$img_url = ! $img_html && function_exists( 'annam_blog_card_placeholder_image_url' ) ? annam_blog_card_placeholder_image_url() : '';
$author_id    = (int) get_the_author_meta( 'ID' );
$author_name  = get_the_author();
$author_email = get_the_author_meta( 'user_email' );
$date_display = get_the_date();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'annam-blog-card' ); ?>>
	<a class="annam-blog-card__image-link" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( $img_html ) : ?>
			<?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image. ?>
		<?php elseif ( $img_url ) : ?>
			<img class="annam-blog-card__image" src="<?php echo esc_url( $img_url ); ?>" alt="" loading="lazy" width="640" height="400" decoding="async" />
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
			<div class="annam-blog-card__meta">
				<span class="annam-blog-card__avatar" aria-hidden="true"><?php echo get_avatar( $author_email, 40, '', '', array( 'class' => 'annam-blog-card__avatar-img' ) ); ?></span>
				<span class="annam-blog-card__meta-text">
					<span class="annam-blog-card__author"><?php echo esc_html( $author_name ); ?></span>
					<?php if ( $date_display ) : ?>
						<span class="annam-blog-card__sep" aria-hidden="true">·</span>
						<time class="annam-blog-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( $date_display ); ?></time>
					<?php endif; ?>
				</span>
			</div>
			<a class="annam-blog-card__readmore" href="<?php echo esc_url( $permalink ); ?>">
				<span class="annam-blog-card__readmore-icon" aria-hidden="true">→</span>
				<?php echo esc_html__( 'Đọc chi tiết', 'generatepress_child' ); ?>
			</a>
		</div>
	</div>
</article>
