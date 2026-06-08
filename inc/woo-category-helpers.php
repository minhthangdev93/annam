<?php
/**
 * Helpers for WooCommerce category / shop tour layout.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Current product_cat term on category archives, or null.
 *
 * @return WP_Term|null
 */
function annam_get_current_product_category() {
	if ( ! is_product_category() ) {
		return null;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term || 'product_cat' !== $term->taxonomy ) {
		return null;
	}

	return $term;
}

/**
 * Hero image URL for a product category (thumbnail or fallback).
 *
 * @param int $term_id Term ID.
 * @return string Escaped URL (empty if nothing available).
 */
function annam_get_product_category_hero_image( $term_id ) {
	$thumb_id = get_term_meta( (int) $term_id, 'thumbnail_id', true );
	if ( $thumb_id ) {
		$size = apply_filters( 'annam_product_category_hero_image_size', 'large' );
		$url   = wp_get_attachment_image_url( (int) $thumb_id, $size );
		if ( $url ) {
			return esc_url( $url );
		}
	}

	$default_rel = '/assets/images/default-category-hero.jpg';
	$default_abs = get_stylesheet_directory() . $default_rel;
	if ( file_exists( $default_abs ) ) {
		return esc_url( get_stylesheet_directory_uri() . $default_rel );
	}

	if ( function_exists( 'wc_placeholder_img_src' ) ) {
		return esc_url( wc_placeholder_img_src( 'woocommerce_single' ) );
	}

	return '';
}

/**
 * Optional hero subtitle (term meta).
 *
 * @param WP_Term $term Product category term.
 * @return string
 */
function annam_get_category_hero_subtitle( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}
	$v = get_term_meta( (int) $term->term_id, '_annam_hero_subtitle', true );
	return is_string( $v ) ? trim( $v ) : '';
}

/**
 * Short plain-text excerpt from category description for hero.
 *
 * @param WP_Term $term Product category term.
 * @param int     $words Word limit.
 * @return string
 */
function annam_get_category_hero_excerpt( $term, $words = 22 ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}
	$raw = term_description( (int) $term->term_id, 'product_cat' );
	if ( ! is_string( $raw ) || '' === trim( wp_strip_all_tags( $raw ) ) ) {
		return '';
	}
	return annam_trim_words_custom( $raw, (int) $words );
}

/**
 * Nav chips: child categories, optional term meta lines, or filter.
 *
 * Each item: [ 'label' => string, 'url' => string ].
 *
 * @param WP_Term $term Product category term.
 * @return array<int, array{label:string,url:string}>
 */
function annam_get_category_hero_chips( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return array();
	}

	$chips = array();

	$lines = get_term_meta( (int) $term->term_id, '_annam_hero_chips', true );
	if ( is_string( $lines ) && '' !== trim( $lines ) ) {
		foreach ( preg_split( '/\r\n|\r|\n/', $lines ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			if ( false !== strpos( $line, '|' ) ) {
				$parts = array_map( 'trim', explode( '|', $line, 2 ) );
				$label = $parts[0] ?? '';
				$url   = $parts[1] ?? '';
			} else {
				$label = $line;
				$url   = '';
			}
			if ( '' === $label ) {
				continue;
			}
			if ( '' === $url ) {
				$url = '#annam-category-products';
			} elseif ( '/' === substr( $url, 0, 1 ) ) {
				$url = home_url( $url );
			} elseif ( ! preg_match( '#^([a-z][a-z0-9+\-.]*:|#)#i', $url ) ) {
				$url = home_url( '/' . ltrim( $url, '/' ) );
			}
			$chips[] = array(
				'label' => $label,
				'url'   => $url,
			);
		}
	}

	if ( empty( $chips ) ) {
		$children = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => (int) $term->term_id,
				'hide_empty' => false,
				'number'     => 10,
				'orderby'    => 'menu_order',
				'order'      => 'ASC',
			)
		);
		if ( ! is_wp_error( $children ) && ! empty( $children ) ) {
			foreach ( $children as $child ) {
				$link = get_term_link( $child );
				if ( is_wp_error( $link ) ) {
					continue;
				}
				$chips[] = array(
					'label' => $child->name,
					'url'   => $link,
				);
			}
		}
	}

	return apply_filters( 'annam_category_hero_chips', $chips, $term );
}

/**
 * Consult CTA URL: term tel or custom URL or filter default.
 *
 * @param WP_Term $term Product category term.
 * @return string
 */
function annam_get_category_hero_consult_url( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}
	$tel = get_term_meta( (int) $term->term_id, '_annam_hero_tel', true );
	if ( is_string( $tel ) && '' !== trim( $tel ) ) {
		$digits = preg_replace( '/\s+/', '', $tel );
		return $digits ? 'tel:' . rawurlencode( $digits ) : '';
	}
	$url = get_term_meta( (int) $term->term_id, '_annam_hero_consult_url', true );
	if ( is_string( $url ) && '' !== trim( $url ) ) {
		return esc_url( $url );
	}
	return apply_filters( 'annam_category_hero_consult_url', '', $term );
}

/**
 * Safe post meta as string.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @param string $default Default if empty.
 * @return string
 */
function annam_get_tour_meta( $post_id, $key, $default = '' ) {
	$val = get_post_meta( (int) $post_id, $key, true );
	if ( '' === $val || null === $val ) {
		return $default;
	}
	return is_string( $val ) ? $val : (string) $val;
}

