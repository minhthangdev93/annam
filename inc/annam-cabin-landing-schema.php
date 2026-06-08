<?php
/**
 * JSON-LD: Service + Offer, FAQPage, BreadcrumbList cho landing cabin VIP.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string,mixed>|null
 */
function annam_cabin_landing_get_schema_graph() {
	if ( ! function_exists( 'annam_cabin_landing_is_template' ) || ! annam_cabin_landing_is_template() ) {
		return null;
	}

	$config  = annam_cabin_landing_get_config();
	$cta     = annam_cabin_landing_get_cta();
	$page_url = get_permalink();
	$brand_url = function_exists( 'annam_schema_brand_url' ) ? annam_schema_brand_url() : trailingslashit( home_url( '/' ) );
	$org_id    = $brand_url . '#travel-agency';

	$offers = array();
	if ( ! empty( $config['pricing'] ) && is_array( $config['pricing'] ) ) {
		foreach ( $config['pricing'] as $route_key => $rows ) {
			if ( ! is_array( $rows ) || 'price_note' === $route_key ) {
				continue;
			}
			foreach ( $rows as $row ) {
				if ( empty( $row['label'] ) || empty( $row['price'] ) ) {
					continue;
				}
				$offers[] = array(
					'@type'           => 'Offer',
					'name'            => (string) $row['label'] . ' — ' . (string) $route_key,
					'price'           => preg_replace( '/[^\d]/', '', (string) $row['price'] ),
					'priceCurrency'   => 'VND',
					'url'             => $page_url,
					'availability'    => 'https://schema.org/InStock',
					'itemCondition'   => 'https://schema.org/NewCondition',
					'priceValidUntil' => gmdate( 'Y-12-31' ),
				);
			}
		}
	}

	$service = array(
		'@type'       => 'Service',
		'@id'         => trailingslashit( (string) $page_url ) . '#cabin-vip-service',
		'name'        => isset( $config['product_name'] ) ? $config['product_name'] . ' — Hà Nội Sapa / Lào Cai' : 'Xe Cabin VIP 22 phòng',
		'description' => isset( $config['hero']['subtitle'] ) ? wp_strip_all_tags( (string) $config['hero']['subtitle'] ) : '',
		'url'         => $page_url,
		'provider'    => array( '@id' => $org_id ),
		'areaServed'  => array(
			array( '@type' => 'City', 'name' => 'Hà Nội' ),
			array( '@type' => 'City', 'name' => 'Sa Pa' ),
			array( '@type' => 'City', 'name' => 'Lào Cai' ),
		),
	);
	if ( ! empty( $offers ) ) {
		$service['offers'] = $offers;
	}

	$graph = array( $service );

	if ( ! empty( $config['faq'] ) && is_array( $config['faq'] ) ) {
		$entities = array();
		foreach ( $config['faq'] as $item ) {
			$q = isset( $item['question'] ) ? wp_strip_all_tags( (string) $item['question'] ) : '';
			$a = isset( $item['answer'] ) ? wp_strip_all_tags( (string) $item['answer'] ) : '';
			if ( '' === $q || '' === $a ) {
				continue;
			}
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => $q,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $a,
				),
			);
		}
		if ( ! empty( $entities ) ) {
			$graph[] = array(
				'@type'      => 'FAQPage',
				'@id'        => trailingslashit( (string) $page_url ) . '#faq',
				'url'        => $page_url . '#faq',
				'mainEntity' => $entities,
			);
		}
	}

	$graph[] = array(
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => __( 'Trang chủ', 'generatepress_child' ),
				'item'     => $brand_url,
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => isset( $config['hero']['title'] ) ? wp_strip_all_tags( (string) $config['hero']['title'] ) : 'Cabin VIP',
				'item'     => $page_url,
			),
		),
	);

	return apply_filters(
		'annam_cabin_landing_schema_graph',
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		)
	);
}

function annam_cabin_landing_print_schema() {
	if ( ! apply_filters( 'annam_cabin_landing_schema_print', true ) ) {
		return;
	}
	$data = annam_cabin_landing_get_schema_graph();
	if ( empty( $data['@graph'] ) ) {
		return;
	}
	$json = wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	if ( ! is_string( $json ) || '' === $json ) {
		return;
	}
	echo "\n<script type=\"application/ld+json\">\n" . $json . "\n</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'annam_cabin_landing_print_schema', 8 );
