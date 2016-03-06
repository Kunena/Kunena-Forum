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
				}).done(function() {
					console.log( "second success" );
				}).fail(function() {
					console.log( "error" );
				});
			});
		});
	}
});
