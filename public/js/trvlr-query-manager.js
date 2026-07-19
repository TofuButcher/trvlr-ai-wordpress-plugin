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

	/**
	 * Sync the category--{slug} class on a grid container to match the active category
	 * in the current query. Reads from query.query_args.tax_query (POST path) or from
	 * a top-level category/category_slug param (GET path).
	 *
	 * @param {Element} container  The .trvlr-cards-container element.
	 * @param {Object}  query      The current query object stored in state.
	 */
	function _updateCategoryClass(container, query) {
		// Remove any existing category-- class.
		Array.from(container.classList).forEach((cls) => {
			if (cls.startsWith('category--')) container.classList.remove(cls);
		});

		let categorySlug = '';

		// POST path: category is in query_args.tax_query
		if (query.query_args && query.query_args.tax_query) {
			const tq = query.query_args.tax_query;
			const clauses = Array.isArray(tq)
				? tq
				: Object.keys(tq).filter((k) => k !== 'relation').map((k) => tq[k]);
			clauses.forEach((clause) => {
				if (!categorySlug && clause && clause.taxonomy === 'category' && clause.terms) {
					categorySlug = Array.isArray(clause.terms) ? clause.terms[0] : clause.terms;
				}
			});
		}

		// GET path: category param at top level
		if (!categorySlug && query.category_slug) categorySlug = query.category_slug;
		if (!categorySlug && query.category)       categorySlug = query.category;

		if (categorySlug) {
			container.classList.add('category--' + categorySlug);
		}
	}

	async function _fetch(gridId, query, page, mode) {
		const state = _state.get(gridId);
		if (!state || state.isLoading) return;

		const container = state.el;
		state.isLoading = true;
		container.classList.remove('trvlr-cards-container--loaded');
		container.classList.add('trvlr-cards-container--loading');
		container.dispatchEvent(new CustomEvent('trvlr:loading', { bubbles: true }));

		try {
			// card_variant is a rendering concern, not a WP_Query key — keep it separate.
			const cardVariant = query.card_variant || '';
			const fetchQuery  = { ...query, paged: page };
			delete fetchQuery.card_variant;

			let response;

			if (fetchQuery.query_args) {
				// POST path: send query_args as JSON body; card_variant at top level.
				const body = { ...fetchQuery };
				if (cardVariant) body.card_variant = cardVariant;
				response = await fetch(_apiUrl, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify(body),
				});
			} else {
				// GET path: all params as URL query string including card_variant.
				const params = new URLSearchParams();
				Object.entries(fetchQuery).forEach(([k, v]) => {
					if (v !== null && v !== undefined && v !== '') {
						params.append(k, Array.isArray(v) ? v.join(',') : v);
					}
				});
				if (cardVariant) params.append('card_variant', cardVariant);
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

			container.dataset.trvlrCurrentPage  = data.current_page;
			container.dataset.trvlrMaxPages     = data.max_pages;
			container.dataset.trvlrFoundPosts   = data.found_posts;
			container.dataset.trvlrCurrentQuery = JSON.stringify(state.currentQuery);

			// Update category--* class to reflect the active category filter.
			_updateCategoryClass(container, state.currentQuery);

			container.dispatchEvent(new CustomEvent('trvlr:loaded', {
				bubbles: true,
				detail: {
					gridId:       gridId,
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
			container.classList.add('trvlr-cards-container--loaded');
		}
	}

	/**
	 * Merge `partialQuery` into the current query for `gridId`, reset to page 1, and fetch.
	 * Keys whose value is null, undefined, or '' are removed from the current query rather
	 * than stored, so each filter only owns its own keys.
	 *
	 * @param {string} gridId
	 * @param {Object} partialQuery Key/value pairs to merge. Null/empty values remove the key.
	 */
	function updateQuery(gridId, partialQuery) {
		const state = _state.get(gridId);
		if (!state) return;
		const next = { ...state.currentQuery };
		Object.entries(partialQuery).forEach(([k, v]) => {
			if (v === null || v === undefined || v === '') {
				delete next[k];
			} else {
				next[k] = v;
			}
		});
		state.currentQuery = next;
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
	 * Remove one or more keys from the current query for `gridId`, reset to page 1, and fetch.
	 *
	 * @param {string}   gridId
	 * @param {string[]} keys   Array of query keys to remove.
	 */
	function removeQueryKeys(gridId, keys) {
		const state = _state.get(gridId);
		if (!state) return;
		const next = { ...state.currentQuery };
		keys.forEach((k) => delete next[k]);
		state.currentQuery = next;
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

	window.TrvlrQueryManager = { updateQuery, setQuery, loadMore, resetQuery, removeQueryKeys, getQuery, getGridIds };
})();
