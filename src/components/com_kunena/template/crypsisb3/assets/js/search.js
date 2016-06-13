/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function($) {

	/* Provide autocomplete user list in search form and in user list */
	if ( $( '#kurl_users' ).length > 0 ) {
		var users_url = $( '#kurl_users' ).val();

		$('#kusersearch').atwho({
			at: "",
			tpl: '<li data-value="${username}"><i class="icon-user"></i> ${username} <small>(${name})</small></li>',
			limit: 7,
			callbacks: {
				remote_filter: function(query, callback)  {
					$.ajax({
						url: users_url,
						data: {
							search : query
						},
						success: function(data) {
							callback(data.names);
						}
					});
				}
			}
		});
	}

	/* Hide search form when there are search results found */
	if ( $('#kunena_search_results').is(':visible') ) {
		$('#search').collapse("hide");
	}

});

