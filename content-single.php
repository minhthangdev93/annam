<?php
/**
 * Single post — layout cẩm nang du lịch (child theme).
 *
 * @package GeneratePress_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$itemprop = '';
if ( function_exists( 'generate_get_schema_type' ) && 'microdata' === generate_get_schema_type() ) {
	$itemprop = ' itemprop="text"';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'annam-single-article' ); ?> <?php if ( function_exists( 'generate_do_microdata' ) ) { generate_do_microdata( 'article' ); } ?>>
	<div class="inside-article">
		<div class="annam-single-post">
			<div class="annam-single-post__container">
				<?php get_template_part( 'template-parts/single/single-hero' ); ?>
			</div>

			<div class="annam-single-main">
				<div class="annam-single-post__content">
					<div class="entry-content annam-single-entry-content"<?php echo $itemprop; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php
						$raw  = get_post_field( 'post_content', get_the_ID() );
						$html = apply_filters( 'the_content', $raw );
						if ( function_exists( 'annam_single_insert_cta_into_html' ) ) {
							$html = annam_single_insert_cta_into_html( $html );
						}
						// Nội dung bài viết đã qua chuỗi filter của WordPress (tương đương the_content).
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $html;

						wp_link_pages(
							array(
								'before' => '<div class="page-links annam-single-page-links">' . esc_html__( 'Trang:', 'generatepress_child' ),
								'after'  => '</div>',
							)
						);
						?>
					</div>
				</div>
			</div>
		</div>

		<?php get_template_part( 'template-parts/single/single-related' ); ?>
		<?php get_template_part( 'template-parts/single/single-tour-categories' ); ?>

		<?php
		if ( function_exists( 'do_action' ) ) {
			do_action( 'generate_after_entry_content' );
			do_action( 'generate_after_content' );
		}
		?>
	</div>
</article>
