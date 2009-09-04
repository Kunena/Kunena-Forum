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
 * @author 		Louis, fxstein
 * @package 	Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
abstract class JHtmlKLink
{
	/**
	 * Method to generate an (X)HTML search engine friendly link as an <a> tag or plain text url.
	 *
	 * <code>
	 *	<?php echo JHtml::_('klink.link', 'atag', $url, $text, ...); ?>
	 * </code>
	 *
	 * @param $linktype	string	type of link: 'atag' for <a> tag, 'url' for plaintext url
	 * @param $url		string	The URL string
	 * @param $text		string	optional text for the contents of the <a> tag.
	 * @param $title	string	optional title for the contents of the <a> tag.
	 * @param $rel		string 	optional rel tag
	 * @param $class	string 	optional css class
	 * @param $anker	string  optional page anker: #anker
	 * @param $attr		string 	optional attr for <a> tag
	 * @return	string	The link as an <a> tag or plain text url.
	 * @since	1.6
	 */
	public static function link($linktype, $url, $text='', $title='', $rel='', $class ='', $anker='', $attr='')
	{
		$link = '';
		switch ($linktype)
		{
			case 'url':
				// JRoute::_($url, false) to supress encoding of & - only for palin text url
				$link = JRoute::_($url,false).($anker?('#'.$anker):'');
				break;
			case 'atag':
			default:
				$link = '<a '.($class ? 'class="'.$class.'" ' : '').'href="'.JRoute::_($url).($anker?('#'.$anker):'').'" '.($title ? ' title="'.$title.'"' : '').($rel ? ' rel="'.$rel.'"' : '').($attr ? ' '.$attr : '').'>'.$text.'</a>';
				break;

		}
		return $link;
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
        return self::link('atag', 'http://www.kunena.com', 'Kunena', 'Kunena', 'follow', NULL, NULL, 'target="_blank"');
    }

    public function teamCredits($catid, $name='')
    {
        return self::link('atag', KUNENA_RELURL.'&func=credits&catid='.$catid, $name, NULL, 'follow');
    }

    public function kunena($name)
    {
        return self::link('atag', KUNENA_RELURL, $name, NULL, 'follow');
    }

	/**
	 * Method to generate an (X)HTML search engine friendly link to a Kunena view. This method is used by
	 * various specialized view link helpers to return the <a> tag for particular links.
	 *
	 * <code>
	 *	<?php echo JHtml::_('klink.view', $linktype, $view, $param, $name, $title, ...); ?>
	 * </code>
	 *
	 * @param $linktype	string	type of link: 'atag' for <a> tag, 'url' for plaintext url
	 * @param $view		string	name of the view itself. e.g recent, thread, category, ...
	 * @param $param	string	param - the additional parameter required for that view
	 * @param $name		string	text for the link to be displayed to the user
	 * @param $title	string	link title
	 * @param $page		integer	optional page number; 1 will surpress limit and limitstart parameters
     * @param $limit	integer optional limit of items per page; model will set this as per backend config settings for the view
     * @param $type		string	optional type override for view if not default
     * @param $format	string	optional format override for view if not default
     * @param $rel		string	optional <a> rel modifier; default is 'follow'
     * @param $class	string 	optional css class for <a> tag
     * @param $anker	string	optional page anker for <a> tag
	 *
	 * @return	string	The link as an <a> tag.
	 *
	 * @since	1.6
	 */
    public static function view($linktype, $view, $param, $name, $title, $type='', $format='', $rel='follow', $class='', $anker='')
    {
        return self::link($linktype, KUNENA_RELURL.'&view='.$view.($type?'&type='.$type:'').($format?'&format='.$format:'').$param, $name, $title, $rel, $class, $anker);
    }
    
    /**
	 * Method to generate an (X)HTML search engine friendly link as an <a> tag.
	 * Specialized helper for category views.
	 *
	 * <code>
	 *	<?php echo JHtml::_('klink.category', 'atag', $catid, $name, $title, ...); ?>
	 * </code>
	 *
	 * @param $linktype	string	type of link: 'atag' for <a> tag, 'url' for plaintext url
	 * @param $catid	integer	category id to be displayed by the view. catid = 0 returns top level category overview
	 * @param $name		string	text for the link to be displayed to the user
	 * @param $title	string	link title
	 * @param $page		integer	optional page number; 1 will surpress limit and limitstart parameters
     * @param $limit	integer optional limit of items per page; model will set this as per backend config settings for the view
     * @param $type		string	optional type override for view if not default
     * @param $format	string	optional format override for view if not default
     * @param $rel		string	optional <a> rel modifier; default is 'follow'
     * @param $class	string 	optional css class for <a> tag
     * @param $anker	string	optional page anker for <a> tag
	 *
	 * @return	string	The link as an <a> tag.
	 *
	 * @since	1.6
	 */
    public function category($linktype, $catid, $name, $title, $page=1, $limit=20, $type='', $format='', $rel='follow', $class='', $anker='')
    {
        if ($page == 1 || !is_numeric($page))
        {
    		$pagelink = self::view($linktype, 'category', '&category='.$catid, $name, $title, $type, $format, $rel, $class, $anker);
        }
        else
        {
    		$pagelink = self::view($linktype, 'category', '&category='.$catid.'&limit='.$limit.'&limitstart='.(($page-1)*$limit), $name, $title, $type, $format, $rel, $class, $anker);
        }

        return $pagelink;
    }

