<?php
/**
 * Header tùy chỉnh (child theme): thay header + navigation mặc định GeneratePress.
 *
 * Menu chính: Giao diện → Menu → gán menu vào vị trí « An Nam Primary Menu ».
 * Mega « Danh mục tour » (chỉ desktop): với mục cha trong menu, bật « CSS Classes »
 * (Screen Options) và thêm class: annam-mega-tour. Các mục con trong admin vẫn dùng
 * cho mobile (accordion); desktop không render cây con đó mà mở panel danh mục WC.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/class-annam-primary-menu-walkers.php';

/**
 * Đăng ký một vị trí menu chính dùng chung desktop + mobile drawer.
 */
function annam_site_header_register_nav() {
	register_nav_menus(
		array(
			'annam_primary_menu' => __( 'An Nam Primary Menu', 'generatepress_child' ),
		)
	);
}
add_action( 'after_setup_theme', 'annam_site_header_register_nav', 20 );

/**
 * Thêm class BEM cho menu desktop / mobile (theo ngữ cảnh render).
 *
 * @param string[] $classes   Classes.
 * @param WP_Post  $menu_item Menu item.
 * @param object   $args      wp_nav_menu args object.
 * @param int      $depth     Depth.
 * @return string[]
 */
function annam_primary_nav_menu_css_class( $classes, $menu_item, $args, $depth ) {
	if ( empty( $args->annam_nav_context ) ) {
		return $classes;
	}
	if ( 'primary_desktop' === $args->annam_nav_context && 0 === (int) $depth ) {
		$classes[] = 'annam-site-header__item';
		if ( in_array( 'annam-mega-tour', $classes, true ) ) {
			$classes[] = 'annam-site-header__item--has-mega';
		}
	}
	if ( 'primary_mobile' === $args->annam_nav_context ) {
		$classes[] = 'annam-site-header__drawer-item';
		$classes[] = 'annam-site-header__drawer-item--depth-' . (int) $depth;
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'annam_primary_nav_menu_css_class', 10, 4 );

/**
 * Menu fallback khi chưa gán menu trong Appearance → Menus.
 */
function annam_site_header_nav_fallback( $args = array() ) {
	$menu_id = ( is_array( $args ) && ! empty( $args['menu_id'] ) ) ? (string) $args['menu_id'] : 'annam-site-header-fallback-menu';
	$shop_url = ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' );
	?>
	<ul id="<?php echo esc_attr( $menu_id ); ?>" class="annam-site-header__menu">
		<li class="annam-site-header__item">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html__( 'Trang chủ', 'generatepress_child' ); ?></a>
		</li>
		<li class="annam-site-header__item annam-site-header__item--has-mega">
			<button type="button" class="annam-site-header__mega-trigger annam-site-header__mega-trigger--btn" aria-expanded="false" aria-haspopup="true" aria-controls="annam-mega-panel-fallback" id="annam-mega-trigger-fallback">
				<?php echo esc_html__( 'Danh mục tour', 'generatepress_child' ); ?>
			</button>
			<?php annam_site_header_render_mega_panel( 'annam-mega-panel-fallback' ); ?>
		</li>
		<li class="annam-site-header__item">
			<a href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html__( 'Tất cả tour', 'generatepress_child' ); ?></a>
		</li>
	</ul>
	<?php
}

/**
 * Fallback menu trong drawer mobile khi chưa gán menu tại An Nam Primary Menu.
 *
 * @param array<string,mixed> $args wp_nav_menu args.
 */
function annam_site_header_nav_fallback_mobile( $args = array() ) {
	$menu_id  = ( is_array( $args ) && ! empty( $args['menu_id'] ) ) ? (string) $args['menu_id'] : 'annam-primary-menu-mobile-fallback';
	$shop_url = ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' );
	$sub_id   = 'annam-drawer-sub-fallback-mega';
	?>
	<ul id="<?php echo esc_attr( $menu_id ); ?>" class="annam-site-header__drawer-menu">
		<li class="menu-item menu-item-home annam-site-header__drawer-item annam-site-header__drawer-item--depth-0">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html__( 'Trang chủ', 'generatepress_child' ); ?></a>
		</li>
		<li class="menu-item menu-item-has-children annam-site-header__drawer-item annam-site-header__drawer-item--depth-0">
			<div class="annam-site-header__drawer-row">
				<span class="annam-site-header__drawer-fallback-label"><?php echo esc_html__( 'Danh mục tour', 'generatepress_child' ); ?></span>
				<button type="button" class="annam-site-header__drawer-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $sub_id ); ?>" aria-label="<?php echo esc_attr__( 'Mở danh mục tour', 'generatepress_child' ); ?>">
					<span class="annam-site-header__drawer-toggle-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></span>
				</button>
			</div>
			<ul id="<?php echo esc_attr( $sub_id ); ?>" class="annam-site-header__drawer-sub sub-menu" hidden aria-hidden="true">
				<?php
				if ( taxonomy_exists( 'product_cat' ) ) {
					$terms = get_terms(
						array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => true,
							'parent'     => 0,
							'number'     => 24,
							'orderby'    => 'menu_order',
							'order'      => 'ASC',
						)
					);
					if ( ! is_wp_error( $terms ) ) {
						foreach ( $terms as $term ) {
							if ( ! $term instanceof WP_Term || 'uncategorized' === $term->slug ) {
								continue;
							}
							$link = get_term_link( $term );
							if ( is_wp_error( $link ) ) {
								continue;
							}
							echo '<li class="annam-site-header__drawer-item annam-site-header__drawer-item--depth-1 menu-item"><a href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a></li>';
						}
					}
				}
				?>
			</ul>
		</li>
		<li class="menu-item annam-site-header__drawer-item annam-site-header__drawer-item--depth-0">
			<a href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html__( 'Tất cả tour', 'generatepress_child' ); ?></a>
		</li>
	</ul>
	<?php
}

