<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Integration\KunenaPrivate;

/**
 * Class KunenaPrivateComprofiler
 *
 * @since   Kunena 6.0
 */
class KunenaPrivateComprofiler extends KunenaPrivate
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * KunenaPrivateComprofiler constructor.
	 *
	 * @param   object  $params  params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(object $params)
	{
		$this->params = $params;
	}

	/**
	 * @param   int  $userid  userid
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function showIcon(int $userid): string
	{
		global $_CB_framework, $_CB_PMS;

		$myid = Application::MyUser()->getUserId();

		// Don't send messages from/to anonymous and to yourself
		if ($myid == 0 || $userid == 0 || $userid == $myid)
		{
			return '';
		}

		outputCbTemplate(Application::Cms()->getClientId());
		$resultArray = $_CB_PMS->getPMSlinks($userid, $myid, '', '', 1);
		$url         = $_CB_framework->userProfileUrl($userid);
		$html        = '<a href="' . $url . '" title="' .
			Text::_('COM_KUNENA_VIEW_PMS') . '"><span class="kicon-profile kicon-profile-pm" title="' . Text::_('COM_KUNENA_VIEW_PMS') . '"></span></a>';

		if ($resultArray > 0)
		{
			$linkItem = '<span class="pm" alt="' . Text::_('COM_KUNENA_VIEW_PMS') . '" />';

			foreach ($resultArray as $res)
			{
				if (is_array($res))
				{
					$html .= '<a href="' . cbSef($res["url"]) . '" title="' . CBTxt::T($res["tooltip"]) . '">' . $linkItem . '</a> ';
				}
			}
		}

		return $html;
	}

	/**
	 * @param   int     $userid  userid
	 * @param   string  $class   class
	 * @param   string  $icon    icon
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function showNewIcon(int $userid, $class = 'btn btn-small', $icon = 'icon icon-comments-2'): string
	{
		global $_CB_framework, $_CB_PMS;

		$myid = Application::MyUser()->getUserId();

		// Don't send messages from/to anonymous and to yourself
		if ($myid == 0 || $userid == 0)
		{
			return '';
		}

		$url  = $_CB_framework->userProfileUrl($userid);
		$html = '<a class="' . $class . '" href="' . $url . '" title="' .
			Text::_('COM_KUNENA_VIEW_PMS') . '"><i class="' . $icon . '"></i>' . Text::_('COM_KUNENA_PM_WRITE') . '</a>';

		if ($userid == $myid)
		{
			$pmCount = $this->getUnreadCount($myid);
			$text    = $pmCount ? Text::sprintf('COM_KUNENA_PMS_INBOX_NEW', $pmCount) : Text::_('COM_KUNENA_PMS_INBOX');
			$url     = $this->getInboxURL();

			return '<a class="' . $class . '" href="' . $url . '"><i class="' . $icon . '"></i>' . $text . '</a>';
		}

		return $html;
	}

	/**
	 * @return  void|string
	 *
	 * @since   Kunena 6.0
	 */
	public function getInboxURL()
	{
		global $_CB_framework;

		$userid = $this->getCBUserid();

		if ($userid === null)
		{
			return;
		}

		return $_CB_framework->userProfileUrl($userid);
	}

	/**
	 * @return  integer|void
	 *
	 * @since   Kunena 6.0
	 */
	protected function getCBUserid()
	{
		global $_CB_framework;

		$cbpath = JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php';

		if (file_exists($cbpath))
		{
			require_once $cbpath;
		}
		else
		{
			return;
		}

		$userid = Application::MyUser()->getUserId();

		$cbUser = CBuser::getInstance((int) $userid);

		if ($cbUser === null)
		{
			return;
		}

		return $userid;
	}

	/**
	 * @param   string  $text  text
	 *
	 * @return  void|string
	 *
	 * @since   Kunena 6.0
	 */
	public function getInboxLink(string $text)
	{
		global $_CB_framework;

		if (!$text)
		{
			$text = Text::_('COM_KUNENA_PMS_INBOX');
		}

		$userid = $this->getCBUserid();

		if ($userid === null)
		{
			return;
		}

		return '<a href="' . $_CB_framework->userProfileUrl($userid) . '" rel="follow">' . $text . '</a>';
	}

	/**
	 * @param   int  $userid  userid
	 *
	 * @return string
	 *
	 * @since   Kunena 6.0
	 */
	protected function getURL(int $userid): string
	{
	}
}
