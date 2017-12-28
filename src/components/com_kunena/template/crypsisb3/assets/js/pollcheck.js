/**
* Kunena Component
* @package Kunena.Template.Crypsis
*
* @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
* @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link https://www.kunena.org
**/

jQuery(document).ready(function($) {
	if ( typeof pollcategoriesid != 'undefined' && $('#poll_exist_edit').length == 0 ) {
		var catid = $('#kcategory_poll').val();

		if ( pollcategoriesid[catid] !== undefined ) {
			$('.pollbutton').show();
		} else {
			$('.pollbutton').hide();
		}
	} else if ( $('#poll_exist_edit').length > 0 ) {
		$('.pollbutton').show();
	} else {
		$('.pollbutton').hide();
	}
});
