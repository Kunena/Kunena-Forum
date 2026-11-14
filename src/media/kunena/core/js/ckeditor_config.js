/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'nl';
	// config.uiColor = '#AADC6E';
	// config.skin = 'moono-lisa';
	if (Joomla.getOptions('com_kunena.ckeditor_userdefaultlanguage') != 'default') {
		config.defaultLanguage = '' + Joomla.getOptions('com_kunena.ckeditor_userdefaultlanguage') + '';
	} else {
		var joomlaLanguage = Joomla.getOptions('com_kunena.ckeditor_joomladefaultlanguage');

		if (joomlaLanguage != 'en') {
			config.defaultLanguage = '' + joomlaLanguage + '';
		}
	}

	config.smiley_descriptions="";

	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'social', groups: [ 'social' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	var remove_buttons_url_image = Joomla.getOptions('com_kunena.ckeditor_remove_buttons_url_image');

	if(Joomla.getOptions('com_kunena.ckeditor_buttons_configuration') !== undefined && remove_buttons_url_image===undefined)
	{
		config.removeButtons = 'Anchor,Paste,Styles,Format,Font,BGColor,Copy,Outdent,Indent,' + Joomla.getOptions('com_kunena.ckeditor_buttons_configuration');
	}
	else
	{
		if (remove_buttons_url_image)
		{
			config.removeButtons = 'Anchor,Paste,Styles,Format,Font,BGColor,Image,Link,Unlink,Copy,Outdent,Indent';
		}
		else
		{
			config.removeButtons = 'Anchor,Paste,Styles,Format,Font,BGColor,Copy,Outdent,Indent';
		}
	}

	config.extraPlugins = 'ebay,x,instagram,map,soundcloud,video,confidential,hidetext,spoiler,code,polls,blockimagepaste';
	var emoticons = Joomla.getOptions('com_kunena.ckeditor_emoticons');
	var obj = jQuery.parseJSON( emoticons );
	var list_emoticons = [];

	jQuery.each(obj, function( index, value ) {
		list_emoticons.push(value);
	});

	if (Joomla.getOptions('com_kunena.ckeditor_smiley_path')!==undefined)
	{
		config.smiley_path = Joomla.getOptions('com_kunena.ckeditor_smiley_path');
	}
	else
	{
		config.smiley_path = '/media/kunena/emoticons/';
	}

	config.smiley_images = list_emoticons;

	// Emoticons are stored as an image path in the message. When a message is loaded
	// in the editor, point them to the current smiley_path, so they also show after the
	// site moved to another (sub)folder or domain.
	if (!CKEDITOR.plugins.get('kunenaemoticons')) {
		CKEDITOR.plugins.add('kunenaemoticons', {
			init: function (editor) {
				editor.dataProcessor.dataFilter.addRules({
					elements: {
						img: function (element) {
							var src = element.attributes['data-cke-saved-src'] || element.attributes.src || '';
							var match = src.match(/^(?:https?:\/\/[^\/]+)?(?:\/[^?#]*)?\/media\/kunena\/emoticons\/([^\/?#]+)$/i);

							if (match) {
								element.attributes.src = editor.config.smiley_path + match[1];
								element.attributes['data-cke-saved-src'] = editor.config.smiley_path + match[1];
							}
						}
					}
				});
			}
		});
	}

	config.extraPlugins += ',kunenaemoticons';

	// A link which opens in a new window is stored as [url=<address> target=_blank]. The
	// BBCode plugin keeps that parameter inside the address, so move it to the target
	// attribute when a message is loaded and back into the address when it is saved, and
	// let the link dialog choose between the same window and a new window.
	if (!CKEDITOR.plugins.get('kunenalinktarget')) {
		var linkTargetParam = /\s+target=(?:"[^"]*"|'[^']*'|\S*)/gi;
		var linkAddressAttributes = ['href', 'data-cke-saved-href'];

		CKEDITOR.plugins.add('kunenalinktarget', {
			init: function (editor) {
				editor.dataProcessor.dataFilter.addRules({
					elements: {
						a: function (element) {
							var hasTarget = false;

							linkAddressAttributes.forEach(function (name) {
								var address = element.attributes[name];

								if (address !== undefined && address.search(linkTargetParam) !== -1) {
									element.attributes[name] = address.replace(linkTargetParam, '');
									hasTarget = true;
								}
							});

							if (hasTarget) {
								element.attributes.target = '_blank';
							}
						}
					}
				});

				editor.dataProcessor.htmlFilter.addRules({
					elements: {
						a: function (element) {
							if (element.attributes.target === undefined) {
								return;
							}

							linkAddressAttributes.forEach(function (name) {
								if (element.attributes[name] !== undefined) {
									element.attributes[name] += ' target=_blank';
								}
							});

							delete element.attributes.target;
						}
					}
				});
			}
		});

		CKEDITOR.on('dialogDefinition', function (event) {
			if (event.data.name != 'link') {
				return;
			}

			// Inside the URL options, so the choice is hidden for e-mail links.
			event.data.definition.getContents('info').get('urlOptions').children.push({
				type: 'select',
				id: 'kunenaLinkTarget',
				label: Joomla.Text._('COM_KUNENA_EDITOR_LINK_TARGET'),
				'default': '',
				items: [
					[Joomla.Text._('COM_KUNENA_EDITOR_LINK_TARGET_SAME'), ''],
					[Joomla.Text._('COM_KUNENA_EDITOR_LINK_TARGET_NEW'), '_blank']
				],
				setup: function (data) {
					this.setValue(data.target && data.target.name ? '_blank' : '');
				},
				commit: function (data) {
					var linkType = this.getDialog().getContentElement('info', 'linkType');

					if (this.getValue() == '_blank' && (!linkType || linkType.getValue() == 'url')) {
						data.target = {type: '_blank', name: '_blank'};
					} else {
						data.target = {type: 'notSet'};
					}
				}
			});
		});
	}

	config.extraPlugins += ',kunenalinktarget';
	config.linkDefaultProtocol= 'https://'

	// Define font sizes in percent values.
	config.fontSize_sizes= "50/50%;85/85%;100/100%;150/150%;200/200%";

	// Set the skin if it's defined in the params settings of the template
	if (Joomla.getOptions('com_kunena.ckeditor_skiname') !== null)
	{
		config.skin = Joomla.getOptions('com_kunena.ckeditor_skiname');
	}
	
	// Disable SpellChecker from Ckeditor to let the brower handle it
	config.disableNativeSpellChecker = false;
};
