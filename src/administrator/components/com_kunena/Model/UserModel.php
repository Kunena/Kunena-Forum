<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Access\Access;
use Kunena\Forum\Libraries\Forum\Category\Category;
use Kunena\Forum\Libraries\Forum\Category\Helper;
use Kunena\Forum\Libraries\Forum\Topic\Topic;
use Kunena\Forum\Libraries\Model\Model;
use Kunena\Forum\Libraries\User\KunenaUser;
use RuntimeException;
use function defined;

/**
 * User Model for Kunena
 *
 * @since  3.0
 */
class UserModel extends Model
{
	/**
	 * @inheritDoc
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// TODO: Implement getForm() method.
	}

	/**
	 * @return  array|Topic[]|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function getSubscriptions()
	{
		$db     = Factory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$query = $db->getQuery(true);
		$query->select('topic_id AS thread')
			->from($db->quoteName('#__kunena_user_topics'))
			->where('user_id = ' . $userid . ' AND subscribed=1');
		$db->setQuery($query);

		try
		{
			$subslist = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		$topic_list = [];

		if (!empty($subslist))
		{
			foreach ($subslist as $sub)
			{
				$topic_list[] = $sub->thread;
			}

			$topic_list = \Kunena\Forum\Libraries\Forum\Topic\Helper::getTopics($topic_list);
		}

		return $topic_list;
	}

	/**
	 * @return  Category[]
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getCatsubcriptions()
	{
		$userid = $this->getState($this->getName() . '.id');

		$subscatslist = Helper::getSubscriptions($userid);

		return $subscatslist;
	}

	/**
	 * @return  array|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getIPlist()
	{
		$db     = Factory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$query = $db->getQuery(true);
		$query->select('ip')
			->from($db->quoteName('#__kunena_messages'))
			->where('userid = ' . $userid)
			->group('ip');
		$db->setQuery($query);

		try
		{
			$iplist = implode("','", (array) $db->loadColumn());
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		$list = [];

		if ($iplist)
		{
			$iplist = "'{$iplist}'";
			$query  = $db->getQuery(true);
			$query->select('m.ip,m.userid,u.username,COUNT(*) as mescnt')
				->from($db->quoteName('#__kunena_messages', 'm'))
				->innerJoin($db->quoteName('#__users', 'u') . ' ON m.userid = u.id')
				->where('m.ip IN (' . $iplist . ')')
				->group('m.userid,m.ip');
			$db->setQuery($query);

			try
			{
				$list = (array) $db->loadObjectlist();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage());

				return;
			}
		}

		$useripslist = [];

		foreach ($list as $item)
		{
			$useripslist[$item->ip][] = $item;
		}

		return $useripslist;
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getListmodcats()
	{
		$user = $this->getUser();

		$modCatList = array_keys(Access::getInstance()->getModeratorStatus($user));

		if (empty($modCatList))
		{
			$modCatList[] = 0;
		}

		$categoryList = [];

		if ($this->me->isAdmin())
		{
			$categoryList[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_GLOBAL_MODERATOR'));
		}

		// Todo: fix params
		$params  = [
			'sections' => false,
			'action'   => 'read'];
		$modCats = HTMLHelper::_('select.genericlist', $categoryList, 'catid', 'class="inputbox form-control" multiple="multiple" size="15"', 'value', 'text');

		return $modCats;
	}

	/**
	 * @return  KunenaUser
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getUser()
	{
		$userid = $this->getState($this->getName() . '.id');

		$user = \Kunena\Forum\Libraries\User\Helper::get($userid);

		return $user;
	}

	/**
	 * @return  array|mixed|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getListuserranks()
	{
		$db   = Factory::getDBO();
		$user = $this->getUser();

		// Grab all special ranks
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_ranks'))
			->where('rank_special = \'1\'');
		$db->setQuery($query);

		try
		{
			$specialRanks = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		$yesnoRank [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_RANK_NO_ASSIGNED'));

		foreach ($specialRanks as $ranks)
		{
			$yesnoRank [] = HTMLHelper::_('select.option', $ranks->rank_id, $ranks->rank_title);
		}

		// Build special ranks select list
		$selectRank = HTMLHelper::_('select.genericlist', $yesnoRank, 'newrank', 'class="inputbox form-control" size="5"', 'value', 'text', $user->rank);

		return $selectRank;
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getMovecatslist()
	{
		return HTMLHelper::_('select.genericlist', '', 'catid', 'class="inputbox form-control"', 'value', 'text');
	}

	/**
	 * @return  array|string|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getMoveuser()
	{
		$db = Factory::getDBO();

		$userids = (array) $this->app->getUserState('kunena.usermove.userids');

		if (!$userids)
		{
			return $userids;
		}

		$userids = implode(',', $userids);
		$query   = $db->getQuery(true);
		$query->select('id,username')
			->from($db->quoteName('#__users'))
			->where('id IN(' . $userids . ')');
		$db->setQuery($query);

		try
		{
			$userids = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		return $userids;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$this->context = 'com_kunena.admin.user';

		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		$layout        = $app->input->get('layout');
		$this->context = 'com_kunena.admin.user';

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$value = Factory::getApplication()->input->getInt('userid');
		$this->setState($this->getName() . '.id', $value);
	}
}
