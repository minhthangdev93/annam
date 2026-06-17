(function () {
	'use strict';

	var cfg = window.annamCarRental || {};

	function qs(sel, root) {
		return (root || document).querySelector(sel);
	}

	function qsa(sel, root) {
		return Array.prototype.slice.call((root || document).querySelectorAll(sel));
	}

	function scrollToEl(selector) {
		var el = typeof selector === 'string' ? qs(selector) : selector;
		if (!el) {
			return;
		}
		var top = el.getBoundingClientRect().top + window.pageYOffset - 72;
		window.scrollTo({ top: top, behavior: 'smooth' });
	}

	function splitRouteLabel(routeLabel) {
		var label = routeLabel || '';
		var sep = label.indexOf('↔') !== -1 ? '↔' : label.indexOf('⇌') !== -1 ? '⇌' : '';
		if (!sep) {
			return { pickup: 'Hà Nội', destination: '' };
		}
		var parts = label.split(sep);
		return {
			pickup: (parts[0] || 'Hà Nội').trim(),
			destination: (parts[1] || '').trim(),
		};
	}

	function trackPriceTableClick(payload) {
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push(
			Object.assign(
				{
					event: 'price_table_quote_click',
					landing_url: window.location.pathname,
				},
				payload
			)
		);
	}

	function pickRouteFromButton(btn) {
		if (!btn) {
			return;
		}

		var heroForm = qs('#annam-cr-form-hero');
		if (!heroForm) {
			return;
		}

		var routeLabel = btn.getAttribute('data-annam-cr-pick-route') || '';
		var pickup = btn.getAttribute('data-annam-cr-pickup') || '';
		var destination = btn.getAttribute('data-annam-cr-destination') || '';
		var price = btn.getAttribute('data-annam-cr-price') || '';
		var sourceNote = btn.getAttribute('data-annam-cr-source-note') || '';

		if (!pickup || !destination) {
			var parsed = splitRouteLabel(routeLabel);
			pickup = pickup || parsed.pickup;
			destination = destination || parsed.destination;
		}

		var routeInput = qs('#annam-cr-route-hero', heroForm);
		var pickupInput = qs('#annam-cr-pickup-hero', heroForm);
		var dropoffInput = qs('#annam-cr-dropoff-hero', heroForm);
		var sourceInput = qs('#annam-cr-source-note-hero', heroForm);
		var vehicleSelect = qs('#annam-cr-vehicle-hero', heroForm);
		var roundTrip = heroForm.querySelector('input[name="annam_cr_round_trip"]');

		if (routeInput) {
			routeInput.value = routeLabel;
		}
		if (pickupInput) {
			pickupInput.value = pickup;
		}
		if (dropoffInput) {
			dropoffInput.value = destination;
		}
		if (sourceInput) {
			sourceInput.value = sourceNote;
		}
		if (vehicleSelect && cfg.vehicleType) {
			vehicleSelect.value = cfg.vehicleType;
		}
		if (roundTrip) {
			roundTrip.checked = true;
		}

		if (btn.hasAttribute('data-annam-cr-price')) {
			trackPriceTableClick({
				vehicle_type: cfg.vehicleType || '',
				route_name: routeLabel,
				pickup: pickup,
				destination: destination,
				price: price,
			});
		}

		scrollToEl('#annam-cr-booking');
	}

	function initPricingSearch() {
		var search = qs('.annam-cr-pricing__search');
		var rows = qsa('[data-annam-cr-pricing-row]');
		var empty = qs('#annam-cr-pricing-empty');
		if (!search || !rows.length) {
			return;
		}

		function filterRows() {
			var q = search.value.trim().toLowerCase();
			var visible = 0;
			rows.forEach(function (row) {
				var hay = (row.getAttribute('data-search') || '').toLowerCase();
				var show = !q || hay.indexOf(q) !== -1;
				row.hidden = !show;
				if (show) {
					visible += 1;
				}
			});
			if (empty) {
				empty.hidden = visible > 0;
			}
		}

		search.addEventListener('input', filterRows);
		search.addEventListener('search', filterRows);
	}

	function applyDateDefaults(form) {
		var today = cfg.dateToday || '';
		if (!today) {
			return;
		}
		qsa('input[type="date"]', form).forEach(function (input) {
			input.value = today;
			input.min = today;
		});
	}

	function openDatePicker(input) {
		if (!input || input.type !== 'date') {
			return;
		}
		input.focus();
		if (typeof input.showPicker === 'function') {
			try {
				input.showPicker();
			} catch (err) {
				/* iOS < 16.4 / Safari: native focus opens picker. */
			}
		}
	}

	function initDateInputs() {
		qsa('.annam-cr-form .annam-cr-input-date, .annam-cr-form input[type="date"]').forEach(function (input) {
			if (cfg.dateToday && !input.value) {
				input.value = cfg.dateToday;
			}

			input.addEventListener('click', function () {
				openDatePicker(input);
			});

			var wrap = input.closest('.annam-cr-input-icon');
			if (wrap && !wrap.getAttribute('data-annam-cr-date-wrap')) {
				wrap.setAttribute('data-annam-cr-date-wrap', '1');
				wrap.addEventListener('click', function (e) {
					if (e.target === input) {
						return;
					}
					openDatePicker(input);
				});
			}
		});
	}

	function showNotice(form, message, isError) {
		var variant = form.getAttribute('data-variant') || 'hero';
		var notice =
			form.querySelector('#annam-cr-form-notice-' + variant) ||
			qs('#annam-cr-form-notice-' + variant);
		if (!notice) {
			return;
		}
		notice.hidden = false;
		notice.textContent = message;
		notice.className = 'annam-cr-form__ajax-notice annam-cr-notice annam-cr-notice--' + (isError ? 'error' : 'success');
	}

	function handleSubmit(form) {
		form.addEventListener('submit', function (e) {
			if (!cfg.ajaxUrl) {
				return;
			}
			e.preventDefault();

			var btn = form.querySelector('[type="submit"]');
			var original = btn ? btn.textContent : '';
			if (btn) {
				btn.disabled = true;
				btn.textContent = (cfg.i18n && cfg.i18n.sending) || 'Đang gửi...';
			}

			var data = new FormData(form);
			data.append('action', cfg.action || 'annam_car_rental_booking');
			if (cfg.nonce) {
				data.set('annam_cr_nonce', cfg.nonce);
			}
			if (cfg.pageUrl) {
				data.append('annam_cr_page_url', cfg.pageUrl);
			}

			fetch(cfg.ajaxUrl, {
				method: 'POST',
				body: data,
				credentials: 'same-origin',
			})
				.then(function (res) {
					return res.json();
				})
				.then(function (json) {
					if (json.success) {
						showNotice(form, (json.data && json.data.message) || 'OK', false);
						form.reset();
						applyDateDefaults(form);
						var roundTrip = form.querySelector('input[name="annam_cr_round_trip"]');
						if (roundTrip) {
							roundTrip.checked = true;
						}
						var sourceNote = form.querySelector('input[name="annam_cr_source_note"]');
						if (sourceNote) {
							sourceNote.value = '';
						}
					} else {
						var msg =
							(json.data && json.data.message) ||
							(cfg.i18n && cfg.i18n.submitError) ||
							'Lỗi';
						showNotice(form, msg, true);
					}
				})
				.catch(function () {
					showNotice(form, (cfg.i18n && cfg.i18n.submitError) || 'Lỗi', true);
				})
				.finally(function () {
					if (btn) {
						btn.disabled = false;
						btn.textContent = original;
					}
				});
		});
	}

	qsa('[data-annam-cr-scroll]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			scrollToEl(btn.getAttribute('data-annam-cr-scroll'));
		});
	});

	qsa('[data-annam-cr-pick-route]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			pickRouteFromButton(btn);
		});
	});

	function initHubPricingTabs() {
		var root = qs('[data-annam-cr-hub-pricing]');
		if (!root) {
			return;
		}

		var tabs = qsa('[data-annam-cr-hub-tab]', root);
		var panels = qsa('[data-annam-cr-hub-panel]', root);
		if (!tabs.length || !panels.length) {
			return;
		}

		tabs.forEach(function (tab) {
			tab.addEventListener('click', function () {
				var key = tab.getAttribute('data-annam-cr-hub-tab');
				tabs.forEach(function (t) {
					var active = t === tab;
					t.classList.toggle('is-active', active);
					t.setAttribute('aria-selected', active ? 'true' : 'false');
				});
				panels.forEach(function (panel) {
					panel.hidden = panel.getAttribute('data-annam-cr-hub-panel') !== key;
				});
			});
		});
	}

	function copyText(text) {
		if (!text) {
			return Promise.reject();
		}
		if (navigator.clipboard && navigator.clipboard.writeText) {
			return navigator.clipboard.writeText(text);
		}
		return new Promise(function (resolve, reject) {
			var ta = document.createElement('textarea');
			ta.value = text;
			ta.setAttribute('readonly', '');
			ta.style.position = 'absolute';
			ta.style.left = '-9999px';
			document.body.appendChild(ta);
			ta.select();
			try {
				document.execCommand('copy');
				resolve();
			} catch (err) {
				reject(err);
			}
			document.body.removeChild(ta);
		});
	}

	function initTrustCopy() {
		qsa('[data-annam-cr-copy]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var text = btn.getAttribute('data-annam-cr-copy') || '';
				var label = btn.querySelector('span');
				var original = label ? label.textContent : '';
				copyText(text)
					.then(function () {
						btn.classList.add('is-copied');
						if (label) {
							label.textContent = (cfg.i18n && cfg.i18n.copied) || 'Đã sao chép';
						}
						window.setTimeout(function () {
							btn.classList.remove('is-copied');
							if (label) {
								label.textContent = original;
							}
						}, 1800);
					})
					.catch(function () {
						/* silent */
					});
			});
		});
	}

	function initTrustGallery() {
		var root = qs('[data-annam-cr-trust-gallery]');
		if (!root) {
			return;
		}

		var dataEl = qs('[data-annam-cr-trust-data]', root);
		var items = [];
		if (dataEl) {
			try {
				items = JSON.parse(dataEl.textContent || '[]');
			} catch (err) {
				items = [];
			}
		}

		var mainBtn = qs('[data-annam-cr-trust-open]', root);
		var mainImg = qs('[data-annam-cr-trust-main-img]', root);
		var mainPlaceholder = qs('[data-annam-cr-trust-main-placeholder]', root);
		var thumbs = qsa('[data-annam-cr-trust-thumb]', root);
		var lightbox = qs('[data-annam-cr-trust-lightbox]');
		var lbImg = lightbox ? qs('[data-annam-cr-trust-lightbox-img]', lightbox) : null;
		var lbCaption = lightbox ? qs('[data-annam-cr-trust-lightbox-caption]', lightbox) : null;
		var activeIndex = 0;
		var autoplayTimer = null;
		var AUTOPLAY_MS = 3000;

		function setMain(index) {
			if (!items.length || index < 0 || index >= items.length) {
				return;
			}
			activeIndex = index;
			var item = items[index];
			thumbs.forEach(function (thumb, i) {
				var isActive = i === index;
				thumb.hidden = isActive;
				thumb.classList.toggle('is-active', isActive);
				thumb.setAttribute('aria-selected', isActive ? 'true' : 'false');
			});

			if (item.url) {
				if (!mainImg && mainBtn) {
					mainImg = document.createElement('img');
					mainImg.className = 'annam-cr-trust-gallery__main-img';
					mainImg.setAttribute('data-annam-cr-trust-main-img', '');
					mainImg.width = 960;
					mainImg.height = 600;
					mainImg.loading = 'lazy';
					mainImg.decoding = 'async';
					mainBtn.insertBefore(mainImg, mainBtn.firstChild);
				}
				if (mainImg) {
					mainImg.src = item.url;
					mainImg.alt = item.alt || item.label || '';
					mainImg.hidden = false;
				}
				if (mainPlaceholder) {
					mainPlaceholder.hidden = true;
				}
			} else if (mainPlaceholder) {
				var label = mainPlaceholder.querySelector('.annam-cr-trust-gallery__placeholder-label');
				if (label) {
					label.textContent = item.label || item.alt || '';
				}
				mainPlaceholder.hidden = false;
				if (mainImg) {
					mainImg.hidden = true;
				}
			}
		}

		function renderLightbox(index) {
			if (!lightbox || !lbImg || !items.length) {
				return;
			}
			var item = items[index];
			if (!item || !item.url) {
				return;
			}
			lbImg.src = item.url;
			lbImg.alt = item.alt || item.label || '';
			if (lbCaption) {
				lbCaption.textContent = item.alt || item.label || '';
			}
		}

		function openLightbox(index) {
			if (!lightbox) {
				return;
			}
			var item = items[index];
			if (!item || !item.url) {
				return;
			}
			activeIndex = index;
			renderLightbox(index);
			if (typeof lightbox.showModal === 'function') {
				lightbox.showModal();
			} else {
				lightbox.setAttribute('open', 'open');
			}
		}

		function closeLightbox() {
			if (!lightbox) {
				return;
			}
			if (typeof lightbox.close === 'function') {
				lightbox.close();
			} else {
				lightbox.removeAttribute('open');
			}
		}

		function stepMain(delta) {
			if (!items.length) {
				return;
			}
			var next = (activeIndex + delta + items.length) % items.length;
			setMain(next);
		}

		function stepLightbox(delta) {
			if (!items.length) {
				return;
			}
			var next = activeIndex;
			do {
				next = (next + delta + items.length) % items.length;
			} while (!items[next].url && next !== activeIndex);
			if (!items[next].url) {
				return;
			}
			activeIndex = next;
			setMain(next);
			renderLightbox(next);
		}

		function isLightboxOpen() {
			return lightbox && (lightbox.open || lightbox.hasAttribute('open'));
		}

		function stopAutoplay() {
			if (autoplayTimer) {
				window.clearInterval(autoplayTimer);
				autoplayTimer = null;
			}
		}

		function startAutoplay() {
			stopAutoplay();
			if (items.length < 2) {
				return;
			}
			autoplayTimer = window.setInterval(function () {
				if (isLightboxOpen()) {
					return;
				}
				stepMain(1);
			}, AUTOPLAY_MS);
		}

		function resetAutoplay() {
			if (isLightboxOpen()) {
				return;
			}
			startAutoplay();
		}

		thumbs.forEach(function (thumb) {
			thumb.addEventListener('click', function () {
				var index = parseInt(thumb.getAttribute('data-index') || '0', 10);
				setMain(index);
				resetAutoplay();
			});
		});

		if (mainBtn) {
			mainBtn.addEventListener('click', function () {
				var item = items[activeIndex];
				if (item && item.url) {
					stopAutoplay();
					openLightbox(activeIndex);
				}
			});
		}

		root.addEventListener('mouseenter', stopAutoplay);
		root.addEventListener('mouseleave', startAutoplay);
		root.addEventListener('focusin', stopAutoplay);
		root.addEventListener('focusout', function (e) {
			if (!root.contains(e.relatedTarget)) {
				startAutoplay();
			}
		});

		if (lightbox) {
			var closeBtn = qs('[data-annam-cr-trust-close]', lightbox);
			var prevBtn = qs('[data-annam-cr-trust-prev]', lightbox);
			var nextBtn = qs('[data-annam-cr-trust-next]', lightbox);
			if (closeBtn) {
				closeBtn.addEventListener('click', closeLightbox);
			}
			if (prevBtn) {
				prevBtn.addEventListener('click', function () {
					stepLightbox(-1);
				});
			}
			if (nextBtn) {
				nextBtn.addEventListener('click', function () {
					stepLightbox(1);
				});
			}
			lightbox.addEventListener('click', function (e) {
				if (e.target === lightbox) {
					closeLightbox();
				}
			});
			lightbox.addEventListener('cancel', function (e) {
				e.preventDefault();
				closeLightbox();
			});
			lightbox.addEventListener('close', startAutoplay);
		}

		if (items.length) {
			setMain(0);
			startAutoplay();
		}

		document.addEventListener('keydown', function (e) {
			if (!lightbox || !lightbox.open) {
				return;
			}
			if (e.key === 'ArrowLeft') {
				stepLightbox(-1);
			} else if (e.key === 'ArrowRight') {
				stepLightbox(1);
			}
		});
	}

	function initSeoContent() {
		qsa('.annam-cr-seo-content').forEach(function (section) {
			var btn = qs('.annam-cr-seo-content__toggle', section);
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

	qsa('[data-annam-cr-form]').forEach(handleSubmit);
	initDateInputs();
	initPricingSearch();
	initHubPricingTabs();
	initTrustGallery();
	initTrustCopy();
	initSeoContent();
})();
