<?php
/**
 * Nội dung trang Liên hệ (template Liên hệ An Nam Discovery).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$page_id = get_the_ID();
$hero_bg = function_exists( 'annam_contact_hero_background_url' ) ? annam_contact_hero_background_url( $page_id ) : '';
$notice  = function_exists( 'annam_contact_get_notice' ) ? annam_contact_get_notice() : null;
$d       = function_exists( 'annam_contact_get_details' ) ? annam_contact_get_details() : array();

$offices = function_exists( 'annam_contact_get_offices' ) ? annam_contact_get_offices() : array();

$services       = function_exists( 'annam_contact_service_options' ) ? annam_contact_service_options() : array();
$contact_today  = wp_date( 'Y-m-d' );
$default_service = 'tour';

$social_links = array(
	array(
		'label' => __( 'Facebook', 'generatepress_child' ),
		'url'   => isset( $d['facebook_url'] ) ? $d['facebook_url'] : '',
	),
	array(
		'label' => __( 'X / Twitter', 'generatepress_child' ),
		'url'   => isset( $d['twitter_url'] ) ? $d['twitter_url'] : '',
	),
	array(
		'label' => __( 'YouTube', 'generatepress_child' ),
		'url'   => isset( $d['youtube_url'] ) ? $d['youtube_url'] : '',
	),
	array(
		'label' => __( 'Instagram', 'generatepress_child' ),
		'url'   => isset( $d['instagram_url'] ) ? $d['instagram_url'] : '',
	),
	array(
		'label' => __( 'Zalo', 'generatepress_child' ),
		'url'   => isset( $d['zalo_url'] ) ? $d['zalo_url'] : '',
	),
	array(
		'label' => __( 'WhatsApp', 'generatepress_child' ),
		'url'   => isset( $d['whatsapp_url'] ) ? $d['whatsapp_url'] : '',
	),
);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'annam-contact-page' ); ?>>
	<?php if ( $notice ) : ?>
		<div class="annam-contact-container">
			<div
				class="annam-contact-page__notice annam-contact-page__notice--<?php echo esc_attr( $notice['type'] ); ?>"
				role="<?php echo 'error' === $notice['type'] ? 'alert' : 'status'; ?>"
				aria-live="polite"
				tabindex="-1"
				id="annam-contact-notice"
			>
				<?php echo esc_html( $notice['message'] ); ?>
			</div>
		</div>
	<?php endif; ?>

	<section class="annam-contact-hero" aria-labelledby="annam-contact-hero-title">
		<div class="annam-contact-hero__bg" style="<?php echo $hero_bg ? 'background-image: url(' . esc_url( $hero_bg ) . ');' : ''; ?>"></div>
		<div class="annam-contact-hero__overlay" aria-hidden="true"></div>
		<div class="annam-contact-hero__inner annam-contact-container">
			<nav class="annam-contact-hero__breadcrumb" aria-label="<?php echo esc_attr__( 'Breadcrumb', 'generatepress_child' ); ?>">
				<ol class="annam-contact-hero__breadcrumb-list">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html__( 'Trang chủ', 'generatepress_child' ); ?></a></li>
					<li aria-current="page"><?php echo esc_html__( 'Liên hệ', 'generatepress_child' ); ?></li>
				</ol>
			</nav>
			<h1 id="annam-contact-hero-title" class="annam-contact-hero__title"><?php echo esc_html__( 'Liên hệ An Nam Discovery', 'generatepress_child' ); ?></h1>
			<p class="annam-contact-hero__desc">
				<?php echo esc_html__( 'Cần tư vấn tour, du thuyền, combo nghỉ dưỡng hoặc vé xe? Đội ngũ An Nam Discovery luôn sẵn sàng hỗ trợ bạn chọn hành trình phù hợp.', 'generatepress_child' ); ?>
			</p>
		</div>
	</section>

	<section class="annam-contact-quick annam-contact-container" aria-labelledby="annam-contact-quick-title">
		<h2 id="annam-contact-quick-title" class="annam-contact-section-title"><?php echo esc_html__( 'Liên hệ nhanh', 'generatepress_child' ); ?></h2>
		<div class="annam-contact-quick__grid" role="list">
			<div class="annam-contact-card annam-contact-card--quick" role="listitem">
				<span class="annam-contact-card__label"><?php echo esc_html__( 'Hotline', 'generatepress_child' ); ?></span>
				<a class="annam-contact-card__value annam-contact-card__value--link" href="<?php echo esc_url( isset( $d['hotline_tel'] ) ? $d['hotline_tel'] : 'tel:19008164' ); ?>"><?php echo esc_html( isset( $d['hotline_display'] ) ? $d['hotline_display'] : '1900 8164' ); ?></a>
			</div>
			<div class="annam-contact-card annam-contact-card--quick" role="listitem">
				<span class="annam-contact-card__label"><?php echo esc_html__( 'Zalo', 'generatepress_child' ); ?></span>
				<a class="annam-contact-card__value annam-contact-card__value--link" href="<?php echo esc_url( isset( $d['zalo_url'] ) ? $d['zalo_url'] : '#' ); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html( isset( $d['mobile_display'] ) ? $d['mobile_display'] : '' ); ?></a>
			</div>
			<div class="annam-contact-card annam-contact-card--quick" role="listitem">
				<span class="annam-contact-card__label"><?php echo esc_html__( 'WhatsApp', 'generatepress_child' ); ?></span>
				<a class="annam-contact-card__value annam-contact-card__value--link" href="<?php echo esc_url( isset( $d['whatsapp_url'] ) ? $d['whatsapp_url'] : '#' ); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html( isset( $d['mobile_display'] ) ? $d['mobile_display'] : '' ); ?></a>
			</div>
			<div class="annam-contact-card annam-contact-card--quick" role="listitem">
				<span class="annam-contact-card__label"><?php echo esc_html__( 'Địa chỉ', 'generatepress_child' ); ?></span>
				<?php if ( ! empty( $d['maps_directions_url'] ) ) : ?>
					<a class="annam-contact-card__value annam-contact-card__value--link" href="<?php echo esc_url( $d['maps_directions_url'] ); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html( isset( $d['address'] ) ? $d['address'] : '' ); ?></a>
				<?php else : ?>
					<span class="annam-contact-card__value"><?php echo esc_html( isset( $d['address'] ) ? $d['address'] : '' ); ?></span>
				<?php endif; ?>
			</div>
			<div class="annam-contact-card annam-contact-card--quick" role="listitem">
				<span class="annam-contact-card__label"><?php echo esc_html__( 'Thời gian hỗ trợ', 'generatepress_child' ); ?></span>
				<span class="annam-contact-card__value"><?php echo esc_html( isset( $d['hours'] ) ? $d['hours'] : __( '08:00 - 22:00 hằng ngày', 'generatepress_child' ) ); ?></span>
			</div>
		</div>
	</section>

	<section class="annam-contact-cta-bar annam-contact-container" aria-labelledby="annam-contact-cta-title">
		<h2 id="annam-contact-cta-title" class="screen-reader-text"><?php echo esc_html__( 'Liên hệ nhanh qua điện thoại và bản đồ', 'generatepress_child' ); ?></h2>
		<div class="annam-contact-cta-bar__inner">
			<a class="annam-contact-btn annam-contact-btn--secondary annam-contact-cta-bar__btn" href="<?php echo esc_url( isset( $d['hotline_tel'] ) ? $d['hotline_tel'] : 'tel:19008164' ); ?>"><?php echo esc_html__( 'Gọi ngay', 'generatepress_child' ); ?></a>
			<a class="annam-contact-btn annam-contact-btn--outline annam-contact-cta-bar__btn" href="<?php echo esc_url( isset( $d['zalo_url'] ) ? $d['zalo_url'] : '#' ); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html__( 'Nhắn Zalo', 'generatepress_child' ); ?></a>
			<a class="annam-contact-btn annam-contact-btn--outline annam-contact-cta-bar__btn" href="<?php echo esc_url( isset( $d['whatsapp_url'] ) ? $d['whatsapp_url'] : '#' ); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html__( 'WhatsApp', 'generatepress_child' ); ?></a>
			<a class="annam-contact-btn annam-contact-btn--outline annam-contact-cta-bar__btn" href="<?php echo esc_url( isset( $d['maps_directions_url'] ) ? $d['maps_directions_url'] : '#' ); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html__( 'Chỉ đường', 'generatepress_child' ); ?></a>
		</div>
	</section>

	<section class="annam-contact-social annam-contact-container" aria-labelledby="annam-contact-social-title">
		<h2 id="annam-contact-social-title" class="annam-contact-section-title"><?php echo esc_html__( 'Mạng xã hội', 'generatepress_child' ); ?></h2>
		<ul class="annam-contact-social__grid">
			<?php foreach ( $social_links as $item ) : ?>
				<?php if ( ! empty( $item['url'] ) ) : ?>
					<li>
						<a class="annam-contact-social__link" href="<?php echo esc_url( $item['url'] ); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html( $item['label'] ); ?></a>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</section>

	<section class="annam-contact-main annam-contact-container" aria-labelledby="annam-contact-main-title">
		<h2 id="annam-contact-main-title" class="screen-reader-text"><?php echo esc_html__( 'Gửi yêu cầu tư vấn', 'generatepress_child' ); ?></h2>
		<div class="annam-contact-main__grid">
			<div class="annam-contact-form-wrap">
				<div class="annam-contact-form__ajax-notice" id="annam-contact-form-notice" role="alert" hidden></div>

				<form id="annam-contact-form" class="annam-contact-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate data-annam-contact-form>
					<?php wp_nonce_field( 'annam_contact_form', 'annam_contact_nonce' ); ?>
					<input type="hidden" name="annam_contact_submit" value="1" />
					<input type="hidden" name="annam_form_ts" id="annam-contact-ts" value="<?php echo esc_attr( (string) time() ); ?>" />

					<p class="annam-contact-form__hp" aria-hidden="true">
						<label for="annam-contact-website"><?php echo esc_html__( 'Website', 'generatepress_child' ); ?></label>
						<input type="text" name="annam_contact_website" id="annam-contact-website" value="" tabindex="-1" autocomplete="off" />
					</p>

					<div class="annam-contact-form__field">
						<label for="annam-contact-name"><?php echo esc_html__( 'Họ và tên', 'generatepress_child' ); ?> <span class="annam-contact-form__req">*</span></label>
						<input type="text" name="annam_contact_name" id="annam-contact-name" required maxlength="100" autocomplete="name" placeholder="<?php echo esc_attr__( 'Nhập họ và tên của quý khách', 'generatepress_child' ); ?>" />
					</div>
					<div class="annam-contact-form__field">
						<label for="annam-contact-phone"><?php echo esc_html__( 'Số điện thoại', 'generatepress_child' ); ?> <span class="annam-contact-form__req">*</span></label>
						<input type="tel" name="annam_contact_phone" id="annam-contact-phone" required maxlength="25" inputmode="tel" autocomplete="tel" placeholder="<?php echo esc_attr__( 'Nhập số điện thoại hoặc Zalo', 'generatepress_child' ); ?>" />
					</div>
					<div class="annam-contact-form__field">
						<label for="annam-contact-service"><?php echo esc_html__( 'Loại dịch vụ cần tư vấn', 'generatepress_child' ); ?> <span class="annam-contact-form__req">*</span></label>
						<select name="annam_contact_service" id="annam-contact-service" required>
							<?php foreach ( $services as $val => $label ) : ?>
								<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $default_service, $val ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="annam-contact-form__row">
						<div class="annam-contact-form__field">
							<label for="annam-contact-date"><?php echo esc_html__( 'Ngày đi dự kiến', 'generatepress_child' ); ?></label>
							<input type="date" name="annam_contact_travel_date" id="annam-contact-date" value="<?php echo esc_attr( $contact_today ); ?>" min="<?php echo esc_attr( $contact_today ); ?>" />
						</div>
						<div class="annam-contact-form__field">
							<label for="annam-contact-guests"><?php echo esc_html__( 'Số lượng khách', 'generatepress_child' ); ?></label>
							<input type="number" name="annam_contact_guests" id="annam-contact-guests" min="1" max="500" placeholder="<?php echo esc_attr__( 'Nhập số lượng khách', 'generatepress_child' ); ?>" />
						</div>
					</div>
					<div class="annam-contact-form__field">
						<label for="annam-contact-message"><?php echo esc_html__( 'Nội dung cần hỗ trợ', 'generatepress_child' ); ?></label>
						<textarea name="annam_contact_message" id="annam-contact-message" rows="5" maxlength="1000"></textarea>
					</div>
					<div class="annam-contact-form__actions">
						<button type="submit" class="annam-contact-btn annam-contact-btn--primary" id="annam-contact-submit"><?php echo esc_html__( 'Gửi yêu cầu tư vấn', 'generatepress_child' ); ?></button>
					</div>
				</form>
			</div>

			<div class="annam-contact-info">
				<h3 class="annam-contact-info__heading"><?php echo esc_html__( 'Thông tin liên hệ', 'generatepress_child' ); ?></h3>
				<p class="annam-contact-info__brand"><?php echo esc_html( isset( $d['brand'] ) ? $d['brand'] : 'An Nam Discovery' ); ?></p>
				<p class="annam-contact-info__text">
					<?php echo esc_html__( 'An Nam Discovery chuyên tư vấn tour, du thuyền Hạ Long, combo nghỉ dưỡng và dịch vụ đi kèm trên toàn quốc.', 'generatepress_child' ); ?>
				</p>
				<?php if ( ! empty( $d['address'] ) ) : ?>
					<p class="annam-contact-info__address">
						<?php if ( ! empty( $d['maps_directions_url'] ) ) : ?>
							<a href="<?php echo esc_url( $d['maps_directions_url'] ); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html( $d['address'] ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $d['address'] ); ?>
						<?php endif; ?>
					</p>
				<?php endif; ?>
				<?php if ( ! empty( $d['email'] ) && is_email( $d['email'] ) ) : ?>
					<p class="annam-contact-info__email">
						<a href="mailto:<?php echo esc_attr( $d['email'] ); ?>"><?php echo esc_html( $d['email'] ); ?></a>
					</p>
				<?php endif; ?>
				<ul class="annam-contact-info__list">
					<li><?php echo esc_html__( 'Tư vấn rõ ràng, không ép đặt tour.', 'generatepress_child' ); ?></li>
					<li><?php echo esc_html__( 'Hỗ trợ báo giá và so sánh lịch trình.', 'generatepress_child' ); ?></li>
					<li><?php echo esc_html__( 'Phản hồi nhanh qua Hotline, Zalo và WhatsApp.', 'generatepress_child' ); ?></li>
				</ul>
				<p class="annam-contact-info__hint">
					<?php echo esc_html__( 'Bạn có thể gọi ngay, nhắn Zalo, WhatsApp hoặc mở chỉ đường bằng các nút ở phần trên trang.', 'generatepress_child' ); ?>
				</p>
			</div>
		</div>
	</section>

	<section class="annam-contact-support annam-contact-container" aria-labelledby="annam-contact-support-title">
		<h2 id="annam-contact-support-title" class="annam-contact-section-title"><?php echo esc_html__( 'Chúng tôi có thể hỗ trợ gì?', 'generatepress_child' ); ?></h2>
		<div class="annam-contact-support__grid" role="list">
			<div class="annam-contact-card annam-contact-card--support" role="listitem">
				<h3 class="annam-contact-card__title"><?php echo esc_html__( 'Tư vấn chọn tour phù hợp', 'generatepress_child' ); ?></h3>
				<p class="annam-contact-card__text"><?php echo esc_html__( 'Gợi ý lịch trình, điểm đến và mức giá phù hợp nhu cầu của bạn.', 'generatepress_child' ); ?></p>
			</div>
			<div class="annam-contact-card annam-contact-card--support" role="listitem">
				<h3 class="annam-contact-card__title"><?php echo esc_html__( 'Báo giá du thuyền Hạ Long', 'generatepress_child' ); ?></h3>
				<p class="annam-contact-card__text"><?php echo esc_html__( 'So sánh tàu, cabin và dịch vụ đi kèm để bạn chọn trải nghiệm tốt nhất.', 'generatepress_child' ); ?></p>
			</div>
			<div class="annam-contact-card annam-contact-card--support" role="listitem">
				<h3 class="annam-contact-card__title"><?php echo esc_html__( 'Thiết kế combo xe + khách sạn', 'generatepress_child' ); ?></h3>
				<p class="annam-contact-card__text"><?php echo esc_html__( 'Kết nối vé xe limousine, lưu trú và hoạt động tại điểm đến.', 'generatepress_child' ); ?></p>
			</div>
			<div class="annam-contact-card annam-contact-card--support" role="listitem">
				<h3 class="annam-contact-card__title"><?php echo esc_html__( 'Hỗ trợ đoàn gia đình, công ty', 'generatepress_child' ); ?></h3>
				<p class="annam-contact-card__text"><?php echo esc_html__( 'Lên chương trình riêng, số lượng lớn và yêu cầu đặc thù.', 'generatepress_child' ); ?></p>
			</div>
		</div>
	</section>

	<section class="annam-contact-map annam-contact-container" aria-labelledby="annam-contact-map-title">
		<h2 id="annam-contact-map-title" class="annam-contact-section-title"><?php echo esc_html__( 'Văn phòng & khu vực phục vụ', 'generatepress_child' ); ?></h2>
		<?php if ( ! empty( $offices ) ) : ?>
			<ul class="annam-contact-offices__grid">
				<?php foreach ( $offices as $office ) : ?>
					<?php
					$o_title = isset( $office['title'] ) ? (string) $office['title'] : '';
					$o_addr  = isset( $office['address'] ) ? (string) $office['address'] : '';
					$o_maps  = isset( $office['maps_url'] ) ? (string) $office['maps_url'] : '';
					if ( '' === $o_title || '' === $o_addr || '' === $o_maps ) {
						continue;
					}
					?>
					<li class="annam-contact-offices__cell">
						<article class="annam-contact-office-card">
							<h3 class="annam-contact-office-card__title"><?php echo esc_html( $o_title ); ?></h3>
							<p class="annam-contact-office-card__address"><?php echo esc_html( $o_addr ); ?></p>
							<div class="annam-contact-office-card__action">
								<a class="annam-contact-btn annam-contact-btn--outline annam-contact-office-card__btn" href="<?php echo esc_url( $o_maps ); ?>" target="_blank" rel="noopener"><?php echo esc_html__( 'Chỉ đường', 'generatepress_child' ); ?></a>
							</div>
						</article>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="annam-contact-offices__note">
				<?php echo esc_html__( 'Hỗ trợ tư vấn tour, du thuyền, combo nghỉ dưỡng và vé xe du lịch miền Bắc qua Hotline, Zalo và WhatsApp.', 'generatepress_child' ); ?>
			</p>
		<?php endif; ?>
	</section>

	<section class="annam-contact-faq annam-contact-container" aria-labelledby="annam-contact-faq-title">
		<h2 id="annam-contact-faq-title" class="annam-contact-section-title"><?php echo esc_html__( 'Câu hỏi thường gặp', 'generatepress_child' ); ?></h2>
		<div class="annam-contact-faq__list">
			<?php foreach ( annam_contact_get_faq_items() as $annam_faq ) : ?>
				<details class="annam-contact-faq__item">
					<summary class="annam-contact-faq__q"><?php echo esc_html( $annam_faq['question'] ); ?></summary>
					<div class="annam-contact-faq__a">
						<p><?php echo esc_html( $annam_faq['answer'] ); ?></p>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
	</section>
</article>
