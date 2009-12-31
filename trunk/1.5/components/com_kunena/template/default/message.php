<?php
/**
* @version $Id: message.php 1210 2009-11-23 06:51:41Z mahagr $
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

global $kunena_is_moderator;

$kunena_my = &JFactory::getUser();
$kunena_config =& CKunenaConfig::getInstance();
$kunena_db = &JFactory::getDBO();

global $kunena_emoticons;

$catid = JRequest::getInt('catid', 0);

if ($kunena_config->fb_profile == 'cb')
{
	$msg_params = array(
		'username' => &$msg_html->username,
		'messageobject' => &$fmessage,
		'subject' => &$msg_html->subject,
		'messagetext' => &$msg_html->text,
		'signature' => &$msg_html->signature,
		'karma' => &$msg_html->karma,
		'karmaplus' => &$msg_html->karmaplus,
		'karmaminus' => &$msg_html->karmaminus
	);
	$kunenaProfile =& CkunenaCBProfile::getInstance();
	$profileHtml = $kunenaProfile->showProfile($fmessage->userid, $msg_params);
} else {
	$profileHtml = null;
}

?>

<table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
    <tbody>
        <tr class = "fb_sth">
            <th colspan = "2" class = "view-th <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader">
        	<a name = "<?php echo $msg_html->id; ?>"></a>
<?php
                echo CKunenaLink::GetSamePageAnkerLink($msg_html->id, '#'.$msg_html->id)
?>
            </th>
        </tr>

        <tr> <!-- -->

        <td class = "fb-msgview-right">
                <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
                    <tr>
                        <td align = "left">
                            <?php
                            $msg_time_since = _KUNENA_TIME_SINCE;
                            $msg_time_since = str_replace('%time%', time_since($fmessage->time, CKunenaTools::fbGetInternalTime()), $msg_time_since);

                            if ($this->prevCheck < $fmessage->time && !in_array($fmessage->thread, $this->read_topics)) {
                                $msgtitle = 'msgtitle_new';
                            } else {
                                $msgtitle = 'msgtitle';
                            }
                            ?>
                            <span class = "<?php echo $msgtitle; ?>"><?php echo $msg_html->subject; ?> </span> <span class = "msgdate" title="<?php echo $msg_html->date; ?>"><?php echo $msg_time_since; ?></span>
                        </td>

                        <td align = "right">
                            <span class = "msgkarma">

                            <?php
                            if (isset($msg_html->karma)) {
                                echo $msg_html->karma;
								if (isset($msg_html->karmaplus))
									echo '&nbsp;&nbsp;' . $msg_html->karmaplus . ' ' . $msg_html->karmaminus;
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
                            <div class = "msgtext"><?php echo $msg_html->text; ?></div>

                            <?php
                            if (!isset($msg_html->closed))
                            {
                            ?>

                                <div id = "sc<?php echo $msg_html->id; ?>" class = "switchcontent">
                                    <!-- make this div distinct from others on this page -->
                                    <?php
                                    //see if we need the users realname or his loginname
                                    if ($kunena_config->username) {
                                        $authorName = $kunena_my->username;
                                    }
                                    else {
                                        $authorName = $kunena_my->name;
                                    }

                                    //contruct the reply subject
                                    $resubject = kunena_htmlspecialchars(strtolower(substr($msg_html->subject, 0, strlen(_POST_RE))) == strtolower(_POST_RE) ? $msg_html->subject : _POST_RE .' '. $msg_html->subject);
                                    ?>

                            <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL. '&amp;func=post'); ?>" method = "post" name = "postform" enctype = "multipart/form-data">
                                <input type = "hidden" name = "parentid" value = "<?php echo $msg_html->id;?>"/>

                                <input type = "hidden" name = "catid" value = "<?php echo $catid;?>"/>

                                <input type = "hidden" name = "action" value = "post"/>

                                <input type = "hidden" name = "contentURL" value = "empty"/>

                                <input type = "text" name = "subject" size = "35" class = "inputbox" maxlength = "<?php echo $kunena_config->maxsubject;?>" value = "<?php echo html_entity_decode($resubject);?>"/>

                                <textarea class = "inputbox" name = "message" rows = "6" cols = "60" style = "height: 100px; width: 100%; overflow:auto;"></textarea>

                                 <?php
								// Begin captcha . Thanks Adeptus
								if ($kunena_config->captcha && $kunena_my->id < 1) { ?>
								<?php echo _KUNENA_CAPDESC.'&nbsp;'?>
								<input name="txtNumber" type="text" id="txtNumber" value="" style="vertical-align:middle" size="10">&nbsp;
								<img src="index2.php?option=com_kunena&func=showcaptcha" alt="" /><br />
								<?php
								}
								// Finish captcha
								?>

                                <input type = "submit" class = "fb_button fb_qr_fire" name = "submit" value = "<?php @print(_GEN_CONTINUE);?>"/>

                                <input type = "button" class = "fb_button fb_qm_cncl_btn" id = "cancel__<?php echo $msg_html->id; ?>" name = "cancel" value = "<?php @print(_KUNENA_CANCEL);?>"/>

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

              <td class = "fb-msgview-left">
                <div class = "fb-msgview-l-cover">
<?php
					if ($profileHtml)
					{
						echo $profileHtml;
					}
					else
					{
?>
                    <span class = "view-username">
<?php
                        if ($userinfo->userid)
                        {
                        	echo CKunenaLink::GetProfileLink($kunena_config, $fmessage->userid, $msg_html->username);
                        }
                        else
                        {
                        	echo $msg_html->username;
                        }
?>
                    </span>
<?php
					if ( $kunena_config->userlist_usertype ) echo '<span class = "msgusertype">('.$msg_html->usertype.')</span>';
?>
                    <br/>
<?php
                        if ($fmessage->userid > 0)
                        {
                        	echo CKunenaLink::GetProfileLink($kunena_config, $fmessage->userid, $msg_html->avatar);
                        }
                        else
                        {
                        	echo $msg_html->avatar;
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

				<?php if (isset($msg_html->personal)) { ?>
                    <div class = "viewcover">
                   <?php echo $msg_html->personal; ?>
                  </div>
                <?php  }?>
                <div class = "viewcover">
                    <?php
                    if (isset($msg_html->userrank)) {
                        echo $msg_html->userrank;
                    }
                    ?>
                </div>

                <div class = "viewcover">
                    <?php
                    if (isset($msg_html->userrankimg)) {
                        echo $msg_html->userrankimg;
                    }
                    ?>
                </div>

                    <?php
                    if (isset($msg_html->posts)) {
                        echo $msg_html->posts;
                    }
                    ?>

                    <?php
                    if (isset($msg_html->myGraph)) {
                        $msg_html->myGraph->BarGraphHoriz();
                    }
                    ?>

                    <?php
                    if (isset($msg_html->myGraphAUP)) {
                        $msg_html->myGraphAUP->BarGraphHoriz();
                    }
                    ?>

                    <?php
                    if (isset($msg_html->online)) {
                        echo $msg_html->online;
                    }
                    ?>

                    <?php
                    if (isset($msg_html->pms)) {
                        echo $msg_html->pms;
                    }
                    ?>

                    <?php
                    if (isset($msg_html->profile)) {
                        echo $msg_html->profile;
                    }
                    ?>
                    <br />
 					<?php
                    if (isset($msg_html->icq)) {
                        echo $msg_html->icq;
                    }
                    ?>
                    <?php
                    if (isset($msg_html->gender)) {
                        echo $msg_html->gender;
                    }
                    ?>
                    <?php
                    if (isset($msg_html->skype)) {
                        echo $msg_html->skype;
                    }
                    ?>
                    <?php
                    if (isset($msg_html->website)) {
                        echo $msg_html->website;
                    }
                    ?>
                    <?php
                    if (isset($msg_html->gtalk)) {
                        echo $msg_html->gtalk;
                    }
                    ?>
                     <?php
                    if (isset($msg_html->yim)) {
                        echo $msg_html->yim;
                    }
                    ?>
                    <?php
                    if (isset($msg_html->msn)) {
                        echo $msg_html->msn;
                    }
                    ?>
					<?php
                    if (isset($msg_html->aim)) {
                        echo $msg_html->aim;
                    }
                    ?>
                    <?php
                    if (isset($msg_html->location)) {
                        echo $msg_html->location;
                    }
                    ?>
                    <?php
                    if (isset($msg_html->birthdate)) {
                        echo $msg_html->birthdate;
                    }
				}
                    ?>

                </div>
            </td>
<!-- -->

        </tr>

	<tr><td class = "fb-msgview-right-b" >
		<div class="fb_message_editMarkUp_cover">
<?php
	if ($fmessage->modified_by) {
		echo '<span class="fb_message_editMarkUp">'. _KUNENA_EDITING_LASTEDIT .': '. date(_DATETIME, $fmessage->modified_time) .' '. _KUNENA_BY .' '. CKunenaTools::whoisID($fmessage->modified_by) .'.';
		if ($fmessage->modified_reason) {
			echo _KUNENA_REASON .': '. kunena_htmlspecialchars(stripslashes($fmessage->modified_reason));
		}
		echo '</span>';
	}

                            if ($kunena_config->reportmsg && $kunena_my->id > 1)
                            {
                                echo '<span class="fb_message_informMarkUp">'.CKunenaLink::GetReportMessageLink($catid, $msg_html->id, _KUNENA_REPORT).'</span>';
                            }
                            if (isset($msg_html->ip))
                            {
				echo '<span class="fb_message_informMarkUp">'.CKunenaLink::GetMessageIPLink($msg_html->ip).'</span>';
                            } ?>
		</div>
		<div class="fb_message_buttons_cover">
			<div class="fb_message_buttons_row">
                <?php
                //we should only show the Quick Reply section to registered users. otherwise we are missing too much information!!
                /*    onClick="expandcontent(this, 'sc<?php echo $msg_html->id;?>')" */
                if ($kunena_my->id > 0 && !isset($msg_html->closed)):
                ?>
                <span id = "fb_qr_sc__<?php echo $msg_html->id;?>" class = "fb_qr_fire" style = "cursor:pointer">
                <?php echo
                    isset($kunena_emoticons['quickmsg']) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['quickmsg'] . '" border="0" alt="' . _KUNENA_QUICKMSG . '" />' . '' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'quickmsg.gif" border="0"   alt="' . _KUNENA_QUICKMSG . '" />'; ?>
                </span>
                <?php
                endif;
                ?>

                <?php
                if ($kunena_emoticons['reply'])
                {
                    if (!isset($msg_html->closed))
                    {
                        echo " " . $msg_html->reply;
                        echo " " . $msg_html->quote;

			if ($kunena_is_moderator) echo ' </div><div class="fb_message_buttons_row">';

                        if (isset($msg_html->merge)) {
                             echo " " . $msg_html->merge;
                        }

                        if (isset($msg_html->split)) {
                             echo " " . $msg_html->split;
                        }
                        if (isset($msg_html->delete)) {
                            echo " " . $msg_html->delete;
                        }
                        if (isset($msg_html->edit)) {
                            echo " " . $msg_html->edit;
                        }

                    }
                    else {
                        echo $msg_html->closed;
                    }

                }
                else
                {
                    if (!isset($msg_html->closed))
                    {
                        echo $msg_html->reply;
                ?>

                        |

                <?php
                echo $msg_html->quote;

                if (isset($msg_html->delete)) {
                    echo " | " . $msg_html->delete;
                }

                if (isset($msg_html->move)) {
                    echo " | " . $msg_html->move;
                }

                if (isset($msg_html->edit)) {
                    echo " | " . $msg_html->edit;
                }

                if (isset($msg_html->sticky)) {
                    echo " | " . $msg_html->sticky;
                }

                if (isset($msg_html->lock)) {
                    echo "| " . $msg_html->lock;
                }
                    }
                    else {
                        echo $msg_html->closed;
                    }
                }
                ?>
			</div>
		</div>
