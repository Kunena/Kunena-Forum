/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

// Register the plugin within the editor.
CKEDITOR.plugins.add( 'soundcloud', {
	icons: 'soundcloud',
	init: function( editor ) {
		editor.addCommand( 'insertSoundcloud', {
			exec: function( editor ) {
				var selectedtext = editor.getSelection().getSelectedText();
				editor.insertHtml( '[soundcloud]' + selectedtext + '[/soundcloud]' );
			}
		});
		editor.ui.addButton( 'Soundcloud', {
			label: Joomla.JText._('COM_KUNENA_EDITOR_SOUNDCLOUD'),
			command: 'insertSoundcloud',
			toolbar: 'social'
		});
	}
});