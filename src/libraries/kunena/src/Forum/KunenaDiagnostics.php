<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\QueryInterface;
use Kunena\Forum\Libraries\Error\KunenaError;
use stdClass;

/**
 * Class KunenaForumDiagnostics
 *
 * @since   Kunena 6.0
 */
abstract class KunenaDiagnostics
{
	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public static function getList(): array
	{
		return [
			'categoryOrphaned',
			'categoryMissingAlias',
			'categoryWrongAlias',
			'aliasMissingCategory',
			'messageBodyMissingMessage',
			'messageMissingMessageBody',
			'topicInSection',
			'topicMissingCategory',
			'topicMissingMessages',
			'topicMissingPoll',
			'topicPollMismatch',
			'movedMissingTopic',
			'movedAndMessages',
			'messageWrongCategory',
			'messageOrphaned',
			'attachmentOrphaned',
			'pollOrphaned',
			'pollTopicMismatch',
			'pollOptionOrphaned',
			'pollUserOrphaned',
			'thankyouOrphaned',
			'userCategoryOrphaned',
			'userReadOrphaned',
			'userReadWrongCategory',
			'userTopicOrphaned',
			'userTopicWrongCategory',
			'ratingOrphaned',
			'channelOrphaned',
			'userAvatarOrphaned',
			'topicsOwnersOrphaned',
		];
	}

	/**
	 * @param   string  $function  function
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public static function count(string $function): int
	{
		$function = 'query_' . $function;

		if (method_exists(__CLASS__, $function))
		{
			$query = self::$function();

			$db = Factory::getContainer()->get('DatabaseDriver');
			$query->select('COUNT(*)');
			$db->setQuery($query);

			try
			{
				return (int) $db->loadResult();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return 0;
	}

	/**
	 * @param   string  $function  function
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public static function getItems(string $function): array
	{
		$queryFunction = 'query_' . $function;

		if (method_exists(__CLASS__, $queryFunction))
		{
			$query          = self::$queryFunction();
			$fieldsFunction = 'fields_' . $function;

			if (!method_exists(__CLASS__, $fieldsFunction))
			{
				$fieldsFunction = 'fields';
			}

			self::$fieldsFunction($query);
			$db = Factory::getContainer()->get('DatabaseDriver');
			$db->setQuery($query);

			try
			{
				return (array) $db->loadAssocList();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return [];
	}

	/**
	 * @param   string  $function  function
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public static function fix(string $function): bool
	{
		$queryFunction = 'fix_' . $function;

		if (method_exists(__CLASS__, $queryFunction))
		{
			$query = self::$queryFunction();
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$db->setQuery($query);

			try
			{
				return (bool) $db->execute();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return false;
	}

	/**
	 * @param   string  $function  function
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public static function canFix(string $function): bool
	{
		$queryFunction = 'fix_' . $function;

		if (method_exists(__CLASS__, $queryFunction))
		{
			return true;
		}

		return false;
	}

	/**
	 * @param   string  $function  function
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public static function canNotice(string $function): bool
	{
		$queryFunction = 'notice_' . $function;

		if (method_exists(__CLASS__, $queryFunction))
		{
			return self::$queryFunction();
		}

		return false;
	}

	/**
	 * @param   string  $function  function
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public static function delete(string $function): bool
	{
		$queryFunction = 'delete_' . $function;

		if (method_exists(__CLASS__, $queryFunction))
		{
			$query = self::$queryFunction();
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$db->setQuery($query);

			try
			{
				return (bool) $db->execute();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return false;
	}

	/**
	 * @param   string  $function  function
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public static function canDelete(string $function): bool
	{
		$queryFunction = 'delete_' . $function;

		if (method_exists(__CLASS__, $queryFunction))
		{
			return true;
		}

		return false;
	}

	/**
	 * @param   string  $function  function
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public static function getFieldInfo(string $function): array
	{
		static $fields = [];

		if (!isset($fields[$function]))
		{
			$fieldsFunction = 'fields_' . $function;

			if (!method_exists(__CLASS__, $fieldsFunction))
			{
				$fieldsFunction = 'fields';
			}

			$fields[$function] = (array) self::$fieldsFunction();
		}

		return $fields[$function];
	}

	/**
	 * @param   string  $function  function
	 *
	 * @return string
	 *
	 * @since   Kunena 6.0
	 */
	public static function getQuery(string $function): string
	{
		$function = 'query_' . $function;

		if (method_exists(__CLASS__, $function))
		{
			$query = self::$function();

			$query->select("COUNT(*)");

			return (string) $query;
		}

		return false;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select("a.*");
		}

