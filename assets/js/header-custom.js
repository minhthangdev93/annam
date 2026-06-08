/**
 * Header custom: mobile drawer + search panel + mega (touch).
 * Khởi tạo sau DOMContentLoaded để chắc chắn DOM đã sẵn sàng.
 */
document.addEventListener("DOMContentLoaded", function () {
	"use strict";

	var PLACEHOLDER = "Bạn muốn khám phá nơi nào?";

	// Không phụ thuộc vào wrapper class; tìm theo data-attr + id.
	var btnMenu = document.querySelector("[data-annam-header-menu]");
	var btnSearch = document.querySelector("[data-annam-header-search]");
	var drawer = document.getElementById("annam-site-header-drawer");
	var searchPanel = document.getElementById("annam-site-header-search-panel");
	var headerRoot = document.querySelector(".annam-site-header");

	function getCurrentLanguageCode(defaultLang) {
		var currentLang = document.documentElement.getAttribute("lang") || defaultLang;
		var googtransMatches = document.cookie.match(/(^|;)\s*googtrans=([^;]*)(;|$)/);

		if (googtransMatches && googtransMatches[2]) {
			var parts = googtransMatches[2].split("/");
			if (parts[2]) {
				currentLang = parts[2];
			}
		}

		return currentLang || defaultLang;
	}

	function syncLanguageSwitcher(switcher) {
		if (!switcher) {
			return;
		}

		var currentLabelNodes = switcher.querySelectorAll("[data-annam-language-current]");
		if (!currentLabelNodes.length) {
			return;
		}

		var defaultLang = currentLabelNodes[0].getAttribute("data-default-lang") || "";
		var currentLang = getCurrentLanguageCode(defaultLang);
		var currentLink = switcher.querySelector('[data-gt-lang="' + currentLang + '"]');
		var currentLabel = "";

		switcher.querySelectorAll(".annam-language-switcher__item").forEach(function (item) {
			item.classList.remove("gt-current-lang", "current-lang");
		});

		if (currentLink) {
			var currentItem = currentLink.closest(".annam-language-switcher__item");
			currentLabel = (currentLink.textContent || "").trim();
			if (currentItem) {
				currentItem.classList.add("gt-current-lang");
			}
		}

		if (!currentLabel) {
			currentLabel = currentLabelNodes[0].getAttribute("data-default-label") || "";
		}

		switcher.querySelectorAll(".annam-language-switcher__trigger-label").forEach(function (node) {
			node.textContent = currentLabel;
		});

		currentLabelNodes.forEach(function (node) {
			node.textContent = currentLabel;
		});
	}

	function syncAllLanguageSwitchers() {
		if (!headerRoot) {
			return;
		}

		headerRoot.querySelectorAll(".annam-language-switcher").forEach(syncLanguageSwitcher);
	}

	function applySearchPlaceholder(scope) {
		if (!scope) {
			return;
		}
		scope.querySelectorAll('input[type="search"], .search-field').forEach(function (input) {
			input.setAttribute("placeholder", PLACEHOLDER);
		});
	}

	// Apply cho search desktop + mobile (kể cả khi panel đang hidden).
	applySearchPlaceholder(document);
	syncAllLanguageSwitchers();

	function setDrawer(open) {
		if (!drawer) {
			return;
		}
		if (open) {
			drawer.removeAttribute("hidden");
			drawer.setAttribute("aria-hidden", "false");
			document.body.classList.add("annam-header-drawer-open");
			if (btnMenu) {
				btnMenu.setAttribute("aria-expanded", "true");
			}
		} else {
			drawer.setAttribute("hidden", "hidden");
			drawer.setAttribute("aria-hidden", "true");
			document.body.classList.remove("annam-header-drawer-open");
			if (btnMenu) {
				btnMenu.setAttribute("aria-expanded", "false");
			}
			drawer.querySelectorAll(".annam-site-header__drawer-sub").forEach(function (ul) {
				ul.setAttribute("hidden", "hidden");
				ul.setAttribute("aria-hidden", "true");
			});
			drawer.querySelectorAll(".annam-site-header__drawer-toggle").forEach(function (b) {
				b.setAttribute("aria-expanded", "false");
			});
			drawer.querySelectorAll("li.is-open").forEach(function (li) {
				li.classList.remove("is-open");
			});
		}
	}

	function setSearch(open) {
		if (!searchPanel) {
			return;
		}
		if (open) {
			searchPanel.removeAttribute("hidden");
			if (btnSearch) {
				btnSearch.setAttribute("aria-expanded", "true");
			}
			// Focus vào ô tìm kiếm và set placeholder thân thiện.
			var input = searchPanel.querySelector('input[type="search"], .search-field');
			if (input) {
				input.setAttribute("placeholder", PLACEHOLDER);
				input.focus();
			}
		} else {
			searchPanel.setAttribute("hidden", "hidden");
			if (btnSearch) {
				btnSearch.setAttribute("aria-expanded", "false");
			}
		}
	}

	if (btnMenu && drawer) {
		btnMenu.addEventListener("click", function () {
			var open = drawer.hasAttribute("hidden");
			setSearch(false);
			setDrawer(open);
		});
	}

	if (btnSearch && searchPanel) {
		btnSearch.addEventListener("click", function () {
			var open = searchPanel.hasAttribute("hidden");
			setDrawer(false);
			setSearch(open);
		});
	}

	if (drawer) {
		// Mở sẵn các nhánh chứa trang hiện tại (current / ancestor).
		drawer.querySelectorAll("li.menu-item-has-children.current-menu-ancestor, li.menu-item-has-children.current_page_ancestor").forEach(function (li) {
			var btn = li.querySelector(".annam-site-header__drawer-toggle");
			var sid = btn && btn.getAttribute("aria-controls");
			if (!btn || !sid) {
				return;
			}
			var sub = document.getElementById(sid);
			if (!sub) {
				return;
			}
			sub.removeAttribute("hidden");
			sub.setAttribute("aria-hidden", "false");
			btn.setAttribute("aria-expanded", "true");
			li.classList.add("is-open");
		});

		drawer.addEventListener("click", function (e) {
			var btn = e.target.closest(".annam-site-header__drawer-toggle");
			if (!btn) {
				return;
			}
			var li = btn.closest("li");
			var sid = btn.getAttribute("aria-controls");
			if (!sid) {
				return;
			}
			var sub = document.getElementById(sid);
			if (!sub) {
				return;
			}
			var expanded = btn.getAttribute("aria-expanded") === "true";
			if (expanded) {
				sub.setAttribute("hidden", "hidden");
				sub.setAttribute("aria-hidden", "true");
				btn.setAttribute("aria-expanded", "false");
				if (li) {
					li.classList.remove("is-open");
				}
			} else {
				sub.removeAttribute("hidden");
				sub.setAttribute("aria-hidden", "false");
				btn.setAttribute("aria-expanded", "true");
				if (li) {
					li.classList.add("is-open");
				}
			}
		});
	}

	document.addEventListener("click", function (e) {
		var t = e.target;
		if (!t || !t.closest) {
			return;
		}
		var langLink = t.closest(".annam-language-switcher [data-gt-lang]");
		if (langLink) {
			var switcher = langLink.closest(".annam-language-switcher");
			if (switcher) {
				setTimeout(function () {
					syncLanguageSwitcher(switcher);
				}, 50);
				setTimeout(function () {
					syncLanguageSwitcher(switcher);
				}, 400);
			}
		}
		if (t.closest("[data-annam-header-close]")) {
			setDrawer(false);
			setSearch(false);
			return;
		}
		if (drawer && !drawer.hasAttribute("hidden")) {
			if (!t.closest("#annam-site-header-drawer") && !t.closest("[data-annam-header-menu]")) {
				setDrawer(false);
			}
		}
		if (searchPanel && !searchPanel.hasAttribute("hidden")) {
			if (!t.closest("#annam-site-header-search-panel") && !t.closest("[data-annam-header-search]")) {
				setSearch(false);
			}
		}
		if (headerRoot) {
			headerRoot.querySelectorAll(".annam-language-switcher[open]").forEach(function (switcher) {
				if (!t.closest("#" + switcher.id)) {
					switcher.removeAttribute("open");
				}
			});
		}
	});

	document.addEventListener("keydown", function (e) {
		if (e.key === "Escape") {
			setDrawer(false);
			setSearch(false);
			if (headerRoot) {
				headerRoot.querySelectorAll(".annam-language-switcher[open]").forEach(function (switcher) {
					switcher.removeAttribute("open");
				});
			}
			document.querySelectorAll(".annam-site-header__item--has-mega.is-open").forEach(function (li) {
				li.classList.remove("is-open");
				var b = li.querySelector(".annam-site-header__mega-trigger--btn");
				if (b) {
					b.setAttribute("aria-expanded", "false");
				}
			});
		}
	});

	window.addEventListener("pageshow", syncAllLanguageSwitchers);
	window.addEventListener("focus", syncAllLanguageSwitchers);

	setInterval(syncAllLanguageSwitchers, 1000);

	document.querySelectorAll(".annam-site-header__mega-trigger--btn").forEach(function (btn) {
		btn.addEventListener("click", function (e) {
			e.preventDefault();
			var li = btn.closest(".annam-site-header__item--has-mega");
			if (!li) {
				return;
			}
			var open = li.classList.toggle("is-open");
			btn.setAttribute("aria-expanded", open ? "true" : "false");
			document.querySelectorAll(".annam-site-header__item--has-mega.is-open").forEach(function (other) {
				if (other !== li) {
					other.classList.remove("is-open");
					var ob = other.querySelector(".annam-site-header__mega-trigger--btn");
					if (ob) {
						ob.setAttribute("aria-expanded", "false");
					}
				}
			});
		});
	});
});
