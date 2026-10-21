<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Inbox
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Inbox\Helper;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * The files which are sent with a private message. They are stored with a random name in a folder which the web server does
 * not serve, and they are only given to the sender and the recipient of the message.
 *
 * @since   Kunena 7.2
 */
class InboxAttachments
{
    /**
     * Types which are never accepted, whatever the configuration of the forum says.
     *
     * @var     string[]
     * @since   Kunena 7.2
     */
    protected const FORBIDDEN = ['php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'inc', 'cgi', 'pl', 'py', 'sh', 'exe', 'bat', 'cmd', 'com', 'js', 'html', 'htm', 'xhtml', 'svg', 'htaccess', 'ini'];

    /**
     * @return  DatabaseInterface
     *
     * @since   Kunena 7.2
     */
    protected static function db(): DatabaseInterface
    {
        return Factory::getContainer()->get(DatabaseInterface::class);
    }

    /**
     * @return  string  The folder of the files (it is created with a file which keeps the web server from serving it).
     *
     * @since   Kunena 7.2
     */
    protected static function folder(): string
    {
        $folder = JPATH_ROOT . '/media/kunena/inbox';

        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        if (!is_file($folder . '/.htaccess')) {
            file_put_contents($folder . '/.htaccess', "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
        }

        if (!is_file($folder . '/index.html')) {
            file_put_contents($folder . '/index.html', '<!DOCTYPE html><title></title>');
        }

        return $folder;
    }

    /**
     * The extensions which are allowed (the file and image types of the forum), without the forbidden ones.
     *
     * @return  string[]
     *
     * @since   Kunena 7.2
     */
    protected static function extensions(): array
    {
        $config = KunenaFactory::getConfig();
        $list   = array_merge(explode(',', (string) $config->imageTypes), explode(',', (string) $config->fileTypes));
        $list   = array_map(static fn ($type) => strtolower(trim($type)), $list);

        return array_values(array_diff(array_filter(array_unique($list), 'strlen'), self::FORBIDDEN));
    }

    /**
     * The files of the request in a list: one array per file.
     *
     * @param   string  $field  The name of the file field.
     *
     * @return  array[]
     *
     * @since   Kunena 7.2
     */
    public static function getUploads(string $field = 'files'): array
    {
        $files = $_FILES[$field] ?? null;

        if (!$files || !isset($files['name'])) {
            return [];
        }

        $list = [];

        foreach ((array) $files['name'] as $index => $name) {
            if ($name === '' || (int) $files['error'][$index] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $list[] = ['name' => (string) $name, 'tmp_name' => $files['tmp_name'][$index], 'size' => (int) $files['size'][$index], 'error' => (int) $files['error'][$index]];
        }

        return $list;
    }

    /**
     * Check the files of a message before it is sent.
     *
     * @param   array[]   $uploads  The files.
     * @param   Registry  $params   The parameters of the plugin.
     *
     * @return  void
     *
     * @throws  \RuntimeException
     * @since   Kunena 7.2
     */
    public static function check(array $uploads, Registry $params): void
    {
        if (!$uploads) {
            return;
        }

        $max = max(1, (int) $params->get('attachmentMax', 3));

        if (!$params->get('attachments', 1) || \count($uploads) > $max) {
            throw new \RuntimeException(Text::sprintf('PLG_KUNENA_INBOX_ERROR_FILE_COUNT', $params->get('attachments', 1) ? $max : 0));
        }

        $config     = KunenaFactory::getConfig();
        $images     = array_map('trim', explode(',', strtolower((string) $config->imageTypes)));
        $allowed    = self::extensions();

        foreach ($uploads as $upload) {
            $name      = basename($upload['name']);
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if ($upload['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($upload['tmp_name'])) {
                throw new \RuntimeException(Text::sprintf('PLG_KUNENA_INBOX_ERROR_FILE_UPLOAD', $name));
            }

            if (!\in_array($extension, $allowed, true)) {
                throw new \RuntimeException(Text::sprintf('PLG_KUNENA_INBOX_ERROR_FILE_TYPE', $name));
            }

            $isImage = \in_array($extension, $images, true);
            $limit   = (int) ($isImage ? $config->imageSize : $config->fileSize) * 1024;

            if ($limit > 0 && $upload['size'] > $limit) {
                throw new \RuntimeException(Text::sprintf('PLG_KUNENA_INBOX_ERROR_FILE_SIZE', $name));
            }

            // An image has to be an image.
            if ($isImage && @getimagesize($upload['tmp_name']) === false) {
                throw new \RuntimeException(Text::sprintf('PLG_KUNENA_INBOX_ERROR_FILE_TYPE', $name));
            }
        }
    }

    /**
     * Store the (checked) files of a message.
     *
     * @param   int      $inboxId  The id of the message.
     * @param   int      $userId   The sender.
     * @param   array[]  $uploads  The files.
     *
     * @return  void
     *
     * @throws  \RuntimeException
     * @since   Kunena 7.2
     */
    public static function store(int $inboxId, int $userId, array $uploads): void
    {
        $db     = self::db();
        $folder = self::folder();

        foreach ($uploads as $upload) {
            $stored = bin2hex(random_bytes(16));

            if (!move_uploaded_file($upload['tmp_name'], $folder . '/' . $stored)) {
                throw new \RuntimeException(Text::sprintf('PLG_KUNENA_INBOX_ERROR_FILE_UPLOAD', basename($upload['name'])));
            }

            $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
            $mime  = $finfo ? (string) finfo_file($finfo, $folder . '/' . $stored) : 'application/octet-stream';

            $row = (object) [
                'inbox_id'     => $inboxId,
                'user_id'      => $userId,
                'filename'     => mb_substr(preg_replace('/[\x00-\x1F"\\\\\/]/', '_', basename($upload['name'])), 0, 255),
                'stored'       => $stored,
                'size'         => (int) filesize($folder . '/' . $stored),
                'mime'         => mb_substr($mime ?: 'application/octet-stream', 0, 100),
                'created_time' => time(),
            ];

            $db->insertObject('#__kunena_inbox_attachments', $row);
        }
    }

    /**
     * The files of messages, by message.
     *
     * @param   int[]  $inboxIds  The ids of the messages.
     *
     * @return  array  message id => list of files
     *
     * @since   Kunena 7.2
     */
    public static function getFiles(array $inboxIds): array
    {
        $inboxIds = array_filter(array_map('intval', $inboxIds));

        if (!$inboxIds) {
            return [];
        }

        $db    = self::db();
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'inbox_id', 'filename', 'size']))
            ->from($db->quoteName('#__kunena_inbox_attachments'))
            ->whereIn($db->quoteName('inbox_id'), $inboxIds)
            ->order($db->quoteName('id') . ' ASC');

        $result = [];

        foreach ($db->setQuery($query)->loadObjectList() as $file) {
            $result[(int) $file->inbox_id][] = [
                'id'   => (int) $file->id,
                'name' => $file->filename,
                'size' => self::formatSize((int) $file->size),
                'url'  => Uri::root(true) . '/index.php?option=com_ajax&plugin=inbox&group=kunena&format=json&task=download&id=' . (int) $file->id,
            ];
        }

        return $result;
    }

    /**
     * @param   int  $bytes  The size.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    protected static function formatSize(int $bytes): string
    {
        return $bytes >= 1048576 ? round($bytes / 1048576, 1) . ' MB' : max(1, (int) round($bytes / 1024)) . ' KB';
    }

    /**
     * Send a file to the user, when he is the sender or the recipient of the message (and has not deleted it).
     *
     * @param   int  $userid  The user.
     * @param   int  $id      The id of the file.
     *
     * @return  void
     *
     * @throws  \RuntimeException
     * @since   Kunena 7.2
     */
    public static function download(int $userid, int $id): void
    {
        $db    = self::db();
        $query = $db->createQuery()
            ->select('a.*, m.user_id AS recipient, m.sender_id AS sender, m.deleted_recipient AS dr, m.deleted_sender AS ds')
            ->from($db->quoteName('#__kunena_inbox_attachments', 'a'))
            ->innerJoin($db->quoteName('#__kunena_inbox', 'm') . ' ON ' . $db->quoteName('m.id') . ' = ' . $db->quoteName('a.inbox_id'))
            ->where($db->quoteName('a.id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $file = $db->setQuery($query)->loadObject();
        $path = $file ? self::folder() . '/' . basename($file->stored) : '';

        if (!$file || !is_file($path) || !(($file->recipient == $userid && !$file->dr) || ($file->sender == $userid && !$file->ds))) {
            throw new \RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR'), 404);
        }

        $app = Factory::getApplication();

        // Always as a download, so a file is never run by the browser.
        header('Content-Type: application/octet-stream');
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: attachment; filename="' . rawurlencode($file->filename) . '"; filename*=UTF-8\'\'' . rawurlencode($file->filename));
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, no-store');

        while (ob_get_level()) {
            ob_end_clean();
        }

        readfile($path);
        $app->close();
    }

    /**
     * Delete the files of messages which are removed.
     *
     * @param   int[]  $inboxIds  The ids of the messages.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function deleteFiles(array $inboxIds): void
    {
        $inboxIds = array_filter(array_map('intval', $inboxIds));

        if (!$inboxIds) {
            return;
        }

        $db    = self::db();
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'stored']))
            ->from($db->quoteName('#__kunena_inbox_attachments'))
            ->whereIn($db->quoteName('inbox_id'), $inboxIds);

        $files = $db->setQuery($query)->loadObjectList();

        foreach ($files as $file) {
            $path = self::folder() . '/' . basename($file->stored);

            if (is_file($path)) {
                unlink($path);
            }
        }

        if ($files) {
            $query = $db->createQuery()
                ->delete($db->quoteName('#__kunena_inbox_attachments'))
                ->whereIn($db->quoteName('inbox_id'), $inboxIds);
            $db->setQuery($query)->execute();
        }
    }
}
