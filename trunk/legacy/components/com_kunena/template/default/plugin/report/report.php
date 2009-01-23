<?php
/**
* @version $Id: report.php 947 2008-08-11 01:56:01Z fxstein $
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

// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

$msg_id = mosGetParam($_REQUEST, 'msg_id');
$catid = (int)mosGetParam($_REQUEST, 'catid', 0);
$reporter = mosGetParam($_REQUEST, 'reporter');
$reason = strval(mosGetParam($_REQUEST, 'reason'));
$text = strval(mosGetParam($_REQUEST, 'text'));
$type = mosGetParam($_REQUEST, 'type', 0); // 0 = send e-mail, 1 = send pm

switch ($do)
{
    case 'report':
        ReportMessage((int)$msg_id, $catid, (int)$reporter, $reason, $text, (int)$type, $redirect);

        break;

    default:
        ReportForm($msg_id, $catid);

        break;
}

function ReportMessage($msg_id, $catid, $reporter, $reason, $text, $type) {
    global $database, $my;

    if (!$my->id) {
        mosNotAuth();
        return;
        }

    $database->setQuery("SELECT a.*, b.message AS msg_text FROM #__fb_messages AS a"
    . "\n LEFT JOIN #__fb_messages_text AS b ON b.mesid = a.id"
    . "\n WHERE a.id={$msg_id}");

    $database->loadObject($row);

    $database->setQuery("SELECT username FROM #__users WHERE id={$row->userid}");
    $baduser = $database->loadResult();

    $database->setQuery("SELECT username FROM #__users WHERE id={$reporter}");
    $sender = $database->loadResult();

    if ($reason) {
        $subject = _FB_REPORT_MSG . ":" . $reason;
        }
    else {
        $subject = _FB_REPORT_MSG . ":" . $row->subject;
        }

    $msglink = "index.php?option=com_fireboard&amp;func=view&amp;catid=" . $row->catid . "&amp;id=" . $row->id . FB_FB_ITEMID_SUFFIX;
    $msglink = sefRelToAbs($msglink . '#' . $row->id);

    $message = $sender . "" . _FB_REPORT_INTRO . "" . $reason . " ";
    $message .= "\n";
    $message .= "\n";
    $message .= "\n";
    $message .= "" . _FB_REPORT_RSENDER . "" . $sender;
    $message .= "\n";
    $message .= "" . _FB_REPORT_RREASON . "" . $reason;
    $message .= "\n";
    $message .= "" . _FB_REPORT_RMESSAGE . "" . $text;
    $message .= "\n";
    $message .= "\n";
    $message .= "\n";
    $message .= "\n";
    $message .= "" . _FB_REPORT_POST_POSTER . "" . $baduser;
    $message .= "\n";
    $message .= "" . _FB_REPORT_POST_SUBJECT . "" . $row->subject;
    $message .= "\n";
    $message .= "" . _FB_REPORT_POST_MESSAGE . "" . $row->msg_text;
    $message .= "\n";
    $message .= "\n";
    $message .= "\n";
    $message .= "" . _FB_REPORT_POST_LINK . "" . $msglink;

    //get category moderators
    $database->setQuery("SELECT userid FROM #__fb_moderation WHERE catid={$row->catid}");
    $mods = $database->loadObjectList();
    	check_dberror("Unable to load moderators.");

    //get admins
    $database->setQuery("SELECT id FROM #__users WHERE gid >= 24");
    $admins = $database->loadObjectList();
    	check_dberror("Unable to load admin.");

    switch ($type)
    {
        default:
        case '0':
            SendReporttoMail($sender, $subject, $message, $msglink, $mods, $admins);

            break;

        case '1':
            SendReporttoPM($reporter, $subject, $message, $msglink, $mods, $admins);

            break;
    }

    echo '<div align="center">' . _FB_REPORT_SUCCESS . '<br /><br />';
    echo '<a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=view&amp;catid=' . $catid . '&amp;id=' . $msg_id) . '#' . $msg_id . '">' . _POST_SUCCESS_VIEW . '</a><br />';
    echo '<a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=showcat&amp;catid=' . $catid) . '">' . _POST_SUCCESS_FORUM . '</a><br />';
    echo '</div>';
?>
     <script language = "javascript">
     setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL.'&func=view&catid='.$catid.'&id='.$msg_id).'#'.$msg_id;?>'", 3500);
     </script>
<?php
}

function SendReporttoMail($sender, $subject, $message, $msglink, $mods, $admins) {
    global $database, $mainframe, $fbConfig;

    //send report to category moderators
    if (count($mods)>0) {
        foreach ($mods as $mod) {
            $database->setQuery("SELECT email FROM #__users WHERE id={$mod->userid}");
            $email = $database->loadResult();

            mosMail($fbConfig->email, $fbConfig->board_title, $email, $subject, $message);
            }
    }

    //send report to site admins
    foreach ($admins as $admin) {
        $database->setQuery("SELECT email FROM #__users WHERE id={$admin->id}");
        $email = $database->loadResult();

        mosMail($fbConfig->email, $fbConfig->board_title, $email, $subject, $message);
        }
    }

function SendReporttoPM($sender, $subject, $message, $msglink, $mods, $admins) {
    global $database, $fbConfig;

    switch ($fbConfig->pm_component)
    {
        case 'no': break;

        //myPMS II Open Source
        case 'pms':
            SendPMS();

            break;

        //Clexus PM
        case 'clexuspm':
            SendClexusPM($reporter, $subject, $message, $msglink, $mods, $admins);

            break;

        //uddeIM
        case 'uddeim':
            SendUddeIM();

            break;

        //JIM
        case 'jim':
            SendJIM();

            break;

        case 'missus':
            SendMissus();

            break;
    }
    }

function ReportForm($msg_id, $catid) {
    global $my, $fbConfig;

    $redirect = 'index.php?option=com_fireboard&amp;func=view&amp;catid=' . $catid . '&amp;id=' . $msg_id . '&amp;Itemid=' . FB_FB_ITEMID . '#' . $msg_id;

    //$redirect = sefRelToAbs($redirect);
    if (!$my->id) {
        mosRedirect ($redirect);
        return;
        }

    if ($fbConfig->reportmsg == 0) {
        mosRedirect ($redirect);
        return;
        }
?>

<div class = "<?php echo $boardclass; ?>_bt_cvr1">
    <div class = "<?php echo $boardclass; ?>_bt_cvr2">
        <div class = "<?php echo $boardclass; ?>_bt_cvr3">
            <div class = "<?php echo $boardclass; ?>_bt_cvr4">
                <div class = "<?php echo $boardclass; ?>_bt_cvr5">
                    <table class = "fb_blocktable" id = "fb_forumfaq" border = "0" cellspacing = "0" cellpadding = "0" width = "100%">
                        <thead>
                            <tr>
                                <th>
                                    <div class = "fb_title_cover">
                                        <span class = "fb_title"><?php echo _FB_COM_A_REPORT ?></span>
                                    </div>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td class = "fb_faqdesc">
                                    <form method = "post" action = "index.php">
                                        <table width = "100%" border = "0">
                                            <tr>
                                                <td width = "10%">
<?php echo _FB_REPORT_REASON ?>:
                                                </td>

                                                <td>
                                                    <input type = "text" name = "reason" class = "inputbox" size = "30"/>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan = "2">
<?php echo _FB_REPORT_MESSAGE ?>:
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan = "2">
                                                    <textarea id = "text" name = "text" cols = "40" rows = "10" class = "inputbox"></textarea>
                                                </td>
                                            </tr>
                                        </table>

                                        <input type = "hidden" name = "option" value = "com_fireboard"/>
                                        <input type = "hidden" name = "func" value = "report"/>
                                        <input type = "hidden" name = "do" value = "report"/>
                                        <input type = "hidden" name = "msg_id" value = "<?php echo $msg_id;?>"/>
                                        <input type = "hidden" name = "catid" value = "<?php echo $catid;?>"/>
                                        <input type = "hidden" name = "reporter" value = "<?php echo $my->id;?>"/>
                                        <input type = "submit" name = "Submit" value = "<?php echo _FB_REPORT_SEND ?>"/>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    }

function SendClexusPM($reporter, $subject, $message, $msglink, $mods, $admins) {
    global $database;
    $time = mosFormatDate(FBTools::fbGetInternalTime(), '%Y-%m-%d %H:%M:%S');

    foreach ($admins as $admin) {
        $database->setQuery("INSERT INTO #__mypms" . "\n ( `userid` , `whofrom` , `time` , `readstate` , `subject` , `message` , `owner` , `folder` , `sent_id` , `replyid` , `ip` , `alert` , `flag` , `pm_notify` , `email_notify` )"
                                . "\n VALUES ('$admin->id', '$reporter', '$time', '0', '$subject', '$message', '$admin->id', NULL , '0', '0', NULL , '0', '0', '0', '1'");
        $database->query();
        }

    foreach ($mods as $mod) {
        $database->setQuery("INSERT INTO #__mypms" . "\n ( `userid` , `whofrom` , `time` , `readstate` , `subject` , `message` , `owner` , `folder` , `sent_id` , `replyid` , `ip` , `alert` , `flag` , `pm_notify` , `email_notify` )"
                                . "\n VALUES ('$mod->id', '$reporter', '$time', '0', '$subject', '$message', '$mod->id', NULL , '0', '0', NULL , '0', '0', '0', '1'");
        $database->query();
        }

    mosErrorAlert('' . _FB_REPORT_SUCCESS . '', 'window.history.go(-2);');
    }
?>