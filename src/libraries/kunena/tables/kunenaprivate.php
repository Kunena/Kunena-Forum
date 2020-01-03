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

use Joomla\CMS\Language\Text;

require_once(__DIR__ . '/kunena.php');

/**
 * Kunena Private Messages
 * Provides access to the #__kunena_private table
 *
 * @since Kunena 6.0
 */
class TableKunenaPrivate extends KunenaTable
{
	/**
	 * @var null
	 * @since version
	 */
	public $id = null;

	/**
	 * @var null
	 * @since version
	 */
	public $parent_id = null;

	/**
	 * @var null
	 * @since version
	 */
	public $author_id = null;

	/**
	 * @var null
	 * @since version
	 */
	public $created_at = null;

	/**
	 * @var null
	 * @since version
	 */
	public $attachments = null;

	/**
	 * @var null
	 * @since version
	 */
	public $subject = null;

	/**
	 * @var null
	 * @since version
	 */
	public $body = null;

	/**
	 * @var null
	 * @since version
	 */
	public $params = null;

	/**
	 * @param   JDatabaseDriver  $db  Database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_private', 'id', $db);
	}

	/**
	 * @return boolean
	 * @since Kunena
	 * @throws Exception
	 */
	public function check()
	{
		if (!$this->created_at)
		{
			$this->created_at = Joomla\CMS\Date\Date::getInstance()->toSql();
		}

		$this->subject = trim($this->subject);

		if (!$this->subject)
		{
			throw new UnexpectedValueException(Text::sprintf('COM_KUNENA_LIB_TABLE_PRIVATE_ERROR_NO_SUBJECT'));
		}

		if (!$this->body && !$this->attachments)
		{
			throw new UnexpectedValueException(Text::sprintf('COM_KUNENA_LIB_TABLE_PRIVATE_ERROR_NO_BODY'));
		}

		if ($this->params instanceof Joomla\Registry\Registry)
		{
			$this->params = $this->params->toString();
		}

		return true;
	}
}
