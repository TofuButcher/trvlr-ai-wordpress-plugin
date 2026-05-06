(function () {
	'use strict';

	const _state = new Map();
	let _apiUrl = '';

	function _init() {
		const config = window.trvlrQueryManagerConfig || {};
		_apiUrl = config.apiUrl || '';

		document.querySelectorAll('[data-trvlr-grid-id]').forEach((el) => {
			const gridId = el.dataset.trvlrGridId;
			if (!gridId) return;

			let initialQuery = {};
			try {
				initialQuery = JSON.parse(el.dataset.trvlrInitialQuery || '{}');
			} catch (_) {}

			_state.set(gridId, {
				el,
				initialQuery: { ...initialQuery },
				currentQuery: { ...initialQuery },
				currentPage:  parseInt(el.dataset.trvlrCurrentPage, 10) || 1,
				maxPages:     parseInt(el.dataset.trvlrMaxPages, 10) || 1,
				foundPosts:   parseInt(el.dataset.trvlrFoundPosts, 10) || 0,
				isLoading:    false,
			});
		});

		document.addEventListener('trvlr:filter', ({ detail = {} }) => {
			if (detail.gridId && detail.query) {
				updateQuery(detail.gridId, detail.query);
			}
		});

		document.addEventListener('trvlr:load-more', ({ detail = {} }) => {
			if (detail.gridId) {
				loadMore(detail.gridId);
			}
		});
	}

	async function _fetch(gridId, query, page, mode) {
		const state = _state.get(gridId);
		if (!state || state.isLoading) return;

		const container = state.el;
		state.isLoading = true;
		container.classList.add('trvlr-cards-container--loading');
		container.dispatchEvent(new CustomEvent('trvlr:loading', { bubbles: true }));

		try {
			const fetchQuery = { ...query, paged: page };
			let response;

			if (fetchQuery.query_args) {
				response = await fetch(_apiUrl, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify(fetchQuery),
				});
			} else {
				const params = new URLSearchParams();
				Object.entries(fetchQuery).forEach(([k, v]) => {
					if (v !== null && v !== undefined && v !== '') {
						params.append(k, v);
					}
				});
				response = await fetch(`${_apiUrl}?${params}`);
			}

			if (!response.ok) throw new Error(`HTTP ${response.status}`);

			const data = await response.json();

			const cardsEl = container.querySelector('.trvlr-cards');
			const temp = document.createElement('div');
			temp.innerHTML = data.html;
			const newCardsEl = temp.firstElementChild;

			if (mode === 'append') {
				if (cardsEl && newCardsEl) {
					cardsEl.append(...Array.from(newCardsEl.children));
				}
			} else {
				if (cardsEl && newCardsEl) {
					cardsEl.replaceWith(newCardsEl);
				} else if (newCardsEl) {
					container.prepend(newCardsEl);
				}
			}

			state.currentPage = data.current_page;
			state.maxPages    = data.max_pages;
			state.foundPosts  = data.found_posts;

			container.dataset.trvlrCurrentPage = data.current_page;
			container.dataset.trvlrMaxPages    = data.max_pages;
			container.dataset.trvlrFoundPosts  = data.found_posts;

			container.dispatchEvent(new CustomEvent('trvlr:loaded', {
				bubbles: true,
				detail: {
					found_posts:  data.found_posts,
					max_pages:    data.max_pages,
					current_page: data.current_page,
				},
			}));
		} catch (err) {
			console.error(`[TrvlrQueryManager] Fetch failed for grid "${gridId}":`, err.message);
			container.dispatchEvent(new CustomEvent('trvlr:error', {
				bubbles: true,
				detail: { error: err.message },
			}));
		} finally {
			state.isLoading = false;
			container.classList.remove('trvlr-cards-container--loading');
		}
	}

	/**
	 * Merge `partialQuery` into the current query for `gridId`, reset to page 1, and fetch.
	 *
	 * @param {string} gridId
	 * @param {Object} partialQuery Key/value pairs to merge into the current query.
	 */
	function updateQuery(gridId, partialQuery) {
		const state = _state.get(gridId);
		if (!state) return;
		state.currentQuery = { ...state.currentQuery, ...partialQuery };
		_fetch(gridId, state.currentQuery, 1, 'replace');
	}

	/**
	 * Replace the entire query for `gridId`, reset to page 1, and fetch.
	 *
	 * @param {string} gridId
	 * @param {Object} fullQuery Complete query replacing the current one.
	 */
	function setQuery(gridId, fullQuery) {
		const state = _state.get(gridId);
		if (!state) return;
		state.currentQuery = { ...fullQuery };
		_fetch(gridId, state.currentQuery, 1, 'replace');
	}

	/**
	 * Fetch the next page and append its cards to the existing grid.
	 * No-op if already on the last page or a fetch is in progress.
	 *
	 * @param {string} gridId
	 */
	function loadMore(gridId) {
		const state = _state.get(gridId);
		if (!state || state.currentPage >= state.maxPages) return;
		_fetch(gridId, state.currentQuery, state.currentPage + 1, 'append');
	}

	/**
	 * Reset `gridId` to its initial query (as rendered server-side) and fetch page 1.
	 *
	 * @param {string} gridId
	 */
	function resetQuery(gridId) {
		const state = _state.get(gridId);
		if (!state) return;
		state.currentQuery = { ...state.initialQuery };
		_fetch(gridId, state.currentQuery, 1, 'replace');
	}

	/**
	 * Return a copy of the current query state for `gridId`, or null if not found.
	 *
	 * @param {string} gridId
	 * @returns {Object|null}
	 */
	function getQuery(gridId) {
		const state = _state.get(gridId);
		return state ? { ...state.currentQuery } : null;
	}

	/**
	 * Return an array of all registered grid IDs on this page.
	 *
	 * @returns {string[]}
	 */
	function getGridIds() {
		return [..._state.keys()];
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', _init);
	} else {
		_init();
	}

	window.TrvlrQueryManager = { updateQuery, setQuery, loadMore, resetQuery, getQuery, getGridIds };
})();
