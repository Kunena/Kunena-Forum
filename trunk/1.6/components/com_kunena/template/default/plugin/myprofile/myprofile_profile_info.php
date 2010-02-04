<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
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
defined( '_JEXEC' ) or die();

        // used for spoof hardening
        $validate = JUtility::getToken();
$kunena_config =& CKunenaConfig::getInstance();
?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=myprofile&amp;do=saveprofileinfo'); ?>" method = "post" name = "postform">
	<input type = "hidden" name = "do" value = "saveprofileinfo"/>
	<table class = "kblocktable" id = "kforumprofile_sub" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
		<thead>
			<tr>
				<th colspan = "2">
					<div class = "ktitle_cover">
						<span class = "ktitle"><?php echo JText::_(COM_KUNENA_GEN_SIGNATURE); ?></span>
					</div>
				</th>
			</tr>
		</thead>

		<tbody class = "kmyprofile_general">

		<tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_PERSONALTEXT); ?> </b></td>

                                <td><input name="personalText" size="50" maxlength="<?php $kunena_config->maxpersotext; ?>" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->personalText))?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%">
                                    <b><?php echo JText::_(COM_KUNENA_MYPROFILE_BIRTHDATE); ?></b>

                                    <div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_BIRTHDATE_DESC); ?></div>
                                </td>
                                <td class="smalltext">
                                    <input name="bday1" size="4" maxlength="4" value="<?php echo $this->ulists["year"];?>" type="text"/> -
                                    <input name="bday2" size="2" maxlength="2" value="<?php echo $this->ulists["month"];?>" type="text"/> -
                                    <input name="bday3" size="2" maxlength="2" value="<?php echo $this->ulists["day"];?>" type="text"/>
                                </td>
                            </tr><tr>

                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_LOCATION); ?> </b></td>
                                <td><input name="location" size="50" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->location));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_GENDER); ?></b></td>
                                <td>
                                    <?php echo $this->ulists["gender"];?>
                                </td>
                            </tr><tr>
                                <td colspan="2"><hr class="" size="1" width="100%" /></td>
                            </tr>
                            <tr>

                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_ICQ); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_ICQ_DESC); ?></div></td>
                                <td><input name="ICQ" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->ICQ));?>" type="text"/></td>
                            </tr><tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_AIM); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_AIM_DESC); ?></div></td>
                                <td><input name="AIM" maxlength="16" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->AIM));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_MSN); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_MSN_DESC); ?></div></td>

                                <td><input name="MSN" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->MSN));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_YIM); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_YIM_DESC); ?></div></td>
                                <td><input name="YIM" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->YIM));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_SKYPE); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_SKYPE_DESC); ?></div></td>
                                <td><input name="SKYPE" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->SKYPE));?>" type="text"/></td>
                            </tr>
                            <tr>
                            <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_TWITTER); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_TWITTER_DESC); ?></div></td>
                                <td><input name="TWITTER" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->TWITTER));?>" type="text"/></td>
                            </tr>
                            <tr>
                            <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_FACEBOOK); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_FACEBOOK_DESC); ?></div></td>
                                <td><input name="FACEBOOK" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->FACEBOOK));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_GTALK); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_GTALK_DESC); ?></div></td>
                                <td><input name="GTALK" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->GTALK));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_MYSPACE); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_MYSPACE_DESC); ?></div></td>
                                <td><input name="MYSPACE" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->MYSPACE));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_LINKEDIN); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_LINKEDIN_DESC); ?></div></td>
                                <td><input name="LINKEDIN" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->LINKEDIN));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_DELICIOUS); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_DELICIOUS_DESC); ?></div></td>
                                <td><input name="DELICIOUS" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->DELICIOUS));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_FRIENDFEED); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_FRIENDFEED_DESC); ?></div></td>
                                <td><input name="FRIENDFEED" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->FRIENDFEED));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_DIGG); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_DIGG_DESC); ?></div></td>
                                <td><input name="DIGG" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->DIGG));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_BLOGSPOT); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_BLOGSPOT_DESC); ?></div></td>
                                <td><input name="BLOGSPOT" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->BLOGSPOT));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_FLICKR); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_FLICKR_DESC); ?></div></td>
                                <td><input name="FLICKR" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->FLICKR));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_BEBO); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_BEBO_DESC); ?></div></td>
                                <td><input name="BEBO" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->BEBO));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_WEBSITE_NAME); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_WEBSITE_NAME_DESC); ?></div></td>
                                <td><input name="websitename" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->websitename));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo JText::_(COM_KUNENA_MYPROFILE_WEBSITE_URL); ?> </b><div class="smalltext"><?php echo JText::_(COM_KUNENA_MYPROFILE_WEBSITE_URL_DESC); ?></div></td>
                                <td><input name="websiteurl" maxlength="32" size="24" value="<?php echo kunena_htmlspecialchars(stripslashes($userinfo->websiteurl));?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr class="hrcolor" size="1" width="100%" /></td>
                            </tr>




			<tr >
				<td ><strong><?php echo JText::_(COM_KUNENA_GEN_SIGNATURE); ?></strong>:

					<br/>

					<em><?php echo $kunena_config->maxsig; ?> <?php echo JText::_(COM_KUNENA_CHARS); ?></em>

					<br/>

					<input readonly="readonly" type = "text" name = "counter" size = "3" maxlength = "3" value = ""/>
					<br/>
