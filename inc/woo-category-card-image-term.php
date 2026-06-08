<?php
/**
 * Term meta: ảnh riêng cho card danh mục (không dùng chung thumbnail hero WooCommerce).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Term meta key — lưu attachment ID (integer).
 */
const ANNAM_CATEGORY_CARD_IMAGE_META = '_annam_category_card_image_id';

/**
 * Attachment ID hiển thị trên card điều hướng: ảnh card → thumbnail WC → 0.
 *
 * @param int $term_id product_cat term ID.
 * @return int Attachment ID hoặc 0.
 */
function annam_get_product_cat_card_image_id( $term_id ) {
	$term_id = absint( $term_id );
	if ( ! $term_id ) {
		return 0;
	}

	$card = absint( get_term_meta( $term_id, ANNAM_CATEGORY_CARD_IMAGE_META, true ) );
	if ( $card && wp_attachment_is_image( $card ) ) {
		return $card;
	}

	$thumb = absint( get_term_meta( $term_id, 'thumbnail_id', true ) );
	if ( $thumb && wp_attachment_is_image( $thumb ) ) {
		return $thumb;
	}

	return 0;
}

/**
 * @return bool
 */
function annam_is_product_cat_admin_screen() {
	if ( ! is_admin() ) {
		return false;
	}

	if ( isset( $_GET['taxonomy'] ) && 'product_cat' === sanitize_key( wp_unslash( $_GET['taxonomy'] ) ) ) {
		return true;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	return $screen && isset( $screen->taxonomy ) && 'product_cat' === $screen->taxonomy;
}

/**
 * @param string $hook_suffix Current admin page.
 */
function annam_enqueue_product_cat_card_image_admin( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'edit-tags.php', 'term.php' ), true ) || ! annam_is_product_cat_admin_screen() ) {
		return;
	}

	wp_enqueue_media();

	$path = get_stylesheet_directory() . '/assets/js/admin-product-cat-card-image.js';
	$ver  = file_exists( $path ) ? (string) filemtime( $path ) : '1.0';

	wp_enqueue_script(
		'annam-admin-product-cat-card-image',
		get_stylesheet_directory_uri() . '/assets/js/admin-product-cat-card-image.js',
		array( 'jquery', 'media-editor' ),
		$ver,
		true
	);

	wp_localize_script(
		'annam-admin-product-cat-card-image',
		'annamCatCardImg',
		array(
			'frameTitle'  => __( 'Chọn ảnh card danh mục', 'generatepress_child' ),
			'frameButton' => __( 'Dùng ảnh này', 'generatepress_child' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'annam_enqueue_product_cat_card_image_admin', 10, 1 );

/**
 * Form thêm danh mục.
 */
function annam_product_cat_card_image_add_form_fields() {
	?>
	<div class="form-field term-group">
		<label for="annam_category_card_image_id"><?php esc_html_e( 'Ảnh card danh mục', 'generatepress_child' ); ?></label>
		<input type="hidden" name="annam_category_card_image_id" id="annam_category_card_image_id" value="" />
		<div id="annam-cat-card-img-preview" class="annam-cat-card-img-preview" style="margin:8px 0;"></div>
		<p>
			<button type="button" class="button" id="annam-cat-card-img-select"><?php esc_html_e( 'Chọn ảnh card', 'generatepress_child' ); ?></button>
			<button type="button" class="button" id="annam-cat-card-img-remove"><?php esc_html_e( 'Xóa ảnh', 'generatepress_child' ); ?></button>
		</p>
		<p class="description">
			<?php esc_html_e( 'Dùng cho card danh mục ở các section điều hướng. Nên dùng ảnh vuông, khuyến nghị 800x800px.', 'generatepress_child' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'annam_product_cat_card_image_add_form_fields', 25 );

/**
 * Form sửa danh mục.
 *
 * @param WP_Term $term Term.
 */
function annam_product_cat_card_image_edit_form_fields( $term ) {
	if ( ! $term instanceof WP_Term || 'product_cat' !== $term->taxonomy ) {
		return;
	}

	$card_id = absint( get_term_meta( $term->term_id, ANNAM_CATEGORY_CARD_IMAGE_META, true ) );
	?>
	<tr class="form-field term-group-wrap">
		<th scope="row">
			<label for="annam_category_card_image_id"><?php esc_html_e( 'Ảnh card danh mục', 'generatepress_child' ); ?></label>
		</th>
		<td>
			<input type="hidden" name="annam_category_card_image_id" id="annam_category_card_image_id" value="<?php echo esc_attr( (string) $card_id ); ?>" />
			<div id="annam-cat-card-img-preview" class="annam-cat-card-img-preview" style="margin-bottom:8px;">
				<?php
				if ( $card_id && wp_attachment_is_image( $card_id ) ) {
					echo wp_get_attachment_image(
						$card_id,
						'medium',
						false,
						array(
							'style' => 'max-width:220px;height:auto;display:block;border-radius:4px;border:1px solid #c3c4c7;',
							'alt'   => '',
						)
					);
				}
				?>
			</div>
			<p>
				<button type="button" class="button" id="annam-cat-card-img-select"><?php esc_html_e( 'Chọn ảnh card', 'generatepress_child' ); ?></button>
				<button type="button" class="button" id="annam-cat-card-img-remove"><?php esc_html_e( 'Xóa ảnh', 'generatepress_child' ); ?></button>
			</p>
			<p class="description">
				<?php esc_html_e( 'Dùng cho card danh mục ở các section điều hướng. Nên dùng ảnh vuông, khuyến nghị 800x800px.', 'generatepress_child' ); ?>
			</p>
		</td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'annam_product_cat_card_image_edit_form_fields', 25, 1 );

/**
 * Lưu term meta (thêm / sửa).
 *
 * @param int $term_id Term ID.
 */
function annam_product_cat_card_image_save( $term_id ) {
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

	if ( ! array_key_exists( 'annam_category_card_image_id', $_POST ) ) {
		return;
	}

	$id = absint( wp_unslash( $_POST['annam_category_card_image_id'] ) );

	if ( $id && wp_attachment_is_image( $id ) ) {
		update_term_meta( $term_id, ANNAM_CATEGORY_CARD_IMAGE_META, $id );
	} else {
		delete_term_meta( $term_id, ANNAM_CATEGORY_CARD_IMAGE_META );
	}
}
add_action( 'edited_product_cat', 'annam_product_cat_card_image_save', 10, 1 );
add_action( 'created_product_cat', 'annam_product_cat_card_image_save', 10, 1 );
