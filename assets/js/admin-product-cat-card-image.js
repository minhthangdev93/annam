/**
 * Media picker for product_cat "Ảnh card danh mục" (term meta).
 */
(function ($) {
	'use strict';

	var frame;

	function previewHtml(url) {
		if (!url) {
			return '';
		}
		return (
			'<img src="' +
			String(url).replace(/"/g, '&quot;') +
			'" alt="" style="max-width:220px;height:auto;display:block;border-radius:4px;border:1px solid #c3c4c7;" />'
		);
	}

	$(document).on('click', '#annam-cat-card-img-select', function (e) {
		e.preventDefault();

		if (frame) {
			frame.open();
			return;
		}

		frame = wp.media({
			title: annamCatCardImg.frameTitle,
			button: { text: annamCatCardImg.frameButton },
			library: { type: 'image' },
			multiple: false,
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			$('#annam_category_card_image_id').val(attachment.id ? String(attachment.id) : '');
			$('#annam-cat-card-img-preview').html(previewHtml(attachment.url || ''));
		});

		frame.open();
	});

	$(document).on('click', '#annam-cat-card-img-remove', function (e) {
		e.preventDefault();
		$('#annam_category_card_image_id').val('');
		$('#annam-cat-card-img-preview').empty();
	});
})(jQuery);
