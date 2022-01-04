/**
 * SCEditor BBCode Plugin
 * http://www.sceditor.com/
 *
 * Copyright (C) 2011-2017, Sam Clarke (samclarke.com)
 *
 * SCEditor is licensed under the MIT license:
 *	http://www.opensource.org/licenses/mit-license.php
 *
 * @fileoverview SCEditor BBCode Format
 * @author Sam Clarke
 */
(function (sceditor) {
	/*eslint max-depth: off*/
	'use strict';

	var escapeEntities  = sceditor.escapeEntities;
	var escapeUriScheme = sceditor.escapeUriScheme;
	var dom             = sceditor.dom;
	var utils           = sceditor.utils;

	var css    = dom.css;
	var attr   = dom.attr;
	var is     = dom.is;
	var extend = utils.extend;
	var each   = utils.each;

	var EMOTICON_DATA_ATTR = 'data-sceditor-emoticon';

	var getEditorCommand = sceditor.command.get;

	var QuoteType = {
		/** @lends BBCodeParser.QuoteType */
		/**
		 * Always quote the attribute value
		 * @type {Number}
		 */
		always: 1,

		/**
		 * Never quote the attributes value
		 * @type {Number}
		 */
		never: 2,

		/**
		 * Only quote the attributes value when it contains spaces to equals
		 * @type {Number}
		 */
		auto: 3
	};

	var defaultCommandsOverrides = {
		bold: {
			txtExec: ['[b]', '[/b]']
		},
		italic: {
			txtExec: ['[i]', '[/i]']
		},
		underline: {
			txtExec: ['[u]', '[/u]']
		},
		strike: {
			txtExec: ['[s]', '[/s]']
		},
		subscript: {
			txtExec: ['[sub]', '[/sub]']
		},
		superscript: {
			txtExec: ['[sup]', '[/sup]']
		},
		left: {
			txtExec: ['[left]', '[/left]']
		},
		center: {
			txtExec: ['[center]', '[/center]']
		},
		right: {
			txtExec: ['[right]', '[/right]']
		},
		justify: {
			txtExec: ['[justify]', '[/justify]']
		},
		font: {
			txtExec: function (caller) {
				var editor = this;

				getEditorCommand('font')._dropDown(
					editor,
					caller,
					function (fontName) {
						editor.insertText(
							'[font=' + fontName + ']',
							'[/font]'
						);
					}
				);
			}
		},
		size: {
			txtExec: function (caller) {
				var editor = this;

				getEditorCommand('size')._dropDown(
					editor,
					caller,
					function (fontSize) {
						editor.insertText(
							'[size=' + fontSize + ']',
							'[/size]'
						);
					}
				);
			}
		},
		color: {
			txtExec: function (caller) {
				var editor = this;

				getEditorCommand('color')._dropDown(
					editor,
					caller,
					function (color) {
						editor.insertText(
							'[color=' + color + ']',
							'[/color]'
						);
					}
				);
			}
		},
		bulletlist: {
			txtExec: function (caller, selected) {
				this.insertText(
					'[ul]\n[li]' +
					selected.split(/\r?\n/).join('[/li]\n[li]') +
					'[/li]\n[/ul]'
				);
			}
		},
		orderedlist: {
			txtExec: function (caller, selected) {
				this.insertText(
					'[ol]\n[li]' +
					selected.split(/\r?\n/).join('[/li]\n[li]') +
					'[/li]\n[/ol]'
				);
			}
		},
		table: {
			txtExec: ['[table][tr][td]', '[/td][/tr][/table]']
		},
		horizontalrule: {
			txtExec: ['[hr]']
		},
		code: {
			txtExec: ['[code]', '[/code]']
		},
		image: {
			txtExec: function (caller, selected) {
				var	editor  = this;

				getEditorCommand('image')._dropDown(
					editor,
					caller,
					selected,
					function (url, width, height) {
						var attrs  = '';

						if (width) {
							attrs += ' width=' + width;
						}

						if (height) {
							attrs += ' height=' + height;
						}

						editor.insertText(
							'[img' + attrs + ']' + url + '[/img]'
						);
					}
				);
			}
		},
		email: {
			txtExec: function (caller, selected) {
				var	editor  = this;

				getEditorCommand('email')._dropDown(
					editor,
					caller,
					function (url, text) {
						editor.insertText(
							'[email=' + url + ']' +
								(text || selected || url) +
							'[/email]'
						);
					}
				);
			}
		},
		link: {
			txtExec: function (caller, selected) {
				var	editor  = this;

				getEditorCommand('link')._dropDown(
					editor,
					caller,
					function (url, text) {
						editor.insertText(
							'[url=' + url + ']' +
								(text || selected || url) +
							'[/url]'
						);
					}
				);
			}
		},
		quote: {
			txtExec: ['[quote]', '[/quote]']
		},
		youtube: {
			txtExec: function (caller) {
				var editor = this;

				getEditorCommand('youtube')._dropDown(
					editor,
					caller,
					function (id) {
						editor.insertText('[youtube]' + id + '[/youtube]');
					}
				);
			}
		},
		rtl: {
			txtExec: ['[rtl]', '[/rtl]']
		},
		ltr: {
			txtExec: ['[ltr]', '[/ltr]']
		}
	};

	var bbcodeHandlers = {
		// START_COMMAND: Bold
		b: {
			tags: {
				b: null,
				strong: null
			},
			styles: {
				// 401 is for FF 3.5
				'font-weight': ['bold', 'bolder', '401', '700', '800', '900']
			},
			format: '[b]{0}[/b]',
			html: '<strong>{0}</strong>'
		},
		// END_COMMAND

		// START_COMMAND: Italic
		i: {
			tags: {
				i: null,
				em: null
			},
			styles: {
				'font-style': ['italic', 'oblique']
			},
			format: '[i]{0}[/i]',
			html: '<em>{0}</em>'
		},
		// END_COMMAND

		// START_COMMAND: Underline
		u: {
			tags: {
				u: null
			},
			styles: {
				'text-decoration': ['underline']
			},
			format: '[u]{0}[/u]',
			html: '<u>{0}</u>'
		},
		// END_COMMAND

		// START_COMMAND: Strikethrough
		s: {
			tags: {
				s: null,
				strike: null
			},
			styles: {
				'text-decoration': ['line-through']
			},
			format: '[s]{0}[/s]',
			html: '<s>{0}</s>'
		},
		// END_COMMAND

		// START_COMMAND: Subscript
		sub: {
			tags: {
				sub: null
			},
			format: '[sub]{0}[/sub]',
			html: '<sub>{0}</sub>'
		},
		// END_COMMAND

		// START_COMMAND: Superscript
		sup: {
			tags: {
				sup: null
			},
			format: '[sup]{0}[/sup]',
			html: '<sup>{0}</sup>'
		},
		// END_COMMAND

		// START_COMMAND: Font
		font: {
			tags: {
				font: {
					face: null
				}
			},
			styles: {
				'font-family': null
			},
			quoteType: QuoteType.never,
			format: function (element, content) {
				var font;

				if (!is(element, 'font') || !(font = attr(element, 'face'))) {
					font = css(element, 'font-family');
				}

				return '[font=' + _stripQuotes(font) + ']' +
					content + '[/font]';
			},
			html: '<font face="{defaultattr}">{0}</font>'
		},
		// END_COMMAND

		// START_COMMAND: Size
		size: {
			tags: {
				font: {
					size: null
				}
			},
			styles: {
				'font-size': null
			},
			format: function (element, content) {
				var	fontSize = attr(element, 'size'),
					size     = 2;

				if (!fontSize) {
					fontSize = css(element, 'fontSize');
				}

				// Most browsers return px value but IE returns 1-7
				if (fontSize.indexOf('px') > -1) {
					// convert size to an int
					fontSize = fontSize.replace('px', '') - 0;

					if (fontSize < 12) {
						size = 1;
					}
					if (fontSize > 15) {
						size = 3;
					}
					if (fontSize > 17) {
						size = 4;
					}
					if (fontSize > 23) {
						size = 5;
					}
					if (fontSize > 31) {
						size = 6;
					}
					if (fontSize > 47) {
						size = 7;
					}
				} else {
					size = fontSize;
				}

				return '[size=' + size + ']' + content + '[/size]';
			},
			html: '<font size="{defaultattr}">{!0}</font>'
		},
		// END_COMMAND

		// START_COMMAND: Color
		color: {
			tags: {
				font: {
					color: null
				}
			},
			styles: {
				color: null
			},
			quoteType: QuoteType.never,
			format: function (elm, content) {
				var	color;

				if (!is(elm, 'font') || !(color = attr(elm, 'color'))) {
					color = elm.style.color || css(elm, 'color');
				}

				return '[color=' + _normaliseColour(color) + ']' +
					content + '[/color]';
			},
			html: function (token, attrs, content) {
				return '<font color="' +
					escapeEntities(_normaliseColour(attrs.defaultattr), true) +
					'">' + content + '</font>';
			}
		},
		// END_COMMAND

		// START_COMMAND: Lists
		ul: {
			tags: {
				ul: null
			},
			breakStart: true,
			isInline: false,
			skipLastLineBreak: true,
			format: '[ul]{0}[/ul]',
			html: '<ul>{0}</ul>'
		},
		list: {
			breakStart: true,
			isInline: false,
			skipLastLineBreak: true,
			html: '<ul>{0}</ul>'
		},
		ol: {
			tags: {
				ol: null
			},
			breakStart: true,
			isInline: false,
			skipLastLineBreak: true,
			format: '[ol]{0}[/ol]',
			html: '<ol>{0}</ol>'
		},
		li: {
			tags: {
				li: null
			},
			isInline: false,
			closedBy: ['/ul', '/ol', '/list', '*', 'li'],
			format: '[li]{0}[/li]',
			html: '<li>{0}</li>'
		},
		'*': {
			isInline: false,
			closedBy: ['/ul', '/ol', '/list', '*', 'li'],
			html: '<li>{0}</li>'
		},
		// END_COMMAND

		// START_COMMAND: Table
		table: {
			tags: {
				table: null
			},
			isInline: false,
			isHtmlInline: true,
			skipLastLineBreak: true,
			format: '[table]{0}[/table]',
			html: '<table>{0}</table>'
		},
		tr: {
			tags: {
				tr: null
			},
			isInline: false,
			skipLastLineBreak: true,
			format: '[tr]{0}[/tr]',
			html: '<tr>{0}</tr>'
		},
		th: {
			tags: {
				th: null
			},
			allowsEmpty: true,
			isInline: false,
			format: '[th]{0}[/th]',
			html: '<th>{0}</th>'
		},
		td: {
			tags: {
				td: null
			},
			allowsEmpty: true,
			isInline: false,
			format: '[td]{0}[/td]',
			html: '<td>{0}</td>'
		},
		// END_COMMAND

		// START_COMMAND: Emoticons
		emoticon: {
			allowsEmpty: true,
			tags: {
				img: {
					src: null,
					'data-sceditor-emoticon': null
				}
			},
			format: function (element, content) {
				return attr(element, EMOTICON_DATA_ATTR) + content;
			},
			html: '{0}'
		},
		// END_COMMAND

		// START_COMMAND: Horizontal Rule
		hr: {
			tags: {
				hr: null
			},
			allowsEmpty: true,
			isSelfClosing: true,
			isInline: false,
			format: '[hr]{0}',
			html: '<hr />'
		},
		// END_COMMAND

		// START_COMMAND: Image
		img: {
			allowsEmpty: true,
			tags: {
				img: {
					src: null
				}
			},
			allowedChildren: ['#'],
			quoteType: QuoteType.never,
			format: function (element, content) {
				var	width, height,
					attribs   = '',
					style     = function (name) {
						return element.style ? element.style[name] : null;
					};

				// check if this is an emoticon image
				if (attr(element, EMOTICON_DATA_ATTR)) {
					return content;
				}

				width = attr(element, 'width') || style('width');
				height = attr(element, 'height') || style('height');

				// only add width and height if one is specified
				if ((element.complete && (width || height)) ||
					(width && height)) {

					attribs = '=' + dom.width(element) + 'x' +
						dom.height(element);
				}

				return '[img' + attribs + ']' + attr(element, 'src') + '[/img]';
			},
			html: function (token, attrs, content) {
				var	undef, width, height, match,
					attribs = '';

				// handle [img width=340 height=240]url[/img]
				width  = attrs.width;
				height = attrs.height;

				// handle [img=340x240]url[/img]
				if (attrs.defaultattr) {
					match = attrs.defaultattr.split(/x/i);

					width  = match[0];
					height = (match.length === 2 ? match[1] : match[0]);
				}

				if (width !== undef) {
					attribs += ' width="' + escapeEntities(width, true) + '"';
				}

				if (height !== undef) {
					attribs += ' height="' + escapeEntities(height, true) + '"';
				}

				return '<img' + attribs +
					' src="' + escapeUriScheme(content) + '" />';
			}
		},
		// END_COMMAND

		// START_COMMAND: URL
		url: {
			allowsEmpty: true,
			tags: {
				a: {
					href: null
				}
			},
			quoteType: QuoteType.never,
			format: function (element, content) {
				var url = attr(element, 'href');

				// make sure this link is not an e-mail,
				// if it is return e-mail BBCode
				if (url.substr(0, 7) === 'mailto:') {
					return '[email="' + url.substr(7) + '"]' +
						content + '[/email]';
				}

				return '[url=' + url + ']' + content + '[/url]';
			},
			html: function (token, attrs, content) {
				attrs.defaultattr =
					escapeEntities(attrs.defaultattr, true) || content;

				return '<a href="' + escapeUriScheme(attrs.defaultattr) + '">' +
					content + '</a>';
			}
		},
		// END_COMMAND

		// START_COMMAND: E-mail
		email: {
			quoteType: QuoteType.never,
			html: function (token, attrs, content) {
				return '<a href="mailto:' +
					(escapeEntities(attrs.defaultattr, true) || content) +
					'">' + content + '</a>';
			}
		},
		// END_COMMAND

		// START_COMMAND: Quote
		quote: {
			tags: {
				blockquote: null
			},
			isInline: false,
			quoteType: QuoteType.never,
			format: function (element, content) {
				var authorAttr = 'data-author';
				var	author = '';
				var cite;
				var children = element.children;

				for (var i = 0; !cite && i < children.length; i++) {
					if (is(children[i], 'cite')) {
						cite = children[i];
					}
				}

				if (cite || attr(element, authorAttr)) {
					author = cite && cite.textContent ||
						attr(element, authorAttr);

					attr(element, authorAttr, author);

					if (cite) {
						element.removeChild(cite);
					}

					content	= this.elementToBbcode(element);
					author  = '=' + author.replace(/(^\s+|\s+$)/g, '');

					if (cite) {
						element.insertBefore(cite, element.firstChild);
					}
				}

				return '[quote' + author + ']' + content + '[/quote]';
			},
			html: function (token, attrs, content) {
				if (attrs.defaultattr) {
					content = '<cite>' + escapeEntities(attrs.defaultattr) +
						'</cite>' + content;
				}

				return '<blockquote>' + content + '</blockquote>';
			}
		},
		// END_COMMAND

		// START_COMMAND: Code
		code: {
			tags: {
				code: null
			},
			isInline: false,
			allowedChildren: ['#', '#newline'],
			format: '[code]{0}[/code]',
			html: '<code>{0}</code>'
		},
		// END_COMMAND


		// START_COMMAND: Left
		left: {
			styles: {
				'text-align': [
					'left',
					'-webkit-left',
					'-moz-left',
					'-khtml-left'
				]
			},
			isInline: false,
			allowsEmpty: true,
			format: '[left]{0}[/left]',
			html: '<div align="left">{0}</div>'
		},
		// END_COMMAND

		// START_COMMAND: Centre
		center: {
			styles: {
				'text-align': [
					'center',
					'-webkit-center',
					'-moz-center',
					'-khtml-center'
				]
			},
			isInline: false,
			allowsEmpty: true,
			format: '[center]{0}[/center]',
			html: '<div align="center">{0}</div>'
		},
		// END_COMMAND

		// START_COMMAND: Right
		right: {
			styles: {
				'text-align': [
					'right',
					'-webkit-right',
					'-moz-right',
					'-khtml-right'
				]
			},
			isInline: false,
			allowsEmpty: true,
			format: '[right]{0}[/right]',
			html: '<div align="right">{0}</div>'
		},
		// END_COMMAND

		// START_COMMAND: Justify
		justify: {
			styles: {
				'text-align': [
					'justify',
					'-webkit-justify',
					'-moz-justify',
					'-khtml-justify'
				]
			},
			isInline: false,
			allowsEmpty: true,
			format: '[justify]{0}[/justify]',
			html: '<div align="justify">{0}</div>'
		},
		// END_COMMAND

		// START_COMMAND: YouTube
		youtube: {
			allowsEmpty: true,
			tags: {
				iframe: {
					'data-youtube-id': null
				}
			},
			format: function (element, content) {
				element = attr(element, 'data-youtube-id');

				return element ? '[youtube]' + element + '[/youtube]' : content;
			},
			html: '<iframe width="560" height="315" frameborder="0" ' +
				'src="https://www.youtube-nocookie.com/embed/{0}?wmode=opaque" ' +
				'data-youtube-id="{0}" allowfullscreen></iframe>'
		},
		// END_COMMAND


		// START_COMMAND: Rtl
		rtl: {
			styles: {
				direction: ['rtl']
			},
			isInline: false,
			format: '[rtl]{0}[/rtl]',
			html: '<div style="direction: rtl">{0}</div>'
		},
		// END_COMMAND

		// START_COMMAND: Ltr
		ltr: {
			styles: {
				direction: ['ltr']
			},
			isInline: false,
			format: '[ltr]{0}[/ltr]',
			html: '<div style="direction: ltr">{0}</div>'
		},
		// END_COMMAND

		// this is here so that commands above can be removed
		// without having to remove the , after the last one.
		// Needed for IE.
		ignore: {}
	};

	/**
	 * Formats a string replacing {name} with the values of
	 * obj.name properties.
	 *
	 * If there is no property for the specified {name} then
	 * it will be left intact.
	 *
	 * @param  {string} str
	 * @param  {Object} obj
	 * @return {string}
	 * @since 2.0.0
	 */
	function formatBBCodeString(str, obj) {
		return str.replace(/\{([^}]+)\}/g, function (match, group) {
			var	undef,
				escape = true;

			if (group.charAt(0) === '!') {
				escape = false;
				group = group.substring(1);
			}

			if (group === '0') {
				escape = false;
			}

			if (obj[group] === undef) {
				return match;
			}

			return escape ? escapeEntities(obj[group], true) : obj[group];
		});
	}

	/**
	 * Removes the first and last divs from the HTML.
	 *
	 * This is needed for pasting
	 * @param  {string} html
	 * @return {string}
	 * @private
	 */
	function removeFirstLastDiv(html) {
		var	node, next, removeDiv,
			output = document.createElement('div');

		removeDiv = function (node, isFirst) {
			// Don't remove divs that have styling
			if (dom.hasStyling(node)) {
				return;
			}

			if ((node.childNodes.length !== 1 ||
				!is(node.firstChild, 'br'))) {
				while ((next = node.firstChild)) {
					output.insertBefore(next, node);
				}
			}

			if (isFirst) {
				var lastChild = output.lastChild;

				if (node !== lastChild && is(lastChild, 'div') &&
					node.nextSibling === lastChild) {
					output.insertBefore(document.createElement('br'), node);
				}
			}

			output.removeChild(node);
		};

		css(output, 'display', 'none');
		output.innerHTML = html.replace(/<\/div>\n/g, '</div>');

		if ((node = output.firstChild) && is(node, 'div')) {
			removeDiv(node, true);
		}

		if ((node = output.lastChild) && is(node, 'div')) {
			removeDiv(node);
		}

		return output.innerHTML;
	}

	function isFunction(fn) {
		return typeof fn === 'function';
	}

	/**
	 * Removes any leading or trailing quotes ('")
	 *
	 * @return string
	 * @since v1.4.0
	 */
	function _stripQuotes(str) {
		return str ?
			str.replace(/\\(.)/g, '$1').replace(/^(["'])(.*?)\1$/, '$2') : str;
	}

	/**
	 * Formats a string replacing {0}, {1}, {2}, ect. with
	 * the params provided
	 *
	 * @param {string} str The string to format
	 * @param {...string} arg The strings to replace
	 * @return {string}
	 * @since v1.4.0
	 */
	function _formatString(str) {
		var	undef;
		var args = arguments;

		return str.replace(/\{(\d+)\}/g, function (_, matchNum) {
			return args[matchNum - 0 + 1] !== undef ?
				args[matchNum - 0 + 1] :
				'{' + matchNum + '}';
		});
	}

	var TOKEN_OPEN = 'open';
	var TOKEN_CONTENT = 'content';
	var TOKEN_NEWLINE = 'newline';
	var TOKEN_CLOSE = 'close';


	/*
	 * @typedef {Object} TokenizeToken
	 * @property {string} type
	 * @property {string} name
	 * @property {string} val
	 * @property {Object.<string, string>} attrs
	 * @property {array} children
	 * @property {TokenizeToken} closing
	 */

	/**
	 * Tokenize token object
	 *
	 * @param  {string} type The type of token this is,
	 *                       should be one of tokenType
	 * @param  {string} name The name of this token
	 * @param  {string} val The originally matched string
	 * @param  {array} attrs Any attributes. Only set on
	 *                       TOKEN_TYPE_OPEN tokens
	 * @param  {array} children Any children of this token
	 * @param  {TokenizeToken} closing This tokens closing tag.
	 *                                 Only set on TOKEN_TYPE_OPEN tokens
	 * @class {TokenizeToken}
	 * @name {TokenizeToken}
	 * @memberOf BBCodeParser.prototype
	 */
	// eslint-disable-next-line max-params
	function TokenizeToken(type, name, val, attrs, children, closing) {
		var base      = this;

		base.type     = type;
		base.name     = name;
		base.val      = val;
		base.attrs    = attrs || {};
		base.children = children || [];
		base.closing  = closing || null;
	};

	TokenizeToken.prototype = {
		/** @lends BBCodeParser.prototype.TokenizeToken */
		/**
		 * Clones this token
		 *
		 * @return {TokenizeToken}
		 */
		clone: function () {
			var base = this;

			return new TokenizeToken(
				base.type,
				base.name,
				base.val,
				extend({}, base.attrs),
				[],
				base.closing ? base.closing.clone() : null
			);
		},
		/**
		 * Splits this token at the specified child
		 *
		 * @param  {TokenizeToken} splitAt The child to split at
		 * @return {TokenizeToken} The right half of the split token or
		 *                         empty clone if invalid splitAt lcoation
		 */
		splitAt: function (splitAt) {
			var offsetLength;
			var base         = this;
			var	clone        = base.clone();
			var offset       = base.children.indexOf(splitAt);

			if (offset > -1) {
				// Work out how many items are on the right side of the split
				// to pass to splice()
				offsetLength   = base.children.length - offset;
				clone.children = base.children.splice(offset, offsetLength);
			}

			return clone;
		}
	};


	/**
	 * SCEditor BBCode parser class
	 *
	 * @param {Object} options
	 * @class BBCodeParser
	 * @name BBCodeParser
	 * @since v1.4.0
	 */
	function BBCodeParser(options) {
		var base = this;

		base.opts = extend({}, BBCodeParser.defaults, options);

		/**
		 * Takes a BBCode string and splits it into open,
		 * content and close tags.
		 *
		 * It does no checking to verify a tag has a matching open
		 * or closing tag or if the tag is valid child of any tag
		 * before it. For that the tokens should be passed to the
		 * parse function.
		 *
		 * @param {string} str
		 * @return {array}
		 * @memberOf BBCodeParser.prototype
		 */
		base.tokenize = function (str) {
			var	matches, type, i;
			var tokens = [];
			// The token types in reverse order of precedence
			// (they're looped in reverse)
			var tokenTypes = [
				{
					type: TOKEN_CONTENT,
					regex: /^([^\[\r\n]+|\[)/
				},
				{
					type: TOKEN_NEWLINE,
					regex: /^(\r\n|\r|\n)/
				},
				{
					type: TOKEN_OPEN,
					regex: /^\[[^\[\]]+\]/
				},
				// Close must come before open as they are
				// the same except close has a / at the start.
				{
					type: TOKEN_CLOSE,
					regex: /^\[\/[^\[\]]+\]/
				}
			];

			strloop:
			while (str.length) {
				i = tokenTypes.length;
				while (i--) {
					type = tokenTypes[i].type;

					// Check if the string matches any of the tokens
					if (!(matches = str.match(tokenTypes[i].regex)) ||
						!matches[0]) {
						continue;
					}

					// Add the match to the tokens list
					tokens.push(tokenizeTag(type, matches[0]));

					// Remove the match from the string
					str = str.substr(matches[0].length);

					// The token has been added so start again
					continue strloop;
				}

				// If there is anything left in the string which doesn't match
				// any of the tokens then just assume it's content and add it.
				if (str.length) {
					tokens.push(tokenizeTag(TOKEN_CONTENT, str));
				}

				str = '';
			}

			return tokens;
		};

		/**
		 * Extracts the name an params from a tag
		 *
		 * @param {string} type
		 * @param {string} val
		 * @return {Object}
		 * @private
		 */
		function tokenizeTag(type, val) {
			var matches, attrs, name,
				openRegex  = /\[([^\]\s=]+)(?:([^\]]+))?\]/,
				closeRegex = /\[\/([^\[\]]+)\]/;

			// Extract the name and attributes from opening tags and
			// just the name from closing tags.
			if (type === TOKEN_OPEN && (matches = val.match(openRegex))) {
				name = lower(matches[1]);

				if (matches[2] && (matches[2] = matches[2].trim())) {
					attrs = tokenizeAttrs(matches[2]);
				}
			}

			if (type === TOKEN_CLOSE &&
				(matches = val.match(closeRegex))) {
				name = lower(matches[1]);
			}

			if (type === TOKEN_NEWLINE) {
				name = '#newline';
			}

			// Treat all tokens without a name and
			// all unknown BBCodes as content
			if (!name || ((type === TOKEN_OPEN || type === TOKEN_CLOSE) &&
				!bbcodeHandlers[name])) {

				type = TOKEN_CONTENT;
				name = '#';
			}

			return new TokenizeToken(type, name, val, attrs);
		}

		/**
		 * Extracts the individual attributes from a string containing
		 * all the attributes.
		 *
		 * @param {string} attrs
		 * @return {Object} Assoc array of attributes
		 * @private
		 */
		function tokenizeAttrs(attrs) {
			var	matches,
				/*
				([^\s=]+)				Anything that's not a space or equals
				=						Equals sign =
				(?:
					(?:
						(["'])					The opening quote
						(
							(?:\\\2|[^\2])*?	Anything that isn't the
												unescaped opening quote
						)
						\2						The opening quote again which
												will close the string
					)
						|				If not a quoted string then match
					(
						(?:.(?!\s\S+=))*.?		Anything that isn't part of
												[space][non-space][=] which
												would be a new attribute
					)
				)
				*/
				attrRegex = /([^\s=]+)=(?:(?:(["'])((?:\\\2|[^\2])*?)\2)|((?:.(?!\s\S+=))*.))/g,
				ret       = {};

			// if only one attribute then remove the = from the start and
			// strip any quotes
			if (attrs.charAt(0) === '=' && attrs.indexOf('=', 1) < 0) {
				ret.defaultattr = _stripQuotes(attrs.substr(1));
			} else {
				if (attrs.charAt(0) === '=') {
					attrs = 'defaultattr' + attrs;
				}

				// No need to strip quotes here, the regex will do that.
				while ((matches = attrRegex.exec(attrs))) {
					ret[lower(matches[1])] =
						_stripQuotes(matches[3]) || matches[4];
				}
			}

			return ret;
		}

		/**
		 * Parses a string into an array of BBCodes
		 *
		 * @param  {string}  str
		 * @param  {boolean} preserveNewLines If to preserve all new lines, not
		 *                                    strip any based on the passed
		 *                                    formatting options
		 * @return {array}                    Array of BBCode objects
		 * @memberOf BBCodeParser.prototype
		 */
		base.parse = function (str, preserveNewLines) {
			var ret  = parseTokens(base.tokenize(str));
			var opts = base.opts;

			if (opts.fixInvalidNesting) {
				fixNesting(ret);
			}

			normaliseNewLines(ret, null, preserveNewLines);

			if (opts.removeEmptyTags) {
				removeEmpty(ret);
			}

			return ret;
		};

		/**
		 * Checks if an array of TokenizeToken's contains the
		 * specified token.
		 *
		 * Checks the tokens name and type match another tokens
		 * name and type in the array.
		 *
		 * @param  {string}    name
		 * @param  {string} type
		 * @param  {array}     arr
		 * @return {Boolean}
		 * @private
		 */
		function hasTag(name, type, arr) {
			var i = arr.length;

			while (i--) {
				if (arr[i].type === type && arr[i].name === name) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Checks if the child tag is allowed as one
		 * of the parent tags children.
		 *
		 * @param  {TokenizeToken}  parent
		 * @param  {TokenizeToken}  child
		 * @return {Boolean}
		 * @private
		 */
		function isChildAllowed(parent, child) {
			var	parentBBCode    = parent ? bbcodeHandlers[parent.name] : {},
				allowedChildren = parentBBCode.allowedChildren;

			if (base.opts.fixInvalidChildren && allowedChildren) {
				return allowedChildren.indexOf(child.name || '#') > -1;
			}

			return true;
		}

		// TODO: Tidy this parseTokens() function up a bit.
		/**
		 * Parses an array of tokens created by tokenize()
		 *
		 * @param  {array} toks
		 * @return {array} Parsed tokens
		 * @see tokenize()
		 * @private
		 */
		function parseTokens(toks) {
			var	token, bbcode, curTok, clone, i, next,
				cloned     = [],
				output     = [],
				openTags   = [],
				/**
				 * Returns the currently open tag or undefined
				 * @return {TokenizeToken}
				 */
				currentTag = function () {
					return last(openTags);
				},
				/**
				 * Adds a tag to either the current tags children
				 * or to the output array.
				 * @param {TokenizeToken} token
				 * @private
				 */
				addTag = function (token) {
					if (currentTag()) {
						currentTag().children.push(token);
					} else {
						output.push(token);
					}
				},
				/**
				 * Checks if this tag closes the current tag
				 * @param  {string} name
				 * @return {Void}
				 */
				closesCurrentTag = function (name) {
					return currentTag() &&
						(bbcode = bbcodeHandlers[currentTag().name]) &&
						bbcode.closedBy &&
						bbcode.closedBy.indexOf(name) > -1;
				};

			while ((token = toks.shift())) {
				next = toks[0];

				/*
				 * Fixes any invalid children.
				 *
				 * If it is an element which isn't allowed as a child of it's
				 * parent then it will be converted to content of the parent
				 * element. i.e.
				 *     [code]Code [b]only[/b] allows text.[/code]
				 * Will become:
				 *     <code>Code [b]only[/b] allows text.</code>
				 * Instead of:
				 *     <code>Code <b>only</b> allows text.</code>
				 */
				// Ignore tags that can't be children
				if (!isChildAllowed(currentTag(), token)) {

					// exclude closing tags of current tag
					if (token.type !== TOKEN_CLOSE || !currentTag() ||
							token.name !== currentTag().name) {
						token.name = '#';
						token.type = TOKEN_CONTENT;
					}
				}

				switch (token.type) {
					case TOKEN_OPEN:
						// Check it this closes a parent,
						// e.g. for lists [*]one [*]two
						if (closesCurrentTag(token.name)) {
							openTags.pop();
						}

						addTag(token);
						bbcode = bbcodeHandlers[token.name];

						// If this tag is not self closing and it has a closing
						// tag then it is open and has children so add it to the
						// list of open tags. If has the closedBy property then
						// it is closed by other tags so include everything as
						// it's children until one of those tags is reached.
						if (bbcode && !bbcode.isSelfClosing &&
							(bbcode.closedBy ||
								hasTag(token.name, TOKEN_CLOSE, toks))) {
							openTags.push(token);
						} else if (!bbcode || !bbcode.isSelfClosing) {
							token.type = TOKEN_CONTENT;
						}
						break;

					case TOKEN_CLOSE:
						// check if this closes the current tag,
						// e.g. [/list] would close an open [*]
						if (currentTag() && token.name !== currentTag().name &&
							closesCurrentTag('/' + token.name)) {

							openTags.pop();
						}

						// If this is closing the currently open tag just pop
						// the close tag off the open tags array
						if (currentTag() && token.name === currentTag().name) {
							currentTag().closing = token;
							openTags.pop();

						// If this is closing an open tag that is the parent of
						// the current tag then clone all the tags including the
						// current one until reaching the parent that is being
						// closed. Close the parent and then add the clones back
						// in.
						} else if (hasTag(token.name, TOKEN_OPEN, openTags)) {

							// Remove the tag from the open tags
							while ((curTok = openTags.pop())) {

								// If it's the tag that is being closed then
								// discard it and break the loop.
								if (curTok.name === token.name) {
									curTok.closing = token;
									break;
								}

								// Otherwise clone this tag and then add any
								// previously cloned tags as it's children
								clone = curTok.clone();

								if (cloned.length) {
									clone.children.push(last(cloned));
								}

								cloned.push(clone);
							}

							// Place block linebreak before cloned tags
							if (next && next.type === TOKEN_NEWLINE) {
								bbcode = bbcodeHandlers[token.name];
								if (bbcode && bbcode.isInline === false) {
									addTag(next);
									toks.shift();
								}
							}

							// Add the last cloned child to the now current tag
							// (the parent of the tag which was being closed)
							addTag(last(cloned));

							// Add all the cloned tags to the open tags list
							i = cloned.length;
							while (i--) {
								openTags.push(cloned[i]);
							}

							cloned.length = 0;

						// This tag is closing nothing so treat it as content
						} else {
							token.type = TOKEN_CONTENT;
							addTag(token);
						}
						break;

					case TOKEN_NEWLINE:
						// handle things like
						//     [*]list\nitem\n[*]list1
						// where it should come out as
						//     [*]list\nitem[/*]\n[*]list1[/*]
						// instead of
						//     [*]list\nitem\n[/*][*]list1[/*]
						if (currentTag() && next && closesCurrentTag(
							(next.type === TOKEN_CLOSE ? '/' : '') +
							next.name
						)) {
							// skip if the next tag is the closing tag for
							// the option tag, i.e. [/*]
							if (!(next.type === TOKEN_CLOSE &&
								next.name === currentTag().name)) {
								bbcode = bbcodeHandlers[currentTag().name];

								if (bbcode && bbcode.breakAfter) {
									openTags.pop();
								} else if (bbcode &&
									bbcode.isInline === false &&
									base.opts.breakAfterBlock &&
									bbcode.breakAfter !== false) {
									openTags.pop();
								}
							}
						}

						addTag(token);
						break;

					default: // content
						addTag(token);
						break;
				}
			}

			return output;
		}

		/**
		 * Normalise all new lines
		 *
		 * Removes any formatting new lines from the BBCode
		 * leaving only content ones. I.e. for a list:
		 *
		 * [list]
		 * [*] list item one
		 * with a line break
		 * [*] list item two
		 * [/list]
		 *
		 * would become
		 *
		 * [list] [*] list item one
		 * with a line break [*] list item two [/list]
		 *
		 * Which makes it easier to convert to HTML or add
		 * the formatting new lines back in when converting
		 * back to BBCode
		 *
		 * @param  {array} children
		 * @param  {TokenizeToken} parent
		 * @param  {boolean} onlyRemoveBreakAfter
		 * @return {void}
		 */
		function normaliseNewLines(children, parent, onlyRemoveBreakAfter) {
			var	token, left, right, parentBBCode, bbcode,
				removedBreakEnd, removedBreakBefore, remove;
			var childrenLength = children.length;
			// TODO: this function really needs tidying up
			if (parent) {
				parentBBCode = bbcodeHandlers[parent.name];
			}

			var i = childrenLength;
			while (i--) {
				if (!(token = children[i])) {
					continue;
				}

				if (token.type === TOKEN_NEWLINE) {
					left   = i > 0 ? children[i - 1] : null;
					right  = i < childrenLength - 1 ? children[i + 1] : null;
					remove = false;

					// Handle the start and end new lines
					// e.g. [tag]\n and \n[/tag]
					if (!onlyRemoveBreakAfter && parentBBCode &&
						parentBBCode.isSelfClosing !== true) {
						// First child of parent so must be opening line break
						// (breakStartBlock, breakStart) e.g. [tag]\n
						if (!left) {
							if (parentBBCode.isInline === false &&
								base.opts.breakStartBlock &&
								parentBBCode.breakStart !== false) {
								remove = true;
							}

							if (parentBBCode.breakStart) {
								remove = true;
							}
						// Last child of parent so must be end line break
						// (breakEndBlock, breakEnd)
						// e.g. \n[/tag]
						// remove last line break (breakEndBlock, breakEnd)
						} else if (!removedBreakEnd && !right) {
							if (parentBBCode.isInline === false &&
								base.opts.breakEndBlock &&
								parentBBCode.breakEnd !== false) {
								remove = true;
							}

							if (parentBBCode.breakEnd) {
								remove = true;
							}

							removedBreakEnd = remove;
						}
					}

					if (left && left.type === TOKEN_OPEN) {
						if ((bbcode = bbcodeHandlers[left.name])) {
							if (!onlyRemoveBreakAfter) {
								if (bbcode.isInline === false &&
									base.opts.breakAfterBlock &&
									bbcode.breakAfter !== false) {
									remove = true;
								}

								if (bbcode.breakAfter) {
									remove = true;
								}
							} else if (bbcode.isInline === false) {
								remove = true;
							}
						}
					}

					if (!onlyRemoveBreakAfter && !removedBreakBefore &&
						right && right.type === TOKEN_OPEN) {

						if ((bbcode = bbcodeHandlers[right.name])) {
							if (bbcode.isInline === false &&
								base.opts.breakBeforeBlock &&
								bbcode.breakBefore !== false) {
								remove = true;
							}

							if (bbcode.breakBefore) {
								remove = true;
							}

							removedBreakBefore = remove;

							if (remove) {
								children.splice(i, 1);
								continue;
							}
						}
					}

					if (remove) {
						children.splice(i, 1);
					}

					// reset double removedBreakBefore removal protection.
					// This is needed for cases like \n\n[\tag] where
					// only 1 \n should be removed but without this they both
					// would be.
					removedBreakBefore = false;
				} else if (token.type === TOKEN_OPEN) {
					normaliseNewLines(token.children, token,
						onlyRemoveBreakAfter);
				}
			}
		}

		/**
		 * Fixes any invalid nesting.
		 *
		 * If it is a block level element inside 1 or more inline elements
		 * then those inline elements will be split at the point where the
		 * block level is and the block level element placed between the split
		 * parts. i.e.
		 *     [inline]A[blocklevel]B[/blocklevel]C[/inline]
		 * Will become:
		 *     [inline]A[/inline][blocklevel]B[/blocklevel][inline]C[/inline]
		 *
		 * @param {array} children
		 * @param {array} [parents] Null if there is no parents
		 * @param {boolea} [insideInline] If inside an inline element
		 * @param {array} [rootArr] Root array if there is one
		 * @return {array}
		 * @private
		 */
		function fixNesting(children, parents, insideInline, rootArr) {
			var	token, i, parent, parentIndex, parentParentChildren, right;

			var isInline = function (token) {
				var bbcode = bbcodeHandlers[token.name];

				return !bbcode || bbcode.isInline !== false;
			};

			parents = parents || [];
			rootArr = rootArr || children;

			// This must check the length each time as it can change when
			// tokens are moved to fix the nesting.
			for (i = 0; i < children.length; i++) {
				if (!(token = children[i]) || token.type !== TOKEN_OPEN) {
					continue;
				}

				if (insideInline && !isInline(token)) {
					// if this is a blocklevel element inside an inline one then
					// split the parent at the block level element
					parent = last(parents);
					right  = parent.splitAt(token);

					parentParentChildren = parents.length > 1 ?
						parents[parents.length - 2].children : rootArr;

					// If parent inline is allowed inside this tag, clone it and
					// wrap this tags children in it.
					if (isChildAllowed(token, parent)) {
						var clone = parent.clone();
						clone.children = token.children;
						token.children = [clone];
					}

					parentIndex = parentParentChildren.indexOf(parent);
					if (parentIndex > -1) {
						// remove the block level token from the right side of
						// the split inline element
						right.children.splice(0, 1);

						// insert the block level token and the right side after
						// the left side of the inline token
						parentParentChildren.splice(
							parentIndex + 1, 0, token, right
						);

						// If token is a block and is followed by a newline,
						// then move the newline along with it to the new parent
						var next = right.children[0];
						if (next && next.type === TOKEN_NEWLINE) {
							if (!isInline(token)) {
								right.children.splice(0, 1);
								parentParentChildren.splice(
									parentIndex + 2, 0, next
								);
							}
						}

						// return to parents loop as the
						// children have now increased
						return;
					}

				}

				parents.push(token);

				fixNesting(
					token.children,
					parents,
					insideInline || isInline(token),
					rootArr
				);

				parents.pop();
			}
		}

		/**
		 * Removes any empty BBCodes which are not allowed to be empty.
		 *
		 * @param {array} tokens
		 * @private
		 */
		function removeEmpty(tokens) {
			var	token, bbcode;

			/**
			 * Checks if all children are whitespace or not
			 * @private
			 */
			var isTokenWhiteSpace = function (children) {
				var j = children.length;

				while (j--) {
					var type = children[j].type;

					if (type === TOKEN_OPEN || type === TOKEN_CLOSE) {
						return false;
					}

					if (type === TOKEN_CONTENT &&
						/\S|\u00A0/.test(children[j].val)) {
						return false;
					}
				}

				return true;
			};

			var i = tokens.length;
			while (i--) {
				// So skip anything that isn't a tag since only tags can be
				// empty, content can't
				if (!(token = tokens[i]) || token.type !== TOKEN_OPEN) {
					continue;
				}

				bbcode = bbcodeHandlers[token.name];

				// Remove any empty children of this tag first so that if they
				// are all removed this one doesn't think it's not empty.
				removeEmpty(token.children);

				if (isTokenWhiteSpace(token.children) && bbcode &&
					!bbcode.isSelfClosing && !bbcode.allowsEmpty) {
					tokens.splice.apply(tokens, [i, 1].concat(token.children));
				}
			}
		}

		/**
		 * Converts a BBCode string to HTML
		 *
		 * @param {string} str
		 * @param {boolean}   preserveNewLines If to preserve all new lines, not
		 *                                  strip any based on the passed
		 *                                  formatting options
		 * @return {string}
		 * @memberOf BBCodeParser.prototype
		 */
		base.toHTML = function (str, preserveNewLines) {
			return convertToHTML(base.parse(str, preserveNewLines), true);
		};

		/**
		 * @private
		 */
		function convertToHTML(tokens, isRoot) {
			var	undef, token, bbcode, content, html, needsBlockWrap,
				blockWrapOpen, isInline, lastChild,
				ret = '';

			isInline = function (bbcode) {
				return (!bbcode || (bbcode.isHtmlInline !== undef ?
					bbcode.isHtmlInline : bbcode.isInline)) !== false;
			};

			while (tokens.length > 0) {
				if (!(token = tokens.shift())) {
					continue;
				}

				if (token.type === TOKEN_OPEN) {
					lastChild = token.children[token.children.length - 1] || {};
					bbcode = bbcodeHandlers[token.name];
					needsBlockWrap = isRoot && isInline(bbcode);
					content = convertToHTML(token.children, false);

					if (bbcode && bbcode.html) {
						// Only add a line break to the end if this is
						// blocklevel and the last child wasn't block-level
						if (!isInline(bbcode) &&
							isInline(bbcodeHandlers[lastChild.name]) &&
							!bbcode.isPreFormatted &&
							!bbcode.skipLastLineBreak) {
							// Add placeholder br to end of block level
							// elements
							content += '<br />';
						}

						if (!isFunction(bbcode.html)) {
							token.attrs['0'] = content;
							html = formatBBCodeString(
								bbcode.html,
								token.attrs
							);
						} else {
							html = bbcode.html.call(
								base,
								token,
								token.attrs,
								content
							);
						}
					} else {
						html = token.val + content +
							(token.closing ? token.closing.val : '');
					}
				} else if (token.type === TOKEN_NEWLINE) {
					if (!isRoot) {
						ret += '<br />';
						continue;
					}

					// If not already in a block wrap then start a new block
					if (!blockWrapOpen) {
						ret += '<div>';
					}

					ret += '<br />';

					// Normally the div acts as a line-break with by moving
					// whatever comes after onto a new line.
					// If this is the last token, add an extra line-break so it
					// shows as there will be nothing after it.
					if (!tokens.length) {
						ret += '<br />';
					}

					ret += '</div>\n';
					blockWrapOpen = false;
					continue;
				// content
				} else {
					needsBlockWrap = isRoot;
					html           = escapeEntities(token.val, true);
				}

				if (needsBlockWrap && !blockWrapOpen) {
					ret += '<div>';
					blockWrapOpen = true;
				} else if (!needsBlockWrap && blockWrapOpen) {
					ret += '</div>\n';
					blockWrapOpen = false;
				}

				ret += html;
			}

			if (blockWrapOpen) {
				ret += '</div>\n';
			}

			return ret;
		}

		/**
		 * Takes a BBCode string, parses it then converts it back to BBCode.
		 *
		 * This will auto fix the BBCode and format it with the specified
		 * options.
		 *
		 * @param {string} str
		 * @param {boolean} preserveNewLines If to preserve all new lines, not
		 *                                strip any based on the passed
		 *                                formatting options
		 * @return {string}
		 * @memberOf BBCodeParser.prototype
		 */
		base.toBBCode = function (str, preserveNewLines) {
			return convertToBBCode(base.parse(str, preserveNewLines));
		};

		/**
		 * Converts parsed tokens back into BBCode with the
		 * formatting specified in the options and with any
		 * fixes specified.
		 *
		 * @param  {array} toks Array of parsed tokens from base.parse()
		 * @return {string}
		 * @private
		 */
		function convertToBBCode(toks) {
			var	token, attr, bbcode, isBlock, isSelfClosing, quoteType,
				breakBefore, breakStart, breakEnd, breakAfter,
				ret = '';

			while (toks.length > 0) {
				if (!(token = toks.shift())) {
					continue;
				}
				// TODO: tidy this
				bbcode        = bbcodeHandlers[token.name];
				isBlock       = !(!bbcode || bbcode.isInline !== false);
				isSelfClosing = bbcode && bbcode.isSelfClosing;

				breakBefore = (isBlock && base.opts.breakBeforeBlock &&
						bbcode.breakBefore !== false) ||
					(bbcode && bbcode.breakBefore);

				breakStart = (isBlock && !isSelfClosing &&
						base.opts.breakStartBlock &&
						bbcode.breakStart !== false) ||
					(bbcode && bbcode.breakStart);

				breakEnd = (isBlock && base.opts.breakEndBlock &&
						bbcode.breakEnd !== false) ||
					(bbcode && bbcode.breakEnd);

				breakAfter = (isBlock && base.opts.breakAfterBlock &&
						bbcode.breakAfter !== false) ||
					(bbcode && bbcode.breakAfter);

				quoteType = (bbcode ? bbcode.quoteType : null) ||
					base.opts.quoteType || QuoteType.auto;

				if (!bbcode && token.type === TOKEN_OPEN) {
					ret += token.val;

					if (token.children) {
						ret += convertToBBCode(token.children);
					}

					if (token.closing) {
						ret += token.closing.val;
					}
				} else if (token.type === TOKEN_OPEN) {
					if (breakBefore) {
						ret += '\n';
					}

					// Convert the tag and it's attributes to BBCode
					ret += '[' + token.name;
					if (token.attrs) {
						if (token.attrs.defaultattr) {
							ret += '=' + quote(
								token.attrs.defaultattr,
								quoteType,
								'defaultattr'
							);

							delete token.attrs.defaultattr;
						}

						for (attr in token.attrs) {
							if (token.attrs.hasOwnProperty(attr)) {
								ret += ' ' + attr + '=' +
									quote(token.attrs[attr], quoteType, attr);
							}
						}
					}
					ret += ']';

					if (breakStart) {
						ret += '\n';
					}

					// Convert the tags children to BBCode
					if (token.children) {
						ret += convertToBBCode(token.children);
					}

					// add closing tag if not self closing
					if (!isSelfClosing && !bbcode.excludeClosing) {
						if (breakEnd) {
							ret += '\n';
						}

						ret += '[/' + token.name + ']';
					}

					if (breakAfter) {
						ret += '\n';
					}

					// preserve whatever was recognized as the
					// closing tag if it is a self closing tag
					if (token.closing && isSelfClosing) {
						ret += token.closing.val;
					}
				} else {
					ret += token.val;
				}
			}

			return ret;
		}

		/**
		 * Quotes an attribute
		 *
		 * @param {string} str
		 * @param {BBCodeParser.QuoteType} quoteType
		 * @param {string} name
		 * @return {string}
		 * @private
		 */
		function quote(str, quoteType, name) {
			var	needsQuotes = /\s|=/.test(str);

			if (isFunction(quoteType)) {
				return quoteType(str, name);
			}

			if (quoteType === QuoteType.never ||
				(quoteType === QuoteType.auto && !needsQuotes)) {
				return str;
			}

			return '"' + str.replace(/\\/g, '\\\\').replace(/"/g, '\\"') + '"';
		}

		/**
		 * Returns the last element of an array or null
		 *
		 * @param {array} arr
		 * @return {Object} Last element
		 * @private
		 */
		function last(arr) {
			if (arr.length) {
				return arr[arr.length - 1];
			}

			return null;
		}

		/**
		 * Converts a string to lowercase.
		 *
		 * @param {string} str
		 * @return {string} Lowercase version of str
		 * @private
		 */
		function lower(str) {
			return str.toLowerCase();
		}
	};

	/**
	 * Quote type
	 * @type {Object}
	 * @class QuoteType
	 * @name BBCodeParser.QuoteType
	 * @since 1.4.0
	 */
	BBCodeParser.QuoteType = QuoteType;

	/**
	 * Default BBCode parser options
	 * @type {Object}
	 */
	BBCodeParser.defaults = {
		/**
		 * If to add a new line before block level elements
		 *
		 * @type {Boolean}
		 */
		breakBeforeBlock: false,

		/**
		 * If to add a new line after the start of block level elements
		 *
		 * @type {Boolean}
		 */
		breakStartBlock: false,

		/**
		 * If to add a new line before the end of block level elements
		 *
		 * @type {Boolean}
		 */
		breakEndBlock: false,

		/**
		 * If to add a new line after block level elements
		 *
		 * @type {Boolean}
		 */
		breakAfterBlock: true,

		/**
		 * If to remove empty tags
		 *
		 * @type {Boolean}
		 */
		removeEmptyTags: true,

		/**
		 * If to fix invalid nesting,
		 * i.e. block level elements inside inline elements.
		 *
		 * @type {Boolean}
		 */
		fixInvalidNesting: true,

		/**
		 * If to fix invalid children.
		 * i.e. A tag which is inside a parent that doesn't
		 * allow that type of tag.
		 *
		 * @type {Boolean}
		 */
		fixInvalidChildren: true,

		/**
		 * Attribute quote type
		 *
		 * @type {BBCodeParser.QuoteType}
		 * @since 1.4.1
		 */
		quoteType: QuoteType.auto,

		/**
		 * Whether to use strict matching on attributes and styles.
		 *
		 * When true this will perform AND matching requiring all tag
		 * attributes and styles to match.
		 *
		 * When false will perform OR matching and will match if any of
		 * a tags attributes or styles match.
		 *
		 * @type {Boolean}
		 * @since 3.1.0
		 */
		strictMatch: false
	};

	/**
	 * Converts a number 0-255 to hex.
	 *
	 * Will return 00 if number is not a valid number.
	 *
	 * @param  {any} number
	 * @return {string}
	 * @private
	 */
	function toHex(number) {
		number = parseInt(number, 10);

		if (isNaN(number)) {
			return '00';
		}

		number = Math.max(0, Math.min(number, 255)).toString(16);

		return number.length < 2 ? '0' + number : number;
	}
	/**
	 * Normalises a CSS colour to hex #xxxxxx format
	 *
	 * @param  {string} colorStr
	 * @return {string}
	 * @private
	 */
	function _normaliseColour(colorStr) {
		var match;

		colorStr = colorStr || '#000';

		// rgb(n,n,n);
		if ((match =
			colorStr.match(/rgb\((\d{1,3}),\s*?(\d{1,3}),\s*?(\d{1,3})\)/i))) {
			return '#' +
				toHex(match[1]) +
				toHex(match[2]) +
				toHex(match[3]);
		}

		// expand shorthand
		if ((match = colorStr.match(/#([0-f])([0-f])([0-f])\s*?$/i))) {
			return '#' +
				match[1] + match[1] +
				match[2] + match[2] +
				match[3] + match[3];
		}

		return colorStr;
	}

	/**
	 * SCEditor BBCode format
	 * @since 2.0.0
	 */
	function bbcodeFormat() {
		var base = this;

		base.stripQuotes = _stripQuotes;

		/**
		 * cache of all the tags pointing to their bbcodes to enable
		 * faster lookup of which bbcode a tag should have
		 * @private
		 */
		var tagsToBBCodes = {};

		/**
		 * Allowed children of specific HTML tags. Empty array if no
		 * children other than text nodes are allowed
		 * @private
		 */
		var validChildren = {
			ul: ['li', 'ol', 'ul'],
			ol: ['li', 'ol', 'ul'],
			table: ['tr'],
			tr: ['td', 'th'],
			code: ['br', 'p', 'div']
		};

		/**
		 * Populates tagsToBBCodes and stylesToBBCodes for easier lookups
		 *
		 * @private
		 */
		function buildBbcodeCache() {
			each(bbcodeHandlers, function (bbcode, handler) {
				var
					isBlock = handler.isInline === false,
					tags   = bbcodeHandlers[bbcode].tags,
					styles = bbcodeHandlers[bbcode].styles;

				if (styles) {
					tagsToBBCodes['*'] = tagsToBBCodes['*'] || {};
					tagsToBBCodes['*'][isBlock] =
						tagsToBBCodes['*'][isBlock] || {};
					tagsToBBCodes['*'][isBlock][bbcode] = [
						['style', Object.entries(styles)]
					];
				}

				if (tags) {
					each(tags, function (tag, values) {
						if (values && values.style) {
							values.style = Object.entries(values.style);
						}

						tagsToBBCodes[tag] = tagsToBBCodes[tag] || {};
						tagsToBBCodes[tag][isBlock] =
							tagsToBBCodes[tag][isBlock] || {};
						tagsToBBCodes[tag][isBlock][bbcode] =
							values && Object.entries(values);
					});
				}
			});
		};

		/**
		 * Handles adding newlines after block level elements
		 *
		 * @param {HTMLElement} element The element to convert
		 * @param {string} content  The tags text content
		 * @return {string}
		 * @private
		 */
		function handleBlockNewlines(element, content) {
			var	tag = element.nodeName.toLowerCase();
			var isInline = dom.isInline;
			if (!isInline(element, true) || tag === 'br') {
				var	isLastBlockChild, parent, parentLastChild,
					previousSibling = element.previousSibling;

				// Skips selection makers and ignored elements
				// Skip empty inline elements
				while (previousSibling &&
						previousSibling.nodeType === 1 &&
						!is(previousSibling, 'br') &&
						isInline(previousSibling, true) &&
						!previousSibling.firstChild) {
					previousSibling = previousSibling.previousSibling;
				}

				// If it's the last block of an inline that is the last
				// child of a block then it shouldn't cause a line break
				// <block><inline><br></inline></block>
				do {
					parent          = element.parentNode;
					parentLastChild = parent && parent.lastChild;

					isLastBlockChild = parentLastChild === element;
					element = parent;
				} while (parent && isLastBlockChild && isInline(parent, true));

				// If this block is:
				//	* Not the last child of a block level element
				//	* Is a <li> tag (lists are blocks)
				if (!isLastBlockChild || tag === 'li') {
					content += '\n';
				}

				// Check for:
				// <block>text<block>text</block></block>
				//
				// The second opening <block> opening tag should cause a
				// line break because the previous sibing is inline.
				if (tag !== 'br' && previousSibling &&
					!is(previousSibling, 'br') &&
					isInline(previousSibling, true)) {
					content = '\n' + content;
				}
			}

			return content;
		}

		/**
		 * Handles a HTML tag and finds any matching BBCodes
		 *
		 * @param {HTMLElement} element The element to convert
		 * @param {string} content  The Tags text content
		 * @param {boolean} blockLevel
		 * @return {string} Content with any matching BBCode tags
		 *                  wrapped around it.
		 * @private
		 */
		function handleTags(element, content, blockLevel) {
			function isStyleMatch(style) {
				var property = style[0];
				var values = style[1];
				var val = dom.getStyle(element, property);
				var parent = element.parentNode;

				// if the parent has the same style use that instead of this one
				// so you don't end up with [i]parent[i]child[/i][/i]
				if (!val || parent && dom.hasStyle(parent, property, val)) {
					return false;
				}

				return !values || values.includes(val);
			}

			function createAttributeMatch(isStrict) {
				return function (attribute) {
					var name = attribute[0];
					var value = attribute[1];

					// code tags should skip most styles
					if (name === 'style' && element.nodeName === 'CODE') {
						return false;
					}

					if (name === 'style' && value) {
						return value[isStrict ? 'every' : 'some'](isStyleMatch);
					} else {
						var val = attr(element, name);

						return val && (!value || value.includes(val));
					}
				};
			}

			function handleTag(tag) {
				if (!tagsToBBCodes[tag] || !tagsToBBCodes[tag][blockLevel]) {
					return;
				}

				// loop all bbcodes for this tag
				each(tagsToBBCodes[tag][blockLevel], function (bbcode, attrs) {
					var fn, format,
						isStrict = bbcodeHandlers[bbcode].strictMatch;

					if (typeof isStrict === 'undefined') {
						isStrict = base.opts.strictMatch;
					}

					// Skip if the element doesn't have the attribute or the
					// attribute doesn't match one of the required values
					fn = isStrict ? 'every' : 'some';
					if (attrs && !attrs[fn](createAttributeMatch(isStrict))) {
						return;
					}

					format = bbcodeHandlers[bbcode].format;
					if (isFunction(format)) {
						content = format.call(base, element, content);
					} else {
						content = _formatString(format, content);
					}
					return false;
				});
			}

			handleTag('*');
			handleTag(element.nodeName.toLowerCase());
			return content;
		}

		/**
		 * Converts a HTML dom element to BBCode starting from
		 * the innermost element and working backwards
		 *
		 * @private
		 * @param {HTMLElement}	element
		 * @return {string} BBCode
		 * @memberOf SCEditor.plugins.bbcode.prototype
		 */
		function elementToBbcode(element) {
			var toBBCode = function (node, vChildren) {
				var ret = '';

				dom.traverse(node, function (node) {
					var	content      = '',
						nodeType     = node.nodeType,
						tag          = node.nodeName.toLowerCase(),
						vChild       = validChildren[tag],
						firstChild   = node.firstChild,
						isValidChild = true;

					if (typeof vChildren === 'object') {
						isValidChild = vChildren.indexOf(tag) > -1;

						// Emoticons should always be converted
						if (is(node, 'img') && attr(node, EMOTICON_DATA_ATTR)) {
							isValidChild = true;
						}

						// if this tag is one of the parents allowed children
						// then set this tags allowed children to whatever it
						// allows, otherwise set to what the parent allows
						if (!isValidChild) {
							vChild = vChildren;
						}
					}

					// 3 = text and 1 = element
					if (nodeType !== 3 && nodeType !== 1) {
						return;
					}

					if (nodeType === 1) {
						// skip empty nlf elements (new lines automatically
						// added after block level elements like quotes)
						if (is(node, '.sceditor-nlf') && !firstChild) {
							return;
						}

						// don't convert iframe contents
						if (tag !== 'iframe') {
							content = toBBCode(node, vChild);
						}

						// TODO: isValidChild is no longer needed. Should use
						// valid children bbcodes instead by creating BBCode
						// tokens like the parser.
						if (isValidChild) {
							// code tags should skip most styles
							if (tag !== 'code') {
								// First parse inline codes
								content = handleTags(node, content, false);
							}

							content = handleTags(node, content, true);
							ret += handleBlockNewlines(node, content);
						} else {
							ret += content;
						}
					} else {
						ret += node.nodeValue;
					}
				}, false, true);

				return ret;
			};

			return toBBCode(element);
		};

		/**
		 * Initializer
		 * @private
		 */
		base.init = function () {
			base.opts = this.opts;
			base.elementToBbcode = elementToBbcode;

			// build the BBCode cache
			buildBbcodeCache();

			this.commands = extend(
				true, {}, defaultCommandsOverrides, this.commands
			);

			// Add BBCode helper methods
			this.toBBCode   = base.toSource;
			this.fromBBCode = base.toHtml;
		};

		/**
		 * Converts BBCode into HTML
		 *
		 * @param {boolean} asFragment
		 * @param {string} source
		 * @param {boolean} [legacyAsFragment] Used by fromBBCode() method
		 */
		function toHtml(asFragment, source, legacyAsFragment) {
			var	parser = new BBCodeParser(base.opts.parserOptions);
			var html = parser.toHTML(
				base.opts.bbcodeTrim ? source.trim() : source
			);

			return (asFragment || legacyAsFragment) ?
				removeFirstLastDiv(html) : html;
		}

		/**
		 * Converts HTML into BBCode
		 *
		 * @param {boolean} asFragment
		 * @param {string}	html
		 * @param {!Document} [context]
		 * @param {!HTMLElement} [parent]
		 * @return {string}
		 * @private
		 */
		function toSource(asFragment, html, context, parent) {
			context = context || document;

			var	bbcode, elements;
			var containerParent = context.createElement('div');
			var container = context.createElement('div');
			var parser = new BBCodeParser(base.opts.parserOptions);

			container.innerHTML = html;
			css(containerParent, 'visibility', 'hidden');
			containerParent.appendChild(container);
			context.body.appendChild(containerParent);

			if (asFragment) {
				// Add text before and after so removeWhiteSpace doesn't remove
				// leading and trailing whitespace
				containerParent.insertBefore(
					context.createTextNode('#'),
					containerParent.firstChild
				);
				containerParent.appendChild(context.createTextNode('#'));
			}

			// Match parents white-space handling
			if (parent) {
				css(container, 'whiteSpace', css(parent, 'whiteSpace'));
			}

			// Remove all nodes with sceditor-ignore class
			elements = container.getElementsByClassName('sceditor-ignore');
			while (elements.length) {
				elements[0].parentNode.removeChild(elements[0]);
			}

			dom.removeWhiteSpace(containerParent);

			bbcode = elementToBbcode(container);

			context.body.removeChild(containerParent);

			bbcode = parser.toBBCode(bbcode, true);

			if (base.opts.bbcodeTrim) {
				bbcode = bbcode.trim();
			}

			return bbcode;
		};

		base.toHtml = toHtml.bind(null, false);
		base.fragmentToHtml = toHtml.bind(null, true);
		base.toSource = toSource.bind(null, false);
		base.fragmentToSource = toSource.bind(null, true);
	};

	/**
	 * Gets a BBCode
	 *
	 * @param {string} name
	 * @return {Object|null}
	 * @since 2.0.0
	 */
	bbcodeFormat.get = function (name) {
		return bbcodeHandlers[name] || null;
	};

	/**
	 * Adds a BBCode to the parser or updates an existing
	 * BBCode if a BBCode with the specified name already exists.
	 *
	 * @param {string} name
	 * @param {Object} bbcode
	 * @return {this}
	 * @since 2.0.0
	 */
	bbcodeFormat.set = function (name, bbcode) {
		if (name && bbcode) {
			// merge any existing command properties
			bbcode = extend(bbcodeHandlers[name] || {}, bbcode);

			bbcode.remove = function () {
				delete bbcodeHandlers[name];
			};

			bbcodeHandlers[name] = bbcode;
		}

		return this;
	};

	/**
	 * Renames a BBCode
	 *
	 * This does not change the format or HTML handling, those must be
	 * changed manually.
	 *
	 * @param  {string} name    [description]
	 * @param  {string} newName [description]
	 * @return {this|false}
	 * @since 2.0.0
	 */
	bbcodeFormat.rename = function (name, newName) {
		if (name in bbcodeHandlers) {
			bbcodeHandlers[newName] = bbcodeHandlers[name];

			delete bbcodeHandlers[name];
		}

		return this;
	};

	/**
	 * Removes a BBCode
	 *
	 * @param {string} name
	 * @return {this}
	 * @since 2.0.0
	 */
	bbcodeFormat.remove = function (name) {
		if (name in bbcodeHandlers) {
			delete bbcodeHandlers[name];
		}

		return this;
	};

	bbcodeFormat.formatBBCodeString = formatBBCodeString;

	sceditor.formats.bbcode = bbcodeFormat;
	sceditor.BBCodeParser = BBCodeParser;
}(sceditor));
