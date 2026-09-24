/**
 * Landing Tour Sapa 3N2Đ — form AJAX, gallery lightbox, fresh nonce.
 */
(function () {
	'use strict';

	var cfg = typeof annamTourSapaLanding !== 'undefined' ? annamTourSapaLanding : {};
	var booking = cfg.booking || {};
	var i18n = cfg.i18n || {};

	function $(sel, root) {
		return (root || document).querySelector(sel);
	}

	function $$(sel, root) {
		return Array.prototype.slice.call((root || document).querySelectorAll(sel));
	}

	function showFormNotice(type, message) {
		var box = $('#annam-tour-sapa-form-notice');
		if (!box) {
			return;
		}
		if (!type || !message) {
			box.hidden = true;
			box.textContent = '';
			box.className = 'annam-tour-sapa-form__ajax-notice';
			return;
		}
		box.hidden = false;
		box.textContent = message;
		box.className = 'annam-tour-sapa-notice annam-tour-sapa-notice--' + type + ' annam-tour-sapa-form__ajax-notice';
	}

	function scrollToForm() {
		var el = $('#annam-tour-sapa-booking') || $('#dat-tour');
		if (el) {
			el.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}
	}

	function fetchFreshBookingNonce() {
		var body = new URLSearchParams();
		body.set('action', booking.nonceAction || 'annam_tour_sapa_booking_nonce');
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

	function pushBookingSuccess(detail) {
		var payload = {
			event: 'tour_sapa_booking_success',
			eventCategory: 'tour_sapa_landing',
			form_id: 'annam-tour-sapa-form',
			landing: 'tour_sapa_3n2d',
		};
		if (detail && typeof detail === 'object') {
			if (detail.hotel) {
				payload.hotel = detail.hotel;
			}
			if (detail.date) {
				payload.date = detail.date;
			}
			if (detail.guests) {
				payload.guests = detail.guests;
			}
		}
		document.body.dispatchEvent(
			new CustomEvent('annam_tour_sapa_track', {
				bubbles: true,
				detail: payload,
			})
		);
		if (typeof window.gtag === 'function') {
			window.gtag('event', 'tour_sapa_booking_success', {
				event_category: 'tour_sapa_landing',
				form_id: 'annam-tour-sapa-form',
			});
		}
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push(payload);
	}

	function initAjaxForm() {
		var form = $('#annam-tour-sapa-form');
		if (!form || !booking.ajaxUrl) {
			return;
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			showFormNotice('', '');

			var nameEl = $('#annam-tour-sapa-name', form);
			var phoneEl = $('#annam-tour-sapa-phone', form);
			var dateEl = $('#annam-tour-sapa-date', form);
			var hotelEl = $('#annam-tour-sapa-hotel', form);
			var guestsEl = $('#annam-tour-sapa-guests', form);

			if (nameEl && !String(nameEl.value || '').trim()) {
				showFormNotice('error', 'Vui lòng nhập họ và tên.');
				nameEl.focus();
				return;
			}
			if (phoneEl && !String(phoneEl.value || '').trim()) {
				showFormNotice('error', 'Vui lòng nhập SĐT, Zalo hoặc WhatsApp.');
				phoneEl.focus();
				return;
			}
			if (dateEl && !dateEl.value) {
				showFormNotice('error', 'Vui lòng chọn ngày khởi hành.');
				dateEl.focus();
				return;
			}

			var submitBtn = $('#annam-tour-sapa-submit', form);
			var originalText = submitBtn ? submitBtn.textContent : '';
			if (submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = i18n.sending || 'Đang gửi...';
			}

			fetchFreshBookingNonce()
				.catch(function () {
					return {
						nonce: booking.nonce || '',
						ts: String(Math.floor(Date.now() / 1000) - 5),
					};
				})
				.then(function (fresh) {
					var nonceField = $('#annam-tour-sapa-nonce', form);
					var tsField = $('#annam-tour-sapa-ts', form);
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
					fd.append('action', booking.action || 'annam_tour_sapa_booking');
					if (fresh.nonce) {
						fd.set('annam_tour_sapa_nonce', fresh.nonce);
					}
					fd.set('annam_tour_sapa_ts', fresh.ts || String(Math.floor(Date.now() / 1000) - 5));
					fd.set('annam_tour_sapa_page_url', booking.pageUrl || window.location.href);

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
						pushBookingSuccess({
							hotel: hotelEl ? hotelEl.value : '',
							date: dateEl ? dateEl.value : '',
							guests: guestsEl ? guestsEl.value : '',
						});
						form.reset();
						if (dateEl && booking.dateToday) {
							dateEl.value = booking.dateToday;
						}
						if (hotelEl) {
							hotelEl.value = '3star';
						}
						if (guestsEl) {
							guestsEl.value = '1';
						}
						var ts = $('#annam-tour-sapa-ts');
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
		$$('[data-annam-pick-hotel]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var hotel = btn.getAttribute('data-annam-pick-hotel');
				var hotelEl = $('#annam-tour-sapa-hotel');
				if (hotel && hotelEl) {
					hotelEl.value = hotel;
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
		var items = cfg.gallery || [];
		var lb = $('#annam-tour-sapa-lightbox');
		if (!lb || !items.length) {
			return;
		}
		var img = $('.annam-tour-sapa-lightbox__img', lb);
		var cap = $('.annam-tour-sapa-lightbox__cap', lb);
		var current = 0;

		function show(index) {
			if (!items.length) {
				return;
			}
			current = ((index % items.length) + items.length) % items.length;
			var item = items[current];
			if (!item || !img) {
				return;
			}
			img.src = item.src;
			img.alt = item.caption || '';
			if (cap) {
				cap.textContent = item.caption || '';
			}
			lb.hidden = false;
			lb.setAttribute('aria-hidden', 'false');
			document.body.style.overflow = 'hidden';
		}

		function close() {
			lb.hidden = true;
			lb.setAttribute('aria-hidden', 'true');
			document.body.style.overflow = '';
		}

		$$('[data-annam-gallery-open]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var idx = parseInt(btn.getAttribute('data-annam-gallery-open'), 10) || 0;
				show(idx);
			});
		});

		$$('[data-annam-lightbox-close]', lb).forEach(function (btn) {
			btn.addEventListener('click', close);
		});

		var prev = $('[data-annam-lightbox-prev]', lb);
		var next = $('[data-annam-lightbox-next]', lb);
		if (prev) {
			prev.addEventListener('click', function (e) {
				e.stopPropagation();
				show(current - 1);
			});
		}
		if (next) {
			next.addEventListener('click', function (e) {
				e.stopPropagation();
				show(current + 1);
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
				show(current - 1);
			} else if (e.key === 'ArrowRight') {
				show(current + 1);
			}
		});
	}

	function initFaq() {
		var root = $('.annam-tour-sapa-faq');
		if (!root) {
			return;
		}
		$$('.annam-tour-sapa-faq__item', root).forEach(function (item) {
			item.addEventListener('toggle', function () {
				if (!item.open) {
					return;
				}
				$$('.annam-tour-sapa-faq__item', root).forEach(function (other) {
					if (other !== item && other.open) {
						other.open = false;
					}
				});
			});
		});
	}

	function initReveal() {
		var nodes = $$('.annam-tour-sapa-reveal');
		if (!nodes.length) {
			return;
		}
		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			nodes.forEach(function (el) {
				el.classList.add('is-in');
			});
			return;
		}
		if (!('IntersectionObserver' in window)) {
			nodes.forEach(function (el) {
				el.classList.add('is-in');
			});
			return;
		}
		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-in');
						io.unobserve(entry.target);
					}
				});
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
		);
		nodes.forEach(function (el) {
			io.observe(el);
		});
	}

	function init() {
		initAjaxForm();
		initPickers();
		initGallery();
		initFaq();
		initReveal();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
