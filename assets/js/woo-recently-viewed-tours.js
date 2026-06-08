/**
 * Desktop: scroll “Tour đã xem gần đây” viewport by one page; update nav disabled state.
 */
(function () {
	"use strict";

	function initSection(section) {
		var vp = section.querySelector("[data-annam-rv-viewport]");
		var prev = section.querySelector("[data-annam-rv-prev]");
		var next = section.querySelector("[data-annam-rv-next]");
		if (!vp || !prev || !next) {
			return;
		}

		function maxScroll() {
			return Math.max(0, vp.scrollWidth - vp.clientWidth);
		}

		function updateNav() {
			var left = vp.scrollLeft;
			var max = maxScroll();
			prev.disabled = left <= 1;
			next.disabled = left >= max - 1;
		}

		function scrollByPage(dir) {
			vp.scrollLeft += dir * vp.clientWidth;
		}

		prev.addEventListener("click", function () {
			scrollByPage(-1);
		});
		next.addEventListener("click", function () {
			scrollByPage(1);
		});
		vp.addEventListener("scroll", updateNav, { passive: true });
		window.addEventListener("resize", updateNav, { passive: true });
		updateNav();
	}

	document.querySelectorAll("[data-annam-recently-viewed]").forEach(initSection);
})();
