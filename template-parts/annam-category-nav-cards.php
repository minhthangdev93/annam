<?php
/**
 * Shared markup: product_cat navigation cards (cells only).
 * Expects $terms (array of WP_Term) from get_template_part $args.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Dữ liệu ưu tiên từ get_template_part(..., $args), fallback về query_var/GLOBALS để tương thích cũ.
 */
$terms = isset( $args['annam_category_nav_terms'] ) && is_array( $args['annam_category_nav_terms'] )
	? $args['annam_category_nav_terms']
	: get_query_var( 'annam_category_nav_terms', array() );
if ( ! is_array( $terms ) || empty( $terms ) ) {
	$terms = isset( $GLOBALS['annam_category_nav_terms'] ) && is_array( $GLOBALS['annam_category_nav_terms'] )
		? $GLOBALS['annam_category_nav_terms']
		: array();
}
if ( empty( $terms ) ) {
	return;
}

$context = isset( $args['annam_category_nav_context'] ) && is_string( $args['annam_category_nav_context'] )
	? $args['annam_category_nav_context']
	: get_query_var( 'annam_category_nav_context', '' );

$is_home_nav = ( 'home' === $context );

foreach ( $terms as $term ) :
	if ( ! $term instanceof WP_Term ) {
		continue;
	}
	$link = get_term_link( $term );
	if ( is_wp_error( $link ) ) {
		continue;
	}

	$display_name = function_exists( 'annam_showcase_category_short_name' )
		? annam_showcase_category_short_name( $term )
		: $term->name;

	$img_id  = function_exists( 'annam_get_product_cat_card_image_id' ) ? annam_get_product_cat_card_image_id( $term->term_id ) : 0;
	$has_img = $img_id > 0;

	$count = function_exists( 'annam_get_product_category_effective_count' )
		? annam_get_product_category_effective_count( $term )
		: ( isset( $term->count ) ? (int) $term->count : 0 );

	$fb_idx = function_exists( 'annam_showcase_category_fallback_index' )
		? annam_showcase_category_fallback_index( $term )
		: ( absint( $term->term_id ) % 6 );

	$letter = function_exists( 'mb_substr' )
		? mb_substr( $display_name, 0, 1, 'UTF-8' )
		: substr( $display_name, 0, 1 );
	if ( '' === $letter ) {
		$letter = '?';
	}
	?>
	<div class="annam-category-showcase__cell" role="listitem">
		<a class="annam-category-card<?php echo $is_home_nav ? ' annam-home-category-card' : ''; ?>" href="<?php echo esc_url( $link ); ?>">
			<div class="annam-category-card__image-wrap">
				<div class="annam-category-card__image">
					<?php if ( $has_img ) : ?>
						<?php
						$nav_size = function_exists( 'annam_get_category_nav_card_image_size' ) ? annam_get_category_nav_card_image_size() : 'medium_large';
						echo wp_get_attachment_image(
							$img_id,
							$nav_size,
							false,
							array(
								'class'    => 'annam-category-card__img',
								'alt'      => wp_strip_all_tags( $display_name ),
								'loading'  => 'lazy',
								'decoding' => 'async',
							)
						);
						?>
					<?php else : ?>
						<div class="annam-category-card__fallback annam-category-card__fallback--<?php echo (int) $fb_idx; ?>" aria-hidden="true">
							<span class="annam-category-card__fallback-letter"><?php echo esc_html( $letter ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="annam-category-card__body">
				<h3 class="annam-category-card__title<?php echo $is_home_nav ? ' annam-home-category-card__title' : ''; ?>"><?php echo esc_html( $display_name ); ?></h3>
				<p class="annam-category-card__count<?php echo $is_home_nav ? ' annam-home-category-card__count' : ''; ?>">
					<span class="annam-category-card__count-line">
						<span class="annam-category-card__count-num"><?php echo esc_html( number_format_i18n( $count ) ); ?></span>
						<span class="annam-category-card__count-label"><?php esc_html_e( 'TOUR', 'generatepress_child' ); ?></span>
					</span>
				</p>
			</div>
		</a>
	</div>
	<?php
endforeach;
