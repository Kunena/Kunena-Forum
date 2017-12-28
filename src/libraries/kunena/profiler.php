<?php
/**
 * Kunena Component
 * @package    Kunena.Framework
 *
 * @copyright  (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ('joomla.error.profiler');

/**
 * Class KunenaProfiler
 */
class KunenaProfiler extends JProfiler
{
	protected static $_instances = array();

	protected $_kstart = array();

	/**
	 * @var array|KunenaProfilerItem[]
	 */
	protected $_heap = array();

	/**
	 * @param string $prefix
	 *
	 * @return KunenaProfiler
	 *
	 * @fixme override getInstance() and fix the function into Joomla
	 */
	public static function instance($prefix = 'Kunena')
	{
		if (empty(self::$_instances[$prefix]))
		{
			$c = __CLASS__;
			self::$_instances[$prefix] = new $c($prefix);
		}

		return self::$_instances[$prefix];
	}

	/**
	 * @param $name
	 */
	public function start($name)
	{
		$item = KunenaProfilerItem::getInstance($name);
		$item->start($this->getmicrotime());
		$this->_heap[] = $item;
	}

	/**
	 * @param $name
	 *
	 * @return float
	 */
	public function getTime($name)
	{
		$item = KunenaProfilerItem::getInstance($name);

		return $this->getmicrotime() - $item->getStartTime();
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 */
	public function stop($name)
	{
		$item = array_pop($this->_heap);

		if (!$item || $item->name != $name)
		{
			trigger_error(__CLASS__.'::'.__FUNCTION__."('$name') is missing start()");
		}

		$delta = $item->stop($this->getmicrotime());

		if (end($this->_heap))
		{
			$this->_heap[key($this->_heap)]->external($delta);
		}

		return $item;
	}

	/**
	 * @return array|KunenaProfilerItem[]
	 */
	public function getAll()
	{
		$items = KunenaProfilerItem::getAll();
		$this->sort($items);

		return $items;
	}

	/**
	 * @param        $array
	 * @param string $property
	 *
	 * @return bool
	 */
	function sort(&$array, $property = 'total')
	{
		return usort($array, function($a, $b) use ($property)
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
 */
class KunenaProfilerItem
{
	/**
	 * @var array|KunenaProfilerItem[]
	 */
	protected static $_instances = array();

	public $start = array();

	/**
	 * @param $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
		$this->calls = 0;
		$this->total = 0.0;
		$this->external = 0.0;
	}

	/**
	 * @param string $name
	 * @return KunenaProfilerItem
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
	 */
	public static function getAll()
	{
		return self::$_instances;
	}

	/**
	 * @return mixed
	 */
	public function getStartTime()
	{
		return end($this->start);
	}

	/**
	 * @return float
	 */
	public function getTotalTime()
	{
		return $this->total;
	}

	/**
	 * @return float
	 */
	public function getInternalTime()
	{
		return $this->total - $this->external;
	}

	/**
	 * @param $starttime
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
	 */
	public function external($delta)
	{
		$this->external += $delta;
	}
}
