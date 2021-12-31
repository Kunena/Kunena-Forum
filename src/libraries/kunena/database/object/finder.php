<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Database
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

use Joomla\CMS\Factory;

// No direct access
defined('_JEXEC') or die;

/**
 * Class KunenaDatabaseObjectFinder
 * @since Kunena
 */
abstract class KunenaDatabaseObjectFinder
{
	/**
	 * Table associated with the model.
	 *
	 * @var string
	 * @since Kunena
	 */
	protected $table;

	/**
	 * @var string
	 * @since Kunena
	 */
	protected $primaryKey = 'id';

	/**
	 * @var JDatabaseQuery
	 * @since Kunena
	 */
	protected $query;

	/**
	 * @var JDatabase
	 * @since Kunena
	 */
	protected $db;

	/**
	 * @var integer
	 * @since Kunena
	 */
	protected $start = 0;

	/**
	 * @var integer
	 * @since Kunena
	 */
	protected $limit = 20;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $skip = false;

	/**
	 * Constructor.
	 * @since Kunena
	 */
	public function __construct()
	{
		if (!$this->table)
		{
			throw new DomainException('Table name missing from ' . get_class($this));
		}

		$this->db    = Factory::getDbo();
		$this->query = $this->db->getQuery(true);
		$this->query->from($this->table . ' AS a');
	}

	/**
	 * Set limitstart for the query.
	 *
	 * @param   int $limitstart limitstart
	 *
	 * @return $this
	 * @since Kunena
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
	 * @param   int $limit limit
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function limit($limit = null)
	{
		if ($limit !== null)
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
	 * @param   string $by        by
	 * @param   int    $direction direction
	 * @param   string $alias     alias
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function order($by, $direction = 1, $alias = 'a')
	{
		$direction = $direction > 0 ? 'ASC' : 'DESC';
		$by        = (!empty($alias) ? ($alias . '.') : '') . $this->db->quoteName($by);
		$this->query->order("{$by} {$direction}");

		return $this;
	}

	/**
	 * Filter by field.
	 *
	 * @param   string       $field     Field name.
	 * @param   string       $operation Operation (>|>=|<|<=|=|IN|NOT IN)
	 * @param   string|array $value     Value.
	 * @param   bool         $escape    Only works for LIKE / NOT LIKE.
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function where($field, $operation, $value, $escape = true)
	{
		$operation = strtoupper($operation);

		switch ($operation)
		{
			case '>':
			case '>=':
			case '<':
			case '<=':
			case '=':
			case '!=':
				$this->query->where("{$this->db->quoteName($field)} {$operation} {$this->db->quote($value)}");
				break;
			case 'BETWEEN':
				list($a, $b) = (array) $value;
				$this->query->where("{$this->db->quoteName($field)} BETWEEN {$this->db->quote($a)} AND {$this->db->quote($b)}");
				break;
			case 'LIKE':
			case 'NOT LIKE':
				$value = $escape ? $this->db->quote($value) : $value;
				$this->query->where("{$this->db->quoteName($field)} {$operation} {$value}");
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
					array_walk(
						$value, function (&$item) use ($db) {
							$item = $db->quote($item);
						}
					);
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
	 * @throws Exception
	 * @since Kunena
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

		try
		{
			$results = (array) $this->db->loadColumn();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $results;
	}

	/**
	 * Override to include your own static filters.
	 *
	 * @param   JDatabaseQuery $query query
	 *
	 * @return void
	 * @since Kunena
	 */
	protected function build(JDatabaseQuery $query)
	{
	}

	/**
	 * Count items.
	 *
	 * @return integer
	 * @throws Exception
	 * @since Kunena
	 */
	public function count()
	{
		$query = clone $this->query;
		$this->build($query);

		if ($query->group)
		{
			$countQuery = $this->db->getQuery(true);
			$countQuery->select('COUNT(*)')->from("({$query}) AS c");
			$this->db->setQuery($countQuery);
		}
		else
		{
			$query->clear('select')->select('COUNT(*)');
			$this->db->setQuery($query);
		}

		try
		{
			$count = (int) $this->db->loadResult();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $count;
	}
}
