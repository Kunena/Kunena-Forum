/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
	const results = $('#poll-results');
	const hide = $('#kpoll_hide_results');

	$('#kpoll_go_results').click(function () {
		if (results.is(':visible') === true) {
			results.hide();
			hide.hide();
		}
		else {
			results.show();
			hide.show();
			$('#kpoll_go_results').hide();
		}
	});

	hide.click(function () {
		if (results.is(':visible') === true) {
			results.hide();
			hide.show();
			$('#kpoll_hide_results').hide();
		}
		else {
			results.show();
			hide.show();
			$('#kpoll_go_results').hide();
		}
	});

	$('#kpoll-moreusers').click(function () {
		$('#kpoll-moreusers-div').show();
	});
});