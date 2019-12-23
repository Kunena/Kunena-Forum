/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
	var pollcategoriesid = jQuery.parseJSON(Joomla.getOptions('com_kunena.pollcategoriesid'));
	var pollexist = $('#poll_exist_edit');

	if (typeof pollcategoriesid !== 'undefined' && pollcategoriesid !== null && pollexist.length === 0) {
		var catid = $('#kcategory_poll').val();

		if (pollcategoriesid[catid] !== undefined) {
			$('.pollbutton').show();
		}
		else {
			$('.pollbutton').hide();
		}
	}
	else if (pollexist.length > 0) {
		$('.pollbutton').show();
	}
	else {
		$('.pollbutton').hide();
	}
});
