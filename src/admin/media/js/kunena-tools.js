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
 * - a button with data-kd-frame="url" opens the page in a dialog (the indexer of Smart Search).
 */
(function () {
    'use strict';

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
