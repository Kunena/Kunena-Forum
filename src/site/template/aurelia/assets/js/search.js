/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
    /* Provide autocomplete user list in search form and in user list */
    function remoteSearch(text, cb) {
        var URL = '/index.php?option=com_kunena&view=user&task=getusersmentionssearch&format=json';
        xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                cb(data);
            } else if (xhr.status === 403) {
                cb([]);
            }
        }
    };
        xhr.open("GET", URL + "&usersearch=" + text, true);
        xhr.send();
    }

    var tribute = new Tribute({
        collection: [],
        //..other config options
        // function retrieving an array of objects
        values: function (text, cb) {
            remoteSearch(text, users => cb(users));
        },
        lookup: 'name',
        fillAttr: 'name'
    });

    tribute.attach(document.getElementById("kusersearch"));

	/* Hide search form when there are search results found */
	if ($('#kunena_search_results').is(':visible')) {
		$('#search').collapse("hide");
	}

	if (jQuery.fn.datepicker !== undefined) {
		jQuery("#searchatdate.input-group.date").datepicker({
			orientation: "top auto",
			language: "kunena",
		});
	}
});
