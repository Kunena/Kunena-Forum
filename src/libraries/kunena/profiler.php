<?php
/**
 * Kunena Component
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.error.profiler');

/**
 * Class KunenaProfiler
 * @since Kunena
 */
class KunenaProfiler extends \Joomla\CMS\Profiler\Profiler
{
	/**
	 * @since Kunena
	 * @var array
	 */
	protected static $_instances = array();

	/**
	 * @since Kunena
	 * @var array
	 */
	protected $_kstart = array();

	/**
	 * @since Kunena
	 * @var array|KunenaProfilerItem[]
	 */
	protected $_heap = array();

	/**
	 * @param   string $prefix string
	 *
	 * @return KunenaProfiler
	 *
	 * @fixme override getInstance() and fix the function into Joomla
	 * @since Kunena
	 */
	public static function instance($prefix = 'Kunena')
	{
		if (empty(self::$_instances[$prefix]))
		{
			$c                         = __CLASS__;
			self::$_instances[$prefix] = new $c($prefix);
		}

		return self::$_instances[$prefix];
	}

	/**
	 * @param   string $name name
	 *
	 * @return void
	 * @since Kunena
	 */
	public function start($name)
	{
		$item = KunenaProfilerItem::getInstance($name);
		$item->start(microtime(true));
		$this->_heap[] = $item;
	}

	/**
	 * @param   string $name name
	 *
	 * @return float
	 * @since Kunena
	 */
	public function getTime($name)
	{
		$item = KunenaProfilerItem::getInstance($name);

		return microtime(true) - $item->getStartTime();
	}

	/**
	 * @param   string $name name
	 *
	 * @return mixed
	 * @since Kunena
	 */
	public function stop($name)
	{
		$item = array_pop($this->_heap);

		if (!$item)
		{
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . "('$name') is missing start()");
		}
		elseif ($item->name != $name)
		{
			$item->start(microtime(true));
		}

		$delta = $item->stop(microtime(true));

		if (end($this->_heap))
		{
			$this->_heap[key($this->_heap)]->external($delta);
		}

		return $item;
	}

	/**
	 * @return array|KunenaProfilerItem[]
	 * @since Kunena
	 */
	public function getAll()
	{
		$items = KunenaProfilerItem::getAll();
		$this->sort($items);

		return $items;
	}

	/**
	 * @param   array  $array    array
	 * @param   string $property property
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function sort(&$array, $property = 'total')
	{
		return usort($array, function ($a, $b) use ($property) {

			if ($a->$property == $b->$property)
			{
				return 0;
			}

			return $a->$property < $b->$property ? 1 : -1;
		});
	}
}

/**
 * Class KunenaProfilerItem
 * @since Kunena
 */
class KunenaProfilerItem
{
	/**
	 * @since Kunena
	 * @var array|KunenaProfilerItem[]
	 */
	protected static $_instances = array();

	/**
	 * @since Kunena
	 * @var array
	 */
	public $start = array();

	/**
	 * @param   string $name name
	 *
	 * @since Kunena
	 */
	public function __construct($name)
	{
		$this->name     = $name;
		$this->calls    = 0;
		$this->total    = 0.0;
		$this->external = 0.0;
	}

	/**
	 * @param   string $name name
	 *
	 * @return KunenaProfilerItem
	 * @since Kunena
	 */
	public static function getInstance($name)
	{
		if (empty(self::$_instances[$name]))
		{
			self::$_instances[$name] = new KunenaProfilerItem($name);
		}

		return self::$_instances[$name];
	}

	/**
	 * @return array|KunenaProfilerItem[]
	 * @since Kunena
	 */
	public static function getAll()
	{
		return self::$_instances;
	}

	/**
	 * @return mixed
	 * @since Kunena
	 */
	public function getStartTime()
	{
		return end($this->start);
	}

	/**
	 * @return float
	 * @since Kunena
	 */
	public function getTotalTime()
	{
		return $this->total;
	}

	/**
	 * @return float
	 * @since Kunena
	 */
	public function getInternalTime()
	{
		return $this->total - $this->external;
	}

	/**
	 * @param   boolean $starttime start time
	 *
	 * @return mixed
	 * @since Kunena
	 */
	public function start($starttime)
	{
		$this->calls++;
		$this->start[] = $starttime;

		return $starttime;
	}

	/**
	 * @param   boolean $stoptime stop time
	 *
	 * @return float
	 * @since Kunena
	 */
	public function stop($stoptime)
	{
		$starttime = array_pop($this->start);

		if (!$starttime || !$stoptime)
		{
			return 0.0;
		}

		$delta       = $stoptime - $starttime;
		$this->total += $delta;

		return $delta;
	}

	/**
	 * @param   void $delta delta
	 *
	 * @return void
	 * @since Kunena
	 */
	public function external($delta)
	{
		$this->external += $delta;
	}
}
