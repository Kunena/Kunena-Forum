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

// FIXME: convert to full MVC

/**
 * User View
 */
class KunenaViewUser extends KunenaView
{
	function displayDefault($tpl = null)
	{
		$this->displayCommon($tpl);
	}

	function displayModerate($tpl = null)
	{
		$this->layout = 'default';
		$this->setLayout($this->layout);
		$this->displayCommon($tpl);
	}

	function displayEdit($tpl = null)
	{
		$userid               = JRequest::getInt('userid');
		$this->usernamechange = JComponentHelper::getParams('com_users')->get('change_login_name', 1);

		if ($userid && $this->me->userid != $userid)
		{
			$user = KunenaFactory::getUser($userid);
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_VIEW_USER_EDIT_AUTH_FAILED', $user->getName()), 'notice');

			return;
		}

		$this->displayCommon($tpl);
	}

	function displayList($tpl = null)
	{
		if ($this->config->userlist_allowed && JFactory::getUser()->guest)
		{
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		$this->total = $this->get('Total');
		$this->count = $this->get('Count');
		$this->users = $this->get('Items');
		// TODO: Deprecated:
		$this->pageNav = $this->getPagination(7);

		$this->_prepareDocument('list');

		$this->render('User/List', $tpl);
	}

	function getPaginationObject($maxpages)
	{
		$pagination = new KunenaPagination($this->count, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination;
	}

	function getPagination($maxpages)
	{
		return $this->getPaginationObject($maxpages)->getPagesLinks();
	}

	protected function displayCommon($tpl = null)
	{
		$userid = JRequest::getInt('userid');

		$this->_db = JFactory::getDBO();
		$this->do  = JRequest::getWord('layout');

		if (!$userid)
		{
			$this->user = JFactory::getUser();
		}
		else
		{
			$this->user = JFactory::getUser($userid);
		}

		if ($this->user->id == 0 || ($this->me->userid == 0 && !$this->config->pubprofile))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILEPAGE_NOT_ALLOWED_FOR_GUESTS'), 'notice');

			return;
		}

		$integration         = KunenaFactory::getProfile();
		$activityIntegration = KunenaFactory::getActivityIntegration();
		$template            = KunenaFactory::getTemplate();
		$this->params        = $template->params;

		if (get_class($integration) == 'KunenaProfileNone')
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILE_DISABLED'), 'notice');

			return;
		}

		$this->allow = true;

		$this->profile = KunenaFactory::getUser($this->user->id);

		if (!$this->profile->exists())
		{
			$this->profile->save();
		}

		if ($this->profile->userid == $this->me->userid)
		{
			if ($this->do != 'edit')
			{
				$this->editlink = $this->profile->getLink(JText::_('COM_KUNENA_EDIT') . ' &raquo;', JText::_('COM_KUNENA_EDIT') . ' &raquo;', 'nofollow', 'edit','');
			}
			else
			{
				$this->editlink = $this->profile->getLink(JText::_('COM_KUNENA_BACK') . ' &raquo;', JText::_('COM_KUNENA_BACK') . ' &raquo;', 'nofollow','','');
			}
		}

		$this->name = $this->user->username;

		if ($this->config->showuserstats)
		{
			$this->rank_image = $this->profile->getRank(0, 'image');
			$this->rank_title = $this->profile->getRank(0, 'title');
			$this->posts      = $this->profile->posts;
			$this->thankyou   = $this->profile->thankyou;
			$this->userpoints = $activityIntegration->getUserPoints($this->profile->userid);
			$this->usermedals = $activityIntegration->getUserMedals($this->profile->userid);
		}

		if ($this->config->userlist_joindate || $this->me->isModerator())
		{
			$this->registerdate = $this->user->registerDate;
		}

		if ($this->config->userlist_lastvisitdate || $this->me->isModerator())
		{
			$this->lastvisitdate = $this->user->lastvisitDate;
		}

		if (!isset($this->lastvisitdate) || $this->lastvisitdate == "0000-00-00 00:00:00")
		{
			$this->lastvisitdate = null;
		}

		$this->avatarlink    = $this->profile->getAvatarImage('kavatar', 'profile');
		$this->personalText  = $this->profile->personalText;
		$this->signature     = $this->profile->signature;
		$this->signatureHtml = KunenaHtmlParser::parseBBCode($this->signature, null, $this->config->maxsig);
		$this->localtime     = KunenaDate::getInstance('now', $this->user->getParam('timezone', $this->app->getCfg('offset', null)));

		try
		{
			$offset = new DateTimeZone($this->user->getParam('timezone', $this->app->getCfg('offset', null)));
		}
		catch (Exception $e)
		{
			$offset = null;
		}

		$this->localtime->setTimezone($offset);
		$this->moderator = KunenaAccess::getInstance()->getModeratorStatus($this->profile);
		$this->admin     = $this->profile->isAdmin();

		switch ($this->profile->gender)
		{
			case 1:
				$this->genderclass = 'male';
				$this->gender      = JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE');
				break;
			case 2:
				$this->genderclass = 'female';
				$this->gender      = JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE');
				break;
			default:
				$this->genderclass = 'unknown';
				$this->gender      = JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN');
		}

		if ($this->profile->location)
		{
			$this->locationlink = '<a href="http://maps.google.com?q=' . $this->escape($this->profile->location) . '" target="_blank">' . $this->escape($this->profile->location) . '</a>';
		}
		else
		{
			$this->locationlink = JText::_('COM_KUNENA_LOCATION_UNKNOWN');
		}

		$this->online           = $this->profile->isOnline();
		$this->showUnusedSocial = true;

		if (!preg_match("~^(?:f|ht)tps?://~i", $this->profile->websiteurl))
		{
			$this->websiteurl = 'http://' . $this->profile->websiteurl;
		}
		else
		{
			$this->websiteurl = $this->profile->websiteurl;
		}

		$avatar           = KunenaFactory::getAvatarIntegration();
		$this->editavatar = ($avatar instanceof KunenaAvatarKunena) ? true : false;

		$this->banInfo = KunenaUserBan::getInstanceByUserid($userid, true);
		$this->canBan  = $this->banInfo->canBan();

		if ($this->config->showbannedreason)
		{
			$this->banReason = $this->banInfo->reason_public;
		}

		// Which tabs to show?
		$this->showUserPosts       = true;
		$this->showSubscriptions   = $this->config->allowsubscriptions && $this->me->userid == $this->profile->userid;
		$this->showFavorites       = $this->config->allowfavorites && $this->me->userid == $this->profile->userid;
		$this->showThankyou        = $this->config->showthankyou && $this->me->exists();
		$this->showUnapprovedPosts = $this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus(); // || $this->me->userid == $this->profile->userid;
		$this->showAttachments     = $this->canManageAttachments() && ($this->me->isModerator() || $this->me->userid == $this->profile->userid);
		$this->showBanManager      = $this->me->isModerator() && $this->me->userid == $this->profile->userid;
		$this->showBanHistory      = $this->me->isModerator() && $this->me->userid != $this->profile->userid;
		$this->showBanUser         = $this->canBan;

		if ($this->me->userid != $this->profile->userid)
		{
			$this->profile->uhits++;
			$this->profile->save();
		}

		$private = KunenaFactory::getPrivateMessaging();

		if ($this->me->userid == $this->user->id)
		{
			$this->pmCount = $private->getUnreadCount($this->me->userid);
			$this->pmLink  = $private->getInboxLink($this->pmCount ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $this->pmCount) : JText::_('COM_KUNENA_PMS_INBOX'));
		}
		else
		{
			$this->pmLink = $this->profile->profileIcon('private');
		}

