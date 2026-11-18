/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 *
 * The dialogs of the forum: native <dialog> elements (they were the modals of Bootstrap, which needed its script).
 *
 * - A modal (a confirmation, the forms to report, warn or ban): opened by <button type="button" command="show-modal"
 *   commandfor="id">, closed by <button type="button" command="close" commandfor="id"> or by a submit button with
 *   formmethod="dialog" in its form. The browser makes the page inert, keeps the focus in the dialog, closes it with Esc and
 *   gives the focus back to the button which opened it. closedby="any": a click on the backdrop closes it too.
 * - A panel in the page (the quick reply): opened and closed by <button type="button" data-kunena-dialog-toggle="id"
 *   aria-controls="id" aria-expanded="false">, with show(): the page stays usable, as the quick reply was. A button with
 *   data-kunena-dialog-close="id" closes it and still does what it does (a reset button empties the form).
 *
 * Invoker commands (command, commandfor) are native since Chrome 135, Firefox 144 and Safari 26.2; closedby since Chrome 134 and
 * Firefox 141. Where they are missing, this script does them: the same markup works everywhere.
 */
(() => {
	'use strict';

	const nativeCommands = 'commandForElement' in HTMLButtonElement.prototype;
	const nativeClosedBy = 'closedBy' in HTMLDialogElement.prototype;

	const dialogOf = (id) => {
		const element = id ? document.getElementById(id) : null;

		return element instanceof HTMLDialogElement ? element : null;
	};

	// The buttons which open or close a panel tell whether it is open (a modal makes the page inert: its button needs no state).
	const syncToggles = (dialog) => {
		document.querySelectorAll('[data-kunena-dialog-toggle]').forEach((toggle) => {
			if (toggle.dataset.kunenaDialogToggle === dialog.id) {
				toggle.setAttribute('aria-expanded', dialog.open ? 'true' : 'false');
			}
		});
	};

	// Outside the box of the dialog: on its backdrop (the dialog is also the target of a click on its own padding).
	const outside = (event, dialog) => {
		const box = dialog.getBoundingClientRect();

		return event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom;
	};

	document.addEventListener('click', (event) => {
		// A button which closes a panel and still does its own work (the reset of the quick reply empties its form).
		const closer = event.target.closest('[data-kunena-dialog-close]');

		if (closer) {
			const dialog = dialogOf(closer.dataset.kunenaDialogClose);

			if (dialog?.open) {
				dialog.close();
			}

			return;
		}

		const toggle = event.target.closest('[data-kunena-dialog-toggle]');

		if (toggle) {
			const dialog = dialogOf(toggle.dataset.kunenaDialogToggle);

			if (dialog) {
				event.preventDefault();

				if (dialog.open) {
					dialog.close();
				} else {
					dialog.show();
					// The first field of the panel, as the quick reply had it with Bootstrap.
					dialog.querySelector('[autofocus], textarea, input:not([type="hidden"])')?.focus();
				}

				syncToggles(dialog);
			}

			return;
		}

		if (nativeCommands) {
			return;
		}

		const invoker = event.target.closest('button[commandfor][command]');

		// A command button of a form needs type="button": a submit button sends the form, a button without type does nothing.
		if (!invoker || invoker.disabled || (invoker.form && invoker.getAttribute('type') !== 'button')) {
			return;
		}

		const dialog = dialogOf(invoker.getAttribute('commandfor'));

		if (!dialog) {
			return;
		}

		switch (invoker.getAttribute('command')) {
			case 'show-modal':
				if (!dialog.open) {
					dialog.showModal();
				}
				break;
			case 'close':
				if (dialog.open) {
					dialog.close(invoker.value);
				}
				break;
			case 'request-close':
				if (dialog.open) {
					typeof dialog.requestClose === 'function' ? dialog.requestClose(invoker.value) : dialog.close(invoker.value);
				}
				break;
		}
	});

	// "close" does not bubble: it is caught on its way down. A panel closed by its close button (or by Esc) updates its buttons.
	document.addEventListener('close', (event) => {
		if (event.target instanceof HTMLDialogElement) {
			syncToggles(event.target);
		}
	}, true);

	// A click on the backdrop closes a modal with closedby="any". The press and the release are both outside: a selection of text
	// which ends on the backdrop does not close it.
	if (!nativeClosedBy) {
		let pressed = null;

		document.addEventListener('pointerdown', (event) => {
			const dialog = event.target instanceof Element ? event.target.closest('dialog[closedby="any"]') : null;

			pressed = dialog && dialog.open && dialog.matches(':modal') && outside(event, dialog) ? dialog : null;
		});

		document.addEventListener('pointerup', (event) => {
			if (pressed && pressed.open && outside(event, pressed)) {
				pressed.close();
			}

			pressed = null;
		});
	}
})();
