/**
 * Admin: chọn nhiều ảnh đội ngũ + kéo sắp xếp.
 */
(function ($) {
	'use strict';

	var l10n = window.annamLimoTeamAdminL10n || {};
	var maxItems = parseInt(l10n.max, 10) || 6;

	function $list() {
		return $('#annam-limo-team-list');
	}

	function count() {
		return $list().find('.annam-limo-team-admin__item').length;
	}

	function updateCount() {
		var $note = $('.annam-limo-team-admin__count');
		if ($note.length) {
			var tpl = l10n.countTpl || 'Đang có %1$d/%2$d ảnh.';
			$note.text(tpl.replace('%1$d', String(count())).replace('%2$d', String(maxItems)));
		}
		$('#annam-limo-team-add').prop('disabled', count() >= maxItems);
		$('#annam-limo-team-clear').prop('disabled', count() === 0);
	}

	function existingIds() {
		var ids = [];
		$list()
			.find('input[name="annam_limo_landing_team[]"]')
			.each(function () {
				var id = parseInt(this.value, 10) || 0;
				if (id > 0) {
					ids.push(id);
				}
			});
		return ids;
	}

	function appendItem(id, thumbUrl) {
		id = parseInt(id, 10) || 0;
		if (!id || count() >= maxItems) {
			return;
		}
		if ($list().find('input[value="' + id + '"]').length) {
			return;
		}
		var $li = $('<li class="annam-gallery-item annam-limo-team-admin__item" data-id="' + id + '"/>');
		$li.append(
			$('<span class="annam-gallery-handle"/>').attr('title', l10n.drag || 'Kéo').text('⠿')
		);
		$li.append(
			$('<span class="annam-gallery-thumb-wrap"/>').append(
				$('<img/>', {
					src: thumbUrl || '',
					alt: '',
					width: 80,
					height: 80,
					loading: 'lazy',
					decoding: 'async',
				})
			)
		);
		$li.append($('<input type="hidden" name="annam_limo_landing_team[]"/>').val(String(id)));
		$li.append(
			$('<button type="button" class="button-link annam-gallery-remove annam-limo-team-admin__remove"/>').text(
				l10n.remove || 'Xóa'
			)
		);
		$list().append($li);
	}

	function initSortable() {
		var $el = $list();
		if (!$el.length || typeof $el.sortable !== 'function') {
			return;
		}
		if ($el.data('annam-limo-team-sortable')) {
			$el.sortable('refresh');
			return;
		}
		$el.sortable({
			handle: '.annam-gallery-handle',
			items: '> .annam-limo-team-admin__item',
			placeholder: 'annam-limo-team-admin__placeholder',
			tolerance: 'pointer',
		});
		$el.data('annam-limo-team-sortable', 1);
	}

	function mediaReady() {
		return typeof wp !== 'undefined' && wp.media;
	}

	$(function () {
		if (!$list().length) {
			return;
		}
		initSortable();
		updateCount();

		$('#annam-limo-team-add').on('click', function (e) {
			e.preventDefault();
			if (!mediaReady()) {
				window.alert((window.annamAdminL10n && window.annamAdminL10n.mediaUnavailable) || 'Media unavailable');
				return;
			}
			if (count() >= maxItems) {
				window.alert(l10n.limitAlert || 'Max 6');
				return;
			}
			var remain = maxItems - count();
			var frame = wp.media({
				title: l10n.pickTitle || 'Chọn ảnh',
				button: { text: l10n.pickButton || 'Dùng ảnh đã chọn' },
				multiple: true,
				library: { type: 'image' },
			});
			frame.on('select', function () {
				var have = existingIds();
				var added = 0;
				frame
					.state()
					.get('selection')
					.each(function (att) {
						if (added >= remain) {
							return;
						}
						var json = att.toJSON();
						var id = json.id ? parseInt(json.id, 10) : 0;
						if (!id || have.indexOf(id) !== -1) {
							return;
						}
						var url =
							json.sizes && json.sizes.thumbnail
								? json.sizes.thumbnail.url
								: json.sizes && json.sizes.medium
									? json.sizes.medium.url
									: json.url || '';
						appendItem(id, url);
						have.push(id);
						added += 1;
					});
				if (added > 0) {
					initSortable();
					updateCount();
				}
				if (frame.state().get('selection').length > remain && added === remain) {
					window.alert(l10n.limitAlert || 'Max 6');
				}
			});
			frame.open();
		});

		$(document).on('click', '.annam-limo-team-admin__remove', function (e) {
			e.preventDefault();
			$(this).closest('.annam-limo-team-admin__item').remove();
			updateCount();
			initSortable();
		});

		$('#annam-limo-team-clear').on('click', function (e) {
			e.preventDefault();
			$list().empty();
			updateCount();
		});
	});
})(jQuery);
