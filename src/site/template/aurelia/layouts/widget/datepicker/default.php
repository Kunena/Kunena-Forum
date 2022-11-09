<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

// Load language strings for bootstrap datepicker
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_SUNDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_MONDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_TUESDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_WEDNESDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_THURSDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_FRIDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYS_SATURDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_SUNDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_MONDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_TUESDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_WEDNESDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_THURSDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_FRIDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSSHORT_SATURDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_SUNDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_MONDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_TUESDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_WEDNESDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_THURSDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_FRIDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_DAYSMIN_SATURDAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_JANUARY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_FEBRUARY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_MARCH');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_APRIL');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_MAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_JUNE');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_JULY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_AUGUST');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_SEPTEMBER');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_OCTOBER');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_NOVEMBER');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_DECEMBER');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_JANUARY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_FEBRUARY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_MARCH');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_APRIL');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_MAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_JUNE');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_JULY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_AUGUST');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_SEPTEMBER');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_OCTOBER');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_NOVEMBER');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTH_SHORT_DECEMBER');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_TODAY');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_MONTHS_TITLE');
Text::script('COM_KUNENA_BOOTSTRAP_DATEPICKER_CLEAR');

$this->addStyleSheet('bootstrap.datepicker.css');
$this->addScript('bootstrap.datepicker.js');
$this->addScript('locales/bootstrap-datepicker.kunena.js');
