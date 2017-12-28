/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
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

jQuery(document).ready(function ($) {
	$('#tabs_kunena_editor a:first').tab('show');

	$('#tabs_kunena_editor a:last').click(function (e) {
		e.preventDefault();

		var preview = $("#kbbcode-preview");
		var message = $("#kbbcode-message");

		preview.css('display', 'block');

		message.hide();

		kPreviewHelper();

		preview.attr('class', 'kbbcode-preview-bottom controls');
		var height = message.css('height');
		preview.css('height', message.css('height'));
	});

	$('#tabs_kunena_editor a:not(:last)').click(function (e) {
		$('#kbbcode-preview').hide();
		$('#kbbcode-message').css('display', 'inline-block');
		$('#markItUpKbbcode-message').css('display', 'inline-block');
	});

	$('#tabs_kunena_editor a:last').click(function (e) {
		$('#kbbcode-message').hide();
		$('#markItUpKbbcode-message').hide();
	});

	/* To enabled emojis in kunena textera feature like on github */
	if ($('#kemojis_allowed').val()==1) {
		var item = '';
		if ($('#kbbcode-message').length > 0 && $('.qreply').length == 0) {
			item = '#kbbcode-message';
		} else if ($('.qreply').length > 0) {
			item = '.qreply';
		}

		if (item != undefined) {
			$(item).atwho({
				at              : ":",
				displayTpl      : "<li data-value='${key}'>${name} <img src='${url}' height='20' width='20' /></li>",
				insertTpl       : '${name}',
				callbacks: {
					remoteFilter: function (query, callback) {
						if (query.length > 0) {
							$.ajax({
								url    : $("#kurl_emojis").val(),
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
	if ($.fn.sisyphus != undefined) {
		$("#postform").sisyphus({
			locationBased: true,
			timeout      : 5
		});
	}

	$('#kshow_attach_form').click(function () {
		if ($('#kattach_form').is(":visible")) {
			$('#kattach_form').hide();
		}
		else {
			$('#kattach_form').show();
		}
	});

	$('.Kreplyclick').click(function () {
		var name = '#' + $(this).attr('data-related');
		if ($(name).is(":visible")) {
			$(name).hide();
		}
		else {
			$(name).show();
		}
	});

	$('.kreply-cancel').click(function () {
		$('.qreplyform').hide();
	});

	$('#form_submit_button').click(function () {
		$("#subject").attr('required', 'required');
		$("#kbbcode-message").attr('required', 'required');
	});

	// Load topic icons by ajax request
	$('#postcatid').change(function () {
		var catid = $('select#postcatid option').filter(':selected').val();
		var kurl_topicons_request = $('#kurl_topicons_request').val();

		if ($('#kanynomous-check').length > 0) {
			if (arrayanynomousbox[catid] !== undefined) {
				$('#kanynomous-check').show();
				$('#kanonymous').prop('checked', true);
			} else {
				$('#kanynomous-check').hide();
				$('#kanonymous').prop('checked', false);
			}
		}

		$.ajax({
			type    : 'POST',
			url     : kurl_topicons_request,
			async   : false,
			dataType: 'json',
			data    : {catid: catid},
			success : function (data) {
				$('#iconset_topic_list').remove();

				var div_object = $('<div>', {'id': 'iconset_topic_list'});

				$('#iconset_inject').append(div_object);

				$.each(data, function (index, value) {
					if (value.type != 'system') {
						if (value.id == 0) {
							var input = $('<input>', {
								type   : 'radio',
								id     : 'radio' + value.id,
								name   : 'topic_emoticon',
								value  : value.id
							}).prop('checked',true);
						}
						else {
							var input = $('<input>', {
								type : 'radio',
								id   : 'radio' + value.id,
								name : 'topic_emoticon',
								value: value.id
							});
						}

						var span_object = $('<span>', {'class': 'kiconsel'}).append(input);

						if (kunena_topicicontype=='B3') {
							var label = $('<label>', {
								'class': 'radio inline',
								'for'  : 'radio' + value.id
							}).append($('<span>', {
								'class': 'glyphicon glyphicon-topic glyphicon-' + value.b3,
								'border': '0',
								'al'    : ''
							}));
						} else if(kunena_topicicontype=='fa') {
							var label = $('<label>', {
								'class': 'radio inline',
								'for'  : 'radio' + value.id
							}).append($('<i>', {
								'class' : 'fa glyphicon-topic fa-2x fa-' + value.fa,
								'border': '0',
								'al'    : ''
							}));
						} else {
							var label = $('<label>', {
								'class': 'radio inline',
								'for'  : 'radio' + value.id
							}).append($('<img>', {'src': value.path, 'border': '0', 'al': ''}));
						}

						span_object.append(label);

						$('#iconset_topic_list').append(span_object);
					}
				});
			}
		});
	});

	if ($.fn.datepicker != undefined) {
		// Load datepicker for poll
		$('#datepoll-container .input-group.date').datepicker({
			orientation: "bottom auto"
		});
	}
});
