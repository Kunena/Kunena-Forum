/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

"use strict";

document.addEventListener("DOMContentLoaded", function () {

	if (Joomla) {
		let displayTooltips = Joomla.getOptions('com_kunena.tooltips', '0');
		let tooltipTriggerList = [].slice.call(document.querySelectorAll('#kunena [title]'))

		if (displayTooltips == 0) {
			// Remove Tooltips
			let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
				tooltipTriggerEl.setAttribute('data-original-title', tooltipTriggerEl.title);
				tooltipTriggerEl.title = "";
			});
		} else {
			// Initialize Tooltips
			let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
				return new bootstrap.Tooltip(tooltipTriggerEl)
			});
		}
	}
});