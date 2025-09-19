<?php

/**
 * Kunena Quickicon Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Quickicon
 *
 * @copyright       Copyright (C) 2023 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Kunena\Forum\Plugin\Quickicon\Kunena\Extension\Kunena;

return new class() implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.3.0
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $plugin     = new Kunena(
                    (array) PluginHelper::getPlugin('quickicon', 'kunena')
                );
                $plugin->setApplication(Factory::getApplication());
                $plugin->setDatabase($container->get('DatabaseDriver'));

                return $plugin;
            }
        );
    }
};
