/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

/*
 * Fix some Mootools bugs
 *
 * provides: [Element.Forms]
 */

Element.implement({
	tidy: function() {
		this.set('value', this.get('value').tidy());
	},

	getTextInRange: function(start, end) {
		return this.get('value').substring(start, end);
	},

	getSelectedText: function() {
		if (this.setSelectionRange) return this.getTextInRange(this.getSelectionStart(), this.getSelectionEnd());
		this.focus();
		return document.selection.createRange().text;
	},

	getSelectedRange: function() {
		this.focus();
		if (this.selectionStart != null){
			return {
				start: this.selectionStart,
				end: this.selectionEnd
			};
		}

		var pos = {
			start: 0,
			end: 0
		};
		var range = this.getDocument().selection.createRange();
		if (!range || range.parentElement() != this) return pos;
		var duplicate = range.duplicate();

		if (this.type == 'text') {
			pos.start = 0 - duplicate.moveStart('character', -100000);
			pos.end = pos.start + range.text.length;
		} else {
			var value = this.get('value');
			var offset = value.length;
			duplicate.moveToElementText(this);
			duplicate.setEndPoint('StartToEnd', range);
			if (duplicate.text.length) offset -= value.match(/[\n\r]*$/)[0].length;
			pos.end = offset - duplicate.text.length;
			duplicate.setEndPoint('StartToStart', range);
			pos.start = offset - duplicate.text.length;
		}
		return pos;
	},

	getSelectionStart: function() {
		return this.getSelectedRange().start;
	},

	getSelectionEnd: function() {
		return this.getSelectedRange().end;
	},

	setCaretPosition: function(pos) {
		if (pos == 'end') pos = this.get('value').length;
		this.selectRange(pos, pos);
		return this;
	},

	getCaretPosition: function() {
		return this.getSelectedRange().start;
	},

	selectRange: function(start, end) {
		this.focus();
		if (this.setSelectionRange) {
			this.setSelectionRange(start, end);
		} else {
			var value = this.get('value');
			var diff = value.substr(start, end - start).replace(/\r/g, '').length;
			start = value.substr(0, start).replace(/\r/g, '').length;
			var range = this.createTextRange();
			range.collapse(true);
			range.moveEnd('character', start + diff);
			range.moveStart('character', start);
			range.select();
		}
		return this;
	},

	insertAtCursor: function(value, select) {
		var pos = this.getSelectedRange();
		var text = this.get('value');
		this.set('value', text.substring(0, pos.start) + value + text.substring(pos.end, text.length));
		if (select !== false) this.selectRange(pos.start, pos.start + value.length);
		else this.setCaretPosition(pos.start + value.length);
		return this;
	},

	insertAroundCursor: function(options, select) {
		// Mootools 1.3+
		options = Object.append({
			before: '',
			defaultMiddle: '',
			after: ''
		}, options);

		var value = this.getSelectedText() || options.defaultMiddle;
		var pos = this.getSelectedRange();
		var text = this.get('value');

		if (pos.start == pos.end){
			this.set('value', text.substring(0, pos.start) + options.before + value + options.after + text.substring(pos.end, text.length));
			this.selectRange(pos.start + options.before.length, pos.end + options.before.length + value.length);
		} else {
			var current = text.substring(pos.start, pos.end);
			this.set('value', text.substring(0, pos.start) + options.before + current + options.after + text.substring(pos.end, text.length));
			var selStart = pos.start + options.before.length;
			if (select !== false) this.selectRange(selStart, selStart + current.length);
			else this.setCaretPosition(selStart + text.length);
		}
		return this;
	}
});

