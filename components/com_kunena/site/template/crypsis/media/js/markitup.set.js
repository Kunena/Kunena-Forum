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

jQuery(document).ready(function (){
	jQuery('#kbbcode-message').markItUp(bbcodeSettings);

	if (jQuery('#modal-code').length == 0) {
		jQuery('.codemodalboxbutton').hide();
	} else {
		jQuery('.codesimplebutton').hide();
	}

	// For code
	jQuery('#code-modal-submit').click(function() {
		var codetype = jQuery("#kcodetype option:selected").val();

		jQuery.markItUp(
			{ openWith:'[code type="'+codetype+'"]',
			  closeWith:'[/code]' }
		);
		return false;
	});

	// For map
	jQuery('#map-modal-submit').click(function() {
		var modalcity = jQuery('#modal-map-city').val();
		var modaltype = jQuery('#modal-map-type').val();
		var modalzoom = jQuery('#modal-map-zoomlevel').val();
		var type = '';
		var zoom = '';

		if (modaltype!=undefined)
		{
			type = 'type='+modaltype;
		}

		if (modalzoom!=undefined)
		{
			zoom = 'zoom='+modalzoom;
		}

		jQuery.markItUp(
		{ openWith:'[map '+type+' '+zoom+']'+modalcity,
		  closeWith:'[/map]' }
		);
		return false;
	});

	// For picture settings
	jQuery('#picture-modal-submit').click(function() {
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
	jQuery('#link-modal-submit').click(function() {
		var modallinkurl = jQuery('#modal-link-url').val();
		var modallinktext = jQuery('#modal-link-text').val();

		var text = '';
		if ( modallinktext.length > 0 ) {
			text = modallinktext;
		}
		else {
			text = modallinkurl;
		}

		if ( modallinkurl.length > 0 ) {
			jQuery.markItUp(
			{ openWith:'[url='+modallinkurl+']'+text,
				closeWith:'[/url]' }
			);

		return false;
		}
	});

	// For video settings
	jQuery('#videosettings-modal-submit').click(function() {
		var kvideoprovider = jQuery('#kvideoprovider-modal').val();
		var providerid = jQuery('#modal-video-id').val();
		var videowidth = jQuery('#modal-video-width').val();
		var videoheight = jQuery('#modal-video-height').val();
		var videosize = jQuery('#modal-video-size').val();
		var kvideoproviderlist = jQuery("#kvideoprovider-list-modal option:selected").val(); 

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

		if (jQuery('#kvideoprovider-modal').length > 0)
		{
			if ( kvideoprovider.lentgth > 0 && providerid.length > 0 ) {
				jQuery.markItUp(
					{ openWith:'[video '+size+' '+width+' '+height+' type='+kvideoprovider+']'+providerid,
					closeWith:'[/video]' }
				);
				return false;
			}
		}
		else
		{
			jQuery.markItUp(
				{ openWith:'[video '+size+' '+width+' '+height+' type='+kvideoproviderlist+']'+providerid,
				closeWith:'[/video]' }
			);
			return false;  
		}
	});

	// For video provider URL
	jQuery('#videourlprovider-modal-submit').click(function() {
		var providerurl = jQuery('#modal-video-urlprovider-input').val();

		jQuery.markItUp(
			{ openWith:'[video]'+providerurl,
			closeWith:'[/video]' }
		);
		return false;
	});

	// For smileys
	jQuery('.smileyimage').click(function() {
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
});
