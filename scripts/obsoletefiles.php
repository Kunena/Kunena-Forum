<?php

/**
 * Kunena Component
 *
 * Writes the list of the files which Kunena no longer ships, from the history of git: src/admin/install/obsolete-files.php.
 * An update of Joomla does not delete the files which left the package: the tool "Prune old files" of the administration
 * finds them on a site with this list, and deletes them.
 *
 * A file is on the list when git deleted it (or renamed it: the old name stays on the sites) in the folders which are
 * installed (src/admin, src/site, src/media/kunena, src/libraries/kunena) and no file of the current tree has that path, also
 * not with other capitals (a server on Windows does not tell them apart). Never on the list:
 * - the language files (the language packs install and remove them);
 * - the SQL files of the updates (the database checker of Joomla reads them);
 * - the emoticons, the avatars and the topic icons (the posts and the profiles of a site can still use them);
 * - the files of a template which Kunena no longer ships (a site can still use that template).
 *
 * Usage: php scripts/obsoletefiles.php   (run it before a release, in the git clone; it writes the list and says its size)
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

$root = \dirname(__DIR__);

/**
 * The release from which the deletions are read (a tag of git).
 */
const KUNENA_SINCE = '6.0';

/**
 * Where a folder of the git clone is installed on a site (from the root of Joomla).
 */
const KUNENA_INSTALLED = [
    'src/admin/'            => 'administrator/components/com_kunena/',
    'src/site/'             => 'components/com_kunena/',
    'src/media/kunena/'     => 'media/kunena/',
    'src/libraries/kunena/' => 'libraries/kunena/',
];

/**
 * The installed path of a deleted file of the git clone, or null when it is not one which the tool may delete.
 *
 * @param   string    $path       The path in the git clone.
 * @param   string[]  $templates  The templates which Kunena ships (the folders of src/site/template).
 *
 * @return  string|null
 */
function kunenaObsoletePath(string $path, array $templates): ?string
{
    foreach (KUNENA_INSTALLED as $source => $installed) {
        if (!str_starts_with($path, $source)) {
            continue;
        }

        if (preg_match('#/language/|/sql/|^src/media/kunena/(emoticons|avatars|topic_?icons)/#i', $path)) {
            return null;
        }

        if (preg_match('#^src/site/template/([^/]+)/#', $path, $match) && !\in_array($match[1], $templates, true)) {
            return null;
        }

        return $installed . substr($path, \strlen($source));
    }

    return null;
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    $git = static function (string $arguments) use ($root): array {
        exec('git -C ' . escapeshellarg($root) . ' ' . $arguments, $lines, $code);

        if ($code !== 0) {
            fwrite(STDERR, "git failed: git {$arguments}\n");
            exit(1);
        }

        return $lines;
    };

    $current   = array_flip(array_map('strtolower', $git('ls-files')));
    $templates = array_map('basename', glob($root . '/src/site/template/*', GLOB_ONLYDIR));
    // Since Kunena 6.0, the first release with the folders of today: before it the files of the clone were elsewhere, and
    // their path does not say where a site has them.
    $deleted   = $git('log ' . KUNENA_SINCE . '..HEAD --no-renames --diff-filter=D --name-only --format= -- '
        . implode(' ', array_map('escapeshellarg', array_keys(KUNENA_INSTALLED))));
    $files     = [];

    foreach (array_unique($deleted) as $path) {
        if ($path === '' || isset($current[strtolower($path)])) {
            continue;
        }

        $installed = kunenaObsoletePath($path, $templates);

        if ($installed !== null) {
            $files[$installed] = true;
        }
    }

    $files = array_keys($files);
    sort($files, SORT_STRING);

    $php = "<?php\n\n/**\n * Kunena Component\n *\n * The files which Kunena no longer ships, from the root of Joomla. Written by scripts/obsoletefiles.php: do not change it\n"
        . " * by hand, run the script again.\n *\n * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.\n"
        . " * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL\n * @link            https://www.kunena.org\n **/\n\n"
        . "\\defined('_JEXEC') or die();\n\nreturn [\n";

    foreach ($files as $file) {
        $php .= '    ' . var_export($file, true) . ",\n";
    }

    file_put_contents($root . '/src/admin/install/obsolete-files.php', $php . "];\n");
    echo \count($files) . " files written to src/admin/install/obsolete-files.php\n";
}
