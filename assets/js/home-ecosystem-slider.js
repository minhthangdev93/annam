/**
 * Slider logo section "Hệ sinh thái" — autoplay, loop, vuốt ngang (vanilla).
 */
(function () {
	'use strict';

	var SELECTOR_ROOT = '[data-annam-ecosystem-slider]';

	function parseGapPx(track) {
		var s = window.getComputedStyle(track);
		var g = s.gap || s.columnGap || '14px';
		var m = String(g).match(/^([\d.]+)px$/);
		return m ? parseFloat(m[1], 10) : 14;
	}

	function getSlideStep(track, firstItem) {
		if (!firstItem) {
			return 0;
		}
		return firstItem.getBoundingClientRect().width + parseGapPx(track);
	}

	function countVisibleApprox(viewportW, step) {
		if (step <= 0) {
			return 1;
		}
		return Math.max(1, Math.floor((viewportW + 1) / step));
	}

	function initOne(root) {
		if (root.getAttribute('data-annam-ecosystem-initialized') === '1') {
			return;
		}
		root.setAttribute('data-annam-ecosystem-initialized', '1');

		var viewport = root.querySelector('.annam-ecosystem__viewport');
		var track = root.querySelector('.annam-ecosystem__track');
		if (!viewport || !track) {
			return;
		}

		var intervalMs = parseInt(root.getAttribute('data-interval-ms'), 10);
		if (!intervalMs || intervalMs < 1500) {
			intervalMs = 3000;
		}

		var prefersReduced =
			window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var hoverPause =
			window.matchMedia &&
			window.matchMedia('(hover: hover) and (pointer: fine)').matches;

		var index = 0;
		var step = 0;
		var originals = [];
		var n = 0;
		var timer = null;
		var needsAutoplay = false;
		var ptrId = null;
		var ptrStartX = 0;
		var ptrStartY = 0;
		var ptrLocked = false;

		function clearTimer() {
			if (timer !== null) {
				window.clearInterval(timer);
				timer = null;
			}
		}

		function setTransition(on) {
			var dur = prefersReduced ? '0.01ms' : '0.45s';
			track.style.transition = on ? 'transform ' + dur + ' ease' : 'none';
		}

		function applyTransform() {
			track.style.transform = 'translate3d(' + -index * step + 'px,0,0)';
		}

		function removeClones() {
			track.querySelectorAll('.annam-ecosystem__item--clone').forEach(function (el) {
				el.remove();
			});
		}

		function appendClones() {
			originals.forEach(function (orig) {
				var c = orig.cloneNode(true);
				c.classList.add('annam-ecosystem__item--clone');
				c.removeAttribute('data-annam-ecosystem-original');
				c.setAttribute('aria-hidden', 'true');
				var img = c.querySelector('img');
				if (img) {
					img.setAttribute('tabindex', '-1');
				}
				track.appendChild(c);
			});
		}

		function onLoopSnap() {
			if (index < n) {
				return;
			}
			setTransition(false);
			index = 0;
			applyTransform();
			void track.offsetWidth;
			setTransition(true);
		}

		function armSnapAfterLoop() {
			function onTe(e) {
				if (e.target !== track) {
					return;
				}
				if (e.propertyName && e.propertyName !== 'transform') {
					return;
				}
				track.removeEventListener('transitionend', onTe);
				onLoopSnap();
			}
			track.addEventListener('transitionend', onTe);
		}

		/** Bước tới (autoplay + vuốt); không phụ thuộc needsAutoplay. */
		function advanceNext() {
			if (n < 2) {
				return;
			}
			setTransition(true);
			index += 1;
			applyTransform();
			if (index >= n) {
				armSnapAfterLoop();
			}
		}

		/** Bước lui (vuốt). */
		function advancePrev() {
			if (n < 2) {
				return;
			}
			if (index <= 0) {
				setTransition(false);
				index = n;
				applyTransform();
				void track.offsetWidth;
				setTransition(true);
				index = n - 1;
				applyTransform();
				return;
			}
			setTransition(true);
			index -= 1;
			applyTransform();
		}

		function tickAutoplay() {
			if (!needsAutoplay) {
				return;
			}
			advanceNext();
		}

		function startAutoplay() {
			clearTimer();
			if (!needsAutoplay || prefersReduced) {
				return;
			}
			timer = window.setInterval(tickAutoplay, intervalMs);
		}

		function rebuild() {
			clearTimer();
			removeClones();
			originals = Array.prototype.slice.call(
				track.querySelectorAll('.annam-ecosystem__item[data-annam-ecosystem-original]')
			);
			n = originals.length;
			if (n === 0) {
				return;
			}

			appendClones();

			window.requestAnimationFrame(function () {
				step = getSlideStep(track, originals[0]);
				var vis = countVisibleApprox(viewport.getBoundingClientRect().width, step);
				needsAutoplay = n > vis && !prefersReduced;

				index = Math.min(index, Math.max(0, n - 1));
				setTransition(false);
				applyTransform();
				void track.offsetWidth;
				setTransition(true);

				if (needsAutoplay) {
					startAutoplay();
				}
			});
		}

		var ro = typeof ResizeObserver !== 'undefined' ? new ResizeObserver(rebuild) : null;
		if (ro) {
			ro.observe(viewport);
		}

		rebuild();
		window.addEventListener('load', rebuild, { once: true });

		if (hoverPause) {
			root.addEventListener('mouseenter', clearTimer);
			root.addEventListener('mouseleave', startAutoplay);
		}

		function scheduleResumeAutoplay() {
			if (needsAutoplay) {
				window.setTimeout(startAutoplay, intervalMs);
			}
		}

		function onPtrDown(e) {
			if (ptrId !== null) {
				return;
			}
			if (e.pointerType === 'mouse' && e.button !== 0) {
				return;
			}
			clearTimer();
			ptrId = e.pointerId;
			ptrStartX = e.clientX;
			ptrStartY = e.clientY;
			ptrLocked = false;
			try {
				viewport.setPointerCapture(e.pointerId);
			} catch (err) {
				/* ignore */
			}
		}

		function onPtrMove(e) {
			if (e.pointerId !== ptrId) {
				return;
			}
			var dx = e.clientX - ptrStartX;
			var dy = e.clientY - ptrStartY;
			if (!ptrLocked && Math.abs(dx) > 12 && Math.abs(dx) > Math.abs(dy) + 6) {
				ptrLocked = true;
			}
			if (ptrLocked) {
				e.preventDefault();
			}
		}

		function endPtrSwipe(e) {
			if (e.pointerId !== ptrId) {
				return;
			}
			try {
				viewport.releasePointerCapture(e.pointerId);
			} catch (err2) {
				/* ignore */
			}
			ptrId = null;
			var dx = e.clientX - ptrStartX;
			ptrLocked = false;
			if (n < 2 || Math.abs(dx) < 28) {
				scheduleResumeAutoplay();
				return;
			}
			clearTimer();
			if (dx < 0) {
				advanceNext();
			} else {
				advancePrev();
			}
			scheduleResumeAutoplay();
		}

		if (typeof window.PointerEvent !== 'undefined') {
			viewport.addEventListener('pointerdown', onPtrDown, { passive: true });
			viewport.addEventListener('pointermove', onPtrMove, { passive: false });
			viewport.addEventListener('pointerup', endPtrSwipe, { passive: true });
			viewport.addEventListener('pointercancel', endPtrSwipe, { passive: true });
			viewport.addEventListener('lostpointercapture', function (e) {
				if (e.pointerId === ptrId) {
					ptrId = null;
					ptrLocked = false;
					scheduleResumeAutoplay();
				}
			});
		} else {
			var tsx = 0;
			var tsy = 0;
			var touchPan = false;
			viewport.addEventListener(
				'touchstart',
				function (ev) {
					if (ev.touches.length !== 1) {
						return;
					}
					clearTimer();
					tsx = ev.touches[0].clientX;
					tsy = ev.touches[0].clientY;
					touchPan = false;
				},
				{ passive: true }
			);
			viewport.addEventListener(
				'touchmove',
				function (ev) {
					if (ev.touches.length !== 1) {
						return;
					}
					var ddx = ev.touches[0].clientX - tsx;
					var ddy = ev.touches[0].clientY - tsy;
					if (!touchPan && Math.abs(ddx) > 14 && Math.abs(ddx) > Math.abs(ddy) + 8) {
						touchPan = true;
					}
					if (touchPan) {
						ev.preventDefault();
					}
				},
				{ passive: false }
			);
			viewport.addEventListener(
				'touchend',
				function (ev) {
					if (!ev.changedTouches.length) {
						return;
					}
					var ddx = ev.changedTouches[0].clientX - tsx;
					if (n < 2 || Math.abs(ddx) < 28) {
						scheduleResumeAutoplay();
						return;
					}
					clearTimer();
					if (ddx < 0) {
						advanceNext();
					} else {
						advancePrev();
					}
					scheduleResumeAutoplay();
				},
				{ passive: true }
			);
			viewport.addEventListener(
				'touchcancel',
				function () {
					touchPan = false;
					scheduleResumeAutoplay();
				},
				{ passive: true }
			);
		}
	}

	function initAll() {
		document.querySelectorAll(SELECTOR_ROOT).forEach(initOne);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAll);
	} else {
		initAll();
	}
})();
