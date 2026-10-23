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

namespace Kunena\Forum\Libraries\Date;

\defined('_JEXEC') or die();

use DateTimeZone;
use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;

/**
 * Class KunenaDate
 *
 * @since   Kunena 6.0
 */
class KunenaDate extends Date
{
    /**
     * The format of the date picker (bootstrap-datepicker: dd/mm/yyyy) as a format of PHP (d/m/Y).
     *
     * @param   string  $format  The format of the date picker.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    protected static function pickerToPhp(string $format): string
    {
        // The longest token first.
        $php    = '';
        $tokens = ['yyyy' => 'Y', 'yy' => 'y', 'MM' => 'F', 'M' => 'M', 'mm' => 'm', 'm' => 'n', 'DD' => 'l', 'D' => 'D', 'dd' => 'd', 'd' => 'j'];

        for ($pos = 0, $length = \strlen($format); $pos < $length;) {
            foreach ($tokens as $token => $replacement) {
                if (substr($format, $pos, \strlen($token)) === $token) {
                    $php .= $replacement;
                    $pos += \strlen($token);
                    continue 2;
                }
            }

            $php .= '\\' . $format[$pos];
            $pos++;
        }

        return $php;
    }

    /**
     * Writes a date (Y-m-d) for a date field in the format of the date picker, so that the field gives back the same date when the
     * form is saved without a change (the value of the field is read with fromPicker()).
     *
     * @param   string  $date    The date as Y-m-d (the value which means "no date" gives an empty text).
     * @param   string  $format  The format of the date picker (empty: the option of the forum).
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    public static function toPicker(string $date, string $format = ''): string
    {
        if ($date === '' || str_starts_with($date, '1000-01-01') || str_starts_with($date, '0000-00-00')) {
            return '';
        }

        if ($format === '') {
            $format = (string) KunenaFactory::getConfig()->datePickerFormat;
        }

        $value = \DateTime::createFromFormat('!Y-m-d', substr($date, 0, 10));

        if (!$value) {
            return '';
        }

        return $format === '' ? $value->format('Y-m-d') : $value->format(self::pickerToPhp($format));
    }

    /**
     * Reads a date which a user typed or chose in a date field of the forum (the search at a date, the birthdate). The field
     * writes the date in the format of the option "Date format for datepicker" (for example dd/mm/yyyy), which the parser of
     * PHP does not know: "23/10/2026" was an error. The format of the option is tried first, then the formats which PHP knows.
     *
     * @param   string  $value   The date of the field.
     * @param   string  $format  The format of the date picker (empty: the option of the forum).
     *
     * @return  string|null  The date as Y-m-d, null for a text which is no date.
     *
     * @since   Kunena 7.2
     */
    public static function fromPicker(string $value, string $format = ''): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if ($format === '') {
            $format = (string) KunenaFactory::getConfig()->datePickerFormat;
        }

        if ($format !== '') {
            $php  = self::pickerToPhp($format);
            $date = \DateTime::createFromFormat('!' . $php, $value);
            $errors = \DateTime::getLastErrors();

            if ($date && (!$errors || (!$errors['warning_count'] && !$errors['error_count']))) {
                return $date->format('Y-m-d');
            }
        }

