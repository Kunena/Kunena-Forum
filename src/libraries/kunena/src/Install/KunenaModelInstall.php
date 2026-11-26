<?php

/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Install;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Cache\CacheController;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Menu;
use Joomla\CMS\Table\MenuType;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Exception\KunenaException;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Menu\KunenaMenuHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * The steps of the upgrade of the database (kunena.install.upgrade.xml, which the install script of the component runs) and the
 * menu of the forum. The web installer of Kunena 1 to 5, with the migration from FireBoard and JoomlaBoard, is gone: Joomla installs
 * and updates the component, and its update runs the blocks of the upgrade file which are newer than the installed version.
 *
 * @since   Kunena 6.0
 */
class KunenaModelInstall extends BaseDatabaseModel
{
    /**
     * The database.
     *
     * @var    DatabaseDriver|null
     * @since  Kunena 6.0
     */
    private ?DatabaseDriver $db = null;

    /**
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function __construct()
    {
        // The texts of the steps, from the component (English first, then the language of the site over it). They were looked for
        // in the folder of the library, where they are not: a step had its key as its name.
        $lang   = Factory::getApplication()->getLanguage();
        $folder = JPATH_ADMINISTRATOR . '/components/com_kunena';

        foreach (['com_kunena.install', 'com_kunena.libraries'] as $file) {
            $lang->load($file, $folder, 'en-GB');
            $lang->load($file, $folder);
        }

        parent::__construct();
        $this->db = Factory::getContainer()->get('DatabaseDriver');

        // A step of the upgrade can take long (the files of the private attachments are renamed): it is not stopped half-way when
        // the administrator leaves the page, nor by a short time limit of PHP.
        if (\function_exists('ignore_user_abort')) {
            ignore_user_abort(true);
        }

        @set_time_limit(300);
    }

    /**
     * @param   object  $action  action
     *
     * @return array|null
     *
     * @since   Kunena 6.0
     */
    public function processUpgradeXMLNode(object $action): ?array
    {
        $result   = null;
        $nodeName = $action->getName();
        $mode     = strtolower((string) $action['mode']);
        $success  = false;

        switch ($nodeName) {
            case 'phpfile':
                $filename = $action['name'];
                $include  = JPATH_ADMINISTRATOR . "/components/com_kunena/install/sql/updates/php/{$filename}.php";
                $function = 'kunena_' . strtr($filename, ['.' => '', '-' => '_']);

                if (is_file($include)) {
                    require $include;

                    if (\is_callable($function)) {
                        $result = \call_user_func($function, $this);

                        if (\is_array($result)) {
                            $success = $result['success'];
                        } else {
                            $success = true;
                        }
                    }
                }

                if (!$success && !$result) {
                    $result = ['action' => Text::_('COM_KUNENA_INSTALL_INCLUDE_STATUS'), 'name' => $filename . '.php', 'success' => $success];
                }

                break;
            case 'query':
                $query = (string) $action;
                $this->db->setQuery($query);

                $success = true;

                try {
                    $this->db->execute();
                } catch (Exception $e) {
                    $success = false;
                }

                if ($action['mode'] == 'silenterror' || $success) {
                    $result = null;
                } else {
                    $result = ['action' => 'SQL Query: ' . $query, 'name' => '', 'success' => $success];
                }
                break;
            default:
                $result = ['action' => 'fail', 'name' => $nodeName, 'success' => false];
        }

        return $result;
    }

    /**
     * Create a Joomla menu for the main
     * navigation tab and publish it in the Kunena module position kunena_menu.
     * In addition it checks if there is a link to Kunena in any of the menus
     * and if not, adds a forum link in the mainmenu.
     *
     * @return  void
     *
     * @throws  Exception
     * @throws  KunenaException
     * @since   Kunena 6.0
     */
    public function createMenu(): void
    {
        KunenaFactory::loadLanguage('com_kunena.install', 'admin');
        $menu = [
            'name'   => Text::_('COM_KUNENA_MENU_ITEM_FORUM'),
            'alias'  => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_FORUM_ALIAS'), 'forum'),
            'link'   => 'index.php?option=com_kunena&view=home',
            'access' => 1,
            'params' => ['catids' => 0],
        ];

        $this->buildMenu($menu);
        KunenaMenuHelper::cleanCache();
    }