/*
	Class: kbbcode (derived from NAWTE)

	License:
		NAWTE is released under the MIT License (http://opensource.org/licenses/mit-license.php)

	Info:
		nawte is a class to help you put together your own WYSIWYM editor. It works by hooking up to
		a textarea. From there you are free to add any *function* you want via the <addFunction> method.
		Any function you add will do two things: First, it will add a button to the textarea's toolbar.
		Second, it will perform a TextTransform operation (this is where nawte is helpful). An example
		function could be "bold". This would add a "bold" button to your textarea and perform any text
		transformation required to make the text bold. The beauty of this is that, nawte doesn't depend on
		any markup language. If you want your output to be html, so be it! If you prefer markdown or textile,
		once again, no problems!

		All the TextTransform functions (<wrapSelection>, <insert>, <replaceSelection>, <processEachLine>)
		take a parameter named "isLast". This parameter is used for chaining multiple text transformation.
		Setting it to false means you are going to do more transformations once the current transformation is applied.
		The example code below will help clarify that. Note that if you are only doing one text transformation
		in your function (for example, only wrapping the selection) you can ignore this parameter, it will be
		set to "true" by default.

	General Example:
		(start code)
		//creating a kbbcode object
		kbbcode = new kbbcode('textAreaId', 'toolBarId');

		//adding a "List" button that will create a new list if nothing is selected
		//or make each line of the selection into a list item if some text is selected...
		kbbcode.addFunction("list", function() {
			selection = this.getSelection();
			if (selection == "") {
				this.replaceSelection("<ul>\n   <li>Item 1</li>\n   <li>...</li>\n</ul>");
			}
			else {

				this.processEachLine(function(line) {
					newline = "  <li>" + line + "</li>";
					return newline;
				}, false);
				// here you see the purpose of the isLast parameter... since we are applying
				// more TextTransform methods to the selection, isLast must be set to false!

				this.insert("<ul>\n", "before", false);
				this.insert("\n</ul>", "after", true); //now isLast is set to true, because it is the last one!

			}
		});
		(end)
*/
var kbbcode = new Class({

	Implements: Options,

	options: {
		displatchChangeEvent: false,
		changeEventDelay: 1000,
		interceptTabs: true
	},

	/*
		Constructor: kbbcode (constructor)
			Creates a new kbbcode object.

		Arguments:
			element - a string, the ID of your textarea
			list - optional, a string, the id of your toolbal (a UL element)
			options - optional, an object, details below

		Options:
			dispatchChangeEvent - a boolean, set to true if you want to dispatch the "change" event. default: false
			changeEventDelay - an integer, they delay for the periodical function that dispatches the change event. default: 200
			interceptTabs - a boolean, set to true if you want the tab key to insert a tab in the text area, set to false for default tab behavior. default: true

		Additional Info:
			It is possible to watch the "change" event of your textarea . This event will notify you when the content
			of your textarea has been changed, eighter by typing in it, or by pressing on one of the toolbar buttons.

			The purpose of this is that normally, the textarea would not fire a "change" event when kbbcode inserts
			text in it (or when you copy/paste some text in there too). By setting dispatchChangeEvent to true, a
			periodical function will watch the textarea for any changes and fire the "change" event whenever the
			content of the textarea has been changed, no matter how it was changed. You can change the delay of this
			periodical function with the changeEventDelay option. The default delay is 1000ms.

		Example:
			(start code)
			var mykbbcode = new kbbcode('myTextarea', 'myToolbar', {
				dispatchChangeEvent: true,
				changeEventDelay: 150,
				interceptTab: true
			});
			(end)
	*/
	initialize: function(element, list, options) {

		this.el = document.id(element);
		this.selection = {start:0, end:0};

		this.setOptions(options);

		if(this.options.dispatchChangeEvent) {
			this.el.addEvents({
				'focus': function(event) {
					this.timer = this.watchChange.periodical(this.options.changeEventDelay, this);
				}.bind(this),

				'blur': function(event) {
					this.timer = clearInterval(this.timer);
				}.bind(this),

				// Fixing IE
				'select': function(event) {
					this.selection = this.el.getSelectedRange();
				}.bind(this),

				'click': function(event) {
					this.selection = this.el.getSelectedRange();
				}.bind(this),

				'keyup': function(event) {
					this.selection = this.el.getSelectedRange();
				}.bind(this)
				// End fixing IE
			});
		}
		if(this.options.interceptTabs) {

			this.el.addEvent('keypress', function(event){
				if(event.key == "tab") {
					event.preventDefault();
					this.replaceSelection("\t");
				}
			}.bind(this));

		}

		if(list == null || list == "") {
			list = new Element('li');
			list.inject(this.el, 'before');
			this.list = list;
		}
		else {
			this.list = document.id(list);
		}
		this.oldContent = this.el.get('value');
		this.list.empty();
	},

	/*
		Event: onchange
			Fired when the content of the textarea is changed.

		Details:
			As mentioned above, the onchange event of the textarea won't be fired when kbbcode inserts content in it
			or when content is pasted in it. By setting the "dispatchChangeEvent" option to true, this event will
			be fired by a periodical function whenever the content of the textarea as changed.

			The periodical function is only running when the textarea is focused, and stops when it is blured.

			This can be useful if you want to create a live-preview or something like that, that way you are notified
			of any changes to the textarea and can reflect it in your live preview.

		Example:
			I want to watch the change event of my textarea (who's id is "myText")
			(start code)
			$('myText').addEvent('change', function(){
				console.log("My content was changed!");
			});
			(end)
	*/
	watchChange: function() {
		if(this.oldContent != this.el.get('value')) {
			this.oldContent = this.el.get('value');
			this.el.fireEvent('change');
		}
	},

	/*
			function focus
				Gets focus in IE7-10
	*/
	focus: function() {
		if (Browser.ie) {
			this.el.selectRange(this.selection.start, this.selection.end);
		}
		return this;
	},

	/*
		Function: getSelection
			Returns the current selection of the textarea

		Return Value:
			selection - a string, the current selection of the textarea
	*/
	getSelection: function() {
		return this.el.getSelectedText();
	},

	/*
		Function: wrapSelection
			Wrap the selection with wrapper text

		Arguments:
			wrapperLeft - a string, this string will wrap the textarea's current selection
			wrapperRight - a string, this string will wrap the textarea's current selection
			select - selects the text after it's been inserted

		Example:
			>this.wrapSelection("-+", "+-");
			>//selection will become: -+selection+-
	*/
	wrapSelection: function(wrapperLeft, wrapperRight, select) {
		select = (select === null) ? true : select;
		this.el.insertAroundCursor({before: wrapperLeft, after: wrapperRight, defaultMiddle: ""});
		if (!select) this.el.selectRange(this.el.getSelectionEnd(), this.el.getSelectionEnd());
		this.selection = this.el.getSelectedRange();
	},

	/*
		Function: insert
			Insert text before or after the current selection. (This is a TextTransform method...)

		Arguments:
			newText - a string, the text to insert before the selection
			where - a string, either "before" or "after" depending on where you want to insert
			select - selects the text after it's been inserted

		Example:
			>this.insert("Hello ", 'before');
			>//selection will become: Hello selection
	*/
	insert: function(newText, where, select) {
		select = (select === null) ? true : select;
		var pos = (where == "before") ? this.el.getSelectionStart() : this.el.getSelectionEnd();
		this.el.selectRange(pos, pos);
		this.el.insertAtCursor(newText, select);
		this.selection = this.el.getSelectedRange();
	},

	/*
		Function: replaceSelection
			Replace the current selection with newText. (This is a TextTransform method...)

		Arguments:
			newText - a string, the text that will replace the selection

		Example:
			>this.replaceSelection("Hello World");
			>//selection will become: Hello World
	*/
	replaceSelection: function(newText, select) {
		select = (select === null) ? true : select;
		this.el.insertAtCursor(newText, select);
		this.selection = this.el.getSelectedRange();
	},

	/*
		Function: processEachLine
			Will process each lines of the selection with "callback". (This is a TextTransform method...)

		Arguments:
			callback - a function, will transform each lines of the selection, should accept the "line" parameter, MUST return the new transformed line

		Example:
			(start code)
			this.processEachLine(function(line){
				newline = "*** " + line;
				return newline;
			});
			// will prepend each lines of the selection with "*** "
			(end)
	*/

	processEachLine: function(callback) {
		var lines = this.el.getSelectedText().split("\n");
		var newlines = [];
		lines.each(function(line) {
			if (line.trim() != "")
				newlines.push(callback.attempt(line.trim(), this));
		}.bind(this));

		this.el.insertAtCursor(newlines.join("\r\n"), true);
	},

	/*
		Function: getValue
			Utility function. Returns the entire content of the text area

		Return Value:
			value - a string, the content of the text area

		Example:
			>var content = this.getValue();
	*/
	getValue: function() {
		return this.el.get('value');
	},

	/*
		Function: setValue
			Utility function. Sets the entire content of the text area

		Arguments:
			value - a string, the new content of the text area

		Example:
			>this.setValue("New Text");
	*/
	setValue: function(text) {
		this.el.set('value', text);
		this.el.focus();
	},

	/*
		Function: addFunction
			The main concept of kbbcode, this is where you add functions (i.e. buttons) to your editor.

		Arguments:
			name - a string, the name of the function to add
			callback - a function, what your button will do
			args - optional, an object, additional HTML properties you want to add to the button

		Additional Info:
			This is the basic idea of kbbcode, each function you add (for example "BOLD") will add a new
			button to your editor. In your callback function, you can use any of the TextTransform functions
			to transform the selection, insert new text etc..

		Example:
			(start code)
			//we will add a bold button that will surround the selection
			//with <b>, </b>
			mykbbcode.addFunction('bold', function() {
				var selection = this.getSelection();
				this.replaceSelection('<b>' + selection + '</b>');
			},
			{title: "Make Text Bold"});
			// and we now have a bold button! Isn't that pure magic!??
			// note the optional "args" argument, just pass an object with html
			// properties to apply them to the newly created button, in this case
			// I am setting it's title to "Make Text Bold"...
			(end)
	*/
	addFunction: function(name, callback, args) {
		var item = new Element('li');
		var itemlink = new Element('a', {
			'events': {
				'click': function(e){
					e.stop();
					callback.attempt(null, this);
				}.bind(this)
			},
			'href': '#'
		});
		itemlink.set('html', '<span>' + name + '</span>');
		itemlink.setProperties(args || {});
		itemlink.inject(item, 'bottom');
		item.inject(this.list, 'inside');
	}
});

