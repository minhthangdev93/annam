<?php
/**
 * Trang chủ: hero slider + lưới danh mục tour (product_cat).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! taxonomy_exists( 'product_cat' ) ) {
	return;
}

$terms = function_exists( 'annam_resolve_category_showcase_terms' ) ? annam_resolve_category_showcase_terms() : array();

$admin_slides = function_exists( 'annam_get_home_sliders' ) ? annam_get_home_sliders() : array();
$legacy_slides = function_exists( 'annam_home_hero_get_slide_rows' ) ? annam_home_hero_get_slide_rows() : array();

/** @var array<int, array<string, mixed>> */
$hero_blocks = array();

if ( ! empty( $admin_slides ) ) {
	foreach ( $admin_slides as $s ) {
		$hero_blocks[] = array(
			'source'      => 'admin',
			'desktop_src' => $s['desktop_src'],
			'mobile_src'  => $s['mobile_src'],
			'alt'         => $s['alt'],
			'title'       => $s['title'],
			'description' => $s['description'],
			'button_text' => $s['button_text'],
			'button_url'  => $s['button_url'],
		);
	}

	if ( count( $hero_blocks ) < 2 && ! empty( $legacy_slides ) ) {
		$existing_desktop = wp_list_pluck( $hero_blocks, 'desktop_src' );

		foreach ( $legacy_slides as $i => $slide ) {
			if ( empty( $slide['src'] ) || in_array( $slide['src'], $existing_desktop, true ) ) {
				continue;
			}

			$hero_blocks[] = array(
				'source'      => 'legacy',
				'desktop_src' => $slide['src'],
				'mobile_src'  => $slide['src'],
				'alt'         => $slide['alt'],
				'title'       => '',
				'description' => '',
				'button_text' => '',
				'button_url'  => '',
			);

			$existing_desktop[] = $slide['src'];

			if ( count( $hero_blocks ) >= 2 ) {
				break;
			}
		}
	}
} else {
	$slides_sanitized = $legacy_slides;
	if ( empty( $slides_sanitized ) ) {
		return;
	}

	$two_distinct_slides = count( $slides_sanitized ) === 2
		&& isset( $slides_sanitized[0]['src'], $slides_sanitized[1]['src'] )
		&& $slides_sanitized[0]['src'] !== $slides_sanitized[1]['src'];

	foreach ( $slides_sanitized as $i => $slide ) {
		$d_src = $slide['src'];
		$m_src = ( 0 === (int) $i && $two_distinct_slides )
			? $slides_sanitized[1]['src']
			: $slide['src'];

		$hero_blocks[] = array(
			'source'        => 'legacy',
			'desktop_src'   => $d_src,
			'mobile_src'    => $m_src,
			'alt'           => $slide['alt'],
			'title'         => '',
			'description'   => '',
			'button_text'   => '',
			'button_url'    => '',
		);
	}
}

if ( empty( $hero_blocks ) ) {
	return;
}

