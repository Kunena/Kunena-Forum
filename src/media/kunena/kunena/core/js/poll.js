/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
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

	/* Under a poll : show more users list */
	$(".link-show-extra-users").click(function () {
		if (!$(this).hasClass('link-show-more')) {
			$("#kpoll-moreusers-span").hide();	
			$(this).hide();		
		}
		else {
			$("#kpoll-moreusers").show();	
			$("#kpoll-moreusers-span").show();
			$("#kpoll-lessusers").show();	
			$(this).hide();
		}
	});
});