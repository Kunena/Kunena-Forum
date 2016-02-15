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
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaPrivate
 */
class KunenaPrivate
{
	protected static $instance = false;

	static public function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			JPluginHelper::importPlugin('kunena');
			$dispatcher = JDispatcher::getInstance();
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

	protected function getOnClick($userid)
	{
		return '';
	}

	protected function getURL($userid)
	{
		return '';
	}

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
		return '<a href="' . $url . '"' .$onclick. ' title="'.JText::_('COM_KUNENA_VIEW_PMS').'"><span class="kicon-profile kicon-profile-pm" alt="' .JText::_('COM_KUNENA_VIEW_PMS'). '"></span></a>';
	}

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

	public function getInboxURL()
	{
		return '';
	}

	public function getUnreadCount($userid)
	{
		return 0;
	}
}
