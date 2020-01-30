<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tables;

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use RuntimeException;
use function defined;

require_once __DIR__ . '/kunena.php';

/**
 * Kunena User Read Table
 * Provides access to the #__kunena_user_read table
 *
 * @since   Kunena 6.0
 */
class KunenaUserRead extends KunenaTable
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $user_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $topic_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $category_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $message_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $time = null;

	/**
	 * @param   DatabaseDriver  $db  Database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_user_read', ['user_id', 'topic_id'], $db);
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	public function check()
	{
		$user  = \Kunena\Forum\Libraries\User\KunenaUserHelper::get($this->user_id);
		$topic = \Kunena\Forum\Libraries\Forum\Topic\TopicHelper::get($this->topic_id);

		if (!$user->exists())
		{
			throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_USER_INVALID', (int) $user->userid));
		}

		if (!$topic->exists())
		{
			throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_USERTOPICS_ERROR_TOPIC_INVALID', (int) $topic->id));
		}
		else
		{
			$this->category_id = $topic->category_id;
		}

		return true;
	}
}
