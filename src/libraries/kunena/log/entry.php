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
 * Implements Kunena log entry.
 *
 * @since 5.0
 */
class KunenaLogEntry
{
	public $data;

	/**
	 * @param                     $type
	 * @param                     $operation
	 * @param                     $data
	 * @param KunenaForumCategory $category
	 * @param KunenaForumTopic    $topic
	 * @param KunenaUser          $user
	 */
	public function __construct(
		$type,
		$operation,
		$data,
		KunenaForumCategory $category = null,
		KunenaForumTopic $topic = null,
		KunenaUser $user = null
	)
	{
		$now = new JDate;

		$this->data = array(
			'type' => (int) $type,
			'user_id' => KunenaUserHelper::getMyself()->userid,
			'category_id' => $category ? $category->id : 0,
			'topic_id' => $topic ? $topic->id : 0,
			'target_user' => $user ? $user->userid : 0,
			'ip' => JFactory::getApplication()->isSite() && isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
			'time' => $now->toUnix(),
			'operation' => $operation,
			'data' => json_encode($data)
		);
	}

	/**
	 * Get all the data.
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}
}
