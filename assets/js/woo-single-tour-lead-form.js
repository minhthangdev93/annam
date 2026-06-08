/**
 * Sidebar tour lead form: validate + AJAX submit.
 */
(function () {
	'use strict';

	var cfg = typeof annamTourLeadForm !== 'undefined' ? annamTourLeadForm : null;
	var i18n = cfg && cfg.i18n ? cfg.i18n : {};

	function digitsOnly(str) {
		return String(str || '').replace(/\D/g, '');
	}

	function validatePhone(dial, local) {
		if (!local || !String(local).trim()) {
			return { ok: false, msg: i18n.phoneRequired || 'Vui lòng nhập số điện thoại.' };
		}
		if (!/^[\d\s+\-().]+$/u.test(local)) {
			return {
				ok: false,
				msg:
					'Số điện thoại chỉ được gồm số, khoảng trắng và các ký tự + - ( ).',
			};
		}
		var all = digitsOnly(dial) + digitsOnly(local);
		if (all.length < 9) {
			return {
				ok: false,
				msg:
					i18n.phoneInvalid ||
					'Số điện thoại không hợp lệ. Vui lòng nhập ít nhất 9 chữ số.',
			};
		}
		if (all.length > 15) {
			return { ok: false, msg: i18n.phoneInvalid || 'Số điện thoại không hợp lệ.' };
		}
		return { ok: true };
	}

	function showNotice(type, message) {
		var el = document.getElementById('annam-tour-lead-notice');
		if (!el) {
			return;
		}
		if (!message) {
			el.hidden = true;
			el.textContent = '';
			el.className = 'annam-tour-form-message annam-tour-form-message--js';
			return;
		}
		el.hidden = false;
		el.className =
			'annam-tour-form-message annam-tour-form-message--' +
			(type === 'success' ? 'success' : 'error');
		el.setAttribute('role', type === 'success' ? 'status' : 'alert');
		el.textContent = message;
		el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
	}

	function onReady(fn) {
		if (document.readyState !== 'loading') {
			fn();
		} else {
			document.addEventListener('DOMContentLoaded', fn);
		}
	}

	onReady(function () {
		var form = document.getElementById('annam-tour-lead-form');
		if (!form) {
			return;
		}

		var hp = document.getElementById('annam_hp_website');
		var dialEl = document.getElementById('annam_phone_country');
		var localEl = document.getElementById('annam_phone_local');
		var submitBtn = document.getElementById('annam-tour-lead-submit');

		form.addEventListener('submit', function (e) {
			e.preventDefault();

			if (hp && hp.value) {
				showNotice('error', i18n.error || '');
				return;
			}

			var dial = dialEl ? dialEl.value : '';
			var local = localEl ? localEl.value : '';
			var res = validatePhone(dial, local);
			if (!res.ok) {
				showNotice('error', res.msg);
				if (localEl) {
					localEl.focus();
				}
				return;
			}

			if (!cfg || !cfg.ajaxUrl) {
				form.submit();
				return;
			}

			var originalLabel = submitBtn ? submitBtn.textContent : '';
			if (submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = i18n.sending || 'Đang gửi...';
			}

			var fd = new FormData(form);
			fd.append('action', cfg.action || 'annam_tour_lead');
			if (cfg.nonce) {
				fd.set('annam_tour_lead_nonce', cfg.nonce);
			}
			if (cfg.productId) {
				fd.set('annam_product_id', String(cfg.productId));
			}
			fd.set('annam_tour_lead_page_url', cfg.pageUrl || window.location.href);

			fetch(cfg.ajaxUrl, {
				method: 'POST',
				body: fd,
				credentials: 'same-origin',
			})
				.then(function (response) {
					return response.json().then(function (json) {
						return { ok: response.ok, json: json };
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
						if (dialEl) {
							dialEl.value = '+84';
						}
						var ts = document.getElementById('annam-tour-form-ts');
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
						submitBtn.textContent =
							originalLabel || i18n.submitLabel || 'Gửi';
					}
				});
		});

		var redirectNotice = document.getElementById('annam-tour-lead-notice');
		if (
			redirectNotice &&
			!redirectNotice.hidden &&
			redirectNotice.classList.contains('annam-tour-form-message--success')
		) {
			redirectNotice.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
		}
	});
})();
