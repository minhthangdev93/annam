/**
 * Landing thuê limo HN–Sapa — form lead, gallery, scroll-to-form.
 */
(function () {
	'use strict';

	var cfg = typeof annamLimoCharterLanding !== 'undefined' ? annamLimoCharterLanding : {};
	var booking = cfg.booking || {};
	var i18n = cfg.i18n || {};
	var gallery = Array.isArray(cfg.gallery) ? cfg.gallery : [];

	function $(sel, root) {
		return (root || document).querySelector(sel);
	}

	function $$(sel, root) {
		return Array.prototype.slice.call((root || document).querySelectorAll(sel));
	}

	function setField(name, value) {
		var el = $('[data-annam-field="' + name + '"]');
		if (el) {
			el.value = value;
		}
	}

	function showFormNotice(type, message) {
		var box = $('#annam-limo-charter-form-notice');
		if (!box) {
			return;
		}
		if (!type || !message) {
			box.hidden = true;
			box.textContent = '';
			box.className = 'annam-limo-charter-form__ajax-notice';
			return;
		}
		box.hidden = false;
		box.textContent = message;
		box.className =
			'annam-limo-charter-notice annam-limo-charter-notice--' +
			type +
			' annam-limo-charter-form__ajax-notice';
	}

	function scrollToForm() {
		var el = $('#nhan-bao-gia') || $('#annam-limo-charter-booking');
		if (el) {
			el.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}
	}

	function fetchFreshNonce() {
		var body = new URLSearchParams();
		body.set('action', booking.nonceAction || 'annam_limo_charter_lead_nonce');
		return fetch(booking.ajaxUrl, {
			method: 'POST',
			body: body,
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
			},
		})
			.then(function (res) {
				return res.json();
			})
			.then(function (json) {
				if (!json || !json.success || !json.data || !json.data.nonce) {
					throw new Error('nonce');
				}
				return {
					nonce: String(json.data.nonce),
					ts: json.data.ts ? String(json.data.ts) : String(Math.floor(Date.now() / 1000) - 5),
				};
			});
	}

	function pushLeadSuccess(detail) {
		var payload = {
			event: 'limo_charter_lead_success',
			eventCategory: 'limo_charter_landing',
			form_id: 'annam-limo-charter-form',
			landing: 'limo_charter_hn_sapa',
		};
		if (detail && typeof detail === 'object') {
			if (detail.route) {
				payload.route = detail.route;
			}
			if (detail.vehicle) {
				payload.vehicle = detail.vehicle;
			}
			if (detail.time) {
				payload.time = detail.time;
			}
		}
		document.body.dispatchEvent(
			new CustomEvent('annam_limo_charter_track', {
				bubbles: true,
				detail: payload,
			})
		);
		if (typeof window.gtag === 'function') {
			window.gtag('event', 'limo_charter_lead_success', {
				event_category: 'limo_charter_landing',
				form_id: 'annam-limo-charter-form',
			});
		}
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push(payload);
	}

	function initAjaxForm() {
		var form = $('#annam-limo-charter-form');
		if (!form || !booking.ajaxUrl) {
			return;
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			showFormNotice('', '');

			var phoneEl = $('#annam-limo-charter-phone', form);
			if (phoneEl && !String(phoneEl.value || '').trim()) {
				showFormNotice('error', 'Vui lòng nhập số điện thoại hoặc Zalo.');
				return;
			}

			var submitBtn = $('#annam-limo-charter-submit', form);
			var originalText = submitBtn ? submitBtn.textContent : '';
			if (submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = i18n.sending || 'Đang gửi...';
			}

			fetchFreshNonce()
				.catch(function () {
					return {
						nonce: booking.nonce || '',
						ts: String(Math.floor(Date.now() / 1000) - 5),
					};
				})
				.then(function (fresh) {
					var nonceField = $('#annam-limo-charter-nonce', form);
					var tsField = $('#annam-limo-charter-ts', form);
					if (nonceField && fresh.nonce) {
						nonceField.value = fresh.nonce;
					}
					if (tsField && fresh.ts) {
						tsField.value = fresh.ts;
					}
					if (fresh.nonce) {
						booking.nonce = fresh.nonce;
					}

					var fd = new FormData(form);
					fd.append('action', booking.action || 'annam_limo_charter_lead');
					if (fresh.nonce) {
						fd.set('annam_limo_charter_nonce', fresh.nonce);
					}
					fd.set('annam_limo_charter_ts', fresh.ts || String(Math.floor(Date.now() / 1000) - 5));
					fd.set('annam_limo_charter_page_url', booking.pageUrl || window.location.href);

					return fetch(booking.ajaxUrl, {
						method: 'POST',
						body: fd,
						credentials: 'same-origin',
					}).then(function (res) {
						return res.json().then(function (json) {
							return { ok: res.ok, json: json };
						});
					});
				})
				.then(function (result) {
					if (result.json && result.json.success) {
						var msg =
							(result.json.data && result.json.data.message) ||
							'Cảm ơn quý khách.';
						showFormNotice('success', msg);
						pushLeadSuccess({
							route: $('#annam-limo-charter-route', form)
								? $('#annam-limo-charter-route', form).value
								: '',
							vehicle: $('#annam-limo-charter-vehicle', form)
								? $('#annam-limo-charter-vehicle', form).value
								: '',
							time: $('#annam-limo-charter-time', form)
								? $('#annam-limo-charter-time', form).value
								: '',
						});
						form.reset();
						var defs = cfg.formDefaults || {};
						if (defs.route) {
							setField('route', defs.route);
						}
						if (defs.vehicle) {
							setField('vehicle', defs.vehicle);
						}
						if (defs.time) {
							setField('time', defs.time);
						}
						if (booking.dateToday && $('#annam-limo-charter-date')) {
							$('#annam-limo-charter-date').value = booking.dateToday;
						}
						var ts = $('#annam-limo-charter-ts');
						if (ts) {
							ts.value = String(Math.floor(Date.now() / 1000));
						}
						form.scrollIntoView({ behavior: 'smooth', block: 'start' });
						return;
					}
					var errMsg =
						(result.json && result.json.data && result.json.data.message) ||
						i18n.submitError ||
						'Không gửi được.';
					showFormNotice('error', errMsg);
				})
				.catch(function () {
					showFormNotice('error', i18n.submitError || 'Không gửi được.');
				})
				.finally(function () {
					if (submitBtn) {
						submitBtn.disabled = false;
						submitBtn.textContent = originalText;
					}
				});
		});
	}

	function initPickers() {
		$$('[data-annam-pick-vehicle]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var vehicle = btn.getAttribute('data-annam-pick-vehicle');
				if (vehicle) {
					setField('vehicle', vehicle);
					window.dataLayer = window.dataLayer || [];
					window.dataLayer.push({
						event: vehicle === '11' ? 'limo_charter_select_11' : 'limo_charter_select_9',
						eventCategory: 'limo_charter_landing',
						vehicle: vehicle,
					});
				}
				scrollToForm();
			});
		});

		$$('[data-annam-scroll-form]').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				scrollToForm();
			});
		});
	}

	function initGallery() {
		var lb = $('#annam-limo-charter-lightbox');
		if (!lb || !gallery.length) {
			return;
		}
		var img = $('.annam-limo-charter-lightbox__img', lb);
		var cap = $('.annam-limo-charter-lightbox__cap', lb);
		var countEl = $('[data-annam-lightbox-count]', lb);
		var index = 0;
		var total = gallery.length;

		function openAt(i) {
			index = ((i % total) + total) % total;
			var item = gallery[index];
			if (!item || !img) {
				return;
			}
			img.src = item.src || '';
			img.alt = item.caption || '';
			if (cap) {
				cap.textContent = item.caption || '';
			}
			if (countEl) {
				countEl.hidden = false;
				countEl.textContent = index + 1 + ' / ' + total;
			}
			lb.hidden = false;
			document.body.classList.add('annam-limo-charter-lb-open');
		}

		function close() {
			lb.hidden = true;
			document.body.classList.remove('annam-limo-charter-lb-open');
		}

		$$('[data-annam-gallery-open]').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				var i = parseInt(btn.getAttribute('data-annam-gallery-open'), 10);
				if (isNaN(i) || i < 0) {
					i = 0;
				}
				openAt(i);
			});
		});

		var closeBtn = $('[data-annam-lightbox-close]', lb);
		var prevBtn = $('[data-annam-lightbox-prev]', lb);
		var nextBtn = $('[data-annam-lightbox-next]', lb);
		if (closeBtn) {
			closeBtn.addEventListener('click', close);
		}
		if (prevBtn) {
			prevBtn.addEventListener('click', function () {
				openAt(index - 1);
			});
		}
		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				openAt(index + 1);
			});
		}
		lb.addEventListener('click', function (e) {
			if (e.target === lb) {
				close();
			}
		});
		document.addEventListener('keydown', function (e) {
			if (lb.hidden) {
				return;
			}
			if (e.key === 'Escape') {
				close();
			} else if (e.key === 'ArrowLeft') {
				openAt(index - 1);
			} else if (e.key === 'ArrowRight') {
				openAt(index + 1);
			}
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		initAjaxForm();
		initPickers();
		initGallery();
	});
})();
