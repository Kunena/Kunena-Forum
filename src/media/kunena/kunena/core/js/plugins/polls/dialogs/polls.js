/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.dialog.add( 'pollsDialog', function( editor ) {
	var options = null;
	var nboptionsmax = jQuery('#nb_options_allowed').val();

	function createNewOptionField(optionText, optionId, isNew) {
		options++;
		var paragraph = new CKEDITOR.dom.element( 'p' );
		paragraph.setStyle( 'margin-top', '5px' );
        var checkbox = new CKEDITOR.dom.element('input');
        checkbox.setAttribute('type', 'checkbox');
        checkbox.addClass('polloptioncheck');
        if (optionId !== undefined) {
            checkbox.setAttribute('id', 'polloptioncheck' + optionId);
            checkbox.setAttribute('name', 'polloptioncheck[' + optionId + ']');
        } else {
            checkbox.setAttribute('id', 'polloptioncheck' + options);
            checkbox.setAttribute('name', 'polloptioncheck[' + options + ']');
        }
        paragraph.append(checkbox);
		var label = new CKEDITOR.dom.element( 'label' );
		label.appendText(Joomla.Text._('COM_KUNENA_POLL_OPTION_NAME')+ ' ' + options + ' ');
        if (optionId !== undefined) {
            label.setAttribute('id', 'labeloption' + optionId);
        } else {
            label.setAttribute('id', 'labeloption' + options);
        }
		paragraph.append( label );
		var br = new CKEDITOR.dom.element( 'br' );
		paragraph.append( br);
		var inputField = new CKEDITOR.dom.element( 'input' );
		inputField.addClass( 'kunenackeditorpolloption' );
		inputField.addClass( 'cke_dialog_ui_input_text' );
        if (optionId !== undefined && (isNew === undefined || !isNew)) {
            inputField.setAttribute('id', 'field_option' + optionId);
            inputField.setAttribute('name', 'polloptionsID[' + optionId + ']');
        } else if (optionId && isNew) {
            inputField.setAttribute('id', 'field_option' + optionId);
            inputField.setAttribute('name', 'polloptionsID[new' + optionId + ']');
        } else {
            inputField.setAttribute('id', 'field_option' + options);
            inputField.setAttribute('name', 'polloptionsID[new' + options + ']');
        }
		inputField.setAttribute('type', 'text');
		inputField.setAttribute('maxLength', 100);
		if(optionText!==undefined)
		{
			inputField.setAttribute('value', optionText);
		}
		paragraph.append( inputField );

		CKEDITOR.document.getById( 'dynamicContent' ).append( paragraph );
	}

	return {
		title: Joomla.Text._('COM_KUNENA_EDITOR_DIALOG_POLLS_PROPERTIES'),
		minWidth: 400,
		minHeight: 200,
		contents: 
		[
			{
				id: 'tab-basic',
				label: Joomla.Text._('COM_KUNENA_EDITOR_DIALOG_BASIC_SETTINGS'),
				elements: 
				[
					{
						type: 'text',
						id: 'polltitle',
						label: Joomla.Text._('COM_KUNENA_POLL_TITLE'),
						default: ''
					},
					{
						type: 'button',
						id: 'polladdoption',
						label: Joomla.Text._('COM_KUNENA_POLL_ADD_POLL_OPTION'),
						title: Joomla.Text._('COM_KUNENA_POLL_ADD_POLL_OPTION'),
						onClick: function() {
							// this = CKEDITOR.ui.dialog.button
							if (!nboptionsmax || (options < nboptionsmax && options >= 2)) {
								createNewOptionField();
							}
							else if (!nboptionsmax || options < 2) {
								createNewOptionField();
								createNewOptionField();
							}
							else {
								console.log('max options reach ');
							}
                            if (options >= nboptionsmax) {
                                this.disable();
                            }
						}
					},
					{
						type: 'button',
						id: 'pollremoveoption',
						label: Joomla.Text._('COM_KUNENA_POLL_REMOVE_POLL_OPTION'),
						title: Joomla.Text._('COM_KUNENA_POLL_REMOVE_POLL_OPTION'),
						onClick: function() {
                            jQuery('.polloptioncheck:checked').each(function () {
                                var optionName = jQuery(this).attr('name');
                                var optionNew = optionName.match(/\[(new)\d+\]/);
                                var optionId;
                                if (optionNew) {
                                    optionId = optionName.match(/\[new(\d+)\]/)[1];
                                } else {
                                    optionId = optionName.match(/\[(\d+)\]/)[1];
                                }
                                jQuery('#field_option' + optionId).closest('p').remove();
                                if (options > 0) {
                                    options--;
                                }
                            });
                            if (options < nboptionsmax) {
                                var button = this._.dialog.getContentElement('tab-basic', 'polladdoption');
                                button.enable();
                            }
                            counter = 0;
                            jQuery('#dynamicContent p').each(function () {
                                counter++;
                                var childInput = jQuery(this).children('.kunenackeditorpolloption').first();
                                var childlabel = jQuery(this).children('label').first();
                                childlabel.text(Joomla.Text._('COM_KUNENA_POLL_OPTION_NAME') + ' ' + counter + ' ');
                                childlabel.attr('id', 'labeloption' + counter);
                                var optionName = childInput.attr('name');
                                var optionNew = optionName.match(/\[(new)\d+\]/);
                                if (optionNew) {
                                    var optionId = optionName.match(/\[new(\d+)\]/)[1];
                                    if (optionId != counter) {
                                        var polloptioncheck = jQuery(this).children('.polloptioncheck').first();
                                        polloptioncheck.attr('id', 'polloptioncheck' + counter);
                                        polloptioncheck.attr('name', 'polloptioncheck[' + counter + ']');
                                        childInput.attr('id', 'field_option' + counter);
                                        childInput.attr('name', 'polloptionsID[new' + counter + ']');
                                    }
                                } else {

                                }
                            });
						}
					},
					{
						type: 'text',
						id: 'polllifespan',
						label: Joomla.Text._('COM_KUNENA_POLL_TIME_TO_LIVE'),
						default: '',
						onShow: function (data) {
							// Set the width of the outer div (otherwise it's affected by the CK CSS classes and is too wide)
							jQuery('#' + this.domId).css('width', 230);
							// Get the input element
							var theInput = jQuery('#' + this.domId).find('input');
							// Apply the datepicker to the input control
							jQuery(theInput.selector).datepicker({
								showButtonPanel: true,
								format: "yyyy-mm-dd",
                                todayHighlight: true,
                                autoclose: true
							});
						},
					},
					// Add HTML container for dynamic content
					{
						id : 'divdynamiccontent',
						type: 'html',
						html: '<div id="dynamicContent"></div>',
						setup: function(selectedTable) {

						},
						commit: function(data) {

						}
					}
				]
			}
		],
		onOk: function() {
            jQuery( '#poll_options' ).empty();
			var inputTitlePoll = new CKEDITOR.dom.element( 'input' );
			inputTitlePoll.setAttribute('type', 'hidden');
			inputTitlePoll.setAttribute('name', 'poll_title' );
			inputTitlePoll.setAttribute('value', this.getValueOf( 'tab-basic', 'polltitle' ) );
			CKEDITOR.document.getById( 'poll_options' ).append( inputTitlePoll );

			var inputPollTTL = new CKEDITOR.dom.element( 'input' );
			inputPollTTL.setAttribute('type', 'hidden');
			inputPollTTL.setAttribute('name', 'poll_time_to_live' );
			inputPollTTL.setAttribute('value', this.getValueOf( 'tab-basic', 'polllifespan' ) );
			CKEDITOR.document.getById( 'poll_options' ).append( inputPollTTL );

            jQuery('.kunenackeditorpolloption').each(function (index) {
                index++
                var optionName = jQuery(this).attr('name');
                var optionNew = optionName.match(/\[(new)\d+\]/);
                if (optionNew) {
                    var optionId = optionName.match(/\[new(\d+)\]/)[1];
                } else {
                    var optionId = optionName.match(/\[(\d+)\]/)[1];
                }
                var inputPollOption = new CKEDITOR.dom.element('input');
                inputPollOption.setAttribute('type', 'hidden');
                if (optionNew) {
                    inputPollOption.setAttribute('name', 'polloptionsID[' + optionNew[1] + optionId + ']');
                } else {
                    inputPollOption.setAttribute('name', 'polloptionsID[' + optionId + ']');
                }
                inputPollOption.setAttribute('value', jQuery('#field_option' + optionId).val());
                CKEDITOR.document.getById('poll_options').append(inputPollOption);
            });
		},
        onCancel: function () {
            jQuery('#dynamicContent').empty();
            options = 0;
        },
		onShow: function() {
			if (jQuery('#poll_exist_edit') !== undefined) {
				this.setValueOf( 'tab-basic', 'polltitle', jQuery('#ckeditor_dialog_polltitle').val() );
				this.setValueOf( 'tab-basic', 'polllifespan', jQuery('#ckeditor_dialog_polltimetolive').val() );
                
                var polloptions = jQuery('#poll_options input[name*="polloptionsID"]');
                var polloptionsset = jQuery('#poll_options').children().length > 0;
                var polloptionsIDs = [];
                var polloptionsNewIDs = [];
                if (polloptions) {
                    polloptions.each(function () {
                        var optionName = jQuery(this).attr('name');
                        var optionId = optionName.match(/\[(\d+)\]$/);
                        if (optionId) {
                            polloptionsIDs.push(optionId[1]);
                        }
                        var optionNewId = optionName.match(/\[new(\d+)\]$/);
                        if (optionNewId) {
                            polloptionsNewIDs.push(optionNewId[1]);
                        }
                    });
                }

                jQuery('.ckeditor_dialog_polloption').each(function () {
                    var optionName = jQuery(this).attr('name');
                    var optionId = optionName.match(/\d+$/);
                    if (jQuery('#field_option' + optionId).length === 0) {
                        if (!polloptionsset
                            || (polloptionsset && polloptionsIDs.length > 0 && polloptionsIDs.includes(optionId[0]))
                        ) {
                            createNewOptionField(jQuery(this).val(), optionId);
                        }
                    }
                });
                jQuery('#poll_options input[name*="polloptionsID[new"]').each(function () {
                    var optionName = jQuery(this).attr('name');
                    var optionId = optionName.match(/\[new(\d+)\]$/);

                    if (optionId && jQuery('#field_option' + optionId[1]).length === 0) {
                        createNewOptionField(jQuery(this).val(), optionId[1], true);
                    }
                });

                if (options >= nboptionsmax) {
                    var button = this.getContentElement('tab-basic', 'polladdoption');
                    button.disable();
                }
			}
		}
	};
});