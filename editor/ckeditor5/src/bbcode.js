/**
 * Kunena Component
 *
 * The BBCode of Kunena for CKEditor 5, which has no BBCode of its own: the message (BBCode) is turned into the HTML of the editor when
 * it is loaded, and the HTML of the editor back into BBCode when it is saved.
 *
 * The tags which the editor shows as formatted text: b, i, u, s, sub, sup, color, size, left, center, right, justify, quote, code,
 * url, email, img, list and *. Every other tag (spoiler, hide, video, map...) and every tag which is not closed stays in the editor
 * as its text, so it comes back as it was written. The parameters which the editor has no field for (the target of a link, the
 * parameters of an image, the form of a [code] tag, the author of a quote) are kept in data- attributes and written back.
 *
 * No DOM is used: the module also runs in Node, for its tests (tests/Js).
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 */

/** The sizes of Kunena ([size=1] to [size=6], layouts/bbcode/size of the templates). */
export const SIZES = { 1: '0.6em', 2: '0.8em', 3: '1em', 4: '1.2em', 5: '1.4em', 6: '1.8em' };

const INLINE = { b: 'strong', i: 'i', u: 'u', s: 's', sub: 'sub', sup: 'sup' };
const ALIGN = { left: 'left', center: 'center', right: 'right', justify: 'justify' };
const KNOWN = new Set(['b', 'i', 'u', 's', 'sub', 'sup', 'color', 'size', 'left', 'center', 'right', 'justify', 'quote', 'code', 'url', 'email', 'img', 'list', '*']);

