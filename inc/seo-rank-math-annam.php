<?php
/**
 * SEO & JSON-LD bổ sung tương thích Rank Math — An Nam Discovery.
 *
 * Không echo JSON-LD thủ công; chỉ filter Rank Math / WP khi cần.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Danh sách URL sameAs mặc định (bổ sung khi KG Rank Math chưa đủ).
 *
 * @return string[]
 */
function annam_seo_default_same_as_urls() {
	if ( function_exists( 'annam_schema_brand_same_as_urls' ) ) {
		return annam_schema_brand_same_as_urls();
	}

	$urls = array(
		'https://web.facebook.com/AnNamDiscovery/',
		'https://x.com/annamdiscovery',
		'https://www.youtube.com/@AnNamDiscovery',
		'https://www.instagram.com/annamdiscovery/',
	);

	return array_values( array_filter( array_unique( apply_filters( 'annam_seo_default_same_as_urls', $urls ) ) ) );
}

/**
 * Query var không coi là “nhiễu” (phân trang sạch vẫn index).
 *
 * @return string[]
 */
function annam_seo_wc_allowed_query_arg_keys() {
	$keys = array( 'paged', 'page', 'amp', 'lang', 'simply_static_page' );
	return apply_filters( 'annam_seo_wc_allowed_query_arg_keys', $keys );
}

/**
 * Có tham số query cần noindex trên shop / product taxonomy không.
 *
 * @return bool
 */
