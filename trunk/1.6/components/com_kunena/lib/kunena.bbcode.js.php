<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

defined( '_JEXEC' ) or die();

$kunena_config = & CKunenaConfig::getInstance ();

$document =& JFactory::getDocument();

ob_start();
//
// Element helper functions for the Kunena bbcode editor
//
// The code is based on nawte - a generic helper class for mootools to implement
// simple markup languge editors. The clas has been adopted and expanded to meed the needs
// of the Kunena bbcode engine. With a wide variety of tags supported, css sprite based
// toolbar, mouseover helpline, selective sub toolbars for various options and live preview
// before changes get applied.
//
?>

Element.implement({

	getSelectedText: function() {
		if(Browser.Engine.trident) return document.selection.createRange().text;
		return this.get('value').substring(this.selectionStart, this.selectionEnd);
	},

	replaceSelectedText: function(newtext, isLast) {
		var isLast = (isLast == null) ? true : isLast;

		var scroll_top = this.scrollTop;

		if(Browser.Engine.trident) {
			this.focus();
			var range = document.selection.createRange();
			range.text = newtext;
			if(isLast) {
				range.select();
				this.scrollTop = scroll_top;
			}

		}
		else {
			originalStart = this.selectionStart;
			originalEnd = this.selectionEnd;
			this.value = this.get('value').substring(0, originalStart) + newtext + this.get('value').substring(originalEnd);
			if(isLast == false) {
				this.setSelectionRange(originalStart, originalStart + newtext.length);
			}
			else {
				this.setSelectionRange(originalStart + newtext.length, originalStart + newtext.length);
				this.scrollTop = scroll_top;
			}
			this.focus();
		}
	}
});
<?php
// A few variable we use in some of the functions
?>
var _currentElement="";
var _previewActive=false;