/**
 * Trim plain text to a word limit.
 *
 * @param string $text  Text.
 * @param int    $limit Word limit.
 * @return string
 */
function annam_trim_words_custom( $text, $limit = 40 ) {
	$text = wp_strip_all_tags( (string) $text );
	return wp_trim_words( $text, (int) $limit, '…' );
}

/**
 * Tour product card: rating row from WooCommerce (average, review count, rating count).
 *
 * @param WC_Product|null $product Product in loop.
 * @return string HTML (safe for echo with wp_kses_post if needed; attributes escaped).
 */
function annam_render_tour_card_rating_html( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return '';
	}

	$ratings_on = ! function_exists( 'wc_review_ratings_enabled' ) || wc_review_ratings_enabled();

	$review_count = (int) $product->get_review_count();
	$avg          = (float) $product->get_average_rating();
	$rating_total = (int) $product->get_rating_count();

	$has_reviews = $review_count > 0;

	ob_start();

	if ( ! $ratings_on ) {
		if ( $has_reviews ) {
			echo '<div class="annam-tour-card__rating annam-tour-card__rating--no-stars" data-rating-total="' . esc_attr( (string) $rating_total ) . '">';
			echo '<span class="annam-tour-card__rating-count annam-tour-card__rating-count--only">';
			echo esc_html(
				sprintf(
					/* translators: %d: number of reviews */
					_n( '%d đánh giá', '%d đánh giá', $review_count, 'generatepress_child' ),
					$review_count
				)
			);
			echo '</span></div>';
		} else {
			echo '<div class="annam-tour-card__rating annam-tour-card__rating--empty" data-rating-total="0">';
			echo '<span class="annam-tour-card__rating-empty-text">' . esc_html__( 'Chưa có đánh giá', 'generatepress_child' ) . '</span>';
			echo '</div>';
		}
		$html = ob_get_clean();
		return $html;
	}

	if ( $review_count < 1 ) {
		?>
		<div class="annam-tour-card__rating annam-tour-card__rating--empty" data-rating-total="<?php echo esc_attr( (string) $rating_total ); ?>" data-review-count="0">
			<div class="annam-tour-card__rating-row">
				<span class="annam-tour-card__stars annam-tour-card__stars--muted" aria-hidden="true">
					<span class="annam-tour-card__stars-bg">★★★★★</span>
				</span>
				<span class="annam-tour-card__rating-empty-text"><?php esc_html_e( 'Chưa có đánh giá', 'generatepress_child' ); ?></span>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	$avg_display = wc_format_decimal( $avg, 1 );
	$pct         = min( 100, max( 0, ( $avg / 5 ) * 100 ) );
	/* translators: 1: average (e.g. 4.5), 2: review count */
	$aria = sprintf(
		__( 'Điểm trung bình %1$s trên 5 sao. %2$s đánh giá.', 'generatepress_child' ),
		$avg_display,
		number_format_i18n( $review_count )
	);

	?>
	<div class="annam-tour-card__rating" data-rating-total="<?php echo esc_attr( (string) $rating_total ); ?>" data-review-count="<?php echo esc_attr( (string) $review_count ); ?>" data-average="<?php echo esc_attr( (string) $avg_display ); ?>">
		<div class="annam-tour-card__rating-row" role="group" aria-label="<?php echo esc_attr( $aria ); ?>">
			<span class="annam-tour-card__stars" aria-hidden="true">
				<span class="annam-tour-card__stars-bg">★★★★★</span>
				<span class="annam-tour-card__stars-fg" style="width: <?php echo esc_attr( (string) round( $pct, 2 ) ); ?>%;">★★★★★</span>
			</span>
			<span class="annam-tour-card__rating-meta">
				<span class="annam-tour-card__rating-score"><?php echo esc_html( (string) $avg_display ); ?></span>
				<span class="annam-tour-card__rating-sep" aria-hidden="true">/</span>
				<span class="annam-tour-card__rating-max">5</span>
				<span class="annam-tour-card__rating-dot" aria-hidden="true"> · </span>
				<a class="annam-tour-card__rating-count annam-tour-card__rating-count--link" href="<?php echo esc_url( $product->get_permalink() . '#annam-product-reviews' ); ?>">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: number of reviews */
							_n( '%d đánh giá', '%d đánh giá', $review_count, 'generatepress_child' ),
							$review_count
						)
					);
					?>
				</a>
			</span>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Shop main archive, product category, or product tag view.
 *
 * @return bool
 */
function annam_is_tour_archive_shop_context() {
	if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'is_shop' ) ) {
		return false;
	}
	return is_shop() || is_product_category() || is_product_tag();
}

/**
 * Render tour card markup in product loops (archive AJAX, related, home, v.v.).
 *
 * @return bool
 */
function annam_is_tour_card_loop_context() {
	if ( function_exists( 'wc_get_loop_prop' ) ) {
		if ( wc_get_loop_prop( 'annam_category_sort_ajax' ) || wc_get_loop_prop( 'annam_tour_related' ) || wc_get_loop_prop( 'annam_home_section' ) || wc_get_loop_prop( 'annam_recently_viewed' ) ) {
			return true;
		}
	}
	return annam_is_tour_archive_shop_context();
}
