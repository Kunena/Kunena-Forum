/**
 * Kunena Component
 *
 * The editor of Kunena: CKEditor 5 with the BBCode of Kunena (bbcode.js), the buttons of the tags of Kunena, and a small API for
 * the scripts of the templates (window.KunenaEditor), which do not depend on CKEditor:
 *
 *   KunenaEditor.create(textarea, options)   the editor of a textarea (a promise)
 *   KunenaEditor.get(id)                     the editor of the textarea with this id, or null
 *   KunenaEditor.ready(id, callback)         call back when the editor of this textarea is ready (at once when it is)
 *
 * An editor of this API: getData() and setData(bbcode), insertText(text), focus(), setCommandEnabled(name, enabled),
 * on('change', callback), and editor (the CKEditor 5 editor itself).
 *
 * Built with editor/ckeditor5/build.mjs into media/kunena/core/js/ckeditor5/.
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 */

import {
	Alignment,
	Autoformat,
	BlockQuote,
	Bold,
	ButtonView,
	ClassicEditor,
	Clipboard,
	CodeBlock,
	Essentials,
	FontColor,
	FontSize,
	GeneralHtmlSupport,
	HtmlDataProcessor,
	Image,
	ImageInline,
	ImageInsertViaUrl,
	ImageResize,
	Italic,
	Link,
	List,
	ListProperties,
	Mention,
	Paragraph,
	Plugin,
	SourceEditing,
	Strikethrough,
	Subscript,
	Superscript,
	Underline,
	Undo,
} from 'ckeditor5';

import { bbcodeToHtml, htmlToBbcode, SIZES } from './bbcode.js';
import { DIALOG_BUTTONS, KunenaDialogs } from './dialogs.js';
import { ICONS } from './icons.js';

const text = (key, fallback) => (window.Joomla && window.Joomla.Text ? window.Joomla.Text._(key, fallback) : fallback);
const option = (key, fallback) => (window.Joomla && window.Joomla.getOptions ? window.Joomla.getOptions(key, fallback) : fallback);

/**
 * The data of the editor is BBCode: the HTML of the editor is made from the BBCode when it is loaded, and the BBCode from the HTML
 * when it is read.
 */
class BBCodeDataProcessor {
	constructor(document, options) {
		this._html = new HtmlDataProcessor(document);
		this._options = options;
	}

	toView(data) {
		return this._html.toView(bbcodeToHtml(data, this._options));
	}

	toData(fragment) {
		return htmlToBbcode(this._html.toData(fragment));
	}

	registerRawContentMatcher(pattern) {
		this._html.registerRawContentMatcher(pattern);
	}

	useFillerType(type) {
		this._html.useFillerType(type);
	}
}

class KunenaBBCode extends Plugin {
	static get pluginName() {
		return 'KunenaBBCode';
	}

	init() {
		const editor = this.editor;
		editor.data.processor = new BBCodeDataProcessor(editor.data.viewDocument, { smileyPath: option('com_kunena.ckeditor_smiley_path', '') });
	}
}

/**
 * The buttons which wrap the selected text in a tag of Kunena ([spoiler]...[/spoiler]); the tag is text in the editor, as in the
 * message.
 */
const WRAP_BUTTONS = [
	{ name: 'spoiler', tag: 'spoiler', label: 'COM_KUNENA_EDITOR_SPOILER', fallback: 'Spoiler' },
	{ name: 'hidetext', tag: 'hide', label: 'COM_KUNENA_EDITOR_HIDE', fallback: 'Hidden text' },
	{ name: 'confidential', tag: 'confidential', label: 'COM_KUNENA_EDITOR_CONFIDENTIAL', fallback: 'Confidential' },
	{ name: 'ebay', tag: 'ebay', label: 'COM_KUNENA_EDITOR_EBAY', fallback: 'eBay' },
	{ name: 'instagram', tag: 'instagram', label: 'COM_KUNENA_EDITOR_INSTAGRAM', fallback: 'Instagram' },
	{ name: 'x', tag: 'tweet', label: 'COM_KUNENA_EDITOR_TWEET', fallback: 'X' },
	{ name: 'soundcloud', tag: 'soundcloud', label: 'COM_KUNENA_EDITOR_SOUNDCLOUD', fallback: 'SoundCloud' },
];

class KunenaTags extends Plugin {
	static get pluginName() {
		return 'KunenaTags';
	}

	init() {
		const editor = this.editor;

		for (const button of WRAP_BUTTONS) {
			editor.ui.componentFactory.add(button.name, (locale) => {
				const view = new ButtonView(locale);

				view.set({ label: text(button.label, button.fallback), icon: ICONS[button.name], tooltip: true });
				markButton(view, button.name);
				view.on('execute', () => {
					wrapSelection(editor, `[${button.tag}]`, `[/${button.tag}]`);
					editor.editing.view.focus();
				});

				return view;
			});
		}
	}
}

