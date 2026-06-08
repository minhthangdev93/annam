/**
 * Landing Cabin VIP — routes, realtime times, AJAX form, schedule sync.
 */
(function () {
	'use strict';

	var cfg = typeof annamCabinLanding !== 'undefined' ? annamCabinLanding : {};
	var booking = cfg.booking || {};
	var formId = cfg.formId || 'annam-cabin-booking';
	var galleryItems = cfg.gallery || [];
	var scheduleTimes = cfg.scheduleTimes || {};
	var routeDest = cfg.routeDestinations || {};
	var routeLabels = cfg.routeLabels || {};
	var i18n = cfg.i18n || {};
	var selectedTime = '';
	var selectedFrom = '';
	var selectedTo = '';
	var autoTomorrowDone = false;

	function $(sel, root) {
		return (root || document).querySelector(sel);
	}

	function $$(sel, root) {
		return Array.prototype.slice.call((root || document).querySelectorAll(sel));
	}

	function getRouteKey(from, to) {
		return (from || '') + '_' + (to || '');
	}

	function getAllTimesForRoute(from, to) {
		var key = getRouteKey(from, to);
		return scheduleTimes[key] && scheduleTimes[key].length ? scheduleTimes[key].slice() : [];
	}

	function timeToMinutes(time) {
		if (!time || typeof time !== 'string') {
			return -1;
		}
		var parts = time.split(':');
		if (parts.length !== 2) {
			return -1;
		}
		var h = parseInt(parts[0], 10);
		var m = parseInt(parts[1], 10);
		if (h === 24 && m === 0) {
			return 24 * 60;
		}
		if (isNaN(h) || isNaN(m) || h < 0 || h > 23 || m < 0 || m > 59) {
			return -1;
		}
		return h * 60 + m;
	}

	/** Giờ hiện tại (0–23) theo múi site WordPress; bỏ phút để tính mốc + leadHours. */
	function getSiteLocalHour() {
		var tz = booking.timezone;
		var d = new Date();
		if (!tz || typeof Intl === 'undefined' || !Intl.DateTimeFormat) {
			return d.getHours();
		}
		try {
			var parts = new Intl.DateTimeFormat('en-US', {
				timeZone: tz,
				hour: 'numeric',
				hourCycle: 'h23',
			}).formatToParts(d);
			for (var i = 0; i < parts.length; i++) {
				if (parts[i].type === 'hour') {
					return parseInt(parts[i].value, 10);
				}
			}
		} catch (e) {}
		return d.getHours();
	}

	/**
	 * Hôm nay: (giờ hiện tại + minLeadHours) * 60 phút từ nửa đêm; không dùng phút hiện tại.
	 * Trả về null nếu không còn khung nào trong ngày.
	 */
	function getTodayThresholdMinutes() {
		var lead = booking.minLeadHours || 2;
		var th = getSiteLocalHour() + lead;
		if (th > 24) {
			return null;
		}
		return th * 60;
	}

	function filterTimesForDate(from, to, dateYmd) {
		var all = getAllTimesForRoute(from, to);
		if (!dateYmd || !booking.dateToday) {
			return all;
		}
		if (dateYmd > booking.dateToday) {
			return all;
		}
		if (dateYmd < booking.dateToday) {
			return [];
		}
		var thresholdMin = getTodayThresholdMinutes();
		if (thresholdMin === null) {
			return [];
		}
		return all.filter(function (time) {
			var mins = timeToMinutes(time);
			return mins >= 0 && mins >= thresholdMin;
		});
	}

	function rebuildDestinationOptions() {
		var fromEl = $('#annam-cabin-from');
		var toEl = $('#annam-cabin-to');
		if (!fromEl || !toEl) {
			return;
		}
		var from = fromEl.value;
		var allowed = routeDest[from] || [];
		var current = toEl.value;
		toEl.innerHTML = '';
		allowed.forEach(function (place) {
			var opt = document.createElement('option');
			opt.value = place;
			opt.textContent = routeLabels[place] || place;
			toEl.appendChild(opt);
		});
		if (allowed.indexOf(current) >= 0) {
			toEl.value = current;
		} else if (allowed.length) {
			toEl.value = allowed[0];
		}
	}

	function showFormNotice(type, message) {
		var box = $('#annam-cabin-form-notice');
		if (!box) {
			return;
		}
		if (!message) {
			box.hidden = true;
			box.textContent = '';
			box.className = 'annam-cabin-form__ajax-notice';
			return;
		}
		box.hidden = false;
		box.className = 'annam-cabin-notice annam-cabin-notice--' + type + ' annam-cabin-form__ajax-notice';
		box.textContent = message;
	}

	function showTimeHint(message) {
		var hint = $('#annam-cabin-time-hint');
		if (!hint) {
			return;
		}
		if (message) {
			hint.hidden = false;
			hint.textContent = message;
		} else {
			hint.hidden = true;
			hint.textContent = '';
		}
	}

	function rebuildTimeSelect(preserveTime, options) {
		options = options || {};
		var fromEl = $('#annam-cabin-from');
		var toEl = $('#annam-cabin-to');
		var dateEl = $('#annam-cabin-date');
		var timeEl = $('#annam-cabin-time');
		if (!fromEl || !toEl || !timeEl) {
			return [];
		}

		var dateYmd = dateEl ? dateEl.value : booking.dateToday;
		var times = filterTimesForDate(fromEl.value, toEl.value, dateYmd);
		var current = preserveTime !== undefined && preserveTime !== null ? preserveTime : timeEl.value;
		var pickLabel = i18n.pickTime || '— Chọn giờ —';

		if (
			!options.skipAutoTomorrow &&
			dateYmd === booking.dateToday &&
			!times.length &&
			booking.dateTomorrow &&
			dateEl &&
			!autoTomorrowDone
		) {
			autoTomorrowDone = true;
			dateEl.value = booking.dateTomorrow;
			showTimeHint(i18n.noTimeToday || '');
			return rebuildTimeSelect('', { skipAutoTomorrow: true });
		}

		timeEl.innerHTML = '';
		var emptyOpt = document.createElement('option');
		emptyOpt.value = '';
		emptyOpt.textContent = pickLabel;
		timeEl.appendChild(emptyOpt);

		var hasCurrent = false;
		times.forEach(function (t) {
			var opt = document.createElement('option');
			opt.value = t;
			opt.textContent = t;
			if (t === current) {
				opt.selected = true;
				hasCurrent = true;
			}
			timeEl.appendChild(opt);
		});

		if (!times.length) {
			showTimeHint(i18n.noTimePickDate || '');
		} else {
			showTimeHint('');
		}

		if (times.length && !hasCurrent) {
			timeEl.value = times[0];
		} else if (!times.length) {
			timeEl.value = '';
		}

		syncScheduleButtons();
		return times;
	}

	/**
	 * Section "Lịch Xe Cabin VIP" — chỉ đồng bộ trạng thái chọn với form;
	 * không lọc/ẩn giờ theo thời gian thực (khác với dropdown giờ trong form).
	 */
	function syncScheduleButtons() {
		var fromEl = $('#annam-cabin-from');
		var toEl = $('#annam-cabin-to');
		var timeEl = $('#annam-cabin-time');
		if (!fromEl || !toEl) {
			return;
		}

		$$('[data-annam-schedule-grid]').forEach(function (grid) {
			var panel = grid.closest('.annam-cabin-tabs__panel');
			$$('.annam-cabin-time-btn', grid).forEach(function (btn) {
				btn.hidden = false;
				btn.disabled = false;
				btn.classList.remove('is-disabled');
				var t = btn.getAttribute('data-annam-pick-time');
				var sel =
					btn.getAttribute('data-from') === fromEl.value &&
					btn.getAttribute('data-to') === toEl.value &&
					t === (timeEl ? timeEl.value : '');
				btn.classList.toggle('is-selected', !!sel);
			});

			var hint = panel ? $('[data-annam-schedule-hint]', panel) : null;
			if (hint) {
				hint.hidden = true;
				hint.textContent = '';
			}
		});
	}

	function scrollToForm() {
		var el = document.getElementById(formId);
		if (!el) {
			return;
		}
		el.scrollIntoView({ behavior: 'smooth', block: 'start' });
		var phone = el.querySelector('#annam-cabin-phone');
		if (phone) {
			setTimeout(function () {
				phone.focus();
			}, 450);
		}
	}

	function setField(name, value) {
		var el = document.querySelector('[data-annam-field="' + name + '"]');
		if (!el) {
			return;
		}
		if (value !== undefined && value !== null && value !== '') {
			el.value = value;
		}
		el.dispatchEvent(new Event('change', { bubbles: true }));
	}

	function applySchedulePick(btn) {
		var time = btn.getAttribute('data-annam-pick-time') || '';
		var from = btn.getAttribute('data-from') || '';
		var to = btn.getAttribute('data-to') || '';

		selectedTime = time;
		selectedFrom = from;
		selectedTo = to;

		setField('from', from);
		rebuildDestinationOptions();
		setField('to', to);
		rebuildTimeSelect(time);
		var timeEl = $('#annam-cabin-time');
		if (timeEl && timeEl.value) {
			setField('time', timeEl.value);
		}
	}

	function initRouteSync() {
		var fromEl = $('#annam-cabin-from');
		var toEl = $('#annam-cabin-to');
		var dateEl = $('#annam-cabin-date');
		if (!fromEl || !toEl) {
			return;
		}

		fromEl.addEventListener('change', function () {
			rebuildDestinationOptions();
			rebuildTimeSelect('');
		});

		toEl.addEventListener('change', function () {
			rebuildTimeSelect('');
		});

		if (dateEl) {
			dateEl.addEventListener('change', function () {
				autoTomorrowDone = false;
				rebuildTimeSelect('');
			});
		}
	}

	function initAjaxForm() {
		var form = $('#annam-cabin-form');
		if (!form || !booking.ajaxUrl) {
			return;
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			showFormNotice('', '');

			var fromEl = $('#annam-cabin-from', form);
			var toEl = $('#annam-cabin-to', form);
			var timeEl = $('#annam-cabin-time', form);

			if (fromEl && toEl && fromEl.value === toEl.value) {
				showFormNotice('error', i18n.sameRoute || 'Tuyến không hợp lệ.');
				return;
			}

			if (timeEl && !timeEl.value) {
				showFormNotice('error', i18n.pickTimeRequired || 'Vui lòng chọn giờ đi.');
				return;
			}

			var submitBtn = $('#annam-cabin-submit', form);
			var originalText = submitBtn ? submitBtn.textContent : '';
			if (submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = i18n.sending || 'Đang gửi...';
			}

			var fd = new FormData(form);
			fd.append('action', booking.action || 'annam_cabin_booking');
			if (booking.nonce) {
				fd.set('annam_cabin_nonce', booking.nonce);
			}
			fd.set('annam_cabin_page_url', booking.pageUrl || window.location.href);

			fetch(booking.ajaxUrl, {
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
						if (defs.cabin_type) {
							setField('cabin_type', defs.cabin_type);
						}
						if (defs.date && $('#annam-cabin-date')) {
							$('#annam-cabin-date').value = defs.date;
						}
						rebuildTimeSelect('');
						var ts = $('#annam-cabin-ts');
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
			var buttons = $$('.annam-cabin-tabs__btn', wrap);
			var panels = $$('.annam-cabin-tabs__panel', wrap);
			buttons.forEach(function (btn) {
				btn.addEventListener('click', function () {
					var tab = btn.getAttribute('data-tab');
					buttons.forEach(function (b) {
						b.classList.toggle('is-active', b === btn);
						b.setAttribute('aria-selected', b === btn ? 'true' : 'false');
					});
					panels.forEach(function (p) {
						p.classList.toggle('is-active', p.getAttribute('data-panel') === tab);
					});
					syncScheduleButtons();
				});
			});
		});
	}

	function initScrollButtons() {
		$$('[data-annam-scroll-form]').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				scrollToForm();
			});
		});

		$$('[data-annam-scroll-form-after-time]').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				var panel = btn.closest('.annam-cabin-tabs__panel');
				var active = panel ? $('.annam-cabin-time-btn.is-selected', panel) : null;
				if (active && !active.disabled) {
					applySchedulePick(active);
				} else if (selectedTime) {
					setField('from', selectedFrom);
					rebuildDestinationOptions();
					setField('to', selectedTo);
					rebuildTimeSelect(selectedTime);
					var teScroll = $('#annam-cabin-time');
					if (teScroll && teScroll.value) {
						setField('time', teScroll.value);
					}
				}
				scrollToForm();
			});
		});
	}

	function initPickTicket() {
		$$('[data-annam-pick-ticket]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var cabin = btn.getAttribute('data-cabin');
				var route = btn.getAttribute('data-route');
				if (route === 'hanoi-sapa') {
					setField('from', 'hanoi');
					rebuildDestinationOptions();
					setField('to', 'sapa');
				} else if (route === 'hanoi-laocai') {
					setField('from', 'hanoi');
					rebuildDestinationOptions();
					setField('to', 'laocai');
				}
				autoTomorrowDone = false;
				rebuildTimeSelect('');
				if (cabin) {
					setField('cabin_type', cabin);
				}
				scrollToForm();
			});
		});
	}

	function initPickCabin() {
		$$('[data-annam-pick-cabin]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				setField('cabin_type', btn.getAttribute('data-annam-pick-cabin'));
				scrollToForm();
			});
		});
	}

	function initPickTime() {
		$$('[data-annam-pick-time]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				if (btn.disabled || btn.hidden) {
					return;
				}
				applySchedulePick(btn);
			});
		});
	}

	function initLightbox() {
		if (!galleryItems.length) {
			return;
		}
		var lb = $('#annam-cabin-lightbox');
		if (!lb) {
			return;
		}
		var img = $('.annam-cabin-lightbox__img', lb);
		var cap = $('.annam-cabin-lightbox__caption', lb);
		var current = 0;

		function show(index) {
			if (!galleryItems[index]) {
				return;
			}
			current = index;
			img.src = galleryItems[index].src;
			img.alt = galleryItems[index].caption || '';
			cap.textContent = galleryItems[index].caption || '';
			lb.hidden = false;
			lb.setAttribute('aria-hidden', 'false');
			document.body.style.overflow = 'hidden';
		}

		function close() {
			lb.hidden = true;
			lb.setAttribute('aria-hidden', 'true');
			document.body.style.overflow = '';
		}

		$$('[data-annam-gallery-index]').forEach(function (trigger) {
			trigger.addEventListener('click', function () {
				var idx = parseInt(trigger.getAttribute('data-annam-gallery-index'), 10);
				if (!isNaN(idx)) {
					show(idx);
				}
			});
		});

		$$('[data-annam-lightbox-close]').forEach(function (btn) {
			btn.addEventListener('click', close);
		});

		var prev = $('[data-annam-lightbox-prev]', lb);
		var next = $('[data-annam-lightbox-next]', lb);
		if (prev) {
			prev.addEventListener('click', function () {
				show((current - 1 + galleryItems.length) % galleryItems.length);
			});
		}
		if (next) {
			next.addEventListener('click', function () {
				show((current + 1) % galleryItems.length);
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
			}
			if (e.key === 'ArrowLeft') {
				show((current - 1 + galleryItems.length) % galleryItems.length);
			}
			if (e.key === 'ArrowRight') {
				show((current + 1) % galleryItems.length);
			}
		});
	}

	function initPageContentToggle() {
		document.querySelectorAll('.annam-cabin-page-content').forEach(function (section) {
			var btn = section.querySelector('.annam-cabin-page-content__toggle');
			if (!btn) {
				return;
			}

			var initial = (btn.textContent || '').trim() || 'Xem thêm';
			var labelMore = btn.getAttribute('data-label-more') || initial;
			var labelLess = btn.getAttribute('data-label-less') || 'Thu gọn';

			btn.addEventListener('click', function () {
				var expanded = section.classList.toggle('is-expanded');
				btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
				btn.textContent = expanded ? labelLess : labelMore;
			});
		});
	}

	function initTracking() {
		document.addEventListener('click', function (e) {
			var t = e.target.closest('[data-track]');
			if (!t) {
				return;
			}
			var eventName = t.getAttribute('data-track');
			document.body.dispatchEvent(
				new CustomEvent('annam_cabin_track', {
					bubbles: true,
					detail: { event: eventName, element: t },
				})
			);
			if (typeof window.gtag === 'function') {
				window.gtag('event', eventName, { event_category: 'cabin_landing' });
			}
			if (typeof window.dataLayer !== 'undefined' && Array.isArray(window.dataLayer)) {
				window.dataLayer.push({
					event: eventName,
					eventCategory: 'cabin_landing',
				});
			}
		});
	}

	function init() {
		initTabs();
		initRouteSync();
		initScrollButtons();
		initPickTicket();
		initPickCabin();
		initPickTime();
		initAjaxForm();
		initLightbox();
		initPageContentToggle();
		initTracking();

		rebuildDestinationOptions();
		rebuildTimeSelect('');

		setInterval(function () {
			var dateEl = $('#annam-cabin-date');
			if (dateEl && dateEl.value === booking.dateToday) {
				rebuildTimeSelect($('#annam-cabin-time') ? $('#annam-cabin-time').value : '');
			} else {
				syncScheduleButtons();
			}
		}, 60000);

		if (window.location.hash === '#annam-cabin-booking') {
			scrollToForm();
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
