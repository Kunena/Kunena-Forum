<?php

/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Profiler;

\defined('_JEXEC') or die();

use Joomla\CMS\Profiler\Profiler;

/**
 * Class KunenaProfiler
 *
 * @since   Kunena 6.0
 */
class KunenaProfiler extends Profiler
{
    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected static $_instances = [];

    /**
     * @var     array| KunenaProfilerItem[]
     * @since   Kunena 6.0
     */
    protected $_heap = [];

    /**
     * @param   string  $prefix  string
     *
     * @return  KunenaProfiler
     *
     * @fixme   override getInstance() and fix the function into Joomla
     * @since   Kunena 6.0
     */
    public static function instance($prefix = 'Kunena'): KunenaProfiler
    {
        if (empty(self::$_instances[$prefix])) {
            $c                         = __CLASS__;
            self::$_instances[$prefix] = new $c($prefix);
        }

        return self::$_instances[$prefix];
    }

    /**
     * @param   string  $name  name
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function start(string $name): void
    {
        $item = KunenaProfilerItem::getInstance($name);
        $item->start(microtime(true));
        $this->_heap[] = $item;
    }

    /**
     * @param   string  $name  name
     *
     * @return  float
     *
     * @since   Kunena 6.0
     */
    public function getTime(string $name): float
    {
        $item = KunenaProfilerItem::getInstance($name);

        return microtime(true) - $item->getStartTime();
    }

    /**
     * @param   string  $name  name
     *
     * @return  mixed
     *
     * @since   Kunena 6.0
     */
    public function stop(string $name)
    {
        // Find the innermost open item with this name; items above it were left open by code paths which skipped stop().
        $found = null;

        for ($i = \count($this->_heap) - 1; $i >= 0; $i--) {
            if ($this->_heap[$i]->name == $name) {
                $found = $i;
                break;
            }
        }

        if ($found === null) {
            trigger_error(__CLASS__ . '::' . __FUNCTION__ . "('$name') is missing start()");

            return KunenaProfilerItem::getInstance($name);
        }

        $item = $this->_heap[$found];
        array_splice($this->_heap, $found);

        $delta = $item->stop(microtime(true));

        if (end($this->_heap)) {
            $this->_heap[key($this->_heap)]->external($delta);
        }

        return $item;
    }

    /**
     * @return array
     *
     * @since   Kunena 6.0
     */
    public function getAll(): array
    {
        $items = KunenaProfilerItem::getAll();
        $this->sort($items);

        return $items;
    }

    /**
     * @param   array   $array     array
     * @param   string  $property  property
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public function sort(array &$array, $property = 'total'): bool
    {
        return usort($array, function ($a, $b) use ($property) {

            if ($a->$property == $b->$property) {
                return 0;
            }

            return $a->$property < $b->$property ? 1 : -1;
        });
    }
}

/**
 * Class KunenaProfilerItem
 *
 * @since   Kunena 6.0
 */
class KunenaProfilerItem
{
    /**
     * @var array|KunenaProfilerItem[]
     * @since   Kunena 6.0
     */
    protected static $_instances = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    public $start = [];
    /**
     * @var string
     * @since Kunena 6.0
     */
    public $name;
    /**
     * @var float
     * @since Kunena 6.0
     */
    public $total;
    /**
     * @var int
     * @since Kunena 6.0
     */
    public $calls;
    /**
     * @var float
     * @since Kunena 6.0
     */
    private $external;

    /**
     * @param   string  $name  name
     *
     * @since   Kunena 6.0
     */
    public function __construct(string $name)
    {
        $this->name     = $name;
        $this->calls    = 0;
        $this->total    = 0.0;
        $this->external = 0.0;
    }

    /**
     * @param   string  $name  name
     *
     * @return  KunenaProfilerItem
     *
     * @since   Kunena 6.0
     */
    public static function getInstance(string $name): KunenaProfilerItem
    {
        if (empty(self::$_instances[$name])) {
            self::$_instances[$name] = new KunenaProfilerItem($name);
        }

        return self::$_instances[$name];
    }

    /**
     * @return array
     *
     * @since   Kunena 6.0
     */
    public static function getAll(): array
    {
        return self::$_instances;
    }

    /**
     * @return  mixed
     *
     * @since   Kunena 6.0
     */
    public function getStartTime()
    {
        return end($this->start);
    }

    /**
     * @return  float
     *
     * @since   Kunena 6.0
     */
    public function getTotalTime(): float
    {
        return $this->total;
    }

    /**
     * @return  float
     *
     * @since   Kunena 6.0
     */
    public function getInternalTime(): float
    {
        return $this->total - $this->external;
    }

    /**
     * @param   float  $starttime  start time
     *
     * @return  float
     *
     * @since   Kunena 6.0
     */
    public function start($starttime): float
    {
        $this->calls++;
        $this->start[] = $starttime;

        return $starttime;
    }

    /**
     * @param   float  $stoptime  stop time
     *
     * @return float
     *
     * @since   Kunena 6.0
     */
    public function stop($stoptime): float
    {
        $starttime = array_pop($this->start);

        if (!$starttime || !$stoptime) {
            return 0.0;
        }

        $delta       = $stoptime - $starttime;
        $this->total += $delta;

        return $delta;
    }

    /**
     * @param   float  $delta  delta
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function external(float $delta): void
    {
        $this->external += $delta;
    }
}
