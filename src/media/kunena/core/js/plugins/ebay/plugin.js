/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.plugins.add( 'ebay', {
    icons: 'ebay',
    init: function( editor ) {
        editor.addCommand( 'insertEbay', {
            exec: function( editor ) {
                editor.insertHtml( '[ebay][/ebay]' );
            }
        });
        editor.ui.addButton( 'Ebay', {
            label: 'Insert Timestamp',
            command: 'insertEbay',
            toolbar: 'insert'
        });
    }
});