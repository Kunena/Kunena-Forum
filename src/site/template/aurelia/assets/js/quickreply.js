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

	const init = () => {
		// "Go to the editor" of a quick reply: its text goes to the full editor, which takes it from the storage of the browser.
		document.addEventListener('click', (event) => {
			const button = event.target instanceof Element ? event.target.closest('.gotoeditor') : null;

			if (!button || !button.id) {
				return;
			}

			const textarea = document.querySelector(`.${CSS.escape(button.id)}`);

			try {
				localStorage.setItem('copyKunenaeditor', textarea ? textarea.value : '');
			} catch (error) {
				// No storage (private window, blocked site data): the full editor opens empty.
			}
		});

		document.body.classList.add('overflow-auto');
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
