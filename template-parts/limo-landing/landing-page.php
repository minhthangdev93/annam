<?php
/**
 * Landing vé Limousine 10 chỗ Hà Nội ⇄ Sapa.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$config  = annam_limo_landing_get_config();
$cta     = annam_limo_landing_get_cta();
$hero    = isset( $config['hero'] ) ? $config['hero'] : array();
$secs    = isset( $config['sections'] ) ? $config['sections'] : array();
$gallery = isset( $config['gallery'] ) ? $config['gallery'] : array();

$seo_html  = '';
$seo_long  = false;
$show_seo  = ! empty( $secs['seo'] );
if ( $show_seo && function_exists( 'annam_limo_landing_page_has_editor_content' ) && annam_limo_landing_page_has_editor_content() ) {
	$seo_html = annam_limo_landing_get_page_content_html();
	$seo_long = strlen( wp_strip_all_tags( $seo_html ) ) > 400;
} else {
	$show_seo = false;
}

$final_bg = '';
if ( has_post_thumbnail() ) {
	$final_bg = (string) get_the_post_thumbnail_url( get_the_ID(), 'full' );
}
?>
<article class="annam-limo-landing">
	<div class="annam-limo-banner">
		<?php
		if ( function_exists( 'annam_limo_landing_print_image' ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo annam_limo_landing_print_image(
				'hero-banner',
				array(
					'alt'     => isset( $hero['title'] ) ? (string) $hero['title'] : __( 'Limousine Hà Nội Sapa', 'generatepress_child' ),
					'loading' => 'eager',
					'class'   => 'annam-limo-banner__img',
				)
			);
		}
		?>
	</div>

	<?php if ( ! empty( $secs['hero'] ) ) : ?>
	<section class="annam-limo-hero">
		<div class="annam-limo-container annam-limo-hero__grid">
			<div class="annam-limo-hero__content">
				<p class="annam-limo-hero__eyebrow"><?php esc_html_e( 'Limousine 10 chỗ', 'generatepress_child' ); ?></p>
				<h1 class="annam-limo-hero__title"><?php echo esc_html( $hero['title'] ); ?></h1>
				<p class="annam-limo-hero__subtitle"><?php echo esc_html( $hero['subtitle'] ); ?></p>
				<p class="annam-limo-hero__price">
					<span class="annam-limo-hero__price-label"><?php esc_html_e( 'Giá từ', 'generatepress_child' ); ?></span>
					<strong><?php echo esc_html( $hero['price_from'] ); ?></strong>
					<span class="annam-limo-hero__price-unit">/ghế/chiều</span>
				</p>
				<?php if ( ! empty( $hero['badges'] ) ) : ?>
					<ul class="annam-limo-hero__badges">
						<?php foreach ( $hero['badges'] as $badge ) : ?>
							<li class="annam-limo-hero__badge"><?php echo esc_html( $badge ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<div class="annam-limo-hero__ctas annam-limo-hero__ctas--desktop">
					<a class="annam-limo-btn annam-limo-btn--primary" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>" data-track="click_hotline"><?php esc_html_e( 'Gọi Đặt Vé Ngay', 'generatepress_child' ); ?></a>
					<a class="annam-limo-btn annam-limo-btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener" data-track="click_zalo"><?php esc_html_e( 'Chat Zalo Giữ Chỗ', 'generatepress_child' ); ?></a>
					<a class="annam-limo-btn annam-limo-btn--outline" href="#gia-ve"><?php esc_html_e( 'Xem Bảng Giá', 'generatepress_child' ); ?></a>
				</div>
			</div>
			<div class="annam-limo-hero__form-col">
				<div class="annam-limo-hero__ctas annam-limo-hero__ctas--mobile">
					<a class="annam-limo-btn annam-limo-btn--primary" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php esc_html_e( 'Gọi Đặt Vé Ngay', 'generatepress_child' ); ?></a>
					<a class="annam-limo-btn annam-limo-btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Chat Zalo Giữ Chỗ', 'generatepress_child' ); ?></a>
				</div>
				<?php get_template_part( 'template-parts/limo-landing/part', 'form' ); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['pricing'] ) ) : ?>
	<section class="annam-limo-section" id="gia-ve">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Bảng Giá Vé Limousine', 'generatepress_child' ); ?></h2>
			<p class="annam-limo-section__lead"><?php esc_html_e( 'Áp dụng cả hai chiều Hà Nội → Sapa và Sapa → Hà Nội.', 'generatepress_child' ); ?></p>
			<div class="annam-limo-price-grid">
				<?php foreach ( $config['pricing']['rows'] as $row ) : ?>
					<article class="annam-limo-price-card<?php echo ! empty( $row['badge'] ) ? ' annam-limo-price-card--highlight' : ''; ?>">
						<?php if ( ! empty( $row['badge'] ) ) : ?>
							<span class="annam-limo-price-card__badge"><?php echo esc_html( $row['badge'] ); ?></span>
						<?php endif; ?>
						<h3 class="annam-limo-price-card__name"><?php echo esc_html( $row['label'] ); ?></h3>
						<p class="annam-limo-price-card__price"><?php echo esc_html( $row['price'] ); ?></p>
						<?php if ( ! empty( $row['desc'] ) ) : ?>
							<p class="annam-limo-price-card__desc"><?php echo esc_html( $row['desc'] ); ?></p>
						<?php endif; ?>
						<button type="button" class="annam-limo-btn annam-limo-btn--primary annam-limo-btn--block" data-annam-pick-seat="<?php echo esc_attr( $row['type'] ); ?>" data-track="select_seat_type"><?php esc_html_e( 'Chọn Vé Này', 'generatepress_child' ); ?></button>
					</article>
				<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $config['pricing']['price_note'] ) ) : ?>
				<p class="annam-limo-section__note"><?php echo esc_html( $config['pricing']['price_note'] ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['schedule'] ) ) : ?>
	<section class="annam-limo-section annam-limo-section--alt" id="lich-xe">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Lịch Xe Limousine', 'generatepress_child' ); ?></h2>
			<p class="annam-limo-section__lead"><?php esc_html_e( '02 chuyến mỗi chiều mỗi ngày · khoảng 06 giờ/chiều.', 'generatepress_child' ); ?></p>
			<div class="annam-limo-schedule" data-annam-tabs="schedule">
				<div class="annam-limo-schedule__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Chiều tuyến', 'generatepress_child' ); ?>">
					<?php foreach ( $config['schedules'] as $i => $sch ) : ?>
						<button type="button" class="annam-limo-tabs__btn annam-limo-schedule__tab<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" data-tab="<?php echo esc_attr( $sch['id'] ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"><?php echo esc_html( $sch['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
				<?php foreach ( $config['schedules'] as $i => $sch ) : ?>
					<?php
					$timeline = isset( $config['timelines'][ $sch['id'] ] ) ? $config['timelines'][ $sch['id'] ] : null;
					$time_meta = array(
						'07:00' => __( 'Chuyến sáng', 'generatepress_child' ),
						'14:00' => __( 'Chuyến chiều', 'generatepress_child' ),
					);
					?>
					<div class="annam-limo-tabs__panel annam-limo-schedule__panel<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tabpanel" data-panel="<?php echo esc_attr( $sch['id'] ); ?>">
						<div class="annam-limo-schedule__card">
							<div class="annam-limo-schedule__times-block">
								<p class="annam-limo-schedule__label"><?php esc_html_e( 'Giờ xuất phát', 'generatepress_child' ); ?></p>
								<div class="annam-limo-time-grid" role="group" aria-label="<?php echo esc_attr( $sch['label'] ); ?>">
									<?php foreach ( $sch['times'] as $ti => $time ) : ?>
										<button type="button" class="annam-limo-time-btn<?php echo 0 === $ti ? ' is-active' : ''; ?>" data-annam-pick-time="<?php echo esc_attr( $time ); ?>" data-from="<?php echo esc_attr( $sch['from'] ); ?>" data-to="<?php echo esc_attr( $sch['to'] ); ?>" data-track="select_departure_time">
											<span class="annam-limo-time-btn__clock"><?php echo esc_html( $time ); ?></span>
											<span class="annam-limo-time-btn__meta"><?php echo esc_html( isset( $time_meta[ $time ] ) ? $time_meta[ $time ] : __( 'Chuyến trong ngày', 'generatepress_child' ) ); ?></span>
										</button>
									<?php endforeach; ?>
								</div>
								<p class="annam-limo-schedule__hint"><?php esc_html_e( 'Bấm giờ để điền vào form giữ chỗ bên trên.', 'generatepress_child' ); ?></p>
							</div>

							<?php if ( $timeline ) : ?>
							<div class="annam-limo-timeline">
								<h3 class="annam-limo-timeline__title"><?php echo esc_html( $timeline['heading'] ); ?></h3>
								<ol class="annam-limo-timeline__list">
									<?php foreach ( $timeline['steps'] as $step ) : ?>
										<li class="annam-limo-timeline__step">
											<span class="annam-limo-timeline__dot" aria-hidden="true"></span>
											<div class="annam-limo-timeline__body">
												<strong class="annam-limo-timeline__place"><?php echo esc_html( $step['place'] ); ?></strong>
												<span class="annam-limo-timeline__note"><?php echo esc_html( $step['note'] ); ?></span>
											</div>
										</li>
									<?php endforeach; ?>
								</ol>
							</div>
							<?php endif; ?>

							<div class="annam-limo-schedule__footer">
								<button type="button" class="annam-limo-btn annam-limo-btn--primary" data-annam-scroll-form><?php esc_html_e( 'Chọn Giờ Này Và Giữ Chỗ', 'generatepress_child' ); ?></button>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['pickup'] ) && ! empty( $config['pickup_tabs'] ) ) : ?>
	<section class="annam-limo-section annam-limo-section--pickup" id="diem-don">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Điểm Đón & Điểm Trả', 'generatepress_child' ); ?></h2>
			<p class="annam-limo-section__lead"><?php esc_html_e( 'Chọn khu vực bên dưới — giờ đón là dự kiến và được xác nhận trước chuyến.', 'generatepress_child' ); ?></p>
			<div class="annam-limo-tabs" data-annam-tabs="pickup">
				<div class="annam-limo-tabs__nav" role="tablist">
					<?php foreach ( $config['pickup_tabs'] as $i => $tab ) : ?>
						<button type="button" class="annam-limo-tabs__btn<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" data-tab="<?php echo esc_attr( $tab['id'] ); ?>"><?php echo esc_html( $tab['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
				<?php foreach ( $config['pickup_tabs'] as $i => $tab ) : ?>
					<div class="annam-limo-tabs__panel<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tabpanel" data-panel="<?php echo esc_attr( $tab['id'] ); ?>">
						<?php if ( ! empty( $tab['heading'] ) ) : ?>
							<p class="annam-limo-pickup__heading">
								<svg class="annam-limo-pickup__heading-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
								<span><?php echo esc_html( $tab['heading'] ); ?></span>
							</p>
						<?php endif; ?>
						<ul class="annam-limo-pickup__list">
							<?php foreach ( $tab['items'] as $j => $item ) : ?>
								<li class="annam-limo-pickup__item">
									<span class="annam-limo-pickup__marker" aria-hidden="true"><?php echo esc_html( (string) ( $j + 1 ) ); ?></span>
									<div class="annam-limo-pickup__body">
										<strong class="annam-limo-pickup__name"><?php echo esc_html( $item['name'] ); ?></strong>
										<span class="annam-limo-pickup__time">
											<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
											<?php echo esc_html( $item['time'] ); ?>
										</span>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
						<?php if ( ! empty( $tab['note'] ) ) : ?>
							<p class="annam-limo-pickup__note"><?php echo esc_html( $tab['note'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['gallery'] ) && ! empty( $gallery ) ) : ?>
	<section class="annam-limo-section annam-limo-section--alt" id="anh-xe">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Hình Ảnh Xe Limousine', 'generatepress_child' ); ?></h2>
			<p class="annam-limo-section__lead"><?php esc_html_e( 'Xe thật — ghế Limousine 10 chỗ trên tuyến Hà Nội ⇄ Sapa.', 'generatepress_child' ); ?></p>
			<div class="annam-limo-gallery" data-annam-limo-gallery>
				<?php foreach ( $gallery as $i => $item ) : ?>
					<?php
					$slot = isset( $item['slot'] ) ? (string) $item['slot'] : '';
					$cap  = isset( $item['caption'] ) ? (string) $item['caption'] : '';
					?>
					<figure class="annam-limo-gallery__item<?php echo 0 === $i ? ' annam-limo-gallery__item--hero' : ''; ?>" data-gallery-index="<?php echo esc_attr( (string) $i ); ?>">
						<button type="button" class="annam-limo-gallery__btn" data-annam-gallery-open="<?php echo esc_attr( (string) $i ); ?>" aria-label="<?php echo esc_attr( $cap ? $cap : __( 'Xem ảnh', 'generatepress_child' ) ); ?>">
							<?php
							if ( $slot && function_exists( 'annam_limo_landing_print_image' ) ) {
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo annam_limo_landing_print_image(
									$slot,
									array(
										'alt'     => $cap,
										'loading' => 0 === $i ? 'eager' : 'lazy',
									)
								);
							}
							?>
						</button>
						<?php if ( $cap ) : ?>
							<figcaption class="annam-limo-gallery__caption"><?php echo esc_html( $cap ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['why'] ) ) : ?>
	<section class="annam-limo-section annam-limo-section--why" id="uu-diem">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Vì Sao Chọn Limousine 10 Chỗ?', 'generatepress_child' ); ?></h2>
			<p class="annam-limo-section__lead"><?php esc_html_e( 'Xe nhỏ hơn, lịch cố định, đón trả gần — giữ chỗ nhanh qua form hoặc Zalo.', 'generatepress_child' ); ?></p>
			<div class="annam-limo-why-grid">
				<?php foreach ( $config['why_cards'] as $i => $card ) :
					$icon = isset( $card['icon'] ) ? (string) $card['icon'] : 'van';
					?>
					<article class="annam-limo-why-card">
						<span class="annam-limo-why-card__index" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="annam-limo-why-card__icon" aria-hidden="true">
							<?php if ( 'clock' === $icon ) : ?>
								<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
							<?php elseif ( 'pin' === $icon ) : ?>
								<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
							<?php elseif ( 'zap' === $icon ) : ?>
								<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/></svg>
							<?php else : ?>
								<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16h13l3-5H8l-2 5z"/><path d="M5 16v2a1 1 0 0 0 1 1h1"/><path d="M14 16v2a1 1 0 0 0 1 1h1"/><circle cx="7.5" cy="19" r="1.5"/><circle cx="16.5" cy="19" r="1.5"/><path d="M8 11V8h6"/><path d="M5 11l1.5-3H9"/></svg>
							<?php endif; ?>
						</span>
						<h3 class="annam-limo-why-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p><?php echo esc_html( $card['text'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['steps'] ) ) : ?>
	<section class="annam-limo-section annam-limo-section--alt" id="dat-ve">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Đặt Vé 3 Bước', 'generatepress_child' ); ?></h2>
			<ol class="annam-limo-steps">
				<?php foreach ( $config['steps'] as $i => $step ) : ?>
					<li class="annam-limo-steps__item">
						<span class="annam-limo-steps__num"><?php echo esc_html( (string) ( $i + 1 ) ); ?></span>
						<div>
							<h3><?php echo esc_html( $step['title'] ); ?></h3>
							<p><?php echo esc_html( $step['text'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
			<p class="annam-limo-section__cta-row">
				<button type="button" class="annam-limo-btn annam-limo-btn--primary" data-annam-scroll-form><?php esc_html_e( 'Giữ Chỗ Ngay', 'generatepress_child' ); ?></button>
			</p>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['faq'] ) ) : ?>
	<section class="annam-limo-section" id="faq">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Câu Hỏi Thường Gặp', 'generatepress_child' ); ?></h2>
			<div class="annam-limo-faq">
				<?php foreach ( $config['faq'] as $item ) : ?>
					<details class="annam-limo-faq__item">
						<summary><?php echo esc_html( $item['question'] ); ?></summary>
						<p><?php echo esc_html( $item['answer'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['final_cta'] ) ) : ?>
	<section class="annam-limo-final" id="cta-cuoi"<?php echo $final_bg ? ' style="--annam-limo-final-bg:url(' . esc_url( $final_bg ) . ')"' : ''; ?>>
		<div class="annam-limo-container annam-limo-final__inner">
			<h2><?php echo esc_html( $config['final_cta']['title'] ); ?></h2>
			<p><?php echo esc_html( $config['final_cta']['subtitle'] ); ?></p>
			<div class="annam-limo-final__ctas">
				<button type="button" class="annam-limo-btn annam-limo-btn--primary" data-annam-scroll-form><?php esc_html_e( 'Giữ Chỗ Ngay', 'generatepress_child' ); ?></button>
				<a class="annam-limo-btn annam-limo-btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Nhắn Zalo', 'generatepress_child' ); ?></a>
				<a class="annam-limo-btn annam-limo-btn--outline" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php echo esc_html( $cta['hotline_display'] ); ?></a>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $show_seo && $seo_html ) : ?>
	<section class="annam-limo-section" id="noi-dung-seo">
		<div class="annam-limo-container annam-limo-container--narrow">
			<div class="annam-limo-seo<?php echo $seo_long ? ' is-collapsible' : ''; ?>" data-annam-seo>
				<div class="annam-limo-seo__body">
					<?php echo $seo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<?php if ( $seo_long ) : ?>
					<button type="button" class="annam-limo-btn annam-limo-btn--outline" data-annam-seo-toggle><?php esc_html_e( 'Xem thêm', 'generatepress_child' ); ?></button>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<div class="annam-limo-lightbox" id="annam-limo-lightbox" hidden>
		<button type="button" class="annam-limo-lightbox__close" data-annam-lightbox-close aria-label="<?php esc_attr_e( 'Đóng', 'generatepress_child' ); ?>">&times;</button>
		<img src="" alt="" class="annam-limo-lightbox__img" />
		<p class="annam-limo-lightbox__cap"></p>
	</div>

	<div class="annam-limo-sticky" role="navigation" aria-label="<?php esc_attr_e( 'Thao tác nhanh', 'generatepress_child' ); ?>">
		<a class="annam-limo-sticky__btn" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php esc_html_e( 'Gọi', 'generatepress_child' ); ?></a>
		<a class="annam-limo-sticky__btn annam-limo-sticky__btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener">Zalo</a>
		<button type="button" class="annam-limo-sticky__btn annam-limo-sticky__btn--form" data-annam-scroll-form><?php esc_html_e( 'Giữ chỗ', 'generatepress_child' ); ?></button>
	</div>
</article>
