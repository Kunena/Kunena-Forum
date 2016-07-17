bbcodeSettings = {
		previewParserPath:	'',
		markupSet: [{className: 'boldbutton',name: Joomla.JText._('COM_KUNENA_EDITOR_BOLD'),key: 'B',openWith: '[b]',closeWith: '[/b]'},{className: 'italicbutton',name: 'Italic',key: 'I',openWith: '[i]',closeWith: '[/i]'},{className: 'underlinebutton',name: 'Underline',key: 'U',openWith: '[u]',closeWith: '[/u]'},{className: 'strokebutton',name: 'Stroke',key: 'T',openWith: '[strike]',closeWith: '[/strike]'},{className: 'subscriptbutton',name: 'Subscript',key: 'T',openWith: '[sub]',closeWith: '[/sub]'},{className: 'supscriptbutton',name: 'Supscript',key: 'T',openWith: '[sup]',closeWith: '[/sup]'},{className: 'sizebutton', name:'Size', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',	dropMenu :[
						{name: Joomla.JText._('COM_KUNENA_EDITOR_SIZE_VERY_VERY_SMALL'), openWith:'[size=1]', 	closeWith:'[/size]' },
						{name: Joomla.JText._('COM_KUNENA_EDITOR_SIZE_VERY_SMALL'), openWith:'[size=2]', 	closeWith:'[/size]' },
						{name: Joomla.JText._('COM_KUNENA_EDITOR_SIZE_SMALL'), openWith:'[size=3]', closeWith:'[/size]' },
						{name: Joomla.JText._('COM_KUNENA_EDITOR_SIZE_NORMAL'), openWith:'[size=4]', closeWith:'[/size]' },
						{name: Joomla.JText._('COM_KUNENA_EDITOR_SIZE_BIG'), openWith:'[size=5]', closeWith:'[/size]' },
						{name: Joomla.JText._('COM_KUNENA_EDITOR_SIZE_SUPER_BIGGER'), openWith:'[size=6]', closeWith:'[/size]' }
						]},{className: 'colors', name: Joomla.JText._('COM_KUNENA_EDITOR_COLORS'), key:'', openWith:'[color=[![Color]!]]', closeWith:'[/color]',dropMenu: [
						{name: Joomla.JText._('COM_KUNENA_EDITOR_COLOR_BLACK'),	openWith:'[color=black]', 	closeWith:'[/color]', className:'col1-1' },
						{name: Joomla.JText._('COM_KUNENA_EDITOR_COLOR_ORANGE'),	openWith:'[color=orange]', 	closeWith:'[/color]', className:'col1-2' },
						{name: Joomla.JText._('COM_KUNENA_EDITOR_COLOR_RED'), 	openWith:'[color=red]', 	closeWith:'[/color]', className:'col1-3' },

						{name: Joomla.JText._('COM_KUNENA_EDITOR_COLOR_BLUE'), 	openWith:'[color=blue]', 	closeWith:'[/color]', className:'col2-1' },
						{name: Joomla.JText._('COM_KUNENA_EDITOR_COLOR_PURPLE'), openWith:'[color=purple]', 	closeWith:'[/color]', className:'col2-2' },
						{name: Joomla.JText._('COM_KUNENA_EDITOR_COLOR_GREEN'), 	openWith:'[color=green]', 	closeWith:'[/color]', className:'col2-3' },

						{name: Joomla.JText._('COM_KUNENA_EDITOR_COLOR_WHITE'), 	openWith:'[color=white]', 	closeWith:'[/color]', className:'col3-1' },
						{name: Joomla.JText._('COM_KUNENA_EDITOR_COLOR_GRAY'), 	openWith:'[color=gray]', 	closeWith:'[/color]', className:'col3-2' }
						]},{separator:'|' },{className: 'bulletedlistbutton',name: Joomla.JText._('COM_KUNENA_EDITOR_UNORDERED_LIST'),openWith: '[ul]\n  [li]',closeWith: '[/li]\n  [li][/li]\n[/ul]'},{className: 'numericlistbutton',name: 'Ordered List',openWith: '[ol]\n  [li]',closeWith: '[/li]\n  [li][/li]\n[/ol]'},{className: 'listitembutton',name: 'Li',openWith: '\n  [li]',closeWith: '[/li]'},{className: 'hrbutton',name: 'HR',openWith: '[hr]'},{className: 'alignleftbutton',name: 'Left',openWith: '[left]',closeWith: '[/left]'},{className: 'centerbutton',name: 'Center',openWith: '[center]',closeWith: '[/center]'},{className: 'alignrightbutton',name: 'Right',openWith: '[right]',closeWith: '[/right]'},{separator:'|' },{className: 'quotebutton',name: 'Quote',openWith: '[quote]',closeWith: '[/quote]'},{className: 'codesimplebutton',name: 'Code',openWith: '[code]',closeWith: '[/code]'},{name:'code', className: 'codemodalboxbutton', beforeInsert:function() {
						jQuery('#code-modal-submit').click(function(event) {
							event.preventDefault();

							jQuery('#modal-code').modal('hide');
						});

						jQuery('#modal-code').modal(
							{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
								dialog.overlay.fadeIn('slow', function () {
									dialog.container.slideDown('slow', function () {
										dialog.data.fadeIn('slow');
									});
								});
							}});
						}
					},{className: 'tablebutton',name: Joomla.JText._('COM_KUNENA_EDITOR_TABLE'),openWith: '[table]\n  [tr]\n   [td][/td]\n   [td][/td]\n  [/tr]',closeWith: '\n  [tr]\n   [td][/td]\n   [td][/td]\n [/tr]\n[/table] \n'},{className: 'spoilerbutton',name: 'Spoiler',openWith: '[spoiler]',closeWith: '[/spoiler]'},{className: 'hiddentextbutton',name: 'Hide',openWith: '[hide]',closeWith: '[/hide]'},{className: 'confidentialbutton',name: 'confidential',openWith: '[confidential]',closeWith: '[/confidential]'},{separator:'|' },{name:'picture', className: 'picturebutton', beforeInsert:function() {
						jQuery('#picture-modal-submit').click(function(event) {
							event.preventDefault();

							jQuery('#modal-picture').modal('hide');
						});

						jQuery('#modal-picture').modal(
							{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
								dialog.overlay.fadeIn('slow', function () {
									dialog.container.slideDown('slow', function () {
										dialog.data.fadeIn('slow');
									});
								});
							}});
						}
					},{name: Joomla.JText._('COM_KUNENA_EDITOR_LINK'), className: 'linkbutton', beforeInsert:function() {
						jQuery('#link-modal-submit').click(function(event) {
							event.preventDefault();

							jQuery('#modal-link').modal('hide');
						});

						jQuery('#modal-link').modal(
							{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
								dialog.overlay.fadeIn('slow', function () {
									dialog.container.slideDown('slow', function () {
										dialog.data.fadeIn('slow');
									});
								});
							}});
						}
					},{separator:'|' },{className: 'ebaybutton',name: Joomla.JText._('COM_KUNENA_EDITOR_EBAY'),key: 'E',openWith: '[ebay]',closeWith: '[/ebay]'},{name:'Video', className: 'videodropdownbutton', dropMenu: [{name: 'videourlprovider', className: 'videourlprovider', beforeInsert:function() {
							jQuery('#videosettings-modal-submit').click(function(event) {
								event.preventDefault();

								jQuery('#modal-video-settings').modal('hide');
							});

							jQuery('#modal-video-settings').modal(
								{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
									dialog.overlay.fadeIn('slow', function () {
										dialog.container.slideDown('slow', function () {
											dialog.data.fadeIn('slow');
										});
									});
								}});
							} },
						{name: Joomla.JText._('COM_KUNENA_EDITOR_VIDEO_PROVIDER_URL'), className: 'videoURLbutton', beforeInsert:function() {
							jQuery('#videourlprovider-modal-submit').click(function(event) {
								event.preventDefault();

								jQuery('#modal-video-urlprovider').modal('hide');
							});

							jQuery('#modal-video-urlprovider').modal(
								{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
									dialog.overlay.fadeIn('slow', function () {
										dialog.container.slideDown('slow', function () {
											dialog.data.fadeIn('slow');
										});
									});
								}});
							} }
						]},{name: Joomla.JText._('COM_KUNENA_EDITOR_MAP'), className: 'mapbutton', beforeInsert:function() {
						jQuery('#map-modal-submit').click(function(event) {
							event.preventDefault();

							jQuery('#modal-map').modal('hide');
						});

						jQuery('#modal-map').modal(
							{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
								dialog.overlay.fadeIn('slow', function () {
									dialog.container.slideDown('slow', function () {
										dialog.data.fadeIn('slow');
									});
								});
							}});
						}
					},{name: Joomla.JText._('COM_KUNENA_EDITOR_POLL_SETTINGS'), className: 'pollbutton', beforeInsert:function() {
						jQuery('#poll-settings-modal-submit').click(function(event) {
							event.preventDefault();

							jQuery('#modal-poll-settings').modal('hide');
						});

						jQuery('#modal-poll-settings').modal(
							{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
								dialog.overlay.fadeIn('slow', function () {
									dialog.container.slideDown('slow', function () {
										dialog.data.fadeIn('slow');
									});
								});
							}});
						}
					},{className: 'tweetbutton',name: Joomla.JText._('COM_KUNENA_EDITOR_TWEET'),openWith: '[tweet]',closeWith: '[/tweet]'},{className: 'soundcloudbutton',name: 'soundcloud',openWith: '[soundcloud]',closeWith: '[/soundcloud]'},{className: 'instagrambutton',name: 'instagram',openWith: '[instagram]',closeWith: '[/instagram]'},{name:'emoticons', className: 'emoticonsbutton', beforeInsert:function() {
						jQuery('#emoticons-modal-submit').click(function(event) {
							event.preventDefault();

							jQuery('#modal-emoticons').modal('hide');
						});

						jQuery('#modal-emoticons').modal(
							{overlayClose:true, autoResize:true, minHeight:500, minWidth:800, onOpen: function (dialog) {
								dialog.overlay.fadeIn('slow', function () {
									dialog.container.slideDown('slow', function () {
										dialog.data.fadeIn('slow');
									});
								});
							}});
						}
					},{separator:'|' },]};
