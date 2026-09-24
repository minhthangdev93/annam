<?php
/**
 * JSON-LD TouristTrip + Offer + FAQPage — Tour Sapa 3N2Đ.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string,mixed>|null
 */
function annam_tour_sapa_landing_get_schema_graph() {
	if ( ! function_exists( 'annam_tour_sapa_landing_is_template' ) || ! annam_tour_sapa_landing_is_template() ) {
		return null;
	}

	$config   = annam_tour_sapa_landing_get_config();
	$cta      = annam_tour_sapa_landing_get_cta();
	$page_url = get_permalink();
	$offers   = array();

	if ( ! empty( $config['pricing']['rows'] ) ) {
		foreach ( $config['pricing']['rows'] as $row ) {
			$price = isset( $row['price'] ) ? preg_replace( '/[^\d]/', '', (string) $row['price'] ) : '';
			if ( ! $price ) {
				continue;
			}
			$offers[] = array(
				'@type'           => 'Offer',
				'name'            => isset( $row['label'] ) ? (string) $row['label'] : '',
				'price'           => $price,
				'priceCurrency'   => 'VND',
				'availability'    => 'https://schema.org/InStock',
				'priceValidUntil' => gmdate( 'Y-12-31' ),
			);
		}
	}

	$faq = array();
	if ( ! empty( $config['faq'] ) ) {
		foreach ( $config['faq'] as $item ) {
			$faq[] = array(
				'@type'          => 'Question',
				'name'           => (string) $item['question'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => (string) $item['answer'],
				),
			);
		}
	}

	$graph = array(
		array(
			'@type'       => 'TouristTrip',
			'@id'         => trailingslashit( (string) $page_url ) . '#tour-sapa-3n2d',
			'name'        => isset( $config['product_name'] ) ? $config['product_name'] : 'Tour Sapa 3N2Đ',
			'description' => isset( $config['hero']['subtitle'] ) ? $config['hero']['subtitle'] : '',
			'url'         => $page_url,
			'provider'    => array(
				'@type' => 'Organization',
				'name'  => isset( $cta['brand'] ) ? $cta['brand'] : 'An Nam Discovery',
				'telephone' => isset( $cta['hotline_display'] ) ? $cta['hotline_display'] : '',
			),
			'offers'      => $offers,
		),
	);

	if ( $faq ) {
		$graph[] = array(
			'@type'      => 'FAQPage',
			'@id'        => trailingslashit( (string) $page_url ) . '#faq',
			'mainEntity' => $faq,
		);
	}

	return apply_filters(
		'annam_tour_sapa_landing_schema_graph',
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		)
	);
}

/**
 * Print schema.
 */
function annam_tour_sapa_landing_print_schema() {
	if ( ! apply_filters( 'annam_tour_sapa_landing_schema_print', true ) ) {
		return;
	}
	$data = annam_tour_sapa_landing_get_schema_graph();
	if ( ! $data ) {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'annam_tour_sapa_landing_print_schema', 8 );
