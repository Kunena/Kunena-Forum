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

$fbConfig =& CKunenaConfig::getInstance();
global $is_Moderator;

// topic emoticons
$topic_emoticons = array ();

$topic_emoticons[0] = KUNENA_URLEMOTIONSPATH . 'default.gif';
$topic_emoticons[1] = KUNENA_URLEMOTIONSPATH . 'exclam.gif';
$topic_emoticons[2] = KUNENA_URLEMOTIONSPATH . 'question.gif';
$topic_emoticons[3] = KUNENA_URLEMOTIONSPATH . 'arrow.gif';
$topic_emoticons[4] = KUNENA_URLEMOTIONSPATH . 'love.gif';
$topic_emoticons[5] = KUNENA_URLEMOTIONSPATH . 'grin.gif';
$topic_emoticons[6] = KUNENA_URLEMOTIONSPATH . 'shock.gif';
$topic_emoticons[7] = KUNENA_URLEMOTIONSPATH . 'smile.gif';

// url of current page that user will be returned to after login
if ($query_string = JRequest::getVar('QUERY_STRING', '', 'SERVER')) {
    $Breturn = 'index.php?' . $query_string;
    }
else {
    $Breturn = 'index.php';
    }

$Breturn = str_replace('&', '&amp;', $Breturn);

$tabclass = array
(
    "sectiontableentry1",
    "sectiontableentry2"
);

$st_count = 0;

if (count($messages[0]) > 0)
{
    foreach ($messages[0] as $leafa)
    {
        if ($leafa->ordering > 0) {
            $st_count++;
            }
    }
}