export function escapeHtml(text) {
	return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

export function decodeEntities(text) {
	return String(text).replace(/&(#x[0-9a-f]+|#\d+|[a-z]+);/gi, (all, name) => {
		if (name[0] === '#') {
			const code = name[1] === 'x' || name[1] === 'X' ? parseInt(name.slice(2), 16) : parseInt(name.slice(1), 10);

			return Number.isFinite(code) ? String.fromCodePoint(code) : all;
		}

		return { amp: '&', lt: '<', gt: '>', quot: '"', apos: "'", nbsp: ' ' }[name.toLowerCase()] ?? all;
	});
}

/* ------------------------------------------------------------------------------------------------------------------------------
 * BBCode -> HTML
 * ---------------------------------------------------------------------------------------------------------------------------- */

/**
 * The tokens of a message: text, and tags ({tag, close, param, raw}).
 */
function tokenize(bbcode) {
	const tokens = [];
	const pattern = /\[(\/?)([a-zA-Z]+|\*)((?:=|\s)[^\]\[]*)?\]/g;
	let last = 0;
	let match;

	while ((match = pattern.exec(bbcode)) !== null) {
		const tag = match[2].toLowerCase();

		if (!KNOWN.has(tag)) {
			continue;
		}

		if (match.index > last) {
			tokens.push({ text: bbcode.slice(last, match.index) });
		}

		tokens.push({ tag, close: match[1] === '/', param: match[3] ?? '', raw: match[0] });
		last = pattern.lastIndex;

		// The text of a code block is not BBCode: up to its closing tag, as it is.
		if (tag === 'code' && match[1] !== '/') {
			const end = bbcode.toLowerCase().indexOf('[/code]', last);

			if (end !== -1) {
				tokens.push({ text: bbcode.slice(last, end), verbatim: true });
				tokens.push({ tag: 'code', close: true, param: '', raw: bbcode.slice(end, end + 7) });
				last = end + 7;
				pattern.lastIndex = last;
			}
		}
	}

	if (last < bbcode.length) {
		tokens.push({ text: bbcode.slice(last) });
	}

	return tokens;
}

/**
 * A tree of the tokens: a tag which is not closed, or closed in the wrong place, becomes its text again.
 */
function parse(tokens) {
	const root = { tag: 'root', children: [] };
	const stack = [root];

	for (const token of tokens) {
		const top = stack[stack.length - 1];

		if (token.text !== undefined) {
			top.children.push(token);
			continue;
		}

		if (token.tag === '*') {
			// A list item ends at the next item or at the end of its list.
			if (top.tag === '*') {
				stack.pop();
			}

			const parent = stack[stack.length - 1];

			if (parent.tag !== 'list' || token.close) {
				parent.children.push({ text: token.raw });
				continue;
			}

			const item = { tag: '*', param: '', raw: token.raw, children: [] };
			parent.children.push(item);
			stack.push(item);
			continue;
		}

		if (!token.close) {
			const node = { tag: token.tag, param: token.param, raw: token.raw, children: [] };
			top.children.push(node);
			stack.push(node);
			continue;
		}

		// A closing tag: the open tag of the same name, the items of a list end with it.
		let depth = stack.length - 1;

		if (stack[depth].tag === '*' && token.tag === 'list') {
			depth--;
		}

		if (stack[depth].tag === token.tag) {
			stack[depth].closed = true;
			stack[depth].closeRaw = token.raw;
			stack.length = depth;
		} else {
			top.children.push({ text: token.raw });
		}
	}

	return unclosed(root);
}

/**
 * The tags which were not closed are text again: their opening tag, then their children.
 */
function unclosed(node) {
	const children = [];

	for (const child of node.children) {
		if (child.text !== undefined) {
			children.push(child);
		} else if (child.closed || child.tag === '*') {
			children.push(unclosed(child));
		} else {
			children.push({ text: child.raw }, ...unclosed(child).children);
		}
	}

	return { ...node, children };
}

/** The value of a parameter: [tag=value] or [tag="value"]. */
function mainParam(param) {
	const value = param.startsWith('=') ? param.slice(1).trim() : '';

	return value.replace(/^"(.*)"$/s, '$1').replace(/^'(.*)'$/s, '$1');
}

function textOf(node) {
	return node.children.map((child) => (child.text !== undefined ? child.text : child.raw + textOf(child) + (child.closeRaw ?? ''))).join('');
}

/**
 * The spaces which HTML would join or drop (the editor shows HTML): a space at the start of a line and every space after another
 * one are non-breaking spaces, which become spaces again in the BBCode (an indented list, a table of Markdown keep their columns).
 */
export function keepSpaces(text) {
	return text.replace(/(^|\n) /g, '$1 ').replace(/ {2,}/g, (spaces) => ' ' + ' '.repeat(spaces.length - 1));
}

/** The source of an emoticon at the address of the emoticons of this site (the message keeps the address of the site where it was written). */
function emoticonSource(src, smileyPath) {
	const match = /^(?:https?:\/\/[^/]+)?(?:\/[^?#]*)?\/media\/kunena\/emoticons\/([^/?#]+)$/i.exec(src);

	return match && smileyPath ? smileyPath + match[1] : src;
}

/**
 * The HTML of the inline content of a node (text, inline tags, links, images). A line break is a <br>.
 */
function inlineHtml(nodes, options) {
	let html = '';

	for (const node of nodes) {
		if (node.text !== undefined) {
			html += escapeHtml(keepSpaces(node.text)).replace(/\n/g, '<br>');
			continue;
		}

		const inner = () => inlineHtml(node.children, options);

		if (INLINE[node.tag]) {
			html += `<${INLINE[node.tag]}>${inner()}</${INLINE[node.tag]}>`;
		} else if (node.tag === 'color') {
			html += `<span style="color:${escapeHtml(mainParam(node.param))}">${inner()}</span>`;
		} else if (node.tag === 'size') {
			const size = mainParam(node.param);
			html += `<span style="font-size:${escapeHtml(SIZES[size] ?? size)}" data-kunena-size="${escapeHtml(size)}">${inner()}</span>`;
		} else if (node.tag === 'url') {
			html += linkHtml(node, options);
		} else if (node.tag === 'email') {
			const address = mainParam(node.param) || textOf(node);
			const text = mainParam(node.param) ? inner() : escapeHtml(address);
			html += `<a href="mailto:${escapeHtml(address)}">${text}</a>`;
		} else if (node.tag === 'img') {
			const src = emoticonSource(textOf(node).trim(), options.smileyPath);
			const params = node.param.trim();
			const width = /(?:^|\s)size=(\d+)/.exec(params);
			const attrs = [`src="${escapeHtml(src)}"`];

			if (width) {
				attrs.push(`width="${width[1]}"`);
			}

			if (params) {
				attrs.push(`data-kunena-params="${escapeHtml(params)}"`);
			}

			html += `<img ${attrs.join(' ')}>`;
		} else {
			// A block tag inside a line (a quote in a list item...): its text.
			html += escapeHtml(node.raw + textOf(node) + (node.closeRaw ?? ''));
		}
	}

	return html;
}

function linkHtml(node, options) {
	const param = node.param.startsWith('=') ? node.param.slice(1) : '';
	const target = /\s+target=(\S+)\s*$/.exec(param);
	const href = mainParam('=' + (target ? param.slice(0, target.index) : param)) || textOf(node);
	const text = param ? inlineHtml(node.children, options) : escapeHtml(href);
	const attrs = [`href="${escapeHtml(href)}"`];

	if (target) {
		attrs.push('target="_blank"', `data-kunena-target="${escapeHtml(target[1])}"`);
	}

	return `<a ${attrs.join(' ')}>${text}</a>`;
}

/**
 * The blocks of a list of nodes: paragraphs of the lines, quotes, code blocks, lists and aligned paragraphs.
 */
function blocksHtml(nodes, options, align = '') {
	let html = '';
	let line = [];
	const style = align ? ` style="text-align:${align}"` : '';

	const flush = (force) => {
		if (line.length || force) {
			const content = inlineHtml(line, options);
			html += `<p${style}>${content || '&nbsp;'}</p>`;
		}

		line = [];
	};

	let afterBlock = false;

	for (const node of nodes) {
		if (node.text !== undefined) {
			// The line break right after a block ends the line of the block.
			const parts = (afterBlock ? node.text.replace(/^\n/, '') : node.text).split('\n');
			afterBlock = false;

			parts.forEach((part, index) => {
				if (index > 0) {
					flush(true);
				}

				if (part !== '') {
					line.push({ text: part });
				}
			});
			continue;
		}

		if (['quote', 'code', 'list', ...Object.keys(ALIGN)].includes(node.tag)) {
			flush(false);
			html += blockHtml(node, options, align);
			afterBlock = true;
			continue;
		}

		afterBlock = false;
		line.push(node);
	}

	flush(false);

	return html;
}

function blockHtml(node, options, align) {
	if (node.tag === 'code') {
		const param = node.param;
		const language = (/^=\s*(\w+)/.exec(param) ?? /type=["']?(\w+)/.exec(param) ?? [])[1] ?? 'plaintext';
		const form = param.startsWith('=') ? 'equals' : (param.trim() ? 'type' : 'none');
		const text = textOf(node).replace(/^\n/, '').replace(/\n$/, '');

		return `<pre data-kunena-code="${form}"><code class="language-${escapeHtml(language)}">${escapeHtml(text)}</code></pre>`;
	}

	if (node.tag === 'quote') {
		// The parameter as it was written: [quote="Jane" post=12] or [quote Jane].
		const cite = node.param ? ` data-kunena-quote="${escapeHtml(node.param)}"` : '';

		return `<blockquote${cite}>${blocksHtml(trimBreaks(node.children), options) || '<p>&nbsp;</p>'}</blockquote>`;
	}

	if (node.tag === 'list') {
		const type = mainParam(node.param);
		const tag = type ? 'ol' : 'ul';
		const styles = { 1: 'decimal', a: 'lower-alpha', A: 'upper-alpha', i: 'lower-roman', I: 'upper-roman' };
		const style = type && styles[type] ? ` style="list-style-type:${styles[type]}"` : '';
		const items = node.children.filter((child) => child.tag === '*');
		// The text before the first [*] ([list]Intro[*]a[/list]) is kept, as a paragraph before the list: only the items were
		// written, and the text was lost at the first save of the message.
		const first = node.children.findIndex((child) => child.tag === '*');
		const intro = first < 0 ? node.children : node.children.slice(0, first);
		const introHtml = intro.some((child) => child.text === undefined || child.text.trim() !== '') ? blocksHtml(trimBreaks(intro), options) : '';

		return `${introHtml}<${tag}${style}>${items.map((item) => `<li>${blocksHtml(trimBreaks(item.children), options) || '<p>&nbsp;</p>'}</li>`).join('')}</${tag}>`;
	}

	// left, center, right, justify: the paragraphs inside are aligned.
	return blocksHtml(trimBreaks(node.children), options, ALIGN[node.tag]);
}

/** The line break right after an opening tag and right before a closing one belongs to the tags, not to the text. */
function trimBreaks(children) {
	const list = children.slice();

	if (list.length && list[0].text !== undefined) {
		list[0] = { text: list[0].text.replace(/^\n/, '') };
	}

	const last = list.length - 1;

	if (last >= 0 && list[last].text !== undefined) {
		list[last] = { text: list[last].text.replace(/\n$/, '') };
	}

	return list;
}

/**
 * The HTML of the editor for a message.
 *
 * @param {string} bbcode The message.
 * @param {{smileyPath?: string}} options The address of the folder of the emoticons of this site.
 */
export function bbcodeToHtml(bbcode, options = {}) {
	const tree = parse(tokenize(String(bbcode ?? '').replace(/\r\n?/g, '\n')));

	return blocksHtml(tree.children, options);
}

/* ------------------------------------------------------------------------------------------------------------------------------
 * HTML -> BBCode
 * ---------------------------------------------------------------------------------------------------------------------------- */

const VOID = new Set(['br', 'img', 'hr', 'input', 'meta', 'link', 'wbr', 'col', 'source']);
const BLOCK = new Set(['p', 'div', 'blockquote', 'pre', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'figure', 'table']);

function parseAttributes(source) {
	const attrs = {};
	const pattern = /([^\s=/>]+)(?:\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+)))?/g;
	let match;

	while ((match = pattern.exec(source)) !== null) {
		attrs[match[1].toLowerCase()] = decodeEntities(match[2] ?? match[3] ?? match[4] ?? '');
	}

	return attrs;
}

/** A tree of the HTML of the editor (which writes well-formed HTML). */
export function parseHtml(html) {
	const root = { name: '#root', attrs: {}, children: [] };
	const stack = [root];
	const pattern = /<!--[\s\S]*?-->|<\/([a-zA-Z][\w-]*)\s*>|<([a-zA-Z][\w-]*)((?:\s+[^\s=/>]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s>]+))?)*)\s*(\/?)>|([^<]+|<)/g;
	let match;

	while ((match = pattern.exec(html)) !== null) {
		const top = stack[stack.length - 1];

		if (match[0].startsWith('<!--')) {
			continue;
		}

		if (match[1]) {
			const name = match[1].toLowerCase();
			const at = stack.map((node) => node.name).lastIndexOf(name);

			if (at > 0) {
				stack.length = at;
			}

			continue;
		}

		if (match[2]) {
			const node = { name: match[2].toLowerCase(), attrs: parseAttributes(match[3] ?? ''), children: [] };
			top.children.push(node);

			if (!VOID.has(node.name) && !match[4]) {
				stack.push(node);
			}

			continue;
		}

		top.children.push({ text: decodeEntities(match[5]) });
	}

	return root;
}