    /**
     * Build the Kunena menu
     *
     * @param   array  $menu  menu
     *
     * @return  boolean
     *
     * @throws  KunenaException
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function buildMenu(array $menu)
    {
        $config = KunenaFactory::getConfig();

        $component_id = (int) ComponentHelper::getComponent('com_kunena')->id;

        KunenaFactory::loadLanguage('com_kunena.install', 'admin');
        $languages = LanguageHelper::getLanguages('default');
        $langCode  = $languages[0]->lang_code;

        // First fix all broken menu items
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->update($db->quoteName('#__menu'))
            ->set($db->quoteName('component_id') . ' = ' . $component_id)
            ->where("link LIKE '%option=com_kunena%'")
            ->andWhere('type = "component"');
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (Exception $e) {
            throw new KunenaException($e->getMessage(), $e->getCode());
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new MenuType($db);
        $data  = [
            'menutype'    => 'kunenamenu',
            'title'       => Text::_('COM_KUNENA_MENU_TITLE'),
            'description' => Text::_('COM_KUNENA_MENU_TITLE_DESC'),
        ];

        if (!$table->bind($data) || !$table->check()) {
            // Menu already exists, do nothing
            return true;
        }

        try {
            $table->store();
        } catch (Exception $e) {
            throw new KunenaException($e->getMessage(), $e->getCode());
        }

        // The menu items are made for every published content language of a multilingual site, else only for the
        // default language.
        $contentLanguages = array_keys(LanguageHelper::getContentLanguages([1], false));
        $multilingual     = \count($contentLanguages) > 1;
        $codes            = $multilingual ? $contentLanguages : [$langCode];
        $currentTag       = Factory::getApplication()->getLanguage()->getTag();

        foreach ($codes as $code) {
            $this->buildMenuForLanguage($menu, $component_id, $code, $multilingual ? $code : '*', $multilingual);
        }

        // Back to the strings of the language of the page.
        Factory::getApplication()->getLanguage()->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena', $currentTag, true);

        return true;
    }

    /**
     * Build the Kunena menu items of one language: the forum item (for all languages on a single language site, else
     * for the language), its sub items with the titles and aliases of the language, the default page of the forum
     * and the alias in the default menu.
     *
     * @param   array   $menu            The forum item (link, access and params).
     * @param   int     $component_id    The id of the component Kunena.
     * @param   string  $langCode        The language of the titles and aliases, like en-GB.
     * @param   string  $parentLanguage  The language of the forum item and of its sub items: * on a single language site, else the language.
     * @param   bool    $multilingual    Are the items made for several languages.
     *
     * @return  boolean
     *
     * @throws  KunenaException
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected function buildMenuForLanguage(array $menu, int $component_id, string $langCode, string $parentLanguage, bool $multilingual): bool
    {
        $config = KunenaFactory::getConfig();

        // The strings of the language (the English ones first, for what a language does not have yet).
        $lang = Factory::getApplication()->getLanguage();
        $lang->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena', 'en-GB', true);
        $lang->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena', $langCode, true);

        if ($multilingual) {
            $menu['name']  = Text::_('COM_KUNENA_MENU_ITEM_FORUM');
            $menu['alias'] = KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_FORUM_ALIAS'), 'forum');
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new Menu($db);
        $table->load(['menutype' => 'kunenamenu', 'link' => $menu['link'], 'language' => $parentLanguage]);
        $paramdata = [
            'menu-anchor_title'     => '',
            'menu-anchor_css'       => '',
            'menu_image'            => '',
            'menu_text'             => 1,
            'page_title'            => '',
            'show_page_heading'     => 0,
            'page_heading'          => '',
            'pageclass_sfx'         => '',
            'menu-meta_description' => '',
            'robots'                => '',
            'secure'                => 0,
        ];

        $gparams = new Registry($paramdata);

        $params = clone $gparams;
        $params->loadArray($menu['params']);
        $data = [
            'menutype'     => 'kunenamenu',
            'title'        => $menu['name'],
            'alias'        => $menu['alias'],
            'link'         => $menu['link'],
            'type'         => 'component',
            'published'    => 1,
            'parentid'     => 1,
            'component_id' => $component_id,
            'access'       => $menu['access'],
            'params'       => (string) $params,
            'home'         => 0,
            'language'     => $parentLanguage,
            'client_id'    => 0,
        ];
        $table->setLocation(1, 'last-child');

        if (!$table->bind($data) || !$table->check() || !$table->store()) {
            $table->alias = 'kunena';

            if (!$table->check() || !$table->store()) {
                // Menu already exists, do nothing
                return true;
            }
        }

        $parent      = $table;
        $defaultmenu = 0;

        $submenu = [
            'index' => [
                'name'    => Text::_('COM_KUNENA_MENU_ITEM_INDEX'),
                'alias'   => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_INDEX_ALIAS'), 'index'),
                'link'    => 'index.php?option=com_kunena&view=category&layout=list',
                'access'  => 1,
                'default' => 'categories',
                'params'  => [],
            ],
            'recent' => [
                'name'    => Text::_('COM_KUNENA_MENU_ITEM_RECENT'),
                'alias'   => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_RECENT_ALIAS'), 'recent'),
                'link'    => 'index.php?option=com_kunena&view=topics&mode=replies',
                'access'  => 1,
                'default' => 'recent',
                'params'  => ['topics_catselection' => '', 'topics_categories' => '', 'topics_time' => ''],
            ],
            'unread' => [
                'name'   => Text::_('COM_KUNENA_MENU_ITEM_UNREAD'),
                'alias'  => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_UNREAD_ALIAS'), 'unread'),
                'link'   => 'index.php?option=com_kunena&view=topics&layout=unread',
                'access' => 2,
                'params' => [],
            ],
            'newtopic' => [
                'name'   => Text::_('COM_KUNENA_MENU_ITEM_NEWTOPIC'),
                'alias'  => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_NEWTOPIC_ALIAS'), 'newtopic'),
                'link'   => 'index.php?option=com_kunena&view=topic&layout=create',
                'access' => 2,
                'params' => [],
            ],
            'noreplies' => [
                'name'   => Text::_('COM_KUNENA_MENU_ITEM_NOREPLIES'),
                'alias'  => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_NOREPLIES_ALIAS'), 'noreplies'),
                'link'   => 'index.php?option=com_kunena&view=topics&mode=noreplies',
                'access' => 2,
                'params' => ['topics_catselection' => '', 'topics_categories' => '', 'topics_time' => ''],
            ],
            'mylatest' => [
                'name'    => Text::_('COM_KUNENA_MENU_ITEM_MYLATEST'),
                'alias'   => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_MYLATEST_ALIAS'), 'mylatest'),
                'link'    => 'index.php?option=com_kunena&view=topics&layout=user&mode=default',
                'access'  => 2,
                'default' => 'my',
                'params'  => ['topics_catselection' => '2', 'topics_categories' => '0', 'topics_time' => ''],
            ],
            'profile' => [
                'name'   => Text::_('COM_KUNENA_MENU_ITEM_PROFILE'),
                'alias'  => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_PROFILE_ALIAS'), 'profile'),
                'link'   => 'index.php?option=com_kunena&view=user',
                'access' => 2,
                'params' => ['integration' => 1],
            ],
            'help' => [
                'name'   => Text::_('COM_KUNENA_MENU_ITEM_HELP'),
                'alias'  => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_HELP_ALIAS'), 'help'),
                'link'   => 'index.php?option=com_kunena&view=misc',
                'access' => 3,
                'params' => ['body' => Text::_('COM_KUNENA_MENU_HELP_BODY'), 'body_format' => 'bbcode'],
            ],
            'search' => [
                'name'   => Text::_('COM_KUNENA_MENU_ITEM_SEARCH'),
                'alias'  => KunenaRoute::stringURLSafe(Text::_('COM_KUNENA_MENU_SEARCH_ALIAS'), 'search'),
                'link'   => 'index.php?option=com_kunena&view=search',
                'access' => 1,
                'params' => [],
            ],
        ];

        foreach ($submenu as $menuitem) {
            $params = clone $gparams;
            $params->loadArray($menuitem['params']);
            $db    = Factory::getContainer()->get(DatabaseInterface::class);
            $table = new Menu($db);
            $table->load(['menutype' => 'kunenamenu', 'link' => $menuitem['link'], 'language' => $parentLanguage]);
            $data = [
                'menutype'     => 'kunenamenu',
                'title'        => $menuitem['name'],
                'alias'        => $menuitem['alias'],
                'link'         => $menuitem['link'],
                'type'         => 'component',
                'published'    => 1,
                'parentid'     => $parent->id,
                'component_id' => $component_id,
                'access'       => $menuitem['access'],
                'params'       => (string) $params,
                'home'         => 0,
                'language'     => $parentLanguage,
                'client_id'    => 0,
            ];
            $table->setLocation($parent->id, 'last-child');

            if (!$table->bind($data) || !$table->check() || !$table->store()) {
                throw new KunenaException($table->getError());
            }

            if (!$defaultmenu || (isset($menuitem['default']) && $config->defaultPage == $menuitem['default'])) {
                $defaultmenu = $table->id;
            }
        }

        // Update forum menuitem to point into default page
        $parent->link .= "&defaultmenu={$defaultmenu}";

        if (!$parent->check() || !$parent->store()) {
            throw new KunenaException($table->getError());
        }

        // Finally create alias
        // On a multilingual site every language has its own home item.
        $siteMenu    = AbstractMenu::getInstance('site');
        $defaultmenu = ($multilingual ? $siteMenu->getDefault($parentLanguage) : null) ?: $siteMenu->getDefault();

        if (!$defaultmenu) {
            return true;
        }

        // The alias of an item of one language can not be the alias of an item for all languages.
        $aliasName = 'kunena-' . Factory::getDate()->format('Y-m-d') . ($multilingual ? '-' . strtolower($langCode) : '');

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new Menu($db);
        $table->load(['menutype' => $defaultmenu->menutype, 'type' => 'alias', 'title' => Text::_('COM_KUNENA_MENU_ITEM_FORUM'), 'language' => $parentLanguage]);

        if (!$table->id) {
            $data = [
                'menutype'          => $defaultmenu->menutype,
                'title'             => Text::_('COM_KUNENA_MENU_ITEM_FORUM'),
                'alias'             => $aliasName,
                'note'              => '',
                'link'              => 'index.php?Itemid=' . $parent->id,
                'type'              => 'alias',
                'published'         => 1,
                'parent_id'         => 1,
                'component_id'      => 0,
                'checked_out'       => null,
                'checked_out_time'  => null,
                'browserNav'        => 0,
                'access'            => 1,
                'img'               => '',
                'template_style_id' => 0,
                'params'            => '{"aliasoptions":"' . (int) $parent->id . '","menu-anchor_title":"","menu-anchor_css":"","menu_image":""}',
                'home'              => 0,
                'language'          => $parentLanguage,
                'client_id'         => 0,
            ];

            $table->setLocation(1, 'last-child');
        } else {
            $data = [
                'alias'  => $aliasName,
                'link'   => 'index.php?Itemid=' . $parent->id,
                'params' => '{"aliasoptions":"' . (int) $parent->id . '","menu-anchor_title":"","menu-anchor_css":"","menu_image":""}',
            ];
        }

        try {
            $table->bind($data);
        } catch (Exception $e) {
            throw new KunenaException($e->getMessage(), $e->getCode());
        }

        if (!$table->check() || !$table->store()) {
            // Menu already exists, do nothing
            return true;
        }

        return true;
    }

    // TODO: move to migration

    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function deleteMenu(): void
    {
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new MenuType($db);
        $table->load(['menutype' => 'kunenamenu']);

        if ($table->id) {
            try {
                $table->delete();
            } catch (Exception $e) {
                Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            }
        }

        /** @var Cache|CacheController $cache */
        $cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)->createCacheController();
        $cache->clean('mod_menu');
    }
}
