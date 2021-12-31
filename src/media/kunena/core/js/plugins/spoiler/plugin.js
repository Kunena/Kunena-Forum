/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
 
 CKEDITOR.plugins.add( 'spoiler', {
	icons: 'spoiler',
	init: function( editor ) {
		editor.addCommand( 'insertSpoiler', {
			exec: function( editor ) {
				var selectedtext = editor.getSelection().getSelectedText();
				editor.insertHtml( '[spoiler]' + selectedtext + '[/spoiler]' );
			}
		});
		editor.ui.addButton( 'Spoiler', {
			label: Joomla.JText._('COM_KUNENA_EDITOR_SPOILER'),
			command: 'insertSpoiler',
			toolbar: 'insert'
		});
	}
});