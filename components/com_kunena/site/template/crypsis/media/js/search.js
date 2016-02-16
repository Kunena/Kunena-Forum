/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function() {

	/* Provide autocomplete user list in search form and in user list */
	if ( jQuery( '#kurl_users' ).length > 0 ) {
		var users_url = jQuery( '#kurl_users' ).val();

		var NameObjs = {};
		var UserNames = [];

		jQuery("#kusersearch").typeahead({
			source: function ( query, process ) {

			jQuery.ajax({
				url: users_url
				,cache: false
				,success: function(data){
					//reset these containers every time the user searches
					//because we're potentially getting entirely different results from the api
					NameObjs = {};
					UserNames = [];

					jQuery.each( data, function( index, item ){

						//for each iteration of this loop the "item" argument contains
						//1 user object from the array in our json, such as:
						// { "id":7, "name":"Pierce Brosnan" }

						//add the label to the display array
						UserNames.push( item.name );

						//also store a hashmap so that when bootstrap gives us the selected
						//name we can map that back to an id value
						NameObjs[ item.name ] = item;
					});

					//send the array of results to bootstrap for display
					process( UserNames );
				}
			});

			}
			,highlighter: function( item ){
				var user = NameObjs[ item ];

				return '<div class="bond">'
					+'<img src="' + user.photo + '" title="" />'
					+'<br/><strong>' + user.name + '</strong>'
					+'</div>';
				}
		});
	}

	/* Hide search form when there are search results found */
	if ( jQuery('#kunena_search_results').is(':visible') ) {
		jQuery('#search').collapse("hide");
	}
});

