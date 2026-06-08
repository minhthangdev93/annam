<?php
/**
 * Schema FAQ + ảnh chia sẻ MXH (OG) cho landing thuê xe.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL ảnh hero meta box (slot hero) — chỉ khi admin đã gán; không fallback ảnh đại diện.
 *
 * @param int $post_id Post ID; 0 = trang hiện tại.
 * @return string
 */
function annam_car_rental_get_hero_share_image_url( $post_id = 0 ) {
	if ( ! function_exists( 'annam_car_rental_get_landing_image_url' ) ) {
		return '';
	}

	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( $post_id <= 0 ) {
		return '';
	}

	return annam_car_rental_get_landing_image_url( $post_id, 'hero', 'full' );
}

/**
 * Rank Math: dùng ảnh hero làm og:image / twitter:image khi có.
 *
 * @param string $attachment_url URL hiện tại từ Rank Math.
 * @return string
 */
function annam_car_rental_filter_rank_math_og_image( $attachment_url ) {
	if ( ! function_exists( 'annam_car_rental_is_landing_template' ) || ! annam_car_rental_is_landing_template() ) {
		return $attachment_url;
	}

	$hero_url = annam_car_rental_get_hero_share_image_url();
	return '' !== $hero_url ? $hero_url : $attachment_url;
}
add_filter( 'rank_math/opengraph/facebook/image', 'annam_car_rental_filter_rank_math_og_image', 20 );
add_filter( 'rank_math/opengraph/twitter/image', 'annam_car_rental_filter_rank_math_og_image', 20 );

/**
 * Fallback og:image khi không có Rank Math.
 */
function annam_car_rental_output_social_hero_meta() {
	if ( defined( 'RANK_MATH_VERSION' ) ) {
		return;
	}

	if ( ! function_exists( 'annam_car_rental_is_landing_template' ) || ! annam_car_rental_is_landing_template() ) {
		return;
	}

	$hero_url = annam_car_rental_get_hero_share_image_url();
	if ( '' === $hero_url ) {
		return;
	}

	echo '<meta property="og:image" content="' . esc_url( $hero_url ) . '" />' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	echo '<meta name="twitter:image" content="' . esc_url( $hero_url ) . '" />' . "\n";
}
add_action( 'wp_head', 'annam_car_rental_output_social_hero_meta', 5 );

/**
 * Output FAQPage JSON-LD.
 */
function annam_car_rental_output_faq_schema() {
	if ( ! function_exists( 'annam_car_rental_is_landing_template' ) || ! annam_car_rental_is_landing_template() ) {
		return;
	}

	$config = annam_car_rental_get_landing_config();
	$faq    = isset( $config['faq'] ) ? $config['faq'] : array();
	if ( empty( $faq ) ) {
		return;
	}

	$entities = array();
	foreach ( $faq as $item ) {
		if ( empty( $item['question'] ) || empty( $item['answer'] ) ) {
			continue;
		}
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $item['question'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $item['answer'],
			),
		);
	}

	if ( empty( $entities ) ) {
		return;
	}

	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'annam_car_rental_output_faq_schema', 20 );
