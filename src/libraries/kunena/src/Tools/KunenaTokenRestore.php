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

use FilesystemIterator;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Install\KunenaObsoleteFiles;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * For the developers (tool "Restore the build tokens"): a site whose Kunena folders are links to a git clone gets the files
 * of an installed package written into the clone, with the tokens of the build replaced (@currentyear@ became the year,
 * @kunenaversion@ the version...). This tool finds those files in the folders reached through a link and puts the tokens
 * back. A folder which is no link holds an installed Kunena: its values are right and it is never scanned.
 *
 * @since   Kunena 8.0
 */
class KunenaTokenRestore
{
    /**
     * The extensions of the files which are scanned.
     *
     * @since   Kunena 8.0
     */
    public const EXTENSIONS = ['php', 'xml', 'js', 'mjs', 'css', 'scss', 'ini', 'sql'];

    /**
     * The folders which are never scanned.
     *
     * @since   Kunena 8.0
     */
    public const SKIP_FOLDERS = ['vendor', 'node_modules', '.git', 'attachments', 'avatars', 'cache'];

    /**
     * The patterns of a file of every type: pattern => replacement.
     *
     * @since   Kunena 8.0
     */
    private const PATTERNS = [
        '#(\(C\) \d{4} - )\d{4}( Kunena Team)#' => '$1@currentyear@$2',
    ];

    /**
     * The patterns of a manifest.
     *
     * @since   Kunena 8.0
     */
    private const XML_PATTERNS = [
        '#<version>[^<@]+</version>#'           => '<version>@kunenaversion@</version>',
        '#<creationDate>[^<@]+</creationDate>#' => '<creationDate>@kunenaversiondate@</creationDate>',
        '#<build>[^<@]+</build>#'               => '<build>@kunenaversionbuild@</build>',
        '#<changelogurl>[^<@]+</changelogurl>#' => '<changelogurl>@kunenachangelog@</changelogurl>',
        '#<versionname>[^<@]+</versionname>#'   => '<versionname>@kunenaversionname@</versionname>',
        '#(_v)\d[^<@]*?(\.zip</file>)#'         => '$1@kunenaversion@$2',
        // Not the attributes versionname="" and versiondate="": kunena.install.upgrade.xml has one per release of Kunena.
    ];

    /**
     * The patterns of the PHP files which compare a value with its token.
     *
     * @since   Kunena 8.0
     */
    private const PHP_PATTERNS = [
        "#\('[^'@]*' == '@' \. 'kunenaversionname' \. '@'\) \? 'Git Repository' : '[^'@]*'#"
                                                                    => "('@kunenaversionname@' == '@' . 'kunenaversionname' . '@') ? 'Git Repository' : '@kunenaversionname@'",
        "#(\\\$data->version == ')\d+\.\d+\.\d+[0-9A-Za-z.\-]*(')#" => '$1@kunenaversion@$2',
        "#(\\\$data->creationdate == ')\d{4}-\d{2}-\d{2}(')#"       => '$1@kunenaversiondate@$2',
    ];

    /**
     * The content of a file with its tokens put back.
     *
     * @param   string  $content    The content.
     * @param   string  $extension  The extension of the file.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function restore(string $content, string $extension): string
    {
        $patterns = self::PATTERNS;

        if ($extension === 'xml') {
            $patterns += self::XML_PATTERNS;
        } elseif ($extension === 'php') {
            $patterns += self::PHP_PATTERNS;
        }

        return (string) preg_replace(array_keys($patterns), array_values($patterns), $content);
    }

    /**
     * The files of the folders whose tokens were replaced: path => number of lines changed.
     *
     * @param   string[]  $folders  The folders (the real ones, behind the links).
     *
     * @return  array<string, int>
     *
     * @since   Kunena 8.0
     */
    public static function find(array $folders): array
    {
        $found = [];

        foreach (self::files($folders) as $path) {
            $content  = (string) file_get_contents($path);
            $restored = self::restore($content, strtolower(pathinfo($path, PATHINFO_EXTENSION)));

            if ($restored !== $content) {
                $found[$path] = self::changedLines($content, $restored);
            }
        }

        ksort($found, SORT_NATURAL | SORT_FLAG_CASE);

        return $found;
    }

    /**
     * Puts the tokens back in the files of the folders.
     *
     * @param   string[]  $folders  The folders.
     *
     * @return  array{restored: int, failed: int}
     *
     * @since   Kunena 8.0
     */
    public static function apply(array $folders): array
    {
        $result = ['restored' => 0, 'failed' => 0];

        foreach (array_keys(self::find($folders)) as $path) {
            $content  = (string) file_get_contents($path);
            $restored = self::restore($content, strtolower(pathinfo($path, PATHINFO_EXTENSION)));

            if (@file_put_contents($path, $restored) !== false) {
                $result['restored']++;
            } else {
                $result['failed']++;
            }
        }

        return $result;
    }

    /**
     * The folders of Kunena on this site which are links: the real folders behind them.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function linkedFolders(): array
    {
        $paths   = array_map(static fn ($root) => JPATH_ROOT . '/' . rtrim($root, '/'), KunenaObsoleteFiles::ROOTS);
        $paths[] = JPATH_ROOT . '/api/components/com_kunena';

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['folder', 'element']))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where('(' . $db->quoteName('element') . ' LIKE ' . $db->quote('%kunena%') . ' OR ' . $db->quoteName('folder') . ' = ' . $db->quote('kunena') . ')');

        foreach ((array) $db->setQuery($query)->loadAssocList() as $plugin) {
            $paths[] = JPATH_PLUGINS . '/' . $plugin['folder'] . '/' . $plugin['element'];
        }

        $folders = [];

        foreach ($paths as $path) {
            if (is_link($path) && ($real = realpath($path)) !== false && is_dir($real)) {
                $folders[str_replace('\\', '/', $real)] = true;
            }
        }

        return array_keys($folders);
    }

    /**
     * The files to scan in the folders.
     *
     * @param   string[]  $folders  The folders.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    private static function files(array $folders): array
    {
        $files = [];

        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                continue;
            }

            $filter = new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS),
                static fn (SplFileInfo $file) => $file->isDir()
                    ? !$file->isLink() && !\in_array($file->getFilename(), self::SKIP_FOLDERS, true)
                    : \in_array(strtolower($file->getExtension()), self::EXTENSIONS, true)
            );

            foreach (new RecursiveIteratorIterator($filter) as $file) {
                $files[str_replace('\\', '/', $file->getPathname())] = true;
            }
        }

        return array_keys($files);
    }

    /**
     * The number of lines which differ.
     *
     * @param   string  $before  The content before.
     * @param   string  $after   The content after.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    private static function changedLines(string $before, string $after): int
    {
        $before = explode("\n", $before);
        $after  = explode("\n", $after);

        return \count(array_diff_assoc($before, $after));
    }
}
