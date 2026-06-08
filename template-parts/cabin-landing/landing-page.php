<?php
/**
 * Landing Cabin VIP 22 phòng — tối ưu chuyển đổi Google Ads.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$config = annam_cabin_landing_get_config();
$cta    = annam_cabin_landing_get_cta();
$hero   = isset( $config['hero'] ) ? $config['hero'] : array();
$secs   = isset( $config['sections'] ) ? $config['sections'] : array();
$gallery = isset( $config['gallery'] ) ? $config['gallery'] : array();

$cabin_page_content_html = '';
$cabin_page_content_long = false;
$cabin_show_page_content = ! empty( $secs['seo'] );
if ( $cabin_show_page_content && function_exists( 'annam_cabin_landing_page_has_editor_content' ) && annam_cabin_landing_page_has_editor_content() ) {
	$cabin_page_content_html = annam_cabin_landing_get_page_content_html();
	$toggle_min              = (int) apply_filters( 'annam_cabin_page_content_toggle_min_chars', 400 );
	$toggle_min              = max( 200, $toggle_min );
	$cabin_page_content_long = strlen( wp_strip_all_tags( $cabin_page_content_html ) ) > $toggle_min;
} else {
	$cabin_show_page_content = false;
}
?>
<article class="annam-cabin-landing">
	<nav class="annam-cabin-anchor" aria-label="<?php esc_attr_e( 'Mục nhanh', 'generatepress_child' ); ?>">
		<div class="annam-cabin-container annam-cabin-anchor__inner">
			<?php foreach ( $config['anchors'] as $anchor ) : ?>
				<a class="annam-cabin-anchor__link" href="#<?php echo esc_attr( $anchor['id'] ); ?>"><?php echo esc_html( $anchor['label'] ); ?></a>
			<?php endforeach; ?>
		</div>
	</nav>

	<?php if ( ! empty( $secs['hero'] ) ) : ?>
	<section class="annam-cabin-hero">
		<div class="annam-cabin-container annam-cabin-hero__grid">
			<div class="annam-cabin-hero__content">
				<p class="annam-cabin-hero__eyebrow"><?php esc_html_e( 'Xe Cabin VIP 22 phòng', 'generatepress_child' ); ?></p>
				<h1 class="annam-cabin-hero__title"><?php echo esc_html( $hero['title'] ); ?></h1>
				<p class="annam-cabin-hero__subtitle"><?php echo esc_html( $hero['subtitle'] ); ?></p>
				<p class="annam-cabin-hero__price">
					<span class="annam-cabin-hero__price-label"><?php esc_html_e( 'Giá từ', 'generatepress_child' ); ?></span>
					<strong><?php echo esc_html( $hero['price_from'] ); ?></strong>
					<span class="annam-cabin-hero__price-unit">/vé</span>
				</p>
				<?php if ( ! empty( $hero['badges'] ) ) : ?>
					<ul class="annam-cabin-hero__badges">
						<?php foreach ( $hero['badges'] as $badge ) : ?>
							<li class="annam-cabin-hero__badge"><?php echo esc_html( $badge ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<div class="annam-cabin-hero__ctas annam-cabin-hero__ctas--desktop">
					<a class="annam-cabin-btn annam-cabin-btn--primary" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>" data-track="click_hotline"><?php esc_html_e( 'Gọi Đặt Vé Ngay', 'generatepress_child' ); ?></a>
					<a class="annam-cabin-btn annam-cabin-btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener" data-track="click_zalo"><?php esc_html_e( 'Chat Zalo Giữ Chỗ', 'generatepress_child' ); ?></a>
					<a class="annam-cabin-btn annam-cabin-btn--outline" href="#gia-ve"><?php esc_html_e( 'Xem Bảng Giá', 'generatepress_child' ); ?></a>
				</div>
			</div>
			<div class="annam-cabin-hero__form-col">
				<div class="annam-cabin-hero__ctas annam-cabin-hero__ctas--mobile">
					<a class="annam-cabin-btn annam-cabin-btn--primary" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>" data-track="click_hotline"><?php esc_html_e( 'Gọi Đặt Vé Ngay', 'generatepress_child' ); ?></a>
					<a class="annam-cabin-btn annam-cabin-btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener" data-track="click_zalo"><?php esc_html_e( 'Chat Zalo Giữ Chỗ', 'generatepress_child' ); ?></a>
				</div>
				<?php get_template_part( 'template-parts/cabin-landing/part', 'form' ); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['pricing'] ) ) : ?>
	<section class="annam-cabin-section" id="gia-ve">
		<div class="annam-cabin-container">
			<h2 class="annam-cabin-section__title"><?php esc_html_e( 'Bảng Giá Vé Cabin VIP', 'generatepress_child' ); ?></h2>
			<div class="annam-cabin-tabs" data-annam-tabs="pricing">
				<div class="annam-cabin-tabs__nav" role="tablist">
					<?php foreach ( $config['routes'] as $i => $route ) : ?>
						<button type="button" class="annam-cabin-tabs__btn<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" data-tab="<?php echo esc_attr( $route['id'] ); ?>"><?php echo esc_html( $route['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
				<?php foreach ( $config['routes'] as $i => $route ) : ?>
					<?php $rows = isset( $config['pricing'][ $route['id'] ] ) ? $config['pricing'][ $route['id'] ] : array(); ?>
					<div class="annam-cabin-tabs__panel<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tabpanel" data-panel="<?php echo esc_attr( $route['id'] ); ?>">
						<div class="annam-cabin-price-grid">
							<?php foreach ( $rows as $row ) : ?>
								<article class="annam-cabin-price-card<?php echo ! empty( $row['badge'] ) ? ' annam-cabin-price-card--highlight' : ''; ?>">
									<?php if ( ! empty( $row['badge'] ) ) : ?>
										<span class="annam-cabin-price-card__badge"><?php echo esc_html( $row['badge'] ); ?></span>
									<?php endif; ?>
									<h3 class="annam-cabin-price-card__name"><?php echo esc_html( $row['label'] ); ?></h3>
									<p class="annam-cabin-price-card__price"><?php echo esc_html( $row['price'] ); ?></p>
									<?php if ( ! empty( $row['desc'] ) ) : ?>
										<p class="annam-cabin-price-card__desc"><?php echo esc_html( $row['desc'] ); ?></p>
									<?php endif; ?>
									<button type="button" class="annam-cabin-btn annam-cabin-btn--primary annam-cabin-btn--block" data-annam-pick-ticket data-route="<?php echo esc_attr( $route['id'] ); ?>" data-cabin="<?php echo esc_attr( $row['type'] ); ?>" data-track="select_ticket_type"><?php esc_html_e( 'Chọn Vé Này', 'generatepress_child' ); ?></button>
								</article>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $config['pricing']['price_note'] ) ) : ?>
				<p class="annam-cabin-section__note"><?php echo esc_html( $config['pricing']['price_note'] ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['schedule'] ) ) : ?>
	<section class="annam-cabin-section annam-cabin-section--alt" id="lich-xe">
		<div class="annam-cabin-container">
			<h2 class="annam-cabin-section__title"><?php esc_html_e( 'Lịch Xe Cabin VIP', 'generatepress_child' ); ?></h2>
			<div class="annam-cabin-tabs" data-annam-tabs="schedule">
				<div class="annam-cabin-tabs__nav annam-cabin-tabs__nav--wrap" role="tablist">
					<?php foreach ( $config['schedules'] as $i => $sch ) : ?>
						<button type="button" class="annam-cabin-tabs__btn<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" data-tab="<?php echo esc_attr( $sch['id'] ); ?>"><?php echo esc_html( $sch['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
				<?php foreach ( $config['schedules'] as $i => $sch ) : ?>
					<div class="annam-cabin-tabs__panel<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tabpanel" data-panel="<?php echo esc_attr( $sch['id'] ); ?>" data-schedule-from="<?php echo esc_attr( $sch['from'] ); ?>" data-schedule-to="<?php echo esc_attr( $sch['to'] ); ?>">
						<p class="annam-cabin-schedule-hint" data-annam-schedule-hint hidden></p>
						<div class="annam-cabin-time-grid" role="group" aria-label="<?php echo esc_attr( $sch['label'] ); ?>" data-annam-schedule-grid>
							<?php foreach ( $sch['times'] as $time ) : ?>
								<button type="button" class="annam-cabin-time-btn" data-annam-pick-time="<?php echo esc_attr( $time ); ?>" data-from="<?php echo esc_attr( $sch['from'] ); ?>" data-to="<?php echo esc_attr( $sch['to'] ); ?>" data-track="select_departure_time">
									<span class="annam-cabin-time-btn__label"><?php echo esc_html( $time ); ?></span>
								</button>
							<?php endforeach; ?>
						</div>
						<p class="annam-cabin-section__cta-row">
							<button type="button" class="annam-cabin-btn annam-cabin-btn--primary" data-annam-scroll-form-after-time><?php esc_html_e( 'Chọn Giờ Này Và Giữ Chỗ', 'generatepress_child' ); ?></button>
						</p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['cabins'] ) ) : ?>
	<section class="annam-cabin-section" id="loai-cabin">
		<div class="annam-cabin-container">
			<h2 class="annam-cabin-section__title"><?php esc_html_e( 'Chọn Loại Cabin Phù Hợp', 'generatepress_child' ); ?></h2>
			<div class="annam-cabin-cabin-grid">
				<?php foreach ( $config['cabins'] as $cabin ) : ?>
					<article class="annam-cabin-cabin-card<?php echo ! empty( $cabin['featured'] ) ? ' annam-cabin-cabin-card--featured' : ''; ?>">
						<?php if ( ! empty( $cabin['tag'] ) ) : ?>
							<span class="annam-cabin-cabin-card__tag"><?php echo esc_html( $cabin['tag'] ); ?></span>
						<?php endif; ?>
						<div class="annam-cabin-cabin-card__media">
							<?php
							$cabin_img_key = ! empty( $cabin['image'] ) ? (string) $cabin['image'] : '';
							if ( $cabin_img_key && function_exists( 'annam_cabin_landing_print_image' ) ) {
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image.
								echo annam_cabin_landing_print_image(
									$cabin_img_key,
									array(
										'alt'     => $cabin['name'],
										'width'   => '400',
										'height'  => '280',
										'loading' => 'lazy',
									)
								);
							} elseif ( ! empty( $cabin['image_url'] ) ) {
								?>
								<img src="<?php echo esc_url( $cabin['image_url'] ); ?>" alt="<?php echo esc_attr( $cabin['name'] ); ?>" width="400" height="280" loading="lazy" decoding="async" />
							<?php } ?>
						</div>
						<div class="annam-cabin-cabin-card__body">
							<h3 class="annam-cabin-cabin-card__title"><?php echo esc_html( $cabin['name'] ); ?></h3>
							<p class="annam-cabin-cabin-card__price"><?php printf( esc_html__( 'Từ %s', 'generatepress_child' ), esc_html( $cabin['price_from'] ) ); ?></p>
							<p class="annam-cabin-cabin-card__desc"><?php echo esc_html( $cabin['description'] ); ?></p>
							<button type="button" class="annam-cabin-btn annam-cabin-btn--outline annam-cabin-btn--block" data-annam-pick-cabin="<?php echo esc_attr( $cabin['type'] ); ?>" data-track="select_ticket_type"><?php printf( esc_html__( 'Chọn %s', 'generatepress_child' ), esc_html( $cabin['name'] ) ); ?></button>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['pickup'] ) && ! empty( $config['pickup_tabs'] ) ) : ?>
	<section class="annam-cabin-section annam-cabin-section--pickup" id="diem-don">
		<div class="annam-cabin-pickup-wrap">
			<h2 class="annam-cabin-section__title annam-cabin-pickup-wrap__title"><?php esc_html_e( 'Điểm Đón & Điểm Trả', 'generatepress_child' ); ?></h2>
			<div class="annam-cabin-tabs annam-cabin-tabs--pickup" data-annam-tabs="pickup">
				<div class="annam-cabin-tabs__nav annam-cabin-tabs__nav--pickup" role="tablist">
					<?php foreach ( $config['pickup_tabs'] as $i => $tab ) : ?>
						<button type="button" class="annam-cabin-tabs__btn annam-cabin-tabs__btn--pickup<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" data-tab="<?php echo esc_attr( $tab['id'] ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"><?php echo esc_html( $tab['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
				<?php foreach ( $config['pickup_tabs'] as $i => $tab ) : ?>
					<div class="annam-cabin-tabs__panel annam-cabin-pickup-panel<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tabpanel" data-panel="<?php echo esc_attr( $tab['id'] ); ?>">
						<?php if ( ! empty( $tab['heading'] ) ) : ?>
							<p class="annam-cabin-pickup-panel__heading"><?php echo esc_html( $tab['heading'] ); ?></p>
						<?php endif; ?>
						<div class="annam-cabin-pickup-cards">
							<article class="annam-cabin-pickup-card">
								<header class="annam-cabin-pickup-card__head">
									<span class="annam-cabin-pickup-card__accent" aria-hidden="true"></span>
									<h3 class="annam-cabin-pickup-card__title">
										<?php echo esc_html( ! empty( $tab['pickup_title'] ) ? $tab['pickup_title'] : __( 'Điểm đón', 'generatepress_child' ) ); ?>
									</h3>
								</header>
								<ul class="annam-cabin-pickup-card__list">
									<?php foreach ( $tab['pickup'] as $point ) : ?>
										<li class="annam-cabin-pickup-card__item"><?php echo esc_html( $point ); ?></li>
									<?php endforeach; ?>
								</ul>
							</article>
							<article class="annam-cabin-pickup-card">
								<header class="annam-cabin-pickup-card__head">
									<span class="annam-cabin-pickup-card__accent" aria-hidden="true"></span>
									<h3 class="annam-cabin-pickup-card__title">
										<?php echo esc_html( ! empty( $tab['dropoff_title'] ) ? $tab['dropoff_title'] : __( 'Điểm trả', 'generatepress_child' ) ); ?>
									</h3>
								</header>
								<ul class="annam-cabin-pickup-card__list">
									<?php foreach ( $tab['dropoff'] as $point ) : ?>
										<li class="annam-cabin-pickup-card__item"><?php echo esc_html( $point ); ?></li>
									<?php endforeach; ?>
								</ul>
							</article>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="annam-cabin-pickup-footer">
				<?php if ( ! empty( $config['pickup_note'] ) ) : ?>
					<p class="annam-cabin-pickup-footer__note"><?php echo esc_html( $config['pickup_note'] ); ?></p>
				<?php endif; ?>
				<div class="annam-cabin-pickup-footer__cta">
					<button type="button" class="annam-cabin-btn annam-cabin-btn--outline annam-cabin-btn--pickup-cta" data-annam-scroll-form><?php esc_html_e( 'Tư Vấn Điểm Đón Gần Tôi', 'generatepress_child' ); ?></button>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['gallery'] ) && count( $gallery ) >= 5 ) : ?>
	<section class="annam-cabin-section annam-cabin-section--gallery" id="anh-xe">
		<div class="annam-cabin-gallery-wrap">
			<h2 class="annam-cabin-section__title annam-cabin-gallery-wrap__title"><?php esc_html_e( 'Hình Ảnh Xe Cabin VIP', 'generatepress_child' ); ?></h2>
			<div class="annam-cabin-gallery" data-annam-gallery>
				<?php
				$hero_img = $gallery[0];
				$thumbs   = array_slice( $gallery, 1, 4 );
				?>
				<div class="annam-cabin-gallery__layout">
					<figure class="annam-cabin-gallery__main">
						<button type="button" class="annam-cabin-gallery__trigger" data-annam-gallery-index="0">
							<?php
							$gallery_main_key = ! empty( $hero_img['image'] ) ? (string) $hero_img['image'] : 'gallery-hero';
							if ( function_exists( 'annam_cabin_landing_print_image' ) ) {
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo annam_cabin_landing_print_image(
									$gallery_main_key,
									array(
										'alt'     => $hero_img['caption'],
										'width'   => '1200',
										'height'  => '750',
										'loading' => 'lazy',
									)
								);
							} elseif ( ! empty( $hero_img['image_url'] ) ) {
								?>
								<img src="<?php echo esc_url( $hero_img['image_url'] ); ?>" alt="<?php echo esc_attr( $hero_img['caption'] ); ?>" width="1200" height="750" loading="lazy" decoding="async" />
							<?php } ?>
							<span class="annam-cabin-gallery__caption"><?php echo esc_html( $hero_img['caption'] ); ?></span>
						</button>
					</figure>
					<div class="annam-cabin-gallery__side">
						<?php foreach ( $thumbs as $ti => $item ) : ?>
							<?php $idx = $ti + 1; ?>
							<figure class="annam-cabin-gallery__item">
								<button type="button" class="annam-cabin-gallery__trigger" data-annam-gallery-index="<?php echo esc_attr( (string) $idx ); ?>">
									<?php
									$thumb_key = ! empty( $item['image'] ) ? (string) $item['image'] : '';
									if ( $thumb_key && function_exists( 'annam_cabin_landing_print_image' ) ) {
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo annam_cabin_landing_print_image(
											$thumb_key,
											array(
												'alt'     => $item['caption'],
												'width'   => '600',
												'height'  => '450',
												'loading' => 'lazy',
											)
										);
									} elseif ( ! empty( $item['image_url'] ) ) {
										?>
										<img src="<?php echo esc_url( $item['image_url'] ); ?>" alt="<?php echo esc_attr( $item['caption'] ); ?>" width="600" height="450" loading="lazy" decoding="async" />
									<?php } ?>
									<span class="annam-cabin-gallery__caption"><?php echo esc_html( $item['caption'] ); ?></span>
								</button>
							</figure>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['amenities'] ) ) : ?>
	<section class="annam-cabin-section annam-cabin-section--alt">
		<div class="annam-cabin-container">
			<h2 class="annam-cabin-section__title"><?php esc_html_e( 'Tiện Ích Trên Xe', 'generatepress_child' ); ?></h2>
			<div class="annam-cabin-amenity-grid">
				<?php foreach ( $config['amenities'] as $amenity ) : ?>
					<div class="annam-cabin-amenity">
						<h3 class="annam-cabin-amenity__title"><?php echo esc_html( $amenity['title'] ); ?></h3>
						<p><?php echo esc_html( $amenity['description'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['why'] ) && ! empty( $config['why_cards'] ) ) : ?>
	<section class="annam-cabin-section">
		<div class="annam-cabin-container">
			<h2 class="annam-cabin-section__title"><?php esc_html_e( 'Vì Sao Chọn An Nam Discovery?', 'generatepress_child' ); ?></h2>
			<div class="annam-cabin-why-grid">
				<?php foreach ( $config['why_cards'] as $card ) : ?>
					<article class="annam-cabin-why-card">
						<h3 class="annam-cabin-why-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p><?php echo esc_html( $card['text'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $config['why_note'] ) ) : ?>
				<p class="annam-cabin-section__note"><?php echo esc_html( $config['why_note'] ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['steps'] ) ) : ?>
	<section class="annam-cabin-section annam-cabin-section--steps" id="dat-ve-3-buoc">
		<div class="annam-cabin-steps-wrap">
			<h2 class="annam-cabin-section__title annam-cabin-steps-wrap__title"><?php esc_html_e( 'Đặt Vé Chỉ 3 Bước', 'generatepress_child' ); ?></h2>
			<ol class="annam-cabin-steps-flow">
				<?php foreach ( $config['steps'] as $si => $step ) : ?>
					<?php
					$is_featured = ! empty( $step['featured'] );
					$icon        = isset( $step['icon'] ) ? (string) $step['icon'] : 'route';
					?>
					<?php if ( $si > 0 ) : ?>
						<li class="annam-cabin-steps-flow__connector" aria-hidden="true">
							<svg class="annam-cabin-steps-flow__arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</li>
					<?php endif; ?>
					<li class="annam-cabin-steps-flow__step">
						<article class="annam-cabin-step-card<?php echo $is_featured ? ' annam-cabin-step-card--featured' : ''; ?>">
							<span class="annam-cabin-step-card__num"><?php echo esc_html( sprintf( '%02d', $si + 1 ) ); ?></span>
							<span class="annam-cabin-step-card__icon" aria-hidden="true">
								<?php if ( 'cabin' === $icon ) : ?>
									<svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 11l3 3L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
								<?php elseif ( 'confirm' === $icon ) : ?>
									<svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 4L12 14.01l-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
								<?php else : ?>
									<svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
								<?php endif; ?>
							</span>
							<h3 class="annam-cabin-step-card__title"><?php echo esc_html( $step['title'] ); ?></h3>
							<p class="annam-cabin-step-card__text"><?php echo esc_html( $step['text'] ); ?></p>
						</article>
					</li>
				<?php endforeach; ?>
			</ol>
			<div class="annam-cabin-steps-footer">
				<button type="button" class="annam-cabin-btn annam-cabin-btn--primary annam-cabin-btn--steps-cta" data-annam-scroll-form><?php esc_html_e( 'Gửi Yêu Cầu Giữ Chỗ Ngay', 'generatepress_child' ); ?></button>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['promises'] ) && ! empty( $config['promises'] ) ) : ?>
	<section class="annam-cabin-section annam-cabin-section--promises" id="cam-ket">
		<div class="annam-cabin-promises-wrap">
			<h2 class="annam-cabin-section__title annam-cabin-promises-wrap__title"><?php esc_html_e( 'Cam Kết Khi Đặt Vé', 'generatepress_child' ); ?></h2>
			<div class="annam-cabin-promises-layout">
				<?php
				$lead_card   = null;
				$other_cards = array();
				foreach ( $config['promises'] as $pi => $promise ) {
					if ( is_string( $promise ) ) {
						$promise = array(
							'title'    => $promise,
							'text'     => '',
							'featured' => 0 === $pi,
							'icon'     => 0 === $pi ? 'shield' : 'check',
						);
					}
					if ( ! empty( $promise['featured'] ) && null === $lead_card ) {
						$lead_card = $promise;
					} else {
						$other_cards[] = $promise;
					}
				}
				if ( null === $lead_card && ! empty( $config['promises'] ) ) {
					$first = $config['promises'][0];
					$lead_card = is_string( $first ) ? array( 'title' => $first, 'text' => '', 'icon' => 'shield' ) : $first;
					$other_cards = array_slice( $config['promises'], 1 );
					$other_cards = array_map(
						static function ( $p ) {
							return is_string( $p ) ? array( 'title' => $p, 'text' => '', 'icon' => 'check' ) : $p;
						},
						$other_cards
					);
				}
				?>
				<?php if ( $lead_card ) : ?>
					<article class="annam-cabin-promise-card annam-cabin-promise-card--lead">
						<span class="annam-cabin-promise-card__icon" aria-hidden="true">
							<svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</span>
						<h3 class="annam-cabin-promise-card__title"><?php echo esc_html( $lead_card['title'] ); ?></h3>
						<?php if ( ! empty( $lead_card['text'] ) ) : ?>
							<p class="annam-cabin-promise-card__text"><?php echo esc_html( $lead_card['text'] ); ?></p>
						<?php endif; ?>
					</article>
				<?php endif; ?>
				<div class="annam-cabin-promises-grid">
					<?php foreach ( $other_cards as $promise ) : ?>
						<article class="annam-cabin-promise-card">
							<span class="annam-cabin-promise-card__icon" aria-hidden="true">
								<svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 4L12 14.01l-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
							<h3 class="annam-cabin-promise-card__title"><?php echo esc_html( $promise['title'] ); ?></h3>
							<?php if ( ! empty( $promise['text'] ) ) : ?>
								<p class="annam-cabin-promise-card__text"><?php echo esc_html( $promise['text'] ); ?></p>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['related_tours'] ) ) : ?>
		<?php get_template_part( 'template-parts/cabin-landing/part', 'tour-sapa' ); ?>
	<?php endif; ?>

	<?php if ( ! empty( $secs['faq'] ) && ! empty( $config['faq'] ) ) : ?>
	<section class="annam-cabin-section annam-cabin-section--faq" id="faq">
		<div class="annam-cabin-faq-wrap">
			<h2 class="annam-cabin-section__title annam-cabin-faq-wrap__title"><?php esc_html_e( 'Câu Hỏi Thường Gặp', 'generatepress_child' ); ?></h2>
			<div class="annam-cabin-faq-grid">
				<?php foreach ( array_chunk( $config['faq'], 5 ) as $col_items ) : ?>
					<div class="annam-cabin-faq-col">
						<?php foreach ( $col_items as $faq ) : ?>
							<details class="annam-cabin-faq__item">
								<summary class="annam-cabin-faq__q"><?php echo esc_html( $faq['question'] ); ?></summary>
								<div class="annam-cabin-faq__a"><p><?php echo esc_html( $faq['answer'] ); ?></p></div>
							</details>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['cta_final'] ) ) : ?>
		<?php
		$cta_final_bg    = '';
		$cta_final_id    = get_the_ID();
		$cta_final_class = 'annam-cabin-cta-final';
		if ( $cta_final_id ) {
			$cta_final_bg = (string) get_the_post_thumbnail_url( $cta_final_id, 'large' );
			if ( ! $cta_final_bg ) {
				$cta_final_bg = (string) get_the_post_thumbnail_url( $cta_final_id, 'full' );
			}
		}
		if ( $cta_final_bg ) {
			$cta_final_class .= ' annam-cabin-cta-final--has-image';
		}
		?>
	<section
		class="<?php echo esc_attr( $cta_final_class ); ?>"
		id="cta-cuoi"
		<?php if ( $cta_final_bg ) : ?>
			style="--annam-cta-final-bg: url('<?php echo esc_url( $cta_final_bg ); ?>');"
		<?php endif; ?>
	>
		<div class="annam-cabin-cta-final__overlay" aria-hidden="true"></div>
		<div class="annam-cabin-container annam-cabin-cta-final__inner">
			<h2 class="annam-cabin-cta-final__title"><?php esc_html_e( 'Cần Đi Sapa Hôm Nay Hoặc Cuối Tuần?', 'generatepress_child' ); ?></h2>
			<p class="annam-cabin-cta-final__desc"><?php esc_html_e( 'Nhắn Zalo hoặc để lại số — kiểm tra cabin trống và tư vấn giờ đi phù hợp.', 'generatepress_child' ); ?></p>
			<div class="annam-cabin-cta-final__actions">
				<a class="annam-cabin-btn annam-cabin-btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener" data-track="click_zalo"><?php esc_html_e( 'Chat Zalo Kiểm Tra Chỗ', 'generatepress_child' ); ?></a>
				<a class="annam-cabin-btn annam-cabin-btn--primary" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>" data-track="click_hotline"><?php esc_html_e( 'Gọi Đặt Vé Ngay', 'generatepress_child' ); ?></a>
				<button type="button" class="annam-cabin-btn annam-cabin-btn--outline annam-cabin-btn--outline-on-dark" data-annam-scroll-form><?php esc_html_e( 'Điền Form Giữ Chỗ', 'generatepress_child' ); ?></button>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $cabin_show_page_content && '' !== $cabin_page_content_html ) : ?>
	<section
		class="annam-cabin-page-content<?php echo $cabin_page_content_long ? '' : ' annam-cabin-page-content--no-toggle'; ?>"
		id="noi-dung-seo"
	>
		<div class="annam-cabin-container annam-cabin-page-content__inner">
			<div class="annam-cabin-page-content__card">
				<h2 class="annam-cabin-page-content__heading"><?php esc_html_e( 'Xem thêm thông tin về vé xe cabin Hà Nội Sapa', 'generatepress_child' ); ?></h2>
				<div class="annam-cabin-page-content__body-wrap">
					<div class="annam-cabin-page-content__body entry-content">
						<?php echo $cabin_page_content_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content filters. ?>
					</div>
				</div>
				<?php if ( $cabin_page_content_long ) : ?>
					<button
						type="button"
						class="annam-cabin-page-content__toggle"
						aria-expanded="false"
						data-label-more="<?php echo esc_attr__( 'Xem thêm', 'generatepress_child' ); ?>"
						data-label-less="<?php echo esc_attr__( 'Thu gọn', 'generatepress_child' ); ?>"
					>
						<?php esc_html_e( 'Xem thêm', 'generatepress_child' ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<div class="annam-cabin-lightbox" id="annam-cabin-lightbox" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Xem ảnh', 'generatepress_child' ); ?>">
		<button type="button" class="annam-cabin-lightbox__close" data-annam-lightbox-close aria-label="<?php esc_attr_e( 'Đóng', 'generatepress_child' ); ?>">&times;</button>
		<button type="button" class="annam-cabin-lightbox__nav annam-cabin-lightbox__nav--prev" data-annam-lightbox-prev aria-label="<?php esc_attr_e( 'Ảnh trước', 'generatepress_child' ); ?>">&#8249;</button>
		<figure class="annam-cabin-lightbox__figure">
			<img class="annam-cabin-lightbox__img" src="" alt="" />
			<figcaption class="annam-cabin-lightbox__caption"></figcaption>
		</figure>
		<button type="button" class="annam-cabin-lightbox__nav annam-cabin-lightbox__nav--next" data-annam-lightbox-next aria-label="<?php esc_attr_e( 'Ảnh sau', 'generatepress_child' ); ?>">&#8250;</button>
	</div>

	<aside class="annam-cabin-sticky" aria-label="<?php esc_attr_e( 'Liên hệ nhanh', 'generatepress_child' ); ?>">
		<a class="annam-cabin-sticky__btn annam-cabin-sticky__btn--call" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>" data-track="click_hotline"><?php esc_html_e( 'Gọi ngay', 'generatepress_child' ); ?></a>
		<a class="annam-cabin-sticky__btn annam-cabin-sticky__btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener" data-track="click_zalo">Zalo</a>
		<button type="button" class="annam-cabin-sticky__btn annam-cabin-sticky__btn--form" data-annam-scroll-form data-track="scroll_to_form"><?php esc_html_e( 'Giữ chỗ', 'generatepress_child' ); ?></button>
	</aside>
</article>
