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
 * Kunena Private Message map to attachments.
 * Provides access to the #__kunena_private_attachment_map table
 *
 * @since   Kunena 6.0
 */
class TableKunenaPrivateAttachmentMap extends KunenaTable
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
	public $attachment_id = null;

	/**
	 * TableKunenaPrivateAttachmentMap constructor.
	 *
	 * @param   DatabaseDriver  $db database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_private_attachment_map', array('private_id', 'attachment_id'), $db);
	}
}
