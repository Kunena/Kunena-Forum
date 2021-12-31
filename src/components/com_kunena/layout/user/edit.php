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
 * @since  K5.1
 */
class KunenaLayoutUserEdit extends KunenaLayout
{
	/**
	 * Method to get tabs for edit profile
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 */
	public function getTabsEdit()
	{
		$myProfile = $this->profile->isMyself() || KunenaUserHelper::getMyself()->isAdmin() || KunenaUserHelper::getMyself()->isModerator();

		// Define all tabs.
		$tabs = array();

		if ($myProfile)
		{
			$tab          = new stdClass;
			$tab->title   = Text::_('COM_KUNENA_PROFILE_EDIT_USER');
			$tab->content = $this->subRequest('User/Edit/User');
			$tab->active  = true;
			$tabs['User'] = $tab;
		}

		if ($myProfile)
		{
			$tab             = new stdClass;
			$tab->title      = Text::_('COM_KUNENA_PROFILE_EDIT_PROFILE');
			$tab->content    = $this->subRequest('User/Edit/Profile');
			$tab->active     = false;
			$tabs['profile'] = $tab;
		}

		if ($myProfile)
		{
			if (KunenaConfig::getInstance()->allowavatarupload || KunenaConfig::getInstance()->allowavatargallery)
			{
				$tab            = new stdClass;
				$tab->title     = Text::_('COM_KUNENA_PROFILE_EDIT_AVATAR');
				$tab->content   = $this->subRequest('User/Edit/Avatar');
				$tab->active    = false;
				$tabs['avatar'] = $tab;
			}
		}

		if ($myProfile)
		{
			$tab              = new stdClass;
			$tab->title       = Text::_('COM_KUNENA_PROFILE_EDIT_SETTINGS');
			$tab->content     = $this->subRequest('User/Edit/Settings');
			$tab->active      = false;
			$tabs['settings'] = $tab;
		}

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

		$plugins = Factory::getApplication()->triggerEvent('onKunenaUserTabsEdit', array($tabs));

		$tabs = $tabs + $plugins;

		return $tabs;
	}
}
