<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Template
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Template;

\defined('_JEXEC') or die();

use Exception;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use stdClass;

/**
 * Kunena Template Helper Class
 *
 * @since  K2.0
 */
abstract class KunenaTemplateHelper
{
    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected static $_instances = [];

    /**
     * isDefault
     *
     * @param   string  $template  template
     *
     * @return  integer
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public static function isDefault(string $template): int
    {
        $config         = KunenaFactory::getConfig();
        $defaultemplate = $config->template;

        return $defaultemplate == $template ? 1 : 0;
    }

    /**
     * Why a template may not be uninstalled, or null when it may.
     *
     * The name is compared as the file system does (not case-sensitive on Windows and macOS: "Aurelia" is the folder "aurelia"),
     * and a name which is no plain folder name ("../template/aurelia" is the system template, reached through a path) is refused
     * before anything is compared.
     *
     * @param   mixed        $name     The name from the request.
     * @param   array        $locked   Names of the templates of the system.
     * @param   string|null  $default  Name of the template which is set as the default.
     *
     * @return  string|null  'invalid', 'locked', 'default' or null.
     *
     * @since   Kunena 7.1
     */
    public static function uninstallBlock($name, array $locked, ?string $default): ?string
    {
        if (!self::isValidName($name)) {
            return 'invalid';
        }

        foreach ($locked as $system) {
            if (strcasecmp($name, (string) $system) === 0) {
                return 'locked';
            }
        }

        if ($default !== null && strcasecmp($name, $default) === 0) {
            return 'default';
        }

        return null;
    }

    /**
     * May this user install, remove, restore or edit the files of a template?
     *
     * A template is PHP which the site runs, so this is the right to install code: the permission to install extensions
     * (core.manage of com_installer), or to administer Kunena (core.admin of com_kunena). The permission to manage Kunena is not
     * enough: it is given to people who run the forum.
     *
     * @param   object|null  $user  The user, with authorise($action, $asset).
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function mayChangeTemplates($user): bool
    {
        if (!\is_object($user) || !method_exists($user, 'authorise')) {
            return false;
        }

        return (bool) $user->authorise('core.manage', 'com_installer') || (bool) $user->authorise('core.admin', 'com_kunena');
    }

    /**
     * Is this the name of a template of the system. The folder is not case sensitive on Windows and macOS, so neither is the
     * comparison.
     *
     * @param   mixed  $name    The name of the folder.
     * @param   array  $locked  The names of the templates of the system.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function isLocked($name, array $locked): bool
    {
        if (!\is_string($name)) {
            return false;
        }

        foreach ($locked as $system) {
            if (strcasecmp($name, (string) $system) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * The name under which an uploaded package is kept while it is extracted: made here, never taken from the client (the name
     * of the file is chosen by the one who uploads it), and only for a kind of archive which can be extracted.
     *
     * @param   mixed  $clientName  The name the browser sent.
     *
     * @return  string|null  A name without a path, or null when the file is not an archive of a template.
     *
     * @since   Kunena 7.1
     */
    public static function packageName($clientName): ?string
    {
        if (!\is_string($clientName)) {
            return null;
        }

        $lower = strtolower(trim($clientName));

        if (preg_match('/\.(tar\.(?:gz|bz2))\z/', $lower, $m)) {
            $ext = $m[1];
        } elseif (preg_match('/\.(zip|tar|tgz|gz|bz2)\z/', $lower, $m)) {
            $ext = $m[1];
        } else {
            return null;
        }

        return 'kunena-template-' . bin2hex(random_bytes(8)) . '.' . $ext;
    }

    /**
     * Is this a possible name of a template folder: letters, digits, underscore and hyphen, so it can not leave the
     * template folder (no slash, no dots).
     *
     * @param   mixed  $name  The name from the request.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function isValidName($name): bool
    {
        return \is_string($name) && preg_match('/^[A-Za-z0-9_-]+\z/', $name) === 1;
    }

    /**
     * Is this the name of an installed Kunena template.
     *
     * @param   mixed  $name  The name from the request.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function isInstalled($name): bool
    {
        return self::isValidName($name) && self::parseXmlFile($name) !== false;
    }

    /**
     * The path of a file which may be edited in the template editor, or null when the file is not allowed: the name has
     * to be a plain file name with the wanted extension (no folders, no hidden files, no script extension in the
     * middle of it), and the file has to be inside the given folder (also after links are followed).
     *
     * @param   string  $baseDir    The folder with the editable files.
     * @param   mixed   $filename   The name of the file from the request.
     * @param   string  $extension  The only extension which is allowed, without the dot.
     *
     * @return  string|null
     *
     * @since   Kunena 7.1
     */
    public static function getEditablePath(string $baseDir, $filename, string $extension): ?string
    {
        $filename = \is_string($filename) ? $filename : '';

        if ($filename === '' || $filename[0] === '.' || $filename !== basename(str_replace('\\', '/', $filename))) {
            return null;
        }

        $parts = explode('.', strtolower($filename));

        if (\count($parts) < 2 || array_pop($parts) !== strtolower($extension)) {
            return null;
        }

        // Never a script in disguise, like shell.php.css
        foreach ($parts as $part) {
            if (preg_match('/^(php\d?|phtml|pht|phps|phar|inc|cgi|pl|py|asp|aspx|jsp|exe|sh)$/', $part)) {
                return null;
            }
        }

        $base = realpath($baseDir);

        if ($base === false) {
            return null;
        }

        $path = $base . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($path) || is_link($path)) {
            $real = realpath($path);

            if ($real === false || dirname($real) !== $base) {
                return null;
            }
        }