function styleOf(node, property) {
	const match = new RegExp(`(?:^|;)\\s*${property}\\s*:\\s*([^;]+)`, 'i').exec(node.attrs.style ?? '');

	return match ? match[1].trim() : '';
}

function plainText(node) {
	if (node.text !== undefined) {
		return node.text;
	}

	return node.name === 'br' ? '\n' : node.children.map(plainText).join('');
}

/** The BBCode of the inline content of a node. */
function inlineBbcode(nodes) {
	let out = '';

	for (const node of nodes) {
		if (node.text !== undefined) {
			out += node.text.replace(/ /g, ' ');
			continue;
		}

		const inner = () => inlineBbcode(node.children);

		switch (node.name) {
			case 'br':
				out += '\n';
				break;
			case 'strong':
			case 'b':
				out += `[b]${inner()}[/b]`;
				break;
			case 'i':
			case 'em':
				out += `[i]${inner()}[/i]`;
				break;
			case 'u':
				out += `[u]${inner()}[/u]`;
				break;
			case 's':
			case 'del':
			case 'strike':
				out += `[s]${inner()}[/s]`;
				break;
			case 'sub':
				out += `[sub]${inner()}[/sub]`;
				break;
			case 'sup':
				out += `[sup]${inner()}[/sup]`;
				break;
			case 'code':
				out += inner();
				break;
			case 'a':
				out += linkBbcode(node);
				break;
			case 'img':
				out += imageBbcode(node);
				break;
			case 'span':
				out += spanBbcode(node);
				break;
			default:
				out += inner();
		}
	}

	return out;
}

