<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
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

		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			$class = 'btn btn-small';
		}
		elseif ($topicicontype == 'B2')
		{
			$class = 'btn btn-small';
		}
		elseif ($topicicontype == 'B3')
		{
			$class = 'btn btn-default btn-sm';
		}
		else
		{
			$class = 'btn btn-small';
		}

		$url = $this->getURL($userid);

		$onclick = $this->getOnClick($userid);

		// No PMS enabled or PM not allowed
		if (empty($url))
		{
			return '';
		}

		// We should offer the user a PM link
		return '<a class="' . $class . '" href="' . $url . '""' . $onclick . '">' . KunenaIcons::pm() .'</a>';
	}

	/**
	 * @param        $userid
	 * @param string $class
	 * @param string $icon
	 *
	 * @return string
	 * @internal param $text
	 */
	public function shownewIcon($userid, $class = '', $icon = '')
	{
		$my = JFactory::getUser();
		$url = $this->getURL($userid);
		$onclick = $this->getOnClick($userid);

		// No PMS enabled or PM not allowed
		if (empty($url) || $my->id == 0 || $userid == 0)
		{
			return '';
		}

		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if (empty($class))
		{
			if ($topicicontype == 'fa')
			{
				$class = 'btn btn-small';
			}
			elseif ($topicicontype == 'B2')
			{
				$class = 'btn btn-small';
			}
			elseif ($topicicontype == 'B3')
			{
				$class = 'btn btn-default btn-sm';
			}
			else
			{
				$class = 'btn btn-small';
			}
		}

		// Don't send messages from/to anonymous and to yourself
		if ($userid == $my->id)
		{
			$this->pmCount = $this->getUnreadCount($my->id);
			$text = $this->pmCount ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $this->pmCount) : JText::_('COM_KUNENA_PMS_INBOX');
			$url = $this->getInboxURL();
			return '<a class="' . $class . '" href="' . $url . '">' . KunenaIcons::pm() . ' ' . $text . '</a>';
		}

		// We should offer the user a PM link
		return '<a class="' . $class . '" href="' . $url . '"' . $onclick . '>'. KunenaIcons::pm() . ' ' . JText::_('COM_KUNENA_PM_WRITE') . '</a>';
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