        return $path;
    }

    /**
     * parseXmlFiles
     *
     * @param   null  $templateBaseDir  template
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    public static function parseXmlFiles($templateBaseDir = null): array
    {
        // Read the template folder to find templates
        if (!$templateBaseDir) {
            $templateBaseDir = KPATH_SITE . '/template';
        }

        $data = self::parseXmlFile('', $templateBaseDir);

        if ($data) {
            // Guess template folder.
            $data->directory = preg_replace('/[^a-z0-9_]/', '', preg_replace('/\s+/', '_', strtolower($data->name)));

            if (!$data->directory) {
                return [];
            }

            // Template found from the root (folder cannot contain more than one template)
            return ['' => $data];
        }

        $templateDirs = Folder::folders($templateBaseDir);
        $rows         = [];

        // Check that the directory contains an xml file
        foreach ($templateDirs as $templateDir) {
            $data = self::parseXmlFile($templateDir, $templateBaseDir);

            if ($data) {
                $rows[$templateDir] = $data;
            }
        }

        ksort($rows);

        return $rows;
    }

    /**
     * @param   string  $templateDir      template dir
     * @param   string  $templateBaseDir  template basedir
     *
     * @return  boolean|stdClass
     *
     * @since   Kunena 6.0
     */
    public static function parseXmlFile(string $templateDir, $templateBaseDir = null)
    {
        // Check if the xml file exists
        if (!$templateBaseDir) {
            $templateBaseDir = KPATH_SITE . '/template';
        }

        if (!is_file($templateBaseDir . '/' . $templateDir . '/config/template.xml')) {
            return false;
        }

        $data = self::parseKunenaInstallFile($templateBaseDir . '/' . $templateDir . '/config/template.xml');

        if (!$data || $data->type != 'kunena-template') {
            return false;
        }

        $data->sourcedir = basename($templateDir);
        $data->directory = basename($templateDir);

        return $data;
    }

    /**
     * parseKunenaInstallFile
     *
     * @param   string  $path  path
     *
     * @return  boolean|stdClass
     *
     * @since   Kunena 6.0
     */
    public static function parseKunenaInstallFile(string $path)
    {
        $xml = simplexml_load_file($path);

        if (!$xml || $xml->getName() != 'kinstall') {
            return false;
        }

        $data               = new stdClass();
        $data->name         = (string) $xml->name;

        if ($xml->targetversion->attributes() !== null) {
            $data->targetversion = (string) $xml->targetversion->attributes()->version;
        }

        $data->type         = (string) $xml->attributes()->type;
        $data->creationdate = (string) $xml->creationDate;
        $data->author       = (string) $xml->author;
        $data->copyright    = (string) $xml->copyright;
        $data->authorEmail  = (string) $xml->authorEmail;
        $data->authorUrl    = (string) $xml->authorUrl;
        $data->version      = (string) $xml->version;
        $data->description  = (string) $xml->description;
        $data->thumbnail    = (string) $xml->thumbnail;
        $data->kversion     = (string) $xml->attributes()->version;

        if ($data->version == '@kunenaversion@') {
            $data->version = KunenaForum::version();
        }

        if ($data->creationdate == '@kunenaversiondate@') {
            $data->creationdate = KunenaForum::versionDate();
        }

        if (!$data->version) {
            $data->version = Text::_('Unknown');
        }

        if (!$data->creationdate) {
            $data->creationdate = Text::_('Unknown');
        }

        if (!$data->author) {
            $data->author = Text::_('Unknown');
        }

        return $data;
    }

    /**
     * Check with the field targetversion in xml file of template zip if it's compatible with kunena
     *
     * @param   string $targetversion The versions of Kunena compatible with the template
     *
     * @return boolean
     * @since Kunena 5.2
     */
    public static function templateIsKunenaCompatible($targetversion = null): bool
    {
        if ($targetversion === null) {
            return false;
        }

        // Validate that the template version is allowed
        if (preg_match('/^' . $targetversion . '/', KunenaForum::version())) {
            return true;
        }

        return false;
    }
}
