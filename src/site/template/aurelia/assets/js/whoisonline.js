/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

/**
 * Filter the online users of the "Members" box by role: the legend buttons show only the users of that role.
 */
document.addEventListener('DOMContentLoaded', function () {
    const box = document.getElementById('kwho');

    if (!box) {
        return;
    }

    const buttons = box.querySelectorAll('button.kwho-filter');
    const members = box.querySelectorAll('.kwho-member');
    const empty = box.querySelector('.kwho-empty');

    if (!buttons.length) {
        return;
    }

    const apply = function (roles) {
        let shown = 0;

        members.forEach(function (member) {
            const visible = !roles.length || roles.indexOf(member.dataset.kwhoRole) !== -1;

            member.hidden = !visible;

            if (visible) {
                shown++;
            }
        });

        // A comma follows a user only when another visible user comes after it in the same list.
        box.querySelectorAll('.kwho-comma').forEach(function (comma) {
            comma.hidden = true;
        });

        box.querySelectorAll('div').forEach(function (list) {
            const visible = Array.prototype.filter.call(list.querySelectorAll(':scope > .kwho-member'), function (member) {
                return !member.hidden;
            });

            visible.slice(0, -1).forEach(function (member) {
                const comma = member.nextElementSibling;

                if (comma && comma.classList.contains('kwho-comma')) {
                    comma.hidden = false;
                }
            });
        });

        if (empty) {
            empty.hidden = shown > 0;
        }
    };

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            const wasActive = button.classList.contains('active');
            const all = box.querySelector('button.kwho-filter[data-kwho-roles=""]');
            const roles = (button.dataset.kwhoRoles || '').split(' ').filter(Boolean);

            // Selecting the active role again goes back to all users.
            const selected = wasActive || !roles.length ? all : button;

            buttons.forEach(function (other) {
                const on = other === selected;

                other.classList.toggle('active', on);
                other.classList.toggle('bg-primary-subtle', on);
                other.classList.toggle('border-primary-subtle', on);
                other.setAttribute('aria-pressed', on ? 'true' : 'false');
            });

            apply((selected.dataset.kwhoRoles || '').split(' ').filter(Boolean));
        });
    });
});
