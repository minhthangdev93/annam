<?php
/**
 * Schema FAQ cho landing thuê xe.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

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
