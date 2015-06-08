<?php
/**
 * @version   $Id$
 * @author	RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Class KunenaDatabaseObjectFinder
 */
abstract class KunenaDatabaseObjectFinder
{
	/**
	 * Table associated with the model.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * @var string
	 */
	protected $primaryKey = 'id';

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

	protected $skip = false;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		if (!$this->table)
		{
			throw new DomainException('Table name missing from ' . get_class($this));
		}

		$this->db = JFactory::getDbo();
		$this->query = $this->db->getQuery(true);
		$this->query->from($this->table . ' AS a');
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
	 * If this function isn't used, RokClub will use threads per page configuration setting.
	 *
	 * @param int $limit
	 *
	 * @return $this
	 */
	public function limit($limit = null)
	{
		if (!is_null($limit))
		{
			$this->limit = $limit;
		}

		return $this;
	}

	/**
	 * Set order by field and direction.
	 *
	 * This function can be used more than once to chain order by.
	 *
	 * @param  string $by
	 * @param  int $direction
	 * @param  string $alias
	 *
	 * @return $this
	 */
	public function order($by, $direction = 1, $alias = 'a')
	{
		$direction = $direction > 0 ? 'ASC' : 'DESC';
		$by = $alias . '.' . $this->db->quoteName($by);
		$this->query->order("{$by} {$direction}");

		return $this;
	}

	/**
	 * Filter by field.
	 *
	 * @param  string        $field       Field name.
	 * @param  string        $operation   Operation (>|>=|<|<=|=|IN|NOT IN)
	 * @param  string|array  $value       Value.
	 *
	 * @return $this
	 */
	public function where($field, $operation, $value)
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
			case 'BETWEEN':
				list($a, $b) = (array) $value;
				$this->query->where("{$this->db->quoteName($field)} BETWEEN {$this->db->quote($a)} AND {$this->db->quote($b)}");
				break;
			case 'IN':
			case 'NOT IN':
				$value = (array) $value;
				if (empty($value))
				{
					// WHERE field IN (nothing).
					$this->query->where('0');
				}
				else
				{
					$db = $this->db;
					array_walk($value, function (&$item) use ($db) { $item = $db->quote($item); });
					$list = implode(',', $value);
					$this->query->where("{$this->db->quoteName($field)} {$operation} ({$list})");
				}
				break;
		}

		return $this;
	}

	/**
	 * Get items.
	 *
	 * Derived classes should generally override this function to return correct objects.
	 *
	 * @return array
	 */
	public function find()
	{
		if ($this->skip)
		{
			return array();
		}

		$query = clone $this->query;
		$this->build($query);
		$query->select('a.' . $this->primaryKey);
		$this->db->setQuery($query, $this->start, $this->limit);
		$results = (array) $this->db->loadColumn();
		KunenaError::checkDatabaseError();

		return $results;
	}

	/**
	 * Count items.
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

	/**
	 * Override to include your own static filters.
	 *
	 * @param  JDatabaseQuery  $query
	 *
	 * @return void
	 */
	protected function build(JDatabaseQuery $query)
	{
	}
}
