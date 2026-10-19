/**
 * Kunena Component
 *
 * Reloads the categories of the forum index in the background and makes the row of a category blink when it got a
 * new message.
 *
 * @package         Kunena.Template.Aurelia
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

(function () {
	'use strict';

	const start = () => {
		const options = (window.Joomla && Joomla.getOptions) ? Joomla.getOptions('com_kunena.categorylist') : null;

		if (!options || !document.querySelector('tr.category[data-category-id]')) {
			return;
		}

		const interval = Math.max(10, parseInt(options.interval, 10) || 30) * 1000;
		let busy = false;

		// The blink is made here, so the template does not need a rebuilt stylesheet.
		if (!document.getElementById('kunena-blink-style')) {
			const style = document.createElement('style');
			style.id = 'kunena-blink-style';
			style.textContent = '@keyframes kunena-topic-blink{0%,100%{background-color:transparent}50%{background-color:rgba(255,193,7,.45)}}'
				+ '.kunena-topic-blink > *{animation:kunena-topic-blink 1s ease-in-out 5}'
				+ '@media (prefers-reduced-motion:reduce){.kunena-topic-blink > *{animation:none;background-color:rgba(255,193,7,.25)}}';
			document.head.appendChild(style);
		}

		const text = (row) => row.textContent.replace(/\s+/g, ' ').trim();

		const categoryRows = (root) => {
			const rows = new Map();

			root.querySelectorAll('tr.category[data-category-id]').forEach((row) => rows.set(row.dataset.categoryId, row));

			return rows;
		};

		const refresh = async () => {
			if (busy || document.hidden) {
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
				const before = categoryRows(document);
				const after = categoryRows(doc);

				// A category was added, removed or became visible: leave this to the next page load.
				if (before.size !== after.size || [...after.keys()].some((id) => !before.has(id))) {
					return;
				}

				after.forEach((row, id) => {
					const old = before.get(id);

					// Same messages, but the relative times (like "2 minutes ago") move on: refresh the row without blinking.
					if (old.dataset.sig === row.dataset.sig) {
						if (text(old) !== text(row)) {
							old.replaceWith(document.importNode(row, true));
						}

						return;
					}

					const blink = old.dataset.lastPost !== row.dataset.lastPost;
					const replacement = document.importNode(row, true);

					old.replaceWith(replacement);

					if (window.bootstrap && bootstrap.Tooltip) {
						replacement.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
					}

					if (blink) {
						replacement.classList.add('kunena-topic-blink');
					}
				});

				// The number of topics in the header of a section.
				document.querySelectorAll('[id^="ksection-count"]').forEach((el) => {
					const fresh = doc.getElementById(el.id);

					if (fresh && fresh.textContent.trim() !== el.textContent.trim()) {
						el.textContent = fresh.textContent.trim();
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

	// The script is loaded in the head, the categories are in the body.
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
