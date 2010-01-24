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

?>

<script language="JavaScript" type="text/javascript">

window.addEvent('domready', function() {

//creating a nawte object
kbbcode = new nawte('kbbcode-message', 'kbbcode-toolbar');

kbbcode.addFunction('Bold', function() {
	this.replaceSelection('[b]' + this.getSelection() + '[/b]');
}, {'id': 'kbbcode-bold_button',
	'title': '<?php echo _KUNENA_EDITOR_BOLD;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_BOLD;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_BOLD;?>")'});

kbbcode.addFunction('Italic', function() {
	this.replaceSelection('[i]' + this.getSelection() + '[/i]');
}, {'id': 'kbbcode-italic_button',
	'title': '<?php echo _KUNENA_EDITOR_ITALIC;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_ITALIC;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_ITALIC;?>")'});

kbbcode.addFunction('Underline', function() {
	this.replaceSelection('[u]' + this.getSelection() + '[/u]');
}, {'id': 'kbbcode-underline_button',
	'title': '<?php echo _KUNENA_EDITOR_UNDERL;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_UNDERL;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_UNDERL;?>")'});

kbbcode.addFunction('Strike', function() {
	this.replaceSelection('[strike]' + this.getSelection() + '[/strike]');
}, {'id': 'kbbcode-strike_button',
	'title': '<?php echo _KUNENA_EDITOR_STRIKE;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_STRIKE;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_STRIKE;?>")'});

kbbcode.addFunction('Sub', function() {
	this.replaceSelection('[sub]' + this.getSelection() + '[/sub]');
}, {'id': 'kbbcode-sub_button',
	'title': '<?php echo _KUNENA_EDITOR_SUB;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_SUB;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_SUB;?>")'});

kbbcode.addFunction('Sup', function() {
	this.replaceSelection('[sup]' + this.getSelection() + '[/sup]');
}, {'id': 'kbbcode-sup_button',
	'title': '<?php echo _KUNENA_EDITOR_SUP;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_SUP;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_SUP;?>")'});

kbbcode.addFunction('Size', function() {
	this.replaceSelection('[size=4]' + this.getSelection() + '[/size]');
}, {'id': 'kbbcode-size_button',
	'title': '<?php echo _KUNENA_EDITOR_FONTSIZE;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_FONTSIZE;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_FONTSIZE;?>")'});

kbbcode.addFunction('Color', function() {
	selection = this.getSelection();
	this.replaceSelection('[color=#FF6600]' + selection + '[/color]');
}, {'id': 'kbbcode-color_button',
	'title': '<?php echo _KUNENA_EDITOR_COLOR;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_COLOR;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_COLOR;?>")'});

<?php
if ($kunena_config->showspoilertag) {
?>
kbbcode.addFunction('Spoiler', function() {
	this.replaceSelection('[spoiler]' + this.getSelection() + '[/spoiler]');
}, {'id': 'kbbcode-spoiler_button',
	'title': '<?php echo _KUNENA_EDITOR_SPOILER;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_SPOILER;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_SPOILER;?>")'});
<?php
}
?>

kbbcode.addFunction('Hide', function() {
	this.replaceSelection('[hide]' + this.getSelection() + '[/hide]');
}, {'id': 'kbbcode-hide_button',
	'title': '<?php echo _KUNENA_EDITOR_HIDE;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_HIDE;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_HIDE;?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator1'});

//adding a "List" button that will create a new list if nothing is selected
//or make each line of the selection into a list item if some text is selected...
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
		// here you see the purpose of the isLast parameter... since we are applying
		// more TextTransform methods to the selection, isLast must be set to false!

		this.insert("[ul]\n", "before", false);
		this.insert("\n[/ul]", "after", true); //now isLast is set to true, because it is the last one!

	}
}, {'id': 'kbbcode-ulist_button',
	'title': '<?php echo _KUNENA_EDITOR_ULIST;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_ULIST;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_ULIST;?>")'});

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
		// here you see the purpose of the isLast parameter... since we are applying
		// more TextTransform methods to the selection, isLast must be set to false!

		this.insert("[ol]\n", "before", false);
		this.insert("\n[/ol]", "after", true); //now isLast is set to true, because it is the last one!

	}
}, {'id': 'kbbcode-olist_button',
	'title': '<?php echo _KUNENA_EDITOR_OLIST;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_OLIST;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_OLIST;?>")'});

kbbcode.addFunction('List', function() {
	this.replaceSelection('[li]' + this.getSelection() + '[/li]');
}, {'id': 'kbbcode-list_button',
	'title': '<?php echo _KUNENA_EDITOR_LIST;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_LIST;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_LIST;?>")'});

