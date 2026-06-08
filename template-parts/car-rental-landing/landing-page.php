<?php
/**
 * Landing thuê xe theo loại (7 / 16 / limo / 29 / 45 chỗ).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$config = annam_car_rental_get_landing_config();
$cta    = annam_car_rental_get_cta();
$hero   = isset( $config['hero'] ) ? $config['hero'] : array();
$routes = isset( $config['routes'] ) ? $config['routes'] : array();
$note   = isset( $config['pricing_note'] ) ? $config['pricing_note'] : array();

$page_id = get_the_ID();
$hero_bg = $page_id ? annam_car_rental_get_landing_image_url( $page_id, 'hero', 'large' ) : '';
if ( ! $hero_bg && $page_id ) {
	$hero_bg = (string) get_the_post_thumbnail_url( $page_id, 'large' );
}
$why_img = $page_id ? annam_car_rental_get_landing_image_url( $page_id, 'why', 'large' ) : '';
if ( ! $why_img ) {
	$why_img = $hero_bg;
}
?>
<article class="annam-cr-landing">
	<section class="annam-cr-hero"<?php echo $hero_bg ? ' style="--annam-cr-hero-bg:url(' . esc_url( $hero_bg ) . ')"' : ''; ?>>
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
					<a class="annam-cr-btn annam-cr-btn--light" href="<?php echo esc_url( $cta['hotline_tel'] ); ?>">
						<?php echo annam_car_rental_icon( 'call' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php esc_html_e( 'Gọi ngay', 'generatepress_child' ); ?>
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

	<?php
	get_template_part(
		'template-parts/car-rental-landing/part',
		'pricing-table',
		array(
			'routes'         => $routes,
			'vehicle_label'  => $config['vehicle_label'] ?? '',
			'note'           => $note,
			'pricing_title'  => $config['pricing_title'] ?? '',
		)
	);
	?>

	<section class="annam-cr-section annam-cr-section--dark">
		<div class="annam-cr-container">
			<header class="annam-cr-section__head annam-cr-section__head--center">
				<h2 class="annam-cr-section__title"><?php echo esc_html( $config['use_cases_title'] ?? '' ); ?></h2>
				<span class="annam-cr-section__accent" aria-hidden="true"></span>
			</header>
			<div class="annam-cr-use-grid">
				<?php foreach ( (array) ( $config['use_cases'] ?? array() ) as $item ) : ?>
					<article class="annam-cr-use-card">
						<span class="annam-cr-use-card__icon"><?php echo annam_car_rental_icon( $item['icon'] ?? 'groups' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<div>
							<h3 class="annam-cr-use-card__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
							<p class="annam-cr-use-card__text"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $config['featured'] ) ) : ?>
		<?php
		$featured_journeys = (array) $config['featured'];
		$featured_count    = count( $featured_journeys );
		$featured_slider   = $featured_count > 3;
		$journey_grid_cls  = 'annam-cr-journey-grid';
		if ( $featured_slider ) {
			$journey_grid_cls .= ' annam-cr-journey-grid--slider';
		} else {
			$journey_grid_cls .= ' annam-cr-journey-grid--cols-' . max( 1, min( 3, $featured_count ) );
		}
		?>
	<section class="annam-cr-section">
		<div class="annam-cr-container">
			<header class="annam-cr-section__head annam-cr-section__head--center">
				<h2 class="annam-cr-section__title"><?php esc_html_e( 'Hành trình phổ biến', 'generatepress_child' ); ?></h2>
				<p class="annam-cr-section__desc"><?php esc_html_e( 'Gợi ý các tuyến đường khách hàng lựa chọn nhiều nhất', 'generatepress_child' ); ?></p>
			</header>
			<div class="<?php echo esc_attr( $journey_grid_cls ); ?>"<?php echo $featured_slider ? ' data-annam-cr-journey-slider' : ''; ?>>
				<?php foreach ( $featured_journeys as $journey ) : ?>
					<?php
					$route_label   = (string) ( $journey['title'] ?? '' );
					$label_display = (string) ( $journey['title_display'] ?? annam_car_rental_format_route_label_display( $route_label ) );
					?>
					<article class="annam-cr-journey-card">
						<?php
						$journey_bg = ! empty( $journey['bg_image'] ) ? (string) $journey['bg_image'] : '';
						$journey_bg_class = 'annam-cr-journey-card__bg';
						if ( $journey_bg ) {
							$journey_bg_class .= ' annam-cr-journey-card__bg--has-image';
						}
						?>
						<div
							class="<?php echo esc_attr( $journey_bg_class ); ?>"
							aria-hidden="true"
							<?php echo $journey_bg ? ' style="background-image:url(' . esc_url( $journey_bg ) . ')"' : ''; ?>
						></div>
						<div class="annam-cr-journey-card__body">
							<h3 class="annam-cr-journey-card__title"><?php echo esc_html( $label_display ); ?></h3>
							<p class="annam-cr-journey-card__price"><?php echo esc_html( $journey['price_label'] ?? '' ); ?></p>
							<button
								type="button"
								class="annam-cr-journey-card__link"
								data-annam-cr-pick-route="<?php echo esc_attr( $route_label ); ?>"
								data-annam-cr-pickup="<?php echo esc_attr( (string) ( $journey['pickup'] ?? '' ) ); ?>"
								data-annam-cr-destination="<?php echo esc_attr( (string) ( $journey['destination'] ?? '' ) ); ?>"
							>
								<?php esc_html_e( 'Nhận báo giá', 'generatepress_child' ); ?>
								<?php echo annam_car_rental_icon( 'arrow_forward' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</button>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<section class="annam-cr-section annam-cr-section--muted" id="dich-vu">
		<div class="annam-cr-container">
			<header class="annam-cr-section__head annam-cr-section__head--center">
				<h2 class="annam-cr-section__title"><?php esc_html_e( 'Dịch vụ theo nhu cầu', 'generatepress_child' ); ?></h2>
				<p class="annam-cr-section__desc"><?php esc_html_e( 'Giải pháp vận tải linh hoạt cho mọi mục đích', 'generatepress_child' ); ?></p>
			</header>
			<div class="annam-cr-service-grid">
				<?php foreach ( (array) ( $config['services'] ?? array() ) as $service ) : ?>
					<article class="annam-cr-service-card">
						<span class="annam-cr-service-card__icon"><?php echo annam_car_rental_icon( $service['icon'] ?? 'work' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<h3 class="annam-cr-service-card__title"><?php echo esc_html( $service['title'] ?? '' ); ?></h3>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/car-rental-landing/part-trust-section' ); ?>

	<section class="annam-cr-section">
		<div class="annam-cr-container annam-cr-why">
			<div class="annam-cr-why__media">
				<?php if ( $why_img ) : ?>
					<img src="<?php echo esc_url( $why_img ); ?>" alt="" width="640" height="400" loading="lazy" decoding="async" class="annam-cr-why__img" />
				<?php else : ?>
					<div class="annam-cr-why__placeholder" aria-hidden="true"></div>
				<?php endif; ?>
			</div>
			<div class="annam-cr-why__content">
				<h2 class="annam-cr-section__title"><?php esc_html_e( 'Vì sao nên chọn An Nam Discovery?', 'generatepress_child' ); ?></h2>
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

	<section class="annam-cr-section annam-cr-section--muted" id="doi-xe">
		<div class="annam-cr-container">
			<header class="annam-cr-section__head annam-cr-section__head--center">
				<h2 class="annam-cr-section__title"><?php esc_html_e( 'Gợi ý dòng xe phù hợp khác', 'generatepress_child' ); ?></h2>
				<p class="annam-cr-section__desc"><?php esc_html_e( 'Chọn xe dựa trên số lượng thành viên trong đoàn', 'generatepress_child' ); ?></p>
			</header>
			<div class="annam-cr-vehicle-grid">
				<?php foreach ( (array) ( $config['related'] ?? array() ) as $vehicle ) : ?>
					<article class="annam-cr-vehicle-card<?php echo ! empty( $vehicle['current'] ) ? ' annam-cr-vehicle-card--current' : ''; ?>">
						<?php if ( ! empty( $vehicle['current'] ) ) : ?>
							<span class="annam-cr-vehicle-card__badge"><?php esc_html_e( 'ĐANG XEM', 'generatepress_child' ); ?></span>
						<?php elseif ( ! empty( $vehicle['badge'] ) ) : ?>
							<span class="annam-cr-vehicle-card__badge annam-cr-vehicle-card__badge--vip"><?php echo esc_html( $vehicle['badge'] ); ?></span>
						<?php endif; ?>
						<span class="annam-cr-vehicle-card__icon"><?php echo annam_car_rental_icon( $vehicle['icon'] ?? 'directions_bus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<h3 class="annam-cr-vehicle-card__title"><?php echo esc_html( $vehicle['label'] ?? '' ); ?></h3>
						<p class="annam-cr-vehicle-card__pax"><?php echo esc_html( $vehicle['passengers'] ?? '' ); ?></p>
						<p class="annam-cr-vehicle-card__price"><?php echo esc_html( $vehicle['price_label'] ?? '' ); ?></p>
						<?php if ( ! empty( $vehicle['current'] ) ) : ?>
							<button type="button" class="annam-cr-btn annam-cr-btn--primary annam-cr-btn--block" data-annam-cr-scroll="#annam-cr-booking"><?php esc_html_e( 'Nhận báo giá', 'generatepress_child' ); ?></button>
						<?php else : ?>
							<a class="annam-cr-btn annam-cr-btn--ghost annam-cr-btn--block" href="<?php echo esc_url( $vehicle['url'] ?? '#' ); ?>"><?php esc_html_e( 'Xem chi tiết', 'generatepress_child' ); ?></a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="annam-cr-section" id="quy-trinh">
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
	<section class="annam-cr-section annam-cr-section--muted" id="faq">
		<div class="annam-cr-container annam-cr-container--narrow">
			<header class="annam-cr-section__head annam-cr-section__head--center">
				<h2 class="annam-cr-section__title"><?php esc_html_e( 'Câu hỏi thường gặp', 'generatepress_child' ); ?></h2>
			</header>
			<div class="annam-cr-faq" data-annam-cr-faq>
				<?php foreach ( $config['faq'] as $fi => $faq ) : ?>
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
	$cta_final     = isset( $config['cta_final'] ) ? $config['cta_final'] : array();
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
