/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
	$('.gotoeditor').click(function () {
		var texteareaId = $(this).attr('id');
		localStorage.setItem("copyKunenaeditor", $("."+texteareaId).val());
	});

	$('body').addClass('overflow-auto');
});