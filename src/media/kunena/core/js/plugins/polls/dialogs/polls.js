/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.dialog.add( 'pollsDialog', function( editor ) {
	var options = null;
	var nboptionsmax = jQuery('#nb_options_allowed').val();

	function createNewOptionField(optionText) {
		options++;
		var paragraph = new CKEDITOR.dom.element( 'p' );
		paragraph.setStyle( 'margin-top', '5px' );
		var label = new CKEDITOR.dom.element( 'label' );
		label.appendText(Joomla.JText._('COM_KUNENA_POLL_OPTION_NAME')+ ' ' + options + ' ');
		label.setAttribute('id', 'labeloption' + options);
		paragraph.append( label );
		var br = new CKEDITOR.dom.element( 'br' );
		paragraph.append( br);
		var inputField = new CKEDITOR.dom.element( 'input' );
		inputField.addClass( 'kunenackeditorpolloption' );
		inputField.addClass( 'cke_dialog_ui_input_text' );
		inputField.setAttribute('id', 'field_option' + options);
		inputField.setAttribute('name', 'polloptionsID[new' + options + ']' );
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
		title: Joomla.JText._('COM_KUNENA_EDITOR_DIALOG_POLLS_PROPERTIES'),
		minWidth: 400,
		minHeight: 200,
		contents: 
		[
			{
				id: 'tab-basic',
				label: Joomla.JText._('COM_KUNENA_EDITOR_DIALOG_BASIC_SETTINGS'),
				elements: 
				[
					{
						type: 'text',
						id: 'polltitle',
						label: Joomla.JText._('COM_KUNENA_POLL_TITLE'),
						default: ''
					},
					{
						type: 'button',
						id: 'polladdoption',
						label: Joomla.JText._('COM_KUNENA_POLL_ADD_POLL_OPTION'),
						title: Joomla.JText._('COM_KUNENA_POLL_ADD_POLL_OPTION'),
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
								// TODO :  Hide button add
								
								console.log('max options reach ');
							}
						}
					},
					{
						type: 'button',
						id: 'pollremoveoption',
						label: Joomla.JText._('COM_KUNENA_POLL_REMOVE_POLL_OPTION'),
						title: Joomla.JText._('COM_KUNENA_POLL_REMOVE_POLL_OPTION'),
						onClick: function() {
							// this = CKEDITOR.ui.dialog.button
							jQuery('#field_option' + options).remove();
							jQuery('#labeloption' + options).remove();
							options--;
							
							// TODO : show button hide if it was hidden
						}
					},
					{
						type: 'text',
						id: 'polllifespan',
						label: Joomla.JText._('COM_KUNENA_POLL_TIME_TO_LIVE'),
						default: '',
						onShow: function (data) {
							// Set the width of the outer div (otherwise it's affected by the CK CSS classes and is too wide)
							jQuery('#' + this.domId).css('width', 230);
							// Get the input element
							var theInput = jQuery('#' + this.domId).find('input');
							// Apply the datepicker to the input control
							jQuery(theInput.selector).datepicker({
								showButtonPanel: true,
								format: "yyyy-mm-dd"
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

			jQuery('.kunenackeditorpolloption').each(function(index) {
				index++
				var inputPollOption = new CKEDITOR.dom.element( 'input' );
				inputPollOption.setAttribute('type', 'hidden');
				inputPollOption.setAttribute('name', 'polloptionsID['+index+']' );
				inputPollOption.setAttribute('value', jQuery('#field_option'+index).val() );
				CKEDITOR.document.getById( 'poll_options' ).append(  inputPollOption );
			});
		},
		onShow: function() {
			if (jQuery('#poll_exist_edit') !== undefined) {
				this.setValueOf( 'tab-basic', 'polltitle', jQuery('#ckeditor_dialog_polltitle').val() );
				this.setValueOf( 'tab-basic', 'polllifespan', jQuery('#ckeditor_dialog_polltimetolive').val() );

				jQuery('.ckeditor_dialog_polloption').each(function () {
					createNewOptionField(jQuery(this).val());
				});
			}
		}
	};
});