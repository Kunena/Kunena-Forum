<script language="JavaScript" type="text/javascript">
<?php
global $fbConfig;
?>
<!--
// bbCode control by
// subBlue design
// www.subBlue.com
// adapted for Simpleboard by the Two Shoes Module Factory (www.tsmf.jigsnet.com)
// Startup variables
var imageTag = false;
var theSelection = false;
// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion);   // Get browser version
var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla') != -1) && (clientPC.indexOf('spoofer') == -1)
                 && (clientPC.indexOf('compatible') == -1)
                 && (clientPC.indexOf(
                         'opera') == -1) && (clientPC.indexOf('webtv') == -1) && (clientPC.indexOf('hotjava') == -1));
var is_moz = 0;
var is_win = ((clientPC.indexOf("win") != -1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac") != -1);
// Define the bbCode tags
bbcode = new Array();
bbtags
    = new Array('[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[quote]', '[/quote]', '[code]',
          '[/code]', '[ul]', '[/ul]', '[ol]', '[/ol]', '[img size=150]', '[/img]', '[url]', '[/url]', '[li]', '[/li]');
imageTag = false;

// Shows the help messages in the helpline window
function helpline(help)
{
    document.adminForm.helpbox.value = eval(help + "_help");
}

// Replacement for arrayname.length property
function getarraysize(thearray)
{
    for (i = 0; i < thearray.length; i++)
    {
        if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
            return i;
    }

    return thearray.length;
}

// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray, value)
{
    thearray[getarraysize(thearray)] = value;
}

// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray)
{
    thearraysize = getarraysize(thearray);
    retval = thearray[thearraysize - 1];
    delete thearray[thearraysize - 1];
    return retval;
}

function bbstyle(bbnumber)
{
    var txtarea = document.adminForm.message;
    txtarea.focus();
    donotinsert = false;
    theSelection = false;
    bblast = 0;

    if (bbnumber == -1)
    { // Close all open tags & default button names
        while (bbcode[0])
        {
            butnumber = arraypop(bbcode) - 1;
            txtarea.value += bbtags[butnumber + 1];
            buttext = eval('document.adminForm.addbbcode' + butnumber + '.value');
            eval('document.adminForm.addbbcode'
                    + butnumber + '.value ="' + buttext.substr(0, (buttext.length - 1)) + '"');
        }

        imageTag = false; // All tags are closed including image tags :D
        txtarea.focus();
        return;
    }

    if ((clientVer >= 4) && is_ie && is_win)
    {
        theSelection = document.selection.createRange().text; // Get text selection

        if (theSelection)
        {
            // Add tags around selection
            document.selection.createRange().text = bbtags[bbnumber] + theSelection + bbtags[bbnumber + 1];
            txtarea.focus();
            theSelection = '';
            return;
        }
    }
    else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
    {
        mozWrap(txtarea, bbtags[bbnumber], bbtags[bbnumber + 1]);
        return;
    }

    // Find last occurance of an open tag the same as the one just clicked
    for (i = 0; i < bbcode.length; i++)
    {
        if (bbcode[i] == bbnumber + 1)
        {
            bblast = i;
            donotinsert = true;
        }
    }

    if (donotinsert)
    { // Close all open tags up to the one just clicked & default button names
        while (bbcode[bblast])
        {
            butnumber = arraypop(bbcode) - 1;
            txtarea.value += bbtags[butnumber + 1];
            buttext = eval('document.adminForm.addbbcode' + butnumber + '.value');
            eval('document.adminForm.addbbcode'
                    + butnumber + '.value ="' + buttext.substr(0, (buttext.length - 1)) + '"');
            imageTag = false;
        }

        txtarea.focus();
        return;
    }
    else
    {                                                     // Open tags
        if (imageTag && (bbnumber != 14))
        {                                                 // Close image tag before adding another
            txtarea.value += bbtags[15];
            lastValue = arraypop(bbcode) - 1;             // Remove the close image tag from the list
            document.adminForm.addbbcode14.value = "Img"; // Return button back to normal state
            imageTag = false;
        }

        // Open tag
        txtarea.value += bbtags[bbnumber];

        if ((bbnumber == 14) && (imageTag == false))
            imageTag = 1; // Check to stop additional tags after an unclosed image tag

        arraypush(bbcode, bbnumber + 1);
        eval('document.adminForm.addbbcode' + bbnumber + '.value += "*"');
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

    var s1 = (txtarea.value).substring(0, selStart);
    var s2 = (txtarea.value).substring(selStart, selEnd)
    var s3 = (txtarea.value).substring(selEnd, selLength);
    txtarea.value = s1 + open + s2 + close + s3;
    return;
}

// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl)
{
    if (textEl.createTextRange)
        textEl.caretPos = document.selection.createRange().duplicate();
}

function bbfontstyle(bbopen, bbclose)
{
    var txtarea = document.adminForm.message;

    if ((clientVer >= 4) && is_ie && is_win)
    {
        theSelection = document.selection.createRange().text;

        if (!theSelection)
        {
            txtarea.value += bbopen + bbclose;
            txtarea.focus();
            return;
        }

        document.selection.createRange().text = bbopen + theSelection + bbclose;
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
        txtarea.value += bbopen + bbclose;
        txtarea.focus();
    }

    storeCaret(txtarea);
}

//#######################################################
//code used in My Profile (userprofile.php)
function textCounter(field, countfield, maxlimit)
{
    if (field.value.length > maxlimit)
    {
        field.value = field.value.substring(0, maxlimit);
    }
    else
    {
        countfield.value = maxlimit - field.value.length;
    }
}

//*********************************************************
// Insert emoticons
function emo($e)
{
    document.adminForm.message.value = document.adminForm.message.value + $e;
    document.adminForm.message.focus();
}

function submitForm()
{
    submitme = 1;
    formname = document.adminForm.fb_authorname.value;

    if ((formname.length < 1))
    {
        alert("You forgot to enter your name");
        submitme = 0;
    }

    formmail = document.adminForm.email.value;

    if ((formmail.length < 1))
    {
        alert("You forgot to enter your email");
        submitme = 0;
    }

    formsubject = document.adminForm.subject.value;

    if ((formsubject.length < 1))
    {
        alert("You forgot to enter a subject");
        submitme = 0;
    }

    if (submitme > 0)
    {
        //change the following line to true to submit form
        return true;
    }
    else
    {
        return false;
    }
}

function cancelForm()
{
    document.forms['adminForm'].action.value = "cancel";
    return true;
}

//**********************************************
// Helpline messages
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
iu_help
    = "<?php echo _IMAGE_DIMENSIONS.": ".$fbConfig->imagewidth."x".$fbConfig->imageheight." - ".$fbConfig->imagesize." KB";?>";
fu_help = "<?php echo _FILE_TYPES.": ".$fbConfig->filetypes." - ".$fbConfig->filesize." KB";?>";
submit_help = "<?php echo _HELP_SUBMIT;?>";
preview_help = "<?php echo _HELP_PREVIEW;?>";
cancel_help = "<?php echo _HELP_CANCEL;?>";
//**************************************************
// Open the preview window (with some post parsing)
function Preview(stylesheet, sbs) {
message=document.getElementById('message');
//these gotta be in both... I don't knwo why, but it works...
messageString = message.innertext;
messageString = message.value;
messageString = messageString.replace(/<(.*?)>/g,"&lt;$1&gt;");
messageString = messageString.replace(/</g,"&lt;");
messageString = messageString.replace(/\n/g,"<br>");
messageString = messageString.replace(/\[b\]/g,"<b>");
messageString = messageString.replace(/\[\/b\]/g,"</b>");
messageString = messageString.replace(/\[i\]/g,"<i>");
messageString = messageString.replace(/\[\/i\]/g,"</i>");
messageString = messageString.replace(/\[u\]/g,"<u>");
messageString = messageString.replace(/\[\/u\]/g,"</u>");
messageString = messageString.replace(/\[quote\]/g,'<div class="fb_review_quote">');
messageString = messageString.replace(/\[\/quote\]/g,'</div>');
messageString = messageString.replace(/\[code\]/g,'<table width="90%" cellspacing="1" cellpadding="3" border="0" align="center"><tr><td><b>Code:</b></span></td></tr><tr><td class="fb_code"><pre>');
messageString = messageString.replace(/\[\/code\]/g,'</pre></tr></table>');
messageString = messageString.replace(/\[code:1\]/g,'<table width="90%" cellspacing="1" cellpadding="3" border="0" align="center"><tr><td><b>Code:</b></span></td></tr><tr><td class="fb_code"><pre>');
messageString = messageString.replace(/\[\/code:1\]/g,'</pre></tr></table>');
messageString = messageString.replace(/\[ul\]/g,"<ul>");
messageString = messageString.replace(/\[\/ul\]/g,"</ul>");
messageString = messageString.replace(/\[ol\]/g,"<ol>");
messageString = messageString.replace(/\[\/ol\]/g,"</ol>");
messageString = messageString.replace(/\[li\]/g,"<li>");
messageString = messageString.replace(/\[\/li\]/g,"</li>");
messageString = messageString.replace(/B\)/g,'<img src="'+sbs+'/emoticons/sun.gif">');
messageString = messageString.replace(/;\)/g,'<img src="'+sbs+'/emoticons/wink.gif">');
messageString = messageString.replace(/:\)/g,'<img src="'+sbs+'/emoticons/happy.gif">');
messageString = messageString.replace(/:laugh:/g,'<img src="'+sbs+'/emoticons/laugh.gif">');
messageString = messageString.replace(/:ohmy:/g,'<img src="'+sbs+'/emoticons/ohmy.gif">');
messageString = messageString.replace(/:x/g,'<img src="'+sbs+'/emoticons/sick.gif">');
messageString = messageString.replace(/:mad:/g,'<img src="'+sbs+'/emoticons/angry.gif">');
messageString = messageString.replace(/:blink:/g,'<img src="'+sbs+'/emoticons/blink.gif">');
messageString = messageString.replace(/:P/g,'<img src="'+sbs+'/emoticons/tongue.gif">');
messageString = messageString.replace(/:\(/g,'<img src="'+sbs+'/emoticons/unhappy.gif">');
messageString = messageString.replace(/:unsure:/g,'<img src="'+sbs+'/emoticons/unsure.gif">');
messageString = messageString.replace(/\[img size=([1-4][0-9][0-9])\](.*?)\[\/img\]/g,"<img src=\"$2\" border\"0\" width=\"$1\">");
messageString = messageString.replace(/\[img\](.*?)\[\/img\]/g,"<img src=\"$1\" border\"0\">");
messageString = messageString.replace(/(\[url\])(.*?)(\[\/url\])/g,"<a href=$2 target=\"_blank\">$2</a>");
messageString = messageString.replace(/\[url=(.*?)\](.*?)\[\/url\]/g,"<a href=\"$1\" target=\"_blank\">$2</a>");
messageString = messageString.replace(/\[size=([1-7])\](.+?)\[\/size\]/g,"<font size=\"$1\">$2</font>");
messageString = messageString.replace(/\[color=(.*?)\](.*?)\[\/color\]/g,"<span style=\"color: $1\">$2</span>");
messageString = messageString.replace(/\[file name=(.*?) size=(.*?)\](.*?)\[\/file\]/g,"<div class=\"fb_file_attachment\"><span class=\"contentheading\">File Attachment:</span><br>File name: <a href=\"$3\">$1</a><br>File size:$2 bytes</div>");
//and finally open the window for displaying the lot
win = window.open(", ", 'Preview', 'width=640, height=480, toolbar = no, status = no, ,resizable,scrollbars');
win.document.write("<link media=\"all\" type=\"text/css\" href=\""+ stylesheet + "\" rel=\"stylesheet\">");
win.document.write("<link media=\"all\" type=\"text/css\" href=\""+ sbs + "/<?php echo $fbConfig->template; ?>/forum.css\" rel=\"stylesheet\">");
win.document.write("<DIV style=\"margin-left:10px; margin-top:10px;margin-right:10px;margin-bottom:10px\">");
win.document.write("<hr size=1 width=\"20%\" align=\"left\">");
win.document.write('' + messageString + '');
win.document.write("<hr size=1 width=\"20%\" align=\"left\">");
win.document.write("<a href=\"javascript:window.close()\"> Close this window </A> ");
win.document.write("</DIV>");
}
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