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
 * Function  that get the menu used in the header of our board
 * @param array $kunena_config
 * @param array $kunena_emoticons
 * @param int $my_id
 *             The user id
 * @param int $type
 *             What kind of header do you want to print:
 *             		1: default (home/profile/latest posts/faq),
 *             		2: extended1 (home/profile/view/pending messages/faq),
 *             		3:extended2 (home/profile/reply/view/pdf/faq)
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

function kunena_get_menu($cbitemid, $kunena_config, $kunena_emoticons, $my_id, $type, $view = "", $catid = 0, $id = 0,
							$thread = 0, $kunena_is_moderator = false, $numPending = 0)
{
	$func = JString::strtolower(JRequest::getCmd('func', ''));
	if ($func == '') // Set default as per config settings
	{
		switch ($kunena_config->fbdefaultpage)
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
    $header .=' >'.CKunenaLink::GetShowLatestLink('<span>'.(array_key_exists('showlatest', $kunena_emoticons) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['showlatest'] . '" border="0" alt="' . _KUNENA_ALL_DISCUSSIONS . '" title="' . _KUNENA_ALL_DISCUSSIONS . '"/>' : _KUNENA_ALL_DISCUSSIONS).'</span>');
    $header .= '</li>';

    if ($my_id != 0)
    {
	    $header .= ' <li ';
	    if ($func == 'mylatest') $header .= ' class="Kunena_item_active" ';
	    $header .=' >'.CKunenaLink::GetShowMyLatestLink('<span>'.(array_key_exists('showmylatest', $kunena_emoticons) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['showmylatest'] . '" border="0" alt="' . _KUNENA_MY_DISCUSSIONS . '" title="' . _KUNENA_MY_DISCUSSIONS . '"/>' : _KUNENA_MY_DISCUSSIONS).'</span>');
	    $header .= '</li>';
    }

    $header .= '<li ';
	 if ($func == 'listcat' ) $header .= ' class="Kunena_item_active" ';
	$header .=' >'.CKunenaLink::GetCategoryListLink('<span>'.(array_key_exists('home', $kunena_emoticons) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['home'] . '" border="0" alt="' . _KUNENA_CATEGORIES . '"  title="' . _KUNENA_CATEGORIES . '" />' : _KUNENA_CATEGORIES).'</span>');
    $header .= '</li>';

    if ($my_id != 0)
    {
        $header .= ' <li ';
	    if ($func == 'myprofile' ) $header .= ' class="Kunena_item_active" ';
        $header .=' >'.CKunenaLink::GetMyProfileLink($kunena_config, $my_id, '<span>'.(array_key_exists('profile', $kunena_emoticons) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['profile'] . '" border="0" alt="' . _GEN_MYPROFILE . '" title="' . _GEN_MYPROFILE . '"/>' : _GEN_MYPROFILE).'</span>');
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
            if ($kunena_is_moderator)
            {
                if ($numPending > 0)
                {
                    $header .= '<li>';
                    $header .= CKunenaLink::GetPendingMessagesLink( $catid, '<span>'.(array_key_exists('pendingmessages', $kunena_emoticons)
                        ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['pendingmessages'] . '" border="0" alt="' . $numPending . ' ' . _SHOWCAT_PENDING . '" />' : '<font color="red">' . $numPending . '</font> ' . _SHOWCAT_PENDING).'</span>');
                    $header .= '</li>';
                }
            }
            break;

        case 1:
        default:

            break;
    }

    if ($kunena_config->enablerulespage)
    {
        $header .= ' <li ';
        if ($func == 'rules' ) $header .= ' class="Kunena_item_active" ';
        $header .= ' >'.CKunenaLink::GetRulesLink($kunena_config, '<span>'.(array_key_exists('rules', $kunena_emoticons) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['rules'] . '" border="0" alt="' . _GEN_RULES . '" title="' . _GEN_RULES . '"/>' : _GEN_RULES).'</span>');
        $header .= '</li>';
    }
	if ($kunena_config->enablehelppage)
    {
        $header .= ' <li ';
        if ($func == 'faq' ) $header .= ' class="Kunena_item_active" ';
        $header .= ' >'.CKunenaLink::GetHelpLink($kunena_config, '<span>'.(array_key_exists('help', $kunena_emoticons) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['help'] . '" border="0" alt="' . _GEN_HELP . '" title="' . _GEN_HELP . '"/>' : _GEN_HELP).'</span>');
        $header .= '</li>';
	}
    $header .= '</ul></div></div>';
    return $header;
}

function getSearchBox()
{
    $return = '<div id="fb_searchbox"><form action="' . JRoute::_(KUNENA_LIVEURLREL . '&amp;func=search') . '" name="searchFB" method="post">';
    $boxsize = JString::strlen(_GEN_SEARCH_BOX);

    if ($boxsize <= 15)
        $boxsize = 15;

   $return .= '<input class="fb_search_inputbox fbs" type="text" name="q" size="'. $boxsize . '" value="' . _GEN_SEARCH_BOX . '" onblur="if(this.value==\'\') this.value=\'' . _GEN_SEARCH_BOX . '\';" onfocus="if(this.value==\'' . _GEN_SEARCH_BOX . '\') this.value=\'\';" />';
	$return .= ' <input type="submit" value="'._KUNENA_GO.'" name="submit" class="fb_button fbs"/>';
    $return .= '</form></div>';
    return $return;
}
?>
