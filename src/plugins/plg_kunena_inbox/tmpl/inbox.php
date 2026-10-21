<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Inbox
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

/*
 * Variables of the plugin: $messages, $notifications, $attachments, $toId, $toName, $token, $ajax, $emailOn.
 */
$strings = [
    'empty'   => Text::_('PLG_KUNENA_INBOX_EMPTY'),
    'loading' => Text::_('PLG_KUNENA_INBOX_LOADING'),
    'from'    => Text::_('PLG_KUNENA_INBOX_FROM'),
    'to'      => Text::_('PLG_KUNENA_INBOX_TO'),
    'error'   => Text::_('PLG_KUNENA_INBOX_ERROR'),
    'block'   => Text::_('PLG_KUNENA_INBOX_BLOCK'),
    'unblock' => Text::_('PLG_KUNENA_INBOX_UNBLOCK'),
    'none'    => Text::_('PLG_KUNENA_INBOX_BLOCKED_NONE'),
    'attach'  => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true" class="align-text-bottom"><path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z"/></svg>',
];
?>
<div class="kinbox" id="kinbox" data-ajax="<?php echo htmlspecialchars($ajax, ENT_QUOTES, 'UTF-8'); ?>"
     data-token="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>"
     data-to="<?php echo htmlspecialchars((string) $toName, ENT_QUOTES, 'UTF-8'); ?>"
     data-attach="<?php echo $attachments ? 1 : 0; ?>"
     data-strings="<?php echo htmlspecialchars(json_encode($strings), ENT_QUOTES, 'UTF-8'); ?>">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-primary active" data-filter="all"><?php echo Text::_('PLG_KUNENA_INBOX_ALL'); ?></button>
            <?php if ($messages) : ?>
                <button type="button" class="btn btn-outline-primary" data-filter="message"><?php echo Text::_('PLG_KUNENA_INBOX_MESSAGES'); ?></button>
            <?php endif; ?>
            <?php if ($notifications) : ?>
                <button type="button" class="btn btn-outline-primary" data-filter="notification"><?php echo Text::_('PLG_KUNENA_INBOX_NOTIFICATIONS_TAB'); ?></button>
            <?php endif; ?>
            <?php if ($messages) : ?>
                <button type="button" class="btn btn-outline-primary" data-filter="sent"><?php echo Text::_('PLG_KUNENA_INBOX_SENT'); ?></button>
            <?php endif; ?>
        </div>
        <?php if ($messages) : ?>
            <button type="button" class="btn btn-primary btn-sm" data-action="compose"><?php echo Text::_('PLG_KUNENA_INBOX_NEW_MESSAGE'); ?></button>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-secondary btn-sm" data-action="markall"><?php echo Text::_('PLG_KUNENA_INBOX_MARK_ALL_READ'); ?></button>
        <button type="button" class="btn btn-outline-danger btn-sm" data-action="delete"><?php echo Text::_('PLG_KUNENA_INBOX_DELETE_SELECTED'); ?></button>
    </div>

    <div class="alert alert-info d-none" data-role="notice"></div>

    <div data-role="compose" class="card card-body mb-3 d-none">
        <div class="mb-2">
            <label class="form-label" for="kinbox-to"><?php echo Text::_('PLG_KUNENA_INBOX_TO'); ?></label>
            <input type="text" class="form-control" id="kinbox-to" maxlength="150" autocomplete="off"/>
        </div>
        <div class="mb-2">
            <label class="form-label" for="kinbox-subject"><?php echo Text::_('PLG_KUNENA_INBOX_SUBJECT'); ?></label>
            <input type="text" class="form-control" id="kinbox-subject" maxlength="150"/>
        </div>
        <div class="mb-2">
            <label class="form-label" for="kinbox-body"><?php echo Text::_('PLG_KUNENA_INBOX_MESSAGE'); ?></label>
            <textarea class="form-control" id="kinbox-body" rows="6"></textarea>
        </div>
        <?php if ($attachments) : ?>
            <div class="mb-2">
                <label class="form-label" for="kinbox-files"><?php echo Text::_('PLG_KUNENA_INBOX_ATTACH'); ?></label>
                <input type="file" class="form-control" id="kinbox-files" multiple/>
            </div>
        <?php endif; ?>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm" data-action="send"><?php echo Text::_('PLG_KUNENA_INBOX_SEND'); ?></button>
            <button type="button" class="btn btn-outline-secondary btn-sm" data-action="cancel"><?php echo Text::_('PLG_KUNENA_INBOX_CANCEL'); ?></button>
        </div>
    </div>

    <div data-role="thread" class="d-none"></div>
    <div data-role="list"></div>
    <div data-role="more" class="mt-2"></div>

    <details class="mt-4" data-role="blockbox">
        <summary><?php echo Text::_('PLG_KUNENA_INBOX_BLOCKED'); ?></summary>
        <p class="small text-muted mt-2"><?php echo Text::_('PLG_KUNENA_INBOX_BLOCKED_HELP'); ?></p>
        <div data-role="blocklist" class="mb-2"></div>
        <div class="input-group input-group-sm" style="max-width: 24rem;">
            <input type="text" class="form-control" id="kinbox-block-name" maxlength="150" placeholder="<?php echo htmlspecialchars(Text::_('PLG_KUNENA_INBOX_TO'), ENT_QUOTES, 'UTF-8'); ?>"/>
            <button type="button" class="btn btn-outline-danger" data-action="blockname"><?php echo Text::_('PLG_KUNENA_INBOX_BLOCK'); ?></button>
        </div>
    </details>

    <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" id="kinbox-email" <?php echo $emailOn ? 'checked' : ''; ?>/>
        <label class="form-check-label" for="kinbox-email"><?php echo Text::_('PLG_KUNENA_INBOX_EMAIL_ALSO'); ?></label>
    </div>