$slider_uid = function_exists( 'wp_unique_id' ) ? wp_unique_id( 'annam-home-hero-' ) : ( 'annam-home-hero-' . uniqid() );
?>
<section class="annam-home-top" aria-label="<?php echo esc_attr__( 'Giới thiệu và danh mục tour', 'generatepress_child' ); ?>">
	<div class="annam-home-hero">
		<div
			class="annam-home-hero__slider"
			id="<?php echo esc_attr( $slider_uid ); ?>"
			data-annam-home-hero-slider
			role="region"
			aria-roledescription="carousel"
			aria-label="<?php echo esc_attr__( 'Banner trang chủ', 'generatepress_child' ); ?>"
		>
			<div class="annam-home-hero__track" aria-live="polite">
				<?php foreach ( $hero_blocks as $i => $block ) : ?>
					<?php
					$d_src    = $block['desktop_src'];
					$m_src    = $block['mobile_src'];
					$alt      = $block['alt'];
					$is_first = ( 0 === (int) $i );
					$diff_m   = $d_src !== $m_src;
					$is_admin = ( 'admin' === $block['source'] );
					?>
					<div
						class="annam-home-hero__slide<?php echo $is_first ? ' is-active' : ''; ?><?php echo $is_admin ? ' annam-home-hero__slide--has-caption' : ''; ?>"
						data-annam-hero-slide
						role="group"
						aria-roledescription="<?php echo esc_attr__( 'Slide', 'generatepress_child' ); ?>"
						aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number */ __( 'Slide %d', 'generatepress_child' ), (int) $i + 1 ) ); ?>"
						aria-hidden="<?php echo $is_first ? 'false' : 'true'; ?>"
					>
						<?php if ( $diff_m ) : ?>
							<?php
							$d_web = function_exists( 'annam_home_hero_matching_webp_url' ) ? annam_home_hero_matching_webp_url( $d_src ) : '';
							$m_web = function_exists( 'annam_home_hero_matching_webp_url' ) ? annam_home_hero_matching_webp_url( $m_src ) : '';
							?>
							<picture>
								<?php if ( '' !== $m_web ) : ?>
									<source type="image/webp" srcset="<?php echo esc_url( $m_web ); ?>" media="(max-width: 768px)" />
								<?php endif; ?>
								<source media="(max-width: 768px)" srcset="<?php echo esc_url( $m_src ); ?>" />
								<?php if ( '' !== $d_web ) : ?>
									<source type="image/webp" srcset="<?php echo esc_url( $d_web ); ?>" media="(min-width: 769px)" />
								<?php endif; ?>
								<img
									class="annam-home-hero__slide-img"
									src="<?php echo esc_url( $d_src ); ?>"
									alt="<?php echo esc_attr( $alt ); ?>"
									width="1600"
									height="900"
									sizes="100vw"
									decoding="<?php echo $is_first ? 'sync' : 'async'; ?>"
									<?php echo $is_first ? ' fetchpriority="high"' : ' loading="lazy"'; ?>
								/>
							</picture>
						<?php elseif ( $first_webp = function_exists( 'annam_home_hero_matching_webp_url' ) ? annam_home_hero_matching_webp_url( $d_src ) : '' ) : ?>
							<picture>
								<source type="image/webp" srcset="<?php echo esc_url( $first_webp ); ?>" />
								<img
									class="annam-home-hero__slide-img"
									src="<?php echo esc_url( $d_src ); ?>"
									alt="<?php echo esc_attr( $alt ); ?>"
									width="1600"
									height="900"
									sizes="100vw"
									decoding="<?php echo $is_first ? 'sync' : 'async'; ?>"
									<?php echo $is_first ? ' fetchpriority="high"' : ' loading="lazy"'; ?>
								/>
							</picture>
						<?php else : ?>
							<img
								class="annam-home-hero__slide-img"
								src="<?php echo esc_url( $d_src ); ?>"
								alt="<?php echo esc_attr( $alt ); ?>"
								width="1600"
								height="900"
								sizes="100vw"
								decoding="<?php echo $is_first ? 'sync' : 'async'; ?>"
								<?php echo $is_first ? ' fetchpriority="high"' : ' loading="lazy"'; ?>
							/>
						<?php endif; ?>

						<?php if ( $is_admin && ( '' !== (string) $block['title'] || '' !== (string) $block['description'] || ( '' !== (string) $block['button_text'] && '' !== (string) $block['button_url'] ) ) ) : ?>
							<div class="annam-home-hero__caption">
								<div class="annam-home-hero__caption-inner annam-container grid-container grid-parent">
									<?php if ( '' !== (string) $block['title'] ) : ?>
										<h2 class="annam-home-hero__caption-title"><?php echo esc_html( (string) $block['title'] ); ?></h2>
									<?php endif; ?>
									<?php if ( '' !== (string) $block['description'] ) : ?>
										<p class="annam-home-hero__caption-desc"><?php echo nl2br( esc_html( (string) $block['description'] ) ); ?></p>
									<?php endif; ?>
									<?php if ( '' !== (string) $block['button_text'] && '' !== (string) $block['button_url'] ) : ?>
										<a class="annam-home-hero__caption-cta" href="<?php echo esc_url( (string) $block['button_url'] ); ?>"><?php echo esc_html( (string) $block['button_text'] ); ?></a>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<button type="button" class="annam-home-hero__nav annam-home-hero__nav--prev" aria-controls="<?php echo esc_attr( $slider_uid ); ?>" aria-label="<?php esc_attr_e( 'Slide trước', 'generatepress_child' ); ?>">
				<span aria-hidden="true">‹</span>
			</button>
			<button type="button" class="annam-home-hero__nav annam-home-hero__nav--next" aria-controls="<?php echo esc_attr( $slider_uid ); ?>" aria-label="<?php esc_attr_e( 'Slide sau', 'generatepress_child' ); ?>">
				<span aria-hidden="true">›</span>
			</button>

			<div class="annam-home-hero__dots" role="tablist" aria-label="<?php echo esc_attr__( 'Chọn slide', 'generatepress_child' ); ?>">
				<?php foreach ( $hero_blocks as $i => $block ) : ?>
					<?php $is_first = ( 0 === (int) $i ); ?>
					<button
						type="button"
						class="annam-home-hero__dot<?php echo $is_first ? ' is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>"
						aria-label="<?php echo esc_attr( sprintf( __( 'Slide %d', 'generatepress_child' ), (int) $i + 1 ) ); ?>"
						tabindex="<?php echo $is_first ? '0' : '-1'; ?>"
					></button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="annam-home-cards annam-home-categories">
		<div class="annam-home-categories__inner annam-home-cards__inner">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Danh mục tour nổi bật', 'generatepress_child' ); ?></h2>
			<div class="annam-category-showcase__grid annam-home-cards__grid" role="list">
				<?php
				if ( ! empty( $terms ) && function_exists( 'annam_render_category_nav_cards' ) ) {
					annam_render_category_nav_cards( $terms, 'home' );
				}
				?>
			</div>
		</div>
	</div>
</section>
