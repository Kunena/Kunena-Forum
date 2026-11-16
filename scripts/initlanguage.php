<?php

/**
 * Kunena Component
 *
 * Create the language files which a translation does not have yet: for every English file (of the backend, the frontend and every
 * plugin) a file with only the header, which the translators fill on Crowdin. Existing files are never changed.
 *
 * Usage: php scripts/initlanguage.php [--dry-run] [nl-NL,de-DE]
 *   Without languages: every translation of the package (the language tags which have a folder somewhere).
 *   A new language tag (xx-XX) gets its folders.
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

require __DIR__ . '/languages.inc.php';

[$options, $arguments] = commandLine($argv);
$dryRun                = isset($options['dry-run']);
$languages             = requestedLanguages($arguments[0] ?? null);

$header = <<<'INI'
; Kunena Forum translation
;
; This file has not been translated yet. All translations are made on https://crowdin.com/project/kunena-forum
; Please join the translation team if you want to contribute your changes to the next release.
;
; License GNU General Public License version 3 or later; see LICENSE.txt
; Note : All ini files need to be saved as UTF-8 - No BOM

INI;

foreach ($languages as $language) {
    if (!isLanguageTag($language)) {
        fwrite(STDERR, "Error: '{$language}' is not a language tag (xx-XX).\n");

        exit(1);
    }
}

echo "\nInitializing language files" . ($dryRun ? ' (dry run, nothing is written)' : '') . "...\n\n";

$created = 0;

foreach ($languages as $language) {
    foreach (languageFolders() as $folder) {
        foreach (iniFiles($folder . '/en-GB') as $file) {
            $outfile = "{$folder}/{$language}/{$file}";

            if (is_file($outfile)) {
                continue;
            }

            echo 'Create ' . relativePath($outfile) . "\n";
            $created++;

            if ($dryRun) {
                continue;
            }

            if (!is_dir(\dirname($outfile)) && !mkdir(\dirname($outfile), 0755, true)) {
                fwrite(STDERR, 'Error: cannot create ' . relativePath(\dirname($outfile)) . "\n");

                exit(1);
            }

            file_put_contents($outfile, $header);
        }
    }
}

echo "\n{$created} file(s) " . ($dryRun ? 'would be created' : 'created') . ".\n\n";
