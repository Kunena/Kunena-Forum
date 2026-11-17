/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

// The form of the profile opens on the tab of the avatar when the page asks for it (after an upload of the avatar).
(function () {
	'use strict';

	const init = () => {
		let avatartab = false;

		try {
			avatartab = JSON.parse(Joomla.getOptions('com_kunena.avatartab') || 'false');
		} catch (error) {
			avatartab = false;
		}

		const link = document.querySelector('.nav-tabs a[href="#editavatar"]');

		if (avatartab && link && window.bootstrap && window.bootstrap.Tab) {
			window.bootstrap.Tab.getOrCreateInstance(link).show();
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
