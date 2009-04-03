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

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$my = &JFactory::getUser();

$database = &JFactory::getDBO();
global $fbConfig;
unset($user);
$database->setQuery("SELECT email, name from #__users WHERE `id`={$my->id}");
$user = $database->loadObject();
?>

<table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
    <tbody>
        <tr class = "fb_sth">
            <th colspan = "2" class = "view-th <?php echo $boardclass; ?>sectiontableheader">
        	<a name = "<?php echo $msg_id; ?>"></a>
<?php
                echo CKunenaLink::GetSamePageAnkerLink($msg_id, '#'.$msg_id)
?>
            </th>
        </tr>

        <tr>
              <td class = "fb-msgview-left">
                <div class = "fb-msgview-l-cover">
                    <span class = "view-username">
<?php
                        if ($fmessage->userid > 0)
                        {
                        	echo CKunenaLink::GetProfileLink($fbConfig, $fmessage->userid, $msg_username);
                        }
                        else
                        {
                        	echo $msg_username;
                        }
?>
                    </span> <span class = "msgusertype">(<?php echo $msg_usertype; ?>)</span>
                    <br/>
<?php
                        if ($fmessage->userid > 0)
                        {
                        	echo CKunenaLink::GetProfileLink($fbConfig, $fmessage->userid, $msg_avatar);
                        }
                        else
                        {
                        	echo $msg_avatar;
                        }
