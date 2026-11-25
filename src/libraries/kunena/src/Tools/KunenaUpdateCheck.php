<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tools;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Throwable;

/**
 * Is the site ready for an update of Kunena (tool "Before you update")? The versions of PHP, Joomla and the database, the
 * extensions of PHP, an update found by Joomla, the database of Kunena, the overrides and the templates which an update
 * can break, the plugins of other developers, and a recent backup.
 *
 * @since   Kunena 8.0
 */
class KunenaUpdateCheck
{
    /**
     * The oldest PHP which this Kunena installs on (the package script: src/script.php).
     *
     * @since   Kunena 8.0
     */
    public const MIN_PHP = '8.4.1';

    /**
     * The oldest Joomla (the package script).
     *
     * @since   Kunena 8.0
     */
    public const MIN_JOOMLA = '6.2.1';

    /**
     * The oldest MySQL (the package script).
     *
     * @since   Kunena 8.0
     */
    public const MIN_MYSQL = '8.0.16';

    /**
     * The oldest MariaDB (the package script).
     *
     * @since   Kunena 8.0
     */
    public const MIN_MARIADB = '10.6';

    /**
     * The extensions of PHP which Kunena needs (the package script).
     *
     * @since   Kunena 8.0
     */
    public const EXTENSIONS = ['dom', 'gd', 'json', 'pcre', 'SimpleXML', 'fileinfo', 'mbstring'];

    /**
     * The templates of Kunena which come with it.
     *
     * @since   Kunena 8.0
     */
    public const SHIPPED_TEMPLATES = ['agora', 'aurelia', 'system'];

    /**
     * A backup older than this many days is old.
     *
     * @since   Kunena 8.0
     */
    public const BACKUP_DAYS = 7;

    public const OK      = 'ok';

    public const WARNING = 'warning';

    public const PROBLEM = 'problem';

    public const INFO    = 'info';

    /**
     * The status of a version against its minimum.
     *
     * @param   string  $version  The version.
     * @param   string  $minimum  The minimum.
     *
     * @return  string  OK or PROBLEM.
     *
     * @since   Kunena 8.0
     */
    public static function versionStatus(string $version, string $minimum): string
    {
        return version_compare(self::clean($version), $minimum, '>=') ? self::OK : self::PROBLEM;
    }

    /**
     * A version without its suffix of the distribution ("8.4.3-1+ubuntu" => "8.4.3", "10.11.6-MariaDB" => "10.11.6").
     *
     * @param   string  $version  The version.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function clean(string $version): string
    {
        return preg_match('/^\d+(?:\.\d+){0,2}/', $version, $m) ? $m[0] : $version;
    }

    /**
     * The newest version of the update files of the database (8.0.0-DEV.sql...).
     *
     * @param   string[]  $files  The names of the files.
     *
     * @return  string  The version, '' without a file.
     *
     * @since   Kunena 8.0
     */
    public static function latestSchema(array $files): string
    {
        $versions = [];

        foreach ($files as $file) {
            if (preg_match('/^(\d+\.\d+\.\d+[\w.\-]*)\.sql$/i', basename((string) $file), $m)) {
                $versions[] = $m[1];
            }
        }

        usort($versions, 'version_compare');

        return $versions ? (string) end($versions) : '';
    }

    /**
     * The plugins of the package of Kunena, from its manifest: "group/element" => true. Null when the manifest is not read.
     *
     * @param   string  $manifest  The XML of pkg_kunena.xml.
     *
     * @return  array<string, bool>|null
     *
     * @since   Kunena 8.0
     */
    public static function shippedPlugins(string $manifest): ?array
    {
        $xml = $manifest !== '' ? @simplexml_load_string($manifest) : false;

        if (!$xml) {
            return null;
        }

        $plugins = [];

        foreach ($xml->xpath('//files/file[@type="plugin"]') ?: [] as $file) {
            $plugins[(string) $file['group'] . '/' . (string) $file['id']] = true;
        }

        return $plugins;
    }

    /**
     * The checks of this site.
     *
     * @return  array  A list of [key, status, value (text), details (list of texts)].
     *
     * @since   Kunena 8.0
     */
    public static function checksOnSite(): array
    {
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $checks = [];

        $checks[] = ['php', self::versionStatus(PHP_VERSION, self::MIN_PHP), PHP_VERSION, [self::MIN_PHP]];
        $checks[] = ['joomla', self::versionStatus(JVERSION, self::MIN_JOOMLA), JVERSION, [self::MIN_JOOMLA]];

        $dbVersion = (string) $db->getVersion();
        $mariadb   = stripos($dbVersion, 'mariadb') !== false || (method_exists($db, 'isMariaDb') && $db->isMariaDb());
        $minimum   = $mariadb ? self::MIN_MARIADB : self::MIN_MYSQL;
        $checks[]  = ['database', $db->getServerType() === 'mysql' ? self::versionStatus($dbVersion, $minimum) : self::PROBLEM,
            ($mariadb ? 'MariaDB ' : 'MySQL ') . self::clean($dbVersion), [$minimum]];

        $missing  = array_values(array_filter(self::EXTENSIONS, static fn ($extension) => !\extension_loaded($extension)));
        $checks[] = ['extensions', $missing ? self::PROBLEM : self::OK, (string) \count(self::EXTENSIONS), $missing];

        $checks[] = self::updateCheck($db);
        $checks[] = self::schemaCheck($db);
        $checks[] = self::overridesCheck();
        $checks[] = self::templatesCheck();
        $checks[] = self::pluginsCheck($db);
        $checks[] = self::backupCheck($db);

        return $checks;
    }

