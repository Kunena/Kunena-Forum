<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
	<div id="kunena">
	<?php
		$this->displayMenu ();
		$this->displayLoginBox ();
		$this->displayBreadcrumb ();
		include 'reply_form.php';
		$this->displayFooter ();
	?>
	</div>
	<script type="text/javascript" src="js/default.js"></script>
	<script type="text/javascript" src="js/editor.js"></script>
	<script type="text/javascript" src="js/validate.js"></script>
	<script type="text/javascript">
	// <![CDATA[
	var kunena_toggler_close = "Collapse";
	var kunena_toggler_open = "Expand";
	// ]]>
	var arrayanynomousbox={}
	var pollcategoriesid = {"77":1,"163":1,"164":1};
	// <![CDATA[
				var number_field = 1;
				// ]]>
	// <![CDATA[
		   var KUNENA_POLL_CATS_NOT_ALLOWED = "The polls are not allowed in this category";
		   var KUNENA_EDITOR_HELPLINE_OPTION = "Poll: Option for the poll";
		   var KUNENA_POLL_OPTION_NAME = "Option";
		   var KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW = "The maximum number of poll options has been reached.";
		   var KUNENA_ICON_ERROR = "/administrator/images/publish_x.png";
		   var kunena_ajax_url_poll = "/forum/json?action=pollcatsallowed";
		// ]]>
	// <![CDATA[
	function kPreviewHelper()
	{
		if (_previewActive == true){
			previewRequest = new Request.JSON({url: "/forum/json?action=preview",
												onSuccess: function(response){
				var __message = $("kbbcode-preview");
				if (__message) {
					__message.set("html", response.preview);
				}
				}}).post({body: $("kbbcode-message").get("value")
			});
		}
	}

	window.addEvent('domready', function() {
	kbbcode = new kbbcode('kbbcode-message', 'kbbcode-toolbar', {
					dispatchChangeEvent: true,
					changeEventDelay: 1000,
					interceptTab: true
				});

	kbbcode.addFunction('Bold', function() {
		this.wrapSelection('[b]', '[/b]', false);
	}, {'id': 'kbbcode-bold-button',
		'title': 'Bold',
		'alt': 'Bold text: [b]text[/b]',
		'onmouseover' : '$("helpbox").set("value", "Bold text: [b]text[/b]")'});

	kbbcode.addFunction('Italic', function() {
		this.wrapSelection('[i]', '[/i]', false);
	}, {'id': 'kbbcode-italic-button',
		'title': 'Italic',
		'alt': 'Italic text: [i]text[/i]',
		'onmouseover' : '$("helpbox").set("value", "Italic text: [i]text[/i]")'});

	kbbcode.addFunction('Underline', function() {
		this.wrapSelection('[u]', '[/u]', false);
	}, {'id': 'kbbcode-underline-button',
		'title': 'Underline',
		'alt': 'Underline text: [u]text[/u]',
		'onmouseover' : '$("helpbox").set("value", "Underline text: [u]text[/u]")'});

	kbbcode.addFunction('Strike', function() {
		this.wrapSelection('[strike]', '[/strike]', false);
	}, {'id': 'kbbcode-strike-button',
		'title': 'Strikethrough',
		'alt': 'Strikethrough Text: [strike]Text[/strike]',
		'onmouseover' : '$("helpbox").set("value", "Strikethrough Text: [strike]Text[/strike]")'});

	kbbcode.addFunction('Sub', function() {
		this.wrapSelection('[sub]', '[/sub]', false);
	}, {'id': 'kbbcode-sub-button',
		'title': 'Subscript',
		'alt': 'Subscript Text: [sub]Text[/sub]',
		'onmouseover' : '$("helpbox").set("value", "Subscript Text: [sub]Text[/sub]")'});

	kbbcode.addFunction('Sup', function() {
		this.wrapSelection('[sup]', '[/sup]', false);
	}, {'id': 'kbbcode-sup-button',
		'title': 'Superscript',
		'alt': 'Superscript Text: [sup]Text[/sup]',
		'onmouseover' : '$("helpbox").set("value", "Superscript Text: [sup]Text[/sup]")'});

	kbbcode.addFunction('Size', function() {
		kToggleOrSwap("kbbcode-size-options");
	}, {'id': 'kbbcode-size-button',
		'title': 'Font size',
		'alt': 'Fontsize: Select Fontsize and Apply to current selection',
		'onmouseover' : '$("helpbox").set("value", "Fontsize: Select Fontsize and Apply to current selection")'});

	kbbcode.addFunction('Color', function() {
		kToggleOrSwap("kbbcode-colorpalette");
	}, {'id': 'kbbcode-color-button',
		'title': 'Color',
		'alt': 'Color: [color=#FF6600]text[/color]',
		'onmouseover' : '$("helpbox").set("value", "Color: [color=#FF6600]text[/color]")'});

	kbbcode.addFunction('#', function() {
	}, {'id': 'kbbcode-separator1'});

	kbbcode.addFunction("uList", function() {
		selection = this.getSelection();
		if (selection == "") {
			this.wrapSelection("\n[ul]\n  [li]", "[/li]\n  [li][/li]\n[/ul]", false);
		}
		else {
			this.processEachLine(function(line) {
				newline = "  [li]" + line + "[/li]";
				return newline;
			}, false);
			this.insert("[ul]\n", "before", false);
			this.insert("\n[/ul]\n", "after", true); //now isLast is set to true, because it is the last one!
		}
	}, {'id': 'kbbcode-ulist-button',
		'title': 'Unordered List',
		'alt': 'Unordered List: [ul] [li]text[/li] [/ul] - Tip: a list must contain List Items',
		'onmouseover' : '$("helpbox").set("value", "Unordered List: [ul] [li]text[/li] [/ul] - Tip: a list must contain List Items")'});

	kbbcode.addFunction("oList", function() {
		selection = this.getSelection();
		if (selection == "") {
			this.wrapSelection("\n[ol]\n  [li]", "[/li]\n  [li][/li]\n[/ol]", false);
		}
		else {
			this.processEachLine(function(line) {
				newline = "  [li]" + line + "[/li]";
				return newline;
			}, false);
			this.insert("[ol]\n", "before", false);
			this.insert("\n[/ol]\n", "after", true); //now isLast is set to true, because it is the last one!
		}
	}, {'id': 'kbbcode-olist-button',
		'title': 'Ordered List',
		'alt': 'Ordered List: [ol] [li]text[/li] [/ol] - Tip: a list must contain List Items',
		'onmouseover' : '$("helpbox").set("value", "Ordered List: [ol] [li]text[/li] [/ol] - Tip: a list must contain List Items")'});

	kbbcode.addFunction('List', function() {
		this.wrapSelection('  [li]', '[/li]', false);
	}, {'id': 'kbbcode-list-button',
		'title': 'List Item',
		'alt': 'List Item: [li] list item [/li]',
		'onmouseover' : '$("helpbox").set("value", "List Item: [li] list item [/li]")'});

	kbbcode.addFunction('Left', function() {
		this.wrapSelection('[left]', '[/left]', false);
	}, {'id': 'kbbcode-left-button',
		'title': 'Align left',
		'alt': 'Align left: [left]Text[/left]',
		'onmouseover' : '$("helpbox").set("value", "Align left: [left]Text[/left]")'});

	kbbcode.addFunction('Center', function() {
		this.wrapSelection('[center]', '[/center]', false);
	}, {'id': 'kbbcode-center-button',
		'title': 'Align center',
		'alt': 'Align center: [center]Text[/center]',
		'onmouseover' : '$("helpbox").set("value", "Align center: [center]Text[/center]")'});

	kbbcode.addFunction('Right', function() {
		this.wrapSelection('[right]', '[/right]', false);
	}, {'id': 'kbbcode-right-button',
		'title': 'Align right',
		'alt': 'Align right: [right]Text[/right]',
		'onmouseover' : '$("helpbox").set("value", "Align right: [right]Text[/right]")'});

	kbbcode.addFunction('#', function() {
	}, {'id': 'kbbcode-separator2'});

	kbbcode.addFunction('Quote', function() {
		this.wrapSelection('[quote]', '[/quote]', false);
	}, {'id': 'kbbcode-quote-button',
		'title': 'Quote',
		'alt': 'Quote text: [quote]text[/quote]',
		'onmouseover' : '$("helpbox").set("value", "Quote text: [quote]text[/quote]")'});

	kbbcode.addFunction('Code', function() {
		kToggleOrSwap("kbbcode-code-options");
	}, {'id': 'kbbcode-code-button',
		'title': 'Code',
		'alt': 'Code display: [code]code[/code]',
		'onmouseover' : '$("helpbox").set("value", "Code display: [code]code[/code]")'});

	kbbcode.addFunction("Table", function() {
		selection = this.getSelection();
		if (selection == "") {
			this.wrapSelection("\n[table]\n  [tr]\n    [td]", "[/td]\n    [td][/td]\n  [/tr]\n  [tr]\n    [td][/td]\n    [td][/td]\n  [/tr]\n[/table]", false);
		}
		else {
			this.processEachLine(function(line) {
				newline = "  [tr][td]" + line + "[/td][/tr]";
				return newline;
			}, false);
			this.insert("[table]\n", "before", false);
			this.insert("\n[/table]\n", "after", true); //now isLast is set to true, because it is the last one!
		}
	}, {'id': 'kbbcode-table-button',
		'title': 'Table',
		'alt': 'Create an embedded table: [table][tr][td]line1[/td][/tr][tr][td]lines[/td][/tr][/table]',
		'onmouseover' : '$("helpbox").set("value", "Create an embedded table: [table][tr][td]line1[/td][/tr][tr][td]lines[/td][/tr][/table]")'});

	kbbcode.addFunction('Spoiler', function() {
		this.wrapSelection('[spoiler]', '[/spoiler]', false);
	}, {'id': 'kbbcode-spoiler-button',
		'title': 'Spoiler',
		'alt': 'Spoiler: Text is only shown after you click the spoiler',
		'onmouseover' : '$("helpbox").set("value", "Spoiler: Text is only shown after you click the spoiler")'});

	kbbcode.addFunction('Hide', function() {
		this.wrapSelection('[hide]', '[/hide]', false);
	}, {'id': 'kbbcode-hide-button',
		'title': 'Hide text from Guests',
		'alt': 'Hidden text: [hide]any hidden text[/hide] - hide part of message from Guests',
		'onmouseover' : '$("helpbox").set("value", "Hidden text: [hide]any hidden text[/hide] - hide part of message from Guests")'});

	kbbcode.addFunction('#', function() {
	}, {'id': 'kbbcode-separator3'});

	kbbcode.addFunction('Image', function() {
		kToggleOrSwap("kbbcode-image-options");
	}, {'id': 'kbbcode-image-button',
		'title': 'Image link',
		'alt': 'Image link: [img size=400]http://www.google.com/images/web_logo_left.gif[/img]',
		'onmouseover' : '$("helpbox").set("value", "Image link: [img size=400]http://www.google.com/images/web_logo_left.gif[/img]")'});

	kbbcode.addFunction('Link', function() {
		sel = this.getSelection();
		if (sel != "") {
			$('kbbcode-link_text').set('value', sel);
		}
		kToggleOrSwap("kbbcode-link-options");
	}, {'id': 'kbbcode-link-button',
		'title': 'Link',
		'alt': 'Link: [url=http://www.zzz.com/]This is a link[/url]',
		'onmouseover' : '$("helpbox").set("value", "Link: [url=http://www.zzz.com/]This is a link[/url]")'});

	kbbcode.addFunction('#', function() {
	}, {'id': 'kbbcode-separator4'});

	kbbcode.addFunction('Poll', function() {
		kToggleOrSwap("kbbcode-poll-options");
	}, {'id': 'kbbcode-poll-button',
	'style':'display: none;',	'title': 'Poll',
		'alt': 'Add or edit a poll to this message',
		'onmouseover' : '$("helpbox").set("value", "Add or edit a poll to this message")'});

	kbbcode.addFunction('#', function() {
	}, {'id': 'kbbcode-separator5'
	,'style':'display: none;'});

	kbbcode.addFunction('eBay', function() {
		this.wrapSelection('[ebay]', '[/ebay]', false);
	}, {'id': 'kbbcode-ebay-button',
		'title': 'eBay Item',
		'alt': 'eBay: [ebay]ItemID[/ebay]',
		'onmouseover' : '$("helpbox").set("value", "eBay: [ebay]ItemID[/ebay]")'});

	kbbcode.addFunction('Video', function() {
		kToggleOrSwap("kbbcode-video-options");
	}, {'id': 'kbbcode-video-button',
		'title': 'Video',
		'alt': 'Video: Select Provider or URL - modus',
		'onmouseover' : '$("helpbox").set("value", "Video: Select Provider or URL - modus")'});

	kbbcode.addFunction('Map', function() {
		this.wrapSelection('[map]', '[/map]', false);
	}, {'id': 'kbbcode-map-button',
		'title': 'Map',
		'alt': 'Insert a Map into the message: [map]Address[/map]',
		'onmouseover' : '$("helpbox").set("value", "Insert a Map into the message: [map]Address[/map]")'});


	kbbcode.addFunction('#', function() {
	}, {'id': 'kbbcode-separator6'});

	kbbcode.addFunction('PreviewBottom', function() {
		kToggleOrSwapPreview("kbbcode-preview-bottom");
	}, {'id': 'kbbcode-previewbottom-button',
		'title': 'Preview Pane Below',
		'alt': 'Display a live message preview pane below the edit area',
		'onmouseover' : '$("helpbox").set("value", "Display a live message preview pane below the edit area")'});

	kbbcode.addFunction('PreviewRight', function() {
		kToggleOrSwapPreview("kbbcode-preview-right");
	}, {'id': 'kbbcode-previewright-button',
		'title': 'Preview Pane Right',
		'alt': 'Display a live message preview pane to the right of the edit area',
		'onmouseover' : '$("helpbox").set("value", "Display a live message preview pane to the right of the edit area")'});

	kbbcode.addFunction('#', function() {
	}, {'id': 'kbbcode-separator7'});


	kbbcode.addFunction('Help', function() {
		window.open('http://docs.kunena.org/index.php/bbcode');
	}, {'id': 'kbbcode-help-button',
		'title': 'Help',
		'alt': 'Get Help on how to use the bbcode editor',
		'onmouseover' : '$("helpbox").set("value", "Get Help on how to use the bbcode editor")'});

	$('kbbcode-message').addEvent('change', function(){
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

		bindAttachments();
		newAttachment();
		//This is need to retrieve the video provider selected by the user in the dropdownlist
		if ($('kvideoprovider') != undefined) {
			$('kvideoprovider').addEvent('change', function() {
				var sel = $$('#kvideoprovider option:selected');
				sel.each(function(el) {
					$('kvideoprovider').store('videoprov',el.value);
				});
			});
		}

		// Fianlly apply some screwy IE7 and IE8 fixes to the html...
		IEcompatibility();
	});



	// ]]>
	// <![CDATA[

	window.addEvent('domready', function(){

		function kunenaSelectUsername(obj, kuser) {
			if (obj.get('checked')) {
				$('kauthorname').set('value',kunena_anonymous_name).removeProperty('disabled');
				$('kanynomous-check-name').setStyle('display');
			} else {
				$('kanynomous-check-name').setStyle('display','none');
				$('kauthorname').set('value',kuser).set('disabled', 'disabled');
			}
		}

		function kunenaCheckPollallowed(catid) {
			if ( pollcategoriesid[catid] != undefined ) {
				$('kpoll-hide-not-allowed').setStyle('display');
				$('kbbcode-separator5').setStyle('display');
				$('kbbcode-poll-button').setStyle('display');
				$('kpoll-not-allowed').set('text', ' ');
			} else {
				$('kbbcode-separator5').setStyle('display','none');
				$('kbbcode-poll-button').setStyle('display','none');
				$('kpoll-hide-not-allowed').setStyle('display','none');
			}
		}

		function kunenaCheckAnonymousAllowed(catid) {
			if ( arrayanynomousbox[catid] != undefined ) {
				$('kanynomous-check').setStyle('display');
			} else {
				$('kanynomous-check').setStyle('display','none');
				kbutton.removeProperty('checked');
			}

			if ( arrayanynomousbox[catid] ) {
				$('kanonymous').set('checked','checked');
			}
					kunenaSelectUsername(kbutton,kuser);
				}
		//	for hide or show polls if category is allowed
		if($('postcatid') != undefined) {
			$('postcatid').addEvent('change', function(e) {
				kunenaCheckPollallowed(this.value);
			});
		}

		if($('kauthorname') != undefined) {
			var kuser = $('kauthorname').get('value');
			var kbutton = $('kanonymous');
					kunenaSelectUsername(kbutton, kuser);
			kbutton.addEvent('click', function(e) {
				kunenaSelectUsername(this, kuser);
			});
				}
		//	to select if anynomous option is allowed on new topic tab
		if($('postcatid') != undefined) {
			$('postcatid').addEvent('change', function(e) {
				kunenaCheckAnonymousAllowed(this.value);
			});
		}

		if($('postcatid') != undefined) {
			kunenaCheckPollallowed($('postcatid').getSelected().get("value"));
			kunenaCheckAnonymousAllowed($('postcatid').getSelected().get("value"));
		}
	});


	// ]]>
	function keepAlive() { new Ajax("index.php", {method: "get"}).request(); }
	window.addEvent("domready", function() { keepAlive.periodical(17940000); });
	// <![CDATA[
			var kunena_anonymous_name = "Anonymous";
		// ]]>
	// <![CDATA[
	 function kShowDetail(srcElement) {var targetID, srcElement, targetElement, imgElementID, imgElement;targetID = srcElement.id + "_details";imgElementID = srcElement.id + "_img";targetElement = document.getElementById(targetID);imgElement = document.getElementById(imgElementID);if (targetElement.style.display == "none") {targetElement.style.display = "";imgElement.src = "http://test20.kunena.org//components/com_kunena/template/default/images/emoticons/w00t.png";} else {targetElement.style.display = "none";imgElement.src = "http://test20.kunena.org//components/com_kunena/template/default/images/emoticons/pinch.png";}}
	 // ]]>

	window.addEvent("domready", function() {
		var JTooltips = new Tips($$(".hasTip"), { maxTitleChars: 50, fixed: false});
	});

	</script>
	<script type='text/javascript'>
	/*<![CDATA[*/
		var jax_live_site = 'http://www.kontentdesign.com/kunena2/';
		var jax_site_type = '1.5';
	/*]]>*/
	</script>