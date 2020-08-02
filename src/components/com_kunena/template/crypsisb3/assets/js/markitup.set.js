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

jQuery(document).ready(function ($) {
	$('#editor').markItUp(bbcodeSettings);

	if ($('#modal-code').length == 0) {
		$('.codemodalboxbutton').hide();
	} else {
		$('.codesimplebutton').hide();
	}

	// For code
	$('#code-modal-submit').click(function () {
		var codetype = $("#kcodetype option:selected").val();

		$.markItUp(
			{
				openWith: '[code type="' + codetype + '"]',
				closeWith: '[/code]'
			}
		);
		return false;
	});

	// For map
	$('#map-modal-submit').click(function () {
		var modalcity = $('#modal-map-city').val();
		var modaltype = $('#modal-map-type').val();
		var modalzoom = $('#modal-map-zoomlevel').val();
		var type = '';
		var zoom = '';

		if (modaltype != undefined) {
			type = 'type=' + modaltype;
		}

		if (modalzoom != undefined) {
			zoom = 'zoom=' + modalzoom;
		}

		if (modalcity.length > 0) {
			$.markItUp(
				{
					openWith: '[map ' + type + ' ' + zoom + ']' + modalcity,
					closeWith: '[/map]'
				}
			);
		}

		$('#modal-map-city').val('');
		$('#modal-map-type').val('');
		$('#modal-map-zoomlevel').val('');

		return false;
	});

	// For picture settings
	$('#picture-modal-submit').click(function () {
		var modalpictureurl = $('#modal-picture-url').val();
		var modalpicturesize = $("#kpicture-size-list-modal option:selected").val();
		var modalpicturealt = $('#modal-picture-alt').val();

		var size = '';
		if (modalpicturesize.length > 0) {
			size = 'size=' + modalpicturesize;
		}

		var alt = '';
		if (modalpicturealt.length > 0) {
			alt = 'alt=' + modalpicturealt;
		}

		if (modalpictureurl.length > 0) {
			$.markItUp(
				{
					openWith: '[img ' + size + ' ' + alt + ']' + modalpictureurl,
					closeWith: '[/img]'
				}
			);
		}

		$('#modal-picture-url').val('');
		$("#kpicture-size-list-modal option:selected").val('');
		$('#modal-picture-alt').val('');

		return false;
	});

	//For link settings
	$('#link-modal-submit').click(function () {
		var modallinkurl = $('#modal-link-url').val();
		var modallinktext = $('#modal-link-text').val();
		var modallinkclass = $('#modal-link-class').val();

		var text = '';
		if (modallinktext.length > 0) {
			text = modallinktext;
		}
		else {
			text = modallinkurl;
		}

		var classitem = '';
		if (modallinkclass.length > 0) {
			classitem = modallinkclass;
		}

		if (modallinkurl.length > 0) {
			$.markItUp(
				{
					openWith: '[url=' + modallinkurl + ' class="' + classitem + '"]' + text,
					closeWith: '[/url]'
				}
			);
		}

		$('#modal-link-url').val('');
		$('#modal-link-text').val('');
		$('#modal-link-class').val('');

		return false;
	});

	// For video settings
	$('#videosettings-modal-submit').click(function () {
		var kvideoprovider = $('#kvideoprovider-modal').val();
		var providerid = $('#modal-video-id').val();
		var videowidth = $('#modal-video-width').val();
		var videoheight = $('#modal-video-height').val();
		var videosize = $('#modal-video-size').val();
		var kvideoproviderlist = $("#kvideoprovider-list-modal option:selected").val();

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

		if ($('#kvideoprovider-modal').length > 0) {
			if (kvideoprovider.lentgth > 0 && providerid.length > 0) {
				$.markItUp(
					{
						openWith: '[video ' + size + ' ' + width + ' ' + height + ' type=' + kvideoprovider + ']' + providerid,
						closeWith: '[/video]'
					}
				);
			}
		}
		else {
			$.markItUp(
				{
					openWith: '[video ' + size + ' ' + width + ' ' + height + ' type=' + kvideoproviderlist + ']' + providerid,
					closeWith: '[/video]'
				}
			);
		}

		$('#kvideoprovider-modal').val('');
		$('#modal-video-id').val('');
		$('#modal-video-width').val('');
		$('#modal-video-height').val('');
		$('#modal-video-size').val('');
		$("#kvideoprovider-list-modal option:selected").val('');

		return false;
	});

	// For video provider URL
	$('#videourlprovider-modal-submit').click(function () {
		var providerurl = $('#modal-video-urlprovider-input').val();

		$.markItUp(
			{
				openWith: '[video]' + providerurl,
				closeWith: '[/video]'
			}
		);

		$('#modal-video-urlprovider-input').val('');

		return false;
	});

	// For smileys
	$('.smileyimage').click(function () {
		var smiley = $(this).attr('alt');

		$.markItUp(
			{
				openWith: smiley,
				closeWith: ''
			}
		);
		return false;
	});

	$('#clearpoll').click(function () {
		$('#kpoll-title').val('');
		$('.inputpollclear').val('');
		$('#poll_time_to_live').val('');
	});

	if (!kunena_showvideotag) {
		$('.videodropdownbutton').remove();
	}

	if (!kunena_disemoticons) {
		$('.emoticonsbutton').remove();
	}

	if (!kunena_showebaytag) {
		$('.ebaybutton').remove();
	}

	if (!kunena_showspoilertag) {
		$('.spoilerbutton').remove();
	}

	if (!kunena_showmapstag) {
		$('.mapbutton').remove();
	}

	if (!kunena_showtwittertag) {
		$('.tweetbutton').remove();
	}

	if (!kunena_showlinktag) {
		$('.linkbutton').remove();
	}

	if (!kunena_showpicturetag) {
		$('.picturebutton').remove();
	}

	if (!kunena_showhidetag) {
		$('.hiddentextbutton').remove();
	}

	if (!kunena_showtabletag) {
		$('.tablebutton').remove();
	}

	if (!kunena_showcodetag) {
		$('.codesimplebutton').remove();
	}

	if (!kunena_showquotetag) {
		$('.quotebutton').remove();
	}

	if (!kunena_showdividertag) {
		$('.markItUpSeparator').remove();
	}

	if (!kunena_showinstagramtag) {
		$('.instagrambutton').remove();
	}

	if (!kunena_showsoundcloudtag) {
		$('.soundcloudbutton').remove();
	}

	if (!kunena_showconfidentialtag) {
		$('.confidentialbutton').remove();
	}

	if (!kunena_showhrtag) {
		$('.hrbutton').remove();
	}

	if (!kunena_showlistitemtag) {
		$('.listitembutton').remove();
	}

	if (!kunena_showsupscripttag) {
		$('.supscriptbutton').remove();
	}

	if (!kunena_showsubscripttag) {
		$('.subscriptbutton').remove();
	}

	if (!kunena_shownumericlisttag) {
		$('.numericlistbutton').remove();
	}

	if (!kunena_showbulletedlisttag) {
		$('.bulletedlistbutton').remove();
	}

	if (!kunena_showalignrighttag) {
		$('.alignrightbutton').remove();
	}

	if (!kunena_showalignlefttag) {
		$('.alignleftbutton').remove();
	}

	if (!kunena_showcentertag) {
		$('.centerbutton').remove();
	}

	if (!kunena_showunderlinetag) {
		$('.underlinebutton').remove();
	}

	if (!kunena_showitalictag) {
		$('.italicbutton').remove();
	}

	if (!kunena_showboldtag) {
		$('.boldbutton').remove();
	}

	if (!kunena_showstrikethroughtag) {
		$('.strokebutton').remove();
	}

	if (!kunena_showcolorstag) {
		$('.colors').remove();
	}

	if (!kunena_showsizetag) {
		$('.sizebutton').remove();
	}
});
