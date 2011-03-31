<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.cache.handler.output' );
kimport ( 'kunena.view' );
kimport ( 'kunena.forum.category.helper' );

/**
 * Common view
 */
class KunenaViewCommon extends KunenaView {
	public $catid = 0;

	function display($layout = null, $tpl = null) {
		$this->assignRef ( 'state', $this->get ( 'State' ) );
		$this->template = KunenaFactory::getTemplate();
		return $this->displayLayout($layout, $tpl);
	}

	function displayDefault($tpl = null) {
		//$this->params = $this->state->get('params');
		$result = $this->loadTemplate($tpl);
		if (JError::isError($result)) {
			return $result;
		}
		echo $result;
	}

	function displayAnnouncement($tpl = null) {
		if (KunenaFactory::getConfig()->showannouncement > 0) {
			$cache = JFactory::getCache('com_kunena', 'output');
			if ($cache->start($this->template->name, 'com_kunena.view.common.announcement')) return;

			$me = KunenaFactory::getUser();
			// User needs to be global moderator to edit announcements
			if ($me->exists() && $me->isModerator('global')) {
				$this->canEdit = true;
			} else {
				$this->canEdit = false;
			}
			// FIXME: move into model
			$db = JFactory::getDBO();
			$query = "SELECT * FROM #__kunena_announcement WHERE published='1' ORDER BY created DESC";
			$db->setQuery ( $query, 0, 1 );
			$this->announcement = $db->loadObject ();
			if (KunenaError::checkDatabaseError()) return;
			if ($this->announcement) {
				$this->annTitle = KunenaHtmlParser::parseText($this->announcement->title);
				$this->annDescription = $this->announcement->sdescription ? KunenaHtmlParser::parseBBCode($this->announcement->sdescription) : KunenaHtmlParser::parseBBCode($this->announcement->description, 300);
				$this->annDate = KunenaDate::getInstance($this->announcement->created);
				$this->annListURL = KunenaRoute::_("index.php?option=com_kunena&view=announcement&layout=list");
				$this->annMoreURL = !empty($this->announcement->description) ? KunenaRoute::_("index.php?option=com_kunena&view=announcement&id={$this->announcement->id}") : null;
				$result = $this->loadTemplate($tpl);
				if (JError::isError($result)) {
					return $result;
				}
				echo $result;
			} else {
				echo ' ';
			}
			$cache->end();
		} else echo ' ';
	}

