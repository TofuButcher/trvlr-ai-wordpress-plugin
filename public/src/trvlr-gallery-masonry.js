import Masonry from 'masonry-layout';
import PhotoSwipe from 'photoswipe';
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';

const GAP = 15;
const MIN_COL_WIDTH = 300;
const MAX_COLS = 4;

function getColumnCount(containerWidth) {
	const cols = Math.floor((containerWidth + GAP) / (MIN_COL_WIDTH + GAP));
	return Math.max(1, Math.min(MAX_COLS, cols));
}

function getColumnWidth(containerWidth, cols) {
	if (cols <= 1) {
		return containerWidth;
	}
	return (containerWidth - (cols - 1) * GAP) / cols;
}

function debounce(fn, ms) {
	let timer;
	return (...args) => {
		clearTimeout(timer);
		timer = setTimeout(() => fn(...args), ms);
	};
}

function initPhotoSwipe(galleryEl) {
	const lightbox = new PhotoSwipeLightbox({
		gallery: galleryEl,
		children: 'a.trvlr-gallery__link',
		pswpModule: PhotoSwipe,
	});
	lightbox.init();
}

function initMasonryLayout(galleryEl) {
	const grid = galleryEl.querySelector('.trvlr-gallery__grid');
	const sizer = galleryEl.querySelector('.trvlr-gallery__sizer');
	if (!grid || !sizer) {
		return null;
	}

	let masonry = null;

	const applyColumnWidths = (colWidthPx) => {
		sizer.style.width = colWidthPx;
		grid.querySelectorAll('.trvlr-gallery__item').forEach((item) => {
			item.style.width = colWidthPx;
		});
	};

	const layout = () => {
		const width = galleryEl.offsetWidth;
		if (width <= 0) {
			return false;
		}

		const cols = getColumnCount(width);
		const colWidth = getColumnWidth(width, cols);
		const colWidthPx = `${colWidth}px`;

		applyColumnWidths(colWidthPx);

		if (!masonry) {
			galleryEl.classList.add('is-masonry-ready');
			masonry = new Masonry(grid, {
				itemSelector: '.trvlr-gallery__item',
				columnWidth: '.trvlr-gallery__sizer',
				gutter: GAP,
				percentPosition: false,
				transitionDuration: 0,
			});
		}

		masonry.layout();
		return true;
	};

	const scheduleLayout = debounce(() => {
		layout();
	}, 80);

	const startLayout = () => {
		if (!layout()) {
			return;
		}
		scheduleLayout();
	};

	grid.querySelectorAll('img').forEach((img) => {
		if (!img.complete) {
			img.addEventListener('load', scheduleLayout, { once: true });
			img.addEventListener('error', scheduleLayout, { once: true });
		}
	});

	if (typeof ResizeObserver !== 'undefined') {
		const resizeObserver = new ResizeObserver(scheduleLayout);
		resizeObserver.observe(galleryEl);
	} else {
		window.addEventListener('resize', scheduleLayout);
	}

	document.querySelectorAll('input[name="trvlr_sa1_tab"]').forEach((input) => {
		input.addEventListener('change', () => {
			if (input.id === 'trvlr_sa1_tab_gallery' && input.checked) {
				requestAnimationFrame(() => {
					layout();
					scheduleLayout();
				});
			}
		});
	});

	return { layout, scheduleLayout };
}

function initMasonryGallery(galleryEl) {
	initPhotoSwipe(galleryEl);

	const runWhenMeasurable = () => {
		if (galleryEl.offsetWidth <= 0) {
			return false;
		}
		if (!galleryEl._trvlrMasonryController) {
			galleryEl._trvlrMasonryController = initMasonryLayout(galleryEl);
		} else {
			galleryEl._trvlrMasonryController.layout();
		}
		return true;
	};

	if (runWhenMeasurable()) {
		return;
	}

	if (typeof ResizeObserver !== 'undefined') {
		const measureObserver = new ResizeObserver(() => {
			if (runWhenMeasurable()) {
				measureObserver.disconnect();
			}
		});
		measureObserver.observe(galleryEl);
	}

	if (typeof IntersectionObserver !== 'undefined') {
		const visibilityObserver = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting && runWhenMeasurable()) {
						visibilityObserver.disconnect();
					}
				});
			},
			{ threshold: 0 }
		);
		visibilityObserver.observe(galleryEl);
	}

	document.querySelectorAll('input[name="trvlr_sa1_tab"]').forEach((input) => {
		input.addEventListener('change', () => {
			if (input.id === 'trvlr_sa1_tab_gallery' && input.checked) {
				requestAnimationFrame(runWhenMeasurable);
			}
		});
	});
}

function initMasonryGalleries() {
	document.querySelectorAll('.trvlr-gallery--masonry').forEach(initMasonryGallery);
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initMasonryGalleries);
} else {
	initMasonryGalleries();
}
