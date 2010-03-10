<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();


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
    function GetSimpleLink($id, $name='', $class='', $attr='')
    {
        return'<a '.($class ? 'class="'.$class.'" ' : '').' href="'.$id.'" '.($attr ? ' '.$attr : '').'>'.$name.'</a>';
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

    function GetKunenaURL($redirect=false)
    {
    	return $redirect == false ? JRoute::_(KUNENA_LIVEURLREL) : htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL));
    }

    function GetRSSLink($name , $rel='follow', $params = '')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=rss' . $params, $name, '', $rel, '', '', 'target="_blank"');
    }

    function GetRSSURL($params = '')
    {
    	return JRoute::_ ( KUNENA_LIVEURLREL . '&amp;func=rss' . $params );
    }

    function GetPDFLink($catid, $id , $name, $rel='nofollow', $title='')
    {
    	return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;id=' . $id . '&amp;catid=' . $catid . '&amp;func=fb_pdf' , $name , $title , $rel);
    }

    function GetCategoryLink($func, $catid, $catname, $rel='follow', $class='', $title='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;catid='.$catid, $catname, $title, $rel, $class);
    }

	//to get & instead of &amp; for redirect and JS links set redirect to true
    function GetCategoryURL($func, $catid='', $redirect=false)
    {
    	$strcatid = '';
		if ($catid!='') $strcatid = "&amp;catid={$catid}";
    	$c_url = JRoute::_ ( KUNENA_LIVEURLREL.'&amp;func='.$func.$strcatid );
    	return $redirect == false ? $c_url : htmlspecialchars_decode ( $c_url);
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

    function GetReviewURL($redirect=false)
    {
    	return $redirect == false ? JRoute::_(KUNENA_LIVEURLREL . '&amp;func=review') : htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL . '&amp;func=review'));
    }

    function GetCategoryReviewListLink($catid, $catname, $rel='nofollow', $class='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=review&amp;action=list&amp;catid='.$catid, $catname, '', $rel, $class);
    }

    function GetThreadLink($func, $catid, $threadid, $threadname, $title, $rel='follow', $class='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;catid='.$catid.'&amp;id='.$threadid, $threadname, $title, $rel, $class);
    }

    function GetThreadPageLink($kunena_config, $func, $catid, $threadid, $page, $limit, $name, $anker='', $rel='follow', $class='')
    {
    	$kunena_config =& CKunenaConfig::getInstance();
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
    function GetThreadPageURL($kunena_config, $func, $catid, $threadid, $page, $limit='', $anker='',$redirect=false)
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

        return $redirect == false ? JRoute::_($pageURL).($anker?('#'.$anker):'') : htmlspecialchars_decode(JRoute::_($pageURL).($anker?('#'.$anker):''));
    }

    function GetSamePageAnkerLink($anker, $name, $rel='nofollow')
    {
    	jimport('joomla.environment.request');
        return CKunenaLink::GetSefHrefLink(JRequest::getURI(), $name, '', $rel, '', $anker);
    }

    function GetReportURL($redirect=false)
    {
    	return $redirect == false ? JRoute::_(KUNENA_LIVEURLREL.'&amp;func=report') : htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL.'&amp;func=report'));
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

    function GetMyProfileLink($kunena_config, $userid, $name, $rel='nofollow')
    {
    	$kunena_config =& CKunenaConfig::getInstance();
    	if($kunena_config->fb_profile == 'jomsocial' || $kunena_config->fb_profile == 'cb' || $kunena_config->fb_profile == 'aup')
    	{
    		return CKunenaLink::GetProfileLink($kunena_config, $userid, $name, $rel);
    	}
    	else
    	{
    		return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=profile', $name, '', $rel);
    	}
    }

	//do only for kunena own profile!
    function GetMyProfileURL($kunena_config, $userid='', $name='', $rel='nofollow', $redirect=false,$do='')
    {
    	$kunena_config =& CKunenaConfig::getInstance();
    	if($kunena_config->fb_profile == 'jomsocial' || $kunena_config->fb_profile == 'cb' || $kunena_config->fb_profile == 'aup')
    	{
    		$link = CKunenaLink::GetProfileURL($userid);
    		if (!empty($link))
    		{
    			return $redirect != true ? $link : htmlspecialchars_decode($link);
    		}
    	}
    	else
    	{
    		$do_do = $do != '' ? '&do='.$do : '';
    		return $redirect != true ? JRoute::_(KUNENA_LIVEURLREL.'&amp;func=profile'.$do_do, $name, '', $rel) : htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL.'&amp;func=myprofile'.$do_do, $name, '', $rel));
    	}
    }

 	function GetProfileSettingsURL($kunena_config, $userid='', $name='', $rel='nofollow', $redirect=false,$do='')
    {
    	$do_do = $do != '' ? '&do='.$do : '';
    	return JRoute::_(KUNENA_LIVEURLREL.'&amp;func=profilesettings'.$do_do, $name, '', $rel);
    }

    function GetProfileLink($kunena_config, $userid, $name, $rel='nofollow', $class='')
    {
    	if ($userid > 0)
    	{
    		$class = '';
			if (CKunenaTools::isAdmin($userid)) { $class = 'admin'; } else if (CKunenaTools::isModerator($userid)) { $class = 'moderator'; } else { $class = ''; }
    		$link = CKunenaLink::GetProfileURL($userid);
    		if (!empty($link)) return CKunenaLink::GetHrefLink($link, $name, '', $rel, $class);
    	}
   		return $name;
    }

    function GetProfileURL($userid)
    {
    	$kunena_config =& CKunenaConfig::getInstance();
    	// Only create links for valid users
    	if ($userid > 0)
    	{
    		if($kunena_config->fb_profile == 'cb')
    		{
    			$kunenaProfile =& CKunenaCBProfile::getInstance();
    			return $kunenaProfile->getProfileURL($userid);
    		}
    		elseif ($kunena_config->fb_profile == 'jomsocial')
    		{
   				return CRoute::_('index.php?option=com_community&view=profile&userid='.$userid);
    		}
    		elseif ($kunena_config->fb_profile == 'aup')
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

    //set redirect to true if you need & and not &amp;
    function GetMyProfileAvatarURL($action='' , $redirect=false)
    {
    	$action_do = $action !== '' ? '&action='.$action : '';
    	$return = $redirect != true ? JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'.$action_do) : htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'.$action_do));
    	return $return;
    }

    function GetMyProfileAvatarLink($name, $title='',$action='',$rel='nofollow')
    {
    	$action_do = $action !== '' ? '&action='.$action : '';
    	return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'.$action_do, $name, $title, $rel);
    }

    //Used in myprofile_avatar_upload.php
    function GetMyProfilAvatarGalleryURL()
    {
    	return htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar&gallery='));
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

    function GetShowNoRepliesLink($name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=noreplies', $name, '', $rel);
    }

    function GetShowLatestThreadsLink($period, $name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=latest&amp;do=show&amp;sel='.$period, $name, '', $rel);
    }

    function GetShowLatestThreadsURL($period)
    {
    	return JRoute::_( KUNENA_LIVEURLREL . '&amp;func=latest&amp;do=show&amp;sel='.$period );
    }

    // Function required to support default template
    function GetLatestPageLink($func, $page, $rel='follow', $class='', $sel='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func='.$func.'&amp;page='.$page.(($sel)?'&amp;sel='.$sel:''), $page, '', $rel, $class);
    }

    function GetPostURL($catid='', $redirect=false)
    {
    	$cat = '';
    	if($catid!='') $cat = "&amp;catid={$catid}";
    	return $redirect == false ? JRoute::_ ( KUNENA_LIVEURLREL . '&amp;func=post'.$cat ) : htmlspecialchars_decode(JRoute::_ ( KUNENA_LIVEURLREL . '&amp;func=post'.$cat ));
    }

    function GetPostNewTopicLink($catid, $name, $rel='nofollow', $class='', $title='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=post&amp;do=reply&amp;catid='.$catid, $name, $title, $rel, $class);
    }

    function GetTopicPostLink($do, $catid, $id, $name, $rel='nofollow', $class='', $title='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=post&amp;do='.$do.'&amp;catid='.$catid.'&amp;id='.$id, $name, $title, $rel, $class);
    }

    function GetTopicPostReplyLink($do, $catid, $id, $name, $rel='nofollow', $class='', $title='', $attr='')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=post&amp;do='.$do.'&amp;catid='.$catid.'&amp;id='.$id, $name, $title, $rel, $class, '', $attr);
    }

    function GetEmailLink($email, $name)
    {
        return CKunenaLink::GetSimpleLink('mailto:'.stripslashes($email), stripslashes($name));
    }

    function GetKarmaLink($do, $catid, $pid, $userid, $name, $rel='nofollow')
    {
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=karma&amp;do='.$do.'&amp;userid='.$userid.'&amp;pid='.$pid.'&amp;catid='.$catid, $name, '', $rel);
    }

    function GetRulesLink($kunena_config, $name, $rel='nofollow')
    {
		$kunena_config =& CKunenaConfig::getInstance();
        $ruleslink = $kunena_config->rules_infb ? KUNENA_LIVEURLREL.'&amp;func=rules' : $kunena_config->rules_link;
        return CKunenaLink::GetSefHrefLink($ruleslink, $name, '', $rel);
    }

    function GetHelpLink($kunena_config, $name, $rel='nofollow')
    {
    	$kunena_config =& CKunenaConfig::getInstance();
        $helplink = $kunena_config->help_infb ? KUNENA_LIVEURLREL.'&amp;func=help' : $kunena_config->help_link;
        return CKunenaLink::GetSefHrefLink($helplink, $name, '', $rel);
    }

    function GetSearchURL($kunena_config, $func, $searchword, $limitstart, $limit, $params='')
    {
    	$kunena_config =& CKunenaConfig::getInstance();
		$limitstr = "";
    	if ($limitstart > 0) $limitstr .= "&amp;limitstart=$limitstart";
		if ($limit > 0 && $limit != $kunena_config->messages_per_page_search) $limitstr .= "&amp;limit=$limit";
		if ($searchword) $searchword = '&amp;q=' . urlencode($searchword);
        return JRoute::_(KUNENA_LIVEURLREL."&amp;func={$func}{$searchword}{$params}{$limitstr}");
    }

    function GetSearchLink($kunena_config, $func, $searchword, $limitstart, $limit, $name, $params='', $rel='nofollow')
    {
    	$kunena_config =& CKunenaConfig::getInstance();
		$limitstr = "";
    	if ($limitstart > 0) $limitstr .= "&amp;limitstart=$limitstart";
		if ($limit > 0 && $limit != $kunena_config->messages_per_page_search) $limitstr .= "&amp;limit=$limit";
		if ($searchword) $searchword = '&amp;q=' . urlencode($searchword);
        return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL."&amp;func={$func}{$searchword}{$params}{$limitstr}", $name, '', $rel);
    }

    function GetAnnouncementURL($kunena_config, $do, $id=NULL)
    {
		$idstring = '';
		if ($id !== NULL) $idstring .= "&amp;id=$id";
		return JRoute::_(KUNENA_LIVEURLREL."&amp;func=announcement&amp;do={$do}{$idstring}");
    }

    function GetAnnouncementLink($kunena_config, $do, $id=NULL , $name, $title, $rel='nofollow')
	{
		$idstring = '';
		if ($id !== NULL) $idstring .= "&amp;id=$id";
		return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL."&amp;func=announcement&amp;do={$do}{$idstring}",$name,$title,$rel);
	}

	function GetPollURL($kunena_config, $do, $id=NULL, $catid){
		  $idstring = '';
		  if ($id !== NULL) $idstring .= "&amp;id=$id";
		  $catidstr = "&amp;catid=$catid";
		  return JRoute::_(KUNENA_LIVEURLREL."&amp;func=poll&amp;do={$do}{$idstring}{$catidstr}");
    }

    function GetJsonURL($action, $do=''){

    	return JRoute::_(KUNENA_LIVEURLREL."&amp;func=json;&amp;action=$action&amp;do=$do");
    }

    function GetMarkThisReadLink( $catid, $name, $rel='nofollow', $title='')
    {
    	return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=markThisRead&amp;catid=' . $catid , $name , $title , $rel );
    }

    function GetWhoIsOnlineLink($name, $class, $rel='follow')
    {
    	return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&func=who', $name, '', $rel, $class);
    }

    function GetStatsLink($name, $class='', $rel='follow')
    {
    	return CKunenaLink::GetSefHrefLink(KUNENA_LIVEURLREL.'&amp;func=stats', $name, '', $rel, $class);
    }

    //
    // Functions for the different pm extensions
    //
    function GetUddeImLink($userid, $name, $rel='nofollow')
    {
    	return CKunenaLink::GetSefHrefLink('index.php?option=com_uddeim&amp;task=new&recip=' . $userid, $name, '', $rel);
    }

    //
    //Some URL functions for the discuss bot
    //
    function GetContentView( $rowid, $rowItemid)
    {
    	return JRoute::_ ( 'index.php?option=com_content&amp;task=view&amp;Itemid=' . $rowItemid . '&amp;id=' . $rowid );
    }

    //
    // Jomsocial
    //
    function GetJomsocialUserListLink($name, $rel='nofollow')
    {
    	return CKunenaLink::GetSefHrefLink('index.php?option=com_community&view=search&task=browse', $name, '', $rel);
    }

    function GetJomsocialLoginLink($name, $rel='nofollow')
    {
    	return CKunenaLink::GetSefHrefLink('index.php?option=com_community&amp;view=frontpage',$name, '', $rel);
    }

    function GetJomsocialRegisterLink($name, $rel='nofollow')
    {
    	return CKunenaLink::GetSefHrefLink('index.php?option=com_community&amp;view=register', $name, '', $rel);
    }

    //
    //CB
    //
    function GetCBUserListLink($name, $rel='nofollow')
    {
    	$cb_url = CKunenaCBProfile::getUserListURL();
    	return CKunenaLink::GetHrefLink($cb_url, $name, '', $rel);
    }

    function GetCBLoginLink($name, $rel='nofollow')
    {
    	$cb_url = CKunenaCBProfile::getLoginURL();
    	return CKunenaLink::GetHrefLink($cb_url,$name, '', $rel);
    }

    function GetCBLogoutLink($name, $rel='nofollow')
    {
    	$cb_url = CKunenaCBProfile::getLogoutURL();
    	return CKunenaLink::GetHrefLink($cb_url,$name,'',$rel);
    }

    function GetCBRegisterLink($name, $rel='nofollow')
    {
    	$cb_url = CKunenaCBProfile::getRegisterURL();
    	return CKunenaLink::GetHrefLink($cb_url,$name,'',$rel);
    }

    function GetCBLostPWLink($name, $rel='nofollow')
    {
    	$cb_url = CKunenaCBProfile::getLostPasswordURL();
    	return CKunenaLink::GetHrefLink($cb_url,$name,'',$rel);
    }

    //
    // Joomla links
    //
    function GetLoginLink($name, $rel='nofollow')
    {
    	return CKunenaLink::GetSefHrefLink('index.php?option=com_user&amp;view=login',$name, '', $rel);
    }

    function GetRegisterLink($name, $rel='nofollow')
    {
    	return CKunenaLink::GetSefHrefLink('index.php?option=com_user&amp;view=register',$name, '', $rel);
    }

    function GetLostpassLink($name, $rel='nofollow')
    {
    	return CKunenaLink::GetSefHrefLink('index.php?option=com_user&amp;view=reset',$name, '', $rel);
    }

    function GetLostuserLink($name, $rel='nofollow')
    {
    	return CKunenaLink::GetSefHrefLink('index.php?option=com_user&amp;view=remind',$name, '', $rel);
    }

    //
    // Macro functions that build more complex html output with embedded links
    //

    //
    // This function builds the auto redirect block to go back to the latest post of a particular thread
    // It is used for various operations. Input parameter is any post id. It will determine the thread,
    // latest post of that thread and number of pages based on the supplied page limit.
    //
    function GetLatestPostAutoRedirectHTML($kunena_config, $pid, $limit, $catid=0)
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
        if (is_object($result)) $Output .= CKunenaLink::GetThreadPageLink($kunena_config, 'view', $catid, $result->thread, $threadPages, $limit, JText::_('COM_KUNENA_POST_SUCCESS_VIEW'), $result->latest_id) .'<br />';
        $Output .= CKunenaLink::GetCategoryLink('showcat', $catid, JText::_('COM_KUNENA_POST_SUCCESS_FORUM')).'<br />';
        $Output .= '</div>';
        if (is_object($result)) $Output .= CKunenaLink::GetAutoRedirectHTML(CKunenaLink::GetThreadPageURL($kunena_config, 'view', $catid, $result->thread, $threadPages, $limit, $result->latest_id), 3500);
        else $Output .= CKunenaLink::GetAutoRedirectHTML(JRoute::_(KUNENA_LIVEURLREL.'&amp;func=showcat&amp;catid='.$catid), 3500);

        return $Output;
    }

    function GetLatestPageAutoRedirectURL($kunena_config, $pid, $limit, $catid=0)
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
        return htmlspecialchars_decode(CKunenaLink::GetThreadPageURL($kunena_config, 'view', $catid, $result->thread, $threadPages, $limit));
    }

    function GetLatestCategoryAutoRedirectHTML($catid)
    {
        $Output  = '<div id="Kunena_post_result" align="center">';
        $Output .= CKunenaLink::GetCategoryLink('showcat', $catid, JText::_('COM_KUNENA_POST_SUCCESS_FORUM')).'<br />';
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

    function GetAutoRedirectThreadPageHTML($kunena_config, $func, $catid, $threadid, $page, $limit='', $anker='',$timeout)
    {
    	$p_url = CKunenaLink::GetThreadPageURL($kunena_config,$func,$catid,$threadid,$page,$limit,$anker);
    	return CKunenaLink::GetAutoRedirectHTML( $p_url, $timeout);
    }
}
?>
