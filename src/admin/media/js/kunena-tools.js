/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

/*
 * The behaviour shared by the pages of the tools:
 * - a button with data-kd-confirm asks first (its value, or the text COM_KUNENA_TOOLS_CONFIRM);
 * - a checkbox with data-kd-all="name" checks or unchecks the checkboxes of the class "name";
 * - a button with data-kd-frame="url" opens the page in a dialog (the indexer of Smart Search);
 * - a field with data-kd-users="url of the suggestions" suggests users while typing; the one chosen opens
 *   data-kd-users-go (its __ID__ becomes the id of the user).
 */
(function () {
    'use strict';

    var suggestCount = 0;

    function setupUserField(input) {
        var endpoint = input.getAttribute('data-kd-users');
        var target = input.getAttribute('data-kd-users-go');
        var listId = 'kd-suggest-' + (++suggestCount);
        var wrap = document.createElement('div');
        var list = document.createElement('ul');
        var items = [];
        var active = -1;
        var timer = null;
        var request = 0;

        wrap.className = 'kd-suggest';
        input.parentNode.insertBefore(wrap, input);
        wrap.appendChild(input);

        list.className = 'kd-suggest__list';
        list.id = listId;
        list.setAttribute('role', 'listbox');
        list.hidden = true;
        wrap.appendChild(list);

        input.setAttribute('role', 'combobox');
        input.setAttribute('aria-autocomplete', 'list');
        input.setAttribute('aria-expanded', 'false');
        input.setAttribute('aria-controls', listId);
        input.setAttribute('autocomplete', 'off');

        function close() {
            list.hidden = true;
            active = -1;
            input.setAttribute('aria-expanded', 'false');
            input.removeAttribute('aria-activedescendant');
        }

        function choose(user) {
            close();
            input.value = user.username;
            window.location.href = target.replace('__ID__', encodeURIComponent(user.id));
        }

        function highlight(index) {
            var options = list.querySelectorAll('[role="option"]');

            active = index;
            options.forEach(function (option, i) {
                option.setAttribute('aria-selected', i === index ? 'true' : 'false');
            });

            if (options[index]) {
                input.setAttribute('aria-activedescendant', options[index].id);
                options[index].scrollIntoView({block: 'nearest'});
            } else {
                input.removeAttribute('aria-activedescendant');
            }
        }

        function render(users) {
            items = users;
            list.textContent = '';

            if (!users.length) {
                var empty = document.createElement('li');

                empty.className = 'kd-suggest__empty';
                empty.textContent = text('COM_KUNENA_TOOLS_USER_NO_MATCH');
                list.appendChild(empty);
            }

            users.forEach(function (user, i) {
                var option = document.createElement('li');
                var username = document.createElement('strong');
                var name = document.createElement('span');

                option.id = listId + '-' + i;
                option.className = 'kd-suggest__option';
                option.setAttribute('role', 'option');
                option.setAttribute('aria-selected', 'false');
                username.textContent = user.username;
                name.textContent = user.name + ' · #' + user.id;
                option.appendChild(username);
                option.appendChild(name);

                // mousedown: before the field loses the focus and closes the list.
                option.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                    choose(user);
                });

                list.appendChild(option);
            });

            list.hidden = false;
            input.setAttribute('aria-expanded', 'true');
            highlight(-1);
        }

        function lookup() {
            var value = input.value.trim();
            var current = ++request;

            if (value.length < 2) {
                close();

                return;
            }

            fetch(endpoint + '&search=' + encodeURIComponent(value), {credentials: 'same-origin', headers: {Accept: 'application/json'}})
                .then(function (response) {
                    return response.ok ? response.json() : [];
                })
                .then(function (users) {
                    // Only the answer to the last text typed.
                    if (current === request && document.activeElement === input) {
                        render(Array.isArray(users) ? users : []);
                    }
                })
                .catch(close);
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(lookup, 200);
        });

        input.addEventListener('keydown', function (event) {
            if (list.hidden || !items.length) {
                return;
            }

            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                if (event.key === 'ArrowDown') {
                    highlight((active + 1) % items.length);
                } else {
                    highlight(active <= 0 ? items.length - 1 : active - 1);
                }
            } else if (event.key === 'Enter' && active >= 0) {
                // The user chosen, not a search of the text typed.
                event.preventDefault();
                choose(items[active]);
            } else if (event.key === 'Escape') {
                event.preventDefault();
                close();
            }
        });

        input.addEventListener('blur', close);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#kunena input[data-kd-users][data-kd-users-go]').forEach(setupUserField);
    });

    function text(key) {
        return window.Joomla && Joomla.Text ? Joomla.Text._(key) : key;
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('#kunena [data-kd-confirm]');

        if (button && !window.confirm(button.getAttribute('data-kd-confirm') || text('COM_KUNENA_TOOLS_CONFIRM'))) {
            event.preventDefault();
        }
    });

    document.addEventListener('change', function (event) {
        var all = event.target.closest('#kunena [data-kd-all]');

        if (!all) {
            return;
        }

        document.querySelectorAll('#kunena .' + all.getAttribute('data-kd-all')).forEach(function (box) {
            box.checked = all.checked;
        });
    });

    document.addEventListener('click', function (event) {
        var opener = event.target.closest('#kunena [data-kd-frame]');

        if (!opener) {
            return;
        }

        event.preventDefault();

        var dialog = document.getElementById('kd-tool-frame');

        if (!dialog) {
            return;
        }

        dialog.querySelector('iframe').src = opener.getAttribute('data-kd-frame');
        dialog.showModal();
    });

    document.addEventListener('click', function (event) {
        var close = event.target.closest('#kd-tool-frame [data-kd-close]');

        if (close) {
            var dialog = document.getElementById('kd-tool-frame');

            dialog.close();
            dialog.querySelector('iframe').src = 'about:blank';

            // The numbers of the page are those of before the indexing.
            if (close.hasAttribute('data-kd-reload')) {
                window.location.reload();
            }
        }
    });
})();
