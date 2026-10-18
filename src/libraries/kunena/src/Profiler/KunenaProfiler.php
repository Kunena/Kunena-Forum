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
     * The panel has a one line summary (page time, items, calls, database queries, memory) and a table of the profiled
     * items. The table can be sorted by clicking a column, filtered by name and by type, the share bars show the part of
     * the page time, and the data can be copied as json or csv. The state (open, sort, filters) is kept in the
     * localStorage of the browser. Styles and script are printed once per request.
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
        $mem = static function (float $bytes): string {
            $sign = $bytes < 0 ? '-' : '';
            $abs  = abs($bytes);

            if ($abs >= 1048576) {
                return $sign . number_format($abs / 1048576, 1) . ' MB';
            }

            return $sign . number_format($abs / 1024, 0) . ' KB';
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
        $queries  = null;

        try {
            $db = \Joomla\CMS\Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);

            if (method_exists($db, 'getCount')) {
                $queries = (int) $db->getCount();
            }
        } catch (\Throwable $e) {
            $queries = null;
        }

        $assets = '';

        if (!self::$cssPrinted) {
            self::$cssPrinted = true;
            $assets = '<style>' . self::panelCss() . '</style><script>' . self::panelScript() . '</script>';
        }

        $rows = '';
        $data = [];

        foreach ($items as $item) {
            $name = (string) $item->name;
            $own  = $item->name === 'Total Time' ? 0.0 : $item->getInternalTime();
            $tot  = $item->getTotalTime();

            if (str_starts_with($name, 'function ')) {
                $type  = 'func';
                $label = substr($name, 9);
            } elseif (str_starts_with($name, 'render layout')) {
                $type  = 'layout';
                $label = trim(substr($name, 13), " '");
            } else {
                $type  = 'other';
                $label = $name;
            }

            // Keep the readable part of "Kunena\Forum\Libraries\Foo\Bar::method()".
            $short = preg_replace('/^(?:\\\\?[A-Za-z0-9_]+\\\\)+/', '', $label);
            $class = $name === 'Total Time' ? '' : ($own > 0.15 ? ' class="kp-bad"' : ($own > 0.05 ? ' class="kp-warn"' : ''));
            $share = max(0.5, min(100, $tot / $pageTime * 100));

            $data[] = ['name' => $name, 'type' => $type, 'calls' => (int) $item->calls, 'own_ms' => round($own * 1000, 3), 'total_ms' => round($tot * 1000, 3), 'memory_bytes' => (int) $item->memory];

            $rows .= '<tr data-name="' . $esc(strtolower($name)) . '" data-type="' . $type . '" data-calls="' . (int) $item->calls . '" data-own="' . round($own * 1000, 3)
                . '" data-total="' . round($tot * 1000, 3) . '" data-mem="' . (int) $item->memory . '">'
                . '<td title="' . $esc($name) . '"><span class="kp-tag kp-' . $type . '">' . $type . '</span>' . $esc($short) . '</td>'
                . '<td>' . (int) $item->calls . '</td>'
                . '<td' . $class . '>' . ($name === 'Total Time' ? '&mdash;' : $fmt($own)) . '</td>'
                . '<td>' . $fmt($tot) . '</td>'
                . '<td>' . $mem((float) $item->memory) . '</td>'
                . '<td><span class="kp-meter"><i style="width:' . number_format($share, 1, '.', '') . '%"></i></span></td></tr>';
        }

        $chips = '<span class="kp-chip">Page <b>' . $fmt($pageTime) . '</b></span>'
            . '<span class="kp-chip">Items <b>' . \count($items) . '</b></span>'
            . '<span class="kp-chip">Calls <b>' . $calls . '</b></span>'
            . ($queries !== null ? '<span class="kp-chip">Queries <b>' . $queries . '</b></span>' : '')
            . '<span class="kp-chip">Peak memory <b>' . $esc($mem((float) memory_get_peak_usage(true))) . '</b></span>';

        $head = static function (string $key, string $label, bool $left = false): string {
            return '<th data-sort="' . $key . '"' . ($left ? ' class="kp-left"' : '') . ' title="Sort by ' . $label . '">' . $label . '<span class="kp-arrow"></span></th>';
        };

        return $assets
            . '<details class="kprofiler"><summary><span class="kp-title">' . $esc($title) . '</span>' . $chips . '</summary>'
            . '<div class="kp-tools">'
            . '<input type="search" class="kp-filter" placeholder="Filter items" aria-label="Filter items">'
            . '<button type="button" class="kp-type kp-on" data-type="func">func</button>'
            . '<button type="button" class="kp-type kp-on" data-type="layout">layout</button>'
            . '<button type="button" class="kp-type kp-on" data-type="other">other</button>'
            . '<span class="kp-count"></span>'
            . '<button type="button" class="kp-copy" data-format="json">Copy JSON</button>'
            . '<button type="button" class="kp-copy" data-format="csv">Copy CSV</button></div>'
            . '<div class="kp-scroll"><table><thead><tr>'
            . $head('name', 'Item', true) . $head('calls', 'Calls') . $head('own', 'Own time') . $head('total', 'Total time') . $head('mem', 'Memory') . '<th>Share</th>'
            . '</tr></thead><tbody>' . $rows . '</tbody></table></div>'
            . '<script type="application/json" class="kp-data">' . str_replace('</', '<\/', (string) json_encode($data)) . '</script></details>';
    }

    /**
     * Css of the profiler panel.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    protected static function panelCss(): string
    {
        return '.kprofiler{--kp-bg:#fff;--kp-fg:#1f2933;--kp-muted:#6b7785;--kp-line:#e3e8ee;--kp-head:#f5f7fa;--kp-bar:#4c8bf5;--kp-warn:#e8a317;--kp-bad:#d64545;--kp-func:#2f6fdd;--kp-layout:#8a4fd6;--kp-other:#6b7785;'
            . 'margin:24px 0;border:1px solid var(--kp-line);border-radius:10px;background:var(--kp-bg);color:var(--kp-fg);font:13px/1.45 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06)}'
            . '@media (prefers-color-scheme:dark){.kprofiler{--kp-bg:#1b1f24;--kp-fg:#e6e9ee;--kp-muted:#9aa5b1;--kp-line:#2e353d;--kp-head:#232930;--kp-bar:#6aa3ff;--kp-func:#6aa3ff;--kp-layout:#b48cf2}}'
            . '.kprofiler summary{display:flex;flex-wrap:wrap;align-items:center;gap:6px 16px;padding:10px 14px;cursor:pointer;background:var(--kp-head);list-style:none}'
            . '.kprofiler summary::-webkit-details-marker{display:none}'
            . '.kprofiler summary::before{content:"\25B8";color:var(--kp-muted);transition:transform .15s}'
            . '.kprofiler[open] summary::before{transform:rotate(90deg)}'
            . '.kprofiler .kp-title{font-weight:600;margin-right:auto}'
            . '.kprofiler .kp-chip{padding:2px 9px;border-radius:999px;border:1px solid var(--kp-line);background:var(--kp-bg);color:var(--kp-muted);font-variant-numeric:tabular-nums;white-space:nowrap}'
            . '.kprofiler .kp-chip b{color:var(--kp-fg);font-weight:600}'
            . '.kprofiler .kp-tools{display:flex;flex-wrap:wrap;align-items:center;gap:8px;padding:8px 12px;border-bottom:1px solid var(--kp-line)}'
            . '.kprofiler .kp-tools input{flex:1 1 160px;min-width:120px;padding:5px 9px;border:1px solid var(--kp-line);border-radius:6px;background:var(--kp-bg);color:var(--kp-fg);font:inherit}'
            . '.kprofiler .kp-tools button{padding:4px 10px;border:1px solid var(--kp-line);border-radius:6px;background:var(--kp-bg);color:var(--kp-muted);font:inherit;cursor:pointer}'
            . '.kprofiler .kp-tools button:hover{color:var(--kp-fg);background:var(--kp-head)}'
            . '.kprofiler .kp-tools .kp-type.kp-on{color:var(--kp-fg);border-color:var(--kp-bar);box-shadow:inset 0 0 0 1px var(--kp-bar)}'
            . '.kprofiler .kp-count{color:var(--kp-muted);margin-left:auto;font-variant-numeric:tabular-nums}'
            . '.kprofiler .kp-scroll{max-height:420px;overflow:auto}'
            . '.kprofiler table{width:100%;border-collapse:collapse;font-variant-numeric:tabular-nums}'
            . '.kprofiler th{position:sticky;top:0;background:var(--kp-head);text-align:right;font-weight:600;color:var(--kp-muted);padding:7px 12px;border-bottom:1px solid var(--kp-line);white-space:nowrap;user-select:none}'
            . '.kprofiler th[data-sort]{cursor:pointer}.kprofiler th[data-sort]:hover{color:var(--kp-fg)}'
            . '.kprofiler th .kp-arrow{display:inline-block;width:10px;margin-left:4px}'
            . '.kprofiler th.kp-asc .kp-arrow::after{content:"\25B2"}.kprofiler th.kp-desc .kp-arrow::after{content:"\25BC"}'
            . '.kprofiler td{padding:6px 12px;border-bottom:1px solid var(--kp-line);text-align:right;white-space:nowrap}'
            . '.kprofiler th.kp-left,.kprofiler td:first-child{text-align:left}'
            . '.kprofiler td:first-child{white-space:normal;word-break:break-word;width:100%}'
            . '.kprofiler tr:last-child td{border-bottom:0}'
            . '.kprofiler tbody tr:hover{background:var(--kp-head)}'
            . '.kprofiler tbody tr[hidden]{display:none}'
            . '.kprofiler .kp-tag{display:inline-block;min-width:44px;margin-right:8px;padding:0 6px;border-radius:4px;background:var(--kp-head);border:1px solid var(--kp-line);font-size:11px;text-align:center;color:var(--kp-other)}'
            . '.kprofiler .kp-tag.kp-func{color:var(--kp-func)}.kprofiler .kp-tag.kp-layout{color:var(--kp-layout)}'
            . '.kprofiler .kp-warn{color:var(--kp-warn);font-weight:600}.kprofiler .kp-bad{color:var(--kp-bad);font-weight:600}'
            . '.kprofiler .kp-meter{display:block;width:120px;height:6px;border-radius:3px;background:var(--kp-line);overflow:hidden}'
            . '.kprofiler .kp-meter i{display:block;height:100%;background:var(--kp-bar)}';
    }

    /**
     * Script of the profiler panel: sorting, filtering, copy buttons and the state in the localStorage.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    protected static function panelScript(): string
    {
        return '(function(){var KEY="kunena-profiler";'
            . 'function load(){try{return JSON.parse(localStorage.getItem(KEY)||"{}")}catch(e){return{}}}'
            . 'function save(s){try{localStorage.setItem(KEY,JSON.stringify(s))}catch(e){}}'
            . 'function init(el){var tbody=el.querySelector("tbody"),rows=[].slice.call(tbody.rows),filter=el.querySelector(".kp-filter"),count=el.querySelector(".kp-count"),'
            . 'heads=[].slice.call(el.querySelectorAll("th[data-sort]")),chips=[].slice.call(el.querySelectorAll(".kp-type")),st=load();'
            . 'st.sort=st.sort||"total";st.dir=st.dir||-1;st.types=st.types||{func:1,layout:1,other:1};st.text=st.text||"";'
            . 'if(st.open){el.open=true}filter.value=st.text;'
            . 'function cmp(a,b){var k=st.sort;if(k==="name"){return st.dir*a.dataset.name.localeCompare(b.dataset.name)}return st.dir*((parseFloat(a.dataset[k])||0)-(parseFloat(b.dataset[k])||0))}'
            . 'function apply(){rows.sort(cmp);var shown=0,text=st.text.toLowerCase();rows.forEach(function(r){tbody.appendChild(r);'
            . 'var ok=st.types[r.dataset.type]&&(!text||r.dataset.name.indexOf(text)>-1);r.hidden=!ok;if(ok){shown++}});'
            . 'count.textContent=shown+" / "+rows.length;'
            . 'heads.forEach(function(h){h.classList.toggle("kp-asc",h.dataset.sort===st.sort&&st.dir>0);h.classList.toggle("kp-desc",h.dataset.sort===st.sort&&st.dir<0)});'
            . 'chips.forEach(function(c){c.classList.toggle("kp-on",!!st.types[c.dataset.type])})}'
            . 'function persist(){st.open=el.open;save(st)}'
            . 'heads.forEach(function(h){h.addEventListener("click",function(){var k=h.dataset.sort;if(st.sort===k){st.dir=-st.dir}else{st.sort=k;st.dir=k==="name"?1:-1}apply();persist()})});'
            . 'chips.forEach(function(c){c.addEventListener("click",function(){st.types[c.dataset.type]=st.types[c.dataset.type]?0:1;apply();persist()})});'
            . 'filter.addEventListener("input",function(){st.text=filter.value;apply();persist()});'
            . 'el.addEventListener("toggle",persist);'
            . 'function copy(text,btn){var done=function(){var t=btn.textContent;btn.textContent="Copied";setTimeout(function(){btn.textContent=t},1200)};'
            . 'if(navigator.clipboard&&window.isSecureContext){navigator.clipboard.writeText(text).then(done)}else{var ta=document.createElement("textarea");ta.value=text;document.body.appendChild(ta);ta.select();try{document.execCommand("copy")}catch(e){}document.body.removeChild(ta);done()}}'
            . '[].forEach.call(el.querySelectorAll(".kp-copy"),function(b){b.addEventListener("click",function(){var d=JSON.parse(el.querySelector(".kp-data").textContent);'
            . 'if(b.dataset.format==="json"){copy(JSON.stringify(d,null,2),b)}else{var head=["name","type","calls","own_ms","total_ms","memory_bytes"];'
            . 'copy([head.join(",")].concat(d.map(function(r){return head.map(function(k){var v=String(r[k]);return /[",\n]/.test(v)?"\""+v.replace(/"/g,"\"\"")+"\"":v}).join(",")})).join("\n"),b)}})});'
            . 'apply()}'
            . 'function run(){[].forEach.call(document.querySelectorAll(".kprofiler"),init)}'
            . 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",run)}else{run()}})();';
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
     * @var     float  Net memory (bytes) which was allocated during the profiled calls.
     * @since   Kunena 7.2
     */
    public $memory = 0.0;

    /**
     * @var     array  Memory usage at the start of the open calls.
     * @since   Kunena 7.2
     */
    protected $memoryStart = [];

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
        $this->memoryStart[] = memory_get_usage();

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
        $memoryStart = array_pop($this->memoryStart);

        if ($memoryStart !== null) {
            $this->memory += memory_get_usage() - $memoryStart;
        }

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
