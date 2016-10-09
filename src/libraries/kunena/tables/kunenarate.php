<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
	public $topic_id = null;

	public $userid = null;

	public $rate = null;

	public $time = null;

	/**
	 * @param   string $db
	 *
	 * @since Kunena
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_rate', 'topic_id', $db);
	}
}
