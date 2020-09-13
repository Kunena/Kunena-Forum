/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.dialog.add( 'videoDialog', function( editor ) {
	return {
		title: 'Video Properties',
		minWidth: 400,
		minHeight: 200,
		contents: [
			{
				id: 'tab-basic',
				label: 'Basic Settings',
				elements: [
			{
		type: 'text',
		id: 'videourl',
		label: 'Video URL',
		default: '',
		validate: CKEDITOR.dialog.validate.notEmpty( "City cannot be empty." )
	},
	{
		type: 'text',
		id: 'videosize',
		label: 'Select of the video',
		'default': ''
	},
	{
		type: 'text',
		id: 'width',
		label: 'Width',
		default: '',
		validate: CKEDITOR.dialog.validate.notEmpty( "City cannot be empty." )
	},
	{
		type: 'text',
		id: 'height',
		label: 'Height',
		default: '',
		validate: CKEDITOR.dialog.validate.notEmpty( "City cannot be empty." )
	},
	{
		type: 'text',
		id: 'provider',
		label: 'Provider',
		default: '',
		validate: CKEDITOR.dialog.validate.notEmpty( "City cannot be empty." )
	},
	{
		type: 'text',
		id: 'id',
		label: 'ID',
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
            editor.insertHtml( '[map type='+ dialog.getValueOf( 'tab-basic', 'maptype' ) +' zoom='+ dialog.getValueOf( 'tab-basic', 'zoomlevel' ) +']' + dialog.getValueOf( 'tab-basic', 'city' ) + '[/map]' );
        }
	};
});