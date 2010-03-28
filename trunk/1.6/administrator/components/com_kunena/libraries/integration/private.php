<?php
/**
 * @version $Id: kunena.session.class.php 2071 2010-03-17 11:27:58Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaPrivateMessages
{
	protected static $instance = null;

	protected function __construct() {}

	public function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new KunenaPrivateMessages();
		}
		return self::$instance;
	}

	function getOnClick($userid)
	{
		return '';
	}

	function getURL($userid)
	{
		return '';
	}

	function showSendPMIcon($userid)
	{
		$my = JFactory::getUser();

		// Don't send messages from/to anonymous and to yourself
		if ($my->id == 0 || $userid == 0 || $userid == $my->id) return '';

		$url = $this->getURL($userid);
		$onclick = $this->getOnClick($userid);
		// No PMS system enabled or PM not allowed
		if (empty($url)) return '';

		// We should offer the user a PM link
		return '<a href="' . $url . '"' .$onclick. ' title="'.JText::_('K_PM_SEND_TITLE').'">' .$this->_getIcon() . '</a>';
	}

	protected function _getIcon()
	{
		global $kunenaIcons;

		if ($kunenaIcons['pms']) {
			$html = '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['pms'] . '" alt="' .JText::_('K_PM_ICONTEXT'). '" />';
		}
		else {
			$html = JText::_('K_PM_ICONTEXT');
		}

		return $html;
	}
}
