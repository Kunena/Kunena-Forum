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

/**
 * CategoryListField
 *
 * @since 7.0.0
 */
class CategoryListField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    public $type = 'CategoryList';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     * @since   1.0.0
     */
    protected function getOptions(): array
    {
        $sections  = isset($this->element['sections']) ? (string) $this->element['sections'] : 0;
        $action    = isset($this->element['action']) ? (string) $this->element['action'] : 'read';
        $select    = isset($this->element['select']) ? (string) $this->element['select'] : '';
        $params    = [
            'sections'       => (bool) $sections,
            'action'         => $action,
            'return_options' => 1
        ];

        if (!empty($select)) {
            $selectOption = [HTMLHelper::_('select.option', 0, Text::_($select))];
        } else {
            $selectOption = [];
        }

        $options = HTMLHelper::_('kunenaforum.categorylist', 'cfg_latestCategory[]', 0, $selectOption, $params);

        return $options;
    }
}
