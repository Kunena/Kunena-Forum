<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaPrivate
 */
class KunenaPrivate
{
	protected static $instance = false;

	/**
	 * @param   null $integration
	 *
	 * @return boolean|KunenaPrivate
	 */
	static public function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			JPluginHelper::importPlugin('kunena');
			$dispatcher = JEventDispatcher::getInstance();
			$classes = $dispatcher->trigger('onKunenaGetPrivate');

			foreach ($classes as $class)
			{
				if (!is_object($class))
				{
					continue;
				}

				self::$instance = $class;
				break;
			}

			if (!self::$instance)
			{
				self::$instance = new KunenaPrivate();
			}
		}

		return self::$instance;
	}

	/**
	 * @param $userid
	 *
	 * @return string
	 */
	protected function getOnClick($userid)
	{
		return '';
	}

	/**
	 * @param $userid
	 *
	 * @return string
	 */
	protected function getURL($userid)
	{
		return '';
	}

	/**
	 * @param $userid
	 *
	 * @return string
	 */
	public function showIcon($userid)
	{
		$my = JFactory::getUser();

		// Don't send messages from/to anonymous and to yourself
		if ($my->id == 0 || $userid == 0 || $userid == $my->id)
		{
			return '';
		}

		$url = $this->getURL($userid);

		$onclick = $this->getOnClick($userid);

		// No PMS enabled or PM not allowed
		if (empty($url))
		{
			return '';
		}

		// We should offer the user a PM link
		return '<a class="btn btn-small" href="' . $url . '"' . $onclick . ' title="' . JText::_('COM_KUNENA_VIEW_PMS') . '"><span class="icon icon-comments-2"></span></a>';
	}

	/**
	 * @param        $userid
	 * @param string $class
	 * @param string $icon
	 *
	 * @return string
	 * @internal param $text
	 */
	public function shownewIcon($userid, $class='btn btn-small', $icon='icon icon-comments-2')
	{
		$my = JFactory::getUser();
		$url = $this->getURL($userid);
		$onclick = $this->getOnClick($userid);

		// No PMS enabled or PM not allowed
		if (empty($url) || $my->id == 0 || $userid == 0)
		{
			return '';
		}

		// Don't send messages from/to anonymous and to yourself
		if ($userid == $my->id)
		{
			$this->pmCount = $this->getUnreadCount($my->id);
			$text = $this->pmCount ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $this->pmCount) : JText::_('COM_KUNENA_PMS_INBOX');
			$url = $this->getInboxURL();
			return '<a class="' . $class . '" href="' . $url . '"><i class="' . $icon . '"></i>' . $text . '</a>';
		}

		// We should offer the user a PM link
		return '<a class="' . $class . '" href="' . $url . '"' . $onclick . '><i class="' . $icon . '"></i>' . JText::_('COM_KUNENA_PM_WRITE') . '</a>';
	}

	public function getInboxLink($text)
	{
		return '';
	}

	/**
	 * @return string
	 */
	public function getInboxURL()
	{
		return '';
	}

	/**
	 * @param $userid
	 *
	 * @return integer
	 */
	public function getUnreadCount($userid)
	{
		return 0;
	}
}
