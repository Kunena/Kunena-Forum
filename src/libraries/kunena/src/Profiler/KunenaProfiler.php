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

use Joomla\CMS\Factory;
use Joomla\CMS\Profiler\Profiler;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Monitor\DebugMonitor;
use Throwable;

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
    protected static mixed $_instances = [];

    /**
     * @var     boolean
     * @since   Kunena 8.0
     */
    protected static mixed $cssPrinted = false;

    /**
     * @var     array| KunenaProfilerItem[]
     * @since   Kunena 6.0
     */
    protected array $_heap = [];

    /**
     * Collect the database queries which were recorded by the query monitor.
     *
     * @return  array  List of arrays: sql, ms (duration), at (ms since the start of the request), caller, memory (bytes), dup (times the same statement ran), similar (times the statement ran with any values).
     *
     * @since   Kunena 8.0
     */
    protected function collectQueries(): array
    {
        $queries = [];

        try {
            $db      = Factory::getContainer()->get(DatabaseInterface::class);
            $monitor = method_exists($db, 'getMonitor') ? $db->getMonitor() : null;

            if (!$monitor || !method_exists($monitor, 'getLogs')) {
                return [];
            }

            $logs    = $monitor->getLogs();
            $timings = $monitor->getTimings();
            $stacks  = method_exists($monitor, 'getCallStacks') ? $monitor->getCallStacks() : [];
            $mem     = method_exists($monitor, 'getMemoryLogs') ? $monitor->getMemoryLogs() : [];
            $origin  = (float) ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true));
            $seen    = [];
            $exact   = [];

            foreach ($logs as $i => $sql) {
                $start        = $timings[$i * 2] ?? null;
                $stop         = $timings[$i * 2 + 1] ?? $start;
                $norm         = trim((string) preg_replace('/\s+/', ' ', (string) preg_replace(["/'(?:[^'\\\\]|\\\\.)*'/", '/\b\d+\b/'], '?', (string) $sql)));
                $seen[$norm]  = ($seen[$norm] ?? 0) + 1;
                $text         = trim((string) preg_replace('/\s+/', ' ', (string) $sql));
                $exact[$text] = ($exact[$text] ?? 0) + 1;

                $queries[] = [
                    'sql'    => trim((string) preg_replace('/\s+/', ' ', (string) $sql)),
                    'ms'     => $start !== null ? round(($stop - $start) * 1000, 3) : 0.0,
                    'at'     => $start !== null ? round(($start - $origin) * 1000, 3) : 0.0,
                    'caller' => self::queryCaller($stacks[$i] ?? []),
                    'stack'  => self::queryStack($stacks[$i] ?? []),
                    'memory' => isset($mem[$i * 2 + 1], $mem[$i * 2]) ? (int) ($mem[$i * 2 + 1] - $mem[$i * 2]) : 0,
                    'norm'   => $norm,
                    'text'   => $text,
                ];
            }

            foreach ($queries as &$query) {
                // dup: the same statement ran more than once; similar: the same statement with other values.
                $query['dup']     = $exact[$query['text']];
                $query['similar'] = $seen[$query['norm']];
                unset($query['norm'], $query['text']);
            }

            unset($query);
        } catch (Throwable $e) {
            return [];
        }

        return $queries;
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
     * Css of the profiler panel.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function panelCss(): string
    {
        return <<<'CSS'
.kprofiler{--kp-bg:#fff;--kp-fg:#1f2933;--kp-muted:#6b7785;--kp-line:#e3e8ee;--kp-head:#f5f7fa;--kp-bar:#4c8bf5;--kp-warn:#e8a317;--kp-bad:#d64545;--kp-func:#2f6fdd;--kp-layout:#8a4fd6;--kp-other:#6b7785;--kp-sql:#1a8f6a;
margin:24px 0;border:1px solid var(--kp-line);border-radius:10px;background:var(--kp-bg);color:var(--kp-fg);font:13px/1.45 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06)}
@media (prefers-color-scheme:dark){.kprofiler{--kp-bg:#1b1f24;--kp-fg:#e6e9ee;--kp-muted:#9aa5b1;--kp-line:#2e353d;--kp-head:#232930;--kp-bar:#6aa3ff;--kp-func:#6aa3ff;--kp-layout:#b48cf2;--kp-sql:#4cc9a0}}
.kprofiler summary{display:flex;flex-wrap:wrap;align-items:center;gap:6px 16px;padding:10px 14px;cursor:pointer;background:var(--kp-head);list-style:none}
.kprofiler summary::-webkit-details-marker{display:none}
.kprofiler summary::before{content:"\25B8";color:var(--kp-muted);transition:transform .15s}
.kprofiler[open] summary::before{transform:rotate(90deg)}
.kprofiler .kp-title{font-weight:600;margin-right:auto}
.kprofiler .kp-chip{padding:2px 9px;border-radius:999px;border:1px solid var(--kp-line);background:var(--kp-bg);color:var(--kp-muted);font-variant-numeric:tabular-nums;white-space:nowrap}
.kprofiler .kp-chip b{color:var(--kp-fg);font-weight:600}
.kprofiler .kp-chip-warn{border-color:var(--kp-warn)}
.kprofiler .kp-tabs{display:flex;flex-wrap:wrap;align-items:center;gap:4px;padding:6px 10px 0;border-bottom:1px solid var(--kp-line);background:var(--kp-head)}
.kprofiler .kp-spacer{flex:1}
.kprofiler button{padding:4px 10px;border:1px solid var(--kp-line);border-radius:6px;background:var(--kp-bg);color:var(--kp-muted);font:inherit;cursor:pointer}
.kprofiler button:hover{color:var(--kp-fg);background:var(--kp-head)}
.kprofiler .kp-tab{border-bottom:0;border-radius:6px 6px 0 0;padding:6px 14px;margin-bottom:-1px}
.kprofiler .kp-tab.kp-on{color:var(--kp-fg);background:var(--kp-bg);font-weight:600;border-color:var(--kp-line)}
.kprofiler .kp-tab span{margin-left:4px;color:var(--kp-muted);font-weight:400}
.kprofiler .kp-tabs .kp-copy{margin-bottom:6px}
.kprofiler .kp-tools{display:flex;flex-wrap:wrap;align-items:center;gap:8px;padding:8px 12px;border-bottom:1px solid var(--kp-line)}
.kprofiler .kp-tools input{flex:1 1 160px;min-width:120px;padding:5px 9px;border:1px solid var(--kp-line);border-radius:6px;background:var(--kp-bg);color:var(--kp-fg);font:inherit}
.kprofiler .kp-tools .kp-on{color:var(--kp-fg);border-color:var(--kp-bar);box-shadow:inset 0 0 0 1px var(--kp-bar)}
.kprofiler .kp-count,.kprofiler .kp-qcount{color:var(--kp-muted);margin-left:auto;font-variant-numeric:tabular-nums}
.kprofiler .kp-hint{color:var(--kp-muted);font-size:12px}
.kprofiler .kp-scroll{max-height:460px;overflow:auto}
.kprofiler table{width:100%;border-collapse:collapse;font-variant-numeric:tabular-nums}
.kprofiler th{position:sticky;top:0;background:var(--kp-head);text-align:right;font-weight:600;color:var(--kp-muted);padding:7px 12px;border-bottom:1px solid var(--kp-line);white-space:nowrap;user-select:none;z-index:1}
.kprofiler th[data-sort],.kprofiler th[data-qsort]{cursor:pointer}.kprofiler th[data-sort]:hover,.kprofiler th[data-qsort]:hover{color:var(--kp-fg)}
.kprofiler th .kp-arrow{display:inline-block;width:10px;margin-left:4px}
.kprofiler th.kp-asc .kp-arrow::after{content:"\25B2"}.kprofiler th.kp-desc .kp-arrow::after{content:"\25BC"}
.kprofiler td{padding:6px 12px;border-bottom:1px solid var(--kp-line);text-align:right;white-space:nowrap;vertical-align:top}
.kprofiler th.kp-left,.kprofiler td:first-child{text-align:left}
.kprofiler .kp-items td:first-child{white-space:normal;word-break:break-word;width:100%}
.kprofiler tr:last-child td{border-bottom:0}
.kprofiler .kp-item{cursor:pointer}
.kprofiler tbody tr:hover{background:var(--kp-head)}
.kprofiler tbody tr[hidden]{display:none}
.kprofiler .kp-detail td{background:var(--kp-head);white-space:normal;text-align:left;padding:10px 16px;cursor:default}
.kprofiler .kp-detail h4{margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--kp-muted)}
.kprofiler .kp-detail .kp-cols{display:flex;flex-wrap:wrap;gap:6px 28px}
.kprofiler .kp-detail ul{margin:0;padding:0;list-style:none}
.kprofiler .kp-detail li{display:flex;justify-content:space-between;gap:16px;padding:1px 0}
.kprofiler .kp-detail li b{font-weight:600}
.kprofiler .kp-mini{width:auto}
.kprofiler .kp-mini td,.kprofiler .kp-mini th{padding:2px 10px;border:0;text-align:right;position:static;background:none}
.kprofiler .kp-tag{display:inline-block;min-width:44px;margin-right:8px;padding:0 6px;border-radius:4px;background:var(--kp-head);border:1px solid var(--kp-line);font-size:11px;text-align:center;color:var(--kp-other)}
.kprofiler .kp-tag.kp-func{color:var(--kp-func)}.kprofiler .kp-tag.kp-layout{color:var(--kp-layout)}
.kprofiler .kp-warn{color:var(--kp-warn);font-weight:600}.kprofiler .kp-bad{color:var(--kp-bad);font-weight:600}
.kprofiler .kp-meter{display:block;width:120px;height:6px;border-radius:3px;background:var(--kp-line);overflow:hidden}
.kprofiler .kp-meter i{display:block;height:100%;background:var(--kp-bar)}
.kprofiler .kp-sql{text-align:left;white-space:normal;width:100%}
.kprofiler .kp-sql code{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font:12px/1.45 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;word-break:break-all;color:var(--kp-sql);cursor:pointer}
.kprofiler .kp-query.kp-open .kp-sql code{display:block;-webkit-line-clamp:unset}
.kprofiler .kp-stack{display:none;margin:6px 0 0;padding-left:22px;color:var(--kp-muted);font:12px/1.5 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace}
.kprofiler .kp-query.kp-open .kp-stack{display:block}
.kprofiler .kp-caller{color:var(--kp-muted);font-size:12px;margin-top:2px}
.kprofiler .kp-dup{display:inline-block;padding:0 6px;border-radius:999px;background:var(--kp-warn);color:#1b1f24;font-size:11px;font-weight:600}
.kprofiler .kp-sim{display:inline-block;padding:0 6px;border-radius:999px;border:1px solid var(--kp-line);color:var(--kp-muted);font-size:11px}
.kprofiler .kp-empty{padding:18px;color:var(--kp-muted)}
.kprofiler .kp-tl{max-height:460px;overflow:auto;padding:8px 12px}
.kprofiler .kp-tl-row{display:flex;align-items:center;gap:10px;padding:2px 0}
.kprofiler .kp-tl-label{flex:0 0 220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px}
.kprofiler .kp-tl-track{position:relative;flex:1;height:14px;background:var(--kp-head);border-radius:3px}
.kprofiler .kp-tl-track i{position:absolute;top:2px;bottom:2px;min-width:2px;border-radius:2px;background:var(--kp-other);opacity:.85}
.kprofiler .kp-tl-track i.kp-func{background:var(--kp-func)}.kprofiler .kp-tl-track i.kp-layout{background:var(--kp-layout)}.kprofiler .kp-tl-track i.kp-sql{background:var(--kp-sql)}
.kprofiler .kp-tl-ruler{display:flex;justify-content:space-between;margin-left:230px;color:var(--kp-muted);font-size:11px;padding-bottom:4px}
CSS;
    }

    /**
     * Script of the profiler panel: tabs, sorting, filtering, call details, queries, timeline, copy buttons and the state.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function panelScript(): string
    {
        return <<<'JS'
(function () {
    var KEY = "kunena-profiler";
    function load() { try { return JSON.parse(localStorage.getItem(KEY) || "{}"); } catch (e) { return {}; } }
    function save(s) { try { localStorage.setItem(KEY, JSON.stringify(s)); } catch (e) {} }
    function esc(t) { return String(t).replace(/[&<>"]/g, function (c) { return {"&": "&amp;", "<": "&lt;", ">": "&gt;", "\"": "&quot;"}[c]; }); }
    function fmt(ms) { return ms >= 1000 ? (ms / 1000).toFixed(2) + " s" : ms.toFixed(1) + " ms"; }
    function shortName(n) { return n.replace(/^function /, "").replace(/^render layout\s*/, "").replace(/^(?:\\?[A-Za-z0-9_]+\\)+/, "").replace(/^'|'$/g, ""); }
    function copy(text, btn) {
        var done = function () { var t = btn.textContent; btn.textContent = "Copied"; setTimeout(function () { btn.textContent = t; }, 1200); };
        if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(text).then(done); return; }
        var ta = document.createElement("textarea"); ta.value = text; document.body.appendChild(ta); ta.select();
        try { document.execCommand("copy"); } catch (e) {} document.body.removeChild(ta); done();
    }

    function init(el) {
        var data = JSON.parse(el.querySelector(".kp-data").textContent), st = load();
        st.tab = st.tab || "items"; st.sort = st.sort || "total"; st.dir = st.dir || -1; st.types = st.types || {func: 1, layout: 1, other: 1};
        st.text = st.text || ""; st.qtext = st.qtext || ""; st.qsort = st.qsort || "order"; st.qdir = st.qdir || 1; st.qslow = !!st.qslow; st.qdup = !!st.qdup; st.qsim = !!st.qsim;
        if (st.open) { el.open = true; }
        function persist() { st.open = el.open; save(st); }
        el.addEventListener("toggle", persist);

        /* tabs */
        var tabs = [].slice.call(el.querySelectorAll(".kp-tab")), panes = [].slice.call(el.querySelectorAll(".kp-pane")), drawn = false;
        function showTab(name) {
            st.tab = name;
            tabs.forEach(function (t) { t.classList.toggle("kp-on", t.dataset.tab === name); });
            panes.forEach(function (p) { p.hidden = p.dataset.pane !== name; });
            if (name === "timeline" && !drawn) { drawTimeline(); drawn = true; }
            persist();
        }
        tabs.forEach(function (t) { t.addEventListener("click", function () { showTab(t.dataset.tab); }); });

        /* items */
        var tbody = el.querySelector(".kp-items tbody"), rows = [].slice.call(tbody.querySelectorAll(".kp-item")), filter = el.querySelector(".kp-filter"),
            count = el.querySelector(".kp-count"), heads = [].slice.call(el.querySelectorAll("th[data-sort]")), chips = [].slice.call(el.querySelectorAll(".kp-type"));
        filter.value = st.text;
        function cmp(a, b) {
            var k = st.sort;
            if (k === "name") { return st.dir * a.dataset.name.localeCompare(b.dataset.name); }
            return st.dir * ((parseFloat(a.dataset[k]) || 0) - (parseFloat(b.dataset[k]) || 0));
        }
        function closeDetails() { [].slice.call(tbody.querySelectorAll(".kp-detail")).forEach(function (r) { r.parentNode.removeChild(r); }); }
        function apply() {
            closeDetails();
            rows.sort(cmp);
            var shown = 0, text = st.text.toLowerCase();
            rows.forEach(function (r) {
                tbody.appendChild(r);
                var ok = st.types[r.dataset.type] && (!text || r.dataset.name.indexOf(text) > -1);
                r.hidden = !ok; if (ok) { shown++; }
            });
            count.textContent = shown + " / " + rows.length;
            heads.forEach(function (h) { h.classList.toggle("kp-asc", h.dataset.sort === st.sort && st.dir > 0); h.classList.toggle("kp-desc", h.dataset.sort === st.sort && st.dir < 0); });
            chips.forEach(function (c) { c.classList.toggle("kp-on", !!st.types[c.dataset.type]); });
        }
        heads.forEach(function (h) { h.addEventListener("click", function () { var k = h.dataset.sort; if (st.sort === k) { st.dir = -st.dir; } else { st.sort = k; st.dir = k === "name" ? 1 : -1; } apply(); persist(); }); });
        chips.forEach(function (c) { c.addEventListener("click", function () { st.types[c.dataset.type] = st.types[c.dataset.type] ? 0 : 1; apply(); persist(); }); });
        filter.addEventListener("input", function () { st.text = filter.value; apply(); persist(); });

        tbody.addEventListener("click", function (e) {
            var tr = e.target.closest("tr.kp-item"); if (!tr) { return; }
            var next = tr.nextElementSibling;
            if (next && next.classList.contains("kp-detail")) { next.parentNode.removeChild(next); return; }
            var item = data.items[parseInt(tr.dataset.idx, 10)], html = "<div class=\"kp-cols\">", callers = Object.keys(item.callers || {});
            html += "<div><h4>Called by</h4><ul>";
            if (!callers.length) { html += "<li>-</li>"; }
            callers.sort(function (a, b) { return item.callers[b] - item.callers[a]; }).slice(0, 12).forEach(function (c) {
                html += "<li><span>" + esc(c ? shortName(c) : "(top level)") + "</span><b>" + item.callers[c] + "x</b></li>";
            });
            html += "</ul></div><div><h4>Calls (" + item.calls + (item.log.length < item.calls ? ", first " + item.log.length : "") + ")</h4><table class=\"kp-mini\"><thead><tr><th>#</th><th>At</th><th>Time</th><th>Called by</th></tr></thead><tbody>";
            item.log.slice(0, 60).forEach(function (c, i) { html += "<tr><td>" + (i + 1) + "</td><td>" + fmt(c[0]) + "</td><td>" + fmt(c[1]) + "</td><td>" + esc(c[2] ? shortName(c[2]) : "-") + "</td></tr>"; });
            html += "</tbody></table></div></div>";
            var d = document.createElement("tr"); d.className = "kp-detail"; d.innerHTML = "<td colspan=\"6\">" + html + "</td>";
            tr.parentNode.insertBefore(d, tr.nextSibling);
        });

        /* queries */
        var qbody = el.querySelector(".kp-queries tbody"), qrows = qbody ? [].slice.call(qbody.querySelectorAll(".kp-query")) : [], qfilter = el.querySelector(".kp-qfilter"),
            qcount = el.querySelector(".kp-qcount"), qheads = [].slice.call(el.querySelectorAll("th[data-qsort]")), qtoggles = [].slice.call(el.querySelectorAll(".kp-qtoggle"));
        qfilter.value = st.qtext;
        function qapply() {
            if (!qbody) { qcount.textContent = "0"; return; }
            qrows.sort(function (a, b) { return st.qdir * ((parseFloat(a.dataset[st.qsort]) || 0) - (parseFloat(b.dataset[st.qsort]) || 0)); });
            var shown = 0, text = st.qtext.toLowerCase(), time = 0;
            qrows.forEach(function (r) {
                qbody.appendChild(r);
                var ok = (!text || r.textContent.toLowerCase().indexOf(text) > -1) && (!st.qslow || parseFloat(r.dataset.ms) > 5) && (!st.qdup || parseInt(r.dataset.dup, 10) > 1) && (!st.qsim || parseInt(r.dataset.similar, 10) > 1);
                r.hidden = !ok; if (ok) { shown++; time += parseFloat(r.dataset.ms) || 0; }
            });
            qcount.textContent = shown + " / " + qrows.length + " queries, " + time.toFixed(1) + " ms";
            qheads.forEach(function (h) { h.classList.toggle("kp-asc", h.dataset.qsort === st.qsort && st.qdir > 0); h.classList.toggle("kp-desc", h.dataset.qsort === st.qsort && st.qdir < 0); });
            qtoggles.forEach(function (t) { t.classList.toggle("kp-on", t.dataset.q === "slow" ? st.qslow : (t.dataset.q === "dup" ? st.qdup : st.qsim)); });
        }
        qheads.forEach(function (h) { h.addEventListener("click", function () { var k = h.dataset.qsort; if (st.qsort === k) { st.qdir = -st.qdir; } else { st.qsort = k; st.qdir = k === "ms" ? -1 : 1; } qapply(); persist(); }); });
        qtoggles.forEach(function (t) { t.addEventListener("click", function () { if (t.dataset.q === "slow") { st.qslow = !st.qslow; } else if (t.dataset.q === "dup") { st.qdup = !st.qdup; } else { st.qsim = !st.qsim; } qapply(); persist(); }); });
        qfilter.addEventListener("input", function () { st.qtext = qfilter.value; qapply(); persist(); });
        if (qbody) { qbody.addEventListener("click", function (e) { var c = e.target.closest("code"); if (c) { c.closest("tr").classList.toggle("kp-open"); } }); }
        el.querySelector(".kp-copy-sql").addEventListener("click", function () {
            copy(data.queries.map(function (q, i) { return "-- #" + (i + 1) + " " + q.ms + " ms" + (q.caller ? " " + q.caller : "") + "\n" + q.sql + ";"; }).join("\n\n"), this);
        });

        /* timeline */
        function drawTimeline() {
            var box = el.querySelector(".kp-tl"), total = 1, list = [], html = "";
            /* The offsets count from the start of the request (the Joomla startup is included), so the scale ends with the last call. */
            data.items.forEach(function (it) { it.log.forEach(function (c) { total = Math.max(total, c[0] + c[1]); }); });
            data.queries.forEach(function (q) { total = Math.max(total, q.at + q.ms); });
            data.items.forEach(function (it) { if (it.name !== "Total Time" && it.log.length && it.total_ms >= 0.5) { list.push(it); } });
            list.sort(function (a, b) { return a.log[0][0] - b.log[0][0]; });
            list = list.slice(0, 70);
            html += "<div class=\"kp-tl-ruler\"><span>0</span><span>" + fmt(total / 4) + "</span><span>" + fmt(total / 2) + "</span><span>" + fmt(total * 3 / 4) + "</span><span>" + fmt(total) + "</span></div>";
            function bar(at, ms, cls, tip) {
                return "<i class=\"" + cls + "\" style=\"left:" + Math.min(99.5, at / total * 100).toFixed(2) + "%;width:" + Math.max(0.15, ms / total * 100).toFixed(2) + "%\" title=\"" + esc(tip) + "\"></i>";
            }
            if (data.queries.length) {
                html += "<div class=\"kp-tl-row\"><div class=\"kp-tl-label\"><b>SQL queries</b> (" + data.queries.length + ")</div><div class=\"kp-tl-track\">";
                data.queries.forEach(function (q) { html += bar(q.at, q.ms, "kp-sql", q.ms + " ms: " + q.sql.slice(0, 120)); });
                html += "</div></div>";
            }
            list.forEach(function (it) {
                html += "<div class=\"kp-tl-row\"><div class=\"kp-tl-label\" title=\"" + esc(it.name) + "\">" + esc(shortName(it.name)) + "</div><div class=\"kp-tl-track\">";
                it.log.forEach(function (c) { html += bar(c[0], c[1], "kp-" + it.type, shortName(it.name) + " " + fmt(c[1]) + " at " + fmt(c[0])); });
                html += "</div></div>";
            });
            box.innerHTML = html || "<p class=\"kp-empty\">No calls were recorded.</p>";
        }

        /* export */
        [].forEach.call(el.querySelectorAll(".kp-copy"), function (b) {
            b.addEventListener("click", function () {
                if (b.dataset.format === "json") { copy(JSON.stringify(data, null, 2), b); return; }
                var head = ["name", "type", "calls", "own_ms", "total_ms", "memory_bytes"];
                copy([head.join(",")].concat(data.items.map(function (r) {
                    return head.map(function (k) { var v = String(r[k]); return /[",\n]/.test(v) ? "\"" + v.replace(/"/g, "\"\"") + "\"" : v; }).join(",");
                })).join("\n"), b);
            });
        });

        apply(); qapply(); showTab(st.tab);
    }
    function run() { [].forEach.call(document.querySelectorAll(".kprofiler"), init); }
    if (document.readyState === "loading") { document.addEventListener("DOMContentLoaded", run); } else { run(); }
})();
JS;
    }

    /**
     * Find the code which ran a query: the first frame of the call stack outside the database package.
     *
     * @param   array  $stack  Frames of debug_backtrace().
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function queryCaller(array $stack): string
    {
        foreach ($stack as $frame) {
            $class = (string) ($frame['class'] ?? '');

            if ($class !== '' && (str_starts_with($class, 'Joomla\\Database\\') || str_contains($class, 'DebugMonitor'))) {
                continue;
            }

            if (!isset($frame['function'])) {
                continue;
            }

            $where = isset($frame['file']) ? basename((string) $frame['file']) . ':' . ($frame['line'] ?? '?') : '';
            $short = $class !== '' ? substr(strrchr('\\' . $class, '\\'), 1) . ($frame['type'] ?? '::') . $frame['function'] : $frame['function'];

            return $short . '()' . ($where !== '' ? ' ' . $where : '');
        }

        return '';
    }

    /**
     * The calls which led to a query, the innermost first (frames of the database package are left out).
     *
     * @param   array  $stack  Frames of debug_backtrace().
     *
     * @return  array  Lines like "Class::method() File.php:123".
     *
     * @since   Kunena 8.0
     */
    protected static function queryStack(array $stack): array
    {
        $lines = [];

        foreach ($stack as $frame) {
            $class = (string) ($frame['class'] ?? '');

            if (!isset($frame['function']) || ($class !== '' && (str_starts_with($class, 'Joomla\\Database\\') || str_contains($class, 'DebugMonitor')))) {
                continue;
            }

            $where   = isset($frame['file']) ? basename((string) $frame['file']) . ':' . ($frame['line'] ?? '?') : '';
            $lines[] = ($class !== '' ? substr(strrchr('\\' . $class, '\\'), 1) . ($frame['type'] ?? '::') : '') . $frame['function'] . '()' . ($where !== '' ? ' ' . $where : '');

            if (\count($lines) >= 14) {
                break;
            }
        }

        return $lines;
    }

    /**
     * Render the collected profiler data as a collapsible html panel (debug mode only).
     *
     * Tabs: the profiled items (sortable, filterable, a click shows who called an item and the single calls),
     * the database queries of the page (duration, caller, slow and repeated queries) and a timeline of the calls.
     * The data can be copied as json or csv. The state (open, tab, sort, filters) is kept in the localStorage of
     * the browser. Styles and script are printed once per request.
     *
     * @param   string  $title  Title of the panel.
     *
     * @return  string  Html.
     *
     * @since   Kunena 8.0
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
        $queries  = $this->collectQueries();
        $sqlTime  = array_sum(array_column($queries, 'ms'));
        $repeated = \count(array_filter($queries, static fn ($q): bool => $q['dup'] > 1));
        $similar  = \count(array_filter($queries, static fn ($q): bool => $q['dup'] <= 1 && $q['similar'] > 1));

        $assets = '';

        if (!self::$cssPrinted) {
            self::$cssPrinted = true;
            $assets           = '<style>' . self::panelCss() . '</style><script>' . self::panelScript() . '</script>';
        }

        $rows = '';
        $data = [];

        foreach (array_values($items) as $idx => $item) {
            $name = (string) $item->name;
            $own  = $name === 'Total Time' ? 0.0 : $item->getInternalTime();
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

            $data[] = [
                'name'         => $name, 'type' => $type, 'calls' => (int) $item->calls, 'own_ms' => round($own * 1000, 3), 'total_ms' => round($tot * 1000, 3),
                'memory_bytes' => (int) $item->memory, 'callers' => (object) $item->callers, 'log' => $item->log,
            ];

            $rows .= '<tr class="kp-item" data-idx="' . $idx . '" data-name="' . $esc(strtolower($name)) . '" data-type="' . $type . '" data-calls="' . (int) $item->calls . '" data-own="' . round($own * 1000, 3)
                . '" data-total="' . round($tot * 1000, 3) . '" data-mem="' . (int) $item->memory . '">'
                . '<td title="' . $esc($name) . '"><span class="kp-tag kp-' . $type . '">' . $type . '</span>' . $esc($short) . '</td>'
                . '<td>' . (int) $item->calls . '</td>'
                . '<td' . $class . '>' . ($name === 'Total Time' ? '&mdash;' : $fmt($own)) . '</td>'
                . '<td>' . $fmt($tot) . '</td>'
                . '<td>' . $mem((float) $item->memory) . '</td>'
                . '<td><span class="kp-meter"><i style="width:' . number_format($share, 1, '.', '') . '%"></i></span></td></tr>';
        }

        $queryRows = '';

        foreach ($queries as $i => $query) {
            $cls = $query['ms'] > 20 ? ' kp-bad' : ($query['ms'] > 5 ? ' kp-warn' : '');
            $queryRows .= '<tr class="kp-query" data-ms="' . $query['ms'] . '" data-at="' . $query['at'] . '" data-order="' . $i . '" data-dup="' . $query['dup'] . '" data-similar="' . $query['similar'] . '">'
                . '<td>' . ($i + 1) . '</td><td>' . number_format($query['at'], 1) . ' ms</td>'
                . '<td class="' . trim($cls) . '">' . number_format($query['ms'], 2) . ' ms</td>'
                . '<td class="kp-sql"><code>' . $esc(mb_strimwidth($query['sql'], 0, 4000, '...')) . '</code>'
                . ($query['dup'] > 1 ? ' <span class="kp-dup" title="The same statement ran ' . $query['dup'] . ' times">x' . $query['dup'] . '</span>'
                    : ($query['similar'] > 1 ? ' <span class="kp-sim" title="The same statement ran ' . $query['similar'] . ' times with other values: load them with one query?">~' . $query['similar'] . '</span>' : ''))
                . ($query['caller'] !== '' ? '<div class="kp-caller">' . $esc($query['caller']) . '</div>' : '')
                . ($query['stack'] ? '<ol class="kp-stack">' . implode('', array_map(static fn ($line): string => '<li>' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</li>', $query['stack'])) . '</ol>' : '')
                . '</td></tr>';
        }

        $chips = '<span class="kp-chip" title="Time of the Kunena code (the Joomla startup is not included)">Kunena <b>' . $fmt($pageTime) . '</b></span>'
            . '<span class="kp-chip">Items <b>' . \count($items) . '</b></span>'
            . '<span class="kp-chip">Calls <b>' . $calls . '</b></span>'
            . '<span class="kp-chip">Queries <b>' . \count($queries) . '</b> &middot; ' . number_format($sqlTime, 1) . ' ms</span>'
            . ($repeated ? '<span class="kp-chip kp-chip-warn" title="The same statement ran more than once">Repeated <b>' . $repeated . '</b></span>' : '')
            . ($similar ? '<span class="kp-chip" title="The same statement ran with other values (one query for each value)">Similar <b>' . $similar . '</b></span>' : '')
            . '<span class="kp-chip">Peak memory <b>' . $esc($mem((float) memory_get_peak_usage(true))) . '</b></span>';

        $head = static function (string $key, string $label, bool $left = false): string {
            return '<th data-sort="' . $key . '"' . ($left ? ' class="kp-left"' : '') . ' title="Sort by ' . $label . '">' . $label . '<span class="kp-arrow"></span></th>';
        };

        $json = (string) json_encode(['page_ms' => round($pageTime * 1000, 3), 'items' => $data, 'queries' => $queries], JSON_INVALID_UTF8_SUBSTITUTE);

        return $assets
            . '<details class="kprofiler"><summary><span class="kp-title">' . $esc($title) . '</span>' . $chips . '</summary>'
            . '<div class="kp-tabs"><button type="button" class="kp-tab kp-on" data-tab="items">Items <span>' . \count($items) . '</span></button>'
            . '<button type="button" class="kp-tab" data-tab="queries">Queries <span>' . \count($queries) . '</span></button>'
            . '<button type="button" class="kp-tab" data-tab="timeline">Timeline</button>'
            . '<span class="kp-spacer"></span>'
            . '<button type="button" class="kp-copy" data-format="json">Copy JSON</button>'
            . '<button type="button" class="kp-copy" data-format="csv">Copy CSV</button></div>'
            // Items
            . '<section class="kp-pane" data-pane="items"><div class="kp-tools">'
            . '<input type="search" class="kp-filter" placeholder="Filter items" aria-label="Filter items">'
            . '<button type="button" class="kp-type kp-on" data-type="func">func</button>'
            . '<button type="button" class="kp-type kp-on" data-type="layout">layout</button>'
            . '<button type="button" class="kp-type kp-on" data-type="other">other</button>'
            . '<span class="kp-count"></span><span class="kp-hint">Click a row for its calls</span></div>'
            . '<div class="kp-scroll"><table class="kp-items"><thead><tr>'
            . $head('name', 'Item', true) . $head('calls', 'Calls') . $head('own', 'Own time') . $head('total', 'Total time') . $head('mem', 'Memory') . '<th>Share</th>'
            . '</tr></thead><tbody>' . $rows . '</tbody></table></div></section>'
            // Queries
            . '<section class="kp-pane" data-pane="queries" hidden><div class="kp-tools">'
            . '<input type="search" class="kp-qfilter" placeholder="Filter queries" aria-label="Filter queries">'
            . '<button type="button" class="kp-qtoggle" data-q="slow">Slow &gt; 5 ms</button>'
            . '<button type="button" class="kp-qtoggle" data-q="dup" title="The same statement ran more than once">Repeated</button>'
            . '<button type="button" class="kp-qtoggle" data-q="similar" title="The same statement with other values">Similar</button>'
            . '<span class="kp-qcount"></span>'
            . '<button type="button" class="kp-copy-sql">Copy SQL</button></div>'
            . ($queries ? '<div class="kp-scroll"><table class="kp-queries"><thead><tr>'
                . '<th data-qsort="order" class="kp-left">#</th><th data-qsort="at">At</th><th data-qsort="ms">Time</th><th class="kp-left">Query</th>'
                . '</tr></thead><tbody>' . $queryRows . '</tbody></table></div>'
                : '<p class="kp-empty">No queries were recorded. Enable the Joomla debug mode or the Kunena debug mode to record them.</p>')
            . '</section>'
            // Timeline
            . '<section class="kp-pane" data-pane="timeline" hidden><div class="kp-tl"></div></section>'
            . '<script type="application/json" class="kp-data">' . str_replace('</', '<\/', $json) . '</script></details>';
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

    /**
     * @param   string  $name  name
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function start(string $name): void
    {
        $item   = KunenaProfilerItem::getInstance($name);
        $parent = end($this->_heap);
        $item->start(microtime(true), $parent ? (string) $parent->name : '');
        $this->_heap[] = $item;
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
     * Make sure that the database queries of the page are recorded (Joomla debug mode records them already).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public function watchQueries(): void
    {
        try {
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            if (method_exists($db, 'getMonitor') && method_exists($db, 'setMonitor') && !$db->getMonitor()) {
                $db->setMonitor(new DebugMonitor(true));
            }
        } catch (Throwable $e) {
            // The queries are optional information of the panel.
        }
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
    protected static mixed $_instances = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    public mixed $start = [];
    /**
     * @var string
     * @since Kunena 6.0
     */
    public mixed $name = null;
    /**
     * @var float
     * @since Kunena 6.0
     */
    public mixed $total = null;
    /**
     * @var int
     * @since Kunena 6.0
     */
    public mixed $calls = null;
    /**
     * @var float
     * @since Kunena 6.0
     */
    private ?float $external = null;

    /**
     * @var     float  Net memory (bytes) which was allocated during the profiled calls.
     * @since   Kunena 8.0
     */
    public mixed $memory = 0.0;

    /**
     * @var     array  The single calls: [start in ms since the start of the request, duration in ms, name of the caller]; the first 300.
     * @since   Kunena 8.0
     */
    public array $log = [];

    /**
     * @var     array  Number of calls by the name of the profiled item which made the call.
     * @since   Kunena 8.0
     */
    public array $callers = [];

    /**
     * @var     array  Callers of the open calls.
     * @since   Kunena 8.0
     */
    protected array $openCallers = [];

    /**
     * @var     array  Memory usage at the start of the open calls.
     * @since   Kunena 8.0
     */
    protected array $memoryStart = [];

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
     * @return  float
     *
     * @since   Kunena 6.0
     */
    public function getInternalTime(): float
    {
        return $this->total - $this->external;
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
     * @param   float   $starttime  start time
     * @param   string  $parent     The caller, for the call counts.
     *
     * @return  float
     *
     * @since   Kunena 6.0
     */
    public function start($starttime, string $parent = ''): float
    {
        $this->calls++;
        $this->openCallers[]    = $parent;
        $this->callers[$parent] = ($this->callers[$parent] ?? 0) + 1;
        $this->start[]          = $starttime;
        $this->memoryStart[]    = memory_get_usage();

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
        $starttime   = array_pop($this->start);
        $memoryStart = array_pop($this->memoryStart);
        $parent      = (string) array_pop($this->openCallers);

        if ($memoryStart !== null) {
            $this->memory += memory_get_usage() - $memoryStart;
        }

        if (!$starttime || !$stoptime) {
            return 0.0;
        }

        $delta       = $stoptime - $starttime;
        $this->total += $delta;

        if (\count($this->log) < 300) {
            $origin      = (float) ($_SERVER['REQUEST_TIME_FLOAT'] ?? $starttime);
            $this->log[] = [round(($starttime - $origin) * 1000, 3), round($delta * 1000, 3), $parent];
        }

        return $delta;
    }
}
