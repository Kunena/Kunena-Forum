/**
 * Kunena       Component
 * @package     Kunena.Template.Crypsis
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
	// Krating element
	var krating = document.querySelector('#krating');

	// Initialize
	(function init() {
		var topic_id = $("#topic_id").val();

		if ($('#krating').length > 0) {
			$.ajax({
				dataType: "json",
				url: $('#krating_url').val(),
				data: 'topic_id=' + topic_id
			}).done(function (response) {
				addRatingWidget(buildItem(), response, topic_id);
			}).fail(function (reponse) {
				//TODO: handle the error of ajax request
			});
		}
	})();

	// Build krating item
	function buildItem() {
		var ratingItem = document.createElement('div');
		ratingItem.innerHTML = '<ul class="c-rating"></ul>';
		krating.appendChild(ratingItem);
		return ratingItem;
	}

	// Add krating widget
	function addRatingWidget(ratingItem, rate, topicid) {
		var ratingElement = ratingItem.querySelector('.c-rating');
		var currentRating = rate;
		var maxRating = 5;
		var callback = function (rating) {
			$.ajax({
				dataType: "json",
				url: $('#krating_submit_url').val(),
				data: 'starid=' + rating + '&topic_id=' + topicid
			}).done(function (response) {
				if (response.success) {
					$('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>' + Joomla.JText._('COM_KUNENA_RATING_SUCCESS_LABEL') + '</h4>' + Joomla.JText._(response.message) + '</div>').appendTo('#system-message-container');
				}
				else {
					$('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>' + Joomla.JText._('COM_KUNENA_RATING_WARNING_LABEL') + '</h4>' + Joomla.JText._(response.message) + '</div>').appendTo('#system-message-container');
				}
			}).fail(function (reponse) {
				$('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>' + Joomla.JText._('COM_KUNENA_RATING_WARNING_LABEL') + '</h4>' + reponse + '</div>').appendTo('#system-message-container');
			});
		};
		var r = rating(ratingElement, currentRating, maxRating, callback);
	}
});
