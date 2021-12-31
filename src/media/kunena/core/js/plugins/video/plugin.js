/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
 
 CKEDITOR.plugins.add( 'video', {
	icons: 'video',
	init: function( editor ) {
		editor.addCommand( 'video', new CKEDITOR.dialogCommand( 'videoDialog' ) );
		editor.ui.addButton( 'Video', {
			label: Joomla.JText._('COM_KUNENA_EDITOR_VIDEO'),
			command: 'video',
			toolbar: 'others'
		});

		CKEDITOR.dialog.add( 'videoDialog', this.path + 'dialogs/video.js' );
	}
});