?>

				<?php
                $gr_title = getFBGroupName($lists["userid"]);

                if ($gr_title->id > 1)
                {
                ?>

                    <span class = "view-group_<?php echo $gr_title->id;?>"> <?php echo $gr_title->title; ?></span>

                <?php
                }
                ?>

				<?php if ($msg_personal) { ?>
                    <div class = "viewcover">
                   <?php echo $msg_personal; ?>
                  </div>
                <?php  }?>
                <div class = "viewcover">
                    <?php
                    if ($msg_userrank) {
                        echo $msg_userrank;
                    }
                    ?>
                </div>

                <div class = "viewcover">
                    <?php
                    if ($msg_userrankimg) {
                        echo $msg_userrankimg;
                    }
                    ?>
                </div>

                    <?php
                    if ($msg_posts) {
                        echo $msg_posts;
                    }
                    ?>

                    <?php
                    if ($useGraph) {
                        $myGraph->BarGraphHoriz();
                    }
                    ?>



                    <?php echo $msg_online; ?>

                    <?php
                    if ($msg_pms) {
                        echo $msg_pms;
                    }
                    ?>

                    <?php
                    if ($msg_profile) {
                        echo $msg_profile;
                    }
                    ?>
                    <br />
 					<?php
                    if ($msg_icq) {
                        echo $msg_icq;
                    }
                    ?>
                    <?php
                    if ($msg_gender) {
                        echo $msg_gender;
                    }
                    ?>
                    <?php
                    if ($msg_skype) {
                        echo $msg_skype;
                    }
                    ?>
                    <?php
                    if ($msg_website) {
                        echo $msg_website;
                    }
                    ?>
                    <?php
                    if ($msg_gtalk) {
                        echo $msg_gtalk;
                    }
                    ?>
                     <?php
                    if ($msg_yim) {
                        echo $msg_yim;
                    }
                    ?>
                    <?php
                    if ($msg_msn) {
                        echo $msg_msn;
                    }
                    ?>
					<?php
                    if ($msg_aim) {
                        echo $msg_aim;
                    }
                    ?>
                    <?php
                    if ($msg_location) {
                        echo $msg_location;
                    }
                    ?>
                    <?php
                    if ($msg_birthdate) {
                        echo $msg_birthdate;
                    }
                    ?>

                </div>
            </td>

            <td class = "fb-msgview-right">
                <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
                    <tr>
                        <td align = "left">
                            <?php
                            $msg_time_since = _KUNENA_TIME_SINCE;
                            $msg_time_since = str_replace('%time%', time_since($fmessage->time , CKunenaTools::fbGetInternalTime()), $msg_time_since);

                            if ($prevCheck < $msg_time && !in_array($fmessage->thread, $read_topics)) {
                                $msgtitle = 'msgtitle_new';
                            } else {
                                $msgtitle = 'msgtitle';
                            }
                            ?>
                            <span class = "<?php echo $msgtitle; ?>"><?php echo $msg_subject; ?> </span> <span class = "msgdate" title="<?php echo $msg_date; ?>"><?php echo $msg_time_since; ?></span>
                        </td>

                        <td align = "right">
                            <span class = "msgkarma">

                            <?php
                            if ($msg_karma) {
                                echo $msg_karma . '&nbsp;&nbsp;' . $msg_karmaplus . ' ' . $msg_karmaminus;
                            }
                            else {
                                echo '&nbsp;';
                            }
                            ?>

                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan = "2" valign = "top">
                            <div class = "msgtext"><?php echo $msg_text; ?></div>

                            <?php
                            if (!$msg_closed)
                            {
                            ?>

                                <div id = "sc<?php echo $msg_id; ?>" class = "switchcontent" style="display:none">
                                    <!-- make this div distinct from others on this page -->
                                    <?php
                                    //see if we need the users realname or his loginname
                                    if ($fbConfig->username) {
                                        $authorName = $my->username;
                                    }
                                    else {
                                        $authorName = $user->name;
                                    }

                                    //contruct the reply subject
                                    $resubject = html_entity_decode_utf8(stripslashes($msg_subject), ENT_COMPAT, 'UTF-8');
                                    $resubject = strtolower(substr($resubject, 0, strlen(_POST_RE))) == strtolower(_POST_RE) ? $resubject : _POST_RE . $resubject;
                                    ?>

                            <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL. '&amp;func=post'); ?>" method = "post" name = "postform" enctype = "multipart/form-data">
                                <input type = "hidden" name = "parentid" value = "<?php echo $msg_id;?>"/>

                                <input type = "hidden" name = "catid" value = "<?php echo $catid;?>"/>

                                <input type = "hidden" name = "action" value = "post"/>

                                <input type = "hidden" name = "contentURL" value = "empty"/>

                                <input type = "text" name = "subject" size = "35" class = "inputbox" maxlength = "<?php echo $fbConfig->maxsubject;?>" value = "<?php echo $resubject;?>"/>

                                <textarea class = "inputbox" name = "message" rows = "6" cols = "60" style = "height: 100px; width: 100%; overflow:auto;"></textarea>

                                 <?php
								// Begin captcha . Thanks Adeptus
								if ($fbConfig->captcha == 1 && $my->id < 1) { ?>
								<?php echo _KUNENA_CAPDESC.'&nbsp;'?>
								<input name="txtNumber" type="text" id="txtNumber" value="" style="vertical-align:middle" size="10">&nbsp;
								<img src="index2.php?option=com_kunena&func=showcaptcha" alt="" /><br />
								<?php
								}
								// Finish captcha
								?>

                                <input type = "submit" class = "fb_qm_btn" name = "submit" value = "<?php echo _GEN_CONTINUE;?>"/>

                                <input type = "button" class = "fb_qm_btn fb_qm_cncl_btn" id = "cancel__<?php echo $msg_id; ?>" name = "cancel" value = "<?php echo _KUNENA_CANCEL;?>"/>

                                <small><em><?php echo _KUNENA_QMESSAGE_NOTE?></em></small>
                            </form>
                                </div>

                            <?php
                            }
                            ?>


                        </td>
                    </tr>


                </table>
            </td>
        </tr>

  <tr>
            <td class = "fb-msgview-left-c">&nbsp;
            </td>
            <td class = "fb-msgview-right-c" >
                         <div class="fb_smalltext" >
                   <?php
                            if ($fbConfig->reportmsg && $my->id > 1)
                            {
                                echo CKunenaLink::GetReportMessageLink($catid, $msg_id, _KUNENA_REPORT);
                            } ?>

                            <?php echo $fbIcons['msgip'] ? '<img src="'.KUNENA_URLICONSPATH.$fbIcons['msgip'] .'" border="0" alt="'._KUNENA_REPORT_LOGGED.'" />' : ' <img src="'.KUNENA_URLEMOTIONSPATH.'ip.gif" border="0" alt="'. _KUNENA_REPORT_LOGGED.'" />';
                            ?> <span class="fb_smalltext"> <?php echo _KUNENA_REPORT_LOGGED;?></span>
                            <?php
                            echo CKunenaLink::GetMessageIPLink($msg_ip);
                            ?>
                            </div>
       </td>
        </tr>
<?php
if ($fmessage->modified_by) {
  ?>
        <tr>
            <td class = "fb-msgview-left-c">&nbsp;
            </td>
            <td class = "fb-msgview-right-c" >
                    <div class="fb_message_editMarkUp_cover">
                    <span class="fb_message_editMarkUp" ><?php echo _KUNENA_EDITING_LASTEDIT;?>: <?php echo date(_DATETIME, $fmessage->modified_time);?> <?php echo _KUNENA_BY; ?> <?php echo CKunenaTools::whoisID($fmessage->modified_by)?>.
                    <?php
                    if ($fmessage->modified_reason) {
                    echo _KUNENA_REASON.": ".$fmessage->modified_reason;
                    }
                        ?></span>
                    </div>
       </td>
        </tr>
<?php
}
?>

