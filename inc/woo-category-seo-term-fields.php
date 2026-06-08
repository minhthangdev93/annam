<?php
/**
 * product_cat: SEO Title & Meta Description riêng (ghi đè mẫu Rank Math khi có nhập).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

const ANNAM_TERM_SEO_TITLE       = '_annam_seo_title';
const ANNAM_TERM_SEO_DESCRIPTION = '_annam_seo_description';

/**
 * Term product_cat đang xem (archive danh mục).
 *
 * @return WP_Term|null
 */
function annam_get_current_product_cat_for_seo() {
	if ( ! function_exists( 'is_product_category' ) || ! is_product_category() ) {
		return null;
	}
	$term = get_queried_object();
	if ( ! $term instanceof WP_Term || 'product_cat' !== $term->taxonomy ) {
		return null;
	}
	return $term;
}

/**
 * SEO Title tùy chỉnh của danh mục (plain text).
 *
 * @param int|WP_Term $term Term ID hoặc object.
 * @return string
 */
function annam_get_product_cat_seo_title( $term ) {
	$term_id = $term instanceof WP_Term ? (int) $term->term_id : absint( $term );
	if ( $term_id <= 0 ) {
		return '';
	}
	$val = get_term_meta( $term_id, ANNAM_TERM_SEO_TITLE, true );
	return is_string( $val ) ? trim( $val ) : '';
}

/**
 * Meta Description tùy chỉnh của danh mục (plain text).
 *
 * @param int|WP_Term $term Term ID hoặc object.
 * @return string
 */
function annam_get_product_cat_seo_description( $term ) {
	$term_id = $term instanceof WP_Term ? (int) $term->term_id : absint( $term );
	if ( $term_id <= 0 ) {
		return '';
	}
	$val = get_term_meta( $term_id, ANNAM_TERM_SEO_DESCRIPTION, true );
	return is_string( $val ) ? trim( $val ) : '';
}

/**
 * Box SEO — thêm danh mục.
 */
function annam_product_cat_seo_add_form_fields() {
	?>
	<div class="form-field term-group annam-term-seo-box">
		<h2><?php esc_html_e( 'SEO danh mục (An Nam)', 'generatepress_child' ); ?></h2>
		<p class="description" style="margin-bottom:12px;">
			<?php esc_html_e( 'Tùy chỉnh tiêu đề & mô tả hiển thị trên Google. Để trống để Rank Math dùng mẫu chung (tên danh mục / mô tả dài).', 'generatepress_child' ); ?>
		</p>
		<p>
			<label for="annam_seo_title"><strong><?php esc_html_e( 'SEO Title', 'generatepress_child' ); ?></strong></label>
			<input type="text" name="annam_seo_title" id="annam_seo_title" value="" class="large-text" maxlength="70" autocomplete="off" />
		</p>
		<p class="description"><?php esc_html_e( 'Ví dụ: Tour Du Thuyền Hạ Long | An Nam Discovery. Nên ~50–60 ký tự.', 'generatepress_child' ); ?></p>
		<p style="margin-top:12px;">
			<label for="annam_seo_description"><strong><?php esc_html_e( 'Meta Description', 'generatepress_child' ); ?></strong></label>
			<textarea name="annam_seo_description" id="annam_seo_description" rows="4" class="large-text" maxlength="320"></textarea>
		</p>
		<p class="description"><?php esc_html_e( 'Ví dụ: Tour Hạ Long 1 ngày, 2 ngày 1 đêm, dinner cruise… Nên ~150–160 ký tự.', 'generatepress_child' ); ?></p>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'annam_product_cat_seo_add_form_fields', 25 );

/**
 * Box SEO — sửa danh mục.
 *
 * @param WP_Term $term Term.
 */
