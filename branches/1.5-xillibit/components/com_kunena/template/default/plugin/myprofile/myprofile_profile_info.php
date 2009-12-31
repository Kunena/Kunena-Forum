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
defined( '_JEXEC' ) or die('Restricted access');
        // used for spoof hardening
        $validate = JUtility::getToken();
$kunena_config =& CKunenaConfig::getInstance();
?>
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
<form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=myprofile&amp;do=saveprofileinfo'); ?>" method = "post" name = "postform">
	<input type = "hidden" name = "do" value = "saveprofileinfo"/>
	<table class = "fb_blocktable" id = "fb_forumprofile_sub" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
		<thead>
			<tr>
				<th colspan = "2">
					<div class = "fb_title_cover">
						<span class = "fb_title"><?php echo _GEN_SIGNATURE; ?></span>
					</div>
				</th>
			</tr>
		</thead>

		<tbody class = "fb_myprofile_general">

		<tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_PERSONALTEXT; ?> </b></td>

                                <td><input name="personalText" size="50" maxlength="50" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->personalText))?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%">
                                    <b><?php echo _KUNENA_MYPROFILE_BIRTHDATE; ?></b>

                                    <div class="smalltext"><?php echo _KUNENA_MYPROFILE_BIRTHDATE_DESC; ?></div>
                                </td>
                                <td class="smalltext">
                                    <input name="bday1" size="4" maxlength="4" value="<?php echo $ulists["year"];?>" type="text"/> -
                                    <input name="bday2" size="2" maxlength="2" value="<?php echo $ulists["month"];?>" type="text"/> -
                                    <input name="bday3" size="2" maxlength="2" value="<?php echo $ulists["day"];?>" type="text"/>
                                </td>
                            </tr><tr>

                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_LOCATION; ?> </b></td>
                                <td><input name="location" size="50" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->location));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_GENDER; ?></b></td>
                                <td>
                                    <?php echo $ulists["gender"];?>
                                </td>
                            </tr><tr>
                                <td colspan="2"><hr class="" size="1" width="100%" /></td>
                            </tr>
                            <tr>

                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_ICQ; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_ICQ_DESC; ?></div></td>
                                <td><input name="ICQ" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->ICQ));?>" type="text"/></td>
                            </tr><tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_AIM; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_AIM_DESC; ?></div></td>
                                <td><input name="AIM" maxlength="16" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->AIM));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_MSN; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_MSN_DESC; ?></div></td>

                                <td><input name="MSN" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->MSN));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_YIM; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_YIM_DESC; ?></div></td>
                                <td><input name="YIM" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->YIM));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_SKYPE; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_SKYPE_DESC; ?></div></td>
                                <td><input name="SKYPE" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->SKYPE));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_GTALK; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_GTALK_DESC; ?></div></td>
                                <td><input name="GTALK" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->GTALK));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_WEBSITE_NAME; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_WEBSITE_NAME_DESC; ?></div></td>
                                <td><input name="websitename" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->websitename));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_WEBSITE_URL; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_WEBSITE_URL_DESC; ?></div></td>
                                <td><input name="websiteurl" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->websiteurl));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr class="hrcolor" size="1" width="100%" /></td>
                            </tr>




			<tr >
				<td ><strong><?php echo _GEN_SIGNATURE; ?></strong>:

					<br/>

					<i><?php echo $kunena_config->maxsig; ?> <?php echo _CHARS; ?></i>

					<br/>

					<input readonly="readonly" type = "text" name = "counter" size = "3" maxlength = "3" value = ""/>
					<br/>
