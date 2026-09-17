/**
 * Landing Limousine HN–Sapa — form, tabs, gallery, fresh nonce.
 */
(function () {
	'use strict';

	var cfg = typeof annamLimoLanding !== 'undefined' ? annamLimoLanding : {};
	var booking = cfg.booking || {};
	var i18n = cfg.i18n || {};
	var routeMap = cfg.routeDestinations || {};
	var scheduleMap = cfg.scheduleTimes || {};
	var routeLabels = cfg.routeLabels || {};
	var autoTomorrowDone = false;

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
		var box = $('#annam-limo-form-notice');
		if (!box) {
			return;
		}
		if (!type || !message) {
			box.hidden = true;
			box.textContent = '';
			box.className = 'annam-limo-form__ajax-notice';
			return;
		}
		box.hidden = false;
		box.textContent = message;
		box.className = 'annam-limo-notice annam-limo-notice--' + type + ' annam-limo-form__ajax-notice';
	}

	function rebuildDestinationOptions() {
		var fromEl = $('#annam-limo-from');
		var toEl = $('#annam-limo-to');
		if (!fromEl || !toEl) {
			return;
		}
		var from = fromEl.value;
		var dests = routeMap[from] || [];
		var prev = toEl.value;
		toEl.innerHTML = '';
		dests.forEach(function (key) {
			var opt = document.createElement('option');
			opt.value = key;
			opt.textContent = routeLabels[key] || key;
			toEl.appendChild(opt);
		});
		if (dests.indexOf(prev) !== -1) {
			toEl.value = prev;
		}
		updateNotePlaceholders();
	}

	var placeNotePlaceholders = {
		hanoi: 'VD: 23 Tú Mỡ / khách sạn Phố Cổ…',
		sapa: 'VD: khách sạn Fansipan / gần chợ Sapa…'
	};

	function updateNotePlaceholders() {
		var fromEl = $('#annam-limo-from');
		var toEl = $('#annam-limo-to');
		var pickupEl = $('#annam-limo-pickup');
		var dropoffEl = $('#annam-limo-dropoff');
		if (!fromEl || !toEl) {
			return;
		}
		var from = fromEl.value;
		var to = toEl.value;
		if (pickupEl) {
			pickupEl.placeholder = placeNotePlaceholders[from] || placeNotePlaceholders.hanoi;
		}
		if (dropoffEl) {
			dropoffEl.placeholder = placeNotePlaceholders[to] || placeNotePlaceholders.sapa;
		}
	}

	function timesForRoute(from, to) {
		var key = from + '_' + to;
		return scheduleMap[key] || [];
	}

	function filterTimesForToday(times, dateYmd) {
		if (!dateYmd || !booking.dateToday) {
			return times;
		}
		if (dateYmd > booking.dateToday) {
			return times;
		}
		if (dateYmd < booking.dateToday) {
			return [];
		}
		var lead = booking.minLeadHours || 2;
		var now = new Date();
		var thresholdHour = now.getHours() + lead;
		if (thresholdHour > 23) {
			return [];
		}
		var thresholdMins = thresholdHour * 60;
		return times.filter(function (t) {
			var parts = String(t).split(':');
			var mins = parseInt(parts[0], 10) * 60 + parseInt(parts[1] || '0', 10);
			return mins >= thresholdMins;
		});
	}

	function rebuildTimeSelect(preferred) {
		var fromEl = $('#annam-limo-from');
		var toEl = $('#annam-limo-to');
		var dateEl = $('#annam-limo-date');
		var timeEl = $('#annam-limo-time');
		var hint = $('#annam-limo-time-hint');
		if (!fromEl || !toEl || !timeEl) {
			return;
		}

		var dateYmd = dateEl ? dateEl.value : booking.dateToday;
		var all = timesForRoute(fromEl.value, toEl.value);
		var filtered = filterTimesForToday(all, dateYmd);

		if (
			dateYmd === booking.dateToday &&
			filtered.length === 0 &&
			!autoTomorrowDone &&
			booking.dateTomorrow &&
			dateEl
		) {
			autoTomorrowDone = true;
			dateEl.value = booking.dateTomorrow;
			if (hint) {
				hint.hidden = false;
				hint.textContent = i18n.noTimeToday || '';
			}
			rebuildTimeSelect(preferred);
			return;
		}

		if (hint && filtered.length > 0) {
			hint.hidden = true;
			hint.textContent = '';
		} else if (hint && filtered.length === 0 && dateYmd !== booking.dateToday) {
			hint.hidden = false;
			hint.textContent = i18n.noTimePickDate || '';
		}

		var defs = cfg.formDefaults || {};
		var defaultTime = defs.time || '07:00';
		var prev = preferred || timeEl.value;
		timeEl.innerHTML = '';
		filtered.forEach(function (t) {
			var opt = document.createElement('option');
			opt.value = t;
			opt.textContent = t;
			timeEl.appendChild(opt);
		});
		if (prev && filtered.indexOf(prev) !== -1) {
			timeEl.value = prev;
		} else if (filtered.indexOf(defaultTime) !== -1) {
			timeEl.value = defaultTime;
		} else if (filtered.length) {
			timeEl.value = filtered[0];
		}
	}

	function scrollToForm() {
		var el = $('#annam-limo-booking');
		if (el) {
			el.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}
	}

	function fetchFreshBookingNonce() {
		var body = new URLSearchParams();
		body.set('action', booking.nonceAction || 'annam_limo_booking_nonce');
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

	function initAjaxForm() {
		var form = $('#annam-limo-form');
		if (!form || !booking.ajaxUrl) {
			return;
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			showFormNotice('', '');

			var fromEl = $('#annam-limo-from', form);
			var toEl = $('#annam-limo-to', form);
			var timeEl = $('#annam-limo-time', form);

			if (fromEl && toEl && fromEl.value === toEl.value) {
				showFormNotice('error', i18n.sameRoute || 'Tuyến không hợp lệ.');
				return;
			}
			if (timeEl && !timeEl.value) {
				showFormNotice('error', i18n.pickTimeRequired || 'Vui lòng chọn giờ đi.');
				return;
			}

			var submitBtn = $('#annam-limo-submit', form);
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
					var nonceField = $('#annam-limo-nonce', form);
					var tsField = $('#annam-limo-ts', form);
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
					fd.append('action', booking.action || 'annam_limo_booking');
					if (fresh.nonce) {
						fd.set('annam_limo_nonce', fresh.nonce);
					}
					fd.set('annam_limo_ts', fresh.ts || String(Math.floor(Date.now() / 1000) - 5));
					fd.set('annam_limo_page_url', booking.pageUrl || window.location.href);

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
						form.reset();
						autoTomorrowDone = false;
						var defs = cfg.formDefaults || {};
						if (defs.from) {
							setField('from', defs.from);
						}
						rebuildDestinationOptions();
						if (defs.to) {
							setField('to', defs.to);
						}
						if (defs.seat) {
							setField('seat', defs.seat);
						}
						if (defs.date && $('#annam-limo-date')) {
							$('#annam-limo-date').value = defs.date;
						}
						rebuildTimeSelect(defs.time || '07:00');
						var ts = $('#annam-limo-ts');
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

	function initTabs() {
		$$('[data-annam-tabs]').forEach(function (wrap) {
			var buttons = $$('.annam-limo-tabs__btn', wrap);
			var panels = $$('.annam-limo-tabs__panel', wrap);
			buttons.forEach(function (btn) {
				btn.addEventListener('click', function () {
					var tab = btn.getAttribute('data-tab');
					buttons.forEach(function (b) {
						var on = b === btn;
						b.classList.toggle('is-active', on);
						b.setAttribute('aria-selected', on ? 'true' : 'false');
					});
					panels.forEach(function (p) {
						p.classList.toggle('is-active', p.getAttribute('data-panel') === tab);
					});
				});
			});
		});
	}

	function initPickers() {
		$$('[data-annam-pick-seat]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var seat = btn.getAttribute('data-annam-pick-seat');
				if (seat) {
					setField('seat', seat);
				}
				scrollToForm();
			});
		});

		$$('[data-annam-pick-time]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var panel = btn.closest('.annam-limo-tabs__panel') || document;
				$$('.annam-limo-time-btn', panel).forEach(function (b) {
					b.classList.remove('is-active');
				});
				btn.classList.add('is-active');
				var from = btn.getAttribute('data-from');
				var to = btn.getAttribute('data-to');
				var time = btn.getAttribute('data-annam-pick-time');
				if (from) {
					setField('from', from);
				}
				rebuildDestinationOptions();
				if (to) {
					setField('to', to);
				}
				updateNotePlaceholders();
				rebuildTimeSelect(time || '');
				if (time) {
					setField('time', time);
				}
			});
		});

		$$('[data-annam-scroll-form]').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				scrollToForm();
			});
		});
	}

	function initRouteListeners() {
		var fromEl = $('#annam-limo-from');
		var toEl = $('#annam-limo-to');
		var dateEl = $('#annam-limo-date');
		if (fromEl) {
			fromEl.addEventListener('change', function () {
				rebuildDestinationOptions();
				rebuildTimeSelect('');
			});
		}
		if (toEl) {
			toEl.addEventListener('change', function () {
				updateNotePlaceholders();
				rebuildTimeSelect('');
			});
		}
		if (dateEl) {
			dateEl.addEventListener('change', function () {
				autoTomorrowDone = false;
				rebuildTimeSelect('');
			});
		}
	}

	function initGallery() {
		var items = cfg.gallery || [];
		var lb = $('#annam-limo-lightbox');
		if (!lb || !items.length) {
			return;
		}
		var img = $('.annam-limo-lightbox__img', lb);
		var cap = $('.annam-limo-lightbox__cap', lb);

		$$('[data-annam-gallery-open]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var idx = parseInt(btn.getAttribute('data-annam-gallery-open'), 10) || 0;
				var item = items[idx];
				if (!item || !img) {
					return;
				}
				img.src = item.src;
				img.alt = item.caption || '';
				if (cap) {
					cap.textContent = item.caption || '';
				}
				lb.hidden = false;
			});
		});

		$$('[data-annam-lightbox-close]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				lb.hidden = true;
			});
		});
		lb.addEventListener('click', function (e) {
			if (e.target === lb) {
				lb.hidden = true;
			}
		});
	}

	function initFaq() {
		var root = $('.annam-limo-faq');
		if (!root) {
			return;
		}
		$$('.annam-limo-faq__item', root).forEach(function (item) {
			item.addEventListener('toggle', function () {
				if (!item.open) {
					return;
				}
				$$('.annam-limo-faq__item', root).forEach(function (other) {
					if (other !== item && other.open) {
						other.open = false;
					}
				});
			});
		});
	}

	function initSeoToggle() {
		var wrap = $('[data-annam-seo]');
		var btn = $('[data-annam-seo-toggle]');
		if (!wrap || !btn) {
			return;
		}
		btn.addEventListener('click', function () {
			var open = wrap.classList.toggle('is-open');
			btn.textContent = open ? 'Thu gọn' : 'Xem thêm';
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		rebuildDestinationOptions();
		rebuildTimeSelect((cfg.formDefaults && cfg.formDefaults.time) || '07:00');
		initRouteListeners();
		initTabs();
		initPickers();
		initAjaxForm();
		initGallery();
		initFaq();
		initSeoToggle();
	});
})();
