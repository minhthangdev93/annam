<?php
/**
 * Landing vé Limousine 11 chỗ Hà Nội ⇄ Sapa.
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
					'alt'           => ! empty( $hero['image_alt'] ) ? (string) $hero['image_alt'] : ( isset( $hero['title'] ) ? (string) $hero['title'] : __( 'Limousine Hà Nội Sapa', 'generatepress_child' ) ),
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'class'         => 'annam-limo-banner__img',
				)
			);
		}
		?>
	</div>

	<?php if ( ! empty( $secs['hero'] ) ) : ?>
	<section class="annam-limo-hero">
		<div class="annam-limo-container annam-limo-hero__grid">
			<div class="annam-limo-hero__content">
				<p class="annam-limo-hero__eyebrow"><?php echo esc_html( ! empty( $hero['eyebrow'] ) ? $hero['eyebrow'] : __( 'Limousine 11 chỗ', 'generatepress_child' ) ); ?></p>
				<h1 class="annam-limo-hero__title"><?php echo esc_html( $hero['title'] ); ?></h1>
				<p class="annam-limo-hero__subtitle"><?php echo esc_html( $hero['subtitle'] ); ?></p>
				<p class="annam-limo-hero__price">
					<span class="annam-limo-hero__price-label"><?php esc_html_e( 'Giá từ', 'generatepress_child' ); ?></span>
					<strong><?php echo esc_html( $hero['price_from'] ); ?></strong>
					<span class="annam-limo-hero__price-unit"><?php echo esc_html( ! empty( $hero['price_unit'] ) ? $hero['price_unit'] : '/ghế/chiều' ); ?></span>
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

	<?php if ( ! empty( $secs['anchor_nav'] ) && ! empty( $config['anchors'] ) ) : ?>
	<?php
	$anchor_icons = array(
		'gia-ve'      => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18M7 15h4"/></svg>',
		'lich-xe'     => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/></svg>',
		'diem-don'    => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s6-5.2 6-10a6 6 0 1 0-12 0c0 4.8 6 10 6 10z"/><circle cx="12" cy="11" r="2"/></svg>',
		'chinh-sach'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l7 3v6c0 4.2-2.8 6.8-7 9-4.2-2.2-7-4.8-7-9V6l7-3z"/></svg>',
		'thanh-toan'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="2.4"/><path d="M7 12h.01M17 12h.01"/></svg>',
		'anh-xe'      => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="2"/><circle cx="9" cy="10" r="1.4"/><path d="m4 16 4.2-3.2L12 15l3.2-2.6L20 16"/></svg>',
		'faq'         => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M9.5 9.6a2.5 2.5 0 1 1 3.3 2.4c-.8.4-1.3.9-1.3 1.7"/><path d="M12 16.6h.01"/></svg>',
	);
	?>
	<nav class="annam-limo-anchors" aria-label="<?php esc_attr_e( 'Mục trên trang', 'generatepress_child' ); ?>">
		<div class="annam-limo-container">
			<ul class="annam-limo-anchors__list">
				<?php foreach ( $config['anchors'] as $anchor ) : ?>
					<?php $anchor_id = isset( $anchor['id'] ) ? (string) $anchor['id'] : ''; ?>
					<li>
						<a href="#<?php echo esc_attr( $anchor_id ); ?>">
							<?php if ( isset( $anchor_icons[ $anchor_id ] ) ) : ?>
								<span class="annam-limo-anchors__icon" aria-hidden="true"><?php echo $anchor_icons[ $anchor_id ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
							<?php endif; ?>
							<span><?php echo esc_html( $anchor['label'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</nav>
	<?php endif; ?>

	<?php if ( ! empty( $secs['pricing'] ) ) : ?>
	<section class="annam-limo-section annam-limo-section--band-orange" id="gia-ve">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Bảng Giá Vé Limousine', 'generatepress_child' ); ?></h2>
			<p class="annam-limo-section__lead"><?php echo esc_html( ! empty( $config['pricing']['lead'] ) ? $config['pricing']['lead'] : __( 'Áp dụng cả hai chiều Hà Nội → Sapa và Sapa → Hà Nội. Xe 11 chỗ đã gồm ghế tài xế — tối đa 10 ghế khách.', 'generatepress_child' ) ); ?></p>
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
			<?php if ( ! empty( $config['pricing']['charter']['type'] ) ) : ?>
				<?php $charter = $config['pricing']['charter']; ?>
				<aside class="annam-limo-charter">
					<div class="annam-limo-charter__body">
						<?php if ( ! empty( $charter['kicker'] ) ) : ?>
							<span class="annam-limo-charter__kicker"><?php echo esc_html( $charter['kicker'] ); ?></span>
						<?php endif; ?>
						<h3><?php echo esc_html( $charter['label'] ); ?></h3>
						<?php if ( ! empty( $charter['points'] ) && is_array( $charter['points'] ) ) : ?>
							<ul class="annam-limo-charter__points">
								<?php foreach ( $charter['points'] as $point ) : ?>
									<li><?php echo esc_html( $point ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php elseif ( ! empty( $charter['text'] ) ) : ?>
							<p><?php echo esc_html( $charter['text'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $charter['note'] ) ) : ?>
							<p class="annam-limo-charter__note"><?php echo esc_html( $charter['note'] ); ?></p>
						<?php endif; ?>
					</div>
					<div class="annam-limo-charter__price">
						<p class="annam-limo-charter__amount"><?php echo esc_html( $charter['price'] ); ?></p>
						<?php if ( ! empty( $charter['unit'] ) ) : ?>
							<p class="annam-limo-charter__unit"><?php echo esc_html( $charter['unit'] ); ?></p>
						<?php endif; ?>
						<button type="button" class="annam-limo-btn annam-limo-btn--primary" data-annam-pick-seat="<?php echo esc_attr( $charter['type'] ); ?>" data-track="select_seat_type"><?php echo esc_html( ! empty( $charter['cta'] ) ? $charter['cta'] : __( 'Thuê xe này', 'generatepress_child' ) ); ?></button>
					</div>
				</aside>
			<?php endif; ?>
			<?php if ( ! empty( $config['pricing']['price_note'] ) ) : ?>
				<p class="annam-limo-section__note"><?php echo esc_html( $config['pricing']['price_note'] ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['schedule'] ) ) : ?>
	<section class="annam-limo-section annam-limo-section--band-teal" id="lich-xe">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Lịch Xe Limousine', 'generatepress_child' ); ?></h2>
			<?php if ( ! empty( $config['schedule_lead'] ) ) : ?>
				<p class="annam-limo-section__lead"><?php echo esc_html( (string) $config['schedule_lead'] ); ?></p>
			<?php endif; ?>
			<div class="annam-limo-schedule" data-annam-tabs="schedule">
				<div class="annam-limo-schedule__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Chiều tuyến', 'generatepress_child' ); ?>">
					<?php foreach ( $config['schedules'] as $i => $sch ) : ?>
						<button type="button" class="annam-limo-tabs__btn annam-limo-schedule__tab<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" data-tab="<?php echo esc_attr( $sch['id'] ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"><?php echo esc_html( $sch['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
				<?php foreach ( $config['schedules'] as $i => $sch ) : ?>
					<?php
					$timeline = ( isset( $config['timelines'][ $sch['id'] ] ) && ! empty( $config['timelines'][ $sch['id'] ]['steps'] ) ) ? $config['timelines'][ $sch['id'] ] : null;
					$time_meta = array(
						'07:00' => __( 'Chuyến sáng', 'generatepress_child' ),
						'07:30' => __( 'Chuyến sáng', 'generatepress_child' ),
						'08:00' => __( 'Chuyến sáng', 'generatepress_child' ),
						'12:00' => __( 'Chuyến trưa', 'generatepress_child' ),
						'13:00' => __( 'Chuyến trưa', 'generatepress_child' ),
						'14:30' => __( 'Chuyến chiều', 'generatepress_child' ),
					);
					?>
					<div class="annam-limo-tabs__panel annam-limo-schedule__panel<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tabpanel" data-panel="<?php echo esc_attr( $sch['id'] ); ?>">
						<div class="annam-limo-schedule__card<?php echo $timeline ? '' : ' annam-limo-schedule__card--solo'; ?>">
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
	<?php
	$pickup_tabs   = $config['pickup_tabs'];
	$pickup_split  = ( ! empty( $config['pickup_layout'] ) && 'split' === $config['pickup_layout'] && count( $pickup_tabs ) >= 2 );
	$render_pickup_col = static function ( $tab ) {
		?>
		<div class="annam-limo-pickup-split__col">
			<p class="annam-limo-pickup__heading">
				<svg class="annam-limo-pickup__heading-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
				<span><?php echo esc_html( ! empty( $tab['heading'] ) ? $tab['heading'] : $tab['label'] ); ?></span>
			</p>
			<ul class="annam-limo-pickup__list annam-limo-pickup__list--stack">
				<?php foreach ( $tab['items'] as $j => $item ) : ?>
					<li class="annam-limo-pickup__item">
						<span class="annam-limo-pickup__marker" aria-hidden="true"><?php echo esc_html( (string) ( $j + 1 ) ); ?></span>
						<div class="annam-limo-pickup__body">
							<strong class="annam-limo-pickup__name"><?php echo esc_html( $item['name'] ); ?></strong>
							<?php if ( ! empty( $item['time'] ) ) : ?>
								<span class="annam-limo-pickup__time">
									<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
									<?php echo esc_html( (string) $item['time'] ); ?>
								</span>
							<?php endif; ?>
							<?php if ( ! empty( $item['map_url'] ) ) : ?>
								<a class="annam-limo-pickup__map" href="<?php echo esc_url( $item['map_url'] ); ?>" target="_blank" rel="noopener" data-track="limo_map_click"><?php esc_html_e( 'Xem Google Maps', 'generatepress_child' ); ?></a>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( ! empty( $tab['note'] ) ) : ?>
				<p class="annam-limo-pickup__note"><?php echo esc_html( $tab['note'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	};
	?>
	<section class="annam-limo-section annam-limo-section--pickup annam-limo-section--band-orange" id="diem-don">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Điểm Đón & Điểm Trả', 'generatepress_child' ); ?></h2>
			<?php if ( ! empty( $config['pickup_lead'] ) ) : ?>
				<p class="annam-limo-section__lead"><?php echo esc_html( (string) $config['pickup_lead'] ); ?></p>
			<?php endif; ?>
			<?php if ( $pickup_split ) : ?>
				<div class="annam-limo-pickup-split">
					<?php $render_pickup_col( $pickup_tabs[0] ); ?>
					<div class="annam-limo-pickup-split__sep" aria-hidden="true"><span>⇄</span></div>
					<?php $render_pickup_col( $pickup_tabs[1] ); ?>
				</div>
			<?php else : ?>
			<div class="annam-limo-tabs" data-annam-tabs="pickup">
				<div class="annam-limo-tabs__nav" role="tablist">
					<?php foreach ( $pickup_tabs as $i => $tab ) : ?>
						<button type="button" class="annam-limo-tabs__btn<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" data-tab="<?php echo esc_attr( $tab['id'] ); ?>"><?php echo esc_html( $tab['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
				<?php foreach ( $pickup_tabs as $i => $tab ) : ?>
					<div class="annam-limo-tabs__panel<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tabpanel" data-panel="<?php echo esc_attr( $tab['id'] ); ?>">
						<?php $render_pickup_col( $tab ); ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['gallery'] ) && ! empty( $gallery ) ) : ?>
	<section class="annam-limo-section annam-limo-section--band-teal" id="anh-xe">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Hình Ảnh Xe Limousine', 'generatepress_child' ); ?></h2>
			<p class="annam-limo-section__lead"><?php echo esc_html( ! empty( $config['gallery_lead'] ) ? $config['gallery_lead'] : __( 'Xe thật — Limousine 11 chỗ (gồm ghế tài xế) trên tuyến Hà Nội ⇄ Sapa.', 'generatepress_child' ) ); ?></p>
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

	<?php
	$video_cfg  = isset( $config['video'] ) ? $config['video'] : array();
	$video_url  = function_exists( 'annam_limo_landing_youtube_url_or_empty' ) ? annam_limo_landing_youtube_url_or_empty() : '';
	$video_src  = ( '' !== $video_url && function_exists( 'annam_limo_landing_youtube_embed_src' ) ) ? annam_limo_landing_youtube_embed_src( $video_url ) : '';
	$show_video = ! empty( $secs['video'] ) && '' !== $video_src;
	?>
	<?php if ( $show_video ) : ?>
	<section class="annam-limo-section annam-limo-section--video annam-limo-section--band-orange" id="video-xe">
		<div class="annam-limo-container annam-limo-container--narrow">
			<h2 class="annam-limo-section__title"><?php echo esc_html( ! empty( $video_cfg['title'] ) ? $video_cfg['title'] : __( 'Xem Xe & Hành Trình Thật', 'generatepress_child' ) ); ?></h2>
			<?php if ( ! empty( $video_cfg['lead'] ) ) : ?>
				<p class="annam-limo-section__lead"><?php echo esc_html( $video_cfg['lead'] ); ?></p>
			<?php endif; ?>
			<div class="annam-limo-video">
				<iframe
					class="annam-limo-video__frame"
					src="<?php echo esc_url( $video_src ); ?>"
					title="<?php echo esc_attr( ! empty( $video_cfg['title'] ) ? $video_cfg['title'] : __( 'Video Limousine', 'generatepress_child' ) ); ?>"
					loading="lazy"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
					allowfullscreen
					referrerpolicy="strict-origin-when-cross-origin"
				></iframe>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['amenities'] ) && ! empty( $config['amenities'] ) ) : ?>
	<?php
	$amenity_icons = array(
		'ac'       => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true"><path d="M12 3v3M12 18v3M4.2 6.2l2.1 2.1M17.7 15.7l2.1 2.1M3 12h3M18 12h3M4.2 17.8l2.1-2.1M17.7 8.3l2.1-2.1"/><circle cx="12" cy="12" r="3"/></svg>',
		'water'    => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3s6 6.4 6 10a6 6 0 0 1-12 0c0-3.6 6-10 6-10z"/></svg>',
		'towel'    => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4h10v16H7z"/><path d="M7 8h10M10 12h4"/></svg>',
		'wifi'     => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true"><path d="M5 12a9 9 0 0 1 14 0"/><path d="M8 15a5 5 0 0 1 8 0"/><path d="M12 18h.01"/></svg>',
		'usb'      => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 3v6M15 3v6"/><path d="M8 9h8v4a4 4 0 0 1-8 0V9z"/></svg>',
		'seat'     => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 19v-6"/><path d="M6 13h9a3 3 0 0 1 3 3v3"/><path d="M6 13V8a2 2 0 0 1 2-2h3"/></svg>',
		'massage'  => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true"><path d="M3 9c2.2-3 4.2-3 6.2 0s4 3 6.2 0 4-3 5.6 0"/><path d="M3 15c2.2-3 4.2-3 6.2 0s4 3 6.2 0 4-3 5.6 0"/></svg>',
	);
	?>
	<section class="annam-limo-section annam-limo-amenities annam-limo-section--band-teal" id="tien-ich">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Tiện ích trên xe', 'generatepress_child' ); ?></h2>
			<ul class="annam-limo-amenities__grid">
				<?php foreach ( $config['amenities'] as $amenity ) : ?>
					<?php $amenity_icon = isset( $amenity['icon'] ) ? (string) $amenity['icon'] : 'ac'; ?>
					<li class="annam-limo-amenities__item">
						<span class="annam-limo-amenities__icon" aria-hidden="true"><?php echo isset( $amenity_icons[ $amenity_icon ] ) ? $amenity_icons[ $amenity_icon ] : $amenity_icons['ac']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
						<strong><?php echo esc_html( $amenity['title'] ); ?></strong>
						<?php if ( ! empty( $amenity['text'] ) ) : ?>
							<span class="annam-limo-amenities__text"><?php echo esc_html( $amenity['text'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['policies'] ) && ! empty( $config['policies'] ) ) : ?>
	<?php
	$policy_icons = array(
		'bag'   => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 8V7a4 4 0 0 1 8 0v1"/><path d="M6 8h12l-1 12H7L6 8z"/></svg>',
		'swap'  => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 7h11l-3-3"/><path d="M17 17H6l3 3"/></svg>',
		'clock' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
		'pin'   => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>',
		'ban'   => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M6 6l12 12"/></svg>',
	);
	$policy_featured = null;
	$policy_cards    = array();
	foreach ( $config['policies'] as $policy_item ) {
		if ( null === $policy_featured && ! empty( $policy_item['tiers'] ) && is_array( $policy_item['tiers'] ) ) {
			$policy_featured = $policy_item;
			continue;
		}
		$policy_cards[] = $policy_item;
	}
	?>
	<section class="annam-limo-section annam-limo-policies annam-limo-section--band-orange" id="chinh-sach">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php esc_html_e( 'Chính sách vé', 'generatepress_child' ); ?></h2>
			<?php if ( ! empty( $config['policies_lead'] ) ) : ?>
				<p class="annam-limo-section__lead"><?php echo esc_html( $config['policies_lead'] ); ?></p>
			<?php endif; ?>
			<?php if ( $policy_featured ) : ?>
				<div class="annam-limo-policies__refund">
					<h3 class="annam-limo-policies__refund-title"><?php echo esc_html( $policy_featured['label'] ); ?></h3>
					<ol class="annam-limo-policies__tiers">
						<?php foreach ( $policy_featured['tiers'] as $tier ) : ?>
							<?php $tier_tone = isset( $tier['tone'] ) ? sanitize_html_class( (string) $tier['tone'] ) : 'full'; ?>
							<li class="annam-limo-policies__tier annam-limo-policies__tier--<?php echo esc_attr( $tier_tone ); ?>">
								<strong><?php echo esc_html( $tier['result'] ); ?></strong>
								<span><?php echo esc_html( $tier['when'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			<?php endif; ?>
			<?php if ( $policy_cards ) : ?>
				<ul class="annam-limo-policies__grid">
					<?php foreach ( $policy_cards as $policy ) : ?>
						<?php
						$policy_icon = isset( $policy['icon'] ) ? (string) $policy['icon'] : 'clock';
						$policy_tone = isset( $policy['tone'] ) ? sanitize_html_class( (string) $policy['tone'] ) : '';
						?>
						<li class="annam-limo-policies__card<?php echo $policy_tone ? ' annam-limo-policies__card--' . esc_attr( $policy_tone ) : ''; ?>">
							<span class="annam-limo-policies__icon" aria-hidden="true"><?php echo isset( $policy_icons[ $policy_icon ] ) ? $policy_icons[ $policy_icon ] : $policy_icons['clock']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
							<div class="annam-limo-policies__body">
								<h3 class="annam-limo-policies__label">
									<?php echo esc_html( $policy['label'] ); ?>
									<?php if ( ! empty( $policy['badge'] ) ) : ?>
										<span class="annam-limo-policies__badge"><?php echo esc_html( $policy['badge'] ); ?></span>
									<?php endif; ?>
								</h3>
								<p><?php echo esc_html( $policy['text'] ); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php
	$payment = ( ! empty( $secs['payment'] ) && ! empty( $config['payment'] ) && is_array( $config['payment'] ) )
		? $config['payment']
		: array();
	?>
	<?php if ( ! empty( $payment['account_number'] ) ) : ?>
	<section class="annam-limo-section annam-limo-section--compact annam-limo-payment annam-limo-section--band-teal" id="thanh-toan">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php echo esc_html( ! empty( $payment['title'] ) ? $payment['title'] : __( 'Thông tin thanh toán công ty', 'generatepress_child' ) ); ?></h2>
			<?php if ( ! empty( $payment['lead'] ) ) : ?>
				<p class="annam-limo-section__lead annam-limo-payment__lead"><?php echo esc_html( $payment['lead'] ); ?></p>
			<?php endif; ?>
			<div class="annam-limo-payment__card">
				<div class="annam-limo-payment__main">
					<div class="annam-limo-payment__row">
						<span class="annam-limo-payment__k"><?php esc_html_e( 'Tên TK', 'generatepress_child' ); ?></span>
						<span class="annam-limo-payment__v"><?php echo esc_html( (string) $payment['account_name'] ); ?></span>
						<button type="button" class="annam-limo-payment__copy" data-annam-limo-copy="<?php echo esc_attr( (string) $payment['account_name'] ); ?>"><?php esc_html_e( 'Copy', 'generatepress_child' ); ?></button>
					</div>
					<div class="annam-limo-payment__row">
						<span class="annam-limo-payment__k"><?php esc_html_e( 'STK', 'generatepress_child' ); ?></span>
						<span class="annam-limo-payment__v annam-limo-payment__stk"><?php echo esc_html( (string) $payment['account_number'] ); ?></span>
						<button type="button" class="annam-limo-payment__copy" data-annam-limo-copy="<?php echo esc_attr( (string) $payment['account_number'] ); ?>"><?php esc_html_e( 'Copy', 'generatepress_child' ); ?></button>
					</div>
					<div class="annam-limo-payment__row">
						<span class="annam-limo-payment__k"><?php esc_html_e( 'Ngân hàng', 'generatepress_child' ); ?></span>
						<span class="annam-limo-payment__v"><?php echo esc_html( (string) $payment['bank_name'] ); ?></span>
					</div>
					<p class="annam-limo-payment__meta">
						<?php
						$meta_bits = array();
						if ( ! empty( $payment['bank_code'] ) ) {
							$meta_bits[] = sprintf(
								/* translators: %s: interbank code */
								__( 'Mã NH %s', 'generatepress_child' ),
								(string) $payment['bank_code']
							);
						}
						if ( ! empty( $payment['swift'] ) ) {
							$meta_bits[] = 'SWIFT ' . (string) $payment['swift'];
						}
						if ( ! empty( $payment['currency'] ) ) {
							$meta_bits[] = (string) $payment['currency'];
						}
						echo esc_html( implode( ' · ', $meta_bits ) );
						?>
					</p>
					<?php if ( ! empty( $payment['alert'] ) ) : ?>
						<p class="annam-limo-payment__alert" role="note"><?php echo esc_html( (string) $payment['alert'] ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $payment['qr_image'] ) ) : ?>
					<figure class="annam-limo-payment__qr">
						<img src="<?php echo esc_url( (string) $payment['qr_image'] ); ?>" alt="<?php esc_attr_e( 'Mã QR chuyển khoản CTCP AN NAM DISCOVERY', 'generatepress_child' ); ?>" width="120" height="120" loading="lazy" decoding="async" />
					</figure>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['why'] ) ) : ?>
	<section class="annam-limo-section annam-limo-section--why annam-limo-section--band-orange" id="uu-diem">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php echo esc_html( ! empty( $config['why_title'] ) ? $config['why_title'] : __( 'Vì Sao Chọn Limousine 11 Chỗ?', 'generatepress_child' ) ); ?></h2>
			<p class="annam-limo-section__lead"><?php echo esc_html( ! empty( $config['why_lead'] ) ? $config['why_lead'] : __( 'Limousine 11 chỗ, lịch cố định, đón trả gần — giữ chỗ nhanh qua form hoặc Zalo.', 'generatepress_child' ) ); ?></p>
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

	<?php
	$team_cfg   = isset( $config['team'] ) && is_array( $config['team'] ) ? $config['team'] : array();
	$team_slots = ( ! empty( $secs['team'] ) && ! empty( $team_cfg['slots'] ) && is_array( $team_cfg['slots'] ) )
		? $team_cfg['slots']
		: array();
	?>
	<?php if ( ! empty( $secs['team'] ) && ! empty( $team_slots ) ) : ?>
	<section class="annam-limo-section annam-limo-section--team annam-limo-section--band-teal" id="doi-ngu">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php echo esc_html( ! empty( $team_cfg['title'] ) ? $team_cfg['title'] : __( 'Đội ngũ An Nam Discovery', 'generatepress_child' ) ); ?></h2>
			<?php if ( ! empty( $team_cfg['lead'] ) ) : ?>
				<p class="annam-limo-section__lead"><?php echo esc_html( $team_cfg['lead'] ); ?></p>
			<?php endif; ?>
			<ul class="annam-limo-team-grid" data-annam-limo-team-gallery>
				<?php
				$team_index = 0;
				foreach ( $team_slots as $team_slot ) :
					$team_url = function_exists( 'annam_limo_landing_image_url' ) ? annam_limo_landing_image_url( (string) $team_slot ) : '';
					if ( '' === $team_url ) {
						continue;
					}
					?>
					<li class="annam-limo-team-grid__item">
						<button
							type="button"
							class="annam-limo-team-grid__btn"
							data-annam-team-open="<?php echo esc_attr( (string) $team_index ); ?>"
							aria-label="<?php esc_attr_e( 'Xem ảnh đội ngũ', 'generatepress_child' ); ?>"
						>
							<?php
							if ( function_exists( 'annam_limo_landing_print_image' ) ) {
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo annam_limo_landing_print_image(
									(string) $team_slot,
									array(
										'alt'     => __( 'Đội ngũ An Nam Discovery', 'generatepress_child' ),
										'loading' => 'lazy',
										'class'   => 'annam-limo-team-grid__img',
									)
								);
							}
							?>
						</button>
					</li>
					<?php
					$team_index++;
				endforeach;
				?>
			</ul>
		</div>
	</section>
	<?php endif; ?>

	<?php
	$cabin_upsell = isset( $config['cabin_upsell'] ) && is_array( $config['cabin_upsell'] ) ? $config['cabin_upsell'] : array();
	$cabin_cards  = ( ! empty( $secs['cabin_upsell'] ) && function_exists( 'annam_limo_landing_get_cabin_upsell_cards' ) )
		? annam_limo_landing_get_cabin_upsell_cards()
		: array();
	$cabin_base   = function_exists( 'annam_limo_landing_get_cabin_upsell_base_url' )
		? annam_limo_landing_get_cabin_upsell_base_url()
		: '';
	?>
	<?php if ( ! empty( $secs['cabin_upsell'] ) && ! empty( $cabin_cards ) ) : ?>
	<section class="annam-limo-section annam-limo-section--cabin-upsell annam-limo-section--band-teal" id="cabin-giuong-nam">
		<div class="annam-limo-container">
			<h2 class="annam-limo-section__title"><?php echo esc_html( ! empty( $cabin_upsell['title'] ) ? $cabin_upsell['title'] : __( 'Muốn nghỉ giường nằm? Chọn cabin VIP', 'generatepress_child' ) ); ?></h2>
			<?php if ( ! empty( $cabin_upsell['lead'] ) ) : ?>
				<p class="annam-limo-section__lead"><?php echo esc_html( $cabin_upsell['lead'] ); ?></p>
			<?php endif; ?>
			<div class="annam-limo-cabin-grid">
				<?php foreach ( $cabin_cards as $cabin ) :
					$cabin_type = isset( $cabin['type'] ) ? (string) $cabin['type'] : '';
					$cabin_name = isset( $cabin['name'] ) ? (string) $cabin['name'] : '';
					$cabin_href = function_exists( 'annam_limo_landing_get_cabin_upsell_link' )
						? annam_limo_landing_get_cabin_upsell_link( $cabin_type )
						: $cabin_base;
					$featured   = ! empty( $cabin['featured'] );
					?>
					<article class="annam-limo-cabin-card<?php echo $featured ? ' annam-limo-cabin-card--featured' : ''; ?>">
						<?php if ( ! empty( $cabin['tag'] ) ) : ?>
							<span class="annam-limo-cabin-card__tag"><?php echo esc_html( (string) $cabin['tag'] ); ?></span>
						<?php endif; ?>
						<div class="annam-limo-cabin-card__media">
							<?php
							$cabin_img_key = ! empty( $cabin['image'] ) ? (string) $cabin['image'] : '';
							if ( $cabin_img_key && function_exists( 'annam_cabin_landing_print_image' ) ) {
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image.
								echo annam_cabin_landing_print_image(
									$cabin_img_key,
									array(
										'alt'     => $cabin_name,
										'width'   => '400',
										'height'  => '280',
										'loading' => 'lazy',
									)
								);
							} elseif ( ! empty( $cabin['image_url'] ) ) {
								?>
								<img src="<?php echo esc_url( (string) $cabin['image_url'] ); ?>" alt="<?php echo esc_attr( $cabin_name ); ?>" width="400" height="280" loading="lazy" decoding="async" />
							<?php } ?>
						</div>
						<div class="annam-limo-cabin-card__body">
							<h3 class="annam-limo-cabin-card__title"><?php echo esc_html( $cabin_name ); ?></h3>
							<?php if ( ! empty( $cabin['price_from'] ) ) : ?>
								<p class="annam-limo-cabin-card__price"><?php printf( esc_html__( 'Từ %s', 'generatepress_child' ), esc_html( (string) $cabin['price_from'] ) ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $cabin['description'] ) ) : ?>
								<p class="annam-limo-cabin-card__desc"><?php echo esc_html( (string) $cabin['description'] ); ?></p>
							<?php endif; ?>
							<a class="annam-limo-btn annam-limo-btn--outline annam-limo-btn--block" href="<?php echo esc_url( $cabin_href ); ?>" data-track="limo_cabin_upsell"><?php printf( esc_html__( 'Chọn %s', 'generatepress_child' ), esc_html( $cabin_name ) ); ?></a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
			<?php if ( '' !== $cabin_base ) : ?>
				<p class="annam-limo-section__cta-row annam-limo-cabin-upsell__footer">
					<a class="annam-limo-cabin-upsell__more" href="<?php echo esc_url( $cabin_base . '#annam-cabin-booking' ); ?>"><?php echo esc_html( ! empty( $cabin_upsell['footer_label'] ) ? $cabin_upsell['footer_label'] : __( 'Xem đầy đủ lịch & điểm đón cabin', 'generatepress_child' ) ); ?></a>
				</p>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['steps'] ) ) : ?>
	<section class="annam-limo-section annam-limo-section--band-orange" id="dat-ve">
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
	<section class="annam-limo-section annam-limo-section--band-teal" id="faq">
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
				<a class="annam-limo-btn annam-limo-btn--outline" href="<?php echo esc_url( $cta['hotline2_tel'] ); ?>"><?php echo esc_html( $cta['hotline2_display'] ); ?></a>
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

	<div class="annam-limo-lightbox" id="annam-limo-lightbox" hidden role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Xem ảnh', 'generatepress_child' ); ?>">
		<button type="button" class="annam-limo-lightbox__ctrl annam-limo-lightbox__close" data-annam-lightbox-close aria-label="<?php esc_attr_e( 'Đóng', 'generatepress_child' ); ?>">
			<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
		</button>
		<button type="button" class="annam-limo-lightbox__ctrl annam-limo-lightbox__nav annam-limo-lightbox__nav--prev" data-annam-lightbox-prev aria-label="<?php esc_attr_e( 'Ảnh trước', 'generatepress_child' ); ?>">
			<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
		</button>
		<figure class="annam-limo-lightbox__figure">
			<img src="" alt="" class="annam-limo-lightbox__img" />
			<figcaption class="annam-limo-lightbox__cap"></figcaption>
		</figure>
		<button type="button" class="annam-limo-lightbox__ctrl annam-limo-lightbox__nav annam-limo-lightbox__nav--next" data-annam-lightbox-next aria-label="<?php esc_attr_e( 'Ảnh sau', 'generatepress_child' ); ?>">
			<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
		</button>
	</div>

	<?php
	$sticky_route   = function_exists( 'annam_limo_landing_current_route' ) ? annam_limo_landing_current_route() : 'sapa';
	$sticky_profile = function_exists( 'annam_limo_landing_get_route_profile' ) ? annam_limo_landing_get_route_profile( $sticky_route ? $sticky_route : 'sapa' ) : array();
	$whatsapp_url   = isset( $sticky_profile['whatsapp_url'] ) ? (string) $sticky_profile['whatsapp_url'] : 'https://wa.me/84942471111';
	$phone_first    = ! empty( $config['form']['phone_first'] );
	?>
	<div class="annam-limo-sticky" role="navigation" aria-label="<?php esc_attr_e( 'Thao tác nhanh', 'generatepress_child' ); ?>">
		<div class="annam-limo-sticky__inner">
			<a class="annam-limo-sticky__btn annam-limo-sticky__btn--call" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>">
				<span class="annam-limo-sticky__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.6a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.5-1.1a2 2 0 0 1 2.1-.4c.8.3 1.7.5 2.6.6a2 2 0 0 1 1.7 2z"/></svg>
				</span>
				<span class="annam-limo-sticky__label"><?php esc_html_e( 'Gọi', 'generatepress_child' ); ?></span>
			</a>
			<a class="annam-limo-sticky__btn annam-limo-sticky__btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener" data-track="limo_zalo_click">
				<span class="annam-limo-sticky__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z"/></svg>
				</span>
				<span class="annam-limo-sticky__label">Zalo</span>
			</a>
			<a class="annam-limo-sticky__btn annam-limo-sticky__btn--whatsapp" href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener" data-track="limo_whatsapp_click">
				<span class="annam-limo-sticky__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z"/></svg>
				</span>
				<span class="annam-limo-sticky__label">WhatsApp</span>
			</a>
			<?php if ( ! $phone_first ) : ?>
			<button type="button" class="annam-limo-sticky__btn annam-limo-sticky__btn--form" data-annam-scroll-form>
				<span class="annam-limo-sticky__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
				</span>
				<span class="annam-limo-sticky__label"><?php esc_html_e( 'Giữ chỗ', 'generatepress_child' ); ?></span>
			</button>
			<?php endif; ?>
		</div>
	</div>
</article>
