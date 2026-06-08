<?php
/**
 * Tour price markup: regular + sale using WooCommerce prices (archive + single card).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * HTML for "Giá chỉ" block (regular strikethrough when on sale; single prominent price otherwise).
 *
 * @param WC_Product $product Product.
 * @return string HTML (pass through wp_kses_post when echoing).
 */
function annam_tour_price_block_html( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return '';
	}

	if ( $product->is_type( 'variable' ) ) {
		return annam_tour_price_block_html_variable( $product );
	}

	return annam_tour_price_block_html_non_variable( $product );
}

/**
 * @param WC_Product $product Simple, grouped, external, etc.
 * @return string
 */
function annam_tour_price_block_html_non_variable( WC_Product $product ) {
	$price = $product->get_price();
	if ( '' === (string) $price ) {
		return '';
	}

	if ( $product->is_on_sale() ) {
		$reg  = $product->get_regular_price();
		$sale = $product->get_sale_price();
		if ( '' !== (string) $reg && '' !== (string) $sale ) {
			$reg_d  = wc_get_price_to_display( $product, array( 'price' => $reg ) );
			$sale_d = wc_get_price_to_display( $product, array( 'price' => $sale ) );
			return annam_tour_price_block_markup( wc_price( $reg_d ), wc_price( $sale_d ), true );
		}
	}

	$display = wc_get_price_to_display( $product );
	return annam_tour_price_block_markup( '', wc_price( $display ), false );
}

/**
 * @param WC_Product_Variable $product Variable product.
 * @return string
 */
function annam_tour_price_block_html_variable( WC_Product $product ) {
	if ( ! $product instanceof WC_Product_Variable || ! $product->has_child() ) {
		return '';
	}

	$min_p = $product->get_variation_price( 'min', false );
	$max_p = $product->get_variation_price( 'max', false );
	if ( '' === (string) $min_p && '' === (string) $max_p ) {
		return '';
	}

	if ( ! $product->is_on_sale() ) {
		return annam_tour_price_block_markup( '', wc_price( wc_get_price_to_display( $product ) ), false );
	}

	$min_reg = $product->get_variation_regular_price( 'min', false );
	$max_reg = $product->get_variation_regular_price( 'max', false );
	$min_sal = $product->get_variation_sale_price( 'min', false );
	$max_sal = $product->get_variation_sale_price( 'max', false );

	if ( '' === (string) $min_sal ) {
		$min_sal = $min_p;
	}
	if ( '' === (string) $max_sal ) {
		$max_sal = $max_p;
	}

	$reg_html  = annam_tour_price_variation_range_html( $product, $min_reg, $max_reg );
	$sale_html = annam_tour_price_variation_range_html( $product, $min_sal, $max_sal );

	if ( '' === $reg_html || '' === $sale_html ) {
		return annam_tour_price_block_markup( '', wc_price( wc_get_price_to_display( $product ) ), false );
	}

	return annam_tour_price_block_markup( $reg_html, $sale_html, true );
}

/**
 * Format min–max variation price with wc_price + tax display.
 *
 * @param WC_Product_Variable $product Variable product.
 * @param string              $min     Raw min price string.
 * @param string              $max     Raw max price string.
 * @return string
 */
function annam_tour_price_variation_range_html( WC_Product_Variable $product, $min, $max ) {
	$min = (string) $min;
	$max = (string) $max;
	if ( '' === $min ) {
		return '';
	}

	$min_d = wc_get_price_to_display( $product, array( 'price' => $min ) );
	if ( '' === $max || $min === $max ) {
		return wc_price( $min_d );
	}

	$max_d = wc_get_price_to_display( $product, array( 'price' => $max ) );

	return wc_price( $min_d ) . '<span class="tour-price__sep"> &ndash; </span>' . wc_price( $max_d );
}

/**
 * @param string $regular_wc_html Price HTML from wc_price() (may include spans).
 * @param string $sale_wc_html    Price HTML from wc_price().
 * @param bool   $on_sale         Show del + sale strong.
 * @return string
 */
