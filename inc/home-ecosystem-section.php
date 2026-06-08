<?php
/**
 * Section “Hệ sinh thái của An Nam Discovery” (slider logo JS).
 *
 * Mặc định in trong footer (`site-footer-custom.php`). Tùy chọn chèn vào nội dung trang chủ:
 * `add_filter( 'annam_ecosystem_insert_in_page_content', '__return_true' );` + marker `<!--annam-ecosystem-->` hoặc H2 “Đối tác của chúng tôi”.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL ảnh logo tạm (có thể ghi đè filter `annam_ecosystem_default_logo_url`).
 *
 * @return string
 */
function annam_ecosystem_default_logo_url() {
	$url = content_url( 'uploads/2026/05/logo-annamdiscovery.png' );
	return apply_filters( 'annam_ecosystem_default_logo_url', $url );
}

/**
 * Danh sách thương hiệu hệ sinh thái (filter `annam_ecosystem_brands`).
 * Mỗi phần tử: name (bắt buộc), url (tùy chọn), logo (URL ảnh, tùy chọn).
 *
 * @return array<int, array{name:string,url?:string,logo?:string}>
 */
function annam_ecosystem_get_brands() {
	if ( function_exists( 'annam_get_ecosystem_items' ) ) {
		$items = annam_get_ecosystem_items();
		if ( ! empty( $items ) ) {
			$brands = array();
			foreach ( $items as $row ) {
				$brands[] = array(
					'name' => $row['name'],
					'url'  => isset( $row['url'] ) ? (string) $row['url'] : '',
					'logo' => isset( $row['logo_url'] ) ? (string) $row['logo_url'] : '',
				);
			}
			return apply_filters( 'annam_ecosystem_brands', $brands );
		}
	}

	return apply_filters( 'annam_ecosystem_brands', array() );
}

/**
 * HTML section (đã escape).
 *
 * @return string
 */
function annam_ecosystem_get_section_html() {
	$brands = annam_ecosystem_get_brands();
	if ( empty( $brands ) ) {
		return '';
	}

	$title    = __( 'Hệ sinh thái của An Nam Discovery', 'generatepress_child' );
	$desc     = __( 'Các thương hiệu và dịch vụ thuộc hệ sinh thái du lịch, vận chuyển và trải nghiệm của chúng tôi.', 'generatepress_child' );
	$interval = (int) apply_filters( 'annam_ecosystem_slider_interval_ms', 3000 );
	$interval = max( 1500, min( 6000, $interval ) );

	ob_start();
	?>
	<section class="annam-ecosystem" aria-labelledby="annam-ecosystem-title">
		<div class="annam-ecosystem__container">
			<h2 id="annam-ecosystem-title" class="annam-ecosystem__title"><?php echo esc_html( $title ); ?></h2>
			<p class="annam-ecosystem__desc"><?php echo esc_html( $desc ); ?></p>
			<div
				class="annam-ecosystem__slider"
				data-annam-ecosystem-slider="1"
				data-interval-ms="<?php echo esc_attr( (string) $interval ); ?>"
			>
				<div class="annam-ecosystem__viewport">
					<div class="annam-ecosystem__track">
						<?php
						foreach ( $brands as $row ) :
							$name = isset( $row['name'] ) ? (string) $row['name'] : '';
							if ( '' === $name ) {
								continue;
							}
							$logo = isset( $row['logo'] ) ? (string) $row['logo'] : '';
							$url  = isset( $row['url'] ) ? (string) $row['url'] : '';
							$url  = trim( $url );
							?>
						<div class="annam-ecosystem__item" data-annam-ecosystem-original="1">
							<?php if ( '' !== $url ) : ?>
							<a class="annam-ecosystem__hit" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener">
								<?php if ( '' !== $logo ) : ?>
								<img class="annam-ecosystem__logo" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" decoding="async" width="120" height="42" />
								<?php else : ?>
								<span class="annam-ecosystem__placeholder"><?php echo esc_html( $name ); ?></span>
								<?php endif; ?>
							</a>
							<?php else : ?>
							<span class="annam-ecosystem__hit annam-ecosystem__hit--static">
								<?php if ( '' !== $logo ) : ?>
								<img class="annam-ecosystem__logo" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" decoding="async" width="120" height="42" />
								<?php else : ?>
								<span class="annam-ecosystem__placeholder"><?php echo esc_html( $name ); ?></span>
								<?php endif; ?>
							</span>
							<?php endif; ?>
						</div>
							<?php
						endforeach;
						?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}

