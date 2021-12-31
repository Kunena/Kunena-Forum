/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
	$('#kpoll_go_results').click(function () {
		if ($('#poll-results').is(':visible') === true) {
			$('#poll-results').hide();
			$('#kpoll_hide_results').hide();
		}
		else {
			$('#poll-results').show();
			$('#kpoll_hide_results').show();
			$('#kpoll_go_results').hide();
		}
	});

	$('#kpoll_hide_results').click(function () {
		if ($('#poll-results').is(':visible') === true) {
			$('#poll-results').hide();
			$('#kpoll_go_results').show();
			$('#kpoll_hide_results').hide();
		}
		else {
			$('#poll-results').show();
			$('#kpoll_hide_results').show();
			$('#kpoll_go_results').hide();
		}
	});

	$('#kpoll-moreusers').click(function () {
		$('#kpoll-moreusers-div').show();
	});
});
