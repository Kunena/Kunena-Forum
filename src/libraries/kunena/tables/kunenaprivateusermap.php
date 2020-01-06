<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\Database\DatabaseDriver;

require_once(__DIR__ . '/kunena.php');

/**
 * Kunena Private Message map to users.
 * Provides access to the #__kunena_private_user_map table
 *
 * @since   Kunena 6.0
 */
class TableKunenaPrivateUserMap extends KunenaTable
{
	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_autoincrement = false;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $private_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $user_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $read_at = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $replied_at = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $deleted_at = null;

	/**
	 * TableKunenaPrivateUserMap constructor.
	 *
	 * @param   DatabaseDriver  $db database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_private_user_map', ['private_id', 'user_id'], $db);
	}
}
