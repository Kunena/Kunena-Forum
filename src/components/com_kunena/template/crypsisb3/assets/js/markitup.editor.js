bbcodeSettings = {
	previewParserPath: '',
	markupSet: [{
		className: 'boldbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_BOLD'),
		key: 'B',
		openWith: '[b]',
		closeWith: '[/b]'
	}, {
		className: 'italicbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_ITALIC'),
		key: 'I',
		openWith: '[i]',
		closeWith: '[/i]'
	}, {
		className: 'underlinebutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_UNDERL'),
		key: 'U',
		openWith: '[u]',
		closeWith: '[/u]'
	}, {
		className: 'strokebutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_STRIKE'),
		key: 'T',
		openWith: '[strike]',
		closeWith: '[/strike]'
	}, {
		className: 'subscriptbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_SUB'),
		key: 'T',
		openWith: '[sub]',
		closeWith: '[/sub]'
	}, {
		className: 'supscriptbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_SUP'),
		key: 'T',
		openWith: '[sup]',
		closeWith: '[/sup]'
	}, {
		className: 'sizebutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_FONTSIZE_SELECTION'),
		key: 'S',
		openWith: '[size=[![Text size]!]]',
		closeWith: '[/size]',
		dropMenu: [
			{name: 'Very very small', openWith: '[size=1]', closeWith: '[/size]'},
			{name: 'Very small', openWith: '[size=2]', closeWith: '[/size]'},
			{name: 'Small', openWith: '[size=3]', closeWith: '[/size]'},
			{name: 'Normal', openWith: '[size=4]', closeWith: '[/size]'},
			{name: 'Big', openWith: '[size=5]', closeWith: '[/size]'},
			{name: 'Bigger', openWith: '[size=6]', closeWith: '[/size]'}
		]
	}, {
		className: 'colors', name: Joomla.JText._('COM_KUNENA_EDITOR_COLORS'), key: '', openWith: '[color=[![Color]!]]', closeWith: '[/color]', dropMenu: [
			{name: 'Black', openWith: '[color=black]', closeWith: '[/color]', className: 'col1-1'},
			{name: 'Orange', openWith: '[color=orange]', closeWith: '[/color]', className: 'col1-2'},
			{name: 'Red', openWith: '[color=red]', closeWith: '[/color]', className: 'col1-3'},

			{name: 'Blue', openWith: '[color=blue]', closeWith: '[/color]', className: 'col2-1'},
			{name: 'Purple', openWith: '[color=purple]', closeWith: '[/color]', className: 'col2-2'},
			{name: 'Green', openWith: '[color=green]', closeWith: '[/color]', className: 'col2-3'},

			{name: 'White', openWith: '[color=white]', closeWith: '[/color]', className: 'col3-1'},
			{name: 'Gray', openWith: '[color=gray]', closeWith: '[/color]', className: 'col3-2'}
		]
	}, {separator: '|'}, {
		className: 'bulletedlistbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_UNORDERED_LIST'),
		openWith: '[ul]\n  [li]',
		closeWith: '[/li]\n  [li][/li]\n[/ul]'
	}, {
		className: 'numericlistbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_OLIST'),
		openWith: '[ol]\n  [li]',
		closeWith: '[/li]\n  [li][/li]\n[/ol]'
	}, {
		className: 'listitembutton',
		name: 'Li',
		openWith: '\n  [li]',
		closeWith: '[/li]'
	}, {className: 'hrbutton', name: Joomla.JText._('COM_KUNENA_EDITOR_HR'), openWith: '[hr]'}, {
		className: 'alignleftbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_LEFT'),
		openWith: '[left]',
		closeWith: '[/left]'
	}, {
		className: 'centerbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_CENTER'),
		openWith: '[center]',
		closeWith: '[/center]'
	}, {
		className: 'alignrightbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_RIGHT'),
		openWith: '[right]',
		closeWith: '[/right]'
	}, {separator: '|'}, {
		className: 'quotebutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_QUOTE'),
		openWith: '[quote]',
		closeWith: '[/quote]'
	}, {className: 'codesimplebutton', name: Joomla.JText._('COM_KUNENA_EDITOR_CODE'), openWith: '[code]', closeWith: '[/code]'}, {
		name: 'code', className: 'codemodalboxbutton', beforeInsert: function () {
			jQuery('#code-modal-submit').click(function (event) {
				event.preventDefault();

				jQuery('#modal-code').modal('hide');
			});

			jQuery('#modal-code').modal(
				{
					overlayClose: true, autoResize: true, minHeight: 500, minWidth: 800, onOpen: function (dialog) {
						dialog.overlay.fadeIn('slow', function () {
							dialog.container.slideDown('slow', function () {
								dialog.data.fadeIn('slow');
							});
						});
					}
				});
		}
	}, {
		className: 'tablebutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_TABLE'),
		openWith: '[table]\n  [tr]\n   [td][/td]\n   [td][/td]\n  [/tr]',
		closeWith: '\n  [tr]\n   [td][/td]\n   [td][/td]\n [/tr]\n[/table] \n'
	}, {
		className: 'spoilerbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_SPOILER'),
		openWith: '[spoiler]',
		closeWith: '[/spoiler]'
	}, {
		className: 'hiddentextbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_HIDE'),
		openWith: '[hide]',
		closeWith: '[/hide]'
	}, {
		className: 'confidentialbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_CONFIDENTIAL'),
		openWith: '[confidential]',
		closeWith: '[/confidential]'
	}, {separator: '|'}, {
		name: Joomla.JText._('COM_KUNENA_EDITOR_IMAGELINK'), className: 'picturebutton', beforeInsert: function () {
			jQuery('#picture-modal-submit').click(function (event) {
				event.preventDefault();

				jQuery('#modal-picture').modal('hide');
			});

			jQuery('#modal-picture').modal(
				{
					overlayClose: true, autoResize: true, minHeight: 500, minWidth: 800, onOpen: function (dialog) {
						dialog.overlay.fadeIn('slow', function () {
							dialog.container.slideDown('slow', function () {
								dialog.data.fadeIn('slow');
							});
						});
					}
				});
		}
	}, {
		name: Joomla.JText._('COM_KUNENA_EDITOR_LINK'), className: 'linkbutton', beforeInsert: function () {
			jQuery('#link-modal-submit').click(function (event) {
				event.preventDefault();

				jQuery('#modal-link').modal('hide');
			});

			jQuery('#modal-link').modal(
				{
					overlayClose: true, autoResize: true, minHeight: 500, minWidth: 800, onOpen: function (dialog) {
						dialog.overlay.fadeIn('slow', function () {
							dialog.container.slideDown('slow', function () {
								dialog.data.fadeIn('slow');
							});
						});
					}
				});
		}
	}, {separator: '|'}, {className: 'ebaybutton', name: Joomla.JText._('COM_KUNENA_EDITOR_EBAY'), key: 'E', openWith: '[ebay]', closeWith: '[/ebay]'}, {
		name: 'Video', className: 'videodropdownbutton', dropMenu: [{
			name: 'Provider', className: 'videourlprovider', beforeInsert: function () {
				jQuery('#videosettings-modal-submit').click(function (event) {
					event.preventDefault();

					jQuery('#modal-video-settings').modal('hide');
				});

				jQuery('#modal-video-settings').modal(
					{
						overlayClose: true, autoResize: true, minHeight: 500, minWidth: 800, onOpen: function (dialog) {
							dialog.overlay.fadeIn('slow', function () {
								dialog.container.slideDown('slow', function () {
									dialog.data.fadeIn('slow');
								});
							});
						}
					});
			}
		},
			{
				name: Joomla.JText._('COM_KUNENA_EDITOR_VIDEO'), className: 'videoURLbutton', beforeInsert: function () {
					jQuery('#videourlprovider-modal-submit').click(function (event) {
						event.preventDefault();

						jQuery('#modal-video-urlprovider').modal('hide');
					});

					jQuery('#modal-video-urlprovider').modal(
						{
							overlayClose: true,
							autoResize: true,
							minHeight: 500,
							minWidth: 800,
							onOpen: function (dialog) {
								dialog.overlay.fadeIn('slow', function () {
									dialog.container.slideDown('slow', function () {
										dialog.data.fadeIn('slow');
									});
								});
							}
						});
				}
			}
		]
	}, {
		name: Joomla.JText._('COM_KUNENA_EDITOR_MAP'), className: 'mapbutton', beforeInsert: function () {
			jQuery('#map-modal-submit').click(function (event) {
				event.preventDefault();

				jQuery('#modal-map').modal('hide');
			});

			jQuery('#modal-map').modal(
				{
					overlayClose: true, autoResize: true, minHeight: 500, minWidth: 800, onOpen: function (dialog) {
						dialog.overlay.fadeIn('slow', function () {
							dialog.container.slideDown('slow', function () {
								dialog.data.fadeIn('slow');
							});
						});
					}
				});
		}
	}, {
		name: Joomla.JText._('COM_KUNENA_EDITOR_POLL_SETTINGS'), className: 'pollbutton', beforeInsert: function () {
			var allow_edit_poll = Joomla.getOptions('com_kunena.allow_edit_poll');

			jQuery('#poll-settings-modal-submit').click(function (event) {
				event.preventDefault();

				jQuery('#modal-poll-settings').modal('hide');
			});

			if (allow_edit_poll){
				jQuery('#modal-poll-settings').modal(
				{
					overlayClose: true, autoResize: true, minHeight: 500, minWidth: 800, onOpen: function (dialog) {
						dialog.overlay.fadeIn('slow', function () {
							dialog.container.slideDown('slow', function () {
								dialog.data.fadeIn('slow');
							});
						});
					}

				});
			}else {
				jQuery('#modal-poll-not-allowed-to-edit').modal(
				{
					overlayClose: true, autoResize: true, minHeight: 500, minWidth: 800, onOpen: function (dialog) {
						dialog.overlay.fadeIn('slow', function () {
							dialog.container.slideDown('slow', function () {
								dialog.data.fadeIn('slow');
							});
						});
					}
				});
			}
		}
	}, {
		className: 'tweetbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_TWEET'),
		openWith: '[tweet]',
		closeWith: '[/tweet]'
	}, {
		className: 'soundcloudbutton',
		name: Joomla.JText._('COM_KUNENA_EDITOR_SOUNDCLOUD'),
		openWith: '[soundcloud]',
		closeWith: '[/soundcloud]'
	}, {className: 'instagrambutton', name: Joomla.JText._('COM_KUNENA_EDITOR_INSTAGRAM'), openWith: '[instagram]', closeWith: '[/instagram]'}, {
		name: Joomla.JText._('COM_KUNENA_EDITOR_EMOTICONS'), className: 'emoticonsbutton', beforeInsert: function () {
			jQuery('#emoticons-modal-submit').click(function (event) {
				event.preventDefault();

				jQuery('#modal-emoticons').modal('hide');
			});

			jQuery('#modal-emoticons').modal(
				{
					overlayClose: true, autoResize: true, minHeight: 500, minWidth: 800, onOpen: function (dialog) {
						dialog.overlay.fadeIn('slow', function () {
							dialog.container.slideDown('slow', function () {
								dialog.data.fadeIn('slow');
							});
						});
					}
				});
		}
	}, {separator: '|'},]
};