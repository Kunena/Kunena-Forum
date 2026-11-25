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
 * The languages of Kunena on this site against English (tool "Translation check"): the texts which are missing (they show
 * in English), and the texts whose placeholders differ from English: a %s which is missing or too much shows a wrong text
 * or an error, a %s glued to a word ("door%s") shows the name in the word.
 *
 * @since   Kunena 8.0
 */
class KunenaTranslationCheck
{
    /**
     * A translation with a placeholder which English does not have: sprintf() gets too few values, PHP stops with an error.
     *
     * @since   Kunena 8.0
     */
    public const EXTRA = 'extra';

    /**
     * A translation without a placeholder of English: the value (a name, a number) is not shown.
     *
     * @since   Kunena 8.0
     */
    public const DROPPED = 'dropped';

    /**
     * A placeholder glued to a word where English has a space before it.
     *
     * @since   Kunena 8.0
     */
    public const GLUED = 'glued';

    /**
     * At most this many problems of a language are listed.
     *
     * @since   Kunena 8.0
     */
    public const LIST_LIMIT = 300;

    /**
     * The languages which write without spaces between the words: a placeholder after a letter is normal there.
     *
     * @since   Kunena 8.0
     */
    public const NO_SPACES = ['zh', 'ja', 'th', 'lo', 'km', 'my'];

    /**
     * The placeholders of a text, in their order: %s, %d, %1$s, %05.2f... (%% is a percent sign, not one). The flag "space"
     * of sprintf is not read: "%time% geleden" has no placeholder "% g", and "% s" is a broken one.
     *
     * @param   string  $text  The text.
     *
     * @return  array<int, array{0: string, 1: int}>  [placeholder, position].
     *
     * @since   Kunena 8.0
     */
    public static function placeholders(string $text): array
    {
        preg_match_all('/%(?:%|(?:\d+\$)?[-+0#]*\d*(?:\.\d+)?[bcdeEfFgGosuxX])/', $text, $matches, PREG_OFFSET_CAPTURE);

        return array_values(array_filter($matches[0], static fn ($match) => $match[0] !== '%%'));
    }

    /**
     * The placeholders as a list to compare: the type of each, sorted (a translation may put them in another order with
     * %1$s, %2$s).
     *
     * @param   string  $text  The text.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function signature(string $text): array
    {
        $types = array_map(static fn ($match) => substr($match[0], -1) === 'd' || substr($match[0], -1) === 'u' ? 'd' : substr($match[0], -1), self::placeholders($text));
        sort($types);

        return $types;
    }

    /**
     * The problems of a language against English.
     *
     * @param   array<string, string>  $english      key => text of English.
     * @param   array<string, string>  $translation  key => text of the language.
     * @param   string                 $tag          The language tag ("nl-NL").
     *
     * @return  array{missing: string[], problems: array<int, array{key: string, kind: string, english: string, text: string}>}
     *
     * @since   Kunena 8.0
     */
    public static function compare(array $english, array $translation, string $tag): array
    {
        $missing  = [];
        $problems = [];
        $spaces   = !\in_array(strtolower(substr($tag, 0, 2)), self::NO_SPACES, true);

        foreach ($english as $key => $text) {
            if (!isset($translation[$key])) {
                $missing[] = (string) $key;

                continue;
            }

            $other = (string) $translation[$key];
            $kind  = self::difference((string) $text, $other);

            if ($kind === null && $spaces && self::glued((string) $text, $other)) {
                $kind = self::GLUED;
            }

            if ($kind !== null) {
                $problems[] = ['key' => (string) $key, 'kind' => $kind, 'english' => (string) $text, 'text' => $other];
            }
        }

        // The worst first: an error of PHP, then a value which is not shown, then a placeholder glued to a word.
        $order = [self::EXTRA => 0, self::DROPPED => 1, self::GLUED => 2];
        usort($problems, static fn ($a, $b) => $order[$a['kind']] <=> $order[$b['kind']]);

        return ['missing' => $missing, 'problems' => $problems];
    }

    /**
     * How the placeholders of a translation differ from English.
     *
     * @param   string  $english  The text of English.
     * @param   string  $other    The translation.
     *
     * @return  string|null  EXTRA (a placeholder more of a type), DROPPED (one less), null when they are the same.
     *
     * @since   Kunena 8.0
     */
    public static function difference(string $english, string $other): ?string
    {
        $mine   = array_count_values(self::signature($english));
        $theirs = array_count_values(self::signature($other));

        foreach ($theirs as $type => $count) {
            if ($count > ($mine[$type] ?? 0)) {
                return self::EXTRA;
            }
        }

        return $mine === $theirs ? null : self::DROPPED;
    }

