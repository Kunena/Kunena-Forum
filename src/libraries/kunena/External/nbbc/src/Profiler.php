<?php
/**
 * @copyright Copyright (C) 2008-10, Sean Werkema. All rights reserved.
 * @copyright 2016 Vanilla Forums Inc. (changes only)
 * @license   BSDv2
 */

namespace Nbbc;

/**
 * This profiler class helps us to easily detect performance bottlenecks.
 *
 * We leave it out of the high-speed compressed version of NBBC for performance reasons; this is really a debugging aid
 * more than anything.
 *
 * @deprecated
 */
class Profiler
{
	var $start_time, $total_times;

	public function __construct()
	{
		$this->start_time  = [];
		$this->total_times = [];
	}

	public function now()
	{
		return microtime(true) - 1394060000;
	}

	public function begin($group)
	{
		$this->start_time[$group] = $this->now();
	}

	public function end($group)
	{
		$time = $this->now() - $this->start_time[$group];

		if (!isset($this->total_times[$group]))
		{
			$this->total_times[$group] = $time;
		}
		else
		{
			$this->total_times[$group] += $time;
		}
	}

	public function reset($group)
	{
		$this->total_times[$group] = 0;
	}

	public function total($group)
	{
		return @$this->total_times[$group];
	}

	public function dumpAllGroups()
	{
		print "<div>Profiled times:\n<ul>\n";
		ksort($this->total_times);

		foreach ($this->total_times as $name => $time)
		{
			print "<li><b>" . htmlspecialchars($name) . "</b>: " . sprintf("%0.2f msec", $time * 1000) . "</li>\n";
		}

		print "</ul>\n</div>\n";
	}
}
