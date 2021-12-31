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

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use UnexpectedValueException;

/**
 * Kunena Private Messages
 * Provides access to the #__kunena_private table
 *
 * @since   Kunena 6.0
 */
class TableKunenaPrivate extends KunenaTable
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
	public $parentid = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $author_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $created_at = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $attachments = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $subject = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $body = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $params = null;

	/**
	 * @param   DatabaseDriver  $db  Database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__kunena_private', 'id', $db);
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function check(): bool
	{
		if (!$this->created_at)
		{
			$this->created_at = Date::getInstance()->toSql();
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

		if ($this->params instanceof Registry)
		{
			$this->params = $this->params->toString();
		}

		return true;
	}
}
