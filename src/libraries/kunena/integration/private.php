<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class KunenaPrivate
 * @since Kunena
 */
class KunenaPrivate
{
	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected static $instance = false;

	/**
	 * @param   null $integration integration
	 *
	 * @return boolean|KunenaPrivate
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

			$classes = Factory::getApplication()->triggerEvent('onKunenaGetPrivate');

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
				self::$instance = new KunenaPrivate;
			}
		}

		return self::$instance;
	}

	/**
	 * @param   integer $userid userid
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public function showIcon($userid)
	{
		$my = Factory::getUser();

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
		return '<a class="' . $class . '" href="' . $url . '""' . $onclick . '">' . KunenaIcons::pm() . '</a>';
	}

	/**
	 * @param   integer $userid userid
	 *
	 * @return string
	 * @since Kunena
	 */
	protected function getURL($userid)
	{
		return '';
	}

	/**
	 * @param   integer $userid userid
	 *
	 * @return string
	 * @since Kunena
	 */
	protected function getOnClick($userid)
	{
		return '';
	}

	/**
	 * @param   integer $userid userid
	 * @param   string  $class  class
	 * @param   string  $icon   icon
	 *
	 * @return string
	 * @throws Exception
	 * @internal param $text
	 * @since    Kunena
	 */
	public function shownewIcon($userid, $class = '', $icon = '')
	{
		$my      = Factory::getUser();
		$url     = $this->getURL($userid);
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
			$pmCount = $this->getUnreadCount($my->id);
			$text    = $pmCount ? Text::sprintf('COM_KUNENA_PMS_INBOX_NEW', $pmCount) : Text::_('COM_KUNENA_PMS_INBOX');
			$url     = $this->getInboxURL();

			return '<a class="' . $class . '" href="' . $url . '">' . KunenaIcons::pm() . ' ' . $text . '</a>';
		}

		// We should offer the user a PM link
		return '<a class="' . $class . '" href="' . $url . '"' . $onclick . '>' . KunenaIcons::pm() . ' ' . Text::_('COM_KUNENA_PM_WRITE') . '</a>';
	}

	/**
	 * @param   integer $userid userid
	 *
	 * @return integer
	 * @since Kunena
	 */
	public function getUnreadCount($userid)
	{
		return 0;
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getInboxURL()
	{
		return '';
	}

	/**
	 * @param   string $text text
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getInboxLink($text)
	{
		return '';
	}
}
