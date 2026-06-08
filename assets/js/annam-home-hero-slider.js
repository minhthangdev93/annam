/**
 * Hero slider: autoplay 3000ms, fade, dots, prev/next.
 * Tạm dừng khi hover (chỉ thiết bị có hover); không dùng focusin để tránh chặn autoplay khi tabindex nhận focus.
 */
(function () {
	'use strict';

	var SEL_ROOT = '[data-annam-home-hero-slider]';

	function qs(root, sel) {
		return root.querySelector(sel);
	}

	function qsa(root, sel) {
		return Array.prototype.slice.call(root.querySelectorAll(sel));
	}

	function init(root) {
		if (!root || root.getAttribute('data-annam-slider-ready') === '1') {
			return;
		}
		root.setAttribute('data-annam-slider-ready', '1');

		var slides = qsa(root, '.annam-home-hero__slide');
		if (slides.length < 1) {
			return;
		}

		var dots = qsa(root, '.annam-home-hero__dot');
		var btnPrev = qs(root, '.annam-home-hero__nav--prev');
		var btnNext = qs(root, '.annam-home-hero__nav--next');

		var canHover =
			typeof window.matchMedia === 'function' &&
			window.matchMedia('(hover: hover) and (pointer: fine)').matches;

		var idx = 0;
		var autoplayMs = 3000;
		var timer = null;

		function clearAutoplay() {
			if (timer !== null) {
				window.clearTimeout(timer);
				timer = null;
			}
		}

		function setActive(i) {
			var n = slides.length;
			if (n < 1) {
				return;
			}
			idx = ((i % n) + n) % n;

			slides.forEach(function (el, j) {
				var on = j === idx;
				el.classList.toggle('is-active', on);
				el.setAttribute('aria-hidden', on ? 'false' : 'true');
			});

			dots.forEach(function (dot, j) {
				var on = j === idx;
				dot.classList.toggle('is-active', on);
				dot.setAttribute('aria-selected', on ? 'true' : 'false');
				if (on) {
					dot.setAttribute('tabindex', '0');
				} else {
					dot.setAttribute('tabindex', '-1');
				}
			});
		}

		function goTo(i) {
			setActive(i);
		}

		function next() {
			goTo(idx + 1);
		}

		function prev() {
			goTo(idx - 1);
		}

		function startAutoplay() {
			clearAutoplay();
			if (slides.length < 2) {
				return;
			}
			/* Một timer tại một thời điểm (chuỗi setTimeout), tránh setInterval lặp vô hạn */
			function schedule() {
				timer = window.setTimeout(function () {
					next();
					schedule();
				}, autoplayMs);
			}
			schedule();
		}

		function pauseAutoplay() {
			clearAutoplay();
		}

		if (btnNext) {
			btnNext.addEventListener('click', function () {
				next();
				startAutoplay();
			});
		}
		if (btnPrev) {
			btnPrev.addEventListener('click', function () {
				prev();
				startAutoplay();
			});
		}

		dots.forEach(function (dot, j) {
			dot.addEventListener('click', function () {
				goTo(j);
				startAutoplay();
			});
		});

		if (canHover) {
			root.addEventListener('mouseenter', pauseAutoplay);
			root.addEventListener('mouseleave', startAutoplay);
		}

		document.addEventListener(
			'keydown',
			function (e) {
				if (!root.contains(e.target)) {
					return;
				}
				if (e.key === 'ArrowRight') {
					e.preventDefault();
					next();
					startAutoplay();
				} else if (e.key === 'ArrowLeft') {
					e.preventDefault();
					prev();
					startAutoplay();
				}
			},
			true
		);

		setActive(0);
		/* Chạy sau tick hiện tại — tránh xung đột với focus/layout khi load */
		window.setTimeout(startAutoplay, 0);

		document.addEventListener('visibilitychange', function () {
			if (document.hidden) {
				pauseAutoplay();
			} else {
				startAutoplay();
			}
		});
	}

	function boot() {
		var roots = document.querySelectorAll(SEL_ROOT);
		for (var i = 0; i < roots.length; i++) {
			init(roots[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