    /**
     * Is a placeholder of the translation glued to the character before it, where the same placeholder of English has a
     * space before it? "door%s" for "by %s", "Düzenleyen:%s" for "by %s".
     *
     * @param   string  $english  The text of English.
     * @param   string  $other    The translation.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function glued(string $english, string $other): bool
    {
        return self::gluedCount($other) > self::gluedCount($english);
    }

    /**
     * The placeholders of a text which follow a letter, a digit or a colon right away: the word and the value run together.
     *
     * @param   string  $text  The text.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    private static function gluedCount(string $text): int
    {
        $count = 0;

        foreach (self::placeholders($text) as [, $position]) {
            if ($position > 0 && preg_match('/[\p{L}\p{N}:]$/u', substr($text, 0, $position))) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * The texts of an ini file of a language of Joomla.
     *
     * @param   string  $file  The file.
     *
     * @return  array<string, string>
     *
     * @since   Kunena 8.0
     */
    public static function read(string $file): array
    {
        if (!is_file($file)) {
            return [];
        }

        return (array) LanguageHelper::parseIniFile($file);
    }

    /**
     * The folders of the language files of Kunena on this site.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function foldersOnSite(): array
    {
        return array_values(array_filter([JPATH_ADMINISTRATOR . '/components/com_kunena/language', JPATH_SITE . '/components/com_kunena/language'], 'is_dir'));
    }

    /**
     * The languages of Kunena on this site with their numbers: tag => [name, texts, missing, problems].
     *
     * @return  array<string, array{name: string, texts: int, missing: int, problems: int, errors: int, installed: bool}>  The installed
     *          languages first.
     *
     * @since   Kunena 8.0
     */
    public static function summaryOnSite(): array
    {
        $known   = LanguageHelper::getKnownLanguages(JPATH_SITE) + LanguageHelper::getKnownLanguages(JPATH_ADMINISTRATOR);
        $summary = [];

        foreach (self::tagsOnSite() as $tag) {
            $result        = self::checkOnSite($tag);
            $summary[$tag] = [
                'name'      => (string) ($known[$tag]['name'] ?? $tag),
                'texts'     => $result['texts'],
                'missing'   => \count($result['missing']),
                'problems'  => \count($result['problems']),
                'errors'    => \count(array_filter($result['problems'], static fn ($p) => $p['kind'] === self::EXTRA)),
                'installed' => isset($known[$tag]),
            ];
        }

        // The languages of the site first: they are the ones the visitors see.
        uksort($summary, static fn ($a, $b) => [!$summary[$a]['installed'], $a] <=> [!$summary[$b]['installed'], $b]);

        return $summary;
    }

    /**
     * The languages which have files of Kunena on this site, English excluded.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function tagsOnSite(): array
    {
        $tags = [];

        foreach (self::foldersOnSite() as $folder) {
            foreach ((array) glob($folder . '/*', GLOB_ONLYDIR) as $dir) {
                $tag = basename((string) $dir);

                if (preg_match('/^[a-z]{2,3}-[A-Z]{2}$/', $tag) && $tag !== 'en-GB') {
                    $tags[$tag] = $tag;
                }
            }
        }

        ksort($tags);

        return array_values($tags);
    }

    /**
     * A language of this site against English, file by file.
     *
     * @param   string  $tag  The language tag.
     *
     * @return  array{texts: int, missing: array<int, array{file: string, key: string, english: string}>, problems: array}
     *
     * @since   Kunena 8.0
     */
    public static function checkOnSite(string $tag): array
    {
        $result = ['texts' => 0, 'missing' => [], 'problems' => []];

        if (!preg_match('/^[a-z]{2,3}-[A-Z]{2}$/', $tag)) {
            return $result;
        }

        foreach (self::foldersOnSite() as $folder) {
            foreach ((array) glob($folder . '/en-GB/*.ini') as $file) {
                $name    = basename((string) $file);
                $english = self::read((string) $file);
                $found   = self::compare($english, self::read($folder . '/' . $tag . '/' . $name), $tag);
                $where   = ($folder === JPATH_ADMINISTRATOR . '/components/com_kunena/language' ? 'admin/' : 'site/') . $name;

                $result['texts'] += \count($english);

                foreach ($found['missing'] as $key) {
                    $result['missing'][] = ['file' => $where, 'key' => $key, 'english' => (string) $english[$key]];
                }

                foreach ($found['problems'] as $problem) {
                    $result['problems'][] = ['file' => $where] + $problem;
                }
            }
        }

        // The worst first over all the files of the language.
        $order = [self::EXTRA => 0, self::DROPPED => 1, self::GLUED => 2];
        usort($result['problems'], static fn ($a, $b) => $order[$a['kind']] <=> $order[$b['kind']]);

        return $result;
    }
}