function spanBbcode(node) {
	let out = inlineBbcode(node.children);
	const size = node.attrs['data-kunena-size'] ?? sizeOf(styleOf(node, 'font-size'));
	const color = styleOf(node, 'color');

	if (size) {
		out = `[size=${size}]${out}[/size]`;
	}

	if (color) {
		out = `[color=${color}]${out}[/color]`;
	}

	return out;
}

/** The [size] of a font size of the editor (the sizes of Kunena), or the size as it is. */
function sizeOf(fontSize) {
	if (!fontSize) {
		return '';
	}

	const found = Object.entries(SIZES).find(([, value]) => value === fontSize);

	return found ? found[0] : fontSize;
}

function linkBbcode(node) {
	const href = node.attrs.href ?? '';
	const text = inlineBbcode(node.children);

	if (/^mailto:/i.test(href)) {
		// An address with a % which is no escape (a%b@example.org) is kept as it is written: decodeURIComponent() threw, the editor
		// stopped converting, and the edits of the message were lost on submit.
		let address;

		try {
			address = decodeURIComponent(href.slice(7));
		} catch {
			address = href.slice(7);
		}

		// A link made without a text has its address as the text (CKEditor writes the href, mailto: included).
		return text === address || text === href || text === '' ? `[email]${address}[/email]` : `[email=${address}]${text}[/email]`;
	}

	const blank = node.attrs.target === '_blank';
	const target = blank ? ` target=${node.attrs['data-kunena-target'] || '_blank'}` : '';

	if (text === href && !target) {
		return `[url]${href}[/url]`;
	}

	return `[url=${href}${target}]${text}[/url]`;
}

