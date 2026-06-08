/**
 * Expand / collapse intro description (category / tag / shop + trang chủ tĩnh).
 */
(function () {
	"use strict";

	var LABEL_LESS = "Thu gọn";

	function initIntroToggle() {
		document.querySelectorAll(".annam-category-intro").forEach(function (section) {
			var btn = section.querySelector(".annam-category-intro__toggle");
			if (!btn) {
				return;
			}

			var initial = (btn.textContent || "").trim() || "Xem thêm";
			if (!btn.getAttribute("data-label-more")) {
				btn.setAttribute("data-label-more", initial);
			}
			var labelMore = btn.getAttribute("data-label-more") || initial;
			var labelLess = btn.getAttribute("data-label-less") || LABEL_LESS;

			btn.addEventListener("click", function () {
				var expanded = section.classList.toggle("is-expanded");
				btn.setAttribute("aria-expanded", expanded ? "true" : "false");
				btn.textContent = expanded ? labelLess : labelMore;
			});
		});
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initIntroToggle);
	} else {
		initIntroToggle();
	}
})();