<?php
//
// function kToggleOrSwap (elementId)
//
// Helper function for bbeditor optional/detailed toolbar
//
// Toogles the visibility of the element passed by ID. If another element in the
// optional toolbar is already visible, it hides the prior one before displaying
// the new option.
//
?>
function kToggleOrSwap(id)
{
	e = $(id);
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
<?php
//
// function kPreviewHelper (elementId)
//
// Helper function for to perform JSON request for preview
//
?>
function kPreviewHelper()
{
	if (_previewActive == true){
		previewRequest = new Request.JSON({url: "<?php echo CKunenaLink::GetJsonURL('preview');?>",
				  							onSuccess: function(response){
			message = $("kbbcode-preview");
			if (message) {
				message.set("html", response.preview);
			}
			}}).post({body: $("kbbcode-message").get("value")
		});
	}
}
<?php
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
?>
function kToggleOrSwapPreview(class)
{
	e = $("kbbcode-preview");
	f = $("kbbcode-message");
	if (e) {
		if (e.getStyle('display') == "none" || e.getProperty('class') != class){
	    	e.setStyle('display', 'block');

			if (class=="kbbcode-preview_right"){
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
		e.setProperty('class', class);
		var height = f.getStyle('height');
		e.setStyle('height', f.getStyle('height'));
	}
}

<?php
//
// kGenColorPalette(width, height)
//
// Helper function to generate the color palette for the bbcode color picker
//
?>
function kGenerateColorPalette(width, height)
{
	var r = 0, g = 0, b = 0;
	var numberList = new Array(6);
	var color = '';
	numberList[0] = '00';
	numberList[1] = '40';
	numberList[2] = '80';
	numberList[3] = 'BF';
	numberList[4] = 'FF';

	document.writeln('<table id="kbbcode-colortable" class="kbbcode-colortable" cellspacing="1" cellpadding="0" border="0" style="width: 100%;">');

	for (r = 0; r < 5; r++)
	{
		document.writeln('<tr>');
		for (g = 0; g < 5; g++)	{
			for (b = 0; b < 5; b++)	{
				color = String(numberList[r]) + String(numberList[g]) + String(numberList[b]);
				document.write('<td id="' + color + '" style="background-color:#' + color + '; width: ' + width + '; height: ' + height + ';">');
				document.write('&nbsp;');
				document.writeln('</td>');
			  }
		}
		document.writeln('</tr>');
	}
	document.writeln('</table>');
}
<?php
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
?>
var kbbcode = new Class({

	Implements: Options,

	options: {
		displatchChangeEvent: false,
		changeEventDelay: 1000,
		interceptTabs: true
	},
<?php
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
?>
	initialize: function(element, list, options) {

		this.el = $(element);

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
				var event = new Event(event);
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
			this.list = $(list);
		}
		this.oldContent = this.el.get('value');
	},
<?php
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
?>
	watchChange: function() {
		if(this.oldContent != this.el.get('value')) {
			this.oldContent = this.el.get('value');
			this.el.fireEvent('change');
		}
	},
<?php
	/*
		Function: getSelection
			Returns the current selection of the textarea

		Return Value:
			selection - a string, the current selection of the textarea
	*/
?>
	getSelection: function() {
		return this.el.getSelectedText();
	},
<?php
	/*
		Function: wrapSelection
			Wrap the selection with wrapper text

		Arguments:
			wrapper - a string, this string will wrap the textarea's current selection
			isLast - see information at the top

		Example:
			>this.wrapSelection("**");
			>//selection will become: **selection**
	*/
?>
	wrapSelection: function(wrapper, isLast) {
		var isLast = (isLast == null) ? true : isLast;
		this.el.replaceSelectedText(wrapper + this.el.getSelectedText() + wrapper, isLast);
	},
<?php
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
?>
	insert: function(insertText, where, isLast) {
		var isLast = (isLast == null) ? true : isLast;
		where = (where == "") ? 'after' : where;
		var newText = (where == "before") ? insertText + this.el.getSelectedText() : this.el.getSelectedText() + insertText;
		this.el.replaceSelectedText(newText, isLast);
	},
<?php
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
?>
	replaceSelection: function(newText, isLast) {
		var isLast = (isLast == null) ? true : isLast;
		this.el.replaceSelectedText(newText, isLast);
	},
<?php
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
?>
	processEachLine: function(callback, isLast) {
		var isLast = (isLast == null) ? true : isLast;
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
<?php
	/*
		Function: getValue
			Utility function. Returns the entire content of the text area

		Return Value:
			value - a string, the content of the text area

		Example:
			>var content = this.getValue();
	*/
?>
	getValue: function() {
		return this.el.get('value');
	},
<?php
	/*
		Function: setValue
			Utility function. Sets the entire content of the text area

		Arguments:
			value - a string, the new content of the text area

		Example:
			>this.setValue("New Text");
	*/
?>
	setValue: function(text) {
		this.el.set('value', text);
		this.el.focus();
	},
<?php
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
?>
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
<?php
// Now we instanciate the class in an object and implement all the buttons and functions.
?>
window.addEvent('domready', function() {
<?php
//creating a kbbcode object
?>
kbbcode = new kbbcode('kbbcode-message', 'kbbcode-toolbar', {
				dispatchChangeEvent: true,
				changeEventDelay: 1000,
				interceptTab: true
			});

kbbcode.addFunction('Bold', function() {
	this.replaceSelection('[b]' + this.getSelection() + '[/b]');
}, {'id': 'kbbcode-bold_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_BOLD');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_BOLD');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_BOLD');?>")'});

kbbcode.addFunction('Italic', function() {
	this.replaceSelection('[i]' + this.getSelection() + '[/i]');
}, {'id': 'kbbcode-italic_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_ITALIC');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ITALIC');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ITALIC');?>")'});

kbbcode.addFunction('Underline', function() {
	this.replaceSelection('[u]' + this.getSelection() + '[/u]');
}, {'id': 'kbbcode-underline_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_UNDERL');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_UNDERL');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_UNDERL');?>")'});

kbbcode.addFunction('Strike', function() {
	this.replaceSelection('[strike]' + this.getSelection() + '[/strike]');
}, {'id': 'kbbcode-strike_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_STRIKE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_STRIKE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_STRIKE');?>")'});

kbbcode.addFunction('Sub', function() {
	this.replaceSelection('[sub]' + this.getSelection() + '[/sub]');
}, {'id': 'kbbcode-sub_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_SUB');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SUB');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SUB');?>")'});

kbbcode.addFunction('Sup', function() {
	this.replaceSelection('[sup]' + this.getSelection() + '[/sup]');
}, {'id': 'kbbcode-sup_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_SUP');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SUP');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SUP');?>")'});

kbbcode.addFunction('Size', function() {
	kToggleOrSwap("kbbcode-size-options");
}, {'id': 'kbbcode-size_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_FONTSIZE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE');?>")'});

kbbcode.addFunction('Color', function() {
	kToggleOrSwap("kbbcode-colorpalette");
}, {'id': 'kbbcode-color_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_COLOR');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_COLOR');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_COLOR');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator1'});

<?php
//adding a "List" button that will create a new list if nothing is selected
//or make each line of the selection into a list item if some text is selected...
 ?>
kbbcode.addFunction("uList", function() {
	selection = this.getSelection();
	if (selection == "") {
		this.replaceSelection("\n[ul]\n   [li]Item 1[/li]\n   [li]Item 2[/li]\n   [li]...[/li]\n[/ul]");
	}
	else {
		this.processEachLine(function(line) {
			newline = "  [li]" + line + "[/li]";
			return newline;
		}, false);
		this.insert("[ul]\n", "before", false);
		this.insert("\n[/ul]", "after", true); //now isLast is set to true, because it is the last one!
	}
}, {'id': 'kbbcode-ulist_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_ULIST');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ULIST');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ULIST');?>")'});

kbbcode.addFunction("oList", function() {
	selection = this.getSelection();
	if (selection == "") {
		this.replaceSelection("\n[ol]\n   [li]Item 1[/li]\n   [li]Item 2[/li]\n   [li]...[/li]\n[/ol]");
	}
	else {
		this.processEachLine(function(line) {
			newline = "  [li]" + line + "[/li]";
			return newline;
		}, false);
		this.insert("[ol]\n", "before", false);
		this.insert("\n[/ol]", "after", true); //now isLast is set to true, because it is the last one!
	}
}, {'id': 'kbbcode-olist_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_OLIST');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_OLIST');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_OLIST');?>")'});

kbbcode.addFunction('List', function() {
	this.replaceSelection('[li]' + this.getSelection() + '[/li]');
}, {'id': 'kbbcode-list_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_LIST');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LIST');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LIST');?>")'});

kbbcode.addFunction('Left', function() {
	this.replaceSelection('[left]' + this.getSelection() + '[/left]');
}, {'id': 'kbbcode-left_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_LEFT');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LEFT');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LEFT');?>")'});

kbbcode.addFunction('Center', function() {
	this.replaceSelection('[center]' + this.getSelection() + '[/center]');
}, {'id': 'kbbcode-center_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_CENTER');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CENTER');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CENTER');?>")'});

kbbcode.addFunction('Right', function() {
	this.replaceSelection('[right]' + this.getSelection() + '[/right]');
}, {'id': 'kbbcode-right_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_RIGHT');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_RIGHT');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_RIGHT');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator2'});

kbbcode.addFunction('Quote', function() {
	this.replaceSelection('[quote]' + this.getSelection() + '[/quote]');
}, {'id': 'kbbcode-quote_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_QUOTE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_QUOTE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_QUOTE');?>")'});

kbbcode.addFunction('Code', function() {
	this.replaceSelection('[code]' + this.getSelection() + '[/code]');
}, {'id': 'kbbcode-code_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_CODE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CODE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CODE');?>")'});

kbbcode.addFunction("Table", function() {
	selection = this.getSelection();
	if (selection == "") {
		this.replaceSelection("\n[table]\n  [tr]\n    [td]Line 1[/td]\n    [td]Column 2[/td]\n  [/tr]\n  [tr]\n    [td]Line 2[/td]\n    [td]...[/td]\n  [/tr]\n[/table]");
	}
	else {
		this.processEachLine(function(line) {
			newline = "  [tr][td]" + line + "[/td][/tr]";
			return newline;
		}, false);
		this.insert("[table]\n", "before", false);
		this.insert("\n[/table]", "after", true); //now isLast is set to true, because it is the last one!
	}
}, {'id': 'kbbcode-table_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_TABLE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_TABLE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_TABLE');?>")'});

<?php
if ($kunena_config->showspoilertag) {
?>
kbbcode.addFunction('Spoiler', function() {
	this.replaceSelection('[spoiler]' + this.getSelection() + '[/spoiler]');
}, {'id': 'kbbcode-spoiler_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_SPOILER');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SPOILER');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SPOILER');?>")'});
<?php
}
?>

kbbcode.addFunction('Hide', function() {
	this.replaceSelection('[hide]' + this.getSelection() + '[/hide]');
}, {'id': 'kbbcode-hide_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_HIDE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_HIDE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_HIDE');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator3'});

kbbcode.addFunction('Image', function() {
	kToggleOrSwap("kbbcode-image-options");
}, {'id': 'kbbcode-image_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_IMAGELINK');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINK');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINK');?>")'});

kbbcode.addFunction('Link', function() {
	sel = this.getSelection();
	if (sel != "") {
		$('kbbcode-link_text').set('value', sel);
	}
	kToggleOrSwap("kbbcode-link-options");
}, {'id': 'kbbcode-link_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_LINK');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINK');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINK');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator4'});

kbbcode.addFunction('Gallery', function() {
	kToggleOrSwap("kbbcode-gallery-options");
}, {'id': 'kbbcode-gallery_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_GALLERY');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_GALLERY');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_GALLERY');?>")'});

kbbcode.addFunction('Poll', function() {
	kToggleOrSwap("kbbcode-poll-options");
}, {'id': 'kbbcode-poll_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_POLL');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLL');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLL');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator5'});

<?php
if ($kunena_config->showebaytag) {
?>
kbbcode.addFunction('eBay', function() {
	this.replaceSelection('[ebay]' + this.getSelection() + '[/ebay]');
}, {'id': 'kbbcode-ebay_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_EBAY');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_EBAY');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_EBAY');?>")'});
<?php
}
?>

<?php
if ($kunena_config->showvideotag) {
?>
kbbcode.addFunction('Video', function() {
	kToggleOrSwap("kbbcode-video-options");
}, {'id': 'kbbcode-video_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_VIDEO');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEO');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEO');?>")'});
<?php
}
?>

kbbcode.addFunction('Map', function() {
	this.replaceSelection('[map]' + this.getSelection() + '[/map]');
}, {'id': 'kbbcode-map_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_MAP');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_MAP');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_MAP');?>")'});

kbbcode.addFunction('Module', function() {
	this.replaceSelection('[module]' + this.getSelection() + '[/module]');
}, {'id': 'kbbcode-module_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_MODULE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_MODULE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_MODULE');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator6'});

kbbcode.addFunction('PreviewBottom', function() {
	kToggleOrSwapPreview("kbbcode-preview_bottom");
}, {'id': 'kbbcode-previewbottom_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_PREVIEWBOTTOM');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEWBOTTOM');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEWBOTTOM');?>")'});

kbbcode.addFunction('PreviewRight', function() {
	kToggleOrSwapPreview("kbbcode-preview_right");
}, {'id': 'kbbcode-previewright_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_PREVIEWRIGHT');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEWRIGHT');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEWRIGHT');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator7'});

kbbcode.addFunction('Help', function() {
	window.open('http://docs.kunena.com/index.php/bbcode');
}, {'id': 'kbbcode-help_button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_HELP');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_HELP');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_HELP');?>")'});

$('kbbcode-message').addEvent('change', function(){
	kPreviewHelper();
});

});

<?php
// Add the click behaviors for our bbcode options
?>
window.addEvent('domready', function() {
	var color = $$("table.kbbcode-colortable td");
	if (color) {
		color.addEvent("click", function(){
			var bg = this.getStyle( "background-color" );
			selection = kbbcode.getSelection();
			kbbcode.replaceSelection('[color='+ bg +']' + selection + '[/color]');
			kToggleOrSwap("kbbcode-colorpalette");
		});
	}
	var size = $$("div#kbbcode-size-options span");
	if (size) {
		size.addEvent("click", function(){
			var tag = this.get( "title" );
			selection = kbbcode.getSelection();
			kbbcode.replaceSelection(tag + selection + '[/size]');
			kToggleOrSwap("kbbcode-size-options");
		});
	}
});

<?php
//
// kInsertImageLink()
//
// Helper function to insert the img tag (image link) bbcode into the message
//
?>
function kInsertImageLink() {
	var size = $("kbbcode-image_size").get("value");
	if (size == "") {
		kbbcode.replaceSelection('[img]'+ $("kbbcode-image_url").get("value") +'[/img]');
	} else {
		kbbcode.replaceSelection('[img size='+size+']'+ $("kbbcode-image_url").get("value") +'[/img]');
	}
	kToggleOrSwap("kbbcode-image-options");
}

function kGrowShrinkMessage(change){
	var m = $('kbbcode-message');
	var p = $('kbbcode-preview');
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

/*
function newAttachment() {
	var newfile = $('knewfile');
	var id = newfile.retrieve('nextid',1);
	newfile.store('nextid',id+1);
	var input = newfile.getElement('input');
	input.addEvent('change', function() {
		this.removeEvents('change');
		var attachment = $('kattachment');
		var file = attachment.clone().inject(newfile,'before').set('id','attachment'+id);
		var filename = file.getElement('.kfile').set('text',input.get('value')).removeProperty('style');
		var status = file.getElement('.kstat').set('text','Uploading..').removeProperty('style');
		var remove = file.removeProperty('style').getElement('a').addEvent('click', function() {file.dispose(); return false; });

		var iframe = new IFrame({
		id: 'upload_target'+id,
		name: 'upload_target'+id,
		styles: {
			display: 'none'
		},
		events: {
			load: function(){
				var item = $H(JSON.decode(window.frames['upload_target'+id].document.body.innerHTML));
				if (!item.error) {
					if (!item.width)
						status.set('text', '('+item.mime+', '+item.size+' bytes)');
					else
						status.set('text', '('+item.mime+', '+item.width+'x'+item.height+'px, '+item.size+' bytes)');
				}
				else {
					status.set('text', '('+item.error+')');
				}
				//this.dispose();
			}
		}
		});
		iframe.inject(status, 'after');

		var form = $('postform');
		var action = form.getElement('input[name=action]');

		var properties = form.getProperties('target', 'action');
		var actionprop =  action.getProperties('value');
		form.set('target','upload_target'+id);
		form.set('action','<?php echo CKunenaLink::GetJsonURL('uploadfile','upload');?>');
		action.set('value', 'uploadfile');
		form.submit();
		form.setProperties(properties);
		action.setProperties(actionprop);
		input.setProperty('value', '');
		newAttachment();
	});
}

window.addEvent('domready', function() {
	newAttachment();
});
*/

window.addEvent('domready', function() {
	var uploader = new plupload.Uploader({
		runtimes : 'flash',
		browse_button : 'kuploadfiles',
		max_file_size : '1mb',
		url : '<?php echo CKunenaLink::GetJsonURL('uploadfile','upload');?>',
		resize : {width : 320, height : 240, quality : 90},
		flash_swf_url : '/plupload/js/plupload.flash.swf',
		//silverlight_xap_url : '/plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip,gz"}
		]
	});

	uploader.bind('Init', function(up, params) {
		$('kattachmentsnote').set('html', "<div>Multi-File Upload enabled: " + params.runtime + "</div>");
	});

	uploader.bind('FilesAdded', function(up, files) {
		$each(files, function(file, i) {
			fileDiv = new Element('div', {id: file.id, html: file.name + ' (' + plupload.formatSize(file.size) + ') <a></a> <b></b>'});
			fileDiv.inject($('kattachments'), 'bottom');
			$$('#'+file.id+' a').addEvent('click', function(e) { $(file.id).dispose(); uploader.removeFile(file); return false;});
		});
		$('kuploadfiles').fireEvent('upload', null, 3000);
	});

	uploader.bind('FilesRemoved', function(up, file) {
	});

	uploader.bind('UploadProgress', function(up, file) {
		$$("#" + file.id + " b").set('html', file.percent + "%");
	});

	$('kuploadfiles').addEvent('upload', function() {
		uploader.start();
	});

	$('kuploadfiles').setProperty('value', '');
	uploader.init();
});

<?php
$script = ob_get_contents();
ob_end_clean();

$document->addScriptDeclaration($script);
