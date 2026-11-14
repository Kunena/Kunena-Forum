/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

// Ask before leaving a form with data-kunena-leave-warning whose subject or message text was changed and not sent.
(function () {
	if (window.kunenaLeaveWarning) {
		return;
	}

	window.kunenaLeaveWarning = true;

	const EDITORS = ['message', 'message_private'];
	const guards = new Map();

	function editorType() {
		return window.Joomla && Joomla.getOptions ? Joomla.getOptions('com_kunena.editor_type') : null;
	}

	// The text of an editor through its own API, or undefined when the editor is not there (yet).
	function editorValue(id) {
		const type = editorType();

		if (type === 'sceditor') {
			const textarea = document.getElementById(id);
			const instance = textarea && window.sceditor ? sceditor.instance(textarea) : null;

			return instance ? instance.val() : undefined;
		}

		if (type === 'ckeditor' && window.CKEDITOR) {
			const instance = CKEDITOR.instances[id];

			return instance && instance.status === 'ready' ? instance.getData() : undefined;
		}

		return undefined;
	}

	function subjectField(form) {
		const subject = form.querySelector('input[type="text"][name="subject"]');

		return subject && !subject.disabled ? subject : null;
	}

	function isChanged(form) {
		const guard = guards.get(form);

		if (form.hasAttribute('data-kunena-unsent')) {
			return true;
		}

		const subject = subjectField(form);

		if (subject && guard.subject !== undefined && subject.value !== guard.subject) {
			return true;
		}

		return EDITORS.some(function (id) {
			if (guard.editors[id] === undefined) {
				return false;
			}

			const value = editorValue(id);

			return value !== undefined && value !== guard.editors[id];
		});
	}

	// The editors normalise the loaded text: the baseline is their own value once they are ready, taken only once.
	function takeEditorBaseline(guard, id) {
		if (guard.editors[id] !== undefined) {
			return;
		}

		const value = editorValue(id);

		if (value !== undefined) {
			guard.editors[id] = value;
		}
	}

	function takeBaselines(guard, form) {
		const subject = subjectField(form);

		if (subject) {
			guard.subject = subject.value;
		}

		EDITORS.forEach(function (id) {
			takeEditorBaseline(guard, id);
		});
	}

	function init() {
		document.querySelectorAll('form[data-kunena-leave-warning]').forEach(function (form) {
			guards.set(form, {armed: true, sent: false, subject: undefined, editors: {}});

			const cancel = form.querySelector('#form_cancel_button');

			if (cancel) {
				cancel.addEventListener('click', function (event) {
					event.preventDefault();

					if (isChanged(form)) {
						if (!window.confirm(Joomla.Text._('COM_KUNENA_POST_CANCEL_CONFIRM'))) {
							return;
						}

						guards.get(form).armed = false;
					}

					// The files uploaded in this form (read before the reset): the server removes only the pending uploads of the user.
					const ids = Array.prototype.map.call(form.querySelectorAll('#kattach-list input[name^="attachments["]'), function (input) {
						return input.name.slice('attachments['.length, -1);
					});
					const url = ids.length && window.Joomla && Joomla.getOptions ? Joomla.getOptions('com_kunena.kunena_upload_files_cancel') : null;

					const leave = function () {
						form.reset();
						window.history.back();
					};

					if (!url) {
						leave();

						return;
					}

					const body = new URLSearchParams();

					ids.forEach(function (id) {
						body.append('ids[]', id);
					});

					// Back also when the request fails: a failed removal leaves the uploads to the scheduled clean-up.
					fetch(url, {method: 'POST', keepalive: true, credentials: 'same-origin', body: body}).then(leave, leave);
				});
			}
		});

		// A CKEditor is ready later, after edit.js has created it and put a copied text into it.
		if (editorType() === 'ckeditor' && window.CKEDITOR) {
			CKEDITOR.on('instanceReady', function (event) {
				if (EDITORS.indexOf(event.editor.name) !== -1) {
					guards.forEach(function (guard) {
						takeEditorBaseline(guard, event.editor.name);
					});
				}
			});
		}

		// After the ready handlers of jQuery, which create the editors and put a copied text into them.
		setTimeout(function () {
			guards.forEach(takeBaselines);
		}, 0);
	}

	// Capture phase, so that a handler which stops the propagation cannot hide a submit from the guard.
	document.addEventListener('submit', function (event) {
		const form = event.target;
		const guard = guards.get(form);

		if (!guard) {
			return;
		}

		guard.armed = false;

		// Another script may cancel this submit after us: then nothing was sent, so arm the guard again.
		setTimeout(function () {
			if (!event.defaultPrevented) {
				guard.sent = true;
			} else if (!guard.sent) {
				guard.armed = true;
			}
		}, 0);
	}, true);

	window.addEventListener('beforeunload', function (event) {
		guards.forEach(function (guard, form) {
			if (guard.armed && isChanged(form)) {
				event.preventDefault();
				event.returnValue = '';
			}
		});
	});

	window.addEventListener('pageshow', function (event) {
		if (event.persisted) {
			guards.forEach(function (guard) {
				guard.armed = true;
				guard.sent = false;
			});
		}
	});

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
