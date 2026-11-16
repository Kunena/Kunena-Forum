<?php

/**
 * Kunena Component
 *
 * Compare the keys of the English language files with the keys of the code (PHP, XML and JavaScript of src); it changes nothing.
 * - Missing: a key of Kunena which the code uses and no English file has (it is shown as the key itself).
 * - Unused: an English key which no file of the code names. A key which the code puts together ('COM_KUNENA_BUTTON_' . $name) is
 *   counted as used when the code has its beginning (a prefix ending with "_"), so the list is short; check a key before removing
 *   it, a name can be put together in other ways.
 *
 * Usage: php scripts/findlanguagestrings.php [--missing] [--unused]   (both lists without an option)
 *
 * Exit code: 1 when a key is missing, 0 otherwise.
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

require __DIR__ . '/languages.inc.php';

[$options]   = commandLine($argv);
$showMissing = isset($options['missing']) || !isset($options['unused']);
$showUnused  = isset($options['unused']) || !isset($options['missing']);

// The English keys, and the file of each.
$english = [];

foreach (languageFolders() as $folder) {
    foreach (iniFiles($folder . '/en-GB') as $file) {
        foreach (array_keys(readStrings("{$folder}/en-GB/{$file}") ?: []) as $key) {
            $english[(string) $key] = relativePath("{$folder}/en-GB/{$file}");
        }
    }
}

// The keys which the code names. A key of Kunena: COM_KUNENA_..., PLG_..._KUNENA..., MOD_KUNENA..., PKG_KUNENA..., KUNENA_....
$kunenaKey = '/^(COM_KUNENA|PLG_[A-Z0-9]+_KUNENA|PLG_KUNENA|MOD_KUNENA|PKG_KUNENA|KUNENA_|TPL_KUNENA)/';
$used      = [];
$prefixes  = [];
$where     = [];
$constants = [];

$files = new RecursiveIteratorIterator(
    new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator(kunenaRoot() . '/src', FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS),
        static fn (SplFileInfo $file) => !$file->isDir() || !\in_array($file->getFilename(), ['External', 'node_modules', 'vendor', 'language'], true)
    )
);

foreach ($files as $file) {
    if (!\in_array($file->getExtension(), ['php', 'xml', 'js'], true) || str_ends_with($file->getFilename(), '.min.js')) {
        continue;
    }

    $path = str_replace('\\', '/', $file->getPathname());

    foreach (file($path) ?: [] as $number => $line) {
        if (!preg_match_all('/\b[A-Z][A-Z0-9]*_[A-Z0-9_]+/', $line, $matches)) {
            continue;
        }

        // Any mention counts as a use (a key in a comment is kept rather than reported as unused).
        foreach ($matches[0] as $token) {
            if (str_ends_with($token, '_')) {
                $prefixes[$token] = true;
            } else {
                $used[$token] = true;
            }
        }

        // A key which the code asks for: a whole string ('KEY', "KEY") or the text of an XML element (>KEY<), outside a comment.
        // The name of a constant (define('KUNENA_LOADED', ...), defined('KUNENA_LOADED')) is not a key.
        $code = (string) preg_replace('#(//|<!--|^\s*\*|^\s*/\*|^\s*\#).*$#', '', $line);

        if (preg_match_all('/\bdefined?\(\s*[\'"]([A-Z0-9_]+)[\'"]/i', $code, $defines)) {
            foreach ($defines[1] as $constant) {
                $constants[$constant] = true;
            }
        }

        if (preg_match_all('/([\'">])([A-Z][A-Z0-9]*_[A-Z0-9_]*[A-Z0-9])(?:\'|"|<)/', $code, $strings)) {
            foreach ($strings[2] as $token) {
                if (!isset($where[$token]) && preg_match($kunenaKey, $token)) {
                    $where[$token] = relativePath($path) . ':' . ($number + 1);
                }
            }
        }
    }
}

// Missing: a key of Kunena in the code, without an English text. A key which is the beginning of English keys is a prefix which
// Joomla completes: Text::plural() (KEY_1, KEY_MORE), the textPrefix of a controller, the langConstPrefix of a task (KEY_TITLE).
$missing = [];
$sorted  = array_keys($english);
sort($sorted);

foreach ($where as $key => $location) {
    if (isset($english[$key]) || isset($constants[$key])) {
        continue;
    }

    // The first English key after "KEY_" in the sorted list starts with it when there is one.
    $position = 0;

    foreach ($sorted as $position => $candidate) {
        if (strcmp($candidate, $key . '_') >= 0) {
            break;
        }
    }

    if (!str_starts_with($sorted[$position] ?? '', $key . '_')) {
        $missing[$key] = $location;
    }
}

ksort($missing);

// Unused: an English key which the code does not name, and which does not start with a prefix of the code.
$unused = [];

foreach ($english as $key => $file) {
    if (isset($used[$key])) {
        continue;
    }

    foreach ($prefixes as $prefix => $dummy) {
        if (str_starts_with($key, $prefix) && \strlen($prefix) > 11) {
            continue 2;
        }
    }

    // The suffixes of Text::plural() and of the numbered texts.
    if (preg_match('/^(.+)_(1|MORE|\d+)$/', $key, $match) && isset($used[$match[1]])) {
        continue;
    }

    $unused[$file][] = $key;
}

ksort($unused);

echo "\n" . \count($english) . ' English keys, ' . \count($where) . " keys of Kunena in the code.\n";

if ($showMissing) {
    echo "\nMissing (used by the code, no English text): " . \count($missing) . "\n";

    foreach ($missing as $key => $location) {
        echo "  {$key}  ({$location})\n";
    }
}

if ($showUnused) {
    echo "\nUnused (no file of the code names them): " . array_sum(array_map('count', $unused)) . "\n";

    foreach ($unused as $file => $keys) {
        echo "  {$file}\n";

        foreach ($keys as $key) {
            echo "    {$key}\n";
        }
    }
}

echo "\n";

exit($missing ? 1 : 0);
