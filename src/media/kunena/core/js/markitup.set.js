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
		const codetype = $("#kcodetype option:selected").val();

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
		const modalcity = $('#modal-map-city').val();
		const modaltype = $('#modal-map-type').val();
		const modalzoom = $('#modal-map-zoomlevel').val();
		let type = '';
		let zoom = '';

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
		const modalpictureurl = $('#modal-picture-url').val();
		const modalpicturesize = $("#kpicture-size-list-modal option:selected").val();
		const modalpicturealt = $('#modal-picture-alt').val();

		let size = '';
		if (modalpicturesize.length > 0) {
			size = 'size=' + modalpicturesize;
		}

		let alt = '';
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
		const modallinkurl = $('#modal-link-url').val();
		const modallinktext = $('#modal-link-text').val();

		let text = '';
		if (modallinktext.length > 0) {
			text = modallinktext;
		}
		else {
			text = modallinkurl;
		}

		if (modallinkurl.length > 0) {
			$.markItUp(
				{
					openWith: '[url=' + modallinkurl + ']' + text,
					closeWith: '[/url]'
				}
			);
		}

		$('#modal-link-url').val('');
		$('#modal-link-text').val('');

		return false;
	});

	// For video settings
	$('#videosettings-modal-submit').click(function () {
		const kvideoprovider = $('#kvideoprovider-modal').val();
		const providerid = $('#modal-video-id').val();
		const videowidth = $('#modal-video-width').val();
		const videoheight = $('#modal-video-height').val();
		const videosize = $('#modal-video-size').val();
		const kvideoproviderlist = $("#kvideoprovider-list-modal option:selected").val();

		let width = '425';
		let height = '344';
		if (videowidth.length > 0 && videoheight.length > 0) {
			width = 'width=' + videowidth;
			height = 'height=' + videoheight;
		} else {
			width = 'width=' + width;
			height = 'height=' + height;
		}

		let size = '';
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
		const providerurl = $('#modal-video-urlprovider-input').val();

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
		const smiley = $(this).attr('alt');

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

	if (!Joomla.getOptions('kunena_showvideotag')) {
		$('.videodropdownbutton').remove();
	}

	if (!Joomla.getOptions('kunena_disemoticons')) {
		$('.emoticonsbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showebaytag')) {
		$('.ebaybutton').remove();
	}

	if (!Joomla.getOptions('kunena_showspoilertag')) {
		$('.spoilerbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showmapstag')) {
		$('.mapbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showtwittertag')) {
		$('.tweetbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showlinktag')) {
		$('.linkbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showpicturetag')) {
		$('.picturebutton').remove();
	}

	if (!Joomla.getOptions('kunena_showhidetag')) {
		$('.hiddentextbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showtabletag')) {
		$('.tablebutton').remove();
	}

	if (!Joomla.getOptions('kunena_showcodetag')) {
		$('.codesimplebutton').remove();
	}

	if (!Joomla.getOptions('kunena_showquotetag')) {
		$('.quotebutton').remove();
	}

	if (!Joomla.getOptions('kunena_showdividertag')) {
		$('.markItUpSeparator').remove();
	}

	if (!Joomla.getOptions('kunena_showinstagramtag')) {
		$('.instagrambutton').remove();
	}

	if (!Joomla.getOptions('kunena_showsoundcloudtag')) {
		$('.soundcloudbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showconfidentialtag')) {
		$('.confidentialbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showhrtag')) {
		$('.hrbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showlistitemtag')) {
		$('.listitembutton').remove();
	}

	if (!Joomla.getOptions('kunena_showsupscripttag')) {
		$('.supscriptbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showsubscripttag')) {
		$('.subscriptbutton').remove();
	}

	if (!Joomla.getOptions('kunena_shownumericlisttag')) {
		$('.numericlistbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showbulletedlisttag')) {
		$('.bulletedlistbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showalignrighttag')) {
		$('.alignrightbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showalignlefttag')) {
		$('.alignleftbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showcentertag')) {
		$('.centerbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showunderlinetag')) {
		$('.underlinebutton').remove();
	}

	if (!Joomla.getOptions('kunena_showitalictag')) {
		$('.italicbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showboldtag')) {
		$('.boldbutton').remove();
	}

	if (!Joomla.getOptions('kunena_showstrikethroughtag')) {
		$('.strokebutton').remove();
	}

	if (!Joomla.getOptions('kunena_showcolorstag')) {
		$('.colors').remove();
	}

	if (!Joomla.getOptions('kunena_showsizetag')) {
		$('.sizebutton').remove();
	}
});
