/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

/**
 *  Helper function for to perform JSON request for preview
 */
var previewActive=false;

function kPreviewHelper(previewActive) {
	if (previewActive == true){
		if ( jQuery('#kbbcode-message').val() != null ) {
			jQuery.ajax({
				type: 'POST',
				url: jQuery('#kpreview_url').val(),
				async: false,
				dataType: 'json',
				data: {body : jQuery('#kbbcode-message').val() },
				success: function(data){
					jQuery('#kbbcode-preview').html(data.preview);
				}
			});
		}
	}
}

jQuery(document).ready(function() {

	/* To enabled emojis in kunena textera feature like on github */
	if ( jQuery('#kemojis_allowed').val() ) {
		var item = '';
		if ( jQuery('#kbbcode-message').length > 0 ) {
			item = '#kbbcode-message';
		} else if ( jQuery('.qreply').length > 0 ) 	{
			item = '.qreply';
		}

		if ( item!=undefined ) {
			jQuery(item).atwho({
				at: ":",
				tpl:"<li data-value='${key}'>${name} <img src='${url}' height='20' width='20' /></li>",
				callbacks: {
					remote_filter: function(query, callback) {
						if(query.length > 0) {
							jQuery.ajax({
								url: jQuery( "#kurl_emojis" ).val(),
								data: {
									search : query
								},
								success: function(data) {
									callback(data.emojis);
								}
							});
						}
					}
				}
			});
		}
	}

	/* To display preview area when clicking on preview button */
	jQuery("#kbutton-preview").click(function() {
		var preview = jQuery("#kbbcode-preview");
		var message = jQuery("#kbbcode-message");

		if ( preview.length > 0 ) {
			if ( !preview.is(":visible") ) {
				preview.css('display', 'block');

				message.css('width', '95%');

				previewActive = true;
				kPreviewHelper(previewActive);
			} else {
				previewActive = false;
				preview.css('display', 'none');
				message.css('width', '95%');
			}
			preview.attr('class', 'kbbcode-preview-bottom controls');
			var height = message.css('height');
			preview.css('height', message.css('height'));
		}
	});

	jQuery('#kbbcode-message').bind('input propertychange', function() {
		kPreviewHelper(previewActive);
	});

	/* Store form data into localstorage every 1 second */
	if ( jQuery.fn.sisyphus!=undefined ) {
		jQuery("#postform").sisyphus( {
			locationBased: true,
			timeout: 5
		});
	}

	jQuery('#kshow_attach_form').click(function() {
		if (jQuery('#kattach_form').is(":visible"))
		{
			jQuery('#kattach_form').hide();
		}
		else
		{
			jQuery('#kattach_form').show();
		}
	});

	// Load topic icons by ajax request
	jQuery('#postcatid').change(function() {
		var kurl_topicons_request = jQuery('#kurl_topicons_request').val();

		jQuery.ajax({
				type: 'POST',
				url: kurl_topicons_request,
				async: false,
				dataType: 'json',
				data: {catid : jQuery('select#postcatid option').filter(':selected').val() },
				success: function(data){
					jQuery('#iconset_topic_list').remove();

					var div_object = jQuery('<div>', {'id': 'iconset_topic_list'});

					jQuery('#iconset_inject').append(div_object);

					jQuery.each(data, function( index, value ) {
						if ( value.type != 'system' )
						{
							if (value.id==0)
							{
								var input = jQuery('<input>', {type: 'radio', id: 'radio'+value.id, checked: 'checked', name: 'topic_emoticon', value: value.id});
							}
							else
							{
								var input = jQuery('<input>', {type: 'radio', id: 'radio'+value.id, name: 'topic_emoticon', value: value.id});
							}

							var span_object = jQuery('<span>', {'class': 'kiconsel'}).append(input);
							var label = jQuery('<label>', {'class': 'radio inline', 'for': value.id}).append(jQuery('<img>', {'src': value.path, 'border': '0', 'al': ''}));
							span_object.append(label);

							jQuery('#iconset_topic_list').append(span_object);
						}
					});
				}
			});
	  });
});

