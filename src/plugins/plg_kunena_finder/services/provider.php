<?php

/**
 * Kunena Finder Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Finder
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Plugin\Kunena\Finder\Extension\Finder;

// The plugin which tells Smart Search about the saved and deleted messages of the forum. Without this file Joomla loaded no plugin
// (it looked for the file of a plugin of Joomla 3), so new, edited and deleted messages reached the search only at the next indexing.
return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $plugin = new Finder((array) PluginHelper::getPlugin('kunena', 'finder'));

                $plugin->setDispatcher($container->get(DispatcherInterface::class));
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
