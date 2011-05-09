<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.utilities.date' );
require_once(KPATH_SITE . '/lib/kunena.config.class.php');

class CKunenaTimeformat {

	function internalTime() {
		$now = new JDate();
		return $now->toUnix();
	}

	function showTimeSince($older_date, $newer_date = false) {
		$chunks = array (array (60 * 60 * 24 * 365, JText::_('COM_KUNENA_DATE_YEAR'), JText::_('COM_KUNENA_DATE_YEARS') ),
			array (60 * 60 * 24 * 30, JText::_('COM_KUNENA_DATE_MONTH'), JText::_('COM_KUNENA_DATE_MONTHS') ),
			array (60 * 60 * 24 * 7, JText::_('COM_KUNENA_DATE_WEEK'), JText::_('COM_KUNENA_DATE_WEEKS') ),
			array (60 * 60 * 24, JText::_('COM_KUNENA_DATE_DAY'), JText::_('COM_KUNENA_DATE_DAYS') ),
			array (60 * 60, JText::_('COM_KUNENA_DATE_HOUR'), JText::_('COM_KUNENA_DATE_HOURS') ),
			array (60, JText::_('COM_KUNENA_DATE_MINUTE'), JText::_('COM_KUNENA_DATE_MINUTES') ) );

		$now = new JDate();
		$newer_date = ($newer_date === false) ? $now->toUnix() : $newer_date;
		$since = $newer_date - $older_date;

		// no negatives!
		if ($since < 0) {
			return '???';
		}

		// we only want to output two chunks of time here, eg:
		// x years, xx months
		// x days, xx hours
		// so there's only two bits of calculation below:

		// step one: the first chunk
		for($i = 0, $j = count ( $chunks ); $i < $j; $i ++) {
			$seconds = $chunks [$i] [0];
			$name = $chunks [$i] [1];
			$names = $chunks [$i] [2];

			// finding the biggest chunk (if the chunk fits, break)
			if (($count = floor ( $since / $seconds )) != 0) {
				break;
			}
		}

		// set output var
		$output = ($count == 1) ? '1 ' . $name : $count . ' ' . $names;

		// step two: the second chunk
		if ($i + 1 < $j) {
			$seconds2 = $chunks [$i + 1] [0];
			$name2 = $chunks [$i + 1] [1];
			$names2 = $chunks [$i + 1] [2];

			if (($count2 = floor ( ($since - ($seconds * $count)) / $seconds2 )) != 0) {
				// add to output var
				$output .= ($count2 == 1) ? ', 1 ' . $name2 : ', ' . $count2 . ' ' . $names2;
			}
		}

		return str_replace ( '%time%', $output, JText::_('COM_KUNENA_TIME_SINCE') );
	}

	function showTimezone($timezone) {
		if (!is_numeric($timezone)) {
			// Joomla 1.6 support
			$date = JFactory::getDate ();
			$timezone = new DateTimeZone($timezone);
			$date->setTimezone($timezone);
			$timezone = $date->getOffset()/3600;
		}

		return sprintf('%+d:%02d', $timezone, ($timezone*60)%60);
	}

	// Format a time to make it look purdy.
	function showDate($time, $mode = 'datetime_today', $tz = 'kunena', $offset=null) {
		$app = & JFactory::getApplication ();
		$kunena_config = KunenaFactory::getConfig ();

		$date = JFactory::getDate ( $time );
		if ($offset === null || strtolower ($tz) != 'utc') {
			$offset = JFactory::getUser()->getParam('timezone', $app->getCfg ( 'offset', 0 ));
		}
		if (is_numeric($offset)) {
			$date->setOffset($offset);
		} else {
			// Joomla 1.6 support
			$offset = new DateTimeZone($offset);
			$date->setTimezone($offset);
		}
		if ($date->toFormat('%Y')<1902) return JText::_('COM_KUNENA_DT_DATETIME_UNKNOWN');
		if (preg_match ( '/^config_/', $mode ) == 1) {
			$option = substr ( $mode, 7 );
			$mode = $kunena_config->$option;
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
				return CKunenaTimeformat::showTimeSince ( $date->toUnix() );
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

		// Today and Yesterday?
		if ($modearr [count ( $modearr ) - 1] == 'today') {
			$now = JFactory::getDate ( 'now' );
			$now = @getdate ( $now->toUnix() );
			$then = @getdate ( $date->toUnix() );

			// Same day of the year, same year.... Today!
			if ($then ['yday'] == $now ['yday'] &&
				$then ['year'] == $now ['year'])
				$usertime_format = $today_format;

			// Day-of-year is one less and same year, or it's the first of the year and that's the last of the year...
			if (($then ['yday'] == $now ['yday'] - 1 && $then ['year'] == $now ['year']) ||
				($now ['yday'] == 0 && $then ['year'] == $now ['year'] - 1) && $then ['mon'] == 12 && $then ['mday'] == 31)
				$usertime_format = $yesterday_format;
		}
		return $date->toFormat ( $usertime_format, true );
	}

}

