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
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

class CKunenaLink
{
    //
    // Basic universal href link
    //
    function GetHrefLink($link, $name, $title, $rel, $class ='', $anker='', $attr='')
    {
        return '<a '.($class ? 'class="'.$class.'" ' : '').'href="'.$link.($anker?('#'.$anker):'').'" title="'.$title.'"'.($rel ? ' rel="'.$rel.'"' : '').($attr ? ' '.$attr : '').'>'.$name.'</a>';
    }

    //
    // Basic universal href link
    //
    function GetSefHrefLink($link, $name, $title, $rel, $class ='', $anker='', $attr='')
    {
        return '<a '.($class ? 'class="'.$class.'" ' : '').'href="'.JRoute::_($link).($anker?('#'.$anker):'').'" title="'.$title.'"'.($rel ? ' rel="'.$rel.'"' : '').($attr ? ' '.$attr : '').'>'.$name.'</a>';
    }

    // Simple link is a barebones href link used for e.g. Jscript links
    function GetSimpleLink($id, $name='')
    {
        return'<a href="'.$id.'">'.$name.'</a>';
    }

    //
    // Central Consolidation of all internal href links
    //

    function GetCreditsLink()
    {
        return CKunenaLink::GetSefHrefLink('http://www.kunena.com', 'Kunena', 'Kunena', 'follow', NULL, NULL, 'target="_blank"');
    }

