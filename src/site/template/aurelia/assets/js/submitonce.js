/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

// Ignore every submit of a form with data-kunena-submit-once after the first one, until the page is left.
(function () {
	if (window.kunenaSubmitOnce) {
		return;
	}

	window.kunenaSubmitOnce = true;

	const locked = new Map();

	function lock(form) {
		const changes = [];

		form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (button) {
			const change = {button: button, addedClass: false, addedAria: false};

			if (!button.classList.contains('disabled')) {
				button.classList.add('disabled');
				change.addedClass = true;
			}

			if (button.getAttribute('aria-disabled') !== 'true') {
				change.previousAria = button.getAttribute('aria-disabled');
				button.setAttribute('aria-disabled', 'true');
				change.addedAria = true;
			}

			changes.push(change);
		});

		const previousBusy = form.getAttribute('aria-busy');

		if (previousBusy !== 'true') {
			form.setAttribute('aria-busy', 'true');
		}

		locked.set(form, {changes: changes, previousBusy: previousBusy});
	}

	function restoreAttribute(element, name, value) {
		if (value === null) {
			element.removeAttribute(name);
		} else {
			element.setAttribute(name, value);
		}
	}

	function release(form) {
		const state = locked.get(form);

		if (!state) {
			return;
		}

		state.changes.forEach(function (change) {
			if (change.addedClass) {
				change.button.classList.remove('disabled');
			}

			if (change.addedAria) {
				restoreAttribute(change.button, 'aria-disabled', change.previousAria);
			}
		});

		if (state.previousBusy !== 'true') {
			restoreAttribute(form, 'aria-busy', state.previousBusy);
		}

		locked.delete(form);
	}

	// Capture phase, so that a handler which stops the propagation cannot bypass the guard.
	document.addEventListener('submit', function (event) {
		const form = event.target;

		if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-kunena-submit-once')) {
			return;
		}

		if (locked.has(form)) {
			event.preventDefault();

			return;
		}

		lock(form);

		// Another script may cancel this submit after us: then nothing was sent, so unlock the form.
		setTimeout(function () {
			if (event.defaultPrevented) {
				release(form);
			}
		}, 0);
	}, true);

	window.addEventListener('pageshow', function (event) {
		if (event.persisted) {
			Array.from(locked.keys()).forEach(release);
		}
	});
})();
