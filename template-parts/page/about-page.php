<?php
/**
 * Nội dung trang Giới thiệu (template Giới thiệu An Nam Discovery).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$page_id = get_the_ID();
$b       = function_exists( 'annam_about_get_brand' ) ? annam_about_get_brand() : array();
$hero_bg = function_exists( 'annam_about_hero_background_url' ) ? annam_about_hero_background_url( $page_id ) : '';
$shop    = function_exists( 'annam_about_get_shop_url' ) ? annam_about_get_shop_url() : home_url( '/' );
$contact = function_exists( 'annam_about_get_contact_url' ) ? annam_about_get_contact_url() : home_url( '/lien-he/' );
$intro_side = function_exists( 'annam_about_intro_side_image_url' ) ? annam_about_intro_side_image_url() : '';
$gallery = function_exists( 'annam_about_get_gallery_items' ) ? annam_about_get_gallery_items( $page_id ) : array();
$reviews = function_exists( 'annam_about_get_testimonials' ) ? annam_about_get_testimonials() : array();

$raw_content = get_post_field( 'post_content', $page_id );
$has_editor  = is_string( $raw_content ) && trim( wp_strip_all_tags( $raw_content ) ) !== '';

$why_cards = array(
	array(
		'title' => __( 'Lịch trình rõ ràng', 'generatepress_child' ),
		'desc'  => __( 'Thời gian, điểm đến và các khoản phí liên quan được trình bày minh bạch trước khi bạn quyết định.', 'generatepress_child' ),
		'icon'  => 'route',
	),
	array(
		'title' => __( 'Tư vấn đúng nhu cầu', 'generatepress_child' ),
		'desc'  => __( 'Chúng tôi lắng nghe thời gian, ngân sách và mong muốn thực tế để đề xuất phương án phù hợp.', 'generatepress_child' ),
		'icon'  => 'chat',
	),
	array(
		'title' => __( 'Dịch vụ đa dạng', 'generatepress_child' ),
		'desc'  => __( 'Tour miền Bắc, du thuyền Hạ Long, combo nghỉ dưỡng, vé limousine và tour riêng theo yêu cầu.', 'generatepress_child' ),
		'icon'  => 'layers',
	),
	array(
		'title' => __( 'Hỗ trợ nhanh', 'generatepress_child' ),
		'desc'  => __( 'Hotline, Zalo và các kênh liên hệ được duy trì để bạn nhận phản hồi kịp thời.', 'generatepress_child' ),
		'icon'  => 'bolt',
	),
	array(
		'title' => __( 'Đối tác chọn lọc', 'generatepress_child' ),
		'desc'  => __( 'Ưu tiên làm việc với các nhà cung cấp có uy tín, giúp hành trình an tâm hơn.', 'generatepress_child' ),
		'icon'  => 'handshake',
	),
	array(
		'title' => __( 'Đồng hành sau khi đặt', 'generatepress_child' ),
		'desc'  => __( 'Hỗ trợ điều chỉnh lịch trình khi cần và giải đáp thắc mắc trong suốt chuyến đi.', 'generatepress_child' ),
		'icon'  => 'support',
	),
);

$service_cards = apply_filters(
	'annam_about_service_cards',
	array(
		array(
			'title' => __( 'Tour du lịch miền Bắc', 'generatepress_child' ),
			'desc'  => __( 'Các tuyến tham quan, trải nghiệm văn hóa và cảnh quan nổi bật.', 'generatepress_child' ),
			'url'   => $shop,
		),
		array(
			'title' => __( 'Du thuyền Hạ Long', 'generatepress_child' ),
			'desc'  => __( 'Lựa chọn hành trình trên vịnh với lịch trình và tiện ích phù hợp.', 'generatepress_child' ),
			'url'   => $shop,
		),
		array(
			'title' => __( 'Combo du lịch nghỉ dưỡng', 'generatepress_child' ),
			'desc'  => __( 'Gói kết hợp lưu trú và trải nghiệm để bạn chủ động thời gian nghỉ ngơi.', 'generatepress_child' ),
			'url'   => $shop,
		),
		array(
			'title' => __( 'Vé xe Limousine', 'generatepress_child' ),
			'desc'  => __( 'Di chuyển thuận tiện giữa các điểm trong hành trình của bạn.', 'generatepress_child' ),
			'url'   => $shop,
		),
		array(
			'title' => __( 'Thiết kế tour riêng', 'generatepress_child' ),
			'desc'  => __( 'Lịch trình theo nhóm nhỏ, gia đình hoặc sở thích cá nhân.', 'generatepress_child' ),
			'url'   => $contact,
		),
		array(
			'title' => __( 'Tư vấn đoàn gia đình, công ty', 'generatepress_child' ),
			'desc'  => __( 'Hỗ trợ lên ý tưởng và phối hợp logistics cho nhóm đông người.', 'generatepress_child' ),
			'url'   => $contact,
		),
	),
	$shop,
	$contact
);

if ( function_exists( 'annam_get_about_image_url' ) ) {
	$n = 1;
	foreach ( $service_cards as $k => $_svc ) {
		$u = annam_get_about_image_url( 'about_service_image_' . $n, 'large' );
		if ( '' !== $u ) {
			$service_cards[ $k ]['image_url'] = $u;
		}
		++$n;
	}
	unset( $n, $k, $_svc, $u );
}

$about_cta_bg = function_exists( 'annam_get_about_image_url' ) ? annam_get_about_image_url( 'about_cta_background_image', 'full' ) : '';

$process_steps = array(
	array(
		'n'     => '01',
		'title' => __( 'Gửi nhu cầu', 'generatepress_child' ),
		'desc'  => __( 'Cho chúng tôi biết điểm đến, thời gian và số lượng khách dự kiến.', 'generatepress_child' ),
		'icon'  => 'send',
	),
	array(
		'n'     => '02',
		'title' => __( 'Tư vấn lịch trình', 'generatepress_child' ),
		'desc'  => __( 'Đội ngũ đề xuất phương án, mức chi phí tham khảo và các lưu ý thực tế.', 'generatepress_child' ),
		'icon'  => 'map',
	),
	array(
		'n'     => '03',
		'title' => __( 'Xác nhận dịch vụ', 'generatepress_child' ),
		'desc'  => __( 'Thống nhất chi tiết dịch vụ và các bước thanh toán theo quy định.', 'generatepress_child' ),
		'icon'  => 'check',
	),
	array(
		'n'     => '04',
		'title' => __( 'Đồng hành trong chuyến đi', 'generatepress_child' ),
		'desc'  => __( 'Hỗ trợ khi bạn cần điều chỉnh hoặc giải đáp trong suốt hành trình.', 'generatepress_child' ),
		'icon'  => 'heart',
	),
);

/**
 * SVG icon (outline, currentColor).
 *
 * @param string $id Icon id.
 */
