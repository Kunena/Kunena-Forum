<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

jimport('joomla.cache.handler.output');
jimport('joomla.document.html.html');

/**
 * Common view
 */
class KunenaViewCommon extends KunenaView
{
	public $catid = 0;
	public $offline = false;

	function display($layout = null, $tpl = null)
	{
		$this->state = $this->get('State');

		if ($this->config->board_offline && !$this->me->isAdmin())
		{
			$this->offline = true;
		}

		return $this->displayLayout($layout, $tpl);
	}

	function displayDefault($tpl = null)
	{
		$this->header = $this->escape($this->header);

		if (empty($this->html))
		{
			$this->body = KunenaHtmlParser::parseBBCode($this->body);
		}

		$result = $this->loadTemplateFile($tpl);

		if (JError::isError($result))
		{
			return $result;
		}

		echo $result;
	}

	function displayAnnouncement($tpl = null)
	{
		if ($this->offline)
		{
			return;
		}

		if ($this->config->showannouncement > 0)
		{
			$items              = KunenaForumAnnouncementHelper::getAnnouncements();
			$this->announcement = array_pop($items);

			if (!$this->announcement)
			{
				echo ' ';

				return;
			}

			$cache    = JFactory::getCache('com_kunena', 'output');
			$annCache = $cache->get('announcement', 'global');

			if (!$annCache)
			{
				$cache->remove("{$this->ktemplate->name}.common.announcement", 'com_kunena.template');
			}

			if ($cache->start("{$this->ktemplate->name}.common.announcement", 'com_kunena.template'))
			{
				return;
			}

			if ($this->announcement && $this->announcement->authorise('read'))
			{
				$this->annListUrl = KunenaForumAnnouncementHelper::getUri('list');
				$this->showdate   = $this->announcement->showdate;

				$result = $this->loadTemplateFile($tpl);

				if (JError::isError($result))
				{
					return $result;
				}

				echo $result;
			}
			else
			{
				echo ' ';
			}

			$cache->store($this->announcement->id, 'announcement', 'global');
			$cache->end();
		}
		else
		{
			echo ' ';
		}
	}

	function displayForumJump($tpl = null)
	{
		if ($this->offline)
		{
			return;
		}

		$allowed = md5(serialize(KunenaAccess::getInstance()->getAllowedCategories()));
		$cache   = JFactory::getCache('com_kunena', 'output');

		if ($cache->start("{$this->ktemplate->name}.common.jump.{$allowed}", 'com_kunena.template'))
		{
			return;
		}

		$options            = array();
		$options []         = JHtml::_('select.option', '0', JText::_('COM_KUNENA_FORUM_TOP'));
		$cat_params         = array('sections' => 1, 'catid' => 0);
		$this->categorylist = JHtml::_('kunenaforum.categorylist', 'catid', 0, $options, $cat_params, 'class="inputbox fbs" size="1" onchange = "this.form.submit()"', 'value', 'text', $this->catid);

		$result = $this->loadTemplateFile($tpl);

		if (JError::isError($result))
		{
			return $result;
		}

		echo $result;

		$cache->end();
	}

