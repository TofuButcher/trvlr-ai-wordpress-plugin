import Splide from '@splidejs/splide';
import '@splidejs/splide/css';

const DYNAMIC_MAIN_BULLETS = 1;
const DYNAMIC_BULLET_SLOTS = DYNAMIC_MAIN_BULLETS + 4;
const GALLERY_STACK_MAX = 800;
const LAYOUTS = new Set(['nav-bottom', 'nav-right', 'nav-right-2col']);

function bulletOuterSize(el) {
	const style = window.getComputedStyle(el);
	return (
		el.offsetWidth +
		(parseFloat(style.marginLeft) || 0) +
		(parseFloat(style.marginRight) || 0)
	);
}

function createDynamicPagination(splide, root) {
	const length = splide.length;
	if (length <= 1) {
		return;
	}

	const pagination = document.createElement('ul');
	pagination.className = 'trvlr-gallery__pagination';
	pagination.setAttribute('role', 'tablist');
	pagination.setAttribute('aria-label', 'Gallery pagination');

	const items = [];
	const bullets = [];

	for (let i = 0; i < length; i++) {
		const item = document.createElement('li');
		item.className = 'trvlr-gallery__pagination-item';
		item.setAttribute('role', 'presentation');

		const bullet = document.createElement('button');
		bullet.type = 'button';
		bullet.className = 'trvlr-gallery__bullet';
		bullet.setAttribute('role', 'tab');
		bullet.setAttribute('aria-label', `Go to slide ${i + 1}`);
		bullet.addEventListener('click', () => {
			splide.go(i);
		});

		item.appendChild(bullet);
		pagination.appendChild(item);
		items.push(item);
		bullets.push(bullet);
	}

	root.appendChild(pagination);

	const stateClasses = [
		'is-active',
		'is-active-main',
		'is-prev',
		'is-prev-prev',
		'is-next',
		'is-next-next',
	];

	const update = () => {
		const current = splide.index;
		const firstIndex = Math.max(current - (DYNAMIC_MAIN_BULLETS - 1), 0);
		const lastIndex = firstIndex + Math.min(length, DYNAMIC_MAIN_BULLETS) - 1;
		const midIndex = (lastIndex + firstIndex) / 2;

		bullets.forEach((bullet, i) => {
			bullet.classList.remove(...stateClasses);
			bullet.removeAttribute('aria-current');

			if (i >= firstIndex && i <= lastIndex) {
				bullet.classList.add('is-active', 'is-active-main');
				bullet.setAttribute('aria-current', 'true');
			} else if (i === firstIndex - 1) {
				bullet.classList.add('is-prev');
			} else if (i === firstIndex - 2) {
				bullet.classList.add('is-prev-prev');
			} else if (i === lastIndex + 1) {
				bullet.classList.add('is-next');
			} else if (i === lastIndex + 2) {
				bullet.classList.add('is-next-next');
			}
		});

		const bulletSize = bulletOuterSize(items[0]);
		pagination.style.width = `${bulletSize * DYNAMIC_BULLET_SLOTS}px`;

		const offset =
			(bulletSize * DYNAMIC_BULLET_SLOTS - bulletSize) / 2 - midIndex * bulletSize;
		items.forEach((item) => {
			item.style.left = `${offset}px`;
		});
	};

	update();
	splide.on('move', update);
	splide.on('moved', update);
	splide.on('refresh', update);

	return () => {
		splide.off('move', update);
		splide.off('moved', update);
		splide.off('refresh', update);
		pagination.remove();
	};
}

function resolveLayout(wrap) {
	const raw = wrap.dataset.trvlrGalleryLayout || 'nav-bottom';
	return LAYOUTS.has(raw) ? raw : 'nav-bottom';
}

function getStackedNavOptions() {
	return {
		type: 'slide',
		direction: 'ltr',
		height: 'auto',
		fixedHeight: '120px',
		fixedWidth: '120px',
		gap: '10px',
		focus: 'center',
		pagination: false,
		arrows: false,
		isNavigation: true,
		wheel: false,
		drag: true,
	};
}

function getNavOptions(layout, compact) {
	if (layout === 'nav-bottom' || compact) {
		return {
			type: 'slide',
			direction: 'ltr',
			height: 'auto',
			fixedHeight: '80px',
			fixedWidth: '100px',
			gap: '10px',
			// focus: 'center',
			pagination: false,
			arrows: false,
			isNavigation: true,
			wheel: false,
			drag: true,
		};
	}

	if (layout === 'nav-right-2col') {
		return {
			type: 'slide',
			direction: 'ttb',
			height: '100%',
			fixedHeight: '120px',
			fixedWidth: '120px',
			focus: 'center',
			pagination: false,
			arrows: false,
			isNavigation: true,
			wheel: false,
			drag: true,
		};
	}

	return {
		type: 'slide',
		direction: 'ttb',
		height: '100%',
		fixedHeight: '120px',
		fixedWidth: '120px',
		// gap: '10px',
		pagination: false,
		arrows: false,
		isNavigation: true,
		wheel: true,
		drag: true,
	};
}

function bindContainerNavLayout(container, navSlider, layout) {
	if (layout === 'nav-bottom') {
		return null;
	}

	let compact = container.clientWidth <= GALLERY_STACK_MAX;

	const observer = new ResizeObserver((entries) => {
		const width = entries[0]?.contentRect?.width ?? container.clientWidth;
		const nextCompact = width <= GALLERY_STACK_MAX;
		if (nextCompact === compact) {
			return;
		}
		compact = nextCompact;
		navSlider.options = getNavOptions(layout, compact);
		navSlider.refresh();
	});

	observer.observe(container);
	return observer;
}

function initGallerySliders() {
	document.querySelectorAll('.trvlr-gallery--slider').forEach((wrap) => {
		const main = wrap.querySelector('.trvlr-gallery__main');
		const nav = wrap.querySelector('.trvlr-gallery__nav');

		if (!main || !nav) {
			return;
		}

		const layout = resolveLayout(wrap);
		const container = wrap.closest('.trvlr-container-sizer') || wrap;
		const compact = layout !== 'nav-bottom' && container.clientWidth <= GALLERY_STACK_MAX;

		const mainSlider = new Splide(main, {
			type: 'slide',
			direction: 'ltr',
			pagination: false,
			arrows: false,
			gap: '11px',
			perPage: 1,
			perMove: 1,
			speed: 400,
			interval: 3000,
		});

		const navSlider = new Splide(nav, getNavOptions(layout, compact));

		mainSlider.sync(navSlider);
		mainSlider.mount();
		navSlider.mount();
		createDynamicPagination(mainSlider, main);
		bindContainerNavLayout(container, navSlider, layout);
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initGallerySliders);
} else {
	initGallerySliders();
}