/**
 * Chèn section vào the_content (mặc định tắt — section hiển thị trong footer).
 * Bật: add_filter( 'annam_ecosystem_insert_in_page_content', '__return_true' );
 *
 * @param string $content Nội dung.
 * @return string
 */
function annam_ecosystem_insert_before_partners( $content ) {
	if ( is_admin() || wp_is_json_request() ) {
		return $content;
	}
	if ( ! apply_filters( 'annam_ecosystem_insert_in_page_content', false ) ) {
		return $content;
	}
	if ( ! function_exists( 'annam_is_page_template_trang_chu' ) || ! annam_is_page_template_trang_chu() ) {
		return $content;
	}
	if ( ! is_main_query() ) {
		return $content;
	}

	static $did = false;
	if ( $did ) {
		return $content;
	}

	$html = annam_ecosystem_get_section_html();
	if ( '' === $html ) {
		return $content;
	}

	$marker = '<!--annam-ecosystem-->';
	$pos    = strpos( $content, $marker );
	if ( false !== $pos ) {
		$did = true;
		return substr_replace( $content, $html, $pos, strlen( $marker ) );
	}

	$pattern = '/(?=<h2\b[^>]*>[\s\S]*?Đối\s*tác\s+của\s+chúng\s+tôi[\s\S]*?<\/h2>)/iu';
	$new     = preg_replace( $pattern, $html, $content, 1, $count );
	if ( $count > 0 ) {
		$did = true;
		return $new;
	}

	return $content;
}
add_filter( 'the_content', 'annam_ecosystem_insert_before_partners', 9 );

/**
 * Enqueue JS slider (một lần mỗi request).
 *
 * @return void
 */
function annam_ecosystem_enqueue_slider_script() {
	if ( is_admin() ) {
		return;
	}
	$dir  = get_stylesheet_directory();
	$uri  = get_stylesheet_directory_uri();
	$path = $dir . '/assets/js/home-ecosystem-slider.js';
	if ( ! file_exists( $path ) ) {
		return;
	}
	if ( wp_script_is( 'annam-home-ecosystem-slider', 'enqueued' ) || wp_script_is( 'annam-home-ecosystem-slider', 'done' ) ) {
		return;
	}
	wp_enqueue_script(
		'annam-home-ecosystem-slider',
		$uri . '/assets/js/home-ecosystem-slider.js',
		array(),
		(string) filemtime( $path ),
		true
	);
	wp_script_add_data( 'annam-home-ecosystem-slider', 'strategy', 'defer' );
}

/**
 * Enqueue CSS + JS (gọi từ annam_enqueue_trang_chu_template_assets).
 *
 * @return void
 */
function annam_ecosystem_enqueue_trang_chu_assets() {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$css = $dir . '/assets/css/home-ecosystem.css';
	if ( file_exists( $css ) && ! wp_style_is( 'annam-home-ecosystem', 'enqueued' ) && ! wp_style_is( 'annam-home-ecosystem', 'done' ) ) {
		wp_enqueue_style(
			'annam-home-ecosystem',
			$uri . '/assets/css/home-ecosystem.css',
			array( 'annam-design-tokens' ),
			(string) filemtime( $css )
		);
	}
	annam_ecosystem_enqueue_slider_script();
}
