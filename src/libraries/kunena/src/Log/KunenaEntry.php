<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Libraries
 * @subpackage    Log
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Log;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * implements \Kunena log entry.
 *
 * @since 5.0
 */
class KunenaEntry
{
	/**
	 * @var     array
	 * @since   Kunena 5.0
	 */
	public $data;

	/**
	 * @param   mixed                $type       type
	 * @param   mixed                $operation  operation
	 * @param   mixed                $data       data
	 * @param   KunenaCategory|null  $category   category
	 * @param   KunenaTopic|null     $topic      topic
	 * @param   KunenaUser|null      $user       user
	 *
	 * @since   Kunena 5.0
	 *
	 * @throws Exception
	 */
	public function __construct(
		$type,
		$operation,
		$data,
		KunenaCategory $category = null,
		KunenaTopic $topic = null,
		KunenaUser $user = null
	)
	{
		$now = new Date;

		$this->data = [
			'type'        => (int) $type,
			'user_id'     => KunenaUserHelper::getMyself()->userid,
			'category_id' => $category ? $category->id : 0,
			'topic_id'    => $topic ? $topic->id : 0,
			'target_user' => $user ? $user->userid : 0,
			'ip'          => Factory::getApplication()->isClient('site') && KunenaUserHelper::getUserIp() !== null ? KunenaUserHelper::getUserIp() : '',
			'time'        => $now->toUnix(),
			'operation'   => $operation,
			'data'        => json_encode($data),
		];
	}

	/**
	 * Get all the data.
	 *
	 * @return  array
	 *
	 * @since   Kunena 5.0
	 */
	public function getData(): array
	{
		return $this->data;
	}
}