	function displayBreadcrumb($tpl = null)
	{
		if ($this->offline)
		{
			return;
		}

		$catid  = JRequest::getInt('catid', 0);
		$id     = JRequest::getInt('id', 0);
		$view   = JRequest::getWord('view', 'default');
		$layout = JRequest::getWord('layout', 'default');

		$this->breadcrumb = $pathway = $this->app->getPathway();
		$active           = $this->app->getMenu()->getActive();

		if (empty($this->pathway))
		{
			KunenaFactory::loadLanguage('com_kunena.sys', 'admin');

			if ($catid)
			{
				$parents         = KunenaForumCategoryHelper::getParents($catid);
				$parents[$catid] = KunenaForumCategoryHelper::get($catid);

				// Remove categories from pathway if menu item contains/excludes them
				if (!empty($active->query['catid']) && isset($parents[$active->query['catid']]))
				{
					$curcatid = $active->query['catid'];
					while (($item = array_shift($parents)) !== null)
					{
						if ($item->id == $curcatid)
						{
							break;
						}
					}
				}

				foreach ($parents as $parent)
				{
					$pathway->addItem($this->escape($parent->name), KunenaRoute::normalize("index.php?option=com_kunena&view=category&catid={$parent->id}"));
				}
			}

			if ($view == 'announcement')
			{
				$pathway->addItem(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'), KunenaRoute::normalize("index.php?option=com_kunena&view=announcement&layout=list"));
			}
			elseif ($id)
			{
				$topic = KunenaForumTopicHelper::get($id);
				$pathway->addItem($this->escape($topic->subject), KunenaRoute::normalize("index.php?option=com_kunena&view=category&catid={$catid}&id={$topic->id}"));
			}

			if ($view == 'topic')
			{
				$active_layout = (!empty($active->query['view']) && $active->query['view'] == 'topic' && !empty($active->query['layout'])) ? $active->query['layout'] : '';
				switch ($layout)
				{
					case 'create':
						if ($active_layout != 'create')
						{
							$pathway->addItem($this->escape(JText::_('COM_KUNENA_NEW'), KunenaRoute::normalize()));
						}
						break;
					case 'reply':
						if ($active_layout != 'reply')
						{
							$pathway->addItem($this->escape(JText::_('COM_KUNENA_BUTTON_MESSAGE_REPLY'), KunenaRoute::normalize()));
						}
						break;
					case 'edit':
						if ($active_layout != 'edit')
						{
							$pathway->addItem($this->escape(JText::_('COM_KUNENA_EDIT'), KunenaRoute::normalize()));
						}
						break;
				}
			}
		}
		$this->pathway = array();

		foreach ($pathway->getPathway() as $pitem)
		{
			$item       = new StdClass();
			$item->name = $this->escape($pitem->name);
			$item->link = KunenaRoute::_($pitem->link);

			if ($item->link)
			{
				$this->pathway[] = $item;
			}
		}

		$result = $this->loadTemplateFile($tpl, array('pathway' => $this->pathway));

		if (JError::isError($result))
		{
			return $result;
		}

		echo $result;
	}

	function displayWhosonline($tpl = null)
	{
		if ($this->offline)
		{
			return;
		}

		$moderator = intval($this->me->isModerator()) + intval($this->me->isAdmin());
		$cache     = JFactory::getCache('com_kunena', 'output');

		if ($cache->start("{$this->ktemplate->name}.common.whosonline.{$moderator}", "com_kunena.template"))
		{
			return;
		}

		$users = KunenaUserHelper::getOnlineUsers();
		KunenaUserHelper::loadUsers(array_keys($users));
		$onlineusers = KunenaUserHelper::getOnlineCount();

		$who = '<strong>' . $onlineusers['user'] . ' </strong>';

		if ($onlineusers['user'] == 1)
		{
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBER') . '&nbsp;';
		}
		else
		{
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBERS') . '&nbsp;';
		}

		$who .= JText::_('COM_KUNENA_WHO_AND');
		$who .= '<strong> ' . $onlineusers['guest'] . ' </strong>';

		if ($onlineusers['guest'] == 1)
		{
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_GUEST') . '&nbsp;';
		}
		else
		{
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_GUESTS') . '&nbsp;';
		}

		$who .= JText::_('COM_KUNENA_WHO_ONLINE_NOW');
		$this->membersOnline = $who;

		$this->onlineList = array();
		$this->hiddenList = array();

		foreach ($users as $userid => $usertime)
		{
			$user = KunenaUserHelper::get($userid);

			if (!$user->showOnline)
			{
				if ($moderator)
				{
					$this->hiddenList[$user->getName()] = $user;
				}
			}
			else
			{
				$this->onlineList[$user->getName()] = $user;
			}
		}

		ksort($this->onlineList);
		ksort($this->hiddenList);

		$this->usersUrl = $this->getUserlistURL('');

		// Fall back to old template file.
		$result = $this->loadTemplateFile($tpl);

		if (JError::isError($result))
		{
			return $result;
		}

		echo $result;

		$cache->end();
	}

	function displayStatistics($tpl = null)
	{
		if ($this->offline)
		{
			return;
		}

		$cache = JFactory::getCache('com_kunena', 'output');

		if ($cache->start("{$this->ktemplate->name}.common.statistics", 'com_kunena.template'))
		{
			return;
		}

		$kunena_stats = KunenaForumStatistics::getInstance();
		$kunena_stats->loadGeneral();

		$this->assign($kunena_stats);
		$this->latestMemberLink = KunenaFactory::getUser(intval($this->lastUserId))->getLink();
		$this->statisticsUrl    = KunenaRoute::_('index.php?option=com_kunena&view=statistics');
		$this->statisticsLink   = $this->getStatsLink($this->config->board_title . ' ' . JText::_('COM_KUNENA_STAT_FORUMSTATS'), '');
		$this->usercountLink    = $this->getUserlistLink('', $this->memberCount);
		$this->userlistLink     = $this->getUserlistLink('', JText::_('COM_KUNENA_STAT_USERLIST') . ' &raquo;');
		$this->moreLink         = $this->getStatsLink(JText::_('COM_KUNENA_STAT_MORE_ABOUT_STATS') . ' &raquo;');

		$result = $this->loadTemplateFile($tpl);

		if (JError::isError($result))
		{
			return $result;
		}

		echo $result;
		$cache->end();
	}

	function displayFooter($tpl = null)
	{
		if ($this->offline)
		{
			return;
		}

		$catid = 0;

		if ($this->config->enablerss)
		{
			if ($catid > 0)
			{
				$category = KunenaForumCategoryHelper::get($catid);

				if ($category->pub_access == 0 && $category->parent)
				{
					$rss_params = '&catid=' . ( int ) $catid;
				}
			}
			else
			{
				$rss_params = '';
			}

			if (isset ($rss_params))
			{
				$document = JFactory::getDocument();
				$document->addCustomTag('<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_KUNENA_LISTCAT_RSS') . '" href="' . $this->getRSSURL($rss_params) . '" />');
				$this->rss = $this->getRSSLink($this->getIcon('krss', JText::_('COM_KUNENA_LISTCAT_RSS')), 'follow', $rss_params);
			}
		}
		$result = $this->loadTemplateFile($tpl);

		if (JError::isError($result))
		{
			return $result;
		}

		echo $result;
	}

