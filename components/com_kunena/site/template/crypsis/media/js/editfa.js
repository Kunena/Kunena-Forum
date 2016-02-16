/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

/**
 *  Helper function for to perform JSON request for preview
 */
var previewActive = false;

function kPreviewHelper(previewActive) {
	if (jQuery('#kbbcode-message').val() != null) {
		jQuery.ajax({
			type    : 'POST',
			url     : jQuery('#kpreview_url').val(),
			async   : false,
			dataType: 'json',
			data    : {body: jQuery('#kbbcode-message').val()},
			success : function (data) {
				jQuery('#kbbcode-preview').html(data.preview);
			}
		});
	}
}

jQuery(document).ready(function () {
	jQuery('#tabs_kunena_editor a:first').tab('show');

	jQuery('#tabs_kunena_editor a:last').click(function (e) {
		e.preventDefault();

		var preview = jQuery("#kbbcode-preview");
		var message = jQuery("#kbbcode-message");

		preview.css('display', 'block');

		message.hide();

		kPreviewHelper();

		preview.attr('class', 'kbbcode-preview-bottom controls');
		var height = message.css('height');
		preview.css('height', message.css('height'));
	});

	jQuery('#tabs_kunena_editor a:not(:last)').click(function (e) {
		jQuery('#kbbcode-preview').hide();
		jQuery('#kbbcode-message').css('display', 'inline-block');
		jQuery('#markItUpKbbcode-message').css('display', 'inline-block');
	});

	jQuery('#tabs_kunena_editor a:last').click(function (e) {
		jQuery('#kbbcode-message').hide();
		jQuery('#markItUpKbbcode-message').hide();
	});

	/* To enabled emojis in kunena textera feature like on github */
	if (jQuery('#kemojis_allowed').val()) {
		var item = '';
		if (jQuery('#kbbcode-message').length > 0) {
			item = '#kbbcode-message';
		} else if (jQuery('.qreply').length > 0) {
			item = '.qreply';
		}

		if (item != undefined) {
			jQuery(item).atwho({
				at       : ":",
				tpl      : "<li data-value='${key}'>${name} <img src='${url}' height='20' width='20' /></li>",
				callbacks: {
					remote_filter: function (query, callback) {
						if (query.length > 0) {
							jQuery.ajax({
								url    : jQuery("#kurl_emojis").val(),
								data   : {
									search: query
								},
								success: function (data) {
									callback(data.emojis);
								}
							});
						}
					}
				}
			});
		}
	}

	/* Store form data into localstorage every 1 second */
	if (jQuery.fn.sisyphus != undefined) {
		jQuery("#postform").sisyphus({
			locationBased: true,
			timeout      : 5
		});
	}

	jQuery('#kshow_attach_form').click(function () {
		if (jQuery('#kattach_form').is(":visible")) {
			jQuery('#kattach_form').hide();
		}
		else {
			jQuery('#kattach_form').show();
		}
	});

	// Load topic icons by ajax request
	jQuery('#postcatid').change(function () {
		var catid = jQuery('select#postcatid option').filter(':selected').val();
		var kurl_topicons_request = jQuery('#kurl_topicons_request').val();

		if (jQuery('#kanynomous-check').length > 0) {
			if (arrayanynomousbox[catid] !== undefined) {
				jQuery('#kanynomous-check').show();
				jQuery('#kanonymous').prop('checked', true);
			} else {
				jQuery('#kanynomous-check').hide();
				jQuery('#kanonymous').prop('checked', false);
			}
		}

		jQuery.ajax({
			type    : 'POST',
			url     : kurl_topicons_request,
			async   : false,
			dataType: 'json',
			data    : {catid: catid},
			success : function (data) {
				jQuery('#iconset_topic_list').remove();

				var div_object = jQuery('<div>', {'id': 'iconset_topic_list'});

				jQuery('#iconset_inject').append(div_object);

				jQuery.each(data, function (index, value) {
					if (value.type != 'system') {
						if (value.id == 0) {
							var input = jQuery('<input>', {
								type   : 'radio',
								id     : 'radio' + value.id,
								checked: 'checked',
								name   : 'topic_emoticon',
								value  : value.id
							});
						}
						else {
							var input = jQuery('<input>', {
								type : 'radio',
								id   : 'radio' + value.id,
								name : 'topic_emoticon',
								value: value.id
							});
						}

						var span_object = jQuery('<span>', {'class': 'kiconsel'}).append(input);
						var label = jQuery('<label>', {
							'class': 'radio inline',
							'for'  : 'radio' + value.id
						}).append(jQuery('<i>', {
							'class' : 'fa glyphicon-topic fa-2x fa-' + value.fa,
							'border': '0',
							'al'    : ''
						}));
						span_object.append(label);

						jQuery('#iconset_topic_list').append(span_object);
					}
				});
			}
		});
	});
});