/** The name of a button of Kunena on its view and on its element (data-kunena-button), for setCommandEnabled() and the templates. */
export function markButton(view, name) {
	view.kunenaName = name;
	view.extendTemplate({ attributes: { 'data-kunena-button': name } });
}

/**
 * Mentions: while @name is typed, the users of the feed of Kunena; the chosen user is written as [mention userid=12]name[/mention],
 * text as the other tags of Kunena (the server shows the mention).
 */
class KunenaMention extends Plugin {
	static get pluginName() {
		return 'KunenaMention';
	}

	init() {
		const command = this.editor.commands.get('mention');

		if (!command) {
			return;
		}

		command.on('execute', (event, args) => {
			const { mention, range } = args[0] ?? {};

			if (!mention || mention.userid === undefined) {
				return;
			}

			event.stop();
			this.editor.model.change((writer) => {
				this.editor.model.insertContent(writer.createText(`[mention userid=${mention.userid}]${mention.username}[/mention] `), range);
			});
		}, { priority: 'high' });
	}
}

function mentionConfig(feed) {
	return {
		feeds: [{
			marker: '@',
			minimumCharacters: 1,
			feed: async (query) => (await feed(query)).map((user) => ({
				id: '@' + user.username,
				userid: user.userid ?? user.id,
				username: user.username,
				avatar: user.avatar,
			})),
			itemRenderer: (item) => {
				const element = document.createElement('span');
				const avatar = document.createElement('img');
				const name = document.createElement('strong');

				avatar.src = item.avatar || '';
				avatar.width = 24;
				avatar.height = 24;
				avatar.alt = '';
				name.textContent = ' ' + item.username;
				element.className = 'kunena-mention-item';
				element.append(avatar, name);

				return element;
			},
		}],
	};
}

/** Put the selected text between an opening and a closing tag, as text. */
export function wrapSelection(editor, open, close) {
	editor.model.change((writer) => {
		const selection = editor.model.document.selection;
		const selected = [...selection.getRanges()]
			.map((range) => [...range.getItems()].map((item) => (item.is('$textProxy') ? item.data : '')).join(''))
			.join('');

		editor.model.insertContent(writer.createText(open + selected + close), selection);
	});
}

/** Pasted images are not allowed (the attachments are for that): they are taken out of the pasted content. */
class KunenaBlockImagePaste extends Plugin {
	static get pluginName() {
		return 'KunenaBlockImagePaste';
	}

	init() {
		const clipboard = this.editor.plugins.get('ClipboardPipeline');

		clipboard.on('inputTransformation', (event, data) => {
			const html = data.dataTransfer.getData('text/html');

			if (html && /<img[\s>]/i.test(html)) {
				const fragment = data.content;
				const range = this.editor.editing.view.createRangeIn(fragment);
				const images = [...range.getItems()].filter((item) => item.is('element', 'img'));

				if (images.length) {
					this.editor.editing.view.change((writer) => images.forEach((image) => writer.remove(image)));
					window.alert(text('COM_KUNENA_EDITOR_PASTE_IMAGES_NOT_ALLOWED', 'Pasting images directly into the editor is not allowed.'));
				}
			}
		});
	}
}

const FONT_SIZES = Object.entries(SIZES).map(([size, css]) => ({
	title: text(`COM_KUNENA_EDITOR_SIZE_${size}`, css),
	model: size,
	view: { name: 'span', styles: { 'font-size': css }, attributes: { 'data-kunena-size': size } },
}));

const CODE_LANGUAGES = [
	{ language: 'plaintext', label: 'Plain text' },
	...['php', 'javascript', 'css', 'html', 'xml', 'sql', 'json', 'bash', 'python', 'java', 'c', 'cpp', 'csharp', 'ruby', 'go', 'yaml', 'ini', 'diff'].map((language) => ({ language, label: language })),
];

/**
 * The buttons which the configuration of the template takes out (editorButtons), with the names of CKEditor 4 (Bold, JustifyRight,
 * X_Social...) or of SCEditor (bold, right, x_social...), as the backend saves them, and the buttons of this editor for them.
 */