	function displayMenu($tpl = null)
	{
		if ($this->offline)
		{
			return;
		}

		$this->params            = $this->state->get('params');
		$private                 = KunenaFactory::getPrivateMessaging();
		$this->pm_link           = $private->getInboxURL();
		$this->announcesListLink = KunenaForumAnnouncementHelper::getUrl('list');
		$result                  = $this->loadTemplateFile($tpl);

		if (JError::isError($result))
		{
			return $result;
		}

		echo $result;
	}

	function getMenu()
	{
		$basemenu = KunenaRoute::getMenu();

		if (!$basemenu)
		{
			return ' ';
		}

		$this->parameters = new JRegistry();
		$this->parameters->set('showAllChildren', $this->ktemplate->params->get('menu_showall', 0));
		$this->parameters->set('menutype', $basemenu->menutype);
		$this->parameters->set('startLevel', $basemenu->level + 1);
		$this->parameters->set('endLevel', $basemenu->level + $this->ktemplate->params->get('menu_levels', 1));

		$this->list      = KunenaMenuHelper::getList($this->parameters);
		$this->menu      = $this->app->getMenu();
		$this->active    = $this->menu->getActive();
		$this->active_id = isset($this->active) ? $this->active->id : $this->menu->getDefault()->id;
		$this->path      = isset($this->active) ? $this->active->tree : array();
		$this->showAll   = $this->parameters->get('showAllChildren');
		$this->class_sfx = htmlspecialchars($this->parameters->get('pageclass_sfx'), ENT_COMPAT, 'UTF-8');

		return count($this->list) ? $this->loadTemplateFile('menu') : '';
	}