if (count($messages[0]) > 0)
{
?>
    <div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
    <form action = "index.php" method = "post" name = "fbBulkActionForm">

        <table class = "fb_blocktable<?php echo $objCatInfo->class_sfx; ?>" id = "fb_flattable" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th colspan = "<?php echo ($is_Moderator?"7":"6");?>">
                        <div class = "fb_title_cover fbm">
                            <span class = "fb_title fbl"><b><?php echo _KUNENA_THREADS_IN_FORUM; ?>:</b> <?php echo '' . kunena_htmlspecialchars(stripslashes($objCatInfo->name)) . ''; ?></span>
                        </div>
                        <!-- FORUM TOOLS -->

                        <?php
                        //(JJ) BEGIN: RECENT POSTS
                        if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/forumtools/forumtools.php')) {
                            include (KUNENA_ABSTMPLTPATH . '/plugin/forumtools/forumtools.php');
                            }
                        else {
                            include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/forumtools/forumtools.php');
                            }
                        //(JJ) FINISH: RECENT POSTS
                        ?>
                    <!-- /FORUM TOOLS -->
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr  class = "fb_sth fbs ">
                    <th class = "th-1 <?php echo $boardclass ?>sectiontableheader" width="1%">&nbsp;</th>
                    <th class = "th-2 <?php echo $boardclass ?>sectiontableheader" width="1%">&nbsp;</th>
                    <th class = "th-3 <?php echo $boardclass ?>sectiontableheader" align="left"><?php echo _GEN_TOPICS; ?></th>
                    <th class = "th-4 <?php echo $boardclass ?>sectiontableheader" width="5%" align="center"><?php echo _GEN_REPLIES; ?></th>
                    <th class = "th-5 <?php echo $boardclass ?>sectiontableheader"  width="5%" align="center"><?php echo _GEN_HITS; ?></th>
                    <th class = "th-6 <?php echo $boardclass ?>sectiontableheader" width="20%" align="left"><?php echo _GEN_LAST_POST; ?></th>

                    <?php
                    if ($is_Moderator)
                    {
                    ?>
                        <th class = "th-7 <?php echo $boardclass ?>sectiontableheader" width="1%" align="center">[X]</th>
                    <?php
                    }
                    ?>
                </tr>

                <?php
                $k = 0;
                $st_c = 0;

                $st_occured = 0;
                foreach ($messages[0] as $leaf)
                {
                    $k = 1 - $k; //used for alternating colours
                    //$leaf->subject = kunena_htmlspecialchars($leaf->subject);
                    $leaf->name = kunena_htmlspecialchars($leaf->name);
                    $leaf->email = kunena_htmlspecialchars($leaf->email);
					$bof_avatar = "";
                ?>

                <?php
                    //(JJ) ATTACHMENTS
                    $kunena_db->setQuery("SELECT mesid FROM #__fb_attachments WHERE mesid='{$leaf->id}'");
                    $attachmentsicon = $kunena_db->loadResult();

                    //(JJ) AVATAR
                    if ($fbConfig->avataroncat)
                    {
							$bof_avatar = $kunenaProfile->showAvatar($last_reply[$leaf->id]->userid, 'catavatar');
					}
				?>

                <?php
                    if ($st_c == 0 && $st_occured != 1 && $st_count != 0)
                    {
                    //$st_occured = 1;
                ?>

                        <tr>
                            <td class = "<?php echo $boardclass ?>contentheading fbm" id = "fb_spot" colspan = "<?php echo ($is_Moderator?"7":"6");?>" align="left">
                                <span><?php echo _KUNENA_SPOTS; ?></span>
                            </td>
                        </tr>

                <?php
                    }

                    if ($st_c == $st_count && $st_occured != 1 && $st_count != 0)
                    {
                        $st_occured = 1;
                        $k = 0;
                ?>

                    <tr>
                        <td class = "<?php echo $boardclass ?>contentheading fbm" id = "fb_fspot" colspan = "<?php echo ($is_Moderator?"7":"6");?>" align="left">
                            <span><?php echo _KUNENA_FORUM; ?></span>
                        </td>
                    </tr>

                <?php
                    }
                ?>

                    <tr class = "<?php echo $boardclass ?><?php echo $tabclass[$k];?><?php if ($leaf->ordering==0) { } else {echo '_stickymsg'; $topicSticky=1; }?>">
                        <?php
                            if ($leaf->locked == 0)
                            {
                                if ($fbConfig->shownew && $kunena_my->id != 0 && !$leaf->moved)
                                {
                                    if (($prevCheck < $last_reply[$leaf->id]->time) && !in_array($last_reply[$leaf->id]->thread, $read_topics))
                                    {
                                        //new post(s) in topic
                                        echo '<td  class="td-1" align="center">';
                                        echo isset($fbIcons['unreadmessage']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['unreadmessage'] . '" border="0" alt="' . _GEN_UNREAD . '" title="' . _GEN_UNREAD . '"/>' : stripslashes($fbConfig->newchar);
                                        echo '</td>';
                                    }
                                    else
                                    {
                                        //no new posts in topic
                                        echo '<td  class="td-1" align="center">';
                                        echo isset($fbIcons['readmessage']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['readmessage'] . '" border="0" alt="' . _GEN_NOUNREAD . '" title="' . _GEN_NOUNREAD . '"/>' : stripslashes($fbConfig->newchar);
                                        echo '</td>';
                                    }
                                }
                                else
                                {
                                    //not Login
                                    echo '<td class="td-1" align="center">';
                                    echo isset($fbIcons['notloginmessage']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['notloginmessage'] . '" border="0" alt="' . _GEN_NOUNREAD . '" title="' . _GEN_NOUNREAD . '"/>' : stripslashes($fbConfig->newchar);
                                    echo '</td>';
                                }
                            }
                            else
                            {
                                echo isset($fbIcons['topiclocked']) ? '<td class="td-1" align="center"><img src="' . KUNENA_URLICONSPATH . $fbIcons['topiclocked'] . '" border="0" alt="' . _GEN_LOCKED_TOPIC . '" />' : '<img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  alt="' . _GEN_LOCKED_TOPIC . '" title="' . _GEN_LOCKED_TOPIC . '" /></td>';
                                $topicLocked = 1;
                            }
                        ?>

                        <?php
                            if ($leaf->moved == 0)
                            {
                        ?>

                                <td class = "td-2"  align="center">
                                    <?php echo CKunenaLink::GetSimpleLink($id);
    echo $leaf->topic_emoticon == 0 ? '<img src="' . KUNENA_URLEMOTIONSPATH . 'default.gif" border="0"  alt="" />' : "<img src=\"" . $topic_emoticons[$leaf->topic_emoticon] . "\" alt=\"emo\" border=\"0\" />"; ?>
                                </td>

                                <?php
                                if ($leaf->ordering == 0) {
                                    echo "<td class=\"td-3\">";
                                    }
                                else
                                {
                                    echo "<td class=\"td-3\">";
                                    echo isset($fbIcons['topicsticky']) ? '<img  class="stickyicon" src="' . KUNENA_URLICONSPATH . $fbIcons['topicsticky']
                                             . '" border="0" alt="' . _GEN_ISSTICKY . '" />' : '<img class="stickyicon" src="' . KUNENA_URLEMOTIONSPATH . 'pushpin.gif"  alt="' . _GEN_ISSTICKY . '" title="' . _GEN_ISSTICKY . '" />';
                                    $topicSticky = 1;
                                }
                                ?>

                                <?php
                                //(JJ) ATTACHMENTS ICON
                                if ($attachmentsicon > 0) {
                                    echo isset($fbIcons['topicattach']) ? '<img  class="attachicon" src="' . KUNENA_URLICONSPATH
                                             . $fbIcons['topicattach'] . '" border="0" alt="' . _KUNENA_ATTACH . '" />' : '<img class="attachicon" src="' . KUNENA_URLEMOTIONSPATH . 'attachment.gif"  alt="' . _KUNENA_ATTACH . '" title="' . _KUNENA_ATTACH . '" />';
                                    }
                                ?>

                                <div class = "fb-topic-title-cover">
                                    <?php echo CKunenaLink::GetThreadLink('view', $leaf->catid, $leaf->id, kunena_htmlspecialchars(stripslashes($leaf->subject)), '', 'follow', 'fb-topic-title fbm');?>
                                    <!--            Favourite       -->

                                    <?php
                                    if ($fbConfig->allowfavorites)
                                    {
                                        $kunena_db->setQuery("SELECT COUNT(*) FROM #__fb_favorites WHERE thread='{$leaf->id}' && userid='{$kunena_my->id}'");

                                        if (intval($kunena_db->loadResult()) > 0) {
                                            echo isset($fbIcons['favoritestar']) ? '<img  class="favoritestar" src="' . KUNENA_URLICONSPATH . $fbIcons['favoritestar']
                                                     . '" border="0" alt="' . _KUNENA_FAVORITE . '" />' : '<img class="favoritestar" src="' . KUNENA_URLEMOTIONSPATH . 'favoritestar.gif"  alt="' . _KUNENA_FAVORITE . '" title="' . _KUNENA_FAVORITE . '" />';
                                            }
                                    }
                                    ?>
                                    <!--            /Favourite       -->

                                    <span class = "fb-topic-by fbs"> <?php echo _GEN_BY.' '.CKunenaLink::GetProfileLink($fbConfig, $leaf->userid, kunena_htmlspecialchars(stripslashes($leaf->name)));?></span>

                                    <?php
                                    if ($fbConfig->shownew && $kunena_my->id != 0)
                                    {
                                        if (($prevCheck < $last_reply[$leaf->id]->time) && !in_array($last_reply[$leaf->id]->thread, $read_topics)) {
                                            //new post(s) in topic
                                            echo '<sup><span class="newchar">&nbsp;(' . $fbConfig->newchar . ")</span></sup>";
                                            }
                                    }
                                    ?>

                                    <?php
                                    // (JJ) AVATAR
                                    if ($fbConfig->avataroncat) {
                                        echo $bof_avatar;
                                        }
                                    ?>

                                    <?php
                                    $totalMessages = $thread_counts[$leaf->id];
                                    $threadPages = 1;

                                    if ($totalMessages > $fbConfig->messages_per_page)
                                    {
                                        $threadPages = ceil($totalMessages / $fbConfig->messages_per_page);
                                        echo ("<span class=\"jr-showcat-perpage\">[");
                                        echo _PAGE.' '.CKunenaLink::GetThreadPageLink($fbConfig, 'view', $leaf->catid, $leaf->id, 1, $fbConfig->messages_per_page, 1);

                                        if ($threadPages > 3)
                                        {
                                            echo ("...");
                                            $startPage = $threadPages - 2;
                                        }
                                        else
                                        {
                                            echo (",");
                                            $startPage = 2;
                                        }

                                        $noComma = true;

                                        for ($hopPage = $startPage; $hopPage <= $threadPages; $hopPage++)
                                        {
                                            if ($noComma) {
                                                $noComma = false;
                                                }
                                            else {
                                                echo (",");
                                                }

                                            echo CKunenaLink::GetThreadPageLink($fbConfig, 'view', $leaf->catid, $leaf->id, $hopPage, $fbConfig->messages_per_page, $hopPage);
                                        }

                                        echo ("]</span>");
                                    }
                                    ?>
                                </div>

                                <?php
                            }
                            else
                            {
                                //this thread has been moved, get the new location
                                $newURL = ""; //init
                                $kunena_db->setQuery("SELECT message, mesid FROM #__fb_messages_text WHERE mesid='{$leaf->id}'");
                                $newURL = $kunena_db->loadResult();
                                // split the string and separate catid and id for proper link assembly
                                parse_str($newURL, $newURLParams);
                                ?>

                            <td class = "td-2">
                                <?php echo CKunenaLink::GetSimpleLink($id);?>

                                <img src = "<?php echo KUNENA_URLEMOTIONSPATH ;?>arrow.gif" alt = "emo"/>
                            </td>

                            <td class = "td-3">
                                <div class = "fb-topic-title-cover">
                                    <?php echo CKunenaLink::GetThreadLink('view', $newURLParams['catid'], $newURLParams['id'], kunena_htmlspecialchars(stripslashes($leaf->subject)), kunena_htmlspecialchars(stripslashes($leaf->subject)), 'follow', 'fb-topic-title-cover');?>
                                </div>

                        <?php
                            }
                        ?>
                            </td>

                            <td class = "td-4 fbm" align="center">
<?php echo $leaf->moved ? _KUNENA_TOPIC_MOVED : (int)$thread_counts[$leaf->id]; ?>
                            </td>

                            <td class = "td-5 fbm" align="center">
<?php echo $leaf->moved ? _KUNENA_TOPIC_MOVED : (int)$hits[$leaf->id]; ?>
                            </td>

                            <td class = "td-6">
                                <div class = "fb-latest-subject-date fbs">
<?php echo $leaf->moved ? _KUNENA_TOPIC_MOVED_LONG : date(_DATETIME, $last_reply[$leaf->id]->time); ?>

<?php
    if ($leaf->moved) {
        }
    else
    {
?>

<?php echo _GEN_BY; ?> <?php echo CKunenaLink::GetProfileLink($fbConfig, $last_reply[$leaf->id]->userid, kunena_htmlspecialchars(stripslashes($last_reply[$leaf->id]->name)));?>

<?php
    }
?>

    <?php
    $tmpicon = '';
    if (!$leaf->moved)
    {
        $tmpicon = isset($fbIcons['latestpost']) ? '<img src="'
                 .KUNENA_URLICONSPATH.$fbIcons['latestpost'].'" border="0" alt="'._SHOW_LAST.'" />':'  <img src="'.KUNENA_URLEMOTIONSPATH.'icon_newest_reply.gif" border="0"  alt="'._SHOW_LAST.'" title="'._SHOW_LAST.'" />';
    }
    echo CKunenaLink::GetThreadPageLink($fbConfig, 'view', $leaf->catid, $leaf->id, $threadPages, $fbConfig->messages_per_page, $tmpicon, $last_reply[$leaf->id]->id);
    ?>
                                </div>
                            </td>

                            <?php
                            if ($is_Moderator)
                            {
                            ?>

                                <td class = "td-7" align="center">
                                    <input type = "checkbox" name = "fbDelete[<?php echo $leaf->id?>]" value = "1"/>
                                </td>

                            <?php
                            }
                            ?>
                    </tr>

                <?php
                $st_c++;
                }
                ?>


            <?php

            if ($is_Moderator)
            {
            ?>


                    <tr class = "<?php echo $boardclass ?>sectiontableentry1">
                        <td colspan = "7" align = "right" class = "td-1 fbs">

                        <script type = "text/javascript">
                            jQuery(document).ready(function()
                            {
                                jQuery('#fbBulkActions').change(function()
                                {
                                    var myList = jQuery(this);

                                    if (jQuery(myList).val() == "bulkMove")
                                    {
                                        jQuery("#KUNENA_AvailableForums").removeAttr('disabled');
                                    }
                                    else
                                    {
                                        jQuery("#KUNENA_AvailableForums").attr('disabled', 'disabled');
                                    }
                                });
                            });
                        </script>

                            <select name = "do" id = "fbBulkActions" class = "inputbox fbs">
                                <option value = "">&nbsp;</option>
                                <option value = "bulkDel"><?php echo _KUNENA_DELETE_SELECTED ; ?></option>
                                <option value = "bulkMove"><?php echo _KUNENA_MOVE_SELECTED ; ?></option>
                            </select>

                            <?php
                            CKunenaTools::showBulkActionCats();
                            ?>

            <input type = "submit" name = "fbBulkActionsGo" class = "fbs" value = "<?php echo _KUNENA_GO ; ?>"/>
                        </td>

                        </tr>


            <?php
            }
            ?>
            </tbody>
        </table>

        <input type = "hidden" name = "Itemid" value = "<?php echo getKunenaItemid();?>"/>
        <input type = "hidden" name = "option" value = "com_kunena"/>
        <input type = "hidden" name = "func" value = "bulkactions" />
        <input type = "hidden" name = "return" value = "<?php echo JRoute::_( $Breturn ); ?>" />
    </form>
</div>
</div>
</div>
</div>
</div>
<?php
}
else {
    echo "<p align=\"center\">" . _VIEW_NO_POSTS . "</p>";
}
?>
