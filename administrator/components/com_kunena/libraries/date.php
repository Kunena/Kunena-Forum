<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.utilities.date' );

class KunenaDate extends JDate {
	public static function getInstance($date = 'now', $tzOffset = 0) {
		return new KunenaDate($date, $tzOffset);
	}

	public function diff($d2 = 'now', $week = false) {
		if (class_exists('DateTime') && method_exists('DateTime', 'diff')) {
			// PHP 5.3:
			$d1 = new DateTime($this->toISO8601());
			$d2 = new DateTime(is_numeric($d2) ? date('c', $d2) : $d2);
			$interval = $d1->diff($d2);
			$diff = array('year'=>$interval->y, 'month'=>$interval->m, 'week'=>intval($interval->d/7), 'day'=>$interval->d,
				'hour'=>$interval->h, 'minute'=>$interval->i, 'second'=>$interval->s);

			if ($week) {
				$diff ['day'] = $interval->d - $diff ['week']*7;
			} else {
				unset($diff ['week']);
			}
			return $diff;
		}

		/* compares two timestamps and returns array with differencies (year, month, day, hour, minute, second) */
		/* Taken and modified from: http://www.php.net/manual/en/function.mktime.php (davix 06-Oct-2009 07:39) */
		$d1 = $this->toUnix();
		$d2 = ($d2 instanceof JDate) ? $d2->toUnix() : JFactory::getDate($d2)->toUnix();
		// TODO: support future time
		if ($d1 < $d2) {
			$d1 = $d2;
			$d2 = $this->toUnix();
		}
		$d1 = date_parse ( date ( "Y-m-d H:i:s", $d1 ) );
		$d2 = date_parse ( date ( "Y-m-d H:i:s", $d2 ) );
		//seconds
		if ($d1 ['second'] >= $d2 ['second']) {
			$diff ['second'] = $d1 ['second'] - $d2 ['second'];
		} else {
			$d1 ['minute'] --;
			$diff ['second'] = 60 - $d2 ['second'] + $d1 ['second'];
		}
		//minutes
		if ($d1 ['minute'] >= $d2 ['minute']) {
			$diff ['minute'] = $d1 ['minute'] - $d2 ['minute'];
		} else {
			$d1 ['hour'] --;
			$diff ['minute'] = 60 - $d2 ['minute'] + $d1 ['minute'];
		}
		//hours
		if ($d1 ['hour'] >= $d2 ['hour']) {
			$diff ['hour'] = $d1 ['hour'] - $d2 ['hour'];
		} else {
			$d1 ['day'] --;
			$diff ['hour'] = 24 - $d2 ['hour'] + $d1 ['hour'];
		}
		//days
		if ($d1 ['day'] >= $d2 ['day']) {
			$diff ['day'] = $d1 ['day'] - $d2 ['day'];
		} else {
			$d1 ['month'] --;
			$diff ['day'] = date ( "t", $this->toUnix() ) - $d2 ['day'] + $d1 ['day'];
		}
		if ($week) {
			$diff ['week'] = intval($diff ['day']/7);
			$diff ['day'] -= $diff ['week']*7;
		}
		//months
		if ($d1 ['month'] >= $d2 ['month']) {
			$diff ['month'] = $d1 ['month'] - $d2 ['month'];
		} else {
			$d1 ['year'] --;
			$diff ['month'] = 12 - $d2 ['month'] + $d1 ['month'];
		}
		//years
		$diff ['year'] = $d1 ['year'] - $d2 ['year'];

		return array_reverse($diff);
	}

	public function toTimeAgo() {
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		$chunks = array (
			'year' => array (JText::_('COM_KUNENA_DATE_YEAR'), JText::_('COM_KUNENA_DATE_YEARS') ),
			'month' => array (JText::_('COM_KUNENA_DATE_MONTH'), JText::_('COM_KUNENA_DATE_MONTHS') ),
			'week' => array (JText::_('COM_KUNENA_DATE_WEEK'), JText::_('COM_KUNENA_DATE_WEEKS') ),
			'day' => array (JText::_('COM_KUNENA_DATE_DAY'), JText::_('COM_KUNENA_DATE_DAYS') ),
			'hour' => array (JText::_('COM_KUNENA_DATE_HOUR'), JText::_('COM_KUNENA_DATE_HOURS') ),
			'minute' => array (JText::_('COM_KUNENA_DATE_MINUTE'), JText::_('COM_KUNENA_DATE_MINUTES') ) );

		// we only want to output two chunks of time here, eg: "x years, xx months" or "x days, xx hours"
		$tick = 0;
		$output = '';
		$diff = self::diff('now', true);
		foreach ($diff as $name=>$count) {
			if (!$count || !isset($chunks[$name])) continue;
			$output .= ($count == 1) ? " 1 {$chunks[$name][0]}" : " {$count} {$chunks[$name][1]}";
			if (2 == ++$tick) break;
		}
		if (!$output) {
			$output .= '0 '.JText::_('COM_KUNENA_DATE_MINUTES');
		}
		$output = str_replace ( '%time%', trim($output), JText::_('COM_KUNENA_TIME_SINCE') );
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $output;
	}

