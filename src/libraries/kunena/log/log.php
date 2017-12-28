<?php
/**
 * Kunena Component
 * @package Kunena.Libraries
 * @subpackage Log
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Implements Kunena Log.
 *
 * @since 5.0
 */
class KunenaLog
{
	// Log types.
	const TYPE_ADMINISTRATION = 0;
	const TYPE_MODERATION = 1;
	const TYPE_ACTION = 2;
	const TYPE_ERROR = 3;
	const TYPE_REPORT = 4;

	// Log operations.
	const LOG_ANNOUNCEMENT_CREATE = 'LOG_ANNOUNCEMENT_CREATE';
	const LOG_ANNOUNCEMENT_EDIT = 'LOG_ANNOUNCEMENT_EDIT';
	const LOG_ANNOUNCEMENT_DELETE = 'LOG_ANNOUNCEMENT_DELETE';
	const LOG_ANNOUNCEMENT_PUBLISH = 'LOG_ANNOUNCEMENT_PUBLISH';
	const LOG_ANNOUNCEMENT_UNPUBLISH = 'LOG_ANNOUNCEMENT_UNPUBLISH';

	const LOG_ATTACHMENT_CREATE = 'LOG_ATTACHMENT_CREATE';
	const LOG_ATTACHMENT_DELETE = 'LOG_ATTACHMENT_DELETE';

	const LOG_POST_CREATE = 'LOG_POST_CREATE';
	const LOG_POST_EDIT = 'LOG_POST_EDIT';
	const LOG_POST_DELETE = 'LOG_POST_DELETE';
	const LOG_POST_UNDELETE = 'LOG_POST_UNDELETE';
	const LOG_POST_DESTROY = 'LOG_POST_DESTROY';
	const LOG_POST_MODERATE = 'LOG_POST_MODERATE';
	const LOG_POST_APPROVE = 'LOG_POST_APPROVE';
	const LOG_POST_REPORT = 'LOG_POST_REPORT';
	const LOG_POST_THANKYOU = 'LOG_POST_THANKYOU';
	const LOG_POST_UNTHANKYOU = 'LOG_POST_UNTHANKYOU';

	const LOG_POLL_MODERATE = 'LOG_POLL_MODERATE';

	const LOG_PRIVATE_POST_CREATE = 'LOG_PRIVATE_POST_CREATE';
	const LOG_PRIVATE_POST_EDIT = 'LOG_PRIVATE_POST_EDIT';
	const LOG_PRIVATE_POST_DELETE = 'LOG_PRIVATE_POST_DELETE';

	const LOG_TOPIC_ICON = 'LOG_TOPIC_ICON';
	const LOG_TOPIC_CREATE = 'LOG_TOPIC_CREATE';
	const LOG_TOPIC_EDIT = 'LOG_TOPIC_EDIT';
	const LOG_TOPIC_FAVORITE = 'LOG_TOPIC_FAVORITE';
	const LOG_TOPIC_LOCK = 'LOG_TOPIC_LOCK';
	const LOG_TOPIC_UNLOCK = 'LOG_TOPIC_UNLOCK';
	const LOG_TOPIC_STICKY = 'LOG_TOPIC_STICKY';
	const LOG_TOPIC_UNFAVORITE = 'LOG_TOPIC_UNFAVORITE';
	const LOG_TOPIC_UNSTICKY = 'LOG_TOPIC_UNSTICKY';
	const LOG_TOPIC_MODERATE = 'LOG_TOPIC_MODERATE';
	const LOG_TOPIC_DELETE = 'LOG_TOPIC_DELETE';
	const LOG_TOPIC_UNDELETE = 'LOG_TOPIC_UNDELETE';
	const LOG_TOPIC_DESTROY = 'LOG_TOPIC_DESTROY';
	const LOG_TOPIC_APPROVE = 'LOG_TOPIC_APPROVE';
	const LOG_TOPIC_REPORT = 'LOG_TOPIC_REPORT';

	const LOG_SHADOW_TOPIC_CREATE = 'LOG_SHADOW_TOPIC_CREATE';
	const LOG_SHADOW_TOPIC_DELETE = 'LOG_SHADOW_TOPIC_DELETE';

	const LOG_USER_EDIT = 'LOG_USER_EDIT';
	const LOG_USER_BLOCK = 'LOG_USER_BLOCK';
	const LOG_USER_UNBLOCK = 'LOG_USER_UNBLOCK';
	const LOG_USER_BAN = 'LOG_USER_BAN';
	const LOG_USER_UNBAN = 'LOG_USER_UNBAN';
	const LOG_USER_WARNING = 'LOG_USER_WARNING';

	const LOG_IMAGE_ATTACHMENT_RESIZE = 'LOG_IMAGE_ATTACHMENT_RESIZE';
	const LOG_IMAGE_AVATAR_RESIZE = 'LOG_IMAGE_AVATAR_RESIZE';

	const LOG_ERROR_GENERAL = 'LOG_ERROR_GENERAL';
	const LOG_ERROR_CRITICAL = 'LOG_ERROR_CRITICAL';
	const LOG_ERROR_ALERT = 'LOG_ERROR_ALERT';
	const LOG_ERROR_EMERGENCY = 'LOG_ERROR_EMERGENCY';
	const LOG_ERROR_FATAL = 'LOG_ERROR_FATAL';

	/**
	 * @var array|KunenaLogEntry[]
	 */
	protected static $entries = array();

	/**
	 * Flush the log entries to the database for storage.
	 *
	 * Should only be called as a shutdown function in order to make sure all items are logged.
	 *
	 * @return void
	 */
	public static function flush()
	{
		if (!empty(static::$entries))
		{
			$db = JFactory::getDbo();
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
	 */
	public static function clear()
	{
		static::$entries = array();
	}

	/**
	 * Log new entry.
	 *
	 * @param int                 $type         Log entry type.
	 * @param string              $operation    Performed operation.
	 * @param string              $data         JSON encoded string.
	 * @param KunenaForumCategory $category     Target category.
	 * @param KunenaForumTopic    $topic        Target topic.
	 * @param KunenaUser          $user         Target user.
	 *
	 * @return void
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
	 * @param KunenaLogEntry  $entry  An instance of an entry to be logged.
	 *
	 * @return void
	 */
	public static function addEntry(KunenaLogEntry $entry)
	{
		static::$entries[] = $entry;
	}
}
