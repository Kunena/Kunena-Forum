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
		/* The names of the users while a name is typed in the user field of the search and of the user list (user-autocomplete.js).
		   The address is made by the router on the server, with the menu item of the forum. */
		if (window.KunenaUserAutocomplete !== undefined) {
			window.KunenaUserAutocomplete.attach(document.getElementById('kusersearch'), {
				url: Joomla.getOptions('com_kunena.usersearch_url'),
			});
		}

		/* The form of the search is folded when the page shows results. */
		const result = document.querySelector('.kunena-search-result');
		const form = document.getElementById('search');

		if (result && form && result.getClientRects().length > 0 && window.bootstrap && window.bootstrap.Collapse) {
			window.bootstrap.Collapse.getOrCreateInstance(form, { toggle: false }).hide();
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
