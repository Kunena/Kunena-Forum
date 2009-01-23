<?php
/**
* @version $Id: fb_bb.js.php 855 2008-07-16 15:35:10Z fxstein $
* Fireboard Component
* @package Fireboard
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
global $fbConfig;
?>
<!--
// bbCode control by
// subBlue design
// www.subBlue.com
// adapted for Joomlaboard by the Two Shoes Module Factory (www.tsmf.net)
// Startup variables
var imageTag = false;
var theSelection = false;
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
// Define the bbCode tags
bbcode = new Array();
bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[quote]','[/quote]','[code]','[/code]','[ul]','[/ul]','[ol]','[/ol]','[img size=150]','[/img]','[url]','[/url]','[li]','[/li]','[hide]','[/hide]');
imageTag = false;
// Shows the help messages in the helpline window
function helpline(help) {
   document.postform.helpbox.value = eval(help + "_help");
}
// Replacement for arrayname.length property
function getarraysize(thearray) {
   for (i = 0; i < thearray.length; i++) {
      if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
         return i;
      }
   return thearray.length;
}
// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
   thearray[ getarraysize(thearray) ] = value;
}
// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
   thearraysize = getarraysize(thearray);
   retval = thearray[thearraysize - 1];
   delete thearray[thearraysize - 1];
   return retval;
}
function bbstyle(bbnumber) {
   var txtarea = document.postform.message;
   txtarea.focus();
   donotinsert = false;
   theSelection = false;
   bblast = 0;
   if (bbnumber == -1) { // Close all open tags & default button names
      while (bbcode[0]) {
         butnumber = arraypop(bbcode) - 1;
         txtarea.value += bbtags[butnumber + 1];
         buttext = eval('document.postform.addbbcode' + butnumber + '.value');
         eval('document.postform.addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
      }
      imageTag = false; // All tags are closed including image tags :D
      txtarea.focus();
      return;
   }
   if ((clientVer >= 4) && is_ie && is_win)
   {
      theSelection = document.selection.createRange().text; // Get text selection
      if (theSelection) {
         // Add tags around selection
         document.selection.createRange().text = bbtags[bbnumber] + theSelection + bbtags[bbnumber+1];
         txtarea.focus();
         theSelection = '';
         return;
      }
      else {
        txtarea.focus();
        document.selection.createRange().text = bbtags[bbnumber] + bbtags[bbnumber + 1];
        return;
      }
   }
   else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
   {
      mozWrap(txtarea, bbtags[bbnumber], bbtags[bbnumber+1]);
      return;
   }
   else //if (txtarea.selectionEnd == txtarea.selectionStart) // don't know if we need it... it works even if commented out. ;)
   {
        txtarea.value = txtarea.value.substring(0, txtarea.selectionStart) + bbtags[bbnumber] + bbtags[bbnumber + 1] + txtarea.value.substring(txtarea.selectionEnd, txtarea.value.length);
        return;
   }
   // Find last occurance of an open tag the same as the one just clicked
   for (i = 0; i < bbcode.length; i++) {
      if (bbcode[i] == bbnumber+1) {
         bblast = i;
         donotinsert = true;
      }
   }
   if (donotinsert) {      // Close all open tags up to the one just clicked & default button names
      while (bbcode[bblast]) {
            butnumber = arraypop(bbcode) - 1;
            txtarea.value += bbtags[butnumber + 1];
            buttext = eval('document.postform.addbbcode' + butnumber + '.value');
            eval('document.postform.addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
            imageTag = false;
         }
         txtarea.focus();
         return;
   } else { // Open tags
      if (imageTag && (bbnumber != 14)) {    // Close image tag before adding another
         txtarea.value += bbtags[15];
         lastValue = arraypop(bbcode) - 1;   // Remove the close image tag from the list
         document.postform.addbbcode14.value = "Img";  // Return button back to normal state
         imageTag = false;
      }
      // Open tag
      txtarea.value += bbtags[bbnumber];
      if ((bbnumber == 14) && (imageTag == false)) imageTag = 1; // Check to stop additional tags after an unclosed image tag
      arraypush(bbcode,bbnumber+1);
      eval('document.postform.addbbcode'+bbnumber+'.value += "*"');
      txtarea.focus();
      return;
   }
   storeCaret(txtarea);
}
// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
{
   var selLength = txtarea.textLength;
   var selStart = txtarea.selectionStart;
   var selEnd = txtarea.selectionEnd;
   if (selEnd == 1 || selEnd == 2)
      selEnd = selLength;
   var s1 = (txtarea.value).substring(0,selStart);
   var s2 = (txtarea.value).substring(selStart, selEnd)
   var s3 = (txtarea.value).substring(selEnd, selLength);
   txtarea.value = s1 + open + s2 + close + s3;
   return;
}
// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
   if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}
function bbfontstyle(bbopen, bbclose) {
   var txtarea = document.postform.message;
   if ((clientVer >= 4) && is_ie && is_win) {
      theSelection = document.selection.createRange().text;
      txtarea.focus();
      if (!theSelection) {
         document.selection.createRange().text = bbopen + bbclose;
      }
      else {
         document.selection.createRange().text = bbopen + theSelection + bbclose;
      }
      txtarea.focus();
      return;
   }
   else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
   {
      mozWrap(txtarea, bbopen, bbclose);
      return;
   }
   else
   {
      txtarea.value = txtarea.value.substring(0, txtarea.selectionStart) + bbopen + bbclose + txtarea.value.substring(txtarea.selectionEnd, txtarea.value.length);
      txtarea.focus();
   }
   storeCaret(txtarea);
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
//*********************************************************
// Insert emoticons
function emo($e)
{
    var textfield = document.postform.message;
    // Support for IE
    if (document.selection)
    {
        textfield.focus();
        var sel = document.selection.createRange();
        sel.text = $e;
    }
    // Support for Mozilla
    else if (textfield.selectionStart || textfield.selectionStart == '0')
    {
        var start = textfield.selectionStart;
        var end = textfield.selectionEnd;
        textfield.value = textfield.value.substring(0, start) + $e + textfield.value.substring(end, textfield.value.length);
    }
    else
    {
        textfield.value = textfield.value + $e;
    }
    textfield.focus();
}
function submitForm() {
 submitme=1;
 formname=document.postform.fb_authorname.value;
 if ((formname.length<1)) {
    alert("<?php echo _POST_FORGOT_NAME_ALERT; ?>");
    submitme=0;
 }
 formmail=document.postform.email.value;
 if ((formmail.length<1)) {
    alert("<?php echo _POST_FORGOT_EMAIL_ALERT; ?>");
    submitme=0;
  }
  formsubject=document.postform.subject.value;
  if ((formsubject.length<1)) {
    alert("<?php echo _POST_FORGOT_SUBJECT_ALERT ?>");
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
//**********************************************
// Helpline messages

h_help = "<?php echo _BBCODE_HIDE;?>";
b_help = "<?php echo _BBCODE_BOLD;?>";
i_help = "<?php echo _BBCODE_ITALIC;?>";
u_help = "<?php echo _BBCODE_UNDERL;?>";
q_help = "<?php echo _BBCODE_QUOTE;?>";
c_help = "<?php echo _BBCODE_CODE;?>";
k_help = "<?php echo _BBCODE_ULIST;?>";
o_help = "<?php echo _BBCODE_OLIST;?>";
p_help = "<?php echo _BBCODE_IMAGE;?>";
w_help = "<?php echo _BBCODE_LINK;?>";
a_help = "<?php echo _BBCODE_CLOSE;?>";
s_help = "<?php echo _BBCODE_COLOR;?>";
f_help = "<?php echo _BBCODE_SIZE;?>";
l_help = "<?php echo _BBCODE_LITEM;?>";
iu_help = "<?php echo _IMAGE_DIMENSIONS.": ".$fbConfig->imagewidth."x".$fbConfig->imageheight." - ".$fbConfig->imagesize." KB";?>";
fu_help = "<?php echo _FILE_TYPES.": ".$fbConfig->filetypes." - ".$fbConfig->filesize." KB";?>";
ip_help = "<?php echo _BBCODE_IMGPH;?>";
fp_help = "<?php echo _BBCODE_FILEPH;?>";
submit_help = "<?php echo _HELP_SUBMIT;?>";
preview_help = "<?php echo _HELP_PREVIEW;?>";
cancel_help = "<?php echo _HELP_CANCEL;?>";
//**************************************************
/**
* Pops up a new window in the middle of the screen
*/
function popupWindow(mypage, myname, w, h, scroll) {
   var winl = (screen.width - w) / 2;
   var wint = (screen.height - h) / 2;
   winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable'
   win = window.open(mypage, myname, winprops);
   if (win.opener == null) win.opener = self;
   if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
}
function popUp(URL) {
    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=no,location=0,statusbar=0,menubar=0,resizable=0,width=300,height=250,left = 262,top = 184');");
}
//-->
</script>