const REMOVED_BUTTONS = {
	bold: ['bold'], italic: ['italic'], underline: ['underline'], strike: ['strikethrough'],
	superscript: ['superscript'], subscript: ['subscript'], undo: ['undo'], redo: ['redo'], source: ['sourceEditing'],
	fontsize: ['fontSize'], size: ['fontSize'], textcolor: ['fontColor'], color: ['fontColor'],
	image: ['insertImageViaUrl'], link: ['link'], unlink: ['link'], bulletedlist: ['bulletedList'], bulletlist: ['bulletedList'],
	numberedlist: ['numberedList'], orderedlist: ['numberedList'], blockquote: ['blockQuote'], quote: ['blockQuote'], code: ['codeBlock'],
	confidential: ['confidential'], hidetext: ['hidetext'], spoiler: ['spoiler'], ebay: ['ebay'], x_social: ['x'], instagram: ['instagram'],
	soundcloud: ['soundcloud'], map: ['map'], video: ['video'], smiley: ['emoticons'], emoticon: ['emoticons'], polls: ['polls'],
};
const ALIGN_BUTTONS = { justifyleft: 'left', left: 'left', justifycenter: 'center', center: 'center', justifyright: 'right', right: 'right', justifyblock: 'justify', justify: 'justify' };

/** The buttons of the toolbar, and the alignments, from the configuration of the template (the buttons which are taken out). */
export function toolbar(removed, available = []) {
	const names = String(removed || '').split(',').map((name) => name.trim().toLowerCase()).filter(Boolean);
	const off = new Set(names.flatMap((name) => REMOVED_BUTTONS[name] ?? []));
	const alignments = ['left', 'center', 'right', 'justify'].filter((align) => !names.some((name) => ALIGN_BUTTONS[name] === align));

	if (!alignments.length) {
		off.add('alignment');
	}

	const all = [
		'undo', 'redo', '|', 'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', '|', 'fontColor', 'fontSize', '|',
		'alignment', 'bulletedList', 'numberedList', 'blockQuote', 'codeBlock', '|', 'link', 'insertImageViaUrl',
		// The buttons of Kunena on the second row ('-' is a line break of the toolbar).
		'-', ...WRAP_BUTTONS.map((button) => button.name), ...available, '|', 'sourceEditing',
	];
	const gap = (name) => name === '|' || name === '-';
	const items = [];

	// No separator at the start, at the end or twice; the line break wins over a separator next to it.
	for (const name of all) {
		if (!gap(name)) {
			if (!off.has(name)) {
				items.push(name);
			}
		} else if (items.length && gap(items.at(-1))) {
			items[items.length - 1] = name === '-' ? '-' : items.at(-1);
		} else if (items.length) {
			items.push(name);
		}
	}

	while (items.length && gap(items.at(-1))) {
		items.pop();
	}

	return { items, alignments };
}

function config(textarea, options) {
	const noLinks = option('com_kunena.ckeditor_remove_buttons_url_image', false);
	const buttons = toolbar([option('com_kunena.ckeditor_buttons_configuration', ''), noLinks ? 'link,image' : ''].join(','), options.buttons ?? DIALOG_BUTTONS);

	const mentions = typeof options.mentions === 'function';

	return {
		licenseKey: 'GPL',
		plugins: [
			Essentials, Paragraph, Autoformat, Undo, Clipboard, Bold, Italic, Underline, Strikethrough, Subscript, Superscript, FontColor, FontSize,
			Alignment, List, ListProperties, BlockQuote, CodeBlock, Link, Image, ImageInline, ImageInsertViaUrl, ImageResize, SourceEditing,
			GeneralHtmlSupport, KunenaBBCode, KunenaTags, KunenaDialogs, KunenaBlockImagePaste,
			...(mentions ? [Mention, KunenaMention] : []),
		],
		...(mentions ? { mention: mentionConfig(options.mentions) } : {}),
		// All the buttons are shown: those which do not fit go on a next row of the toolbar, not into its "more" menu.
		toolbar: { items: buttons.items, shouldNotGroupWhenFull: true },
		language: options.language || option('com_kunena.ckeditor_language', 'en'),
		fontSize: { options: FONT_SIZES, supportAllValues: false },
		alignment: { options: buttons.alignments.length ? buttons.alignments : ['left'] },
		codeBlock: { languages: CODE_LANGUAGES },
		list: { properties: { styles: true, startIndex: false, reversed: false } },
		image: { insert: { type: 'inline' } },
		link: {
			addTargetToExternalLinks: false,
			decorators: {
				newWindow: { mode: 'manual', label: text('COM_KUNENA_EDITOR_LINK_TARGET_NEW', 'Open in a new window'), attributes: { target: '_blank' } },
			},
		},
		// The parameters of Kunena which the editor has no field for (bbcode.js).
		htmlSupport: {
			allow: [
				{ name: 'a', attributes: ['data-kunena-target'] },
				{ name: 'img', attributes: ['data-kunena-params'] },
				{ name: 'pre', attributes: ['data-kunena-code'] },
				{ name: 'blockquote', attributes: ['data-kunena-quote'] },
			],
		},
		...options.config,
	};
}

