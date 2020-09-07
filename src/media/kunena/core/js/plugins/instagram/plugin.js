/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.plugins.add( 'instagram', {
    icons: 'instagram',
    init: function( editor ) {
        editor.addCommand( 'insertInstagram', {
            exec: function( editor ) {
                editor.insertHtml( '[instagram][/instagram]' );
            }
        });
        editor.ui.addButton( 'Instagram', {
            label: 'Insert Timestamp',
            command: 'insertInstagram',
            toolbar: 'insert'
        });
    }
});