<?php
if (isset($msg_html->signature)) {
	echo '<div class="msgsignature">';
	echo $msg_html->signature;
	echo '</div>';
}
?>

            </td>
            <td class = "fb-msgview-left-b">&nbsp;

            </td>

        </tr>
    </tbody>
</table>
<!-- Begin: Message Module Positions -->
<?php
if (JDocumentHTML::countModules('kunena_msg_'.$this->mmm))
{
?>
    <div class = "kunena_msg_<?php echo $this->mmm; ?>">
        <?php
	        $document	= &JFactory::getDocument();
	        $renderer	= $document->loadRenderer('modules');
	        $options	= array('style' => 'xhtml');
	        $position	= 'kunena_msg_'.$this->mmm;
	        echo $renderer->render($position, $options, null);
        ?>
    </div>
<?php
}
?>
<!-- Finish: Message Module Positions -->
<?php
// --------------------------------------------------------------
//  Legend to the variables used
// --------------------------------------------------------------
// $msg_html->id          = Message ID#
// $msg_html->username    = Username (with email link if enabled)
// $msg_html->avatar      = User Avatar
// $msg_html->usertype    = User Type (Visitor/Member/moderator/Admin)
// $msg_html->userrank    = User Rank
// $msg_html->userrankimg = User Rank Image
// $msg_html->posts       = Post Count
// $msg_html->karma       = Karma Points
// $msg_html->karmaplus   = Linked Image for Karma+
// $msg_html->karmaminus  = Linked Image for Karma-
// $msg_html->ip          = IP of Poster
// $msg_html->ip_link     = Link to look up IP of Poster
// $msg_html->date        = Date of Post
// $msg_html->subject     = Post Subject
// $msg_html->text        = Post Text
// $msg_html->signature   = User's Signature
// $msg_html->reply       = Reply Option
// $msg_html->quote       = Quote Option
// $msg_html->edit        = Edit Option
// $msg_html->closed      = Locked/Diabled message
// $msg_html->delete      = Delete Option
// $msg_html->sticky      = Sticky/Unsticky Option
// $msg_html->lock        = Lock/Unlock Option
// $msg_html->aim         = User's AIM
// $msg_html->icq         = User's ICQ#
// $msg_html->msn         = User's MSN
// $msg_html->yahoo       = User's Yahoo
// $msg_html->profile     = Image link to user's Profile Page
// $msg_html->pms         = Linked image for PMS2
// $msg_html->buddy       = Add buddy image link
// $msg_html->loc         = User's Location
// $msg_html->regdate     = User's Registration Date
// $tabclass[$k]    = CSS Class for TD (use to alternate colors)
// fb_messagebody   = CSS Class for post text
// fb_signature     = CSS Class for signature
?>