	/**
	 * Method to generate an (X)HTML search engine friendly link as an <a> tag.
	 * Specialized helper for thread views.
	 *
	 * <code>
	 *	<?php echo JHtml::_('klink.thread', 'atag', $threadid, $name, $title, ...); ?>
	 * </code>
	 *
	 * @param $linktype	string	type of link: 'atag' for <a> tag, 'url' for plaintext url
	 * @param $threadid	integer	thread id to be displayed by the view.
	 * @param $name		string	text for the link to be displayed to the user
	 * @param $title	string	link title
	 * @param $page		integer	optional page number; 1 will surpress limit and limitstart parameters
     * @param $limit	integer optional limit of items per page; model will set this as per backend config settings for the view
     * @param $type		string	optional type override for view if not default
     * @param $format	string	optional format override for view if not default
     * @param $rel		string	optional <a> rel modifier; default is 'follow'
     * @param $class	string 	optional css class for <a> tag
     * @param $anker	string	optional page anker for <a> tag
	 *
	 * @return	string	The link as an <a> tag.
	 *
	 * @since	1.6
	 */
	public function thread($linktype, $threadid, $name, $title, $page=1, $limit=20, $type='', $format='', $rel='follow', $class='', $anker='')
    {
        if ($page == 1 || !is_numeric($page))
        {
    		$pagelink = self::view($linktype, 'thread', '&thread='.$threadid, $name, $title, $type, $format, $rel, $class, $anker);
        }
        else
        {
    		$pagelink = self::view($linktype, 'thread', '&thread='.$threadid.'&limit='.$limit.'&limitstart='.(($page-1)*$limit), $name, $title, $type, $format, $rel, $class, $anker);
        }

        return $pagelink;
    }

    /**
	 * Method to generate an (X)HTML search engine friendly link as an <a> tag.
	 * Specialized helper for recent views.
	 *
	 * <code>
	 *	<?php echo JHtml::_('klink.recent', 'atag', $name, $title, ...); ?>
	 * </code>
	 *
	 * @param $linktype	string	type of link: 'atag' for <a> tag, 'url' for plaintext url
	 * @param $name		string	text for the link to be displayed to the user
	 * @param $title	string	link title
	 * @param $page		integer	optional page number; 1 will surpress limit and limitstart parameters
     * @param $limit	integer optional limit of items per page; model will set this as per backend config settings for the view
     * @param $type		string	optional type override for view if not default
     * @param $format	string	optional format override for view if not default
     * @param $rel		string	optional <a> rel modifier; default is 'follow'
     * @param $class	string 	optional css class for <a> tag
     * @param $anker	string	optional page anker for <a> tag
	 *
	 * @return	string	The link as an <a> tag.
	 *
	 * @since	1.6
	 */
    public function recent($linktype, $name, $title, $page=1, $limit=20, $type='', $format='', $rel='follow', $class='', $anker='')
    {
        if ($page == 1 || !is_numeric($page))
        {
    		$pagelink = self::view($linktype, 'recent', $name, $title, $type, $format, $rel, $class, $anker);
        }
        else
        {
    		$pagelink = self::view($linktype, 'recent', '&limit='.$limit.'&limitstart='.(($page-1)*$limit), $name, $title, $type, $format, $rel, $class, $anker);
        }

        return $pagelink;
    }

    /**
	 * Method to generate an (X)HTML search engine friendly link as an <a> tag.
	 * Specialized helper for link to user profile.
	 *
	 * <code>
	 *	<?php echo JHtml::_('klink.user', 'atag', $name, $title, ...); ?>
	 * </code>
	 *
	 * @param $linktype	string	type of link: 'atag' for <a> tag, 'url' for plaintext url
	 * @param $name		string	text for the link to be displayed to the user
	 * @param $title	string	link title
     * @param $type		string	optional type override for view if not default
     * @param $format	string	optional format override for view if not default
     * @param $rel		string	optional <a> rel modifier; default is 'follow'
     * @param $class	string 	optional css class for <a> tag
     * @param $anker	string	optional page anker for <a> tag
	 *
	 * @return	string	The link as an <a> tag.
	 *
	 * @since	1.6
	 */
    public function user($linktype, $userid, $name, $title, $type='', $format='', $rel='follow', $class='', $anker='')
    {
        //TODO: Insert user profile link define into function call
        return self::link($linktype, 'insert-link-to-user-profile-here', $name, $title, $type, $format, $rel, $class, $anker);
    }

    public function pageAnker($linktype, $anker, $name, $rel='nofollow')
    {
    	jimport('joomla.environment.request');
        return self::link($linktype, JRequest::getURI(), $name, NULL, $rel, NULL, $anker);
    }

    function reportMessage($messageid, $name, $rel='nofollow')
    {
        return self::link('atag', KUNENA_RELURL.'&view=report&messageid='.$messageid, $name, '', $rel);
    }

    function messageIP($msg_ip, $rel='nofollow')
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

	public function userList($linktype, $action, $name, $rel='nofollow', $class='')
	{
		return self::link($linktype, KUNENA_RELURL.'&view=userlist'.$action, $name, '', $rel, $class);
	}

    public function pendingMessages($linktype, $catid, $name, $rel='nofollow')
    {
        return self::link($linktype, KUNENA_RELURL.'&view=pending&category='.$catid, $name, '', $rel);
    }

    public function postNewThread($linktype, $catid, $name, $rel='nofollow')
    {
        return self::link($linktype, KUNENA_RELURL.'&view=post&type=newthread&category='.$catid, $name, '', $rel);
    }

    public function postRely($linktype, $messageid, $name, $rel='nofollow')
    {
        return self::link($linktype, KUNENA_RELURL.'&view=post&type=reply&message='.$messageid, $name, '', $rel);
    }
    
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