function annam_seo_wc_archive_has_noisy_query_args() {
	if ( empty( $_GET ) || ! is_array( $_GET ) ) {
		return false;
	}

	$allowed = array_flip( annam_seo_wc_allowed_query_arg_keys() );
	$noisy   = array(
		'orderby',
		'order',
		'min_price',
		'max_price',
		'rating_filter',
		'add-to-cart',
		'stock_status',
		'search_id',
		'v',
	);

	foreach ( array_keys( $_GET ) as $key ) {
		$key = strtolower( (string) $key );
		if ( isset( $allowed[ $key ] ) ) {
			continue;
		}
		if ( 0 === strpos( $key, 'utm_' ) || in_array( $key, array( 'gclid', 'fbclid' ), true ) ) {
			continue;
		}
		if ( in_array( $key, $noisy, true ) ) {
			return true;
		}
		if ( 0 === strpos( $key, 'filter_' ) ) {
			return true;
		}
		if ( 0 === strpos( $key, 'query_type_' ) ) {
			return true;
		}
		if ( 0 === strpos( $key, 'attribute_' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * @param array<string,mixed> $data JSON-LD fragments from Rank Math.
 * @param mixed                 $jsonld JsonLD instance.
 * @return array<string,mixed>
 */
function annam_seo_rank_math_json_ld( $data, $jsonld ) {
	if ( ! is_array( $data ) ) {
		return $data;
	}

	if ( ! empty( $data['publisher'] ) && is_array( $data['publisher'] ) ) {
		$p      =& $data['publisher'];
		$types  = isset( $p['@type'] ) ? (array) $p['@type'] : array();
		$is_org = in_array( 'Organization', $types, true );

		if ( $is_org ) {
			if ( empty( $p['sameAs'] ) || ! is_array( $p['sameAs'] ) ) {
				$p['sameAs'] = annam_seo_default_same_as_urls();
			} else {
				$p['sameAs'] = array_values( array_unique( array_merge( $p['sameAs'], annam_seo_default_same_as_urls() ) ) );
			}

			if ( empty( $p['telephone'] ) ) {
				$p['telephone'] = '+841908164';
			}

			if ( empty( $p['email'] ) ) {
				$p['email'] = 'annamdiscoveryvn@gmail.com';
			}

			if ( empty( $p['address'] ) && function_exists( 'annam_schema_brand_postal_addresses' ) ) {
				$p['address'] = annam_schema_brand_postal_addresses();
			}
		}
	}

	return $data;
}
add_filter( 'rank_math/json_ld', 'annam_seo_rank_math_json_ld', 25, 2 );

/**
 * Trang chủ: bỏ JSON-LD Rank Math trùng với theme (TravelAgency + WebSite).
 *
 * Theme in @graph riêng qua annam_print_homepage_json_ld().
 * Tắt chống trùng: add_filter( 'annam_homepage_schema_suppress_rank_math_duplicates', '__return_false' );
 *
 * @param array<string,mixed> $data   JSON-LD fragments from Rank Math.
 * @param mixed               $jsonld JsonLD instance.
 * @return array<string,mixed>
 */
function annam_seo_rank_math_suppress_homepage_duplicate_schema( $data, $jsonld ) {
	if ( ! apply_filters( 'annam_homepage_schema_suppress_rank_math_duplicates', true ) ) {
		return $data;
	}
	if ( ! apply_filters( 'annam_homepage_schema_print', true ) ) {
		return $data;
	}
	if ( ! function_exists( 'annam_is_homepage_for_schema' ) || ! annam_is_homepage_for_schema() ) {
		return $data;
	}
	if ( ! is_array( $data ) ) {
		return $data;
	}

	$remove_keys = apply_filters(
		'annam_homepage_schema_rank_math_remove_keys',
		array(
			'WebSite',
			'publisher',
			'place',
			'WebPage',
			'primaryImage',
		)
	);

	foreach ( $remove_keys as $key ) {
		unset( $data[ $key ] );
	}

	return $data;
}
add_filter( 'rank_math/json_ld', 'annam_seo_rank_math_suppress_homepage_duplicate_schema', 99, 2 );

/**
 * Breadcrumb Woo: nhãn trang Shop → "Tour du lịch" (chỉ khi dùng breadcrumb Woo fallback).
 *
 * @param array<int,array<string,mixed>> $crumbs Crumbs.
 * @return array<int,array<string,mixed>>
 */
function annam_seo_woocommerce_breadcrumb_defaults( $crumbs ) {
	if ( ! is_array( $crumbs ) || ! function_exists( 'wc_get_page_id' ) ) {
		return $crumbs;
	}
	$shop_id = (int) wc_get_page_id( 'shop' );
	if ( $shop_id <= 0 ) {
		return $crumbs;
	}
	$shop_url = get_permalink( $shop_id );
	if ( ! $shop_url ) {
		return $crumbs;
	}
	foreach ( $crumbs as $i => $c ) {
		if ( ! is_array( $c ) || empty( $c[1] ) ) {
			continue;
		}
		if ( (string) $c[1] === (string) $shop_url ) {
			$crumbs[ $i ][0] = __( 'Tour du lịch', 'generatepress_child' );
			break;
		}
	}
	return $crumbs;
}
add_filter( 'woocommerce_get_breadcrumb', 'annam_seo_woocommerce_breadcrumb_defaults', 20 );

/**
 * Rank Math breadcrumb: nhãn trang Shop → "Tour du lịch".
 *
 * @param array<int,array<int|string,bool|string>> $crumbs Crumbs.
 * @return array<int,array<int|string,bool|string>>
 */
function annam_seo_rank_math_breadcrumb_items( $crumbs ) {
	if ( ! is_array( $crumbs ) || ! function_exists( 'wc_get_page_id' ) ) {
		return $crumbs;
	}
	$shop_id = (int) wc_get_page_id( 'shop' );
	if ( $shop_id <= 0 ) {
		return $crumbs;
	}
	$shop_url = get_permalink( $shop_id );
	if ( ! $shop_url ) {
		return $crumbs;
	}
	$label = __( 'Tour du lịch', 'generatepress_child' );
	foreach ( $crumbs as $i => $c ) {
		if ( ! is_array( $c ) || empty( $c[1] ) ) {
			continue;
		}
		if ( (string) $c[1] === (string) $shop_url ) {
			$crumbs[ $i ][0] = $label;
			break;
		}
	}
	return $crumbs;
}
add_filter( 'rank_math/frontend/breadcrumb/items', 'annam_seo_rank_math_breadcrumb_items', 20 );

/**
 * Robots: noindex cho URL lọc Woo, search, author/date archive.
 *
 * @param array<string,string> $robots Robots directives.
 * @return array<string,string>
 */
function annam_seo_rank_math_robots( $robots ) {
	if ( ! is_array( $robots ) ) {
		$robots = array();
	}

	if ( is_search() ) {
		$robots['index']  = 'noindex';
		$robots['follow'] = 'follow';
		return $robots;
	}

	if ( is_author() || is_date() ) {
		$robots['index']  = 'noindex';
		$robots['follow'] = 'follow';
		return $robots;
	}

	if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_taxonomy() ) && annam_seo_wc_archive_has_noisy_query_args() ) {
		$robots['index']  = 'noindex';
		$robots['follow'] = 'follow';
		return $robots;
	}

	return $robots;
}
add_filter( 'rank_math/frontend/robots', 'annam_seo_rank_math_robots', 50 );

/**
 * Canonical: bỏ query string trên archive Woo khi có tham số lọc.
 *
 * @param string $canonical Canonical URL.
 * @return string
 */
function annam_seo_rank_math_canonical( $canonical ) {
	if ( ! is_string( $canonical ) || '' === $canonical ) {
		return $canonical;
	}

	if ( class_exists( 'WooCommerce' ) && is_singular( 'product' ) ) {
		$plink = get_permalink( get_queried_object_id() );
		if ( is_string( $plink ) && '' !== $plink ) {
			return $plink;
		}
	}

	if ( ! class_exists( 'WooCommerce' ) || ! ( is_shop() || is_product_taxonomy() ) ) {
		return $canonical;
	}
	if ( ! annam_seo_wc_archive_has_noisy_query_args() ) {
		return $canonical;
	}
	$parts = wp_parse_url( $canonical );
	if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) ) {
		return $canonical;
	}
	$path = isset( $parts['path'] ) ? $parts['path'] : '';
	return $parts['scheme'] . '://' . $parts['host'] . $path;
}
add_filter( 'rank_math/frontend/canonical', 'annam_seo_rank_math_canonical', 50 );

