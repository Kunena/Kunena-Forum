/**
 * Kunena Component
 *
 * Reloads the list of topics (recent topics, unread, no replies, my topics, ...) in the background and makes the
 * rows blink when there is a new topic or a topic got a new message.
 *
 * @package         Kunena.Template.Aurelia
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

(function () {
	'use strict';

	const start = () => {
		const options = (window.Joomla && Joomla.getOptions) ? Joomla.getOptions('com_kunena.topiclist') : null;
		const list = document.querySelector('tbody.topic-list');

		if (!options || !list) {
			return;
		}

		const interval = Math.max(10, parseInt(options.interval, 10) || 30) * 1000;
		let busy = false;

		// The blink is made here, so the template does not need a rebuilt stylesheet.
		const style = document.createElement('style');
		style.textContent = '@keyframes kunena-topic-blink{0%,100%{background-color:transparent}50%{background-color:rgba(255,193,7,.45)}}'
			+ '.kunena-topic-blink > *{animation:kunena-topic-blink 1s ease-in-out 5}'
			+ '@media (prefers-reduced-motion:reduce){.kunena-topic-blink > *{animation:none;background-color:rgba(255,193,7,.25)}}';
		document.head.appendChild(style);

		const text = (row) => row.textContent.replace(/\s+/g, ' ').trim();

		const topicRows = (body) => {
			const rows = new Map();

			body.querySelectorAll('tr[data-topic-id]').forEach((row) => rows.set(row.dataset.topicId, row));

			return rows;
		};

		// Do not replace the list while somebody is using it.
		const inUse = () => {
			const form = document.getElementById('ktopicsform');
			const bar = document.getElementById('ktopicsfilter');
			const typing = /^(INPUT|SELECT|TEXTAREA)$/.test(document.activeElement.tagName);

			return !!(form && (form.querySelector('input.kcheck:checked, input[type=checkbox]:checked:not(.kcheckall)')
				|| form.contains(document.activeElement) && typing))
				|| !!(bar && bar.contains(document.activeElement) && typing);
		};

		let loadNumber = 0;

		const refresh = async () => {
			if (busy || document.hidden || inUse()) {
				return;
			}

			busy = true;

			const asked = loadNumber;

			try {
				const response = await fetch(window.location.href, {
					credentials: 'same-origin',
					headers: {'X-Requested-With': 'XMLHttpRequest'},
				});

				if (!response.ok) {
					return;
				}

				const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
				const fresh = doc.querySelector('tbody.topic-list');

				if (!fresh || inUse() || asked !== loadNumber) {
					return;
				}

				const before = topicRows(list);
				const after = topicRows(fresh);
				const changed = [];
				let sameOrder = before.size === after.size;

				const oldIds = [...before.keys()];
				let index = 0;

				after.forEach((row, id) => {
					const old = before.get(id);

					if (!old || old.dataset.lastPost !== row.dataset.lastPost) {
						changed.push(id);
					}

					if (oldIds[index++] !== id) {
						sameOrder = false;
					}
				});

				if (!changed.length && sameOrder) {
					// No new topic or message: only refresh the rows whose relative times (like "2 minutes ago") moved on.
					after.forEach((row, id) => {
						const old = before.get(id);

						if (text(old) !== text(row)) {
							old.replaceWith(document.importNode(row, true));
						}
					});

					return;
				}

				list.replaceChildren(...[...fresh.childNodes].map((node) => document.importNode(node, true)));

				if (window.bootstrap && bootstrap.Tooltip) {
					list.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
				}

				changed.forEach((id) => {
					const row = list.querySelector('tr[data-topic-id="' + id + '"]');

					if (row) {
						row.classList.add('kunena-topic-blink');
					}
				});
			} catch (e) {
				// The next time may work: nothing to tell the visitor.
			} finally {
				busy = false;
			}
		};

		// The filter bar: category, status, subject and author. The server does the filtering, the page only asks for the
		// list again with the filters in the address (so a filtered list can be shared or reloaded).
		const filterForm = document.getElementById('ktopicsfilter');

		const replaceList = (doc) => {
			const fresh = doc.querySelector('tbody.topic-list');

			if (!fresh) {
				return false;
			}

			list.replaceChildren(...[...fresh.childNodes].map((node) => document.importNode(node, true)));

			if (window.bootstrap && bootstrap.Tooltip) {
				list.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
			}

			// The pagination (above and under the list) and the number of topics.
			const pagination = document.querySelectorAll('[data-ktopics-pagination]');

			doc.querySelectorAll('[data-ktopics-pagination]').forEach((block, index) => {
				if (pagination[index]) {
					pagination[index].replaceChildren(...[...block.childNodes].map((node) => document.importNode(node, true)));
				}
			});

			const count = document.querySelector('[data-ktopics-count]');
			const freshCount = doc.querySelector('[data-ktopics-count]');

			if (count && freshCount) {
				count.textContent = freshCount.textContent;
			}

			return true;
		};

		const load = async (url, push) => {
			const number = ++loadNumber;

			busy = true;
			list.style.opacity = '.5';

			try {
				const response = await fetch(url, {
					credentials: 'same-origin',
					headers: {'X-Requested-With': 'XMLHttpRequest'},
				});

				if (number !== loadNumber) {
					// A newer filter was asked for meanwhile.
					return;
				}

				if (!response.ok || !replaceList(new DOMParser().parseFromString(await response.text(), 'text/html'))) {
					// Let the browser show what is wrong.
					window.location.href = url;

					return;
				}

				if (push) {
					window.history.pushState({}, '', url);
				}
			} catch (e) {
				window.location.href = url;
			} finally {
				if (number === loadNumber) {
					list.style.opacity = '';
					busy = false;
				}
			}
		};

		if (filterForm) {
			const filterUrl = () => {
				const url = new URL(filterForm.action, window.location.href);

				url.search = '';

				new FormData(filterForm).forEach((value, key) => {
					value = String(value).trim();

					if (value !== '' && !(key === 'catid' && value === '0')) {
						url.searchParams.append(key, value);
					}
				});

				return url.toString();
			};

			let timer = null;

			filterForm.addEventListener('submit', (event) => {
				event.preventDefault();
				window.clearTimeout(timer);
				load(filterUrl(), true);
			});

			filterForm.addEventListener('change', (event) => {
				if (event.target.tagName === 'SELECT') {
					load(filterUrl(), true);
				}
			});

			filterForm.addEventListener('input', (event) => {
				if (event.target.tagName === 'INPUT') {
					window.clearTimeout(timer);
					timer = window.setTimeout(() => load(filterUrl(), true), 400);
				}
			});

			filterForm.querySelector('[data-ktopics-reset]')?.addEventListener('click', (event) => {
				event.preventDefault();
				window.clearTimeout(timer);
				filterForm.reset();
				filterForm.querySelectorAll('input[type=search]').forEach((input) => {
					input.value = '';
				});
				filterForm.querySelectorAll('select').forEach((select) => {
					select.selectedIndex = 0;
				});
				load(event.currentTarget.href, true);
			});

			// The pages of a filtered list.
			document.addEventListener('click', (event) => {
				const link = event.target.closest('[data-ktopics-pagination] a[href]');

				if (link && !event.ctrlKey && !event.metaKey && !event.shiftKey && link.getAttribute('href').charAt(0) !== '#') {
					event.preventDefault();
					load(link.href, true);
				}
			});

			// Back and forward: show the list of that address and the filters in the bar.
			window.addEventListener('popstate', () => {
				const params = new URL(window.location.href).searchParams;

				filterForm.querySelectorAll('[name]').forEach((field) => {
					field.value = params.get(field.name) || (field.tagName === 'SELECT' ? field.options[0].value : '');
				});

				load(window.location.href, false);
			});
		}

		window.setInterval(refresh, interval);
		document.addEventListener('visibilitychange', () => {
			if (!document.hidden) {
				refresh();
			}
		});
	};

	// The script is loaded in the head, the list is in the body.
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
