<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Upload
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Upload;

\defined('_JEXEC') or die();

use Joomla\CMS\Client\ClientHelper;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;

/**
 * Kunena Upload Backend Helper Class
 *
 * @since   Kunena 6.0
 */
class KunenaUploadHelper
{
    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected static mixed $_instances = [];

    /**
     * @since   Kunena 6.0
     */
    private function __construct()
    {
    }

    /**
     * @param   mixed   $file          file
     * @param   string  $uploadfolder  upload
     * @param   string  $format        format
     *
     * @return bool
     *
     * @since   Kunena 6.0
     */
    public static function upload($file, string $uploadfolder, string $format): bool
    {
        // Set FTP credentials, if given
        ClientHelper::setCredentialsFromRequest('ftp');

        // Make the filename safe
        $file['name'] = File::makeSafe($file['name']);

        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || !empty($file['error'])) {
            return false;
        }

        if (!is_dir($uploadfolder)) {
            return false;
        }

        if (isset($file['name'])) {
            $filepath = Path::clean($uploadfolder . '/' . strtolower($file['name']));

            if (is_file($filepath)) {
                if ($format == 'json') {
                    header('HTTP/1.0 409 Conflict');
                    jexit('Error. File already exists');
                } else {
                    $ext         = File::getExt($file['name']);
                    $name        = File::stripExt($file['name']);
                    $newFileName = '';

                    for ($i = 2; file_exists("{$uploadfolder}/{$newFileName}"); $i++) {
                        $newFileName = $name . "-$i." . $ext;
                    }

                    $filepath = $uploadfolder . '/' . $newFileName;
                }
            }

            if (!File::upload($file['tmp_name'], $filepath)) {
                if ($format == 'json') {
                    header('HTTP/1.0 400 Bad Request');
                    jexit('Error. Unable to upload file');
                } else {
                    return false;
                }
            } else {
                if ($format == 'json') {
                    jexit('Upload complete');
                } else {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * A name for a file in a folder which is not taken yet: the safe name in lower case, else with -2, -3... before its
     * extension.
     *
     * @param   string    $name    The name of the uploaded file.
     * @param   callable  $exists  Is a name taken: fn (string $name): bool.
     *
     * @return  string|null  Null when no safe name is left (an empty name, or a name of dots only).
     *
     * @since   Kunena 8.0
     */
    public static function uniqueName(string $name, callable $exists): ?string
    {
        $name = strtolower(File::makeSafe($name));

        if (trim($name, '.') === '') {
            return null;
        }

        $ext       = pathinfo($name, PATHINFO_EXTENSION);
        $base      = $ext === '' ? $name : substr($name, 0, -\strlen($ext) - 1);
        $candidate = $name;

        for ($i = 2; $exists($candidate); $i++) {
            if ($i > 999) {
                return null;
            }

            $candidate = $base . '-' . $i . ($ext === '' ? '' : '.' . $ext);
        }

        return $candidate;
    }

    /**
     * Stores an uploaded file in a folder under a name which is not taken (uniqueName()). The file is not checked here: the
     * caller checks its type first.
     *
     * @param   array   $file    The uploaded file ($_FILES).
     * @param   string  $folder  The folder.
     *
     * @return  string|null  The name it got, null when it was not stored.
     *
     * @since   Kunena 8.0
     */
    public static function store(array $file, string $folder): ?string
    {
        if (!empty($file['error']) || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || !is_dir($folder)) {
            return null;
        }

        $name = self::uniqueName((string) ($file['name'] ?? ''), static fn (string $name) => file_exists($folder . '/' . $name));

        if ($name === null || !File::upload($file['tmp_name'], Path::clean($folder . '/' . $name))) {
            return null;
        }

        return $name;
    }
}
