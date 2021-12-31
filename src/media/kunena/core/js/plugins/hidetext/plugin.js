/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
 
 CKEDITOR.plugins.add( 'hidetext', {
	icons: 'hidetext',
	init: function( editor ) {
		editor.addCommand( 'insertHidetext', {
			exec: function( editor ) {
				var selectedtext = editor.getSelection().getSelectedText();
				editor.insertHtml( '[hide]' + selectedtext + '[/hide]' );
			}
		});
		editor.ui.addButton( 'Hidetext', {
			label: Joomla.JText._('COM_KUNENA_EDITOR_HIDE'),
			command: 'insertHidetext',
			toolbar: 'insert'
		});
	}
});