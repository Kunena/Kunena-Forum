/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
 
 CKEDITOR.plugins.add( 'polls', {
	icons: 'polls',
	init: function( editor ) {
		editor.addCommand( 'polls', new CKEDITOR.dialogCommand( 'pollsDialog' ) );
		editor.ui.addButton( 'Polls', {
			label: Joomla.JText._('COM_KUNENA_EDITOR_POLLS'),
			command: 'polls',
			toolbar: 'others'
		});

		CKEDITOR.dialog.add( 'pollsDialog', this.path + 'dialogs/polls.js' );
	}
});