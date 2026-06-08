<?php
/**
 * Category hero: background, overlay, breadcrumb, H1, subtitle, CTAs, badges, chips.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_product_category() ) {
	return;
}

$term = annam_get_current_product_category();
if ( ! $term ) {
	return;
}

$image_url   = annam_get_product_category_hero_image( (int) $term->term_id );
$has_image   = (bool) $image_url;
$subtitle    = annam_get_category_hero_subtitle( $term );
$title       = single_term_title( '', false ) ? single_term_title( '', false ) : $term->name;
$chips       = annam_get_category_hero_chips( $term );
$consult_url = annam_get_category_hero_consult_url( $term );
if ( '' === $consult_url ) {
	$consult_url = 'mailto:' . antispambot( get_option( 'admin_email' ), 0 );
}

$hero_classes = 'annam-category-hero';
if ( ! $has_image ) {
	$hero_classes .= ' annam-category-hero--no-image';
}
?>
<div class="annam-category-hero-outer annam-container grid-container grid-parent">
	<section class="<?php echo esc_attr( $hero_classes ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
		<?php if ( $has_image ) : ?>
			<div class="annam-category-hero__image" style="background-image: url(<?php echo esc_url( $image_url ); ?>);"></div>
		<?php endif; ?>
		<div class="annam-category-hero__overlay" aria-hidden="true"></div>
		<div class="annam-category-hero__content">
			<div class="annam-category-hero__inner">
				<div class="annam-category-hero__breadcrumb">
					<?php
					$annam_rm_bc = '';
					if ( function_exists( 'rank_math_get_breadcrumbs' ) && class_exists( '\RankMath\Helper' ) && \RankMath\Helper::is_breadcrumbs_enabled() ) {
						$annam_rm_bc = rank_math_get_breadcrumbs(
							array(
								'delimiter'   => '<span class="annam-category-hero__bc-sep" aria-hidden="true">/</span>',
								'wrap_before' => '<nav class="annam-category-hero__breadcrumb-nav rank-math-breadcrumb" aria-label="breadcrumb"><p>',
								'wrap_after'  => '</p></nav>',
								'before'      => '',
								'after'       => '',
							)
						);
					}
					if ( is_string( $annam_rm_bc ) && '' !== trim( $annam_rm_bc ) ) {
						echo wp_kses_post( $annam_rm_bc );
					} elseif ( function_exists( 'woocommerce_breadcrumb' ) ) {
						woocommerce_breadcrumb(
							array(
								'delimiter'   => '<span class="annam-category-hero__bc-sep" aria-hidden="true">/</span>',
								'wrap_before' => '<nav class="annam-category-hero__breadcrumb-nav woocommerce-breadcrumb" aria-label="breadcrumb">',
								'wrap_after'  => '</nav>',
								'before'      => '',
								'after'       => '',
							)
						);
					}
					?>
				</div>

				<h1 class="annam-category-hero__title"><?php echo esc_html( $title ); ?></h1>

				<?php if ( $subtitle ) : ?>
					<p class="annam-category-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>

				<ul class="annam-category-hero__badges" aria-label="<?php echo esc_attr( 'Điểm nổi bật' ); ?>">
					<li class="annam-category-hero__badge"><?php echo esc_html( 'Khởi hành hằng ngày' ); ?></li>
					<li class="annam-category-hero__badge"><?php echo esc_html( 'Giá rõ ràng' ); ?></li>
					<li class="annam-category-hero__badge"><?php echo esc_html( 'Hỗ trợ nhanh' ); ?></li>
				</ul>

				<div class="annam-category-hero__ctas">
					<a class="annam-category-hero__cta annam-category-hero__cta--primary" href="#annam-category-products"><?php echo esc_html( 'Xem tour ngay' ); ?></a>
					<a class="annam-category-hero__cta annam-category-hero__cta--secondary" href="<?php echo esc_url( $consult_url ); ?>"><?php echo esc_html( 'Tư vấn nhanh' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $chips ) ) : ?>
		<nav class="annam-category-hero-chips" aria-label="<?php echo esc_attr( 'Điều hướng nhanh' ); ?>">
			<?php foreach ( $chips as $chip ) : ?>
				<?php
				$label = isset( $chip['label'] ) ? (string) $chip['label'] : '';
				$url   = isset( $chip['url'] ) ? (string) $chip['url'] : '#annam-category-products';
				if ( '' === $label ) {
					continue;
				}
				?>
				<a class="annam-category-hero-chip" href="<?php echo 0 === strpos( $url, '#' ) ? esc_attr( $url ) : esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>
</div>
