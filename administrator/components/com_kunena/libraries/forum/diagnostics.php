<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

abstract class KunenaForumDiagnostics {
	public static function getList() {
		return array(
			'messagetextsMissingMessage',
			'messagesMissingBody',
			'topicsInSection',
			'topicsMissingCategory',
			'messagesInWrongCategory',
			'messagesOrphaned',
			'pollsOrphaned',
			'pollOptionsOrphaned',
			'pollUsersOrphaned',
		);
	}

	public static function count($function) {
		if (method_exists(__CLASS__, $function)) {
			$query = self::$function();
			$query->select("COUNT(*)");
			$db = JFactory::getDbo();
			$db->setQuery($query);
			return $db->loadResult();
		}
	}

	public static function getQuery($function) {
		if (method_exists(__CLASS__, $function)) {
			$query = self::$function();
			$query->select("COUNT(*)");
			return (string) $query;
		}
	}

	protected static function messagetextsMissingMessage() {
		// Query to find broken messages (orphan message text)
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_messages_text AS a")->leftJoin("#__kunena_messages AS m ON a.mesid=m.id")->where("m.id IS NULL");
		return $query;
	}
	protected static function messagesMissingBody() {
		// Query to find broken messages (message is missing body)
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_messages AS a")->leftJoin("#__kunena_messages_text AS t ON t.mesid=a.id")->where("t.mesid IS NULL");
		return $query;
	}
	protected static function topicsInSection() {
		// Query to find topics which are located in section, not in category
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_topics AS a")->innerJoin("#__kunena_categories AS c ON c.id=a.category_id")->where("c.parent_id=0");
		return $query;
	}
	protected static function topicsMissingCategory() {
		// Query to find topics which do not have existing category
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_topics AS a")->leftJoin("#__kunena_categories AS c ON c.id=a.category_id")->where("c.id IS NULL");
		return $query;
	}
	protected static function messagesInWrongCategory() {
		// Query to find messages which have wrong category id
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_messages AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.thread")->where("t.category_id!=a.catid");
		return $query;
	}
	protected static function messagesOrphaned() {
		// Query to find messages which do not belong in any existing topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_messages AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.thread")->where("t.id IS NULL");
		return $query;
	}
	protected static function pollsOrphaned() {
		// Query to find messages which do not belong in any existing topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_polls AS a")->leftJoin("#__kunena_topics AS t ON t.poll_id=a.id")->where("t.id IS NULL");
		return $query;
	}
	protected static function pollOptionsOrphaned() {
		// Query to find messages which do not belong in any existing topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_polls_options AS a")->leftJoin("#__kunena_polls AS p ON p.id=a.pollid")->where("p.id IS NULL");
		return $query;
	}
	protected static function pollUsersOrphaned() {
		// Query to find messages which do not belong in any existing topic
		$query = new KunenaDatabaseQuery();
		$query->from("#__kunena_polls_users AS a")->leftJoin("#__kunena_polls AS p ON p.id=a.pollid")->where("p.id IS NULL");
		return $query;
	}
}
/*
-- Find orphan attachments (to delete them):
SELECT a.* FROM jos_kunena_attachments AS a LEFT JOIN jos_kunena_messages AS m ON m.id=a.mesid WHERE m.id IS NULL

-- Delete orphan favorites/subscriptions:
DELETE f FROM jos_kunena_favorites AS f LEFT JOIN jos_kunena_messages AS m ON m.id=f.thread LEFT JOIN jos_users AS u ON u.id=f.userid WHERE m.id IS NULL OR u.id IS NULL
DELETE s FROM jos_kunena_subscriptions AS s LEFT JOIN jos_kunena_messages AS m ON m.id=s.thread LEFT JOIN jos_users AS u ON u.id=s.userid WHERE m.id IS NULL OR u.id IS NULL

-- Fix category channels (category selection bug):
UPDATE jos_kunena_categories SET channels='THIS' WHERE channels='none' OR channels=NULL
*/