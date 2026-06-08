<?php
/**
 * Title + description + read more (shop, category, tag).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title       = '';
$description = '';

if ( is_product_category() ) {
	$term = annam_get_current_product_category();
	if ( $term ) {
		$title       = single_term_title( '', false ) ? single_term_title( '', false ) : $term->name;
		$description = term_description( $term->term_id, 'product_cat' );
	}
} elseif ( is_product_tag() ) {
	$tag = get_queried_object();
	if ( $tag instanceof WP_Term && 'product_tag' === $tag->taxonomy ) {
		$title       = single_term_title( '', false ) ? single_term_title( '', false ) : $tag->name;
		$description = term_description( $tag->term_id, 'product_tag' );
	}
} elseif ( is_shop() ) {
	$title = function_exists( 'woocommerce_page_title' ) ? woocommerce_page_title( false ) : '';
	if ( ! $title ) {
		$title = __( 'Shop', 'woocommerce' );
	}
	$shop_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
	if ( $shop_id > 0 ) {
		$post = get_post( $shop_id );
		if ( $post && ! empty( $post->post_content ) ) {
			$description = apply_filters( 'the_content', $post->post_content );
		}
	}
}

$description = is_string( $description ) ? $description : '';
$plain_len   = strlen( wp_strip_all_tags( $description ) );
$min_toggle  = (int) apply_filters( 'annam_category_intro_toggle_min_chars', 180 );
$min_toggle  = max( 80, $min_toggle );
$show_toggle = $plain_len > $min_toggle;
$section_class = 'annam-category-intro annam-category-description';
if ( ! $show_toggle ) {
	$section_class .= ' annam-category-intro--no-toggle';
}
if ( is_product_category() ) {
	$section_class .= ' annam-category-intro--no-heading';
}
?>
<section class="<?php echo esc_attr( $section_class ); ?>">
	<div class="annam-container grid-container grid-parent">
		<?php if ( ! is_product_category() ) : ?>
			<h1 class="annam-category-intro__heading"><?php echo esc_html( $title ); ?></h1>
		<?php endif; ?>
		<?php if ( $description ) : ?>
			<div class="annam-category-intro__desc-wrap">
				<div class="annam-category-intro__desc annam-category-description__content">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			</div>
			<?php if ( $show_toggle ) : ?>
				<button
					type="button"
					class="annam-category-intro__toggle"
					aria-expanded="false"
					data-label-more="<?php echo esc_attr__( 'Xem thêm', 'generatepress_child' ); ?>"
					data-label-less="<?php echo esc_attr__( 'Thu gọn', 'generatepress_child' ); ?>"
				>
					<?php echo esc_html__( 'Xem thêm', 'generatepress_child' ); ?>
				</button>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
