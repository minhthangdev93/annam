/**
 * AJAX sort: Danh sách tour (mới nhất / giá tăng / giá giảm).
 */
(function () {
	"use strict";

	var cfg = typeof annamCategorySort === "object" ? annamCategorySort : null;
	if (!cfg || !cfg.ajaxUrl) {
		return;
	}

	var ORDERBY_KEY = "annam_category_orderby";

	function getSection() {
		return document.getElementById("annam-category-products");
	}

	function getLoop() {
		var section = getSection();
		return section ? section.querySelector("[data-annam-category-loop]") : null;
	}

	function getSelect() {
		var section = getSection();
		return section ? section.querySelector("[data-annam-category-sort]") : null;
	}

	function setLoading(on) {
		var loop = getLoop();
		var section = getSection();
		if (loop) {
			loop.classList.toggle("is-loading", !!on);
			loop.setAttribute("aria-busy", on ? "true" : "false");
		}
		if (section) {
			section.classList.toggle("annam-category-products--loading", !!on);
		}
	}

	function fetchProducts(orderby, paged) {
		var body = new FormData();
		body.append("action", "annam_category_products_sort");
		body.append("nonce", cfg.nonce);
		body.append("orderby", orderby);
		body.append("paged", String(paged || 1));
		if (cfg.taxonomy) {
			body.append("taxonomy", cfg.taxonomy);
		}
		if (cfg.termId) {
			body.append("term_id", String(cfg.termId));
		}

		return fetch(cfg.ajaxUrl, {
			method: "POST",
			credentials: "same-origin",
			body: body,
		}).then(function (res) {
			return res.json();
		});
	}

	function applyHtml(html) {
		var loop = getLoop();
		if (!loop) {
			return;
		}
		loop.innerHTML = html;
		bindPagination();
	}

	function bindPagination() {
		var loop = getLoop();
		if (!loop) {
			return;
		}
		loop.querySelectorAll(".woocommerce-pagination a").forEach(function (link) {
			link.addEventListener("click", onPaginationClick);
		});
	}

	function getPageFromUrl(url) {
		try {
			var u = new URL(url, window.location.origin);
			var p = u.searchParams.get("paged") || u.searchParams.get("page");
			if (p) {
				return parseInt(p, 10) || 1;
			}
			var m = u.pathname.match(/\/page\/(\d+)\//);
			if (m) {
				return parseInt(m[1], 10) || 1;
			}
		} catch (e) {
			/* ignore */
		}
		return 1;
	}

	function onPaginationClick(ev) {
		ev.preventDefault();
		var select = getSelect();
		var orderby = select ? select.value : "date";
		var page = getPageFromUrl(ev.currentTarget.href);
		load(orderby, page, true);
	}

	function getScrollTargetTop(el) {
		if (!el) {
			return 0;
		}
		var rect = el.getBoundingClientRect();
		var top = rect.top + window.pageYOffset;
		var header = document.querySelector(".site-header, #masthead");
		var headerOffset = 0;
		if (header) {
			headerOffset = header.getBoundingClientRect().height || 0;
		}
		return Math.max(0, top - headerOffset - 12);
	}

	function scrollSectionToTop() {
		var section = getSection();
		if (!section) {
			return;
		}
		window.scrollTo({
			top: getScrollTargetTop(section),
			behavior: "smooth",
		});
	}

	function load(orderby, paged, scrollToTop) {
		setLoading(true);
		fetchProducts(orderby, paged)
			.then(function (data) {
				if (!data || !data.success || !data.data || typeof data.data.html !== "string") {
					throw new Error("invalid");
				}
				try {
					sessionStorage.setItem(ORDERBY_KEY, orderby);
				} catch (e) {
					/* ignore */
				}
				applyHtml(data.data.html);
				if (scrollToTop) {
					scrollSectionToTop();
				}
			})
			.catch(function () {
				window.alert(cfg.labels && cfg.labels.error ? cfg.labels.error : "Error");
			})
			.finally(function () {
				setLoading(false);
			});
	}

	function init() {
		var select = getSelect();
		if (!select) {
			return;
		}

		var saved = "";
		try {
			saved = sessionStorage.getItem(ORDERBY_KEY) || "";
		} catch (e) {
			/* ignore */
		}
		if (saved && select.querySelector('option[value="' + saved + '"]')) {
			select.value = saved;
			if (saved !== "date") {
				load(saved, 1, false);
			}
		}

		select.addEventListener("change", function () {
			load(select.value, 1, true);
		});

		bindPagination();
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", init);
	} else {
		init();
	}
})();
