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
 * Kunena Rate
 * Provides access to the #__kunena_rate table
 * @since Kunena
 */
class TableKunenaRate extends KunenaTable
{
	/**
	 * @var null
	 * @since Kunena
	 */
	public $topic_id = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $userid = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $rate = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $time = null;

	/**
	 * @param   JDatabaseDriver $db Database driver
	 *
	 * @since Kunena
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_rate', 'topic_id', $db);
	}
}
