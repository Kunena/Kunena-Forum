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

use Joomla\Database\DatabaseDriver;
use UnexpectedValueException;

/**
 * Kunena Keywords Table
 * Provides access to the #__kunena_keywords table
 *
 * @since   Kunena 6.0
 */
class TableKunenaKeywords extends KunenaTable
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
	public $name = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $public_count = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $total_count = null;

	/**
	 * @param   DatabaseDriver  $db  Database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__kunena_keywords', 'id', $db);
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function check(): bool
	{
		$this->name = trim($this->name);

		if (!$this->name)
		{
			throw new UnexpectedValueException;
		}

		return true;
	}
}
