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

defined( '_JEXEC' ) or die('Restricted access');


/**
 * Function  that get the menu used in the header of our board
 * @param int $cbitemid
 *             Community builder itemid, used for linking to cb profile
 * @param array $kunena_config
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
 * @param boolean $kunena_is_moderator
 *             Only needs to be passed when type==2
 * @param int $numPending
 *             Number of pending messages, only needs to be passed when type==2
 * @return String $header
 *             The menu :-)
 */
function kunena_get_menu($cbitemid, $kunena_config, $fbIcons, $my_id, $type, $view = "", $catid = 0, $id = 0, $thread = 0, $kunena_is_moderator = false, $numPending = 0)
{
    $header = '<div id="fb_topmenu" >';
    $header .= CKunenaLink::GetCategoryListLink('<span>'.(isset($fbIcons['home']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['home'] . '" border="0" alt="' . _KUNENA_CATEGORIES . '"  title="' . _KUNENA_CATEGORIES . '" />' : _KUNENA_CATEGORIES).'</span>');

    if ($my_id != 0)
    {
        $header .= CKunenaLink::GetMyProfileLink( $kunena_config, $my_id, isset($fbIcons['profile']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['profile'] . '" border="0" alt="' . _GEN_MYPROFILE . '" title="' . _GEN_MYPROFILE . '"/>' : _GEN_MYPROFILE);
    }

    switch ($type)
    {
        case 3:
            /* DISABLE MENU
            $header.= '<a href="'.JRoute::_(KUNENA_LIVEURLREL.'&amp;func=post&amp;do=reply&amp;replyto='.$thread.'&amp;catid='.$catid).'" >';
            $header.= isset($fbIcons['menureply']) ? '<img src="' . KUNENA_URLICONSPATH.$fbIcons['menureply'].'" border="0" alt="'._GEN_POST_REPLY.'" title="'._GEN_POST_REPLY.'"/>' : _GEN_POST_REPLY;
            $header.= '</a>';
           */
//Disable threaded view option alltogether for Kunena
//            if ($view == "flat") {
//    			$header .= CKunenaLink::GetViewLink('view', $id, $catid, 'threaded', (isset($fbIcons['threadedview']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['threadedview'] . '" border="0" alt="' . _GEN_THREADED_VIEW . '" title="' . _GEN_THREADED_VIEW . '"/>' : _GEN_THREADED_VIEW));
//            }
//            else
//            {
//                $header .= CKunenaLink::GetViewLink('view', $id, $catid, 'flat', (isset($fbIcons['flatview']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['flatview'] . '" border="0" alt="' . _GEN_FLAT_VIEW . '" title="' . _GEN_FLAT_VIEW . '"/>' : _GEN_FLAT_VIEW));
//            }
            break;

        case 2:
//Disable threaded view option alltogether for Kunena
//            if ($view == "flat")
//            {
//    			$header .= CKunenaLink::GetViewLink('showcat', $id, $catid, 'threaded', (isset($fbIcons['threadedview']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['threadedview'] . '" border="0" alt="' . _GEN_THREADED_VIEW . '" title="' . _GEN_THREADED_VIEW . '"/>' : _GEN_THREADED_VIEW));
//            }
//			else
//			{
//                $header .= CKunenaLink::GetViewLink('showcat', $id, $catid, 'flat', (isset($fbIcons['flatview']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['flatview'] . '" border="0" alt="' . _GEN_FLAT_VIEW . '" title="' . _GEN_FLAT_VIEW . '"/>' : _GEN_FLAT_VIEW));
//			}
            if ($kunena_is_moderator)
            {
                if ($numPending > 0)
                {
                    $numcolor = '<font color="red">';
                    $header .= CKunenaLink::GetPendingMessagesLink( $catid, (isset($fbIcons['pendingmessages']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['pendingmessages'] . '" border="0" alt="' . $numPending . ' ' . _SHOWCAT_PENDING . '" />' : $numcolor . '' . $numPending . '</font> ' . _SHOWCAT_PENDING));
                }
            }

            break;

        case 1:
        default:
            $header .= CKunenaLink::GetShowLatestLink( (isset($fbIcons['showlatest']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['showlatest'] . '" border="0" alt="' . _GEN_LATEST_POSTS . '" title="' . _GEN_LATEST_POSTS . '"/>' : _GEN_LATEST_POSTS));
            break;
    }

    if ($kunena_config->enablerulespage)
    {
        $header .= CKunenaLink::GetRulesLink($kunena_config, (isset($fbIcons['rules']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['rules'] . '" border="0" alt="' . _GEN_RULES . '" title="' . _GEN_RULES . '"/>' : _GEN_RULES));
    }
	if ($kunena_config->enablehelppage)
    {
        $header .= CKunenaLink::GetHelpLink($kunena_config, (isset($fbIcons['help']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['help'] . '" border="0" alt="' . _GEN_HELP . '" title="' . _GEN_HELP . '"/>' : _GEN_HELP));
	}
    $header .= '</div>';
    return $header;
}

function getSearchBox()
{
    $return = '<div id="fb_searchbox"><form action="' . JRoute::_(KUNENA_LIVEURLREL . '&amp;func=search') . '" name="searchFB" method="post">';
    $boxsize = strlen(_GEN_SEARCH_BOX);

    if ($boxsize <= 15)
        $boxsize = 15;

   $return .= '<input class="fb_search_inputbox fbs" type="text" name="q" size="'. $boxsize . '" value="' . _GEN_SEARCH_BOX . '" onblur="if(this.value==\'\') this.value=\'' . _GEN_SEARCH_BOX . '\';" onfocus="if(this.value==\'' . _GEN_SEARCH_BOX . '\') this.value=\'\';" />';
	$return .= ' <input type="submit" value="'._KUNENA_GO.'" name="submit" class="fb_search_button fbs"/>';
    $return .= '</form></div>';
    return $return;
}
?>
