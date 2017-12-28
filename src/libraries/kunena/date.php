<?php
/**
 * Kunena Component
 * @package    Kunena.Framework
 *
 * @copyright  (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.utilities.date');

/**
 * Class KunenaDate
 */
class KunenaDate extends JDate
{
	/**
	 * @param   string $date
	 * @param   null   $tz
	 *
	 * @return KunenaDate
	 */
	public static function getInstance($date = 'now', $tz = null)
	{
		return new KunenaDate($date, $tz);
	}

	/**
	 * @return string
	 */
	public function toTimeAgo()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$chunks = array (
			'y' => array (JText::_('COM_KUNENA_DATE_YEAR'), JText::_('COM_KUNENA_DATE_YEARS') ),
			'm' => array (JText::_('COM_KUNENA_DATE_MONTH'), JText::_('COM_KUNENA_DATE_MONTHS') ),
			'w' => array (JText::_('COM_KUNENA_DATE_WEEK'), JText::_('COM_KUNENA_DATE_WEEKS') ),
			'd' => array (JText::_('COM_KUNENA_DATE_DAY'), JText::_('COM_KUNENA_DATE_DAYS') ),
			'h' => array (JText::_('COM_KUNENA_DATE_HOUR'), JText::_('COM_KUNENA_DATE_HOURS') ),
			'i' => array (JText::_('COM_KUNENA_DATE_MINUTE'), JText::_('COM_KUNENA_DATE_MINUTES') ) );

		// We only want to output two chunks of time here, eg: "x years, xx months" or "x days, xx hours"
		$tick = 0;
		$output = '';
		$diff = $this->diff(new JDate);

		foreach ($diff as $name => $count)
		{
			if ($name == 'd')
			{
				// Days are special case as we want to break it into weeks and days.
				$weeks = (int) ($count / 7);

				if ($weeks)
				{
					$count %= 7;
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
			$output .= JText::_('COM_KUNENA_LIB_TIME_NOW');
		}
		else
		{
			$output = JText::sprintf('COM_KUNENA_LIB_TIME_AGO', trim($output));
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $output;
	}

	/**
	 * @return string
	 */
	public function toTimezone()
	{
		$timezone = $this->getOffsetFromGMT(true);

		return sprintf('%+d:%02d', $timezone, ($timezone * 60) % 60);
	}

	/**
	 * @param   string $mode
	 * @param   string $title
	 * @param   bool   $offset
	 * @param   string $class
	 *
	 * @return string
	 */
	public function toSpan($mode = 'datetime_today', $title = 'ago', $offset = false, $class='')
	{
		return '<span class="kdate ' . $class . '" title="' . $this->toKunena($title, $offset) . '">' . $this->toKunena($mode, $offset) . '</span>';
	}

	/**
	 * @param   string $mode
	 * @param   bool   $offset
	 *
	 * @return string
	 * @throws Exception
	 */
	public function toKunena($mode = 'datetime_today', $offset = false)
	{
		if ($this->format('Y') < 1902)
		{
			return JText::_('COM_KUNENA_LIB_DATETIME_UNKNOWN');
		}

		if (preg_match('/^config_/', $mode) == 1)
		{
			$option = substr($mode, 7);
			$mode = KunenaFactory::getConfig()->$option;
		}

		$modearr = explode('_', $mode);
		$dateformat = strtolower($modearr[0]);
		$time = false;

		switch ($dateformat)
		{
			case 'none' :
				return '';
			case 'ago' :
				return $this->toTimeAgo();
			case 'time' :
				$time = true;
				$usertime_format = JText::_('COM_KUNENA_LIB_TIME_FMT');
				break;
			case 'date' :
				$usertime_format = JText::_('COM_KUNENA_LIB_DATE_FMT');
				break;
			case 'datetime':
				$time = true;
				$usertime_format = JText::_('COM_KUNENA_LIB_DATETIME_FMT');
				break;
			default:
				$usertime_format = $mode;
		}

		if (!$offset)
		{
			$app = JFactory::getApplication();
			$my = JFactory::getUser();

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
			$now = JFactory::getDate('now');

			if ($offset)
			{
				$now->setTimezone($offset);
			}

			$now = @getdate($now->toUnix(true));
			$then = @getdate($this->toUnix(true));

			// Same day of the year, same year.... Today!
			if ($then ['yday'] == $now ['yday']
				&& $then ['year'] == $now ['year']
)
			{
				return trim(JText::sprintf('COM_KUNENA_LIB_DATE_TODAY', $time ? $this->format(JText::_('COM_KUNENA_LIB_TIME_FMT'), true) : ''));
			}

			// Day-of-year is one less and same year, or it's the first of the year and that's the last of the year...
			if (($then ['yday'] == $now ['yday'] - 1 && $then ['year'] == $now ['year'])
				|| ($now ['yday'] == 0 && $then ['year'] == $now ['year'] - 1) && $then ['mon'] == 12 && $then ['mday'] == 31
)
			{
				return trim(JText::sprintf('COM_KUNENA_LIB_DATE_YESTERDAY', $time ? $this->format(JText::_('COM_KUNENA_LIB_TIME_FMT'), true) : ''));
			}
		}

		return $this->format($usertime_format, true);
	}
}
