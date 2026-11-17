/**
 * Kunena Component
 *
 * The dialogs of the buttons of Kunena in CKEditor 5 (Bootstrap 5 modals, as the rest of the forum): the poll of the topic, a map,
 * a video and the emoticons. The map, the video and the emoticons write their tag (or code) as text; the poll fills the hidden
 * fields of the form (#poll_options), as the dialog of CKEditor 4 did.
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 */

import { ButtonView, Plugin } from 'ckeditor5';
import { ICONS } from './icons.js';

const text = (key, fallback) => {
	const value = window.Joomla && window.Joomla.Text ? window.Joomla.Text._(key, fallback) : fallback;

	return value === key ? fallback : value;
};

function element(tag, attributes = {}, children = []) {
	const node = document.createElement(tag);

	for (const [name, value] of Object.entries(attributes)) {
		if (name === 'text') {
			node.textContent = value;
		} else if (value !== undefined && value !== null && value !== false) {
			node.setAttribute(name, value === true ? '' : value);
		}
	}

	node.append(...children);

	return node;
}

/**
 * A Bootstrap modal with a body and the buttons OK and Cancel; onOk() returns false to keep it open.
 */
function openModal({ title, body, onOk, onCancel, okLabel }) {
	const ok = element('button', { type: 'button', class: 'btn btn-primary', text: okLabel ?? text('JAPPLY', 'OK') });
	const cancel = element('button', { type: 'button', class: 'btn btn-secondary', 'data-bs-dismiss': 'modal', text: text('JCANCEL', 'Cancel') });
	const titleId = 'kunena-editor-dialog-title';
	const modal = element('div', { class: 'modal fade kunena-editor-dialog', tabindex: '-1', 'aria-labelledby': titleId, 'aria-hidden': 'true' }, [
		element('div', { class: 'modal-dialog modal-dialog-scrollable' }, [
			element('div', { class: 'modal-content' }, [
				element('div', { class: 'modal-header' }, [
					element('h3', { class: 'modal-title fs-5', id: titleId, text: title }),
					element('button', { type: 'button', class: 'btn-close', 'data-bs-dismiss': 'modal', 'aria-label': text('JCLOSE', 'Close') }),
				]),
				element('div', { class: 'modal-body' }, [body]),
				element('div', { class: 'modal-footer' }, onOk ? [cancel, ok] : [cancel]),
			]),
		]),
	]);

	// Inside #kunena: the colours and the styles of the forum.
	(document.getElementById('kunena') ?? document.body).append(modal);

	const instance = window.bootstrap.Modal.getOrCreateInstance(modal);
	let accepted = false;

	ok.addEventListener('click', () => {
		if (onOk && onOk() !== false) {
			accepted = true;
			instance.hide();
		}
	});
	modal.addEventListener('hidden.bs.modal', () => {
		if (!accepted && onCancel) {
			onCancel();
		}

		instance.dispose();
		modal.remove();
	});
	instance.show();

	return modal;
}

function field(label, input) {
	const id = 'kunena-dialog-' + Math.random().toString(36).slice(2);

	input.id = id;
	input.classList.add(input.tagName === 'SELECT' ? 'form-select' : 'form-control');

	return element('div', { class: 'mb-3' }, [element('label', { class: 'form-label', for: id, text: label }), input]);
}

/* The poll ------------------------------------------------------------------------------------------------------------------- */

/**
 * The poll of the topic: the title, the options and the date until which it can be voted. The values are hidden fields of the form
 * (#poll_options: poll_title, poll_time_to_live, polloptionsID[id] for an option which exists, polloptionsID[newN] for a new one),
 * read when the message is saved.
 */
