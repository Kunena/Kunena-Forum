<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Framework
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
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
	 * @param   string  $date  date
	 * @param   null    $tz    tz
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
		$timezone = $this->getOffsetFromGMT(true);

		return sprintf('%+d:%02d', $timezone, ($timezone * 60) % 60);
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
		return '<span class="kdate ' . $class . '" title="' . $this->toKunena($title, $offset) . '">' . $this->toKunena($mode, $offset) . '</span>';
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
		if ($this->format('Y') < 1902)
		{
			return Text::_('COM_KUNENA_LIB_DATETIME_UNKNOWN');
		}

		if (preg_match('/^config_/', $mode) == 1)
		{
			$option = substr($mode, 7);
			$mode   = KunenaFactory::getConfig()->$option;
		}

		$modearr    = explode('_', $mode);
		$dateformat = strtolower($modearr[0]);
		$time       = false;

		switch ($dateformat)
		{
			case 'none' :
				return '';
			case 'ago' :
				return $this->toTimeAgo();
			case 'time' :
				$time            = true;
				$usertime_format = Text::_('COM_KUNENA_LIB_TIME_FMT');
				break;
			case 'date' :
				$usertime_format = Text::_('COM_KUNENA_LIB_DATE_FMT');
				break;
			case 'datetime':
				$time            = true;
				$usertime_format = Text::_('COM_KUNENA_LIB_DATETIME_FMT');
				break;
			default:
				$usertime_format = $mode;
		}

		if (!$offset)
		{
			$app = Factory::getApplication();
			$my  = $app->getIdentity();

			if ($my->id)
			{
				$offset = $my->getParam('timezone', $app->get('offset', 'utc'));
			}
			else
			{
				$offset = $app->get('offset', 'utc');
			}
		}

		try
		{
			$offset = new DateTimeZone($offset);
			$this->setTimezone($offset);
		}
		catch (Exception $e)
		{
			trigger_error('Kunena: Timezone issue!');
		}

		// Today and Yesterday?
		if (end($modearr) == 'today')
		{
			$now = Factory::getDate('now');

			if ($offset)
			{
				$now->setTimezone($offset);
			}

			$now  = @getdate($now->toUnix());
			$then = @getdate($this->toUnix());

			// Same day of the year, same year.... Today!
			if ($then ['yday'] == $now ['yday']
				&& $then ['year'] == $now ['year']
			)
			{
				return trim(Text::sprintf('COM_KUNENA_LIB_DATE_TODAY', $time ? $this->format(Text::_('COM_KUNENA_LIB_TIME_FMT'), true) : ''));
			}

			// Day-of-year is one less and same year, or it's the first of the year and that's the last of the year...
			if (($then ['yday'] == $now ['yday'] - 1 && $then ['year'] == $now ['year'])
				|| ($now ['yday'] == 0 && $then ['year'] == $now ['year'] - 1) && $then ['mon'] == 12 && $then ['mday'] == 31
			)
			{
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
		$diff   = $this->diff(new Date);

		foreach ($diff as $name => $count)
		{
			if ($name == 'd')
			{
				// Days are special case as we want to break it into weeks and days.
				$weeks = (int) ($count / 7);

				if ($weeks)
				{
					$count  %= 7;
					$output .= ($weeks == 1) ? " 1 {$chunks['w'][0]}" : " {$weeks} {$chunks['w'][1]}";

					if (2 == ++$tick)
					{
						break;
					}
				}
			}

			if (!$count || !isset($chunks[$name]))
			{
				continue;
			}

			$output .= ($count == 1) ? " 1 {$chunks[$name][0]}" : " {$count} {$chunks[$name][1]}";

			if (2 == ++$tick)
			{
				break;
			}
		}

		if (!$output)
		{
			$output .= Text::_('COM_KUNENA_LIB_TIME_NOW');
		}
		else
		{
			$output = Text::sprintf('COM_KUNENA_LIB_TIME_AGO', trim($output));
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $output;
	}
}
