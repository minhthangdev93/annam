/**
 * Home product sections: carousel controls and scroll indicator.
 */
(function () {
	'use strict';

	function clamp(value, min, max) {
		return Math.min(Math.max(value, min), max);
	}

	function getViewport(section) {
		return section.querySelector('.annam-home-product-section__viewport');
	}

	function getSlides(section) {
		return Array.prototype.slice.call(section.querySelectorAll('.annam-home-product-section__grid > li.product'));
	}

	function getScrollIndicator(section) {
		return section.querySelector('.annam-home-product-section__progress-bar');
	}

	function getNearestSlideIndex(viewport, slides) {
		var viewportRect = viewport.getBoundingClientRect();
		var viewportCenter = viewportRect.left + (viewportRect.width / 2);
		var nearestIndex = 0;
		var nearestDistance = Infinity;

		slides.forEach(function (slide, index) {
			var slideRect = slide.getBoundingClientRect();
			var slideCenter = slideRect.left + (slideRect.width / 2);
			var distance = Math.abs(slideCenter - viewportCenter);
			if (distance < nearestDistance) {
				nearestDistance = distance;
				nearestIndex = index;
			}
		});

		return nearestIndex;
	}

	function scrollToSlide(viewport, slide, behavior) {
		var viewportRect = viewport.getBoundingClientRect();
		var slideRect = slide.getBoundingClientRect();
		var targetLeft = viewport.scrollLeft + slideRect.left - viewportRect.left - ((viewportRect.width - slideRect.width) / 2);
		var maxScroll = Math.max(0, viewport.scrollWidth - viewport.clientWidth);

		viewport.scrollTo({
			left: clamp(targetLeft, 0, maxScroll),
			behavior: behavior || 'smooth',
		});
	}

	function updateScrollIndicator(section, viewport) {
		var indicator = getScrollIndicator(section);
		var maxScroll = viewport.scrollWidth - viewport.clientWidth;
		var thumbRatio = viewport.scrollWidth > 0 ? viewport.clientWidth / viewport.scrollWidth : 1;
		var thumbWidth = clamp(thumbRatio * 100, 12, 100);
		var travel = 100 - thumbWidth;
		var position = maxScroll > 0 ? (viewport.scrollLeft / maxScroll) * travel : 0;

		if (!indicator) {
			return;
		}

		indicator.style.setProperty('--annam-home-scrollbar-width', thumbWidth + '%');
		indicator.style.setProperty('--annam-home-scrollbar-left', position + '%');
	}

	function bindFeatureCarousel(section) {
		var viewport = getViewport(section);
		var slides = getSlides(section);
		var prev = section.querySelector('.annam-home-product-section__nav--prev');
		var next = section.querySelector('.annam-home-product-section__nav--next');
		var rafId = 0;
		var resizeTimer = 0;

		if (!viewport || slides.length < 1) {
			return;
		}

		function syncState() {
			rafId = 0;
			updateScrollIndicator(section, viewport);
		}

		function queueSync() {
			if (rafId) {
				return;
			}
			rafId = window.requestAnimationFrame(syncState);
		}

		function move(direction) {
			var activeIndex = getNearestSlideIndex(viewport, slides);
			var targetIndex = (activeIndex + direction + slides.length) % slides.length;

			scrollToSlide(viewport, slides[targetIndex]);
		}

		if (prev) {
			prev.addEventListener('click', function () {
				move(-1);
			});
		}

		if (next) {
			next.addEventListener('click', function () {
				move(1);
			});
		}

		viewport.addEventListener('scroll', queueSync, { passive: true });

		window.addEventListener('resize', function () {
			window.clearTimeout(resizeTimer);
			resizeTimer = window.setTimeout(function () {
				var activeIndex = getNearestSlideIndex(viewport, slides);
				updateScrollIndicator(section, viewport);
				scrollToSlide(viewport, slides[activeIndex], 'auto');
			}, 140);
		});

		updateScrollIndicator(section, viewport);
		window.setTimeout(function () {
			var initialIndex = Math.floor(slides.length / 2);
			scrollToSlide(viewport, slides[initialIndex], 'auto');
			updateScrollIndicator(section, viewport);
		}, 60);
	}

	function bindSection(section) {
		var sectionRoot = section.closest('.annam-home-product-section') || section;

		if (sectionRoot.classList.contains('annam-home-product-section--feature-carousel')) {
			bindFeatureCarousel(sectionRoot);
			return;
		}

		var viewport = getViewport(section);
		var prev = section.querySelector('.annam-home-product-section__nav--prev');
		var next = section.querySelector('.annam-home-product-section__nav--next');

		if (!viewport) {
			return;
		}

		function scrollStep(direction) {
			var delta = Math.max(120, Math.floor(viewport.clientWidth * 0.92));
			viewport.scrollBy({
				left: direction * delta,
				behavior: 'smooth',
			});
		}

		if (prev) {
			prev.addEventListener('click', function () {
				scrollStep(-1);
			});
		}

		if (next) {
			next.addEventListener('click', function () {
				scrollStep(1);
			});
		}
	}

	function init() {
		var sections = document.querySelectorAll('[data-annam-section-slider="1"]');
		if (!sections.length) {
			return;
		}

		sections.forEach(function (section) {
			bindSection(section);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
