/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {
    const qreply = $('.qreply');
    const editor = $('#editor');
    const submitButton = $('#form_submit_button');

    let pollcategoriesid;

    if(Joomla.getOptions('com_kunena.pollcategoriesid') != undefined){
		pollcategoriesid = jQuery.parseJSON(Joomla.getOptions('com_kunena.pollcategoriesid'));
    }

    const pollexist = $('#poll_exist_edit');
    const pollcatid = jQuery('#poll_catid').val();
    const polliconstatus = false;
    // The poll of the edited topic may not be edited: the button only shows it, the save checks it again
    const pollEditRefused = Joomla.getOptions('com_kunena.pollEditRefused');

    // Check is anynomous options can be displayed on newtopic page
    const catiddefault = $('#postcatid').val();

    let arrayanynomousbox = null;

    if(Joomla.getOptions('com_kunena.arrayanynomousbox') != undefined){
		arrayanynomousbox = jQuery.parseJSON(Joomla.getOptions('com_kunena.arrayanynomousbox'));

		if (arrayanynomousbox !== null) {
            if (arrayanynomousbox[catiddefault] == 1) {
                $('#kanonymous').prop('checked', true);
            }
        }
    }

    $('#reset').on('click', function () {
        localStorage.removeItem('copyKunenaeditor');
    });

    /* To enabled emojis in kunena textera feature like on github */
    if ($('#kemojis_allowed').val() == 1) {
        let item = '';
        if (editor.length > 0 && qreply.length == 0) {
            item = '#editor';
        } else if (qreply.length > 0) {
            item = '.qreply';
        }
    }

    /* "Recover the form" of the template: what is written in the form is kept in the browser until it is sent (form-recover.js) */
    if (window.KunenaFormRecover !== undefined) {
        window.KunenaFormRecover.protect(document.getElementById('postform'), {timeout: 5});
    }

    $('#kshow_attach_form').click(function () {
        $('#kattach_form').toggle();
    });

    submitButton.click(function () {
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
        const catid = $('#postcatid').val();
        const kurl_topicons_request = $('#kurl_topicons_request').val();
        const pollcategoriesidOption = Joomla.getOptions('com_kunena.pollcategoriesid');
        const pollcategoriesid = pollcategoriesidOption !== undefined ? jQuery.parseJSON(pollcategoriesidOption) : undefined;
        const pollexist = jQuery('#poll_exist_edit');
        const pollcatid = jQuery('#poll_catid').val();
        const polliconstatus = true;

        if (pollEditRefused !== undefined) {
            if (Joomla.getOptions('com_kunena.ckeditor_config') !== undefined) {
                window.KunenaEditor.get('message')?.setCommandEnabled('polls', false);
            }
        } else if (typeof pollcategoriesid !== 'undefined' && pollcategoriesid !== null && pollexist.length === 0) {
            if (pollcatid !== undefined) {
                const catid = jQuery('#kcategory_poll').val();
            }

            if (pollcategoriesid[catid] !== undefined) {
                if (Joomla.getOptions('com_kunena.ckeditor_config') !== undefined) {
                    window.KunenaEditor.get('message')?.setCommandEnabled('polls', true);
                }

            } else {
				if (Joomla.getOptions('com_kunena.ckeditor_config') !== undefined) {
                    window.KunenaEditor.get('message')?.setCommandEnabled('polls', false);
                }

            }
        } else if (pollexist.length > 0) {
			if (Joomla.getOptions('com_kunena.ckeditor_config') !== undefined) {
                window.KunenaEditor.get('message')?.setCommandEnabled('polls', true);
            }

        } else {
			if (Joomla.getOptions('com_kunena.ckeditor_config') !== undefined) {
                window.KunenaEditor.get('message')?.setCommandEnabled('polls', false);
            }

        }

        if ($('#kanynomous-check').length > 0 && arrayanynomousbox !== null) {
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
    // Keep the chosen icon when the icons of the new category have it
    const checked = $('input[name="topic_emoticon"]:checked').val();

    $('#iconset_topicList').remove();

    const div_object = $('<div>', {'id': 'iconset_topicList'});
    $('#iconset_inject').append(div_object);

    const icontype = Joomla.getOptions('com_kunena.kunena_topicicontype');
    const iconLabels = Joomla.getOptions('com_kunena.topicIconLabels');

    // The same markup as the layout of the form
    $.each(data, function (index, value) {
        const input = $('<input>', {
            type: 'radio',
            id: 'radio' + value.id,
            name: 'topic_emoticon',
            value: value.id
        });

        const label = $('<label>', {
            'class': 'radio inline',
            'for': 'radio' + value.id
        });

        if (icontype === 'svg') {
            label.append($(value.svg));
        } else if (icontype === 'fa') {
            label.append($('<i>', {'class': 'fa fa-' + value.fa + ' glyphicon-topic fa-2x'}));
        } else {
            label.append($('<img>', {'loading': 'lazy', 'src': value.path, 'alt': value.name, 'style': 'border:0;'}));
        }

        if (iconLabels) {
            label.append($('<span>', {'class': 'd-block small text-center'}).text(value.label));
        }

        // A space between the icons, as between the lines of the layout
        if (index > 0) {
            div_object.append(document.createTextNode(' '));
        }

        div_object.append($('<label>', {'for': 'radio' + value.id}), input, label);
    });

    const radios = div_object.find('input[name="topic_emoticon"]');
    const previous = radios.filter(function () {
        return this.value === checked;
    });

    (previous.length ? previous : radios.first()).prop('checked', true);
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
                    if (Joomla.getOptions('com_kunena.ckeditor_config') !== undefined) {
                        const editor_text = (window.KunenaEditor.get('message')?.getData() ?? '');

                        if (editor_text.length > 1) {
                            if (editor_text.length > 1) {
                                $('#modal_confirm_template_category').modal('show');
                            } else {
                                window.KunenaEditor.get('message')?.setData(category_template_text);
                            }
                        } else {
                            if (data.length > 1) {
                                $('#modal_confirm_template_category').modal('show');
                            } else {
                                window.KunenaEditor.get('message')?.setData(data.data);
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
        const textarea = $("#editor").next();
        textarea.empty();
        window.KunenaEditor.get('message')?.setData(category_template_text?.responseJSON?.data);
    });

    $('#modal_confirm_erase_keep_old').click(function () {
        $('#modal_confirm_template_category').modal('hide');
        const existing_content = (window.KunenaEditor.get('message')?.getData() ?? '');
        const textarea = $("#editor").next();
        textarea.empty();
        window.KunenaEditor.get('message')?.setData(category_template_text?.responseJSON?.data + ' ' + existing_content);
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
        // The submit button: enabled when one of the editors has a text.
        const updateSubmit = function () {
            const length = ['message', 'message_private'].reduce(function (total, id) {
                const instance = window.KunenaEditor.get(id);

                return total + (instance ? instance.getData().length : 0);
            }, 0);

            if (length > 0) {
                submitButton.removeAttr('disabled');
            } else if (submitButton.disabled === undefined) {
                submitButton.prop('disabled', true);
            }
        };

        // The poll button: only in a category with polls, and not when the poll cannot be changed any more.
        const updatePollButton = function (instance) {
            if (pollEditRefused !== undefined) {
                instance.setCommandEnabled('polls', false);
                $(instance.editor.ui.view.element).find('[data-kunena-button="polls"]').attr({'title': pollEditRefused, 'aria-describedby': 'poll-edit-refused'});
            } else if (pollcatid !== undefined && typeof pollcategoriesid !== 'undefined' && pollcategoriesid !== null && pollexist.length === 0) {
                instance.setCommandEnabled('polls', pollcategoriesid[$('#kcategory_poll').val()] !== undefined);
            } else {
                instance.setCommandEnabled('polls', pollexist.length > 0);
            }
        };

        // A text which was copied from the quick reply to the full editor.
        const restoreCopy = function (instance) {
            const copy = localStorage.getItem('copyKunenaeditor');

            if (copy) {
                instance.setData(copy);
                localStorage.removeItem('copyKunenaeditor');
            }
        };

        window.KunenaEditor.create('message', {mentions: dataFeed}).then(function (instance) {
            restoreCopy(instance);
            updatePollButton(instance);
            updateSubmit();
            instance.on('change', updateSubmit);
        });

        if (document.getElementById('message_private')) {
            window.KunenaEditor.create('message_private').then(function (instance) {
                if (Joomla.getOptions('com_kunena.localstorage')) {
                    restoreCopy(instance);
                }

                instance.setCommandEnabled('polls', false);
                updateSubmit();
                instance.on('change', updateSubmit);
            });
        }
    }

// The users which can be mentioned: a promise of the list (the editor asks for it while @name is typed).
function dataFeed(query) {
    let url = Joomla.getOptions('com_kunena.kunena_url_topic_get_mentions');

    // A new topic has no users yet: the users are searched with the typed text, for the category.
    if (Joomla.getOptions('com_kunena.mentions_search')) {
        const catid = jQuery('#postcatid').val() || jQuery('#poll_catid').val() || '';

        url += '&catid=' + encodeURIComponent(catid) + '&q=' + encodeURIComponent(query || '');
    }

    return new Promise(function (resolve) {
        $.getJSON(url)
            .done(function (json) {
                const users = JSON.parse(json) || [];
                const text = String(query || '').toLowerCase();

                resolve(users.filter(function (user) {
                    return String(user.username || '').toLowerCase().indexOf(text) !== -1;
                }).slice(0, 10));
            })
            .fail(function (jqxhr, textStatus, error) {
                console.error('Request Failed: ' + textStatus + ', ' + error);
                resolve([]);
            });
    });
}
});
