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
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;

/**
 * Kunena Sessions
 * Provides access to the #__kunena_sessions table
 *
 * @since   Kunena 6.0
 */
class KunenaSessions extends KunenaTable
{
	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $userid = 0;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $allowed = 'na';

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $allowedcats = null;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $lasttime = 0;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $readtopics = 0;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $currvisit = 0;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_exists = false;

	/**
	 * TableKunenaSessions constructor.
	 *
	 * @param   DatabaseDriver  $db  Database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__kunena_sessions', 'userid', $db);
	}

	/**
	 * @param   null  $oid    oid
	 * @param   bool  $reset  reset
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function load($oid = null, $reset = true): bool
	{
		if (!$oid)
		{
			return false;
		}

		return parent::load($oid, $reset);
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
		$user = KunenaUserHelper::get($this->userid);

		if (!$user->exists())
		{
			throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_SESSIONS_ERROR_USER_INVALID', (int) $user->userid));
		}

		return true;
	}
}
