<?php
/**
 * Header site tùy chỉnh (GeneratePress child).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

$tel_label = '1900 8164';
$tel_href  = 'tel:19008164';
$zalo_url  = 'https://zalo.me/0942471111';
$site_name = get_bloginfo( 'name' );
$logo_svg  = function_exists( 'strip_theme_get_svg' ) ? trim( strip_theme_get_svg( 'logo_full' ) ) : '';

if ( '' !== $logo_svg ) {
	$logo_svg = preg_replace(
		'/<svg\b([^>]*)>/',
		'<svg$1 class="annam-site-header__logo-svg" aria-hidden="true" focusable="false">',
		$logo_svg,
		1
	);
}
?>
<header <?php generate_do_attr( 'header', array( 'class' => 'annam-site-header ' ) ); ?>>
	<div class="annam-site-header__inner">
		<!-- Hàng 1: desktop -->
		<div class="annam-site-header__row annam-site-header__row--top annam-site-header__row--desktop">
			<div class="annam-site-header__container annam-container grid-container grid-parent">
				<div class="annam-site-header__logo">
					<?php
					if ( '' !== $logo_svg ) {
						?>
						<a class="custom-logo-link annam-site-header__logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $site_name ); ?>">
							<?php echo $logo_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
						<?php
					} elseif ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
						the_custom_logo();
					} else {
						?>
						<a class="annam-site-header__title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $site_name ); ?></a>
						<?php
					}
					?>
				</div>

				<div class="annam-site-header__search annam-site-header__search--desktop">
					<?php
					if ( function_exists( 'get_product_search_form' ) ) {
						get_product_search_form();
					} else {
						get_search_form();
					}
					?>
				</div>

				<div class="annam-site-header__gtranslate annam-site-header__gtranslate--desktop" aria-label="<?php echo esc_attr__( 'Chọn ngôn ngữ', 'generatepress_child' ); ?>">
					<?php echo do_shortcode( '[annam_language_switcher class="annam-language-switcher--header annam-language-switcher--header-desktop"]' ); ?>
				</div>

				<div class="annam-site-header__actions annam-site-header__actions--desktop">
					<a class="annam-site-header__action annam-site-header__action--call" href="<?php echo esc_url( $tel_href ); ?>">
						<span class="annam-site-header__action-icon" aria-hidden="true">☎</span>
						<span class="annam-site-header__action-text"><?php echo esc_html__( 'Gọi ngay', 'generatepress_child' ); ?></span>
						<span class="annam-site-header__action-num"><?php echo esc_html( $tel_label ); ?></span>
					</a>
					<a class="annam-site-header__action annam-site-header__action--zalo" href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="annam-site-header__action-icon annam-site-header__action-icon--zalo" aria-hidden="true"></span>
						<span class="annam-site-header__action-text"><?php echo esc_html__( 'Tư vấn Zalo', 'generatepress_child' ); ?></span>
					</a>
				</div>
			</div>
		</div>

		<!-- Hàng 1: mobile -->
		<div class="annam-site-header__row annam-site-header__row--top annam-site-header__row--mobile">
			<div class="annam-site-header__container annam-site-header__container--mobile annam-container grid-container grid-parent">
				<div class="annam-site-header__logo annam-site-header__logo--mobile">
					<?php
					if ( '' !== $logo_svg ) {
						?>
						<a class="custom-logo-link annam-site-header__logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $site_name ); ?>">
							<?php echo $logo_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
						<?php
					} elseif ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
						the_custom_logo();
					} else {
						?>
						<a class="annam-site-header__title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $site_name ); ?></a>
						<?php
					}
					?>
				</div>
				<div class="annam-site-header__mobile-tools">
					<div class="annam-site-header__gtranslate annam-site-header__gtranslate--mobile">
						<?php echo do_shortcode( '[annam_language_switcher class="annam-language-switcher--header annam-language-switcher--header-mobile"]' ); ?>
					</div>
					<button type="button" class="annam-site-header__icon-btn" data-annam-header-search aria-expanded="false" aria-controls="annam-site-header-search-panel" aria-label="<?php echo esc_attr__( 'Mở tìm kiếm', 'generatepress_child' ); ?>">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
					</button>
					<button type="button" class="annam-site-header__icon-btn" data-annam-header-menu aria-expanded="false" aria-controls="annam-site-header-drawer" aria-label="<?php echo esc_attr__( 'Mở menu', 'generatepress_child' ); ?>">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
					</button>
				</div>
			</div>
		</div>

		<!-- Ô tìm kiếm mobile (mở bằng icon) -->
		<div id="annam-site-header-search-panel" class="annam-site-header__search-panel" hidden>
			<div class="annam-site-header__container annam-container grid-container grid-parent">
				<?php
				if ( function_exists( 'get_product_search_form' ) ) {
					get_product_search_form();
				} else {
					get_search_form();
				}
				?>
			</div>
		</div>

		<!-- Hàng 2: menu desktop + mega -->
		<div class="annam-site-header__row annam-site-header__row--nav annam-site-header__row--desktop">
			<div class="annam-site-header__container annam-site-header__container--nav annam-container grid-container grid-parent">
				<nav class="annam-site-header__nav" aria-label="<?php echo esc_attr__( 'Menu chính', 'generatepress_child' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location'    => 'annam_primary_menu',
							'container'         => false,
							'menu_id'           => 'annam-primary-menu-desktop',
							'menu_class'        => 'annam-site-header__menu',
							'fallback_cb'       => 'annam_site_header_nav_fallback',
							'depth'             => 0,
							'item_spacing'      => 'discard',
							'walker'            => new Annam_Primary_Menu_Walker_Desktop(),
							'annam_nav_context' => 'primary_desktop',
							'items_wrap'        => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						)
					);
					?>
				</nav>
			</div>
		</div>
	</div>

	<!-- Off-canvas mobile -->
	<div id="annam-site-header-drawer" class="annam-site-header__drawer" hidden aria-hidden="true">
		<div class="annam-site-header__drawer-backdrop" data-annam-header-close tabindex="-1"></div>
		<div class="annam-site-header__drawer-panel" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__( 'Menu', 'generatepress_child' ); ?>">
			<div class="annam-site-header__drawer-head">
				<span class="annam-site-header__drawer-title"><?php echo esc_html__( 'Menu', 'generatepress_child' ); ?></span>
				<button type="button" class="annam-site-header__drawer-close" data-annam-header-close aria-label="<?php echo esc_attr__( 'Đóng menu', 'generatepress_child' ); ?>">×</button>
			</div>
			<div class="annam-site-header__drawer-body">
				<nav class="annam-site-header__drawer-nav" aria-label="<?php echo esc_attr__( 'Menu chính', 'generatepress_child' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location'    => 'annam_primary_menu',
							'container'         => false,
							'menu_id'           => 'annam-primary-menu-mobile',
							'menu_class'        => 'annam-site-header__drawer-menu',
							'fallback_cb'       => 'annam_site_header_nav_fallback_mobile',
							'depth'             => 0,
							'item_spacing'      => 'discard',
							'walker'            => new Annam_Primary_Menu_Walker_Mobile(),
							'annam_nav_context' => 'primary_mobile',
							'items_wrap'        => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						)
					);
					?>
				</nav>
				<div class="annam-site-header__drawer-actions" aria-label="<?php echo esc_attr__( 'Liên hệ nhanh', 'generatepress_child' ); ?>">
					<a class="annam-site-header__action annam-site-header__action--call annam-site-header__action--drawer" href="<?php echo esc_url( $tel_href ); ?>">
						<span class="annam-site-header__action-icon" aria-hidden="true">☎</span>
						<span class="annam-site-header__action-text"><?php echo esc_html__( 'Gọi ngay', 'generatepress_child' ); ?></span>
						<span class="annam-site-header__action-num"><?php echo esc_html( $tel_label ); ?></span>
					</a>
					<a class="annam-site-header__action annam-site-header__action--zalo annam-site-header__action--drawer" href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="annam-site-header__action-icon annam-site-header__action-icon--zalo" aria-hidden="true"></span>
						<span class="annam-site-header__action-text"><?php echo esc_html__( 'Tư vấn Zalo', 'generatepress_child' ); ?></span>
					</a>
				</div>
			</div>
		</div>
	</div>

</header>
