<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\BBCode;

\defined('_JEXEC') or die();

/**
 * Converts Markdown and a safe subset of HTML into BBCode.
 *
 * The BBCode parser stays the only place which creates HTML for a message: everything which is not understood stays
 * plain text, and plain text is escaped by the parser. Links and images are only converted when their address is
 * a http(s) address, a mail address or a path of the site, so "javascript:" and "data:" addresses never get through.
 *
 * Existing BBCode is not touched: code blocks and links are protected, so a message in BBCode looks the same with
 * and without this conversion.
 *
 * @since   Kunena 8.0
 */
final class KunenaMarkup
{
    /**
     * The deepest quote in a quote which Markdown makes.
     *
     * @var     int
     * @since   Kunena 8.0
     */
    private const int MAX_QUOTE_DEPTH = 10;

    /**
     * Pieces of text which are protected against the conversion, restored at the end.
     *
     * @var     array
     * @since   Kunena 8.0
     */
    private array $store = [];

    /**
     * Code with the text of the code as a verbatim BBCode block.
     *
     * @param   string  $code      The code.
     * @param   string  $language  The language of the code, only letters and digits are kept.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    private function codeBlock(string $code, string $language = ''): string
    {
        // The code must not be able to end the block.
        $code     = str_ireplace('[/code]', '[ /code]', trim($code, "\n"));
        $language = preg_replace('/[^A-Za-z0-9+#_-]/', '', $language);

        return $this->stash('[code' . ($language !== '' ? '=' . $language : '') . ']' . $code . '[/code]');
    }

    /**
     * @param   string  $text      The message.
     * @param   bool    $markdown  Understand Markdown.
     * @param   bool    $html      Understand a safe subset of HTML.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    private function convert(string $text, bool $markdown, bool $html): string
    {
        // The markers used for the protected pieces must not be in the message.
        $text = str_replace(["\x1A", "\x1B", "\r\n", "\r"], ['', '', "\n", "\n"], $text);

        // Code is never converted: existing BBCode code, and the code of Markdown and HTML.
        $text = (string) preg_replace_callback('~\[(code|noparse)\b[^\]]*\].*?\[/\1\]~is', fn (array $m): string => $this->stash($m[0]), $text);

        if ($markdown) {
            $text = $this->fencedCode($text);
        }

        if ($html) {
            $text = $this->html($text);
        }

        // Links and other tags with an address are protected, the address must stay as it is.
        $text = (string) preg_replace_callback('~\[(url|img|video|file|attachment|email|map|ebay)\b[^\]]*\].*?\[/\1\]~is', fn (array $m): string => $this->stash($m[0]), $text);
        $text = (string) preg_replace_callback('~(?<![(<])\bhttps?://[^\s<>\[\]"]+~i', fn (array $m): string => $this->stash($m[0]), $text);

        if ($markdown) {
            $text = $this->markdown($text);
        }

        return $this->restore($text);
    }

    /**
     * Markdown code between lines with three backticks or tildes.
     *
     * @param   string  $text  The message.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    private function fencedCode(string $text): string
    {
        return (string) preg_replace_callback(
            '#^[ \t]{0,3}(`{3,}|~{3,})[ \t]*([^\n`]*)\n(.*?)\n?^[ \t]{0,3}\1[`~]*[ \t]*$#ms',
            fn (array $m): string => $this->codeBlock($m[3], trim($m[2])),
            $text
        );
    }

    /**
     * The safe subset of HTML.
     *
     * @param   string  $text  The message.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    private function html(string $text): string
    {
        // Code blocks.
        $text = (string) preg_replace_callback(
            '~<pre\b[^>]*>\s*(?:<code\b(?:[^>]*?class\s*=\s*["\'][^"\']*?language-([A-Za-z0-9+#_-]+)[^"\']*["\'])?[^>]*>)?(.*?)(?:</code>)?\s*</pre>~is',
            fn (array $m): string => $this->codeBlock(htmlspecialchars_decode(strip_tags($m[2]), ENT_QUOTES), $m[1] ?? ''),
            $text
        );

        // Code in a line.
        $text = (string) preg_replace_callback(
            '~<code\b[^>]*>(.*?)</code>~is',
            fn (array $m): string => $this->stash('[font=monospace]' . htmlspecialchars_decode(strip_tags($m[1]), ENT_QUOTES) . '[/font]'),
            $text
        );

        // An editor can store the characters of a tag as entities: they are the tag again before it is converted.
        $text = str_ireplace(['&lt;', '&gt;', '&quot;', '&#039;', '&#39;', '&nbsp;'], ['<', '>', '"', "'", "'", ' '], $text);

        // Links and images, with an address which is checked.
        $text = (string) preg_replace_callback(
            '~<a\s[^>]*?href\s*=\s*(["\'])(.*?)\1[^>]*>(.*?)</a>~is',
            function (array $m): string {
                $url = self::safeUrl($m[2]);

                return $url === null ? $m[3] : '[url=' . $url . ']' . $m[3] . '[/url]';
            },
            $text
        );
        $text = (string) preg_replace_callback(
            '~<img\s[^>]*?src\s*=\s*(["\'])(.*?)\1[^>]*>~is',
            function (array $m): string {
                $url = self::safeUrl($m[2]);

                return $url === null ? '' : '[img]' . $url . '[/img]';
            },
            $text
        );

        // Headings.
        $sizes = [1 => 6, 2 => 5, 3 => 4, 4 => 3, 5 => 3, 6 => 2];
        $text  = (string) preg_replace_callback(
            '~<h([1-6])\b[^>]*>(.*?)</h\1>~is',
            fn (array $m): string => "\n[size=" . $sizes[(int) $m[1]] . '][b]' . $m[2] . "[/b][/size]\n",
            $text
        );

        // Tags with the same meaning in BBCode. The attributes of these tags are dropped.
        $pairs = [
            'b'     => 'b', 'strong' => 'b', 'i' => 'i', 'em' => 'i', 'u' => 'u', 's' => 's', 'strike' => 's', 'del' => 's',
            'sup'   => 'sup', 'sub' => 'sub', 'blockquote' => 'quote', 'ul' => 'ul', 'ol' => 'ol', 'li' => 'li',
            'table' => 'table', 'tr' => 'tr', 'th' => 'th', 'td' => 'td',
        ];

        foreach ($pairs as $tag => $bbcode) {
            $text = (string) preg_replace('~<' . $tag . '(?:\s[^<>]*)?>~i', '[' . $bbcode . ']', $text);
            $text = (string) preg_replace('~</' . $tag . '\s*>~i', '[/' . $bbcode . ']', $text);
        }

        // Paragraphs and line breaks.
        $text = (string) preg_replace('~</p\s*>~i', "\n\n", $text);
        $text = (string) preg_replace('~<p(?:\s[^<>]*)?>~i', '', $text);
        $text = (string) preg_replace('~<br\s*/?>~i', "\n", $text);
        $text = (string) preg_replace('~</?div(?:\s[^<>]*)?>~i', "\n", $text);
        $text = (string) preg_replace('~<hr\s*/?>~i', "\n", $text);

