/**
 * Kunena       Component
 * @package     Kunena.BackendTemplate
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

$(document).ready(function() {
	$('#catidmodslist').on( "change", function() {
	    $('#catidmodslist option').removeAttr("selected");
	});
});
