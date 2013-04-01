/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

//
// Element helper functions for the Kunena bbcode editor
//
// The code is based on nawte - a generic helper class for mootools to implement
// simple markup languge editors. The clas has been adopted and expanded to meed the needs
// of the Kunena bbcode engine. With a wide variety of tags supported, css sprite based
// toolbar, mouseover helpline, selective sub toolbars for various options and live preview
// before changes get applied.
//

Element.implement({

	getSelectedText: function() {
		if (typeof this.selectionStart != "undefined") {
			return this.get('value').substring(this.selectionStart, this.selectionEnd);
		}
		if(typeof document.selection != "undefined") {
			// Something for IE
			this.focus();
			return document.selection.createRange().text;
		}
		return '';
	},

	wrapSelectedText: function(newtext, wrapperLeft, wrapperRight, isLast) {
		isLast = (isLast == null) ? true : isLast;

		var scroll_top = this.scrollTop;

		this.focus();
		if(typeof this.selectionStart != "undefined") {
			var originalStart = this.selectionStart;
			var originalEnd = this.selectionEnd;
			this.value = this.get('value').substring(0, originalStart) + wrapperLeft + newtext + wrapperRight + this.get('value').substring(originalEnd);
			if(isLast == false) {
				this.setSelectionRange(originalStart + wrapperLeft.length, originalStart + wrapperLeft.length + newtext.length);
			} else {
				var position = originalStart + newtext.length + wrapperLeft.length +  wrapperRight.length;
				this.setSelectionRange(position, position);
			}
		} else if(typeof document.selection != "undefined") {
			// Something for IE
			var range = document.selection.createRange();
			range.text = wrapperLeft + newtext + wrapperRight;
			if(isLast) {
				range.select();
			}
		}
		this.scrollTop = scroll_top;
	},

	replaceSelectedText: function(newtext, isLast) {
		isLast = (isLast == null) ? true : isLast;
		this.wrapSelectedText(newtext, '', '', isLast);
	}
});

// A few variable we use in some of the functions
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
		}
		else
		{
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

		this.setOptions(options);

		if(this.options.dispatchChangeEvent) {
			this.el.addEvents({
				'focus': function() {
					this.timer = this.watchChange.periodical(this.options.changeEventDelay, this);
				}.bind(this),

				'blur': function() {
					this.timer = $clear(this.timer);
				}.bind(this)
			});
		}
		if(this.options.interceptTabs) {

			this.el.addEvent('keypress', function(event){
				event = new Event(event);
				if(event.key == "tab") {
					event.preventDefault();
					this.replaceSelection("\t");
				}
			}.bind(this));

		}

		if(! $defined(list) || list == "") {
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
			wrapper - a string, this string will wrap the textarea's current selection
			isLast - see information at the top

		Example:
			>this.wrapSelection("-+", "+-");
			>//selection will become: -+selection+-
	*/
	wrapSelection: function(wrapperLeft, wrapperRight, isLast) {
		isLast = (isLast == null) ? true : isLast;
		this.el.wrapSelectedText(this.el.getSelectedText(), wrapperLeft, wrapperRight, isLast);
	},

	/*
		Function: insert
			Insert text before or after the current selection. (This is a TextTransform method...)

		Arguments:
			insertText - a string, the text to insert before the selection
			where - a string, either "before" or "after" depending on where you want to insert
			isLast - see information at the top

		Example:
			>this.insert("Hello ", 'before');
			>//selection will become: Hello selection
	*/
	insert: function(insertText, where, isLast) {
		isLast = (isLast == null) ? true : isLast;
		var wrapperLeft = '';
		var wrapperRight = '';
		where = (where == "") ? 'after' : where;
		var newText = (where == "before") ? wrapperLeft = insertText : wrapperRight = insertText;
		this.el.wrapSelectedText(this.el.getSelectedText(), wrapperLeft, wrapperRight, isLast);
	},

	/*
		Function: replaceSelection
			Replace the current selection with newText. (This is a TextTransform method...)

		Arguments:
			newText - a string, the text that will replace the selection
			isLast - see information at the top

		Example:
			>this.replaceSelection("Hello World");
			>//selection will become: Hello World
	*/
	replaceSelection: function(newText, isLast) {
		isLast = (isLast == null) ? true : isLast;
		this.el.replaceSelectedText(newText, isLast);
	},

	/*
		Function: processEachLine
			Will process each lines of the selection with "callback". (This is a TextTransform method...)

		Arguments:
			callback - a function, will transform each lines of the selection, should accept the "line" parameter, MUST return the new transformed line
			isLast - see information at the top

		Example:
			(start code)
			this.processEachLine(function(line){
				newline = "*** " + line;
				return newline;
			});
			// will prepend each lines of the selection with "*** "
			(end)
	*/

	processEachLine: function(callback, isLast) {
		isLast = (isLast == null) ? true : isLast;
		var lines = this.el.getSelectedText().split("\n");
		var newlines = [];
		lines.each(function(line) {
			if (line != "")
				newlines.push(callback.attempt(line, this));
			else
				newlines.push("");
		}.bind(this));

		this.el.replaceSelectedText(newlines.join("\n"), isLast);
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
					new Event(e).stop();
					callback.attempt(null, this);
				}.bind(this)
			},
			'href': '#'
		});
		itemlink.set('html', '<span>' + name + '</span>');
		itemlink.setProperties(args || {});
		itemlink.inject(item, 'bottom');
		item.injectInside(this.list);
	}

});

function kInsertCode() {
	var kcodetype = document.id('kcodetype').get('value');
	if (kcodetype != '') kcodetype = ' type='+kcodetype;
	kbbcode.wrapSelection('[code'+kcodetype+']', '[/code]', false); 
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
		kbbcode.replaceSelection('[img]'+ document.id("kbbcode-image_url").get("value") +'[/img]');
	} else {
		kbbcode.replaceSelection('[img size='+size+']'+ document.id("kbbcode-image_url").get("value") +'[/img]');
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

// 
// Helper function for various IE7 and IE8 work arounds
//
function IEcompatibility() {
	// Only do anything if this is IE
	if(Browser.Engine.trident){
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
Selectors.Pseudo.selected = function(){
	return (this.selected && this.get('tag') == 'option');
};

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
	kbbcode.replaceSelection( '[video'+(videosize ? ' size='+videosize:'')+' width='+videowidth+' height='+videoheigth+' type='+provider+']'+videoid+'[/video]', false);
	kToggleOrSwap("kbbcode-video-options");
}

function kInsertVideo2() {
	var videourl = document.id("kvideourl").get("value");
	kbbcode.replaceSelection('[video]'+ videourl +'[/video]', false);
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
			kbbcode.wrapSelection('[color='+ bg +']', '[/color]', false);
			kToggleOrSwap("kbbcode-colorpalette");
		});
	}
	var size = $$("div#kbbcode-size-options span");
	if (size) {
		size.addEvent("click", function(){
			var tag = this.get( "title" );
			kbbcode.wrapSelection(tag , '[/size]', false);
			kToggleOrSwap("kbbcode-size-options");
		});
	}

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