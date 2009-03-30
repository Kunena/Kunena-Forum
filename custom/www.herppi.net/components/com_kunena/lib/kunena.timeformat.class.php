<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

//added time format
$GLOBALS['KUNENA_DT_txt']['days'] = array (
    _KUNENA_DT_LDAY_SUN,
    _KUNENA_DT_LDAY_MON,
    _KUNENA_DT_LDAY_TUE,
    _KUNENA_DT_LDAY_WED,
    _KUNENA_DT_LDAY_THU,
    _KUNENA_DT_LDAY_FRI,
    _KUNENA_DT_LDAY_SAT
);

$GLOBALS['KUNENA_DT_txt']['days_short'] = array (
    _KUNENA_DT_DAY_SUN,
    _KUNENA_DT_DAY_MON,
    _KUNENA_DT_DAY_TUE,
    _KUNENA_DT_DAY_WED,
    _KUNENA_DT_DAY_THU,
    _KUNENA_DT_DAY_FRI,
    _KUNENA_DT_DAY_SAT
);

$GLOBALS['KUNENA_DT_txt']['months'] = array (
    0,
    _KUNENA_DT_LMON_JAN,
    _KUNENA_DT_LMON_FEB,
    _KUNENA_DT_LMON_MAR,
    _KUNENA_DT_LMON_APR,
    _KUNENA_DT_LMON_MAY,
    _KUNENA_DT_LMON_JUN,
    _KUNENA_DT_LMON_JUL,
    _KUNENA_DT_LMON_AUG,
    _KUNENA_DT_LMON_SEP,
    _KUNENA_DT_LMON_OCT,
    _KUNENA_DT_LMON_NOV,
    _KUNENA_DT_LMON_DEC,
);

$GLOBALS['KUNENA_DT_txt']['months_short'] = array
(
    0,
    _KUNENA_DT_MON_JAN,
    _KUNENA_DT_MON_FEB,
    _KUNENA_DT_MON_MAR,
    _KUNENA_DT_MON_APR,
    _KUNENA_DT_MON_MAY,
    _KUNENA_DT_MON_JUN,
    _KUNENA_DT_MON_JUL,
    _KUNENA_DT_MON_AUG,
    _KUNENA_DT_MON_SEP,
    _KUNENA_DT_MON_OCT,
    _KUNENA_DT_MON_NOV,
    _KUNENA_DT_MON_DEC,
);

class CKunenaTimeformat
{

    function internalTime($time=null) {
    	// tells internal FB representing time from UTC $time
        global $fbConfig;
        // Prevent zeroes
        if($time===0) {
          return 0;
        }
        if($time===null) {
          $time = time();
        }
        return $time + ($fbConfig->board_ofset * 3600);
    }

    function showTimeSince($older_date, $newer_date=false)
    {
        // ToDo: return code plus string to decide concatenation.
        // array of time period chunks
        $chunks = array(
            array(60 * 60 * 24 * 365 , _KUNENA_DATE_YEAR, _KUNENA_DATE_YEARS),
            array(60 * 60 * 24 * 30 , _KUNENA_DATE_MONTH, _KUNENA_DATE_MONTHS),
            array(60 * 60 * 24 * 7, _KUNENA_DATE_WEEK, _KUNENA_DATE_WEEKS),
            array(60 * 60 * 24 , _KUNENA_DATE_DAY, _KUNENA_DATE_DAYS),
            array(60 * 60 , _KUNENA_DATE_HOUR, _KUNENA_DATE_HOURS),
            array(60 , _KUNENA_DATE_MINUTE, _KUNENA_DATE_MINUTES),
        );

        // $newer_date is false if we want to know the time elapsed between a date and the current time
        // $newer_date has a value if we want to work out time elapsed between two known dates
        $newer_date = ($newer_date === false) ? CKunenaTimeformat::internalTime() : $newer_date;

        // difference in seconds
        $since = $newer_date - $older_date;

        // no negatives!
        if($since<=0) {
          return '?';
        }

        // we only want to output two chunks of time here, eg:
        // x years, xx months
        // x days, xx hours
        // so there's only two bits of calculation below:

        // step one: the first chunk
        for ($i = 0, $j = count($chunks); $i < $j; $i++)
            {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            $names = $chunks[$i][2];

            // finding the biggest chunk (if the chunk fits, break)
            if (($count = floor($since / $seconds)) != 0)
            {
                break;
            }
        }

        // set output var
        $output = ($count == 1) ? '1 '.$name : $count.' '.$names ;

        // step two: the second chunk
        if ($i + 1 < $j)
        {
            $seconds2 = $chunks[$i + 1][0];
            $name2 = $chunks[$i + 1][1];
            $names2 = $chunks[$i + 1][2];

            if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0)
            {
                // add to output var
                $output .= ($count2 == 1) ? ', 1 '.$name2 : ', '.$count2.' '.$names2;
            }
        }