export function openPollDialog() {
	// The value of the first field which exists (undefined when none does).
	const valueOf = (selector) => document.querySelector(selector)?.value;
	const maximum = parseInt(valueOf('#nb_options_allowed'), 10) || 0;
	const container = document.getElementById('poll_options');
	const list = element('div', { class: 'kunena-poll-options' });
	const title = element('input', { type: 'text', maxlength: '100' });
	const lifespan = element('input', { type: 'date' });
	const add = element('button', { type: 'button', class: 'btn btn-outline-primary btn-sm', text: text('COM_KUNENA_POLL_ADD_POLL_OPTION', 'Add an option') });
	const remove = element('button', { type: 'button', class: 'btn btn-outline-danger btn-sm', text: text('COM_KUNENA_POLL_REMOVE_POLL_OPTION', 'Remove the checked options') });
	let created = 0;

	const renumber = () => {
		[...list.children].forEach((row, index) => {
			row.querySelector('label').textContent = `${text('COM_KUNENA_POLL_OPTION_NAME', 'Option')} ${index + 1}`;
		});
		add.disabled = maximum > 0 && list.children.length >= maximum;
	};

	// An option: an existing one keeps its id, a new one is numbered (polloptionsID[new3]).
	const addOption = (value = '', id = null) => {
		const key = id ?? `new${++created}`;
		const input = element('input', { type: 'text', class: 'form-control kunena-poll-option', maxlength: '100', 'data-option': key, value });
		const check = element('input', { type: 'checkbox', class: 'form-check-input mt-2', 'aria-label': text('COM_KUNENA_POLL_REMOVE_POLL_OPTION', 'Remove') });
		const row = element('div', { class: 'd-flex gap-2 align-items-start mb-2' }, [check, element('div', { class: 'flex-grow-1' }, [element('label', { class: 'form-label small mb-1' }), input])]);

		list.append(row);
		renumber();
	};

	add.addEventListener('click', () => {
		// A poll has two options at least.
		do {
			addOption();
		} while (list.children.length < 2 && (!maximum || list.children.length < maximum));
	});
	remove.addEventListener('click', () => {
		[...list.children].filter((row) => row.querySelector('input[type="checkbox"]').checked).forEach((row) => row.remove());
		renumber();
	});

	// The poll which is edited, or the values of an earlier use of this dialog.
	title.value = valueOf('#poll_options input[name="poll_title"]') ?? valueOf('#ckeditor_dialog_polltitle') ?? '';
	lifespan.value = String(valueOf('#poll_options input[name="poll_time_to_live"]') ?? valueOf('#ckeditor_dialog_polltimetolive') ?? '').slice(0, 10);

	const kept = document.querySelectorAll('#poll_options input[name^="polloptionsID"]');

	if (kept.length) {
		kept.forEach((input) => {
			const match = /\[(new)?(\d+)\]$/.exec(input.name);

			if (match) {
				match[1] ? (created = Math.max(created, +match[2]), addOption(input.value, `new${match[2]}`)) : addOption(input.value, match[2]);
			}
		});
	} else {
		document.querySelectorAll('.ckeditor_dialog_polloption').forEach((input) => {
			const id = (/\d+$/.exec(input.name) ?? [])[0];

			if (id) {
				addOption(input.value, id);
			}
		});
	}

	const body = element('div', {}, [
		field(text('COM_KUNENA_POLL_TITLE', 'Title of the poll'), title),
		element('div', { class: 'd-flex gap-2 mb-3' }, [add, remove]),
		list,
		field(text('COM_KUNENA_POLL_TIME_TO_LIVE', 'Vote until'), lifespan),
	]);

	renumber();

	openModal({
		title: text('COM_KUNENA_EDITOR_DIALOG_POLLS_PROPERTIES', 'Poll'),
		body,
		onOk: () => {
			container.replaceChildren(
				element('input', { type: 'hidden', name: 'poll_title', value: title.value }),
				element('input', { type: 'hidden', name: 'poll_time_to_live', value: lifespan.value }),
				...[...list.querySelectorAll('.kunena-poll-option')].map((input) => element('input', { type: 'hidden', name: `polloptionsID[${input.dataset.option}]`, value: input.value })),
			);
		},
	});
}

/* The map, the video, the emoticons ------------------------------------------------------------------------------------------ */

function openMapDialog(insert) {
	const type = element('select', {}, ['HYBRID', 'ROADMAP', 'TERRAIN', 'SATELITE'].map((value) => element('option', { value, text: value })));
	const zoom = element('select', {}, ['2', '4', '6', '8', '10', '12', '14', '16', '18'].map((value) => element('option', { value, text: value })));
	const place = element('input', { type: 'text' });

	zoom.value = '10';

	openModal({
		title: text('COM_KUNENA_EDITOR_MAP', 'Map'),
		body: element('div', {}, [
			field(text('COM_KUNENA_EDITOR_MAP_TYPE', 'Type of the map'), type),
			field(text('COM_KUNENA_EDITOR_MAP_ZOOM', 'Zoom level'), zoom),
			field(text('COM_KUNENA_EDITOR_MAP_PLACE', 'Address or place'), place),
		]),
		onOk: () => {
			if (!place.value.trim()) {
				place.focus();

				return false;
			}

			insert(`[map type=${type.value} mode=${zoom.value}]${place.value.trim()}[/map]`);
		},
	});
}

const VIDEO_PROVIDERS = ['Bofunk', 'Break', 'Clipfish', 'DivX', 'Flash', 'FlashVars', 'MediaPlayer', 'Metacafe', 'MySpace', 'QuickTime', 'RealPlayer', 'RuTube', 'Sapo', 'Streetfire', 'Veoh', 'Videojug', 'Vimeo', 'Wideo.fr', 'YouTube'];