function imageBbcode(node) {
	let params = node.attrs['data-kunena-params'] ?? '';
	const width = node.attrs.width ?? (styleOf(node, 'width').match(/^(\d+)px$/) ?? [])[1];

	// The size which the editor shows: written back into the parameters, when it changed.
	if (width) {
		const size = /(^|\s)size=(\d+)(x\d+)?/.exec(params);

		if (!size) {
			params = `size=${width}` + (params ? ` ${params}` : '');
		} else if (size[2] !== String(width)) {
			params = params.replace(size[0], `${size[1]}size=${width}`);
		}
	}

	return `[img${params ? ' ' + params : ''}]${node.attrs.src ?? ''}[/img]`;
}

/** The BBCode of the blocks inside a block (a quote, a list item): as the top level. */
function blocksBbcode(nodes) {
	return groupAlign(nodes);
}

function blockBbcode(node) {
	switch (node.name) {
		case 'p':
		case 'div':
		case 'h1':
		case 'h2':
		case 'h3':
		case 'h4':
		case 'h5':
		case 'h6': {
			const text = inlineBbcode(node.children);
			const align = styleOf(node, 'text-align');

			return { text: text === ' ' ? '' : text, align: align && align !== 'left' ? align : (align === 'left' ? 'left' : '') };
		}
		case 'pre': {
			const code = node.children.find((child) => child.name === 'code') ?? node;
			const language = ((code.attrs.class ?? '').match(/language-(\w+)/) ?? [])[1] ?? 'plaintext';
			const form = node.attrs['data-kunena-code'] ?? (language === 'plaintext' ? 'none' : 'type');
			const open = form === 'none' || language === 'plaintext' ? '[code]' : (form === 'equals' ? `[code=${language}]` : `[code type=${language}]`);

			return { text: `${open}\n${plainText(code)}\n[/code]` };
		}
		case 'blockquote': {
			const author = node.attrs['data-kunena-quote'];

			return { text: `[quote${author ?? ''}]${blocksBbcode(node.children)}[/quote]` };
		}
		case 'ul':
		case 'ol': {
			const types = { decimal: '1', 'lower-alpha': 'a', 'upper-alpha': 'A', 'lower-roman': 'i', 'upper-roman': 'I' };
			const style = styleOf(node, 'list-style-type');
			const type = node.name === 'ol' ? (types[style] ?? '1') : '';
			const items = node.children.filter((child) => child.name === 'li').map((li) => `[*]${blocksBbcode(li.children)}`);

			return { text: `[list${type ? '=' + type : ''}]\n${items.join('\n')}\n[/list]` };
		}
		default:
			return { text: blocksBbcode(node.children) };
	}
}

/**
 * The BBCode of the HTML of the editor.
 *
 * @param {string} html The HTML of the editor.
 */
export function htmlToBbcode(html) {
	return groupAlign(parseHtml(String(html ?? '')).children);
}

/**
 * The blocks of the top level: the paragraphs with the same alignment one after the other are one [center]...[/center].
 */
function groupAlign(nodes) {
	const out = [];
	let group = null;
	let inline = [];

	const flushInline = () => {
		if (inline.length) {
			out.push(inlineBbcode(inline));
		}

		inline = [];
	};

	const flushGroup = () => {
		if (group) {
			out.push(`[${group.align}]${group.lines.join('\n')}[/${group.align}]`);
		}

		group = null;
	};

	for (const node of nodes) {
		if (node.text !== undefined && node.text.trim() === '' && !inline.length) {
			continue;
		}

		if (node.text !== undefined || !BLOCK.has(node.name)) {
			flushGroup();
			inline.push(node);
			continue;
		}

		flushInline();
		const block = blockBbcode(node);

		if (block.align && ALIGN[block.align]) {
			if (group && group.align === block.align) {
				group.lines.push(block.text);
			} else {
				flushGroup();
				group = { align: block.align, lines: [block.text] };
			}

			continue;
		}

		flushGroup();
		out.push(block.text);
	}

	flushInline();
	flushGroup();

	return out.join('\n');
}