        try {
            return (new \DateTime($value))->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Return an instance of KunenaDate object
     *
     * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
     * @param   mixed   $tz    Time zone to be used for the date. Might be a string or a DateTimeZone object.
     *
     * @return  KunenaDate
     *
     * @since   Kunena 6.0
     */
    public static function getInstance($date = 'now', $tz = null): KunenaDate
    {
        return new self($date, $tz);
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function toTimezone(): string
    {
        $minutes = (int) round($this->getOffsetFromGMT(true) * 60);

        return \sprintf('%s%d:%02d', $minutes < 0 ? '-' : '+', intdiv(abs($minutes), 60), abs($minutes) % 60);
    }

    /**
     * @param   string  $mode    mode
     * @param   string  $title   title
     * @param   bool    $offset  offset
     * @param   string  $class   class
     *
     * @return  string
     *
     * @since   Kunena 6.0
     * @throws  Exception
     */
    public function toSpan($mode = 'datetime_today', $title = 'ago', $offset = false, $class = ''): string
    {
        return '<span class="kdate ' . $class . '" data-bs-toggle="tooltip" title="' . $this->toKunena($title, $offset) . '">' . $this->toKunena($mode, $offset) . '</span>';
    }

    /**
     * @param   string  $mode    mode
     * @param   bool    $offset  offset
     *
     * @return  string
     *
     * @since   Kunena 6.0
     * @throws  Exception
     */
    public function toKunena($mode = 'datetime_today', $offset = false): string
    {
        if ($this->format('Y') < 1902) {
            return Text::_('COM_KUNENA_LIB_DATETIME_UNKNOWN');
        }

        if (preg_match('/^config_/', $mode) == 1) {
            $option = substr($mode, 7);
            $mode   = KunenaFactory::getConfig()->$option;
        }

        $modearr    = explode('_', $mode);
        $dateformat = strtolower($modearr[0]);
        $time       = false;

        switch ($dateformat) {
            case 'none':
                return '';
            case 'ago':
                return $this->toTimeAgo();
            case 'time':
                $time            = true;
                $usertime_format = Text::_('COM_KUNENA_LIB_TIME_FMT');
                break;
            case 'date':
                $usertime_format = Text::_('COM_KUNENA_LIB_DATE_FMT');
                break;
            case 'datetime':
                $time            = true;
                $usertime_format = Text::_('COM_KUNENA_LIB_DATETIME_FMT');
                break;
            default:
                $usertime_format = $mode;
        }

        if (!$offset) {
            $app = Factory::getApplication();
            $my  = $app->getIdentity();

            if ($my->id) {
                $offset = $my->getParam('timezone', $app->get('offset', 'utc'));
            } else {
                $offset = $app->get('offset', 'utc');
            }
        }

        try {
            $offset = new DateTimeZone($offset);
            $this->setTimezone($offset);
        } catch (Exception $e) {
            trigger_error('Kunena: Timezone issue!');
        }

        // Today and Yesterday?
        if (end($modearr) == 'today') {
            $now = Factory::getDate('now');

            if ($offset) {
                $now->setTimezone($offset);
            }

            $now  = @getdate($now->toUnix());
            $then = @getdate($this->toUnix());

            // Same day of the year, same year.... Today!
            if (
                $then ['yday'] == $now ['yday']
                && $then ['year'] == $now ['year']
            ) {
                return trim(Text::sprintf('COM_KUNENA_LIB_DATE_TODAY', $time ? $this->format(Text::_('COM_KUNENA_LIB_TIME_FMT'), true) : ''));
            }

            // Day-of-year is one less and same year, or it's the first of the year and that's the last of the year...
            if (
                ($then ['yday'] == $now ['yday'] - 1 && $then ['year'] == $now ['year'])
                || ($now ['yday'] == 0 && $then ['year'] == $now ['year'] - 1) && $then ['mon'] == 12 && $then ['mday'] == 31
            ) {
                return trim(Text::sprintf('COM_KUNENA_LIB_DATE_YESTERDAY', $time ? $this->format(Text::_('COM_KUNENA_LIB_TIME_FMT'), true) : ''));
            }
        }

        return $this->format($usertime_format, true);
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function toTimeAgo(): string
    {
        KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
        $chunks = [
            'y' => [Text::_('COM_KUNENA_DATE_YEAR'), Text::_('COM_KUNENA_DATE_YEARS')],
            'm' => [Text::_('COM_KUNENA_DATE_MONTH'), Text::_('COM_KUNENA_DATE_MONTHS')],
            'w' => [Text::_('COM_KUNENA_DATE_WEEK'), Text::_('COM_KUNENA_DATE_WEEKS')],
            'd' => [Text::_('COM_KUNENA_DATE_DAY'), Text::_('COM_KUNENA_DATE_DAYS')],
            'h' => [Text::_('COM_KUNENA_DATE_HOUR'), Text::_('COM_KUNENA_DATE_HOURS')],
            'i' => [Text::_('COM_KUNENA_DATE_MINUTE'), Text::_('COM_KUNENA_DATE_MINUTES')], ];

        // We only want to output two chunks of time here, eg: "x years, xx months" or "x days, xx hours"
        $tick   = 0;
        $output = '';
        $diff   = $this->diff(new Date());

        foreach ($diff as $name => $count) {
            if ($name == 'd') {
                // Days are special case as we want to break it into weeks and days.
                $weeks = (int) ($count / 7);

                if ($weeks) {
                    $count  %= 7;
                    $output .= ($weeks == 1) ? " 1 {$chunks['w'][0]}" : " {$weeks} {$chunks['w'][1]}";

                    if (2 == ++$tick) {
                        break;
                    }
                }
            }

            if (!$count || !isset($chunks[$name])) {
                continue;
            }

            $output .= ($count == 1) ? " 1 {$chunks[$name][0]}" : " {$count} {$chunks[$name][1]}";

            if (2 == ++$tick) {
                break;
            }
        }

        if (!$output) {
            $output .= Text::_('COM_KUNENA_LIB_TIME_NOW');
        } else {
            $output = Text::sprintf('COM_KUNENA_LIB_TIME_AGO', trim($output));
        }

        KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

        return $output;
    }
}
