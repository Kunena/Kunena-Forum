<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*/

defined('_JEXEC') or die('Invalid Request.');

/**
 * HTML helper class for handling Kunena link rendering.
 *
 * @package 	Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
abstract class JHtmlKLink
{
	/**
	 * Method to generate an simple (X)HTML link as an <a> tag.
	 *
	 * <code>
	 *	<?php echo JHtml::_('klink.simple', $url, $text); ?>
	 * </code>
	 *
	 * @param	string	The URL for the href attribute of the <a> tag.
	 * @param	string	The text for the contents of the <a> tag.
	 * @return	string	The link as an <a> tag.
	 * @since	1.6
	 */
	public static function simple($url, $text = '')
	{
		return'<a href="'.$url.'">'.$text.'</a>';
	}

	/**
	 * Method to generate an (X)HTML search engine friendly link as an <a> tag.
	 *
	 * <code>
	 *	<?php echo JHtml::_('klink.sef', $url, $text, ...); ?>
	 * </code>
	 *
	 * @param	string	The URL for the href attribute of the <a> tag.
	 * @param	string	The text for the contents of the <a> tag.
	 * @return	string	The link as an <a> tag.
	 * @since	1.6
	 */
	public static function sef($url, $text, $title, $rel, $class ='', $anker='', $attr='')
	{
		return '<a '.($class ? 'class="'.$class.'" ' : '').'href="'.JRoute::_($url).($anker?('#'.$anker):'').'" title="'.$title.'"'.($rel ? ' rel="'.$rel.'"' : '').($attr ? ' '.$attr : '').'>'.$text.'</a>';
	}

	/**
	 * Method to generate an (X)HTML search engine friendly link to the credits.
	 *
	 * <code>
	 *	<?php echo JHtml::_('klink.credits'); ?>
	 * </code>
	 *
	 * @return	string	The link as an <a> tag.
	 * @since	1.6
	 */
    public function credits()
    {
        return self::sef('http://www.kunena.com', 'Kunena', 'Kunena', 'follow', NULL, NULL, 'target="_blank"');
    }

    public function teamCredits($catid, $name='')
    {
        return self::sef(KUNENA_LIVEURLREL.'&amp;func=credits&amp;catid='.$catid, $name, NULL, 'follow');
    }

    public function kunena($name , $rel='follow')
    {
        return self::sef(KUNENA_LIVEURLREL, $name, NULL, $rel);
    }

    public function rss($name , $rel='follow')
    {
        return self::sef(KUNENA_LIVEURLREL.'&amp;func=rss', $name, NULL, $rel, NULL, NULL, 'target="_blank"');
    }

	/**
	 * Method to generate an (X)HTML search engine friendly link as an <a> tag.
	 *
	 * <code>
	 *	<?php echo JHtml::_('klink.category', $type, $catid, $catname ...); ?>
	 * </code>
	 *
	 * @param	string	the view type of the category view. Anay available view type inside the category view
	 * @param	string	...
	 * @param	string	...
	 * 	 * @return	string	The link as an <a> tag.
	 * @since	1.6
	 */
    public static function category($type, $catid, $catname, $rel='follow', $class='')
    {
        return self::sef(KUNENA_LIVEURLREL.'&amp;view=category&amp;type='.$type.'&amp;catid='.$catid, $catname, '', $rel, $class);
    }

    public function categoryPage($type, $catid, $page, $pagename, $rel='follow', $class='')
    {
        if ($page == 1 || !is_numeric($page))
        {
            // page 1 is identical to a link to the regular category link
            $pagelink  = self::category($type, $catid, $pagename, $rel, $class);
        }
        else
        {
            $pagelink  = self::sef(KUNENA_LIVEURLREL.'&amp;view=category&amp;type='.$type.'&amp;catid='.$catid.'&amp;page='.$page, $pagename, '', $rel, $class);
        }

        return $pagelink;
    }

