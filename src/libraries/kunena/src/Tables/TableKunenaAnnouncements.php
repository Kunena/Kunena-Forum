<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tables;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;
use UnexpectedValueException;

/**
 * Kunena Announcements
 * Provides access to the #__kunena_announcements table
 *
 * @since   Kunena 6.0
 */
class TableKunenaAnnouncements extends KunenaTable
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $title = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $created_by = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $sdescription = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $description = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $created = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $publish_up = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $publish_down = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $published = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $ordering = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $showdate = null;

	/**
	 * @param   DatabaseDriver  $db  Database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__kunena_announcement', 'id', $db);
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function check(): bool
	{
		if ($this->created_by)
		{
			$user = KunenaUserHelper::get($this->created_by);

			if (!$user->exists())
			{
				throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_USER_INVALID', (int) $user->userid));
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
			throw new UnexpectedValueException(Text::_('COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_NO_TITLE'));
		}

		$this->sdescription = trim($this->sdescription);
		$this->description  = trim($this->description);

		if (!$this->sdescription)
		{
			throw new UnexpectedValueException(Text::_('COM_KUNENA_LIB_TABLE_ANNOUNCEMENTS_ERROR_NO_DESCRIPTION'));
		}

		return true;
	}
}
