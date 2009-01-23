<?php
/**
* @version $Id: fb_pathway_old.php 831 2008-07-15 04:14:59Z fxstein $
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
?>
<!-- Pathway -->

<table border = "0" cellspacing = "0" cellpadding = "0" width = "100%" class = "contentpane">
    <tr>
        <td>
            <div>
                <?php
                /*  danial */
                $catids = intval($catid);
                $parent_ids = 1000;

                while ($parent_ids)
                {
                    $query = "select * from #__fb_categories where id=$catids and published=1";
                    $database->setQuery($query);
                    $results = $database->query() or trigger_dberror("Unable to read categories.");
                    ;
                    $parent_ids = @mysql_result($results, 0, 'parent');
                    //$cids=@mysql_result( $results, 0, 'id' );
                    $sname = "<a href='" . sefRelToAbs(JB_LIVEURLREL . '&amp;func=showcat&amp;catid=' . $catids) . "'>" . @mysql_result($results, 0, 'name') . "</a>";

                    // write path
                    if (empty($spath)) {
                        $spath = $sname;
                    }
                    else {
                        $spath = $sname . ' » ' . $spath;
                    }

                    // next looping
                    $catids = $parent_ids;
                }

                $shome = '<a href="' . sefRelToAbs(JB_LIVEURLREL) . '">' . _GEN_FORUMLIST . '</a> ';
                $pathNames = $shome . ' » ' . $spath . " ";
                echo $pathNames;

                //Get the category name for breadcrumb
                $database->setQuery("SELECT name,locked,review,id, description, parent from #__fb_categories where id='$catid'");
                $database->loadObject($objCatInfo) or trigger_dberror("Unable to read from categories.");
                //Get the Category's parent category name for breadcrumb
                $database->setQuery("SELECT name,id FROM #__fb_categories WHERE id='$objCatInfo->parent'");
                $database->loadObject($objCatParentInfo) or trigger_dberror("Unable to read from categories.");
                // set page title
                $mainframe->setPageTitle($objCatParentInfo->name . ' - ' . $objCatInfo->name . ' - ' . $fbConfig->board_title);
                //check if this forum is locked
                $forumLocked = $objCatInfo->locked;
                //check if this forum is subject to review
                $forumReviewed = $objCatInfo->review;
                /*      echo '<a href="'.sefRelToAbs(JB_LIVEURLREL).'">';
                      echo $fbIcons['forumlist'] ? '<img src="' . JB_TMPLTURL . '/images/icons/'.$fbIcons['forumlist'].'" border="0" alt="'._GEN_FORUMLIST.'" > > ' : _GEN_FORUMLIST;
                      echo '</a> ';
                      if (file_exists($mosConfig_absolute_path.'/templates/'.$mainframe->getTemplate().'/images/arrow.png')) {
                      echo '<img src="'.JB_JLIVEURL.'/templates/'.$mainframe->getTemplate().'/images/arrow.png" alt="" />';
                      } else {
                      echo '<img src="'.JB_JLIVEURL.'/images/M_images/arrow.png" alt="" />';
                    }
                      echo ' <a href="'.sefRelToAbs(JB_LIVEURLREL.'&amp;func=showcat&amp;catid='.$objCatParentInfo->id).'">'.$objCatParentInfo->name.'</a> ';
                      if (file_exists($mosConfig_absolute_path.'/templates/'.$mainframe->getTemplate().'/images/arrow.png')) {
                      echo '<img src="'.JB_JLIVEURL.'/templates/'.$mainframe->getTemplate().'/images/arrow.png" alt="" />';
                      } else {
                      echo '<img src="'.JB_JLIVEURL.'/images/M_images/arrow.png" alt="" /> ';
                    }*/
                // echo '<strong> '.$objCatInfo->name.'</strong>  ';
                if ($forumLocked)
                {
                    echo $fbIcons['forumlocked'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['forumlocked']
                             . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '  <img src="' . JB_URLEMOTIONSPATH . 'lock.gif"  border="0"   alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '">';
                    $lockedForum = 1;
                }
                else {
                    echo "";
                }

                if ($forumReviewed)
                {
                    echo $fbIcons['forummoderated'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['forummoderated']
                             . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : '  <img src="' . JB_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '">';
                    $moderatedForum = 1;
                }
                else {
                    echo "";
                }
                ?>
            </div>
        </td>
    </tr>
</table>
<!-- / Pathway -->