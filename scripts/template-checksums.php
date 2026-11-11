<?php

/**
 * Kunena Component
 *
 * Write checksums.json (the SHA-256 of every file) into each template of a folder of templates. The build runs it on the
 * templates of the package, after the replacement of the tokens; the backend compares the templates of a site with it.
 *
 * Usage: php scripts/template-checksums.php <folder of templates>
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

\define('_JEXEC', 1);

require __DIR__ . '/../src/libraries/kunena/src/Template/KunenaTemplateChecksums.php';

use Kunena\Forum\Libraries\Template\KunenaTemplateChecksums;

$templates = $argv[1] ?? '';

if ($templates === '' || !is_dir($templates)) {
    fwrite(STDERR, "Usage: php scripts/template-checksums.php <folder of templates>\n");

    exit(1);
}

foreach (new DirectoryIterator($templates) as $template) {
    if ($template->isDot() || !$template->isDir()) {
        continue;
    }

    $count = \count(KunenaTemplateChecksums::write($template->getPathname()));

    echo $template->getFilename() . '/' . KunenaTemplateChecksums::FILENAME . ': ' . $count . " files\n";
}
