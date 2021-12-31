/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.dialog.add( 'mapDialog', function( editor ) {
	return {
		title: Joomla.JText._('COM_KUNENA_EDITOR_DIALOG_MAPS_PROPERTIES'),
		minWidth: 400,
		minHeight: 200,
		contents: [
            {
                id: 'tab-basic',
                label: Joomla.JText._('COM_KUNENA_EDITOR_DIALOG_BASIC_SETTINGS'),
                elements: [
                    {
    type: 'select',
    id: 'maptype',
    label: 'Select the map type',
    items: [ [ 'HYBRID' ], [ 'ROADMAP' ], [ 'TERRAIN' ], [ 'SATELITE' ] ],
    'default': 'Roadmap',
    onChange: function( api ) {
        // this = CKEDITOR.ui.dialog.select
        //alert( 'Current value: ' + this.getValue() );
    }
},
 {
    type: 'select',
    id: 'zoomlevel',
    label: 'Select the zoom level',
    items: [ [ '2' ], [ '4' ], [ '6' ], [ '8' ], [ '10' ], [ '12' ], [ '14' ], [ '16' ], [ '18' ] ],
    'default': '10',
    onChange: function( api ) {
        // this = CKEDITOR.ui.dialog.select
    }
},
{
                        type: 'text',
                        id: 'city',
                        label: 'City',
                        default: '',
                        validate: CKEDITOR.dialog.validate.notEmpty( "City cannot be empty." )
                    }
                ]
            }
        ],
        onOk: function() {
            var dialog = this;

            var abbr = editor.document.createElement( 'city' );
            
            abbr.setText( dialog.getValueOf( 'tab-basic', 'city' ) );

            editor.insertHtml( '[map type='+dialog.getValueOf( 'tab-basic', 'maptype' )+' mode='+dialog.getValueOf( 'tab-basic', 'zoomlevel' )+']' + dialog.getValueOf( 'tab-basic', 'city' ) + '[/map]' );
        }
	};
});