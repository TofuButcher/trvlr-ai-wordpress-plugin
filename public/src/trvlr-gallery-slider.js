import Splide from '@splidejs/splide';
import '@splidejs/splide/css';

function initGallerySliders() {
	document.querySelectorAll('.trvlr-gallery--slider').forEach((wrap) => {
		const main = wrap.querySelector('.trvlr-gallery__main');
		const nav = wrap.querySelector('.trvlr-gallery__nav');

		if (!main || !nav) {
			return;
		}

		const isTheme3 = wrap.classList.contains('trvlr-gallery--theme-3') || wrap.dataset.trvlrGalleryVariant === 'theme-3';

		const mainSlider = new Splide(main, {
			type: 'slide',
			direction: 'ltr',
			pagination: true,
			arrows: false,
			gap: '11px',
			perPage: 1,
			perMove: 1,
			speed: 400,
			interval: 3000,
		});

		const navConfig = isTheme3 ? {
			type: 'slide',
			direction: 'ltr',
			height: '510px',
			fixedHeight: '120px',
			fixedWidth: '120px',
			gap: '10px',
			pagination: false,
			arrows: false,
			isNavigation: true,
			drag: false,
			breakpoints: {
				700: {
					height: 'auto',
					fixedWidth: '100px',
					fixedHeight: '100px',
					drag: true,
				},
			},
		} : {
			type: 'slide',
			direction: 'ttb',
			height: '460px',
			fixedHeight: '100px',
			fixedWidth: '140px',
			gap: '11px',
			pagination: false,
			arrows: false,
			isNavigation: true,
			wheel: true,
			breakpoints: {
				768: {
					direction: 'ltr',
					height: 'auto',
					fixedWidth: '100px',
					fixedHeight: '70px',
				},
			},
		};

		const navSlider = new Splide(nav, navConfig);

		mainSlider.sync(navSlider);
		mainSlider.mount();
		navSlider.mount();
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initGallerySliders);
} else {
	initGallerySliders();
}
