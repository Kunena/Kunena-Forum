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
     * @var     boolean
     * @since   Kunena 7.2
     */
    protected static $cssPrinted = false;

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
     * Render the collected profiler data as a collapsible html panel (debug mode only).
     *
     * The panel has a one line summary (total time, items, calls, memory) and a table of the profiled items sorted by
     * total time, with the own time, the total time, the number of calls and a bar which shows the share of the page time.
     *
     * @param   string  $title  Title of the panel.
     *
     * @return  string  Html.
     *
     * @since   Kunena 7.2
     */
    public function render(string $title = 'Kunena Profile Information'): string
    {
        $items = $this->getAll();

        if (!$items) {
            return '';
        }

        $esc = static fn ($text): string => htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
        $fmt = static function (float $seconds): string {
            return $seconds >= 1 ? number_format($seconds, 2) . ' s' : number_format($seconds * 1000, 1) . ' ms';
        };

        $pageTime = 0.0;
        $calls    = 0;

        foreach ($items as $item) {
            $calls += (int) $item->calls;
            $pageTime = max($pageTime, $item->getTotalTime());

            if ($item->name === 'Total Time') {
                $pageTime = $item->getTotalTime();
                break;
            }
        }

        $pageTime = $pageTime > 0 ? $pageTime : 1.0;
        $memory   = number_format(memory_get_peak_usage(true) / 1048576, 1) . ' MB';

        $css = '';

        if (!self::$cssPrinted) {
            self::$cssPrinted = true;
            $css = '<style>'
                . '.kprofiler{--kp-bg:#fff;--kp-fg:#1f2933;--kp-muted:#6b7785;--kp-line:#e3e8ee;--kp-head:#f5f7fa;--kp-bar:#4c8bf5;--kp-warn:#e8a317;--kp-bad:#d64545;'
                . 'margin:24px 0;border:1px solid var(--kp-line);border-radius:10px;background:var(--kp-bg);color:var(--kp-fg);font:13px/1.45 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06)}'
                . '@media (prefers-color-scheme:dark){.kprofiler{--kp-bg:#1b1f24;--kp-fg:#e6e9ee;--kp-muted:#9aa5b1;--kp-line:#2e353d;--kp-head:#232930;--kp-bar:#6aa3ff}}'
                . '.kprofiler summary{display:flex;flex-wrap:wrap;align-items:center;gap:6px 16px;padding:10px 14px;cursor:pointer;background:var(--kp-head);list-style:none}'
                . '.kprofiler summary::-webkit-details-marker{display:none}'
                . '.kprofiler summary::before{content:"\25B8";color:var(--kp-muted);transition:transform .15s}'
                . '.kprofiler[open] summary::before{transform:rotate(90deg)}'
                . '.kprofiler .kp-title{font-weight:600;margin-right:auto}'
                . '.kprofiler .kp-chip{padding:2px 9px;border-radius:999px;border:1px solid var(--kp-line);background:var(--kp-bg);color:var(--kp-muted);font-variant-numeric:tabular-nums;white-space:nowrap}'
                . '.kprofiler .kp-chip b{color:var(--kp-fg);font-weight:600}'
                . '.kprofiler .kp-scroll{max-height:420px;overflow:auto}'
                . '.kprofiler table{width:100%;border-collapse:collapse;font-variant-numeric:tabular-nums}'
                . '.kprofiler th{position:sticky;top:0;background:var(--kp-head);text-align:right;font-weight:600;color:var(--kp-muted);padding:7px 12px;border-bottom:1px solid var(--kp-line);white-space:nowrap}'
                . '.kprofiler td{padding:6px 12px;border-bottom:1px solid var(--kp-line);text-align:right;white-space:nowrap}'
                . '.kprofiler th:first-child,.kprofiler td:first-child{text-align:left;white-space:normal;word-break:break-word;width:100%}'
                . '.kprofiler tr:last-child td{border-bottom:0}'
                . '.kprofiler tbody tr:hover{background:var(--kp-head)}'
                . '.kprofiler .kp-tag{display:inline-block;min-width:44px;margin-right:8px;padding:0 6px;border-radius:4px;background:var(--kp-head);border:1px solid var(--kp-line);color:var(--kp-muted);font-size:11px;text-align:center}'
                . '.kprofiler .kp-warn{color:var(--kp-warn);font-weight:600}.kprofiler .kp-bad{color:var(--kp-bad);font-weight:600}'
                . '.kprofiler .kp-meter{display:block;width:120px;height:6px;border-radius:3px;background:var(--kp-line);overflow:hidden}'
                . '.kprofiler .kp-meter i{display:block;height:100%;background:var(--kp-bar)}'
                . '</style>';
        }

        $rows = '';

        foreach ($items as $item) {
            $name = (string) $item->name;
            $own  = $item->getInternalTime();
            $tot  = $item->getTotalTime();

            if (str_starts_with($name, 'function ')) {
                $tag   = 'func';
                $label = substr($name, 9);
            } elseif (str_starts_with($name, 'render layout')) {
                $tag   = 'layout';
                $label = trim(substr($name, 13), " '");
            } else {
                $tag   = 'other';
                $label = $name;
            }

            // Keep the readable part of "Kunena\Forum\Libraries\Foo\Bar::method()".
            $short = preg_replace('/^(?:\\\\?[A-Za-z0-9_]+\\\\)+/', '', $label);
            $class = $name === 'Total Time' ? '' : ($own > 0.15 ? ' class="kp-bad"' : ($own > 0.05 ? ' class="kp-warn"' : ''));
            $share = max(0.5, min(100, $tot / $pageTime * 100));

            $rows .= '<tr><td title="' . $esc($name) . '"><span class="kp-tag">' . $tag . '</span>' . $esc($short) . '</td>'
                . '<td>' . (int) $item->calls . '</td>'
                . '<td' . $class . '>' . $fmt($own) . '</td>'
                . '<td>' . $fmt($tot) . '</td>'
                . '<td><span class="kp-meter"><i style="width:' . number_format($share, 1, '.', '') . '%"></i></span></td></tr>';
        }

        return $css
            . '<details class="kprofiler"><summary><span class="kp-title">' . $esc($title) . '</span>'
            . '<span class="kp-chip">Page <b>' . $fmt($pageTime) . '</b></span>'
            . '<span class="kp-chip">Items <b>' . \count($items) . '</b></span>'
            . '<span class="kp-chip">Calls <b>' . $calls . '</b></span>'
            . '<span class="kp-chip">Peak memory <b>' . $esc($memory) . '</b></span></summary>'
            . '<div class="kp-scroll"><table><thead><tr><th>Item</th><th>Calls</th><th>Own time</th><th>Total time</th><th>Share</th></tr></thead>'
            . '<tbody>' . $rows . '</tbody></table></div></details>';
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
