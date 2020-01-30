<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Message
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Log;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\QueryInterface;
use Kunena\Forum\Libraries\Collection\Collection;
use Kunena\Forum\Libraries\Error\KunenaError;
use function defined;

/**
 * Class KunenaLogFinder
 *
 * @since 5.0
 */
class Finder extends \Kunena\Forum\Libraries\Database\Object\Finder
{
	/**
	 * @var     string
	 * @since   Kunena 5.0
	 */
	protected $table = '#__kunena_logs';

	/**
	 * Constructor.
	 *
	 * @since   Kunena 5.0
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Filter by time.
	 *
	 * @param   Date  $starting  Starting date or null if older than ending date.
	 * @param   Date  $ending    Ending date or null if newer than starting date.
	 *
	 * @return  $this
	 *
	 * @since   Kunena 5.0
	 */
	public function filterByTime(Date $starting = null, Date $ending = null)
	{
		if ($starting && $ending)
		{
			$this->query->where($this->db->quoteName('a.time') . ' BETWEEN ' . $this->db->quote($starting->toUnix()) . ' AND ' . $this->db->quote($ending->toUnix()));
		}
		elseif ($starting)
		{
			$this->query->where($this->db->quoteName('a.time') . ' > ' . $this->db->quote($starting->toUnix()));
		}
		elseif ($ending)
		{
			$this->query->where($this->db->quoteName('a.time') . ' <= ' . $this->db->quote($ending->toUnix()));
		}

		return $this;
	}

	/**
	 * @param   mixed  $condition  condition
	 *
	 * @return  $this
	 *
	 * @since   Kunena 5.0
	 */
	public function innerJoin($condition)
	{
		$this->query->innerJoin($condition);

		return $this;
	}

	/**
	 * @param   mixed  $columns  columns
	 *
	 * @return  $this
	 *
	 * @since   Kunena 5.0
	 */
	public function select($columns)
	{
		$this->query->select($columns);

		return $this;
	}

	/**
	 * @param   mixed  $columns  columns
	 *
	 * @return  $this
	 *
	 * @since   Kunena 5.0
	 */
	public function group($columns)
	{
		$this->query->group($columns);

		return $this;
	}

	/**
	 * Get log entries.
	 *
	 * @return  array|Collection
	 *
	 * @since   Kunena 5.0
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
		$query->select($this->db->quoteName('a.*'));
		$query->setLimit($this->limit, $this->start);
		$this->db->setQuery($query);

		try
		{
			$results = new Collection((array) $this->db->loadObjectList('id'));
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $results;
	}

	/**
	 * @param   QueryInterface  $query  query
	 *
	 * @return  void
	 *
	 * @since   Kunena 5.0
	 */
	protected function build(QueryInterface $query = null)
	{
	}
}
