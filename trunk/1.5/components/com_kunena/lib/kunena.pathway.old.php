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

$app =& JFactory::getApplication();
$fbConfig =& CKunenaConfig::getInstance();

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
                    $kunena_db->setQuery($query);
                    $results = $kunena_db->query() or trigger_dberror("Unable to read categories.");
                    ;
                    $parent_ids = @mysql_result($results, 0, 'parent');
                    //$cids=@mysql_result( $results, 0, 'id' );
                    $sname = "<a href='" . JRoute::_(KUNENA_LIVEURLREL . '&amp;func=showcat&amp;catid=' . $catids) . "'>" . @mysql_result($results, 0, 'name') . "</a>";

                    // write path
                    if (empty($spath)) {
                        $spath = $sname;
                    }
                    else {
                        $spath = $sname . ' � ' . $spath;
                    }

                    // next looping
                    $catids = $parent_ids;
                }

                $shome = '<a href="' . JRoute::_(KUNENA_LIVEURLREL) . '">' . _GEN_FORUMLIST . '</a> ';
                $pathNames = $shome . ' � ' . $spath . " ";
                echo $pathNames;

                //Get the category name for breadcrumb
                $kunena_db->setQuery("SELECT name,locked,review,id, description, parent from #__fb_categories where id='$catid'");
                $objCatInfo = $kunena_db->loadObject() or trigger_dberror("Unable to read from categories.");
                //Get the Category's parent category name for breadcrumb
                $kunena_db->setQuery("SELECT name,id FROM #__fb_categories WHERE id='$objCatInfo->parent'");
                $objCatParentInfo = $kunena_db->loadObject() or trigger_dberror("Unable to read from categories.");
                // set page title
		$document=& JFactory::getDocument();
                $document->setTitle(stripslashes($objCatParentInfo->name) . ' - ' . stripslashes($objCatInfo->name) . ' - ' . stripslashes($fbConfig->board_title));
                //check if this forum is locked
                $forumLocked = $objCatInfo->locked;
                //check if this forum is subject to review
                $forumReviewed = $objCatInfo->review;
                /*      echo '<a href="'.JRoute::_(KUNENA_LIVEURLREL).'">';
                      echo isset($fbIcons['forumlist']) ? '<img src="' . KUNENA_TMPLTURL . '/images/icons/'.$fbIcons['forumlist'].'" border="0" alt="'._GEN_FORUMLIST.'" > > ' : _GEN_FORUMLIST;
                      echo '</a> ';
                      if (file_exists(KUNENA_ROOT_PATH .DS. 'templates/'.$app->getTemplate().'/images/arrow.png')) {
                      echo '<img src="'.KUNENA_JLIVEURL.'/templates/'.$app->getTemplate().'/images/arrow.png" alt="" />';
                      } else {
                      echo '<img src="'.KUNENA_JLIVEURL.'/images/M_images/arrow.png" alt="" />';
                    }
                      echo ' <a href="'.JRoute::_(KUNENA_LIVEURLREL.'&amp;func=showcat&amp;catid='.$objCatParentInfo->id).'">'.$objCatParentInfo->name.'</a> ';
                      if (file_exists(KUNENA_ROOT_PATH .DS. 'templates/'.$app->getTemplate().'/images/arrow.png')) {
                      echo '<img src="'.KUNENA_JLIVEURL.'/templates/'.$app->getTemplate().'/images/arrow.png" alt="" />';
                      } else {
                      echo '<img src="'.KUNENA_JLIVEURL.'/images/M_images/arrow.png" alt="" /> ';
                    }*/
                // echo '<strong> '.$objCatInfo->name.'</strong>  ';
                if ($forumLocked)
                {
                    echo isset($fbIcons['forumlocked']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['forumlocked']
                             . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  border="0"   alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '">';
                    $lockedForum = 1;
                }
                else {
                    echo "";
                }

                if ($forumReviewed)
                {
                    echo isset($fbIcons['forummoderated']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['forummoderated']
                             . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '">';
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
