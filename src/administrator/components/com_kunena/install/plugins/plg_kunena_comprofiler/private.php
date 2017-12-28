<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Comprofiler
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

class KunenaPrivateComprofiler extends KunenaPrivate
{
	protected $params = null;

	/**
	 * KunenaPrivateComprofiler constructor.
	 *
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param $userid
	 *
	 * @return string|void
	 */
	protected function getURL($userid)
	{
	}

	/**
	 * @param $userid
	 *
	 * @return string
	 */
	public function showIcon($userid)
	{
		global $_CB_framework, $_CB_PMS;

		$myid = $_CB_framework->myId();

		// Don't send messages from/to anonymous and to yourself
		if ($myid == 0 || $userid == 0 || $userid == $myid)
		{
			return '';
		}

		outputCbTemplate($_CB_framework->getUi());
		$resultArray = $_CB_PMS->getPMSlinks($userid, $myid, '', '', 1);
		$url = $_CB_framework->userProfileUrl($userid);
		$html        = '<a href="' . $url . '" title="' .
			JText::_('COM_KUNENA_VIEW_PMS') . '"><span class="kicon-profile kicon-profile-pm" alt="' . JText::_('COM_KUNENA_VIEW_PMS') . '"></span></a>';

		if (count($resultArray) > 0)
		{
			$linkItem = '<span class="pm" alt="' . JText::_('COM_KUNENA_VIEW_PMS') . '" />';

			foreach ($resultArray as $res)
			{
				if (is_array($res))
				{
					$html .= '<a href="' . cbSef($res["url"]) . '" title="' . getLangDefinition($res["tooltip"]) . '">' . $linkItem . '</a> ';
				}
			}
		}

		return $html;
	}

	/**
	 * @param        $userid
	 * @param string $class
	 * @param string $icon
	 *
	 * @return string
	 */
	public function shownewIcon($userid, $class='btn btn-small', $icon='icon icon-comments-2')
	{
		global $_CB_framework, $_CB_PMS;

		$myid = $_CB_framework->myId();

		// Don't send messages from/to anonymous and to yourself
		if ($myid == 0 || $userid == 0)
		{
			return '';
		}

		$url = $_CB_framework->userProfileUrl($userid);
		$html        = '<a class="' . $class . '" href="' . $url . '" title="' .
			JText::_('COM_KUNENA_VIEW_PMS') . '"><i class="' . $icon . '"></i>' . JText::_('COM_KUNENA_PM_WRITE') . '</a>';

		if ($userid == $myid)
		{
			$this->pmCount = $this->getUnreadCount($myid);
			$text = $this->pmCount ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $this->pmCount) : JText::_('COM_KUNENA_PMS_INBOX');
			$url = $this->getInboxURL();

			return '<a class="' . $class . '" href="' . $url . '"><i class="' . $icon . '"></i>' . $text . '</a>';
		}

		return $html;
	}

	/**
	 * @param $text
	 *
	 * @return null|string
	 */
	public function getInboxLink($text)
	{
		global $_CB_framework;

		if (!$text)
		{
			$text = JText::_('COM_KUNENA_PMS_INBOX');
		}

		$userid = $this->getCBUserid();

		if ($userid === null)
		{
			return null;
		}

		return '<a href="' . $_CB_framework->userProfileUrl($userid) . '" rel="follow">' . $text . '</a>';
	}

	/**
	 * @return null
	 */
	protected function getCBUserid()
	{
		global $_CB_framework;

		$cbpath = JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php';

		if (file_exists($cbpath))
		{
			require_once($cbpath);
		}
		else
		{
			return null;
		}

		$userid = $_CB_framework->myId();

		$cbUser = CBuser::getInstance((int) $userid);

		if ($cbUser === null)
		{
			return null;
		}

		return $userid;
	}

	/**
	 * @return null|string
	 */
	public function getInboxURL()
	{
		global $_CB_framework;

		$userid = $this->getCBUserid();

		if ($userid === null)
		{
			return null;
		}

		return $_CB_framework->userProfileUrl($userid);
	}
}
