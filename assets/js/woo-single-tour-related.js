/**
 * Desktop carousel: scroll viewport by one page; update button disabled state.
 */
(function () {
	"use strict";

	function initSection(section) {
		var vp = section.querySelector("[data-annam-related-viewport]");
		var prev = section.querySelector("[data-annam-related-prev]");
		var next = section.querySelector("[data-annam-related-next]");
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

	document.querySelectorAll("[data-annam-tour-related]").forEach(initSection);
})();
