<?php
/**
* @version $Id: fb_layout.php 970 2009-08-15 23:43:14Z mahagr $
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
function KUNENA_print_pathway(&$kunena_db, $obj_fb_cat, $bool_set_title, $obj_post = 0) {
    echo '<div class="fb_pathway">' . fb_get_pathway($kunena_db, $obj_fb_cat, $bool_set_title, $obj_post) . '</div>';
}
/**
 *  Function to print the pathway
 *  @param object    database object
 *  @param object    category object
 *  @param int        the post id
 *  @param boolean    set title
 */
function KUNENA_get_pathway(&$kunena_db, $obj_fb_cat, $bool_set_title, $obj_post = 0)
{
    global $fbIcons;

	$document=& JFactory::getDocument();
	$fbConfig =& CKunenaConfig::getInstance();
    //Get the Category's parent category name for breadcrumb
    $kunena_db->setQuery("SELECT name, id FROM #__fb_categories WHERE id='" . $obj_fb_cat->getParent() ."'");
    $objCatParentInfo = $kunena_db->loadObject();
    	check_dberror("Unable to load category.");
    //get the Moderator list for display
    $kunena_db->setQuery("SELECT * FROM #__fb_moderation AS m LEFT JOIN #__users AS u ON u.id=m.userid WHERE m.catid='" . $obj_fb_cat->getId() . "'");
    $modslist = $kunena_db->loadObjectList();
    	check_dberror("Unable to load moderators.");
    //    echo '<div class="fb_pathway">';
    // List of Forums
    // show folder icon
    $return = '<img src="' . KUNENA_URLIMAGESPATH . 'folder.gif" border="0" alt="' . _GEN_FORUMLIST . '" style="vertical-align: middle;" />&nbsp;';
    // link to List of Forum Categories
    $return .= '&nbsp;'.CKunenaLink::GetKunenaLink(_GEN_FORUMLIST).'<br />';

    // List of    Categories
    if ($objCatParentInfo)
    {
		if ($bool_set_title) 
		{
			$document->setTitle(stripslashes($objCatParentInfo->name) . ' - ' . stripslashes($obj_fb_cat->getName()) . ' - ' . stripslashes($fbConfig->board_title));
		}

        // show lines
        $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'tree-end.gif" alt="|-" border="0" style="vertical-align: middle;" />';
        $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'folder.gif" alt="' . $objCatParentInfo->name . '" border="0" style="vertical-align: middle;" />&nbsp;';
        // link to Category
        $return .= '&nbsp;'.CKunenaLink::GetCategoryLink('listcat', $objCatParentInfo->id, $objCatParentInfo->name).'<br />';
        $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'tree-blank.gif" alt="| " border="0" style="vertical-align: middle;" />';
    }
    else
    {
		if ($bool_set_title)
		{
			$document->setTitle(stripslashes($obj_fb_cat->getName()) . ' - ' . stripslashes($fbConfig->board_title));
		}
    }

    // Forum
    // show lines
    $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'tree-end.gif" alt="|-" border="0" style="vertical-align: middle;" />';
    $return .= '&nbsp;<img src="' . KUNENA_URLIMAGESPATH . 'folder.gif" alt="+" border="0" style="vertical-align: middle;" />&nbsp;';
    // Link to forum
    $return .= '&nbsp;'.CKunenaLink::GetCategoryLink('showcat', $obj_fb_cat->getId(), $obj_fb_cat->getName());

    //check if this forum is locked
    if ($obj_fb_cat->getLocked()) {
        $return .= isset($fbIcons['forumlocked']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $fbIcons['forumlocked'] . '" border="0" alt="'
            . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '    <img src="' . KUNENA_URLIMAGESPATH . 'lock.gif"    border="0" width="13" height="13" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '">';
    }

    // check if this forum is reviewed
    if ($obj_fb_cat->getReview()) {
        $return .= isset($fbIcons['forumreviewed']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $fbIcons['forumreviewed']
            . '" border="0" alt="' . _GEN_REVIEWED . '" title="' . _GEN_REVIEWED . '"/>' : '    <img src="' . KUNENA_URLIMAGESPATH . 'review.gif" border="0" width="15" height="15" alt="' . _GEN_REVIEWED . '" title="' . _GEN_REVIEWED . '">';
    }

    //check if this forum is moderated
    if ($obj_fb_cat->getModerated())
    {
        $return .= isset($fbIcons['forummoderated']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $fbIcons['forummoderated']
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
		{
			$document->setTitle($obj_post->subject . ' - ' . stripslashes($fbConfig->board_title));
		}

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
 * Function  that get the menu used in the header of our board
 * @param array $fbConfig
 * @param array $fbIcons
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

function KUNENA_get_menu($cbitemid, $fbConfig, $fbIcons, $my_id, $type, $view = "", $catid = 0, $id = 0, $thread = 0, $is_moderator = false, $numPending = 0)
{
	$func = strtolower(JRequest::getCmd('func', ''));
	if ($func == '') // Set default as per config settings
	{
		switch ($fbConfig->fbdefaultpage)
		{
			case 'recent':
				$func = 'latest';
				break;
			case 'my':
				$func = $my_id ? 'mylatest' : 'latest';
				break;
			default:
				$func = 'listcat';
		}
	}

    $header = '<div id="fb_topmenu" ><div id="Kunena_tab"><ul>';

    $header .= ' <li ';
    if ($func == 'latest' || $func == '') $header .= ' class="Kunena_item_active" ';
    $header .=' >'.CKunenaLink::GetShowLatestLink('<span>'.(array_key_exists('showlatest', $fbIcons) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['showlatest'] . '" border="0" alt="' . _KUNENA_ALL_DISCUSSIONS . '" title="' . _KUNENA_ALL_DISCUSSIONS . '"/>' : _KUNENA_ALL_DISCUSSIONS).'</span>');
    $header .= '</li>';

    if ($my_id != 0)
    {
	    $header .= ' <li ';
	    if ($func == 'mylatest') $header .= ' class="Kunena_item_active" ';
	    $header .=' >'.CKunenaLink::GetShowMyLatestLink('<span>'.(array_key_exists('showmylatest', $fbIcons) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['showmylatest'] . '" border="0" alt="' . _KUNENA_MY_DISCUSSIONS . '" title="' . _KUNENA_MY_DISCUSSIONS . '"/>' : _KUNENA_MY_DISCUSSIONS).'</span>');
	    $header .= '</li>';
    }

    $header .= '<li ';
	 if ($func == 'listcat' ) $header .= ' class="Kunena_item_active" ';
	$header .=' >'.CKunenaLink::GetCategoryListLink('<span>'.(array_key_exists('home', $fbIcons) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['home'] . '" border="0" alt="' . _KUNENA_CATEGORIES . '"  title="' . _KUNENA_CATEGORIES . '" />' : _KUNENA_CATEGORIES).'</span>');
    $header .= '</li>';

    if ($my_id != 0)
    {
        $header .= ' <li ';
	    if ($func == 'myprofile' ) $header .= ' class="Kunena_item_active" ';
        $header .=' >'.CKunenaLink::GetMyProfileLink($fbConfig, $my_id, '<span>'.(array_key_exists('profile', $fbIcons) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['profile'] . '" border="0" alt="' . _GEN_MYPROFILE . '" title="' . _GEN_MYPROFILE . '"/>' : _GEN_MYPROFILE).'</span>');
        $header .= '</li>';
    }

    switch ($type)
    {
        case 3:
//Threaded view option removed from Kunena
//            if ($view == "flat") {
//    			$header .= '<li>';
//    			$header .= CKunenaLink::GetViewLink('view', $id, $catid, 'threaded', '<span>'. _GEN_THREADED_VIEW .'</span>');
//                $header .= '</li>';
//            }
//            else
//            {
//    			$header .= '<li>';
//                $header .= CKunenaLink::GetViewLink('view', $id, $catid, 'flat', '<span>'. _GEN_FLAT_VIEW .'</span>');
//                $header .= '</li>';
//            }

            break;

        case 2:
//Threaded view option removed from Kunena
//            if ($view == "flat")
//            {
//    			$header .= '<li>';
//    			$header .= CKunenaLink::GetViewLink('showcat', $id, $catid, 'threaded', '<span>'. _GEN_THREADED_VIEW .'</span>');
//                $header .= '</li>';
//            }
//			else
//			{
//    			$header .= '<li>';
//                $header .= CKunenaLink::GetViewLink('showcat', $id, $catid, 'flat', '<span>'. _GEN_FLAT_VIEW .'</span>');
//                $header .= '</li>';
//			}
            if ($is_moderator)
            {
                if ($numPending > 0)
                {
                    $numcolor = '<font color="red">';
                    $header .= '<li>';
                    $header .= CKunenaLink::GetPendingMessagesLink( $catid, '<span>'.(array_key_exists('pendingmessages', $fbIcons)
                        ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['pendingmessages'] . '" border="0" alt="' . $numPending . ' ' . _SHOWCAT_PENDING . '" />' : $numcolor . '' . $numPending . '</font> ' . _SHOWCAT_PENDING).'</span>');
                    $header .= '</li>';
                }
            }
            break;

        case 1:
        default:

            break;
    }

    if ($fbConfig->enablerulespage)
    {
        $header .= ' <li ';
        if ($func == 'rules' ) $header .= ' class="Kunena_item_active" ';
        $header .= ' >'.CKunenaLink::GetRulesLink($fbConfig, '<span>'.(array_key_exists('rules', $fbIcons) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['rules'] . '" border="0" alt="' . _GEN_RULES . '" title="' . _GEN_RULES . '"/>' : _GEN_RULES).'</span>');
        $header .= '</li>';
    }
	if ($fbConfig->enablehelppage)
    {
        $header .= ' <li ';
        if ($func == 'faq' ) $header .= ' class="Kunena_item_active" ';
        $header .= ' >'.CKunenaLink::GetHelpLink($fbConfig, '<span>'.(array_key_exists('help', $fbIcons) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['help'] . '" border="0" alt="' . _GEN_HELP . '" title="' . _GEN_HELP . '"/>' : _GEN_HELP).'</span>');
        $header .= '</li>';
	}
    $header .= '</ul></div></div>';
    return $header;
}

function getSearchBox()
{
    $return = '<div id="fb_searchbox"><form action="' . JRoute::_(KUNENA_LIVEURLREL . '&amp;func=search') . '" name="searchFB" method="post">';
    $boxsize = strlen(_GEN_SEARCH_BOX);

    if ($boxsize <= 15)
        $boxsize = 15;

   $return .= '<input class="fb_search_inputbox fbs" type="text" name="q" size="'. $boxsize . '" value="' . _GEN_SEARCH_BOX . '" onblur="if(this.value==\'\') this.value=\'' . _GEN_SEARCH_BOX . '\';" onfocus="if(this.value==\'' . _GEN_SEARCH_BOX . '\') this.value=\'\';" />';
	$return .= ' <input type="submit" value="'._KUNENA_GO.'" name="submit" class="fb_button fbs"/>';
    $return .= '</form></div>';
    return $return;
}
?>
