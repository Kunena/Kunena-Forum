<?php

/**
 * @package     Kunena.Site
 * @subpackage  com_kunena
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Site\Service;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Profiler\Profiler;
use Joomla\Database\DatabaseInterface;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Routing class of com_kunena
 *
 * @since   6.0
 */
class Router extends RouterView
{
    protected mixed $noIDs = false;

    /**
     * The category factory
     *
     * @var CategoryFactoryInterface
     *
     * @since   6.0
     */
    private ?CategoryFactoryInterface $categoryFactory = null;

    /**
     * The db
     *
     * @var DatabaseInterface
     *
     * @since   6.0
     */
    private ?DatabaseInterface $db = null;

    /**
     * Kunena Component router constructor
     *
     * @param   SiteApplication  $app   The application object
     * @param   AbstractMenu     $menu  The menu object to work with
     *
     * @since   6.0
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu)
    {
        $params      = ComponentHelper::getParams('com_kunena');
        $this->noIDs = (bool) $params->get('sef_ids');

        $kunena = new RouterViewConfiguration('kunena');
        $kunena->setKey('id');
        $this->registerView($kunena);

        parent::__construct($app, $menu);

        $this->attachRule(new StandardRules($this));
    }

    /**
     * @return  Router|null
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function getInstance()
    {
        static $instance = null;

        // The router of the site, as Joomla makes it (the constructor needs the application and its menu; there is no load()).
        if (!$instance) {
            $app      = Factory::getApplication();
            $instance = new Router($app, $app->getMenu());
        }

        return $instance;
    }

    /**
     * The menu item of an address without one, before it is built (Route::_() of Joomla, the redirect of the system plugin SEF
     * from a non-SEF address): the menu item of the forum, as the redirect of Kunena for a missing menu item takes it. Without it
     * the address was /component/kunena/..., which Kunena redirected again (a chain of a 301 and a 303). KunenaRoute::getItemID()
     * needs the document, which does not exist yet while Joomla routes.
     *
     * @param   array  $query  The variables of the address.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public function preprocess($query)
    {
        $query = parent::preprocess($query);

        // A topic, a category and a profile have the same address under any menu item of the forum. A list (the index, the recent
        // topics, the search...) has the address of its own menu item: Kunena redirects to it (permanently).
        $view    = $query['view'] ?? '';
        $ownPage = ($view === 'topic' && !empty($query['id']) && empty($query['layout']))
            || ($view === 'category' && !empty($query['catid']) && empty($query['layout']))
            || ($view === 'user' && !empty($query['userid']) && empty($query['layout']));

        if ($ownPage && empty($query['Itemid']) && ($query['option'] ?? '') === 'com_kunena') {
            $itemid = KunenaRoute::fixMissingItemID();

            if ($itemid > 0) {
                $query['Itemid'] = $itemid;
            }
        }

        // A menu item of another view (a list: the index, the recent topics...) cannot carry the view of the address: build() puts
        // the name of the view in the path, which parse() reads as a layout of the view of the menu item, and the page was a 404
        // (index.php?option=com_kunena&view=topics&mode=replies&Itemid=<index> was redirected to /forum/index/topics/mode-replies).
        // Such an address is built under the home menu item of the forum, which carries any view.
        if (($query['option'] ?? '') === 'com_kunena' && !empty($query['Itemid']) && $view !== '') {
            $item = $this->menu->getItem((int) $query['Itemid']);

            if ($item && $item->component === 'com_kunena' && !self::canCarry((array) $item->query, $query)) {
                $query = self::withoutMenuItemVariables((array) $item->query, $query);
                $home  = $this->homeItemid((string) $item->language);

                if ($home > 0) {
                    $query['Itemid'] = $home;
                } else {
                    unset($query['Itemid']);
                }
            }
        }

        return $query;
    }

    /**
     * Whether an address can be built under a menu item of the forum so that parse() gives its view back: the home menu item
     * carries any view, a menu item carries its own view, and a category or a topic with its category (the alias of the category
     * is the first segment) goes under a menu item which reads that alias (a category or a topic). Any other view is a segment
     * which the menu item of another view reads as a layout.
     *
     * @param   array  $menuQuery  The variables of the link of the menu item.
     * @param   array  $query      The variables of the address.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function canCarry(array $menuQuery, array $query): bool
    {
        $menuView = (string) ($menuQuery['view'] ?? '');
        $view     = (string) ($query['view'] ?? '');

        if ($view === '' || \in_array($menuView, ['', 'home', 'entrypage'], true) || $view === $menuView) {
            return true;
        }

        // The alias of the category in the path is read only under the menu items of these views (KunenaRoute::$sefviews).
        if ($view === 'category' || $view === 'topic') {
            return \in_array($menuView, ['category', 'topic'], true) && !empty($query['catid']);
        }

        return false;
    }

    /**
     * The address without the variables of the menu item which it leaves: Joomla gives the variables of the active menu item to
     * the request (layout=list of the index, mode=replies of the recent topics), and they belong to the view of that menu item.
     *
     * @param   array  $menuQuery  The variables of the link of the menu item.
     * @param   array  $query      The variables of the address.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function withoutMenuItemVariables(array $menuQuery, array $query): array
    {
        foreach ($menuQuery as $name => $value) {
            if ($name !== 'option' && $name !== 'view' && isset($query[$name]) && (string) $query[$name] === (string) $value) {
                unset($query[$name]);
            }
        }

        return $query;
    }

    /**
     * The home menu item of the forum (view home), in the language of a menu item when there is one in it, else in all languages,
     * else any.
     *
     * @param   string  $language  The language of the menu item which is replaced.
     *
     * @return  integer  0 when the forum has no home menu item.
     *
     * @since   Kunena 8.0
     */
    protected function homeItemid(string $language): int
    {
        $best = [];

        foreach ((array) $this->menu->getItems('component', 'com_kunena') as $item) {
            if (!\in_array((string) ($item->query['view'] ?? ''), ['home', 'entrypage'], true)) {
                continue;
            }

            $rank = $item->language === $language ? 0 : ($item->language === '*' ? 1 : 2);

            if (!isset($best[$rank])) {
                $best[$rank] = (int) $item->id;
            }
        }

        ksort($best);

        return (int) (reset($best) ?: 0);
    }

