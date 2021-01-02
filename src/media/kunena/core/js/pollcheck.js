/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright     Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
	const pollcategoriesid = jQuery.parseJSON(Joomla.getOptions('com_kunena.pollcategoriesid'));

	if (typeof pollcategoriesid !== 'undefined' && pollcategoriesid !== null && $('#poll_exist_edit').length === 0) {
		const catid = $('#kcategory_poll').val();

		if (pollcategoriesid[catid] !== undefined) {
			$('.pollbutton').show();
		}
		else {
			$('.pollbutton').hide();
		}
	}
	else if ($('#poll_exist_edit').length > 0) {
		$('.pollbutton').show();
	}
	else {
		$('.pollbutton').hide();
	}
});
