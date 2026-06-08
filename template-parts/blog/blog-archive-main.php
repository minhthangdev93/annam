<?php
/**
 * Nội dung chính: tiêu đề archive + danh sách card + phân trang.
 * Dùng với main query (archive / home).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $loop_context ) ) {
	$loop_context = 'archive';
}
$loop_context = (string) $loop_context;

/**
 * generate_before_loop hook.
 */
do_action( 'generate_before_loop', $loop_context );

get_template_part( 'template-parts/blog/blog-archive', 'hero' );
?>
<div class="annam-blog-archive">
	<div class="annam-blog-archive__container annam-blog-inner">
		<div class="annam-blog-list">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/blog/blog', 'card' );
			endwhile;
			?>
		</div>
		<?php annam_blog_pagination(); ?>
	</div>
</div>
<?php
/**
 * generate_after_loop hook (plugin khác có thể gắn; phân trang GP đã tắt bằng filter).
 */
do_action( 'generate_after_loop', $loop_context );
