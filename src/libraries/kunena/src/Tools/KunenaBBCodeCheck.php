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

use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\BBCode\KunenaBBCode;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Forum\Libraries\BBCode\KunenaMarkup;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Nbbc\BBCode;
use ReflectionClass;

/**
 * The messages whose BBCode is broken (tool "BBCode check"): a tag which is never closed, a closing tag without its
 * opening one, a tag which Kunena does not know. The content of a verbatim tag ([code], [img]...) is not read: it is
 * shown as it is; such a tag without its closing tag is shown as text by the parser, so it is no problem. The rules are
 * those of the parser of Kunena (KunenaBBCodeLibrary), and the text is the one it gets: Markdown and HTML converted.
 *
 * @since   Kunena 8.0
 */
class KunenaBBCodeCheck
{
    /**
     * At most this many recent messages are checked.
     *
     * @since   Kunena 8.0
     */
    public const SCAN_LIMIT = 3000;

    /**
     * At most this many messages with problems are listed.
     *
     * @since   Kunena 8.0
     */
    public const LIST_LIMIT = 100;

    /**
     * A tag left open.
     *
     * @since   Kunena 8.0
     */
    public const UNCLOSED = 'unclosed';

    /**
     * A closing tag without its opening one.
     *
     * @since   Kunena 8.0
     */
    public const STRAY = 'stray';

    /**
     * A tag which Kunena does not know (opened and closed: "[sic]" alone is text).
     *
     * @since   Kunena 8.0
     */
    public const UNKNOWN = 'unknown';

    /**
     * The tags which Kunena itself writes alone: [PRIVATE=id] is the text of a message with only private content
     * (TopicController).
     *
     * @since   Kunena 8.0
     */
    public const STANDALONE = ['private'];

    /**
     * The tags of Kunena from the rules of its parser: name => [void: no closing tag, verbatim: content not parsed].
     *
     * @param   array  $rules  The rules (KunenaBBCodeLibrary::$default_tag_rules).
     *
     * @return  array<string, array{void: bool, verbatim: bool}>
     *
     * @since   Kunena 8.0
     */
    public static function tags(array $rules): array
    {
        $tags = [];

        foreach ($rules as $name => $rule) {
            $tags[strtolower((string) $name)] = [
                'void' => ($rule['end_tag'] ?? null) === BBCode::BBCODE_PROHIBIT || ($rule['end_tag'] ?? null) === BBCode::BBCODE_OPTIONAL
                    || \in_array(strtolower((string) $name), self::STANDALONE, true),
                'verbatim' => ($rule['content'] ?? null) === BBCode::BBCODE_VERBATIM,
            ];
        }

        return $tags;
    }

    /**
     * The tags of the parser of this Kunena.
     *
     * @return  array<string, array{void: bool, verbatim: bool}>
     *
     * @since   Kunena 8.0
     */
    public static function tagsOnSite(): array
    {
        // The library is declared in the file of KunenaBBCode: the autoloader finds it through that class.
        class_exists(KunenaBBCode::class);

        $defaults = (new ReflectionClass(KunenaBBCodeLibrary::class))->getDefaultProperties();

        return self::tags((array) ($defaults['default_tag_rules'] ?? []));
    }

    /**
     * The problems of a text.
     *
     * @param   string  $text  The BBCode.
     * @param   array   $tags  The tags (tags()).
     *
     * @return  array  A list of [kind, tag].
     *
     * @since   Kunena 8.0
     */
    public static function check(string $text, array $tags): array
    {
        preg_match_all('#\[(/?)(\*|[a-z][a-z0-9]*)([= ][^\]\[]*)?\]#i', $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        // As the lexer of the parser: a start tag whose name is followed by a word without a value is text in square
        // brackets ("[Quote of the day]"), not a tag.
        $matches = array_values(array_filter($matches, static function (array $match): bool {
            $params = (string) ($match[3][0] ?? '');

            return $match[1][0] === '/' || trim($params) === '' || preg_match('#^\s*=|^\s+("[^"]*"|\'[^\']*\'|[^\s="\']+)\s*=#', $params);
        }));

        $problems = [];
        $stack    = [];
        $unknown  = [];
        $count    = \count($matches);

        for ($i = 0; $i < $count; $i++) {
            $match   = $matches[$i];
            $closing = $match[1][0] === '/';
            $name    = strtolower($match[2][0]);

            // A verbatim tag (code): nothing is read up to its own closing tag. Without a closing tag the parser shows the
            // tag as text and reads the rest as usual ("the [code] tag" in a sentence), so it is no problem.
            if (!$closing && isset($tags[$name]) && $tags[$name]['verbatim']) {
                for ($end = $i + 1; $end < $count; $end++) {
                    if ($matches[$end][1][0] === '/' && strtolower($matches[$end][2][0]) === $name) {
                        break;
                    }
                }

                if ($end < $count) {
                    $i = $end;
                }

                continue;
            }

            if (!isset($tags[$name])) {
                $unknown[$name][$closing ? 1 : 0] = true;

                continue;
            }

            if ($tags[$name]['void']) {
                continue;
            }

            if (!$closing) {
                $stack[] = $name;

                continue;
            }

            $at = array_search($name, array_reverse($stack, true), true);

            if ($at === false) {
                $problems[] = [self::STRAY, $name];

                continue;
            }

            // The tags opened after it and not closed are left open.
            foreach (\array_slice($stack, $at + 1) as $open) {
                $problems[] = [self::UNCLOSED, $open];
            }

            $stack = \array_slice($stack, 0, $at);
        }

        foreach ($stack as $open) {
            $problems[] = [self::UNCLOSED, $open];
        }

        foreach ($unknown as $name => $sides) {
            if (\count($sides) === 2) {
                $problems[] = [self::UNKNOWN, $name];
            }
        }

        return $problems;
    }

    /**
     * The recent messages of this site with problems.
     *
     * @return  array{checked: int, items: array}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(): array
    {
        $tags  = self::tagsOnSite();
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['m.id', 'm.thread', 'm.catid', 'm.subject', 'm.time', 'm.name', 't.message']))
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->innerJoin($db->quoteName('#__kunena_messages_text', 't') . ' ON ' . $db->quoteName('t.mesid') . ' = ' . $db->quoteName('m.id'))
            ->where($db->quoteName('m.hold') . ' = ' . KunenaForum::PUBLISHED)
            ->order($db->quoteName('m.id') . ' DESC');
        $rows  = (array) $db->setQuery($query, 0, self::SCAN_LIMIT)->loadAssocList();
        $items = [];

        // The parser reads Markdown and HTML as BBCode when the settings allow them (KunenaBBCode::parse()): the text is
        // checked as the parser gets it ("![img](...)" is an image, not an [img] tag which is not closed).
        $config   = KunenaConfig::getInstance();
        $markdown = !empty($config->enableMarkdown);
        $html     = !empty($config->enableHtml);

        foreach ($rows as $row) {
            $text     = (string) $row['message'];
            $problems = self::check($markdown || $html ? KunenaMarkup::toBBCode($text, $markdown, $html) : $text, $tags);

            if ($problems) {
                $items[] = $row + ['problems' => $problems];

                if (\count($items) >= self::LIST_LIMIT) {
                    break;
                }
            }
        }

        return ['checked' => \count($rows), 'items' => $items];
    }
}
