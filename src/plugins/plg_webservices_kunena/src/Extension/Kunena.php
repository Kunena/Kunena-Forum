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
 * The routes of the REST API of Kunena (the web services of Joomla): the categories, the topics and the posts of the forum, and a
 * reply (POST v1/kunena/posts, never public). With the option public, a request without a token reads as a guest (the permissions
 * of the forum for a guest); a request with the API token of a user reads what this user may read, and may post a reply.
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
     * The routes: a list and an item for each resource (GET), and the reply (POST). Nothing is changed or deleted.
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

        // A reply: never public, only with the API token of a user (who must have the permission to reply in the forum).
        $routes[] = new Route(['POST'], 'v1/kunena/posts', 'posts.add', [], ['component' => 'com_kunena', 'public' => false]);

        $event->getRouter()->addRoutes($routes);
    }
}
