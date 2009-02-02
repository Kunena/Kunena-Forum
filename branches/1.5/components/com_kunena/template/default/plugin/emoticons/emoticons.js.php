<script language = "JavaScript" type = "text/javascript">


// Replaces the currently selected text with the passed text.
function replaceText(text, textarea)
{

	// Attempt to create a text range (IE).
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange)
	{

		var caretPos = textarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		caretPos.select();
	}

	// Mozilla text range replace.
	else if (typeof(textarea.selectionStart) != "undefined")
	{
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text + end;
		if (textarea.setSelectionRange)
		{
			textarea.focus();
			textarea.setSelectionRange(begin.length + text.length, begin.length + text.length);
		}
		textarea.scrollTop = scrollPos;
	}
	
	// Just put it on the end.
	else
	{
		textarea.value += text;
		textarea.focus(textarea.value.length - 1);
	}
}


var smilies_for_window= [<?php  $text = "";
                             foreach ($rowset as $data){
								$text .= "['".addslashes($data['code'])."', '".$data['location']."', '".addslashes($data['code'])."'],\n";
                             }
                             echo (substr_replace($text, "", -2));
                              ?>];

var smileyPopupWindow;
function moreForumSmileys()
{
	var i;

	if (smileyPopupWindow)
	smileyPopupWindow.close();

	smileyPopupWindow = window.open("", "add_smileys", "toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,width=480,height=640,resizable=yes");
	smileyPopupWindow.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n<html>');
	smileyPopupWindow.document.write('\n\t<head>\n\t\t<title><?php echo _FB_EMOTICONS_ADDITIONAL_EMOTICONS; ?></title>\n\t\t <style>\n\t\t.tborder\n\t\t\t{\n\t\t\t\t\tborder-top: solid  1px #454545;\n\t\t\t\t\tborder-left: solid 1px #454545;\n\t\t\t\t\tborder-right: solid  1px black;\n\t\t\t\t\tborder-bottom: solid 1px black;\n\t\t\t\t\tbackground-color: #ffffff;\n\t\t\t\t\tpadding: 0px;\n\t\t\t\t\tborder-collapse:collapse;\n\t\t\t}\n\t\tbody\n\t\t\t{\n\t\t\t\t\tbackground-color: #cccccc;\n\t\t\t\t\tmargin: 0px;\n\t\t\t\t\tpadding: 5px 20px 5px 20px;\n\t\t\t}\n\t\tbody, td\n\t\t\t{\n\t\t\t\t\tcolor: #ffffff;\n\t\t\t\t\tfont-size: small;\n\t\t\t\t\tfont-family: tahoma, helvetica, serif;\n\t\t\t}\n\t\t.titlebg, tr.titlebg td\n\t\t\t{\n\t\t\t\t\tbackground-color: #333333;\n\t\t\t\t\tfont-weight: bold;\n\t\t\t\t\tcolor: #eeeeee;\n\t\t\t\t\tfont-style: normal;\n\t\t\t}\n\t\t.windowbg\n\t\t\t{\n\t\t\t\t\tfont-family: tahoma, helvetica, serif;\n\t\t\t\t\tcolor: #ffffff;\n\t\t\t\t\tbackground-color: #eeeeee;\n\t\t\t\t\tborder-top: solid  1px #606060;\n\t\t\t\t\tborder-left: solid 1px #606060;\n\t\t\t\t\tborder-right: solid  1px black;\n\t\t\t\t\tborder-bottom: solid 1px black;\n\t\t\t}\n\t\t</style> \n\t</head>');
	smileyPopupWindow.document.write('\n\t<body style="margin: 1ex;">\n\t\t<table width="100%" cellpadding="5" cellspacing="0" border="0" class="tborder">\n\t\t\t<tr class="titlebg"><td align="left"><?php echo _FB_EMOTICONS_PICK_A_SMILEY; ?></td></tr>\n\t\t\t<tr class="windowbg"><td align="left">');

	for (i = 0; i < smilies_for_window.length; i++)
	{
		smilies_for_window[i][2] = smilies_for_window[i][2].replace(/"/g, '&quot;');
		smileyPopupWindow.document.write('<a href="javascript:void(0);" onclick="window.opener.replaceText(&quot; ' + smilies_for_window[i][0] + '&quot;, window.opener.document.forms.postform.message); window.focus(); return false;"><img src="<?php  echo(JB_URLEMOTIONSPATH);?>' + smilies_for_window[i][1] + '" alt="' + smilies_for_window[i][2] + '" title="' + smilies_for_window[i][2] + '" style="padding: 4px;" border="0" /></a> ');
	}
	smileyPopupWindow.document.write("<br />");


	smileyPopupWindow.document.write('</td></tr>\n\t\t\t<tr><td align="center" class="windowbg"><a href="javascript:window.close();\"><?php echo _FB_EMOTICONS_CLOSE_WINDOW; ?></a></td></tr>\n\t\t</table>\n\t</body>\n</html>');
	smileyPopupWindow.document.close();
}
-->
</script>