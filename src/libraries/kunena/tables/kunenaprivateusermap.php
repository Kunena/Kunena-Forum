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
require_once(__DIR__ . '/kunena.php');

/**
 * Kunena Private Message map to users.
 * Provides access to the #__kunena_private_user_map table
 *
 * @since Kunena 6.0
 */
class TableKunenaPrivateUserMap extends KunenaTable
{
	protected $_autoincrement = false;
	public $private_id = null;
	public $user_id = null;
	public $read_at = null;
	public $replied_at = null;
	public $deleted_at = null;

	public function __construct($db)
	{
		parent::__construct('#__kunena_private_user_map', array('private_id', 'user_id'), $db);
	}
}
