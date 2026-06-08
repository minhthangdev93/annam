<?php
/**
 * product_cat: hiển thị section sản phẩm trên trang chủ + thứ tự.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

const ANNAM_TERM_SHOW_ON_HOME = '_annam_show_on_home';
const ANNAM_TERM_HOME_ORDER   = '_annam_home_order';

/**
 * Danh sách product_cat bật hiển thị trên trang chủ (đã sắp theo _annam_home_order).
 *
 * @return WP_Term[]
 */
function annam_get_product_categories_for_home_sections() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'meta_query' => array(
				array(
					'key'   => ANNAM_TERM_SHOW_ON_HOME,
					'value' => '1',
				),
			),
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	usort(
		$terms,
		static function ( $a, $b ) {
			$oa = (int) get_term_meta( $a->term_id, ANNAM_TERM_HOME_ORDER, true );
			$ob = (int) get_term_meta( $b->term_id, ANNAM_TERM_HOME_ORDER, true );
			if ( $oa !== $ob ) {
				return $oa <=> $ob;
			}
			return strcasecmp( $a->name, $b->name );
		}
	);

	return $terms;
}

/**
 * Dòng phụ section trang chủ: ưu tiên hero subtitle (term meta _annam_hero_subtitle),
 * chỉ khi trống mới rút gọn mô tả danh mục (plain text, giới hạn từ).
 *
 * @param WP_Term $term Term.
 * @param int     $words Giới hạn từ khi fallback từ mô tả dài.
 * @return string Plain text (template dùng esc_html).
 */
function annam_get_product_cat_home_section_excerpt( WP_Term $term, $words = 18 ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}
	if ( function_exists( 'annam_get_category_hero_subtitle' ) ) {
		$subtitle = annam_get_category_hero_subtitle( $term );
		if ( '' !== $subtitle ) {
			return $subtitle;
		}
	}
	if ( function_exists( 'annam_get_category_hero_excerpt' ) ) {
		return annam_get_category_hero_excerpt( $term, (int) $words );
	}
	$raw = term_description( (int) $term->term_id, 'product_cat' );
	if ( ! is_string( $raw ) || '' === trim( wp_strip_all_tags( $raw ) ) ) {
		return '';
	}
	if ( function_exists( 'annam_trim_words_custom' ) ) {
		return annam_trim_words_custom( $raw, (int) $words );
	}
	return wp_trim_words( wp_strip_all_tags( $raw ), (int) $words, '…' );
}

/**
 * Form thêm danh mục.
 */
function annam_product_cat_home_display_add_form_fields() {
	?>
	<div class="form-field term-group">
		<label for="annam_show_on_home">
			<input type="checkbox" name="annam_show_on_home" id="annam_show_on_home" value="1" />
			<?php esc_html_e( 'Hiển thị trên trang chủ', 'generatepress_child' ); ?>
		</label>
		<p class="description"><?php esc_html_e( 'Bật để hiển thị section tour của danh mục này trên trang chủ (template Trang chủ tĩnh).', 'generatepress_child' ); ?></p>
	</div>
	<div class="form-field term-group">
		<label for="annam_home_order"><?php esc_html_e( 'Thứ tự hiển thị trang chủ', 'generatepress_child' ); ?></label>
		<input type="number" name="annam_home_order" id="annam_home_order" value="" min="0" step="1" class="small-text" placeholder="0" />
		<p class="description"><?php esc_html_e( 'Số nhỏ hiển thị trước (mặc định 0).', 'generatepress_child' ); ?></p>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'annam_product_cat_home_display_add_form_fields', 40 );

/**
 * Form sửa danh mục.
 *
 * @param WP_Term $term Term.
 */
function annam_product_cat_home_display_edit_form_fields( $term ) {
	if ( ! $term instanceof WP_Term || 'product_cat' !== $term->taxonomy ) {
		return;
	}

	$show  = '1' === (string) get_term_meta( $term->term_id, ANNAM_TERM_SHOW_ON_HOME, true );
	$order = (int) get_term_meta( $term->term_id, ANNAM_TERM_HOME_ORDER, true );
	?>
	<tr class="form-field term-group-wrap">
		<th scope="row"><?php esc_html_e( 'Trang chủ', 'generatepress_child' ); ?></th>
		<td>
			<label for="annam_show_on_home">
				<input type="checkbox" name="annam_show_on_home" id="annam_show_on_home" value="1" <?php checked( $show ); ?> />
				<?php esc_html_e( 'Hiển thị trên trang chủ', 'generatepress_child' ); ?>
			</label>
			<p class="description"><?php esc_html_e( 'Bật để hiển thị section tour của danh mục này trên trang chủ (template Trang chủ tĩnh).', 'generatepress_child' ); ?></p>
		</td>
	</tr>
	<tr class="form-field term-group-wrap">
		<th scope="row">
			<label for="annam_home_order"><?php esc_html_e( 'Thứ tự hiển thị trang chủ', 'generatepress_child' ); ?></label>
		</th>
		<td>
			<input type="number" name="annam_home_order" id="annam_home_order" value="<?php echo esc_attr( (string) $order ); ?>" min="0" step="1" class="small-text" />
			<p class="description"><?php esc_html_e( 'Số nhỏ hiển thị trước (mặc định 0).', 'generatepress_child' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'annam_product_cat_home_display_edit_form_fields', 40, 1 );

/**
 * Lưu term meta (thêm / sửa).
 *
 * @param int $term_id Term ID.
 */
function annam_product_cat_home_display_save( $term_id ) {
	$term_id = absint( $term_id );
	if ( ! $term_id || ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	$nonce_edit = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
	$nonce_add  = isset( $_POST['_wpnonce_add-tag'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce_add-tag'] ) ) : '';

	$ok_edit = $nonce_edit && wp_verify_nonce( $nonce_edit, 'update-tag_' . $term_id );
	$ok_add  = $nonce_add && wp_verify_nonce( $nonce_add, 'add-tag' );

	if ( ! $ok_edit && ! $ok_add ) {
		return;
	}

	if ( isset( $_POST['annam_show_on_home'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['annam_show_on_home'] ) ) ) {
		update_term_meta( $term_id, ANNAM_TERM_SHOW_ON_HOME, '1' );
	} else {
		delete_term_meta( $term_id, ANNAM_TERM_SHOW_ON_HOME );
	}

	if ( array_key_exists( 'annam_home_order', $_POST ) ) {
		$order = absint( wp_unslash( $_POST['annam_home_order'] ) );
		if ( $order > 0 ) {
			update_term_meta( $term_id, ANNAM_TERM_HOME_ORDER, $order );
		} else {
			delete_term_meta( $term_id, ANNAM_TERM_HOME_ORDER );
		}
	}
}
add_action( 'edited_product_cat', 'annam_product_cat_home_display_save', 10, 1 );
add_action( 'created_product_cat', 'annam_product_cat_home_display_save', 10, 1 );
