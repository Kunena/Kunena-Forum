<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Database
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Database\Object;

\defined('_JEXEC') or die;

use DomainException;
use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\QueryInterface;
use Kunena\Forum\Libraries\Error\KunenaError;

/**
 * Class KunenaDatabaseObjectFinder
 *
 * @since   Kunena 6.0
 */
abstract class KunenaFinder
{
	/**
	 * Table associated with the model.
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $table;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $primaryKey = 'id';

	/**
	 * @var     DatabaseQuery
	 * @since   Kunena 6.0
	 */
	protected $query;

	/**
	 * @var     DatabaseDriver
	 * @since   Kunena 6.0
	 */
	protected $db;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	protected $start = 0;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	protected $limit = 20;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $skip = false;

	/**
	 * Constructor.
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct()
	{
		if (!$this->table)
		{
			throw new DomainException('Table name missing from ' . \get_class($this));
		}

		$this->db    = Factory::getContainer()->get('DatabaseDriver');
		$this->query = $this->db->getQuery(true);
		$this->query->from($this->db->quoteName($this->table, 'a'));
	}

	/**
	 * Set limitstart for the query.
	 *
	 * @param   int  $limitstart  limitstart
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function start($limitstart = 0): KunenaFinder
	{
		$this->start = $limitstart;

		return $this;
	}

	/**
	 * Set limit to the query.
	 *
	 * If this function isn't used, RokClub will use threads per page configuration setting.
	 *
	 * @param   int  $limit  limit
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function limit($limit = null): KunenaFinder
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
	 * @param   string  $by         by
	 * @param   int     $direction  direction
	 * @param   string  $alias      alias
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function order(string $by, $direction = 1, $alias = 'a'): KunenaFinder
	{
		$direction = $direction > 0 ? 'ASC' : 'DESC';
		$by        = (!empty($alias) ? ($alias . '.') : '') . $this->db->quoteName($by);
		$this->query->order("{$by} {$direction}");

		return $this;
	}

	/**
	 * Filter by field.
	 *
	 * @param   string        $field      Field name.
	 * @param   string        $operation  Operation (>|>=|<|<=|=|IN|NOT IN)
	 * @param   string|array  $value      Value.
	 * @param   bool          $escape     Only works for LIKE / NOT LIKE.
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function where(string $field, string $operation, $value, $escape = true): KunenaFinder
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
						$value,
						function (&$item) use ($db) {
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
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function find()
	{
		if ($this->skip)
		{
			return [];
		}

		$query = clone $this->query;
		$this->build($query);
		$query->select('a.' . $this->primaryKey);
		$query->setLimit($this->limit, $this->start);
		$this->db->setQuery($query);

		try
		{
			$results = (array) $this->db->loadColumn();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $results;
	}

	/**
	 * Override to include your own static filters.
	 *
	 * @param   QueryInterface|null  $query  query
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function build(QueryInterface $query = null): void
	{
	}

	/**
	 * Count items.
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function count(): int
	{
		$query = clone $this->query;
		$this->build($query);

		if ($query->group)
		{
			$query->select('COUNT(*)')->from($this->db->quoteName($query, 's'));
			$this->db->setQuery($query);
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
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $count;
	}
}
