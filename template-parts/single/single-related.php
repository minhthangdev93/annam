<?php
/**
 * Bài viết liên quan (cùng category).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$current_id = (int) get_the_ID();
$cats       = get_the_category( $current_id );
$cat_ids    = array();
foreach ( $cats as $c ) {
	if ( $c instanceof WP_Term ) {
		$cat_ids[] = (int) $c->term_id;
	}
}
$cat_ids = array_values( array_filter( array_unique( $cat_ids ) ) );

if ( empty( $cat_ids ) ) {
	return;
}

$related = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 4,
		'post__not_in'        => array( $current_id ),
		'category__in'        => $cat_ids,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $related->have_posts() ) {
	return;
}
?>
<section class="annam-single-related" aria-labelledby="annam-single-related-title">
	<div class="annam-single-related__inner">
		<h2 id="annam-single-related-title" class="annam-single-related__heading"><?php echo esc_html__( 'Bài viết liên quan', 'generatepress_child' ); ?></h2>
		<div class="annam-single-related__track" role="list">
			<?php
			while ( $related->have_posts() ) :
				$related->the_post();
				get_template_part( 'template-parts/single/single-related-card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
