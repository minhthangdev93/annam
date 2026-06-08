<?php
/**
 * JSON-LD FAQPage cho trang Liên hệ (đồng bộ annam_contact_get_faq_items).
 *
 * Tắt: add_filter( 'annam_contact_faq_schema_print', '__return_false' );
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string, mixed>|null
 */
function annam_get_contact_faq_schema() {
	if ( ! function_exists( 'annam_contact_get_faq_items' ) ) {
		return null;
	}

	$items = annam_contact_get_faq_items();
	if ( empty( $items ) ) {
		return null;
	}

	$main_entity = array();
	foreach ( $items as $item ) {
		$question = isset( $item['question'] ) ? wp_strip_all_tags( (string) $item['question'] ) : '';
		$answer   = isset( $item['answer'] ) ? wp_strip_all_tags( (string) $item['answer'] ) : '';
		if ( '' === $question || '' === $answer ) {
			continue;
		}
		$main_entity[] = array(
			'@type'          => 'Question',
			'name'           => $question,
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $answer,
			),
		);
	}

	if ( empty( $main_entity ) ) {
		return null;
	}

	$page_url = get_permalink();
	$schema   = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $main_entity,
	);

	if ( is_string( $page_url ) && '' !== $page_url ) {
		$schema['@id']  = trailingslashit( $page_url ) . '#faq';
		$schema['url']  = $page_url;
		$schema['name'] = __( 'Câu hỏi thường gặp — Liên hệ An Nam Discovery', 'generatepress_child' );
		if ( function_exists( 'annam_schema_brand_url' ) ) {
			$schema['isPartOf'] = array(
				'@id' => annam_schema_brand_url() . '#website',
			);
		}
	}

	return apply_filters( 'annam_contact_faq_schema', $schema );
}

/**
 * In FAQPage JSON-LD trên template Liên hệ.
 */
function annam_print_contact_faq_json_ld() {
	if ( ! apply_filters( 'annam_contact_faq_schema_print', true ) ) {
		return;
	}
	if ( ! function_exists( 'annam_contact_is_contact_template' ) || ! annam_contact_is_contact_template() ) {
		return;
	}

	$schema = annam_get_contact_faq_schema();
	if ( empty( $schema['mainEntity'] ) ) {
		return;
	}

	$json = wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	if ( ! is_string( $json ) || '' === $json ) {
		return;
	}

	echo "\n<script type=\"application/ld+json\">\n" . $json . "\n</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'annam_print_contact_faq_json_ld', 7 );