//A few variable we use in some of the functions
var _currentElement="";
var _previewActive=false;

//
// function kToggleOrSwap (elementId)
//
// Helper function for bbeditor optional/detailed toolbar
//
// Toogles the visibility of the element passed by ID. If another element in the
// optional toolbar is already visible, it hides the prior one before displaying
// the new option.
//
function kToggleOrSwap(id)
{
	e = document.id(id);
	if (e) {
		if (e.getStyle('display') == "none"){
			if (_currentElement != "") {_currentElement.setStyle('display', 'none');}
			e.setStyle('display', 'block');
			_currentElement=e;
		} else {
			e.setStyle('display', 'none');
			_currentElement = "";
		}
	}
}

//
// function kToggleOrSwapPreview (className)
//
// Helper function for bbeditor optional/detailed toolbar
//
// Toogles the visibility of the preview element passed by ID. If another preview
// is already visible and active, it hides the prior one before displaying
// the new preview.
// That way we can not only turn preview on and off, but also switch between horizontal
// (preview to the right) and vertical (preview at the bottom) modes
//

function kToggleOrSwapPreview(kunenaclass)
{
	e = document.id("kbbcode-preview");
	f = document.id("kbbcode-message");
	if (e) {
		if (e.getStyle('display') == "none" || e.getProperty('class') != kunenaclass){
			e.setStyle('display', 'block');

			if (kunenaclass=="kbbcode-preview-right"){
				f.setStyle('width', '47%');
			} else {
				f.setStyle('width', '95%');
			}

			_previewActive=true;
			kPreviewHelper();
		}
		else
		{
			e.setStyle('display', 'none');
			f.setStyle('width', '95%');
			_previewActive=false;
		}
		e.setProperty('class', kunenaclass);
		var height = f.getStyle('height');
		e.setStyle('height', f.getStyle('height'));
	}
}

