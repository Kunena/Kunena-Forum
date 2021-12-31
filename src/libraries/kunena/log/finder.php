<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum.Message
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaLogFinder
 *
 * @since 5.0
 */
class KunenaLogFinder extends KunenaDatabaseObjectFinder
{
	/**
	 * @var string
	 * @since Kunena 5.0
	 */
	protected $table = '#__kunena_logs';

	/**
	 * Constructor.
	 * @since Kunena 5.0
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Filter by time.
	 *
	 * @param   \Joomla\CMS\Date\Date $starting Starting date or null if older than ending date.
	 * @param   \Joomla\CMS\Date\Date $ending   Ending date or null if newer than starting date.
	 *
	 * @return $this
	 * @since Kunena 5.0
	 */
	public function filterByTime(\Joomla\CMS\Date\Date $starting = null, \Joomla\CMS\Date\Date $ending = null)
	{
		if ($starting && $ending)
		{
			$this->query->where("a.time BETWEEN {$this->db->quote($starting->toUnix())} AND {$this->db->quote($ending->toUnix())}");
		}
		elseif ($starting)
		{
			$this->query->where("a.time > {$this->db->quote($starting->toUnix())}");
		}
		elseif ($ending)
		{
			$this->query->where("a.time <= {$this->db->quote($ending->toUnix())}");
		}

		return $this;
	}

	/**
	 * @param   mixed $condition condition
	 *
	 * @return $this
	 * @since Kunena 5.0
	 */
	public function innerJoin($condition)
	{
		$this->query->innerJoin($condition);

		return $this;
	}

	/**
	 * @param   mixed $columns columns
	 *
	 * @return $this
	 * @since Kunena 5.0
	 */
	public function select($columns)
	{
		$this->query->select($columns);

		return $this;
	}

	/**
	 * @param   mixed $columns columns
	 *
	 * @return $this
	 * @since Kunena 5.0
	 */
	public function group($columns)
	{
		$this->query->group($columns);

		return $this;
	}

	/**
	 * Get log entries.
	 *
	 * @return array|KunenaCollection
	 * @throws Exception
	 * @since Kunena 5.0
	 */
	public function find()
	{
		if ($this->skip)
		{
			return array();
		}

		$query = clone $this->query;
		$this->build($query);
		$query->select('a.*');
		$this->db->setQuery($query, $this->start, $this->limit);

		try
		{
			$results = new KunenaCollection((array) $this->db->loadObjectList('id'));
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $results;
	}

	/**
	 * @param   JDatabaseQuery $query query
	 *
	 * @since Kunena 5.0
	 * @return void
	 */
	protected function build(JDatabaseQuery $query)
	{
	}
}