function annam_product_cat_seo_edit_form_fields( $term ) {
	if ( ! $term instanceof WP_Term || 'product_cat' !== $term->taxonomy ) {
		return;
	}

	$seo_title = annam_get_product_cat_seo_title( $term );
	$seo_desc  = annam_get_product_cat_seo_description( $term );
	?>
	<tr class="form-field annam-term-seo-box">
		<th scope="row" valign="top">
			<label><?php esc_html_e( 'SEO danh mục', 'generatepress_child' ); ?></label>
			<p class="description" style="font-weight:400;margin-top:8px;">
				<?php esc_html_e( 'An Nam', 'generatepress_child' ); ?>
			</p>
		</th>
		<td>
			<p class="description" style="margin-top:0;">
				<?php esc_html_e( 'Ghi đè mẫu Rank Math khi có nội dung. Để trống = dùng cấu hình chung.', 'generatepress_child' ); ?>
			</p>
			<p style="margin-top:12px;">
				<label for="annam_seo_title"><strong><?php esc_html_e( 'SEO Title', 'generatepress_child' ); ?></strong></label><br />
				<input type="text" name="annam_seo_title" id="annam_seo_title" value="<?php echo esc_attr( $seo_title ); ?>" class="large-text" maxlength="70" autocomplete="off" />
			</p>
			<p class="description"><?php esc_html_e( 'Ví dụ: Tour Du Thuyền Hạ Long | An Nam Discovery. Nên ~50–60 ký tự.', 'generatepress_child' ); ?></p>
			<p style="margin-top:14px;">
				<label for="annam_seo_description"><strong><?php esc_html_e( 'Meta Description', 'generatepress_child' ); ?></strong></label><br />
				<textarea name="annam_seo_description" id="annam_seo_description" rows="4" class="large-text" maxlength="320"><?php echo esc_textarea( $seo_desc ); ?></textarea>
			</p>
			<p class="description"><?php esc_html_e( 'Ví dụ: Tour Hạ Long 1 ngày, 2 ngày 1 đêm, dinner cruise… Nên ~150–160 ký tự.', 'generatepress_child' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'annam_product_cat_seo_edit_form_fields', 25, 1 );

/**
 * Lưu SEO term meta (thêm / sửa).
 *
 * @param int $term_id Term ID.
 */
function annam_product_cat_seo_save_term( $term_id ) {
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

	if ( array_key_exists( 'annam_seo_title', $_POST ) ) {
		$title = sanitize_text_field( wp_unslash( $_POST['annam_seo_title'] ) );
		if ( '' !== $title ) {
			update_term_meta( $term_id, ANNAM_TERM_SEO_TITLE, $title );
		} else {
			delete_term_meta( $term_id, ANNAM_TERM_SEO_TITLE );
		}
	}

	if ( array_key_exists( 'annam_seo_description', $_POST ) ) {
		$desc = sanitize_textarea_field( wp_unslash( $_POST['annam_seo_description'] ) );
		if ( '' !== $desc ) {
			update_term_meta( $term_id, ANNAM_TERM_SEO_DESCRIPTION, $desc );
		} else {
			delete_term_meta( $term_id, ANNAM_TERM_SEO_DESCRIPTION );
		}
	}
}
add_action( 'edited_product_cat', 'annam_product_cat_seo_save_term', 10, 1 );
add_action( 'created_product_cat', 'annam_product_cat_seo_save_term', 10, 1 );

/**
 * Rank Math: SEO Title tùy chỉnh trên archive product_cat.
 *
 * @param string $title Title sau mẫu Rank Math.
 * @return string
 */
function annam_product_cat_seo_rank_math_title( $title ) {
	$term = annam_get_current_product_cat_for_seo();
	if ( ! $term ) {
		return $title;
	}
	$custom = annam_get_product_cat_seo_title( $term );
	return '' !== $custom ? $custom : $title;
}
add_filter( 'rank_math/frontend/title', 'annam_product_cat_seo_rank_math_title', 99 );

/**
 * Rank Math: Meta Description tùy chỉnh trên archive product_cat.
 *
 * @param string $description Description sau mẫu Rank Math.
 * @return string
 */
function annam_product_cat_seo_rank_math_description( $description ) {
	$term = annam_get_current_product_cat_for_seo();
	if ( ! $term ) {
		return $description;
	}
	$custom = annam_get_product_cat_seo_description( $term );
	return '' !== $custom ? $custom : $description;
}
add_filter( 'rank_math/frontend/description', 'annam_product_cat_seo_rank_math_description', 99 );

/**
 * Fallback <title> khi Rank Math không hoạt động.
 *
 * @param string $title Document title.
 * @return string
 */
function annam_product_cat_seo_document_title( $title ) {
	$term = annam_get_current_product_cat_for_seo();
	if ( ! $term ) {
		return $title;
	}
	$custom = annam_get_product_cat_seo_title( $term );
	return '' !== $custom ? $custom : $title;
}
add_filter( 'pre_get_document_title', 'annam_product_cat_seo_document_title', 99 );

/**
 * Fallback meta description khi Rank Math không in thẻ description.
 */
function annam_product_cat_seo_head_meta_description() {
	if ( ! function_exists( 'rank_math' ) ) {
		$term = annam_get_current_product_cat_for_seo();
		if ( ! $term ) {
			return;
		}
		$custom = annam_get_product_cat_seo_description( $term );
		if ( '' === $custom ) {
			return;
		}
		printf(
			'<meta name="description" content="%s" />' . "\n",
			esc_attr( $custom )
		);
	}
}
add_action( 'wp_head', 'annam_product_cat_seo_head_meta_description', 1 );
