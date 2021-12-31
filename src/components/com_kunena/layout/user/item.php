<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * KunenaLayoutUserItem
 *
 * @since  K4.0
 */
class KunenaLayoutUserItem extends KunenaLayout
{
	/**
	 * Method to get tabs for user profile
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 */
	public function getTabs()
	{
		$banInfo   = KunenaUserBan::getInstanceByUserid($this->user->id, true);
		$myProfile = $this->profile->isMyself();
		$moderator = $this->me->isModerator();

		// Decide which tabs to display.
		$showPosts             = true;
		$showSubscriptions     = $this->config->allowsubscriptions && ($myProfile || $moderator);
		$showFavorites         = $this->config->allowfavorites && $myProfile;
		$showThankYou          = $this->config->showthankyou && $this->me->exists();
		$showUnapproved        = $myProfile && ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus());
		$showAttachments       = $this->config->show_imgfiles_manage_profile && ($moderator || $myProfile);
		$showListofBanGiven    = $moderator && $myProfile;
		$showBanUser           = $moderator && !$myProfile;

		try
		{
			$showBanHistory = $banInfo->canBan();
		}
		catch (Exception $e)
		{
			$showBanHistory = false;
		}

		// Define all tabs.
		$tabs = array();

		if ($showPosts)
		{
			$params = array(
				'embedded'            => 1,
				'topics_categories'   => 0,
				'topics_catselection' => 1,

				'userid'           => $this->profile->userid,
				'mode'             => 'latest',
				'sel'              => -1,
				'limit'            => 10,
				'filter_order'     => 'time',
				'limitstart'       => 0,
				'filter_order_Dir' => 'desc',
				'display'          => $this->state->get('display', ''),
			);

			$tab           = new stdClass;
			$tab->title    = Text::_('COM_KUNENA_USERPOSTS');
			$tab->content  = $this->subRequest('Message/List/Recent', new \Joomla\Input\Input($params), $params);
			$tab->active   = true;
			$tabs['posts'] = $tab;
		}

		if ($showSubscriptions)
		{
			$tab          = new stdClass;
			$tab->title   = Text::_('COM_KUNENA_SUBSCRIPTIONS');
			$tab->content = '';

			if ($this->config->category_subscriptions != 'disabled')
			{
				$params       = array(
					'embedded' => 1,

					'userid'           => $this->profile->userid,
					'limit'            => 10,
					'filter_order'     => 'time',
					'limitstart'       => 0,
					'filter_order_Dir' => 'desc',
				);
				$tab->content .= $this->subRequest('Category/Subscriptions', new \Joomla\Input\Input($params), $params);
			}

			if ($this->config->topic_subscriptions != 'disabled')
			{
				$params       = array(
					'embedded'            => 1,
					'topics_categories'   => 0,
					'topics_catselection' => 1,

					'userid'           => $this->profile->userid,
					'mode'             => 'subscriptions',
					'sel'              => -1,
					'limit'            => 10,
					'filter_order'     => 'time',
					'limitstart'       => 0,
					'filter_order_Dir' => 'desc',
				);
				$tab->content .= $this->subRequest('Topic/List/User', new \Joomla\Input\Input($params), $params);
			}

			$tab->active = false;

			if ($tab->content)
			{
				$tabs['subscriptions'] = $tab;
			}
		}

		if ($showFavorites)
		{
			$params = array(
				'embedded'            => 1,
				'topics_categories'   => 0,
				'topics_catselection' => 1,

				'userid'           => $this->profile->userid,
				'mode'             => 'favorites',
				'sel'              => -1,
				'limit'            => 10,
				'filter_order'     => 'time',
				'limitstart'       => 0,
				'filter_order_Dir' => 'desc',
			);

			$tab               = new stdClass;
			$tab->title        = Text::_('COM_KUNENA_FAVORITES');
			$tab->content      = $this->subRequest('Topic/List/User', new \Joomla\Input\Input($params), $params);
			$tab->active       = false;
			$tabs['favorites'] = $tab;
		}

