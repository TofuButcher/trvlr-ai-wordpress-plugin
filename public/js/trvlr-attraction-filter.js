(function () {
	'use strict';

	function debounce(fn, delay) {
		var timer;
		return function () {
			var args = arguments;
			clearTimeout(timer);
			timer = setTimeout(function () { fn.apply(null, args); }, delay);
		};
	}

	function initDropdown(filterEl) {
		var container      = filterEl.querySelector('.filter-buttons__container');
		var containerInner = filterEl.querySelector('.filter-buttons__container-inner');
		var content        = filterEl.querySelector('.filter-buttons__container-content');
		var toggle         = filterEl.querySelector('.filter-btns-dropdown__toggle');
		var openMenuBtn    = filterEl.querySelector('.open-filter-menu');

		if (!container || !content) return;

		function setDimensions() {
			var wasOpen = container.classList.contains('is-open');
			if (!wasOpen) {
				container.style.maxWidth  = 'none';
				container.style.maxHeight = 'none';
			}
			var w = content.scrollWidth;
			var h = content.scrollHeight;
			if (!wasOpen) {
				container.style.maxWidth  = '';
				container.style.maxHeight = '';
			}
			container.style.setProperty('--container-full-width', w + 'px');
			container.style.setProperty('--container-full-height', h + 'px');
		}

		setDimensions();
		window.addEventListener('resize', debounce(setDimensions, 150));

		function open() {
			container.classList.add('is-open');
			if (toggle) toggle.setAttribute('aria-expanded', 'true');
		}

		function close() {
			container.classList.remove('is-open');
			if (toggle) toggle.setAttribute('aria-expanded', 'false');
		}

		function toggleMenu() {
			container.classList.contains('is-open') ? close() : open();
		}

		if (toggle) {
			toggle.addEventListener('click', function (e) {
				e.preventDefault();
				toggleMenu();
			}, true);
		}

		if (openMenuBtn) {
			openMenuBtn.addEventListener('click', function (e) {
				e.preventDefault();
				toggleMenu();
			}, true);
		}

		document.addEventListener('click', function (e) {
			if (!e.target.closest('.filter-buttons__container') && !e.target.closest('.open-filter-menu')) {
				close();
			}
		});

		if (window.innerWidth > 1024 && toggle) {
			toggle.removeAttribute('aria-expanded');
			toggle.removeAttribute('aria-label');
		}
	}

	function initFilter(filterEl) {
		var target      = filterEl.dataset.trvlrFilterTarget;
		var openMenuBtn = filterEl.querySelector('.open-filter-menu');
		var container   = filterEl.querySelector('.filter-buttons__container');

		if (!target) return;

		var buttons = Array.from(filterEl.querySelectorAll('.filter-btn[data-trvlr-query]'));

		buttons.forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();

				buttons.forEach(function (b) { b.classList.remove('active'); });
				btn.classList.add('active');

				if (openMenuBtn && window.innerWidth <= 1024) {
					var clickedLabel = btn.querySelector('.filter-btn__label');
					var menuLabel    = openMenuBtn.querySelector('.filter-btn__label');
					if (clickedLabel && menuLabel) {
						menuLabel.textContent = clickedLabel.textContent;
					}
					if (container) container.classList.remove('is-open');
				}

				if (!window.TrvlrQueryManager) return;

				var query = {};
				try {
					query = JSON.parse(btn.dataset.trvlrQuery || '{}');
				} catch (_) {}

				window.TrvlrQueryManager.updateQuery(target, query);
			});
		});

		document.addEventListener('trvlr:loaded', function (e) {
			if (!e.detail || e.detail.gridId !== target) return;
			var activeBtn = buttons.find(function (b) { return b.classList.contains('active'); });
			if (openMenuBtn && activeBtn) {
				var lbl = activeBtn.querySelector('.filter-btn__label');
				var menuLbl = openMenuBtn.querySelector('.filter-btn__label');
				if (lbl && menuLbl) menuLbl.textContent = lbl.textContent;
			}
		});
	}

	function init() {
		document.querySelectorAll('.trvlr-attraction-filter[data-trvlr-filter-target]').forEach(function (el) {
			initDropdown(el);
			initFilter(el);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
