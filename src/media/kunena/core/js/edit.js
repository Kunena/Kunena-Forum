/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

/**
 *  Helper function for to perform JSON request for preview
 */
var previewActive = false;

function kPreviewHelper(previewActive) {
	var editor = jQuery('#editor');
	if (editor.val() !== null) {
		jQuery.ajax({
			type: 'POST',
			url: jQuery('#kpreview_url').val(),
			async: true,
			dataType: 'json',
			data: {body: editor.val()}
		})
			.done(function (data) {
				jQuery('#kbbcode-preview').html(data.preview);
			})
			.fail(function () {
				//TODO: handle the error of ajax request
			});
	}
}

jQuery(document).ready(function ($) {
	var qreply = $('.qreply');
	var editor = $('#editor');
	var pollcategoriesid = jQuery.parseJSON(Joomla.getOptions('com_kunena.pollcategoriesid'));
	var arrayanynomousbox = jQuery.parseJSON(Joomla.getOptions('com_kunena.arrayanynomousbox'));
	var pollexist = $('#poll_exist_edit');
	var pollcatid = jQuery('#poll_catid').val();
	var polliconstatus = false;

	$('#tabs_kunena_editor a:first').tab('show');

	$('#tabs_kunena_editor a:last').click(function (e) {
		e.preventDefault();

		var preview = $("#kbbcode-preview");
		var message = $("#editor");

		preview.css('display', 'block');

		message.hide();

		kPreviewHelper();

		preview.attr('class', 'kbbcode-preview-bottom controls');
		var height = message.css('height');
		preview.css('height', height);
	});

	$('#tabs_kunena_editor a:not(:last)').click(function (e) {
		$('#kbbcode-preview').hide();
		editor.css('display', 'inline-block');
	});

	$('#tabs_kunena_editor a:last').click(function (e) {
		editor.hide();
	});

	// Check is anynomous options can be displayed on newtopic page
	var catiddefault = $('#postcatid').val();

	if (arrayanynomousbox !== null)
	{
		if(arrayanynomousbox[catiddefault]==1)
		{
			$('#kanonymous').prop('checked', true);
		}
	}

	if ($("#editor").length > 0)
	{
		var datalocalstorage = localStorage.getItem('copyKunenaeditor');
		if (datalocalstorage) {
			var textarea = $("#editor").next();
			textarea.empty();
			$('#editor').val(datalocalstorage);
			localStorage.removeItem('copyKunenaeditor');
		}
	}

	$('#reset').onclick = function() {
		localStorage.removeItem('copyKunenaeditor');
	};

	/* To enabled emojis in kunena textera feature like on github */
	if ($('#kemojis_allowed').val() == 1) {
		var item = '';
		if (editor.length > 0 && qreply.length == 0) {
			item = '#editor';
		}
		else if (qreply.length > 0) {
			item = '.qreply';
		}

		if (item != undefined) {
			$(item).atwho({
				at: ":",
				displayTpl: "<li data-value='${key}'>${name} <img src='${url}' height='20' width='20' /></li>",
				insertTpl: '${name}',
				callbacks: {
					remoteFilter: function (query, callback) {
						if (query.length > 0) {
							$.ajax({
								url: $("#kurl_emojis").val(),
								data: {
									search: query
								}
							})
								.done(function (data) {
									callback(data.emojis);
								})
								.fail(function () {
									//TODO: handle the error of ajax request
								});
						}
					}
				}
			});
		}
	}

	if (item !== undefined) {
		var users_url = $('#kurl_users').val();
		$(item).atwho({
			at: "@",
			data: users_url,
			limit: 5
		});
	}


	/* Store form data into localstorage every 1 second */
	if ($.fn.sisyphus !== undefined) {
		$("#postform").sisyphus({
			locationBased: true,
			timeout: 5
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

	$('#form_submit_button').click(function () {
		$("#subject").attr('required', 'required');
		$("#editor").attr('required', 'required');
		localStorage.removeItem('copyKunenaeditor');
	});

	// Needed to open and close quickreply from on blue Eagle5
	$('.Kreplyclick').click(function () {
		var name = '#' + $(this).attr('data-related');
		if ($(name).is(":visible")) {
			$(name).hide();
		} else {
			$(name).show();
		}
	});

	$('.kreply-cancel').click(function () {
		var name = '#' + $(this).attr('data-related');
		$(name).hide();
	});

	var category_template_text;
	$('#postcatid').change(function () {
		var catid = $('select#postcatid option').filter(':selected').val();
		var kurl_topicons_request = $('#kurl_topicons_request').val();
		var pollcategoriesid = jQuery.parseJSON(Joomla.getOptions('com_kunena.pollcategoriesid'));
		var pollexist = jQuery('#poll_exist_edit');
		var pollcatid = jQuery('#poll_catid').val();
		polliconstatus = true;

		if (typeof pollcategoriesid !== 'undefined' && pollcategoriesid !== null && pollexist.length === 0) 
		{
			if(pollcatid !== undefined)
			{
				var catid = jQuery('#kcategory_poll').val();
			}

			if (pollcategoriesid[catid] !== undefined) {
				if( typeof(CKEDITOR) !== "undefined" )
				{
					CKEDITOR.instances.message.getCommand( 'polls' ).enable();
				}
			}
			else {
				if( typeof(CKEDITOR) !== "undefined" )
				{
					CKEDITOR.instances.message.getCommand( 'polls' ).disable();
				}
			}
		}
		else if (pollexist.length > 0) {
			if( typeof(CKEDITOR) !== "undefined" )
			{
				CKEDITOR.instances.message.getCommand( 'polls' ).enable();
			}
		}
		else {
			if( typeof(CKEDITOR) !== "undefined" )
			{
				CKEDITOR.instances.message.getCommand( 'polls' ).disable();
			}
		}

		if ($('#kanynomous-check').length > 0) {
			if (arrayanynomousbox[catid] !== undefined) {
				$('#kanynomous-check').show();
				$('#kanonymous').prop('checked', true);
			}
			else {
				$('#kanynomous-check').hide();
				$('#kanonymous').prop('checked', false);
			}
		}

		// Load topic icons by ajax request
		$.ajax({
			type: 'POST',
			url: kurl_topicons_request,
			async: true,
			dataType: 'json',
			data: {catid: catid}
		})
			.done(function (data) {
				$('#iconset_topic_list').remove();

				var div_object = $('<div>', {'id': 'iconset_topic_list'});

				$('#iconset_inject').append(div_object);

				$.each(data, function (index, value) {
					if (value.type !== 'system') {
						if (value.id === 0) {
							var input = $('<input>', {
								type: 'radio',
								id: 'radio' + value.id,
								name: 'topic_emoticon',
								value: value.id
							}).prop('checked', true);
						}
						else {
							var input = $('<input>', {
								type: 'radio',
								id: 'radio' + value.id,
								name: 'topic_emoticon',
								value: value.id
							});
						}

						var span_object = $('<span>', {'class': 'kiconsel'}).append(input);

						if (Joomla.getOptions('com_kunena.kunena_topicicontype') === 'B2') {
							var label = $('<label>', {
								'class': 'radio inline',
								'for': 'radio' + value.id
							}).append($('<span>', {
								'class': 'icon icon-topic icon-' + value.b2,
								'border': '0',
								'al': ''
							}));
						}
						else if (Joomla.getOptions('com_kunena.kunena_topicicontype') === 'B3') {
							var label = $('<label>', {
								'class': 'radio inline',
								'for': 'radio' + value.id
							}).append($('<span>', {
								'class': 'glyphicon glyphicon-topic glyphicon-' + value.b3,
								'border': '0',
								'al': ''
							}));
						}
						else if (Joomla.getOptions('com_kunena.kunena_topicicontype') === 'B4') {
							var label = $('<label>', {
								'class': 'radio inline',
								'for': 'radio' + value.id
							}).append($('<img>', {
								'src': '/media/kunena/topic_icons/default/user/svg/'+value.b4,
								'alt': value.name,
								'height': '32',
								'width': '32'
							}));
						}
						else if (Joomla.getOptions('com_kunena.kunena_topicicontype') === 'fa') {
							var label = $('<label>', {
								'class': 'radio inline',
								'for': 'radio' + value.id
							}).append($('<i>', {
								'class': 'fa glyphicon-topic fa-2x fa-' + value.fa,
								'border': '0',
								'al': ''
							}));
						}
						else {
							var label = $('<label>', {
								'class': 'radio inline',
								'for': 'radio' + value.id
							}).append($('<img>', {'src': value.path, 'border': '0', 'al': ''}));
						}

						span_object.append(label);

						$('#iconset_topic_list').append(span_object);
					}
				});
			})
			.fail(function () {
				//TODO: handle the error of ajax request
			});

		// Load template text for the category by ajax request
		category_template_text = function cat_template_text() {
			return $.ajax({
				type: 'POST',
				url: $('#kurl_category_template_text').val(),
				async: true,
				dataType: 'json',
				data: {catid: catid}
			})
				.done(function (data) {
					var editor_text = '';

					if( typeof(CKEDITOR) !== "undefined" )
					{
						editor_text = CKEDITOR.instances.message.getData();
					}
					else
					{
						editor_text = $('#editor').val();
					}

					if (editor_text.length > 1) {
						if (editor_text.length > 1) {
							$('#modal_confirm_template_category').modal('show');
						}
						else {
							if( typeof(CKEDITOR) !== "undefined" )
							{
								CKEDITOR.instances.message.setData(category_template_text);
							}
						}
					}
					else {
						if (data.length > 1) {
							$('#modal_confirm_template_category').modal('show');
						}
						else {
							if( typeof(CKEDITOR) !== "undefined" )
							{
								CKEDITOR.instances.message.setData(data);
							}
						}
					}

				})
				.fail(function () {
					//TODO: handle the error of ajax request
				});
		}();
	});

	$('#modal_confirm_erase').click(function () {
		$('#modal_confirm_template_category').modal('hide');
		var textarea = $("#editor").next();
		textarea.empty();

		if( typeof(CKEDITOR) !== "undefined" )
		{
			CKEDITOR.instances.message.setData(category_template_text.responseJSON);
		}
		else
		{
			$('#editor').val(category_template_text.responseJSON);
		}
	});

	$('#modal_confirm_erase_keep_old').click(function () {
		$('#modal_confirm_template_category').modal('hide');
		var existing_content = CKEDITOR.instances.message.getData();
		var textarea = $("#editor").next();
		textarea.empty();

		if( typeof(CKEDITOR) !== "undefined" )
		{
			CKEDITOR.instances.message.setData(category_template_text.responseJSON + ' ' + existing_content);
		}
		else
		{
			$('#editor').val(category_template_text.responseJSON + ' ' + existing_content);
		}
	});

	if ($.fn.datepicker !== undefined) {
		// Load datepicker for poll
		$('#datepoll-container .input-append.date').datepicker({
			orientation: "top auto"
		});
	}

	$('.gotoeditor').click(function () {
		var texteareaId = $(this).attr('id');
		localStorage.setItem("copyKunenaeditor", $('.' + texteareaId).val());
	});

	$('#gotoeditor').click(function () {
		localStorage.setItem("copyKunenaeditor", $('#editor').val());
	});

	if (Joomla.getOptions('com_kunena.ckeditor_config')!== undefined)
	{
		CKEDITOR.replace( 'message', {
			customConfig: Joomla.getOptions('com_kunena.ckeditor_config'),
			on: {
				instanceReady: function(event) {
					event.editor.on("beforeCommandExec", function(event) {
						// Show the paste dialog for the paste buttons and right-click paste
						if (event.data.name == "paste") {
							event.editor._.forcePasteDialog = true;
						}
	
						// Don't show the paste dialog for Ctrl+Shift+V
						if (event.data.name == "pastetext" && event.data.commandData.from == "keystrokeHandler") {
							event.cancel();
						}
					})
				},
				mode: function( evt ) {
					var cat = localStorage.getItem('copyKunenaeditor');

					if (cat) {
						evt.editor.setData(cat);
						localStorage.removeItem('copyKunenaeditor');
					}

					if(polliconstatus === false)
					{
						if(pollcatid !== undefined)
						{
							if (typeof pollcategoriesid !== 'undefined' && pollcategoriesid !== null && pollexist.length === 0) {
								var catid = $('#kcategory_poll').val();

								if (pollcategoriesid[catid] !== undefined) {
									evt.editor.getCommand( 'polls' ).enable();
								}
								else {
									evt.editor.getCommand( 'polls' ).disable();
								}
							}
							else if (pollexist.length > 0) {
								evt.editor.getCommand( 'polls' ).enable();
							}
							else {
								evt.editor.getCommand( 'polls' ).disable();
							}
						}
						else
						{
							evt.editor.getCommand( 'polls' ).disable();
						}
					}
				}
			}
		});

		CKEDITOR.on( 'dialogDefinition', function( ev )
		{
			var dialogName = ev.data.name;
			var dialogDefinition = ev.data.definition;

			if(dialogName=='link')
			{
				var linkType = dialogDefinition.getContents('info').get('linkType');
				// Remove the 'anchor' link option.	 
				linkType.items.splice(1,1);
				// Remove the 'phone' link option.	 
				linkType.items.splice(2,2);

				var protocol = dialogDefinition.getContents('info').get('protocol');
				protocol.items.splice(2,4);
			}
		});
		
		
	}
});
