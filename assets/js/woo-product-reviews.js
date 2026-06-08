(function () {
	'use strict';

	/* -------------------------------------------------------------------------- */
	/* Review image lightbox (per-review gallery from data-full)                */
	/* -------------------------------------------------------------------------- */

	var lb = document.getElementById('annam-review-image-lightbox');
	var lbImg = lb ? lb.querySelector('.annam-review-lightbox__img') : null;
	var lbPrev = lb ? lb.querySelector('[data-annam-lightbox-prev]') : null;
	var lbNext = lb ? lb.querySelector('[data-annam-lightbox-next]') : null;
	var lbStage = lb ? lb.querySelector('.annam-review-lightbox__stage') : null;

	var lbUrls = [];
	var lbIndex = 0;
	var touchStartX = null;

	function isLightboxOpen() {
		return lb && !lb.hasAttribute('hidden');
	}

	function updateLightboxNav() {
		if (!lbPrev || !lbNext) {
			return;
		}
		var n = lbUrls.length;
		if (n <= 1) {
			lbPrev.setAttribute('hidden', '');
			lbNext.setAttribute('hidden', '');
			return;
		}
		lbPrev.removeAttribute('hidden');
		lbNext.removeAttribute('hidden');
	}

	function showLightboxSlide() {
		if (!lbImg || !lbUrls.length) {
			return;
		}
		var u = lbUrls[lbIndex];
		lbImg.removeAttribute('src');
		lbImg.setAttribute('src', u);
		lbImg.alt = '';
		updateLightboxNav();
	}

	function openReviewLightbox(urls, startIndex) {
		if (!lb || !lbImg || !urls || !urls.length) {
			return;
		}
		lbUrls = urls.slice();
		lbIndex = Math.max(0, Math.min(startIndex || 0, lbUrls.length - 1));
		lb.removeAttribute('hidden');
		lb.setAttribute('aria-hidden', 'false');
		document.body.classList.add('annam-review-lightbox-open');
		showLightboxSlide();
	}

	function closeReviewLightbox() {
		if (!lb) {
			return;
		}
		lb.setAttribute('hidden', '');
		lb.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('annam-review-lightbox-open');
		lbUrls = [];
		lbIndex = 0;
		if (lbImg) {
			lbImg.removeAttribute('src');
		}
	}

	function lightboxPrev() {
		if (lbUrls.length < 2) {
			return;
		}
		lbIndex = (lbIndex - 1 + lbUrls.length) % lbUrls.length;
		showLightboxSlide();
	}

	function lightboxNext() {
		if (lbUrls.length < 2) {
			return;
		}
		lbIndex = (lbIndex + 1) % lbUrls.length;
		showLightboxSlide();
	}

	if (lb && lbStage) {
		lbStage.addEventListener(
			'touchstart',
			function (e) {
				if (e.touches && e.touches.length === 1) {
					touchStartX = e.touches[0].clientX;
				}
			},
			{ passive: true }
		);

		lbStage.addEventListener(
			'touchend',
			function (e) {
				if (touchStartX === null || !e.changedTouches || !e.changedTouches.length) {
					return;
				}
				var x = e.changedTouches[0].clientX;
				var dx = x - touchStartX;
				touchStartX = null;
				if (Math.abs(dx) < 56) {
					return;
				}
				if (dx > 0) {
					lightboxPrev();
				} else {
					lightboxNext();
				}
			},
			{ passive: true }
		);
	}

	if (lb) {
		lb.addEventListener('click', function (e) {
			var t = e.target;
			if (t && t.closest && t.closest('[data-annam-lightbox-close]')) {
				e.preventDefault();
				closeReviewLightbox();
			}
		});

		if (lbPrev) {
			lbPrev.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				lightboxPrev();
			});
		}
		if (lbNext) {
			lbNext.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				lightboxNext();
			});
		}

		if (lbStage) {
			lbStage.addEventListener('click', function (e) {
				if (e.target === lbStage) {
					closeReviewLightbox();
				}
			});
		}
	}

	document.addEventListener('click', function (e) {
		var thumb = e.target.closest && e.target.closest('.annam-review-images__thumb');
		if (!thumb || !lb) {
			return;
		}
		var gallery = thumb.closest('[data-annam-review-gallery]');
		if (!gallery) {
			return;
		}
		e.preventDefault();
		var nodes = gallery.querySelectorAll('.annam-review-images__thumb[data-full]');
		var urls = [];
		for (var i = 0; i < nodes.length; i++) {
			var u = nodes[i].getAttribute('data-full');
			if (u) {
				urls.push(u);
			}
		}
		if (!urls.length) {
			return;
		}
		var raw = thumb.getAttribute('data-index');
		var start = raw !== null && raw !== '' ? parseInt(raw, 10) : 0;
		if (isNaN(start)) {
			start = 0;
		}
		openReviewLightbox(urls, start);
	});

	document.addEventListener('keydown', function (e) {
		if (!isLightboxOpen()) {
			return;
		}
		if (e.key === 'Escape') {
			e.preventDefault();
			closeReviewLightbox();
			return;
		}
		if (e.key === 'ArrowLeft') {
			e.preventDefault();
			lightboxPrev();
			return;
		}
		if (e.key === 'ArrowRight') {
			e.preventDefault();
			lightboxNext();
		}
	});

	/* -------------------------------------------------------------------------- */
	/* Review form modal + AJAX (requires annamProductReviews)                   */
	/* -------------------------------------------------------------------------- */

	var cfg = typeof annamProductReviews === 'undefined' ? null : annamProductReviews;

	var modal = document.getElementById('annam-review-modal');
	var openBtn = document.getElementById('annam-open-review-modal');
	var form = document.getElementById('annam-review-form');
	var starsWrap = document.getElementById('annam-review-stars');
	var ratingInput = document.getElementById('annam-review-rating-input');
	var fileInput = document.getElementById('annam-review-images');
	var fileListEl = document.getElementById('annam-review-file-list');
	var msgEl = document.getElementById('annam-review-form-msg');

	function syncReviewFileList() {
		if (!fileListEl) {
			return;
		}
		fileListEl.innerHTML = '';
		if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
			fileListEl.setAttribute('hidden', '');
			return;
		}
		fileListEl.removeAttribute('hidden');
		for (var j = 0; j < fileInput.files.length; j++) {
			var li = document.createElement('li');
			li.textContent = fileInput.files[j].name;
			fileListEl.appendChild(li);
		}
	}

	function setModalOpen(open) {
		if (!modal) {
			return;
		}
		if (open) {
			modal.removeAttribute('hidden');
			modal.setAttribute('aria-hidden', 'false');
			document.body.classList.add('annam-review-modal-open');
		} else {
			modal.setAttribute('hidden', '');
			modal.setAttribute('aria-hidden', 'true');
			document.body.classList.remove('annam-review-modal-open');
		}
	}

	function resetStars() {
		if (!starsWrap || !ratingInput || !form) {
			return;
		}
		ratingInput.value = '';
		var btns = starsWrap.querySelectorAll('.annam-review-modal__star-btn');
		btns.forEach(function (b) {
			b.classList.remove('is-selected', 'is-active');
			b.setAttribute('aria-checked', 'false');
		});
		form.setAttribute('hidden', '');
		if (msgEl) {
			msgEl.setAttribute('hidden', '');
			msgEl.textContent = '';
			msgEl.classList.remove('is-error', 'is-success');
		}
		if (fileInput) {
			fileInput.value = '';
		}
		syncReviewFileList();
	}

	function applyStarState(value) {
		if (!starsWrap) {
			return;
		}
		var btns = starsWrap.querySelectorAll('.annam-review-modal__star-btn');
		btns.forEach(function (b) {
			var r = parseInt(b.getAttribute('data-rating'), 10);
			var on = r <= value;
			b.classList.toggle('is-selected', on);
			b.classList.toggle('is-active', r === value);
			b.setAttribute('aria-checked', r === value ? 'true' : 'false');
		});
	}

	function openModal() {
		setModalOpen(true);
		resetStars();
		var tsEl = document.getElementById('annam-review-form-ts');
		if (tsEl) {
			tsEl.value = String(Math.floor(Date.now() / 1000));
		}
	}

	function closeModal() {
		setModalOpen(false);
		if (form) {
			form.reset();
		}
		resetStars();
	}

	document.addEventListener('keydown', function (e) {
		if (e.key !== 'Escape') {
			return;
		}
		if (isLightboxOpen()) {
			return;
		}
		if (modal && !modal.hasAttribute('hidden')) {
			closeModal();
		}
	});

	if (!cfg || !cfg.ajaxUrl) {
		return;
	}

	if (openBtn) {
		openBtn.addEventListener('click', openModal);
	}

	if (modal) {
		modal.querySelectorAll('[data-annam-review-close]').forEach(function (el) {
			el.addEventListener('click', closeModal);
		});
	}

	if (starsWrap && ratingInput && form) {
		starsWrap.addEventListener('click', function (e) {
			var btn = e.target.closest('.annam-review-modal__star-btn');
			if (!btn || !starsWrap.contains(btn)) {
				return;
			}
			var v = parseInt(btn.getAttribute('data-rating'), 10);
			if (v < 1 || v > 5) {
				return;
			}
			ratingInput.value = String(v);
			applyStarState(v);
			form.removeAttribute('hidden');
		});
	}

	if (fileInput) {
		fileInput.addEventListener('change', function () {
			if (fileInput.files && fileInput.files.length > 3) {
				alert('Tối đa 3 ảnh.');
				fileInput.value = '';
			}
			syncReviewFileList();
		});
	}

	function showMsg(text, ok) {
		if (!msgEl) {
			return;
		}
		msgEl.removeAttribute('hidden');
		msgEl.textContent = text;
		msgEl.classList.toggle('is-success', !!ok);
		msgEl.classList.toggle('is-error', !ok);
	}

	if (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var rating = parseInt(ratingInput && ratingInput.value ? ratingInput.value : '0', 10);
			if (rating < 1 || rating > 5) {
				showMsg('Vui lòng chọn số sao từ 1 đến 5.', false);
				return;
			}

			var fd = new FormData();
			fd.append('action', 'annam_submit_product_review');
			fd.append('security', cfg.nonce);
			fd.append('nonce', cfg.reviewNonce);
			fd.append('product_id', String(cfg.productId));
			fd.append('rating', String(rating));

			var author = form.querySelector('[name="author"]');
			var phone = form.querySelector('[name="phone"]');
			var comment = form.querySelector('[name="comment"]');
			var hp = form.querySelector('[name="annam_review_company"]');

			fd.append('author', author && author.value ? author.value : '');
			fd.append('phone', phone && phone.value ? phone.value : '');
			fd.append('comment', comment && comment.value ? comment.value : '');
			fd.append('annam_review_company', hp && hp.value ? hp.value : '');

			var tsEl = document.getElementById('annam-review-form-ts');
			fd.append('annam_review_form_ts', tsEl && tsEl.value ? tsEl.value : '');

			if (fileInput && fileInput.files && fileInput.files.length) {
				for (var k = 0; k < fileInput.files.length; k++) {
					fd.append('review_images[]', fileInput.files[k]);
				}
			}

			var submitBtn = form.querySelector('[type="submit"]');
			if (submitBtn) {
				submitBtn.disabled = true;
			}
			if (msgEl) {
				msgEl.setAttribute('hidden', '');
			}

			fetch(cfg.ajaxUrl, {
				method: 'POST',
				body: fd,
				credentials: 'same-origin',
			})
				.then(function (res) {
					return res.json();
				})
				.then(function (json) {
					if (json && json.success && json.data && json.data.message) {
						showMsg(json.data.message, true);
						setTimeout(function () {
							window.location.reload();
						}, 1200);
					} else {
						var m =
							json && json.data && json.data.message
								? json.data.message
								: 'Không gửi được, vui lòng thử lại.';
						showMsg(m, false);
					}
				})
				.catch(function () {
					showMsg('Lỗi mạng, vui lòng thử lại.', false);
				})
				.finally(function () {
					if (submitBtn) {
						submitBtn.disabled = false;
					}
				});
		});
	}
})();