//
// kGenColorPalette(width, height)
//
// Helper function to generate the color palette for the bbcode color picker
//
function kGenerateColorPalette(width, height)
{
	var r = 0, g = 0, b = 0;
	var numberList = new Array(6);
	var color = '';
	numberList[0] = '00';
	numberList[1] = '44';
	numberList[2] = '88';
	numberList[3] = 'BB';
	numberList[4] = 'FF';

	document.writeln('<table id="kbbcode-colortable" class="kbbcode-colortable" cellspacing="1" cellpadding="0" border="0" style="width: 100%;">');

	for (r = 0; r < 5; r++)
	{
		document.writeln('<tr>');
		for (g = 0; g < 5; g++)	{
			for (b = 0; b < 5; b++)	{
				color = String(numberList[r]) + String(numberList[g]) + String(numberList[b]);
				document.writeln('<td style="background-color:#' + color + '; width: ' + width + '; height: ' + height + ';">&nbsp;</td>');
			  }
		}
		document.writeln('</tr>');
	}
	document.writeln('</table>');
}

function kInsertCode() {
	var kcodetype = '';
	if( document.id('kcodetype') != undefined ) kcodetype = document.id('kcodetype').get('value');
	if (kcodetype != '') kcodetype = ' type='+kcodetype;
	kbbcode.focus().wrapSelection('[code'+kcodetype+']', '[/code]', false);
	kToggleOrSwap("kbbcode-code-options");
}

