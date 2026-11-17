/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

// The categories of the batch of moderators: a choice in the list takes away the choice which the page selected first.
(function () {
	'use strict';

	const init = () => {
		const list = document.getElementById('catidmodslist');

		if (list) {
			list.addEventListener('change', () => {
				list.querySelectorAll('option[selected]').forEach((option) => option.removeAttribute('selected'));
			});
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
