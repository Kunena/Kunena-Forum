<?php
/**
 * Kunena Plugin
 *
 * @package       Kunena.Plugins
 * @subpackage    Comprofiler
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

class KunenaPrivateComprofiler extends KunenaPrivate
{
	protected $params = null;

	public function __construct($params)
	{
		$this->params = $params;
	}

	protected function getURL($userid)
	{
	}

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
		$url = 'index.php?option=com_comprofiler&task=userProfile&user=' . $userid . getCBprofileItemid();
		$html        = '<a href="' . $url . '" title="'.JText::_('COM_KUNENA_VIEW_PMS').'"><span class="kicon-profile kicon-profile-pm" alt="' .JText::_('COM_KUNENA_VIEW_PMS'). '"></span></a>';

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

	public function shownewIcon($userid, $class='btn btn-small', $icon='icon icon-comments-2')
	{
		global $_CB_framework, $_CB_PMS;

		$myid = $_CB_framework->myId();

		// Don't send messages from/to anonymous and to yourself
		if ($myid == 0 || $userid == 0)
		{
			return '';
		}

		$url = 'index.php?option=com_comprofiler&task=userProfile&user=' . $userid . getCBprofileItemid();
		$html        = '<a class="' . $class . '" href="' . $url . '" title="'.JText::_('COM_KUNENA_VIEW_PMS').'"><i class="' . $icon . '"></i>' . JText::_('COM_KUNENA_PM_WRITE') . '</a>';

		if ($userid == $myid)
		{
			$this->pmCount = $this->getUnreadCount($myid);
			$text = $this->pmCount ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $this->pmCount) : JText::_('COM_KUNENA_PMS_INBOX');
			$url = $this->getInboxURL();
			return '<a class="' . $class . '" href="' . $url . '"><i class="' . $icon . '"></i>' . $text . '</a>';
		}

		return $html;
	}

	public function getInboxLink($text)
	{
		if (!$text)
		{
			$text = JText::_('COM_KUNENA_PMS_INBOX');
		}

		$userid = $this->getCBUserid();
		if ($userid === null)
		{
			return null;
		}

		$itemid = getCBprofileItemid();

		return '<a href="' . cbSef('index.php?option=com_comprofiler&task=userProfile&user=' . $userid . $itemid) . '" rel="follow">' . $text . '</a>';
	}

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

	public function getInboxURL()
	{
		$userid = $this->getCBUserid();
		if ($userid === null)
		{
			return null;
		}

		$itemid = getCBprofileItemid();

		return cbSef('index.php?option=com_comprofiler&task=userProfile&user=' . $userid . $itemid);
	}
}