//
// kInsertImageLink()
//
// Helper function to insert the img tag (image link) bbcode into the message
//
function kInsertImageLink() {
	var size = document.id("kbbcode-image_size").get("value");
	if (size == "") {
		kbbcode.focus().replaceSelection('[img]'+ document.id("kbbcode-image_url").get("value") +'[/img]', false);
	} else {
		kbbcode.focus().replaceSelection('[img size='+size+']'+ document.id("kbbcode-image_url").get("value") +'[/img]', false);
	}
	kToggleOrSwap("kbbcode-image-options");
}

function kGrowShrinkMessage(change){
	var m = document.id('kbbcode-message');
	var p = document.id('kbbcode-preview');
	var currentheight = parseInt(m.getStyle('height'));
	var newheight = currentheight + change;

	if (newheight > 100) {
		m.setStyle( 'height', newheight + 'px');
		p.setStyle( 'height', newheight + 'px');
	} else {
		m.setStyle( 'height', '100px');
		p.setStyle( 'height', '100px');
	}
}

function myValidate(f) {
	if (document.formvalidator.isValid(f)) {
		return true;
	}
	return false;
}

function cancelForm() {
	document.forms['postform'].action.value = "cancel";
	return true;
}

var __attachment_limit = 0;

function setLimitOnEdit() {
	var attachs_available = $$('li.kattachment-old');

	if (attachs_available.length > 0) {
		__attachment_limit = attachs_available.length;
	}

	if (__attachment_limit == config_attachment_limit || __attachment_limit > config_attachment_limit) {
		document.id('kattachment-id').setStyle('display', 'none');
	}
}

function newAttachment() {
	if ( config_attachment_limit > 0 ) {
		if (__attachment_limit < config_attachment_limit) __attachment_limit++;
		else return false;
	}

	var __kattachment = document.id('kattachment-id');
	if (!__kattachment) return;
	__kattachment.setStyle('display', 'none');
	__kattachment.getElement('input').setProperty('value', '');

	var __id = __kattachment.retrieve('nextid',1);
	__kattachment.store('nextid',__id+1);
	var __file = __kattachment.clone().inject(__kattachment,'before').set('id','kattachment'+__id).setStyle('display');
	__file.getElement('span.kattachment-id-container').set('text', __id+'. ');
	var input = __file.getElement('input.kfile-input').set('name', 'kattachment'+__id).removeProperty('onchange');
	input.addEvent('change', function() {
		this.removeEvents('change');
		var __filename = this.get('value');
		if (__filename.lastIndexOf('\\') > -1) {
			__filename = __filename.substring(1 + __filename.lastIndexOf('\\'));
		}
		this.addEvent('change', function() {
			__file.getElement('input.kfile-input-textbox').set('value', __filename);
		});
		__file.getElement('input.kfile-input-textbox').set('value', __filename);

		__file.getElement('.kattachment-insert').removeProperty('style').addEvent('click', function() {kbbcode.focus().insert('\n[attachment:'+ __id +']'+ __filename +'[/attachment]\n', 'after', false); return false; } );
		__file.getElement('.kattachment-remove').removeProperty('style').addEvent('click', function() {__file.dispose(); return false; } );
		newAttachment();
	});
}

