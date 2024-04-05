<?php

/**
 * Kunena Privacy Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Privacy
 *
 * @copyright       Copyright (C) 2023 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Plugin\Privacy\Kunena\Extension\Kunena;

return new class implements ServiceProviderInterface
{
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   6.1.0
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $subject = $container->get(DispatcherInterface::class);
                $config  = (array) PluginHelper::getPlugin('privacy', 'kunena');
                $plugin = new Kunena(
                    $subject,
                    $config
                );
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
