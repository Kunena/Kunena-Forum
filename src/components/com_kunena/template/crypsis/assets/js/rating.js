/**
 * Kunena       Component
 * @package     Kunena.Template.Crypsis
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/

jQuery(document).ready(function() {
	if (jQuery('#krating_block') != undefined) {
		jQuery('.kratingstar').each(function (el) {
			jQuery(this).click(function() {
				var ratelevel = jQuery(this).val();
				
				jQuery.ajax({
					dataType: "json",
					url: jQuery('#krating_submit_url').val(),
					data: 'starid=' + ratelevel 
				}).done(function(response) {
					jQuery('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Success</h4>'+response+'</div>').appendTo('#system-message-container');
				}).fail(function(reponse) {
					jQuery('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Warning!</h4>'+reponse+'</div>').appendTo('#system-message-container');
				});
			});
		});
	}
});