function openVideoDialog(insert) {
	const url = element('input', { type: 'url', placeholder: 'https://' });
	const provider = element('select', {}, [element('option', { value: '', text: '' }), ...VIDEO_PROVIDERS.map((value) => element('option', { value, text: value }))]);
	const id = element('input', { type: 'text' });
	const size = element('input', { type: 'number', min: '1' });
	const width = element('input', { type: 'number', min: '1' });
	const height = element('input', { type: 'number', min: '1' });

	openModal({
		title: text('COM_KUNENA_EDITOR_VIDEO', 'Video'),
		body: element('div', {}, [
			field(text('COM_KUNENA_EDITOR_VIDEO_URL', 'Address of the video'), url),
			element('p', { class: 'small text-body-secondary', text: text('COM_KUNENA_EDITOR_VIDEO_OR_PROVIDER', 'Or a video of a provider by its id:') }),
			field(text('COM_KUNENA_EDITOR_VIDEO_PROVIDER', 'Provider'), provider),
			field(text('COM_KUNENA_EDITOR_VIDEO_ID', 'Id of the video'), id),
			field(text('COM_KUNENA_EDITOR_VIDEO_SIZE', 'Size'), size),
			element('div', { class: 'row' }, [
				element('div', { class: 'col' }, [field(text('COM_KUNENA_EDITOR_VIDEO_WIDTH', 'Width'), width)]),
				element('div', { class: 'col' }, [field(text('COM_KUNENA_EDITOR_VIDEO_HEIGHT', 'Height'), height)]),
			]),
		]),
		onOk: () => {
			if (url.value.trim()) {
				insert(`[video]${url.value.trim()}[/video]`);

				return;
			}

			if (!provider.value || !id.value.trim()) {
				url.focus();

				return false;
			}

			const params = [`type=${provider.value}`];

			if (size.value) {
				params.push(`size=${size.value}`);
			}

			if (width.value && height.value) {
				params.push(`width=${width.value}`, `height=${height.value}`);
			}

			insert(`[video ${params.join(' ')}]${id.value.trim()}[/video]`);
		},
	});
}

function openEmoticonsDialog(insert, emoticons, path) {
	const grid = element('div', { class: 'd-flex flex-wrap gap-1 kunena-emoticons' });
	let modal = null;

	for (const [code, file] of Object.entries(emoticons)) {
		const button = element('button', { type: 'button', class: 'btn btn-light p-1', title: code, 'aria-label': code }, [
			element('img', { src: path + file, alt: code, width: '24', height: '24', loading: 'lazy' }),
		]);

		button.addEventListener('click', () => {
			insert(code + ' ');
			window.bootstrap.Modal.getInstance(modal)?.hide();
		});
		grid.append(button);
	}

	modal = openModal({ title: text('COM_KUNENA_EDITOR_EMOTICONS', 'Emoticons'), body: grid });
}

/**
 * The buttons of the dialogs. The emoticons come from the options of the page (com_kunena.ckeditor_emoticons: code => file).
 */
export class KunenaDialogs extends Plugin {
	static get pluginName() {
		return 'KunenaDialogs';
	}

	init() {
		const editor = this.editor;
		const insert = (value) => {
			editor.model.change((writer) => editor.model.insertContent(writer.createText(value)));
			editor.editing.view.focus();
		};
		const option = (key, fallback) => (window.Joomla && window.Joomla.getOptions ? window.Joomla.getOptions(key, fallback) : fallback);
		const raw = option('com_kunena.ckeditor_emoticons', '{}');
		const emoticons = typeof raw === 'string' ? JSON.parse(raw || '{}') : (raw ?? {});
		const path = option('com_kunena.ckeditor_smiley_path', '/media/kunena/emoticons/');

		const buttons = [
			{ name: 'polls', label: text('COM_KUNENA_EDITOR_POLL', 'Poll'), open: () => openPollDialog() },
			{ name: 'map', label: text('COM_KUNENA_EDITOR_MAP', 'Map'), open: () => openMapDialog(insert) },
			{ name: 'video', label: text('COM_KUNENA_EDITOR_VIDEO', 'Video'), open: () => openVideoDialog(insert) },
			{ name: 'emoticons', label: text('COM_KUNENA_EDITOR_EMOTICONS', 'Emoticons'), open: () => openEmoticonsDialog(insert, emoticons, path) },
		];

		for (const button of buttons) {
			editor.ui.componentFactory.add(button.name, (locale) => {
				const view = new ButtonView(locale);

				view.set({ label: button.label, icon: ICONS[button.name], tooltip: true });
				view.kunenaName = button.name;
				view.extendTemplate({ attributes: { 'data-kunena-button': button.name } });
				view.on('execute', () => button.open());

				return view;
			});
		}
	}
}

export const DIALOG_BUTTONS = ['emoticons', 'map', 'video', 'polls'];
