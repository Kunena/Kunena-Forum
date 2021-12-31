/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.plugins.add( 'ebay', {
	icons: 'ebay',
	init: function( editor ) {
		editor.addCommand( 'insertEbay', {
			exec: function( editor ) {
				var selectedtext = editor.getSelection().getSelectedText();
				editor.insertHtml( '[ebay]' + selectedtext + '[/ebay]' );
			}
		});
		editor.ui.addButton( 'Ebay', {
			label: Joomla.JText._('COM_KUNENA_EDITOR_EBAY'),
			command: 'insertEbay',
			toolbar: 'social'
		});
	}
});