    /**
     * An update of Kunena found by Joomla.
     *
     * @param   object  $db  The database.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    private static function updateCheck($db): array
    {
        try {
            $query = $db->createQuery()
                ->select($db->quoteName(['version', 'infourl']))
                ->from($db->quoteName('#__updates'))
                ->where($db->quoteName('element') . ' = ' . $db->quote('pkg_kunena'));
            $update = $db->setQuery($query)->loadAssoc();
        } catch (Throwable $e) {
            $update = null;
        }

        return $update
            ? ['update', self::INFO, (string) $update['version'], array_filter([(string) $update['infourl']])]
            : ['update', self::INFO, '', []];
    }

    /**
     * The version of the database of Kunena against its newest update file.
     *
     * @param   object  $db  The database.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    private static function schemaCheck($db): array
    {
        $latest = self::latestSchema((array) glob(JPATH_ADMINISTRATOR . '/components/com_kunena/sql/updates/mysql/*.sql'));

        try {
            $query = $db->createQuery()
                ->select($db->quoteName('s.version_id'))
                ->from($db->quoteName('#__schemas', 's'))
                ->innerJoin($db->quoteName('#__extensions', 'e') . ' ON ' . $db->quoteName('e.extension_id') . ' = ' . $db->quoteName('s.extension_id'))
                ->where($db->quoteName('e.element') . ' = ' . $db->quote('com_kunena'))
                ->where($db->quoteName('e.type') . ' = ' . $db->quote('component'));
            $current = (string) $db->setQuery($query)->loadResult();
        } catch (Throwable $e) {
            $current = '';
        }

        return ['schema', $latest === '' || $current === $latest ? self::OK : self::WARNING, $current, [$latest]];
    }

    /**
     * The overrides of the layouts of Kunena in the templates of the site.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    private static function overridesCheck(): array
    {
        $found = [];

        foreach ((array) glob(JPATH_SITE . '/templates/*', GLOB_ONLYDIR) as $template) {
            foreach (['html/com_kunena', 'html/layouts/com_kunena'] as $folder) {
                if (is_dir($template . '/' . $folder)) {
                    $found[] = basename($template) . '/' . $folder;
                }
            }
        }

        return ['overrides', $found ? self::WARNING : self::OK, (string) \count($found), $found];
    }

    /**
     * The templates of Kunena which do not come with it (an update does not update them).
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    private static function templatesCheck(): array
    {
        $found = [];

        foreach ((array) glob(JPATH_SITE . '/components/com_kunena/template/*', GLOB_ONLYDIR) as $folder) {
            $name = basename($folder);

            if (!\in_array($name, self::SHIPPED_TEMPLATES, true)) {
                $xml     = is_file($folder . '/' . $name . '.xml') ? @simplexml_load_file($folder . '/' . $name . '.xml') : false;
                $found[] = $name . ($xml && (string) $xml->version !== '' ? ' ' . (string) $xml->version : '');
            }
        }

        return ['templates', $found ? self::WARNING : self::OK, (string) \count($found), $found];
    }

    /**
     * The plugins for Kunena of other developers (an update of Kunena can need an update of them).
     *
     * @param   object  $db  The database.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    private static function pluginsCheck($db): array
    {
        $query = $db->createQuery()
            ->select($db->quoteName(['name', 'folder', 'element', 'enabled', 'manifest_cache']))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where('(' . $db->quoteName('folder') . ' = ' . $db->quote('kunena') . ' OR ' . $db->quoteName('element') . ' LIKE ' . $db->quote('%kunena%') . ')');
        $found   = [];
        $shipped = self::shippedPlugins((string) @file_get_contents(JPATH_ADMINISTRATOR . '/manifests/packages/pkg_kunena.xml'));

        foreach ((array) $db->setQuery($query)->loadAssocList() as $plugin) {
            $manifest = json_decode((string) $plugin['manifest_cache'], true) ?: [];

            // A plugin of the package is updated with Kunena, whoever wrote it (EasyBlog, EasySocial...).
            $own = $shipped !== null
                ? isset($shipped[$plugin['folder'] . '/' . $plugin['element']])
                : stripos((string) ($manifest['author'] ?? ''), 'kunena') !== false;

            if (!$own) {
                $found[] = $plugin['folder'] . '/' . $plugin['element'] . ' ' . (string) ($manifest['version'] ?? '?')
                    . ' (' . (string) ($manifest['author'] ?? '?') . ')' . ((int) $plugin['enabled'] ? '' : ' ×');
            }
        }

        return ['plugins', $found ? self::WARNING : self::OK, (string) \count($found), $found];
    }

    /**
     * The last backup of Akeeba Backup, when it is installed.
     *
     * @param   object  $db  The database.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    private static function backupCheck($db): array
    {
        $prefix = $db->getPrefix();
        $tables = array_map('strtolower', (array) $db->getTableList());

        foreach (['akeebabackup_backups', 'ak_stats'] as $table) {
            if (!\in_array(strtolower($prefix . $table), $tables, true)) {
                continue;
            }

            try {
                $query = $db->createQuery()
                    ->select('MAX(' . $db->quoteName('backupstart') . ')')
                    ->from($db->quoteName('#__' . $table))
                    ->where($db->quoteName('status') . ' = ' . $db->quote('complete'));
                $last = (string) $db->setQuery($query)->loadResult();
            } catch (Throwable $e) {
                $last = '';
            }

            $recent = $last !== '' && strtotime($last . ' UTC') >= time() - self::BACKUP_DAYS * 86400;

            return ['backup', $recent ? self::OK : self::WARNING, $last, ['akeeba']];
        }

        return ['backup', self::WARNING, '', []];
    }
}
