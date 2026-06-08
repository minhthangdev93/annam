<?php
/**
 * Trang chủ (template Trang chủ tĩnh): nội dung trang + rút gọn mô tả giống category-intro.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

global $post;
if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
	require get_template_directory() . '/content-page.php';
	return;
}

$content_raw = (string) $post->post_content;
$plain_len   = strlen( wp_strip_all_tags( $content_raw ) );
$show_toggle = $plain_len > 180;

$section_class = 'annam-category-intro annam-category-intro--home';
if ( ! $show_toggle ) {
	$section_class .= ' annam-category-intro--no-toggle';
}

$itemprop = '';
if ( function_exists( 'generate_get_schema_type' ) && 'microdata' === generate_get_schema_type() ) {
	$itemprop = ' itemprop="text"';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php generate_do_microdata( 'article' ); ?>>
	<div class="inside-article">
		<?php
		/**
		 * Hook: generate_before_content.
		 */
		do_action( 'generate_before_content' );
		?>

		<?php if ( generate_show_entry_header() ) : ?>
			<header <?php generate_do_attr( 'entry-header' ); ?>>
				<?php
				do_action( 'generate_before_page_title' );
				if ( generate_show_title() ) {
					$params = generate_get_the_title_parameters();
					the_title( $params['before'], $params['after'] );
				}
				do_action( 'generate_after_page_title' );
				?>
			</header>
		<?php endif; ?>

		<?php do_action( 'generate_after_entry_header' ); ?>

		<?php if ( '' !== trim( $content_raw ) ) : ?>
			<section class="<?php echo esc_attr( $section_class ); ?>">
				<div class="annam-container grid-container grid-parent">
					<div class="annam-category-intro__desc-wrap">
						<div class="annam-category-intro__desc entry-content"<?php echo $itemprop; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php
							the_content();

							wp_link_pages(
								array(
									'before' => '<div class="page-links">' . __( 'Pages:', 'generatepress' ),
									'after'  => '</div>',
								)
							);
							?>
						</div>
					</div>
					<?php if ( $show_toggle ) : ?>
						<button type="button" class="annam-category-intro__toggle" aria-expanded="false">
							<?php echo esc_html__( 'Xem thêm', 'woocommerce' ); ?>
						</button>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php
		/**
		 * Hook: generate_after_content.
		 */
		do_action( 'generate_after_content' );
		?>
	</div>
</article>
