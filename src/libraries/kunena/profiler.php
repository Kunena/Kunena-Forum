<?php
/**
 * Kunena Component
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.error.profiler');

/**
 * Class KunenaProfiler
 * @since Kunena
 */
class KunenaProfiler extends JProfiler
{
	/**
	 * @var array
	 * @since Kunena
	 */
	protected static $_instances = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $_kstart = array();

	/**
	 * @var array|KunenaProfilerItem[]
	 * @since Kunena
	 */
	protected $_heap = array();

	/**
	 * @param   string $prefix
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
	 * @param $name
	 *
	 * @since Kunena
	 */
	public function start($name)
	{
		$item = KunenaProfilerItem::getInstance($name);
		$item->start(microtime(true));
		$this->_heap[] = $item;
	}

	/**
	 * @param $name
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
	 * @param $name
	 *
	 * @return mixed
	 * @since Kunena
	 */
	public function stop($name)
	{
		$item = array_pop($this->_heap);

		if (!$item || $item->name != $name)
		{
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . "('$name') is missing start()");
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
	 * @param          $array
	 * @param   string $property
	 *
	 * @return boolean
	 * @since Kunena
	 */
	function sort(&$array, $property = 'total')
	{
		return usort($array, function ($a, $b) use ($property)
		{
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
	 * @var array|KunenaProfilerItem[]
	 * @since Kunena
	 */
	protected static $_instances = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	public $start = array();

	/**
	 * @param $name
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
	 * @param   string $name
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
	 * @param $starttime
	 *
	 * @since Kunena
	 */
	public function start($starttime)
	{
		$this->calls++;
		$this->start[] = $starttime;
	}

	/**
	 * @param $stoptime
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

		$delta = $stoptime - $starttime;
		$this->total += $delta;

		return $delta;
	}

	/**
	 * @param $delta
	 *
	 * @since Kunena
	 */
	public function external($delta)
	{
		$this->external += $delta;
	}
}