		$this->_prepareDocument('common');
		$layout = $this->getLayout() != 'default' ? "User/{$this->getLayout()}" : 'User/Item';
		$this->render($layout, $tpl);
	}

	function displayUnapprovedPosts()
	{
		$params = array(
			'topics_categories'   => 0,
			'topics_catselection' => 1,
			'userid'              => $this->user->id,
			'mode'                => 'unapproved',
			'sel'                 => -1,
			'limit'               => 6,
			'filter_order'        => 'time',
			'limitstart'          => 0,
			'filter_order_Dir'    => 'desc',
		);
		KunenaForum::display('topics', 'posts', 'embed', $params);
	}

	function displayUserPosts()
	{
		$params = array(
			'topics_categories'   => 0,
			'topics_catselection' => 1,
			'userid'              => $this->user->id,
			'mode'                => 'latest',
			'sel'                 => 8760,
			'limit'               => 6,
			'filter_order'        => 'time',
			'limitstart'          => 0,
			'filter_order_Dir'    => 'desc',
		);
		KunenaForum::display('topics', 'posts', 'embed', $params);
	}

	function displayGotThankyou()
	{
		$params = array(
			'topics_categories'   => 0,
			'topics_catselection' => 1,
			'userid'              => $this->user->id,
			'mode'                => 'mythanks',
			'sel'                 => -1,
			'limit'               => 6,
			'filter_order'        => 'time',
			'limitstart'          => 0,
			'filter_order_Dir'    => 'desc',
		);
		KunenaForum::display('topics', 'posts', 'embed', $params);
	}

	function displaySaidThankyou()
	{
		$params = array(
			'topics_categories'   => 0,
			'topics_catselection' => 1,
			'userid'              => $this->user->id,
			'mode'                => 'thankyou',
			'sel'                 => -1,
			'limit'               => 6,
			'filter_order'        => 'time',
			'limitstart'          => 0,
			'filter_order_Dir'    => 'desc',
		);
		KunenaForum::display('topics', 'posts', 'embed', $params);
	}

	function displayFavorites()
	{
		$params = array(
			'topics_categories'   => 0,
			'topics_catselection' => 1,
			'userid'              => $this->user->id,
			'mode'                => 'favorites',
			'sel'                 => -1,
			'limit'               => 6,
			'filter_order'        => 'time',
			'limitstart'          => 0,
			'filter_order_Dir'    => 'desc',
		);
		KunenaForum::display('topics', 'user', 'embed', $params);
	}

	function displaySubscriptions()
	{
		if ($this->config->topic_subscriptions == 'disabled')
		{
			return;
		}

		$params = array(
			'topics_categories'   => 0,
			'topics_catselection' => 1,
			'userid'              => $this->user->id,
			'mode'                => 'subscriptions',
			'sel'                 => -1,
			'limit'               => 6,
			'filter_order'        => 'time',
			'limitstart'          => 0,
			'filter_order_Dir'    => 'desc',
		);

		KunenaForum::display('topics', 'user', 'embed', $params);
	}

	function displayCategoriesSubscriptions()
	{
		if ($this->config->category_subscriptions == 'disabled')
		{
			return;
		}
		$params = array(
			'userid'           => $this->user->id,
			'limit'            => 6,
			'filter_order'     => 'time',
			'limitstart'       => 0,
			'filter_order_Dir' => 'desc',
		);

		KunenaForum::display('category', 'user', 'embed', $params);
	}

	function displayBanUser()
	{
		$this->baninfo = KunenaUserBan::getInstanceByUserid($this->profile->userid, true);
		echo $this->loadTemplateFile('ban');
	}

	function displayBanHistory()
	{
		$this->banhistory = KunenaUserBan::getUserHistory($this->profile->userid);
		echo $this->loadTemplateFile('history');
	}

	function displayBanManager()
	{
		// TODO: move ban manager somewhere else and add pagination
		$this->bannedusers = KunenaUserBan::getBannedUsers(0, 50);

		if (!empty($this->bannedusers))
		{
			KunenaUserHelper::loadUsers(array_keys($this->bannedusers));
		}

		echo $this->loadTemplateFile('banmanager');
	}

	function displaySummary()
	{
		$private = KunenaFactory::getPrivateMessaging();

		if ($this->me->userid == $this->user->id)
		{
			$PMCount      = $private->getUnreadCount($this->me->userid);
			$this->PMlink = $private->getInboxLink($PMCount ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $PMCount) : JText::_('COM_KUNENA_PMS_INBOX'));
		}
		else
		{
			$this->PMlink = $this->profile->profileIcon('private');
		}

		echo $this->loadTemplateFile('summary');
	}

	function displayTab()
	{
		$this->email = null;

		if ($this->user->email)
		{
			if ($this->config->showemail && (!$this->profile->hideEmail || $this->me->isModerator()))
			{
				$this->email = JHtml::_('email.cloak', $this->user->email);
			}
			elseif ($this->me->isAdmin())
			{
				$this->email = JHtml::_('email.cloak', $this->user->email);
			}
		}
		else
		{
			$this->email = '';
		}

		switch ($this->do)
		{
			case 'edit':
				$user = JFactory::getUser();

				if ($user->id == $this->user->id)
				{
					echo $this->loadTemplateFile('tab');
				}

				break;
			default:
				echo $this->loadTemplateFile('tab');
		}
	}

	function displayKarma()
	{
		$userkarma = '';

		if ($this->config->showkarma && $this->profile->userid)
		{
			$userkarma = '<strong>' . JText::_('COM_KUNENA_KARMA') . "</strong>: " . $this->profile->karma;

			if ($this->me->userid && $this->me->userid != $this->profile->userid)
			{
				$userkarma .= ' ' . JHtml::_('kunenaforum.link', 'index.php?option=com_kunena&view=user&task=karmadown&userid=' . $this->profile->userid . '&' . JSession::getFormToken() . '=1', '<span class="kkarma-minus" title="' . JText::_('COM_KUNENA_KARMA_SMITE') . '"> </span>');
				$userkarma .= ' ' . JHtml::_('kunenaforum.link', 'index.php?option=com_kunena&view=user&task=karmaup&userid=' . $this->profile->userid . '&' . JSession::getFormToken() . '=1', '<span class="kkarma-plus" title="' . JText::_('COM_KUNENA_KARMA_APPLAUD') . '"> </span>');
			}
		}

		return $userkarma;
	}

	function getAvatarGallery($path)
	{
		$files = KunenaFolder::files($path, '(\.gif|\.png|\.jpg|\.jpeg)$');

		return $files;
	}

	// This function was modified from the one posted to PHP.net by rockinmusicgv
	// It is available under the readdir() entry in the PHP online manual
	function getAvatarGalleries($path, $select_name)
	{
		jimport('joomla.utilities.string');
		$folders   = KunenaFolder::folders($path, '.', true, true);
		$galleries = array();

		if ($this->getAvatarGallery($path))
		{
			$galleries[] = JHtml::_('select.option', 'default', JText::_('COM_KUNENA_DEFAULT_GALLERY'));
		}

		foreach ($folders as $folder)
		{
			$folder = substr($folder, strlen($path) + 1);

			if (!$this->getAvatarGallery($path . '/' . $folder))
			{
				continue;
			}

			$galleries[] = JHtml::_('select.option', $folder, JString::ucwords(str_replace('/', ' / ', $folder)));
		}

		$selected = JString::trim($this->gallery);

		return $galleries ? JHtml::_('select.genericlist', $galleries, $this->escape($select_name), '', 'value', 'text', $selected, 'avatar_category_select') : null;
	}

	function displayEditUser()
	{
		$this->user = JFactory::getUser();
		$this->me   = KunenaUserHelper::get();

		// check to see if Frontend User Params have been enabled
		if (JComponentHelper::getParams('com_users')->get('frontend_userparams'))
		{
			$usersConfig = JComponentHelper::getParams('com_users');

			if ($usersConfig->get('frontend_userparams', 0))
			{
				$lang = JFactory::getLanguage();
				$lang->load('com_users', JPATH_ADMINISTRATOR);

				JForm::addFormPath(JPATH_ROOT . '/components/com_users/models/forms');
				JForm::addFieldPath(JPATH_ROOT . '/components/com_users/models/fields');
				JPluginHelper::importPlugin('user');
				$registry     = new JRegistry($this->user->params);
				$form         = JForm::getInstance('com_users.profile', 'frontend');
				$data         = new StdClass();
				$data->params = $registry->toArray();
				$dispatcher   = JDispatcher::getInstance();
				$dispatcher->trigger('onContentPrepareForm', array($form, $data));
				$form->bind($data);
				// this get only the fields for user settings (template, editor, language...)
				$this->userparameters = $form->getFieldset('params');
			}
		}

		echo $this->loadTemplateFile('user');
	}

	function displayEditProfile()
	{
		$bd = @explode("-", $this->profile->birthdate);

		$this->birthdate["year"]  = $bd[0];
		$this->birthdate["month"] = $bd[1];
		$this->birthdate["day"]   = $bd[2];

		$this->genders[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'));
		$this->genders[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'));
		$this->genders[] = JHtml::_('select.option', '2', JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'));

		$this->social = array('twitter', 'facebook', 'myspace', 'skype', 'linkedin', 'delicious',
			'friendfeed', 'digg', 'yim', 'aim', 'gtalk', 'icq', 'msn', 'blogspot', 'flickr', 'bebo');

		echo $this->loadTemplateFile('profile');
	}

	function displayEditAvatar()
	{
		if (!$this->editavatar)
		{
			return;
		}

		$this->gallery = JRequest::getString('gallery', 'default');

		if ($this->gallery == 'default')
		{
			$this->gallery = '';
		}

		$path             = JPATH_ROOT . '/media/kunena/avatars/gallery';
		$this->galleryurl = JUri::root(true) . '/media/kunena/avatars/gallery';
		$this->galleries  = $this->getAvatarGalleries($path, 'gallery');
		$this->galleryimg = $this->getAvatarGallery($path . '/' . $this->gallery);

		$this->galleryImagesList = $this->getAllImagesInGallery();

		$this->row(true);
		echo $this->loadTemplateFile('avatar');
	}

	function getAllImagesInGallery()
	{
		$path              = JPATH_ROOT . '/media/kunena/avatars/gallery';
		$galleryFolders    = KunenaFolder::folders($path);
		$files_list        = array();
		$defaultGallery    = KunenaFolder::files($path);
		$newdefaultGallery = array();

		foreach ($defaultGallery as $image)
		{
			if ($image != 'index.html')
			{
				$newdefaultGallery[] = $image;
			}
		}

		$files_list['default'] = json_encode($newdefaultGallery);

		foreach ($galleryFolders as $folder)
		{
			$tmp               = KunenaFolder::files($path . '/' . $folder);
			$newgalleryFolders = array();

			foreach ($tmp as $img)
			{
				if ($img != 'index.html')
				{
					$newgalleryFolders[] = $img;
				}
			}
			$files_list[$folder] = json_encode($newgalleryFolders);
		}

		return $files_list;
	}

	function displayEditSettings()
	{
		$item             = new StdClass();
		$item->name       = 'messageordering';
		$item->label      = JText::_('COM_KUNENA_USER_ORDER');
		$options          = array();
		$options[]        = JHtml::_('select.option', 0, JText::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[]        = JHtml::_('select.option', 2, JText::_('COM_KUNENA_USER_ORDER_ASC'));
		$options[]        = JHtml::_('select.option', 1, JText::_('COM_KUNENA_USER_ORDER_DESC'));
		$item->field      = JHtml::_('select.genericlist', $options, 'messageordering', 'class="kinputbox" size="1"', 'value', 'text', $this->escape($this->profile->ordering), 'kmessageordering');
		$this->settings[] = $item;

		$item             = new StdClass();
		$item->name       = 'hidemail';
		$item->label      = JText::_('COM_KUNENA_USER_HIDEEMAIL');
		$options          = array();
		$options[]        = JHtml::_('select.option', 0, JText::_('COM_KUNENA_NO'));
		$options[]        = JHtml::_('select.option', 1, JText::_('COM_KUNENA_YES'));
		$item->field      = JHtml::_('select.genericlist', $options, 'hidemail', 'class="kinputbox" size="1"', 'value', 'text', $this->escape($this->profile->hideEmail), 'khidemail');
		$this->settings[] = $item;

		$item             = new StdClass();
		$item->name       = 'showonline';
		$item->label      = JText::_('COM_KUNENA_USER_SHOWONLINE');
		$options          = array();
		$options[]        = JHtml::_('select.option', 0, JText::_('COM_KUNENA_NO'));
		$options[]        = JHtml::_('select.option', 1, JText::_('COM_KUNENA_YES'));
		$item->field      = JHtml::_('select.genericlist', $options, 'showonline', 'class="kinputbox" size="1"', 'value', 'text', $this->escape($this->profile->showOnline), 'kshowonline');
		$this->settings[] = $item;

		$item             = new StdClass();
		$item->name       = 'cansubscribe';
		$item->label      = JText::_('COM_KUNENA_USER_CANSUBSCRIBE');
		$options          = array();
		$options[]        = JHtml::_('select.option', -1, JText::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[]        = JHtml::_('select.option', 0, JText::_('COM_KUNENA_NO'));
		$options[]        = JHtml::_('select.option', 1, JText::_('COM_KUNENA_YES'));
		$item->field      = JHtml::_('select.genericlist', $options, 'cansubscribe', 'class="kinputbox" size="1"', 'value', 'text', $this->escape($this->profile->canSubscribe), 'kcansubscribe');
		$this->settings[] = $item;

		$item             = new StdClass();
		$item->name       = 'userlisttime';
		$item->label      = JText::_('COM_KUNENA_USER_USERLISTTIME');
		$options          = array();
		$options[]        = JHtml::_('select.option', -2, JText::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[]        = JHtml::_('select.option', -1, JText::_('COM_KUNENA_SHOW_ALL'));
		$options[]        = JHtml::_('select.option', 0, JText::_('COM_KUNENA_SHOW_LASTVISIT'));
		$options[]        = JHtml::_('select.option', 4, JText::_('COM_KUNENA_SHOW_4_HOURS'));
		$options[]        = JHtml::_('select.option', 8, JText::_('COM_KUNENA_SHOW_8_HOURS'));
		$options[]        = JHtml::_('select.option', 12, JText::_('COM_KUNENA_SHOW_12_HOURS'));
		$options[]        = JHtml::_('select.option', 24, JText::_('COM_KUNENA_SHOW_24_HOURS'));
		$options[]        = JHtml::_('select.option', 48, JText::_('COM_KUNENA_SHOW_48_HOURS'));
		$options[]        = JHtml::_('select.option', 168, JText::_('COM_KUNENA_SHOW_WEEK'));
		$options[]        = JHtml::_('select.option', 720, JText::_('COM_KUNENA_SHOW_MONTH'));
		$options[]        = JHtml::_('select.option', 8760, JText::_('COM_KUNENA_SHOW_YEAR'));
		$item->field      = JHtml::_('select.genericlist', $options, 'userlisttime', 'class="kinputbox" size="1"', 'value',
			'text', $this->escape($this->profile->userListtime), 'kuserlisttime');
		$this->settings[] = $item;

		$this->row(true);
		echo $this->loadTemplateFile('settings');
	}

	function displayUserList()
	{
		echo $this->loadTemplateFile('list');
	}

	function displayUserRow($user)
	{
		$this->user  = KunenaFactory::getUser($user->id);
		$this->email = '';

		if ($this->user->email && $this->config->userlist_email && (!$this->user->hideEmail || $this->me->isModerator()))
		{
			$this->email = JHtml::_('email.cloak', $this->user->email);
		}

		$this->rank_image = $this->user->getRank(0, 'image');
		$this->rank_title = $this->user->getRank(0, 'title');
		echo $this->loadTemplateFile('row');
	}

	function getLastvisitdate($date)
	{
		$lastvisit = JHtml::_('date', $date, 'Y-m-d\TH:i:sP ');

		return $lastvisit;
	}

	function canManageAttachments()
	{
		if ($this->config->show_imgfiles_manage_profile)
		{
			$params            = array('file' => '1', 'image' => '1', 'orderby' => 'desc', 'limit' => '30');
			$this->userattachs = KunenaAttachmentHelper::getByUserid($this->profile, $params);

			if ($this->userattachs)
			{
				if ($this->me->isModerator() || $this->profile->userid == $this->me->userid)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		return false;
	}

	function displayAttachments()
	{
		$this->title = JText::_('COM_KUNENA_MANAGE_ATTACHMENTS');
		$this->items = $this->userattachs;

		if (!empty($this->userattachs))
		{
			// Preload messages
			$attach_mesids = array();

			foreach ($this->userattachs as $attach)
			{
				$attach_mesids[] = (int) $attach->mesid;
			}

			$messages = KunenaForumMessageHelper::getMessages($attach_mesids, 'none');

			// Preload topics
			$topic_ids = array();

			foreach ($messages as $message)
			{
				$topic_ids[] = $message->thread;
			}

			KunenaForumTopicHelper::getTopics($topic_ids, 'none');
		}

		echo $this->loadTemplateFile('attachments');
	}

	protected function _prepareDocument($type)
	{
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive(); // get the active item
		$params = $menu_item->params; // get the params
		$params_title = $params->get('page_title');
		$params_keywords = $params->get('menu-meta_keywords');
		$params_description = $params->get('menu-description');

		if ($type == 'list')
		{

			$page  = intval($this->state->get('list.start') / $this->state->get('list.limit')) + 1;
			$pages = intval(($this->total - 1) / $this->state->get('list.limit')) + 1;

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$title = JText::_('COM_KUNENA_VIEW_USER_LIST') . " ({$page}/{$pages})";
				$this->setTitle($title);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title;
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = JText::_('COM_KUNENA_VIEW_USER_LIST') . ': ' . $this->config->board_title;
				$this->setDescription($description);
			}
		}
		else
		{
			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$title = JText::sprintf('COM_KUNENA_VIEW_USER_DEFAULT', $this->profile->getName());
				$this->setTitle($title);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title . ', ' . $this->profile->getName();
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = JText::sprintf('COM_KUNENA_META_PROFILE', $this->profile->getName(), $this->config->board_title, $this->profile->getName(), $this->config->board_title);
				$this->setDescription($description);
			}
		}
	}
}
