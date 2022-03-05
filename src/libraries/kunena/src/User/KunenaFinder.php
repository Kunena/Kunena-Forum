<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\User;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Kunena\Forum\Libraries\Config\KunenaConfig;

/**
 * Class \Kunena\Forum\Libraries\User\KunenaUserFinder
 *
 * @since   Kunena 6.0
 */
class KunenaFinder extends \Kunena\Forum\Libraries\Database\Object\KunenaFinder
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $table = '#__users';

	/**
	 * @var     KunenaConfig|mixed
	 * @since   Kunena 6.0
	 */
	protected $config;

	/**
	 * Constructor.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function __construct()
	{
		parent::__construct();

		$this->config = KunenaConfig::getInstance();
		$this->limit  = $this->config->userlistRows;

		$this->query->leftJoin($this->db->quoteName('#__kunena_users', 'ku') . ' ON ' . $this->db->quoteName('ku.userid') . ' = ' . $this->db->quoteName('a.id'));
	}

	/**
	 * Filter by time, either on registration or last visit date.
	 *
	 * @param   Date|null  $starting  Starting date or null if older than ending date.
	 * @param   Date|null  $ending    Ending date or null if newer than starting date.
	 * @param   bool       $register  True = registration date, False = last visit date.
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByTime(Date $starting = null, Date $ending = null, $register = true): KunenaFinder
	{
		$name = $register ? 'registerDate' : 'lastvisitDate';

		if ($starting && $ending)
		{
			$this->query->where($this->db->quoteName('a.' . $name) . ' BETWEEN ' . $this->db->quote($starting->toUnix()) . ' AND ' . $this->db->quote($ending->toUnix()));
		}
		elseif ($starting)
		{
			$this->query->where($this->db->quoteName('a.' . $name) . ' > ' . $this->db->quote($starting->toUnix()));
		}
		elseif ($ending)
		{
			$this->query->where($this->db->quoteName('a.' . $name) . ' <= ' . $this->db->quote($ending->toUnix()));
		}

		return $this;
	}

	/**
	 * @param   array  $ignore  ignore
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByConfiguration(array $ignore = []): KunenaFinder
	{
		if ($this->config->userlistCountUsers == '1')
		{
			$this->query->where('(' . $this->db->quoteName('a.block') . ' = 0 OR ' . $this->db->quoteName('a.activation') . '="")');
		}
		elseif ($this->config->userlistCountUsers == '2')
		{
			$this->query->where('(' . $this->db->quoteName('a.block') . ' = 0 AND ' . $this->db->quoteName('a.activation') . '="")');
		}
		elseif ($this->config->userlistCountUsers == '3')
		{
			$this->query->where($this->db->quoteName('a.block') . ' = 0');
		}

		// Hide super admins from the list
		if ($this->config->superAdminUserlist && $ignore)
		{
			$this->query->where($this->db->quoteName('a.id') . ' NOT IN (' . implode(',', $ignore) . ')');
		}

		return $this;
	}

	/**
	 * @param   string  $search  search
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByName(string $search): KunenaFinder
	{
		if ($search)
		{
			if ($this->config->username)
			{
				$this->query->where($this->db->quoteName('a.username') . ' LIKE ' . $this->db->quote($search));
			}
			else
			{
				$this->query->where($this->db->quoteName('a.name') . ' LIKE ' . $this->db->quote($search));
			}
		}

		return $this;
	}

	/**
	 * Get users.
	 *
	 * @return array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws \Exception
	 */
	public function find(): array
	{
		$results = parent::find();

		return KunenaUserHelper::loadUsers($results);
	}
}