kbbcode.addFunction('Left', function() {
	this.replaceSelection('[left]' + this.getSelection() + '[/left]');
}, {'id': 'kbbcode-left_button',
	'title': '<?php echo _KUNENA_EDITOR_LEFT;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_LEFT;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_LEFT;?>")'});

kbbcode.addFunction('Center', function() {
	this.replaceSelection('[center]' + this.getSelection() + '[/center]');
}, {'id': 'kbbcode-center_button',
	'title': '<?php echo _KUNENA_EDITOR_CENTER;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_CENTER;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_CENTER;?>")'});

kbbcode.addFunction('Right', function() {
	this.replaceSelection('[right]' + this.getSelection() + '[/right]');
}, {'id': 'kbbcode-right_button',
	'title': '<?php echo _KUNENA_EDITOR_RIGHT;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_RIGHT;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_RIGHT;?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator2'});

kbbcode.addFunction('Quote', function() {
	this.replaceSelection('[quote]' + this.getSelection() + '[/quote]');
}, {'id': 'kbbcode-quote_button',
	'title': '<?php echo _KUNENA_EDITOR_QUOTE;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_QUOTE;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_QUOTE;?>")'});

kbbcode.addFunction('Code', function() {
	this.replaceSelection('[code]' + this.getSelection() + '[/code]');
}, {'id': 'kbbcode-code_button',
	'title': '<?php echo _KUNENA_EDITOR_CODE;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_CODE;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_CODE;?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator3'});

kbbcode.addFunction('Image', function() {
	this.replaceSelection('[img]' + this.getSelection() + '[/img]');
}, {'id': 'kbbcode-image_button',
	'title': '<?php echo _KUNENA_EDITOR_IMAGE;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_IMAGE;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_IMAGE;?>")'});

kbbcode.addFunction('Link', function() {
	var selection = this.getSelection();
var response = prompt('Enter Link URL','');
if(response == null)
     return;
this.replaceSelection('[url=' +  (response == '' ? 'http://link_url/' : response) + ']' + (selection == '' ? 'Link Text' : selection) + '[/url]');
}, {'id': 'kbbcode-link_button',
	'title': '<?php echo _KUNENA_EDITOR_LINK;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_LINK;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_LINK;?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator4'});

<?php
if ($kunena_config->showebaytag) {
?>
kbbcode.addFunction('eBay', function() {
	this.replaceSelection('[ebay]' + this.getSelection() + '[/ebay]');
}, {'id': 'kbbcode-ebay_button',
	'title': '<?php echo _KUNENA_EDITOR_EBAY;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_EBAY;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_EBAY;?>")'});
<?php
}
?>

<?php
if ($kunena_config->showvideotag) {
?>
kbbcode.addFunction('Video', function() {
	this.replaceSelection('[video]' + this.getSelection() + '[/video]');
}, {'id': 'kbbcode-video_button',
	'title': '<?php echo _KUNENA_EDITOR_VIDEO;?>',
	'alt': '<?php echo _KUNENA_EDITOR_HELPLINE_VIDEO;?>',
	'onmouseover' : 'javascript:$("helpbox").set("value", "<?php echo _KUNENA_EDITOR_HELPLINE_VIDEO;?>")'});
<?php
}
?>

});






