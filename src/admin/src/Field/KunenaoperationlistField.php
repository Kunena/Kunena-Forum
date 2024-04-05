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

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use ReflectionClass;

/**
 * Kunenacategorylist field.
 *
 * @since  Kunena 6.0
 */
class KunenaoperationlistField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  Kunena 6.0
     */
    protected $type = 'KunenaoperationList';

    /**
     * Method to get the field options.
     *
     * @since  Kunena 6.3.0-BETA3
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        if (!class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') || !KunenaForum::installed()) {
            echo '<a href="' . Route::_('index.php?option=com_kunena') . '">PLEASE COMPLETE KUNENA INSTALLATION</a>';

            return '';
        }

        Factory::getApplication()->bootComponent('com_kunena');
        KunenaFactory::loadLanguage('com_kunena');

        $reflection = new ReflectionClass('Kunena\Forum\Libraries\Log\KunenaLog');

        $constants = $reflection->getConstants();
        ksort($constants);
        $operationOptions = [];

        foreach ($constants as $key => $value) {
            if (strpos($key, 'LOG_') === 0) {
                $operationOptions[] = (object) [
                    'value'  => $key,
                    'text' => Text::_("COM_KUNENA_{$value}"),
                ];
            }
        }

        $options = parent::getOptions();

        if (empty($options)) {
            $options[] = HTMLHelper::_('select.option', '', Text::_('COM_KUNENA_FILTER_SELECT_OPERATION'));
        }

        return array_merge($options, $operationOptions);
    }
}
