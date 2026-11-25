<?php

/**
 * Kunena Component
 *
 * Write the lists of the SHA-256 and the SHA-512 of the files which a manifest installs into a folder of a package (the
 * component, the library): the Security audit of the backend compares the installed files with them. The build runs it
 * after the replacement of the tokens, so the lists match the files of the package.
 *
 * Usage: php scripts/folder-checksums.php <folder> <list, relative to the folder> <manifest> <files element under extension>
 * Example: php scripts/folder-checksums.php build/com_kunena/site src/checksums.json build/com_kunena/kunena.xml files
 *          (the admin part: administration/files; no leading slash, which a shell of Windows takes for a path)
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

\define('_JEXEC', 1);

require __DIR__ . '/../src/libraries/kunena/src/Template/KunenaTemplateChecksums.php';

use Kunena\Forum\Libraries\Template\KunenaTemplateChecksums;

[, $folder, $list, $manifest, $element] = $argv + [null, '', '', '', ''];

if ($folder === '' || !is_dir($folder) || $list === '' || !is_file($manifest) || !preg_match('#^[a-z]+(/[a-z]+)*$#', $element)) {
    fwrite(STDERR, "Usage: php scripts/folder-checksums.php <folder> <list> <manifest> <files element under extension>\n");

    exit(1);
}

$xpath = '/extension/' . $element;
$xml   = simplexml_load_file($manifest);
$files = $xml ? $xml->xpath($xpath) : false;

if (!$files) {
    fwrite(STDERR, "No element $xpath in $manifest\n");

    exit(1);
}

// The folders and the files which the manifest installs into this folder.
$entries = [];

foreach ($files[0]->children() as $child) {
    if (\in_array($child->getName(), ['folder', 'filename', 'file'], true)) {
        $entries[] = trim((string) $child);
    }
}

// A list for each algorithm: SHA-256 and SHA-512.
foreach (KunenaTemplateChecksums::ALGORITHMS as $algorithm) {
    $count = \count(KunenaTemplateChecksums::writeList($folder, $list, $entries, $algorithm));

    echo basename($folder) . '/' . KunenaTemplateChecksums::listName($list, $algorithm) . ': ' . $count . " files\n";
}
