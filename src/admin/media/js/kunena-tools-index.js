/**
 * Kunena Component
 *
 * The page of the tools: a search which filters the cards while typing (name and description, the words in any order,
 * accents ignored; Enter opens the first one), the tools pinned with their star and the tools used last in this browser on
 * top.
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

(function () {
    'use strict';

    var RECENT = 'kunena.admin.tools.recent';
    var PINNED = 'kunena.admin.tools.pinned';
    var KEEP = 4;
    var PINS = 24;

    function normal(text) {
        return String(text || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    }

    // The storage of the browser can be refused (a private window, blocked site data): the page works without it.
    function read(key) {
        try {
            var list = JSON.parse(window.localStorage.getItem(key) || '[]');

            return Array.isArray(list) ? list.filter(function (item) { return typeof item === 'string'; }) : [];
        } catch (e) {
            return [];
        }
    }

    function write(key, list, keep) {
        try {
            window.localStorage.setItem(key, JSON.stringify(list.slice(0, keep)));
        } catch (e) {
            // Not kept: the next visit does not have it.
        }
    }

    function start() {
        var input = document.getElementById('kd-tools-search');
        var none = document.getElementById('kd-tools-none');
        var recent = document.getElementById('kd-tools-recent');
        var pinned = document.getElementById('kd-tools-pinned');

        if (!input) {
            return;
        }

        var sections = Array.prototype.slice.call(document.querySelectorAll('#kunena .kd-section[data-kd-tools]'));
        var cards = Array.prototype.slice.call(document.querySelectorAll('#kunena .kd-section[data-kd-tools] .kd-card'));
        var texts = cards.map(function (card) { return normal(card.textContent); });

        function original(layout) {
            return cards.find(function (c) { return c.getAttribute('data-kd-tool') === layout; });
        }

        function title(card) {
            var name = card.querySelector('.kd-card__title');

            return name ? name.textContent.trim() : '';
        }

        // The star of a card: a button next to the link (a button cannot be inside a link), in a box around both.
        function addPin(card) {
            if (!pinned) {
                return card;
            }

            var box = document.createElement('div');
            var pin = document.createElement('button');

            box.className = 'kd-card-box';
            pin.type = 'button';
            pin.className = 'kd-pin';
            pin.setAttribute('data-kd-pin', card.getAttribute('data-kd-tool'));
            pin.innerHTML = '<span class="fa-solid fa-star" aria-hidden="true"></span>';
            card.parentNode.insertBefore(box, card);
            box.appendChild(card);
            box.appendChild(pin);

            return box;
        }

        function copy(layout) {
            var card = original(layout);

            if (!card) {
                return null;
            }

            var holder = document.createElement('div');

            holder.appendChild(card.cloneNode(true));

            return pinned ? addPin(holder.firstChild) : holder.firstChild;
        }

        function showPins() {
            var list = read(PINNED);
            var texts = pinned ? [pinned.getAttribute('data-kd-pin'), pinned.getAttribute('data-kd-unpin')] : ['', ''];

            document.querySelectorAll('#kunena [data-kd-pin]').forEach(function (pin) {
                var layout = pin.getAttribute('data-kd-pin');
                var on = list.indexOf(layout) !== -1;
                var card = original(layout);

                pin.setAttribute('aria-pressed', on ? 'true' : 'false');
                pin.firstChild.className = (on ? 'fa-solid' : 'fa-regular') + ' fa-star';
                pin.setAttribute('aria-label', (on ? texts[1] : texts[0]).replace('%s', card ? title(card) : layout));
                pin.title = pin.getAttribute('aria-label');
            });
        }

        function fillPinned() {
            if (!pinned) {
                return;
            }

            var holder = pinned.querySelector('.kd-cards');

            holder.textContent = '';

            read(PINNED).forEach(function (layout) {
                var card = copy(layout);

                if (card) {
                    holder.appendChild(card);
                }
            });

            pinned.hidden = input.value.trim() !== '' || !holder.firstChild;
            showPins();
        }

        cards.forEach(addPin);

        // The tools used last, as copies of their cards.
        if (recent) {
            read(RECENT).forEach(function (layout) {
                var card = copy(layout);

                if (card) {
                    recent.querySelector('.kd-cards').appendChild(card);
                }
            });

            recent.hidden = !recent.querySelector('.kd-card');
        }

        fillPinned();

        document.addEventListener('click', function (event) {
            var pin = event.target.closest('#kunena [data-kd-pin]');

            if (pin) {
                var layout = pin.getAttribute('data-kd-pin');
                var list = read(PINNED);

                list = list.indexOf(layout) === -1 ? list.concat([layout]) : list.filter(function (item) { return item !== layout; });
                write(PINNED, list, PINS);
                fillPinned();

                return;
            }

            var card = event.target.closest('#kunena .kd-card[data-kd-tool]');

            if (card) {
                var tool = card.getAttribute('data-kd-tool');
                write(RECENT, [tool].concat(read(RECENT).filter(function (item) { return item !== tool; })), KEEP);
            }
        });

        function filter() {
            var words = normal(input.value).split(/\s+/).filter(Boolean);
            var found = 0;

            cards.forEach(function (card, i) {
                var match = words.every(function (word) { return texts[i].indexOf(word) !== -1; });

                (card.parentNode.classList.contains('kd-card-box') ? card.parentNode : card).hidden = !match;
                found += match ? 1 : 0;
            });

            sections.forEach(function (section) {
                section.hidden = !section.querySelector('.kd-card-box:not([hidden]), .kd-cards > .kd-card:not([hidden])');
            });

            [recent, pinned].forEach(function (extra) {
                if (extra) {
                    extra.hidden = words.length > 0 || !extra.querySelector('.kd-card');
                }
            });

            if (none) {
                none.hidden = found > 0;
            }
        }

        input.addEventListener('input', filter);

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                var first = cards.find(function (card) { return !(card.parentNode.hidden || card.hidden); });

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
