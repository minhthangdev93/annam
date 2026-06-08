<?php
/**
 * Template Name: Cẩm nang du lịch (danh sách bài)
 * Trang hiển thị danh sách bài viết dạng card (WP_Query).
 *
 * @package GeneratePress_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

$annam_blog_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 10,
		'paged'          => $paged,
	)
);
?>

	<div <?php generate_do_attr( 'content' ); ?>>
		<main <?php generate_do_attr( 'main' ); ?>>
			<?php do_action( 'generate_before_main_content' ); ?>

			<?php get_template_part( 'template-parts/blog/blog-archive', 'hero' ); ?>

			<div class="annam-blog-archive annam-blog-archive--page-template">
				<div class="annam-blog-archive__container annam-blog-inner">
					<?php if ( $annam_blog_query->have_posts() ) : ?>
						<div class="annam-blog-list">
							<?php
							while ( $annam_blog_query->have_posts() ) :
								$annam_blog_query->the_post();
								get_template_part( 'template-parts/blog/blog', 'card' );
							endwhile;
							wp_reset_postdata();
							?>
						</div>
						<?php annam_blog_pagination( $annam_blog_query ); ?>
					<?php else : ?>
						<p class="annam-blog-archive__empty"><?php echo esc_html__( 'Chưa có bài viết.', 'generatepress_child' ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<?php do_action( 'generate_after_main_content' ); ?>
		</main>
	</div>

	<?php
	do_action( 'generate_after_primary_content_area' );

	generate_construct_sidebars();

	get_footer();
