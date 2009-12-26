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
$kunena_config =& CKunenaConfig::getInstance();
$fbSession =& CKunenaSession::getInstance();
global $kunena_is_moderator;
global $messages, $forumLocked;

$kunena_db = &JFactory::getDBO();
require_once(KUNENA_PATH_LIB .DS. 'kunena.authentication.php');

//Security basics begin
//Securing passed form elements:
$catid = (int)$catid; // redundant

//resetting some things:
$moderatedForum = 0;
$forumLocked = 0;
$topicLocked = 0;
$topicSticky = 0;

//get the allowed forums and turn it into an array
$allow_forum = ($fbSession->allowed <> '')?explode(',', $fbSession->allowed):array();

if (in_array($catid, $allow_forum))
{
    $threads_per_page = $kunena_config->threads_per_page;

    if ($catid <= 0) {
        //make sure we got a valid category id
        $catid = 1;
    }

    $view = $view == "" ? $settings[current_view] : $view;
    setcookie("fboard_settings[current_view]", $view, time() + 31536000, '/');
    /*//////////////// Start selecting messages, prepare them for threading, etc... /////////////////*/
    $page = (int)$page;
    $page = $page < 1 ? 1 : $page;
    $offset = ($page - 1) * $threads_per_page;
    $row_count = $page * $threads_per_page;
    $kunena_db->setQuery("SELECT COUNT(*) FROM #__fb_messages WHERE parent='0' AND catid='{$catid}' AND hold='0'");
    $total = (int)$kunena_db->loadResult();
    	check_dberror("Unable to load messages.");
    $kunena_db->setQuery(
        "SELECT a.*, MAX(b.time) AS lastpost FROM  #__fb_messages AS a LEFT JOIN #__fb_messages AS b ON b.thread = a.thread WHERE a.parent='0' AND a.catid='{$catid}' AND a.hold='0' GROUP BY id ORDER BY ordering DESC, lastpost DESC", $offset, $threads_per_page);

    $threadids = array();
    $messages = array();
    $messages[0] = array();
    $thread_counts = array();
    foreach ($kunena_db->loadObjectList()as $message)
    {
        $threadids[] = $message->id;
        $messages[$message->parent][] = $message;
        $last_reply[$message->id] = $message;
        $hits[$message->id] = $message->hits;
        $thread_counts[$message->id] = 0;
    }

    if (count($threadids) > 0)
    {
        $idstr = @join("','", $threadids);
        $kunena_db->setQuery("SELECT a.* FROM #__fb_messages AS a WHERE thread IN ('$idstr') AND id NOT IN ('$idstr') and hold='0'");

        foreach ($kunena_db->loadObjectList()as $message)
        {
            $messages[$message->parent][] = $message;
            $thread_counts[$message->thread]++;
            $last_reply[$message->thread] = ($last_reply[$message->thread]->time < $message->time) ? $message : $last_reply[$message->thread];
        }
    }

    //get number of pending messages
    $kunena_db->setQuery("SELECT COUNT(*) FROM #__fb_messages WHERE catid='{$catid}' AND hold='1'");
    $numPending = $kunena_db->loadResult();
    	check_dberror("Unable to load messages.");
    //@rsort($messages[0]);

    //Get the category name for breadcrumb
    unset($objCatInfo, $objCatParentInfo);
    $kunena_db->setQuery("SELECT * FROM #__fb_categories WHERE id='{$catid}'");
    $objCatInfo = $kunena_db->loadObject();
    //Get the Category's parent category name for breadcrumb
    $kunena_db->setQuery("SELECT name, id FROM #__fb_categories WHERE id='{$objCatInfo->parent}'");
    $objCatParentInfo = $kunena_db->loadObject();
    //check if this forum is locked
    $forumLocked = $objCatInfo->locked;
    //check if this forum is subject to review
    $forumReviewed = $objCatInfo->review;
?>
<!-- Pathway -->
<?php
    if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_pathway.php')) {
        require_once(KUNENA_ABSTMPLTPATH . '/fb_pathway.php');
    }
    else {
        require_once(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_pathway.php');
    }
?>
<!-- / Pathway -->
<?php if($objCatInfo->headerdesc) { ?>
<div class="fb_forum-headerdesc"><?php
		$kunena_emoticons = smile::getEmoticons(0);
		$headerdesc = stripslashes(smile::smileReplace($objCatInfo->headerdesc, 0, $kunena_config->disemoticons, $kunena_emoticons));
        $headerdesc = nl2br($headerdesc);
        //wordwrap:
        $headerdesc = smile::htmlwrap($headerdesc, $kunena_config->wrap);
		echo $headerdesc;	?></div>
<?php } ?>
<?php
    //(JJ)
    if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_sub_category_list.php')) {
        include(KUNENA_ABSTMPLTPATH . '/fb_sub_category_list.php');
    }
    else {
        include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_sub_category_list.php');
    }
