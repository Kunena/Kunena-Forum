<?php
/**
 * Kunena Component
 * @package         Kunena.Framework
 * @subpackage      User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaUserFinder
 * @since Kunena
 */
class KunenaUserFinder extends KunenaDatabaseObjectFinder
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $table = '#__users';

	/**
	 * @var KunenaConfig|mixed
	 * @since Kunena
	 */
	protected $config;

	/**
	 * Constructor.
	 * @since Kunena
	 * @throws Exception
	 */
	public function __construct()
	{
		parent::__construct();

		$this->config = KunenaConfig::getInstance();
		$this->limit  = $this->config->userlist_rows;

		$this->query->leftJoin($this->db->quoteName('#__kunena_users') . ' AS ku ON ku.userid=a.id');
	}

	/**
	 * Filter by time, either on registration or last visit date.
	 *
	 * @param   \Joomla\CMS\Date\Date $starting Starting date or null if older than ending date.
	 * @param   \Joomla\CMS\Date\Date $ending   Ending date or null if newer than starting date.
	 * @param   bool                  $register True = registration date, False = last visit date.
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function filterByTime(\Joomla\CMS\Date\Date $starting = null, \Joomla\CMS\Date\Date $ending = null, $register = true)
	{
		$name = $register ? 'registerDate' : 'lastvisitDate';

		if ($starting && $ending)
		{
			$this->query->where("a.{$name} BETWEEN {$this->db->quote($starting->toUnix())} AND {$this->db->quote($ending->toUnix())}");
		}
		elseif ($starting)
		{
			$this->query->where("a.{$name} > {$this->db->quote($starting->toUnix())}");
		}
		elseif ($ending)
		{
			$this->query->where("a.{$name} <= {$this->db->quote($ending->toUnix())}");
		}

		return $this;
	}

	/**
	 * @param   array $ignore ignore
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function filterByConfiguration(array $ignore = array())
	{
		if ($this->config->userlist_count_users == '1')
		{
			$this->query->where('(a.block=0 OR a.activation="")');
		}
		elseif ($this->config->userlist_count_users == '2')
		{
			$this->query->where('(a.block=0 AND a.activation="")');
		}
		elseif ($this->config->userlist_count_users == '3')
		{
			$this->query->where('a.block=0');
		}

		// Hide super admins from the list
		if ($this->config->superadmin_userlist && $ignore)
		{
			$this->query->where('a.id NOT IN (' . implode(',', $ignore) . ')');
		}

		return $this;
	}

	/**
	 * @param   string $search search
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function filterByName($search)
	{
		if ($search)
		{
			if ($this->config->username)
			{
				$this->query->where("a.username LIKE '%{$this->db->escape($search)}%'");
			}
			else
			{
				$this->query->where("a.name LIKE '%{$this->db->escape($search)}%'");
			}
		}

		return $this;
	}

	/**
	 * Get users.
	 *
	 * @return array|KunenaUser[]
	 * @throws Exception
	 * @since Kunena
	 */
	public function find()
	{
		$results = parent::find();

		return KunenaUserHelper::loadUsers($results);
	}
}
