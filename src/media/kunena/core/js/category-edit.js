/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

// The aliases of a category (the form of a category in the backend and in the forum): while an alias is typed, the server says
// whether another category has it (task category.chkaliases); the buttons which save are disabled while it is taken.
(function () {
	'use strict';

	const init = () => {
		const input = document.getElementById('jform_aliases');

		if (!input) {
			return;
		}

		const box = document.getElementById('aliascheck');
		const info = document.getElementById('aliasinfo');
		const buttons = () => document.querySelectorAll('.button-apply, .button-save, .button-save-new, .button-save-copy');
		let request = null;

		input.addEventListener('input', () => {
			const catid = (document.querySelector('input[name="catid"]') || {}).value || 0;
			// From the root of the site or of the backend: on a page of the forum with addresses of the router (/forum/category/...),
			// a relative "index.php" is a path under the page.
			const base = (window.Joomla && Joomla.getOptions('system.paths') || {}).base || '';
			const url = `${base}/index.php?option=com_kunena&view=category&task=category.chkaliases&alias=${encodeURIComponent(input.value)}&catid=${encodeURIComponent(catid)}&format=json`;

			// Only the answer of the last alias counts.
			if (request) {
				request.abort();
			}

			request = new AbortController();
			fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' }, signal: request.signal })
				.then((response) => response.json())
				.then((answer) => {
					const free = Boolean(answer && answer.msg);

					input.classList.add('inputbox');
					input.classList.toggle('invalid-border', !free);

					if (box) {
						buttons().forEach((button) => {
							button.disabled = !free;
						});
					}

					if (!free && box) {
						box.classList.remove('valid', 'icon-ok');
						box.classList.add('invalid', 'icon', 'icon-remove');
						box.textContent = Joomla.Text._('COM_KUNENA_CATEGORIES_ERROR_CHOOSE_ANOTHER_ALIAS');
					} else if (free && box && info) {
						info.classList.add('valid', 'icon', 'icon-ok');
						info.textContent = '';
					}
				})
				.catch(() => {
					// An aborted check (a newer alias), or no answer: the form stays as it is.
				});
		});
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
