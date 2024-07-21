/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.plugins.add( 'x', {
	icons: 'x_social',
	init: function( editor ) {
		editor.addCommand( 'insertTwitter', {
			exec: function( editor ) {
				var selectedtext = editor.getSelection().getSelectedText();
				editor.insertHtml( '[tweet]' + selectedtext + '[/tweet]' );
			}
		});
		editor.ui.addButton( 'X_Social', {
			label: Joomla.Text._('COM_KUNENA_EDITOR_TWEET'),
			command: 'insertTwitter',
			toolbar: 'social'
		});
	}
});
