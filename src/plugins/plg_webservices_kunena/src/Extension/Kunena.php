<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      WebServices
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\WebServices\Kunena\Extension;

\defined('_JEXEC') or die();

use Joomla\CMS\Event\Application\BeforeApiRouteEvent;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Router\Route;

/**
 * The routes of the REST API of Kunena (the web services of Joomla): the categories, the topics and the posts of the forum, read
 * only. With the option public, a request without a token reads as a guest (the permissions of the forum for a guest); a request
 * with the API token of a user reads what this user may read.
 *
 * @since   Kunena 8.0
 */
final class Kunena extends CMSPlugin implements SubscriberInterface
{
    /**
     * The resources of the API: their path and their controller.
     *
     * @since   Kunena 8.0
     */
    public const array RESOURCES = [
        'v1/kunena/categories' => 'categories',
        'v1/kunena/topics'     => 'topics',
        'v1/kunena/posts'      => 'posts',
    ];

    /**
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onBeforeApiRoute' => 'onBeforeApiRoute',
        ];
    }

    /**
     * The routes: a list and an item for each resource, with the method GET only (nothing is created, changed or deleted).
     *
     * @param   BeforeApiRouteEvent  $event  The event of the router.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public function onBeforeApiRoute(BeforeApiRouteEvent $event): void
    {
        $defaults = ['component' => 'com_kunena', 'public' => (bool) $this->params->get('public', 1)];
        $routes   = [];

        foreach (self::RESOURCES as $path => $controller) {
            $routes[] = new Route(['GET'], $path, $controller . '.displayList', [], $defaults);
            $routes[] = new Route(['GET'], $path . '/:id', $controller . '.displayItem', ['id' => '(\d+)'], $defaults);
        }

        $event->getRouter()->addRoutes($routes);
    }
}
