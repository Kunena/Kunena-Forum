<?php

/**
 * Kunena Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Kunena
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
use Kunena\Forum\Plugin\Kunena\Easyblog\Extension\Easyblog;

return new class implements ServiceProviderInterface
{
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     * @since   2.0.0
     */
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $subject = $container->get(DispatcherInterface::class);
                $config  = (array) PluginHelper::getPlugin('kunena', 'easyblog');
                $plugin  = new Easyblog($config);

                $plugin->traitSetDispatcher($subject);
                $plugin->setApplication(Factory::getApplication());
                $plugin->setDatabase($container->get('DatabaseDriver'));

                return $plugin;
            }
        );
    }
};