    /**
     * Build SEF URL
     *
     * All SEF URLs are formatted like this:
     *
     * http://site.com/menuitem/category/10-subject/[view]/[layout]/[param1]-value1/[param2]-value2?param3=value3&param4=value4
     *
     * - If catid exists, category will always be in the first segment
     * - If there is no catid, second segment for message will not be used (param-value: id-10)
     * - [view] and [layout] are the only parameters without value
     * - all other segments (task, id, userid, page, sel) are using param-value format
     *
     * NOTE! Only major variables are using SEF segments
     *
     * @param   array  $query  query
     *
     * @return  array Segments
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function build(&$query)
    {
        static $categoryAliasCache = [];
        static $topicSubjectCache  = [];
        static $userSlugCache      = [];

        $segments = [];

        // If Kunena Forum isn't installed or SEF is not enabled, do nothing
        if (!$this->isEnabled()) {
            return $segments;
        }

        KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __FUNCTION__ . '()') : null;

        // Get menu item
        $menuitem = null;

        if (isset($query['Itemid'])) {
            static $menuitems = [];
            $Itemid           = $query['Itemid'] = (int) $query['Itemid'];

            if (!isset($menuitems[$Itemid])) {
                $menuitems[$Itemid] = Factory::getApplication()->getMenu()->getItem($Itemid);

                if (!$menuitems[$Itemid]) {
                    // Itemid doesn't exist or is invalid
                    unset($query['Itemid']);
                }
            }

            $menuitem = $menuitems[$Itemid];
        }

        // Safety check: we need view in order to create SEF URLs
        if (!isset($menuitem->query['view']) && empty($query['view'])) {
            KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __FUNCTION__ . '()') : null;

            return $segments;
        }

        // Get view for later use (query wins menu item)
        $view = isset($query['view']) ? (string) preg_replace('/[^a-z]/', '', $query['view']) : $menuitem->query['view'];

        // Get default values for URI variables
        if (isset(KunenaRoute::$views[$view])) {
            $defaults = KunenaRoute::$views[$view];
        }

        // Check all URI variables and remove those which aren't needed
        foreach ($query as $var => $value) {
            if (isset($defaults[$var]) && !isset($menuitem->query[$var]) && $value == $defaults[$var]) {
                // Remove URI variable which has default value
                unset($query[$var]);
            } elseif (isset($menuitem->query[$var]) && $value == $menuitem->query[$var] && $var != 'Itemid' && $var != 'option') {
                // Remove URI variable which has the same value as menu item
                unset($query[$var]);
            }
        }

        // We may have catid also in the menu item (it will not be in URI)
        $numeric = !empty($menuitem->query['catid']);
        $pos     = 0;

        // Support URIs like: /forum/category
        if (!empty($query['catid']) && ($view == 'category' || $view == 'topic' || $view == 'home')) {
            // The views which have a category in their path: a category (the segment says the view, view= goes below), a topic (its
            // id follows) and the home page of a category (view=home stays in the address).
            $catid = (int) $query['catid'];

            if ($catid) {
                $numeric = true;

                if (!\array_key_exists($catid, $categoryAliasCache)) {
                    $categoryAliasCache[$catid] = (string) KunenaCategoryHelper::get($catid)->alias;
                }

                $alias = $categoryAliasCache[$catid];

                // If category alias is empty, use category id; otherwise use alias
                $segments[] = empty($alias) ? $catid : $alias;

                // This segment fully defines category view so the variable is no longer needed
                if ($view == 'category') {
                    unset($query['view']);
                }
            } elseif ($query['catid'] == '@') {
                $numeric    = true;
                $segments[] = '%' . ++$pos . '$s';

                if ($view == 'category') {
                    unset($query['view']);
                }
            }

            unset($query['catid']);
        }

        // Support URIs like: /forum/category/123-topic
        if (!empty($query['id']) && $numeric) {
            $id = (int) $query['id'];

            if ($id) {
                if (!\array_key_exists($id, $topicSubjectCache)) {
                    $topicSubjectCache[$id] = KunenaRoute::stringURLSafe((string) KunenaTopicHelper::get($id)->subject);
                }

                $subject = $topicSubjectCache[$id];

                if (empty($subject)) {
                    $segments[] = $id;
                } else {
                    $segments[] = "{$id}-{$subject}";
                }

                // This segment fully defines topic view so the variable is no longer needed
                if ($view == 'topic') {
                    unset($query['view']);
                }
            } elseif ($query['id'] == '@') {
                $segments[] = '%' . ++$pos . '$s';

                // This segment fully defines topic view so the variable is no longer needed
                if ($view == 'topic') {
                    unset($query['view']);
                }
            }

            unset($query['id']);
        } else {
            // No id available, do not use numeric variable for mesid
            $numeric = false;
        }

        // View gets added only when we do not use short URI for category/topic
        if (!empty($query['view'])) {
            // Use filtered value
            $segments[] = $view;

            // Handle attachment ID
            if ($view == 'attachment') {
                if (!empty($query['id'])) {
                    // Use filtered value
                    $segments[] = (int) $query['id'];
                    unset($query['id']);
                }

                if (!empty($query['thumb'])) {
                    // Use filtered value
                    $segments[] = 'thumb';
                    unset($query['thumb']);
                }

                unset($query['format']);

                if (!empty($query['filename'])) {
                    $segments[] = $query['filename'];
                    unset($query['filename']);
                }
            }
        }

        // Support URIs like: /forum/category/123-topic/reply
        if (!empty($query['layout'])) {
            // Use filtered value
            $segments[] = (string) preg_replace('/[^a-z]/', '', $query['layout']);
        }

        // Support URIs like: /forum/category/123-topic/reply/124
        if (isset($query['mesid']) && $numeric) {
            if ($query['mesid'] == '@') {
                $segments[] = '%' . ++$pos . '$s';
            } else {
                $segments[] = (int) $query['mesid'];
            }

            unset($query['mesid']);
        }

        // Support URIs like: /forum/user/128-matias
        if (isset($query['userid']) && $view == 'user') {
            if ($query['userid'] == '@') {
                $segments[] = '%' . ++$pos . '$s';
            } else {
                $userid = (int) $query['userid'];

                if (!\array_key_exists($userid, $userSlugCache)) {
                    $userSlugCache[$userid] = KunenaRoute::stringURLSafe((string) KunenaUserHelper::get($userid)->getName());
                }

                $segments[] = $userid . '-' . $userSlugCache[$userid];
            }

            unset($query['userid']);
        }

        unset($query['view'], $query['layout']);

        // Rest of the known parameters are in var-value form
        foreach (KunenaRoute::$parsevars as $var => $dummy) {
            // A list (id[]=1) cannot be a part of the path: it stays in the query.
            if (isset($query[$var]) && !\is_array($query[$var])) {
                $segments[] = "{$var}-{$query[$var]}";
                unset($query[$var]);
            }
        }

        if (isset($query['start']) && $query['start'] == '@' && $query['start'] !== 0) {
            $query['start'] = '%' . ++$pos . '$d';
        }

        KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __FUNCTION__ . '()') : null;

        return $segments;
    }

    /**
     * @param   array  $segments  segments
     *
     * @return  array
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function parse(&$segments)
    {
        // If Kunena Forum isn't installed do nothing
        if (!class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') || !KunenaForum::isCompatible('7.1') || !KunenaForum::installed()) {
            return [];
        }

        $profiler = Profiler::getInstance('Application');
        KUNENA_PROFILER ? $profiler->mark('kunenaRoute') : null;
        $starttime = microtime(true);

        // Get current menu item and get query variables from it
        $active = Factory::getApplication()->getMenu()->getActive();
        $vars   = isset($active->query) ? $active->query : ['view' => 'home'];

        if (empty($vars['view']) || $vars['view'] == 'home' || $vars['view'] == 'entrypage') {
            $vars['view'] = '';
        }

        $vars = $this->parseSegments($segments, $vars);

        if (empty($vars['layout'])) {
            $vars['layout'] = 'default';
        }

        KunenaRoute::$time = microtime(true) - $starttime;

        foreach ($vars as $var => $value) {
            KunenaRoute::$current->setVar($var, $value);
        }

        return $vars;
    }

    /**
     * Turn the segments of a SEF URL into the query variables.
     *
     * @param   array  $segments  segments
     * @param   array  $vars      variables of the active menu item
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected function parseSegments(array &$segments, array $vars): array
    {
        $total = \count($segments);

        for ($i = 0; $i < $total; $i++) {
            $segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
        }

        // Use category SEF feature?
        $sefcats = isset($this->getSefViews()[$vars['view']]) && empty($vars['id']);

        // The aliases of all the segments are read with one query (resolveAlias asks for them one after the other). Only an address
        // with category aliases resolves them (a chain of categories starts with one too).
        if ($sefcats && class_exists('Kunena\Forum\Libraries\Route\KunenaRoute') && method_exists('Kunena\Forum\Libraries\Route\KunenaRoute', 'preloadAliases')) {
            KunenaRoute::preloadAliases(array_map(static fn ($segment) => strtr((string) $segment, ':', '-'), $segments));
        }

        // Category of the chain of category aliases (forum/parent/child): the next alias may name one of its descendants
        $chain = 0;

        // The layout of the menu item is only a default until the URL names one
        $menuView  = $vars['view'];
        $inherited = !empty($vars['layout']);

        // Handle all segments
        while (($segment = array_shift($segments)) !== null) {
            // Skip //
            if (!$segment) {
                $chain = 0;
                continue;
            }

            if ($sefcats || $chain) {
                // Find out if we have SEF alias (category, view or layout)
                $alias     = strtr($segment, ':', '-');
                $variables = $this->resolveAlias($alias);

                if ($chain && !(isset($variables['catid']) && $this->isDescendantCategory((int) $variables['catid'], $chain))) {
                    // The chain ends: this segment is handled as any segment after the category
                    $variables = [];
                }

                $chain = 0;

                if ($variables) {
                    $sefcats = false;
                    $chain   = (int) ($variables['catid'] ?? 0);

                    // A category alias identifies a category, it does not decide which view
                    // renders it. resolveAlias() reports view=category alongside the catid,
                    // but that is only a default. When the active menu item already names a
                    // view (the stock "New Topic" item is view=topic&layout=create), the menu
                    // item wins and the alias contributes only the catid. Without this the
                    // union below replaces view=topic with view=category, which combined with
                    // layout=create resolves to no controller and returns a 404.
                    if (!empty($vars['view']) && isset($variables['catid'], $variables['view']) && $variables['view'] === 'category') {
                        unset($variables['view']);
                    }

                    if (isset($variables['layout'])) {
                        $inherited = false;
                    }

                    $vars    = $variables + $vars;
                    continue;
                }
            }

            $sefcats = false;

            // Generate variable and value
            $seg   = explode(':', $segment);
            $var   = array_shift($seg);
            $value = array_shift($seg);

            if (empty($var) && empty($value)) {
                // Skip /-/
                continue;
            }

            if (is_numeric($var)) {
                // Handle variables starting by number
                $value = (int) $var;

                if ($vars['view'] == 'user') {
                    // Special case: User view
                    $var = 'userid';
                } elseif (empty($vars['catid'])) {
                    // First number is always category
                    $var          = 'catid';
                    $vars['view'] = 'category';
                } elseif (empty($vars['id'])) {
                    // Second number is always topic
                    $var          = 'id';
                    $vars['view'] = 'topic';
                    $sefcats      = false;
                } elseif (empty($vars['mesid'])) {
                    // Third number is always message
                    $var          = 'mesid';
                    $vars['view'] = 'topic';
                } else {
                    // Invalid parameter, skip it
                    continue;
                }

                // The layout of the menu item belongs to its view: a topic of a category list has no list layout
                if ($inherited && $vars['view'] !== $menuView && ($var === 'id' || $var === 'mesid')) {
                    unset($vars['layout']);
                    $inherited = false;
                }
            } elseif ($value === null) {
                // Simple variable without value is always either view or layout
                $value = $var;

                if (empty($vars['view']) || ($value == 'topic' && $vars['view'] == 'category')) {
                    // View
                    $var = 'view';

                    // The layout of the menu item belongs to its view, not to the view named in the URL
                    if ($inherited && $value !== $menuView) {
                        unset($vars['layout']);
                        $inherited = false;
                    }
                } elseif (empty($vars['layout']) || $inherited) {
                    // Layout, which replaces the default layout of the menu item
                    $var       = 'layout';
                    $inherited = false;
                } elseif (!empty($vars['view'])) {
                    // Unknown parameter: skip
                    if (!empty($vars['view'])) {
                        continue;
                    }
                } else {
                    // Unknown view or non-existing category
                    $var = 'view';
                }
            }

            $vars[$var] = $value;

            // Handle attachment ID
            if ($var == 'view' && $value == 'attachment') {
                $segment    = array_shift($segments);
                $vars['id'] = (int) $segment;
                $segment    = array_shift($segments);

                if ($segment) {
                    $vars[$segment] = 1;
                }

                $vars['format'] = 'raw';
            }
        }

        return $vars;
    }

    /**
     * Whether Kunena Forum is installed and the SEF URLs are on.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    protected function isEnabled(): bool
    {
        return class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('7.1') && KunenaForum::installed() && KunenaConfig::getInstance()->sef;
    }

    /**
     * The views whose URLs may start with the alias of a category.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected function getSefViews(): array
    {
        return KunenaRoute::$sefviews;
    }

    /**
     * Find the variables of a SEF alias (category, view or layout).
     *
     * @param   string  $alias  alias
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected function resolveAlias(string $alias): array
    {
        if (!class_exists('Kunena\Forum\Libraries\Route\KunenaRoute') || !method_exists('Kunena\Forum\Libraries\Route\KunenaRoute', 'resolveAlias')) {
            return [];
        }

        return KunenaRoute::resolveAlias($alias);
    }

    /**
     * Whether a category is below another one (child, grandchild, ...). Access and the published state are not checked.
     *
     * @param   int  $catid     category
     * @param   int  $ancestor  category above it
     *
     * @return  boolean  False also when the parents of the category form a cycle.
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    protected function isDescendantCategory(int $catid, int $ancestor): bool
    {
        $parents = [];
        $id      = $catid;

        for ($depth = 0; $depth < 100; $depth++) {
            $id = (int) KunenaCategoryHelper::get($id)->parentid;

            if (!$id) {
                return isset($parents[$ancestor]);
            }

            if ($id === $catid || isset($parents[$id])) {
                return false;
            }

            $parents[$id] = true;
        }

        return false;
    }
}
