/**
 * Kunena Component
 *
 * The filters of the moderation log: changing a filter, or going to another page, reloads the list in the background,
 * the filters stay in the address. Without this script the form works as a normal form.
 *
 * @package         Kunena.Template.Aurelia
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

(function () {
	'use strict';

	const start = () => {
		const form = document.getElementById('kmodlog-filter');
		const box = document.getElementById('kmodlog-results');

		if (!form || !box) {
			return;
		}

		let timer = null;
		let loadNumber = 0;

		const filterUrl = () => {
			const url = new URL(form.action, window.location.href);

			url.search = '';

			new FormData(form).forEach((value, key) => {
				value = String(value).trim();

				if (value !== '' && !(key === 'fcat' && value === '0')) {
					url.searchParams.append(key, value);
				}
			});

			return url.toString();
		};

		const load = async (url, push) => {
			const number = ++loadNumber;

			box.style.opacity = '.5';

			try {
				const response = await fetch(url, {
					credentials: 'same-origin',
					headers: {'X-Requested-With': 'XMLHttpRequest'},
				});

				if (number !== loadNumber) {
					// A newer filter was asked for meanwhile.
					return;
				}

				const fresh = response.ok
					? new DOMParser().parseFromString(await response.text(), 'text/html').getElementById('kmodlog-results')
					: null;

				if (!fresh) {
					// Let the browser show what is wrong.
					window.location.href = url;

					return;
				}

				box.replaceChildren(...[...fresh.childNodes].map((node) => document.importNode(node, true)));

				if (push) {
					window.history.pushState({}, '', url);
				}
			} catch (e) {
				window.location.href = url;
			} finally {
				if (number === loadNumber) {
					box.style.opacity = '';
				}
			}
		};

		form.addEventListener('submit', (event) => {
			event.preventDefault();
			window.clearTimeout(timer);
			load(filterUrl(), true);
		});

		form.addEventListener('change', (event) => {
			if (event.target.tagName === 'SELECT') {
				load(filterUrl(), true);
			}
		});

		form.addEventListener('input', (event) => {
			if (event.target.tagName === 'INPUT') {
				window.clearTimeout(timer);
				timer = window.setTimeout(() => load(filterUrl(), true), 400);
			}
		});

		form.querySelector('[data-kmodlog-reset]')?.addEventListener('click', (event) => {
			event.preventDefault();
			window.clearTimeout(timer);
			form.querySelectorAll('select').forEach((select) => {
				select.selectedIndex = 0;
			});
			form.querySelectorAll('input[type=search]').forEach((input) => {
				input.value = '';
			});
			load(event.currentTarget.href, true);
		});

		// The pages of a filtered list.
		document.addEventListener('click', (event) => {
			const link = event.target.closest('[data-kmodlog-pagination] a[href]');

			if (link && !event.ctrlKey && !event.metaKey && !event.shiftKey && link.getAttribute('href').charAt(0) !== '#') {
				event.preventDefault();
				load(link.href, true);
			}
		});

		// Back and forward: show the list of that address and the filters in the bar.
		window.addEventListener('popstate', () => {
			const params = new URL(window.location.href).searchParams;

			form.querySelectorAll('[name]').forEach((field) => {
				field.value = params.get(field.name) || (field.tagName === 'SELECT' ? field.options[0].value : '');
			});

			load(window.location.href, false);
		});
	};

	// The script is loaded in the head, the list is in the body.
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
