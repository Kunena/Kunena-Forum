/**
 * Kunena       Component
 * @package     Kunena.Template.Crypsis
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/

jQuery(document).ready(function() {
	if (jQuery('#krating_block') != undefined) {
		jQuery('.kratingstar').each(function (el) {
			jQuery('.kratingstar').click(function() {
				var ratelevel = jQuery('.kratingstar').val();
				var url = jQuery('#krating_submit_url').val();
				var res = new Request({
					method    : 'post',
					url       : url,
					data      : 'starid=' + ratelevel,
					onComplete: function (r) {
						jQuery('#star' + ratelevel).val('checked', 'checked');
					}
				}).send();
			});
		});
	}
});
