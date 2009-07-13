<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
?>

<script language = "JavaScript" type = "text/javascript">
<?php
$fbConfig =& CKunenaConfig::getInstance();
?>
<!--
// bbCode control by
// subBlue design
// www.subBlue.com
// adapted for Joomlaboard by the Two Shoes Module Factory (www.tsmf.net)
// Startup variables
var imageTag = false;
var theSelection = false;
var baseHeight;

// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version
var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;
var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);
var s;
var newheight = 300;
var change;

function dE(n)
{
  s = document.postform.speicher.value;
  if (document.getElementById(n).style.display == "none")
    { 
    if (s != "") {document.getElementById(s).style.display = "none";}
    document.getElementById(n).style.display = "block"; 
    s=document.getElementById(n).id;
    document.postform.speicher.value = s;
    }
  else
    {
      document.getElementById(n).style.display = "none";
      s = "";
      document.postform.speicher.value = s;
    }
}

function size_messagebox(change)
{
    newheight = newheight + change;
    if (newheight > 200) {document.postform.message.style.height = newheight + "px";}
    else {
      document.postform.message.style.height = "300px";
      newheight = 300;}
}

/**
* Color pallette. From http://www.phpbb.de
*/
function colorPalette(dir, width, height)
{
	var r = 0, g = 0, b = 0;
	var numberList = new Array(6);
	var color = '';
	numberList[0] = '00';
	numberList[1] = '40';
	numberList[2] = '80';
	numberList[3] = 'BF';
	numberList[4] = 'FF';
	document.writeln('<table class="fb-color_table" cellspacing="1" cellpadding="0" border="0" style="width: 100%;">');
	for (r = 0; r < 5; r++)
	{
		if (dir == 'h')	{document.writeln('<tr>');}
		for (g = 0; g < 5; g++)	{
			if (dir == 'v')	{document.writeln('<tr>');}
			for (b = 0; b < 5; b++)	{
				color = String(numberList[r]) + String(numberList[g]) + String(numberList[b]);
				document.write('<td id="' + color + '" style="background-color:#' + color + '; width: ' + width + '; height: ' + height + ';">');
				document.write('&nbsp;');
				document.writeln('</td>');
			  }
			if (dir == 'v')	{document.writeln('</tr>');}
		}
		if (dir == 'h')	{document.writeln('</tr>');}
	}
	document.writeln('</table>');
}

jQuery(document).ready(function()
{
	jQuery('table.fb-color_table td').click( function() 
	{ 
//		var color = jQuery(this).css('background-color');
		var color = jQuery(this).attr('id');
		bbfontstyle('[color=#' + color + ']', '[/color]'); return false; 
	} );
	jQuery('select#fb-bbcode_size').change( function() 
	{ 
		var size = jQuery(this).val();
		bbfontstyle('[size=' + size + ']', '[/size]'); return false; 
	} );	
} );
	
// From http://www.massless.org/mozedit/

function mozWrap(txtarea, open, close)
{
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	var scrollTop = txtarea.scrollTop;

	if (selEnd == 1 || selEnd == 2) 
	{
		selEnd = selLength;
	}

	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);

	txtarea.value = s1 + open + s2 + close + s3;
	txtarea.selectionStart = selEnd + open.length + close.length;
	txtarea.selectionEnd = txtarea.selectionStart;
	txtarea.focus();
	txtarea.scrollTop = scrollTop;

	return;
}

// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl)
{
	if (textEl.createTextRange)
	{
		textEl.caretPos = document.selection.createRange().duplicate();
	}
}

// Insert BBCode in textarea. Code from
// http://www.phpbb.de/
function bbfontstyle(bbopen, bbclose) {
	theSelection = false;
   var txtarea = document.postform.message;
	txtarea.focus();
   if ((clientVer >= 4) && is_ie && is_win) {
    theSelection = document.selection.createRange().text;
		if (theSelection)
		{
			// Add tags around selection
			document.selection.createRange().text = bbopen + theSelection + bbclose;
			document.postform.message.focus();
			theSelection = '';
			if (document.postform.previewspeicher.value == "preview") {fbGetPreview(document.postform.message.value,<?php echo getKunenaItemid(); ?>);}
			return;
		}
  }

	else if (document.postform.message.selectionEnd && (document.postform.message.selectionEnd - document.postform.message.selectionStart > 0))
	{
		mozWrap(document.postform.message, bbopen, bbclose);
		document.postform.message.focus();
		theSelection = '';
			if (document.postform.previewspeicher.value == "preview") {fbGetPreview(document.postform.message.value,<?php echo getKunenaItemid(); ?>);}
		return;
	}
	//The new position for the cursor after adding the bbcode
	var caret_pos = getCaretPosition(txtarea).start;
	var new_pos = caret_pos + bbopen.length;		

	// Open tag
	insert_text(bbopen + bbclose);

	// Center the cursor when we don't have a selection
	// Gecko and proper browsers
	if (!isNaN(txtarea.selectionStart))
	{
		txtarea.selectionStart = new_pos;
		txtarea.selectionEnd = new_pos;
	}	
	// IE
	else if (document.selection)
	{
		var range = txtarea.createTextRange(); 
		range.move("character", new_pos); 
		range.select();
		storeCaret(txtarea);
	}

	txtarea.focus();
			if (document.postform.previewspeicher.value == "preview") {fbGetPreview(document.postform.message.value,<?php echo getKunenaItemid(); ?>);}
	return;
}

