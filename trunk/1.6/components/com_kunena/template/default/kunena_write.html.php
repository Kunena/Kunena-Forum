<?php
/**
* @version $Id:kunena_write.html.php 884 2009-06-16 03:48:56Z fxstein $
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

$kunenaConfig =& CKunenaConfig::getInstance();
//Some initial thingies needed anyway:
if (!isset($htmlText)) $htmlText = '';
if (!isset($setFocus)) $setFocus = 0;
if (!isset($no_image_upload)) $no_image_upload = 0;
if (!isset($no_file_upload)) $no_file_upload = 0;
$authorName = stripslashes($authorName);

include_once(KUNENA_PATH_LIB .DS. 'kunena.bbcode.js.php');
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "kunena_blocktable<?php echo $objCatInfo->class_sfx; ?>" id="kunena_postmessage"  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th colspan = "2">
                <div class = "kunena_title_cover kunenam">
                    <span class = "kunena_title kunenal"> <?php echo _POST_MESSAGE; ?>"<?php echo kunena_htmlspecialchars(stripslashes($objCatInfo->name)); ?>"</span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody id = "kunena_post_message">
        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
            <td class = "kunena_leftcolumn">
                <strong><?php echo _GEN_NAME; ?></strong>:
            </td>

            <?php
            if (($kunenaConfig->regonly == "1" || $kunenaConfig->changename == '0') && $kunena_my->id != "" && !$is_Moderator) {
                echo "<td><input type=\"hidden\" name=\"kunena_authorname\" size=\"35\" class=\"" . $boardclass . "inputbox postinput\"  maxlength=\"35\" value=\"$authorName\" /><b>$authorName</b></td>";
            }
            else
            {
                if ($registeredUser == 1) {
                    echo "<td><input type=\"text\" name=\"kunena_authorname\" size=\"35\"  class=\"" . $boardclass . "inputbox postinput\"  maxlength=\"35\" value=\"$authorName\" /></td>";
                }
                else
                {
                    echo "<td><input type=\"text\" name=\"kunena_authorname\" size=\"35\"  class=\"" . $boardclass . "inputbox postinput\"  maxlength=\"35\" value=\"\" />";
                    echo "<script type=\"text/javascript\">document.postform.kunena_authorname.focus();</script></td>";
                    $setFocus = 1;
                }
            }
            ?>
        </tr>

        <?php
        if ($kunenaConfig->askemail)
        {
            echo '<tr class = "'. $boardclass . 'sectiontableentry2"><td class = "kunena_leftcolumn"><strong>' . _GEN_EMAIL . ' *</strong>:</td>';
            if (($kunenaConfig->regonly == "1" || $kunenaConfig->changename == '0') && $kunena_my->id != "" && !$is_Moderator) {
                echo "<td>$my_email</td>";
            }
            else
            {
                echo "<td><input type=\"text\" name=\"email\"  size=\"35\" class=\"" . $boardclass . "inputbox postinput\" maxlength=\"35\" value=\"$my_email\" /></td>";
            }
            echo '</tr>';
        }
        ?>

        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
            <?php
            if (!$fromBot)
            {
            ?>

                <td class = "kunena_leftcolumn">
                    <strong><?php echo _GEN_SUBJECT; ?></strong>:
                </td>

                <td>
                    <input type = "text" class = "<?php echo $boardclass; ?>inputbox postinput" name = "subject" size = "35" maxlength = "<?php echo $kunenaConfig->maxsubject;?>" value = "<?php echo $resubject;?>" />
                </td>

            <?php
            }
            else
            {
            ?>

                <td class = "kunena_leftcolumn">
                    <strong><?php echo _GEN_SUBJECT; ?></strong>:
                </td>

                <td>
                    <input type = "hidden" class = "inputbox" name = "subject" size = "35" maxlength = "<?php echo $kunenaConfig->maxsubject;?>" value = "<?php echo $resubject;?>" /><?php echo $resubject; ?>
                </td>

            <?php
            }
            ?>

            <?php
            if ($setFocus == 0 && $id == 0 && !$fromBot)
            {
                echo "<script type=\"text/javascript\">document.postform.subject.focus();</script>";
                $setFocus = 1;
            }
            ?>
        </tr>

            <?php
            if ($parentid == 0)
            {
?>
        <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
            <td class = "kunena_leftcolumn">
                <strong><?php echo _GEN_TOPIC_ICON; ?></strong>:
            </td>

            <td class = "kunena_topicicons">
                <?php
                $topicToolbar = smile::topicToolbar(0, $kunenaConfig->rtewidth);
                echo $topicToolbar;
                ?>
            </td>
        </tr>
            <?php
            }
            ?>

        <?php
        if ($kunenaConfig->rtewidth == 0) {
            $useRte = 0;
        }
        else {
            $useRte = 1;
        }

        $kunenaTextArea = smile::kunenaWriteTextarea('message', $htmlText, $kunenaConfig->rtewidth, $kunenaConfig->rteheight, $useRte, $kunenaConfig->disemoticons);
        echo $kunenaTextArea;

        if ($setFocus == 0) {
            echo '<tr><td style="display:none;"><script type="text/javascript">document.postform.message.focus();</script></td></tr>';
        }

        //check if this user is already subscribed to this topic but only if subscriptions are allowed
        if ($kunenaConfig->allowsubscriptions == 1)
        {
            if ($id == 0) {
                $kunena_thread = -1;
            }
            else
            {
                $kunena_db->setQuery("SELECT thread FROM #__kunena_messages WHERE id='{$id}'");
                $kunena_thread = $kunena_db->loadResult();
            }

            $kunena_db->setQuery("SELECT thread FROM #__kunena_subscriptions WHERE userid='{$kunena_my->id}' AND thread='{$kunena_thread}'");
            $kunena_subscribed = $kunena_db->loadResult();

            if ($kunena_subscribed == "" || $id == 0) {
                $kunena_cansubscribe = 1;
            }
            else {
                $kunena_cansubscribe = 0;
            }
        }
?>

               <!-- preview -->
        <tr class = "<?php echo $boardclass; ?>sectiontableentry2" id="previewContainer" style="display:none;">
           <td class = "kunena_leftcolumn">
                <strong><?php echo _PREVIEW; ?></strong>:
            </td>
           <td>
  <div class="previewMsg" id="previewMsg" style="height:<?php echo $kunenaConfig->rteheight;?>px;overflow:auto;"></div>
            </td>
        </tr>
<!-- /preview -->
<?php
        if (($kunenaConfig->allowimageupload || ($kunenaConfig->allowimageregupload && $kunena_my->id != 0) || $is_Moderator) && $no_image_upload == "0")
        {
        ?>

            <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
                <td class = "kunena_leftcolumn">
                    <strong><?php echo _IMAGE_SELECT_FILE; ?></strong>
                </td>

                <td>
                    <input type = 'file' class = 'kunena_button' name = 'attachimage' onmouseover = "javascript:kunenaShowHelp('<?php @print(_IMAGE_DIMENSIONS).": ".$kunenaConfig->imagewidth."x".$kunenaConfig->imageheight." - ".$kunenaConfig->imagesize." KB";?>')" />
                    <input type = "button" class = "kunena_button" name = "addImagePH" value = "<?php @print(_POST_ATTACH_IMAGE);?>" style = "cursor:auto; width: 4em" onclick = "bbfontstyle(' [img/] ','');" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_IMGPH);?>')" />
                </td>
            </tr>

        <?php
        }
        ?>

        <?php
        if (($kunenaConfig->allowfileupload || ($kunenaConfig->allowfileregupload && $kunena_my->id != 0) || $is_Moderator) && $no_file_upload == "0")
        {
        ?>

            <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
                <td class = "kunena_leftcolumn">
                    <strong><?php echo _FILE_SELECT_FILE; ?></strong>
                </td>

                <td>
                    <input type = 'file' class = 'kunena_button' name = 'attachfile' onmouseover = "javascript:kunenaShowHelp('<?php @print(_FILE_TYPES).": ".$kunenaConfig->filetypes." - ".$kunenaConfig->filesize." KB";?>')" style = "cursor:auto" />
                    <input type = "button" class = "kunena_button" name = "addFilePH" value = "<?php @print(_POST_ATTACH_FILE);?>" style = "cursor:auto; width: 4em" onclick = "bbfontstyle(' [file/] ','');" onmouseover = "javascript:kunenaShowHelp('<?php @print(_KUNENA_EDITOR_HELPLINE_FILEPH);?>')" />
                </td>
            </tr>

        <?php
        }

        if ($kunena_my->id != 0 && $kunenaConfig->allowsubscriptions == 1 && $kunena_cansubscribe == 1 && !$editmode)
        {
        ?>

            <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
                <td class = "kunena_leftcolumn">
                    <strong><?php echo _POST_SUBSCRIBE; ?></strong>:
                </td>

                <td>
                    <?php
                    if ($kunenaConfig->subscriptionschecked == 1)
                    {
                    ?>

                            <input type = "checkbox" name = "subscribeMe" value = "1" checked/>

                            <i><?php echo _POST_NOTIFIED; ?></i>

                    <?php
                    }
                    else
                    {
                    ?>

                        <input type = "checkbox" name = "subscribeMe" value = "1" />

                        <i><?php echo _POST_NOTIFIED; ?></i>

                    <?php
                    }
                    ?>
                </td>
            </tr>

        <?php
        }
        ?>
		<?php
		// Begin captcha . Thanks Adeptus
		if ($kunenaConfig->captcha == 1 && $kunena_my->id < 1) { ?>
        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
            <td class = "kunena_leftcolumn">&nbsp;<strong><?php echo _KUNENA_CAPDESC; ?></strong>&nbsp;</td>
            <td align="left" valign="middle" height="35px">&nbsp;<input name="txtNumber" type="text" id="txtNumber" value="" class="kunena_button" style="vertical-align:top" size="15">
			<img src="index2.php?option=com_kunena&func=showcaptcha" alt="" />
		 </td>
         </tr>
        <?php
		}
		// Finish captcha
		?>
        <tr>
            <td id="kunena_post_buttons" colspan = "2" style = "text-align: center;">
                <input type="submit" name="submit"  class="kunena_button" value="<?php @print(' '._GEN_CONTINUE.' ');?>" onclick="return submitForm()" onmouseover = "javascript:jQuery('input[name=helpbox]').val('<?php @print(_KUNENA_EDITOR_HELPLINE_SUBMIT);?>')" />
                <input type="button" name="preview" class="kunena_button" value="<?php @print(' '._PREVIEW.' ');?>"      onClick="kunenaGetPreview(document.postform.message.value,<?php echo KUNENA_COMPONENT_ITEMID?>);" onmouseover = "javascript:jQuery('input[name=helpbox]').val('<?php @print(_KUNENA_EDITOR_HELPLINE_PREVIEW);?>')" />
                <input type="button" name="cancel"  class="kunena_button" value="<?php @print(' '._GEN_CANCEL.' ');?>"   onclick="javascript:window.history.back();" onmouseover = "javascript:jQuery('input[name=helpbox]').val('<?php @print(_KUNENA_EDITOR_HELPLINE_CANCEL);?>')" />
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<input type="hidden" value="<?php echo KUNENA_DIRECTURL . '/template/default';?>" name="templatePath" />
<input type="hidden" value="<?php echo JRoute::_(KUNENA_LIVEURLREL);?>" name="kunenaPath" />
</form>

</td>

</tr>

<tr>
    <td>
        <?php
        if ($kunenaConfig->askemail) {
            echo $kunenaConfig->showemail == '0' ? "<em>* - " . _POST_EMAIL_NEVER . "</em>" : "<em>* - " . _POST_EMAIL_REGISTERED . "</em>";
        }
        ?>
    </td>
</tr>

<tr>
    <td style = "text-align: left;">
        <br/>

    <?php
    $no_upload = "0"; //reset the value.. you just never know..

    if ($kunenaConfig->showhistory == 1) {
        listThreadHistory($id, $kunenaConfig, $kunena_db);
    }
?>
