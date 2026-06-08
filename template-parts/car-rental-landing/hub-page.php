<?php
/**
 * Hub thuê xe hợp đồng — điều hướng loại xe.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$config   = annam_car_rental_get_landing_config( 'hub' );
$cta      = annam_car_rental_get_cta();
$hero     = isset( $config['hero'] ) ? $config['hero'] : array();
$vehicles = isset( $config['vehicles'] ) ? $config['vehicles'] : array();
$note     = isset( $config['pricing_note'] ) ? $config['pricing_note'] : array();
$cta_final = isset( $config['cta_final'] ) ? $config['cta_final'] : array();

$page_id = get_the_ID();
$hero_bg = $page_id ? annam_car_rental_get_landing_image_url( $page_id, 'hero', 'large' ) : '';
if ( ! $hero_bg && $page_id ) {
	$hero_bg = (string) get_the_post_thumbnail_url( $page_id, 'large' );
}
$why_img = $page_id ? annam_car_rental_get_landing_image_url( $page_id, 'why', 'large' ) : '';
if ( ! $why_img ) {
	$why_img = $hero_bg;
}

$hero_classes = 'annam-cr-hero annam-cr-hero--hub';
if ( ! $hero_bg ) {
	$hero_classes .= ' annam-cr-hero--placeholder';
}
?>
<article class="annam-cr-landing annam-cr-landing--hub">
	<section
		class="<?php echo esc_attr( $hero_classes ); ?>"
		<?php echo $hero_bg ? ' style="--annam-cr-hero-bg:url(' . esc_url( $hero_bg ) . ')"' : ''; ?>
	>
		<div class="annam-cr-hero__overlay" aria-hidden="true"></div>
		<div class="annam-cr-container annam-cr-hero__grid">
			<div class="annam-cr-hero__content">
				<?php if ( ! empty( $hero['eyebrow'] ) ) : ?>
					<p class="annam-cr-hero__eyebrow"><?php echo esc_html( $hero['eyebrow'] ); ?></p>
				<?php endif; ?>
				<h1 class="annam-cr-hero__title">
					<?php echo esc_html( $hero['title'] ?? '' ); ?>
					<?php if ( ! empty( $hero['title_accent'] ) ) : ?>
						<span class="annam-cr-hero__title-accent"><?php echo esc_html( $hero['title_accent'] ); ?></span>
					<?php endif; ?>
				</h1>
				<?php if ( ! empty( $hero['subtitle'] ) ) : ?>
					<p class="annam-cr-hero__subtitle"><?php echo esc_html( $hero['subtitle'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $hero['price_from'] ) ) : ?>
					<p class="annam-cr-hero__price">
						<strong><?php echo esc_html( $hero['price_from'] ); ?></strong>
						<?php if ( ! empty( $hero['price_unit'] ) ) : ?>
							<span><?php echo esc_html( $hero['price_unit'] ); ?></span>
						<?php endif; ?>
					</p>
				<?php endif; ?>
				<?php if ( ! empty( $config['hero_badges'] ) ) : ?>
					<ul class="annam-cr-hero__badges">
						<?php
						$hero_badge_count = count( $config['hero_badges'] );
						foreach ( $config['hero_badges'] as $bi => $badge ) :
							$badge_icon  = is_array( $badge ) && ! empty( $badge['icon'] ) ? (string) $badge['icon'] : 'check_circle';
							$badge_label = is_array( $badge ) ? (string) ( $badge['label'] ?? '' ) : (string) $badge;
							$badge_class = 'annam-cr-hero__badge';
							if ( 1 === $hero_badge_count % 2 && (int) $bi === $hero_badge_count - 1 ) {
								$badge_class .= ' annam-cr-hero__badge--full';
							}
							?>
							<li class="<?php echo esc_attr( $badge_class ); ?>">
								<?php echo annam_car_rental_icon( $badge_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span><?php echo esc_html( $badge_label ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<div class="annam-cr-hero__ctas">
					<button type="button" class="annam-cr-btn annam-cr-btn--cta" data-annam-cr-scroll="#loai-xe">
						<?php esc_html_e( 'Chọn loại xe', 'generatepress_child' ); ?>
					</button>
					<a class="annam-cr-btn annam-cr-btn--light" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>">
						<?php echo annam_car_rental_icon( 'call' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php esc_html_e( 'Gọi tư vấn', 'generatepress_child' ); ?>
					</a>
					<a class="annam-cr-btn annam-cr-btn--zalo" href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener noreferrer">
						<?php echo annam_car_rental_icon( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php esc_html_e( 'Chat Zalo', 'generatepress_child' ); ?>
					</a>
				</div>
			</div>
			<div class="annam-cr-hero__form-col">
				<?php get_template_part( 'template-parts/car-rental-landing/part', 'form', array( 'variant' => 'hero' ) ); ?>
			</div>
		</div>
	</section>

	<section class="annam-cr-section" id="loai-xe">
		<div class="annam-cr-container">
			<header class="annam-cr-section__head annam-cr-section__head--center">
				<h2 class="annam-cr-section__title"><?php esc_html_e( 'Chọn loại xe phù hợp', 'generatepress_child' ); ?></h2>
				<p class="annam-cr-section__desc"><?php esc_html_e( 'Giá tham khảo 2 chiều, từ Hà Nội — chi tiết theo từng tuyến trên trang loại xe', 'generatepress_child' ); ?></p>
				<span class="annam-cr-section__accent" aria-hidden="true"></span>
			</header>
			<div class="annam-cr-vehicle-grid annam-cr-vehicle-grid--hub">
				<?php foreach ( $vehicles as $vehicle ) : ?>
					<article class="annam-cr-vehicle-card annam-cr-vehicle-card--hub">
						<?php if ( ! empty( $vehicle['badge'] ) ) : ?>
							<span class="annam-cr-vehicle-card__badge annam-cr-vehicle-card__badge--vip"><?php echo esc_html( $vehicle['badge'] ); ?></span>
						<?php endif; ?>
						<div class="annam-cr-vehicle-card__media">
							<?php if ( ! empty( $vehicle['image_url'] ) ) : ?>
								<img class="annam-cr-vehicle-card__img" src="<?php echo esc_url( $vehicle['image_url'] ); ?>" alt="<?php echo esc_attr( $vehicle['label'] ?? '' ); ?>" width="640" height="480" loading="lazy" decoding="async" />
							<?php else : ?>
								<div class="annam-cr-vehicle-card__placeholder" aria-hidden="true">
									<span class="annam-cr-vehicle-card__placeholder-label"><?php echo esc_html( $vehicle['label'] ?? '' ); ?></span>
								</div>
							<?php endif; ?>
						</div>
						<div class="annam-cr-vehicle-card__body">
							<span class="annam-cr-vehicle-card__icon"><?php echo annam_car_rental_icon( $vehicle['icon'] ?? 'directions_bus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<h3 class="annam-cr-vehicle-card__title"><?php echo esc_html( $vehicle['label'] ?? '' ); ?></h3>
							<p class="annam-cr-vehicle-card__pax"><?php echo esc_html( $vehicle['passengers'] ?? '' ); ?></p>
							<p class="annam-cr-vehicle-card__price"><?php echo esc_html( $vehicle['price_label'] ?? '' ); ?></p>
							<a class="annam-cr-btn annam-cr-btn--primary annam-cr-btn--block" href="<?php echo esc_url( $vehicle['url'] ?? '#' ); ?>"><?php esc_html_e( 'Xem chi tiết', 'generatepress_child' ); ?></a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="annam-cr-section annam-cr-section--muted" id="bang-gia">
		<div class="annam-cr-container">
			<header class="annam-cr-section__head annam-cr-section__head--center">
				<h2 class="annam-cr-section__title"><?php esc_html_e( 'Bảng giá tham khảo (2 chiều)', 'generatepress_child' ); ?></h2>
				<p class="annam-cr-section__desc"><?php esc_html_e( '19 tuyến từ Hà Nội — xem đầy đủ trên trang từng loại xe', 'generatepress_child' ); ?></p>
			</header>
			<?php get_template_part( 'template-parts/car-rental-landing/part-hub-pricing', 'table' ); ?>
			<?php if ( ! empty( $note ) ) : ?>
				<div class="annam-cr-pricing-note annam-cr-pricing-note--hub">
					<p class="annam-cr-pricing-note__line">
						<strong class="annam-cr-pricing-note__in"><?php esc_html_e( 'Báo giá đã bao gồm:', 'generatepress_child' ); ?></strong>
						<?php echo esc_html( $note['included'] ?? '' ); ?>
					</p>
					<p class="annam-cr-pricing-note__line">
						<strong class="annam-cr-pricing-note__out"><?php esc_html_e( 'Báo giá chưa bao gồm:', 'generatepress_child' ); ?></strong>
						<?php echo esc_html( $note['excluded'] ?? '' ); ?>
					</p>
					<?php if ( ! empty( $note['disclaimer'] ) ) : ?>
						<p class="annam-cr-pricing-note__disclaimer"><?php echo esc_html( $note['disclaimer'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="annam-cr-section">
		<div class="annam-cr-container annam-cr-why">
			<div class="annam-cr-why__media">
				<?php if ( $why_img ) : ?>
					<img src="<?php echo esc_url( $why_img ); ?>" alt="" width="640" height="400" loading="lazy" decoding="async" class="annam-cr-why__img" />
				<?php else : ?>
					<div class="annam-cr-why__placeholder" aria-hidden="true">
						<span class="annam-cr-why__placeholder-label"><?php esc_html_e( 'Ảnh đội xe', 'generatepress_child' ); ?></span>
					</div>
				<?php endif; ?>
			</div>
			<div class="annam-cr-why__content">
				<h2 class="annam-cr-section__title"><?php esc_html_e( 'Vì sao chọn An Nam Discovery?', 'generatepress_child' ); ?></h2>
				<ul class="annam-cr-why__list">
					<?php foreach ( (array) ( $config['why'] ?? array() ) as $why ) : ?>
						<li class="annam-cr-why__item">
							<?php echo annam_car_rental_icon( 'check_circle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<div>
								<h3 class="annam-cr-why__item-title"><?php echo esc_html( $why['title'] ?? '' ); ?></h3>
								<p><?php echo esc_html( $why['text'] ?? '' ); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</section>

	<section class="annam-cr-section annam-cr-section--muted" id="quy-trinh">
		<div class="annam-cr-container">
			<header class="annam-cr-section__head annam-cr-section__head--center">
				<h2 class="annam-cr-section__title"><?php echo esc_html( annam_car_rental_get_steps_section_title() ); ?></h2>
			</header>
			<ol class="annam-cr-steps">
				<?php foreach ( (array) ( $config['steps'] ?? array() ) as $si => $step ) : ?>
					<li class="annam-cr-step<?php echo ! empty( $step['featured'] ) ? ' annam-cr-step--featured' : ''; ?>">
						<span class="annam-cr-step__num"><?php echo esc_html( sprintf( '%02d', $si + 1 ) ); ?></span>
						<h3 class="annam-cr-step__title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
						<p class="annam-cr-step__text"><?php echo esc_html( $step['text'] ?? '' ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<?php if ( ! empty( $config['faq'] ) ) : ?>
	<section class="annam-cr-section" id="faq">
		<div class="annam-cr-container annam-cr-container--narrow">
			<header class="annam-cr-section__head annam-cr-section__head--center">
				<h2 class="annam-cr-section__title"><?php esc_html_e( 'Câu hỏi thường gặp', 'generatepress_child' ); ?></h2>
			</header>
			<div class="annam-cr-faq">
				<?php foreach ( $config['faq'] as $faq ) : ?>
					<details class="annam-cr-faq__item">
						<summary class="annam-cr-faq__q"><?php echo esc_html( $faq['question'] ?? '' ); ?></summary>
						<div class="annam-cr-faq__a"><p><?php echo esc_html( $faq['answer'] ?? '' ); ?></p></div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php
	$cta_final_bg  = $page_id ? annam_car_rental_get_landing_image_url( $page_id, 'cta_final', 'large' ) : '';
	$cta_final_cls = 'annam-cr-cta-final';
	if ( $cta_final_bg ) {
		$cta_final_cls .= ' annam-cr-cta-final--has-bg';
	}
	?>
	<section
		class="<?php echo esc_attr( $cta_final_cls ); ?>"
		<?php echo $cta_final_bg ? ' style="--annam-cr-cta-final-bg:url(' . esc_url( $cta_final_bg ) . ')"' : ''; ?>
	>
		<div class="annam-cr-container annam-cr-cta-final__inner">
			<h2 class="annam-cr-cta-final__title"><?php echo esc_html( $cta_final['title'] ?? '' ); ?></h2>
			<p class="annam-cr-cta-final__desc"><?php echo esc_html( $cta_final['desc'] ?? '' ); ?></p>
			<div class="annam-cr-cta-final__form-card">
				<?php get_template_part( 'template-parts/car-rental-landing/part', 'form', array( 'variant' => 'final' ) ); ?>
				<div class="annam-cr-cta-final__contacts">
					<a href="<?php echo esc_url( $cta['hotline_tel'] ); ?>"><?php echo annam_car_rental_icon( 'call' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( $cta['hotline_display'] ); ?></a>
					<a href="<?php echo esc_url( $cta['zalo_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="annam-cr-cta-final__zalo"><?php echo annam_car_rental_icon( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php esc_html_e( 'Chat Zalo', 'generatepress_child' ); ?></a>
				</div>
			</div>
		</div>
	</section>
</article>
