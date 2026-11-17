/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

(function () {
	'use strict';

	// The sibling just before or after an element, when it matches the selector (as prev() and next() of jQuery).
	const sibling = (element, selector, after) => {
		const next = after ? element.nextElementSibling : element.previousElementSibling;

		return next && next.matches(selector) ? next : null;
	};

	const show = (element, shown) => {
		if (element) {
			element.style.display = shown ? '' : 'none';
		}
	};

	const isHidden = (element) => getComputedStyle(element).display === 'none';

	document.addEventListener('click', (e) => {
		/* To hide or open spoiler on click */
		const spoiler = e.target.closest('.kspoiler-expand');

		if (spoiler) {
			e.preventDefault();

			const button = spoiler.children[0];
			const open = button.value === Joomla.Text._('COM_KUNENA_LIB_BBCODE_SPOILER_EXPAND');

			button.value = Joomla.Text._(open ? 'COM_KUNENA_LIB_BBCODE_SPOILER_HIDE' : 'COM_KUNENA_LIB_BBCODE_SPOILER_EXPAND');

			[...spoiler.parentElement.children].filter((child) => child.matches('.kspoiler-wrapper')).forEach((wrapper) => {
				wrapper.style.display = isHidden(wrapper) ? 'block' : 'none';
			});

			return;
		}

		/* Button to show more info on profilebox: "more" opens the details, "less" closes them, each button replaces the other */
		const heading = e.target.closest('.heading');

		if (heading) {
			const less = heading.classList.contains('heading-less');
			const other = less ? sibling(heading, '.heading', true) : sibling(heading, '.heading', false);
			const content = sibling(less ? other || heading : heading, '.content', true);

			show(other, true);
			show(heading, false);

			if (content) {
				content.style.display = isHidden(content) ? 'block' : 'none';
			}
		}
	});

	/* On moderate page display subject or field to enter manually the topic ID */
	const topicChosen = (select) => {
		const value = parseInt(select.value, 10);

		show(document.getElementById('kmod_subject'), value !== 0);
		show(document.getElementById('kmod_targetid'), value === -1);

		// "- Do not move -": the subject and move options do nothing, so do not show them.
		document.querySelectorAll('.kmod-moveoption').forEach((option) => {
			if (value === -2 || option.id !== 'kmod_subject') {
				show(option, value !== -2);
			}
		});
	};

	// The topics of the category which is chosen: the fixed options (do not move, new topic, enter an id: 0 or below) are kept.
	const categoryChosen = async (select) => {
		const topics = document.getElementById('kmod_topics');

		if (!topics || typeof kunena_url_ajax === 'undefined') {
			return;
		}

		const url = new URL(kunena_url_ajax, window.location.href);
		url.searchParams.set('catid', select.value);

		let json;

		try {
			const response = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
			json = await response.json();
		} catch {
			return;
		}

		[...topics.options].filter((option) => parseInt(option.value, 10) > 0).forEach((option) => option.remove());

		// Another category is a move: a message which would not move goes into a new topic there.
		if (parseInt(topics.value, 10) === -2) {
			topics.value = '0';
			topics.dispatchEvent(new Event('change', { bubbles: true }));
		}

		(Array.isArray(json?.topiclist) ? json.topiclist : []).forEach((topic) => {
			topics.append(new Option(topic.subject, topic.id));
		});
	};

	const init = () => {
		document.getElementById('kmod_topics')?.addEventListener('change', (e) => topicChosen(e.target));
		document.getElementById('kmod_categories')?.addEventListener('change', (e) => categoryChosen(e.target));
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
