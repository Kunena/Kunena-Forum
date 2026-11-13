<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Image
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Image;

\defined('_JEXEC') or die;

use DOMDocument;
use enshrined\svgSanitize\Sanitizer;
use Exception;

/**
 * Cleans an untrusted SVG file (an uploaded avatar) with the bundled enshrined/svg-sanitize library.
 *
 * @since   Kunena 8.0
 */
class KunenaSvgSanitizer
{
    /**
     * Clean the markup of an SVG file: scripts, event attributes, javascript: links, foreignObject and remote references are removed.
     *
     * @param   string  $svg  The content of the uploaded file.
     *
     * @return  string|null  The cleaned SVG markup, or null when the input cannot be made safe.
     *
     * @since   Kunena 8.0
     */
    public static function clean(string $svg): ?string
    {
        // An entity declaration is refused, not stripped (XXE, entity expansion).
        if (stripos($svg, '<!ENTITY') !== false) {
            return null;
        }

        $sanitizer = new Sanitizer();
        $sanitizer->removeRemoteReferences(true);

        try {
            $clean = $sanitizer->sanitize($svg);
        } catch (Exception $e) {
            // The library throws on some documents which are not SVG (no svg element).
            return null;
        }

        if ($clean === false || trim($clean) === '' || stripos($clean, '<!DOCTYPE') !== false) {
            return null;
        }

        // The root of the cleaned document must be svg: an XML or HTML file under an .svg name is refused.
        $previous = libxml_use_internal_errors(true);
        $dom      = new DOMDocument();
        $loaded   = $dom->loadXML($clean, LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded || $dom->documentElement === null || $dom->documentElement->localName !== 'svg') {
            return null;
        }

        return $clean;
    }
}