/**
 * Redirect attachment URLs to file (tránh thin attachment pages).
 */
function annam_seo_redirect_attachment_to_file() {
	if ( ! is_attachment() ) {
		return;
	}
	$url = wp_get_attachment_url( get_queried_object_id() );
	if ( $url ) {
		wp_safe_redirect( $url, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'annam_seo_redirect_attachment_to_file', 1 );

/**
 * Loại attachment khỏi XML sitemap Rank Math.
 *
 * @param bool   $exclude   Exclude flag.
 * @param string $post_type Post type.
 * @return bool
 */
function annam_seo_sitemap_exclude_attachment( $exclude, $post_type ) {
	if ( 'attachment' === $post_type ) {
		return true;
	}
	return $exclude;
}
add_filter( 'rank_math/sitemap/exclude_post_type', 'annam_seo_sitemap_exclude_attachment', 20, 2 );

/**
 * Loại một số trang hệ thống Woo / slug rác khỏi sitemap.
 *
 * @param array<string,mixed> $url  Entry.
 * @param string              $type Type.
 * @param object              $post Post row.
 * @return array<string,mixed>
 */
function annam_seo_sitemap_exclude_entries( $url, $type, $post ) {
	if ( 'post' !== $type || ! is_object( $post ) || empty( $post->ID ) ) {
		return $url;
	}

	$exclude_ids = array();
	if ( function_exists( 'wc_get_page_id' ) ) {
		foreach ( array( 'cart', 'checkout', 'myaccount' ) as $page ) {
			$pid = (int) wc_get_page_id( $page );
			if ( $pid > 0 ) {
				$exclude_ids[] = $pid;
			}
		}
	}

	$exclude_slugs = apply_filters(
		'annam_seo_sitemap_exclude_page_slugs',
		array( 'thank-you', 'thankyou', 'test', 'gio-hang' )
	);

	if ( in_array( (int) $post->ID, $exclude_ids, true ) ) {
		return array();
	}

	if ( ! empty( $post->post_name ) && is_array( $exclude_slugs ) ) {
		$slug = (string) $post->post_name;
		if ( in_array( $slug, array_map( 'sanitize_title', $exclude_slugs ), true ) ) {
			return array();
		}
	}

	return $url;
}
add_filter( 'rank_math/sitemap/entry', 'annam_seo_sitemap_exclude_entries', 20, 3 );

/**
 * Đổi http://schema.org/... → https://schema.org/... cho availability & itemCondition (Merchant / Rich Results).
 *
 * @param mixed $data Schema fragment.
 * @return mixed
 */
function annam_merchant_schema_org_https_upgrade( $data ) {
	if ( ! is_array( $data ) ) {
		return $data;
	}
	$prefix = 'http://schema.org/';
	$plen   = strlen( $prefix );
	foreach ( $data as $key => &$val ) {
		if ( ( 'availability' === $key || 'itemCondition' === $key ) && is_string( $val ) && 0 === strpos( $val, $prefix ) ) {
			$val = 'https://schema.org/' . substr( $val, $plen );
		} elseif ( is_array( $val ) ) {
			$val = annam_merchant_schema_org_https_upgrade( $val );
		}
	}
	unset( $val );
	return $data;
}

/**
 * itemCondition theo meta sản phẩm (mặc định mới).
 *
 * @param int $product_id Product ID.
 * @return string
 */
function annam_merchant_product_item_condition_url( $product_id ) {
	$map = array(
		'new'         => 'https://schema.org/NewCondition',
		'used'        => 'https://schema.org/UsedCondition',
		'refurbished' => 'https://schema.org/RefurbishedCondition',
	);
	$raw = get_post_meta( (int) $product_id, '_annam_item_condition', true );
	$raw = is_string( $raw ) ? sanitize_key( $raw ) : '';
	return isset( $map[ $raw ] ) ? $map[ $raw ] : $map['new'];
}

/**
 * availability đồng bộ trạng thái kho WooCommerce.
 *
 * @param WC_Product $product Product.
 * @return string
 */
function annam_merchant_product_availability_url( WC_Product $product ) {
	if ( $product->is_in_stock() ) {
		$slug = ( 'onbackorder' === $product->get_stock_status() ) ? 'BackOrder' : 'InStock';
	} else {
		$slug = 'OutOfStock';
	}
	return 'https://schema.org/' . $slug;
}

/**
 * Bổ sung brand + chuẩn hóa offer/rating cho Product schema Rank Math (single product).
 *
 * @param array<string,mixed> $schema Entity schema.
 * @return array<string,mixed>
 */
function annam_seo_rank_math_product_entity( $schema ) {
	if ( ! is_array( $schema ) || ! function_exists( 'wc_get_product' ) || ! is_singular( 'product' ) ) {
		return $schema;
	}

	$product = wc_get_product( get_queried_object_id() );
	if ( ! $product instanceof WC_Product ) {
		return $schema;
	}

	$pid = (int) $product->get_id();

	if ( empty( $schema['sku'] ) && $product->get_sku() ) {
		$schema['sku'] = $product->get_sku();
	}

	if ( empty( $schema['description'] ) ) {
		$desc = wp_strip_all_tags( (string) $product->get_short_description() );
		if ( '' === $desc ) {
			$desc = wp_strip_all_tags( (string) $product->get_description() );
		}
		if ( '' !== $desc ) {
			if ( function_exists( 'mb_substr' ) ) {
				$desc = mb_strlen( $desc, 'UTF-8' ) > 8000 ? mb_substr( $desc, 0, 8000, 'UTF-8' ) : $desc;
			} else {
				$desc = strlen( $desc ) > 8000 ? substr( $desc, 0, 8000 ) : $desc;
			}
			$schema['description'] = $desc;
		}
	}

	$brand_meta = get_post_meta( $pid, '_annam_brand_name', true );
	$brand_meta = is_string( $brand_meta ) ? trim( $brand_meta ) : '';
	if ( '' !== $brand_meta ) {
		$schema['brand'] = array(
			'@type' => 'Brand',
			'name'  => sanitize_text_field( $brand_meta ),
		);
	} elseif ( empty( $schema['brand'] ) || ! is_array( $schema['brand'] ) ) {
		$schema['brand'] = array(
			'@type' => 'Brand',
			'name'  => 'An Nam Discovery',
		);
	}

	$mpn = get_post_meta( $pid, '_annam_mpn', true );
	$mpn = is_string( $mpn ) ? trim( $mpn ) : '';
	if ( '' !== $mpn ) {
		$schema['mpn'] = sanitize_text_field( $mpn );
	}

	$gpc = get_post_meta( $pid, '_annam_google_product_category', true );
	$gpc = is_string( $gpc ) ? trim( $gpc ) : '';
	if ( '' !== $gpc ) {
		$gpe = array(
			'@type' => 'PropertyValue',
			'name'  => 'google_product_category',
			'value' => sanitize_text_field( $gpc ),
		);
		if ( ! empty( $schema['additionalProperty'] ) && is_array( $schema['additionalProperty'] ) ) {
			if ( isset( $schema['additionalProperty']['@type'] ) ) {
				$schema['additionalProperty'] = array( $schema['additionalProperty'] );
			}
			$schema['additionalProperty'][] = $gpe;
		} else {
			$schema['additionalProperty'] = array( $gpe );
		}
	}

	$custom_label = get_post_meta( $pid, '_annam_merchant_custom_label', true );
	$custom_label = is_string( $custom_label ) ? trim( $custom_label ) : '';
	if ( '' !== $custom_label ) {
		$pv = array(
			'@type' => 'PropertyValue',
			'name'  => 'custom_label_0',
			'value' => sanitize_text_field( $custom_label ),
		);
		if ( ! empty( $schema['additionalProperty'] ) && is_array( $schema['additionalProperty'] ) ) {
			if ( isset( $schema['additionalProperty']['@type'] ) ) {
				$schema['additionalProperty'] = array( $schema['additionalProperty'] );
			}
			$schema['additionalProperty'][] = $pv;
		} else {
			$schema['additionalProperty'] = array( $pv );
		}
	}

	if ( isset( $schema['offers'] ) ) {
		$schema['offers'] = annam_seo_normalize_product_offers( $schema['offers'], $product );
		if ( null === $schema['offers'] ) {
			unset( $schema['offers'] );
		}
	}

	$schema = annam_seo_product_schema_ratings_and_reviews( $schema, $product );

	$schema = annam_merchant_schema_org_https_upgrade( $schema );

	return $schema;
}
add_filter( 'rank_math/snippet/rich_snippet_product_entity', 'annam_seo_rank_math_product_entity', 30 );
add_filter( 'rank_math/snippet/rich_snippet_woocommerceproduct_entity', 'annam_seo_rank_math_product_entity', 30 );

/**
 * @param mixed                 $offers  Offer or AggregateOffer.
 * @param WC_Product            $product Product.
 * @return array<string,mixed>|null|null
 */
function annam_seo_normalize_product_offers( $offers, $product ) {
	$currency = function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'VND';

	if ( is_array( $offers ) && isset( $offers['@type'] ) ) {
		if ( 'AggregateOffer' === $offers['@type'] ) {
			if ( empty( $offers['priceCurrency'] ) ) {
				$offers['priceCurrency'] = $currency;
			}
			if ( empty( $offers['url'] ) ) {
				$offers['url'] = $product->get_permalink();
			}
			if ( $product->is_type( 'variable' ) ) {
				$dec     = wc_get_price_decimals();
				$min_raw = $product->get_variation_price( 'min', false );
				$max_raw = $product->get_variation_price( 'max', false );
				if ( '' !== (string) $min_raw && is_numeric( $min_raw ) ) {
					$offers['lowPrice'] = wc_format_decimal( wc_get_price_to_display( $product, array( 'price' => $min_raw ) ), $dec );
				}
				if ( '' !== (string) $max_raw && is_numeric( $max_raw ) ) {
					$offers['highPrice'] = wc_format_decimal( wc_get_price_to_display( $product, array( 'price' => $max_raw ) ), $dec );
				}
			}
			$offers['availability']  = annam_merchant_product_availability_url( $product );
			$offers['itemCondition'] = annam_merchant_product_item_condition_url( $product->get_id() );
			return $offers;
		}
		if ( 'Offer' === $offers['@type'] ) {
			return annam_seo_normalize_single_offer( $offers, $product, $currency );
		}
	}

	return $offers;
}

/**
 * @param array<string,mixed> $offer    Offer fragment.
 * @param WC_Product          $product  Product.
 * @param string              $currency Currency code.
 * @return array<string,mixed>|null
 */
function annam_seo_normalize_single_offer( array $offer, WC_Product $product, $currency ) {
	$price_raw = $product->get_price();
	if ( '' === $price_raw || null === $price_raw ) {
		return null;
	}

	if ( ! is_numeric( $price_raw ) ) {
		return null;
	}

	$price_num = (float) $price_raw;
	if ( $price_num < 0 ) {
		return null;
	}

	$offer['priceCurrency'] = $currency;
	$display_price            = wc_get_price_to_display( $product, array( 'price' => $price_raw ) );
	$offer['price']           = wc_format_decimal( $display_price, wc_get_price_decimals() );
	$offer['url']             = empty( $offer['url'] ) ? $product->get_permalink() : $offer['url'];
	$offer['availability']    = annam_merchant_product_availability_url( $product );
	$offer['itemCondition']   = annam_merchant_product_item_condition_url( $product->get_id() );

	return $offer;
}

/**
 * aggregateRating / review: chỉ khi WooCommerce có review đã duyệt; đồng bộ số liệu từ $product.
 *
 * @param array<string,mixed> $schema  Product schema entity.
 * @param WC_Product          $product Product.
 * @return array<string,mixed>
 */
function annam_seo_product_schema_ratings_and_reviews( array $schema, WC_Product $product ) {
	$review_count = (int) $product->get_review_count();
	$avg           = (float) $product->get_average_rating();
	$rating_count  = (int) $product->get_rating_count();

	$valid_summary = ( $review_count > 0 && $avg > 0 && $avg <= 5 );

	if ( ! $valid_summary ) {
		unset( $schema['aggregateRating'], $schema['review'] );
		return $schema;
	}

	$schema['aggregateRating'] = array(
		'@type'         => 'AggregateRating',
		'ratingValue'   => number_format( round( $avg, 1 ), 1, '.', '' ),
		'bestRating'    => '5',
		'worstRating'   => '1',
		'reviewCount'   => $review_count,
		'ratingCount'   => max( 0, $rating_count ),
	);

	if ( isset( $schema['review'] ) ) {
		$schema['review'] = annam_seo_sanitize_product_review_entities( $schema['review'] );
		if ( empty( $schema['review'] ) ) {
			unset( $schema['review'] );
		}
	}

	return $schema;
}

/**
 * Giữ lại các Review schema hợp lệ (rating 1–5), bỏ mục lỗi.
 *
 * @param mixed $reviews Review hoặc mảng Review từ Rank Math.
 * @return array<int,array<string,mixed>>
 */
function annam_seo_sanitize_product_review_entities( $reviews ) {
	if ( empty( $reviews ) ) {
		return array();
	}

	$list = isset( $reviews['@type'] ) && 'Review' === $reviews['@type'] ? array( $reviews ) : $reviews;
	if ( ! is_array( $list ) ) {
		return array();
	}

	$out = array();
	foreach ( $list as $item ) {
		if ( ! is_array( $item ) || empty( $item['@type'] ) || 'Review' !== $item['@type'] ) {
			continue;
		}
		$rr = isset( $item['reviewRating'] ) && is_array( $item['reviewRating'] ) ? $item['reviewRating'] : array();
		$rv = isset( $rr['ratingValue'] ) ? (float) $rr['ratingValue'] : 0.0;
		if ( $rv < 1 || $rv > 5 ) {
			continue;
		}
		$out[] = $item;
	}

	return $out;
}

/**
 * Alt ảnh sản phẩm: nếu trống, dùng tên tour (archive / single).
 *
 * @param array<string,string>     $attr       Attributes.
 * @param WP_Post                    $attachment Attachment.
 * @param string|int[]|mixed|null  $size       Size.
 * @return array<string,string>
 */
function annam_seo_product_image_alt( $attr, $attachment, $size ) {
	if ( ! is_array( $attr ) || ! $attachment instanceof WP_Post ) {
		return $attr;
	}
	if ( ! empty( $attr['alt'] ) ) {
		return $attr;
	}
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return $attr;
	}
	$attr['alt'] = wp_strip_all_tags( $product->get_name() );
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'annam_seo_product_image_alt', 25, 3 );
