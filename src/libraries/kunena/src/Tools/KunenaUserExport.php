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

use Joomla\CMS\Event\Privacy\ExportRequestEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Component\Privacy\Administrator\Table\RequestTable;
use Joomla\Database\ParameterType;
use RuntimeException;
use ZipArchive;

/**
 * Everything a user has in the forum in one file (tool "User data export"), for a request of the user about his data: a
 * ZIP with the data as JSON and his attachments. The data is that of the privacy plugin of Kunena, the one which answers
 * the export requests of Joomla: one list of what a user has, not a second one.
 *
 * @since   Kunena 8.0
 */
class KunenaUserExport
{
    /**
     * The data of the privacy domains as plain arrays: name => list of items (field => value).
     *
     * @param   iterable  $domains  The domains (Joomla\Component\Privacy\Administrator\Export\Domain).
     *
     * @return  array<string, array>
     *
     * @since   Kunena 8.0
     */
    public static function domainsToArray(iterable $domains): array
    {
        $data = [];

        foreach ($domains as $domain) {
            $items = [];

            foreach ($domain->getItems() as $item) {
                $fields = [];

                foreach ($item->getFields() as $field) {
                    $fields[(string) $field->name] = $field->value;
                }

                $items[] = $fields;
            }

            $data[(string) $domain->name] = $items;
        }

        return $data;
    }

    /**
     * The name of a file of the ZIP: no folder, no character which a system refuses.
     *
     * @param   int     $id    The attachment.
     * @param   string  $name  Its name.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function entryName(int $id, string $name): string
    {
        // The part after the last slash (basename() of Windows takes "a:" for a drive), then no character which a system refuses.
        $name = str_replace('\\', '/', $name);
        $name = substr($name, (int) strrpos('/' . $name, '/'));
        $name = trim((string) preg_replace('#[:*?"<>|\x00-\x1f]+#', '_', $name), '. ');

        return 'attachments/' . $id . '-' . ($name !== '' ? $name : 'file');
    }

    /**
     * The data of a user of this site, from the privacy plugin of Kunena.
     *
     * @param   int  $userid  The user.
     *
     * @return  array<string, array>
     *
     * @since   Kunena 8.0
     *
     * @throws  RuntimeException  The user or the plugin is missing.
     */
    public static function dataOnSite(int $userid): array
    {
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($userid);

        if (!$user->id) {
            throw new RuntimeException('COM_KUNENA_PROFILE_NO_USER');
        }

        $app = Factory::getApplication();

        // The privacy plugin of Kunena, even when the privacy component of Joomla does not use it.
        $plugin         = $app->bootPlugin('kunena', 'privacy');
        $request        = new RequestTable(Factory::getContainer()->get('DatabaseDriver'));
        $request->email = (string) $user->email;

        $event = new ExportRequestEvent('onPrivacyExportRequest', ['subject' => $request, 'user' => $user]);
        $plugin->onPrivacyExportRequest($event);

        $domains = [];

        foreach ((array) $event->getArgument('result', []) as $result) {
            foreach ((array) $result as $domain) {
                $domains[] = $domain;
            }
        }

        return ['user' => [['id' => (int) $user->id, 'username' => (string) $user->username, 'name' => (string) $user->name,
            'email'              => (string) $user->email, 'registered' => (string) $user->registerDate]]] + self::domainsToArray($domains);
    }

    /**
     * Writes the ZIP of a user of this site.
     *
     * @param   int     $userid  The user.
     * @param   string  $file    The path of the ZIP.
     *
     * @return  array{items: int, files: int}
     *
     * @since   Kunena 8.0
     *
     * @throws  RuntimeException  The ZIP cannot be written.
     */
    public static function writeOnSite(int $userid, string $file): array
    {
        $data = self::dataOnSite($userid);
        $zip  = new ZipArchive();

        if ($zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('COM_KUNENA_TOOLS_EXPORT_ERROR_ZIP');
        }

        $zip->addFromString('data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'folder', 'filename', 'filename_real']))
            ->from($db->quoteName('#__kunena_attachments'))
            ->where($db->quoteName('userid') . ' = :id')
            ->bind(':id', $userid, ParameterType::INTEGER);
        $files = 0;
        $root  = realpath(JPATH_ROOT . '/media/kunena/attachments');

        foreach ((array) $db->setQuery($query)->loadAssocList() as $row) {
            $path = realpath(JPATH_ROOT . '/' . trim((string) $row['folder'], '/') . '/' . $row['filename']);

            // Only a file of the folder of the attachments.
            if ($path && $root && str_starts_with($path, $root . DIRECTORY_SEPARATOR) && is_file($path)) {
                $zip->addFile($path, self::entryName((int) $row['id'], (string) ($row['filename_real'] ?: $row['filename'])));
                $files++;
            }
        }

        $zip->close();

        return ['items' => array_sum(array_map('count', $data)), 'files' => $files];
    }
}
