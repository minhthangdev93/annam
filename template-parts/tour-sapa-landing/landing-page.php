<?php
/**
 * Landing Tour Sapa 3N2Đ markup (luxury).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$config = function_exists( 'annam_tour_sapa_landing_get_config' ) ? annam_tour_sapa_landing_get_config() : array();
$cta    = function_exists( 'annam_tour_sapa_landing_get_cta' ) ? annam_tour_sapa_landing_get_cta() : array();
$secs   = isset( $config['sections'] ) ? $config['sections'] : array();
$notice = function_exists( 'annam_tour_sapa_landing_get_notice' ) ? annam_tour_sapa_landing_get_notice() : null;
$hero   = isset( $config['hero'] ) ? $config['hero'] : array();
$exp    = isset( $config['experience'] ) ? $config['experience'] : array();
$brand  = isset( $cta['brand'] ) ? $cta['brand'] : 'An Nam Discovery';
?>
<div class="annam-tour-sapa-landing">

	<?php if ( $notice ) : ?>
	<div class="annam-tour-sapa-container">
		<div class="annam-tour-sapa-notice annam-tour-sapa-notice--<?php echo esc_attr( $notice['type'] ); ?>" role="status"><?php echo esc_html( $notice['message'] ); ?></div>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $secs['experience'] ) && count( $exp ) >= 6 ) : ?>
	<section class="annam-tour-sapa-experience" id="trai-nghiem" aria-label="<?php esc_attr_e( 'Trải nghiệm Sapa', 'generatepress_child' ); ?>">
		<div class="annam-tour-sapa-experience__intro annam-tour-sapa-reveal">
			<p class="annam-tour-sapa-experience__headline"><?php esc_html_e( 'Tour Sapa 3 ngày 2 đêm', 'generatepress_child' ); ?></p>
			<p class="annam-tour-sapa-experience__tagline"><?php esc_html_e( 'Du lịch Sapa 3N2Đ · Cát Cát · Fansipan · Moana', 'generatepress_child' ); ?></p>
		</div>
		<div class="annam-tour-sapa-mosaic-wrap">
			<div class="annam-tour-sapa-mosaic" data-annam-tour-sapa-gallery>
				<?php
				$mosaic      = array_slice( $exp, 0, 6 );
				$mosaic_last = count( $mosaic ) - 1;
				foreach ( $mosaic as $i => $item ) :
					$slot = isset( $item['slot'] ) ? (string) $item['slot'] : '';
					$cap  = isset( $item['caption'] ) ? (string) $item['caption'] : '';
					$mod  = 0 === $i ? ' annam-tour-sapa-mosaic__cell--hero' : '';
					// Desktop: Gallery trên ô 5; mobile: Gallery trên ô 6.
					$gallery_desktop = ( 4 === $i );
					$gallery_mobile  = ( $i === $mosaic_last );
					if ( $gallery_desktop ) {
						$mod .= ' annam-tour-sapa-mosaic__cell--gallery-btn-desktop';
					}
					if ( $gallery_mobile ) {
						$mod .= ' annam-tour-sapa-mosaic__cell--gallery-btn-mobile';
					}
					?>
					<figure class="annam-tour-sapa-mosaic__cell<?php echo esc_attr( $mod ); ?>">
						<button type="button" class="annam-tour-sapa-mosaic__trigger" data-annam-gallery-open="<?php echo esc_attr( (string) $i ); ?>" aria-label="<?php echo esc_attr( $cap ? $cap : __( 'Xem ảnh', 'generatepress_child' ) ); ?>">
							<?php
							if ( $slot && function_exists( 'annam_tour_sapa_landing_print_image' ) ) {
								echo annam_tour_sapa_landing_print_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									$slot,
									array(
										'alt'     => $cap,
										'loading' => 0 === $i ? 'eager' : 'lazy',
										'class'   => 'annam-tour-sapa-mosaic__img',
									)
								);
							}
							?>
							<span class="annam-tour-sapa-mosaic__veil" aria-hidden="true"></span>
							<?php if ( $gallery_desktop || $gallery_mobile ) : ?>
								<span class="annam-tour-sapa-mosaic__gallery-label">
									<?php
									$shown = $gallery_desktop ? 4 : 5;
									$extra = max( 1, count( $exp ) - $shown );
									?>
									<span class="annam-tour-sapa-mosaic__gallery-count">+<?php echo esc_html( (string) $extra ); ?></span>
									<span><?php esc_html_e( 'Gallery', 'generatepress_child' ); ?></span>
								</span>
							<?php endif; ?>
							<?php if ( $cap && ! $gallery_desktop && ! $gallery_mobile ) : ?>
								<span class="annam-tour-sapa-mosaic__cap"><?php echo esc_html( $cap ); ?></span>
							<?php elseif ( $cap && $gallery_desktop ) : ?>
								<span class="annam-tour-sapa-mosaic__cap"><?php echo esc_html( $cap ); ?></span>
							<?php endif; ?>
						</button>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['hero'] ) ) : ?>
	<section class="annam-tour-sapa-hero" id="dat-tour">
		<div class="annam-tour-sapa-container">
			<div class="annam-tour-sapa-hero__grid">
				<div class="annam-tour-sapa-hero__copy annam-tour-sapa-reveal">
					<p class="annam-tour-sapa-kicker"><?php echo esc_html( $brand ); ?></p>
					<p class="annam-tour-sapa-hero__eyebrow"><?php esc_html_e( 'Du lịch Sapa · Tour 3N2Đ', 'generatepress_child' ); ?></p>
					<h1 class="annam-tour-sapa-hero__title"><?php echo esc_html( ! empty( $hero['title'] ) ? $hero['title'] : __( 'Tour Sapa 3 Ngày 2 Đêm', 'generatepress_child' ) ); ?></h1>
					<?php if ( ! empty( $hero['subtitle'] ) ) : ?>
						<p class="annam-tour-sapa-hero__subtitle"><?php echo esc_html( $hero['subtitle'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $hero['price_from'] ) ) : ?>
						<p class="annam-tour-sapa-hero__price">
							<span class="annam-tour-sapa-hero__price-label"><?php esc_html_e( 'Giá từ', 'generatepress_child' ); ?></span>
							<strong><?php echo esc_html( $hero['price_from'] ); ?></strong>
							<span class="annam-tour-sapa-hero__price-unit"><?php esc_html_e( '/ khách', 'generatepress_child' ); ?></span>
						</p>
					<?php endif; ?>
					<?php if ( ! empty( $hero['badges'] ) ) : ?>
						<ul class="annam-tour-sapa-hero__badges">
							<?php foreach ( $hero['badges'] as $badge ) : ?>
								<li class="annam-tour-sapa-hero__badge"><?php echo esc_html( $badge ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<div class="annam-tour-sapa-hero__ctas annam-tour-sapa-hero__ctas--desktop">
						<a class="annam-tour-sapa-btn annam-tour-sapa-btn--primary" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php esc_html_e( 'Gọi Đặt Tour', 'generatepress_child' ); ?></a>
						<a class="annam-tour-sapa-btn annam-tour-sapa-btn--ghost" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Chat Zalo', 'generatepress_child' ); ?></a>
						<a class="annam-tour-sapa-btn annam-tour-sapa-btn--text" href="#gia-tour"><?php esc_html_e( 'Xem bảng giá', 'generatepress_child' ); ?></a>
					</div>
				</div>
				<div class="annam-tour-sapa-hero__form annam-tour-sapa-reveal">
					<div class="annam-tour-sapa-hero__ctas annam-tour-sapa-hero__ctas--mobile">
						<a class="annam-tour-sapa-btn annam-tour-sapa-btn--primary" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php esc_html_e( 'Gọi', 'generatepress_child' ); ?></a>
						<a class="annam-tour-sapa-btn annam-tour-sapa-btn--ghost" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Zalo', 'generatepress_child' ); ?></a>
					</div>
					<?php get_template_part( 'template-parts/tour-sapa-landing/part', 'form', array( 'config' => $config ) ); ?>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['highlights'] ) && ! empty( $config['highlights'] ) ) : ?>
	<section class="annam-tour-sapa-section" id="diem-noi-bat">
		<div class="annam-tour-sapa-container">
			<header class="annam-tour-sapa-section__head annam-tour-sapa-reveal">
				<p class="annam-tour-sapa-kicker"><?php esc_html_e( 'Hành trình', 'generatepress_child' ); ?></p>
				<h2 class="annam-tour-sapa-section__title"><?php esc_html_e( 'Điểm Nổi Bật Trong Tour', 'generatepress_child' ); ?></h2>
			</header>
			<div class="annam-tour-sapa-highlights">
				<?php foreach ( $config['highlights'] as $hi => $h ) : ?>
					<article class="annam-tour-sapa-highlight annam-tour-sapa-reveal">
						<span class="annam-tour-sapa-highlight__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $hi + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><?php echo esc_html( $h['title'] ); ?></h3>
						<p><?php echo esc_html( $h['text'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['pricing'] ) && ! empty( $config['pricing']['rows'] ) ) : ?>
	<section class="annam-tour-sapa-section annam-tour-sapa-section--mist" id="gia-tour">
		<div class="annam-tour-sapa-container">
			<header class="annam-tour-sapa-section__head annam-tour-sapa-reveal">
				<p class="annam-tour-sapa-kicker"><?php esc_html_e( 'Giá tour Sapa', 'generatepress_child' ); ?></p>
				<h2 class="annam-tour-sapa-section__title"><?php esc_html_e( 'Giá Tour Sapa 3 Ngày 2 Đêm', 'generatepress_child' ); ?></h2>
				<?php if ( ! empty( $config['pricing']['lead'] ) ) : ?>
					<p class="annam-tour-sapa-section__lead"><?php echo esc_html( $config['pricing']['lead'] ); ?></p>
				<?php endif; ?>
			</header>
			<div class="annam-tour-sapa-price-grid">
				<?php foreach ( $config['pricing']['rows'] as $row ) : ?>
					<article class="annam-tour-sapa-price-card annam-tour-sapa-reveal<?php echo ! empty( $row['badge'] ) ? ' annam-tour-sapa-price-card--highlight' : ''; ?>">
						<?php if ( ! empty( $row['badge'] ) ) : ?>
							<span class="annam-tour-sapa-price-card__badge"><?php echo esc_html( $row['badge'] ); ?></span>
						<?php endif; ?>
						<h3 class="annam-tour-sapa-price-card__name"><?php echo esc_html( $row['label'] ); ?></h3>
						<p class="annam-tour-sapa-price-card__price"><?php echo esc_html( $row['price'] ); ?><span>/ khách</span></p>
						<p class="annam-tour-sapa-price-card__desc"><?php echo esc_html( $row['desc'] ); ?></p>
						<button type="button" class="annam-tour-sapa-btn annam-tour-sapa-btn--ghost annam-tour-sapa-btn--block" data-annam-pick-hotel="<?php echo esc_attr( $row['type'] ); ?>"><?php esc_html_e( 'Chọn hạng này', 'generatepress_child' ); ?></button>
					</article>
				<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $config['pricing']['note'] ) ) : ?>
				<p class="annam-tour-sapa-section__note"><?php echo esc_html( $config['pricing']['note'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $config['children'] ) ) : ?>
				<details class="annam-tour-sapa-children">
					<summary><?php esc_html_e( 'Chính sách trẻ em', 'generatepress_child' ); ?></summary>
					<ul>
						<?php foreach ( $config['children'] as $line ) : ?>
							<li><?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</details>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['itinerary'] ) && ! empty( $config['itinerary'] ) ) : ?>
	<section class="annam-tour-sapa-section" id="lich-trinh">
		<div class="annam-tour-sapa-container annam-tour-sapa-container--narrow">
			<header class="annam-tour-sapa-section__head annam-tour-sapa-reveal">
				<p class="annam-tour-sapa-kicker"><?php esc_html_e( 'Lịch trình 3N2Đ', 'generatepress_child' ); ?></p>
				<h2 class="annam-tour-sapa-section__title"><?php esc_html_e( 'Lịch Trình Tour Sapa 3 Ngày 2 Đêm', 'generatepress_child' ); ?></h2>
			</header>
			<div class="annam-tour-sapa-itin">
				<?php foreach ( $config['itinerary'] as $i => $day ) : ?>
					<details class="annam-tour-sapa-itin__day annam-tour-sapa-reveal" <?php echo 0 === $i ? 'open' : ''; ?>>
						<summary>
							<span class="annam-tour-sapa-itin__day-label"><?php echo esc_html( $day['day'] ); ?></span>
							<span class="annam-tour-sapa-itin__day-title"><?php echo esc_html( $day['title'] ); ?></span>
							<?php if ( ! empty( $day['meals'] ) ) : ?>
								<span class="annam-tour-sapa-itin__meals"><?php echo esc_html( $day['meals'] ); ?></span>
							<?php endif; ?>
						</summary>
						<?php
						$day_slot = isset( $day['image'] ) ? (string) $day['image'] : '';
						$day_img  = $day_slot && function_exists( 'annam_tour_sapa_landing_print_image' )
							? annam_tour_sapa_landing_print_image(
								$day_slot,
								array(
									'alt'     => isset( $day['title'] ) ? (string) $day['title'] : '',
									'loading' => 'lazy',
									'class'   => 'annam-tour-sapa-itin__img',
								)
							)
							: '';
						if ( $day_img ) :
							?>
							<figure class="annam-tour-sapa-itin__media">
								<?php echo $day_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php if ( ! empty( $day['day'] ) && ! empty( $day['title'] ) ) : ?>
									<figcaption class="annam-tour-sapa-itin__media-cap"><?php echo esc_html( $day['day'] . ' · ' . $day['title'] ); ?></figcaption>
								<?php endif; ?>
							</figure>
						<?php endif; ?>
						<ol class="annam-tour-sapa-itin__steps">
							<?php foreach ( $day['steps'] as $step ) : ?>
								<li>
									<strong><?php echo esc_html( $step['time'] ); ?></strong>
									<span><?php echo esc_html( $step['text'] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ol>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['includes'] ) ) : ?>
	<section class="annam-tour-sapa-section annam-tour-sapa-section--ink" id="bao-gom">
		<div class="annam-tour-sapa-container">
			<header class="annam-tour-sapa-section__head annam-tour-sapa-reveal">
				<p class="annam-tour-sapa-kicker annam-tour-sapa-kicker--light"><?php esc_html_e( 'Minh bạch', 'generatepress_child' ); ?></p>
				<h2 class="annam-tour-sapa-section__title"><?php esc_html_e( 'Bao Gồm & Không Bao Gồm', 'generatepress_child' ); ?></h2>
			</header>
			<div class="annam-tour-sapa-include-grid">
				<div class="annam-tour-sapa-include annam-tour-sapa-include--yes annam-tour-sapa-reveal">
					<h3><?php esc_html_e( 'Bao gồm', 'generatepress_child' ); ?></h3>
					<ul>
						<?php foreach ( $config['includes'] as $line ) : ?>
							<li><?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="annam-tour-sapa-include annam-tour-sapa-include--no annam-tour-sapa-reveal">
					<h3><?php esc_html_e( 'Không bao gồm', 'generatepress_child' ); ?></h3>
					<ul>
						<?php foreach ( $config['excludes'] as $line ) : ?>
							<li><?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['faq'] ) && ! empty( $config['faq'] ) ) : ?>
	<section class="annam-tour-sapa-section" id="faq">
		<div class="annam-tour-sapa-container">
			<header class="annam-tour-sapa-section__head annam-tour-sapa-reveal">
				<p class="annam-tour-sapa-kicker"><?php esc_html_e( 'Hỏi đáp', 'generatepress_child' ); ?></p>
				<h2 class="annam-tour-sapa-section__title"><?php esc_html_e( 'Câu Hỏi Thường Gặp', 'generatepress_child' ); ?></h2>
			</header>
			<div class="annam-tour-sapa-faq">
				<?php foreach ( $config['faq'] as $item ) : ?>
					<details class="annam-tour-sapa-faq__item annam-tour-sapa-reveal">
						<summary><?php echo esc_html( $item['question'] ); ?></summary>
						<p><?php echo esc_html( $item['answer'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['reviews'] ) ) : ?>
	<div id="danh-gia" class="annam-tour-sapa-reviews-slot">
		<?php
		// Trustindex Google — cùng khối trang chủ (social proof trước CTA cuối).
		get_template_part( 'template-parts/home/home', 'reviews' );
		?>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $secs['related_tours'] ) ) : ?>
		<?php get_template_part( 'template-parts/tour-sapa-landing/part', 'woo-tours' ); ?>
	<?php endif; ?>

	<?php if ( ! empty( $secs['final_cta'] ) ) : ?>
	<section class="annam-tour-sapa-final" id="cta-cuoi">
		<div class="annam-tour-sapa-container annam-tour-sapa-final__inner annam-tour-sapa-reveal">
			<p class="annam-tour-sapa-kicker annam-tour-sapa-kicker--light"><?php echo esc_html( $brand ); ?></p>
			<h2><?php echo esc_html( $config['final_cta']['title'] ); ?></h2>
			<p><?php echo esc_html( $config['final_cta']['subtitle'] ); ?></p>
			<div class="annam-tour-sapa-final__ctas">
				<button type="button" class="annam-tour-sapa-btn annam-tour-sapa-btn--gold" data-annam-scroll-form><?php esc_html_e( 'Giữ Chỗ Ngay', 'generatepress_child' ); ?></button>
				<a class="annam-tour-sapa-btn annam-tour-sapa-btn--ghost-light" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Nhắn Zalo', 'generatepress_child' ); ?></a>
				<a class="annam-tour-sapa-btn annam-tour-sapa-btn--ghost-light" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php echo esc_html( $cta['hotline_display'] ); ?></a>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<nav class="annam-tour-sapa-sticky" aria-label="<?php esc_attr_e( 'Liên hệ nhanh', 'generatepress_child' ); ?>">
		<a class="annam-tour-sapa-sticky__btn" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>">
			<span><?php esc_html_e( 'Gọi', 'generatepress_child' ); ?></span>
		</a>
		<a class="annam-tour-sapa-sticky__btn annam-tour-sapa-sticky__btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener">
			<span><?php esc_html_e( 'Zalo', 'generatepress_child' ); ?></span>
		</a>
		<button type="button" class="annam-tour-sapa-sticky__btn annam-tour-sapa-sticky__btn--book" data-annam-scroll-form>
			<span><?php esc_html_e( 'Giữ chỗ', 'generatepress_child' ); ?></span>
		</button>
	</nav>

	<div class="annam-tour-sapa-lightbox" id="annam-tour-sapa-lightbox" hidden role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Xem ảnh', 'generatepress_child' ); ?>">
		<button type="button" class="annam-tour-sapa-lightbox__close" data-annam-lightbox-close aria-label="<?php esc_attr_e( 'Đóng', 'generatepress_child' ); ?>">×</button>
		<button type="button" class="annam-tour-sapa-lightbox__nav annam-tour-sapa-lightbox__nav--prev" data-annam-lightbox-prev aria-label="<?php esc_attr_e( 'Trước', 'generatepress_child' ); ?>">‹</button>
		<figure class="annam-tour-sapa-lightbox__figure">
			<img class="annam-tour-sapa-lightbox__img" src="" alt="" />
			<figcaption class="annam-tour-sapa-lightbox__cap"></figcaption>
		</figure>
		<button type="button" class="annam-tour-sapa-lightbox__nav annam-tour-sapa-lightbox__nav--next" data-annam-lightbox-next aria-label="<?php esc_attr_e( 'Sau', 'generatepress_child' ); ?>">›</button>
	</div>
</div>
