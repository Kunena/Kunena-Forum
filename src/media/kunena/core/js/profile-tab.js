/**
 * Kunena Component
 * @package     Kunena.Media
 *
 * @copyright   Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

/**
 * The profile of a user opens on the tab which its address names: #banuser (the link "New Ban" of the moderation of a post), #posts,
 * #thankyou... A name which is no tab of the page changes nothing.
 */
(function () {
	'use strict';

	const show = () => {
		const name = window.location.hash.slice(1);

		if (!/^[\w-]+$/.test(name) || !window.bootstrap || !window.bootstrap.Tab) {
			return;
		}

		const button = document.querySelector(`.nav-tabs [data-bs-target="#${name}"], .nav-tabs [href="#${name}"]`);

		if (button) {
			window.bootstrap.Tab.getOrCreateInstance(button).show();
			// After the load: the browser puts the page back where it was before, which undid an earlier scroll.
			const scroll = () => button.scrollIntoView({ block: 'start', behavior: 'smooth' });

			document.readyState === 'complete' ? scroll() : window.addEventListener('load', () => setTimeout(scroll, 0), { once: true });
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', show);
	} else {
		show();
	}

	window.addEventListener('hashchange', show);
})();
