<?php
/**
 * Extra fields on product_cat edit screen for category hero (subtitle, chips, consult).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fields on edit category screen.
 *
 * @param WP_Term $term Term.
 */
function annam_product_cat_hero_edit_fields( $term ) {
	if ( ! $term instanceof WP_Term || 'product_cat' !== $term->taxonomy ) {
		return;
	}

	$subtitle = get_term_meta( $term->term_id, '_annam_hero_subtitle', true );
	$chips    = get_term_meta( $term->term_id, '_annam_hero_chips', true );
	$tel      = get_term_meta( $term->term_id, '_annam_hero_tel', true );
	$url      = get_term_meta( $term->term_id, '_annam_hero_consult_url', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="annam_hero_subtitle"><?php esc_html_e( 'Hero: dòng phụ', 'woocommerce' ); ?></label></th>
		<td>
			<input name="annam_hero_subtitle" id="annam_hero_subtitle" type="text" value="<?php echo esc_attr( is_string( $subtitle ) ? $subtitle : '' ); ?>" class="regular-text" />
			<p class="description"><?php esc_html_e( 'Dòng phụ dưới H1 trong hero danh mục và dưới tên danh mục ở section tour trên trang chủ.', 'woocommerce' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="annam_hero_chips"><?php esc_html_e( 'Hero: chip điều hướng', 'woocommerce' ); ?></label></th>
		<td>
			<textarea name="annam_hero_chips" id="annam_hero_chips" rows="5" class="large-text"><?php echo esc_textarea( is_string( $chips ) ? $chips : '' ); ?></textarea>
			<p class="description"><?php esc_html_e( 'Mỗi dòng một chip. Định dạng: Tên|https://… hoặc chỉ Tên (link mặc định tới khu vực danh sách tour). Nếu để trống, theme sẽ dùng danh mục con (nếu có).', 'woocommerce' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="annam_hero_tel"><?php esc_html_e( 'Hero: SĐT tư vấn (nút gọi)', 'woocommerce' ); ?></label></th>
		<td>
			<input name="annam_hero_tel" id="annam_hero_tel" type="text" value="<?php echo esc_attr( is_string( $tel ) ? $tel : '' ); ?>" class="regular-text" placeholder="0900 000 000" />
			<p class="description"><?php esc_html_e( 'Nút “Tư vấn nhanh” dùng tel:. Nếu trống, có thể dùng URL tùy chỉnh bên dưới.', 'woocommerce' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="annam_hero_consult_url"><?php esc_html_e( 'Hero: URL tư vấn (tuỳ chọn)', 'woocommerce' ); ?></label></th>
		<td>
			<input name="annam_hero_consult_url" id="annam_hero_consult_url" type="url" value="<?php echo esc_attr( is_string( $url ) ? $url : '' ); ?>" class="regular-text" placeholder="https://…" />
			<p class="description"><?php esc_html_e( 'Chỉ dùng khi không nhập SĐT. Liên kết trang liên hệ / Zalo / form.', 'woocommerce' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'annam_product_cat_hero_edit_fields', 15, 1 );

/**
 * Save term meta from edit screen.
 *
 * @param int $term_id Term ID.
 */
function annam_product_cat_hero_save_term( $term_id ) {
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-tag_' . $term_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	if ( isset( $_POST['annam_hero_subtitle'] ) ) {
		update_term_meta( $term_id, '_annam_hero_subtitle', sanitize_text_field( wp_unslash( $_POST['annam_hero_subtitle'] ) ) );
	}

	if ( isset( $_POST['annam_hero_chips'] ) ) {
		update_term_meta( $term_id, '_annam_hero_chips', sanitize_textarea_field( wp_unslash( $_POST['annam_hero_chips'] ) ) );
	}

	if ( isset( $_POST['annam_hero_tel'] ) ) {
		update_term_meta( $term_id, '_annam_hero_tel', sanitize_text_field( wp_unslash( $_POST['annam_hero_tel'] ) ) );
	}

	if ( isset( $_POST['annam_hero_consult_url'] ) ) {
		$u = esc_url_raw( wp_unslash( $_POST['annam_hero_consult_url'] ) );
		update_term_meta( $term_id, '_annam_hero_consult_url', $u );
	}
}
add_action( 'edited_product_cat', 'annam_product_cat_hero_save_term', 10, 1 );
