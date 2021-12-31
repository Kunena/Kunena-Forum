<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Announcement.List
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutAnnouncementItem
 *
 * @since  K4.0
 */
class KunenaLayoutAnnouncementItem extends KunenaLayout
{
	/**
	 * @var
	 * @since Kunena
	 */
	public $buttons;

	/**
	 * Method to get moderation actions for announcements
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getActions()
	{
		$this->buttons = array();

		if ($this->announcement->isAuthorised('edit'))
		{
			$this->buttons['edit'] = $this->getButton($this->announcement->getUri('edit'), 'edit', 'announcement', 'moderation');
		}

		if ($this->announcement->isAuthorised('delete'))
		{
			$this->buttons['delete'] = $this->getButton($this->announcement->getTaskUri('delete'), 'delete', 'announcement', 'permanent');
		}

		if ($this->buttons)
		{
			$this->buttons['cpanel'] = $this->getButton(KunenaForumAnnouncementHelper::getUri('list'), 'list', 'announcement', 'communication');
		}

		return $this->buttons;
	}

	/**
	 * Get button.
	 *
	 * @param   string $url    Target link (do not route it).
	 * @param   string $name   Name of the button.
	 * @param   string $scope  Scope of the button.
	 * @param   string $type   Type of the button.
	 * @param   int    $id     Id of the button.
	 * @param   bool   $normal Define if the button will have the class btn or btn-small
	 *
	 * @return KunenaLayout|KunenaLayoutBase
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getButton($url, $name, $scope, $type, $id = null, $normal = true)
	{
		return KunenaLayout::factory('Widget/Announcement/Button')
			->setProperties(array('url' => KunenaRoute::_($url), 'name' => $name, 'scope' => $scope, 'type' => $type, 'id' => $id, 'normal' => $normal));
	}
}