function bindAttachments() {
	var __kattachment = $$('.kattachment-old');
	if (!__kattachment) return;
	__kattachment.each(function(el) {
		el.getElement('.kattachment-insert').removeProperty('style').addEvent('click', function() {kbbcode.focus().insert('\n[attachment='+ el.getElement('input[type="checkbox"]').get('value') +']'+ el.getElement('.kfilename').get('text') +'[/attachment]\n', 'after', false); return false; } );
	});
}

//
// Helper function for various IE7 and IE8 work arounds
//
function IEcompatibility() {
	// Only do anything if this is IE
	if(Browser.ie){
		var __fix = $$("#kbbcode-size-options", "#kbbcode-size-options span",
						"#kbbcode-colortable", "#kbbcode-colortable td");
		if (__fix) {
			__fix.setProperty('unselectable', 'on');
		}
	}
}

//
// kInsertVideo()
//
// Helper function to insert the video bbcode into the message
//

//This selector can be re-used for the dropdwown list, to get the item selected easily
Slick.definePseudo(this, function(value){
	return (this.selected && this.get('tag') == 'option');
})

function kInsertVideo1() {
	var videosize = document.id('kvideosize').get('value');
//	if ( videosize == '') { NO DEFAULT
//		videosize = '100';
//	}
	var videowidth = document.id('kvideowidth').get('value');
	if ( videowidth == '') {
		videowidth = '425';
	}
	var videoheigth = document.id('kvideoheight').get('value');
	if ( videoheigth == '') {
		videoheigth = '344';
	}
	var provider = document.id('kvideoprovider').get('value');
	if ( provider == '') {
		provider = '';
	}
	var videoid = document.id('kvideoid').get('value');
	kbbcode.focus().insert( '[video'+(videosize ? ' size='+videosize:'')+' width='+videowidth+' height='+videoheigth+' type='+provider+']'+videoid+'[/video]', 'after', false);
	kToggleOrSwap("kbbcode-video-options");
}

function kInsertVideo2() {
	var videourl = document.id("kvideourl").get("value");
	kbbcode.focus().insert('[video]'+ videourl +'[/video]', 'after', false);
	kToggleOrSwap("kbbcode-video-options");
}

function kEditorInitialize() {
	$$('#kbbcode-toolbar li a').addEvent('mouseover', function(){
		document.id('helpbox').set('value', this.getProperty('alt'));
	});

	document.id('kbbcode-message').addEvent('change', function(){
		kPreviewHelper();
	});

	var color = $$("table.kbbcode-colortable td");
	if (color) {
		color.addEvent("click", function(){
			var bg = this.getStyle( "background-color" );
			kbbcode.focus().wrapSelection('[color='+ bg +']', '[/color]', true);
			kToggleOrSwap("kbbcode-color-options");
		});
	}
	var size = $$("div#kbbcode-size-options span");
	if (size) {
		size.addEvent("click", function(){
			var tag = this.get( "title" );
			kbbcode.focus().wrapSelection(tag , '[/size]', true);
			kToggleOrSwap("kbbcode-size-options");
		});
	}

	bindAttachments();
	setLimitOnEdit();
	newAttachment();
	//This is need to retrieve the video provider selected by the user in the dropdownlist
	if (document.id('kvideoprovider') != undefined) {
		document.id('kvideoprovider').addEvent('change', function() {
			var sel = $$('#kvideoprovider option:selected');
			sel.each(function(el) {
				document.id('kvideoprovider').store('videoprov',el.value);
			});
		});
	}

	// Fianlly apply some screwy IE7 and IE8 fixes to the html...
	IEcompatibility();
}
