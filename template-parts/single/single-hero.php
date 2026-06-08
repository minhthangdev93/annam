<?php
/**
 * Hero single post: breadcrumb, tiêu đề, meta, ảnh đại diện.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$title       = get_the_title();
$hero_url    = get_the_post_thumbnail_url( get_the_ID(), 'large' );
$fallback    = function_exists( 'annam_single_post_hero_fallback_image_url' ) ? annam_single_post_hero_fallback_image_url() : '';
$hero_url    = $hero_url ? $hero_url : $fallback;
$list_url    = function_exists( 'annam_single_cam_nang_list_url' ) ? annam_single_cam_nang_list_url() : home_url( '/' );
$author_name = get_the_author();
$date_iso    = get_the_date( 'c' );
$date_human  = get_the_date();
?>
<section class="annam-single-hero" aria-labelledby="annam-single-hero-title">
	<div class="annam-single-hero__inner">
		<nav class="annam-single-hero__breadcrumb" aria-label="<?php echo esc_attr__( 'Breadcrumb', 'generatepress_child' ); ?>">
			<ol class="annam-single-hero__breadcrumb-list">
				<li class="annam-single-hero__breadcrumb-item">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html__( 'Trang chủ', 'generatepress_child' ); ?></a>
				</li>
				<li class="annam-single-hero__breadcrumb-item">
					<a href="<?php echo esc_url( $list_url ); ?>"><?php echo esc_html__( 'Cẩm nang du lịch', 'generatepress_child' ); ?></a>
				</li>
				<li class="annam-single-hero__breadcrumb-item annam-single-hero__breadcrumb-item--current" aria-current="page">
					<?php echo esc_html( wp_strip_all_tags( $title ) ); ?>
				</li>
			</ol>
		</nav>

		<h1 id="annam-single-hero-title" class="annam-single-hero__title"><?php echo esc_html( wp_strip_all_tags( $title ) ); ?></h1>

		<div class="annam-single-hero__meta">
			<span class="annam-single-hero__meta-item">
				<span class="annam-single-hero__meta-label"><?php echo esc_html__( 'Tác giả', 'generatepress_child' ); ?></span>
				<span class="annam-single-hero__meta-value"><?php echo esc_html( $author_name ); ?></span>
			</span>
			<?php if ( $date_human ) : ?>
				<span class="annam-single-hero__meta-sep" aria-hidden="true">·</span>
				<span class="annam-single-hero__meta-item">
					<span class="annam-single-hero__meta-label"><?php echo esc_html__( 'Ngày đăng', 'generatepress_child' ); ?></span>
					<time class="annam-single-hero__meta-value" datetime="<?php echo esc_attr( $date_iso ); ?>"><?php echo esc_html( $date_human ); ?></time>
				</span>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $hero_url ) : ?>
		<div class="annam-single-hero__media annam-single-post__featured-image">
			<img
				class="annam-single-hero__img"
				src="<?php echo esc_url( $hero_url ); ?>"
				alt="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>"
				width="1024"
				height="576"
				loading="eager"
				decoding="async"
				fetchpriority="high"
			/>
		</div>
	<?php endif; ?>
</section>
