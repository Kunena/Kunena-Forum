/**
 * Kunena Component
 * @package     Kunena.Media
 *
 * @copyright   Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

/**
 * Form recovery (the option "Recover the form" of the template): what is written in a form is kept in the storage of the browser
 * while it is written, and put back when the page of the form is opened again (after a crash, a closed tab, a lost connection).
 * A submit or a reset of the form removes it.
 *
 *   KunenaFormRecover.protect(form, {timeout: 5})   keeps the fields of the form, and restores them now
 *   KunenaFormRecover.release(form)                 removes what is kept of the form
 *
 * The keys are the ones of Sisyphus, which Kunena used before: the address of the page, then the id and the name of the form and of
 * the field ("[id=postform][name=postform]"), so a text kept before an update is still restored after it.
 *
 * The editors keep their text in the textarea of the form: CKEditor 5 of Kunena (window.KunenaEditor) at every change, SCEditor
 * when it is asked to (updateOriginal()). Hidden fields are not kept: the server sets them (the token of the form among them).
 */
(function () {
	'use strict';

	const SKIPPED_TYPES = ['submit', 'reset', 'button', 'image', 'file', 'password', 'hidden'];
	const forms = new Map();

	const storage = {
		get(key) {
			try {
				return window.localStorage.getItem(key);
			} catch (error) {
				return null;
			}
		},
		set(key, value) {
			try {
				window.localStorage.setItem(key, value);
			} catch (error) {
				// Full or switched off: the form works without it.
			}
		},
		remove(key) {
			try {
				window.localStorage.removeItem(key);
			} catch (error) {
				// Nothing to remove.
			}
		},
	};

	// "[id=<id>][name=<name>]", with "undefined" for a missing attribute, as Sisyphus wrote it with jQuery.
	const identifier = (element) => `[id=${element.getAttribute('id') ?? 'undefined'}][name=${element.getAttribute('name') ?? 'undefined'}]`;

	// The fields of a form which are kept: the ones the user fills in, with an id or a name.
	const fieldsOf = (form) => [...form.elements].filter((field) => {
		const type = (field.getAttribute('type') || '').toLowerCase();

		return !(field instanceof HTMLButtonElement)
			&& !(field instanceof HTMLFieldSetElement)
			&& !(field instanceof HTMLOutputElement)
			&& !(field instanceof HTMLObjectElement)
			&& !SKIPPED_TYPES.includes(type)
			&& !field.disabled
			&& !field.readOnly
			&& (field.getAttribute('id') !== null || field.getAttribute('name') !== null);
	});

	const keyOf = (state, field) => state.prefix + identifier(field);

	// A name like "polloptions[]" holds several values of the same name.
	const isList = (field) => (field.getAttribute('name') || '').includes('[');

	// SCEditor shows its text in an iframe: it writes it to its textarea when asked.
	const syncEditors = (form) => {
		if (!window.sceditor || typeof window.sceditor.instance !== 'function') {
			return;
		}

		form.querySelectorAll('textarea').forEach((textarea) => {
			const editor = window.sceditor.instance(textarea);

			if (editor && typeof editor.updateOriginal === 'function') {
				editor.updateOriginal();
			}
		});
	};

	const save = (state) => {
		// Sent or reset: nothing is kept any more, until the user writes again.
		if (state.released) {
			return;
		}

		syncEditors(state.form);

		const lists = new Set();

		fieldsOf(state.form).forEach((field) => {
			const key = keyOf(state, field);
			const type = (field.getAttribute('type') || '').toLowerCase();

			if (type === 'checkbox') {
				if (isList(field)) {
					// The checked values of all the boxes of the name, under the key of the first box of the name.
					if (lists.has(field.name)) {
						return;
					}

					lists.add(field.name);
					storage.set(key, [...state.form.querySelectorAll(`input[type="checkbox"][name="${CSS.escape(field.name)}"]:checked`)].map((box) => box.value).join(','));
				} else {
					storage.set(key, String(field.checked));
				}
			} else if (type === 'radio') {
				if (field.checked) {
					storage.set(key, field.value);
				}
			} else if (field instanceof HTMLSelectElement && field.multiple) {
				storage.set(key, [...field.selectedOptions].map((option) => option.value).join(','));
			} else {
				storage.set(key, field.value);
			}
		});
	};

	const restoreField = (field, value) => {
		const type = (field.getAttribute('type') || '').toLowerCase();

		if (type === 'checkbox') {
			field.checked = isList(field) ? value.split(',').includes(field.value) : value !== 'false';
		} else if (type === 'radio') {
			field.checked = field.value === value;
		} else if (field instanceof HTMLSelectElement && field.multiple) {
			const values = value.split(',');

			[...field.options].forEach((option) => {
				option.selected = values.includes(option.value);
			});
		} else {
			field.value = value;

			if (field instanceof HTMLTextAreaElement) {
				restoreEditor(field, value);
			}
		}
	};

	// The editor of a textarea shows the restored text too: CKEditor 5 when it is ready (it may be made after this), SCEditor if it is
	// already there (one made later reads the textarea).
	const restoreEditor = (textarea, value) => {
		if (textarea.id && window.KunenaEditor && typeof window.KunenaEditor.ready === 'function') {
			window.KunenaEditor.ready(textarea.id, (editor) => {
				if (editor && editor.getData() !== value) {
					editor.setData(value);
				}
			});
		}

		const sceditor = window.sceditor && typeof window.sceditor.instance === 'function' ? window.sceditor.instance(textarea) : null;

		if (sceditor && typeof sceditor.val === 'function') {
			sceditor.val(value);
		}
	};

	const restore = (state) => {
		const lists = new Set();

		fieldsOf(state.form).forEach((field) => {
			// A list of boxes is kept under the key of its first box.
			if ((field.getAttribute('type') || '').toLowerCase() === 'checkbox' && isList(field)) {
				if (!lists.has(field.name)) {
					lists.add(field.name);
					state.listKeys.set(field.name, keyOf(state, field));
				}

				const value = storage.get(state.listKeys.get(field.name));

				if (value !== null) {
					restoreField(field, value);
				}

				return;
			}

			const value = storage.get(keyOf(state, field));

			if (value !== null) {
				restoreField(field, value);
			}
		});
	};

	const release = (form) => {
		const state = forms.get(form);

		if (!state) {
			return;
		}

		window.clearTimeout(state.pending);
		state.released = true;
		fieldsOf(form).forEach((field) => storage.remove(keyOf(state, field)));
		state.listKeys.forEach((key) => storage.remove(key));
	};

	const protect = (form, options = {}) => {
		if (!(form instanceof HTMLFormElement) || forms.has(form)) {
			return;
		}

		const state = {
			form,
			prefix: window.location.hostname + window.location.pathname + window.location.search + window.location.hash + identifier(form),
			listKeys: new Map(),
			pending: 0,
			released: false,
		};
		// A text is kept a moment after the last key (not at every key), and at the latest every "timeout" seconds. Writing again
		// after a submit which did not leave the page (a check of the form stopped it) keeps the text again.
		const later = () => {
			state.released = false;
			window.clearTimeout(state.pending);
			state.pending = window.setTimeout(() => save(state), 500);
		};

		forms.set(form, state);
		restore(state);

		form.addEventListener('input', later);
		form.addEventListener('change', later);
		form.addEventListener('submit', () => release(form));
		form.addEventListener('reset', () => release(form));
		// The page is left (a closed tab, another page): what is written now is kept.
		window.addEventListener('pagehide', () => {
			if (forms.has(form)) {
				save(state);
			}
		});

		if (options.timeout > 0) {
			window.setInterval(() => save(state), options.timeout * 1000);
		}

		// CKEditor 5 writes its text to the textarea without an input event of the form.
		if (window.KunenaEditor && typeof window.KunenaEditor.ready === 'function') {
			form.querySelectorAll('textarea[id]').forEach((textarea) => {
				window.KunenaEditor.ready(textarea.id, (editor) => editor && editor.on('change', later));
			});
		}
	};

	window.KunenaFormRecover = { protect, release };
})();
