/**
 * Kunena       Component
 * @package     Kunena.Template.Crypsis
 *
 * @copyright     Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
        // Krating element
        const krating = document.querySelector('#krating');

        // Initialize
        (function init() {
            const topic_id = $("#topic_id").val();

            if ($('#krating').length > 0) {
                $.ajax({
                        dataType: "json",
                        url: $('#krating_url').val(),
                        data: 'topic_id=' + topic_id
                    }
                ).done(function (response) {
                        addRatingWidget(buildItem(), response, topic_id);
                    }
                ).fail(function (reponse) {
                        //TODO: handle the error of ajax request
                    }
                );
            }
        })();

        // Build krating item
        function buildItem() {
            const ratingItem = document.createElement('div');
            ratingItem.innerHTML = Joomla.sanitizeHtml('<ul class="c-rating"></ul>');
            krating.appendChild(ratingItem);
            return ratingItem;
        }

        // Add krating widget
        function addRatingWidget(ratingItem, rate, topicid) {
            const ratingElement = ratingItem.querySelector('.c-rating');
            const currentRating = rate;
            const maxRating = 5;
            const callback = function (rating) {
                $.ajax({
                        dataType: "json",
                        url: $('#krating_submit_url').val(),
                        data: 'starid=' + rating + '&topic_id=' + topicid
                    }
                ).done(function (response) {
                        if (response.success) {
                            $('<div class="alert alert-success alert-dismissible fade show" role="alert">' + Joomla.Text._(response.message) + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>').appendTo('#system-message-container');
                        } else {
                            $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + Joomla.Text._(response.message) + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>').appendTo('#system-message-container');
                        }
                    }
                ).fail(function (response) {
                        $('<div class="alert alert-danger alert-dismissible fade show" role="alert"><h4>' + Joomla.Text._('COM_KUNENA_RATING_WARNING_LABEL') + '</h4>' + response + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>').appendTo('#system-message-container');
                    }
                );
            };
            const r = rating(ratingElement, currentRating, maxRating, callback);
        }
    }
);
