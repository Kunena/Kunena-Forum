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
		{name:'Bold', className: 'boldbutton', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'Italic', className: 'italicbutton', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'Underline', className: 'underlinebutton', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{name:'Stroke', className: 'strokebutton', key:'T', openWith:'[strike]', closeWith:'[/strike]'},
		{name:'Subscript', className: 'subscriptbutton', key:'T', openWith:'[sub]', closeWith:'[/sub]'},
		{name:'Supscript', className: 'supscriptbutton', key:'T', openWith:'[sup]', closeWith:'[/sup]'},
		{name:'Size', className: 'sizebutton', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',
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
					{name:'Black',	openWith:'[color=black]', 	closeWith:'[/color]', className:"col1-1" },
					{name:'Orange',	openWith:'[color=orange]', 	closeWith:'[/color]', className:"col1-2" },
					{name:'Red', 	openWith:'[color=red]', 	closeWith:'[/color]', className:"col1-3" },

					{name:'Blue', 	openWith:'[color=blue]', 	closeWith:'[/color]', className:"col2-1" },
					{name:'Purple', openWith:'[color=purple]', 	closeWith:'[/color]', className:"col2-2" },
					{name:'Green', 	openWith:'[color=green]', 	closeWith:'[/color]', className:"col2-3" },

					{name:'White', 	openWith:'[color=white]', 	closeWith:'[/color]', className:"col3-1" },
					{name:'Gray', 	openWith:'[color=gray]', 	closeWith:'[/color]', className:"col3-2" }
				]
		},
		{separator:'|' },
		{name:'Bulleted list', className: 'bulletedlistbutton', openWith:'[ul]\n[li][/li]\n[li][/li]', closeWith:'\n[/ul]'},
		{name:'Numeric list', className: 'numericlistbutton', openWith:'[ol=[![Starting number]!]]\n[li][/li]\n[li][/li]', closeWith:'\n[/ol]'},
		{name:'List item', className: 'listitembutton', openWith:'[li]\n', closeWith:'\n[/li]'},
		{name:'HR', className: 'hrbutton', openWith:'[hr]'},
		{name:'Align left', className: 'alignleftbutton', openWith:'[left]\n', closeWith:'\n[/left]'},
		{name:'Center', className: 'centerbutton', openWith:'[center]\n', closeWith:'\n[/center]'},
		{name:'Align right', className: 'alignrightbutton', openWith:'[right]\n', closeWith:'\n[/right]'},
		{separator:'|' },
		{name:'Quote', className: 'quotebutton', openWith:'[quote]', closeWith:'[/quote]'},
		{name: 'Code', className: 'codemodalboxbutton', beforeInsert:function() {
			jQuery('#code-modal-submit').click(
    function(event) {
				event.preventDefault();

				jQuery('#modal-code').modal('hide');
			});

			jQuery('#modal-code').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn(
    'slow', function () {
						dialog.container.slideDown(
    'slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			}
		},
		{name: 'Code', className: 'codesimplebutton', openWith:'[code]', closeWith:'[/code]' },
		{name:'Table', className: 'tablebutton', openWith:'[table]\n[tr]\n[td][/td]\n[td][/td]\n[/tr]\n[tr]\n[td][/td]\n[td][/td]\n[/tr]', closeWith:'[/table]'},
		{name:'Spoiler', className: 'spoilerbutton',  openWith:'[spoiler]', closeWith:'[/spoiler]'},
		{name:'Hidden text', className: 'hiddentextbutton',  openWith:'[hide]', closeWith:'[/hide]'},
		{separator:'|' },
		{name: 'Picture', className: 'picturebutton', beforeInsert:function() {
			jQuery('#picture-modal-submit').click(
	function(event) {
				event.preventDefault();

				jQuery('#modal-picture').modal('hide');
			});

			jQuery('#modal-picture').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn(
	'slow', function () {
						dialog.container.slideDown(
	'slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			}
		},
		{name:'Link',  className: 'linkbutton', beforeInsert:function() {
			jQuery('#link-modal-submit').click(
	function(event) {
				event.preventDefault();

				jQuery('#modal-link').modal('hide');
			});

			jQuery('#modal-link').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn(
	'slow', function () {
						dialog.container.slideDown(
	'slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			}
		},
		{separator:'|' },
		{name:'Ebay', className:'ebaybutton', key:'E', openWith:'[ebay]', closeWith:'[/ebay]'},
		{name:'Video', className: 'videodropdownbutton', dropMenu: [
		{name: 'Video Settings', className: 'videosettingsbutton', beforeInsert:function() {
			jQuery('#videosettings-modal-submit').click(
	function(event) {
				event.preventDefault();

				jQuery('#modal-video-settings').modal('hide');
			});

			jQuery('#modal-video-settings').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn(
	'slow', function () {
						dialog.container.slideDown(
	'slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			} },
		{name: 'Video Provider URL', className: 'videoURLbutton', beforeInsert:function() {
			jQuery('#videourlprovider-modal-submit').click(
	function(event) {
				event.preventDefault();

				jQuery('#modal-video-urlprovider').modal('hide');
			});

			jQuery('#modal-video-urlprovider').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn(
	'slow', function () {
						dialog.container.slideDown(
	'slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			} }
		]},
		{name: 'Map', className: 'mapbutton', beforeInsert:function() {
			jQuery('#map-modal-submit').click(
	function(event) {
				event.preventDefault();

				jQuery('#modal-map').modal('hide');
			});

			jQuery('#modal-map').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn(
	'slow', function () {
						dialog.container.slideDown(
	'slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
			}
		},
		{name: 'Poll', className: 'pollbutton', beforeInsert:function() {
			if (jQuery('#modal-poll-settings').length != 0) {
					jQuery('#poll-settings-modal-submit').click(
	  function(event) {
						event.preventDefault();

						jQuery('#modal-poll-settings').modal('hide');
					});

					jQuery('#modal-poll-settings').modal(
	  {overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
						dialog.overlay.fadeIn(
	  'slow', function () {
							dialog.container.slideDown(
	  'slow', function () {
								dialog.data.fadeIn('slow');
							});
						});
					}});
				}
			}
		},
		{name:'Tweet', className: 'tweetbutton', openWith:'[tweet]', closeWith:'[/tweet]'},
		{name: 'Emoticons', className: 'emoticonsbutton', beforeInsert:function() {
			jQuery('#map-modal-submit').click(
	function(event) {
				event.preventDefault();

				jQuery('#modal-map').modal('hide');
			});

			jQuery('#modal-emoticons').modal(
				{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
					dialog.overlay.fadeIn(
	'slow', function () {
						dialog.container.slideDown(
	'slow', function () {
							dialog.data.fadeIn('slow');
						});
					});
				}});
		}}
	]
};

jQuery(document).ready(
	function (){
	jQuery('#kbbcode-message').markItUp(bbcodeSettings);

	if (jQuery('#modal-code').length == 0) {
		jQuery('.codemodalboxbutton').hide();
	} else {
		jQuery('.codesimplebutton').hide();
	}

	// For code
	jQuery('#code-modal-submit').click(
	function() {
		var codetype = jQuery("#kcodetype option:selected").val();

		jQuery.markItUp(
			{ openWith:'[code type="' + codetype + '"]',
			  closeWith:'[/code]' }
		);
		return false;
	});

	// For map
	jQuery('#map-modal-submit').click(
	function() {
		var modalcity = jQuery('#modal-map-city').val();
		var modaltype = jQuery('#modal-map-type').val();
		var modalzoom = jQuery('#modal-map-zoomlevel').val();
		var type = '';
		var zoom = '';

		if (modaltype != undefined)
		{
			type = 'type=' + modaltype;
		}

		if (modalzoom != undefined)
		{
			zoom = 'zoom=' + modalzoom;
		}

		jQuery.markItUp(
	{ openWith:'[map ' + type + ' ' + zoom + ']' + modalcity,
		  closeWith:'[/map]' }
		);
		return false;
	});

	// For picture settings
	jQuery('#picture-modal-submit').click(
	function() {
		var modalpictureurl = jQuery('#modal-picture-url').val();
		var modalpicturesize = jQuery('#modal-picture-size').val();

		var size = '';
		if (modalpicturesize.length > 0) {
			size = 'size=' + modalpicturesize;
		}

		if (modalpictureurl.length > 0) {
			jQuery.markItUp(
				{ openWith:'[img ' + size + ']' + modalpictureurl,
				closeWith:'[/img]' }
			);
		return false;
		}
	});

	//For link settings
	jQuery('#link-modal-submit').click(
	function() {
		var modallinkurl = jQuery('#modal-link-url').val();
		var modallinktext = jQuery('#modal-link-text').val();

		var text = '';
		if (modallinktext.length > 0) {
			text = modallinktext;
		}
		else {
			text = modallinkurl;
		}

		if (modallinkurl.length > 0) {
			jQuery.markItUp(
	{ openWith:'[url=' + modallinkurl + ']' + text,
				closeWith:'[/url]' }
			);

		return false;
		}
	});

	// For video settings
	jQuery('#videosettings-modal-submit').click(
	function() {
		var kvideoprovider = jQuery('#kvideoprovider-modal').val();
		var providerid = jQuery('#modal-video-id').val();
		var videowidth = jQuery('#modal-video-width').val();
		var videoheight = jQuery('#modal-video-height').val();
		var videosize = jQuery('#modal-video-size').val();
		var kvideoproviderlist = jQuery("#kvideoprovider-list-modal option:selected").val();

		var width = '425';
		var height = '344';
		if (videowidth.length > 0 && videoheight.length > 0) {
			width = 'width=' + videowidth;
			height = 'height=' + videoheight;
		} else {
			width = 'width=' + width;
			height = 'height=' + height;
		}

		var size = '';
		if (videosize.length > 0) {
			size = 'size=' + videosize;
		}

		if (jQuery('#kvideoprovider-modal').length > 0)
		{
			if (kvideoprovider.lentgth > 0 && providerid.length > 0) {
				jQuery.markItUp(
					{ openWith:'[video ' + size + ' ' + width + ' ' + height + ' type=' + kvideoprovider + ']' + providerid,
					closeWith:'[/video]' }
				);
				return false;
			}
		}
		else
		{
			jQuery.markItUp(
				{ openWith:'[video ' + size + ' ' + width + ' ' + height + ' type=' + kvideoproviderlist + ']' + providerid,
				closeWith:'[/video]' }
			);
			return false;
		}
	});

	// For video provider URL
	jQuery('#videourlprovider-modal-submit').click(
	function() {
		var providerurl = jQuery('#modal-video-urlprovider-input').val();

		jQuery.markItUp(
			{ openWith:'[video]' + providerurl,
			closeWith:'[/video]' }
		);
		return false;
	});

	// For smileys
	jQuery('.smileyimage').click(
	function() {
		var smiley = jQuery(this).attr('alt');

		jQuery.markItUp(
	{ openWith:smiley,
			closeWith:'' }
		);
		return false;
	});

	if (!kunena_showvideotag) {
		jQuery('.videodropdownbutton').remove();
	}

	if (!kunena_disemoticons) {
		jQuery('.emoticonsbutton').remove();
	}

	if (!kunena_showebaytag) {
		jQuery('.ebaybutton').remove();
	}

	if (!kunena_showspoilertag) {
		jQuery('.spoilerbutton').remove();
	}

	if (!kunena_showmapstag) {
		jQuery('.mapbutton').remove();
	}

	if (!kunena_showtwittertag) {
		jQuery('.tweetbutton').remove();
	}

	if (!kunena_showlinktag) {
		jQuery('.linkbutton').remove();
	}

	if (!kunena_showpicturetag) {
		jQuery('.picturebutton').remove();
	}

	if (!kunena_showhidetag) {
		jQuery('.hiddentextbutton').remove();
	}

	if (!kunena_showtabletag) {
		jQuery('.tablebutton').remove();
	}

	if (!kunena_showcodetag) {
		jQuery('.codesimplebutton').remove();
	}

	if (!kunena_showquotetag) {
		jQuery('.quotebutton').remove();
	}

	if (!kunena_showdividertag) {
		jQuery('.markItUpSeparator').remove();
	}

	if (!kunena_showinstagramtag) {
		jQuery('.instagrambutton').remove();
	}

	if (!kunena_showsoundcloudtag) {
		jQuery('.soundcloudbutton').remove();
	}
});