//    function GetCategoryReviewListLink($catid, $catname, $rel='nofollow', $class='')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=review&amp;action=list&amp;catid='.$catid, $catname, '', $rel, $class);
//    }
//
//    function GetThreadLink($func, $catid, $threadid, $threadname, $title, $rel='follow', $class='')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;catid='.$catid.'&amp;id='.$threadid, $threadname, $title, $rel, $class);
//    }
//
//    function GetThreadPageLink($kunenaConfig, $func, $catid, $threadid, $page, $limit, $name, $anker='', $rel='follow', $class='')
//    {
//    	$kunenaConfig =& CKunenaConfig::getInstance();
//        if ($page == 1 || !is_numeric($page) || !is_numeric($limit))
//        {
//            // page 1 is identical to a link to the top of the thread
//            $pagelink  = CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;catid='.$catid.'&amp;id='.$threadid,
//            										$name, '', $rel, $class, $anker);
//        }
//        else
//        {
//            $pagelink  = CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;catid='.$catid.'&amp;id='.$threadid
//                          .'&amp;limit='.$limit.'&amp;limitstart='.(($page-1)*$limit), $name, '', $rel, $class, $anker);
//        }
//
//        return $pagelink;
//    }
//
//    // GetThreadPageURL is basically identically to the prior function except that it returns a clear text
//    // non-encoded URL. This functions is used by the email function to notify users about new posts.
//    function GetThreadPageURL($kunenaConfig, $func, $catid, $threadid, $page, $limit, $anker='')
//    {
//        if ($page == 1 || !is_numeric($page) || !is_numeric($limit))
//        {
//            // page 1 is identical to a link to the top of the thread
//            $pageURL = htmlspecialchars_decode(KUNENA_LIVEURLREL).'&func='.$func.'&catid='.$catid.'&id='.$threadid;
//        }
//        else
//        {
//            $pageURL = htmlspecialchars_decode(KUNENA_LIVEURLREL).'&func='.$func.'&catid='.$catid.'&id='.$threadid
//                          .'&limit='.$limit.'&limitstart='.(($page-1)*$limit);
//        }
//
//        return JRoute::_($pageURL).($anker?('#'.$anker):'');
//    }
//
//    function GetSamePageAnkerLink($anker, $name, $rel='nofollow')
//    {
//    	jimport('joomla.environment.request');
//        return CKunenaLink::GetSefHrefLink(JRequest::getURI(), $name, '', $rel, '', $anker);
//    }
//
//    function GetReportMessageLink($catid, $id, $name, $rel='nofollow')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=report&amp;catid='.$catid.'&amp;id='.$id, $name, '', $rel);
//    }
//
//    function GetMessageIPLink($msg_ip, $rel='nofollow')
//    {
//        if (!empty($msg_ip))
//        {
//            $iplink = '<a href="http://whois.domaintools.com/'.$msg_ip.'" target="_blank">';
//            $iplink .= 'IP: '.$msg_ip.'</a>';
//        }
//        else
//        {
//            $iplink = '&nbsp;';
//        }
//
//        return $iplink;
//    }
//
//    function GetMyProfileLink($kunenaConfig, $userid, $name, $rel='nofollow')
//    {
//    	$kunenaConfig =& CKunenaConfig::getInstance();
//    	if($kunenaConfig->kunena_profile != 'kunena')
//    	{
//    		return CKunenaLink::GetProfileLink($kunenaConfig, $userid, $name, $rel);
//    	}
//    	else
//    	{
//    		return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=myprofile', $name, '', $rel);
//    	}
//    }
//
//    function GetProfileLink($kunenaConfig, $userid, $name, $rel='nofollow', $class='')
//    {
//    	$kunenaProfile = CKunenaProfile::getInstance();
//		if ($link = $kunenaProfile->getProfileURL($userid))
//		{
//			return CKunenaLink::GetSefHrefLink($link, $name, '', $rel, $class);
//		}
//		else
//		{
//			return $name;
//		}
//    }
//
//	function GetUserlistURL($action='')
//	{
//		return JRoute::_(KUNENA_LIVEURLREL.'&amp;func=userlist'.$action);
//	}
//
//	function GetUserlistLink($action, $name, $rel='nofollow', $class='')
//	{
//		return self::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=userlist'.$action, $name, '', $rel, $class);
//	}
//
//    function GetViewLink($func, $id, $catid, $view, $name, $rel='nofollow')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;id='.$id.'&amp;view='.$view.'&amp;catid='.$catid, $name, '', $rel);
//    }
//
//    function GetPendingMessagesLink($catid, $name, $rel='nofollow')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=review&action=list&amp;catid='.$catid, $name, '', $rel);
//    }
//
//    function GetShowLatestLink($name, $rel='follow')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=latest', $name, '', $rel);
//    }
//
//    function GetShowLatestURL()
//    {
//        return JRoute::_(KUNENA_LIVEURLREL.'&amp;func=latest');
//    }
//
//    function GetShowMyLatestLink($name, $rel='nofollow')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=mylatest', $name, '', $rel);
//    }
//
//    function GetShowLatestThreadsLink($period, $name, $rel='nofollow')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel='.$period, $name, '', $rel);
//    }
//
//    // Function required to support default_ex template
//    function GetLatestPageLink($func, $page, $rel='follow', $class='', $sel='')
//    {
//    	// if ($func=='') $func = 'latest';
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;page='.$page.(($sel)?'&amp;sel='.$sel:''), $page, '', $rel, $class);
//    }
//
//    function GetPostNewTopicLink($catid, $name, $rel='nofollow')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=post&amp;do=reply&amp;catid='.$catid, $name, '', $rel);
//    }
//
//    function GetTopicPostLink($do, $catid, $id, $name, $rel='nofollow')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=post&amp;do='.$do.'&amp;catid='.$catid.'&amp;id='.$id, $name, '', $rel);
//    }
//
//    function GetTopicPostReplyLink($do, $catid, $id, $name, $rel='nofollow')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=post&amp;do='.$do.'&amp;catid='.$catid.'&amp;id='.$id, $name, '', $rel);
//    }
//
//    function GetEmailLink($email, $name)
//    {
//        return CKunenaLink::GetSimpleLink('mailto:'.stripslashes($email), stripslashes($name));
//    }
//
//    function GetKarmaLink($do, $catid, $pid, $userid, $name, $rel='nofollow')
//    {
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=karma&amp;do='.$do.'&amp;userid='.$userid.'&amp;pid='.$pid.'&amp;catid='.$catid, $name, '', $rel);
//    }
//
//    function GetRulesLink($kunenaConfig, $name, $rel='nofollow')
//    {
//		$kunenaConfig =& CKunenaConfig::getInstance();
//        $ruleslink = $kunenaConfig->rules_inkunena ? KUNENA_LIVEURLREL.'&amp;func=rules' : $kunenaConfig->rules_link;
//        return CKunenaLink::GetSefHrefLink($ruleslink, $name, '', $rel);
//    }
//
//    function GetHelpLink($kunenaConfig, $name, $rel='nofollow')
//    {
//    	$kunenaConfig =& CKunenaConfig::getInstance();
//        $helplink = $kunenaConfig->help_inkunena ? KUNENA_LIVEURLREL.'&amp;func=faq' : $kunenaConfig->help_link;
//        return CKunenaLink::GetSefHrefLink($helplink, $name, '', $rel);
//    }
//
//    function GetSearchURL($kunenaConfig, $func, $searchword, $limitstart, $limit, $params='')
//    {
//    	$kunenaConfig =& CKunenaConfig::getInstance();
//		$limitstr = "";
//    	if ($limitstart > 0) $limitstr .= "&amp;limitstart=$limitstart";
//		if ($limit > 0 && $limit != $kunenaConfig->messages_per_page_search) $limitstr .= "&amp;limit=$limit";
//		if ($searchword) $searchword = '&amp;q=' . urlencode($searchword);
//        return JRoute::_(KUNENA_LIVEURLREL."&amp;func={$func}&amp;q={$searchword}{$params}{$limitstr}");
//    }
//
//    function GetSearchLink($kunenaConfig, $func, $searchword, $limitstart, $limit, $name, $params='', $rel='nofollow')
//    {
//    	$kunenaConfig =& CKunenaConfig::getInstance();
//		$limitstr = "";
//    	if ($limitstart > 0) $limitstr .= "&amp;limitstart=$limitstart";
//		if ($limit > 0 && $limit != $kunenaConfig->messages_per_page_search) $limitstr .= "&amp;limit=$limit";
//		if ($searchword) $searchword = '&amp;q=' . urlencode($searchword);
//        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL."&amp;func={$func}{$searchword}{$params}{$limitstr}", $name, '', $rel);
//    }
//
//    function GetAnnouncementURL($kunenaConfig, $do, $id=NULL)
//    {
//		$idstring = '';
//		if ($id !== NULL) $idstring .= "&amp;id=$id";
//		return JRoute::_(KUNENA_LIVEURLREL."&amp;func=announcement&amp;do={$do}{$idstring}");
//    }
//
//    //
//    // Macro functions that build more complex html output with embedded links
//    //
//
//    //
//    // This function builds the auto redirect block to go back to the latest post of a particular thread
//    // It is used for various operations. Input parameter is any post id. It will determine the thread,
//    // latest post of that thread and number of pages based on the supplied page limit.
//    //
//    function GetLatestPostAutoRedirectHTML($kunenaConfig, $pid, $limit, $catid=0)
//    {
//        $kunena_db = &JFactory::getDBO();
//        // First determine the thread, latest post and number of posts for the post supplied
//        $where = '';
//		if ($catid > 0) $where .= " AND a.catid = {$catid} ";
//		$kunena_db->setQuery("SELECT a.id AS thread, a.latest_post_id AS latest_id, a.catid AS catid, a.posts AS totalmessages
//                             FROM #__kunena_threads AS a, #__kunena_messages AS b
//                             WHERE b.id='{$pid}' AND a.id = b.thread AND a.hold='0' {$where}");
//
//        $result = $kunena_db->loadObject();
//        	check_dberror("Unable to retrieve latest post.");
//
//        // Now Calculate the number of pages for this particular thread
//        if (is_object($result))
//        {
//        	$catid = $result->catid;
//        	$threadPages = ceil($result->totalmessages / $limit);
//        }
//
//        // Finally build output block
//
//        $Output  = '<div align="center">';
//        if (is_object($result)) $Output .= CKunenaLink::GetThreadPageLink($kunenaConfig, 'view', $catid, $result->thread, $threadPages, $limit, _POST_SUCCESS_VIEW, $result->latest_id) .'<br />';
//        $Output .= CKunenaLink::GetCategoryLink('showcat', $catid, _POST_SUCCESS_FORUM).'<br />';
//        $Output .= '</div>';
//        if (is_object($result)) $Output .= CKunenaLink::GetAutoRedirectHTML(CKunenaLink::GetThreadPageURL($kunenaConfig, 'view', $catid, $result->thread, $threadPages, $limit, $result->latest_id), 3500);
//        else $Output .= CKunenaLink::GetAutoRedirectHTML(JRoute::_(KUNENA_LIVEURLREL.'&amp;func=showcat&amp;catid='.$catid), 3500);
//
//        return $Output;
//    }
//
//    function GetLatestPageAutoRedirectURL($kunenaConfig, $pid, $limit, $catid=0)
//    {
//        $kunena_db = &JFactory::getDBO();
//        // First determine the thread, latest post and number of posts for the post supplied
//        $where = '';
//		if ($catid > 0) $where .= " AND a.catid = {$catid} ";
//        $kunena_db->setQuery("SELECT a.id AS thread, a.latest_post_id AS latest_id, a.catid AS catid, a.posts AS totalmessages
//                             FROM #__kunena_threads AS a, #__kunena_messages AS b
//                             WHERE b.id='{$pid}' AND a.id = b.thread AND a.hold='0' {$where}");
//        $result = $kunena_db->loadObject();
//        	check_dberror("Unable to retrieve latest post.");
//        if (!is_object($result)) return htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL.'&amp;func=showcat&amp;catid='.$catid));
//
//        // Now Calculate the number of pages for this particular thread
//		$catid = $result->catid;
//        $threadPages = ceil($result->totalmessages / $limit);
//
//		// Finally build output block
//        return htmlspecialchars_decode(CKunenaLink::GetThreadPageURL($kunenaConfig, 'view', $catid, $result->thread, $threadPages, $limit));
//    }
//
//    function GetLatestCategoryAutoRedirectHTML($catid)
//    {
//        $Output  = '<div id="Kunena_post_result" align="center">';
//        $Output .= CKunenaLink::GetCategoryLink('showcat', $catid, _POST_SUCCESS_FORUM).'<br />';
//        $Output .= '</div>';
//        $Output .= CKunenaLink::GetAutoRedirectHTML(KUNENA_LIVEURLREL . '&func=showcat&catid=' . $catid, 3500);
//
//        return $Output;
//    }
//
//    function GetAutoRedirectHTML($url, $timeout)
//    {
//		$url = htmlspecialchars_decode($url);
//        $Output = "\n<script type=\"text/javascript\">\n// <![CDATA[\n";
//        $Output .= "kunenaRedirectTimeout('$url', $timeout);";
//        $Output .= "\n// ]]>\n</script>\n";
//
//        return $Output;
//    }

}
