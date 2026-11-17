/**
 * Kunena Component
 * @package     Kunena.Media
 *
 * @copyright   Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

/**
 * The lightbox of Kunena: a link with data-kunena-lightbox="<group>" opens its image in a dialog over the page, with the other images
 * of the same group (in the order of the page) to go through. A link with data-fancybox (a template override written for the former
 * lightbox) works the same way.
 *
 * The dialog is the native <dialog> of the browser: it keeps the focus inside, Escape closes it, and the focus goes back to the link.
 * Previous / next: the buttons, the arrow keys (Home and End for the first and the last image) and a swipe on a touch screen.
 * A click with a modifier key or the middle button keeps opening the image in a new tab.
 */
(function () {
	'use strict';

	const SELECTOR = 'a[data-kunena-lightbox], a[data-fancybox]';
	let dialog = null;
	let parts = {};
	let items = [];
	let index = 0;
	let opener = null;
	let swipe = null;

	const text = (key, fallback) => {
		const value = window.Joomla && window.Joomla.Text ? window.Joomla.Text._(key, '') : '';

		return value && value !== key ? value : fallback;
	};

	const groupOf = (link) => link.getAttribute('data-kunena-lightbox') ?? link.getAttribute('data-fancybox') ?? '';

	// The caption: a caption of the link, else its title (Bootstrap keeps it in data-bs-original-title for its tooltip), else the
	// text of the image.
	const captionOf = (link) => {
		const image = link.querySelector('img');

		return (link.getAttribute('data-caption') || link.getAttribute('data-bs-original-title') || link.getAttribute('title') || (image && image.getAttribute('alt')) || '').trim();
	};

	// The images of the group of a link, in the order of the page, each address once (a thumbnail and a link to the same file are one
	// image).
	const collect = (link) => {
		const group = groupOf(link);
		const seen = new Map();

		document.querySelectorAll(SELECTOR).forEach((candidate) => {
			if (groupOf(candidate) !== group || !candidate.href || seen.has(candidate.href)) {
				return;
			}

			seen.set(candidate.href, {
				href: candidate.href,
				caption: captionOf(candidate),
				// The address which the forum sends as a file (an attachment) and its name as it was uploaded.
				download: candidate.getAttribute('data-kunena-download') || candidate.href,
				filename: candidate.getAttribute('data-kunena-filename') || '',
			});
		});

		return [...seen.values()];
	};

	/**
	 * The icons of the buttons: lines drawn in the same box of 20 × 20, each centred on its middle (10, 10), so that they line up in
	 * their buttons (the characters of a font, as × or ‹, sit where the font puts them).
	 */
	const ICONS = {
		close: 'M4.5 4.5l11 11M15.5 4.5l-11 11',
		previous: 'M13 4 7 10l6 6',
		next: 'M7 4l6 6-6 6',
		download: 'M10 3.5v9M5.5 8 10 12.5 14.5 8M4 16.5h12',
	};

	const icon = (name) => `<svg aria-hidden="true" focusable="false" viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="${ICONS[name]}"/></svg>`;

	const button = (className, label, name) => {
		const element = document.createElement('button');

		element.type = 'button';
		element.className = `kunena-lightbox__button ${className}`;
		element.setAttribute('aria-label', label);
		element.title = label;
		element.innerHTML = icon(name);

		return element;
	};

	const build = () => {
		dialog = document.createElement('dialog');
		dialog.className = 'kunena-lightbox';
		dialog.setAttribute('aria-label', text('COM_KUNENA_LIGHTBOX', 'Image viewer'));

		const figure = document.createElement('figure');
		const image = document.createElement('img');
		const caption = document.createElement('figcaption');
		const status = document.createElement('p');
		const counter = document.createElement('p');
		const close = button('kunena-lightbox__close', text('COM_KUNENA_LIGHTBOX_CLOSE', 'Close'), 'close');
		const previous = button('kunena-lightbox__previous', text('COM_KUNENA_LIGHTBOX_PREVIOUS', 'Previous image'), 'previous');
		const next = button('kunena-lightbox__next', text('COM_KUNENA_LIGHTBOX_NEXT', 'Next image'), 'next');
		// The download is a link: the browser saves the file itself (show() sets its address and how it opens).
		const download = document.createElement('a');

		download.className = 'kunena-lightbox__button kunena-lightbox__download';
		download.innerHTML = icon('download');

		// The dialog starts on the close button (not on "previous", which is disabled on the first image).
		close.autofocus = true;
		figure.className = 'kunena-lightbox__figure';
		image.className = 'kunena-lightbox__image';
		image.alt = '';
		caption.className = 'kunena-lightbox__caption';
		status.className = 'kunena-lightbox__status';
		status.setAttribute('role', 'status');
		counter.className = 'kunena-lightbox__counter';
		counter.setAttribute('aria-live', 'polite');

		figure.append(image, caption);
		dialog.append(figure, status, counter, previous, next, download, close);
		document.body.append(dialog);
		parts = { image, caption, status, counter, previous, next, download };

		close.addEventListener('click', () => dialog.close());
		previous.addEventListener('click', () => go(index - 1));
		next.addEventListener('click', () => go(index + 1));

		// A click beside the image (on the backdrop or the empty part of the dialog) closes it.
		dialog.addEventListener('click', (event) => {
			if (event.target === dialog || event.target === figure || event.target === status) {
				dialog.close();
			}
		});

		dialog.addEventListener('keydown', (event) => {
			const rtl = getComputedStyle(dialog).direction === 'rtl';
			const keys = { ArrowLeft: rtl ? 1 : -1, ArrowRight: rtl ? -1 : 1 };

			if (event.key in keys) {
				go(index + keys[event.key]);
			} else if (event.key === 'Home') {
				go(0);
			} else if (event.key === 'End') {
				go(items.length - 1);
			} else {
				return;
			}

			event.preventDefault();
		});

		// A horizontal swipe on a touch screen goes to the previous or the next image.
		dialog.addEventListener('pointerdown', (event) => {
			swipe = event.pointerType === 'mouse' ? null : { x: event.clientX, y: event.clientY };
		});
		dialog.addEventListener('pointerup', (event) => {
			if (!swipe) {
				return;
			}

			const dx = event.clientX - swipe.x;
			const dy = event.clientY - swipe.y;

			swipe = null;

			if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.5) {
				const rtl = getComputedStyle(dialog).direction === 'rtl';

				go(index + ((dx < 0) !== rtl ? 1 : -1));
			}
		});

		dialog.addEventListener('close', () => {
			document.documentElement.classList.remove('kunena-lightbox-open');
			parts.image.removeAttribute('src');

			if (opener && document.contains(opener)) {
				opener.focus({ preventScroll: true });
			}

			opener = null;
		});
	};

	/**
	 * The download link of an image. A browser saves a file of the site itself (with the name of the attachment); an image of another
	 * site cannot be saved by a link (the browser ignores download there), so the link opens it in a new tab and says so.
	 */
	const setDownload = (link, item) => {
		let url;

		try {
			url = new URL(item.download, document.baseURI);
		} catch (error) {
			link.hidden = true;

			return;
		}

		const local = url.origin === window.location.origin;
		const label = local ? text('COM_KUNENA_LIGHTBOX_DOWNLOAD', 'Download') : text('COM_KUNENA_LIGHTBOX_OPEN', 'Open in a new tab');

		link.hidden = !/^https?:$/.test(url.protocol);
		link.href = url.href;
		link.setAttribute('aria-label', label);
		link.title = label;

		if (local) {
			link.setAttribute('download', item.filename);
			link.removeAttribute('target');
			link.removeAttribute('rel');
		} else {
			link.removeAttribute('download');
			link.target = '_blank';
			link.rel = 'noopener noreferrer';
		}
	};

	const show = () => {
		const item = items[index];
		const { image, caption, status, counter, previous, next } = parts;
		const several = items.length > 1;

		dialog.classList.add('is-loading');
		dialog.classList.remove('is-error');
		status.textContent = text('COM_KUNENA_LIGHTBOX_LOADING', 'Loading…');
		caption.textContent = item.caption;
		caption.hidden = !item.caption;
		image.alt = item.caption;
		counter.hidden = !several;
		counter.textContent = text('COM_KUNENA_LIGHTBOX_COUNTER', 'Image {current} of {total}').replace('{current}', String(index + 1)).replace('{total}', String(items.length));
		previous.hidden = !several;
		next.hidden = !several;
		previous.disabled = index === 0;
		next.disabled = index === items.length - 1;
		setDownload(parts.download, item);

		image.onload = () => {
			dialog.classList.remove('is-loading');
			status.textContent = '';
		};
		image.onerror = () => {
			dialog.classList.remove('is-loading');
			dialog.classList.add('is-error');
			status.textContent = text('COM_KUNENA_LIGHTBOX_ERROR', 'The image could not be loaded.');
		};
		image.src = item.href;

		// The neighbours are loaded ahead, so that going through the images does not wait.
		[index - 1, index + 1].forEach((neighbour) => {
			if (items[neighbour]) {
				new Image().src = items[neighbour].href;
			}
		});
	};

	const go = (target) => {
		if (target < 0 || target >= items.length || target === index) {
			return;
		}

		index = target;
		show();
	};

	const open = (link) => {
		if (!dialog) {
			build();
		}

		items = collect(link);
		index = Math.max(0, items.findIndex((item) => item.href === link.href));
		opener = link;
		show();
		document.documentElement.classList.add('kunena-lightbox-open');
		dialog.showModal();
	};

	document.addEventListener('click', (event) => {
		const link = event.target instanceof Element ? event.target.closest(SELECTOR) : null;

		if (!link || !link.href || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || typeof HTMLDialogElement === 'undefined') {
			return;
		}

		event.preventDefault();
		open(link);
	});

	window.KunenaLightbox = { open };
})();
