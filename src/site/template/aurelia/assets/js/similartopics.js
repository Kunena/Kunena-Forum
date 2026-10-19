/**
 * Kunena Component
 *
 * Suggests the topics with a similar subject while the subject of a new topic is written.
 *
 * @package         Kunena.Template.Aurelia
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

(function () {
	'use strict';

	const start = () => {
		const options = (window.Joomla && Joomla.getOptions) ? Joomla.getOptions('com_kunena.similartopics') : null;
		const subject = document.getElementById('subject');
		const box = document.getElementById('similar-topics');
		const list = document.getElementById('similar-topics-list');

		if (!options || !subject || !box || !list) {
			return;
		}

		let timer = null;
		let asked = 0;
		let last = '';

		const show = (topics) => {
			list.replaceChildren();

			topics.forEach((topic) => {
				const item = document.createElement('li');
				const link = document.createElement('a');
				const info = document.createElement('small');

				link.href = topic.url;
				link.target = '_blank';
				link.rel = 'noopener';
				link.textContent = topic.subject;

				info.className = 'text-muted ms-2';
				info.textContent = topic.category + ' - '
					+ (topic.replies === 1 ? options.reply : options.replies.replace('%d', topic.replies));

				item.append(link, info);
				list.appendChild(item);
			});

			box.classList.toggle('d-none', !topics.length);
		};

		const search = async () => {
			const text = subject.value.trim();

			// Nothing to look for in a very short subject, and the same subject was already asked.
			if (text.length < 4 || text === last) {
				if (text.length < 4) {
					show([]);
				}

				return;
			}

			last = text;

			const number = ++asked;

			try {
				const response = await fetch(options.url + '&subject=' + encodeURIComponent(text), {
					credentials: 'same-origin',
					headers: {'X-Requested-With': 'XMLHttpRequest'},
				});

				if (!response.ok || number !== asked) {
					return;
				}

				const json = await response.json();

				if (number === asked) {
					show(Array.isArray(json.data) ? json.data : []);
				}
			} catch (e) {
				// The suggestions are a help: nothing to tell the visitor when they fail.
			}
		};

		subject.addEventListener('input', () => {
			window.clearTimeout(timer);
			timer = window.setTimeout(search, 400);
		});

		// A subject which is already filled (a draft or a failed post).
		search();
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
