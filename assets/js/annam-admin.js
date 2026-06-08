/**
 * An Nam Settings admin: tabs, slider rows, about images, gallery sortable.
 */
(function ($) {
	'use strict';

	function reindexEcosystemRows() {
		$('#annam-ecosystem-rows .annam-ecosystem-card').each(function (i) {
			$(this)
				.find('input, textarea')
				.each(function () {
					var n = this.name;
					if (n && n.indexOf('annam_ecosystem_items[') === 0) {
						this.name = n.replace(/annam_ecosystem_items\[[^\]]+\]/, 'annam_ecosystem_items[' + i + ']');
					}
				});
		});
	}

	function setEcosystemPreview($card, url) {
		var $box = $card.find('.annam-ecosystem-card__preview');
		$box.empty();
		if (url) {
			$('<img>', { src: url, alt: '', width: 120, height: 80 }).appendTo($box);
		} else {
			$box.append(
				$('<span>', {
					class: 'annam-media-placeholder',
					text: annamAdminL10n.placeholderLogo || 'Chưa chọn logo',
				})
			);
		}
	}

	function openEcosystemLogoFrame($card) {
		var frame = wp.media({
			title: annamAdminL10n.pickLogo || annamAdminL10n.pickTitle,
			button: { text: annamAdminL10n.pickButton },
			multiple: false,
			library: { type: 'image' },
		});
		frame.on('select', function () {
			var att = frame.state().get('selection').first().toJSON();
			var id = att.id ? parseInt(att.id, 10) : 0;
			var url = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
			$card.find('.annam-ecosystem-logo-id').val(id);
			setEcosystemPreview($card, url || '');
		});
		frame.open();
	}

	function reindexRows() {
		$('#annam-home-sliders-rows .annam-slide-card').each(function (i) {
			$(this)
				.find('input, textarea, select')
				.each(function () {
					var n = this.name;
					if (n && n.indexOf('annam_home_sliders[') === 0) {
						this.name = n.replace(/annam_home_sliders\[[^\]]+\]/, 'annam_home_sliders[' + i + ']');
					}
				});
		});
	}

	function nextSlideOrder($rows) {
		var maxOrder = -1;
		$rows.find('.annam-slide-card input[type="number"]').each(function () {
			var value = parseInt($(this).val(), 10);
			if (!Number.isNaN(value) && value > maxOrder) {
				maxOrder = value;
			}
		});
		return maxOrder + 1;
	}

	function buildEmptySlideRow() {
		var tpl = document.getElementById('annam-home-slider-row-template');
		if (tpl && tpl.innerHTML) {
			var index = $('#annam-home-sliders-rows .annam-slide-card').length;
			return $(tpl.innerHTML.replace(/__INDEX__/g, String(index)).trim());
		}

		var $rows = $('#annam-home-sliders-rows');
		var $first = $rows.find('.annam-slide-card').first();
		if (!$first.length) {
			return $();
		}

		var $clone = $first.clone(true, true);
		$clone.find('input[type="text"], input[type="url"], textarea').val('');
		$clone.find('input[type="number"]').val('0');
		$clone.find('input[type="hidden"]').val('');
		$clone.find('input[type="checkbox"]').prop('checked', true);
		$clone.find('.annam-media-preview').each(function (idx) {
			$(this).html(
				'<span class="annam-media-placeholder">' +
					(idx === 0 ? annamAdminL10n.placeholderDesktop : annamAdminL10n.placeholderMobile) +
					'</span>'
			);
		});
		return $clone;
	}

	function appendSlideCard(seed) {
		var $rows = $('#annam-home-sliders-rows');
		var $card = buildEmptySlideRow();
		if (!$card.length) {
			return $();
		}

		var order = nextSlideOrder($rows);

		if (seed && seed.desktopId) {
			$card.find('.annam-desktop-id').val(String(seed.desktopId));
			setPreview($card, 'desktop', seed.desktopUrl || '');
		}
		if (seed && seed.mobileId) {
			$card.find('.annam-mobile-id').val(String(seed.mobileId));
			setPreview($card, 'mobile', seed.mobileUrl || '');
		}
		if (seed && seed.title) {
			$card.find('input[name$="[title]"]').val(seed.title);
		}

		$card.find('input[type="checkbox"]').prop('checked', true);
		$card.find('input[type="number"]').val(String(order));

		$rows.append($card);
		reindexRows();

		return $card;
	}

	function setPreview($card, which, url) {
		var $box = $card.find('.annam-slide-card__field--media').eq(which === 'desktop' ? 0 : 1).find('.annam-media-preview');
		$box.empty();
		if (url) {
			$('<img>', {
				src: url,
				alt: '',
				class: 'annam-preview-' + which,
				width: 120,
				height: 120,
			}).appendTo($box);
		} else {
			$box.append(
				$('<span>', {
					class: 'annam-media-placeholder',
					text: which === 'desktop' ? annamAdminL10n.placeholderDesktop : annamAdminL10n.placeholderMobile,
				})
			);
		}
	}

	function openSliderFrame($card, which) {
		var frame = wp.media({
			title: annamAdminL10n.pickTitle,
			button: { text: annamAdminL10n.pickButton },
			multiple: false,
			library: { type: 'image' },
		});

		frame.on('select', function () {
			var att = frame.state().get('selection').first().toJSON();
			var id = att.id ? parseInt(att.id, 10) : 0;
			var url = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
			if (which === 'desktop') {
				$card.find('.annam-desktop-id').val(id);
				setPreview($card, 'desktop', url || '');
			} else {
				$card.find('.annam-mobile-id').val(id);
				setPreview($card, 'mobile', url || '');
			}
		});

		frame.open();
	}

	function clearSliderField($card, which) {
		if (which === 'desktop') {
			$card.find('.annam-desktop-id').val('');
			setPreview($card, 'desktop', '');
		} else {
			$card.find('.annam-mobile-id').val('');
			setPreview($card, 'mobile', '');
		}
	}

	function setAboutPreview($wrap, url) {
		var $box = $wrap.find('.annam-about-image-field__preview');
		$box.empty();
		if (url) {
			$('<img>', { src: url, alt: '', width: 120, height: 120 }).appendTo($box);
		} else {
			$box.append(
				$('<span>', { class: 'annam-media-placeholder', text: annamAdminL10n.placeholderImage })
			);
		}
	}

	function mediaReady() {
		return typeof wp !== 'undefined' && wp.media;
	}

	function mediaUnavailableAlert() {
		var msg =
			(annamAdminL10n && annamAdminL10n.mediaUnavailable) ||
			'Không mở được thư viện ảnh. Vui lòng tải lại trang.';
		window.alert(msg);
	}

	var bulkSlidesFrame = null;

	function openAboutSingleFrame($wrap) {
		if (!mediaReady()) {
			mediaUnavailableAlert();
			return;
		}
		var frame = wp.media({
			title: annamAdminL10n.pickTitle,
			button: { text: annamAdminL10n.pickButton },
			multiple: false,
			library: { type: 'image' },
		});
		frame.on('select', function () {
			var att = frame.state().get('selection').first().toJSON();
			var id = att.id ? parseInt(att.id, 10) : 0;
			var url = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
			$wrap.find('.annam-about-attachment-id').val(id);
			setAboutPreview($wrap, url || '');
		});
		frame.open();
	}

	function bindAboutImagePickers() {
		$(document).on('click', '.annam-about-pick-image', function (e) {
			e.preventDefault();
			openAboutSingleFrame($(this).closest('[data-annam-about-image]'));
		});
		$(document).on('click', '.annam-about-clear-image', function (e) {
			e.preventDefault();
			var $w = $(this).closest('[data-annam-about-image]');
			$w.find('.annam-about-attachment-id').val('0');
			setAboutPreview($w, '');
		});
	}

	function galleryAppendItem(id, thumbUrl) {
		id = parseInt(id, 10) || 0;
		if (!id) {
			return;
		}
		var $list = $('#annam-about-gallery-list');
		if ($list.find('input[value="' + id + '"]').length) {
			return;
		}
		var $li = $('<li class="annam-gallery-item" data-id="' + id + '"/>');
		$li.append(
			$('<span class="annam-gallery-handle" title="' + (annamAdminL10n.galleryDrag || '') + '">⠿</span>')
		);
		$li.append(
			$('<span class="annam-gallery-thumb-wrap"/>').append(
				$('<img/>', { src: thumbUrl || '', alt: '', width: 80, height: 80, loading: 'lazy', decoding: 'async' })
			)
		);
		$li.append(
			$('<input type="hidden" name="annam_about_settings[about_gallery_images][]"/>').val(String(id))
		);
		$li.append(
			$('<button type="button" class="button-link annam-gallery-remove"/>').text(annamAdminL10n.galleryRemove || 'Xóa')
		);
		$list.append($li);
		if (!$list.data('annam-sortable-init')) {
			initGallerySortable();
		} else {
			$list.sortable('refresh');
		}
	}

	function initGallerySortable() {
		var $list = $('#annam-about-gallery-list');
		if (!$list.length || !$list.sortable) {
			return;
		}
		if ($list.data('annam-sortable-init')) {
			return;
		}
		$list.sortable({
			handle: '.annam-gallery-handle',
			axis: 'y',
			items: '> li.annam-gallery-item',
		});
		$list.data('annam-sortable-init', 1);
	}

	$(function () {
		window.annamAdminL10n = window.annamAdminL10n || {
			pickTitle: 'Chọn ảnh',
			pickButton: 'Dùng ảnh này',
			placeholderDesktop: 'Chưa có ảnh',
			placeholderMobile: 'Tùy chọn',
			placeholderImage: 'Chưa chọn ảnh',
			placeholderLogo: 'Chưa chọn logo',
			pickLogo: 'Chọn logo',
			pickManyTitle: 'Chọn nhiều ảnh cho slider',
			pickManyButton: 'Tạo slide từ ảnh đã chọn',
			galleryRemove: 'Xóa',
			galleryDrag: 'Kéo',
			mediaUnavailable: 'Không mở được thư viện ảnh. Vui lòng tải lại trang.',
		};

		bindAboutImagePickers();

		if (!mediaReady()) {
			return;
		}

		initGallerySortable();

		$(document).on('click', '.annam-pick-desktop', function (e) {
			e.preventDefault();
			openSliderFrame($(this).closest('[data-annam-slide-row]'), 'desktop');
		});
		$(document).on('click', '.annam-pick-mobile', function (e) {
			e.preventDefault();
			openSliderFrame($(this).closest('[data-annam-slide-row]'), 'mobile');
		});
		$(document).on('click', '.annam-clear-desktop', function (e) {
			e.preventDefault();
			clearSliderField($(this).closest('[data-annam-slide-row]'), 'desktop');
		});
		$(document).on('click', '.annam-clear-mobile', function (e) {
			e.preventDefault();
			clearSliderField($(this).closest('[data-annam-slide-row]'), 'mobile');
		});

		$('#annam-gallery-add').on('click', function (e) {
			e.preventDefault();
			var frame = wp.media({
				title: annamAdminL10n.galleryAdd || annamAdminL10n.pickTitle,
				button: { text: annamAdminL10n.pickButton },
				multiple: true,
				library: { type: 'image' },
			});
			frame.on('select', function () {
				var sel = frame.state().get('selection');
				sel.each(function (att) {
					var json = att.toJSON();
					var id = json.id ? parseInt(json.id, 10) : 0;
					var url =
						json.sizes && json.sizes.thumbnail
							? json.sizes.thumbnail.url
							: json.sizes && json.sizes.medium
								? json.sizes.medium.url
								: json.url;
					galleryAppendItem(id, url);
				});
			});
			frame.open();
		});

		$(document).on('click', '.annam-gallery-remove', function (e) {
			e.preventDefault();
			$(this).closest('li.annam-gallery-item').remove();
			var $list = $('#annam-about-gallery-list');
			if ($list.data('annam-sortable-init')) {
				$list.sortable('refresh');
			}
		});

		$('#annam-add-slide').on('click', function (e) {
			e.preventDefault();
			appendSlideCard();
		});

		$('#annam-add-slides-bulk').on('click', function (e) {
			e.preventDefault();
			if (!bulkSlidesFrame) {
				var selection = new wp.media.model.Selection([], {
					multiple: true,
				});
				var libraryState = new wp.media.controller.Library({
					title: annamAdminL10n.pickManyTitle || annamAdminL10n.pickTitle,
					library: wp.media.query({ type: 'image' }),
					multiple: true,
					selection: selection,
					filterable: 'uploaded',
					sortable: false,
					priority: 20,
				});

				bulkSlidesFrame = wp.media({
					frame: 'select',
					button: { text: annamAdminL10n.pickManyButton || annamAdminL10n.pickButton },
					states: [libraryState],
				});

				bulkSlidesFrame.on('open', function () {
					var selection = bulkSlidesFrame.state().get('selection');
					if (selection) {
						selection.reset();
						selection.multiple = true;
					}
				});

				bulkSlidesFrame.on('select', function () {
					var sel = bulkSlidesFrame.state().get('selection');
					sel.each(function (att) {
						var json = att.toJSON();
						var id = json.id ? parseInt(json.id, 10) : 0;
						var url =
							json.sizes && json.sizes.medium
								? json.sizes.medium.url
								: json.sizes && json.sizes.thumbnail
									? json.sizes.thumbnail.url
									: json.url;

						appendSlideCard({
							desktopId: id,
							desktopUrl: url || '',
							title: json.title || '',
						});
					});
				});
			}

			bulkSlidesFrame.open();
		});

		$(document).on('click', '.annam-remove-slide', function (e) {
			e.preventDefault();
			var $rows = $('#annam-home-sliders-rows');
			var $cards = $rows.find('.annam-slide-card');
			if ($cards.length <= 1) {
				$cards.find('input[type="text"], input[type="url"], textarea').val('');
				$cards.find('input[type="number"]').val('0');
				$cards.find('input[type="hidden"]').val('');
				$cards.find('input[type="checkbox"]').prop('checked', true);
				$cards.find('.annam-media-preview').each(function (idx) {
					$(this).html(
						'<span class="annam-media-placeholder">' +
							(idx === 0 ? annamAdminL10n.placeholderDesktop : annamAdminL10n.placeholderMobile) +
							'</span>'
					);
				});
				return;
			}
			$(this).closest('.annam-slide-card').remove();
			reindexRows();
		});

		$(document).on('click', '.annam-ecosystem-pick-logo', function (e) {
			e.preventDefault();
			openEcosystemLogoFrame($(this).closest('[data-annam-ecosystem-row]'));
		});
		$(document).on('click', '.annam-ecosystem-clear-logo', function (e) {
			e.preventDefault();
			var $c = $(this).closest('[data-annam-ecosystem-row]');
			$c.find('.annam-ecosystem-logo-id').val('');
			setEcosystemPreview($c, '');
		});

		$('#annam-add-ecosystem-row').on('click', function (e) {
			e.preventDefault();
			var $rows = $('#annam-ecosystem-rows');
			var $first = $rows.find('.annam-ecosystem-card').first();
			if (!$first.length) {
				return;
			}
			var $clone = $first.clone(true, true);
			$clone.find('input[type="text"], input[type="url"], textarea').val('');
			$clone.find('input[type="number"]').val('0');
			$clone.find('.annam-ecosystem-logo-id').val('');
			$clone.find('input[type="checkbox"]').prop('checked', true);
			setEcosystemPreview($clone, '');
			$rows.append($clone);
			reindexEcosystemRows();
		});

		$(document).on('click', '.annam-remove-ecosystem-row', function (e) {
			e.preventDefault();
			var $rows = $('#annam-ecosystem-rows');
			var $cards = $rows.find('.annam-ecosystem-card');
			if ($cards.length <= 1) {
				var $one = $cards.first();
				$one.find('input[type="text"], input[type="url"], textarea').val('');
				$one.find('input[type="number"]').val('0');
				$one.find('.annam-ecosystem-logo-id').val('');
				$one.find('input[type="checkbox"]').prop('checked', true);
				setEcosystemPreview($one, '');
				return;
			}
			$(this).closest('.annam-ecosystem-card').remove();
			reindexEcosystemRows();
		});

		$('#annam-ecosystem-items-form').on('submit', function () {
			reindexEcosystemRows();
		});

		$('#annam-home-sliders-form').on('submit', function () {
			reindexRows();
		});
	});
})(jQuery);
