<?php
/**
 * Kunena Plugin
 *
 * @package       Kunena.Plugins
 * @subpackage    UddeIM
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

class KunenaPrivateUddeIM extends KunenaPrivate
{
	protected $uddeim = null;
	protected $params = null;

	/**
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->params = $params;

		if (!class_exists('uddeIMAPI'))
		{
			return;
		}

		$this->uddeim = new uddeIMAPI();

		if ($this->uddeim->version() < 1)
		{
			return;
		}
	}

	/**
	 * @param $userid
	 *
	 * @return string
	 */
	protected function getURL($userid)
	{
		static $itemid = false;

		if ($itemid === false)
		{
			$itemid = 0;

			if (method_exists($this->uddeim, 'getItemid'))
			{
				$itemid = $this->uddeim->getItemid();
			}

			if ($itemid)
			{
				$itemid = '&Itemid=' . (int) $itemid;
			}
			else
			{
				$itemid = '';
			}
		}

		return JRoute::_('index.php?option=com_uddeim&task=new&recip=' . (int) $userid . $itemid);
	}

	/**
	 * @param $userid
	 *
	 * @return mixed
	 */
	public function getUnreadCount($userid)
	{
		return $this->uddeim->getInboxUnreadMessages($userid);
	}

	/**
	 * @param $text
	 *
	 * @return string
	 */
	public function getInboxLink($text)
	{
		if (!$text)
		{
			$text = JText::_('COM_KUNENA_PMS_INBOX');
		}

		return '<a href="' . JRoute::_($this->uddeim->getLinkToBox('inbox', false)) . '" rel="follow">' . $text . '</a>';
	}

	/**
	 * @return string
	 */
	public function getInboxURL()
	{
		return JRoute::_($this->uddeim->getLinkToBox('inbox', false));
	}
}