		if ($showThankYou)
		{
			$tab          = new stdClass;
			$tab->title   = Text::_('COM_KUNENA_THANK_YOU');
			$tab->content = '';

			$params       = array(
				'embedded'            => 1,
				'topics_categories'   => 0,
				'topics_catselection' => 1,

				'userid'           => $this->profile->userid,
				'mode'             => 'mythanks',
				'sel'              => -1,
				'limit'            => 10,
				'filter_order'     => 'time',
				'limitstart'       => 0,
				'filter_order_Dir' => 'desc',
			);
			$tab->content .= $this->subRequest('Message/List/Recent', new \Joomla\Input\Input($params), $params);

			$params       = array(
				'embedded'            => 1,
				'topics_categories'   => 0,
				'topics_catselection' => 1,

				'userid'           => $this->profile->userid,
				'mode'             => 'thankyou',
				'sel'              => -1,
				'limit'            => 10,
				'filter_order'     => 'time',
				'limitstart'       => 0,
				'filter_order_Dir' => 'desc',
			);
			$tab->content .= $this->subRequest('Message/List/Recent', new \Joomla\Input\Input($params), $params);

			$tab->active      = false;
			$tabs['thankyou'] = $tab;
		}

		if ($showUnapproved)
		{
			$params             = array(
				'embedded'            => 1,
				'topics_categories'   => 0,
				'topics_catselection' => 1,

				'userid'           => $this->profile->userid,
				'mode'             => 'unapproved',
				'sel'              => -1,
				'limit'            => 10,
				'filter_order'     => 'time',
				'limitstart'       => 0,
				'filter_order_Dir' => 'desc',
			);
			$tab                = new stdClass;
			$tab->title         = Text::_('COM_KUNENA_MESSAGE_ADMINISTRATION');
			$tab->content       = $this->subRequest('Message/List/Recent', new \Joomla\Input\Input($params), $params);
			$tab->active        = false;
			$tabs['unapproved'] = $tab;
		}

		if ($showAttachments)
		{
			$params              = array(
				'embedded' => 1,
				'userid'   => $this->profile->userid,
			);
			$tab                 = new stdClass;
			$tab->title          = Text::_('COM_KUNENA_MANAGE_ATTACHMENTS');
			$tab->content        = $this->subRequest('User/Attachments', new \Joomla\Input\Input($params), $params);
			$tab->active         = false;
			$tabs['attachments'] = $tab;
		}

		if ($showListofBanGiven)
		{
			$params              = array(
				'embedded' => 1,
				'userid'   => $this->profile->userid,
			);
			$tab                = new stdClass;
			$tab->title         = Text::_('COM_KUNENA_BAN_LIST_OF_BANNED_USERS');
			$tab->content       = $this->subRequest('User/Ban/Manager', new \Joomla\Input\Input($params), $params);
			$tab->active        = false;
			$tabs['banmanager'] = $tab;
		}

		if ($showBanHistory)
		{
			$tab                = new stdClass;
			$tab->title         = Text::_('COM_KUNENA_BAN_BANHISTORY');
			$tab->content       = $this->subRequest('User/Ban/History');
			$tab->active        = false;
			$tabs['banhistory'] = $tab;
		}

		if ($showBanUser)
		{
			$tab             = new stdClass;
			$tab->title      = $banInfo->exists() ? Text::_('COM_KUNENA_BAN_EDIT') : Text::_('COM_KUNENA_BAN_NEW');
			$tab->content    = $this->subRequest('User/Ban/Form');
			$tab->active     = false;
			$tabs['banuser'] = $tab;
		}

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

		$plugins = Factory::getApplication()->triggerEvent('onKunenaUserTabs', array($tabs));

		$tabs = $tabs + $plugins;

		return $tabs;
	}

	/**
	 * Method to display unapproved posts
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayUnapprovedPosts()
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

	/**
	 * Method to display user posts
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayUserPosts()
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

	/**
	 * Method to display who got thankyou
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayGotThankyou()
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

	/**
	 * Method to display who said thankyou
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function displaySaidThankyou()
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

	/**
	 * Method to display favorites topics
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayFavorites()
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

	/**
	 * Method to display subscriptions
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function displaySubscriptions()
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

	/**
	 * Method to display categories subscriptions
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayCategoriesSubscriptions()
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
}