	function displayForumJump($tpl = null) {
		$cache = JFactory::getCache('com_kunena', 'output');
		$user = KunenaFactory::getUser ();
		// TODO: we can improve this (not by user)
		if ($cache->start("{$this->template->name}.{$user->userid}.{$this->catid}", 'com_kunena.view.common.forumjump')) return;

		$options = array ();
		$options [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_FORUM_TOP') );
		$cat_params = array ('sections'=>1, 'catid'=>0);
		$this->assignRef ( 'categorylist', JHTML::_('kunenaforum.categorylist', 'catid', 0, $options, $cat_params, 'class="inputbox fbs" size="1" onchange = "this.form.submit()"', 'value', 'text', $this->catid));

		$result = $this->loadTemplate($tpl);
		if (JError::isError($result)) {
			return $result;
		}
		echo $result;
		$cache->end();
	}

	function displayBreadcrumb($tpl = null) {
		$user = KunenaFactory::getUser ();
		$catid = JRequest::getInt ( 'catid', 0 );
		$id = JRequest::getInt ( 'id', 0 );
		$view = JRequest::getWord ( 'view', 'default' );
		$layout = JRequest::getWord ( 'layout', 'default' );
		//$cache = JFactory::getCache('com_kunena', 'output');
		//if ($cache->start("{$this->template->name}.{$catid}.{$id}", 'com_kunena.view.common.breadcrumb')) return;

		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$active = JFactory::getApplication()->getMenu ()->getActive ();

		if (empty($this->pathway)) {
			if ($catid) {
				$parents = KunenaForumCategoryHelper::getParents($catid);
				$parents[$catid] = KunenaForumCategoryHelper::get($catid);

				// Remove categories from pathway if menu item contains/excludes them
				if (!empty($active->query['catid']) && isset($parents[$active->query['catid']])) {
					$curcatid = $active->query['catid'];
					while (($item = array_shift($parents)) !== null) {
						if ($item->id == $curcatid) break;
					}
				}
				foreach ( $parents as $parent ) {
					$pathway->addItem($this->escape( $parent->name ), KunenaRoute::normalize("index.php?option=com_kunena&view=category&catid={$parent->id}"));
				}
			}
			if ($id) {
				$topic = KunenaForumTopicHelper::get($id);
				$pathway->addItem($this->escape( $topic->subject ), KunenaRoute::normalize("index.php?option=com_kunena&view=category&catid={$catid}&id={$topic->id}"));
			}
			if ($view == 'topic') {
				$active_layout = (!empty($active->query['view']) && $active->query['view'] == 'topic' && !empty($active->query['layout'])) ? $active->query['layout'] : '';
				switch ($layout) {
					case 'create':
						if ($active_layout != 'create') $pathway->addItem($this->escape( JText::_('COM_KUNENA_BUTTON_NEW_TOPIC'), KunenaRoute::normalize() ));
						break;
					case 'reply':
						if ($active_layout != 'reply') $pathway->addItem($this->escape( JText::_('COM_KUNENA_BUTTON_REPLY_TOPIC'), KunenaRoute::normalize() ));
						break;
					case 'edit':
						if ($active_layout != 'edit') $pathway->addItem($this->escape( JText::_('COM_KUNENA_BUTTON_EDIT'), KunenaRoute::normalize() ));
						break;
				}
			}
		}
		$this->pathway = array();
		foreach ($pathway->getPathway() as $pitem) {
			$item = new StdClass();
			$item->name = $this->escape($pitem->name);
			$item->link = KunenaRoute::_($pitem->link);
			if ($item->link) $this->pathway[] = $item;
		}

		$result = $this->loadTemplate($tpl);
		if (JError::isError($result)) {
			return $result;
		}
		echo $result;
		//$cache->end();
	}

	function displayWhosonline($tpl = null) {
		$moderator = (int) KunenaFactory::getUser()->isModerator();
		$cache = JFactory::getCache('com_kunena', 'output');
		if ($cache->start("{$this->template->name}.{$moderator}", "com_kunena.view.common.whosonline")) return;

		$this->my = JFactory::getUser();
		$this->me = KunenaFactory::getUser();

		$users = KunenaUserHelper::getOnlineUsers();
		KunenaUserHelper::loadUsers(array_keys($users));
		$onlineusers = KunenaUserHelper::getOnlineCount();

		$who = '<strong>'.$onlineusers['user'].' </strong>';
		if($onlineusers['user']==1) {
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBER').'&nbsp;';
		} else {
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBERS').'&nbsp;';
		}
		$who .= JText::_('COM_KUNENA_WHO_AND');
		$who .= '<strong> '. $onlineusers['guest'].' </strong>';
		if($onlineusers['guest']==1) {
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_GUEST').'&nbsp;';
		} else {
			$who .= JText::_('COM_KUNENA_WHO_ONLINE_GUESTS').'&nbsp;';
		}
		$who .= JText::_('COM_KUNENA_WHO_ONLINE_NOW');
		$this->membersOnline = $who;

		$this->onlineList = array();
		$this->hiddenList = array();
		foreach ($users as $userid=>$usertime) {
			$user = KunenaUserHelper::get($userid);
			if ( !$user->showOnline ) {
				if ($this->me->isModerator()) $this->hiddenList[$user->getName()] = $user;
			} else {
				$this->onlineList[$user->getName()] = $user;
			}
		}
		ksort($this->onlineList);
		ksort($this->hiddenList);

		$this->usersURL = KunenaRoute::_('index.php?option=com_kunena&view=users');

		$result = $this->loadTemplate($tpl);
		if (JError::isError($result)) {
			return $result;
		}
		echo $result;
		$cache->end();
	}

	function displayStatistics($tpl = null) {
		$this->config = KunenaFactory::getConfig();
		$cache = JFactory::getCache('com_kunena', 'output');
		if ($cache->start("{$this->template->name}", 'com_kunena.view.common.statistics')) return;
		// FIXME: refactor code
		require_once(KPATH_SITE.'/lib/kunena.link.class.php');
		require_once(KPATH_SITE.'/lib/kunena.stats.class.php');
		$kunena_stats = CKunenaStats::getInstance ( );
		$kunena_stats->loadGenStats();
		$kunena_stats->loadLastUser();
		$kunena_stats->loadLastDays();
		$kunena_stats->loadTotalTopics();
		$kunena_stats->loadTotalCategories();
		$this->todayOpenCount = $kunena_stats->todayopen;
		$this->yesterdayOpenCount = $kunena_stats->yesterdayopen;
		$this->todayAnswerCount = $kunena_stats->todayanswer;
		$this->yesterdayAnswerCount = $kunena_stats->yesterdayanswer;
		$this->totalSubjectsCount = $kunena_stats->totaltitles;
		$this->totalPostsCount = $kunena_stats->totalmsgs;
		$this->totalSectionsCount = $kunena_stats->totalsections;
		$this->totalCategoriesCount = $kunena_stats->totalcats;
		$this->totalUsersCount = $kunena_stats->totalmembers;
		$this->latestMemberLink = CKunenaLink::GetProfileLink($kunena_stats->lastestmemberid);
		$this->statisticsURL = KunenaRoute::_('index.php?option=com_kunena&view=statistics');

		$result = $this->loadTemplate($tpl);
		if (JError::isError($result)) {
			return $result;
		}
		echo $result;
		$cache->end();
	}

	function displayMenu($tpl = null) {
		// Menu module has already caching in it
		$menu = KunenaRoute::getMenu ();
		$key = $menu ? "{$menu->id}.{$menu->name}" : '0';
		$result = $this->loadTemplate($tpl);
		if (JError::isError($result)) {
			return $result;
		}
		echo $result;
	}

	function displayLoginBox($tpl = null) {
		$my = JFactory::getUser ();
		$cache = JFactory::getCache('com_kunena', 'output');
		$token = JUtility::getToken();
		if ($cache->start("{$this->template->name}.{$my->id}.$token", 'com_kunena.view.common.loginbox')) return;

		$this->assign ( 'me', KunenaFactory::getUser ());

		require_once KPATH_SITE . '/lib/kunena.link.class.php';

		$uri = JFactory::getURI ();
		$this->assign ( 'return',  base64_encode ( $uri->toString ( array ('path', 'query', 'fragment' ) ) ) );
		$this->assign ( 'moduleHtml', $this->getModulePosition('kunena_profilebox'));

		$login = KunenaFactory::getLogin();
		if ($my->get ( 'guest' )) {
			$this->setLayout('login');
			if ($login) {
				$this->assignRef ( 'login', $login->getLoginFormFields() );
				$this->assignRef ( 'register', $login->getRegistrationURL() );
				$this->assignRef ( 'lostpassword', $login->getResetURL() );
				$this->assignRef ( 'lostusername', $login->getRemindURL() );
			}
		} else {
			$this->setLayout('logout');
			if ($login) $this->assignRef ( 'logout', $login->getLogoutFormFields() );
			$this->lastvisitDate = KunenaDate::getInstance($this->me->lastvisitDate);

			// Private messages
			$private = KunenaFactory::getPrivateMessaging();
			if ($private) {
				$count = $private->getUnreadCount($this->me->userid);
				$this->assign ( 'privateMessagesLink', $private->getInboxLink($count ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $count) : JText::_('COM_KUNENA_PMS_INBOX')));
			}

			// TODO: Edit profile (need to get link to edit page, even with integration)
			//$this->assign ( 'editProfileLink', '<a href="' . CKunenaLink::GetAnnouncementURL ( 'show' ).'">'. JText::_('COM_KUNENA_PROFILE_EDIT').'</a>');

			// Announcements
			if ( $this->me->isModerator() || $this->me->isAdmin()) {
				$this->assign ( 'announcementsLink', '<a href="' . CKunenaLink::GetAnnouncementURL ( 'show' ).'">'. JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS').'</a>');
			}

		}
		$result = $this->loadTemplate($tpl);
		if (JError::isError($result)) {
			return $result;
		}
		echo $result;
		$cache->end();
	}

	function displayFooter($tpl = null) {
		require_once KPATH_SITE . '/lib/kunena.link.class.php';
		$catid = 0;
		if (KunenaFactory::getConfig ()->enablerss) {
			if ($catid > 0) {
				kimport ( 'kunena.forum.category.helper' );
				$category = KunenaForumCategoryHelper::get ( $catid );
				if ($category->pub_access == 0 && $category->parent)
					$rss_params = '&catid=' . ( int ) $catid;
			} else {
				$rss_params = '';
			}
			if (isset ( $rss_params )) {
				$document = JFactory::getDocument ();
				$document->addCustomTag ( '<link rel="alternate" type="application/rss+xml" title="' . JText::_ ( 'COM_KUNENA_LISTCAT_RSS' ) . '" href="' . CKunenaLink::GetRSSURL ( $rss_params ) . '" />' );
				$this->assign ( 'rss', CKunenaLink::GetRSSLink ( $this->getIcon ( 'krss', JText::_('COM_KUNENA_LISTCAT_RSS') ), 'follow', $rss_params ));
			}
		}
		$template = KunenaFactory::getTemplate ();
		$credits = CKunenaLink::GetTeamCreditsLink ( $catid, JText::_('COM_KUNENA_POWEREDBY') ) . ' ' . CKunenaLink::GetCreditsLink ();
		if ($template->params->get('templatebyText') !='') {
			$credits .= ' :: <a href ="'. $template->params->get('templatebyLink').'" rel="follow">' . $template->params->get('templatebyText') .' '. $template->params->get('templatebyName') .'</a>';
		}
		$this->assign ( 'credits', $credits );
		$result = $this->loadTemplate($tpl);
		if (JError::isError($result)) {
			return $result;
		}
		echo $result;
	}
}