<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      File
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https: //www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https: //www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Form\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Date\KunenaDate;

/**
 * PostDateFormatField
 *
 * @since 7.0.0
 */
class PostDateFormatField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    public $type = 'PostDateFormat';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     * @since   1.0.0
     */
    protected function getOptions(): array
    {
        $time      = KunenaDate::getInstance(time() - 80000);
        $options[] = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_OPTION_DATEFORMAT_NONE'));
        $options[] = HTMLHelper::_('select.option', 'ago', $time->toKunena('ago'));
        $options[] = HTMLHelper::_('select.option', 'datetime_today', $time->toKunena('datetime_today'));
        $options[] = HTMLHelper::_('select.option', 'datetime', $time->toKunena('datetime'));

        return $options;
    }
}
