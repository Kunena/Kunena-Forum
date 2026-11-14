/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.plugins.add( 'code', {
	icons: 'code',
	init: function( editor ) {
		editor.addCommand( 'insertCode', {
			exec: function( editor ) {
				var selectedtext = editor.getSelection().getSelectedText();
				editor.insertHtml( '[code]' + selectedtext + '[/code]' );
			}
		});
		editor.ui.addButton( 'Code', {
			label: Joomla.Text._('COM_KUNENA_EDITOR_CODE'),
			command: 'insertCode',
			toolbar: 'insert'
		});

		// The bbcode plugin turns the text of a [code] block into smileys, <br> and formatting. Keep the block
		// verbatim instead: before the bbcode conversion (setData, priority 10) each block is swapped for a
		// token, after it the token becomes a <pre> holding the escaped text. The opening tag and the white
		// space around the block, which the editor would drop or change next to a <pre>, are kept in attributes.
		var openRegex = /^\[code(?:=[^\]]*|\s+type=[^\]]*)?\]$/i;
		var blockRegex = /([ \t\r\n]*)(\[code(?:=[^\]]*|\s+type=[^\]]*)?\])([\s\S]*?)\[\/code\]([ \t\r\n]*)/gi;
		var loaded = {};
		var saved = {};

		editor.on( 'setData', function( evt ) {
			var data = evt.data.dataValue;

			if ( typeof data !== 'string' || !/\[code/i.test( data ) ) {
				return;
			}

			var prefix = newTokenPrefix( data );
			var count = 0;

			evt.data.dataValue = data.replace( blockRegex, function( match, before, open, content, after ) {
				var token = prefix + ( count++ ) + 'e';
				loaded[ token ] = { bbcode: match, before: before, open: open, content: content, after: after };

				return token;
			});
		}, null, null, 5 );

		editor.on( 'setData', function( evt ) {
			var html = evt.data.dataValue;
			var tokens = Object.keys( loaded );

			if ( !tokens.length ) {
				return;
			}

			var blocks = loaded;
			loaded = {};

			if ( typeof html !== 'string' ) {
				return;
			}

			var tokenRegex = new RegExp( tokens.join( '|' ), 'g' );

			// A token inside a tag (the address of a link, for example) gets back its BBCode as escaped text.
			evt.data.dataValue = html.replace( new RegExp( '<[^>]*>|' + tokenRegex.source, 'g' ), function( match ) {
				if ( match.charAt( 0 ) === '<' ) {
					return match.replace( tokenRegex, function( token ) {
						return escapeHtml( blocks[ token ].bbcode );
					});
				}

				var block = blocks[ match ];

				// The HTML parser drops a line break right after <pre>, so one is added before the content.
				return '<pre data-kunena-code="' + escapeHtml( block.open ) + '"'
					+ ' data-kunena-code-before="' + encodeSpace( block.before ) + '"'
					+ ' data-kunena-code-after="' + encodeSpace( block.after ) + '">\n'
					+ escapeHtml( block.content ) + '</pre>';
			});
		}, null, null, 15 );

		// The browser ends a <pre> with a <br> to show its last line when it is empty, and the HTML parser would
		// make a line break of it: it is removed before the parsing (priority 5).
		editor.on( 'toDataFormat', function( evt ) {
			if ( typeof evt.data.dataValue === 'string' ) {
				evt.data.dataValue = evt.data.dataValue.replace( /(<pre\b[^>]*\bdata-kunena-code="[^>]*>(?:(?!<\/?pre\b)[\s\S])*?)<br\s*\/?>(<\/pre>)/gi, '$1$2' );
			}
		}, null, null, 4 );

		// When the data is read (after the HTML is parsed, priority 5), each such <pre> is written back as its
		// opening tag, its text as typed and [/code]. A token stands in for it through the bbcode filter and
		// writer, which would encode the text, and is replaced once the BBCode is written (priority 15).
		editor.on( 'toDataFormat', function( evt ) {
			var fragment = evt.data.dataValue;

			if ( !fragment || typeof fragment.forEach !== 'function' ) {
				return;
			}

			var prefix = newTokenPrefix( '' );
			var count = 0;
			var blocks = [];

			fragment.forEach( function( element ) {
				if ( element.name === 'pre' && element.attributes[ 'data-kunena-code' ] !== undefined ) {
					blocks.push( element );
				}
			}, CKEDITOR.NODE_ELEMENT );

			for ( var i = 0; i < blocks.length; i++ ) {
				var attributes = blocks[ i ].attributes;
				var open = attributes[ 'data-kunena-code' ];
				var token = prefix + ( count++ ) + 'e';

				saved[ token ] = decodeSpace( attributes[ 'data-kunena-code-before' ] )
					+ ( openRegex.test( open ) ? open : '[code]' ) + textOf( blocks[ i ] ) + '[/code]'
					+ decodeSpace( attributes[ 'data-kunena-code-after' ] );
				blocks[ i ].replaceWith( new CKEDITOR.htmlParser.text( token ) );
			}
		}, null, null, 7 );

		editor.on( 'toDataFormat', function( evt ) {
			var tokens = Object.keys( saved );

			if ( !tokens.length ) {
				return;
			}

			var blocks = saved;
			saved = {};

			if ( typeof evt.data.dataValue !== 'string' ) {
				return;
			}

			evt.data.dataValue = evt.data.dataValue.replace( new RegExp( tokens.join( '|' ), 'g' ), function( token ) {
				return blocks[ token ];
			});
		}, null, null, 16 );

		// A token of letters and digits only, which the bbcode plugin leaves alone and the text does not contain.
		function newTokenPrefix( text ) {
			var prefix;

			do {
				prefix = 'kunenacode' + Math.random().toString( 36 ).slice( 2 ) + 'n';
			} while ( text.indexOf( prefix ) !== -1 );

			return prefix;
		}

		// The text of the <pre> as typed: entities decoded, a non-breaking space as a space, a <br> as a line break.
		function textOf( element ) {
			var text = '';

			for ( var i = 0; i < element.children.length; i++ ) {
				var child = element.children[ i ];

				if ( child.type === CKEDITOR.NODE_TEXT ) {
					text += CKEDITOR.tools.htmlDecode( child.value ).replace( / /g, ' ' );
				} else if ( child.type === CKEDITOR.NODE_ELEMENT ) {
					text += child.name === 'br' ? '\n' : textOf( child );
				}
			}

			return text;
		}

		// White space in an attribute, as letters: s (space), t (tab), r and n.
		function encodeSpace( space ) {
			return space.replace( / /g, 's' ).replace( /\t/g, 't' ).replace( /\r/g, 'r' ).replace( /\n/g, 'n' );
		}

		function decodeSpace( value ) {
			if ( !value || !/^[stnr]+$/.test( value ) ) {
				return '';
			}

			return value.replace( /s/g, ' ' ).replace( /t/g, '\t' ).replace( /r/g, '\r' ).replace( /n/g, '\n' );
		}

		function escapeHtml( text ) {
			return text.replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' )
				.replace( /"/g, '&quot;' ).replace( /'/g, '&#39;' );
		}
	}
});
