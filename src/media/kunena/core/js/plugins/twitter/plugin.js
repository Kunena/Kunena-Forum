/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.plugins.add( 'twitter', {
    icons: 'twitter',
    init: function( editor ) {
        editor.addCommand( 'insertTwitter', {
            exec: function( editor ) {
                editor.insertHtml( '[tweet][/tweet]' );
            }
        });
        editor.ui.addButton( 'Twitter', {
            label: 'Insert Timestamp',
            command: 'insertTwitter',
            toolbar: 'others'
        });
    }
});