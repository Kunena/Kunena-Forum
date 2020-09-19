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
		default: ''
	},
	{
		type: 'text',
		id: 'videosize',
		label: 'Size of the video',
		'default': ''
	},
	{
		type: 'text',
		id: 'width',
		label: 'Width',
		default: ''
	},
	{
		type: 'text',
		id: 'height',
		label: 'Height',
		default: ''
	},
	{
		type: 'text',
		id: 'provider',
		label: 'Provider',
		default: ''
	},
	{
		type: 'text',
		id: 'videoid',
		label: 'ID',
		default: ''
	}
		]
            }
        ],
        onOk: function() {
			var dialog = this;
			var videourl = null;
			var videosize = '';
			var width = '';
			var height = '';

			if(dialog.getValueOf( 'tab-basic', 'videourl' ).length != 0)
			{
				videourl = dialog.getValueOf( 'tab-basic', 'videourl' );
			}
			else
			{
				videosize = dialog.getValueOf( 'tab-basic', 'videosize' );
				videosize = 'type='+videosize;
				width = dialog.getValueOf( 'tab-basic', 'width' );
				width = 'width='+width;
				height = dialog.getValueOf( 'tab-basic', 'height' );
				height = 'height='+height;
				videourl = dialog.getValueOf( 'tab-basic', 'videoid' );
			}

			editor.insertHtml( '[video '+videosize+' '+width+' '+height+']' + videourl + '[/video]' );
		}
	};
});