<?php
if ($msg_signature) {
  ?>
        <tr>
            <td class = "fb-msgview-left-c">&nbsp;
            </td>
            <td class = "fb-msgview-right-c" >

				   <div class="msgsignature" >
					<?php   echo $msg_signature; ?>
				</div>
       </td>
        </tr>
<?php
}
?>

        <tr>
            <td class = "fb-msgview-left-b">&nbsp;

            </td>

            <td class = "fb-msgview-right-b" align = "right">
                <span id = "fb_qr_sc__<?php echo $msg_id;?>" class = "fb_qr_fire" style = "cursor:hand; cursor:pointer">

                <?php
                //we should only show the Quick Reply section to registered users. otherwise we are missing too much information!!
                /*    onClick="expandcontent(this, 'sc<?php echo $msg_id;?>')" */
                if ($my->id > 0 && !$msg_closed)
                {
                ?>

                <?php echo
                    $fbIcons['quickmsg']
                        ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['quickmsg'] . '" border="0" alt="' . _KUNENA_QUICKMSG . '" title="' . _KUNENA_QUICKMSG . '" />' . '' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'quickmsg.gif" border="0"   alt="' . _KUNENA_QUICKMSG . '" />'; ?>
                <?php
                }
                ?>
                </span>

                <?php
                if ($fbIcons['reply'])
                {
                    if ($msg_closed == "")
                    {
                        echo $msg_reply;
                        echo " " . $msg_quote;

                        if ($msg_delete) {
                            echo " " . $msg_delete;
                        }

                        if ($msg_move) {
                            echo " " . $msg_move;
                        }

                        if ($msg_merge) {
                             echo " " . $msg_merge;
                         }

                        if ($msg_split) {
                             echo " " . $msg_split;
                         }

                        if ($msg_edit) {
                            echo " " . $msg_edit;
                        }

                        if ($msg_sticky) {
                            echo " " . $msg_sticky;
                        }

                        if ($msg_lock) {
                            echo " " . $msg_lock;
                        }
                    }
                    else {
                        echo $msg_closed;
                    }
                }
                else
                {
                    if ($msg_closed == "")
                    {
                        echo $msg_reply;
                ?>

                        |

                <?php
                echo $msg_quote;

                if ($msg_delete) {
                    echo " | " . $msg_delete;
                }

                if ($msg_move) {
                    echo " | " . $msg_move;
                }

                if ($msg_edit) {
                    echo " | " . $msg_edit;
                }

                if ($msg_sticky) {
                    echo " | " . $msg_sticky;
                }

                if ($msg_lock) {
                    echo "| " . $msg_lock;
                }
                    }
                    else {
                        echo $msg_closed;
                    }
                }
                ?>

            </td>
        </tr>
    </tbody>
</table>
<!-- Begin: Message Module Positions -->
<jdoc:exists type="modules" condition="kunena_msg_<?php echo $mmm; ?>" />
    <div class = "kunena_msg_<?php echo $mmm; ?>">
	<jdoc:include type="modules" name="kunena_msg_<?php echo $mmm; ?>" />
    </div>
</jdoc:exists>
<!-- Finish: Message Module Positions -->
<?php
// --------------------------------------------------------------
//  Legend to the variables used
// --------------------------------------------------------------
// $msg_id          = Message ID#
// $msg_username    = Username (with email link if enabled)
// $msg_avatar      = User Avatar
// $msg_usertype    = User Type (Visitor/Member/moderator/Admin)
// $msg_userrank    = User Rank
// $msg_userrankimg = User Rank Image
// $msg_posts       = Post Count
// $msg_karma       = Karma Points
// $msg_karmaplus   = Linked Image for Karma+
// $msg_karmaminus  = Linked Image for Karma-
// $msg_ip          = IP of Poster
// $msg_ip_link     = Link to look up IP of Poster
// $msg_date        = Date of Post
// $msg_subject     = Post Subject
// $msg_text        = Post Text
// $msg_signature   = User's Signature
// $msg_reply       = Reply Option
// $msg_quote       = Quote Option
// $msg_edit        = Edit Option
// $msg_closed      = Locked/Diabled message
// $msg_delete      = Delete Option
// $msg_sticky      = Sticky/Unsticky Option
// $msg_lock        = Lock/Unlock Option
// $msg_aim         = User's AIM
// $msg_icq         = User's ICQ#
// $msg_msn         = User's MSN
// $msg_yahoo       = User's Yahoo
// $msg_profile     = Image link to user's Profile Page
// $msg_pms         = Linked image for PMS2
// $msg_buddy       = Add buddy image link
// $msg_loc         = User's Location
// $msg_regdate     = User's Registration Date
// $tabclass[$k]    = CSS Class for TD (use to alternate colors)
// fb_messagebody   = CSS Class for post text
// fb_signature     = CSS Class for signature
?>