        // The ampersand of an address or a text, the parser escapes the result again.
        return str_ireplace('&amp;', '&', $text);
    }

    /**
     * Markdown in a line of text: code, links, images, bold, italic and strikethrough.
     *
     * @param   string  $text  The text.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    private function inline(string $text): string
    {
        // A backslash makes the next character plain text.
        $text = (string) preg_replace_callback('/\\\\([\\\\`*_{}\[\]()#+\-.!>|~])/', fn (array $m): string => $this->stash($m[1]), $text);

        // Code in a line.
        $text = (string) preg_replace_callback('/`([^`\n]+)`/', fn (array $m): string => $this->stash('[font=monospace]' . $m[1] . '[/font]'), $text);

        // Images and links: the address must be allowed.
        $text = (string) preg_replace_callback(
            '/!\[[^\]\n]*\]\(\s*<?([^\s)>]+)>?(?:\s+"[^"\n]*")?\s*\)/',
            function (array $m): string {
                $url = self::safeUrl($m[1]);

                return $url === null ? $m[0] : $this->stash('[img]' . $url . '[/img]');
            },
            $text
        );
        $text = (string) preg_replace_callback(
            '/\[([^\]\n]+)\]\(\s*<?([^\s)>]+)>?(?:\s+"[^"\n]*")?\s*\)/',
            function (array $m): string {
                $url = self::safeUrl($m[2]);

                return $url === null ? $m[0] : $this->stash('[url=' . $url . ']' . $this->inline($m[1]) . '[/url]');
            },
            $text
        );
        $text = (string) preg_replace_callback(
            '/<((?:https?:\/\/|mailto:)[^\s<>]+)>/i',
            function (array $m): string {
                $url = self::safeUrl($m[1]);

                return $url === null ? $m[0] : $this->stash('[url]' . $url . '[/url]');
            },
            $text
        );

        // Bold, italic and strikethrough. An underscore inside a word is not a mark.
        $text = (string) preg_replace('/\*\*(?=\S)(.+?)(?<=\S)\*\*/', '[b]$1[/b]', $text);
        $text = (string) preg_replace('/(?<![\w])__(?=\S)(.+?)(?<=\S)__(?![\w])/', '[b]$1[/b]', $text);
        $text = (string) preg_replace('/(?<![\w*])\*(?=[^\s*])(.+?)(?<=[^\s*])\*(?![\w*])/', '[i]$1[/i]', $text);
        $text = (string) preg_replace('/(?<![\w])_(?=[^\s_])(.+?)(?<=[^\s_])_(?![\w])/', '[i]$1[/i]', $text);
        $text = (string) preg_replace('/~~(?=\S)(.+?)(?<=\S)~~/', '[s]$1[/s]', $text);

        return $text;
    }

    /**
     * Markdown, line by line: headings, quotes, lists and tables. The text of the lines is converted by inline().
     *
     * @param   string  $text   The message.
     * @param   int     $depth  The number of quotes around the text.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    private function markdown(string $text, int $depth = 0): string
    {
        $lines = explode("\n", $text);
        $out   = [];
        $stack = [];
        $count = \count($lines);

        $closeLists = static function () use (&$out, &$stack): void {
            while ($stack) {
                $list  = array_pop($stack);
                $out[] = '[/li][/' . $list['type'] . ']';
            }
        };

        for ($i = 0; $i < $count; $i++) {
            $line = $lines[$i];

            // List item.
            if (preg_match('/^([ \t]*)([-*+]|\d{1,9}[.)])[ \t]+(.+)$/', $line, $m) && !preg_match('/^[ \t]*([-*_])([ \t]*\1){2,}[ \t]*$/', $line)) {
                $indent = \strlen(str_replace("\t", '    ', $m[1]));
                $type   = ctype_digit($m[2][0]) ? 'ol' : 'ul';
                $item   = '[li]' . $this->inline($m[3]);

                if (!$stack || $indent > end($stack)['indent']) {
                    $stack[] = ['type' => $type, 'indent' => $indent];
                    $out[]   = '[' . $type . ']' . $item;
                } else {
                    while (\count($stack) > 1 && $indent < end($stack)['indent']) {
                        $list  = array_pop($stack);
                        $out[] = '[/li][/' . $list['type'] . ']';
                    }

                    $out[] = '[/li]' . $item;
                }

                continue;
            }

            $closeLists();

            // Heading.
            if (preg_match('/^[ \t]{0,3}(#{1,6})[ \t]+(.+?)[ \t]*#*[ \t]*$/', $line, $m)) {
                $sizes = [1 => 6, 2 => 5, 3 => 4, 4 => 3, 5 => 3, 6 => 2];
                $out[] = '[size=' . $sizes[\strlen($m[1])] . '][b]' . $this->inline($m[2]) . '[/b][/size]';

                continue;
            }

            // Quote: all following lines which start with ">". Every level copies the text of the quote, so the levels are
            // limited: thousands of ">" used all the memory of the page. Deeper marks stay text.
            if ($depth < self::MAX_QUOTE_DEPTH && preg_match('/^[ \t]{0,3}>[ \t]?(.*)$/', $line, $m)) {
                $quote = [$m[1]];

                while ($i + 1 < $count && preg_match('/^[ \t]{0,3}>[ \t]?(.*)$/', $lines[$i + 1], $next)) {
                    $quote[] = $next[1];
                    $i++;
                }

                $out[] = '[quote]' . $this->markdown(implode("\n", $quote), $depth + 1) . '[/quote]';

                continue;
            }

            // Table: a row with a pipe, followed by the row with the dashes.
            if (strpos($line, '|') !== false && $i + 1 < $count && preg_match('/^[ \t]*\|?[ \t]*:?-{3,}:?[ \t]*(\|[ \t]*:?-{3,}:?[ \t]*)*\|?[ \t]*$/', $lines[$i + 1])) {
                $rows = ['[tr]' . $this->tableCells($line, 'th') . '[/tr]'];
                $i++;

                while ($i + 1 < $count && strpos($lines[$i + 1], '|') !== false) {
                    $i++;
                    $rows[] = '[tr]' . $this->tableCells($lines[$i], 'td') . '[/tr]';
                }

                $out[] = '[table]' . implode("\n", $rows) . '[/table]';

                continue;
            }

            $out[] = $this->inline($line);
        }

        $closeLists();

        return implode("\n", $out);
    }

    /**
     * @param   string  $text  Text with markers.
     *
     * @return  string  The text with the protected pieces back in place.
     *
     * @since   Kunena 8.0
     */
    private function restore(string $text): string
    {
        // A protected piece can contain markers of another piece.
        for ($i = 0; $i < 10 && strpos($text, "\x1A") !== false; $i++) {
            $text = (string) preg_replace_callback('/\x1A(\d+)\x1B/', fn (array $m): string => $this->store[(int) $m[1]] ?? '', $text);
        }

        return $text;
    }

    /**
     * Check an address: only web addresses, mail addresses and paths of the site are allowed.
     *
     * @param   string  $url  The address.
     *
     * @return  string|null  The address, null when it is not allowed.
     *
     * @since   Kunena 8.0
     */
    public static function safeUrl(string $url): ?string
    {
        $url = trim(htmlspecialchars_decode($url, ENT_QUOTES));

        if (!preg_match('~^(?:https?://|mailto:|/(?!/))[^\s"\'<>\[\]\\\\\x00-\x1F]+$~i', $url)) {
            return null;
        }

        return $url;
    }

    /**
     * Keep a piece of text out of the conversion.
     *
     * @param   string  $value  The text.
     *
     * @return  string  A marker for the text.
     *
     * @since   Kunena 8.0
     */
    private function stash(string $value): string
    {
        $this->store[] = $value;

        return "\x1A" . (\count($this->store) - 1) . "\x1B";
    }

    /**
     * @param   string  $row  A row of a Markdown table.
     * @param   string  $tag  th or td.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    private function tableCells(string $row, string $tag): string
    {
        $cells = explode('|', trim(trim($row), '|'));
        $out   = '';

        foreach ($cells as $cell) {
            $out .= '[' . $tag . ']' . $this->inline(trim($cell)) . '[/' . $tag . ']';
        }

        return $out;
    }

    /**
     * Convert a message to BBCode.
     *
     * @param   string  $text      The message.
     * @param   bool    $markdown  Understand Markdown.
     * @param   bool    $html      Understand a safe subset of HTML.
     *
     * @return  string  The message as BBCode.
     *
     * @since   Kunena 8.0
     */
    public static function toBBCode(string $text, bool $markdown, bool $html): string
    {
        if ((!$markdown && !$html) || $text === '') {
            return $text;
        }

        return (new self())->convert($text, $markdown, $html);
    }
}
