/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.plugins.add( 'instagram', {
	icons: 'instagram',
	init: function( editor ) {
		editor.addCommand( 'insertInstagram', {
			exec: function( editor ) {
				var selectedtext = editor.getSelection().getSelectedText();
				editor.insertHtml( '[instagram]' + selectedtext + '[/instagram]' );
			}
		});
		editor.ui.addButton( 'Instagram', {
			label: Joomla.JText._('COM_KUNENA_EDITOR_INSTAGRAM'),
			command: 'insertInstagram',
			toolbar: 'social'
		});
	}
});