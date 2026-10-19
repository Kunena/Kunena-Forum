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

			return !!(form && (form.querySelector('input.kcheck:checked, input[type=checkbox]:checked:not(.kcheckall)')
				|| form.contains(document.activeElement) && /^(INPUT|SELECT|TEXTAREA)$/.test(document.activeElement.tagName)));
		};

		const refresh = async () => {
			if (busy || document.hidden || inUse()) {
				return;
			}

			busy = true;

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

				if (!fresh || inUse()) {
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
