/**
 * Kunena Component
 *
 * The edit of a topic in the administration: the editor of the forum (window.KunenaEditor, CKEditor 5 with the BBCode of
 * Kunena) on the text of each message. The textarea keeps the BBCode which the form saves.
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

(function () {
    'use strict';

    function start() {
        if (!window.KunenaEditor) {
            return;
        }

        document.querySelectorAll('#kunena textarea.kd-post__text:not([readonly])').forEach(function (textarea) {
            // The editor hides the textarea: the browser cannot point at a required field which is hidden.
            textarea.removeAttribute('required');

            window.KunenaEditor.create(textarea).then(function (editor) {
                // A poll belongs to a topic, not to the text of a message.
                editor.setCommandEnabled('polls', false);
            }).catch(function (error) {
                // The textarea stays: the BBCode can still be edited by hand.
                textarea.setAttribute('required', '');
                window.console && console.error(error);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
