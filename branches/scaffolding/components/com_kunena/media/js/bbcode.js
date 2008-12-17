/**
* @version		$Id: bbcode.js 423 2008-11-13 05:21:21Z louis $
* @package		JXtended
* @subpackage	JavaScript
* @copyright	Copyright (C) 2008 Webimagery, LLC. All rights reserved.
* @license		GNU/GPL unless otherwise specified
*/

/**
 * NAWTE 0.3 - Compressed
 * NAWTE is released under the MIT License (http://opensource.org/licenses/mit-license.php)
 */
Element.extend({getSelectedText:function(){if(window.ie)return document.selection.createRange().text;return this.getValue().substring(this.selectionStart,this.selectionEnd);},replaceSelectedText:function(newtext,isLast){var isLast=(isLast==null)?true:isLast;var scroll_top=this.scrollTop;if(window.ie){this.focus();var range=document.selection.createRange();range.text=newtext;if(isLast){range.select();this.scrollTop=scroll_top;}}
else{originalStart=this.selectionStart;originalEnd=this.selectionEnd;this.value=this.value.substring(0,originalStart)+newtext+this.value.substring(originalEnd);if(isLast==false){this.setSelectionRange(originalStart,originalStart+newtext.length);}
else{this.setSelectionRange(originalStart+newtext.length,originalStart+newtext.length);this.scrollTop=scroll_top;}
this.focus();}}});var nawte=new Class({initialize:function(element,list,options){this.el=$(element);this.options=Object.extend({dispatchChangeEvent:false,changeEventDelay:200,interceptTabs:true},options||{});if(this.options.dispatchChangeEvent){this.el.addEvents({'focus':function(){this.timer=this.watchChange.periodical(this.options.changeEventDelay,this);}.bind(this),'blur':function(){this.timer=$clear(this.timer);}.bind(this)});}
if(this.options.interceptTabs){this.el.addEvent('keypress',function(event){if(event.keyCode==9){event.preventDefault();this.replaceSelection("\t");}}.bind(this));}
if(!$defined(list)||list==""){list=new Element('li');list.injectBefore(this.el);this.list=list;}
else{this.list=$(list);}
this.oldContent=this.el.value;},watchChange:function(){if(this.oldContent!=this.el.value){this.oldContent=this.el.value;this.el.fireEvent('change');}},getSelection:function(){return this.el.getSelectedText();},wrapSelection:function(wrapper,isLast){var isLast=(isLast==null)?true:isLast;this.el.replaceSelectedText(wrapper+this.el.getSelectedText()+wrapper,isLast);},insertBefore:function(insertText,isLast){var isLast=(isLast==null)?true:isLast;this.el.replaceSelectedText(insertText+this.el.getSelectedText(),isLast);},insertAfter:function(insertText,isLast){var isLast=(isLast==null)?true:isLast;this.el.replaceSelectedText(this.el.getSelectedText()+insertText,isLast);},replaceSelection:function(newText,isLast){var isLast=(isLast==null)?true:isLast;this.el.replaceSelectedText(newText,isLast);},processEachLine:function(callback,isLast){var isLast=(isLast==null)?true:isLast;var lines=this.el.getSelectedText().split("\n");var newlines=[];lines.each(function(line){if(line!="")
newlines.push(callback.attempt(line,this));else
newlines.push("");}.bind(this));this.el.replaceSelectedText(newlines.join("\n"),isLast);},getValue:function(){return this.el.value;},setValue:function(text){this.el.setProperty('value',text);this.el.focus();},addFunction:function(name,callback,args){var item=new Element('li');var itemlink=new Element('a',{'events':{'click':function(e){new Event(e).stop();callback.attempt(null,this);}.bind(this)},'href':'#'});itemlink.setHTML('<span>'+name+'</span>');itemlink.setProperties(args||{});itemlink.injectInside(item);item.injectInside(this.list);}});
// end Nawte 0.3 Compressed

/**
 * BBCode editor behavior for a textarea
 *
 * @package		JXtended
 * @subpackage	JavaScript
 * @version		1.0
 */
