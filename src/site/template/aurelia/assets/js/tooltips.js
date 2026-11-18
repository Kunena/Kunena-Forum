/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

"use strict";

// The titles of the forum become the tooltips of Bootstrap (option Tooltips of the template on), or are removed (off). Only the
// elements of the forum (#kunena): the menus and modules of the site template keep their own titles. An element which already
// toggles something else (a dropdown, a collapse, a modal) keeps its title as it is: Bootstrap has one data-bs-toggle. The editors
// (SCEditor, CKEditor 5) keep the titles of their own buttons: they may be made before this script runs, and the poll button
// explains with its title why it is disabled.
document.addEventListener("DOMContentLoaded", function () {
	if (typeof Joomla === "undefined") {
		return;
	}

	const forum = document.getElementById("kunena");

	if (!forum) {
		return;
	}

	const displayTooltips = Joomla.getOptions("com_kunena.tooltips", "0");
	const canShow = typeof bootstrap !== "undefined" && typeof bootstrap.Tooltip === "function";

	// An element inside another one with a title (an avatar in the link to the profile): the outer one has the tooltip, which says
	// what a click does. Two tooltips of Bootstrap would open over each other (the browser shows only one native title).
	const hasTitledAncestor = (element) => {
		const outer = element.parentElement && element.parentElement.closest("[title], [data-bs-original-title], [data-original-title]");

		return outer !== null && forum.contains(outer)
			&& (outer.getAttribute("title") || outer.getAttribute("data-bs-original-title") || outer.getAttribute("data-original-title"));
	};

	forum.querySelectorAll("[title]").forEach((element) => {
		const toggle = element.getAttribute("data-bs-toggle");

		if ((toggle !== null && toggle !== "tooltip") || !element.title || element.closest(".sceditor-container, .ck-editor")) {
			return;
		}

		if (hasTitledAncestor(element)) {
			element.setAttribute("data-original-title", element.title);
			element.title = "";

			return;
		}

		if (displayTooltips == 0) {
			// Tooltips off: no title either.
			element.setAttribute("data-original-title", element.title);
			element.title = "";
		} else if (canShow) {
			bootstrap.Tooltip.getOrCreateInstance(element);
		}
	});
});