function annam_tour_price_block_markup( $regular_wc_html, $sale_wc_html, $on_sale ) {
	$label = esc_html__( 'Giá chỉ', 'woocommerce' );
	$unit  = esc_html__( '/khách', 'woocommerce' );

	$html  = '<div class="tour-price">';
	$html .= '<span class="tour-price__label">' . $label . '</span>';

	if ( $on_sale && '' !== $regular_wc_html && '' !== $sale_wc_html ) {
		$html .= '<del class="tour-price__regular">' . $regular_wc_html . '</del>';
		$html .= '<span class="tour-price__tail">';
		$html .= '<strong class="tour-price__sale">' . $sale_wc_html . '</strong>';
		$html .= '<span class="tour-price__unit">' . $unit . '</span>';
		$html .= '</span>';
	} else {
		$html .= '<span class="tour-price__tail">';
		$html .= '<strong class="tour-price__sale tour-price__sale--regular">' . $sale_wc_html . '</strong>';
		$html .= '<span class="tour-price__unit">' . $unit . '</span>';
		$html .= '</span>';
	}

	$html .= '</div>';

	return $html;
}

/**
 * Whether the card shows strikethrough regular + sale (same rules as annam_tour_price_block_html()).
 *
 * @param WC_Product $product Product.
 * @return bool
 */
function annam_tour_product_has_card_promo_price( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return false;
	}
	if ( $product->is_type( 'variable' ) ) {
		if ( ! $product instanceof WC_Product_Variable ) {
			return false;
		}
		return annam_tour_product_has_card_promo_price_variable( $product );
	}
	if ( ! $product->is_on_sale() ) {
		return false;
	}
	$reg  = $product->get_regular_price();
	$sale = $product->get_sale_price();
	return '' !== (string) $reg && '' !== (string) $sale;
}

/**
 * @param WC_Product_Variable $product Variable product.
 * @return bool
 */
function annam_tour_product_has_card_promo_price_variable( WC_Product_Variable $product ) {
	if ( ! $product->has_child() ) {
		return false;
	}
	if ( ! $product->is_on_sale() ) {
		return false;
	}

	$min_p = $product->get_variation_price( 'min', false );
	$max_p = $product->get_variation_price( 'max', false );
	if ( '' === (string) $min_p && '' === (string) $max_p ) {
		return false;
	}

	$min_reg = $product->get_variation_regular_price( 'min', false );
	$max_reg = $product->get_variation_regular_price( 'max', false );
	$min_sal = $product->get_variation_sale_price( 'min', false );
	$max_sal = $product->get_variation_sale_price( 'max', false );

	if ( '' === (string) $min_sal ) {
		$min_sal = $min_p;
	}
	if ( '' === (string) $max_sal ) {
		$max_sal = $max_p;
	}

	$reg_html  = annam_tour_price_variation_range_html( $product, $min_reg, $max_reg );
	$sale_html = annam_tour_price_variation_range_html( $product, $min_sal, $max_sal );

	return '' !== $reg_html && '' !== $sale_html;
}

/**
 * Approximate discount percent for badge (min “from” price on variable products).
 *
 * @param WC_Product $product Product.
 * @return int|null 1–99 or null if not computable.
 */
function annam_tour_product_card_discount_percent( WC_Product $product ) {
	if ( ! annam_tour_product_has_card_promo_price( $product ) ) {
		return null;
	}

	if ( $product->is_type( 'variable' ) && $product instanceof WC_Product_Variable ) {
		$min_reg_raw = $product->get_variation_regular_price( 'min', false );
		$min_sal_raw = $product->get_variation_sale_price( 'min', false );
		if ( '' === (string) $min_sal_raw ) {
			$min_sal_raw = $product->get_variation_price( 'min', false );
		}
	} else {
		$min_reg_raw = $product->get_regular_price();
		$min_sal_raw = $product->get_sale_price();
	}

	$reg  = (float) wc_get_price_to_display( $product, array( 'price' => (string) $min_reg_raw ) );
	$sale = (float) wc_get_price_to_display( $product, array( 'price' => (string) $min_sal_raw ) );

	if ( $reg <= 0.00001 || $sale >= $reg ) {
		return null;
	}

	$pct = (int) round( ( 1 - $sale / $reg ) * 100 );

	return max( 1, min( 99, $pct ) );
}
