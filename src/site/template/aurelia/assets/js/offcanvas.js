/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

"use strict";

document.addEventListener("DOMContentLoaded", function () {

	let offcanvasElementList = [].slice.call(document.querySelectorAll('.offcanvas'))
	let offcanvasList = offcanvasElementList.map(function (offcanvasEl) {
	  return new bootstrap.Offcanvas(offcanvasEl)
	})
});