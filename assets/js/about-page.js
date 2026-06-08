/**
 * Trang Giới thiệu: lightbox gallery + slider đánh giá.
 */
(function () {
	'use strict';

	function openLightbox(src) {
		var lb = document.getElementById('annam-about-lightbox');
		var img = lb ? lb.querySelector('[data-annam-about-lightbox-img]') : null;
		if (!lb || !img || !src) {
			return;
		}
		img.setAttribute('src', src);
		img.setAttribute('alt', '');
		lb.hidden = false;
		document.documentElement.style.overflow = 'hidden';
	}

	function closeLightbox() {
		var lb = document.getElementById('annam-about-lightbox');
		var img = lb ? lb.querySelector('[data-annam-about-lightbox-img]') : null;
		if (!lb || !img) {
			return;
		}
		lb.hidden = true;
		img.removeAttribute('src');
		document.documentElement.style.overflow = '';
	}

	document.addEventListener('click', function (e) {
		var btn = e.target.closest('.annam-about-gallery__item');
		if (btn) {
			var full = btn.getAttribute('data-full');
			if (full) {
				openLightbox(full);
			}
			return;
		}
		if (e.target.closest('[data-annam-about-lightbox-close]')) {
			closeLightbox();
			return;
		}
		if (e.target.id === 'annam-about-lightbox') {
			closeLightbox();
		}
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			closeLightbox();
		}
	});

	var sliders = document.querySelectorAll('[data-annam-about-slider]');
	sliders.forEach(function (root) {
		var track = root.querySelector('.annam-about-reviews__track');
		var slides = track ? track.querySelectorAll('.annam-about-review-card') : null;
		if (!track || !slides || slides.length < 2) {
			return;
		}
		var i = 0;
		function slideStep() {
			var gap = parseFloat(window.getComputedStyle(track).gap) || 16;
			return root.getBoundingClientRect().width + gap;
		}
		function go(dir) {
			i += dir;
			if (i < 0) {
				i = slides.length - 1;
			}
			if (i >= slides.length) {
				i = 0;
			}
			track.style.transform = 'translateX(' + -i * slideStep() + 'px)';
		}
		root.querySelectorAll('[data-dir]').forEach(function (navBtn) {
			navBtn.addEventListener('click', function () {
				var d = parseInt(navBtn.getAttribute('data-dir'), 10) || 1;
				go(d);
			});
		});
		window.addEventListener(
			'resize',
			function () {
				track.style.transform = 'translateX(' + -i * slideStep() + 'px)';
			},
			{ passive: true }
		);
	});
})();