// Insert text at position. Code from
// http://www.phpbb.de/
function insert_text(text, spaces, popup)
{
	var txtarea;
	
	if (!popup) 
	{
		txtarea = document.postform.message;
	} 
	else 
	{
		txtarea = opener.document.postform.message;
	}
	if (spaces) 
	{
		text = ' ' + text + ' ';
	}
	
	if (!isNaN(txtarea.selectionStart))
	{
		var sel_start = txtarea.selectionStart;
		var sel_end = txtarea.selectionEnd;

		mozWrap(txtarea, text, '')
		txtarea.selectionStart = sel_start + text.length;
		txtarea.selectionEnd = sel_end + text.length;
	}
	else if (txtarea.createTextRange && txtarea.caretPos)
	{
		if (baseHeight != txtarea.caretPos.boundingHeight) 
		{
			txtarea.focus();
			storeCaret(txtarea);
		}

		var caret_pos = txtarea.caretPos;
		caret_pos.text = caret_pos.text.charAt(caret_pos.text.length - 1) == ' ' ? caret_pos.text + text + ' ' : caret_pos.text + text;
	}
	else
	{
		txtarea.value = txtarea.value + text;
	}
	if (!popup) 
	{
		txtarea.focus();
	}
}

// Caret Position object. Code from
// http://www.phpbb.de/
function caretPosition()
{
	var start = null;
	var end = null;
}

// Get the caret position in an textarea. Code from
// http://www.phpbb.de/
function getCaretPosition(txtarea)
{
	var caretPos = new caretPosition();
	
	// simple Gecko/Opera way
	if(txtarea.selectionStart || txtarea.selectionStart == 0)
	{
		caretPos.start = txtarea.selectionStart;
		caretPos.end = txtarea.selectionEnd;
	}
	// dirty and slow IE way
	else if(document.selection)
	{
	
		// get current selection
		var range = document.selection.createRange();

		// a new selection of the whole txtarea
		var range_all = document.body.createTextRange();
		range_all.moveToElementText(txtarea);
		
		// calculate selection start point by moving beginning of range_all to beginning of range
		var sel_start;
		for (sel_start = 0; range_all.compareEndPoints('StartToStart', range) < 0; sel_start++)
		{		
			range_all.moveStart('character', 1);
		}
	
		txtarea.sel_start = sel_start;
	
		// we ignore the end value for IE, this is already dirty enough and we don't need it
		caretPos.start = txtarea.sel_start;
		caretPos.end = txtarea.sel_start;			
	}

	return caretPos;
}
//#######################################################
//code used in My Profile (userprofile.php)
function textCounter(field, countfield, maxlimit) {
   if(field.value.length > maxlimit){
      field.value = field.value.substring(0, maxlimit);
   }
   else{
      countfield.value = maxlimit - field.value.length;
   }
}


function submitForm() {
 submitme=1;
 formname=document.postform.fb_authorname.value;
 if ((formname.length<1)) {
    alert("<?php @print( _POST_FORGOT_NAME_ALERT); ?>");
    submitme=0;
 }
<?php if ($fbConfig->askemail) { ?>
 formmail=document.postform.email.value;
 if ((formmail.length<1)) {
    alert("<?php @print( _POST_FORGOT_EMAIL_ALERT); ?>");
    submitme=0;
  }
  <?php } ?>
  formsubject=document.postform.subject.value;
  if ((formsubject.length<1)) {
    alert("<?php @print( _POST_FORGOT_SUBJECT_ALERT); ?>");
    submitme=0;
  }
  if (submitme>0) {
//  var message = document.postform.message.value;
//  message = message.replace(/</g,"&lt;");
//  message = message.replace(/>/g,"&gt;");
//  document.postform.message.value = message;
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
