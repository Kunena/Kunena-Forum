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

use Joomla\CMS\Language\Text;

require_once __DIR__ . '/kunena.php';

/**
 * Kunena Sessions
 * Provides access to the #__kunena_sessions table
 * @since Kunena
 */
class TableKunenaSessions extends KunenaTable
{
	/**
	 * @var integer
	 * @since Kunena
	 */
	public $userid = 0;

	/**
	 * @var string
	 * @since Kunena
	 */
	public $allowed = 'na';

	/**
	 * @var null
	 * @since Kunena
	 */
	public $allowedcats = null;

	/**
	 * @var integer
	 * @since Kunena
	 */
	public $lasttime = 0;

	/**
	 * @var integer
	 * @since Kunena
	 */
	public $readtopics = 0;

	/**
	 * @var integer
	 * @since Kunena
	 */
	public $currvisit = 0;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $_exists = false;

	/**
	 * TableKunenaSessions constructor.
	 *
	 * @param   JDatabaseDriver $db Database driver
	 *
	 * @since Kunena
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_sessions', 'userid', $db);
	}

	/**
	 * @param   null $oid   oid
	 * @param   bool $reset reset
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function load($oid = null, $reset = true)
	{
		if (!$oid)
		{
			return false;
		}

		return parent::load($oid, $reset);
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function check()
	{
		$user = KunenaUserHelper::get($this->userid);

		if (!$user->exists())
		{
			$this->setError(Text::sprintf('COM_KUNENA_LIB_TABLE_SESSIONS_ERROR_USER_INVALID', (int) $user->userid));
		}

		return $this->getError() == '';
	}
}
