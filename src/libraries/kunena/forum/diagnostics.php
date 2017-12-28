<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaForumDiagnostics
 */
abstract class KunenaForumDiagnostics
{
	/**
	 * @return array
	 */
	public static function getList()
	{
		return array(
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
			'channelOrphaned'
		);
	}

	/**
	 * @param   string $function
	 *
	 * @return integer
	 */
	public static function count($function)
	{
		$function = 'query_' . $function;

		if (method_exists(__CLASS__, $function))
		{
			$query = self::$function();
			// @var KunenaDatabaseQuery $query

			$query->select("COUNT(*)");
			$db = JFactory::getDbo();
			$db->setQuery($query);

			return (int) $db->loadResult();
		}

		return 0;
	}

	/**
	 * @param   string $function
	 *
	 * @return array
	 */
	public static function getItems($function)
	{
		$queryFunction = 'query_' . $function;

		if (method_exists(__CLASS__, $queryFunction))
		{
			$query = self::$queryFunction();
			$fieldsFunction = 'fields_' . $function;

			if (!method_exists(__CLASS__, $fieldsFunction))
			{
				$fieldsFunction = 'fields';
			}

			self::$fieldsFunction($query);
			$db = JFactory::getDbo();
			$db->setQuery($query);

			return (array) $db->loadAssocList();
		}

		return array();
	}

	/**
	 * @param   string $function
	 *
	 * @return boolean
	 */
	public static function fix($function)
	{
		$queryFunction = 'fix_' . $function;

		if (method_exists(__CLASS__, $queryFunction))
		{
			$query = self::$queryFunction();
			$db = JFactory::getDbo();
			$db->setQuery($query);

			return (bool) $db->execute();
		}

		return false;
	}

	/**
	 * @param   string $function
	 *
	 * @return boolean
	 */
	public static function canFix($function)
	{
		$queryFunction = 'fix_' . $function;

		if (method_exists(__CLASS__, $queryFunction))
		{
			return true;
		}

		return false;
	}

	/**
	 * @param   string $function
	 *
	 * @return boolean
	 */
	public static function delete($function)
	{
		$queryFunction = 'delete_' . $function;

		if (method_exists(__CLASS__, $queryFunction))
		{
			$query = self::$queryFunction();
			$db = JFactory::getDbo();
			$db->setQuery($query);

			return (bool) $db->execute();
		}

		return false;
	}

	/**
	 * @param   string $function
	 *
	 * @return boolean
	 */
	public static function canDelete($function)
	{
		$queryFunction = 'delete_' . $function;

		if (method_exists(__CLASS__, $queryFunction))
		{
			return true;
		}

		return false;
	}