        return str_replace('%time%', $output, _KUNENA_TIME_SINCE);
    }

    // Format a time to make it look purdy.
    function showDate($time, $mode='datetime_today', $tz='internal')
    {
        global $fbConfig, $mosConfig_offset;

	$site_offset = $mosConfig_offset;
        if (!is_numeric($time)) $time = strtotime($time);
        switch (strtolower($tz)) {
            case 'utc':
                $time = $time + $site_offset * 3600;
                break;
            case 'internal':
                break;
            default:
		$time = $time + ($site_offset - (float)$tz + $fbConfig->board_ofset) * 3600;
                break;
        }
        if (preg_match('/^config_/', $mode) == 1) {
            $option = substr($mode, 7);
            $mode = $fbConfig->$option;
        }
        $mode = split('_', $mode);
        switch (strtolower($mode[0])) {
            case 'none':
                return '';
            case 'time':
                $usertime_format = _KUNENA_DT_TIME_FMT;
                $today_format = _KUNENA_DT_TIME_FMT;
                $yesterday_format = _KUNENA_DT_TIME_FMT;
                break;
            case 'date':
                $usertime_format = _KUNENA_DT_DATE_FMT;
                $today_format = _KUNENA_DT_DATE_TODAY_FMT;
                $yesterday_format = _KUNENA_DT_DATE_YESTERDAY_FMT;
                break;
            case 'ago':
                return CKunenaTimeformat::showTimeSince($time);
                break;
            default:
                $usertime_format = _KUNENA_DT_DATETIME_FMT;
                $today_format = _KUNENA_DT_DATETIME_TODAY_FMT;
                $yesterday_format = _KUNENA_DT_DATETIME_YESTERDAY_FMT;
                break;
        }
        $todayMod = 2;

        // We can't have a negative date (on Windows, at least.)
        if ($time < 0) {
            $time = 0;
        }

        // Today and Yesterday?
        if ($mode[count($mode)-1] == 'today')
        {
            // Get the current time.
            $nowtime = CKunenaTimeformat::InternalTime();
            $then = @getdate($time);
            $now = @getdate($nowtime);

            // Same day of the year, same year.... Today!
            if ($then['yday'] == $now['yday'] && $then['year'] == $now['year'])
                $usertime_format = $today_format;

            // Day-of-year is one less and same year, or it's the first of the year and that's the last of the year...
            if ($todayMod == '2' && (($then['yday'] == $now['yday'] - 1 && $then['year'] == $now['year']) || ($now['yday'] == 0 && $then['year'] == $now['year'] - 1) && $then['mon'] == 12 && $then['mday'] == 31))
                $usertime_format = $yesterday_format;
        }

        $str = $usertime_format;

        // Do-it-yourself time localization.  Fun.
        foreach (array
        (
            '%a' => 'days_short',
            '%A' => 'days',
            '%b' => 'months_short',
            '%B' => 'months'
        )as $token => $text_label)
            if (strpos($str, $token) !== false)
                $str = str_replace($token, $GLOBALS['KUNENA_DT_txt'][$text_label][(int)strftime($token === '%a' || $token === '%A' ? '%w' : '%m', $time)], $str);

        if (strpos($str, '%p'))
            $str = str_replace('%p', (strftime('%H', $time) < 12 ? 'am' : 'pm'), $str);

        // Format any other characters..
        return strftime($str, $time);
    }

}