	public function toTimezone() {
		if (version_compare(JVERSION, '1.6', '>')) {
			$timezone = $this->getOffsetFromGMT(true);
		} else {
			$timezone = $this->getOffset();
		}
		return sprintf('%+d:%02d', $timezone, ($timezone*60)%60);
	}

	public function toSpan($mode = 'datetime_today', $title = 'ago', $offset=false) {
		return '<span class="kdate" title="'.$this->toKunena($title, $offset).'">'.$this->toKunena($mode, $offset).'</span>';
	}

	public function toKunena($mode = 'datetime_today', $offset=false) {
		if (preg_match ( '/^config_/', $mode ) == 1) {
			$option = substr ( $mode, 7 );
			$mode = KunenaFactory::getConfig ()->$option;
		}
		$modearr = explode ( '_', $mode );
		switch (strtolower ( $modearr [0] )) {
			case 'none' :
				return '';
			case 'time' :
				$usertime_format = JText::_('COM_KUNENA_DT_TIME_FMT');
				$today_format = JText::_('COM_KUNENA_DT_TIME_FMT');
				$yesterday_format = JText::_('COM_KUNENA_DT_TIME_FMT');
				break;
			case 'date' :
				$usertime_format = JText::_('COM_KUNENA_DT_DATE_FMT');
				$today_format = JText::_('COM_KUNENA_DT_DATE_TODAY_FMT');
				$yesterday_format = JText::_('COM_KUNENA_DT_DATE_YESTERDAY_FMT');
				break;
			case 'ago' :
				if ($this->toFormat('%Y')<1902) return JText::_('COM_KUNENA_DT_DATETIME_UNKNOWN');
				return self::toTimeAgo ( $this->toUnix() );
				break;
			case 'datetime':
				$usertime_format = JText::_('COM_KUNENA_DT_DATETIME_FMT');
				$today_format = JText::_('COM_KUNENA_DT_DATETIME_TODAY_FMT');
				$yesterday_format = JText::_('COM_KUNENA_DT_DATETIME_YESTERDAY_FMT');
				break;
			default:
				$usertime_format = $mode;
				$today_format = $mode;
				$yesterday_format = $mode;
		}
		if ($this->toFormat('%Y')<1902) return JText::_('COM_KUNENA_DT_DATETIME_UNKNOWN');

		if ($offset === false) {
			$app = JFactory::getApplication ();
			$my = JFactory::getUser();
			if ($my->id) $offset = $my->getParam('timezone', $app->getCfg ( 'offset', 0 ));
			else $offset = $app->getCfg ( 'offset', 0 );
			if ($offset == 'utc') $offset = 0;
		}
		$now = JFactory::getDate ( 'now' );
		if (version_compare(JVERSION, '1.6', '<') || is_numeric($offset)) {
			// Joomla 1.5 and Kunena timezone
			$this->setOffset($offset);
			$now->setOffset($offset);
		} else {
			// Joomla 1.6 support
			try {
				$offset = new DateTimeZone($offset);
			} catch (Exception $e) {
				$offset = new DateTimeZone('UTC');
			}
			$this->setTimezone($offset);
			$now->setTimezone($offset);
		}
		// Today and Yesterday?
		if (end($modearr) == 'today') {
			$now = @getdate ( $now->toUnix(true) );
			$then = @getdate ( $this->toUnix(true) );

			// Same day of the year, same year.... Today!
			if ($then ['yday'] == $now ['yday'] &&
				$then ['year'] == $now ['year'])
				$usertime_format = $today_format;

			// Day-of-year is one less and same year, or it's the first of the year and that's the last of the year...
			if (($then ['yday'] == $now ['yday'] - 1 && $then ['year'] == $now ['year']) ||
				($now ['yday'] == 0 && $then ['year'] == $now ['year'] - 1) && $then ['mon'] == 12 && $then ['mday'] == 31)
				$usertime_format = $yesterday_format;
		}
		return $this->toFormat ( $usertime_format, true );
	}
}
