<?php

/**
 * Kunena Component
 *
 * Check the language files of the git clone (it changes nothing):
 * - every file can be read by Joomla (a wrong quote makes Joomla drop the whole file);
 * - every key is a key of Joomla (A-Z, 0-9 and _);
 * - every translation folder is a language tag (xx-XX): Joomla does not load a folder "es_GT" or "sv";
 * - every translated file has an English file, and every translated key an English key (warnings: the text is not used).
 *
 * Usage: php scripts/checklanguages.php [--verbose] [nl-NL,de-DE]
 *   --verbose  also list the keys which are not in the English file.
 *
 * Exit code: 1 when a file cannot be read, a key is not valid or a folder is not a language tag; 0 otherwise.
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

require __DIR__ . '/languages.inc.php';

[$options, $arguments] = commandLine($argv);
$verbose               = isset($options['verbose']);
$only                  = isset($arguments[0]) ? array_flip(requestedLanguages($arguments[0])) : null;

$errors   = 0;
$warnings = 0;
$checked  = 0;

$error = static function (string $message) use (&$errors): void {
    $errors++;
    echo "ERROR   {$message}\n";
};

$warning = static function (string $message) use (&$warnings): void {
    $warnings++;
    echo "WARNING {$message}\n";
};

echo "\nChecking the language files of " . kunenaRoot() . "\n\n";

foreach (languageFolders() as $folder) {
    $english = [];

    foreach (iniFiles($folder . '/en-GB') as $file) {
        $strings = readStrings("{$folder}/en-GB/{$file}", $message);
        $checked++;

        if ($strings === false) {
            $error(relativePath("{$folder}/en-GB/{$file}") . ": cannot be read ({$message})");
            continue;
        }

        $english[$file] = $strings;

        foreach (array_keys($strings) as $key) {
            if (!preg_match('/^[A-Z0-9_]+$/', (string) $key)) {
                $error(relativePath("{$folder}/en-GB/{$file}") . ": key {$key} is not A-Z, 0-9 and _");
            }
        }
    }

    foreach (translationFolders($folder) as $tag) {
        if ($only !== null && !isset($only[$tag])) {
            continue;
        }

        if (!isLanguageTag($tag)) {
            $error(relativePath("{$folder}/{$tag}") . ': the folder is not a language tag (xx-XX), Joomla does not load it');
        }

        foreach (iniFiles("{$folder}/{$tag}") as $file) {
            $path    = "{$folder}/{$tag}/{$file}";
            $strings = readStrings($path, $message);
            $checked++;

            if ($strings === false) {
                $error(relativePath($path) . ": cannot be read ({$message})");
                continue;
            }

            if (!isset($english[$file])) {
                $warning(relativePath($path) . ': there is no English file ' . $file);
                continue;
            }

            foreach (array_keys($strings) as $key) {
                if (!preg_match('/^[A-Z0-9_]+$/', (string) $key)) {
                    $error(relativePath($path) . ": key {$key} is not A-Z, 0-9 and _");
                }
            }

            $unknown = array_diff_key($strings, $english[$file]);

            if ($unknown) {
                $warning(relativePath($path) . ': ' . \count($unknown) . ' key(s) which the English file does not have'
                    . ($verbose ? ': ' . implode(', ', array_keys($unknown)) : ''));
            }
        }
    }
}

echo "\n{$checked} files checked: {$errors} error(s), {$warnings} warning(s).\n\n";

exit($errors > 0 ? 1 : 0);
