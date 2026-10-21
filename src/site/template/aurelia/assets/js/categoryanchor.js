/**
 * Kunena Component
 *
 * Makes a link to a category work: example.com/forum#123 (or #category123, #section123) goes to the category, and opens the
 * section when it is collapsed.
 *
 * @package         Kunena.Template.Aurelia
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

(function () {
	'use strict';

	const find = (hash) => {
		const match = /^#(category|section)?(\d+)$/.exec(hash);

		if (!match) {
			return null;
		}

		const row = document.getElementById('category' + match[2]);
		const header = document.getElementById('category-header' + match[2]);

		// The number alone, or category: the row of the category (or its title when it is a section). Section: the title.
		return match[1] === 'section' ? (header || row) : (row || header);
	};

	const go = () => {
		const target = find(window.location.hash);

		if (!target) {
			return;
		}

		const body = target.closest('.collapse');

		if (body && !body.classList.contains('show')) {
			if (window.bootstrap && window.bootstrap.Collapse) {
				// The scroll waits until the section is open (it grows while it opens).
				body.addEventListener('shown.bs.collapse', () => target.scrollIntoView(), {once: true});
				window.bootstrap.Collapse.getOrCreateInstance(body, {toggle: false}).show();

				return;
			}

			body.classList.add('show');
		}

		target.scrollIntoView();
	};

	window.addEventListener('hashchange', go);

	// The page can still change its height while it loads: go again when it is ready.
	if (document.readyState === 'complete') {
		go();
	} else {
		window.addEventListener('load', go);
	}
})();