?>
    <!-- top nav -->

    <table border = "0" cellspacing = "0" class = "jr-topnav" cellpadding = "0">
        <tr>
            <td class = "jr-topnav-left">
                <?php
                //go to bottom
                echo '<a name="forumtop" /> ';
                echo CKunenaLink::GetSamePageAnkerLink('forumbottom', isset($fbIcons['bottomarrow']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['bottomarrow'] . '" border="0" alt="' . _GEN_GOTOBOTTOM . '" title="' . _GEN_GOTOBOTTOM . '"/>' : _GEN_GOTOBOTTOM);
                ?>

                <?php
                if ($kunena_is_moderator || ($forumLocked == 0 && ($kunena_my->id > 0 || $kunena_config->pubwrite)))
                {
                    //this user is allowed to post a new topic:
                    echo CKunenaLink::GetPostNewTopicLink($catid, isset($fbIcons['new_topic']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['new_topic'] . '" alt="' . _GEN_POST_NEW_TOPIC . '" title="' . _GEN_POST_NEW_TOPIC . '" border="0" />' : _GEN_POST_NEW_TOPIC);
                }

                echo '</td><td class="jr-topnav-right">';

                //pagination 1
                if (count($messages[0]) > 0)
                {
                    echo '<div class="jr-pagenav">'. _PAGE;

                    if (($page - 2) > 1)
                    {
                        echo ' '.CKunenaLink::GetCategoryPageLink('showcat', $catid, 1, 1, $rel='follow', $class='jr-pagenav-nb');
                    }

                    for ($i = ($page - 2) <= 0 ? 1 : ($page - 2); $i <= $page + 2 && $i <= ceil($total / $threads_per_page); $i++)
                    {
                        if ($page == $i) {
                            echo "<div class=\"jr-pagenav-nb-act\"> $i</div>";
                        }
                        else {
                        echo ' '.CKunenaLink::GetCategoryPageLink('showcat', $catid, $i, $i, $rel='follow', $class='jr-pagenav-nb');
                        }
                    }

                    if ($page + 2 < ceil($total / $threads_per_page))
                    {
                        echo "<div class=\"jr-pagenav-nb\"> ...&nbsp;</div>";

                        echo ' '.CKunenaLink::GetCategoryPageLink('showcat', $catid, ceil($total / $threads_per_page), ceil($total / $threads_per_page), $rel='follow', $class='jr-pagenav-nb');
                    }

                    echo '</div>';
                }
                ?>
            </td>
        </tr>
    </table>
    <!-- /top nav -->

    <?php
    //get all readTopics in an array
    $readTopics = "";
    $kunena_db->setQuery("SELECT readtopics FROM #__fb_sessions WHERE userid='{$kunena_my->id}'");
    $readTopics = $kunena_db->loadResult();

    if (count($readTopics) == 0) {
        $readTopics = "0";
    } //make sure at least something is in there..
    //make it into an array
    $read_topics = explode(',', $readTopics);

    if (count($messages) > 0)
    {
        if ($view == "flat")
            if (file_exists(KUNENA_ABSTMPLTPATH . '/flat.php')) {
                include(KUNENA_ABSTMPLTPATH . '/flat.php');
            }
            else {
                include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'flat.php');
            }
        else if (file_exists(KUNENA_ABSTMPLTPATH . '/thread.php')) {
            include(KUNENA_ABSTMPLTPATH . '/thread.php');
        }
        else {
            include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'thread.php');
        }
    }
    else
    {
        echo "<p align=\"center\">";
        echo '<br /><br />' . _SHOWCAT_NO_TOPICS;
        echo "</p>";
    }
    ?>
    <!-- bottom nav -->

    <table border = "0" cellspacing = "0" class = "jr-bottomnav" cellpadding = "0">
        <tr>
            <td class = "jr-topnav-left">
                <?php
                //go to top
                echo '<a name="forumbottom" />';
                echo CKunenaLink::GetSamePageAnkerLink('forumtop', isset($fbIcons['toparrow']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['toparrow'] . '" border="0" alt="' . _GEN_GOTOTOP . '" title="' . _GEN_GOTOTOP . '"/>' : _GEN_GOTOTOP);
                ?>

                <?php
                if ($kunena_is_moderator || ($forumLocked == 0 && ($kunena_my->id > 0 || $kunena_config->pubwrite)))
                {
                    //this user is allowed to post a new topic:
                    echo CKunenaLink::GetPostNewTopicLink($catid, isset($fbIcons['new_topic']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['new_topic'] . '" alt="' . _GEN_POST_NEW_TOPIC . '" title="' . _GEN_POST_NEW_TOPIC . '" border="0" />' : _GEN_POST_NEW_TOPIC);
                }

                echo '</td><td class="jr-topnav-right">';

                //pagination 2
                if (count($messages[0]) > 0)
                {
                    echo '<div class="jr-pagenav">'. _PAGE;

                    if (($page - 2) > 1)
                    {
                        echo ' '.CKunenaLink::GetCategoryPageLink('showcat', $catid, 1, 1, $rel='follow', $class='jr-pagenav-nb');
                    }

                    for ($i = ($page - 2) <= 0 ? 1 : ($page - 2); $i <= $page + 2 && $i <= ceil($total / $threads_per_page); $i++)
                    {
                        if ($page == $i) {
                            echo "<div class=\"jr-pagenav-nb-act\"> $i</div>";
                        }
                        else {
                        echo ' '.CKunenaLink::GetCategoryPageLink('showcat', $catid, $i, $i, $rel='follow', $class='jr-pagenav-nb');
                        }
                    }

                    if ($page + 2 < ceil($total / $threads_per_page))
                    {
                        echo "<div class=\"jr-pagenav-nb\"> ...&nbsp;</div>";

                        echo ' '.CKunenaLink::GetCategoryPageLink('showcat', $catid, ceil($total / $threads_per_page), ceil($total / $threads_per_page), $rel='follow', $class='jr-pagenav-nb');
                    }

                    echo '</div>';
                }
                ?>
            </td>
        </tr>
    </table>
    <!-- -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
    <table class = "fb_blocktable"  id="fb_bottomarea" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th class = "th-left" align="left" >
                    <?php
                    if ($kunena_my->id != 0)
                    {
                        echo CKunenaLink::GetCategoryLink('markthisread', $catid, isset($fbIcons['markThisForumRead']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['markThisForumRead'] . '" border="0" alt="' . _GEN_MARK_THIS_FORUM_READ . '" title="' . _GEN_MARK_THIS_FORUM_READ . '"/>' : _GEN_MARK_THIS_FORUM_READ, $rel='nofollow');
                    }
                    ?>
                    <!-- Mod List -->


                        <?php
                        //get the Moderator list for display
                        $fb_queryName = $kunena_config->username ? "username" : "name";
                        $kunena_db->setQuery("select m.userid, u.{$fb_queryName} AS username FROM #__fb_moderation AS m LEFT JOIN #__users AS u ON u.id=m.userid WHERE m.catid='{$catid}'");
                        $modslist = $kunena_db->loadObjectList();
                        	check_dberror("Unable to load moderators.");
                        ?>

                        <?php
                        if (count($modslist) > 0)
                        { ?>
                         <div class = "jr-bottomarea-modlist fbs">
                        <?php
                            echo '' . _GEN_MODERATORS . ": ";

                            foreach ($modslist as $mod) {
                                echo CKunenaLink::GetProfileLink($kunena_config, $mod->userid, ($kunena_config->username ? $mod->username : $mod->name)).'&nbsp; ';
                            } ?>
                             </div>
                            <?php
                        }
                        ?>

                <!-- /Mod List -->
                </th>

                <th  class = "th-right fbs" align="right">
                    <?php
                    //(JJ) FINISH: CAT LIST BOTTOM
                    if ($kunena_config->enableforumjump)
                        require_once (KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');
                    ?>
                </th>
            </tr>
        </thead>
        <!-- /bottom nav -->
        <tbody id = "fb-bottomarea_tbody">
            <tr class = "<?php echo $boardclass ;?>sectiontableentry1">
                <td class = "td-1 fbs" align="left">
                    <?php
                    if ($kunena_my->id != 0)
                    {
                        echo isset($fbIcons['unreadmessage']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['unreadmessage'] . '" border="0" alt="' . _GEN_UNREAD . '" title="' . _GEN_UNREAD . '" />' : $kunena_config->newchar;
                        echo ' - ' . _GEN_UNREAD . '';
                    }
                    ?>

                    <br/>

                    <?php
                    if ($kunena_my->id != 0)
                    {
                        echo isset($fbIcons['readmessage']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['readmessage'] . '" border="0" alt="' . _GEN_NOUNREAD . '" title="' . _GEN_NOUNREAD . '"/>' : $kunena_config->newchar;
                        echo ' - ' . _GEN_NOUNREAD . '';
                    }
                    ?>

                    <br/>

                <?php
                if ($moderatedForum == 1) {
                    echo isset($fbIcons['forummoderated']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['forummoderated']
                             . '" border="0" alt="' . _GEN_MODERATED . '" /> - ' . _GEN_MODERATED . '' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '" /> - ' . _GEN_MODERATED . '';
                }
                else {
                    echo "";
                }
                ?>
                </td>

                <td class = "td-2 fbs" align="left">
                    <?php
                    if ($topicLocked) {
                        echo
                            isset($fbIcons['topiclocked']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['topiclocked'] . '" border="0" alt="' . _GEN_LOCKED_TOPIC
                                . '" title="' . _GEN_LOCKED_TOPIC . '" /> - ' . _GEN_LOCKED_TOPIC . '' : '<img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif" alt="' . _GEN_LOCKED_TOPIC . '" title="' . _GEN_LOCKED_TOPIC . '" /> - ' . _GEN_LOCKED_TOPIC . '';
                    }
                    ?>

                    <br/>

                    <?php
                    if ($topicSticky) {
                        echo isset($fbIcons['topicsticky']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['topicsticky'] . '" border="0" alt="'
                                 . _GEN_ISSTICKY . '" title="' . _GEN_ISSTICKY . '" /> - ' . _GEN_ISSTICKY . '' : '<img src="' . KUNENA_URLEMOTIONSPATH . 'pushpin.gif" alt="' . _GEN_ISSTICKY . '" title="' . _GEN_ISSTICKY . '" /> - ' . _GEN_ISSTICKY . '';
                    }
                    ?>

                    <br/>

                        <?php
                        if ($forumLocked == 1)
                        {
                            echo isset($fbIcons['forumlocked']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['forumlocked']
                                     . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" /> - ' . _GEN_LOCKED_FORUM . '' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif" border="0"   alt="' . _GEN_LOCKED_FORUM . '" /> - ' . _GEN_LOCKED_FORUM . '';
                            echo '<br />';
                        }
                        else {
                            echo "";
                        }
                        ?>
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
else
{
?>
    <!-- e jump -->

    <div>
        <?php
        echo _KUNENA_NO_ACCESS;

        if ($kunena_config->enableforumjump)
        {
        ?>

            <form id = "jumpto" name = "jumpto" method = "post" target = "_self" action = "index.php" onsubmit = "if(document.jumpto.catid.value &lt; 2){return false;}">
                <div style = "width: 100%" align = "right">
                    <input type = "hidden" name = "Itemid" value = "<?php echo KUNENA_COMPONENT_ITEMID;?>"/>

                    <input type = "hidden" name = "option" value = "com_kunena"/>

                    <input type = "hidden" name = "func" value = "showcat"/>

                    <input type = "submit" name = "Go"  class="fbs" value = "<?php echo _KUNENA_GO; ?>"/>

                    <select name = "catid" class="fbs" onchange = "if(this.options[this.selectedIndex].value > 0){ forms['jumpto'].submit() }">
                        <option name = "" SELECTED><?php echo _GEN_FORUM_JUMP; ?></option>

                        <?php
                        showChildren(0, "", $allow_forum);
                        ?>
                    </select>
                </div>
            </form>
    </div>
    <!-- /e jump -->

<?php
        }
}

function showChildren($category, $prefix = "", &$allow_forum)
{
    $kunena_db = &JFactory::getDBO();
    $kunena_db->setQuery("SELECT id, name, parent FROM #__fb_categories WHERE parent='{$category}' AND published='1' ORDER BY ordering");
    $forums = $kunena_db->loadObjectList();
    	check_dberror("Unable to load categories.");

    foreach ($forums as $forum)
    {
        if (in_array($forum->id, $allow_forum)) {
            echo("<option value=\"{$forum->id}\">$prefix {$forum->name}</option>");
        }

        showChildren($forum->id, $prefix . "---", $allow_forum);
    }
}
?>
