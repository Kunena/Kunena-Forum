<?php
/**
* @version $Id:kunena_layout.php 884 2009-06-16 03:48:56Z fxstein $
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

defined( '_JEXEC' ) or die('Restricted access');

// ################################################################
/**
 *  Function to print the pathway
 *  @param object    database object
 *  @param object    category object
 *  @param int        the post id
 *  @param boolean    set title
 */
function KUNENA_print_pathway(&$kunena_db, $obj_kunena_cat, $bool_set_title, $obj_post = 0) {
    echo '<div class="kunena_pathway">' . kunena_get_pathway($kunena_db, $obj_kunena_cat, $bool_set_title, $obj_post) . '</div>';
}
/**
 *  Function to print the pathway
 *  @param object    database object
 *  @param object    category object
 *  @param int        the post id
 *  @param boolean    set title
 */
function KUNENA_get_pathway(&$kunena_db, $obj_kunena_cat, $bool_set_title, $obj_post = 0)
{
    global $kunenaIcons;

	$document=& JFactory::getDocument();
	$kunenaConfig =& CKunenaConfig::getInstance();
    //Get the Category's parent category name for breadcrumb
    $kunena_db->setQuery("SELECT name, id FROM #__kunena_categories WHERE id='".$obj_kunena_cat->getParent())."'";
    $objCatParentInfo = $kunena_db->loadObject();
    	check_dberror("Unable to load categories.");
    //get the Moderator list for display
    $kunena_db->setQuery("SELECT * FROM #__kunena_moderation AS m LEFT JOIN #__users AS u ON u.id=m.userid WHERE m.catid='" . $obj_kunena_cat->getId() . "'");
    $modslist = $kunena_db->loadObjectList();
    	check_dberror("Unable to load moderators.");
    //    echo '<div class="kunena_pathway">';
    // List of Forums
    // show folder icon
    $return = '<img src="' . KUNENA_URLIMAGESPATH . 'folder.gif" border="0" alt="' . _GEN_FORUMLIST . '" style="vertical-align: middle;" />&nbsp;';
    // link to List of Forum Categories
    $return .= '&nbsp;' . kunena_Link::GetKunenaLink(_GEN_FORUMLIST) . '<br />';

    // List of    Categories
    if ($objCatParentInfo)
    {
        if ($bool_set_title)
            $document->setTitle(stripslashes($objCatParentInfo->name) . ' - ' . stripslashes($obj_kunena_cat->getName()) . ' - ' . stripslashes($kunenaConfig->board_title));

        // show lines
        $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'tree-end.gif" alt="|-" border="0" style="vertical-align: middle;" />';
        $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'folder.gif" alt="' . $objCatParentInfo->name . '" border="0" style="vertical-align: middle;" />&nbsp;';
        // link to Category
        $return .= '&nbsp;'. kunenalink::GetCategoryLink('listcat', $objCatParentInfo->id, $objCatParentInfo->name) . '<br />';
        $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'tree-blank.gif" alt="| " border="0" style="vertical-align: middle;" />';
    }
    else
    {
        if ($bool_set_title)
            $document->setTitle(stripslashes($obj_kunena_cat->getName()) . ' - ' . stripslashes($kunenaConfig->board_title));
    }

    // Forum
    // show lines
    $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'tree-end.gif" alt="|-" border="0" style="vertical-align: middle;" />';
    $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'folder.gif" alt="+" border="0" style="vertical-align: middle;" />&nbsp;';
    // Link to forum
    $return .= '&nbsp;' . kunenaLink::GetCategoryLink('listcat', $obj_kunena_cat->getId(), $obj_kunena_cat->getName());

    //check if this forum is locked
    if ($obj_kunena_cat->getLocked()) {
        $return .= isset($kunenaIcons['forumlocked']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['forumlocked'] . '" border="0" alt="'
            . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '    <img src="' . KUNENA_URLIMAGESPATH . 'lock.gif"    border="0" width="13" height="13" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '">';
    }

    // check if this forum is reviewed
    if ($obj_kunena_cat->getReview()) {
        $return .= isset($kunenaIcons['forumreviewed']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['forumreviewed']
            . '" border="0" alt="' . _GEN_REVIEWED . '" title="' . _GEN_REVIEWED . '"/>' : '    <img src="' . KUNENA_URLIMAGESPATH . 'review.gif" border="0" width="15" height="15" alt="' . _GEN_REVIEWED . '" title="' . _GEN_REVIEWED . '">';
    }

    //check if this forum is moderated
    if ($obj_kunena_cat->getModerated())
    {
        $return .= isset($kunenaIcons['forummoderated']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['forummoderated']
            . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : '    <img src="' . KUNENA_URLEMOTIONSPATH . 'moderate.gif" border="0"  alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>';
        $text = '';

        if (count($modslist) > 0)
        {
            foreach ($modslist as $mod) {
                $text = $text . ', ' . $mod->username;
            }

            $return .= '&nbsp;(' . _GEN_MODERATORS . ': ' . ltrim($text, ",") . ')';
        }
    }

    if ($obj_post != 0)
    {
        if ($bool_set_title)
            $document->setTitle(stripslashes($obj_post->subject) . ' - ' . stripslashes($kunenaConfig->board_title));

        // Topic
        // show lines
        $return .= '<br />&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'tree-blank.gif" alt="| " border="0" style="vertical-align: middle;" />';
        $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'tree-blank.gif" alt="| " border="0" style="vertical-align: middle;" />';
        $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'tree-end.gif" alt="|-" border="0" style="vertical-align: middle;" />';
        $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'folder.gif" alt="+" border="0" style="vertical-align: middle;" />&nbsp;';
        $return .= '&nbsp;<b>' . $obj_post->subject . '</b>';

        // Check if the Topic is locked?
        if ((int)$obj_post->locked != 0) {
            $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'lock.gif"    border="0" width="13" height="13" alt="' . _GEN_LOCKED_TOPIC . '" title="' . _GEN_LOCKED_TOPIC . '"/>';
        }
    }

    //    echo '</div>';
    return $return;
}
/**
 * Function to generate the page list of a forum
 */

/**
 * Function  that get the menu used in the header of our board
 * @param int $cbitemid
 *             Community builder itemid, used for linking to cb profile
 * @param array $kunenaConfig
 * @param array $kunenaIcons
 * @param int $my_id
 *             The user id
 * @param int $type
 *             What kind of header do you want to print: 1: default (home/profile/latest posts/faq), 2: extended1 (home/profile/view/pending messages/faq) ,3:extended2 (home/profile/reply/view/pdf/faq)
 * @param string $view
 *             The view the user is currently using, only needs to be pass when type==3 or type==2
 * @param int $catid
 *             Only needs to be passed when type==3 or type==2
 * @param int $id
 *             Only needs to be passed when type==3 or type==2
 * @param int $thread
 *             Only needs to be passed when type==3 or type==2 (well actually just give 0 when type==2)
 * @param boolean $is_moderator
 *             Only needs to be passed when type==2
 * @param int $numPending
 *             Number of pending messages, only needs to be passed when type==2
 * @return String $header
 *             The menu :-)
 */
function KUNENA_get_menu($cbitemid, $kunenaConfig, $kunenaIcons, $my_id, $type, $view = "", $catid = 0, $id = 0, $thread = 0, $is_moderator = false, $numPending = 0)
{
    $header = '<div id="kunena_topmenu" >';
    $header .= CKunenaLink::GetCategoryListLink('<span>'.(isset($kunenaIcons['home']) ? '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['home'] . '" border="0" alt="' . _KUNENA_CATEGORIES . '"  title="' . _KUNENA_CATEGORIES . '" />' : _KUNENA_CATEGORIES).'</span>');

    if ($my_id != 0)
    {
        $header .= CKunenaLink::GetMyProfileLink( $kunenaConfig, $my_id, isset($kunenaIcons['profile']) ? '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['profile'] . '" border="0" alt="' . _GEN_MYPROFILE . '" title="' . _GEN_MYPROFILE . '"/>' : _GEN_MYPROFILE);
    }

    switch ($type)
    {
        case 3:
            /* DISABLE MENU
            $header.= '<a href="'.JRoute::_(KUNENA_LIVEURLREL.'&amp;func=post&amp;do=reply&amp;replyto='.$thread.'&amp;catid='.$catid).'" >';
            $header.= isset($kunenaIcons['menureply']) ? '<img src="' . KUNENA_URLICONSPATH.$kunenaIcons['menureply'].'" border="0" alt="'._GEN_POST_REPLY.'" title="'._GEN_POST_REPLY.'"/>' : _GEN_POST_REPLY;
            $header.= '</a>';
           */
//Disable threaded view option alltogether for Kunena
//            if ($view == "flat") {
//    			$header .= CKunenaLink::GetViewLink('view', $id, $catid, 'threaded', (isset($kunenaIcons['threadedview']) ? '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['threadedview'] . '" border="0" alt="' . _GEN_THREADED_VIEW . '" title="' . _GEN_THREADED_VIEW . '"/>' : _GEN_THREADED_VIEW));
//            }
//            else
//            {
//                $header .= CKunenaLink::GetViewLink('view', $id, $catid, 'flat', (isset($kunenaIcons['flatview']) ? '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['flatview'] . '" border="0" alt="' . _GEN_FLAT_VIEW . '" title="' . _GEN_FLAT_VIEW . '"/>' : _GEN_FLAT_VIEW));
//            }
            break;

        case 2:
//Disable threaded view option alltogether for Kunena
//            if ($view == "flat")
//            {
//    			$header .= CKunenaLink::GetViewLink('showcat', $id, $catid, 'threaded', (isset($kunenaIcons['threadedview']) ? '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['threadedview'] . '" border="0" alt="' . _GEN_THREADED_VIEW . '" title="' . _GEN_THREADED_VIEW . '"/>' : _GEN_THREADED_VIEW));
//            }
//			else
//			{
//                $header .= CKunenaLink::GetViewLink('showcat', $id, $catid, 'flat', (isset($kunenaIcons['flatview']) ? '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['flatview'] . '" border="0" alt="' . _GEN_FLAT_VIEW . '" title="' . _GEN_FLAT_VIEW . '"/>' : _GEN_FLAT_VIEW));
//			}
            if ($is_moderator)
            {
                if ($numPending > 0)
                {
                    $numcolor = '<font color="red">';
                    $header .= CKunenaLink::GetPendingMessagesLink( $catid, (isset($kunenaIcons['pendingmessages']) ? '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['pendingmessages'] . '" border="0" alt="' . $numPending . ' ' . _SHOWCAT_PENDING . '" />' : $numcolor . '' . $numPending . '</font> ' . _SHOWCAT_PENDING));
                }
            }

            break;

        case 1:
        default:
            $header .= CKunenaLink::GetShowLatestLink( (isset($kunenaIcons['showlatest']) ? '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['showlatest'] . '" border="0" alt="' . _GEN_LATEST_POSTS . '" title="' . _GEN_LATEST_POSTS . '"/>' : _GEN_LATEST_POSTS));
            break;
    }

    if ($kunenaConfig->enablerulespage)
    {
        $header .= CKunenaLink::GetRulesLink($kunenaConfig, (isset($kunenaIcons['rules']) ? '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['rules'] . '" border="0" alt="' . _GEN_RULES . '" title="' . _GEN_RULES . '"/>' : _GEN_RULES));
    }
	if ($kunenaConfig->enablehelppage)
    {
        $header .= CKunenaLink::GetHelpLink($kunenaConfig, (isset($kunenaIcons['help']) ? '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['help'] . '" border="0" alt="' . _GEN_HELP . '" title="' . _GEN_HELP . '"/>' : _GEN_HELP));
	}
    $header .= '</div>';
    return $header;
}

function getSearchBox()
{
    $return = '<div id="kunena_searchbox"><form action="' . JRoute::_(KUNENA_LIVEURLREL . '&amp;func=search') . '" name="searchFB" method="post">';
    $boxsize = strlen(_GEN_SEARCH_BOX);

    if ($boxsize <= 15)
        $boxsize = 15;

   $return .= '<input class="kunena_search_inputbox kunenas" type="text" name="q" size="'. $boxsize . '" value="' . _GEN_SEARCH_BOX . '" onblur="if(this.value==\'\') this.value=\'' . _GEN_SEARCH_BOX . '\';" onfocus="if(this.value==\'' . _GEN_SEARCH_BOX . '\') this.value=\'\';" />';
	$return .= ' <input type="submit" value="'._KUNENA_GO.'" name="submit" class="kunena_search_button kunenas"/>';
    $return .= '</form></div>';
    return $return;
}
?>
