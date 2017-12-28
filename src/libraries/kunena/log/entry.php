<?php
/**
 * Kunena Component
 * @package       Kunena.Libraries
 * @subpackage    Log
 *
 * @copyright     Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Implements Kunena log entry.
 *
 * @since 5.0
 */
class KunenaLogEntry
{
	/**
	 * @var array
	 * @since Kunena
	 */
	public $data;

	/**
	 * @param   mixed               $type      type
	 * @param   mixed               $operation operation
	 * @param   mixed               $data      data
	 * @param   KunenaForumCategory $category  category
	 * @param   KunenaForumTopic    $topic     topic
	 * @param   KunenaUser          $user      user
	 *
	 * @throws Exception
	 * @since Kunena
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
		$now = new \Joomla\CMS\Date\Date;

		$this->data = array(
			'type'        => (int) $type,
			'user_id'     => KunenaUserHelper::getMyself()->userid,
			'category_id' => $category ? $category->id : 0,
			'topic_id'    => $topic ? $topic->id : 0,
			'target_user' => $user ? $user->userid : 0,
			'ip'          => \Joomla\CMS\Factory::getApplication()->isClient('site') && isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
			'time'        => $now->toUnix(),
			'operation'   => $operation,
			'data'        => json_encode($data),
		);
	}

	/**
	 * Get all the data.
	 *
	 * @return array
	 * @since Kunena
	 */
	public function getData()
	{
		return $this->data;
	}
}