	function displayLoginBox($tpl = null)
	{
		if ($this->offline)
		{
			return;
		}

		$my         = JFactory::getUser();
		$cache      = JFactory::getCache('com_kunena', 'output');
		$cachekey   = "{$this->ktemplate->name}.common.loginbox.u{$my->id}";
		$cachegroup = 'com_kunena.template';

		// FIXME: enable caching after fixing the issues
		$contents = false; //$cache->get($cachekey, $cachegroup);

		if (!$contents)
		{
			$this->moduleHtml = $this->getModulePosition('kunena_profilebox');

			$login = KunenaLogin::getInstance();

			if ($my->get('guest'))
			{
				$this->setLayout('login');

				if ($login)
				{
					$this->login           = $login;
					$this->registerUrl     = $login->getRegistrationUrl();
					$this->lostPasswordUrl = $login->getResetUrl();
					$this->lostUsernameUrl = $login->getRemindUrl();
					$this->remember        = $login->getRememberMe();
				}
			}
			else
			{
				$this->setLayout('logout');

				if ($login)
				{
					$this->logout = $login;
				}

				$this->lastvisitDate = KunenaDate::getInstance($this->me->lastvisitDate);

				// Private messages
				$this->getPrivateMessageLink();

				// TODO: Edit profile (need to get link to edit page, even with integration)
				//$this->editProfileLink = '<a href="' . $url.'">'. JText::_('COM_KUNENA_PROFILE_EDIT').'</a>';

				// Announcements
				if ($this->me->isModerator())
				{
					$this->announcementsLink = '<a href="' . KunenaForumAnnouncementHelper::getUrl('list') . '">' . JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . '</a>';
				}

			}
			$contents = $this->loadTemplateFile($tpl);

			if (JError::isError($contents))
			{
				return $contents;
			}
			// FIXME: enable caching after fixing the issues
			//$cache->store($contents, $cachekey, $cachegroup);
		}

		$contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillLoginBoxInfo'), $contents);
		echo $contents;
	}

	function fillLoginBoxInfo($matches)
	{
		switch ($matches[1])
		{
			case 'RETURN_URL':
				return base64_encode(JFactory::getURI()->toString(array('path', 'query', 'fragment')));
			case 'TOKEN':
				return JHtml::_('form.token');
			case 'MODULE':
				return $this->getModulePosition('kunena_profilebox');
		}
	}

	function getPrivateMessageLink()
	{
		// Private messages
		$private = KunenaFactory::getPrivateMessaging();

		if ($private)
		{
			$count                     = $private->getUnreadCount($this->me->userid);
			$this->privateMessagesLink = $private->getInboxLink($count ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $count) : JText::_('COM_KUNENA_PMS_INBOX'));
		}
	}

	function getUserlistURL($action = '', $xhtml = true)
	{
		$profile = KunenaFactory::getProfile();

		return $profile->getUserListURL($action, $xhtml);
	}

	/**
	 * Method to get Kunena URL RSS feed by taking config option to define the data to display
	 *
	 * @param   string $params Add extras params to the URL
	 * @param   string $xhtml  Replace & by & for XML compilance.
	 *
	 * @return string
	 */
	private function getRSSURL($params = '', $xhtml = true)
	{
		$mode = KunenaFactory::getConfig()->rss_type;

		switch ($mode)
		{
			case 'topic' :
				$rss_type = 'mode=topics';
				break;
			case 'recent' :
				$rss_type = 'mode=replies';
				break;
			case 'post' :
				$rss_type = 'layout=posts';
				break;
		}

		return KunenaRoute::_("index.php?option=com_kunena&view=topics&format=feed&layout=default&{$rss_type}{$params}", $xhtml);
	}

	function getRSSLink($name, $rel = 'follow', $params = '')
	{
		return '<a href="' . $this->getRSSURL($params) . '" rel="' . $rel . '">' . $name . '</a>';
	}

	public function getStatsLink($name, $class = '', $rel = 'follow')
	{
		$my = KunenaFactory::getUser();

		if (KunenaFactory::getConfig()->statslink_allowed == 0 && $my->userid == 0)
		{
			return false;
		}

		return '<a href="' . KunenaRoute::_('index.php?option=com_kunena&view=statistics') . '" rel="' . $rel . '" class="' . $class . '">' . $name . '</a>';
	}

	public function getUserlistLink($action, $name, $rel = 'nofollow', $class = '')
	{
		$my = KunenaFactory::getUser();

		if ($name == $this->memberCount) {
			$link = KunenaFactory::getProfile ()->getUserListURL ( $action );

			if ($link)
			{
				return '<a href="' . $link . '" rel="' . $rel . '" class="' . $class . '">' . $name . '</a>';
			}
			else {
				return  $name;
			}
		}
		elseif ($my->userid == 0 && KunenaFactory::getConfig()->userlist_allowed) {
			return false;
		}
		else {
			$link = KunenaFactory::getProfile ()->getUserListURL ( $action );
			return '<a href="'. $link .'" rel="'.$rel.'" class="'.$class.'">'.$name.'</a>';
		}
	}
}