    function GetTeamCreditsLink($catid, $name='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=credits&amp;catid='.$catid, $name, '', 'follow');
    }

    function GetKunenaLink($name , $rel='follow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL, $name, '', $rel);
    }

    function GetRSSLink($name , $rel='follow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=fb_rss&amp;no_html=1', $name, '', $rel, '', '', 'target="_blank"');
    }

    function GetCategoryLink($func, $catid, $catname, $rel='follow', $class='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;catid='.$catid, $catname, '', $rel, $class);
    }

    function GetCategoryListLink($name, $rel='follow', $class='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=listcat', $name, '', $rel, $class);
    }

    function GetCategoryPageLink($func, $catid, $page, $pagename, $rel='follow', $class='')
    {
        if ($page == 1 || !is_numeric($page))
        {
            // page 1 is identical to a link to the regular category link
            $pagelink  = CKunenaLink::GetCategoryLink($func, $catid, $pagename, $rel, $class);
        }
        else
        {
            $pagelink  = CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;catid='.$catid.'&amp;page='.$page, $pagename, '', $rel, $class);
        }

        return $pagelink;
    }

    function GetCategoryReviewListLink($catid, $catname, $rel='nofollow', $class='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=review&amp;action=list&amp;catid='.$catid, $catname, '', $rel, $class);
    }

    function GetThreadLink($func, $catid, $threadid, $threadname, $title, $rel='follow', $class='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;catid='.$catid.'&amp;id='.$threadid, $threadname, $title, $rel, $class);
    }

    function GetThreadPageLink($fbConfig, $func, $catid, $threadid, $page, $limit, $name, $anker='', $rel='follow', $class='')
    {
    	$fbConfig =& CKunenaConfig::getInstance();
        if ($page == 1 || !is_numeric($page) || !is_numeric($limit))
        {
            // page 1 is identical to a link to the top of the thread
            $pagelink  = CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;catid='.$catid.'&amp;id='.$threadid,
            										$name, '', $rel, $class, $anker);
        }
        else
        {
            $pagelink  = CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;catid='.$catid.'&amp;id='.$threadid
                          .'&amp;limit='.$limit.'&amp;limitstart='.(($page-1)*$limit), $name, '', $rel, $class, $anker);
        }

        return $pagelink;
    }

    // GetThreadPageURL is basically identically to the prior function except that it returns a clear text
    // non-encoded URL. This functions is used by the email function to notify users about new posts.
    function GetThreadPageURL($fbConfig, $func, $catid, $threadid, $page, $limit='', $anker='')
    {
        if ($page == 1 || !is_numeric($page) || !is_numeric($limit))
        {
            // page 1 is identical to a link to the top of the thread
            $pageURL = htmlspecialchars_decode(KUNENA_LIVEURLREL).'&func='.$func.'&catid='.$catid.'&id='.$threadid;
        }
        else
        {
            $pageURL = htmlspecialchars_decode(KUNENA_LIVEURLREL).'&func='.$func.'&catid='.$catid.'&id='.$threadid
                          .'&limit='.$limit.'&limitstart='.(($page-1)*$limit);
        }

        return JRoute::_($pageURL).($anker?('#'.$anker):'');
    }

    function GetSamePageAnkerLink($anker, $name, $rel='nofollow')
    {
    	jimport('joomla.environment.request');
        return CKunenaLink::GetSefHrefLink(JRequest::getURI(), $name, '', $rel, '', $anker);
    }

    function GetReportMessageLink($catid, $id, $name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=report&amp;catid='.$catid.'&amp;id='.$id, $name, '', $rel);
    }

    function GetMessageIPLink($msg_ip, $rel='nofollow')
    {
        if (!empty($msg_ip))
        {
            $iplink = '<a href="http://whois.domaintools.com/'.$msg_ip.'" target="_blank">';
            $iplink .= 'IP: '.$msg_ip.'</a>';
        }
        else
        {
            $iplink = '&nbsp;';
        }

        return $iplink;
    }

    function GetMyProfileLink($fbConfig, $userid, $name, $rel='nofollow')
    {
    	$fbConfig =& CKunenaConfig::getInstance();
    	if($fbConfig->fb_profile == 'jomsocial' || $fbConfig->fb_profile == 'cb' || $fbConfig->fb_profile == 'aup')
    	{
    		return CKunenaLink::GetProfileLink($fbConfig, $userid, $name, $rel);
    	}
    	else
    	{
    		return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=myprofile', $name, '', $rel);
    	}
    }

    function GetProfileLink($fbConfig, $userid, $name, $rel='nofollow', $class='')
    {
    	if ($userid > 0)
    	{
    		$link = CKunenaLink::GetProfileURL($userid);
    		if (!empty($link)) return CKunenaLink::GetHrefLink($link, $name, '', $rel, $class);
    	}
   		return $name;
    }

    function GetProfileURL($userid)
    {
    	$fbConfig =& CKunenaConfig::getInstance();
    	// Only create links for valid users
    	if ($userid > 0)
    	{
    		if($fbConfig->fb_profile == 'cb')
    		{
    			$kunenaProfile =& CKunenaCBProfile::getInstance();
    			return $kunenaProfile->getProfileURL($userid);
    		}
    		elseif ($fbConfig->fb_profile == 'jomsocial') 
    		{
   				return CRoute::_('index.php?option=com_community&view=profile&userid='.$userid);
    		}
    		elseif ($fbConfig->fb_profile == 'aup') 
    		{
				$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
				if ( file_exists($api_AUP)) {
					$useridAUP = AlphaUserPointsHelper::getAnyUserReferreID( $userid );
					return JRoute::_(KUNENA_PROFILE_LINK_SUFFIX.$useridAUP);
				}
			}
			else
			{
   				return JRoute::_(KUNENA_PROFILE_LINK_SUFFIX.$userid);
    		}
    	}
   		return '';
    }

    function GetUserlistURL($action='')
	{
		return JRoute::_(KUNENA_LIVEURLREL.'&amp;func=userlist'.$action);
	}

	function GetUserlistLink($action, $name, $rel='nofollow', $class='')
	{
		return self::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=userlist'.$action, $name, '', $rel, $class);
	}

    function GetViewLink($func, $id, $catid, $view, $name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;id='.$id.'&amp;view='.$view.'&amp;catid='.$catid, $name, '', $rel);
    }

    function GetPendingMessagesLink($catid, $name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=review&action=list&amp;catid='.$catid, $name, '', $rel);
    }

    function GetShowLatestLink($name, $rel='follow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=latest', $name, '', $rel);
    }

    function GetShowLatestURL()
    {
        return JRoute::_(KUNENA_LIVEURLREL.'&amp;func=latest');
    }

    function GetShowMyLatestLink($name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=mylatest', $name, '', $rel);
    }

    function GetShowLatestThreadsLink($period, $name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel='.$period, $name, '', $rel);
    }

    // Function required to support default_ex template
    function GetLatestPageLink($func, $page, $rel='follow', $class='', $sel='')
    {
    	// if ($func=='') $func = 'latest';
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;page='.$page.(($sel)?'&amp;sel='.$sel:''), $page, '', $rel, $class);
    }

    function GetPostNewTopicLink($catid, $name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=post&amp;do=reply&amp;catid='.$catid, $name, '', $rel);
    }

    function GetTopicPostLink($do, $catid, $id, $name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=post&amp;do='.$do.'&amp;catid='.$catid.'&amp;id='.$id, $name, '', $rel);
    }

    function GetTopicPostReplyLink($do, $catid, $id, $name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=post&amp;do='.$do.'&amp;catid='.$catid.'&amp;id='.$id, $name, '', $rel);
    }

    function GetEmailLink($email, $name)
    {
        return CKunenaLink::GetSimpleLink('mailto:'.stripslashes($email), stripslashes($name));
    }

    function GetKarmaLink($do, $catid, $pid, $userid, $name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=karma&amp;do='.$do.'&amp;userid='.$userid.'&amp;pid='.$pid.'&amp;catid='.$catid, $name, '', $rel);
    }

    function GetRulesLink($fbConfig, $name, $rel='nofollow')
    {
		$fbConfig =& CKunenaConfig::getInstance();
        $ruleslink = $fbConfig->rules_infb ? KUNENA_LIVEURLREL.'&amp;func=rules' : $fbConfig->rules_link;
        return CKunenaLink::GetSefHrefLink($ruleslink, $name, '', $rel);
    }

    function GetHelpLink($fbConfig, $name, $rel='nofollow')
    {
    	$fbConfig =& CKunenaConfig::getInstance();
        $helplink = $fbConfig->help_infb ? KUNENA_LIVEURLREL.'&amp;func=faq' : $fbConfig->help_link;
        return CKunenaLink::GetSefHrefLink($helplink, $name, '', $rel);
    }

    function GetSearchURL($fbConfig, $func, $searchword, $limitstart, $limit, $params='')
    {
    	$fbConfig =& CKunenaConfig::getInstance();
		$limitstr = "";
    	if ($limitstart > 0) $limitstr .= "&amp;limitstart=$limitstart";
		if ($limit > 0 && $limit != $fbConfig->messages_per_page_search) $limitstr .= "&amp;limit=$limit";
		if ($searchword) $searchword = '&amp;q=' . urlencode($searchword);
        return JRoute::_(KUNENA_LIVEURLREL."&amp;func={$func}&amp;q={$searchword}{$params}{$limitstr}");
    }

    function GetSearchLink($fbConfig, $func, $searchword, $limitstart, $limit, $name, $params='', $rel='nofollow')
    {
    	$fbConfig =& CKunenaConfig::getInstance();
		$limitstr = "";
    	if ($limitstart > 0) $limitstr .= "&amp;limitstart=$limitstart";
		if ($limit > 0 && $limit != $fbConfig->messages_per_page_search) $limitstr .= "&amp;limit=$limit";
		if ($searchword) $searchword = '&amp;q=' . urlencode($searchword);
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL."&amp;func={$func}{$searchword}{$params}{$limitstr}", $name, '', $rel);
    }

    function GetAnnouncementURL($fbConfig, $do, $id=NULL)
    {
		$idstring = '';
		if ($id !== NULL) $idstring .= "&amp;id=$id";
		return JRoute::_(KUNENA_LIVEURLREL."&amp;func=announcement&amp;do={$do}{$idstring}");
    }

    //
    // Macro functions that build more complex html output with embedded links
    //

    //
    // This function builds the auto redirect block to go back to the latest post of a particular thread
    // It is used for various operations. Input parameter is any post id. It will determine the thread,
    // latest post of that thread and number of pages based on the supplied page limit.
    //
    function GetLatestPostAutoRedirectHTML($fbConfig, $pid, $limit, $catid=0)
    {
        $kunena_db = &JFactory::getDBO();
        // First determine the thread, latest post and number of posts for the post supplied
        $where = '';
		if ($catid > 0) $where .= " AND a.catid = {$catid} ";
		$kunena_db->setQuery("SELECT a.thread AS thread, MAX(a.id) AS latest_id, MAX(a.catid) AS catid, COUNT(*) AS totalmessages
                             FROM #__fb_messages AS a,
                                (SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$pid}') AS b
                             WHERE a.thread = b.thread AND a.hold='0' {$where}
                             GROUP BY a.thread");
        $result = $kunena_db->loadObject();
        	check_dberror("Unable to retrieve latest post.");

        // Now Calculate the number of pages for this particular thread
        if (is_object($result))
        {
        	$catid = $result->catid;
        	$threadPages = ceil($result->totalmessages / $limit);
        }

        // Finally build output block

        $Output  = '<div align="center">';
        if (is_object($result)) $Output .= CKunenaLink::GetThreadPageLink($fbConfig, 'view', $catid, $result->thread, $threadPages, $limit, _POST_SUCCESS_VIEW, $result->latest_id) .'<br />';
        $Output .= CKunenaLink::GetCategoryLink('showcat', $catid, _POST_SUCCESS_FORUM).'<br />';
        $Output .= '</div>';
        if (is_object($result)) $Output .= CKunenaLink::GetAutoRedirectHTML(CKunenaLink::GetThreadPageURL($fbConfig, 'view', $catid, $result->thread, $threadPages, $limit, $result->latest_id), 3500);
        else $Output .= CKunenaLink::GetAutoRedirectHTML(JRoute::_(KUNENA_LIVEURLREL.'&amp;func=showcat&amp;catid='.$catid), 3500);

        return $Output;
    }

    function GetLatestPageAutoRedirectURL($fbConfig, $pid, $limit, $catid=0)
    {
        $kunena_db = &JFactory::getDBO();
        // First determine the thread, latest post and number of posts for the post supplied
        $where = '';
		if ($catid > 0) $where .= " AND a.catid = {$catid} ";
        $kunena_db->setQuery("SELECT a.thread AS thread, MAX(a.id) AS latest_id, MAX(a.catid) AS catid, COUNT(*) AS totalmessages
                             FROM #__fb_messages AS a,
                                (SELECT MAX(thread) AS thread FROM #__fb_messages WHERE id='{$pid}') AS b
                             WHERE a.thread = b.thread AND a.hold='0' {$where}
                             GROUP BY a.thread");
        $result = $kunena_db->loadObject();
        	check_dberror("Unable to retrieve latest post.");
        if (!is_object($result)) return htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL.'&amp;func=showcat&amp;catid='.$catid));

        // Now Calculate the number of pages for this particular thread
		$catid = $result->catid;
        $threadPages = ceil($result->totalmessages / $limit);

		// Finally build output block
        return htmlspecialchars_decode(CKunenaLink::GetThreadPageURL($fbConfig, 'view', $catid, $result->thread, $threadPages, $limit));
    }

    function GetLatestCategoryAutoRedirectHTML($catid)
    {
        $Output  = '<div id="Kunena_post_result" align="center">';
        $Output .= CKunenaLink::GetCategoryLink('showcat', $catid, _POST_SUCCESS_FORUM).'<br />';
        $Output .= '</div>';
        $Output .= CKunenaLink::GetAutoRedirectHTML(KUNENA_LIVEURLREL . '&func=showcat&catid=' . $catid, 3500);

        return $Output;
    }

    function GetAutoRedirectHTML($url, $timeout)
    {
		$url = htmlspecialchars_decode($url);
        $Output = "\n<script type=\"text/javascript\">\n// <![CDATA[\n";
        $Output .= "kunenaRedirectTimeout('$url', $timeout);";
        $Output .= "\n// ]]>\n</script>\n";

        return $Output;
    }
}
?>
