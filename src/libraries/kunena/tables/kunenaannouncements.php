<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

require_once __DIR__ . '/kunena.php';

/**
 * Kunena Announcements
 * Provides access to the #__kunena_announcements table
 * @since Kunena
 */
class TableKunenaAnnouncements extends KunenaTable
{
	/**
	 * @var null
	 * @since Kunena
	 */
	public $id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $title = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $created_by = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $sdescription = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $description = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $created = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $publish_up = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $publish_down = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $published = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $ordering = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $showdate = null;

	/**
	 * @param   JDatabaseDriver $db Database driver
	 *
	 * @since Kunena
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_announcement', 'id', $db);
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function check()
	{
		if ($this->created_by)
		{
			$user = KunenaUserHelper::get($this->created_by);

			if (!$user->exists())
			{
				$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_USER_INVALID', (int) $user->userid));
			}
		}
		else
		{
			$this->created_by = KunenaUserHelper::getMyself()->userid;
		}

		if (!$this->created)
		{
			$this->created = Factory::getDate()->toSql();
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
			$this->setError(Text::_('COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_NO_TITLE'));
		}

		$this->sdescription = trim($this->sdescription);
		$this->description  = trim($this->description);

		if (!$this->sdescription)
		{
			$this->setError(Text::_('COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_NO_DESCRIPTION'));
		}

		return $this->getError() == '';
	}
}
