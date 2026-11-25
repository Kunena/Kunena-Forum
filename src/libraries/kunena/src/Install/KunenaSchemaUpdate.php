<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Install
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Install;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Schema\ChangeItem;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;
use Throwable;

/**
 * The database of a site which runs Kunena from a copy of the code (Git Install, for the developers): no package is
 * installed, so the new statements of the update files (sql/updates/mysql) are never run. Each statement is checked against
 * the database with the change items of Joomla (as System, Database) and the missing ones are applied. Only the files from
 * the version of the schema of the site onwards are read: an old file may add a column which a later version removed.
 *
 * @since   Kunena 8.0
 */
class KunenaSchemaUpdate
{
    /**
     * The update files to read: those from the version of the schema of the site (the same one too, which grows while a
     * version is developed); all of them when the site has no version.
     *
     * @param   string[]  $files    The update files.
     * @param   string    $current  The version of the schema of the site (#__schemas), '' when none.
     *
     * @return  string[]  In the order of the versions.
     *
     * @since   Kunena 8.0
     */
    public static function filesFrom(array $files, string $current): array
    {
        $version = static fn (string $file) => basename(str_replace('\\', '/', $file), '.sql');
        $files   = array_values(array_filter($files, static fn ($file) => $current === '' || version_compare($version($file), $current, '>=')));

        usort($files, static fn ($a, $b) => version_compare($version($a), $version($b)));

        return $files;
    }

    /**
     * The newest version of the update files.
     *
     * @param   string[]  $files  The update files.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function latest(array $files): string
    {
        $sorted = self::filesFrom($files, '');

        return $sorted ? basename(str_replace('\\', '/', (string) end($sorted)), '.sql') : '';
    }

    /**
     * The folder of the update files of this site.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function folderOnSite(): string
    {
        return JPATH_ADMINISTRATOR . '/components/com_kunena/sql/updates/mysql';
    }

    /**
     * The version of the schema of Kunena on this site (#__schemas).
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function currentOnSite(): string
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName('s.version_id'))
            ->from($db->quoteName('#__schemas', 's'))
            ->innerJoin($db->quoteName('#__extensions', 'e') . ' ON ' . $db->quoteName('e.extension_id') . ' = ' . $db->quoteName('s.extension_id'))
            ->where($db->quoteName('e.element') . ' = ' . $db->quote('com_kunena'))
            ->where($db->quoteName('e.type') . ' = ' . $db->quote('component'));

        return (string) $db->setQuery($query)->loadResult();
    }

    /**
     * The statements of the update files which this site needs, checked against its database.
     *
     * @return  array{current: string, latest: string, files: string[], pending: array, applied: int, skipped: int}
     *          pending: the statements which are not applied yet (file, query, items: the change item of Joomla).
     *
     * @since   Kunena 8.0
     */
    public static function statusOnSite(): array
    {
        $db      = Factory::getContainer()->get('DatabaseDriver');
        $all     = (array) glob(self::folderOnSite() . '/*.sql');
        $current = self::currentOnSite();
        $files   = self::filesFrom($all, $current);
        $status  = ['current' => $current, 'latest' => self::latest($all), 'files' => array_map('basename', $files), 'pending' => [], 'applied' => 0, 'skipped' => 0, 'items' => []];

        foreach ($files as $file) {
            foreach (DatabaseDriver::splitSql((string) file_get_contents($file)) as $query) {
                if (trim($query) === '') {
                    continue;
                }

                $item = ChangeItem::getInstance($db, $file, $query);
                $item->check();
                $status['items'][] = $item;

                // 1: done; -2: not done yet; -1: a statement which cannot be checked (data, a statement of another kind).
                if ($item->checkStatus === -2) {
                    $status['pending'][] = ['file' => basename($file), 'query' => trim($query)];
                } elseif ($item->checkStatus === 1) {
                    $status['applied']++;
                } else {
                    $status['skipped']++;
                }
            }
        }

        return $status;
    }

    /**
     * Applies the statements of this site which are not applied yet, and records the newest version of the schema.
     *
     * @return  array{fixed: int, failed: string[], version: string}  failed: the statements which still fail.
     *
     * @since   Kunena 8.0
     */
    public static function updateOnSite(): array
    {
        $status = self::statusOnSite();
        $result = ['fixed' => 0, 'failed' => [], 'version' => $status['latest']];

        foreach ($status['items'] as $item) {
            if ($item->checkStatus !== -2) {
                continue;
            }

            try {
                $item->fix();
            } catch (Throwable $e) {
                // Counted below as failed.
            }

            if ($item->checkStatus === 1) {
                $result['fixed']++;
            } else {
                $result['failed'][] = trim((string) $item->updateQuery);
            }
        }

        if ($status['latest'] !== '' && !$result['failed']) {
            self::recordOnSite($status['latest']);
        }

        return $result;
    }

    /**
     * Records the version of the schema of Kunena on this site.
     *
     * @param   string  $version  The version.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    private static function recordOnSite(string $version): void
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName('extension_id'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_kunena'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));
        $id = (int) $db->setQuery($query)->loadResult();

        if (!$id) {
            return;
        }

        $db->setQuery($db->createQuery()->delete($db->quoteName('#__schemas'))->where($db->quoteName('extension_id') . ' = :id')->bind(':id', $id, ParameterType::INTEGER))->execute();
        $db->setQuery(
            $db->createQuery()->insert($db->quoteName('#__schemas'))->columns($db->quoteName(['extension_id', 'version_id']))
                ->values(':id, :version')->bind(':id', $id, ParameterType::INTEGER)->bind(':version', $version)
        )->execute();
    }
}
