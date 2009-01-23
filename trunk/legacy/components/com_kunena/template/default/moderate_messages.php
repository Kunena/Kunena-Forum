<?php
/**
* @version $Id: moderate_messages.php 1029 2008-08-25 15:40:07Z racoon $
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

global $my;
//securing form elements
$catid = (int)$catid;

if (!$is_Moderator) {
    die ("You are not a moderator!!<br />This error is logged and your IP address has been sent to the SuperAdmin(s) of this site; sorry..");
}

//but we don't send the email; we might do that in the future, but for now we just want to scare 'em off..
// determine what to do
$action = mosGetParam($_POST, 'action', 'list');
$cid = mosGetParam($_POST, 'cid', array ());

switch ($action)
{
    case _MOD_DELETE:
        switch (jbDeletePosts($database, $cid))
        {
            case -1:
                mosRedirect(JB_LIVEURL . 'func=review&amp;catid=' . $catid, "ERROR: The post has been deleted but the text could not be deleted\n Check the #__fb_messages_text table for mesid IN " . explode(',', $cid));

                break;

            case 0:
                mosRedirect(JB_LIVEURL . '&amp;func=review&amp;catid=' . $catid, _MODERATION_DELETE_ERROR);

                break;

            case 1:
            default:
                mosRedirect(JB_LIVEURL . '&amp;func=review&amp;catid=' . $catid, _MODERATION_DELETE_SUCCESS);

                break;
        }

        break;

    case _MOD_APPROVE:
        switch (jbApprovePosts($database, $cid))
        {
            case 0:
                mosRedirect(JB_LIVEURL . 'amp;func=review&amp;catid=' . $catid, _MODERATION_APPROVE_ERROR);

                break;

            default:
            case 1:
                mosRedirect(JB_LIVEURL . '&amp;func=review&amp;catid=' . $catid, _MODERATION_APPROVE_SUCCESS);

                break;
        }

        break;

    default:
    case 'list':
        echo '<p class="sectionname"><?php echo _MESSAGE_ADMINISTRATION; ?></p>';

        $database->setQuery("SELECT m.id,m.time,m.name,m.subject,m.hold,t.message FROM #__fb_messages AS m JOIN #__fb_messages_text as t ON m.id=t.mesid WHERE hold='1' AND catid=$catid ORDER BY id ASC");

        if (!$database->query())
            echo $database->getErrorMsg();

        $allMes = $database->loadObjectList();
        	check_dberror("Unable to load messages.");

        if (count($allMes) > 0)
            jbListMessages($allMes, $catid);
        else
            echo '<p style="text-align:center">' . _MODERATION_MESSAGES . '</p>';

        break;
}
/**
 * Lists messages to be moderated
 * @param array    allMes list of object
 * @param string fbs action string
 */
function jbListMessages($allMes, $catid)
{
    global $fbConfig;
    echo '<form action="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=review') . '" name="moderation" method="post">';
?>

    <script>
        function ConfirmDelete()
        {
            if (confirm("<?php echo _MODERATION_DELETE_MESSAGE; ?>"))
                document.moderation.submit();
            else
                return false;
        }
    </script>

    <table width = "100%" border = 0 cellspacing = 1 cellpadding = 3>
        <tr height = "10" class = "fb_table_header">
            <th align = "center">
                <b><?php echo _GEN_DATE; ?></b>
            </th>

            <th width = "8%" align = "center">
                <b><?php echo _GEN_AUTHOR; ?></b>
            </th>

            <th width = "13%" align = "center">
                <b><?php echo _GEN_SUBJECT; ?></b>
            </th>

            <th width = "55%" align = "center">
                <b><?php echo _GEN_MESSAGE; ?></b>
            </th>

            <th width = "13%" align = "center">
                <b><?php echo _GEN_ACTION; ?></b>
            </th>
        </tr>

        <?php
        $i = 1;
        //avoid calling it each time
        $smileyList = smile::getEmoticons("");

        foreach ($allMes as $message)
        {
            $i = 1 - $i;
            echo '<tr class="fb_message' . $i . '">';
            echo '<td valign="top">' . date(_DATETIME, $message->time) . '</td>';
            echo '<td valign="top">' . $message->name . '</td>';
            echo '<td valign="top"><b>' . $message->subject . '<b></td>';


            $fb_message_txt = stripslashes($message->message);
            echo '<td valign="top">' . smile::smileReplace($fb_message_txt, 0, $fbConfig->disemoticons, $smileyList) . '</td>';
            echo '<td valign="top"><input type="checkbox" name="cid[]" value="' . $message->id . '" /></td>';
            echo '</tr>';
        }
        ?>

<tr>
    <td colspan = "5" align = "center" valign = "top" style = "text-align:center">
        <input type = "hidden" name = "catid" value = "<?php echo $catid; ?>"/>

        <input type = "submit"
            align = "center"
            class = "button" name = "action" value = "<?php echo _MOD_APPROVE; ?>" border = "0"> <input type = "submit" align = "center" class = "button" name = "action" onclick = "ConfirmDelete()" value = "<?php echo _MOD_DELETE; ?>" border = "0">
    </td>
</tr>

<tr height = "10" bgcolor = "#e2e2e2">
    <td colspan = "5">
        &nbsp;
    </td>
</tr>
    </table>

    </form>

<?php
}
/**
 * delete selected messages
 * @param object database
 * @param array    cid post ids
 * @param string fbs action string
 */
function jbDeletePosts($database, $cid)
{
    if (count($cid) == 0)
        return 0;

    $ids = implode(',', $cid);
    $database->setQuery('DELETE FROM `#__fb_messages` WHERE `id` IN (' . $ids . ')');

    if ($database->query())
    {
        $database->setQuery('DELETE FROM `#__fb_messages_text` WHERE `mesid` IN (' . $ids . ')');

        if ($database->query())
            return 1;
        else
            return -1;
    }

    return 0;
}
/**
 * approve selected messages
 * @param object database
 * @param array cid post ids
 */
function jbApprovePosts($database, $cid)
{
    if (count($cid) == 0)
        return 0;

    $ret = 1;
    reset($cid);
    foreach($cid as $id) {
    	$id = (int)$id;
        $newQuery = "SELECT * FROM #__fb_messages WHERE id = " . $id . " LIMIT 1";
        $database->setQuery($newQuery);
        $msg = null;
        $database->loadObject($msg);
        if(!$msg) { continue; }
        // continue stats
        $database->setQuery("UPDATE `#__fb_messages` SET `hold`=0 WHERE `id`=".$id);
        if(!$database->query()) {
        	$ret = 0; // mark error
        }
        FBTools::modifyCategoryStats($id, $msg->parent, $msg->time, $msg->catid);
    }
    return $ret;
}
?>
