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
		type: 'select',
		id: 'provider',
		label: 'Select a video provider',
		items: [ [ '' ], [ 'Bofunk' ], [ 'Break' ], [ 'Clipfish' ], [ 'DivX,divx]http://' ], [ 'Flash,flash]http://' ], [ 'FlashVars,flashvars param=]http://' ], 
		[ 'MediaPlayer,mediaplayer]http://' ], [ 'Metacafe' ], [ 'MySpace' ], [ 'QuickTime,quicktime]http://' ], [ 'RealPlayer,realplayer]http://' ], [ 'RuTube' ], [ 'Sapo' ]
		, [ 'Streetfire' ], [ 'Veoh' ], [ 'Videojug' ], [ 'Vimeo' ], [ 'Wideo.fr' ], [ 'YouTube' ] ],
		'default': '',
		onChange: function( api ) {
			// this = CKEDITOR.ui.dialog.select
		}
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
			var videoprovider = '';

			if(dialog.getValueOf( 'tab-basic', 'videourl' ).length != 0)
			{
				videourl = dialog.getValueOf( 'tab-basic', 'videourl' );

				editor.insertHtml( '[video]' + videourl + '[/video]' );
			}
			else
			{
				videosize = dialog.getValueOf( 'tab-basic', 'videosize' );
				videosize = 'size='+videosize;
				width = dialog.getValueOf( 'tab-basic', 'width' );
				width = 'width='+width;
				height = dialog.getValueOf( 'tab-basic', 'height' );
				height = 'height='+height;
				videourl = dialog.getValueOf( 'tab-basic', 'videoid' );
				videoprovider = dialog.getValueOf( 'tab-basic', 'provider' );
				videoprovider = 'type='+videoprovider;

				editor.insertHtml( '[video '+videoprovider+' '+videosize+' '+width+' '+height+']' + videourl + '[/video]' );
			}
		}
	};
});