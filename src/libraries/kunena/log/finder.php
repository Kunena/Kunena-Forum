<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Message
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaLogFinder
 *
 * @since 5.0
 */
class KunenaLogFinder extends KunenaDatabaseObjectFinder
{
	protected $table = '#__kunena_logs';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Filter by time.
	 *
	 * @param JDate $starting  Starting date or null if older than ending date.
	 * @param JDate $ending    Ending date or null if newer than starting date.
	 *
	 * @return $this
	 */
	public function filterByTime(JDate $starting = null, JDate $ending = null)
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

	public function innerJoin($condition)
	{
		$this->query->innerJoin($condition);

		return $this;
	}

	public function select($columns)
	{
		$this->query->select($columns);

		return $this;
	}

	public function group($columns)
	{
		$this->query->group($columns);

		return $this;
	}

	/**
	 * Get log entries.
	 *
	 * @return array|KunenaCollection
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

	protected function build(JDatabaseQuery $query)
	{
	}
}
