<?php
/**
 * Danh mục tour (product_cat) dưới bài viết.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'annam_single_get_tour_category_terms' ) ) {
	return;
}

$terms = annam_single_get_tour_category_terms();
if ( empty( $terms ) ) {
	return;
}
?>
<section class="annam-category-showcase annam-single-tour-cats" aria-labelledby="annam-single-tour-cats-title">
	<div class="annam-container grid-container grid-parent">
		<header class="annam-category-showcase__header">
			<h2 id="annam-single-tour-cats-title" class="annam-category-showcase__title"><?php echo esc_html__( 'Khám phá tour cùng An Nam Discovery', 'generatepress_child' ); ?></h2>
			<p class="annam-category-showcase__desc"><?php echo esc_html__( 'Chọn điểm đến phù hợp cho hành trình tiếp theo của bạn.', 'generatepress_child' ); ?></p>
		</header>
		<div class="annam-category-showcase__grid annam-single-tour-cats__grid" role="list">
			<?php
			if ( function_exists( 'annam_render_category_nav_cards' ) ) {
				annam_render_category_nav_cards( $terms, 'single_post' );
			}
			?>
		</div>
	</div>
</section>
