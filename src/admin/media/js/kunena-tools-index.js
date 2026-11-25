/**
 * Kunena Component
 *
 * The page of the tools: a search which filters the cards while typing (name and description, the words in any order,
 * accents ignored; Enter opens the first one), and the tools used last in this browser on top.
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

(function () {
    'use strict';

    var KEY = 'kunena.admin.tools.recent';
    var KEEP = 4;

    function normal(text) {
        return String(text || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    }

    // The storage of the browser can be refused (a private window, blocked site data): the page works without it.
    function readRecent() {
        try {
            var list = JSON.parse(window.localStorage.getItem(KEY) || '[]');

            return Array.isArray(list) ? list.filter(function (item) { return typeof item === 'string'; }) : [];
        } catch (e) {
            return [];
        }
    }

    function writeRecent(list) {
        try {
            window.localStorage.setItem(KEY, JSON.stringify(list.slice(0, KEEP)));
        } catch (e) {
            // Not kept: the next visit shows no recent tools.
        }
    }

    function start() {
        var input = document.getElementById('kd-tools-search');
        var none = document.getElementById('kd-tools-none');
        var recent = document.getElementById('kd-tools-recent');

        if (!input) {
            return;
        }

        var sections = Array.prototype.slice.call(document.querySelectorAll('#kunena .kd-section:not(#kd-tools-recent)'));
        var cards = Array.prototype.slice.call(document.querySelectorAll('#kunena .kd-section:not(#kd-tools-recent) .kd-card'));
        var texts = cards.map(function (card) { return normal(card.textContent); });

        // The tools used last, as copies of their cards.
        if (recent) {
            var shown = 0;

            readRecent().forEach(function (layout) {
                var card = cards.find(function (c) { return c.getAttribute('data-kd-tool') === layout; });

                if (card) {
                    recent.querySelector('.kd-cards').appendChild(card.cloneNode(true));
                    shown++;
                }
            });

            recent.hidden = shown === 0;
        }

        document.addEventListener('click', function (event) {
            var card = event.target.closest('#kunena .kd-card[data-kd-tool]');

            if (card) {
                var layout = card.getAttribute('data-kd-tool');
                writeRecent([layout].concat(readRecent().filter(function (item) { return item !== layout; })));
            }
        });

        function filter() {
            var words = normal(input.value).split(/\s+/).filter(Boolean);
            var found = 0;

            cards.forEach(function (card, i) {
                var match = words.every(function (word) { return texts[i].indexOf(word) !== -1; });

                card.hidden = !match;
                found += match ? 1 : 0;
            });

            sections.forEach(function (section) {
                section.hidden = !section.querySelector('.kd-card:not([hidden])');
            });

            if (recent) {
                recent.hidden = words.length > 0 || !recent.querySelector('.kd-card');
            }

            if (none) {
                none.hidden = found > 0;
            }
        }

        input.addEventListener('input', filter);

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                var first = cards.find(function (card) { return !card.hidden; });

                event.preventDefault();

                if (first && input.value.trim() !== '') {
                    first.click();
                }
            } else if (event.key === 'Escape') {
                input.value = '';
                filter();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
