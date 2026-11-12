<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Field
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\GroupedlistField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Access filter of the categories: the Joomla access levels and the Joomla user groups.
 *
 * @since  Kunena 8.0
 */
class KunenaaccessfilterField extends GroupedlistField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  Kunena 8.0
     */
    protected $type = 'KunenaaccessFilter';

    /**
     * Method to get the field option groups.
     *
     * @return  array  The field option objects as a nested array in groups.
     *
     * @since   Kunena 8.0
     */
    protected function getGroups()
    {
        // The options of the XML (the empty option) come first, ungrouped.
        $groups = parent::getGroups();

        $levels = [];

        foreach (HTMLHelper::_('access.assetgroups') as $level) {
            $levels[] = HTMLHelper::_('select.option', 'joomla.level.' . $level->value, $level->text);
        }

        $userGroups = [];

        foreach (HTMLHelper::_('user.groups', true) as $group) {
            $userGroups[] = HTMLHelper::_('select.option', 'joomla.group.' . $group->value, $group->text);
        }

        $groups[Text::_('COM_KUNENA_FILTER_ACCESS_GROUP_LEVELS')]     = $levels;
        $groups[Text::_('COM_KUNENA_FILTER_ACCESS_GROUP_USERGROUPS')] = $userGroups;

        return $groups;
    }
}
