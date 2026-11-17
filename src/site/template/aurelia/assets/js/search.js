/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
    /* The names of the users while a name is typed in the user field of the search and of the user list (user-autocomplete.js).
       The address is made by the router on the server, with the menu item of the forum. */
    if (window.KunenaUserAutocomplete !== undefined) {
        window.KunenaUserAutocomplete.attach(document.getElementById('kusersearch'), {
            url: Joomla.getOptions('com_kunena.usersearch_url'),
        });
    }

	/* Hide search form when there are search results found */
	if ($('.kunena-search-result').first().is(':visible')) {
		$('#search').collapse("hide");
	}

	if (jQuery.fn.datepicker !== undefined) {
		jQuery("#searchatdate.input-group.date").datepicker({
			orientation: "top auto",
			language: "kunena",
		});
	}
});
