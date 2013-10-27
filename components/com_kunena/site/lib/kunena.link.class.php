<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Lib
 *
 * Based on FireBoard Component
 * @copyright   (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 * @link        http://www.bestofjoomla.com
 *
 *
 **/
defined('_JEXEC') or die;

/**
 * Deprecated class placeholder. You should use methods in topic, message and user classes instead to get links.
 *
 * @package     Kunena.Site
 * @subpackage  Lib
 * @since       1.0.7
 * @deprecated  3.0.0
 */
class CKunenaLink
{
	/**
	 * Basic universal href link
	 *
	 * @param   string  $link   Link to be returned
	 * @param   string  $name   Name for the link
	 * @param   string  $title  Title for the link
	 * @param   string  $rel    Rel attribute for the link
	 * @param   string  $class  Class attribute for the link
	 * @param   string  $anker  Anker attribute for the link
	 * @param   string  $attr   Others attributes for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetHrefLink($link, $name, $title = '', $rel = 'nofollow', $class = '', $anker = '', $attr = '')
	{
		return '<a ' . ($class ? 'class="' . $class . '" ' : '') . 'href="' . $link . ($anker ? ('#' . $anker) : '') . '" title="' . $title . '"' . ($rel ? ' rel="' . $rel . '"' : '') . ($attr ? ' ' . $attr : '') . '>' . $name . '</a>';
	}

	/**
	 * Basic universal href link
	 *
	 * @param   string  $link   Link to be returned
	 * @param   string  $name   Name for the link
	 * @param   string  $title  Title for the link
	 * @param   string  $rel    Rel attribute for the link
	 * @param   string  $class  Class attribute for the link
	 * @param   string  $anker  Anker attribute for the link
	 * @param   string  $attr   Others attributes for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetSefHrefLink($link, $name, $title = '', $rel = 'nofollow', $class = '', $anker = '', $attr = '')
	{
		$uri = $link instanceof JUri ? $link : JUri::getInstance($link);

		if ($anker)
		{
			$uri->setFragment($anker);
		}

		return JHtml::_('kunenaforum.link', $uri, $name, $title, $class, $rel, $attr);
	}

	/**
	 * Method to get attachment link
	 *
	 * @param   string  $folder    Folder where is located the attachment
	 * @param   string  $filename  Name of the attachment
	 * @param   string  $name      Name for the link
	 * @param   string  $title     Title for the link
	 * @param   string  $rel       Rel attribute for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetAttachmentLink($folder,$filename,$name,$title = '', $rel = 'nofollow')
	{
		return self::GetHrefLink(JUri::root() . "{$folder}/{$filename}", $name, $title, $rel);
	}

	/**
	 * Method to get RSS Link
	 *
	 * @param   string  $name    Name for the link
	 * @param   string  $rel     Rel attribute for the link
	 * @param   string  $params  Extra parameters for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetRSSLink($name, $rel = 'follow', $params = '')
	{
		return self::GetHrefLink(self::GetRSSURL($params), $name, '', $rel, '', '', 'target="_blank"');
	}

	/**
	 * Method to get RSS URL
	 *
	 * @param   string  $params  Extra parameters for the URL
	 * @param   string  $xhtml   Replace & by & for XML compilance.
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetRSSURL($params = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();

		// Do it only for the basic generic rss.
		if (($config->rss_feedburner_url) && ($params == ''))
		{
			return $config->rss_feedburner_url;
		}
		else
		{
			return KunenaRoute::_("index.php?option=com_kunena&view=topics&format=feed&layout=default&mode=topics{$params}", $xhtml);
		}
	}

	/**
	 * Method to get Category Action Link
	 *
	 * @param   string  $task     Task will be executed in the controller
	 * @param   int     $catid    Catid to execute the task
	 * @param   string  $catname  Name of the category
	 * @param   string  $rel      Rel attribute for the link
	 * @param   string  $class    Class attribute for the link
	 * @param   string  $title    Title attribute for the link
	 * @param   string  $extra    Extra parameter for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetCategoryActionLink($task, $catid, $catname, $rel = 'follow', $class = '', $title = '', $extra = '')
	{
		$token = '&' . JSession::getFormToken() . '=1';

		return self::GetSefHrefLink("index.php?option=com_kunena&view=category&task={$task}&catid={$catid}{$extra}{$token}", $catname, $title, $rel, $class);
	}

	/**
	 * Method to get Category Review List Link
	 *
	 * @param   int     $catid    Catid to execute the task
	 * @param   string  $catname  Name of the category
	 * @param   string  $rel      Rel attribute for the link
	 * @param   string  $class    Class attribute for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetCategoryReviewListLink($catid, $catname, $rel = 'nofollow', $class = '')
	{
		return self::GetSefHrefLink("index.php?option=com_kunena&view=review&action=list&catid={$catid}", $catname, '', $rel, $class);
	}

	/**
	 * Method to get Page Anker Link
	 *
	 * @param   int     $anker  ID to set the anker in the link
	 * @param   string  $name   Name for the link
	 * @param   string  $rel    Rel attribute for the link
	 * @param   string  $class  Class attribute for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetSamePageAnkerLink($anker, $name, $rel = 'nofollow', $class = '')
	{
		return '<a ' . ($class ? 'class="' . $class . '" ' : '') . 'href="#' . $anker . '"' . ($rel ? ' rel="' . $rel . '"' : '') . '>' . $name . '</a>';
	}

	/**
	 * Method to get Report Message Link
	 *
	 * @param   int     $catid  Catid to execute the task
	 * @param   int     $id     Id of the message
	 * @param   string  $name   Name for the link
	 * @param   string  $rel    Rel attribute for the link
	 * @param   string  $class  Class attribute for the link
	 * @param   string  $title  Title attribute for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetReportMessageLink($catid, $id, $name, $rel = 'nofollow', $class = '', $title = '')
	{
		$message = KunenaForumMessageHelper::get($id);

		return self::GetSefHrefLink("index.php?option=com_kunena&view=topic&layout=report&catid={$catid}&id={$message->thread}&mesid={$message->id}", $name, $title, $rel, $class);
	}

	/**
	 * Method to get link to see details on the IP adress
	 *
	 * @param   string  $msg_ip  IP adress of the user which has posted the message
	 * @param   string  $rel     Rel attribute for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetMessageIPLink($msg_ip, $rel = 'nofollow')
	{
		if (! empty ( $msg_ip ))
		{
			$iplink = '<a href="http://whois.domaintools.com/' . $msg_ip . '" target="_blank">';
			$iplink .= 'IP: ' . $msg_ip . '</a>';
		}
		else
		{
			$iplink = '&nbsp;';
		}

		return $iplink;
	}

	/**
	 * Method the get the link of the user profile
	 *
	 * @param   string  $userid  Userid
	 * @param   string  $name    Name of the user
	 * @param   string  $rel     Rel attribute for the link
	 * @param   string  $task    Task attribute for the link
	 * @param   string  $class   Class attribute for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetMyProfileLink($userid, $name = null, $rel = 'nofollow', $task = '', $class = '')
	{
		if (!$name)
		{
			$profile = KunenaFactory::getUser($userid);
			$name = htmlspecialchars($profile->getName(), ENT_COMPAT, 'UTF-8');
		}

		return self::GetHrefLink(self::GetMyProfileURL($userid, $task), $name, '', $rel, $class);
	}

	/**
	 * Method to get the URL of the user profile
	 *
	 * @param   int     $userid  Userid
	 * @param   string  $task    Task attribute for the link
	 * @param   string  $xhtml   Replace & by & for XML compilance.
	 * @param   string  $extra   Extra parameter for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetMyProfileURL($userid = 0, $task = '', $xhtml = true, $extra = '')
	{
		$my = JFactory::getUser();

		if ($userid && $userid != $my->id)
		{
			$userid = "&userid=$userid";
		}
		else
		{
			$userid = '';
		}

		if ($task)
		{
			$task = "&do=$task";
		}

		return KunenaRoute::_("index.php?option=com_kunena&view=profile{$userid}{$task}{$extra}", $xhtml);
	}

	/**
	 * Method to get Userlist URL
	 *
	 * @param   string  $action  Task which will be executed
	 * @param   string  $xhtml   Replace & by & for XML compilance.
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetUserlistURL($action = '', $xhtml = true)
	{
		$profile = KunenaFactory::getProfile();

		return $profile->getUserListURL($action, $xhtml);
	}

	/**
	 * Method to get the link to moderate an user
	 *
	 * @param   int     $userid  Userid
	 * @param   string  $name    Name of the link
	 * @param   string  $title   Title of the link
	 * @param   string  $rel     Rel attirbute for the link
	 * @param   string  $class   Class attirbute for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetModerateUserLink($userid, $name = null, $title ='', $rel = 'nofollow', $class = '')
	{
		return self::GetSefHrefLink("index.php?option=com_kunena&view=moderateuser&userid={$userid}", $name, $title, $rel, $class);
	}

	/**
	 * Method to get the link of user list
	 *
	 * @param   string  $action  Task which will executed in the controller
	 * @param   string  $name    Name of the link
	 * @param   string  $rel     Rel attirbute for the link
	 * @param   string  $class   Class attirbute for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetUserlistLink($action, $name, $rel = 'nofollow', $class = '')
	{
		$link = self::GetUserlistURL($action);

		if ($link)
		{
			return self::GetHrefLink($link, $name, '', $rel, $class);
		}

		return $name;
	}

	/**
	 * Method to get the link to see the lastest topics
	 *
	 * @param   string  $name  Name of the link
	 * @param   string  $do    Task which be executed by the controller
	 * @param   string  $rel   Rel attirbute for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return mixed
	 */
	static function GetShowLatestLink($name, $do = '', $rel = 'follow')
	{
		if ($do)
		{
			$do = "&do=$do";
		}

		return self::GetSefHrefLink("index.php?option=com_kunena&view=latest{$do}", $name, '', $rel);
	}

	/**
	 * Method to get the karma link
	 *
	 * @param   string  $do      Task to execute
	 * @param   int     $catid   Category Id
	 * @param   int     $pid     Id of message
	 * @param   int     $userid  Userid
	 * @param   string  $name    Name for the link
	 * @param   string  $rel     Rel attirbute for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetKarmaLink($do, $catid, $pid, $userid, $name, $rel = 'nofollow')
	{
		$token = '&' . JSession::getFormToken() . '=1';

		return self::GetSefHrefLink("index.php?option=com_kunena&view=karma&do={$do}&userid={$userid}&catid={$catid}&pid={$pid}{$token}", $name, '', $rel);
	}

	/**
	 * Method to get the search URL
	 *
	 * @param   string  $view        View to load
	 * @param   string  $searchword  Search word entered by the user
	 * @param   int     $limitstart  Limistart for pagiantion
	 * @param   int     $limit       Limit for pagination
	 * @param   string  $params      Extra parameters for URL
	 * @param   string  $xhtml       Replace & by & for XML compilance.
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetSearchURL($view, $searchword='', $limitstart=0, $limit=0, $params = '', $xhtml=true)
	{
		$config = KunenaFactory::getConfig();
		$limitstr = "";

		if ($limitstart > 0)
		{
			$limitstr .= "&limitstart=$limitstart";
		}

		if ($limit > 0 && $limit != $config->messages_per_page_search)
		{
			$limitstr .= "&limit=$limit";
		}

		if ($searchword)
		{
			$searchword = '&q=' . urlencode($searchword);
		}

		return KunenaRoute::_("index.php?option=com_kunena&view={$view}{$searchword}{$params}{$limitstr}", $xhtml);
	}

	/**
	 * Method to get the poll URL
	 *
	 * @param   string  $do     Task which will be executed in the controller
	 * @param   string  $id     Id of the poll
	 * @param   int     $catid  Catid where are the poll
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetPollURL($do, $id = null, $catid=0)
	{
		$idstring = '';
		$catidstr = '';

		if ($id)
		{
			$idstring .= "&id=$id";
		}

		if ( $catid > 0 )
		{
			$catidstr = "&catid=$catid";
		}

		return KunenaRoute::_("index.php?option=com_kunena&view=poll&do={$do}{$catidstr}{$idstring}");
	}

	/**
	 * Method to get the statistics link
	 *
	 * @param   string  $name   Name which will be used in the link
	 * @param   string  $class  Class which will be used in the link
	 * @param   string  $rel    Rel attribute for the link
	 *
	 * @deprecated  3.0.0
	 *
	 * @return string
	 */
	static function GetStatsLink($name, $class = '', $rel = 'follow')
	{
		return self::GetHrefLink(KunenaRoute::_('index.php?option=com_kunena&view=stats'), $name, '', $rel, $class);
	}
}
