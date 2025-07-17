<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Dispatcher
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * ComponentDispatcher class for com_kunena
 *
 * @since  6.0
 */
class Dispatcher extends ComponentDispatcher
{
    /**
     * The URL option for the component.
     *
     * @var    string
     */
    protected $option = 'com_kunena';

    /**
     * Load the language
     *
     * @return  void
     * @since   6.0.0
     */
    protected function loadLanguage(): void
    {
        KunenaFactory::loadLanguage('com_kunena', 'admin');
        KunenaFactory::loadLanguage('com_kunena.views', 'admin');
        KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');
        KunenaFactory::loadLanguage('com_kunena.sys', 'admin');
        KunenaFactory::loadLanguage('com_kunena.install', 'admin');
        KunenaFactory::loadLanguage('com_kunena.models', 'admin');
        KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');
        KunenaFactory::loadLanguage('com_plugins', 'admin');
        KunenaFactory::loadLanguage('com_kunena', 'site');
    }
}
