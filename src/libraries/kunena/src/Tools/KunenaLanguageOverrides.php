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

use Joomla\CMS\Language\LanguageHelper;

/**
 * The language overrides of Joomla for texts of Kunena (tool "Language overrides check"): an override of a text which
 * Kunena no longer has does nothing, and an override whose placeholders differ from the text of Kunena shows a wrong
 * value or causes an error of PHP. Both happen after an update which renamed or changed a text.
 *
 * @since   Kunena 8.0
 */
class KunenaLanguageOverrides
{
    /**
     * An override of a text which does not exist (any more).
     *
     * @since   Kunena 8.0
     */
    public const UNKNOWN = 'unknown';

    /**
     * The clients of Joomla with their own overrides.
     *
     * @since   Kunena 8.0
     */
    public const CLIENTS = ['site', 'administrator'];

    /**
     * Is a key one of Kunena?
     *
     * @param   string  $key  The key.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isKunena(string $key): bool
    {
        return str_contains(strtoupper($key), 'KUNENA');
    }

    /**
     * The problems of the overrides of Kunena of one file.
     *
     * @param   array<string, string>  $overrides  key => text of the override.
     * @param   array<string, string>  $english    key => text of English of Kunena.
     *
     * @return  array<int, array{key: string, kind: string, text: string, english: string}>  kind: UNKNOWN or a kind of
     *          KunenaTranslationCheck::difference().
     *
     * @since   Kunena 8.0
     */
    public static function check(array $overrides, array $english): array
    {
        $problems = [];

        foreach ($overrides as $key => $text) {
            $key = (string) $key;

            if (!self::isKunena($key)) {
                continue;
            }

            if (!isset($english[$key])) {
                $problems[] = ['key' => $key, 'kind' => self::UNKNOWN, 'text' => (string) $text, 'english' => ''];

                continue;
            }

            $kind = KunenaTranslationCheck::difference((string) $english[$key], (string) $text);

            if ($kind !== null) {
                $problems[] = ['key' => $key, 'kind' => $kind, 'text' => (string) $text, 'english' => (string) $english[$key]];
            }
        }

        return $problems;
    }

    /**
     * The folder of the overrides of a client.
     *
     * @param   string  $client  One of CLIENTS.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function folder(string $client): string
    {
        return ($client === 'administrator' ? JPATH_ADMINISTRATOR : JPATH_SITE) . '/language/overrides';
    }

    /**
     * The English texts of Kunena on this site: the component, its plugins and its modules, wherever their files are.
     *
     * @return  array<string, string>
     *
     * @since   Kunena 8.0
     */
    public static function englishOnSite(): array
    {
        $files = [];

        foreach (KunenaTranslationCheck::foldersOnSite() as $folder) {
            $files = array_merge($files, glob($folder . '/en-GB/*.ini') ?: []);
        }

        foreach ([JPATH_SITE . '/language/en-GB', JPATH_ADMINISTRATOR . '/language/en-GB', JPATH_PLUGINS . '/*/*/language/en-GB',
            JPATH_SITE . '/modules/*/language/en-GB', JPATH_ADMINISTRATOR . '/modules/*/language/en-GB'] as $pattern) {
            foreach (glob($pattern . '/*.ini') ?: [] as $file) {
                if (stripos(basename($file), 'kunena') !== false) {
                    $files[] = $file;
                }
            }
        }

        $english = [];

        foreach (array_unique($files) as $file) {
            $english += KunenaTranslationCheck::read($file);
        }

        return $english;
    }

    /**
     * The problems of the overrides of this site, per file.
     *
     * @return  array<int, array{client: string, tag: string, problems: array}>
     *
     * @since   Kunena 8.0
     */
    public static function checkOnSite(): array
    {
        $english = self::englishOnSite();
        $result  = [];

        foreach (self::CLIENTS as $client) {
            foreach (glob(self::folder($client) . '/*.override.ini') ?: [] as $file) {
                $problems = self::check(KunenaTranslationCheck::read($file), $english);

                if ($problems) {
                    $result[] = ['client' => $client, 'tag' => basename($file, '.override.ini'), 'problems' => $problems];
                }
            }
        }

        return $result;
    }

    /**
     * The overrides of a form: "client|tag|KEY".
     *
     * @param   string[]  $values  The values.
     *
     * @return  array<string, string[]>  The file => the keys.
     *
     * @since   Kunena 8.0
     */
    public static function parse(array $values): array
    {
        $files = [];

        foreach ($values as $value) {
            $parts = explode('|', (string) $value);

            if (\count($parts) !== 3 || !\in_array($parts[0], self::CLIENTS, true) || !preg_match('/^[a-z]{2,3}-[A-Z]{2}$/', $parts[1])
                || !preg_match('/^[A-Z0-9_.\-]+$/', $parts[2]) || !self::isKunena($parts[2])) {
                continue;
            }

            $files[self::folder($parts[0]) . '/' . $parts[1] . '.override.ini'][] = $parts[2];
        }

        return $files;
    }

    /**
     * Removes overrides from their files (only overrides of Kunena).
     *
     * @param   array<string, string[]>  $files  The file => the keys (parse()).
     *
     * @return  int  The overrides removed.
     *
     * @since   Kunena 8.0
     */
    public static function removeOnSite(array $files): int
    {
        $removed = 0;

        foreach ($files as $file => $keys) {
            if (!is_file($file)) {
                continue;
            }

            $strings = KunenaTranslationCheck::read($file);
            $before  = \count($strings);
            $strings = array_diff_key($strings, array_flip($keys));

            if (\count($strings) !== $before && LanguageHelper::saveToIniFile($file, $strings)) {
                $removed += $before - \count($strings);
            }
        }

        return $removed;
    }
}