$annam_about_print_icon = static function ( $id ) {
	$common = ' xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
	switch ( $id ) {
		case 'route':
			echo '<svg' . $common . '><path d="M12 2C8 5 5 9 5 11a7 7 0 0 0 14 0c0-2-3-6-7-9z"/><circle cx="12" cy="11" r="2.5"/></svg>';
			break;
		case 'chat':
			echo '<svg' . $common . '><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
			break;
		case 'layers':
			echo '<svg' . $common . '><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>';
			break;
		case 'bolt':
			echo '<svg' . $common . '><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>';
			break;
		case 'handshake':
			echo '<svg' . $common . '><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
			break;
		case 'support':
			echo '<svg' . $common . '><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
			break;
		case 'send':
			echo '<svg' . $common . '><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>';
			break;
		case 'map':
			echo '<svg' . $common . '><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/></svg>';
			break;
		case 'check':
			echo '<svg' . $common . '><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
			break;
		case 'heart':
			echo '<svg' . $common . '><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>';
			break;
		default:
			echo '<svg' . $common . '><circle cx="12" cy="12" r="10"/></svg>';
	}
};
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'annam-about' ); ?>>
	<section class="annam-about-hero" aria-labelledby="annam-about-hero-title">
		<div class="annam-about-hero__bg" style="<?php echo $hero_bg ? 'background-image:url(' . esc_url( $hero_bg ) . ');' : ''; ?>"></div>
		<div class="annam-about-hero__overlay" aria-hidden="true"></div>
		<div class="annam-about-hero__inner annam-about-container">
			<nav class="annam-about-breadcrumb" aria-label="<?php echo esc_attr__( 'Breadcrumb', 'generatepress_child' ); ?>">
				<ol class="annam-about-breadcrumb__list">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html__( 'Trang chủ', 'generatepress_child' ); ?></a></li>
					<li aria-current="page"><?php echo esc_html__( 'Giới thiệu', 'generatepress_child' ); ?></li>
				</ol>
			</nav>
			<h1 id="annam-about-hero-title" class="annam-about-hero__title"><?php echo esc_html__( 'Giới thiệu An Nam Discovery', 'generatepress_child' ); ?></h1>
			<p class="annam-about-hero__subtitle"><?php echo esc_html__( 'Đồng hành cùng bạn trong những hành trình khám phá Việt Nam', 'generatepress_child' ); ?></p>
			<p class="annam-about-hero__lead">
				<?php echo esc_html__( 'Tư vấn tour, du thuyền, combo nghỉ dưỡng và dịch vụ du lịch miền Bắc với lịch trình rõ ràng, hỗ trợ nhanh và thông tin minh bạch.', 'generatepress_child' ); ?>
			</p>
			<div class="annam-about-hero__cta">
				<a class="annam-about-btn annam-about-btn--primary" href="<?php echo esc_url( $shop ); ?>"><?php echo esc_html__( 'Xem tour nổi bật', 'generatepress_child' ); ?></a>
				<a class="annam-about-btn annam-about-btn--ghost" href="<?php echo esc_url( $contact ); ?>"><?php echo esc_html__( 'Liên hệ tư vấn', 'generatepress_child' ); ?></a>
			</div>
		</div>
	</section>

	<section class="annam-about-section annam-about-who" aria-labelledby="annam-about-who-title">
		<div class="annam-about-container">
			<h2 id="annam-about-who-title" class="annam-about-section__title"><?php echo esc_html__( 'Chúng tôi là ai?', 'generatepress_child' ); ?></h2>
			<div class="annam-about-who__grid">
				<div class="annam-about-who__content">
					<p class="annam-about-prose">
						<?php echo esc_html__( 'An Nam Discovery là đơn vị tư vấn và tổ chức dịch vụ du lịch, tập trung vào các tuyến tour miền Bắc, du thuyền Hạ Long, combo nghỉ dưỡng và dịch vụ di chuyển du lịch.', 'generatepress_child' ); ?>
					</p>
					<p class="annam-about-prose">
						<?php echo esc_html__( 'Chúng tôi hướng đến việc giúp khách hàng lựa chọn hành trình phù hợp, rõ chi phí, rõ lịch trình và có người hỗ trợ trước, trong và sau chuyến đi.', 'generatepress_child' ); ?>
					</p>
					<?php if ( $has_editor ) : ?>
						<div class="annam-about-editor">
							<?php echo apply_filters( 'the_content', $raw_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- same as the_content(). ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="annam-about-who__media">
					<img src="<?php echo esc_url( $intro_side ); ?>" alt="" width="640" height="480" loading="lazy" decoding="async" class="annam-about-who__img" />
				</div>
			</div>
		</div>
	</section>

	<section class="annam-about-section annam-about-stats" aria-labelledby="annam-about-stats-title">
		<div class="annam-about-container">
			<h2 id="annam-about-stats-title" class="annam-about-section__title annam-about-section__title--center"><?php echo esc_html__( 'Uy tín qua từng hành trình', 'generatepress_child' ); ?></h2>
			<div class="annam-about-stats__grid">
				<div class="annam-about-stat-card">
					<span class="annam-about-stat-card__num"><?php echo esc_html__( '100+', 'generatepress_child' ); ?></span>
					<h3 class="annam-about-stat-card__title"><?php echo esc_html__( 'Lịch trình tour / combo', 'generatepress_child' ); ?></h3>
					<p class="annam-about-stat-card__desc"><?php echo esc_html__( 'Đa dạng lựa chọn cho miền Bắc và các trải nghiệm kết hợp.', 'generatepress_child' ); ?></p>
				</div>
				<div class="annam-about-stat-card">
					<span class="annam-about-stat-card__num">2</span>
					<h3 class="annam-about-stat-card__title"><?php echo esc_html__( 'Văn phòng tại Hà Nội', 'generatepress_child' ); ?></h3>
					<p class="annam-about-stat-card__desc"><?php echo esc_html__( 'Hoàn Kiếm và Yên Hòa — thuận tiện tư vấn trực tiếp.', 'generatepress_child' ); ?></p>
				</div>
				<div class="annam-about-stat-card">
					<span class="annam-about-stat-card__num">∞</span>
					<h3 class="annam-about-stat-card__title"><?php echo esc_html__( 'Hỗ trợ online mỗi ngày', 'generatepress_child' ); ?></h3>
					<p class="annam-about-stat-card__desc"><?php echo esc_html__( 'Hotline, Zalo và email được duy trì để phản hồi nhanh.', 'generatepress_child' ); ?></p>
				</div>
				<div class="annam-about-stat-card">
					<span class="annam-about-stat-card__num"><?php echo esc_html__( 'Nhiều', 'generatepress_child' ); ?></span>
					<h3 class="annam-about-stat-card__title"><?php echo esc_html__( 'Điểm đến miền Bắc', 'generatepress_child' ); ?></h3>
					<p class="annam-about-stat-card__desc"><?php echo esc_html__( 'Thông tin lịch trình và dịch vụ được trình bày minh bạch.', 'generatepress_child' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="annam-about-section annam-about-why" aria-labelledby="annam-about-why-title">
		<div class="annam-about-container">
			<h2 id="annam-about-why-title" class="annam-about-section__title annam-about-section__title--center"><?php echo esc_html__( 'Vì sao chọn An Nam Discovery?', 'generatepress_child' ); ?></h2>
			<div class="annam-about-why__grid">
				<?php foreach ( $why_cards as $card ) : ?>
					<div class="annam-about-icon-card">
						<div class="annam-about-icon-card__icon"><?php $annam_about_print_icon( $card['icon'] ); ?></div>
						<h3 class="annam-about-icon-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="annam-about-icon-card__desc"><?php echo esc_html( $card['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="annam-about-section annam-about-services" aria-labelledby="annam-about-services-title">
		<div class="annam-about-container">
			<h2 id="annam-about-services-title" class="annam-about-section__title annam-about-section__title--center"><?php echo esc_html__( 'Dịch vụ chính', 'generatepress_child' ); ?></h2>
			<div class="annam-about-services__grid">
				<?php foreach ( $service_cards as $svc ) : ?>
					<div class="annam-about-service-card">
						<div class="annam-about-service-card__visual" aria-hidden="true">
							<?php if ( ! empty( $svc['image_url'] ) ) : ?>
								<img src="<?php echo esc_url( $svc['image_url'] ); ?>" alt="" class="annam-about-service-card__visual-img" width="480" height="240" loading="lazy" decoding="async" />
							<?php endif; ?>
						</div>
						<h3 class="annam-about-service-card__title"><?php echo esc_html( $svc['title'] ); ?></h3>
						<p class="annam-about-service-card__desc"><?php echo esc_html( $svc['desc'] ); ?></p>
						<a class="annam-about-service-card__link" href="<?php echo esc_url( isset( $svc['url'] ) ? $svc['url'] : $shop ); ?>"><?php echo esc_html__( 'Xem chi tiết', 'generatepress_child' ); ?></a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="annam-about-section annam-about-legal" aria-labelledby="annam-about-legal-title">
		<div class="annam-about-container">
			<h2 id="annam-about-legal-title" class="annam-about-section__title annam-about-section__title--center"><?php echo esc_html__( 'Văn phòng & pháp lý', 'generatepress_child' ); ?></h2>
			<div class="annam-about-legal__grid">
				<div class="annam-about-legal__block">
					<h3 class="annam-about-legal__company"><?php echo esc_html( isset( $b['company'] ) ? $b['company'] : '' ); ?></h3>
					<p class="annam-about-legal__line"><strong><?php echo esc_html( isset( $b['license_label'] ) ? $b['license_label'] : '' ); ?></strong></p>
					<p class="annam-about-legal__line"><?php echo esc_html__( 'Số:', 'generatepress_child' ); ?> <?php echo esc_html( isset( $b['license_number'] ) ? $b['license_number'] : '' ); ?></p>
					<p class="annam-about-legal__line"><?php echo esc_html__( 'Mã số thuế:', 'generatepress_child' ); ?> <?php echo esc_html( isset( $b['tax_id'] ) ? $b['tax_id'] : '' ); ?></p>
					<p class="annam-about-legal__line"><?php echo esc_html__( 'Email:', 'generatepress_child' ); ?> <a href="<?php echo esc_url( 'mailto:' . ( isset( $b['email'] ) ? $b['email'] : '' ) ); ?>"><?php echo esc_html( isset( $b['email'] ) ? $b['email'] : '' ); ?></a></p>
				</div>
				<div class="annam-about-legal__offices">
					<div class="annam-about-office-card">
						<h3 class="annam-about-office-card__title"><?php echo esc_html( isset( $b['office1_title'] ) ? $b['office1_title'] : '' ); ?></h3>
						<p class="annam-about-office-card__addr"><?php echo esc_html( isset( $b['office1_address'] ) ? $b['office1_address'] : '' ); ?></p>
						<a class="annam-about-btn annam-about-btn--outline" href="<?php echo esc_url( isset( $b['office1_maps'] ) ? $b['office1_maps'] : '#' ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'Chỉ đường', 'generatepress_child' ); ?></a>
					</div>
					<div class="annam-about-office-card">
						<h3 class="annam-about-office-card__title"><?php echo esc_html( isset( $b['office2_title'] ) ? $b['office2_title'] : '' ); ?></h3>
						<p class="annam-about-office-card__addr"><?php echo esc_html( isset( $b['office2_address'] ) ? $b['office2_address'] : '' ); ?></p>
						<a class="annam-about-btn annam-about-btn--outline" href="<?php echo esc_url( isset( $b['office2_maps'] ) ? $b['office2_maps'] : '#' ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'Chỉ đường', 'generatepress_child' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="annam-about-section annam-about-process" aria-labelledby="annam-about-process-title">
		<div class="annam-about-container">
			<h2 id="annam-about-process-title" class="annam-about-section__title annam-about-section__title--center"><?php echo esc_html__( 'Quy trình tư vấn và đặt tour', 'generatepress_child' ); ?></h2>
			<div class="annam-about-process__steps">
				<?php foreach ( $process_steps as $step ) : ?>
					<div class="annam-about-step">
						<div class="annam-about-step__num"><?php echo esc_html( $step['n'] ); ?></div>
						<div class="annam-about-step__icon"><?php $annam_about_print_icon( $step['icon'] ); ?></div>
						<h3 class="annam-about-step__title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="annam-about-step__desc"><?php echo esc_html( $step['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="annam-about-section annam-about-gallery-wrap" aria-labelledby="annam-about-gallery-title">
		<div class="annam-about-container">
			<h2 id="annam-about-gallery-title" class="annam-about-section__title annam-about-section__title--center"><?php echo esc_html__( 'Hành trình & hình ảnh thực tế', 'generatepress_child' ); ?></h2>
			<p class="annam-about-section__lead annam-about-section__lead--center"><?php echo esc_html__( 'Một số khoảnh khắc từ các tuyến tour và dịch vụ đồng hành cùng khách.', 'generatepress_child' ); ?></p>
			<div class="annam-about-gallery" data-annam-about-lightbox-root>
				<?php foreach ( $gallery as $item ) : ?>
					<button type="button" class="annam-about-gallery__item" data-full="<?php echo esc_url( $item['full'] ); ?>" aria-label="<?php echo esc_attr__( 'Xem ảnh lớn', 'generatepress_child' ); ?>">
						<img src="<?php echo esc_url( $item['url'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>" loading="lazy" decoding="async" width="400" height="300" />
					</button>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $reviews ) ) : ?>
		<section class="annam-about-section annam-about-reviews" aria-labelledby="annam-about-reviews-title">
			<div class="annam-about-container">
				<h2 id="annam-about-reviews-title" class="annam-about-section__title annam-about-section__title--center"><?php echo esc_html__( 'Đánh giá từ khách hàng', 'generatepress_child' ); ?></h2>
				<div class="annam-about-reviews__slider" data-annam-about-slider>
					<div class="annam-about-reviews__track">
						<?php foreach ( $reviews as $rev ) : ?>
							<blockquote class="annam-about-review-card">
								<p class="annam-about-review-card__quote"><?php echo esc_html( $rev['quote'] ); ?></p>
								<footer class="annam-about-review-card__meta">
									<strong><?php echo esc_html( $rev['name'] ); ?></strong>
									<?php if ( $rev['role'] !== '' ) : ?>
										<span class="annam-about-review-card__role"><?php echo esc_html( $rev['role'] ); ?></span>
									<?php endif; ?>
								</footer>
							</blockquote>
						<?php endforeach; ?>
					</div>
					<?php if ( count( $reviews ) > 1 ) : ?>
						<div class="annam-about-reviews__nav">
							<button type="button" class="annam-about-reviews__btn annam-about-reviews__btn--prev" data-dir="-1" aria-label="<?php echo esc_attr__( 'Slide trước', 'generatepress_child' ); ?>">‹</button>
							<button type="button" class="annam-about-reviews__btn annam-about-reviews__btn--next" data-dir="1" aria-label="<?php echo esc_attr__( 'Slide sau', 'generatepress_child' ); ?>">›</button>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="annam-about-cta" aria-labelledby="annam-about-cta-title">
		<?php if ( '' !== $about_cta_bg ) : ?>
			<div class="annam-about-cta__bg" style="background-image:url(<?php echo esc_url( $about_cta_bg ); ?>);" aria-hidden="true"></div>
		<?php endif; ?>
		<div class="annam-about-cta__overlay" aria-hidden="true"></div>
		<div class="annam-about-container annam-about-cta__inner">
			<h2 id="annam-about-cta-title" class="annam-about-cta__title"><?php echo esc_html__( 'Bạn đang cần tư vấn một hành trình phù hợp?', 'generatepress_child' ); ?></h2>
			<p class="annam-about-cta__desc">
				<?php echo esc_html__( 'An Nam Discovery sẵn sàng hỗ trợ bạn chọn tour, du thuyền hoặc combo du lịch theo thời gian, ngân sách và nhu cầu thực tế.', 'generatepress_child' ); ?>
			</p>
			<div class="annam-about-cta__actions">
				<a class="annam-about-btn annam-about-btn--cta" href="<?php echo esc_url( isset( $b['hotline_tel'] ) ? $b['hotline_tel'] : 'tel:19008164' ); ?>"><?php echo esc_html__( 'Gọi ngay:', 'generatepress_child' ); ?> <?php echo esc_html( isset( $b['hotline_display'] ) ? $b['hotline_display'] : '1900 8164' ); ?></a>
				<a class="annam-about-btn annam-about-btn--ghost" href="<?php echo esc_url( isset( $b['zalo_url'] ) ? $b['zalo_url'] : '#' ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'Tư vấn Zalo', 'generatepress_child' ); ?></a>
				<a class="annam-about-btn annam-about-btn--primary" href="<?php echo esc_url( $shop ); ?>"><?php echo esc_html__( 'Xem tour', 'generatepress_child' ); ?></a>
			</div>
		</div>
	</section>
</article>

<div class="annam-about-lightbox" id="annam-about-lightbox" hidden data-annam-about-lightbox>
	<button type="button" class="annam-about-lightbox__close" data-annam-about-lightbox-close aria-label="<?php echo esc_attr__( 'Đóng', 'generatepress_child' ); ?>">×</button>
	<img src="" alt="" class="annam-about-lightbox__img" data-annam-about-lightbox-img />
</div>