/* The API of the templates ------------------------------------------------------------------------------------------------------ */

const editors = new Map();
const waiting = new Map();

class KunenaEditorInstance {
	constructor(editor, textarea) {
		this.editor = editor;
		this.textarea = textarea;
	}

	getData() {
		// In the source view the editor has the text of the view first (it takes it over when the view is left).
		if (this.isSourceMode()) {
			this.editor.plugins.get('SourceEditing').updateEditorData();
		}

		return this.editor.getData();
	}

	/**
	 * The value which the form sends: the BBCode of the editor once the text was changed in it, else the message as it was written.
	 * Called on the submit of the form (and by the tests).
	 */
	updateElement() {
		if (this.textarea.dataset.kunenaChanged === '1') {
			this.textarea.value = this.getData();
		}

		return this.textarea.value;
	}

	/** Whether the editor shows the source (the BBCode as text). */
	isSourceMode() {
		return this.editor.plugins.has('SourceEditing') && this.editor.plugins.get('SourceEditing').isSourceEditingMode;
	}

	/** Show the source, or the formatted text again. */
	setSourceMode(on) {
		if (this.editor.plugins.has('SourceEditing')) {
			this.editor.plugins.get('SourceEditing').isSourceEditingMode = Boolean(on);
		}
	}

	setData(bbcode) {
		this.editor.setData(String(bbcode ?? ''));
		this.textarea.value = String(bbcode ?? '');
		this.textarea.dataset.kunenaChanged = '';
	}

	insertText(value) {
		this.editor.model.change((writer) => {
			this.editor.model.insertContent(writer.createText(String(value)));
		});
	}

	focus() {
		this.editor.editing.view.focus();
	}

	/**
	 * Take every match of a pattern out of the texts of the editor (the [attachment=12]...[/attachment] of a removed file) and leave
	 * everything else as it is: the paragraphs, the formatting and the undo history.
	 *
	 * @return {number} The number of matches which were taken out.
	 */
	removeText(pattern) {
		const model = this.editor.model;
		const global = new RegExp(pattern.source, pattern.flags.includes('g') ? pattern.flags : pattern.flags + 'g');
		const ranges = [];

		for (const { item } of model.createRangeIn(model.document.getRoot()).getWalker({ ignoreElementEnd: true })) {
			if (!item.is('$textProxy')) {
				continue;
			}

			for (const match of item.data.matchAll(global)) {
				const start = item.startOffset + match.index;
				ranges.push(model.createRange(
					model.createPositionAt(item.parent, start),
					model.createPositionAt(item.parent, start + match[0].length),
				));
			}
		}

		if (ranges.length) {
			model.change((writer) => ranges.reverse().forEach((range) => writer.remove(range)));
		}

		return ranges.length;
	}

	/** Enable or disable a button (the poll button when the category has no polls). */
	setCommandEnabled(name, enabled) {
		const item = [...this.editor.ui.view.toolbar.items].find((view) => view.kunenaName === name);
		const command = this.editor.commands.get(name);

		if (command) {
			enabled ? command.clearForceDisabled('kunena') : command.forceDisabled('kunena');
		}

		if (item) {
			item.isEnabled = enabled;
		}
	}

	on(event, callback) {
		if (event === 'change') {
			this.editor.model.document.on('change:data', () => callback(this));
		}
	}
}

const KunenaEditor = {
	async create(textarea, options = {}) {
		const element = typeof textarea === 'string' ? document.getElementById(textarea) : textarea;
		const editor = await ClassicEditor.create(element, config(element, options));
		const instance = new KunenaEditorInstance(editor, element);

		// The textarea keeps the BBCode: the form, the drafts and the leave warning read it. It keeps the message as it was written
		// until the text is changed in the editor: a message which is sent without a change of its text is not rewritten by the editor
		// (which writes some things in its own way, the order of [b][i] around the same text for example).
		editor.model.document.on('change:data', () => {
			element.value = editor.getData();
		});
		element.form?.addEventListener('submit', () => instance.updateElement(), true);
		editor.model.document.on('change:data', () => {
			element.dataset.kunenaChanged = '1';
		});

		editors.set(element.id, instance);
		(waiting.get(element.id) ?? []).forEach((callback) => callback(instance));
		waiting.delete(element.id);

		return instance;
	},

	get(id) {
		return editors.get(id) ?? null;
	},

	ready(id, callback) {
		if (editors.has(id)) {
			callback(editors.get(id));
		} else {
			waiting.set(id, [...(waiting.get(id) ?? []), callback]);
		}
	},

	wrapSelection,
};

window.KunenaEditor = KunenaEditor;

export default KunenaEditor;
