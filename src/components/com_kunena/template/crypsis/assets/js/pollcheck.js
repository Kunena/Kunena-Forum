/**
* Kunena Component
* @package Kunena.Template.Crypsis
*
* @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/

jQuery(document).ready(function() {
	if ( typeof pollcategoriesid != 'undefined' && jQuery('#poll_exist_edit').length == 0 ) {
		var catid = jQuery('#kcategory_poll').val();

		if ( pollcategoriesid[catid] !== undefined ) {
			jQuery('.pollbutton').show();
		} else {
			jQuery('.pollbutton').hide();
		}
	} else if ( jQuery('#poll_exist_edit').length > 0 ) {
		jQuery('.pollbutton').show();
	} else {
		jQuery('.pollbutton').hide();
	}
});