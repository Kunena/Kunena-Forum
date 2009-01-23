<?php
/**
* @version $Id: fb_timeformat.class.php 462 2007-12-10 00:05:53Z fxstein $
* Fireboard Component
* @package Fireboard
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
$GLOBALS['FB_DT_txt']['days'] = array (
    _FB_DT_LDAY_SUN,
    _FB_DT_LDAY_MON,
    _FB_DT_LDAY_TUE,
    _FB_DT_LDAY_WED,
    _FB_DT_LDAY_THU,
    _FB_DT_LDAY_FRI,
    _FB_DT_LDAY_SAT
);

$GLOBALS['FB_DT_txt']['days_short'] = array (
    _FB_DT_DAY_SUN,
    _FB_DT_DAY_MON,
    _FB_DT_DAY_TUE,
    _FB_DT_DAY_WED,
    _FB_DT_DAY_THU,
    _FB_DT_DAY_FRI,
    _FB_DT_DAY_SAT
);

$GLOBALS['FB_DT_txt']['months'] = array (
    0,
    _FB_DT_LMON_JAN,
    _FB_DT_LMON_FEB,
    _FB_DT_LMON_MAR,
    _FB_DT_LMON_APR,
    _FB_DT_LMON_MAY,
    _FB_DT_LMON_JUN,
    _FB_DT_LMON_JUL,
    _FB_DT_LMON_AUG,
    _FB_DT_LMON_SEP,
    _FB_DT_LMON_OCT,
    _FB_DT_LMON_NOV,
    _FB_DT_LMON_DEC,
);

$GLOBALS['FB_DT_txt']['months_short'] = array
(
    0,
    _FB_DT_MON_JAN,
    _FB_DT_MON_FEB,
    _FB_DT_MON_MAR,
    _FB_DT_MON_APR,
    _FB_DT_MON_MAY,
    _FB_DT_MON_JUN,
    _FB_DT_MON_JUL,
    _FB_DT_MON_AUG,
    _FB_DT_MON_SEP,
    _FB_DT_MON_OCT,
    _FB_DT_MON_NOV,
    _FB_DT_MON_DEC,
);

// Format a time to make it look purdy.
function FB_timeformat($logTime, $show_today = true)
{
	// formatts a time in Display space! Don't pass internal times!
	// ToDo: Pass format!
   $usertime_format = _FB_DT_DATETIME_FMT;

    global $mosConfig_locale;
    $time = $logTime;
    $todayMod = 2;

    // We can't have a negative date (on Windows, at least.)
    if ($time < 0) {
        $time = 0;
    }

    // Today and Yesterday?
    if ($show_today === true)
    {
        // Get the current time.
        $nowtime = FBTools::fbGetShowTime();
        $then = @getdate($time);
        $now = @getdate($nowtime);
        // Try to make something of a time format string...
        $s = strpos($usertime_format, '%S') === false ? '' : ':%S';

        if (strpos($usertime_format, '%H') === false && strpos($usertime_format, '%T') === false) {
            $today_fmt = '%I:%M' . $s . ' %p';
        }
        else {
            $today_fmt = '%H:%M' . $s;
        }

        // Same day of the year, same year.... Today!
        if ($then['yday'] == $now['yday'] && $then['year'] == $now['year'])
            return '' . _TIME_TODAY . '' . FB_timeformat($logTime, $today_fmt);

        // Day-of-year is one less and same year, or it's the first of the year and that's the last of the year...
        if ($todayMod == '2' && (($then['yday'] == $now['yday'] - 1 && $then['year'] == $now['year']) || ($now['yday'] == 0 && $then['year'] == $now['year'] - 1) && $then['mon'] == 12 && $then['mday'] == 31))
            return '' . _TIME_YESTERDAY . '' . FB_timeformat($logTime, $today_fmt);
    }

    $str = !is_bool($show_today) ? $show_today : $usertime_format;

    /*
    // setlocale issues known in multithreaded server env. this affects many shared hostings!
    if (setlocale(LC_TIME, $mosConfig_locale))
    {
        foreach (array
        (
            '%a',
            '%A',
            '%b',
            '%B'
        )as $token)
            if (strpos($str, $token) !== false)
                $str = str_replace($token, ucwords((strftime($token, $time))), $str);
    }
    else
    */
    {
        // Do-it-yourself time localization.  Fun.
        foreach (array
        (
            '%a' => 'days_short',
            '%A' => 'days',
            '%b' => 'months_short',
            '%B' => 'months'
        )as $token => $text_label)
            if (strpos($str, $token) !== false)
                $str = str_replace($token, $GLOBALS['FB_DT_txt'][$text_label][(int)strftime($token === '%a' || $token === '%A' ? '%w' : '%m', $time)], $str);

        if (strpos($str, '%p'))
            $str = str_replace('%p', (strftime('%H', $time) < 12 ? 'am' : 'pm'), $str);
    }

    // Format any other characters..
    return strftime($str, $time);
}

?>