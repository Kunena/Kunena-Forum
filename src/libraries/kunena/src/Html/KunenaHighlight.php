<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Html
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Html;

\defined('_JEXEC') or die();

use DOMDocument;
use DOMText;
use DOMXPath;

/**
 * The words of a search marked in HTML (the results of the search): only in the text, never in a tag, an attribute or an entity
 * ("amp" in "&amp;"), and with the case of the text. A regular expression over the HTML broke it.
 *
 * @since   Kunena 8.0
 */
class KunenaHighlight
{
    /**
     * The elements whose text is not marked: code, and what is no text to read.
     *
     * @since   Kunena 8.0
     */
    public const SKIPPED = ['script', 'style', 'textarea', 'code', 'pre', 'mark'];

    /**
     * The words marked in the HTML.
     *
     * @param   string    $html   The HTML.
     * @param   string[]  $words  The words.
     *
     * @return  string  The HTML with <mark class="kunena-searchword"> around each word, the HTML as it was without a word.
     *
     * @since   Kunena 8.0
     */
    public static function html(string $html, array $words): string
    {
        $words = array_values(array_unique(array_filter(array_map(static fn ($word) => trim((string) $word), $words), static fn ($word) => $word !== '')));

        if ($html === '' || !$words) {
            return $html;
        }

        // The longest first: "forum" before "for".
        usort($words, static fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        $pattern = '/(' . implode('|', array_map(static fn ($word) => preg_quote($word, '/'), $words)) . ')/iu';

        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded   = $document->loadHTML('<?xml encoding="utf-8"?><div id="kunena-highlight">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $loaded ? $document->getElementById('kunena-highlight') : null;

        if (!$root) {
            return $html;
        }

        $skipped = implode(' or ', array_map(static fn ($name) => 'ancestor::' . $name, self::SKIPPED));
        $texts   = (new DOMXPath($document))->query('.//text()[not(' . $skipped . ')]', $root);

        foreach (iterator_to_array($texts) as $text) {
            self::mark($text, $pattern);
        }

        $out = '';

        foreach ($root->childNodes as $child) {
            $out .= $document->saveHTML($child);
        }

        return $out;
    }

    /**
     * The words of a text node in marks.
     *
     * @param   DOMText  $text     The text.
     * @param   string   $pattern  The words.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    private static function mark(DOMText $text, string $pattern): void
    {
        $parts = preg_split($pattern, $text->data, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($parts === false || \count($parts) < 2) {
            return;
        }

        $document = $text->ownerDocument;
        $parent   = $text->parentNode;

        foreach ($parts as $index => $part) {
            if ($part === '') {
                continue;
            }

            // The odd parts are the words which were found.
            if ($index % 2) {
                $mark = $document->createElement('mark');
                $mark->setAttribute('class', 'kunena-searchword');
                $mark->appendChild($document->createTextNode($part));
                $parent->insertBefore($mark, $text);
            } else {
                $parent->insertBefore($document->createTextNode($part), $text);
            }
        }

        $parent->removeChild($text);
    }
}
