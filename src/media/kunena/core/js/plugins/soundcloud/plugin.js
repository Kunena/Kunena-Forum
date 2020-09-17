/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

// Register the plugin within the editor.
CKEDITOR.plugins.add( 'soundcloud', {
    icons: 'soundcloud',
    init: function( editor ) {
        editor.addCommand( 'insertSoundcloud', {
            exec: function( editor ) {
                editor.insertHtml( '[soundcloud][/soundcloud]' );
            }
        });
        editor.ui.addButton( 'Soundcloud', {
            label: 'Insert Timestamp',
            command: 'insertSoundcloud',
            toolbar: 'insert'
        });
    }
});