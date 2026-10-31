/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

/*
 * Helpers of the autocomplete of the user field. They do not touch the page, so that they can be tested on their own.
 */
(function (root) {
	'use strict';

	const HTML = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'};

	const KunenaUserSearch = {
		// Text for a place in html. The names come from the accounts of the site: they are text, never markup.
		escape: function (text) {
			return String(text === null || text === undefined ? '' : text).replace(/[&<>"']/g, function (c) {
				return HTML[c];
			});
		},

		// The address of the search, below the folder of the site (the site may be installed in a sub-folder), with the term encoded.
		url: function (term) {
			const paths = root.Joomla && root.Joomla.getOptions ? root.Joomla.getOptions('system.paths') : null;
			const base = paths && typeof paths.base === 'string' ? paths.base.replace(/\/+$/, '') : '';

			return base + '/index.php?option=com_kunena&view=user&task=getusersmentionssearch&format=json&usersearch=' + encodeURIComponent(term);
		},

		// The line of the dropdown for one user.
		menuItem: function (item) {
			const user = item && item.original ? item.original : {};

			return KunenaUserSearch.escape(user.name !== undefined ? user.name : item && item.string);
		}
	};

	root.KunenaUserSearch = KunenaUserSearch;
})(typeof window !== 'undefined' ? window : globalThis);

jQuery(document).ready(function ($) {
    /* Provide autocomplete user list in search form and in user list */
    function remoteSearch(text, cb) {
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    let data = [];

                    try {
                        data = JSON.parse(xhr.responseText);
                    } catch (e) {
                        data = [];
                    }

                    cb(Array.isArray(data) ? data : []);
                } else {
                    cb([]);
                }
            }
        };
        xhr.open("GET", KunenaUserSearch.url(text), true);
        xhr.send();
    }

    const userField = document.getElementById("kusersearch");

    // The page of the list of users has no script of Tribute.
    if (userField && typeof Tribute !== 'undefined') {
        const tribute = new Tribute({
            collection: [],
            //..other config options
            // function retrieving an array of objects
            values: function (text, cb) {
                remoteSearch(text, users => cb(users));
            },
            lookup: 'name',
            fillAttr: 'name',
            // The default template writes the name into the page as html.
            menuItemTemplate: KunenaUserSearch.menuItem
        });

        tribute.attach(userField);
    }

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
