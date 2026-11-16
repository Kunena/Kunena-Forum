<?php

/**
 * Kunena Component
 *
 * The language files of the git clone, for the language scripts of this folder (checklanguages, convertlanguage, initlanguage,
 * findlanguagestrings). Every folder "language" of src (the backend, the frontend and every plugin) has an en-GB folder, which is
 * the source of the translations: <tag>/<file>.ini translates en-GB/<file>.ini.
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

/**
 * The root of the git clone.
 */
function kunenaRoot(): string
{
    return str_replace('\\', '/', \dirname(__DIR__));
}

/**
 * A path relative to the root of the git clone, for the messages.
 */
function relativePath(string $path): string
{
    $path = str_replace('\\', '/', $path);
    $root = kunenaRoot() . '/';

    return str_starts_with($path, $root) ? substr($path, \strlen($root)) : $path;
}

/**
 * The language folders of the package: every folder "language" under src which has an en-GB folder.
 *
 * @return  string[]  Absolute paths, sorted.
 */
function languageFolders(): array
{
    $folders = [];
    $source  = kunenaRoot() . '/src';

    $iterator = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS),
            static fn (SplFileInfo $file) => $file->isDir() && !\in_array($file->getFilename(), ['External', 'node_modules', 'vendor'], true)
        ),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $folder) {
        if ($folder->getFilename() === 'language' && is_dir($folder->getPathname() . '/en-GB')) {
            $folders[] = str_replace('\\', '/', $folder->getPathname());
        }
    }

    sort($folders);

    return $folders;
}

/**
 * Whether a folder name is a language tag which Joomla loads: xx-XX (or xxx-XX).
 */
function isLanguageTag(string $name): bool
{
    return (bool) preg_match('/^[a-z]{2,3}-[A-Z]{2}$/', $name);
}

/**
 * The translations of a language folder: its sub folders other than en-GB (and "all", the language package).
 *
 * @return  string[]  Folder names, sorted.
 */
function translationFolders(string $languageFolder): array
{
    $names = [];

    foreach (new DirectoryIterator($languageFolder) as $item) {
        if ($item->isDir() && !$item->isDot() && !\in_array($item->getFilename(), ['en-GB', 'all'], true)) {
            $names[] = $item->getFilename();
        }
    }

    sort($names);

    return $names;
}

/**
 * The ini files of a folder.
 *
 * @return  string[]  File names, sorted.
 */
function iniFiles(string $folder): array
{
    $files = array_map('basename', glob($folder . '/*.ini') ?: []);
    sort($files);

    return $files;
}

/**
 * The strings of a language file, as Joomla reads them (INI_SCANNER_RAW: \" and _QQ_ are kept as they are written).
 *
 * @param   string       $file   The file.
 * @param   string|null  $error  The error of PHP when the file cannot be read.
 *
 * @return  array|false  key => text, false when the file is not valid.
 */
function readStrings(string $file, ?string &$error = null): array|false
{
    $error = null;

    set_error_handler(static function (int $number, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $strings = parse_ini_file($file, false, INI_SCANNER_RAW);
    } finally {
        restore_error_handler();
    }

    return \is_array($strings) ? $strings : false;
}

/**
 * The language tags given on the command line (a comma separated list), or all translations of the package.
 *
 * @param   string|null  $argument  The argument.
 *
 * @return  string[]
 */
function requestedLanguages(?string $argument): array
{
    if ($argument !== null && $argument !== '') {
        return array_values(array_filter(array_map('trim', explode(',', $argument)), static fn ($tag) => $tag !== '' && $tag !== 'en-GB'));
    }

    $tags = [];

    foreach (languageFolders() as $folder) {
        foreach (translationFolders($folder) as $tag) {
            if (isLanguageTag($tag)) {
                $tags[$tag] = true;
            }
        }
    }

    ksort($tags);

    return array_keys($tags);
}

/**
 * The options of the command line: --name and --name=value, the rest are the arguments.
 *
 * @return  array{0: array<string, string|true>, 1: string[]}
 */
function commandLine(array $argv): array
{
    $options   = [];
    $arguments = [];

    foreach (\array_slice($argv, 1) as $argument) {
        if (preg_match('/^--([a-z-]+)(?:=(.*))?$/', $argument, $match)) {
            $options[$match[1]] = $match[2] ?? true;
        } else {
            $arguments[] = $argument;
        }
    }

    return [$options, $arguments];
}
