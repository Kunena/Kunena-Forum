/**
 * Kunena       Component
 * @package     Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

// Colours the [code] blocks of the messages with highlight.js, and nothing else on the page.
(function () {
    'use strict';

    function highlightCodeBlocks() {
        const hljs = window.hljs;

        if (!hljs) {
            return;
        }

        document.querySelectorAll('pre.bbcode_code_body > code:not([data-highlighted])').forEach(function (block) {
            const match = /(?:^|\s)language-(\S+)/.exec(block.className);

            // A language highlight.js does not know stays plain text.
            if (match && !hljs.getLanguage(match[1])) {
                return;
            }

            hljs.highlightElement(block);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', highlightCodeBlocks);
    } else {
        highlightCodeBlocks();
    }
})();
