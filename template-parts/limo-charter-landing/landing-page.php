<?php
/**
 * Landing thuê nguyên xe Limousine Hà Nội ⇄ Sapa.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$config = annam_limo_charter_landing_get_config();
$cta    = annam_limo_charter_landing_get_cta();
$hero   = isset( $config['hero'] ) ? $config['hero'] : array();
$secs   = isset( $config['sections'] ) ? $config['sections'] : array();

$settings    = function_exists( 'annam_limo_charter_landing_get_settings' ) ? annam_limo_charter_landing_get_settings() : array();
$default_map = function_exists( 'annam_limo_charter_landing_default_maps_url' )
	? annam_limo_charter_landing_default_maps_url()
	: 'https://maps.app.goo.gl/6mQkPgdUMFhRfRnK7';
$map_query   = ! empty( $settings['map_address'] ) ? (string) $settings['map_address'] : $default_map;
$map_embed   = ( $map_query && function_exists( 'annam_contact_maps_embed_url' ) ) ? annam_contact_maps_embed_url( $map_query ) : '';
$maps_link   = ! empty( $cta['maps_url'] ) ? (string) $cta['maps_url'] : $default_map;
// Hiển thị địa chỉ chữ; short link Maps → dùng address CTA.
$map_address = ( preg_match( '#^https?://#i', $map_query ) )
	? ( isset( $cta['address'] ) ? (string) $cta['address'] : '' )
	: $map_query;
?>
<article class="annam-limo-charter-landing">

	<?php if ( ! empty( $secs['hero'] ) ) : ?>
	<section class="annam-limo-charter-hero">
		<div class="annam-limo-charter-container annam-limo-charter-hero__grid">
			<div class="annam-limo-charter-hero__content">
				<h1 class="annam-limo-charter-hero__title"><?php echo esc_html( $hero['title'] ); ?></h1>
				<p class="annam-limo-charter-hero__subtitle"><?php echo esc_html( $hero['subtitle'] ); ?></p>
				<?php if ( ! empty( $hero['badges'] ) ) : ?>
					<ul class="annam-limo-charter-hero__badges">
						<?php foreach ( $hero['badges'] as $badge ) : ?>
							<li class="annam-limo-charter-hero__badge"><?php echo esc_html( $badge ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( ! empty( $hero['note'] ) ) : ?>
					<p class="annam-limo-charter-hero__note"><?php echo esc_html( $hero['note'] ); ?></p>
				<?php endif; ?>
				<div class="annam-limo-charter-hero__ctas">
					<a class="annam-limo-charter-btn annam-limo-charter-btn--primary" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php esc_html_e( 'Gọi ngay', 'generatepress_child' ); ?></a>
					<a class="annam-limo-charter-btn annam-limo-charter-btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener">Zalo</a>
				</div>
				<figure class="annam-limo-charter-hero__media">
					<?php
					$hero_lb = function_exists( 'annam_limo_charter_landing_lightbox_index' )
						? annam_limo_charter_landing_lightbox_index( 'hero' )
						: -1;
					$hero_alt = isset( $hero['title'] ) ? (string) $hero['title'] : __( 'Limousine thuê nguyên xe', 'generatepress_child' );
					if ( $hero_lb >= 0 ) :
						?>
						<button type="button" class="annam-limo-charter-lb-trigger" data-annam-gallery-open="<?php echo esc_attr( (string) $hero_lb ); ?>" aria-label="<?php echo esc_attr( $hero_alt ); ?>">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo annam_limo_charter_landing_print_image(
								'hero',
								array(
									'alt'     => $hero_alt,
									'loading' => 'eager',
									'class'   => 'annam-limo-charter-hero__img',
									'width'   => '1200',
									'height'  => '800',
								)
							);
							?>
						</button>
					<?php else : ?>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo annam_limo_charter_landing_print_image(
							'hero',
							array(
								'alt'     => $hero_alt,
								'loading' => 'eager',
								'class'   => 'annam-limo-charter-hero__img',
								'width'   => '1200',
								'height'  => '800',
							)
						);
						?>
					<?php endif; ?>
				</figure>
			</div>
			<div class="annam-limo-charter-hero__form-col">
				<?php get_template_part( 'template-parts/limo-charter-landing/part', 'form' ); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['pricing'] ) && ! empty( $config['pricing']['rows'] ) ) : ?>
	<section class="annam-limo-charter-section" id="gia-thue">
		<div class="annam-limo-charter-container">
			<h2 class="annam-limo-charter-section__title"><?php esc_html_e( 'Bảng Giá Thuê Nguyên Xe', 'generatepress_child' ); ?></h2>
			<p class="annam-limo-charter-section__lead"><?php esc_html_e( 'Giá trọn gói 1 chiều · có tài xế · Hà Nội ⇄ Sapa.', 'generatepress_child' ); ?></p>
			<div class="annam-limo-charter-price-grid">
				<?php foreach ( $config['pricing']['rows'] as $row ) : ?>
					<article class="annam-limo-charter-price-card<?php echo ! empty( $row['badge'] ) ? ' annam-limo-charter-price-card--highlight' : ''; ?>">
						<?php if ( ! empty( $row['badge'] ) ) : ?>
							<span class="annam-limo-charter-price-card__badge"><?php echo esc_html( $row['badge'] ); ?></span>
						<?php endif; ?>
						<div class="annam-limo-charter-price-card__media">
							<?php
							$img_slot = isset( $row['image'] ) ? (string) $row['image'] : '';
							$img_alt  = isset( $row['label'] ) ? (string) $row['label'] : '';
							$img_lb   = ( $img_slot && function_exists( 'annam_limo_charter_landing_lightbox_index' ) )
								? annam_limo_charter_landing_lightbox_index( $img_slot )
								: -1;
							if ( $img_slot && $img_lb >= 0 ) :
								?>
								<button type="button" class="annam-limo-charter-lb-trigger" data-annam-gallery-open="<?php echo esc_attr( (string) $img_lb ); ?>" aria-label="<?php echo esc_attr( $img_alt ? $img_alt : __( 'Xem ảnh', 'generatepress_child' ) ); ?>">
									<?php
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									echo annam_limo_charter_landing_print_image(
										$img_slot,
										array(
											'alt'     => $img_alt,
											'loading' => 'lazy',
											'width'   => '1200',
											'height'  => '800',
										)
									);
									?>
								</button>
							<?php elseif ( $img_slot ) : ?>
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo annam_limo_charter_landing_print_image(
									$img_slot,
									array(
										'alt'     => $img_alt,
										'loading' => 'lazy',
										'width'   => '1200',
										'height'  => '800',
									)
								);
								?>
							<?php endif; ?>
						</div>
						<h3 class="annam-limo-charter-price-card__name"><?php echo esc_html( $row['label'] ); ?></h3>
						<p class="annam-limo-charter-price-card__price">
							<strong><?php echo esc_html( $row['price'] ); ?></strong>
							<span><?php echo esc_html( isset( $row['unit'] ) ? $row['unit'] : '' ); ?></span>
						</p>
						<?php if ( ! empty( $row['bullets'] ) ) : ?>
							<ul class="annam-limo-charter-price-card__bullets">
								<?php foreach ( $row['bullets'] as $bullet ) : ?>
									<li><?php echo esc_html( $bullet ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<button type="button" class="annam-limo-charter-btn annam-limo-charter-btn--primary annam-limo-charter-btn--block" data-annam-pick-vehicle="<?php echo esc_attr( $row['type'] ); ?>">
							<?php echo esc_html( isset( $row['cta'] ) ? $row['cta'] : __( 'Chọn xe', 'generatepress_child' ) ); ?>
						</button>
					</article>
				<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $config['pricing']['included'] ) ) : ?>
				<p class="annam-limo-charter-section__note"><?php echo esc_html( $config['pricing']['included'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $config['pricing']['extra'] ) ) : ?>
				<p class="annam-limo-charter-section__note"><?php echo esc_html( $config['pricing']['extra'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $config['pricing']['note'] ) ) : ?>
				<p class="annam-limo-charter-section__note annam-limo-charter-section__note--strong"><?php echo esc_html( $config['pricing']['note'] ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['trust'] ) && ! empty( $config['trust_strip'] ) ) : ?>
	<section class="annam-limo-charter-trust-strip" aria-label="<?php esc_attr_e( 'Cam kết nhanh', 'generatepress_child' ); ?>">
		<div class="annam-limo-charter-container">
			<ul class="annam-limo-charter-trust-strip__list">
				<?php foreach ( $config['trust_strip'] as $item ) :
					$label = is_array( $item ) ? ( isset( $item['label'] ) ? (string) $item['label'] : '' ) : (string) $item;
					$icon  = is_array( $item ) && isset( $item['icon'] ) ? (string) $item['icon'] : 'check';
					if ( '' === $label ) {
						continue;
					}
					?>
					<li class="annam-limo-charter-trust-strip__item">
						<span class="annam-limo-charter-trust-strip__icon" aria-hidden="true">
							<?php if ( 'driver' === $icon ) : ?>
								<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16h13l3-5H8l-2 5z"/><circle cx="7.5" cy="19" r="1.4"/><circle cx="16.5" cy="19" r="1.4"/><path d="M8 11V8h6"/><circle cx="18.5" cy="7.5" r="2.2"/><path d="M16.8 9.8c.4 1.2 1.5 2 2.7 2s2.3-.8 2.7-2"/></svg>
							<?php elseif ( 'pin' === $icon ) : ?>
								<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.4"/></svg>
							<?php elseif ( 'chat' === $icon ) : ?>
								<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z"/></svg>
							<?php else : ?>
								<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.2 2.2 4.8-5"/></svg>
							<?php endif; ?>
						</span>
						<span class="annam-limo-charter-trust-strip__label"><?php echo esc_html( $label ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['gallery'] ) && ! empty( $config['gallery'] ) ) : ?>
	<section class="annam-limo-charter-section annam-limo-charter-section--alt" id="anh-xe">
		<div class="annam-limo-charter-container">
			<h2 class="annam-limo-charter-section__title"><?php esc_html_e( 'Hình Ảnh Xe Limousine', 'generatepress_child' ); ?></h2>
			<p class="annam-limo-charter-section__lead"><?php esc_html_e( '9 chỗ & 11 chỗ — xem xe trước khi gọi.', 'generatepress_child' ); ?></p>
			<div class="annam-limo-charter-gallery" data-annam-limo-charter-gallery>
				<?php foreach ( $config['gallery'] as $i => $item ) : ?>
					<?php
					$slot = isset( $item['slot'] ) ? (string) $item['slot'] : '';
					$cap  = isset( $item['caption'] ) ? (string) $item['caption'] : '';
					if ( function_exists( 'annam_limo_charter_landing_get_image_caption' ) && $slot ) {
						$saved_cap = annam_limo_charter_landing_get_image_caption( $slot );
						if ( $saved_cap ) {
							$cap = $saved_cap;
						}
					}
					$lb_i = ( $slot && function_exists( 'annam_limo_charter_landing_lightbox_index' ) )
						? annam_limo_charter_landing_lightbox_index( $slot )
						: (int) $i;
					?>
					<figure class="annam-limo-charter-gallery__item" data-gallery-index="<?php echo esc_attr( (string) $lb_i ); ?>">
						<button type="button" class="annam-limo-charter-gallery__btn annam-limo-charter-lb-trigger" data-annam-gallery-open="<?php echo esc_attr( (string) max( 0, $lb_i ) ); ?>" aria-label="<?php echo esc_attr( $cap ? $cap : __( 'Xem ảnh', 'generatepress_child' ) ); ?>">
							<?php
							if ( $slot ) {
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo annam_limo_charter_landing_print_image(
									$slot,
									array(
										'alt'     => $cap,
										'loading' => 0 === $i ? 'eager' : 'lazy',
										'width'   => '1200',
										'height'  => '800',
									)
								);
							}
							?>
						</button>
						<?php if ( $cap ) : ?>
							<figcaption class="annam-limo-charter-gallery__caption"><?php echo esc_html( $cap ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['proof'] ) ) : ?>
	<section class="annam-limo-charter-section" id="uy-tin">
		<div class="annam-limo-charter-container">
			<h2 class="annam-limo-charter-section__title"><?php esc_html_e( 'Uy Tín An Nam Discovery', 'generatepress_child' ); ?></h2>
			<p class="annam-limo-charter-section__lead"><?php esc_html_e( 'Người thật · địa chỉ thật · hotline rõ ràng.', 'generatepress_child' ); ?></p>
			<div class="annam-limo-charter-proof-grid">
				<div class="annam-limo-charter-staff">
					<?php foreach ( array( 'staff-1', 'staff-2', 'staff-3', 'staff-4', 'staff-5', 'staff-6' ) as $staff_slot ) :
						$staff_cap = annam_limo_charter_landing_get_image_caption( $staff_slot );
						$staff_lb  = function_exists( 'annam_limo_charter_landing_lightbox_index' )
							? annam_limo_charter_landing_lightbox_index( $staff_slot )
							: -1;
						?>
						<figure class="annam-limo-charter-staff__item">
							<?php if ( $staff_lb >= 0 ) : ?>
								<button type="button" class="annam-limo-charter-lb-trigger" data-annam-gallery-open="<?php echo esc_attr( (string) $staff_lb ); ?>" aria-label="<?php echo esc_attr( $staff_cap ? $staff_cap : __( 'Xem ảnh', 'generatepress_child' ) ); ?>">
									<?php
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									echo annam_limo_charter_landing_print_image(
										$staff_slot,
										array(
											'alt'     => $staff_cap,
											'loading' => 'lazy',
											'width'   => '800',
											'height'  => '600',
										)
									);
									?>
								</button>
							<?php else : ?>
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo annam_limo_charter_landing_print_image(
									$staff_slot,
									array(
										'alt'     => $staff_cap,
										'loading' => 'lazy',
										'width'   => '800',
										'height'  => '600',
									)
								);
								?>
							<?php endif; ?>
						</figure>
					<?php endforeach; ?>
				</div>
				<div class="annam-limo-charter-map-block">
					<?php if ( $map_embed ) : ?>
						<iframe
							class="annam-limo-charter-map"
							src="<?php echo esc_url( $map_embed ); ?>"
							title="<?php echo esc_attr( isset( $cta['brand'] ) ? $cta['brand'] : 'An Nam Discovery' ); ?>"
							loading="lazy"
							referrerpolicy="no-referrer-when-downgrade"
							allowfullscreen
						></iframe>
					<?php endif; ?>
					<p class="annam-limo-charter-map-block__meta">
						<strong><?php echo esc_html( isset( $cta['brand'] ) ? $cta['brand'] : 'An Nam Discovery' ); ?></strong><br />
						<?php echo esc_html( $map_address ); ?><br />
						<a href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php echo esc_html( $cta['hotline_display'] ); ?></a>
						·
						<a href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener">Zalo</a>
						<?php if ( $maps_link ) : ?>
							· <a href="<?php echo esc_url( $maps_link ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Mở Google Maps', 'generatepress_child' ); ?></a>
						<?php endif; ?>
					</p>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php
	// Trustindex ngay dưới Uy Tín — chỉ shortcode, không tiêu đề.
	if ( ! empty( $secs['reviews'] ) || ! empty( $secs['proof'] ) ) :
		$ti_shortcode = (string) apply_filters( 'annam_limo_charter_trustindex_shortcode', '[trustindex no-registration=google]' );
		$ti_html      = '' !== trim( $ti_shortcode ) ? do_shortcode( $ti_shortcode ) : '';
		if ( $ti_html && trim( $ti_html ) !== trim( $ti_shortcode ) ) :
			?>
	<div class="annam-limo-charter-trustindex" id="danh-gia">
		<div class="annam-limo-charter-container">
			<?php echo $ti_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trustindex widget HTML. ?>
		</div>
	</div>
			<?php
		endif;
	endif;
	?>

	<?php if ( ! empty( $secs['why'] ) && ! empty( $config['why'] ) ) : ?>
	<section class="annam-limo-charter-section annam-limo-charter-section--why" id="vi-sao">
		<div class="annam-limo-charter-container">
			<header class="annam-limo-charter-why-head">
				<h2 class="annam-limo-charter-section__title"><?php esc_html_e( 'Vì Sao Thuê Nguyên Xe?', 'generatepress_child' ); ?></h2>
				<p class="annam-limo-charter-section__lead"><?php esc_html_e( 'Bốn lý do khách chọn thuê riêng limo HN–Sapa thay vì vé ghế.', 'generatepress_child' ); ?></p>
			</header>
			<div class="annam-limo-charter-why-grid">
				<?php foreach ( $config['why'] as $i => $card ) :
					$icon = isset( $card['icon'] ) ? (string) $card['icon'] : 'van';
					$tone = 0 === ( $i % 2 ) ? 'teal' : 'orange';
					?>
					<article class="annam-limo-charter-why-card annam-limo-charter-why-card--<?php echo esc_attr( $tone ); ?>">
						<span class="annam-limo-charter-why-card__index" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="annam-limo-charter-why-card__icon" aria-hidden="true">
							<?php if ( 'clock' === $icon ) : ?>
								<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
							<?php elseif ( 'pin' === $icon ) : ?>
								<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
							<?php elseif ( 'tag' === $icon ) : ?>
								<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20.6 13.4 12 22l-9-9V4h9l8.6 9.4z"/><circle cx="7.5" cy="7.5" r="1.2" fill="currentColor" stroke="none"/></svg>
							<?php else : ?>
								<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16h13l3-5H8l-2 5z"/><path d="M5 16v2a1 1 0 0 0 1 1h1"/><path d="M14 16v2a1 1 0 0 0 1 1h1"/><circle cx="7.5" cy="19" r="1.5"/><circle cx="16.5" cy="19" r="1.5"/><path d="M8 11V8h6"/><path d="M5 11l1.5-3H9"/></svg>
							<?php endif; ?>
						</span>
						<div class="annam-limo-charter-why-card__body">
							<h3 class="annam-limo-charter-why-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
							<p class="annam-limo-charter-why-card__text"><?php echo esc_html( $card['text'] ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['steps'] ) && ! empty( $config['steps'] ) ) : ?>
	<section class="annam-limo-charter-section" id="quy-trinh">
		<div class="annam-limo-charter-container">
			<h2 class="annam-limo-charter-section__title"><?php esc_html_e( 'Quy Trình 3 Bước', 'generatepress_child' ); ?></h2>
			<ol class="annam-limo-charter-steps">
				<?php foreach ( $config['steps'] as $i => $step ) : ?>
					<li>
						<span class="annam-limo-charter-steps__num"><?php echo esc_html( (string) ( $i + 1 ) ); ?></span>
						<div>
							<h3><?php echo esc_html( $step['title'] ); ?></h3>
							<p><?php echo esc_html( $step['text'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['faq'] ) && ! empty( $config['faq'] ) ) : ?>
	<section class="annam-limo-charter-section" id="faq">
		<div class="annam-limo-charter-container">
			<h2 class="annam-limo-charter-section__title"><?php esc_html_e( 'Câu Hỏi Thường Gặp', 'generatepress_child' ); ?></h2>
			<div class="annam-limo-charter-faq">
				<?php foreach ( $config['faq'] as $item ) : ?>
					<details class="annam-limo-charter-faq__item">
						<summary><?php echo esc_html( $item['question'] ); ?></summary>
						<p><?php echo esc_html( $item['answer'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['compare'] ) && ! empty( $config['compare'] ) ) : ?>
	<section class="annam-limo-charter-section annam-limo-charter-section--compare" id="khac-ve-ghe">
		<div class="annam-limo-charter-container">
			<div class="annam-limo-charter-compare">
				<h2 class="annam-limo-charter-compare__title"><?php echo esc_html( $config['compare']['title'] ); ?></h2>
				<p><?php echo esc_html( $config['compare']['text'] ); ?></p>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['cross_sell'] ) && ! empty( $config['cross_sell'] ) ) : ?>
	<section class="annam-limo-charter-section annam-limo-charter-section--alt" id="goi-y-khac">
		<div class="annam-limo-charter-container">
			<h2 class="annam-limo-charter-section__title"><?php esc_html_e( 'Gợi Ý Lựa Chọn Khác', 'generatepress_child' ); ?></h2>
			<p class="annam-limo-charter-section__lead"><?php esc_html_e( 'Cần vé ghế, cabin hoặc tour trọn gói? Xem trang riêng hoặc gọi ngay.', 'generatepress_child' ); ?></p>
			<div class="annam-limo-charter-cross-grid">
				<?php foreach ( $config['cross_sell'] as $card ) : ?>
					<article class="annam-limo-charter-cross-card">
						<div class="annam-limo-charter-cross-card__media">
							<?php
							$slot = isset( $card['slot'] ) ? (string) $card['slot'] : '';
							if ( $slot ) {
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo annam_limo_charter_landing_print_image(
									$slot,
									array(
										'alt'     => isset( $card['title'] ) ? (string) $card['title'] : '',
										'loading' => 'lazy',
										'width'   => '800',
										'height'  => '600',
									)
								);
							}
							?>
						</div>
						<div class="annam-limo-charter-cross-card__body">
							<h3><?php echo esc_html( $card['title'] ); ?></h3>
							<p class="annam-limo-charter-cross-card__line"><?php echo esc_html( $card['line'] ); ?></p>
							<p class="annam-limo-charter-cross-card__price"><?php echo esc_html( $card['price'] ); ?></p>
							<div class="annam-limo-charter-cross-card__ctas">
								<a class="annam-limo-charter-btn annam-limo-charter-btn--primary" href="<?php echo esc_url( $card['url'] ); ?>" data-track="<?php echo esc_attr( isset( $card['track'] ) ? $card['track'] : '' ); ?>">
									<?php echo esc_html( $card['cta'] ); ?>
								</a>
								<a class="annam-limo-charter-btn annam-limo-charter-btn--outline" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php esc_html_e( 'Gọi', 'generatepress_child' ); ?></a>
								<a class="annam-limo-charter-btn annam-limo-charter-btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener">Zalo</a>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( ! empty( $secs['related_tours'] ) ) : ?>
		<?php get_template_part( 'template-parts/limo-charter-landing/part', 'woo-tours' ); ?>
	<?php endif; ?>

	<?php if ( ! empty( $secs['final_cta'] ) ) : ?>
	<section class="annam-limo-charter-final" id="cta-cuoi">
		<div class="annam-limo-charter-container annam-limo-charter-final__inner">
			<h2><?php echo esc_html( $config['final_cta']['title'] ); ?></h2>
			<p><?php echo esc_html( $config['final_cta']['subtitle'] ); ?></p>
			<div class="annam-limo-charter-final__ctas">
				<a class="annam-limo-charter-btn annam-limo-charter-btn--primary" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php esc_html_e( 'Gọi ngay', 'generatepress_child' ); ?></a>
				<a class="annam-limo-charter-btn annam-limo-charter-btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener">Zalo</a>
				<button type="button" class="annam-limo-charter-btn annam-limo-charter-btn--outline" data-annam-scroll-form><?php esc_html_e( 'Để lại SĐT', 'generatepress_child' ); ?></button>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<div class="annam-limo-charter-lightbox" id="annam-limo-charter-lightbox" hidden role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Thư viện ảnh', 'generatepress_child' ); ?>">
		<button type="button" class="annam-limo-charter-lightbox__ctrl annam-limo-charter-lightbox__close" data-annam-lightbox-close aria-label="<?php esc_attr_e( 'Đóng', 'generatepress_child' ); ?>">×</button>
		<button type="button" class="annam-limo-charter-lightbox__ctrl annam-limo-charter-lightbox__nav annam-limo-charter-lightbox__nav--prev" data-annam-lightbox-prev aria-label="<?php esc_attr_e( 'Ảnh trước', 'generatepress_child' ); ?>">‹</button>
		<figure class="annam-limo-charter-lightbox__figure">
			<img src="" alt="" class="annam-limo-charter-lightbox__img" />
			<figcaption class="annam-limo-charter-lightbox__cap"></figcaption>
			<p class="annam-limo-charter-lightbox__count" data-annam-lightbox-count hidden></p>
		</figure>
		<button type="button" class="annam-limo-charter-lightbox__ctrl annam-limo-charter-lightbox__nav annam-limo-charter-lightbox__nav--next" data-annam-lightbox-next aria-label="<?php esc_attr_e( 'Ảnh sau', 'generatepress_child' ); ?>">›</button>
	</div>

	<nav class="annam-limo-charter-sticky" aria-label="<?php esc_attr_e( 'Thao tác nhanh', 'generatepress_child' ); ?>">
		<div class="annam-limo-charter-sticky__inner">
			<a class="annam-limo-charter-sticky__btn annam-limo-charter-sticky__btn--call" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php esc_html_e( 'Gọi', 'generatepress_child' ); ?></a>
			<a class="annam-limo-charter-sticky__btn annam-limo-charter-sticky__btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener">Zalo</a>
			<button type="button" class="annam-limo-charter-sticky__btn annam-limo-charter-sticky__btn--form" data-annam-scroll-form><?php esc_html_e( 'Nhận báo giá', 'generatepress_child' ); ?></button>
		</div>
	</nav>
</article>
