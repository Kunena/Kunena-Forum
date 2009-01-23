<?php
/**
* @version $Id: flat.php 992 2008-08-13 22:51:35Z fxstein $
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

global $fbConfig;
global $is_Moderator;

// topic emoticons
$topic_emoticons = array ();

$topic_emoticons[0] = JB_URLEMOTIONSPATH . 'default.gif';
$topic_emoticons[1] = JB_URLEMOTIONSPATH . 'exclam.gif';
$topic_emoticons[2] = JB_URLEMOTIONSPATH . 'question.gif';
$topic_emoticons[3] = JB_URLEMOTIONSPATH . 'arrow.gif';
$topic_emoticons[4] = JB_URLEMOTIONSPATH . 'love.gif';
$topic_emoticons[5] = JB_URLEMOTIONSPATH . 'grin.gif';
$topic_emoticons[6] = JB_URLEMOTIONSPATH . 'shock.gif';
$topic_emoticons[7] = JB_URLEMOTIONSPATH . 'smile.gif';

// url of current page that user will be returned to after login
if ($query_string = mosGetParam($_SERVER, 'QUERY_STRING', '')) {
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
                            <span class = "fb_title fbl"><b><?php echo _FB_THREADS_IN_FORUM; ?>:</b> <?php echo '' . $objCatInfo->name . ''; ?></span>
                        </div>
                        <!-- FORUM TOOLS -->

                        <?php
                        //(JJ) BEGIN: RECENT POSTS
                        if (file_exists(JB_ABSTMPLTPATH . '/plugin/forumtools/forumtools.php')) {
                            include (JB_ABSTMPLTPATH . '/plugin/forumtools/forumtools.php');
                            }
                        else {
                            include (JB_ABSPATH . '/template/default/plugin/forumtools/forumtools.php');
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

                foreach ($messages[0] as $leaf)
                {
                    $k = 1 - $k; //used for alternating colours
                    //$leaf->subject = htmlspecialchars($leaf->subject);
                    $leaf->name = htmlspecialchars($leaf->name);
                    $leaf->email = htmlspecialchars($leaf->email);
					$bof_avatar = "";
                ?>

                <?php
                    //(JJ) ATTACHMENTS
                    $database->setQuery("SELECT mesid FROM #__fb_attachments WHERE mesid=$leaf->id");
                    $attachmentsicon = $database->loadResult();

                    //(JJ) AVATAR
                    if ($fbConfig->avataroncat)
                    {
                        // ///////
                        //first we gather some information about this person
                        unset($CatUser);
                            $database->setQuery("SELECT * FROM #__fb_users as su"
                                                . "\nLEFT JOIN #__users as u on u.id=su.userid WHERE su.userid={$leaf->userid}");

                            $database->loadObject($CatUser);
                            $javatar = $CatUser->avatar;

                        if ($fbConfig->avatar_src == "cb")
                        {
                            $database->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id={$leaf->userid}");
                            $javatar = $database->loadResult();
                        }

                        if ($fbConfig->avatar_src == "cb" and $javatar!=false) {
                            $bof_avatar = '<img class="catavatar" src="images/comprofiler/' . $javatar . '" alt=" " />';
                            }
                        elseif ($javatar!=false) {
                            $bof_avatar = '<img class="catavatar" src="images/fbfiles/avatars/' . $javatar . '" alt=" " />';
                            }
                    // //////////
                    }
                ?>

                <?php
                    if ($st_c == 0 && $st_occured != 1 && $st_count != 0)
                    {
                    //$st_occured = 1;
                ?>

                        <tr>
                            <td class = "<?php echo $boardclass ?>contentheading fbm" id = "fb_spot" colspan = "<?php echo ($is_Moderator?"7":"6");?>" align="left">
                                <span><?php echo _FB_SPOTS; ?></span>
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
                            <span><?php echo _FB_FORUM; ?></span>
                        </td>
                    </tr>

                <?php
                    }
                ?>

                    <tr class = "<?php echo $boardclass ?><?php echo $tabclass[$k];?><?php if ($leaf->ordering==0) { } else {echo '_stickymsg'; $topicSticky=1; }?>">
                        <?php
                            if ($leaf->locked == 0)
                            {
                                if ($fbConfig->shownew && $my->id != 0 && !$leaf->moved)
                                {
                                    if (($prevCheck < $last_reply[$leaf->id]->time) && !in_array($last_reply[$leaf->id]->thread, $read_topics))
                                    {
                                        //new post(s) in topic
                                        echo '<td  class="td-1" align="center">';
                                        echo $fbIcons['unreadmessage'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['unreadmessage'] . '" border="0" alt="' . _GEN_UNREAD . '" title="' . _GEN_UNREAD . '"/>' : $fbConfig->newchar;
                                        echo '</td>';
                                    }
                                    else
                                    {
                                        //no new posts in topic
                                        echo '<td  class="td-1" align="center">';
                                        echo $fbIcons['readmessage'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['readmessage'] . '" border="0" alt="' . _GEN_NOUNREAD . '" title="' . _GEN_NOUNREAD . '"/>' : $fbConfig->newchar;
                                        echo '</td>';
                                    }
                                }
                                else
                                {
                                    //not Login
                                    echo '<td class="td-1" align="center">';
                                    echo $fbIcons['notloginmessage'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['notloginmessage'] . '" border="0" alt="' . _GEN_NOUNREAD . '" title="' . _GEN_NOUNREAD . '"/>' : $fbConfig->newchar;
                                    echo '</td>';
                                }
                            }
                            else
                            {
                                echo $fbIcons['topiclocked'] ? '<td class="td-1" align="center"><img src="' . JB_URLICONSPATH
                                         . '' . $fbIcons['topiclocked'] . '" border="0" alt="' . _GEN_LOCKED_TOPIC . '" />' : '<img src="' . JB_URLEMOTIONSPATH . 'lock.gif"  alt="' . _GEN_LOCKED_TOPIC . '" title="' . _GEN_LOCKED_TOPIC . '" /></td>';
                                $topicLocked = 1;
                            }
                        ?>

                        <?php
                            if ($leaf->moved == 0)
                            {
                        ?>

                                <td class = "td-2"  align="center">
                                    <?php echo fb_link::GetSimpleLink($id);
    echo $leaf->topic_emoticon == 0 ? '<img src="' . JB_URLEMOTIONSPATH . 'default.gif" border="0"  alt="" />' : "<img src=\"" . $topic_emoticons[$leaf->topic_emoticon] . "\" alt=\"emo\" border=\"0\" />"; ?>
                                </td>

                                <?php
                                if ($leaf->ordering == 0) {
                                    echo "<td class=\"td-3\">";
                                    }
                                else
                                {
                                    echo "<td class=\"td-3\">";
                                    echo $fbIcons['topicsticky'] ? '<img  class="stickyicon" src="' . JB_URLICONSPATH . '' . $fbIcons['topicsticky']
                                             . '" border="0" alt="' . _GEN_ISSTICKY . '" />' : '<img class="stickyicon" src="' . JB_URLEMOTIONSPATH . 'pushpin.gif"  alt="' . _GEN_ISSTICKY . '" title="' . _GEN_ISSTICKY . '" />';
                                    $topicSticky = 1;
                                }
                                ?>

                                <?php
                                //(JJ) ATTACHMENTS ICON
                                if ($attachmentsicon > 0) {
                                    echo $fbIcons['topicattach'] ? '<img  class="attachicon" src="' . JB_URLICONSPATH . ''
                                             . $fbIcons['topicattach'] . '" border="0" alt="' . _FB_ATTACH . '" />' : '<img class="attachicon" src="' . JB_URLEMOTIONSPATH . 'attachment.gif"  alt="' . _FB_ATTACH . '" title="' . _FB_ATTACH . '" />';
                                    }
                                ?>

                                <div class = "fb-topic-title-cover">
                                    <?php echo fb_link::GetThreadLink('view', $leaf->catid, $leaf->id, htmlspecialchars(stripslashes($leaf->subject)), htmlspecialchars(stripslashes($messagetext[$leaf->id])), 'follow', 'fb-topic-title fbm');?>
                                    <!--            Favourite       -->

                                    <?php
                                    if ($fbConfig->allowfavorites)
                                    {
                                        $database->setQuery("select count(*) from #__fb_favorites where thread = $leaf->id && userid = $my->id");

                                        if (intval($database->loadResult()) > 0) {
                                            echo $fbIcons['favoritestar'] ? '<img  class="favoritestar" src="' . JB_URLICONSPATH . '' . $fbIcons['favoritestar']
                                                     . '" border="0" alt="' . _FB_FAVORITE . '" />' : '<img class="favoritestar" src="' . JB_URLEMOTIONSPATH . 'favoritestar.gif"  alt="' . _FB_FAVORITE . '" title="' . _FB_FAVORITE . '" />';
                                            }
                                    }
                                    ?>
                                    <!--            /Favourite       -->

                                    <span class = "fb-topic-by fbs"> <?php echo _GEN_BY.' '.fb_link::GetProfileLink($leaf->userid, $leaf->name);?></span>

                                    <?php
                                    if ($fbConfig->shownew && $my->id != 0)
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
                                        echo _PAGE.' '.fb_link::GetThreadPageLink('view', $leaf->catid, $leaf->id, 1, $fbConfig->messages_per_page, 1);

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

                                            echo fb_link::GetThreadPageLink('view', $leaf->catid, $leaf->id, $hopPage, $fbConfig->messages_per_page, $hopPage);
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
                                $database->setQuery("SELECT `message` FROM #__fb_messages_text WHERE `mesid`='" . $leaf->id . "'");
                                $newURL = $database->loadResult();
                                // split the string and separate catid and id for proper link assembly
                                parse_str($newURL, $newURLParams);
                                ?>

                            <td class = "td-2">
                                <?php echo fb_link::GetSimpleLink($id);?>

                                <img src = "<?php echo JB_URLEMOTIONSPATH ;?>arrow.gif" alt = "emo"/>
                            </td>

                            <td class = "td-3">
                                <div class = "fb-topic-title-cover">
                                    <?php echo fb_link::GetThreadLink('view', $newURLParams['catid'], $newURLParams['id'], htmlspecialchars(stripslashes($leaf->subject)), htmlspecialchars(stripslashes($leaf->subject)), 'follow', 'fb-topic-title-cover');?>
                                </div>

                        <?php
                            }
                        ?>
                            </td>

                            <td class = "td-4 fbm" align="center">
<?php echo $leaf->moved ? _FB_TOPIC_MOVED : (int)$thread_counts[$leaf->id]; ?>
                            </td>

                            <td class = "td-5 fbm" align="center">
<?php echo $leaf->moved ? _FB_TOPIC_MOVED : (int)$hits[$leaf->id]; ?>
                            </td>

                            <td class = "td-6">
                                <div class = "fb-latest-subject-date fbs">
<?php echo $leaf->moved ? _FB_TOPIC_MOVED_LONG : date(_DATETIME, $last_reply[$leaf->id]->time); ?>

<?php
    if ($leaf->moved) {
        }
    else
    {
?>

<?php echo _GEN_BY; ?> <?php echo fb_link::GetProfileLink($last_reply[$leaf->id]->userid, $last_reply[$leaf->id]->name);?>

<?php
    }
?>

    <?php
    $tmpicon = '';
    if (!$leaf->moved)
    {
        $tmpicon = $fbIcons['latestpost'] ? '<img src="'
                 .JB_URLICONSPATH.''.$fbIcons['latestpost'].'" border="0" alt="'._SHOW_LAST.'" />':'  <img src="'.JB_URLEMOTIONSPATH.'icon_newest_reply.gif" border="0"  alt="'._SHOW_LAST.'" title="'._SHOW_LAST.'" />';
    }
    echo fb_link::GetThreadPageLink('view', $leaf->catid, $leaf->id, $threadPages, $fbConfig->messages_per_page, $tmpicon, $last_reply[$leaf->id]->id);
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
                        <script type = "text/javascript">
                            jQuery(document).ready(function()
                            {
                                jQuery('#fbBulkActions').change(function()
                                {
                                    var myList = jQuery(this);

                                    if (jQuery(myList).val() == "bulkMove")
                                    {
                                        jQuery("#FB_AvailableForums").removeAttr('disabled');
                                    }
                                    else
                                    {
                                        jQuery("#FB_AvailableForums").attr('disabled', 'disabled');
                                    }
                                });
                            });
                        </script>

                        <td colspan = "7" align = "right" class = "td-1 fbs">
                            <select name = "do" id = "fbBulkActions" class = "inputbox fbs">
                                <option value = "">&nbsp;</option>
                                <option value = "bulkDel"><?php echo _FB_DELETE_SELECTED ; ?></option>
                                <option value = "bulkMove"><?php echo _FB_MOVE_SELECTED ; ?></option>
                            </select>

                            <?php
                            FBTools::showBulkActionCats();
                            ?>

            <input type = "submit" name = "fbBulkActionsGo" class = "fbs" value = "<?php echo _FB_GO ; ?>"/>
                        </td>

                        <tr>


            <?php
            }
            ?>
            </tbody>
        </table>

        <input type = "hidden" name = "Itemid" value = "<?php echo FB_FB_ITEMID;?>"/>
        <input type = "hidden" name = "option" value = "com_fireboard"/>
        <input type = "hidden" name = "func" value = "bulkactions" />
        <input type = "hidden" name = "return" value = "<?php echo sefRelToAbs( $Breturn ); ?>" />
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
