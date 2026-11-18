/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
 
(function () {
	'use strict';

	// A piece of markup of a dialog of a button: its element.
	var kunenaHtml = function (html) {
		var template = document.createElement('template');

		template.innerHTML = html.trim();

		return template.content.firstElementChild;
	};

	// A JSON answer of the forum.
	var kunenaGetJSON = function (url) {
		return fetch(url, {headers: {Accept: 'application/json'}, credentials: 'same-origin'}).then(function (response) {
			if (!response.ok) {
				throw new Error('HTTP ' + response.status);
			}

			return response.json();
		});
	};

	// The send button of the form.
	var kunenaSubmit = function (enabled) {
		var button = document.getElementById('form_submit_button');

		if (button) {
			button.disabled = !enabled;
		}
	};

	var kunenaInit = function () {
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
					'url': 'https://www.liveleak.com/ll_embed?i=',
					'html': '<iframe width="500" height="300" src="{url}" frameborder="0" data-kunena-vt="{type}" data-kunena-vsrc="{src}"></iframe>'
				},
				'MetaCafe': {
					'match': /metacafe\.com\/watch\/([^\/]+)/,
					'url': 'https://www.metacafe.com/embed/',
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
		
		// Shared by the [soundcloud], [ebay], [instagram] and [tweet] placeholders below: the default attribute is kept in
		// data-kunena-<tag>-defaultattr and each named attribute (only names of letters, digits, _ and -) in data-kunena-<tag>-<name>.
		var kunenaEmbedHtml = function (tag, tooltip, attrs, content) {
			var data = '';

			if (attrs.defaultattr !== undefined)
				data += ' data-kunena-' + tag + '-defaultattr="' + sceditor.escapeEntities(attrs.defaultattr, true) + '"';

			Object.keys(attrs).forEach(function (name) {
				if (name !== 'defaultattr' && /^[a-z0-9_-]+$/i.test(name))
					data += ' data-kunena-' + tag + '-' + name + '="' + sceditor.escapeEntities(attrs[name], true) + '"';
			});

			// The content is the text of the tag, already escaped by the parser.
			return '<div data-kunena-' + tag + '="1"' + data +
				' title="' + sceditor.escapeEntities(Joomla.Text._(tooltip), true) + '"' +
				' style="border: 1px dashed #888; padding: 4px 8px;">' + content + '</div>';
		};

		// Gives back [tag=default name=value ...]content[/tag] from a placeholder made by kunenaEmbedHtml.
		var kunenaEmbedFormat = function (tag, element, content) {
			var prefix = 'data-kunena-' + tag + '-',
				attrs = '';

			if (element.hasAttribute(prefix + 'defaultattr'))
				attrs += '=' + element.getAttribute(prefix + 'defaultattr');

			Array.prototype.forEach.call(element.attributes, function (attr) {
				if (attr.name.indexOf(prefix) === 0 && attr.name !== prefix + 'defaultattr')
					attrs += ' ' + attr.name.substr(prefix.length) + '=' + attr.value;
			});

			return '[' + tag + attrs + ']' + content + '[/' + tag + ']';
		};

		// Add Hidetext
		sceditor.command.set('hidetext', {
			exec: function () {
				this.insert('\n[hidetext] ','[/hidetext]');
			},
			txtExec: ['[hide]', '[/hide]'],
		}),
		// End of Hidetext

		// Add Confidential
		sceditor.command.set('confidential', {
			exec: function () {
				this.insert('\n[confidential] ', '[/confidential]');
			},
			txtExec: ['[confidential]', '[/confidential]'],
			tooltip: Joomla.Text._('COM_KUNENA_EDITOR_CONFIDENTIAL')
		}),
		// End Confidential

		// Add Spoiler
		sceditor.command.set('spoiler', {
			exec: function () {
				this.insert('\n[spoiler] ','[/spoiler]');
			},
			txtExec: ['[spoiler]', '[/spoiler]'],
			tooltip: Joomla.Text._('COM_KUNENA_EDITOR_SPOILER')
		}),
		// End spoiler

		// Add bbcode soundcloud: [soundcloud]url[/soundcloud] is shown as a placeholder with the url, as [map] is.
		sceditor.formats.bbcode.set('soundcloud', {
			tags: {
				div: {
					'data-kunena-soundcloud': null
				}
			},
			isInline: false,
			allowedChildren: ['#', '#newline'],
			format: function (element, content) {
				return kunenaEmbedFormat('soundcloud', element, content);
			},
			html: function (token, attrs, content) {
				return kunenaEmbedHtml('soundcloud', 'COM_KUNENA_SCEDITOR_COMMAND_INSERT_SOUNDCLOUD', attrs, content);
			}
		});
	
		sceditor.command.set('soundcloud', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = kunenaHtml(
					'<div>' +
					'<div>' +
					'<label for="soundcloud_id">Soundcloud id:</label> ' +
					'<input type="text" id="soundcloud_id" size="10" />' +
					'</div>' +
					'<div>' +
					'<input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" />' +
					'</div>' +
					'</div>'
				);
	
				$content.querySelector('.button').addEventListener('click', function (e) {
					var soundcloud_id = $content.querySelector('#soundcloud_id').value;
	
					if (soundcloud_id)
						editor.insert('[soundcloud]' + soundcloud_id + '[/soundcloud]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertmap', $content);
			},
			exec: function (caller) {
				sceditor.command.get('soundcloud')._dropDown(this, caller);
			},
			txtExec: function (caller) {
				sceditor.command.get('soundcloud')._dropDown(this, caller);
			},
			tooltip: Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_SOUNDCLOUD'),
		});
	
		// Add bbcode ebay: [ebay]id[/ebay] is shown as a placeholder with the item id, as [map] is.
		sceditor.formats.bbcode.set('ebay', {
			tags: {
				div: {
					'data-kunena-ebay': null
				}
			},
			isInline: false,
			allowedChildren: ['#', '#newline'],
			format: function (element, content) {
				return kunenaEmbedFormat('ebay', element, content);
			},
			html: function (token, attrs, content) {
				return kunenaEmbedHtml('ebay', 'COM_KUNENA_SCEDITOR_COMMAND_INSERT_EBAY', attrs, content);
			}
		});
	
		sceditor.command.set('ebay', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = kunenaHtml(
					'<div>' +
					'<div>' +
					'<label for="ebay_id">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_EBAY_ITEM_ID') + '</label> ' +
					'<input type="text" id="ebay_id" size="10" />' +
					'</div>' +
					'<div>' +
					'<input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" />' +
					'</div>' +
					'</div>'
				);
	
				$content.querySelector('.button').addEventListener('click', function (e) {
					var ebay_id = $content.querySelector('#ebay_id').value;
	
					if (ebay_id)
						editor.insert('[ebay]' + ebay_id + '[/ebay]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertmap', $content);
			},
			exec: function (caller) {
				sceditor.command.get('ebay')._dropDown(this, caller);
			},
			txtExec: function (caller) {
				sceditor.command.get('ebay')._dropDown(this, caller);
			},
			tooltip: Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_EBAY'),
		});
	
		// Add bbcode instagram: [instagram]url[/instagram] is shown as a placeholder with the url, as [map] is.
		sceditor.formats.bbcode.set('instagram', {
			tags: {
				div: {
					'data-kunena-instagram': null
				}
			},
			isInline: false,
			allowedChildren: ['#', '#newline'],
			format: function (element, content) {
				return kunenaEmbedFormat('instagram', element, content);
			},
			html: function (token, attrs, content) {
				return kunenaEmbedHtml('instagram', 'COM_KUNENA_SCEDITOR_COMMAND_INSERT_INSTAGRAM', attrs, content);
			}
		});
	
		sceditor.command.set('instagram', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = kunenaHtml(
					'<div>' +
					'<div>' +
					'<label for="instagram_id">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_INSTAGRAM_ID') + '</label> ' +
					'<input type="text" id="instagram_id" size="10" />' +
					'</div>' +
					'<div>' +
					'<input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" />' +
					'</div>' +
					'</div>'
				);
	
				$content.querySelector('.button').addEventListener('click', function (e) {
					var instagram_id = $content.querySelector('#instagram_id').value;
	
					if (instagram_id)
						editor.insert('[instagram]' + instagram_id + '[/instagram]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertmap', $content);
			},
			exec: function (caller) {
				sceditor.command.get('instagram')._dropDown(this, caller);
			},
			txtExec: function (caller) {
				sceditor.command.get('instagram')._dropDown(this, caller);
			},
			tooltip: Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_INSTAGRAM'),
		});
	
		// Add bbcode X: [tweet]id[/tweet] (inserted by the x_social button) is shown as a placeholder with the id, as [map] is.
		sceditor.formats.bbcode.set('tweet', {
			tags: {
				div: {
					'data-kunena-tweet': null
				}
			},
			isInline: false,
			allowedChildren: ['#', '#newline'],
			format: function (element, content) {
				return kunenaEmbedFormat('tweet', element, content);
			},
			html: function (token, attrs, content) {
				return kunenaEmbedHtml('tweet', 'COM_KUNENA_SCEDITOR_COMMAND_INSERT_X_SOCIAL', attrs, content);
			}
		});
	
		sceditor.command.set('x_social', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = kunenaHtml(
					'<div>' +
					'<div>' +
					'<label for="tweet_id">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_X_SOCIAL') + '</label> ' +
					'<input type="text" id="tweet_id" size="10" />' +
					'</div>' +
					'<div>' +
					'<input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" />' +
					'</div>' +
					'</div>'
				);
	
				$content.querySelector('.button').addEventListener('click', function (e) {
					var tweet_id = $content.querySelector('#tweet_id').value;
	
					if (tweet_id)
						editor.insert('[tweet]' + tweet_id + '[/tweet]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'inserttweet', $content);
			},
			exec: function (caller) {
				sceditor.command.get('x_social')._dropDown(this, caller);
			},
			txtExec: function (caller) {
				sceditor.command.get('x_social')._dropDown(this, caller);
			},
			tooltip: Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_X_SOCIAL'),
		});
	
		// Add bbcode maps: [map type=.. zoom=.. control=..]place[/map] is shown as a placeholder with the place name (no map in the
		// editor), which keeps its attributes in data-kunena-map-* to give the same bbcode back.
		sceditor.formats.bbcode.set('map', {
			tags: {
				div: {
					'data-kunena-map': null
				}
			},
			isInline: false,
			allowedChildren: ['#', '#newline'],
			format: function (element, content) {
				var attrs = '';

				['type', 'zoom', 'control'].forEach(function (name) {
					if (element.hasAttribute('data-kunena-map-' + name))
						attrs += ' ' + name + '=' + element.getAttribute('data-kunena-map-' + name);
				});

				return '[map' + attrs + ']' + content + '[/map]';
			},
			html: function (token, attrs, content) {
				var data = '';

				['type', 'zoom', 'control'].forEach(function (name) {
					if (attrs[name] !== undefined)
						data += ' data-kunena-map-' + name + '="' + sceditor.escapeEntities(attrs[name], true) + '"';
				});

				// The content is the text of the tag, already escaped by the parser.
				return '<div data-kunena-map="1"' + data +
					' title="' + sceditor.escapeEntities(Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP'), true) + '"' +
					' style="border: 1px dashed #888; padding: 4px 8px;">' + content + '</div>';
			}
		});
	
		sceditor.command.set('map', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = kunenaHtml(
					'<div>' +
					'<div>' +
					'<label for="type-select">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE') + '</label> ' +
					'<select name="type" id="type-select">' +
					'<option value="hybrid">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_HYBRID') + '</option>' +
					'<option value="roadmap">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_ROADMAP') + '</option>' +
					'<option value="terrain">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_TERRAIN') + '</option>' +
					'<option value="satelite">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_TYPE_SATELITE') + '</option>' +
					'</select>' +
					'</div>' +
					'<div>' +
					'<label for="zoom-select">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_ZOOM_LEVEL') + '</label> ' +
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
					'<label for="city">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_MAP_CITY') + '</label> ' +
					'<input type="text" id="city" size="10" />' +
					'</div>' +
					'<div>' +
					'<input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" />' +
					'</div>' +
					'</div>'
				);
	
				$content.querySelector('.button').addEventListener('click', function (e) {
					var city = $content.querySelector('#city').value,
						type = $content.querySelector('#type-select').value,
						zoom = $content.querySelector('#zoom-select').value
					;
	
					var attrs = ' type=' + type + ' zoom=' + zoom;
	
					if (city)
						editor.insert('[map' + attrs + ']' + city + '[/map]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertmap', $content);
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
				return '[video=' + $element.getAttribute('data-kunena-vt') + ']' + $element.getAttribute('data-kunena-vsrc') + '[/video]';
			},
			html: function (token, attrs, content) {
				var provider = Object.keys(kunenaCmd.video).find(key => key.toLowerCase() === attrs.defaultattr);
				var params = provider ? kunenaCmd.video[provider] : undefined;
				var url;

				// Build the embed url from the id which the provider pattern finds in the given address.
				if (params) {
					var found = params.match ? content.match(params.match) : null;

					if (found) {
						// The id is the last capture group which holds a value (some patterns end with an optional separator group).
						for (var g = found.length - 1; g > 0; g--) {
							if (found[g] && found[g] !== '/') {
								url = params.url + encodeURIComponent(found[g]);
								break;
							}
						}
					}
				}

				if (url) {
					return params['html']
						.replace('{url}', sceditor.escapeEntities(url, true))
						.replace('{src}', sceditor.escapeEntities(content, true))
						.replace('{type}', sceditor.escapeEntities(attrs.defaultattr, true));
				}
				return sceditor.escapeEntities(token.val + content + (token.closing ? token.closing.val : ''));
			}
		});
	
		sceditor.command.set('video', {
			_dropDown: function (editor, caller) {
				var $content, videourl, videotype, videoOpts = '';
	
				Object.keys(kunenaCmd.video).forEach(function (provider) {
					videoOpts += '<option value="' + provider.toLowerCase() + '">' + editor._(provider) + '</option>';
				});
				$content = kunenaHtml(
					'<div>' +
					'<div>' +
					'<label for="videotype">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_TYPE') + '</label> ' +
					'<select id="videotype">' + videoOpts + '</select>' +
					'</div>' +
					'<div>' +
					'<label for="videourl">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_URL') + '</label> ' +
					'<input type="text" id="videourl" placeholder="https://" />' +
					'</div>' +
					'<div>' +
					'<label for="videosize">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_SIZE') + '</label> ' +
					'<input type="text" id="videosize" />' +
					'</div>' +
					'<div>' +
					'<label for="videoheight">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_HEIGHT') + '</label> ' +
					'<input type="text" id="videoheight" />' +
					'</div>' +
					'<div>' +
					'<label for="videowidth">' + Joomla.Text._('COM_KUNENA_SCEDITOR_COMMAND_INSERT_VIDEO_WIDTH') + '</label> ' +
					'<input type="text" id="videowidth" />' +
					'</div>' +
					'<div><input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" /></div>' +
					'</div>'
				);
	
				$content.querySelector('.button').addEventListener('click', function (e) {
					videourl = $content.querySelector('#videourl').value;
					videotype = $content.querySelector('#videotype').value;
	
					if (videourl !== '' && videourl !== 'http://')
						editor.insert('[video=' + videotype + ']' + videourl + '[/video]');
	
					editor.closeDropDown(true);
					e.preventDefault();
				});
	
				editor.createDropDown(caller, 'insertvideo', $content);
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
	
		sceditor.command.set('poll', {
			_dropDown: function (editor, caller) {
				var $content;
	
				$content = kunenaHtml(
					'<div>' +
					'<div>' +
					'<label for="polltitle">' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_POLL_TITLE') + '</label> ' +
					'<input type="text" id="polltitle" />' +
					'</div>' +
					'<div>' +
					'<button type="button" class="btn btn-primary btn-sm addpolloption" name="addpolloption">' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_BUTTON_ADD_POLL_OPTION') + '</button> ' +
					'</div>' +
					'<div>' +
					'<button type="button" class="btn btn-primary btn-sm removepolloption" name="removepolloption">' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_BUTTON_REMOVE_POLL_OPTION') + '</button> ' +
					'</div>' +
					'<div id="sceditor_poll_options">' +
					'</div>' +
					'<div>' +
					'<label for="polllifespan">' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_POLL_LIFE_SPAN') + '</label> ' +
					'<input type="date" id="polllifespan" />' +
					'</div>' +
					'<div><input type="button" class="button" value="' + Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL') + '" /></div>' +
					'</div>'
				);

				var poll_number_option = 0;
				// The date field of the browser takes yyyy-mm-dd: the date of a stored end (Y-m-d H:i:s), none for the empty date.
				var pollDate = function (value) {
					value = String(value || '').slice(0, 10);

					return /^\d{4}-\d{2}-\d{2}$/.test(value) && value !== '1000-01-01' ? value : '';
				};
				var sceditor_poll_options = $content.querySelector('#sceditor_poll_options');
				var field = function (selector) {
					return $content.querySelector(selector);
				};

				// A field of an existing option keeps the id of that option, a field without id is a new option.
				var addPollOption = function (text, optionId) {
					poll_number_option++;

					var poll_option_label = document.createElement('label');
					var poll_option = document.createElement('input');

					poll_option_label.htmlFor = 'poll_option' + poll_number_option;
					poll_option_label.id = 'poll_option_label' + poll_number_option;
					poll_option_label.textContent = 'Poll option ' + poll_number_option;
					poll_option.type = 'text';
					poll_option.id = 'poll_option' + poll_number_option;

					if (text !== undefined) {
						poll_option.value = text;
					}

					if (optionId !== undefined) {
						poll_option.setAttribute('data-poll-option-id', optionId);
					}

					sceditor_poll_options.append(poll_option_label, poll_option);
				};

				// Prefill: the poll inserted before on this page, else the poll of the edited topic.
				var inserted_title = document.querySelector('#poll_options input[name="poll_title"]');
				var pageValue = function (selector) {
					var element = document.querySelector(selector);

					return element ? element.value : '';
				};

				if (inserted_title) {
					field('#polltitle').value = inserted_title.value;
					field('#polllifespan').value = pollDate(pageValue('#poll_options input[name="poll_time_to_live"]'));

					document.querySelectorAll('#poll_options input[name^="polloptionsID["]').forEach(function (input) {
						var key = input.name.slice('polloptionsID['.length, -1);

						addPollOption(input.value, /^\d+$/.test(key) ? key : undefined);
					});
				} else if (document.getElementById('poll_exist_edit')) {
					field('#polltitle').value = pageValue('#ckeditor_dialog_polltitle');
					field('#polllifespan').value = pollDate(pageValue('#ckeditor_dialog_polltimetolive'));

					document.querySelectorAll('.ckeditor_dialog_polloption').forEach(function (input) {
						addPollOption(input.value, input.id.replace('ckeditor_dialog_polloption', ''));
					});
				}

				field('.addpolloption').addEventListener('click', function () {
					addPollOption();
				});

				field('.removepolloption').addEventListener('click', function () {
					if (poll_number_option > 0) {
						field('#poll_option' + poll_number_option)?.remove();
						field('#poll_option_label' + poll_number_option)?.remove();
						poll_number_option--;
					}
				});

				// The poll is kept in hidden fields of the form (#poll_options), which are sent with the message.
				field('.button').addEventListener('click', function (e) {
					var poll_options = document.getElementById('poll_options');
					var hidden = function (name, value) {
						var input = document.createElement('input');

						input.type = 'hidden';
						input.name = name;
						input.value = value;

						return input;
					};

					if (poll_options) {
						poll_options.replaceChildren();

						if (poll_number_option > 0) {
							poll_options.append(hidden('poll_title', field('#polltitle').value), hidden('poll_time_to_live', field('#polllifespan').value));

							for (var i = 1; i <= poll_number_option; i++) {
								var option = field('#poll_option' + i);
								var optionId = option.getAttribute('data-poll-option-id');
								var key = optionId !== null ? optionId : 'new' + i;

								poll_options.append(hidden('polloptionsID[' + key + ']', option.value));
							}
						}
					}

					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'insertpoll', $content);
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
		var textarea_private = document.getElementById('message_private');

		// The buttons are not configured (the option is empty): the basic toolbar is used.
		if(Joomla.getOptions('com_kunena.template_editor_buttons_configuration') !== undefined && Joomla.getOptions('com_kunena.template_editor_buttons_configuration') !== null)
		{
			var toolbar_buttons_config = Joomla.getOptions('com_kunena.template_editor_buttons_configuration');

			//list in config:         bold,italic,underline,strike,superscript,subscript, right,left,justify,center, font,size,color,removeformat, bulletlist,orderedlist,table,horizontalrule, confidential,hidetext,spoiler, emoticon,quote,image,video,link,unlink,code, map,x_social,instagram,ebay,soundcloud,maximize,
			//not in config:          fix: undo,redo,cut,copy,pastetext,source,  date,time,print  br?
			//lists of all buttons (with spacers)   TODO: need to change the values(bold, italic) from template parameters to be handled here
					          
			var toolbar_buttons = 'bold,italic,underline,strike,subscript,superscript,|left,center,right,justify,|font,size,color,removeformat,|bulletlist,orderedlist,table,|confidential,hidetext,spoiler,|emoticon,quote,image,video,link,unlink,code,|map,x_social,instagram,ebay,soundcloud,|undo,redo,cut,copy,pastetext,horizontalrule,source,maximize';

			var button_match = toolbar_buttons.split(',');
			button_match.forEach(function(buttonmatch) {
				buttonmatch = buttonmatch.replace('|','');

				if (toolbar_buttons_config.indexOf(buttonmatch) ==-1 && buttonmatch.length > 1)
				{
					toolbar_buttons = toolbar_buttons.replace(buttonmatch + ',','');
				}
			});
		}
		else
		{	//basic setting
			toolbar_buttons = 'bold,italic,underline,strike,subscript,superscript|left,center,right,justify|font,size,color,removeformat|bulletlist,orderedlist,table,|emoticon,quote,image,video,link,unlink,code|map,x_social,instagram,ebay,soundcloud|undo,redo,cut,copy,pastetext,source';
		}

		//add email for private, before inserting poll
		var toolbar_buttons_private = '';
		toolbar_buttons_private = toolbar_buttons.replace('emoticon','email|emoticon');

		// The poll button: when polls are possible (a category which allows polls, or a topic which has a poll). It is enabled
		// or disabled while the category is chosen.
		if (Joomla.getOptions('com_kunena.pollcategoriesid') !== undefined || document.getElementById('poll_exist_edit'))
		{
			toolbar_buttons += '|poll';
		}

		var emoticons = Joomla.getOptions('com_kunena.ckeditor_emoticons');
		var obj = emoticons ? JSON.parse(emoticons) : null;

		var userlang = Joomla.getOptions('com_kunena.sceditor_userdefaultlanguage');
		userlang = (typeof userlang == 'undefined') ? Joomla.getOptions('com_kunena.sceditor_joomladefaultlanguage') : userlang;

		// The mention of a user: [mention userid=1]name[/mention] (the same bbcode as of the CKEditor).
		sceditor.formats.bbcode.set('mention', {
			tags: {
				span: {
					'data-kmention-userid': null
				}
			},
			isInline: true,
			format: function (element, content) {
				return '[mention userid=' + element.getAttribute('data-kmention-userid') + ']' + content + '[/mention]';
			},
			html: function (token, attrs, content) {
				return '<span class="kmention" data-kmention-userid="' + attrs.userid + '">' + content + '</span>';
			}
		});

		// No images pasted into the editor (like the blockimagepaste plugin of the CKEditor): the pasted images are removed.
		sceditor.plugins.kunenapaste = function () {
			this.signalPasteRaw = function (data) {
				if (data.html && /<img[\s>/]/i.test(data.html)) {
					data.html = data.html.replace(/<img[^>]*>/gi, '');

					if (!data.html.replace(/<[^>]*>/g, '').trim()) {
						data.html = '';
						data.text = '';
					}

					alert(Joomla.Text._('COM_KUNENA_EDITOR_PASTE_IMAGE_BLOCKED') || 'Pasting images directly into the editor is not allowed.');
				}
			};
		};

		// The text which "Go to editor" of the quick reply copied: into the textarea, which SCEditor reads when it is made. A value
		// set into the editor just after it is made was lost: its frame loads afterwards, with an empty document.
		var kunenaCopy = localStorage.getItem('copyKunenaeditor');

		if (kunenaCopy && textarea && textarea.value === '') {
			textarea.value = kunenaCopy;
			localStorage.removeItem('copyKunenaeditor');
		}

		sceditor.create(textarea, {
			plugins: 'undo,autoyoutube,kunenapaste',
			format: 'bbcode',
			toolbar: toolbar_buttons,
			locale: userlang,
			style: Joomla.getOptions('com_kunena.sceditor_style_path'),
			emoticonsRoot: Joomla.getOptions('com_kunena.root_path')+'/media/kunena/emoticons/',
			emoticons: {
				// Emoticons to be included in the dropdown
				dropdown: obj,
				// Emoticons to be included in the more section
				/*more: {
					':alien:': 'emoticons/alien.png',
					':blink:': 'emoticons/blink.png'
				},*/
				// Emoticons that are not shown in the dropdown but will still
				// be converted. Can be used for things like aliases
				/*hidden: {
					':aliasforalien:': 'emoticons/alien.png',
					':aliasforblink:': 'emoticons/blink.png'
				}*/
			}
		});

		if (sceditor.instance(textarea).val().length > 0)
		{
			kunenaSubmit(true);
		}

		sceditor.instance(textarea).bind('valuechanged', function(e) {
			if (sceditor.instance(textarea).val().length > 0)
			{
				kunenaSubmit(true);
			}

			// Only when the other editor (the text for a private message) is empty too: it can send the message by itself.
			if (sceditor.instance(textarea).val().length == 0 && sceditor.instance(textarea_private).val().length == 0)
			{
				kunenaSubmit(false);
			}
		});

		sceditor.create(textarea_private, {
			plugins: 'undo,autoyoutube,kunenapaste',
			format: 'bbcode',
			toolbar: toolbar_buttons_private,
			locale: userlang,
			style: Joomla.getOptions('com_kunena.sceditor_style_path'),
			emoticonsRoot: Joomla.getOptions('com_kunena.root_path')+'/media/kunena/emoticons/',
			emoticons: {
				// Emoticons to be included in the dropdown
				dropdown: obj,
				// Emoticons to be included in the more section
				/*more: {
					':alien:': 'emoticons/alien.png',
					':blink:': 'emoticons/blink.png'
				},*/
				// Emoticons that are not shown in the dropdown but will still
				// be converted. Can be used for things like aliases
				/*hidden: {
					':aliasforalien:': 'emoticons/alien.png',
					':aliasforblink:': 'emoticons/blink.png'
				}*/
			}
		});

		if (sceditor.instance(textarea_private).val().length > 0)
		{
			kunenaSubmit(true);
		}

		sceditor.instance(textarea_private).bind('valuechanged', function(e) {
			if (sceditor.instance(textarea_private).val().length > 0)
			{
				kunenaSubmit(true);
			}

			if (sceditor.instance(textarea_private).val().length == 0 && sceditor.instance(textarea).val().length == 0)
			{
				kunenaSubmit(false);
			}
		});

		// ---- The features of the CKEditor which the SCEditor did not have: mentions, the poll button only where a poll is
		// ---- allowed and no images pasted into the editor.

		// The poll of the edited topic may not be edited: the button only shows it, the save checks it again.
		var kunenaPollEditRefused = Joomla.getOptions('com_kunena.pollEditRefused');

		// Is a poll allowed: in a category which allows polls (when the topic has no poll yet) or when the topic has a poll.
		var kunenaPollAllowed = function () {
			if (kunenaPollEditRefused !== undefined) {
				return false;
			}

			var pollcategoriesidOption = Joomla.getOptions('com_kunena.pollcategoriesid');
			var pollcategoriesid = pollcategoriesidOption !== undefined ? JSON.parse(pollcategoriesidOption) : undefined;
			var catid = (document.getElementById('postcatid') || {}).value || (document.getElementById('poll_catid') || {}).value;

			if (document.getElementById('poll_exist_edit')) {
				return true;
			}

			return !!(pollcategoriesid && pollcategoriesid[catid] !== undefined);
		};

		var kunenaPollButtonUpdate = function () {
			var allowed = kunenaPollAllowed();

			document.querySelectorAll('.sceditor-button-poll').forEach(function (button) {
				button.classList.toggle('disabled', !allowed);

				if (kunenaPollEditRefused !== undefined) {
					button.setAttribute('title', kunenaPollEditRefused);
					button.setAttribute('aria-describedby', 'poll-edit-refused');
				}
			});
		};

		// The poll command works only where a poll is allowed. The editor has its own copy of the commands.
		var kunenaPollCommand = sceditor.instance(textarea).commands.poll;
		var kunenaPollExec = kunenaPollCommand.exec;
		var kunenaPollTxtExec = kunenaPollCommand.txtExec;

		kunenaPollCommand.exec = function (caller) {
			if (kunenaPollAllowed()) {
				kunenaPollExec.call(this, caller);
			}
		};
		kunenaPollCommand.txtExec = function (caller) {
			if (kunenaPollAllowed()) {
				kunenaPollTxtExec.call(this, caller);
			}
		};

		// A switch between source and WYSIWYG mode enables the buttons again: the state of the poll button is set after it.
		var kunenaSourceCommand = sceditor.instance(textarea).commands.source;
		var kunenaSourceExec = kunenaSourceCommand.exec;
		var kunenaSourceTxtExec = kunenaSourceCommand.txtExec;

		kunenaSourceCommand.exec = function (caller) {
			kunenaSourceExec.call(this, caller);
			kunenaPollButtonUpdate();
		};
		kunenaSourceCommand.txtExec = function (caller) {
			kunenaSourceTxtExec.call(this, caller);
			kunenaPollButtonUpdate();
		};

		kunenaPollButtonUpdate();
		document.getElementById('postcatid')?.addEventListener('change', kunenaPollButtonUpdate);

		// The mentions: typing @ shows the users of the topic (the same list as of the CKEditor). The list is made here and
		// not with a library, because the editor is an iframe.
		if (Joomla.getOptions('com_kunena.kunena_url_topic_get_mentions')) {
			var kunenaMentionUsers = null;

			var kunenaMentionRequest = 0;

			// A new topic has no users yet: the users are searched with the typed text, for the category.
			var kunenaMentionSearch = function (query, cb) {
				var request = ++kunenaMentionRequest;
				var catid = (document.getElementById('postcatid') || {}).value || (document.getElementById('poll_catid') || {}).value || '';
				var url = Joomla.getOptions('com_kunena.kunena_url_topic_get_mentions') + '&catid=' + encodeURIComponent(catid) + '&q=' + encodeURIComponent(query);

				kunenaGetJSON(url).then(function (json) {
					var users = [];

					try {
						users = typeof json === 'string' ? JSON.parse(json) : json;
					} catch (e) {
						users = [];
					}

					// Only the answer to the last typed text counts.
					if (request === kunenaMentionRequest) {
						cb(users);
					}
				}).catch(function () {
					if (request === kunenaMentionRequest) {
						cb([]);
					}
				});
			};

			var kunenaMentionLoad = function (cb) {
				if (kunenaMentionUsers !== null) {
					cb(kunenaMentionUsers);

					return;
				}

				kunenaGetJSON(Joomla.getOptions('com_kunena.kunena_url_topic_get_mentions')).then(function (json) {
					try {
						kunenaMentionUsers = typeof json === 'string' ? JSON.parse(json) : json;
					} catch (e) {
						kunenaMentionUsers = [];
					}

					cb(kunenaMentionUsers);
				}).catch(function () {
					// New topic: there are no users to mention yet.
					kunenaMentionUsers = [];
					cb(kunenaMentionUsers);
				});
			};

			var kunenaMentionEditor = sceditor.instance(textarea);
			var kunenaMentionFrame = kunenaMentionEditor.getContentAreaContainer();
			var kunenaMentionBody = kunenaMentionEditor.getBody();

			if (kunenaMentionFrame && kunenaMentionBody) {
				var kunenaMentionWin = kunenaMentionFrame.contentWindow;
				var kunenaMentionMenu = document.createElement('ul');

				kunenaMentionMenu.className = 'kmention-menu list-group shadow';
				Object.assign(kunenaMentionMenu.style, {
					position: 'absolute', zIndex: 2000, display: 'none', maxHeight: '240px', overflowY: 'auto', minWidth: '180px'
				});
				document.body.append(kunenaMentionMenu);
				var kunenaMention = {items: [], index: 0, node: null, start: 0, end: 0};

				var kunenaMentionHide = function () {
					kunenaMentionMenu.style.display = 'none';
					kunenaMention.items = [];
				};

				var kunenaMentionDraw = function () {
					kunenaMentionMenu.replaceChildren();

					kunenaMention.items.forEach(function (user, i) {
						var item = document.createElement('li');
						var name = document.createElement('strong');

						item.className = 'list-group-item list-group-item-action py-1';
						item.style.cursor = 'pointer';
						item.classList.toggle('active', i === kunenaMention.index);

						if (user.avatar) {
							var avatar = document.createElement('img');

							avatar.width = 24;
							avatar.height = 24;
							avatar.alt = '';
							avatar.className = 'me-2 rounded-circle';
							avatar.src = user.avatar;
							item.append(avatar);
						}

						name.textContent = user.username;
						item.append(name);

						// mousedown: the editor keeps the focus.
						item.addEventListener('mousedown', function (e) {
							e.preventDefault();
							kunenaMentionSelect(i);
						});

						kunenaMentionMenu.append(item);
					});
				};

				var kunenaMentionSelect = function (index) {
					var user = kunenaMention.items[index];

					if (!user || !kunenaMention.node) {
						return;
					}

					var range = kunenaMentionFrame.contentDocument.createRange();
					range.setStart(kunenaMention.node, kunenaMention.start);
					range.setEnd(kunenaMention.node, kunenaMention.end);

					var selection = kunenaMentionWin.getSelection();
					selection.removeAllRanges();
					selection.addRange(range);
					range.deleteContents();

					kunenaMentionEditor.wysiwygEditorInsertHtml(
						'<span class="kmention" data-kmention-userid="' + parseInt(user.userid, 10) + '">'
						+ sceditor.escapeEntities(String(user.username)) + '</span>&nbsp;'
					);

					kunenaMentionHide();
				};

				// Look for an @ and the letters after it, before the cursor.
				var kunenaMentionCheck = function () {
					var selection = kunenaMentionWin.getSelection();

					if (!selection || !selection.rangeCount || !selection.isCollapsed || !selection.anchorNode || selection.anchorNode.nodeType !== 3) {
						kunenaMentionHide();

						return;
					}

					var node = selection.anchorNode;
					var text = node.textContent.slice(0, selection.anchorOffset);
					var found = /(^|[\s\u00a0])@([^\s\u00a0@]{0,30})$/.exec(text);

					if (!found) {
						kunenaMentionHide();

						return;
					}

					var query = found[2].toLowerCase();

					var kunenaMentionDone = function (users) {
						// The users of a new topic were searched by the server (on any of the names of the user).
						var items = Joomla.getOptions('com_kunena.mentions_search') ? users.slice(0, 8) : users.filter(function (user) {
							return user.username.toLowerCase().indexOf(query) !== -1;
						}).slice(0, 8);

						if (!items.length) {
							kunenaMentionHide();

							return;
						}

						kunenaMention.items = items;
						kunenaMention.index = 0;
						kunenaMention.node = node;
						kunenaMention.end = selection.anchorOffset;
						kunenaMention.start = selection.anchorOffset - query.length - 1;
						kunenaMentionDraw();

						// Under the cursor: the position of the cursor in the iframe, on the page.
						var rect = selection.getRangeAt(0).getClientRects()[0] || kunenaMentionBody.getBoundingClientRect();
						var frameRect = kunenaMentionFrame.getBoundingClientRect();

						kunenaMentionMenu.style.left = Math.round(frameRect.left + window.pageXOffset + rect.left) + 'px';
						kunenaMentionMenu.style.top = Math.round(frameRect.top + window.pageYOffset + rect.bottom + 4) + 'px';
						kunenaMentionMenu.style.display = '';
					};

					if (Joomla.getOptions('com_kunena.mentions_search')) {
						kunenaMentionSearch(query, kunenaMentionDone);
					} else {
						kunenaMentionLoad(kunenaMentionDone);
					}
				};

				kunenaMentionBody.addEventListener('keydown', function (e) {
					if (!kunenaMention.items.length) {
						return;
					}

					if (e.which === 40 || e.which === 38) {
						kunenaMention.index = (kunenaMention.index + (e.which === 40 ? 1 : kunenaMention.items.length - 1)) % kunenaMention.items.length;
						kunenaMentionDraw();
						e.preventDefault();
					} else if (e.which === 13 || e.which === 9) {
						kunenaMentionSelect(kunenaMention.index);
						e.preventDefault();
					} else if (e.which === 27) {
						kunenaMentionHide();
						e.preventDefault();
					}
				});

				['keyup', 'click', 'input'].forEach(function (type) {
					kunenaMentionBody.addEventListener(type, function (e) {
						// The keys of the list do not start a new search.
						if (e.type === 'keyup' && [13, 9, 27, 38, 40].indexOf(e.which) !== -1) {
							return;
						}

						kunenaMentionCheck();
					});
				});

				kunenaMentionBody.addEventListener('blur', function () {
					window.setTimeout(kunenaMentionHide, 150);
				});
			}
		}
	}
};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', kunenaInit);
	} else {
		kunenaInit();
	}
})();