var JXBBCodeEditor = new Class({

    getOptions: function(){
        return {};
    },

    initialize: function(options) {
		// handle the options
        this.setOptions(this.getOptions(), options);
        if (this.options.initialize) this.options.initialize.call(this);

		var els = $$('div#bbcode-editor');
		console.log(els);
		els.each(function(el){
			// create editor
			nawte = new nawte($(el).getElement('textarea.editor'), $(el).getElement('ul.toolbar'));
console.log(nawte);
			// buttons
			nawte.addFunction('B', function() {
				selection = this.getSelection();
				this.replaceSelection('[b]' + selection + '[/b]');
				if (selection.length < 1) {
					this.el.setSelectionRange(this.el.selectionStart - 4, this.el.selectionStart - 4);
				}
			}, {'id': 'bold_button'});
			nawte.addFunction('I', function() {
				selection = this.getSelection();
				this.replaceSelection('[i]' + selection + '[/i]');
				if (selection.length < 1) {
					this.el.setSelectionRange(this.el.selectionStart - 4, this.el.selectionStart - 4);
				}
			}, {'id': 'italic_button'});
			nawte.addFunction('U', function() {
				selection = this.getSelection();
				this.replaceSelection('[u]' + selection + '[/u]');
				if (selection.length < 1) {
					this.el.setSelectionRange(this.el.selectionStart - 4, this.el.selectionStart - 4);
				}
			}, {'id': 'underline_button'});
			nawte.addFunction('S', function() {
				selection = this.getSelection();
				this.replaceSelection('[s]' + selection + '[/s]');
				if (selection.length < 1) {
					this.el.setSelectionRange(this.el.selectionStart - 4, this.el.selectionStart - 4);
				}
			}, {'id': 'strikethrough_button'});
			nawte.addFunction(JXLang.QUOTE, function() {
				selection = this.getSelection();
				this.replaceSelection('[quote]' + selection + '[/quote]');
				if (selection.length < 1) {
					this.el.setSelectionRange(this.el.selectionStart - 8, this.el.selectionStart - 8);
				}
			}, {'id': 'quote_button'});
			nawte.addFunction(JXLang.IMAGE, function() {
				var selection = this.getSelection();
				if (selection.length < 1) {
					response = prompt(JXLang.ENTER_IMAGE,'http://');
					if (response != null) {
						selection == response;
					}
				}
				this.replaceSelection('[img]'+selection+'[/img]');
				if (selection.length < 1) {
					this.el.setSelectionRange(this.el.selectionStart - 6, this.el.selectionStart - 6);
				}
			}, {'id': 'image_button'});
			nawte.addFunction(JXLang.LINK, function() {
				var selection = this.getSelection();
				var response = prompt(JXLang.ENTER_LINK,'http://');
				if (response == null) {
					return;
				}
				this.replaceSelection('[url='+(response == '' ? 'http://' : response)+']'+(selection == '' ? JXLang.LINK_TEXT : selection)+'[/url]');
				if (selection.length < 1) {
					this.el.setSelectionRange(this.el.selectionStart - (JXLang.LINK_TEXT.length + 6), this.el.selectionStart - 6);
				}
			}, {'id': 'link_button'});
			nawte.addFunction(JXLang.CODE, function() {
				var selection = this.getSelection();
				var response = prompt(JXLang.ENTER_LANG,'html');
				if (response == null) {
					return;
				}
				this.replaceSelection('[code='+(response == '' ? 'html' : response)+']'+selection+'[/code]');
				if (selection.length < 1) {
					this.el.setSelectionRange(this.el.selectionStart - 7, this.el.selectionStart - 7);
				}
			}, {'id': 'code_button'});
			
			emoticons = $(el).getElements('img.emoticon-palette');
			emoticons.each(function(emoticon){
				emoticon.addEvent('click', function(){
					nawte.replaceSelection(this.getProperty('alt'));
				})
			})
		});
    }
});
	
// bind the Events and Options objects to the JXHighlighter class
JXBBCodeEditor.implement(new Events);
JXBBCodeEditor.implement(new Options);


window.addEvent('domready', function(){
	new JXBBCodeEditor();
})