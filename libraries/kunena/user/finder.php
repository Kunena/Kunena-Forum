<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaUserFinder
 */
class KunenaUserFinder
{
	/**
	 * @var JDatabaseQuery
	 */
	protected $query;
	/**
	 * @var JDatabase
	 */
	protected $db;
	protected $start = 0;
	protected $limit = 20;
	protected $hold = array(0);
	protected $moved = null;
	protected $joinKunena = false;
	protected $config;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->config =  KunenaConfig::getInstance();
		$this->limit = $this->config->userlist_rows;
		$this->db = JFactory::getDbo();
		$this->query = $this->db->getQuery(true);
		$this->query
			->from('#__users AS u')
			->leftJoin('#__kunena_users AS ku ON ku.userid=u.id');
	}

	/**
	 * Set limitstart for the query.
	 *
	 * @param int $limitstart
	 *
	 * @return $this
	 */
	public function start($limitstart = 0)
	{
		$this->start = $limitstart;

		return $this;
	}

	/**
	 * Set limit to the query.
	 *
	 * If this function isn't used, Kunena will use threads per page configuration setting.
	 *
	 * @param int $limit
	 *
	 * @return $this
	 */
	public function limit($limit = null)
	{
		if (!isset($limit)) $limit = $this->config->userlist_rows;
		$this->limit = $limit;

		return $this;
	}

	/**
	 * Set order by field and direction.
	 *
	 * This function can be used more than once to chain order by.
	 *
	 * @param  string $by
	 * @param  int $direction
	 *
	 * @return $this
	 */
	public function order($by, $direction = 1)
	{
		$direction = $direction > 0 ? 'ASC' : 'DESC';
		$by = $this->db->quoteName($by);
		$this->query->order("{$by} {$direction}");

		return $this;
	}

	public function filterBy($field, $operation, $value)
	{
		$operation = strtoupper($operation);
		switch ($operation)
		{
			case '>':
			case '>=':
			case '<':
			case '<=':
			case '=':
				$this->query->where("{$this->db->quoteName($field)} {$operation} {$this->db->quote($value)}");
				break;
			case 'IN':
			case 'NOT IN':
				$value = (array) $value;
				if (empty($value)) {
					// WHERE field IN (nothing).
					$this->query->where('0');
				} else {
					$list = implode(',', $value);
					$this->query->where("{$this->db->quoteName($field)} {$operation} ({$list})");
				}
				break;
		}

		return $this;
	}

	/**
	 * Filter by time, either on registration or last visit date.
	 *
	 * @param JDate $starting  Starting date or null if older than ending date.
	 * @param JDate $ending    Ending date or null if newer than starting date.
	 * @param bool  $register  True = registration date, False = last visit date.
	 *
	 * @return $this
	 */
	public function filterByTime(JDate $starting = null, JDate $ending = null, $register = true)
	{
		$name = $register ? 'registerDate' : 'lastvisitDate';

		if ($starting && $ending) {
			$this->query->where("u.{$name} BETWEEN {$this->db->quote($starting->toUnix())} AND {$this->db->quote($ending->toUnix())}");
		} elseif ($starting) {
			$this->query->where("u.{$name} > {$this->db->quote($starting->toUnix())}");
		} elseif ($ending) {
			$this->query->where("u.{$name} <= {$this->db->quote($ending->toUnix())}");
		}

		return $this;
	}

	public function filterByConfiguration(array $ignore = array()) {
		if ($this->config->userlist_count_users == '1' ) {
			$this->query->where('(u.block=0 OR u.activation="")');
		} elseif ($this->config->userlist_count_users == '2' ) {
			$this->query->where('(u.block=0 AND u.activation="")');
		} elseif ($this->config->userlist_count_users == '3' ) {
			$this->query->where('u.block=0');
		}
		// Hide super admins from the list
		if ($ignore) $this->query->where('u.id NOT IN ('.implode(',', $ignore).')');

		return $this;
	}

	public function filterByName($search) {
		if ($search) {
			if ($this->config->username) {
				$this->query->where("u.username LIKE '%{$this->db->escape($search)}%'");
			} else {
				$this->query->where("u.name LIKE '%{$this->db->escape($search)}%'");
			}
		}

		return $this;
	}

	/**
	 * Get users.
	 *
	 * @return array|KunenaUser[]
	 */
	public function find()
	{
		$query = clone $this->query;
		$this->build($query);
		$query->select('u.id');
		$this->db->setQuery($query, $this->start, $this->limit);
		$results = (array) $this->db->loadColumn();
		KunenaError::checkDatabaseError();

		return KunenaUserHelper::loadUsers($results);
	}

	/**
	 * Count users.
	 *
	 * @return int
	 */
	public function count()
	{
		$query = clone $this->query;
		$this->build($query);
		$query->select('COUNT(*)');
		$this->db->setQuery($query);
		$count = (int) $this->db->loadResult();
		KunenaError::checkDatabaseError();

		return $count;
	}

	protected function build($query)
	{
	}
}
