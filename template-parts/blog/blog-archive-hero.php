<?php
/**
 * Hero đầu trang blog / archive bài viết (nền ảnh, breadcrumb, tiêu đề cố định).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$bg_url = function_exists( 'annam_blog_hero_background_url' ) ? annam_blog_hero_background_url() : '';
$style  = $bg_url ? '--annam-blog-hero-bg-image: url(' . esc_url( $bg_url ) . ');' : '';
?>
<section class="annam-blog-hero" aria-label="<?php echo esc_attr__( 'Cẩm nang du lịch', 'generatepress_child' ); ?>">
	<div class="annam-blog-hero__media"<?php echo $style ? ' style="' . esc_attr( $style ) . '"' : ''; ?>>
		<span class="annam-blog-hero__overlay" aria-hidden="true"></span>
		<div class="annam-blog-hero__inner">
			<div class="annam-blog-inner">
				<nav class="annam-blog-hero__breadcrumb" aria-label="<?php echo esc_attr__( 'Breadcrumb', 'generatepress_child' ); ?>">
					<ol class="annam-blog-hero__breadcrumb-list">
						<li class="annam-blog-hero__breadcrumb-item">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html__( 'Trang chủ', 'generatepress_child' ); ?></a>
						</li>
						<li class="annam-blog-hero__breadcrumb-item annam-blog-hero__breadcrumb-item--current" aria-current="page">
							<?php echo esc_html__( 'Cẩm nang du lịch', 'generatepress_child' ); ?>
						</li>
					</ol>
				</nav>
				<h1 class="annam-blog-hero__title"><?php echo esc_html__( 'Cẩm nang du lịch', 'generatepress_child' ); ?></h1>
			</div>
		</div>
	</div>
</section>
