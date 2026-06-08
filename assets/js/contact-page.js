/**
 * Trang Liên hệ: validate + gửi form AJAX.
 */
(function () {
	'use strict';

	var cfg = typeof annamContactForm !== 'undefined' ? annamContactForm : null;
	var i18n = cfg && cfg.i18n ? cfg.i18n : {};

	function $(sel, root) {
		return (root || document).querySelector(sel);
	}

	function countPhoneDigits(value) {
		return String(value || '').replace(/\D/g, '').length;
	}

	function isValidPhone(value) {
		return countPhoneDigits(value) >= 9;
	}

	function showNotice(type, message) {
		var el = document.getElementById('annam-contact-form-notice');
		if (!el) {
			return;
		}
		if (!message) {
			el.hidden = true;
			el.textContent = '';
			el.className = 'annam-contact-form__ajax-notice';
			return;
		}
		el.hidden = false;
		el.className = 'annam-contact-form__ajax-notice annam-contact-form__ajax-notice--' + type;
		el.textContent = message;
		el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
		try {
			el.focus();
		} catch (err) {
			/* ignore */
		}
	}

	function validateForm(form) {
		var name = $('#annam-contact-name', form);
		var phone = $('#annam-contact-phone', form);
		var service = $('#annam-contact-service', form);
		var dateEl = $('#annam-contact-date', form);
		var hp = $('#annam-contact-website', form);

		if (hp && hp.value) {
			return { ok: false, message: i18n.error || '' };
		}

		if (!name || !String(name.value || '').trim()) {
			return { ok: false, message: i18n.nameRequired || 'Vui lòng nhập họ và tên.' };
		}

		if (!phone || !String(phone.value || '').trim()) {
			return { ok: false, message: i18n.phoneRequired || 'Vui lòng nhập số điện thoại.' };
		}

		if (!isValidPhone(phone.value)) {
			return { ok: false, message: i18n.phoneInvalid || 'Số điện thoại không hợp lệ.' };
		}

		if (!service || !service.value) {
			return { ok: false, message: i18n.serviceRequired || 'Vui lòng chọn loại dịch vụ.' };
		}

		if (dateEl && dateEl.value && cfg && cfg.dateToday && dateEl.value < cfg.dateToday) {
			return { ok: false, message: i18n.datePast || 'Ngày đi không hợp lệ.' };
		}

		return { ok: true };
	}

	var form = document.getElementById('annam-contact-form');
	if (!form) {
		return;
	}

	if (cfg && cfg.dateToday) {
		var dateInput = document.getElementById('annam-contact-date');
		if (dateInput) {
			dateInput.min = cfg.dateToday;
			if (!dateInput.value) {
				dateInput.value = cfg.dateToday;
			}
		}
	}

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		showNotice('', '');

		var check = validateForm(form);
		if (!check.ok) {
			showNotice('error', check.message);
			return;
		}

		if (!cfg || !cfg.ajaxUrl) {
			form.submit();
			return;
		}

		var submitBtn = document.getElementById('annam-contact-submit');
		var originalLabel = submitBtn ? submitBtn.textContent : '';
		if (submitBtn) {
			submitBtn.disabled = true;
			submitBtn.textContent = i18n.sending || 'Đang gửi...';
		}

		var fd = new FormData(form);
		fd.append('action', cfg.action || 'annam_contact_form');
		if (cfg.nonce) {
			fd.set('annam_contact_nonce', cfg.nonce);
		}
		fd.set('annam_contact_page_url', cfg.pageUrl || window.location.href);

		fetch(cfg.ajaxUrl, {
			method: 'POST',
			body: fd,
			credentials: 'same-origin',
		})
			.then(function (res) {
				return res.json().then(function (json) {
					return { ok: res.ok, json: json };
				});
			})
			.then(function (result) {
				if (result.json && result.json.success) {
					var msg =
						(result.json.data && result.json.data.message) ||
						i18n.success ||
						'Cảm ơn quý khách.';
					showNotice('success', msg);
					form.reset();
					var service = document.getElementById('annam-contact-service');
					if (service) {
						service.value = 'tour';
					}
					if (cfg.dateToday) {
						var dateReset = document.getElementById('annam-contact-date');
						if (dateReset) {
							dateReset.value = cfg.dateToday;
						}
					}
					var ts = document.getElementById('annam-contact-ts');
					if (ts) {
						ts.value = String(Math.floor(Date.now() / 1000));
					}
					return;
				}

				var errMsg =
					(result.json &&
						result.json.data &&
						result.json.data.message) ||
					i18n.error ||
					'Có lỗi xảy ra.';
				showNotice('error', errMsg);
			})
			.catch(function () {
				showNotice('error', i18n.error || 'Có lỗi xảy ra.');
			})
			.finally(function () {
				if (submitBtn) {
					submitBtn.disabled = false;
					submitBtn.textContent = originalLabel || i18n.submitLabel || 'Gửi yêu cầu tư vấn';
				}
			});
	});

	var redirectNotice = document.getElementById('annam-contact-notice');
	if (redirectNotice) {
		redirectNotice.scrollIntoView({ behavior: 'smooth', block: 'center' });
		try {
			redirectNotice.focus();
		} catch (err) {
			/* ignore */
		}
	}
})();
