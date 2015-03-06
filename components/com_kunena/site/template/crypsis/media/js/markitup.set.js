// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
bbcodeSettings = {
	previewParserPath:	'', // path to your BBCode parser
	markupSet: [
		{name:'Bold', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'Italic', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'Underline', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{name:'Stroke', key:'T', openWith:'[strike]', closeWith:'[/strike]'},
		{name:'Subscript', key:'T', openWith:'[sub]', closeWith:'[/sub]'},
		{name:'Supscript', key:'T', openWith:'[sup]', closeWith:'[/sup]'},
		{name:'Size', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',
			dropMenu :[
			{name:'Very very small', openWith:'[size=1]', 	closeWith:'[/size]' },
			{name:'Very Small', openWith:'[size=2]', 	closeWith:'[/size]' },
			{name:'Small', openWith:'[size=3]', closeWith:'[/size]' },
			{name:'Normal', openWith:'[size=4]', closeWith:'[/size]' },
			{name:'Big', openWith:'[size=5]', closeWith:'[/size]' },
			{name:'Super Bigger', openWith:'[size=6]', closeWith:'[/size]' }
			]},
		{
		name:'Colors', 
			className:'colors', 
			openWith:'[color=[![Color]!]]', 
			closeWith:'[/color]', 
				dropMenu: [
					{name:'Black',	openWith:'[color=#000000]', 	closeWith:'[/color]', className:"col1-1" },
					{name:'Orange',	openWith:'[color=#000044]', 	closeWith:'[/color]', className:"col1-2" },
					{name:'Red', 	openWith:'[color=#000088]', 	closeWith:'[/color]', className:"col1-3" },
					{name:'Red', 	openWith:'[color=#0000bb]', 	closeWith:'[/color]', className:"col1-4" },
					{name:'Red', 	openWith:'[color=#0000ff]', 	closeWith:'[/color]', className:"col1-5" },
					
					{name:'Blue', 	openWith:'[color=blue]', 	closeWith:'[/color]', className:"col2-1" },
					{name:'Purple', openWith:'[color=purple]', 	closeWith:'[/color]', className:"col2-2" },
					{name:'Green', 	openWith:'[color=green]', 	closeWith:'[/color]', className:"col2-3" },
					
					{name:'White', 	openWith:'[color=white]', 	closeWith:'[/color]', className:"col3-1" },
					{name:'Gray', 	openWith:'[color=gray]', 	closeWith:'[/color]', className:"col3-2" },
					{name:'Black',	openWith:'[color=black]', 	closeWith:'[/color]', className:"col3-3" }
				]
		},
		{separator:'---------------' },
		{name:'Bulleted list', openWith:'[ul]\n[li][/li]\n[li][/li]', closeWith:'\n[/ul]'},
		{name:'Numeric list', openWith:'[ol=[![Starting number]!]]\n[li][/li]\n[li][/li]', closeWith:'\n[/ol]'}, 
		{name:'List item', openWith:'[li]\n', closeWith:'\n[/li]'},
		{name:'Align left', openWith:'[left]\n', closeWith:'\n[/left]'},
		{name:'Center', openWith:'[center]\n', closeWith:'\n[/center]'},
		{name:'Align right', openWith:'[right]\n', closeWith:'\n[/right]'},		
		{separator:'---------------' },
		{name:'Quote', openWith:'[quote]', closeWith:'[/quote]'},		
		{name: 'Code', className: 'CodeContenair', beforeInsert:function() {
			jQuery('#modal-code').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn('slow', function () {
						dialog.container.slideDown('slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			}
		},
		{name:'Table', openWith:'[table]\n[tr]\n[td][/td]\n[td][/td]\n[/tr]\n[tr]\n[td][/td]\n[td][/td]\n[/tr]', closeWith:'[/table]'},
		{name:'Spoiler', openWith:'[spoiler]', closeWith:'[/spoiler]'},
		{name:'Hidden text', openWith:'[hide]', closeWith:'[/hide]'}, 
		{separator:'---------------' },    
		{name: 'Picture', className: 'PicContenair', beforeInsert:function() {
			jQuery('#modal-picture').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn('slow', function () {
						dialog.container.slideDown('slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			}
		},
		{name:'Link',  className: 'LinkContenair', beforeInsert:function() {
			jQuery('#modal-link').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn('slow', function () {
						dialog.container.slideDown('slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			}
		}, 
		{separator:'---------------' },
		{name:'Ebay', key:'E', openWith:'[ebay]', closeWith:'[/ebay]'},
		{name:'Video', className: 'VideoDropdown', dropMenu: [
		{name: 'Video Settings', className: 'VideoContenairSettings', beforeInsert:function() {
			jQuery('#modal-video-settings').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn('slow', function () {
						dialog.container.slideDown('slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			} },
		{name: 'Video Provider URL', className: 'VideoContenairURL', beforeInsert:function() {
			jQuery('#modal-video-urlprovider').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn('slow', function () {
						dialog.container.slideDown('slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			} }
		]},
		{name: 'Map', className: 'MapContenair', beforeInsert:function() {
			jQuery('#modal-map').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn('slow', function () {
						dialog.container.slideDown('slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			}
		},
		{name: 'Poll', className: 'PollContenair', beforeInsert:function() {
			jQuery('#modal-poll-settings').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn('slow', function () {
						dialog.container.slideDown('slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			}
		},
		{separator:'---------------' },
		{name:'Clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } }		
	]
}

jQuery(document).ready(function (){
	jQuery('#kbbcode-message').markItUp(bbcodeSettings);

	// For code
	jQuery('#modal-code').change(function() {
		var codetype = jQuery("#kcodetype option:selected").val();
	
		jQuery.markItUp(
			{ openWith:'[code type="'+codetype+'"]',
			  closeWith:'[/code]' }
		);
		return false;
	});
	
	// For map
	jQuery('#modal-map-city').change(function() {
		var modalcity = jQuery('#modal-map-city').val();

		jQuery.markItUp(
		{ openWith:'[map]'+modalcity,
		  closeWith:'[/map]' }
		);
		return false;
	});

	// For picture settings
	jQuery('#modal-picture').change(function() {
		var modalpictureurl = jQuery('#modal-picture-url').val();
		var modalpicturesize = jQuery('#modal-picture-size').val();

		var size = '';
		if ( modalpicturesize.length > 0 ) {
			size = 'size='+modalpicturesize;
		}

		if ( modalpictureurl.length > 0 ) {
			jQuery.markItUp(
				{ openWith:'[img '+size+']'+modalpictureurl,
				closeWith:'[/img]' }
			);
		return false;
		}
	});

	//For link settings
	jQuery('#modal-link').change(function() {
		var modallinkurl = jQuery('#modal-link-url').val();
		var modallinksize = jQuery('#modal-link-size').val();

		var size = '';
		if ( modalpicturesize.length > 0 ) {
			size = 'size='+modalpicturesize;
		}

		if ( modalpictureurl.length > 0 ) {
			jQuery.markItUp(
				{ openWith:'[url '+size+']'+modalpictureurl,
				closeWith:'[/url]' }
			);
			return false;
		}
	});

	// For video settings
	jQuery('#modal-video-id').change(function() {
		var kvideoprovider = jQuery('#kvideoprovider-modal').val();
		var providerid = jQuery('#modal-video-id').val();
		var videowidth = jQuery('#modal-video-width').val();
		var videoheight = jQuery('#modal-video-height').val();
		var videosize = jQuery('#modal-video-size').val();

		var width = '425';
		var height = '344';
		if ( videowidth.length > 0 && videoheight.length > 0 ) {
			width = 'width='+videowidth;
			height = 'height='+videoheight;  
		} else {
			width = 'width='+width;
			height = 'height='+height;
		}

		var size = '';
		if (videosize.length > 0) {
			size = 'size='+videosize;
		}

		if ( kvideoprovider.lentgth > 0 && providerid.length > 0 ) {
			jQuery.markItUp(
				{ openWith:'[video '+size+' '+width+' '+height+' type='+kvideoprovider+']'+providerid,
				closeWith:'[/video]' }
			);
			return false;
		}
	});

	// For video provider URL
	jQuery('#modal-video-urlprovider-input').change(function() {
		var providerurl = jQuery('#modal-video-urlprovider-input').val();

		jQuery.markItUp(
			{ openWith:'[video]'+providerurl,
			closeWith:'[/video]' }
		);
		return false;
	});
});