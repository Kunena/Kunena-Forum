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

require_once __DIR__ . '/kunena.php';

/**
 * Kunena Polls
 * Provides access to the #__kunena_polls table
 * @since Kunena
 */
class TableKunenaPolls extends KunenaTable
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
	public $threadid = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $polltimetolive = null;

	/**
	 * @param   JDatabaseDriver $db Database driver
	 *
	 * @since Kunena
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_polls', 'id', $db);
	}
}
