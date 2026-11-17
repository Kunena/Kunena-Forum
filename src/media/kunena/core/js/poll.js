/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

// The poll of a topic: the buttons which show and hide its results, and the links which show all the voters or fewer of them.
(function () {
	'use strict';

	// The elements start hidden by their style attribute: showing one takes that away.
	const show = (element) => element && (element.style.display = '');
	const hide = (element) => element && (element.style.display = 'none');
	const visible = (element) => Boolean(element) && element.getClientRects().length > 0;

	const init = () => {
		const results = document.getElementById('poll-results');
		const goResults = document.getElementById('kpoll_go_results');
		const hideResults = document.getElementById('kpoll_hide_results');

		if (goResults) {
			goResults.addEventListener('click', () => {
				if (visible(results)) {
					hide(results);
					hide(hideResults);
				} else {
					show(results);
					show(hideResults);
					hide(goResults);
				}
			});
		}

		if (hideResults) {
			hideResults.addEventListener('click', () => {
				if (visible(results)) {
					hide(results);
					show(hideResults);
					hide(hideResults);
				} else {
					show(results);
					show(hideResults);
					hide(goResults);
				}
			});
		}

		/* Under a poll: all the users who voted, or fewer of them. */
		document.querySelectorAll('.link-show-extra-users').forEach((link) => {
			link.addEventListener('click', (event) => {
				event.preventDefault();

				if (!link.classList.contains('link-show-more')) {
					hide(document.getElementById('kpoll-moreusers-span'));
					show(document.getElementById('kpoll-moreusers'));
				} else {
					hide(document.getElementById('kpoll-moreusers'));
					show(document.getElementById('kpoll-moreusers-span'));
					show(document.getElementById('kpoll-lessusers'));
				}

				hide(link);
			});
		});
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