		return [];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_categoryOrphaned(): string
	{
		return "DELETE a FROM #__kunena_categories AS a LEFT JOIN #__kunena_categories AS c ON a.parentid=c.id WHERE a.parentid>0 AND c.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_categoryOrphaned(): QueryInterface
	{
		// Query to find orphaned categories
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_categories AS a")->leftJoin("#__kunena_categories AS c ON a.parentid=c.id")->where("a.parentid>0 AND c.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_categoryOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.id, a.parentid, a.name, a.alias, a.description');
		}

		return ['name' => 'link', 'parentid' => 'invalid', '_link' => '&view=categories&layout=edit&catid={$id}'];
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_categoryMissingAlias(): QueryInterface
	{
		// Query to find categories with missing alias
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_categories AS a")->leftJoin("#__kunena_aliases AS c ON a.alias=c.alias")->where("c.alias IS NULL");

		return $query;
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected static function notice_categoryWrongAlias(): string
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_categories', 'a'))
			->leftJoin($db->quoteName('#__kunena_aliases', 'c') . ' ON a.alias=c.alias')
			->where("c.type!='catid'")
			->orWhere("c.item!=a.id");
		$db->setQuery((string) $query);

		$list = (array) $db->loadObjectList();

		$ids = new stdClass;

		foreach ($list as $item)
		{
			$ids->id = $item->id;
		}

		return 'Please fix the alias for category id:' . $ids->id;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_categoryMissingAlias(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.id, a.parentid, a.name, a.alias, a.description');
		}

		return ['name' => 'link', 'alias' => 'invalid', '_link' => '&view=categories&layout=edit&catid={$id}'];
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_categoryWrongAlias(): QueryInterface
	{
		// Query to find categories with wrong alias
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_categories AS a")->innerJoin("#__kunena_aliases AS c ON a.alias=c.alias")->where("c.type!='catid' OR c.item!=a.id");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_categoryWrongAlias(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.id, a.parentid, a.name, a.alias, a.description');
		}

		return ['name' => 'link', 'alias' => 'invalid', '_link' => '&view=categories&layout=edit&catid={$id}'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_aliasMissingCategory(): string
	{
		return "DELETE a FROM #__kunena_aliases AS a LEFT JOIN #__kunena_categories AS c ON a.item=c.id WHERE a.type='catid' AND c.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_aliasMissingCategory(): QueryInterface
	{
		// Query to find orphaned aliases
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_aliases AS a")->leftJoin("#__kunena_categories AS c ON a.item=c.id")->where("a.type='catid' AND c.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_aliasMissingCategory(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return ['item' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_messageBodyMissingMessage(): string
	{
		return "DELETE a FROM #__kunena_messages_text AS a LEFT JOIN #__kunena_messages AS m ON a.mesid=m.id WHERE m.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_messageBodyMissingMessage(): QueryInterface
	{
		// Query to find broken messages (orphan message text)
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_messages_text AS a")->leftJoin("#__kunena_messages AS m ON a.mesid=m.id")->where("m.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_messageBodyMissingMessage(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return ['mesid' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_messageMissingMessageBody(): string
	{
		return "DELETE a FROM #__kunena_messages AS a LEFT JOIN #__kunena_messages_text AS t ON t.mesid=a.id WHERE t.mesid IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_messageMissingMessageBody(): QueryInterface
	{
		// Query to find broken messages (message is missing body)
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_messages AS a")->leftJoin("#__kunena_messages_text AS t ON t.mesid=a.id")->where("t.mesid IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_messageMissingMessageBody(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select("a.id, a.parent, a.thread, a.catid, a.hold, a.name, a.userid, a.subject, FROM_UNIXTIME(a.time) AS time, 'MISSING' AS message");
		}

		return ['message' => 'invalid'];
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_topicInSection(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.id, a.category_id, a.hold, a.subject');
		}

		return ['category_id' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_topicInSection(): string
	{
		return "DELETE a FROM #__kunena_topics AS a LEFT JOIN #__kunena_categories AS c ON c.id=a.category_id WHERE c.parentid=0";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_topicInSection(): QueryInterface
	{
		// Query to find topics which are located in section, not in category
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_topics AS a")->innerJoin("#__kunena_categories AS c ON c.id=a.category_id")->where("c.parentid=0");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_topicMissingCategory(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.id, a.category_id, a.hold, a.subject');
		}

		return ['category_id' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_topicMissingCategory(): string
	{
		return "DELETE a FROM #__kunena_topics AS a LEFT JOIN #__kunena_categories AS c ON c.id=a.category_id WHERE c.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_topicMissingCategory(): QueryInterface
	{
		// Query to find topics which do not have existing category
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_topics AS a")->leftJoin("#__kunena_categories AS c ON c.id=a.category_id")->where("c.id IS NULL");

		return $query;
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_topicMissingMessages(): string
	{
		return "DELETE a FROM #__kunena_topics AS a LEFT JOIN #__kunena_messages AS m ON m.thread=a.id WHERE a.moved_id=0 AND m.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_topicMissingMessages(): QueryInterface
	{
		// Query to find topics without messages
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_topics AS a")->leftJoin("#__kunena_messages AS m ON m.thread=a.id")->where("a.moved_id=0 AND m.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_topicMissingMessages(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select("a.id, a.category_id, a.hold, a.subject, 'MISSING' AS messages");
		}

		return ['messages' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_topicMissingPoll(): string
	{
		return "DELETE a FROM #__kunena_topics AS a LEFT JOIN #__kunena_polls AS p ON p.id=a.poll_id WHERE a.moved_id=0 AND a.poll_id>0 AND p.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_topicMissingPoll(): QueryInterface
	{
		// Query to find topics which have missing poll
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_topics AS a")->leftJoin("#__kunena_polls AS p ON p.id=a.poll_id")->where("a.moved_id=0 AND a.poll_id>0 AND p.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_topicMissingPoll(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select("a.id, a.category_id, a.hold, a.subject, poll_id");
		}

		return ['poll_id' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_topicPollMismatch(): string
	{
		return "DELETE a FROM #__kunena_topics AS a INNER JOIN #__kunena_polls AS p ON p.id=a.poll_id LEFT JOIN #__kunena_topics AS t ON p.threadid=t.id WHERE a.moved_id=0 AND a.poll_id>0 AND p.threadid!=a.id";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_topicPollMismatch(): QueryInterface
	{
		// Query to find polls which have wrong topic
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_topics AS a")->innerJoin("#__kunena_polls AS p ON p.id=a.poll_id")->leftJoin("#__kunena_topics AS t ON p.threadid=t.id")->where("a.moved_id=0 AND a.poll_id>0 AND p.threadid!=a.id");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_topicPollMismatch(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select("a.id, a.category_id, a.hold, a.subject, p.title AS poll_title, CONCAT(a.poll_id, ' != ', p.threadid) AS poll_id, t.subject AS real_topic_subject");
		}

		return ['poll_id' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_movedMissingTopic(): string
	{
		return "DELETE a FROM #__kunena_topics AS a LEFT JOIN #__kunena_topics AS t ON t.id=a.moved_id WHERE a.moved_id>0 AND t.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_movedMissingTopic(): QueryInterface
	{
		// Query to find moved topics pointing to non-existent topic
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_topics AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.moved_id")->where("a.moved_id>0 AND t.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_movedMissingTopic(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.id, a.category_id, a.hold, a.subject, a.moved_id');
		}

		return ['moved_id' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_movedAndMessages(): string
	{
		return "DELETE a FROM #__kunena_topics AS a INNER JOIN #__kunena_messages AS m ON m.thread=a.id LEFT JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE a.moved_id>0";
	}

	/**
	 * @return  QueryInterface
	 * @since   Kunena 6.0
	 */
	protected static function query_movedAndMessages(): QueryInterface
	{
		// Query to find topics without messages
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_topics AS a")->innerJoin("#__kunena_messages AS m ON m.thread=a.id")->leftJoin("#__kunena_messages_text AS t ON m.id=t.mesid")->where("a.moved_id>0");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_movedAndMessages(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.id, a.category_id, a.hold, a.subject, m.id AS mesid, m.subject AS message_subject, t.message');
		}

		return ['mesid' => 'invalid'];
	}

	/**
	 * @return  QueryInterface
	 * @since   Kunena 6.0
	 */
	protected static function fix_messageWrongCategory(): QueryInterface
	{
		return self::query_messageWrongCategory()->update('#__kunena_messages AS a')->set('a.catid=t.category_id');
	}

	/**
	 * @return  QueryInterface
	 * @since   Kunena 6.0
	 */
	protected static function query_messageWrongCategory(): QueryInterface
	{
		// Query to find messages which have wrong category id
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_messages AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.thread")->leftJoin("#__kunena_messages_text AS mt ON a.id=mt.mesid")->where("t.category_id!=a.catid");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_messageWrongCategory(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select("a.id, a.parent, a.thread, CONCAT(a.catid, ' != ', t.category_id) AS catid, a.hold, a.name, a.userid, a.subject, FROM_UNIXTIME(a.time) AS time, mt.message");
		}

		return ['catid' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_messageOrphaned(): string
	{
		return "DELETE a FROM #__kunena_messages AS a LEFT JOIN #__kunena_topics AS t ON t.id=a.thread LEFT JOIN #__kunena_messages_text AS mt ON a.id=mt.mesid WHERE t.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_messageOrphaned(): QueryInterface
	{
		// Query to find messages which do not belong in any existing topic
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_messages AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.thread")->leftJoin("#__kunena_messages_text AS mt ON a.id=mt.mesid")->where("t.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_messageOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select("a.id, a.parent, a.thread, a.catid, a.hold, a.name, a.userid, a.subject, FROM_UNIXTIME(a.time) AS time, mt.message");
		}

		return ['thread' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_attachmentOrphaned(): string
	{
		return "DELETE a FROM #__kunena_attachments AS a LEFT JOIN #__kunena_messages AS m ON a.mesid=m.id WHERE m.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_attachmentOrphaned(): QueryInterface
	{
		// Query to find attachments which do not belong in any existing message
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_attachments AS a")->leftJoin("#__kunena_messages AS m ON a.mesid=m.id")->where("m.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_attachmentOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.id, a.mesid, a.userid, a.folder, a.filename');
		}

		return ['mesid' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_pollOrphaned(): string
	{
		return "DELETE a FROM #__kunena_polls AS a LEFT JOIN #__kunena_topics AS t ON t.id=a.threadid WHERE t.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_pollOrphaned(): QueryInterface
	{
		// Query to find polls which do not belong in any existing topic
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_polls AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.threadid")->where("t.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_pollOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return ['threadid' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_pollTopicMismatch(): string
	{
		return "DELETE a FROM #__kunena_polls AS a INNER JOIN #__kunena_topics AS t ON t.id=a.threadid LEFT JOIN #__kunena_topics AS tt ON tt.poll_id=a.id WHERE t.poll_id!=a.id";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_pollTopicMismatch(): QueryInterface
	{
		// Query to find polls which do not belong in any existing topic
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_polls AS a")->innerJoin("#__kunena_topics AS t ON t.id=a.threadid")->leftJoin("#__kunena_topics AS tt ON tt.poll_id=a.id")->where("t.poll_id!=a.id");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_pollTopicMismatch(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select("a.id, a.title, CONCAT(a.threadid, ' != ', IF(tt.id,tt.id,'0')) AS threadid, t.subject AS topic1_subject, tt.subject AS topic2_subject");
		}

		return ['threadid' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_pollOptionOrphaned(): string
	{
		return "DELETE a FROM #__kunena_polls_options AS a LEFT JOIN #__kunena_polls AS p ON p.id=a.pollid WHERE p.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_pollOptionOrphaned(): QueryInterface
	{
		// Query to find poll options which do not belong in any existing poll
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_polls_options AS a")->leftJoin("#__kunena_polls AS p ON p.id=a.pollid")->where("p.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_pollOptionOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return ['pollid' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_pollUserOrphaned(): string
	{
		return "DELETE a FROM #__kunena_polls_users AS a LEFT JOIN #__kunena_polls AS p ON p.id=a.pollid WHERE p.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_pollUserOrphaned(): QueryInterface
	{
		// Query to find poll users which do not belong in any existing poll
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_polls_users AS a")->leftJoin("#__kunena_polls AS p ON p.id=a.pollid")->where("p.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_pollUserOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return ['pollid' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_thankyouOrphaned(): string
	{
		return "DELETE a FROM #__kunena_thankyou AS a LEFT JOIN #__kunena_messages AS m ON m.id=a.postid WHERE m.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_thankyouOrphaned(): QueryInterface
	{
		// Query to find thankyous which do not belong in any existing message
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_thankyou AS a")->leftJoin("#__kunena_messages AS m ON m.id=a.postid")->where("m.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_thankyouOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return ['postid' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_userCategoryOrphaned(): string
	{
		return "DELETE a FROM #__kunena_user_categories AS a LEFT JOIN #__kunena_categories AS c ON c.id=a.category_id WHERE a.category_id>0 AND c.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_userCategoryOrphaned(): QueryInterface
	{
		// Query to find user categories which do not belong in any existing category
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_user_categories AS a")->leftJoin("#__kunena_categories AS c ON c.id=a.category_id")->where("a.category_id>0 AND c.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_userCategoryOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return ['category_id' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_userReadOrphaned(): string
	{
		return "DELETE a FROM #__kunena_user_read AS a LEFT JOIN #__kunena_topics AS t ON t.id=a.topic_id WHERE t.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_userReadOrphaned(): QueryInterface
	{
		// Query to find user read which do not belong in any existing topic
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_user_read AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("t.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_userReadOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return ['topic_id' => 'invalid'];
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fix_userReadWrongCategory(): QueryInterface
	{
		return self::query_userReadWrongCategory()->update('#__kunena_user_read AS a')->set('a.category_id=t.category_id');
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_userReadWrongCategory(): QueryInterface
	{
		// Query to find user read which wrong category information
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_user_read AS a")->innerJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("a.category_id!=t.category_id");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_userReadWrongCategory(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select("a.user_id, a.topic_id, CONCAT(a.category_id, ' != ', t.category_id) AS category_id, a.message_id, FROM_UNIXTIME(a.time) AS time");
		}

		return ['category_id' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_userTopicOrphaned(): string
	{
		return "DELETE a FROM #__kunena_user_topics AS a LEFT JOIN #__kunena_topics AS t ON t.id=a.topic_id WHERE t.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_userTopicOrphaned(): QueryInterface
	{
		// Query to find user topics which do not belong in any existing topic
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_user_topics AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("t.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_userTopicOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('a.user_id, a.topic_id, a.category_id, a.posts, a.last_post_id, a.owner, a.favorite, a.subscribed');
		}

		return ['topic_id' => 'invalid'];
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fix_userTopicWrongCategory(): QueryInterface
	{
		return self::query_userTopicWrongCategory()->update('#__kunena_user_topics AS a')->set('a.category_id=t.category_id');
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_userTopicWrongCategory(): QueryInterface
	{
		// Query to find user topic which wrong category information
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_user_topics AS a")->innerJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("a.category_id!=t.category_id");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_userTopicWrongCategory(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select("a.user_id, a.topic_id, CONCAT(a.category_id, ' != ', t.category_id) AS category_id, a.posts, a.last_post_id, a.owner, a.favorite, a.subscribed");
		}

		return ['category_id' => 'invalid'];
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected static function delete_ratingOrphaned(): string
	{
		return "DELETE r FROM #__kunena_rate AS r LEFT JOIN #__kunena_topics AS t ON t.id=r.topic_id WHERE t.id IS NULL";
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_ratingOrphaned(): QueryInterface
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);

		// Query to find orphaned ratings
		$query->from("#__kunena_rate AS r")->leftJoin("#__kunena_topics AS t ON t.id=r.topic_id")->where("t.id IS NULL");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_ratingOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('r.*');
		}

		return ['topic_id' => 'invalid'];
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fix_channelOrphaned(): QueryInterface
	{
		return self::query_channelOrphaned()->update('#__kunena_categories')->set("channels='THIS'")->where("channels='none' OR channels=NULL");
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_channelOrphaned(): QueryInterface
	{
		// Query to find user read which do not belong in any existing topic
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_categories")->where("channels IS NULL OR 'none'");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_channelOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('*');
		}

		return ['channels' => 'invalid'];
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_ownerOrphaned(): QueryInterface
	{
		// Query to find user read which do not belong in any existing topic
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_topics AS t")->leftJoin("#__kunena_user_topics AS j ON j.topic_id=t.id")->where("t.first_post_userid > 0");

		return $query;
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fix_ownerOrphaned(): QueryInterface
	{
		return self::query_channelOrphaned()->update('#__kunena_categories')->set("channels='THIS'")->where("channels='none' OR channels=NULL");
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_ownerOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('t.id, t.first_post_userid, 1');
		}

		return ['channels' => 'invalid'];
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fix_userAvatarOrphaned(): QueryInterface
	{
		return self::query_useravatarOrphaned()->update('#__kunena_users')->set("avatar=NULL")->where("avatar=''");
	}

	/**
	 * @return  QueryInterface
	 *
	 * @since   Kunena 6.0
	 */
	protected static function query_userAvatarOrphaned(): QueryInterface
	{
		// Query to find user read which do not belong in any existing topic
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->from("#__kunena_users")->where("avatar=''");

		return $query;
	}

	/**
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected static function fields_userAvatarOrphaned(QueryInterface $query = null): array
	{
		if ($query)
		{
			$query->select('*');
		}

		return ['channels' => 'invalid'];
	}

	//  /**
	//   * @return QueryInterface
	//   * @since   Kunena
	//   */
	//  protected static function query_topicsOwnersOrphaned()
	//  {
	//      // Query to find user read which do not belong in any existing topic
	//      $db    = Factory::getContainer()->get('DatabaseDriver');
	//      $query = $db->getQuery(true);
	//      $query->from("#__kunena_users");
	//
	//      return $query;
	//  }
	//
	//  /**
	//   * @param   QueryInterface  $query  query
	//   *
	//   * @return  array
	//   * @since   Kunena
	//   */
	//  protected static function fields_topicsOwnersOrphaned(QueryInterface $query = null)
	//  {
	//      if ($query)
	//      {
	//          $query->select('*');
	//      }
	//
	//      return array('channels' => 'invalid');
	//  }
	//
	//  /**
	//   * @return QueryInterface
	//   * @since   Kunena
	//   */
	//  protected static function fix_topicsOwnersOrphaned()
	//  {
	//      $query = self::query_topicsOwnersOrphaned()->insert('#__kunena_user_topics');
	//
	//      return $query;
	//  }
}
/*
-- Find and update topics without owners:
INSERT INTO `#__kunena_user_topics` (topic_id, user_id, owner)
(SELECT t.id, t.first_post_userid, 1
FROM #__kunena_topics AS t
INNER JOIN (SELECT topic_id, MAX(owner) AS owner FROM `#__kunena_user_topics` GROUP BY topic_id HAVING owner=0) AS j ON j.topic_id=t.id
WHERE t.first_post_userid>0)
ON DUPLICATE KEY UPDATE owner=1
*/
