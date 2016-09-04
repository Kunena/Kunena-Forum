/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function() {
	if (jQuery.fn.datepicker != undefined) {
		jQuery('#birthdate .input-group.date').datepicker({
			orientation: "top auto"
		});
	}
});
