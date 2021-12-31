/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
	const quickreplyid = Joomla.getOptions('com_kunena.kunena_quickreplymesid');
    $('#gotoeditor' + quickreplyid).click(function () {
        localStorage.setItem("copyKunenaeditor", $(".qrlocalstorage" + quickreplyid).val());
    });

	$('body').addClass('overflow-auto');
});