<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Private
 *
 * @copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\KunenaPrivate\Message;

\defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\KunenaPrivate\KunenaPrivateMessage;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Private message finder.
 *
 * @since   Kunena 6.0
 */
class KunenaFinder extends \Kunena\Forum\Libraries\Database\Object\KunenaFinder
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $table = '#__kunena_private';

	/**
	 * @param   KunenaUser  $user  user object
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByUser(KunenaUser $user): KunenaFinder
	{
		if (!$user->userid)
		{
			$this->skip = true;
		}
		else
		{
			$this->query->innerJoin('#__kunena_private_user_map AS um ON a.id=um.private_id');
			$this->query->where("um.user_id = {$this->db->quote($user->userid)}");
		}

		return $this;
	}

	/**
	 * @param   KunenaMessage  $message  message object
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByMessage(KunenaMessage $message): KunenaFinder
	{
		if (!$message->id)
		{
			$this->skip = true;
		}
		else
		{
			$this->query->innerJoin('#__kunena_private_post_map AS pm ON a.id=pm.private_id');
			$this->query->where("pm.message_id = {$this->db->quote($message->id)}");
		}

		return $this;
	}

	/**
	 * @param   array  $ids  ids
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByMessageIds(array $ids): KunenaFinder
	{
		if (empty($ids))
		{
			$this->skip = true;
		}
		else
		{
			$this->query->innerJoin('#__kunena_private_post_map AS pm ON a.id=pm.private_id');
			$this->query->where("pm.message_id IN (" . implode(',', $ids) . ")");
		}

		return $this;
	}

	/**
	 * @return \Kunena\Forum\Libraries\KunenaPrivate\KunenaPrivateMessage
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws \Exception
	 */
	public function firstOrNew(): KunenaPrivateMessage
	{
		$results = $this->find();
		$first   = array_pop($results);

		return $first ?: new KunenaPrivateMessage;
	}

	/**
	 * Get private messages.
	 *
	 * @return array
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 */
	public function find(): array
	{
		return $this->load(parent::find());
	}

	/**
	 * @param   array  $ids  ids
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function load(array $ids): array
	{
		if (empty($ids))
		{
			return [];
		}

		$query = $this->db->getQuery(true);
		$query->select('*')->from('#__kunena_private')->where('id IN(' . implode(',', $ids) . ')');
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList('id');

		$_instances = [];

		foreach ($results as $id => $instance)
		{
			$pm_instance = new KunenaPrivateMessage;
			$pm_instance->load($id);
			$pm_instance->id  = $id;
			$_instances [$id] = $pm_instance;
		}

		return $_instances;
	}
}
