/**
 * Kunena Component
 *
 * Asks in the background whether the topic which is read got new replies (the replies of the other users after the last message of
 * the topic when the page was made), and shows a bar with their number and a link to the first one; the title of the tab gets the
 * number too. Nothing is asked while the tab is hidden.
 *
 * @package         Kunena.Template.Aurelia
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

(function () {
	'use strict';

	const start = () => {
		const options = (window.Joomla && Joomla.getOptions) ? Joomla.getOptions('com_kunena.topicnew') : null;
		const root = document.querySelector('.kunena-topic-item');

		if (!options || !options.url || !root) {
			return;
		}

		const interval = Math.max(10, parseInt(options.interval, 10) || 30) * 1000;
		const text = (name) => (window.Joomla && Joomla.Text ? Joomla.Text._(name) : name);
		const title = document.title;
		let busy = false;
		let shown = 0;

		const bar = document.createElement('div');
		bar.className = 'ktopic-new-replies';
		bar.hidden = true;

		const status = document.createElement('span');
		status.setAttribute('role', 'status');

		const link = document.createElement('a');
		link.className = 'btn btn-primary btn-sm';
		link.textContent = text('COM_KUNENA_TOPIC_NEW_REPLIES_SHOW');

		bar.append(status, link);
		root.prepend(bar);

		const show = (count, url) => {
			if (count === shown) {
				return;
			}

			shown = count;
			bar.hidden = count === 0;

			if (count > 0) {
				status.textContent = text(count === 1 ? 'COM_KUNENA_TOPIC_NEW_REPLIES_1' : 'COM_KUNENA_TOPIC_NEW_REPLIES').replace('%d', String(count));
				link.href = url;
				document.title = '(' + count + ') ' + title;
			} else {
				document.title = title;
			}
		};

		const ask = async () => {
			if (busy || document.hidden) {
				return;
			}

			busy = true;

			try {
				const response = await fetch(options.url, {
					credentials: 'same-origin',
					headers: {'X-Requested-With': 'XMLHttpRequest'},
				});

				if (!response.ok) {
					return;
				}

				const json = await response.json();
				const data = json && json.data ? json.data : json;

				if (data && typeof data.count === 'number') {
					show(data.count, data.url || window.location.href);
				}
			} catch (e) {
				// No answer (offline, logged out): the next question tries again.
			} finally {
				busy = false;
			}
		};

		window.setInterval(ask, interval);

		// A tab which comes back asks at once.
		document.addEventListener('visibilitychange', () => {
			if (!document.hidden) {
				ask();
			}
		});
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
