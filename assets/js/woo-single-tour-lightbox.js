/**
 * Tour product header gallery: vanilla JS lightbox from img[data-full] in .annam-tour-gallery.
 */
(function () {
	'use strict';

	function onReady(fn) {
		if (document.readyState !== 'loading') {
			fn();
		} else {
			document.addEventListener('DOMContentLoaded', fn);
		}
	}

	onReady(function () {
		var root = document.querySelector('.annam-tour-gallery');
		if (!root) {
			return;
		}

		var imgs = Array.prototype.slice.call(root.querySelectorAll('img[data-full]'));
		if (!imgs.length) {
			return;
		}

		var slides = imgs.map(function (img) {
			return {
				url: img.getAttribute('data-full') || '',
				alt: img.getAttribute('alt') || '',
			};
		});

		var lb = document.createElement('div');
		lb.className = 'annam-tour-lightbox';
		lb.id = 'annam-tour-lightbox';
		lb.setAttribute('role', 'dialog');
		lb.setAttribute('aria-modal', 'true');
		lb.setAttribute('aria-label', 'Thư viện ảnh');
		lb.setAttribute('hidden', 'hidden');
		lb.innerHTML =
			'<div class="annam-tour-lightbox__backdrop" data-annam-lb-close="1"></div>' +
			'<div class="annam-tour-lightbox__wrap">' +
			'<button type="button" class="annam-tour-lightbox__close" data-annam-lb-close="1" aria-label="Đóng">&times;</button>' +
			'<button type="button" class="annam-tour-lightbox__prev" aria-label="Ảnh trước">&#8249;</button>' +
			'<div class="annam-tour-lightbox__stage">' +
			'<img class="annam-tour-lightbox__img" alt="" decoding="async" />' +
			'</div>' +
			'<button type="button" class="annam-tour-lightbox__next" aria-label="Ảnh sau">&#8250;</button>' +
			'</div>';
		document.body.appendChild(lb);

		var backdrop = lb.querySelector('.annam-tour-lightbox__backdrop');
		var wrap = lb.querySelector('.annam-tour-lightbox__wrap');
		var imgEl = lb.querySelector('.annam-tour-lightbox__img');
		var btnClose = lb.querySelector('.annam-tour-lightbox__close');
		var btnPrev = lb.querySelector('.annam-tour-lightbox__prev');
		var btnNext = lb.querySelector('.annam-tour-lightbox__next');

		var current = 0;
		var open = false;
		var lastFocus = null;

		function slideCount() {
			return slides.length;
		}

		function updateNav() {
			var n = slideCount();
			var hide = n <= 1;
			btnPrev.hidden = hide;
			btnNext.hidden = hide;
			btnPrev.disabled = hide;
			btnNext.disabled = hide;
		}

		function render() {
			var s = slides[current];
			if (!s || !s.url) {
				return;
			}
			imgEl.setAttribute('src', s.url);
			imgEl.setAttribute('alt', s.alt);
		}

		function trapFocus(e) {
			if (!open || e.key !== 'Tab') {
				return;
			}
			var focusables = lb.querySelectorAll(
				'button:not([disabled]):not([hidden]), [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
			);
			var list = Array.prototype.slice.call(focusables).filter(function (el) {
				return el.offsetParent !== null || el === btnClose;
			});
			if (!list.length) {
				return;
			}
			if (list.length <= 1) {
				return;
			}
			var first = list[0];
			var last = list[list.length - 1];
			if (e.shiftKey) {
				if (document.activeElement === first) {
					e.preventDefault();
					last.focus();
				}
			} else if (document.activeElement === last) {
				e.preventDefault();
				first.focus();
			}
		}

		function onKeydown(e) {
			if (!open) {
				return;
			}
			if (e.key === 'Escape') {
				e.preventDefault();
				closeLb();
				return;
			}
			if (e.key === 'ArrowLeft' && slideCount() > 1) {
				e.preventDefault();
				prev();
			}
			if (e.key === 'ArrowRight' && slideCount() > 1) {
				e.preventDefault();
				next();
			}
			trapFocus(e);
		}

		function lockScroll(lock) {
			document.documentElement.classList.toggle('annam-tour-lightbox--scroll-lock', lock);
			document.body.classList.toggle('annam-tour-lightbox--scroll-lock', lock);
		}

		function openAt(index) {
			var n = slideCount();
			if (!n) {
				return;
			}
			current = ((index % n) + n) % n;
			lastFocus = document.activeElement;
			render();
			updateNav();
			lb.removeAttribute('hidden');
			open = true;
			lockScroll(true);
			btnClose.focus();
		}

		function closeLb() {
			if (!open) {
				return;
			}
			lb.setAttribute('hidden', 'hidden');
			open = false;
			lockScroll(false);
			imgEl.removeAttribute('src');
			if (lastFocus && typeof lastFocus.focus === 'function') {
				lastFocus.focus();
			}
		}

		function prev() {
			var n = slideCount();
			if (n < 2) {
				return;
			}
			current = (current - 1 + n) % n;
			render();
		}

		function next() {
			var n = slideCount();
			if (n < 2) {
				return;
			}
			current = (current + 1) % n;
			render();
		}

		function indexOfImg(targetImg) {
			return imgs.indexOf(targetImg);
		}

		root.addEventListener('click', function (e) {
			var t = e.target;
			if (!t || !t.closest) {
				return;
			}
			var moreBtn = t.closest('[data-annam-lightbox-more]');
			if (moreBtn) {
				e.preventDefault();
				var lastSide = root.querySelector('.annam-tour-gallery__side-item--last img[data-full]');
				var idx = lastSide ? indexOfImg(lastSide) : 0;
				if (idx < 0) {
					idx = 0;
				}
				openAt(idx);
				return;
			}
			var trigger = t.closest('.annam-tour-gallery__trigger');
			if (trigger && root.contains(trigger)) {
				var innerImg = trigger.querySelector('img[data-full]');
				if (innerImg) {
					e.preventDefault();
					var i = indexOfImg(innerImg);
					if (i >= 0) {
						openAt(i);
					}
				}
			}
		});

		lb.addEventListener('click', function (e) {
			var closeEl = e.target.closest('[data-annam-lb-close]');
			if (closeEl) {
				e.preventDefault();
				closeLb();
			}
		});

		btnPrev.addEventListener('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			prev();
		});
		btnNext.addEventListener('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			next();
		});
		btnClose.addEventListener('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			closeLb();
		});

		document.addEventListener('keydown', onKeydown);
	});
})();
