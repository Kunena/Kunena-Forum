<?php
/**
* @version $Id: thread.php 951 2009-08-15 01:45:15Z mahagr $
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
// arrows and lines
$join = '<img src="' . KUNENA_URLIMAGESPATH . 'tree-join.gif" width="12" height="18" alt="thread link" />';
$end = '<img src="' . KUNENA_URLIMAGESPATH . 'tree-end.gif" width="12" height="18" alt="thread link" />';
$blank = '<img src="' . KUNENA_URLIMAGESPATH . 'tree-blank.gif" width="12" height="18" alt="thread link" />';
$vert = '<img src="' . KUNENA_URLIMAGESPATH . 'tree-vert.gif" width="12" height="18" alt="thread link" />';
$loc_emoticons = KUNENA_URLEMOTIONSPATH;

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

function thread_flat(&$tree, &$leaves, $branchid = 0, $level = 0)
{
    foreach ($leaves[$branchid] as $leaf)
    {
        $leaf->level = $level;
        $tree[] = $leaf;
        $GLOBALS['KUNENA_c']++;

        if (is_array($leaves[$leaf->id]))
            thread_flat($tree, $leaves, $leaf->id, $level + 1);
    }

    return $tree;
}

$GLOBALS['KUNENA_c'] = 0;
$tree = thread_flat($tree, $messages);
?>
<div id="fb_threadview">
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table width = "100%" border = "0" cellspacing = "0" cellpadding = "0" class = "fb_blocktable" >
    <thead>
        <tr  class = "fb_sth fbs ">
        <?php
        if ($fbConfig->shownew && $kunena_my->id != 0) { ?>

       <th width="10" class = "th-1 <?php echo $boardclass ?>sectiontableheader">&nbsp;</th>
            <?php
        }
        ?>
        <th class = "th-2 <?php echo $boardclass ?>sectiontableheader" align = "center" width = "5">&nbsp; </th>
        <th class = "th-3 <?php echo $boardclass ?>sectiontableheader" align = "center" width = "5">&nbsp; </th>
        <?php
        if ($kunena_my->id == 0) {
            echo '<td class="sectiontableheader" width="5" align="center">&nbsp;</td>';
        }
        ?>
        <th class = "th-3 <?php echo $boardclass ?>sectiontableheader"  width = "60%" align = "center"><?php echo _GEN_TOPICS; ?></th>
        <th class = "th-3 <?php echo $boardclass ?>sectiontableheader"  width = "15%" align = "center"><?php echo _GEN_AUTHOR; ?></th>

        <th class = "th-3 <?php echo $boardclass ?>sectiontableheader"  align = "center"><?php echo _GEN_DATE; ?></th>
    </tr>
</thead>
<tbody>
    <?php
    foreach ($tree as $leaf)
    {
        $leaf->name = kunena_htmlspecialchars($leaf->name);
        $leaf->subject = kunena_htmlspecialchars($leaf->subject);
        $leaf->email = kunena_htmlspecialchars($leaf->email);
        //get all html out of the subject & email & name before posting:
    ?>

        <tr class="fb_threadview_row">
            <?php
            if ($fbConfig->shownew && $kunena_my->id != 0 && !$leaf->moved)
            {
                if (($prevCheck < ($leaf->time)) && (sizeof($read_topics) == 0) || !in_array($leaf->thread, $read_topics))
                {
                    //new post
                    echo '<td width="1%" class="fb_new">';
                   // echo isset($fbIcons['unreadmessage']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['unreadmessage'] . '" border="0" alt="' . _GEN_UNREAD . '" title="' . _GEN_UNREAD . '"/>' : $fbConfig->newchar;
                    echo '</td>';
                }
                else
                {
                    //not new posts
                    echo '<td width="1%" class="fb_notnew">';
                   // echo isset($fbIcons['readmessage']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['readmessage'] . '" border="0" alt="' . _GEN_NOUNREAD . '" title="' . _GEN_NOUNREAD . '"/>' : $fbConfig->newchar;
                    echo '</td>';
                }
            }
            else {
                echo '<td>&nbsp;</td>';
            }
            ?>

            <td align = "center" width = "5"<?php echo $leaf->id==$id?" class=\"".$boardclass."sectiontableentry2\">":">";
                  if ($leaf->ordering==0)
                  {
                     if($leaf->locked==0)
                     {
                     //  echo "&nbsp;";
                     }
                     else
                     {
                      //  echo isset($fbIcons['topiclocked']) ? '<img src="' . KUNENA_URLICONSPATH.$fbIcons['topiclocked'].'" border="0" alt="'._GEN_LOCKED_TOPIC.'" title="'._GEN_LOCKED_TOPIC.'" />' : '<img src="'.KUNENA_URLEMOTIONSPATH.'lock.gif" width="15" height="15" alt="'._GEN_LOCKED_TOPIC.'" />';
                        $topicLocked=1;
                     }
                  }
                  else
                  {
                   //  echo isset($fbIcons['topicsticky']) ? '<img src="' . KUNENA_URLICONSPATH.$fbIcons['topicsticky'].'" border="0" alt="'._GEN_ISSTICKY.'" title="'._GEN_ISSTICKY.'" />' : '<img src="'.KUNENA_URLEMOTIONSPATH.'pushpin.gif" width="15" height="15" alt="'._GEN_ISSTICKY.'" />';
                     $topicSticky=1;
                  }
                  ?></td>
                <td align = "center" width = "5"<?php echo $leaf->id==$id?" class=\"".$boardclass."sectiontableentry2\"":"";?>>
<?php // echo $leaf->topic_emoticon == 0 ? '<img src="' . KUNENA_URLIMAGESPATH . 'tree-blank.gif"  alt="thread link" />' : "<img src=\"" . $topic_emoticons[$leaf->topic_emoticon] . "\" alt=\"emo\" />"; ?>
                </td>

                <td<?php echo $leaf->id == $id ? " class=\"".$boardclass."sectiontableentry2\"" : ""; ?>>
        <table border = "0" cellspacing = "0" cellpadding = "0">
            <tr>
                <td<?php echo $leaf->id == $id ? " class=\"".$boardclass."sectiontableentry2\"" : ""; ?>>
            <?php
            $array[$leaf->level + 1] = count($messages[$leaf->id]);
            $array[$leaf->level]--;

            for ($i = 0; $i < $leaf->level; $i++)
            {
                if ($array[$i] > 0)
                    echo ($vert);

                elseif ($array[$i] == 0)
                    echo ($blank);
            }

            if ($array[$leaf->level] > 0)
                echo ($join);

            elseif ($array[$leaf->level] == 0 && $leaf->parent != 0)
                echo ($end);
            //else echo($blank);
            ?>
                </td>

                    <?php
                     $newURL = "index.php?option=com_kunena".KUNENA_COMPONENT_ITEMID_SUFFIX."&amp;func=view&amp;catid=". $catid."&amp;id=";

                    if ($leaf->moved)
                    {
                        $kunena_db->setQuery("SELECT `mesid` FROM #__fb_messages_text WHERE `mesid`=" . $leaf->id);
                        $newURL .= $kunena_db->loadResult();
                    }
                    else
                        $newURL .= '&amp;id=' . $leaf->id . $viewstr . '&amp;catid=' . $catid;

                    $newURL = JRoute::_($newURL);
                    ?>

                    <td<?php echo $leaf->id == $id ? " class=\"".$boardclass."sectiontableentry2\"" : ""; ?>>
    <a class="fb_threadview_link"  href = "<?php echo $newURL; ?>"><?php echo stripslashes($leaf->subject); ?>
<!--            Favourite       -->
<?php
if ($fbConfig->allowfavorites)
{
    $kunena_db->setQuery("select count(*) from #__fb_favorites where thread = $leaf->id && userid = $kunena_my->id");

    if (intval($kunena_db->loadResult()) > 0) {
        echo isset($fbIcons['favoritestar']) ? '<img  class="favoritestar" src="' . KUNENA_URLICONSPATH . $fbIcons['favoritestar'] . '" border="0" alt="' . _KUNENA_FAVORITE . '" />' : '<img class="favoritestar" src="' . KUNENA_URLEMOTIONSPATH . 'favoritestar.gif"  alt="' . _KUNENA_FAVORITE . '" title="' . _KUNENA_FAVORITE . '" />';
    }
}
?>

    <!--            /Favourite       -->

    </a>
</td>
            </tr>
        </table>
    </td>

    <td align = "center" <?php echo $leaf->id==$id?' class="'.$boardclass.'sectiontableentry2"':'';?>>
        <small><?php echo $leaf->email != "" && $kunena_my->id > 0 && $fbConfig->showemail ? '<a href="mailto:' . stripslashes($leaf->email) . '">' . stripslashes($leaf->name) . '</a>' : stripslashes($leaf->name); ?></small>
    </td>

    <td align = "center" <?php echo $leaf->id==$id?' class=""'.$boardclass.'sectiontableentry2"':'';?>>
        <small><?php echo $leaf->moved ? date(_DATETIME, $leaf->time) : date(_DATETIME, $leaf->time); ?></small>
    </td>
        </tr>

    <?php
    }
    ?>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>