<?php echo JText::_(COM_KUNENA_HTML_YES); ?>
				</td>

				<td ><textarea style = "width: <?php echo $kunena_config->rtewidth-150?>px; height: 60px;" class = "inputbox"
							onmouseover = "textCounter(this.form.message,this.form.counter,<?php echo $kunena_config->maxsig;?>);" onclick = "textCounter(this.form.message,this.form.counter,<?php echo $kunena_config->maxsig;?>);"
							onkeydown = "textCounter(this.form.message,this.form.counter,<?php echo $kunena_config->maxsig;?>);" onkeyup = "textCounter(this.form.message,this.form.counter,<?php echo $kunena_config->maxsig;?>);"
							rows="6" cols="60" name = "message"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->signature), ENT_QUOTES); ?></textarea>

					<br/>

					<input name="speicher" type="hidden" size="30" maxlength="100">
					<img class = "k-bbcode" title = "Bold" name = "addbbcode0" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>text_bold.png" alt="B" onclick = "bbfontstyle('[b]', '[/b]');" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_BOLD)));?>')" />
					<img class = "k-bbcode" name = "addbbcode2" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>text_italic.png" alt="I" onclick = "bbfontstyle('[i]', '[/i]');" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_ITALIC)));?>')" />
					<img class = "k-bbcode" name = "addbbcode4" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>text_underline.png" alt="U" onclick = "bbfontstyle('[u]', '[/u]');" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_UNDERL)));?>')" />
					<img class = "k-bbcode" name = "addbbcode62" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>text_smallcaps.png" alt="<?php @print(JText::_(COM_KUNENA_SMILE_SIZE)); ?>" onclick = "bbfontstyle('[size=' + document.postform.addbbcode22.options[document.postform.addbbcode22.selectedIndex].value + ']', '[/size]');" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_FONTSIZE)));?>')" />
					<select id = "k-bbcode_size" class = "kslcbox" name = "addbbcode22" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(_KUNENA_EDITOR_HELPLINE_FONTSIZESELECTION));?>')">
						<option value = "1"><?php @print(_SIZE_VSMALL); ?></option>
						<option value = "2"><?php @print(_SIZE_SMALL); ?></option>
						<option value = "3" selected = "selected"><?php @print(_SIZE_NORMAL); ?></option>
						<option value = "4"><?php @print(_SIZE_BIG); ?></option>
						<option value = "5"><?php @print(_SIZE_VBIG); ?></option>
					</select>
					<img id="ueberschrift" class = "k-bbcode" name = "addbbcode20" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>color_swatch.png" alt="<?php @print(JText::_(COM_KUNENA_SMILE_COLOUR)); ?>" onclick = "javascript:change_palette();" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_COLOR)));?>')" />
					<img class = "k-bbcode" name = "addbbcode14" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>picture_link.png" alt="Img" onclick = "javascript:dE('image');" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_IMAGELINK)));?>')" />
					<img class = "k-bbcode" name = "addbbcode16" src="<?php echo KUNENA_LIVEUPLOADEDPATH.'/editor/'; ?>link_url.png" alt="URL" onclick = "javascript:dE('link');" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_LINK)));?>')" />
					<br />
					<div id="k-color_palette" style="margin-top: 14px; border: 1px; display: none;">
						<script type="text/javascript">
							function change_palette() {dE('k-color_palette');}
							colorPalette('h', '4%', '15px');
						</script>
					</div>

					<div id="link" style="margin-top: 14px; border: 1px; display: none;">
						<?php @print(JText::_(COM_KUNENA_EDITOR_LINK_URL)); ?><input name="url" type="text" size="20" maxlength="100" value="http://" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_LINKURL)));?>')">
						<?php @print(JText::_(COM_KUNENA_EDITOR_LINK_TEXT)); ?><input name="text2" type="text" size="20" maxlength="100" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_LINKTEXT)));?>')">
						<input type="button" name="Link" accesskey = "w" value="<?php @print(JText::_(COM_KUNENA_EDITOR_LINK_INSERT)); ?>""
							onclick="bbfontstyle('[url=' + this.form.url.value + ']'+ this.form.text2.value,'[/url]')" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_LINKAPPLY)));?>')">
					</div>

					<div id="image" style="margin-top: 14px; border: 1px; display: none;">
						<?php @print(_KUNENA_EDITOR_IMAGE_SIZE); ?><input name="size" type="text" size="3" maxlength="10" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_IMAGELINKSIZE)));?>')">
						<?php @print(_KUNENA_EDITOR_IMAGE_URL); ?><input name="url2" type="text" size="20" maxlength="100" value="http://" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_IMAGELINKURL)));?>')">
						<input type="button" name="Link" accesskey = "p" value="<?php @print(_KUNENA_EDITOR_IMAGE_INSERT); ?>" onclick="check_image()" onmouseover = "javascript:kunenaShowHelp('<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_IMAGELINKAPPLY)));?>')">
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
					<div class="kposthint">
						<input class = "k-bbcode" type = "text" name = "helpbox" size = "45" class = "kinputbox" maxlength = "100" value = "<?php @print(addslashes(JText::_(COM_KUNENA_EDITOR_HELPLINE_HINT)));?>" style="width: 99%;" />
					</div>

					<br />

					<input type = "checkbox" value = "1" name = "deleteSig"/>
					<em> <?php echo JText::_(COM_KUNENA_USER_DELETE); ?></em>
				</td>
			</tr>

			<tr><td colspan = "2" align="center"><input type = "submit" class = "button" value = "<?php echo JText::_(COM_KUNENA_GEN_SUBMIT);?>"/></td>
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