</div>
<script>
(function () {
    var root = document.getElementById('kinbox');

    if (!root || root.dataset.ready) {
        return;
    }

    root.dataset.ready = '1';

    var S = JSON.parse(root.dataset.strings);
    var el = function (role) { return root.querySelector('[data-role="' + role + '"]'); };
    var state = {filter: 'all', start: 0, selected: {}, reply: 0};

    // The text is also put in attributes (the link of a message, data-url): the quotes are escaped too, or a quote in the text ends the
    // attribute and what follows is an attribute of the attacker.
    function esc(text) {
        return String(text == null ? '' : text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function html(text) {
        // The attribute is written in two parts: the SEF plugin of Joomla rewrites every href="..." in the page, also in a string of this
        // script, and the address would get the path of the site in front of it.
        return esc(text).replace(/(https?:\/\/[^\s<]+)/g, function (url) {
            return '<a rel="nofollow noopener" target="_blank" href' + '="' + url + '">' + url + '</a>';
        }).replace(/\n/g, '<br>');
    }

    function call(task, data, post) {
        var params = data instanceof FormData ? data : new URLSearchParams(data || {});
        params.set('task', task);
        var url = root.dataset.ajax;
        var options = {credentials: 'same-origin', headers: {'Accept': 'application/json'}};

        if (post) {
            params.set(root.dataset.token, '1');
            options.method = 'POST';
            options.body = params;
        } else {
            url += '&' + params.toString();
        }

        return fetch(url, options).then(function (response) { return response.json(); }).then(function (json) {
            if (!json.success) {
                throw new Error(json.message || S.error);
            }

            // com_ajax gives the result of every plugin which answers: the one of this plugin is the first.
            return Array.isArray(json.data) ? json.data[0] : json.data;
        });
    }

    function notice(text, type) {
        var box = el('notice');
        box.className = 'alert alert-' + (type || 'info');
        box.textContent = text;

        if (!text) {
            box.classList.add('d-none');
        }
    }

    function badge(unread) {
        var tab = document.querySelector('[data-bs-target="#inbox"], a[href="#inbox"], button[aria-controls="inbox"]');

        if (tab) {
            tab.textContent = tab.textContent.replace(/\s*\(\d+\)\s*$/, '') + (unread ? ' (' + unread + ')' : '');
        }
    }

    function renderBlocked(list) {
        el('blocklist').innerHTML = list.length ? list.map(function (user) {
            return '<div class="d-flex align-items-center gap-2 mb-1">' + esc(user.name)
                + ' <button type="button" class="btn btn-outline-secondary btn-sm py-0" data-unblock="' + user.id + '">' + esc(S.unblock) + '</button></div>';
        }).join('') : '<div class="text-muted small">' + esc(S.none) + '</div>';
    }

    function blockCall(task, data) {
        return call(task, data, true).then(function (result) {
            renderBlocked(result.blocked);
            notice('');
        }).catch(function (error) { notice(error.message, 'danger'); });
    }

    function row(item) {
        var title = item.type === 'message' ? '<strong>' + esc(item.subject) + '</strong>' : esc(item.text);
        var who = item.type === 'message' ? (state.filter === 'sent' ? S.to + ': ' + esc(item.to) : S.from + ': ' + esc(item.from)) : '';
        var sub = item.type === 'message' ? '<div class="small text-muted">' + esc(item.text) + '</div>' : '';
        var clip = item.files && item.files.length ? ' <span title="' + item.files.length + '">' + S.attach + ' ' + item.files.length + '</span>' : '';

        return '<div class="list-group-item d-flex gap-2 align-items-start' + (item.read ? '' : ' list-group-item-primary') + '" data-id="' + item.id + '">'
            + '<input class="form-check-input mt-1" type="checkbox" data-select="' + item.id + '">'
            + '<a href="#" class="flex-grow-1 text-decoration-none" data-open="' + item.id + '" data-url="' + esc(item.url) + '" data-type="' + item.type + '">'
            + title + clip + (who ? '<div class="small">' + who + '</div>' : '') + sub + '</a>'
            + '<span class="small text-muted text-nowrap">' + esc(item.time) + '</span></div>';
    }

    function load(append) {
        if (!append) {
            state.start = 0;
            state.selected = {};
            el('list').innerHTML = '<div class="text-muted">' + esc(S.loading) + '</div>';
        }

        call('list', {filter: state.filter, start: state.start}).then(function (data) {
            var list = el('list');
            var rows = data.items.map(row).join('');

            if (!append) {
                list.innerHTML = rows ? '<div class="list-group">' + rows + '</div>' : '<div class="text-muted">' + esc(S.empty) + '</div>';
            } else {
                list.querySelector('.list-group').insertAdjacentHTML('beforeend', rows);
            }

            state.start += data.items.length;
            el('more').innerHTML = state.start < data.total ? '<button type="button" class="btn btn-outline-secondary btn-sm" data-action="more">+</button>' : '';
            badge(data.unread);
        }).catch(function (error) { notice(error.message, 'danger'); });
    }

    function showList() {
        el('thread').classList.add('d-none');
        el('list').classList.remove('d-none');
        el('more').classList.remove('d-none');
        load(false);
    }

    function openItem(id, type, url) {
        call('view', {id: id}).then(function (data) {
            badge(data.unread);

            if (type !== 'message') {
                if (url) {
                    window.location.href = url;
                } else {
                    load(false);
                }

                return;
            }

            var box = el('thread');
            var last = data.items[data.items.length - 1];
            var other = data.items.filter(function (m) { return !m.mine; })[0];
            var otherId = other ? other.from_id : data.items[0].to_id;
            var otherName = other ? other.from : data.items[0].to;
            state.reply = last.id;
            box.innerHTML = '<button type="button" class="btn btn-link btn-sm px-0" data-action="back">&larr;</button>'
                + '<button type="button" class="btn btn-outline-danger btn-sm float-end" data-action="blockuser" data-uid="' + otherId + '">' + esc(S.block) + ' ' + esc(otherName) + '</button>'
                + '<h4>' + esc(data.items[0].subject) + '</h4>'
                + data.items.map(function (m) {
                    return '<div class="card mb-2' + (m.mine ? ' border-primary' : '') + '"><div class="card-body">'
                        + '<div class="small text-muted mb-1">' + esc(m.from) + ' &rarr; ' + esc(m.to) + ' &middot; ' + esc(m.time) + '</div>'
                        + '<div>' + html(m.text) + '</div>'
                        + (m.files && m.files.length ? '<div class="mt-2 small">' + m.files.map(function (f) { return '<a href="' + esc(f.url) + '" class="me-3">' + S.attach + ' ' + esc(f.name) + ' (' + esc(f.size) + ')</a>'; }).join('') + '</div>' : '')
                        + '</div></div>';
                }).join('')
                + '<div class="mt-2"><textarea class="form-control mb-2" rows="4" data-role="replybody"></textarea>'
                + (root.dataset.attach === '1' ? '<input type="file" class="form-control mb-2" multiple data-role="replyfiles">' : '')
                + '<button type="button" class="btn btn-primary btn-sm" data-action="reply">&crarr;</button></div>';
            el('list').classList.add('d-none');
            el('more').classList.add('d-none');
            box.classList.remove('d-none');
        }).catch(function (error) { notice(error.message, 'danger'); });
    }

    function send(data, fileInput) {
        var form = new FormData();

        Object.keys(data).forEach(function (key) { form.append(key, data[key]); });

        if (fileInput && fileInput.files) {
            Array.prototype.forEach.call(fileInput.files, function (file) { form.append('files[]', file); });
        }

        return call('send', form, true).then(function (result) {
            notice(result.message, 'success');
            el('compose').classList.add('d-none');
            ['kinbox-subject', 'kinbox-body', 'kinbox-files'].forEach(function (id) { var input = document.getElementById(id); if (input) { input.value = ''; } });
            return true;
        }).catch(function (error) { notice(error.message, 'danger'); return false; });
    }

    root.addEventListener('click', function (event) {
        var target = event.target.closest('[data-filter],[data-action],[data-open],[data-unblock]');

        if (!target) {
            return;
        }

        event.preventDefault();

        if (target.dataset.filter) {
            root.querySelectorAll('[data-filter]').forEach(function (button) { button.classList.toggle('active', button === target); });
            state.filter = target.dataset.filter;
            notice('');
            showList();
        } else if (target.dataset.unblock) {
            blockCall('unblock', {id: target.dataset.unblock});
        } else if (target.dataset.open) {
            openItem(target.dataset.open, target.dataset.type, target.dataset.url);
        } else {
            switch (target.dataset.action) {
                case 'compose':
                    el('compose').classList.remove('d-none');
                    document.getElementById('kinbox-' + (document.getElementById('kinbox-to').value ? 'subject' : 'to')).focus();
                    break;
                case 'cancel':
                    el('compose').classList.add('d-none');
                    break;
                case 'send':
                    send({to: document.getElementById('kinbox-to').value, subject: document.getElementById('kinbox-subject').value, body: document.getElementById('kinbox-body').value}, document.getElementById('kinbox-files')).then(function (ok) {
                        if (ok) {
                            state.filter = 'sent';
                            root.querySelectorAll('[data-filter]').forEach(function (button) { button.classList.toggle('active', button.dataset.filter === 'sent'); });
                            showList();
                        }
                    });
                    break;
                case 'reply':
                    send({reply: state.reply, body: root.querySelector('[data-role="replybody"]').value, subject: ''}, root.querySelector('[data-role="replyfiles"]')).then(function (ok) {
                        if (ok) {
                            openItem(state.reply, 'message', '');
                        }
                    });
                    break;
                case 'back':
                    showList();
                    break;
                case 'blockuser':
                    blockCall('block', {id: target.dataset.uid}).then(function () { root.querySelector('[data-role="blockbox"]').open = true; });
                    break;
                case 'blockname':
                    blockCall('block', {to: document.getElementById('kinbox-block-name').value}).then(function () { document.getElementById('kinbox-block-name').value = ''; });
                    break;
                case 'more':
                    load(true);
                    break;
                case 'markall':
                    call('read', {}, true).then(function (data) { badge(data.unread); load(false); }).catch(function (error) { notice(error.message, 'danger'); });
                    break;
                case 'delete':
                    var ids = Object.keys(state.selected).filter(function (id) { return state.selected[id]; });

                    if (ids.length) {
                        var params = {};
                        ids.forEach(function (id, index) { params['ids[' + index + ']'] = id; });
                        call('delete', params, true).then(function (data) { badge(data.unread); load(false); }).catch(function (error) { notice(error.message, 'danger'); });
                    }

                    break;
            }
        }
    });

    root.addEventListener('change', function (event) {
        if (event.target.dataset.select) {
            state.selected[event.target.dataset.select] = event.target.checked;
        } else if (event.target.id === 'kinbox-email') {
            call('setmail', {value: event.target.checked ? 1 : 0}, true).catch(function (error) { notice(error.message, 'danger'); });
        }
    });

    root.querySelector('[data-role="blockbox"]').addEventListener('toggle', function () {
        if (this.open) {
            call('blocked').then(function (result) { renderBlocked(result.blocked); }).catch(function (error) { notice(error.message, 'danger'); });
        }
    });

    if (root.dataset.to) {
        document.getElementById('kinbox-to').value = root.dataset.to;
        el('compose').classList.remove('d-none');
    }

    load(false);
}());
</script>
