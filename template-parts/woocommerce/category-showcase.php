<?php
/**
 * Featured product_cat cards — below category product list (navigation).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! taxonomy_exists( 'product_cat' ) ) {
	return;
}

$terms = function_exists( 'annam_resolve_category_showcase_terms' ) ? annam_resolve_category_showcase_terms() : array();

if ( empty( $terms ) ) {
	return;
}
?>
<section class="annam-category-showcase" aria-labelledby="annam-category-showcase-heading">
	<div class="annam-container grid-container grid-parent">
		<header class="annam-category-showcase__header">
			<h2 id="annam-category-showcase-heading" class="annam-category-showcase__title">
				<?php esc_html_e( 'Khám phá thêm các tour du lịch cùng An Nam Discovery', 'generatepress_child' ); ?>
			</h2>
			<p class="annam-category-showcase__desc">
				<?php esc_html_e( 'Đơn vị tư vấn tour, du thuyền và dịch vụ du lịch miền Bắc uy tín.', 'generatepress_child' ); ?>
			</p>
		</header>

		<div class="annam-category-showcase__grid" role="list">
			<?php
			if ( function_exists( 'annam_render_category_nav_cards' ) ) {
				annam_render_category_nav_cards( $terms );
			}
			?>
		</div>
	</div>
</section>
