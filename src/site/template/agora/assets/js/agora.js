/**
 * Kunena Component
 * @package Kunena.Template.Agora
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

"use strict";

/* The colour scheme of the options follows the device: the forum (the scheme of Bootstrap, data-bs-theme, of #kunena) and the
 * pop-ups which Bootstrap puts at the end of the page get the light or the dark colours, also when the device changes them. */
document.addEventListener("DOMContentLoaded", function () {
	const forum = document.getElementById("kunena");

	if (!forum || !forum.classList.contains("agora") || forum.dataset.agoraScheme !== "auto") {
		return;
	}

	const media = window.matchMedia("(prefers-color-scheme: dark)");

	const apply = function () {
		const theme = media.matches ? "dark" : "light";

		forum.setAttribute("data-bs-theme", theme);

		document.querySelectorAll("body > .tooltip, body > .popover, body > .modal").forEach(function (el) {
			el.setAttribute("data-bs-theme", theme);
		});
	};

	media.addEventListener("change", apply);
	apply();
});
