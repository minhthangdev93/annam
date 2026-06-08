<?php
/**
 * Footer site — An Nam Discovery (child theme).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$pay_src       = annam_footer_payment_image_url();
$sale_noti_src = function_exists( 'annam_footer_sale_notice_image_url' ) ? annam_footer_sale_notice_image_url() : '';
$tour_links  = function_exists( 'annam_footer_get_tour_links' ) ? annam_footer_get_tour_links() : array();
$show_tours = (bool) apply_filters( 'annam_footer_show_tours_column', ! empty( $tour_links ) );
$info_links = function_exists( 'annam_footer_get_info_links' ) ? annam_footer_get_info_links() : array();
$social     = function_exists( 'annam_footer_get_social_links' ) ? annam_footer_get_social_links() : array();
$map_urls   = function_exists( 'annam_footer_office_map_urls' ) ? annam_footer_office_map_urls() : array();
$url_tu_mo  = isset( $map_urls['tu_mo'] ) ? (string) $map_urls['tu_mo'] : '';
$url_hoan_kiem = isset( $map_urls['hoan_kiem'] ) ? (string) $map_urls['hoan_kiem'] : '';
$show_ecosystem = (bool) apply_filters( 'annam_footer_show_ecosystem', true );
$ecosystem_html = '';
if ( $show_ecosystem && function_exists( 'annam_ecosystem_get_section_html' ) ) {
	$ecosystem_html = annam_ecosystem_get_section_html();
}
?>
<footer class="annam-site-footer<?php echo $show_tours ? '' : ' annam-site-footer--no-tours-col'; ?>" role="contentinfo">
	<?php if ( '' !== $ecosystem_html ) : ?>
		<div class="annam-site-footer__ecosystem">
			<?php echo $ecosystem_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML do theme tạo, đã esc từng field trong hàm. ?>
		</div>
	<?php endif; ?>
	<div class="annam-site-footer__main">
		<div class="annam-site-footer__inner">
			<div class="annam-site-footer__grid">

				<div class="annam-site-footer__col annam-site-footer__col--company">
					<p class="annam-site-footer__company-name"><?php echo esc_html__( 'CÔNG TY CỔ PHẦN AN NAM DISCOVERY', 'generatepress_child' ); ?></p>

					<div class="annam-site-footer__fact annam-site-footer__fact--legal">
						<span class="annam-site-footer__fact-icon">
							<?php echo annam_footer_icon_svg( 'document' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh tin cậy. ?>
						</span>
						<div class="annam-site-footer__fact-body">
							<p class="annam-site-footer__company-text"><?php echo esc_html__( 'Giấy phép kinh doanh dịch vụ lữ hành quốc tế', 'generatepress_child' ); ?></p>
							<p class="annam-site-footer__company-accent"><?php echo esc_html__( 'Số: 01-3006/2025/CDL-GVN-GP LHQT', 'generatepress_child' ); ?></p>
						</div>
					</div>

					<div class="annam-site-footer__fact annam-site-footer__fact--legal">
						<span class="annam-site-footer__fact-icon">
							<?php echo annam_footer_icon_svg( 'tax' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh tin cậy. ?>
						</span>
						<div class="annam-site-footer__fact-body">
							<p class="annam-site-footer__company-accent"><?php echo esc_html__( 'Mã số thuế: 0111205475', 'generatepress_child' ); ?></p>
						</div>
					</div>

					<div class="annam-site-footer__company-block annam-site-footer__company-block--address">
						<p class="annam-site-footer__company-label"><?php echo esc_html__( 'Địa chỉ', 'generatepress_child' ); ?></p>
						<?php if ( $url_tu_mo !== '' ) : ?>
							<div class="annam-site-footer__fact annam-site-footer__fact--address">
								<span class="annam-site-footer__fact-icon">
									<?php echo annam_footer_icon_svg( 'location' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh tin cậy. ?>
								</span>
								<div class="annam-site-footer__fact-body">
									<a class="annam-site-footer__addr-link" href="<?php echo esc_url( $url_tu_mo ); ?>" target="_blank" rel="noopener"><?php echo esc_html__( '23 Tú Mỡ, Phường Yên Hòa, Tp Hà Nội', 'generatepress_child' ); ?></a>
								</div>
							</div>
						<?php else : ?>
							<div class="annam-site-footer__fact annam-site-footer__fact--address">
								<span class="annam-site-footer__fact-icon">
									<?php echo annam_footer_icon_svg( 'location' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh tin cậy. ?>
								</span>
								<div class="annam-site-footer__fact-body">
									<span class="annam-site-footer__company-text"><?php echo esc_html__( '23 Tú Mỡ, Phường Yên Hòa, Tp Hà Nội', 'generatepress_child' ); ?></span>
								</div>
							</div>
						<?php endif; ?>
						<?php if ( $url_hoan_kiem !== '' ) : ?>
							<div class="annam-site-footer__fact annam-site-footer__fact--address">
								<span class="annam-site-footer__fact-icon">
									<?php echo annam_footer_icon_svg( 'location' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh tin cậy. ?>
								</span>
								<div class="annam-site-footer__fact-body">
									<a class="annam-site-footer__addr-link" href="<?php echo esc_url( $url_hoan_kiem ); ?>" target="_blank" rel="noopener"><?php echo esc_html__( '214 Đ. Trần Quang Khải, Hoàn Kiếm, Hà Nội', 'generatepress_child' ); ?></a>
								</div>
							</div>
						<?php else : ?>
							<div class="annam-site-footer__fact annam-site-footer__fact--address">
								<span class="annam-site-footer__fact-icon">
									<?php echo annam_footer_icon_svg( 'location' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh tin cậy. ?>
								</span>
								<div class="annam-site-footer__fact-body">
									<span class="annam-site-footer__company-text"><?php echo esc_html__( '214 Đ. Trần Quang Khải, Hoàn Kiếm, Hà Nội', 'generatepress_child' ); ?></span>
								</div>
							</div>
						<?php endif; ?>
					</div>

					<div class="annam-site-footer__fact">
						<span class="annam-site-footer__fact-icon">
							<?php echo annam_footer_icon_svg( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh tin cậy. ?>
						</span>
						<div class="annam-site-footer__fact-body annam-site-footer__fact-body--inline">
							<span class="annam-site-footer__company-label"><?php echo esc_html__( 'Tổng đài', 'generatepress_child' ); ?></span>
							<span class="annam-site-footer__company-text">
								<a href="<?php echo esc_url( 'tel:19008164' ); ?>"><?php echo esc_html__( '1900 8164', 'generatepress_child' ); ?></a>
							</span>
						</div>
					</div>

					<div class="annam-site-footer__fact">
						<span class="annam-site-footer__fact-icon">
							<?php echo annam_footer_icon_svg( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh tin cậy. ?>
						</span>
						<div class="annam-site-footer__fact-body annam-site-footer__fact-body--inline">
							<span class="annam-site-footer__company-label"><?php echo esc_html__( 'Hotline', 'generatepress_child' ); ?></span>
							<span class="annam-site-footer__company-text">
								<a href="<?php echo esc_url( 'tel:+84942471111' ); ?>"><?php echo esc_html__( '094 247 1111', 'generatepress_child' ); ?></a>
							</span>
						</div>
					</div>

					<div class="annam-site-footer__fact annam-site-footer__fact--email">
						<span class="annam-site-footer__fact-icon">
							<?php echo annam_footer_icon_svg( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh tin cậy. ?>
						</span>
						<div class="annam-site-footer__fact-body annam-site-footer__fact-body--inline">
							<span class="annam-site-footer__company-label"><?php echo esc_html__( 'Email', 'generatepress_child' ); ?></span>
							<span class="annam-site-footer__company-text">
								<a href="<?php echo esc_url( 'mailto:annamdiscoveryvn@gmail.com' ); ?>">annamdiscoveryvn@gmail.com</a>
							</span>
						</div>
					</div>
				</div>

				<?php if ( $show_tours ) : ?>
				<div class="annam-site-footer__col annam-site-footer__col--tours">
					<h2 class="annam-site-footer__heading"><?php echo esc_html__( 'Tour phổ biến', 'generatepress_child' ); ?></h2>
					<ul class="annam-site-footer__list">
						<?php foreach ( $tour_links as $item ) : ?>
							<?php
							$link_class = ( isset( $item['variant'] ) && 'more' === $item['variant'] ) ? 'annam-site-footer__list-link--more' : '';
							?>
							<li>
								<a
									href="<?php echo esc_url( $item['url'] ); ?>"
									<?php echo '' !== $link_class ? ' class="' . esc_attr( $link_class ) . '"' : ''; ?>
								><?php echo esc_html( $item['label'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>

				<div class="annam-site-footer__col annam-site-footer__col--info">
					<h2 class="annam-site-footer__heading"><?php echo esc_html__( 'Thông tin', 'generatepress_child' ); ?></h2>
					<ul class="annam-site-footer__list">
						<?php foreach ( $info_links as $item ) : ?>
							<li>
								<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="annam-site-footer__col annam-site-footer__col--follow">
					<h2 class="annam-site-footer__heading"><?php echo esc_html__( 'Theo dõi Annam', 'generatepress_child' ); ?></h2>
					<ul class="annam-site-footer__social" aria-label="<?php echo esc_attr__( 'Mạng xã hội', 'generatepress_child' ); ?>">
						<?php foreach ( $social as $soc ) : ?>
							<li>
								<a
									class="annam-site-footer__social-link annam-site-footer__social-link--<?php echo esc_attr( $soc['key'] ); ?>"
									href="<?php echo esc_url( $soc['url'] ); ?>"
									target="_blank"
									rel="noopener noreferrer"
									aria-label="<?php echo esc_attr( $soc['label'] ); ?>"
								>
									<?php
									switch ( $soc['key'] ) {
										case 'facebook':
											?>
											<svg class="annam-site-footer__social-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M24 12.073C24 5.446 18.627 0 12 0S0 5.446 0 12.073c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
											<?php
											break;
										case 'twitter':
											?>
											<svg class="annam-site-footer__social-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
											<?php
											break;
										case 'youtube':
											?>
											<svg class="annam-site-footer__social-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
											<?php
											break;
										case 'instagram':
										default:
											?>
											<svg class="annam-site-footer__social-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
											<?php
											break;
									}
									?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>

					<h2 class="annam-site-footer__heading annam-site-footer__heading--sub"><?php echo esc_html__( 'Kênh thanh toán', 'generatepress_child' ); ?></h2>
					<div class="annam-site-footer__payment">
						<img
							src="<?php echo esc_url( $pay_src ); ?>"
							alt="<?php echo esc_attr__( 'Kênh thanh toán', 'generatepress_child' ); ?>"
							width="400"
							height="48"
							loading="lazy"
							decoding="async"
						/>
					</div>

					<?php if ( $sale_noti_src !== '' ) : ?>
						<div class="annam-site-footer__sale-noti-wrap">
							<img
								class="annam-site-footer__sale-noti"
								src="<?php echo esc_url( $sale_noti_src ); ?>"
								alt="<?php echo esc_attr__( 'Thông báo chương trình khuyến mãi', 'generatepress_child' ); ?>"
								width="800"
								height="160"
								loading="lazy"
								decoding="async"
							/>
						</div>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</div>

	<div class="annam-site-footer__bottom">
		<div class="annam-site-footer__inner annam-site-footer__inner--bottom">
			<p class="annam-site-footer__copyright">
				<?php echo esc_html__( 'Copyright © 2026 Annam Discovery Theme. All Rights Reserved.', 'generatepress_child' ); ?>
			</p>
		</div>
	</div>
</footer>