	/**
	 * @param $function
	 *
	 * @return array
	 */
	public static function getFieldInfo($function)
	{
		static $fields = array();

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
	 * @param   string $function
	 *
	 * @return string|null
	 */
	public static function getQuery($function)
	{
		$function = 'query_' . $function;

		if (method_exists(__CLASS__, $function))
		{
			$query = self::$function();
			// @var KunenaDatabaseQuery $query

			$query->select("COUNT(*)");
			return (string) $query;
		}

		return null;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select("a.*");
		}

		return array();
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_categoryOrphaned()
	{
		// Query to find orphaned categories
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_categories AS a")->leftJoin("#__kunena_categories AS c ON a.parent_id=c.id")->where("a.parent_id>0 AND c.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_categoryOrphaned()
	{
		$query = self::query_categoryOrphaned()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_categoryOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.id, a.parent_id, a.name, a.alias, a.description');
		}

		return array('name' => 'link', 'parent_id' => 'invalid', '_link' => '&view=categories&layout=edit&catid={$id}');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_categoryMissingAlias()
	{
		// Query to find categories with missing alias
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_categories AS a")->leftJoin("#__kunena_aliases AS c ON a.alias=c.alias")->where("c.alias IS NULL");

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_categoryMissingAlias(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.id, a.parent_id, a.name, a.alias, a.description');
		}

		return array('name' => 'link', 'alias' => 'invalid', '_link' => '&view=categories&layout=edit&catid={$id}');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_categoryWrongAlias()
	{
		// Query to find categories with wrong alias
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_categories AS a")->innerJoin("#__kunena_aliases AS c ON a.alias=c.alias")->where("c.type!='catid' OR c.item!=a.id");

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_categoryWrongAlias(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.id, a.parent_id, a.name, a.alias, a.description');
		}

		return array('name' => 'link', 'alias' => 'invalid', '_link' => '&view=categories&layout=edit&catid={$id}');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_aliasMissingCategory()
	{
		// Query to find orphaned aliases
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_aliases AS a")->leftJoin("#__kunena_categories AS c ON a.item=c.id")->where("a.type='catid' AND c.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_aliasMissingCategory()
	{
		$query = self::query_aliasMissingCategory()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_aliasMissingCategory(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return array('item' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_messageBodyMissingMessage()
	{
		// Query to find broken messages (orphan message text)
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_messages_text AS a")->leftJoin("#__kunena_messages AS m ON a.mesid=m.id")->where("m.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_messageBodyMissingMessage()
	{
		$query = self::query_messageBodyMissingMessage()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_messageBodyMissingMessage(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return array('mesid' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_messageMissingMessageBody()
	{
		// Query to find broken messages (message is missing body)
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_messages AS a")->leftJoin("#__kunena_messages_text AS t ON t.mesid=a.id")->where("t.mesid IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_messageMissingMessageBody()
	{
		$query = self::query_messageMissingMessageBody()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_messageMissingMessageBody(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select("a.id, a.parent, a.thread, a.catid, a.hold, a.name, a.userid, a.subject, FROM_UNIXTIME(a.time) AS time, 'MISSING' AS message");
		}

		return array('message' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_topicInSection()
	{
		// Query to find topics which are located in section, not in category
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_topics AS a")->innerJoin("#__kunena_categories AS c ON c.id=a.category_id")->where("c.parent_id=0");

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_topicInSection(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.id, a.category_id, a.hold, a.subject');
		}

		return array('category_id' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_topicInSection()
	{
		$query = self::query_topicInSection()->delete('a');

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_topicMissingCategory()
	{
		// Query to find topics which do not have existing category
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_topics AS a")->leftJoin("#__kunena_categories AS c ON c.id=a.category_id")->where("c.id IS NULL");

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_topicMissingCategory(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.id, a.category_id, a.hold, a.subject');
		}

		return array('category_id' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_topicMissingCategory()
	{
		$query = self::query_topicMissingCategory()->delete('a');

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_topicMissingMessages()
	{
		// Query to find topics without messages
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_topics AS a")->leftJoin("#__kunena_messages AS m ON m.thread=a.id")->where("a.moved_id=0 AND m.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_topicMissingMessages()
	{
		$query = self::query_topicMissingMessages()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_topicMissingMessages(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select("a.id, a.category_id, a.hold, a.subject, 'MISSING' AS messages");
		}

		return array('messages' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_topicMissingPoll()
	{
		// Query to find topics which have missing poll
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_topics AS a")->leftJoin("#__kunena_polls AS p ON p.id=a.poll_id")->where("a.moved_id=0 AND a.poll_id>0 AND p.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_topicMissingPoll()
	{
		$query = self::query_topicMissingPoll()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_topicMissingPoll(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select("a.id, a.category_id, a.hold, a.subject, poll_id");
		}

		return array('poll_id' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_topicPollMismatch()
	{
		// Query to find polls which have wrong topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_topics AS a")->innerJoin("#__kunena_polls AS p ON p.id=a.poll_id")->leftJoin("#__kunena_topics AS t ON p.threadid=t.id")->where("a.moved_id=0 AND a.poll_id>0 AND p.threadid!=a.id");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_topicPollMismatch()
	{
		$query = self::query_topicPollMismatch()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_topicPollMismatch(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select("a.id, a.category_id, a.hold, a.subject, p.title AS poll_title, CONCAT(a.poll_id, ' != ', p.threadid) AS poll_id, t.subject AS real_topic_subject");
		}

		return array('poll_id' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_movedMissingTopic()
	{
		// Query to find moved topics pointing to non-existent topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_topics AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.moved_id")->where("a.moved_id>0 AND t.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_movedMissingTopic()
	{
		$query = self::query_movedMissingTopic()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_movedMissingTopic(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.id, a.category_id, a.hold, a.subject, a.moved_id');
		}

		return array('moved_id' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_movedAndMessages()
	{
		// Query to find topics without messages
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_topics AS a")->innerJoin("#__kunena_messages AS m ON m.thread=a.id")->leftJoin("#__kunena_messages_text AS t ON m.id=t.mesid")->where("a.moved_id>0");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_movedAndMessages()
	{
		$query = self::query_movedAndMessages()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_movedAndMessages(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.id, a.category_id, a.hold, a.subject, m.id AS mesid, m.subject AS message_subject, t.message');
		}

		return array('mesid' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_messageWrongCategory()
	{
		// Query to find messages which have wrong category id
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_messages AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.thread")->leftJoin("#__kunena_messages_text AS mt ON a.id=mt.mesid")->where("t.category_id!=a.catid");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function fix_messageWrongCategory()
	{
		$query = self::query_messageWrongCategory()->update('#__kunena_messages AS a')->set('a.catid=t.category_id');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_messageWrongCategory(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select("a.id, a.parent, a.thread, CONCAT(a.catid, ' != ', t.category_id) AS catid, a.hold, a.name, a.userid, a.subject, FROM_UNIXTIME(a.time) AS time, mt.message");
		}

		return array('catid' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_messageOrphaned()
	{
		// Query to find messages which do not belong in any existing topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_messages AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.thread")->leftJoin("#__kunena_messages_text AS mt ON a.id=mt.mesid")->where("t.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_messageOrphaned()
	{
		$query = self::query_messageOrphaned()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_messageOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select("a.id, a.parent, a.thread, a.catid, a.hold, a.name, a.userid, a.subject, FROM_UNIXTIME(a.time) AS time, mt.message");
		}

		return array('thread' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_attachmentOrphaned()
	{
		// Query to find attachments which do not belong in any existing message
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_attachments AS a")->leftJoin("#__kunena_messages AS m ON a.mesid=m.id")->where("m.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_attachmentOrphaned()
	{
		$query = self::query_attachmentOrphaned()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_attachmentOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.id, a.mesid, a.userid, a.folder, a.filename');
		}

		return array('mesid' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_pollOrphaned()
	{
		// Query to find polls which do not belong in any existing topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_polls AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.threadid")->where("t.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_pollOrphaned()
	{
		$query = self::query_pollOrphaned()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_pollOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return array('threadid' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_pollTopicMismatch()
	{
		// Query to find polls which do not belong in any existing topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_polls AS a")->innerJoin("#__kunena_topics AS t ON t.id=a.threadid")->leftJoin("#__kunena_topics AS tt ON tt.poll_id=a.id")->where("t.poll_id!=a.id");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_pollTopicMismatch()
	{
		$query = self::query_pollTopicMismatch()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_pollTopicMismatch(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select("a.id, a.title, CONCAT(a.threadid, ' != ', IF(tt.id,tt.id,'0')) AS threadid, t.subject AS topic1_subject, tt.subject AS topic2_subject");
		}

		return array('threadid' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_pollOptionOrphaned()
	{
		// Query to find poll options which do not belong in any existing poll
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_polls_options AS a")->leftJoin("#__kunena_polls AS p ON p.id=a.pollid")->where("p.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_pollOptionOrphaned()
	{
		$query = self::query_pollOptionOrphaned()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_pollOptionOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return array('pollid' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_pollUserOrphaned()
	{
		// Query to find poll users which do not belong in any existing poll
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_polls_users AS a")->leftJoin("#__kunena_polls AS p ON p.id=a.pollid")->where("p.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_pollUserOrphaned()
	{
		$query = self::query_pollUserOrphaned()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_pollUserOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return array('pollid' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_thankyouOrphaned()
	{
		// Query to find thankyous which do not belong in any existing message
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_thankyou AS a")->leftJoin("#__kunena_messages AS m ON m.id=a.postid")->where("m.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_thankyouOrphaned()
	{
		$query = self::query_thankyouOrphaned()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_thankyouOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return array('postid' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_userCategoryOrphaned()
	{
		// Query to find user categories which do not belong in any existing category
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_user_categories AS a")->leftJoin("#__kunena_categories AS c ON c.id=a.category_id")->where("a.category_id>0 AND c.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_userCategoryOrphaned()
	{
		$query = self::query_userCategoryOrphaned()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_userCategoryOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return array('category_id' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_userReadOrphaned()
	{
		// Query to find user read which do not belong in any existing topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_user_read AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("t.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_userReadOrphaned()
	{
		$query = self::query_userReadOrphaned()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_userReadOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.*');
		}

		return array('topic_id' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_userReadWrongCategory()
	{
		// Query to find user read which wrong category information
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_user_read AS a")->innerJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("a.category_id!=t.category_id");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function fix_userReadWrongCategory()
	{
		$query = self::query_userReadWrongCategory()->update('#__kunena_user_read AS a')->set('a.category_id=t.category_id');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_userReadWrongCategory(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select("a.user_id, a.topic_id, CONCAT(a.category_id, ' != ', t.category_id) AS category_id, a.message_id, FROM_UNIXTIME(a.time) AS time");
		}

		return array('category_id' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_userTopicOrphaned()
	{
		// Query to find user topics which do not belong in any existing topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_user_topics AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("t.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_userTopicOrphaned()
	{
		$query = self::query_userTopicOrphaned()->delete('a');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_userTopicOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('a.user_id, a.topic_id, a.category_id, a.posts, a.last_post_id, a.owner, a.favorite, a.subscribed');
		}

		return array('topic_id' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_userTopicWrongCategory()
	{
		// Query to find user topic which wrong category information
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_user_topics AS a")->innerJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("a.category_id!=t.category_id");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function fix_userTopicWrongCategory()
	{
		$query = self::query_userTopicWrongCategory()->update('#__kunena_user_topics AS a')->set('a.category_id=t.category_id');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_userTopicWrongCategory(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select("a.user_id, a.topic_id, CONCAT(a.category_id, ' != ', t.category_id) AS category_id, a.posts, a.last_post_id, a.owner, a.favorite, a.subscribed");
		}

		return array('category_id' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_ratingOrphaned()
	{
		// Query to find orphaned ratings
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_rate AS r")->leftJoin("#__kunena_topics AS t ON t.id=r.topic_id")->where("t.id IS NULL");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function delete_ratingOrphaned()
	{
		$query = self::query_ratingOrphaned()->delete('r');

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_ratingOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('r.*');
		}

		return array('topic_id' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_channelOrphaned()
	{
		// Query to find orphaned categories channels
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_categories")->where("channels IS NULL OR 'none'");

		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function fix_channelOrphaned()
	{
		$query = self::query_channelOrphaned()->update('#__kunena_categories')->set("channels='THIS'")->where("channels='none' OR channels=NULL");

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_channelOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('*');
		}

		return array('channels' => 'invalid');
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function query_ownerOrphaned()
	{
		// Query to find user read which do not belong in any existing topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_topics AS t")->leftJoin("#__kunena_user_topics AS j ON j.topic_id=t.id")->where("t.first_post_userid > 0");
		return $query;
	}

	/**
	 * @return KunenaDatabaseQuery
	 */
	protected static function fix_ownerOrphaned()
	{
		$query = self::query_channelOrphaned()->update('#__kunena_categories')->set("channels='THIS'")->where("channels='none' OR channels=NULL");

		return $query;
	}

	/**
	 * @param   KunenaDatabaseQuery $query
	 *
	 * @return array
	 */
	protected static function fields_ownerOrphaned(KunenaDatabaseQuery $query = null)
	{
		if ($query)
		{
			$query->select('t.id, t.first_post_userid, 1');
		}

		return array('channels' => 'invalid');
	}
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
