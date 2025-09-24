<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      File
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Form\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

class KunenaConfigField extends FormField
{
    /**
     * @var string
     */
    protected $type  = 'KunenaConfig';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     * @since   7.0.0
     */
    protected function getInput(): string
    {
        Factory::getApplication()->bootComponent('com_kunena');
        KunenaFactory::loadLanguage('com_kunena', 'admin');
        KunenaFactory::loadLanguage('com_kunena.views', 'admin');
        KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');
        KunenaFactory::loadLanguage('com_kunena.sys', 'admin');
        KunenaFactory::loadLanguage('com_kunena.install', 'admin');
        KunenaFactory::loadLanguage('com_kunena.models', 'admin');
        KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');
        KunenaFactory::loadLanguage('com_plugins', 'admin');
        KunenaFactory::loadLanguage('com_kunena', 'site');

        return '';
    }
}