/**
 * Panel mega: danh mục sản phẩm cấp 1 (WooCommerce).
 *
 * @param string $panel_id ID duy nhất cho aria-controls.
 */
function annam_site_header_render_mega_panel( $panel_id ) {
	$cats = array();
	if ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'parent'     => 0,
				'number'     => 16,
				'orderby'    => 'menu_order',
				'order'      => 'ASC',
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( ! $term instanceof WP_Term || 'uncategorized' === $term->slug ) {
					continue;
				}
				$cats[] = $term;
			}
		}
	}
	?>
	<div class="annam-site-header__mega" id="<?php echo esc_attr( $panel_id ); ?>" role="region" aria-label="<?php echo esc_attr__( 'Danh mục tour', 'generatepress_child' ); ?>">
		<div class="annam-site-header__mega-inner annam-container grid-container grid-parent">
			<ul class="annam-site-header__mega-grid">
				<?php foreach ( $cats as $term ) : ?>
					<?php
					$link = get_term_link( $term );
					if ( is_wp_error( $link ) ) {
						continue;
					}
					?>
					<li class="annam-site-header__mega-item">
						<a class="annam-site-header__mega-link" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term->name ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<?php
}

/**
 * Gỡ header + navigation + top bar GP (frontend) để tránh trùng với header custom.
 */
function annam_site_header_remove_generate_defaults() {
	if ( is_admin() ) {
		return;
	}

	remove_action( 'generate_header', 'generate_construct_header', 10 );
	remove_action( 'generate_after_header', 'generate_add_navigation_after_header', 5 );
	remove_action( 'generate_before_header', 'generate_add_navigation_before_header', 5 );
	remove_action( 'generate_after_header_content', 'generate_add_navigation_float_right', 5 );
	remove_action( 'generate_before_right_sidebar_content', 'generate_add_navigation_before_right_sidebar', 5 );
	remove_action( 'generate_before_left_sidebar_content', 'generate_add_navigation_before_left_sidebar', 5 );
	remove_action( 'generate_before_header', 'generate_top_bar', 5 );
}
add_action( 'wp', 'annam_site_header_remove_generate_defaults', 4 );

/**
 * Render header custom.
 */
function annam_site_header_render_custom() {
	if ( is_admin() ) {
		return;
	}
	get_template_part( 'template-parts/header/site-header', 'custom' );
}
add_action( 'generate_header', 'annam_site_header_render_custom', 10 );

/**
 * Body class để style / sticky tách biệt.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function annam_site_header_body_class( $classes ) {
	if ( ! is_admin() ) {
		$classes[] = 'annam-has-custom-header';
	}
	return $classes;
}
add_filter( 'body_class', 'annam_site_header_body_class' );

/**
 * Enqueue CSS/JS header custom (frontend).
 */
function annam_site_header_enqueue_assets() {
	if ( is_admin() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	$deps = array( 'annam-design-tokens' );

	$css = $dir . '/assets/css/header-custom.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'annam-header-custom',
			$uri . '/assets/css/header-custom.css',
			$deps,
			(string) filemtime( $css )
		);
	}

	$js = $dir . '/assets/js/header-custom.js';
	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'annam-header-custom',
			$uri . '/assets/js/header-custom.js',
			array(),
			(string) filemtime( $js ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'annam_site_header_enqueue_assets', 15 );
