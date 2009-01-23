<?php
/**
* @version $Id: recentposts.php 947 2008-08-11 01:56:01Z fxstein $
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

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $mosConfig_absolute_path, $mosConfig_lang, $mosConfig_live_site, $mainframe;
global $fbConfig;

$fireboard_adm_path = "$mosConfig_absolute_path/administrator/components/com_fireboard";
//Get right Language file
$fireboard_language_file = "$fireboard_adm_path/language/$mosConfig_lang.php";

if (file_exists($fireboard_language_file)) {
    require_once($fireboard_language_file);
    }
else {
    $fireboard_language_file = "$fireboard_adm_path/language/english.php";

    if (file_exists($fireboard_language_file)) {
        require_once($fireboard_language_file);
        }
    }

//Tabber check
//
$source_file = "$mosConfig_absolute_path/components/com_fireboard/template/default/plugin/recentposts/tabber.css";
$source_file = "$mosConfig_absolute_path/components/com_fireboard/template/default/plugin/recentposts/tabber.js";
$source_file = "$mosConfig_absolute_path/components/com_fireboard/template/default/plugin/recentposts/tabber-minimized.js";
$source_file = "$mosConfig_absolute_path/components/com_fireboard/template/default/plugin/recentposts/function.tabber.php";
//
$category = trim($fbConfig->latestcategory); // 2,3,4
$count = $fbConfig->latestcount;
$count_per_page = intval($fbConfig->latestcountperpage);
$show_author = $fbConfig->latestshowauthor; // 0 = none, 1= username, 2= realname
$singlesubject = $fbConfig->latestsinglesubject;
$replysubject = $fbConfig->latestreplysubject;
$subject_length = intval($fbConfig->latestsubjectlength);
$show_date = $fbConfig->latestshowdate;
$show_order_number = "1";
$tooltips_enable = "1";
$show_hits = $fbConfig->latestshowhits;

$topic_emoticons = array ();
$topic_emoticons[0] = JB_URLEMOTIONSPATH . 'default.gif';
$topic_emoticons[1] = JB_URLEMOTIONSPATH . 'exclam.gif';
$topic_emoticons[2] = JB_URLEMOTIONSPATH . 'question.gif';
$topic_emoticons[3] = JB_URLEMOTIONSPATH . 'arrow.gif';
$topic_emoticons[4] = JB_URLEMOTIONSPATH . 'love.gif';
$topic_emoticons[5] = JB_URLEMOTIONSPATH . 'grin.gif';
$topic_emoticons[6] = JB_URLEMOTIONSPATH . 'shock.gif';
$topic_emoticons[7] = JB_URLEMOTIONSPATH . 'smile.gif';
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id = "fb_recentposts" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th colspan = "5">
                <div class = "fb_title_cover fbm">
                    <span class = "fb_title fbl"><?php echo _RECENT_RECENT_POSTS; ?></span>
                </div>

                <img id = "BoxSwitch_recentposts__recentposts_tbody" class = "hideshow" src = "<?php echo JB_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
            </th>
        </tr>
    </thead>

    <tbody id = "recentposts_tbody">
        <tr>
            <td valign = "top">
                <?php

				// find messages
				$sq1 = ($category)?"AND msg2.catid in ($category)":"";
				if ($fbConfig->latestsinglesubject) {
					$sq2 = "SELECT msg1.* FROM (SELECT msg2.* FROM #__fb_messages msg2"
						. " WHERE msg2.hold = 0 AND moved=0 AND msg2.catid IN ($fbSession->allowed) $sq1 ORDER BY msg2.time"
						. (($fbConfig->latestreplysubject)?" DESC":"") . ") msg1"
						. " GROUP BY msg1.thread";
				} else {
					$sq2 = "SELECT msg2.* FROM #__fb_messages msg2"
						. " WHERE msg2.hold = 0 AND moved=0 AND msg2.catid IN ($fbSession->allowed) $sq1";
				}
				$query = " SELECT u.id, IFNULL(u.username, '"._FB_GUEST."') AS username, IFNULL(u.name,'"._FB_GUEST."') AS name,"
					. " msg.subject, msg.id AS fbid, msg.catid, from_unixtime(msg.time) AS date,"
					. " thread.hits AS hits, msg.locked, msg.topic_emoticon, msg.parent, cat.name AS catname"
					. " FROM ($sq2) msg"
					. " LEFT JOIN #__users u ON u.id = msg.userid"
					. " LEFT JOIN #__fb_categories cat ON cat.id = msg.catid"
					. " LEFT JOIN #__fb_messages thread ON thread.id = msg.thread"
					. " ORDER BY msg.time DESC LIMIT $count";
				$database->setQuery($query);
                $rows = $database->loadObjectList();
                	check_dberror("Unable to load recent messages.");

                // cycle through the returned rows displaying them in a table
                // with links to the content item
                // escaping in and out of php is now permitted
                $numitems = count($rows);

                if ($numitems > $count_per_page) {
                    include_once("$mosConfig_absolute_path/components/com_fireboard/template/default/plugin/recentposts/function.tabber.php");
                    $tabs = new my_tabs(1, 1);
                    $tabs->my_pane_start('mod_fb_last_subjects-pane');
                    $tabs->my_tab_start(1, 1);
                    }

                $i = 0;
                $tabid = 1;
                $k = 2;
                echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
                echo "<tr  class = \"fb_sth\" >";
                echo "<th class=\"th-1  " . $boardclass . "sectiontableheader  fbs\" width=\"1%\" align=\"center\" > </th>";
                echo "<th class=\"th-2  " . $boardclass . "sectiontableheader fbs\" align=\"left\" >" . _RECENT_TOPICS . "</th>";

                switch ($show_author)
                {
                    case '0': break;

                    case '1':
                        echo "<th class=\"th-3  " . $boardclass . "sectiontableheader fbs\" width=\"10%\"  align=\"center\" >" . _RECENT_AUTHOR . "</th>";

                        break;

                    case '2':
                        echo "<th class=\"th-3  " . $boardclass . "sectiontableheader fbs\" width=\"10%\" align=\"center\" >" . _RECENT_AUTHOR . "</th>";

                        break;
                }

                echo "<th class=\"th-4  " . $boardclass . "sectiontableheader fbs\"   width=\"20%\" align=\"left\" >" . _RECENT_CATEGORIES . "</th>";

                if ($show_date) {
                    echo "<th class=\"th-5  " . $boardclass . "sectiontableheader fbs\"  width=\"20%\" align=\"left\" >" . _RECENT_DATE . "</th>";
                    }

                if ($show_hits) {
                    echo "<th  class=\"th-6  " . $boardclass . "sectiontableheader fbs\"  width=\"5%\" align=\"center\" >" . _RECENT_HITS . "</th></tr>";
                    }

                if ($rows) foreach ($rows as $row) {
                    $i++;
                    $overlib = "<table>";
                    //$row->subject = htmlentities($row->subject, ENT_QUOTES);
                    $overlib .= "<tr><td valign=top>" . _GEN_TOPIC . "</td><td>$row->subject</td></tr>";
                    $row_catname = stripslashes($row->catname);
                    $row_username = stripslashes($row->username);
                    $row_date = mosFormatDate($row->date);
                    $row_lock = ($row->locked ? _CMN_YES : _CMN_NO);
                    $overlib .= "<tr><td valign=top>" . _GEN_CATEGORY . "</td><td>$row_catname</td></tr>";
                    $overlib .= "<tr><td valign=top>" . ucfirst(_GEN_BY) . "</td><td>$row_username</td></tr>";
                    $overlib .= "<tr><td valign=top>" . _GEN_DATE . "</td><td>$row_date</td></tr>";

                    if (!$row->parent) {
                        $overlib .= "<tr><td valign=top>" . _GEN_VIEWS . "</td><td>$row->hits</td></tr>";
                        }

                    $overlib .= "<tr><td valign=top>" . ucfirst(_GEN_LOCK) . "</td><td>$row_lock</td></tr>";
                    $overlib .= "</table>";
                    $link = sefRelToAbs(JB_LIVEURLREL . "&amp;func=view&amp;id=$row->fbid" . "&amp;catid=$row->catid#$row->fbid");

                    $tooltips = '';

                    if ($tooltips_enable == 1) {
                        $title = _GEN_POSTS_DISPLAY;
                        $tooltips = " onmouseout='return nd();'" . " onmouseover=\"return overlib('$overlib',CAPTION,'$title',BELOW,RIGHT);\"";
                        }

                    $k = 3 - $k;
                ?>

                    <tr class = "<?php echo $boardclass ;?>sectiontableentry<?php echo "$k"; ?>">
                <?php
                        echo "<td class=\"td-1\"  align=\"center\" >";
                        //echo '<img src="' . JB_URLEMOTIONSPATH  . 'resultset_next.gif"  border="0"   alt=" " />';
                        echo "<img src=\"" . $topic_emoticons[$row->topic_emoticon] . "\" alt=\"emo\" />";
                        echo "</td>";
                        echo "<td class=\"td-2 fbm\"  align=\"left\" >";
                        echo " <a class=\"fbrecent fbm\" href='$link' >";
                        echo substr(stripslashes($row->subject), 0, $subject_length);
                        echo "</a>";
                        echo "</td>";

                        switch ($show_author)
                        {
                            case '0': break;

                            case '1':
                                echo "<td  class=\"td-3 fbm\"  align=\"center\"  ><a href=\"";

                                echo sefRelToAbs(FB_PROFILE_LINK_SUFFIX . "" . $row->id);
                                echo "\">";
                                echo stripslashes($row->username);
                                echo "</a></td>";
                                break;

                            case '2':
                                echo "<td  class=\"td-3 fbm\"  align=\"center\"  ><a href=\"";

                                echo sefRelToAbs(FB_PROFILE_LINK_SUFFIX . "" . $row->id);
                                echo "\">";
                                echo stripslashes($row->name);
                                echo "</a></td>";
                                break;
                        }

                        echo "<td class=\"td-4 fbm\"  align=\"left\" >";
                        echo $row_catname;
                        echo "</td>";

                        if ($show_date) {
                            echo "<td  class=\"td-5 fbm\"  align=\"left\" >";
                            echo mosFormatDate(date($row->date), $date_format);
                            echo "</td>";
                            }

                        if ($show_hits) {
                            echo "<td  class=\"td-6 fbm\"  align=\"center\"  >";
                            echo $row->hits;
                            echo "</td>";
                            }

                        echo "</tr>";

                        if ($numitems > $count_per_page) {
                            if (($i % $count_per_page == 0) and ($i <> $numitems)) {
                                echo($show_order_number ? "</table>" : "</ul>");
                                $tabs->my_tab_end();
                                $tabid++;
                                $tabs->my_tab_start($tabid, $tabid);
                                $order_start = $i + 1;
                                echo(
                                    $show_order_number ? "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr  class = \"fb_sth\" ><th width=\"1%\"  align=\"center\" class=\"th-1 " . $boardclass . "sectiontableheader fbs\"> </th><th class=\"th-2 " . $boardclass . "sectiontableheader fbs\"  align=\"left\" >" . _RECENT_TOPICS. "</th><th width=\"10%\"  class=\"th-3 " . $boardclass . "sectiontableheader fbs\"   align=\"center\" >" . _RECENT_AUTHOR . "</th><th   align=\"left\"  width=\"20%\"  class=\"th-4 " . $boardclass . "sectiontableheader fbs\">" . _RECENT_CATEGORIES . "</th><th class=\"th-5 " . $boardclass . "sectiontableheader fbs\" width=\"20%\"  align=\"left\"  >" . _RECENT_DATE . "</th><th  class=\"th-6 " . $boardclass . "sectiontableheader fbs\" width=\"5%\"   align=\"center\" >" . _RECENT_HITS . "</th></tr>" : "<ul>");
                                }
                            }
                    }

                echo($show_order_number ? "</table>" : "</ul>");

                if ($numitems > $count_per_page) {
                    $tabs->my_tab_end();
                    $tabs->my_pane_end();
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
