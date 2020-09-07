/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

// Register the plugin within the editor.
CKEDITOR.plugins.add( 'map', {
	icons: 'map',
	init: function( editor ) {
		editor.addCommand( 'map', new CKEDITOR.dialogCommand( 'mapDialog' ) );
		editor.ui.addButton( 'Map', {
			label: 'Insert Map',
			command: 'map',
			toolbar: 'others'
		});

		CKEDITOR.dialog.add( 'mapDialog', this.path + 'dialogs/map.js' );
	}
});