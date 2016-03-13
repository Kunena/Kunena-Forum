/**
 * Kunena       Component
 * @package     Kunena.Template.Crypsis
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/

jQuery(document).ready(function() {
	// SHOP ELEMENT
	var shop = document.querySelector('#krating');

	// INITIALIZE
	(function init() {
		var topic_id = jQuery("#topic_id").val();
		
		jQuery.ajax({
			 dataType: "json",
			 url: 'index.php?option=com_kunena&view=topic&layout=getrate&format=raw',
			 data: 'topic_id=' + topic_id 
			}).done(function(response) {
				addRatingWidget(buildItem(), response, topic_id);
			}).fail(function(reponse) {
			
			});
	})();
	// BUILD SHOP ITEM
	function buildShopItem(data) {
		var shopItem = document.createElement('div');
		var html = '<div class="c-shop-item__img"></div>' +
			'<div class="c-shop-item__details">' +
				'<h3 class="c-shop-item__title">' + data.title + '</h3>' +
				'<p class="c-shop-item__description">' + data.description + '</p>' +
				'<ul class="c-rating"></ul>' +
			'</div>';
		shopItem.classList.add('c-shop-item');
		shopItem.innerHTML = html;
		shop.appendChild(shopItem);
		return shopItem;
	}

	function buildItem(){
		var shopItem = document.createElement('div');
		var html = '<ul class="c-rating"></ul>';
		shopItem.innerHTML = html;
		shop.appendChild(shopItem);
		return shopItem;
	}

	// ADD RATING WIDGET
	function addRatingWidget(shopItem, rate, topicid) {
		var ratingElement = shopItem.querySelector('.c-rating');
		var currentRating = rate;
		var maxRating = 5;
		var callback = function(rating) {
			jQuery.ajax({
				dataType: "json",
				url: jQuery('#krating_submit_url').val(),
				data: 'starid=' + rating + '&topic_id=' + topicid  
				}).done(function(response) {
					jQuery('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Success</h4>'+response+'</div>').appendTo('#system-message-container');
				}).fail(function(reponse) {
					jQuery('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Warning!</h4>'+reponse+'</div>').appendTo('#system-message-container');
				});  
		};
		var r = rating(ratingElement, currentRating, maxRating, callback);
	}
});
