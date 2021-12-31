<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tables;

\defined('_JEXEC') or die();

use Joomla\Database\DatabaseDriver;

/**
 * Kunena Private Message map to forum posts.
 * Provides access to the #__kunena_private_post_map table
 *
 * @since   Kunena 6.0
 */
class TableKunenaPrivatePostMap extends KunenaTable
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $private_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $message_id = null;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_autoincrement = false;

	/**
	 * TableKunenaPrivatePostMap constructor.
	 *
	 * @param   DatabaseDriver  $db  database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__kunena_private_post_map', ['private_id', 'message_id'], $db);
	}
}
