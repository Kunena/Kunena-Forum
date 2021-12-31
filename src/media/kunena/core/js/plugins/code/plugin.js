/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.plugins.add( 'code', {
	icons: 'code',
	init: function( editor ) {
		editor.addCommand( 'insertCode', {
			exec: function( editor ) {
				var selectedtext = editor.getSelection().getSelectedText();
				editor.insertHtml( '[code]' + selectedtext + '[/code]' );
			}
		});
		editor.ui.addButton( 'Code', {
			label: Joomla.JText._('COM_KUNENA_EDITOR_CODE'),
			command: 'insertCode',
			toolbar: 'insert'
		});
	}
});