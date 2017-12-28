<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

require_once(__DIR__ . '/kunena.php');
/**
 * Kunena Announcements
 * Provides access to the #__kunena_announcements table
 */
class TableKunenaAnnouncements extends KunenaTable
{
	public $id = null;
	public $title = null;
	public $created_by = null;
	public $sdescription = null;
	public $description = null;
	public $created = null;
	public $publish_up = null;
	public $publish_down = null;
	public $published = null;
	public $ordering = null;
	public $showdate = null;

	/**
	 * @param   string $db
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_announcement', 'id', $db);
	}

	/**
	 * @return boolean
	 */
	public function check()
	{
		if ($this->created_by)
		{
			$user = KunenaUserHelper::get($this->created_by);
			if (!$user->exists())
			{
				$this->setError(JText::sprintf('COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_USER_INVALID', (int) $user->userid));
			}
		}
		else
		{
			$this->created_by = KunenaUserHelper::getMyself()->userid;
		}

		if (!$this->created)
		{
			$this->created = JFactory::getDate()->toSql();
		}

		if (!$this->publish_up)
		{
			$this->publish_up = '';
		}

		if (!$this->publish_down)
		{
			$this->publish_down = '';
		}

		$this->title = trim($this->title);
		if (!$this->title)
		{
			$this->setError(JText::_('COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_NO_TITLE'));
		}

		$this->sdescription = trim($this->sdescription);
		$this->description = trim($this->description);

		if (!$this->sdescription)
		{
			$this->setError(JText::_('COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_NO_DESCRIPTION'));
		}

		return ($this->getError() == '');
	}
}
