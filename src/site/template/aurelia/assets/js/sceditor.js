/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
 
jQuery(document).ready(function ($) {
	if (Joomla.getOptions('com_kunena.editor_type') == 'sceditor') {
		var kunenaCmd = {
			align: ['left', 'center', 'right', 'justify'],
			fsStr: ['xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large'],
			fSize: [9, 12, 15, 17, 23, 31],
			video: {
				'Dailymotion': {
					'match': /(dailymotion\.com\/video\/|dai\.ly\/)([^\/]+)/,
					'url': '//www.dailymotion.com/embed/video/',
					'html': '<iframe frameborder="0" width="480" height="270" src="{url}" data-kunena-vt="{type}" data-kunena-vsrc="{src}"></iframe>'
				},
				'Facebook': {
					'match': /facebook\.com\/(?:photo.php\?v=|video\/video.php\?v=|video\/embed\?video_id=|v\/?)(\d+)/,
					'url': 'https://www.facebook.com/video/embed?video_id=',
					'html': '<iframe src="{url}" width="625" height="350" frameborder="0" data-kunena-vt="{type}" data-kunena-vsrc="{src}"></iframe>'
				},
				'Liveleak': {
					'match': /liveleak\.com\/(?:view\?[a-z]=)([^\/]+)/,
					'url': 'http://www.liveleak.com/ll_embed?i=',
					'html': '<iframe width="500" height="300" src="{url}" frameborder="0" data-kunena-vt="{type}" data-kunena-vsrc="{src}"></iframe>'
				},
				'MetaCafe': {
					'match': /metacafe\.com\/watch\/([^\/]+)/,
					'url': 'http://www.metacafe.com/embed/',
					'html': '<iframe src="{url}" width="440" height="248" frameborder=0 data-kunena-vt="{type}" data-kunena-vsrc="{src}"></iframe>'
				},
				'Mixer': {
					'match': /mixer\.com\/([^\/]+)/,
					'url': '//mixer.com/embed/player/',
					'html': '<iframe allowfullscreen="true" src="{url}" width="620" height="349" frameborder="0" data-kunena-vt="{type}" data-kunena-vsrc="{src}"></iframe>'
				},
				'Vimeo': {
					'match': /vimeo.com\/(\d+)($|\/)/,
					'url': '//player.vimeo.com/video/',
					'html': '<iframe src="{url}" width="500" height="281" frameborder="0" data-kunena-vt="{type}" data-kunena-vsrc="{src}"></iframe>'
				},
				'Youtube': {
					'match': /(?:v=|v\/|embed\/|youtu\.be\/)(.{11})/,
					'url': '//www.youtube-nocookie.com/embed/',
					'html': '<iframe width="560" height="315" src="{url}" frameborder="0" data-kunena-vt="{type}" data-kunena-vsrc="{src}"></iframe>'
				},
				'Twitch': {
					'match': /twitch\.tv\/(?:[\w+_-]+)\/v\/(\d+)/,
					'url': '//player.twitch.tv/?video=v',
					'html': '<iframe src="{url}" frameborder="0" scrolling="no" height="378" width="620" data-kunena-vt="{type}" data-kunena-vsrc="{src}"></iframe>'
				}
			}
		};

		// Add bbcode soundcloud
		sceditor.formats.bbcode.set('soundcloud', {
			format: function (element, content) {
				if (jQuery(element).data('sceditor-emoticon'))
					return content;
	
				var url = jQuery(element).attr('src'),
					width = jQuery(element).attr('width'),
					height = jQuery(element).attr('height'),
					align = jQuery(element).data('scealign');
	
				var attrs = width !== undefined && height !== undefined && width > 0 && height > 0
					? '=' + width + 'x' + height
					: ''
				;
	
				if (align === 'left' || align === 'right')
					attrs += ' align='+align
	
				return '[soundcloud' + attrs + ']' + url + '[/soundcloud]';
			},
			html: function (token, attrs, content) {
				var	width, height, match,
					align = attrs.align,
					attribs = '';
	
				// handle [img=340x240]url[/img]
				if (attrs.defaultattr) {
					match = attrs.defaultattr.split(/x/i);
	
					width  = match[0];
					height = (match.length === 2 ? match[1] : match[0]);
	
					if (width !== undefined && height !== undefined && width > 0 && height > 0) {
						attribs +=
							' width="' + sceditor.escapeEntities(width, true) + '"' +
							' height="' + sceditor.escapeEntities(height, true) + '"';
					}
				}
	
				if (align === 'left' || align === 'right')
					attribs += ' style="float: ' + align + '" data-scealign="' + align + '"';
	
				return '<img alt="" ' + attribs +
					' src="' + sceditor.escapeUriScheme(content) + '" />';
			}
		})
	
		sceditor.command.set('soundcloud', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = jQuery(
					'<div>' +
					'<div>' +
					'<label for="height">Soundcloud id:</label> ' +
					'<input type="text" id="soundcloud_id" size="10" />' +
					'</div>' +
					'<div>' +
					'<input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" />' +
					'</div>' +
					'</div>'
				);
	
				$content.find('.button').on('click', function (e) {
					var soundcloud_id = $content.find('#soundcloud_id').val();
	
					if (soundcloud_id)
						editor.insert('[soundcloud]' + soundcloud_id + '[/soundcloud]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertmap', $content.get(0));
			},
			exec: function (caller) {
				sceditor.command.get('soundcloud')._dropDown(this, caller);
			},
			txtExec: function (caller) {
				sceditor.command.get('soundcloud')._dropDown(this, caller);
			},
			tooltip: Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_SOUNDCLOUD'),
		});
	
	
		// Add bbcode ebay
		sceditor.formats.bbcode.set('ebay', {
			format: function (element, content) {
				if (jQuery(element).data('sceditor-emoticon'))
					return content;
	
				var url = jQuery(element).attr('src'),
					width = jQuery(element).attr('width'),
					height = jQuery(element).attr('height'),
					align = jQuery(element).data('scealign');
	
				var attrs = width !== undefined && height !== undefined && width > 0 && height > 0
					? '=' + width + 'x' + height
					: ''
				;
	
				if (align === 'left' || align === 'right')
					attrs += ' align='+align
	
				return '[ebay' + attrs + ']' + url + '[/ebay]';
			},
			html: function (token, attrs, content) {
				var	width, height, match,
					align = attrs.align,
					attribs = '';
	
				// handle [img=340x240]url[/img]
				if (attrs.defaultattr) {
					match = attrs.defaultattr.split(/x/i);
	
					width  = match[0];
					height = (match.length === 2 ? match[1] : match[0]);
	
					if (width !== undefined && height !== undefined && width > 0 && height > 0) {
						attribs +=
							' width="' + sceditor.escapeEntities(width, true) + '"' +
							' height="' + sceditor.escapeEntities(height, true) + '"';
					}
				}
	
				if (align === 'left' || align === 'right')
					attribs += ' style="float: ' + align + '" data-scealign="' + align + '"';
	
				return '<img alt="" ' + attribs +
					' src="' + sceditor.escapeUriScheme(content) + '" />';
			}
		})
	
		sceditor.command.set('ebay', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = jQuery(
					'<div>' +
					'<div>' +
					'<label for="height">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_EBAY_ITEM_ID') + '</label> ' +
					'<input type="text" id="ebay_id" size="10" />' +
					'</div>' +
					'<div>' +
					'<input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" />' +
					'</div>' +
					'</div>'
				);
	
				$content.find('.button').on('click', function (e) {
					var ebay_id = $content.find('#ebay_id').val();
	
					if (ebay_id)
						editor.insert('[ebay]' + ebay_id + '[/ebay]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertmap', $content.get(0));
			},
			exec: function (caller) {
				sceditor.command.get('ebay')._dropDown(this, caller);
			},
			txtExec: function (caller) {
				sceditor.command.get('ebay')._dropDown(this, caller);
			},
			tooltip: Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_EBAY'),
		});
	
		// Add bbcode instagram
		sceditor.formats.bbcode.set('instagram', {
			format: function (element, content) {
				if (jQuery(element).data('sceditor-emoticon'))
					return content;
	
				var url = jQuery(element).attr('src'),
					width = jQuery(element).attr('width'),
					height = jQuery(element).attr('height'),
					align = jQuery(element).data('scealign');
	
				var attrs = width !== undefined && height !== undefined && width > 0 && height > 0
					? '=' + width + 'x' + height
					: ''
				;
	
				if (align === 'left' || align === 'right')
					attrs += ' align='+align
	
				return '[instagram' + attrs + ']' + url + '[/instagram]';
			},
			html: function (token, attrs, content) {
				var	width, height, match,
					align = attrs.align,
					attribs = '';
	
				// handle [img=340x240]url[/img]
				if (attrs.defaultattr) {
					match = attrs.defaultattr.split(/x/i);
	
					width  = match[0];
					height = (match.length === 2 ? match[1] : match[0]);
	
					if (width !== undefined && height !== undefined && width > 0 && height > 0) {
						attribs +=
							' width="' + sceditor.escapeEntities(width, true) + '"' +
							' height="' + sceditor.escapeEntities(height, true) + '"';
					}
				}
	
				if (align === 'left' || align === 'right')
					attribs += ' style="float: ' + align + '" data-scealign="' + align + '"';
	
				return '<img alt="" ' + attribs +
					' src="' + sceditor.escapeUriScheme(content) + '" />';
			}
		})
	
		sceditor.command.set('instagram', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = jQuery(
					'<div>' +
					'<div>' +
					'<label for="height">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_INSTAGRAM_ID') + '</label> ' +
					'<input type="text" id="instagram_id" size="10" />' +
					'</div>' +
					'<div>' +
					'<input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" />' +
					'</div>' +
					'</div>'
				);
	
				$content.find('.button').on('click', function (e) {
					var instagram_id = $content.find('#instagram_id').val();
	
					if (instagram_id)
						editor.insert('[instagram]' + instagram_id + '[/instagram]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertmap', $content.get(0));
			},
			exec: function (caller) {
				sceditor.command.get('instagram')._dropDown(this, caller);
			},
			txtExec: function (caller) {
				sceditor.command.get('instagram')._dropDown(this, caller);
			},
			tooltip: Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_INSTAGRAM'),
		});
	
		// Add bbcode twitter
		sceditor.formats.bbcode.set('twitter', {
			format: function (element, content) {
				if (jQuery(element).data('sceditor-emoticon'))
					return content;
	
				var url = jQuery(element).attr('src'),
					width = jQuery(element).attr('width'),
					height = jQuery(element).attr('height'),
					align = jQuery(element).data('scealign');
	
				var attrs = width !== undefined && height !== undefined && width > 0 && height > 0
					? '=' + width + 'x' + height
					: ''
				;
	
				if (align === 'left' || align === 'right')
					attrs += ' align='+align
	
				return '[tweet' + attrs + ']' + url + '[/tweet]';
			},
			html: function (token, attrs, content) {
				var	width, height, match,
					align = attrs.align,
					attribs = '';
	
				// handle [img=340x240]url[/img]
				if (attrs.defaultattr) {
					match = attrs.defaultattr.split(/x/i);
	
					width  = match[0];
					height = (match.length === 2 ? match[1] : match[0]);
	
					if (width !== undefined && height !== undefined && width > 0 && height > 0) {
						attribs +=
							' width="' + sceditor.escapeEntities(width, true) + '"' +
							' height="' + sceditor.escapeEntities(height, true) + '"';
					}
				}
	
				if (align === 'left' || align === 'right')
					attribs += ' style="float: ' + align + '" data-scealign="' + align + '"';
	
				return '<img alt="" ' + attribs +
					' src="' + sceditor.escapeUriScheme(content) + '" />';
			}
		})
	
		sceditor.command.set('twitter', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = jQuery(
					'<div>' +
					'<div>' +
					'<label for="height">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_TWITTER_ID') + '</label> ' +
					'<input type="text" id="tweet_id" size="10" />' +
					'</div>' +
					'<div>' +
					'<input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" />' +
					'</div>' +
					'</div>'
				);
	
				$content.find('.button').on('click', function (e) {
					var tweet_id = $content.find('#tweet_id').val();
	
					if (tweet_id)
						editor.insert('[tweet]' + tweet_id + '[/tweet]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertmap', $content.get(0));
			},
			exec: function (caller) {
				sceditor.command.get('twitter')._dropDown(this, caller);
			},
			txtExec: function (caller) {
				sceditor.command.get('twitter')._dropDown(this, caller);
			},
			tooltip: Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_TWITTER'),
		});
	
		// Add bbcode maps
		sceditor.formats.bbcode.set('map', {
			format: function (element, content) {
				if (jQuery(element).data('sceditor-emoticon'))
					return content;
	
				var url = jQuery(element).attr('src'),
					width = jQuery(element).attr('width'),
					height = jQuery(element).attr('height'),
					align = jQuery(element).data('scealign');
	
				var attrs = width !== undefined && height !== undefined && width > 0 && height > 0
					? '=' + width + 'x' + height
					: ''
				;
	
				if (align === 'left' || align === 'right')
					attrs += ' align='+align
	
				return '[map' + attrs + ']' + url + '[/map]';
			},
			html: function (token, attrs, content) {
				var	width, height, match,
					align = attrs.align,
					attribs = '';
	
				// handle [img=340x240]url[/img]
				if (attrs.defaultattr) {
					match = attrs.defaultattr.split(/x/i);
	
					width  = match[0];
					height = (match.length === 2 ? match[1] : match[0]);
	
					if (width !== undefined && height !== undefined && width > 0 && height > 0) {
						attribs +=
							' width="' + sceditor.escapeEntities(width, true) + '"' +
							' height="' + sceditor.escapeEntities(height, true) + '"';
					}
				}
	
				if (align === 'left' || align === 'right')
					attribs += ' style="float: ' + align + '" data-scealign="' + align + '"';
	
				return '<img alt="" ' + attribs +
					' src="' + sceditor.escapeUriScheme(content) + '" />';
			}
		})
	
		sceditor.command.set('map', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = jQuery(
					'<div>' +
					'<div>' +
					'<label for="map">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE') + '</label> ' +
					'<select name="type" id="type-select">' +
					'<option value="hybrid">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_HYBRID') + '</option>' +
					'<option value="roadmap">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_ROADMAP') + '</option>' +
					'<option value="terrain">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_TERRAIN') + '</option>' +
					'<option value="satelite">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_SATELITE') + '</option>' +
					'</select>' +
					'</div>' +
					'<div>' +
					'<label for="width">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_ZOOM_LEVEL') + '</label> ' +
					'<select name="zoom" id="zoom-select">' +
					'<option value="2">2</option>' +
					'<option value="4">4</option>' +
					'<option value="8">8</option>' +
					'<option value="10">10</option>' +
					'<option value="12">12</option>' +
					'<option value="14">14</option>' +
					'<option value="16">16</option>' +
					'<option value="18">18</option>' +
					'</select>' +
					'</div>' +
					'<div>' +
					'<label for="height">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_CITY') + '</label> ' +
					'<input type="text" id="city" size="10" />' +
					'</div>' +
					'<div>' +
					'<input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" />' +
					'</div>' +
					'</div>'
				);
	
				$content.find('.button').on('click', function (e) {
					var city = $content.find('#city').val(),
						width = $content.find('#width').val(),
						height = $content.find('#height').val()
					;
	
					var attrs = width !== undefined && height !== undefined && width > 0 && height > 0
						? '=' + width + 'x' + height
						: ''
					;
	
					if (city)
						editor.insert('[map' + attrs + ']' + city + '[/map]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertmap', $content.get(0));
			},
			exec: function (caller) {
				sceditor.command.get('map')._dropDown(this, caller);
			},
			txtExec: function (caller) {
				sceditor.command.get('map')._dropDown(this, caller);
			},
			tooltip: Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP'),
		});
	
		// Add video command
		sceditor.formats.bbcode.set('video', {
			allowsEmpty: true,
			allowedChildren: ['#', '#newline'],
			tags: {
				iframe: {
					'data-kunena-vt': null
				}
			},
			format: function ($element, content) {
				return '[video=' + $($element).data('kunena-vt') + ']' + $($element).data('kunena-vsrc') + '[/video]';
			},
			html: function (token, attrs, content) {
				var params = kunenaCmd.video[Object.keys(kunenaCmd.video).find(key => key.toLowerCase() === attrs.defaultattr)];
				var url;
	
				if (url) {
					return params['html'].replace('{url}', url).replace('{src}', content).replace('{type}', attrs.defaultattr);
				}
				return sceditor.escapeEntities(token.val + content + (token.closing ? token.closing.val : ''));
			}
		});
	
		sceditor.command.set('video', {
			_dropDown: function (editor, caller) {
				var $content, videourl, videotype, videoOpts;
	
				jQuery.each(kunenaCmd.video, function (provider, data) {
					videoOpts += '<option value="' + provider.toLowerCase() + '">' + editor._(provider) + '</option>';
				});
				$content = $(
					'<div>' +
					'<div>' +
					'<label for="videotype">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_TYPE') + '</label> ' +
					'<select id="videotype">' + videoOpts + '</select>' +
					'</div>' +
					'<div>' +
					'<label for="link">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_URL') + '</label> ' +
					'<input type="text" id="videourl" placeholder="https://" />' +
					'</div>' +
					'<div>' +
					'<label for="link">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_SIZE') + '</label> ' +
					'<input type="text" id="videosize" />' +
					'</div>' +
					'<div>' +
					'<label for="link">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_HEIGHT') + '</label> ' +
					'<input type="text" id="videoheight" />' +
					'</div>' +
					'<div>' +
					'<label for="link">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_WIDTH') + '</label> ' +
					'<input type="text" id="videowifth" />' +
					'</div>' +
					'<div><input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" /></div>' +
					'</div>'
				);
	
				$content.find('.button').on('click', function (e) {
					videourl = $content.find('#videourl').val();
					videotype = $content.find('#videotype').val();
	
					if (videourl !== '' && videourl !== 'http://')
						editor.insert('[video=' + videotype + ']' + videourl + '[/video]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertvideo', $content.get(0));
			},
			exec: function (caller) {
				sceditor.command.get('video')._dropDown(this, caller);
			},
			txtExec: function (caller) {
				sceditor.command.get('video')._dropDown(this, caller);
			},
			tooltip: Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO')
		});

		// Add poll command
		sceditor.formats.bbcode.set('poll', {
			allowsEmpty: true,
			allowedChildren: ['#', '#newline'],
			tags: {
				iframe: {
					'data-kunena-vt': null
				}
			},
			format: function ($element, content) {
				return '[video=' + $($element).data('kunena-vt') + ']' + $($element).data('kunena-vsrc') + '[/video]';
			},
			html: function (token, attrs, content) {
				var params = kunenaCmd.video[Object.keys(kunenaCmd.video).find(key => key.toLowerCase() === attrs.defaultattr)];
				var url;
	
				if (url) {
					return params['html'].replace('{url}', url).replace('{src}', content).replace('{type}', attrs.defaultattr);
				}
				return sceditor.escapeEntities(token.val + content + (token.closing ? token.closing.val : ''));
			}
		});
	
		sceditor.command.set('poll', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = $(
					'<div>' +
					'<div>' +
					'<label for="polltitle">' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_POLL_TITLE') + '</label> ' +
					'<input type="text" id="polltitle" />' +
					'</div>' +
					'<div>' +
					'<button type="button" class="btn btn-primary btn-sm" name="addpolloption">' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_BUTTON_ADD_POLL_OPTION') + '</button> ' +
					'</div>' +
					'<div>' +
					'<button type="button" class="btn btn-primary btn-sm" name="removepolloption">' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_BUTTON_REMOVE_POLL_OPTION') + '</button> ' +
					'</div>' +
					'<div>' +
					'<label for="polllifespan">' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_POLL_LIFE_SPAN') + '</label> ' +
					'<input type="text" id="polllifespan" />' +
					'</div>' +
					'<div><input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" /></div>' +
					'</div>'
				);
	
				$content.find('.button').on('click', function (e) {
					var polltitle = $content.find('#polltitle').val();
	
					if (polltitle)
						editor.insert('[tweet]' + tweet_id + '[/tweet]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertpoll', $content.get(0));
			},
			exec: function (caller) {
				sceditor.command.get('poll')._dropDown(this, caller);
			},
			txtExec: function (caller) {
				sceditor.command.get('poll')._dropDown(this, caller);
			},
			tooltip: Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_POLL')
		});

		var textarea = document.getElementById('message');
		var toolbar_buttons = '';
		if(Joomla.getOptions('com_kunena.template_editor_buttons_configuration') !== undefined)
		{
			// TODO: need to change the values(bold, italic) from template parameters to be handled here
			toolbar_buttons = 'bold,italic,underline,strike,subscript,superscript|left,center,right,justify|font,size,color,removeformat|cut,copy,paste|bulletlist,orderedlist|table,code,quote,image,link,unlink,emoticon,video,map,twitter,instagram,ebay,soundcloud,poll|source';
		}
		else
		{
			toolbar_buttons = 'bold,italic,underline,strike,subscript,superscript|left,center,right,justify|font,size,color,removeformat|cut,copy,paste|bulletlist,orderedlist|table,code,quote,image,link,unlink,emoticon,video,map,twitter,instagram,ebay,soundcloud,poll|source';
		}
	
		var emoticons = Joomla.getOptions('com_kunena.ckeditor_emoticons');
		var obj = jQuery.parseJSON( emoticons );
		var list_emoticons = [];
	
		jQuery.each(obj, function( index, value ) {
			list_emoticons.push(value);
		});
	
		sceditor.create(textarea, {
			format: 'bbcode',
			toolbar: toolbar_buttons,
			style: Joomla.getOptions('com_kunena.sceditor_style_path'),
			emoticonsRoot: Joomla.getOptions('com_kunena.root_path')+'/media/kunena/emoticons/',
			/*emoticons: {
				// Emoticons to be included in the dropdown
				dropdown: list_emoticons,
				// Emoticons to be included in the more section
				more: {
					':alien:': 'emoticons/alien.png',
					':blink:': 'emoticons/blink.png'
				},
				// Emoticons that are not shown in the dropdown but will still
				// be converted. Can be used for things like aliases
				hidden: {
					':aliasforalien:': 'emoticons/alien.png',
					':aliasforblink:': 'emoticons/blink.png'
				}
			}*/
		});

		if (sceditor.instance(textarea).val().length > 0)
		{
			$('#form_submit_button').removeAttr("disabled");
		}

		sceditor.instance(textarea).bind('valuechanged', function(e) {
			if (sceditor.instance(textarea).val().length > 0)
			{
				$('#form_submit_button').removeAttr("disabled");
			}

			if (sceditor.instance(textarea).val().length == 0 && $('#form_submit_button').disabled === undefined)
			{
				$('#form_submit_button').prop("disabled", true);
			}
		});
	}
});