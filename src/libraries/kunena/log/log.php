<?php
/**
 * Kunena Component
 * @package       Kunena.Libraries
 * @subpackage    Log
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Implements Kunena Log.
 *
 * @since 5.0
 */
class KunenaLog
{
	// Log types.
	/**
	 * @since Kunena 5.0
	 */
	const TYPE_ADMINISTRATION = 0;

	/**
	 * @since Kunena 5.0
	 */
	const TYPE_MODERATION = 1;

	/**
	 * @since Kunena 5.0
	 */
	const TYPE_ACTION = 2;

	/**
	 * @since Kunena 5.0
	 */
	const TYPE_ERROR = 3;

	/**
	 * @since Kunena 5.0
	 */
	const TYPE_REPORT = 4;

	// Log operations.
	/**
	 * @since Kunena 5.0
	 */
	const LOG_ANNOUNCEMENT_CREATE = 'LOG_ANNOUNCEMENT_CREATE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_ANNOUNCEMENT_EDIT = 'LOG_ANNOUNCEMENT_EDIT';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_ANNOUNCEMENT_DELETE = 'LOG_ANNOUNCEMENT_DELETE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_ANNOUNCEMENT_PUBLISH = 'LOG_ANNOUNCEMENT_PUBLISH';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_ANNOUNCEMENT_UNPUBLISH = 'LOG_ANNOUNCEMENT_UNPUBLISH';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_ATTACHMENT_CREATE = 'LOG_ATTACHMENT_CREATE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_ATTACHMENT_DELETE = 'LOG_ATTACHMENT_DELETE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_POST_CREATE = 'LOG_POST_CREATE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_POST_EDIT = 'LOG_POST_EDIT';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_POST_DELETE = 'LOG_POST_DELETE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_POST_UNDELETE = 'LOG_POST_UNDELETE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_POST_DESTROY = 'LOG_POST_DESTROY';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_POST_MODERATE = 'LOG_POST_MODERATE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_POST_APPROVE = 'LOG_POST_APPROVE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_POST_REPORT = 'LOG_POST_REPORT';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_POST_THANKYOU = 'LOG_POST_THANKYOU';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_POST_UNTHANKYOU = 'LOG_POST_UNTHANKYOU';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_POLL_MODERATE = 'LOG_POLL_MODERATE';

	/**
	 * @since Kunena 5.2
	 */
	const LOG_USER_REPORT_STOPFORUMSPAM = 'LOG_USER_REPORT_STOPFORUMSPAM';

	/**
	 * @since Kunena
	 */
	// Const LOG_PRIVATE_POST_CREATE = 'LOG_PRIVATE_POST_CREATE';

	/**
	 * @since Kunena
	 */
	// Const LOG_PRIVATE_POST_EDIT = 'LOG_PRIVATE_POST_EDIT';

	/**
	 * @since Kunena
	 */
	// Const LOG_PRIVATE_POST_DELETE = 'LOG_PRIVATE_POST_DELETE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_ICON = 'LOG_TOPIC_ICON';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_CREATE = 'LOG_TOPIC_CREATE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_EDIT = 'LOG_TOPIC_EDIT';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_FAVORITE = 'LOG_TOPIC_FAVORITE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_LOCK = 'LOG_TOPIC_LOCK';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_UNLOCK = 'LOG_TOPIC_UNLOCK';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_STICKY = 'LOG_TOPIC_STICKY';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_UNFAVORITE = 'LOG_TOPIC_UNFAVORITE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_UNSTICKY = 'LOG_TOPIC_UNSTICKY';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_MODERATE = 'LOG_TOPIC_MODERATE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_DELETE = 'LOG_TOPIC_DELETE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_UNDELETE = 'LOG_TOPIC_UNDELETE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_DESTROY = 'LOG_TOPIC_DESTROY';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_APPROVE = 'LOG_TOPIC_APPROVE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_TOPIC_REPORT = 'LOG_TOPIC_REPORT';

	/**
	 * @since Kunena
	 */
	// Const LOG_SHADOW_TOPIC_CREATE = 'LOG_SHADOW_TOPIC_CREATE';

	/**
	 * @since Kunena
	 */
	// Const LOG_SHADOW_TOPIC_DELETE = 'LOG_SHADOW_TOPIC_DELETE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_USER_EDIT = 'LOG_USER_EDIT';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_USER_BLOCK = 'LOG_USER_BLOCK';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_USER_UNBLOCK = 'LOG_USER_UNBLOCK';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_USER_BAN = 'LOG_USER_BAN';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_USER_UNBAN = 'LOG_USER_UNBAN';

	/**
	 * @since Kunena
	 */
	// Const LOG_USER_WARNING = 'LOG_USER_WARNING';

	/**
	 * @since Kunena
	 */
	// Const LOG_IMAGE_ATTACHMENT_RESIZE = 'LOG_IMAGE_ATTACHMENT_RESIZE';

	/**
	 * @since Kunena
	 */
	// Const LOG_IMAGE_AVATAR_RESIZE = 'LOG_IMAGE_AVATAR_RESIZE';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_ERROR_GENERAL = 'LOG_ERROR_GENERAL';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_ERROR_CRITICAL = 'LOG_ERROR_CRITICAL';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_ERROR_ALERT = 'LOG_ERROR_ALERT';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_ERROR_EMERGENCY = 'LOG_ERROR_EMERGENCY';

	/**
	 * @since Kunena 5.0
	 */
	const LOG_ERROR_FATAL = 'LOG_ERROR_FATAL';

	/**
	 * @var array|KunenaLogEntry[]
	 * @since Kunena 5.0
	 */
	protected static $entries = array();

	/**
	 * Flush the log entries to the database for storage.
	 *
	 * Should only be called as a shutdown function in order to make sure all items are logged.
	 *
	 * @return void
	 * @since Kunena 5.0
	 */
	public static function flush()
	{
		if (!empty(static::$entries))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->insert('#__kunena_logs')
				->columns('type, user_id, category_id, topic_id, target_user, ip, time, operation, data');

			foreach (static::$entries as $entry)
			{
				$fields = array();

				foreach ($entry->getData() as $field)
				{
					$fields[] = $db->quote($field);
				}

				$fields = implode(',', $fields);

				$query->values($fields);
			}

			$db->setQuery($query);
			$db->execute();
			static::clear();
		}
	}

	/**
	 * Clear out the log entries.
	 *
	 * @return void
	 * @since Kunena 5.0
	 */
	public static function clear()
	{
		static::$entries = array();
	}

	/**
	 * Log new entry.
	 *
	 * @param   int                 $type      Log entry type.
	 * @param   string              $operation Performed operation.
	 * @param   string|array        $data      JSON encoded string.
	 * @param   KunenaForumCategory $category  Target category.
	 * @param   KunenaForumTopic    $topic     Target topic.
	 * @param   KunenaUser          $user      Target user.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena 5.0
	 */
	public static function log(
		$type,
		$operation,
		$data,
		KunenaForumCategory $category = null,
		KunenaForumTopic $topic = null,
		KunenaUser $user = null
	)
	{
		static::$entries[] = new KunenaLogEntry($type, $operation, $data, $category, $topic, $user);
	}

	/**
	 * Add a KunenaLogEntry entry to the Kunena log.
	 *
	 * @param   KunenaLogEntry $entry An instance of an entry to be logged.
	 *
	 * @return void
	 * @since Kunena 5.0
	 */
	public static function addEntry(KunenaLogEntry $entry)
	{
		static::$entries[] = $entry;
	}
}
