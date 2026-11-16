<?php

/**
 * Kunena Component
 *
 * Rewrite the files of a translation in the order and the layout of their English file: the comments and the empty lines of the
 * English file, then each key with its translation. A key which is not translated is written as a comment (; KEY=""), a key which
 * the English file no longer has is left out (it is counted), a translation which was commented out is used again. Only files which
 * change are written; the texts are written as they were (\" and _QQ_ are kept).
 *
 * Usage: php scripts/convertlanguage.php [--dry-run] [nl-NL,de-DE]
 *   Without languages: every translation of the package. Files which a translation does not have are not created (initlanguage.php
 *   does that).
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

require __DIR__ . '/languages.inc.php';

[$options, $arguments] = commandLine($argv);
$dryRun                = isset($options['dry-run']);
$languages             = requestedLanguages($arguments[0] ?? null);

/**
 * The translations of a file: its keys, and the keys which were commented out (; KEY="...") when the file has no other text for them.
 *
 * @return  array|false  key => text, false when the file cannot be read.
 */
function loadTranslations(string $file): array|false
{
    $strings = readStrings($file);

    if ($strings === false) {
        return false;
    }

    $commented = (string) preg_replace('/^(?!;\s*[A-Z0-9_]+\s*=).*$/m', '', (string) file_get_contents($file));
    $commented = (string) preg_replace('/^;\s*/m', '', $commented);
    $restored  = @parse_ini_string($commented, false, INI_SCANNER_RAW);

    if (\is_array($restored)) {
        // An empty commented key (; KEY="") is a key which was not translated.
        $strings += array_filter($restored, static fn ($text) => $text !== '');
    }

    return $strings;
}

/**
 * The file in the layout of the English file.
 *
 * @param   string  $englishFile   The English file.
 * @param   array   $translations  key => text of the translation.
 * @param   array   $stats         translated, missing.
 *
 * @return  string
 */
function convert(string $englishFile, array $translations, array &$stats): string
{
    $lines = [];

    foreach (preg_split('/\r\n|\n|\r/', (string) file_get_contents($englishFile)) as $line) {
        if (!preg_match('/^\s*([A-Z0-9_]+)\s*=/', $line, $match)) {
            // A comment, an empty line or a section of the English file.
            $lines[] = rtrim($line);
            continue;
        }

        $key = $match[1];

        if (isset($translations[$key]) && $translations[$key] !== '') {
            $lines[] = $key . '="' . $translations[$key] . '"';
            $stats['translated']++;
        } else {
            $lines[] = '; ' . $key . '=""';
            $stats['missing']++;
        }
    }

    return rtrim(implode("\n", $lines)) . "\n";
}

echo "\nConverting language files" . ($dryRun ? ' (dry run, nothing is written)' : '') . "...\n\n";

$written = 0;
$failed  = 0;

foreach ($languages as $language) {
    $totals = ['files' => 0, 'translated' => 0, 'missing' => 0, 'dropped' => 0];

    foreach (languageFolders() as $folder) {
        foreach (iniFiles($folder . '/en-GB') as $file) {
            $outfile = "{$folder}/{$language}/{$file}";

            if (!is_file($outfile)) {
                continue;
            }

            $translations = loadTranslations($outfile);
            $english      = readStrings("{$folder}/en-GB/{$file}");

            if ($translations === false || $english === false) {
                echo 'ERROR ' . relativePath($translations === false ? $outfile : "{$folder}/en-GB/{$file}") . " cannot be read, skipped\n";
                $failed++;
                continue;
            }

            $stats    = ['translated' => 0, 'missing' => 0];
            $contents = convert("{$folder}/en-GB/{$file}", $translations, $stats);
            $dropped  = \count(array_diff_key($translations, $english));

            $totals['translated'] += $stats['translated'];
            $totals['missing'] += $stats['missing'];
            $totals['dropped'] += $dropped;

            if ($contents === file_get_contents($outfile)) {
                continue;
            }

            $totals['files']++;
            $written++;

            echo ($dryRun ? 'Would save ' : 'Save ') . relativePath($outfile)
                . " ({$stats['translated']} translated, {$stats['missing']} not translated, {$dropped} left out)\n";

            if (!$dryRun) {
                file_put_contents($outfile, $contents);
            }
        }
    }

    echo "{$language}: {$totals['files']} file(s) changed, {$totals['translated']} translated, {$totals['missing']} not translated, "
        . "{$totals['dropped']} key(s) left out which English no longer has\n\n";
}

echo "{$written} file(s) " . ($dryRun ? 'would be written' : 'written') . ".\n\n";

exit($failed > 0 ? 1 : 0);
