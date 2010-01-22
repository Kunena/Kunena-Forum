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


/*
	Class: nawte
		
	Info:
		nawte is a class to help you put together your own WYSIWYM editor. It works by hooking up to
		a textarea. From there you are free to add any *function* you want via the <addFunction> method.
		Any function you add will do two things: First, it will add a button to the textarea's toolbar. 
		Second, it will perform a TextTransform operation (this is where nawte is helpful). An example
		function could be "bold". This would add a "bold" button to your textarea and perform any text 
		transformation required to make the text bold. The beauty of this is that, nawte doesn't depend on
		any markup language. If you want your output to be html, so be it! If you prefer markdown or textile,
		once again, no problems!
		
		All the TextTransform functions (<wrapSelection>, <insertBefore>, <insertAfter>, 
		<replaceSelection>, <processEachLine>) take a parameter 
		named "isLast". This parameter is used for chaining multiple text transformation. Setting it to
		false means you are going to do more transformations once the current transformation is applied.
		The example code below will help clarify that. Note that if you are only doing one text transformation
		in your function (for example, only wrapping the selection) you can ignore this parameter, it will be
		set to "true" by default.
		
	General Example:
		(start code)
		//creating a nawte object
		nawte = new nawte('thetext', 'toolbar');
		
		//adding a "List" button that will create a new list if nothing is selected
		//or make each line of the selection into a list item if some text is selected...
		nawte.addFunction("list", function() {
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
				
				this.insertBefore("<ul>\n", false); 
				this.insertAfter("\n</ul>", true); //now isLast is set to true, because it is the last one!
				
			}
		});
		(end)
	
	Version:
		Current Version: 0.3
	
	License:
		NAWTE is released under the MIT License (http://opensource.org/licenses/mit-license.php)
		
	Changelog:
		*From version 0.2 to version 0.3:*
		- New "onchange" event: <nawte.onchange>
		- New "interceptTabs" option
		- Several bugs fixed
		
		Check out the new contructor here: <nawte.nawte(constructor)>
			
		
*/

var nawte = new Class({

	Implements: Options,
	
	options: {
		displatchChangeEvent: false,
		changeEventDelay: 200,
		interceptTabs: true
	},
	/*
		Constructor: nawte (constructor)
			Creates a new nawte object.
			
		Arguments:
			element - a string, the ID of your textarea
			list - optional, a string, the id of your toolbal (a UL element)
			options - optional, an object, details below
			
		Options:
			dispatchChangeEvent - a boolean, set to true if you want to dispatch the "change" event. default: false
			changeEventDelay - an integer, they delay for the periodical function that dispatches the change event. default: 200
			interceptTabs - a boolean, set to true if you want the tab key to insert a tab in the text area, set to false for default tab behavior. default: true
		
		Additional Info:
			As you might've noticed, there is a new options parameter in nawte 0.3. It is now possible to watch the
			"change" event of your textarea . This event will notify you when the content of your textarea has been
			changed, eighter by typing in it, or by pressing on one of the toolbar buttons. 
			
			The purpose of this is that normally, the textarea would not fire a "change" event when nawte inserts text in it (or when you copy/paste
			some text in there too). By setting dispatchChangeEvent to true, a periodical function will watch the textarea
			for any changes and fire the "change" event whenever the content of the textarea has been changed, no matter how
			it was changed. You can change the delay of this periodical function with the changeEventDelay option. The default
			delay is 200ms.
			
		Example:
			(start code)
			var myNawte = new nawte('myTextarea', 'myToolbar', {
				dispatchChangeEvent: true, 
				changeEventDelay: 150, 
				interceptTab: true
			});
			(end)
	*/
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
	
	/*
		Event: onchange
			Fired when the content of the textarea is changed.
			
		Details:
			As mentioned above, the onchange event of the textarea won't be fired when nawte inserts content in it
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
			>this.wrapSelection("**");
			>//selection will become: **selection**
	*/
	
	wrapSelection: function(wrapper, isLast) {
		var isLast = (isLast == null) ? true : isLast;
		this.el.replaceSelectedText(wrapper + this.el.getSelectedText() + wrapper, isLast);
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
		var isLast = (isLast == null) ? true : isLast;
		where = (where == "") ? 'after' : where;
		var newText = (where == "before") ? insertText + this.el.getSelectedText() : this.el.getSelectedText() + insertText;
		this.el.replaceSelectedText(newText, isLast);
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
		var isLast = (isLast == null) ? true : isLast;
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
			The main concept of NAWTE, this is where you add functions (i.e. buttons) to your editor.
			
		Arguments:
			name - a string, the name of the function to add
			callback - a function, what your button will do
			args - optional, an object, additional HTML properties you want to add to the button
			
		Additional Info:
			This is the basic idea of NAWTE, each function you add (for example "BOLD") will add a new
			button to your editor. In your callback function, you can use any of the TextTransform functions
			to transform the selection, insert new text etc..
		
		Example:
			(start code)
			//we will add a bold button that will surround the selection
			//with <b>, </b>
			myNawte.addFunction('bold', function() {
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
