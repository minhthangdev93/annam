/**
 * Meta box gallery uy tín — landing thuê xe.
 */
(function ($) {
	'use strict';

	var l10n = window.annamCrTrustGalleryL10n || {};
	var maxItems = parseInt(l10n.maxItems, 10) || 5;

	function $list() {
		return $('#annam-cr-trust-gallery-list');
	}

	function reindexRows() {
		$list()
			.find('.annam-cr-trust-gallery-admin__item')
			.each(function (i) {
				$(this)
					.find('input')
					.each(function () {
						var name = this.name;
						if (name && name.indexOf('annam_cr_trust_gallery[') === 0) {
							this.name = name.replace(/annam_cr_trust_gallery\[[^\]]+\]/, 'annam_cr_trust_gallery[' + i + ']');
						}
					});
			});
		updateCount();
	}

	function filledRowCount() {
		var count = 0;
		$list()
			.find('.annam-about-attachment-id')
			.each(function () {
				if (parseInt(this.value, 10) > 0) {
					count += 1;
				}
			});
		return count;
	}

	function rowCount() {
		return $list().find('.annam-cr-trust-gallery-admin__item').length;
	}

	function updateCount() {
		var $note = $('.annam-cr-trust-gallery-admin__count');
		if (!$note.length) {
			return;
		}
		var filled = filledRowCount();
		var tpl = l10n.countTpl || 'Đang có %1$d/%2$d ảnh.';
		$note.text(tpl.replace('%1$d', String(filled)).replace('%2$d', String(maxItems)));
		$('#annam-cr-trust-gallery-add').prop('disabled', rowCount() >= maxItems);
	}

	function buildRow() {
		var tpl = document.getElementById('annam-cr-trust-gallery-row-tpl');
		if (!tpl || !tpl.innerHTML) {
			return $();
		}
		var index = rowCount();
		return $(tpl.innerHTML.replace(/__INDEX__/g, String(index)).trim());
	}

	function initSortable() {
		var $el = $list();
		if (!$el.length || !$el.sortable) {
			return;
		}
		if ($el.data('annam-cr-trust-sortable')) {
			$el.sortable('refresh');
			return;
		}
		$el.sortable({
			handle: '.annam-gallery-handle',
			axis: 'y',
			items: '> .annam-cr-trust-gallery-admin__item',
			stop: reindexRows,
		});
		$el.data('annam-cr-trust-sortable', 1);
	}

	$(function () {
		initSortable();
		updateCount();

		$('#annam-cr-trust-gallery-add').on('click', function (e) {
			e.preventDefault();
			if (rowCount() >= maxItems) {
				window.alert(l10n.addLimit || 'Đã đủ 5 ảnh.');
				return;
			}
			var $row = buildRow();
			if (!$row.length) {
				return;
			}
			$list().append($row);
			reindexRows();
			initSortable();
		});

		$(document).on('click', '.annam-cr-trust-gallery-remove', function (e) {
			e.preventDefault();
			var $items = $list().find('.annam-cr-trust-gallery-admin__item');
			if ($items.length <= 1) {
				var $row = $items.first();
				$row.find('.annam-about-attachment-id').val('0');
				$row.find('input[type="text"]').val('');
				$row.find('.annam-about-image-field__preview').html(
					'<span class="annam-media-placeholder">' + (window.annamAdminL10n && window.annamAdminL10n.placeholderImage ? window.annamAdminL10n.placeholderImage : 'Chưa chọn ảnh') + '</span>'
				);
				$row.find('.annam-cabin-image-field__current').remove();
				updateCount();
				return;
			}
			$(this).closest('.annam-cr-trust-gallery-admin__item').remove();
			reindexRows();
			initSortable();
		});

		$(document).on('click', '.annam-about-pick-image, .annam-about-clear-image', function () {
			window.setTimeout(updateCount, 0);
		});
	});
})(jQuery);
