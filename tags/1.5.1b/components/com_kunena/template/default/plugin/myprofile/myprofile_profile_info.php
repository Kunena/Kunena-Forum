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
$fbConfig =& CKunenaConfig::getInstance();
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
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

                                <td><input name="personalText" size="50" maxlength="50" value="<?php echo html_entity_decode_utf8(stripslashes($userinfo->personalText))?>" type="text"/></td>
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
                                <td><input name="location" size="50" value="<?php echo html_entity_decode_utf8(stripslashes($userinfo->location));?>" type="text"/></td>
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
                                <td><input name="ICQ" size="24" value="<?php echo $userinfo->ICQ;?>" type="text"/></td>
                            </tr><tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_AIM; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_AIM_DESC; ?></div></td>
                                <td><input name="AIM" maxlength="16" size="24" value="<?php echo $userinfo->AIM;?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_MSN; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_MSN_DESC; ?></div></td>

                                <td><input name="MSN" size="24" value="<?php echo $userinfo->MSN;?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_YIM; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_YIM_DESC; ?></div></td>
                                <td><input name="YIM" maxlength="32" size="24" value="<?php echo $userinfo->YIM;?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_SKYPE; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_SKYPE_DESC; ?></div></td>
                                <td><input name="SKYPE" maxlength="32" size="24" value="<?php echo $userinfo->SKYPE;?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_GTALK; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_GTALK_DESC; ?></div></td>
                                <td><input name="GTALK" maxlength="32" size="24" value="<?php echo $userinfo->GTALK;?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_WEBSITE_NAME; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_WEBSITE_NAME_DESC; ?></div></td>
                                <td><input name="websitename" maxlength="32" size="24" value="<?php echo $userinfo->websitename;?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td width="40%"><b><?php echo _KUNENA_MYPROFILE_WEBSITE_URL; ?> </b><div class="smalltext"><?php echo _KUNENA_MYPROFILE_WEBSITE_URL_DESC; ?></div></td>
                                <td><input name="websiteurl" maxlength="32" size="24" value="<?php echo $userinfo->websiteurl;?>" type="text"/></td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr class="hrcolor" size="1" width="100%" /></td>
                            </tr>




			<tr >
				<td ><strong><?php echo _GEN_SIGNATURE; ?></strong>:

					<br/>

					<i><?php echo $fbConfig->maxsig; ?> <?php echo _CHARS; ?></i>

					<br/>

					<input readonly="readonly" type = "text" name = "counter" size = "3" maxlength = "3" value = ""/>
					<br/>
<?php echo _HTML_YES; ?>
				</td>

				<td ><textarea style = "width: <?php echo $fbConfig->rtewidth-150?>px; height: 60px;" class = "inputbox"
							onmouseover = "textCounter(this.form.message,this.form.counter,<?php echo $fbConfig->maxsig;?>);" onclick = "textCounter(this.form.message,this.form.counter,<?php echo $fbConfig->maxsig;?>);"
							onkeydown = "textCounter(this.form.message,this.form.counter,<?php echo $fbConfig->maxsig;?>);" onkeyup = "textCounter(this.form.message,this.form.counter,<?php echo $fbConfig->maxsig;?>);"
							rows="6" cols="60" name = "message"><?php echo htmlspecialchars(stripslashes($userinfo->signature), ENT_QUOTES); ?></textarea>

					<br/>

					<input type = "button" class = "button" accesskey = "b" name = "addbbcode0" value = " B " style = "font-weight:bold; width: 30px" onclick = "bbstyle(0)" onmouseover = "helpline('b')"/>

					<input type = "button" class = "button" accesskey = "i" name = "addbbcode2" value = " i " style = "font-style:italic; width: 30px" onclick = "bbstyle(2)" onmouseover = "helpline('i')"/>

					<input type = "button" class = "button" accesskey = "u" name = "addbbcode4" value = " u " style = "text-decoration: underline; width: 30px" onclick = "bbstyle(4)" onmouseover = "helpline('u')"/>

					<input type = "button" class = "button" accesskey = "p" name = "addbbcode14" value = "Img" style = "width: 40px" onclick = "bbstyle(14)" onmouseover = "helpline('p')"/>

					<input type = "button" class = "button" accesskey = "w" name = "addbbcode16" value = "URL" style = "text-decoration: underline; width: 40px" onclick = "bbstyle(16)" onmouseover = "helpline('w')"/>

					<br/> <?php echo _SMILE_COLOUR; ?>:

					<select name = "addbbcode20" onchange = "bbfontstyle('[color=' + this.form.addbbcode20.options[this.form.addbbcode20.selectedIndex].value + ']', '[/color]');this.selectedIndex=0;" onmouseover = "helpline('s')">
						<option style = "color:black;  background-color: #FAFAFA" value = ""><?php echo _COLOUR_DEFAULT; ?></option>

						<option style = "color:red;    background-color: #FAFAFA" value = "#FF0000"><?php echo _COLOUR_RED; ?></option>

						<option style = "color:blue;   background-color: #FAFAFA" value = "#0000FF"><?php echo _COLOUR_BLUE; ?></option>

						<option style = "color:green;  background-color: #FAFAFA" value = "#008000"><?php echo _COLOUR_GREEN; ?></option>

						<option style = "color:yellow; background-color: #FAFAFA" value = "#FFFF00"><?php echo _COLOUR_YELLOW; ?></option>

						<option style = "color:orange; background-color: #FAFAFA" value = "#FF6600"><?php echo _COLOUR_ORANGE; ?></option>
					</select>
<?php echo _SMILE_SIZE; ?>:

					<select name = "addbbcode22" onchange = "bbfontstyle('[size=' + this.form.addbbcode22.options[this.form.addbbcode22.selectedIndex].value + ']', '[/size]')" onmouseover = "helpline('f')">
						<option value = "1"><?php echo _SIZE_VSMALL; ?></option>

						<option value = "2"><?php echo _SIZE_SMALL; ?></option>

						<option value = "3" selected = "selected"><?php echo _SIZE_NORMAL; ?></option>

						<option value = "4"><?php echo _SIZE_BIG; ?></option>

						<option value = "5"><?php echo _SIZE_VBIG; ?></option>
					</select>

					<br/>

					<input type = "text" name = "helpbox" size = "45" maxlength = "100" style = "width: <?php echo $fbConfig->rtewidth-150?>px; font-size:9px" class = "helpline" value = "<?php echo _BBCODE_HINT;?>"/>

					<br/>

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
