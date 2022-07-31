/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
    const qreply = $('.qreply');
    const editor = $('#editor');

    if(Joomla.getOptions('com_kunena.pollcategoriesid') != undefined){
		const pollcategoriesid = jQuery.parseJSON(Joomla.getOptions('com_kunena.pollcategoriesid'));
    }

    const pollexist = $('#poll_exist_edit');
    const pollcatid = jQuery('#poll_catid').val();
    const polliconstatus = false;

    // Check is anynomous options can be displayed on newtopic page
    const catiddefault = $('#postcatid').val();

    if(Joomla.getOptions('com_kunena.arrayanynomousbox') != undefined){
		const arrayanynomousbox = jQuery.parseJSON(Joomla.getOptions('com_kunena.arrayanynomousbox'));

		if (arrayanynomousbox !== null) {
            if (arrayanynomousbox[catiddefault] == 1) {
                $('#kanonymous').prop('checked', true);
            }
        }
    }

    $('#reset').onclick = function () {
        localStorage.removeItem('copyKunenaeditor');
    };

    /* To enabled emojis in kunena textera feature like on github */
    if ($('#kemojis_allowed').val() == 1) {
        let item = '';
        if (editor.length > 0 && qreply.length == 0) {
            item = '#editor';
        } else if (qreply.length > 0) {
            item = '.qreply';
        }

        if (item != undefined) {
            /*$(item).atwho({
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
            });*/
        }
    }

    /*if (item !== undefined) {
        const users_url = $('#kurl_users').val();
        $(item).atwho({
            at: "@",
            data: users_url,
            limit: 5
        });
    }*/


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
        } else {
            $('#kattach_form').show();
        }
    });

    $('#form_submit_button').click(function () {
        $("#subject").attr('required', 'required');
        $("#editor").attr('required', 'required');
        localStorage.removeItem('copyKunenaeditor');
    });

    // Needed to open and close quickreply from template
    $('.Kreplyclick').click(function () {
        const name = '#' + $(this).attr('data-related');
        if ($(name).is(":visible")) {
            $(name).hide();
        } else {
            $(name).show();
        }
    });

    $('.kreply-cancel').click(function () {
        const name = '#' + $(this).attr('data-related');
        $(name).hide();
    });

    let category_template_text = null;
    $('#postcatid').change(function () {
        const catid = $('select#postcatid option').filter(':selected').val();
        const kurl_topicons_request = $('#kurl_topicons_request').val();
        const pollcategoriesid = jQuery.parseJSON(Joomla.getOptions('com_kunena.pollcategoriesid'));
        const pollexist = jQuery('#poll_exist_edit');
        const pollcatid = jQuery('#poll_catid').val();
        const polliconstatus = true;

        if (typeof pollcategoriesid !== 'undefined' && pollcategoriesid !== null && pollexist.length === 0) {
            if (pollcatid !== undefined) {
                const catid = jQuery('#kcategory_poll').val();
            }

            if (pollcategoriesid[catid] !== undefined) {
                CKEDITOR.instances.message.getCommand('polls').enable();

            } else {
                CKEDITOR.instances.message.getCommand('polls').disable();

            }
        } else if (pollexist.length > 0) {
            CKEDITOR.instances.message.getCommand('polls').enable();

        } else {
            CKEDITOR.instances.message.getCommand('polls').disable();

        }

        if ($('#kanynomous-check').length > 0) {
            if (arrayanynomousbox[catid] !== undefined) {
                $('#kanynomous-check').show();
                $('#kanonymous').prop('checked', true);
            } else {
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

                const div_object = $('<div>', {'id': 'iconset_topic_list'});

                $('#iconset_inject').append(div_object);

                $.each(data, function (index, value) {
                    if (value.type !== 'system') {
                        if (value.id === 0) {
                            const input = $('<input>', {
                                type: 'radio',
                                id: 'radio' + value.id,
                                name: 'topic_emoticon',
                                value: value.id
                            }).prop('checked', true);
                        } else {
                            const input = $('<input>', {
                                type: 'radio',
                                id: 'radio' + value.id,
                                name: 'topic_emoticon',
                                value: value.id
                            });
                        }

                        const span_object = $('<span>', {'class': 'kiconsel'}).append(input);

                        if (Joomla.getOptions('com_kunena.kunena_topicicontype') === 'B2') {
                            const label = $('<label>', {
                                'class': 'radio inline',
                                'for': 'radio' + value.id
                            }).append($('<span>', {
                                'class': 'icon icon-topic icon-' + value.b2,
                                'border': '0',
                                'al': ''
                            }));
                        } else if (Joomla.getOptions('com_kunena.kunena_topicicontype') === 'fa') {
                            const label = $('<label>', {
                                'class': 'radio inline',
                                'for': 'radio' + value.id
                            }).append($('<i>', {
                                'class': 'fa glyphicon-topic fa-2x fa-' + value.fa,
                                'border': '0',
                                'al': ''
                            }));
                        } else {
                            const label = $('<label>', {
                                'class': 'radio inline',
                                'for': 'radio' + value.id
                            }).append($('<img alt="">', {'src': value.path, 'border': '0', 'al': ''}));
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
                    const editor_text = CKEDITOR.instances.message.getData();

                    if (editor_text.length > 1) {
                        if (editor_text.length > 1) {
                            $('#modal_confirm_template_category').modal('show');
                        } else {
                            CKEDITOR.instances.message.setData(category_template_text);
                        }
                    } else {
                        if (data.length > 1) {
                            $('#modal_confirm_template_category').modal('show');
                        } else {
                            CKEDITOR.instances.message.setData(data);
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
        const textarea = $("#editor").next();
        textarea.empty();
        CKEDITOR.instances.message.setData(category_template_text.responseJSON);
    });

    $('#modal_confirm_erase_keep_old').click(function () {
        $('#modal_confirm_template_category').modal('hide');
        const existing_content = CKEDITOR.instances.message.getData();
        const textarea = $("#editor").next();
        textarea.empty();
        CKEDITOR.instances.message.setData(category_template_text.responseJSON + ' ' + existing_content);
    });

    if ($.fn.datepicker !== undefined) {
        // Load datepicker for poll
        $('#datepoll-container .input-append.date').datepicker({
            orientation: "top auto"
        });
    }

	$('#gotoeditor').click(function () {
		localStorage.setItem("copyKunenaeditor", $('#editor').val());
	});

    if (Joomla.getOptions('com_kunena.ckeditor_config') !== undefined) {
        CKEDITOR.replace('message', {
			customConfig: Joomla.getOptions('com_kunena.ckeditor_config'),
			on: {
				instanceReady: function (event) {
					CKEDITOR.plugins.clipboard.preventDefaultDropOnElement(event.editor.document);
					
					if (event.editor.getData().length > 0)
					{
						$('#form_submit_button').removeAttr("disabled");
					}

					event.editor.on("beforeCommandExec", function (event) {
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
				change : function (event) {
					if (event.editor.getData().length > 0)
					{
						$('#form_submit_button').removeAttr("disabled");
					}

					if (event.editor.getData().length == 0 && $('#form_submit_button').disabled === undefined)
					{
						$('#form_submit_button').prop("disabled", true);
					}

				},
                mode: function (evt) {
                    const cat = localStorage.getItem('copyKunenaeditor');

                    if (cat) {
                        evt.editor.setData(cat);
                        localStorage.removeItem('copyKunenaeditor');
                    }

                    if (polliconstatus === false) {
                        if (pollcatid !== undefined) {
                            if (typeof pollcategoriesid !== 'undefined' && pollcategoriesid !== null && pollexist.length === 0) {
                                const catid = $('#kcategory_poll').val();

                                if (pollcategoriesid[catid] !== undefined) {
                                    evt.editor.getCommand('polls').enable();
                                } else {
                                    evt.editor.getCommand('polls').disable();
                                }
                            } else if (pollexist.length > 0) {
                                evt.editor.getCommand('polls').enable();
                            } else {
                                evt.editor.getCommand('polls').disable();
                            }
                        } else {
                            evt.editor.getCommand('polls').disable();
                        }
                    }
                }
            }
        });

        CKEDITOR.on('dialogDefinition', function (ev) {
            const dialogName = ev.data.name;
            const dialogDefinition = ev.data.definition;

            if (dialogName == 'link') {
                const linkType = dialogDefinition.getContents('info').get('linkType');
                // Remove the 'anchor' link option.
                linkType.items.splice(1, 1);
                // Remove the 'phone' link option.
                linkType.items.splice(2, 2);

                const protocol = dialogDefinition.getContents('info').get('protocol');
                protocol.items.splice(2, 4);
            }
        });
    }
});