<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget;

\defined('_JEXEC') or die;

use Joomla\CMS\Menu\AbstractMenu;
use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since  Kunena 6.1
 */
class WidgetBreadcrumb extends KunenaLayout
{
    public mixed $breadcrumb = null;

    /**
     * Leave out the crumb of the default page of a Kunena home menu item when it directly follows the home crumb: both
     * crumbs open the same page.
     *
     * @param   array         $pathway  The items of the pathway (each with a name and a link).
     * @param   AbstractMenu  $menu     The menu of the site.
     *
     * @return  array  The items, as a list.
     *
     * @since   Kunena 8.0
     */
    public static function withoutDefaultPageCrumb(array $pathway, AbstractMenu $menu): array
    {
        $pathway = array_values($pathway);

        foreach ($pathway as $index => $crumb) {
            $itemid = self::getItemid($crumb);
            $home   = $itemid ? $menu->getItem($itemid) : null;

            if (!self::isHome($home)) {
                continue;
            }

            $default = self::getDefaultItem($menu, $home);
            $next    = $pathway[$index + 1] ?? null;

            if ($default && $next && self::getItemid($next) == $default->id) {
                unset($pathway[$index + 1]);

                return array_values($pathway);
            }
        }

        return $pathway;
    }

    /**
     * The Itemid in the link of a crumb.
     *
     * @param   object  $crumb  The item of the pathway.
     *
     * @return  integer  The Itemid, 0 when there is none.
     *
     * @since   Kunena 8.0
     */
    private static function getItemid(object $crumb): int
    {
        if (empty($crumb->link)) {
            return 0;
        }

        parse_str((string) parse_url($crumb->link, PHP_URL_QUERY), $query);

        return isset($query['Itemid']) && \is_scalar($query['Itemid']) ? (int) $query['Itemid'] : 0;
    }

    /**
     * Is the menu item a Kunena home menu item?
     *
     * @param   mixed  $item  The menu item or null.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    private static function isHome(mixed $item): bool
    {
        return $item && $item->component == 'com_kunena' && ($item->query['view'] ?? null) == 'home';
    }

    /**
     * The default page of a Kunena home menu item, by the rules of HomeDisplay::getDefaultMenuItem(), without warnings.
     *
     * @param   AbstractMenu  $menu     The menu of the site.
     * @param   object        $active   The home menu item.
     * @param   array         $visited  The ids of the home menu items already followed.
     *
     * @return  object|null  The menu item, null when there is no valid default page.
     *
     * @since   Kunena 8.0
     */
    private static function getDefaultItem(AbstractMenu $menu, object $active, array $visited = []): ?object
    {
        if (empty($active->query['defaultmenu']) || $active->id == $active->query['defaultmenu']) {
            return null;
        }

        $item = $menu->getItem($active->query['defaultmenu']);

        if (!$item || isset($visited[$item->id])) {
            return null;
        }

        if (empty($item->component) || $item->component != 'com_kunena' || !isset($item->query['view'])) {
            return null;
        }

        if ($item->query['view'] == 'home') {
            $visited[$item->id] = 1;

            return self::getDefaultItem($menu, $item, $visited);
        }

        return $item;
    }
}