//
//
//<!--
//// bbCode control by
//// subBlue design
//// www.subBlue.com
//// adapted for Joomlaboard by the Two Shoes Module Factory (www.tsmf.net)
//// Startup variables
//var imageTag = false;
//var theSelection = false;
//var baseHeight;
//
//// Check for Browser & Platform for PC & IE specific bits
//// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
//var clientPC = navigator.userAgent.toLowerCase(); // Get client info
//var clientVer = parseInt(navigator.appVersion); // Get browser version
//var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
//var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
//                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
//                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
//var is_moz = 0;
//var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
//var is_mac = (clientPC.indexOf("mac")!=-1);
//var s;
//var newheight = 150;
//var change;
//
//function dE(n)
//{
//  s = document.postform.speicher.value;
//  if (document.getElementById(n).style.display == "none")
//    {
//    if (s != "") {document.getElementById(s).style.display = "none";}
//    document.getElementById(n).style.display = "block";
//    s=document.getElementById(n).id;
//    document.postform.speicher.value = s;
//    }
//  else
//    {
//      document.getElementById(n).style.display = "none";
//      s = "";
//      document.postform.speicher.value = s;
//    }
//}
//
//function size_messagebox(change)
//{
//    newheight = newheight + change;
//    if (newheight > 150) {document.postform.message.style.height = newheight + "px";}
//    else {
//      document.postform.message.style.height = "150px";
//      newheight = 150;}
//}
//
///**
//* Color pallette. From http://www.phpbb.de
//*/
//function colorPalette(dir, width, height)
//{
//	var r = 0, g = 0, b = 0;
//	var numberList = new Array(6);
//	var color = '';
//	numberList[0] = '00';
//	numberList[1] = '40';
//	numberList[2] = '80';
//	numberList[3] = 'BF';
//	numberList[4] = 'FF';
//	document.writeln('<table class="fb-color_table" cellspacing="1" cellpadding="0" border="0" style="width: 100%;">');
//	for (r = 0; r < 5; r++)
//	{
//		if (dir == 'h')	{document.writeln('<tr>');}
//		for (g = 0; g < 5; g++)	{
//			if (dir == 'v')	{document.writeln('<tr>');}
//			for (b = 0; b < 5; b++)	{
//				color = String(numberList[r]) + String(numberList[g]) + String(numberList[b]);
//				document.write('<td id="' + color + '" style="background-color:#' + color + '; width: ' + width + '; height: ' + height + ';">');
//				document.write('&nbsp;');
//				document.writeln('</td>');
//			  }
//			if (dir == 'v')	{document.writeln('</tr>');}
//		}
//		if (dir == 'h')	{document.writeln('</tr>');}
//	}
//	document.writeln('</table>');
//}
//
//// TODO: Remove jQuery based logic and replace with mootools behavior
////jQuery(document).ready(function()
////{
////	jQuery('table.fb-color_table td').click( function()
////	{
//////		var color = jQuery(this).css('background-color');
////		var color = jQuery(this).attr('id');
////		bbfontstyle('[color=#' + color + ']', '[/color]'); return false;
////	} );
////	jQuery('select#fb-bbcode_size').change( function()
////	{
////		var size = jQuery(this).val();
////		bbfontstyle('[size=' + size + ']', '[/size]'); return false;
////	} );
////} );
//
//// From http://www.massless.org/mozedit/
//
//function mozWrap(txtarea, open, close)
//{
//	var selLength = txtarea.textLength;
//	var selStart = txtarea.selectionStart;
//	var selEnd = txtarea.selectionEnd;
//	var scrollTop = txtarea.scrollTop;
//
//	if (selEnd == 1 || selEnd == 2)
//	{
//		selEnd = selLength;
//	}
//
//	var s1 = (txtarea.value).substring(0,selStart);
//	var s2 = (txtarea.value).substring(selStart, selEnd);
//	var s3 = (txtarea.value).substring(selEnd, selLength);
//
//	txtarea.value = s1 + open + s2 + close + s3;
//	txtarea.selectionStart = selEnd + open.length + close.length;
//	txtarea.selectionEnd = txtarea.selectionStart;
//	txtarea.focus();
//	txtarea.scrollTop = scrollTop;
//
//	return;
//}
//
//// Insert at Claret position. Code from
//// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
//function storeCaret(textEl)
//{
//	if (textEl.createTextRange)
//	{
//		textEl.caretPos = document.selection.createRange().duplicate();
//	}
//}
//
//// Insert BBCode in textarea. Code from
//// http://www.phpbb.de/
//function bbfontstyle(bbopen, bbclose) {
//	theSelection = false;
//   var txtarea = document.postform.message;
//	txtarea.focus();
//   if ((clientVer >= 4) && is_ie && is_win) {
//    theSelection = document.selection.createRange().text;
//		if (theSelection)
//		{
//			// Add tags around selection
//			document.selection.createRange().text = bbopen + theSelection + bbclose;
//			document.postform.message.focus();
//			theSelection = '';
//			return;
//		}
//  }
//
//	else if (document.postform.message.selectionEnd && (document.postform.message.selectionEnd - document.postform.message.selectionStart > 0))
//	{
//		mozWrap(document.postform.message, bbopen, bbclose);
//		document.postform.message.focus();
//		theSelection = '';
//		return;
//	}
//	//The new position for the cursor after adding the bbcode
//	var caret_pos = getCaretPosition(txtarea).start;
//	var new_pos = caret_pos + bbopen.length;
//
//	// Open tag
//	insert_text(bbopen + bbclose);
//
//	// Center the cursor when we don't have a selection
//	// Gecko and proper browsers
//	if (!isNaN(txtarea.selectionStart))
//	{
//		txtarea.selectionStart = new_pos;
//		txtarea.selectionEnd = new_pos;
//	}
//	// IE
//	else if (document.selection)
//	{
//		var range = txtarea.createTextRange();
//		range.move("character", new_pos);
//		range.select();
//		storeCaret(txtarea);
//	}
//
//	txtarea.focus();
//	return;
//}
//
//// Insert text at position. Code from
//// http://www.phpbb.de/
//function insert_text(text, spaces, popup)
//{
//	var txtarea;
//
//	if (!popup)
//	{
//		txtarea = document.postform.message;
//	}
//	else
//	{
//		txtarea = opener.document.postform.message;
//	}
//	if (spaces)
//	{
//		text = ' ' + text + ' ';
//	}
//
//	if (!isNaN(txtarea.selectionStart))
//	{
//		var sel_start = txtarea.selectionStart;
//		var sel_end = txtarea.selectionEnd;
//
//		mozWrap(txtarea, text, '');
//		txtarea.selectionStart = sel_start + text.length;
//		txtarea.selectionEnd = sel_end + text.length;
//	}
//	else if (txtarea.createTextRange && txtarea.caretPos)
//	{
//		if (baseHeight != txtarea.caretPos.boundingHeight)
//		{
//			txtarea.focus();
//			storeCaret(txtarea);
//		}
//
//		var caret_pos = txtarea.caretPos;
//		caret_pos.text = caret_pos.text.charAt(caret_pos.text.length - 1) == ' ' ? caret_pos.text + text + ' ' : caret_pos.text + text;
//	}
//	else
//	{
//		txtarea.value = txtarea.value + text;
//	}
//	if (!popup)
//	{
//		txtarea.focus();
//	}
//}
//
//// Caret Position object. Code from
//// http://www.phpbb.de/
//function caretPosition()
//{
//	var start = null;
//	var end = null;
//}
//
//// Get the caret position in an textarea. Code from
//// http://www.phpbb.de/
//function getCaretPosition(txtarea)
//{
//	var caretPos = new caretPosition();
//
//	// simple Gecko/Opera way
//	if(txtarea.selectionStart || txtarea.selectionStart == 0)
//	{
//		caretPos.start = txtarea.selectionStart;
//		caretPos.end = txtarea.selectionEnd;
//	}
//	// dirty and slow IE way
//	else if(document.selection)
//	{
//
//		// get current selection
//		var range = document.selection.createRange();
//
//		// a new selection of the whole txtarea
//		var range_all = document.body.createTextRange();
//		range_all.moveToElementText(txtarea);
//
//		// calculate selection start point by moving beginning of range_all to beginning of range
//		var sel_start;
//		for (sel_start = 0; range_all.compareEndPoints('StartToStart', range) < 0; sel_start++)
//		{
//			range_all.moveStart('character', 1);
//		}
//
//		txtarea.sel_start = sel_start;
//
//		// we ignore the end value for IE, this is already dirty enough and we don't need it
//		caretPos.start = txtarea.sel_start;
//		caretPos.end = txtarea.sel_start;
//	}
//
//	return caretPos;
//}
////#######################################################
//function textCounter(field, countfield, maxlimit) {
//   if(field.value.length > maxlimit){
//      field.value = field.value.substring(0, maxlimit);
//   }
//   else{
//      countfield.value = maxlimit - field.value.length;
//   }
//}

function submitForm() {
 submitme=1;
 formname=document.postform.fb_authorname.value;
 polltitle=document.getElementById('poll_title').value;
 polloptionone=document.getElementById('field_option0').value;
 if((polltitle.length<1) && (polloptionone.length<1)){
    alert("<?php echo _KUNENA_POLL_FORGOT_TITLE_OPTIONS;?>");
    submitme=0;
 }
 if ((formname.length<1)) {
    alert("<?php echo _POST_FORGOT_NAME_ALERT;?>");
    submitme=0;
 }
<?php
if ($kunena_config->askemail) {
?>
 formmail=document.postform.email.value;
 if ((formmail.length<1)) {
    alert("<?php echo _POST_FORGOT_EMAIL_ALERT;?>");
    submitme=0;
  }
<?php
}
?>
  formsubject=document.postform.subject.value;
  if ((formsubject.length<1)) {
    alert("<?php echo _POST_FORGOT_SUBJECT_ALERT;?>");
    submitme=0;
  }
  if (submitme>0) {
  //change the following line to true to submit form
    return true;
  }else{
    return false;
  }
}
function cancelForm() {
   document.forms['postform'].action.value = "cancel";
   return true;
}
//-->
</script>