<?php echo _HTML_YES; ?>
				</td>

				<td ><textarea style = "width: <?php echo $kunena_config->rtewidth-150?>px; height: 60px;" class = "inputbox"
							onmouseover = "textCounter(this.form.message,this.form.counter,<?php echo $kunena_config->maxsig;?>);" onclick = "textCounter(this.form.message,this.form.counter,<?php echo $kunena_config->maxsig;?>);"
							onkeydown = "textCounter(this.form.message,this.form.counter,<?php echo $kunena_config->maxsig;?>);" onkeyup = "textCounter(this.form.message,this.form.counter,<?php echo $kunena_config->maxsig;?>);"
							rows="6" cols="60" name = "message"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->signature), ENT_QUOTES); ?></textarea>

					<br/>

					<input name="speicher" type="hidden" size="30" maxlength="100">
					<img class = "fb-bbcode" title = "Bold" accesskey = "b" name = "addbbcode0" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>text_bold.png" alt="B" onclick = "bbfontstyle('[b]', '[/b]');" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_BOLD);?>')" />
					<img class = "fb-bbcode" accesskey = "i" name = "addbbcode2" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>text_italic.png" alt="I" onclick = "bbfontstyle('[i]', '[/i]');" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_ITALIC);?>')" />
					<img class = "fb-bbcode" accesskey = "u" name = "addbbcode4" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>text_underline.png" alt="U" onclick = "bbfontstyle('[u]', '[/u]');" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_UNDERL);?>')" />
					<img class = "fb-bbcode" name = "addbbcode62" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>text_smallcaps.png" alt="<?php @print(_SMILE_SIZE); ?>" onclick = "bbfontstyle('[size=' + document.postform.addbbcode22.options[document.postform.addbbcode22.selectedIndex].value + ']', '[/size]');" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_FONTSIZE);?>')" />
					<select id = "fb-bbcode_size" class = "<?php echo KUNENA_BOARD_CLASS;?>slcbox" name = "addbbcode22" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_FONTSIZESELECTION);?>')">
						<option value = "1"><?php @print(_SIZE_VSMALL); ?></option>
						<option value = "2"><?php @print(_SIZE_SMALL); ?></option>
						<option value = "3" selected = "selected"><?php @print(_SIZE_NORMAL); ?></option>
						<option value = "4"><?php @print(_SIZE_BIG); ?></option>
						<option value = "5"><?php @print(_SIZE_VBIG); ?></option>
					</select>
					<img id="ueberschrift" class = "fb-bbcode" name = "addbbcode20" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>color_swatch.png" alt="<?php @print(_SMILE_COLOUR); ?>" onclick = "javascript:change_palette();" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_COLOR);?>')" />
					<img class = "fb-bbcode" accesskey = "p" name = "addbbcode14" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>picture_link.png" alt="Img" onclick = "javascript:dE('image');" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_IMAGELINK);?>')" />
					<img class = "fb-bbcode" accesskey = "w" name = "addbbcode16" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>link_url.png" alt="URL" onclick = "javascript:dE('link');" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_LINK);?>')" />	
					<br />
					<div id="fb-color_palette" style="margin-top: 14px; border: 1px; display: none;">
						<script type="text/javascript">
							function change_palette() {dE('fb-color_palette');}
							colorPalette('h', '4%', '15px');
						</script>
					</div>		
								
					<div id="link" style="margin-top: 14px; border: 1px; display: none;">
						<?php @print(_KUNENA_EDITOR_LINK_URL); ?><input name="url" type="text" size="20" maxlength="100" value="http://" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_LINKURL);?>')"> 
						<?php @print(_KUNENA_EDITOR_LINK_TEXT); ?><input name="text2" type="text" size="20" maxlength="100" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_LINKTEXT);?>')"> 
						<input type="button" name="Link" accesskey = "w" value="<?php @print(_KUNENA_EDITOR_LINK_INSERT); ?>""
							onclick="bbfontstyle('[url=' + this.form.url.value + ']'+ this.form.text2.value,'[/url]')" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_LINKAPPLY);?>')">
					</div>

					<div id="image" style="margin-top: 14px; border: 1px; display: none;">
						<?php @print(_KUNENA_EDITOR_IMAGE_SIZE); ?><input name="size" type="text" size="3" maxlength="10" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_IMAGELINKSIZE);?>')"> 
						<?php @print(_KUNENA_EDITOR_IMAGE_URL); ?><input name="url2" type="text" size="20" maxlength="100" value="http://" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_IMAGELINKURL);?>')"> 
						<input type="button" name="Link" accesskey = "p" value="<?php @print(_KUNENA_EDITOR_IMAGE_INSERT); ?>" onclick="check_image()" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY);?>')">
						<script type="text/javascript">
							function check_image() {
								if (document.postform.size.value == "") {
									bbfontstyle('[img]'+ document.postform.url2.value,'[/img]');
								} else {
									bbfontstyle('[img size=' + document.postform.size.value + ']'+ document.postform.url2.value,'[/img]');
								}
							}
						</script>
					</div> 

					<br />
					<div class="<?php echo KUNENA_BOARD_CLASS;?>posthint">
						<input class = "fb-bbcode" type = "text" name = "helpbox" size = "45" class = "<?php echo KUNENA_BOARD_CLASS;?>inputbox" maxlength = "100" value = "<?php @print(_KUNENA_EDITOR_HELPLINE_HINT);?>" style="width: 99%;" />
					</div>

					<br />

					<input type = "checkbox" value = "1" name = "deleteSig"/>
					<i> <?php echo _USER_DELETE; ?></i>
				</td>
			</tr>

			<tr><td colspan = "2" align="center"><input type = "submit" class = "button" value = "<?php echo _GEN_SUBMIT;?>"/></td>
			</tr>
		</tbody>
	</table>
            <input type="hidden" name="<?php echo $validate; ?>" value="1" />
            <input type="hidden" name="id" value="<?php echo $kunena_my->id;?>" />
</form>
</div>
</div>
</div>
</div>
</div>