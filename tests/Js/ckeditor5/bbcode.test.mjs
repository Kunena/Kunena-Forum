/**
 * Kunena Component
 *
 * The BBCode of CKEditor 5 (editor/ckeditor5/src/bbcode.js): a message comes back from the editor as it was written, for every tag
 * which the editor shows as formatted text, and every other tag stays as its text.
 *
 * Run: node --test tests/Js
 * With the messages of a site as well: KUNENA_BBCODE_CORPUS=/path/to/corpus.json node --test tests/Js
 * (a JSON list of {"source", "bbcode"}).
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 */

import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { bbcodeToHtml, htmlToBbcode } from '../../../editor/ckeditor5/src/bbcode.js';

const roundTrip = (bbcode, options) => htmlToBbcode(bbcodeToHtml(bbcode, options));

const same = [
	['plain text', 'Hello world'],
	['lines and an empty line', 'one\ntwo\n\nfour'],
	['bold, italic, underline, strike', '[b]b[/b] [i]i[/i] [u]u[/u] [s]s[/s]'],
	['nested inline tags', '[b]bold [i]both[/i][/b]'],
	['subscript and superscript', 'H[sub]2[/sub]O and x[sup]2[/sup]'],
	['color', '[color=#ff0000]red[/color] and [color=blue]blue[/color]'],
	['the sizes of Kunena', '[size=1]a[/size] [size=3]b[/size] [size=6]c[/size]'],
	['a size of CSS', '[size=14px]d[/size]'],
	['a link with a text', '[url=https://example.org]the site[/url]'],
	['a link without a text', '[url]https://example.org/a?b=1&c=2[/url]'],
	['a link in a new window', '[url=https://example.org target=_blank]text[/url]'],
	['any target value', '[url=https://example.org target=_new]text[/url]'],
	['an e-mail with a text', '[email=a@example.org]write to me[/email]'],
	['an e-mail without a text', '[email]a@example.org[/email]'],
	['an image', '[img]https://example.org/f.png[/img]'],
	['an image with a size', '[img size=200]https://example.org/a.png[/img]'],
	['an image with two sizes', '[img size=200x100]https://example.org/b.png[/img]'],
	['an image with an alt text', '[img alt=my words]https://example.org/c.png[/img]'],
	['an image with all parameters', '[img size=150 alt=photo float=left]https://example.org/e.png[/img]'],
	['a quote', '[quote]said[/quote]'],
	['a quote with an author', '[quote="Jane" post=12]said[/quote]'],
	['a quote with lines', '[quote]one\ntwo[/quote]'],
	['a code block', '[code]\n  one <two> & "three"\n[/code]'],
	['a code block with a language', '[code=php]\n\t$a = 1; // :P\n[/code]'],
	['a code block with type', '[code type=php]\n  [i]x[/i] :)\n[/code]'],
	['a list', '[list]\n[*]one\n[*]two\n[/list]'],
	['a numbered list', '[list=1]\n[*]one\n[*]two\n[/list]'],
	['a list with letters', '[list=a]\n[*]one\n[/list]'],
	['centered text', '[center]middle[/center]'],
	['right aligned lines', '[right]one\ntwo[/right]'],
	['text around a quote', 'before\n[quote]in[/quote]\nafter'],
	['a tag of Kunena which the editor does not show', '[spoiler]hidden[/spoiler] [video]https://youtu.be/x[/video]'],
	['a mention', '[mention=12]Jane[/mention] hello'],
	['a tag which is not closed', '[b]open and [i]closed[/i]'],
	['a closing tag alone', 'text[/b] more'],
	['brackets in the text', 'array[0] = [1, 2]'],
	['HTML in the text', '<script>alert(1)</script> & "quotes"'],
];

for (const [name, bbcode] of same) {
	test(`round trip: ${name}`, () => {
		assert.equal(roundTrip(bbcode), bbcode);
	});
}

test('the code block is shown as written, no tag or smiley inside is converted', () => {
	const html = bbcodeToHtml('[code=php]\necho "[b]bold[/b]"; // <img src=x onerror="x()"> :D\n[/code]');

	assert.match(html, /^<pre data-kunena-code="equals"><code class="language-php">/);
	assert.ok(!html.includes('<strong>'));
	assert.ok(!html.includes('<img'));
	assert.ok(html.includes('&lt;img src=x onerror=&quot;x()&quot;&gt;'));
});

test('the parameters of an image cannot break out of their attribute', () => {
	const html = bbcodeToHtml('[img size=200 alt="><svg onload=window.kunenaXss=1>]https://example.org/a.png[/img]');

	assert.ok(!html.includes('<svg'));
	assert.equal(roundTrip('[img size=200 alt="><svg onload=x>]https://example.org/a.png[/img]'), '[img size=200 alt="><svg onload=x>]https://example.org/a.png[/img]');
});

test('a link which opens in a new window has the target of the editor', () => {
	assert.equal(bbcodeToHtml('[url=https://example.org target=_blank]t[/url]'), '<p><a href="https://example.org" target="_blank" data-kunena-target="_blank">t</a></p>');
});

test('the size of an image which was changed in the editor is written back', () => {
	assert.equal(htmlToBbcode('<p><img src="https://example.org/a.png" width="300" data-kunena-params="size=200 alt=x"></p>'), '[img size=300 alt=x]https://example.org/a.png[/img]');
	assert.equal(htmlToBbcode('<p><img src="https://example.org/a.png" width="120"></p>'), '[img size=120]https://example.org/a.png[/img]');
});

test('the e-mail in quotes (as SCEditor writes it) is read', () => {
	assert.equal(roundTrip('[email="a@example.org"]text[/email]'), '[email=a@example.org]text[/email]');
});

test('an e-mail link made without a text (its href as the text) is written without a text', () => {
	assert.equal(htmlToBbcode('<p><a href="mailto:a@example.org">mailto:a@example.org</a></p>'), '[email]a@example.org[/email]');
});

test('an emoticon points to the emoticons of this site', () => {
	const html = bbcodeToHtml('[img]https://old.example.org/sub/media/kunena/emoticons/smile.png[/img]', { smileyPath: '/media/kunena/emoticons/' });

	assert.equal(html, '<p><img src="/media/kunena/emoticons/smile.png"></p>');
});

test('the formatting of the editor becomes BBCode', () => {
	assert.equal(htmlToBbcode('<p><strong>a</strong> <i>b</i> <s>c</s> <sub>d</sub> <sup>e</sup></p>'), '[b]a[/b] [i]b[/i] [s]c[/s] [sub]d[/sub] [sup]e[/sup]');
	assert.equal(htmlToBbcode('<p><span style="font-size:1.8em">big</span></p>'), '[size=6]big[/size]');
	assert.equal(htmlToBbcode('<p><span style="color:#00ff00">green</span></p>'), '[color=#00ff00]green[/color]');
	assert.equal(htmlToBbcode('<p style="text-align:center;">a</p><p style="text-align:center;">b</p><p>c</p>'), '[center]a\nb[/center]\nc');
	assert.equal(htmlToBbcode('<ol style="list-style-type:upper-alpha;"><li>x</li></ol>'), '[list=A]\n[*]x\n[/list]');
	assert.equal(htmlToBbcode('<p>a&nbsp;b &amp; c &lt;d&gt;</p>'), 'a b & c <d>');
	assert.equal(htmlToBbcode('<p>one<br>two</p><p>&nbsp;</p><p>four</p>'), 'one\ntwo\n\nfour');
});

test('the text before the first item of a list is kept (before the list)', () => {
	// Only the items were written: the text was lost at the first save of the message.
	assert.equal(roundTrip('[list]Intro[*]a[*]b[/list]'), 'Intro\n[list]\n[*]a\n[*]b\n[/list]');
});

test('an e-mail address with a % which is no escape is kept as it is written', () => {
	// decodeURIComponent() threw: the editor stopped converting, and the edits of the message were lost on submit.
	assert.equal(roundTrip('[email]a%b@example.org[/email]'), '[email]a%b@example.org[/email]');
});

test('Windows line endings are read as line breaks', () => {
	assert.equal(roundTrip('one\r\ntwo'), 'one\ntwo');
});

const corpus = process.env.KUNENA_BBCODE_CORPUS;

// The white space which a WYSIWYG editor cannot keep: a non-breaking space is a space, and the spaces at the end of a line (and a
// line of spaces) are not shown, so they are not kept. Every other character and every tag must come back.
const visible = (text) => text
	.replace(/\r\n?/g, '\n')
	.replace(/ /g, ' ')
	.replace(/[ \t]+$/gm, '')
	// The spaces of a line which only closes a block ("  [/quote]") are at the end of the last line of the block.
	.replace(/^[ \t]+(?=\[\/(?:quote|list|code|left|center|right|justify)\])/gm, '')
	.replace(/\n+$/, '');

test('the messages of a site come back as they were written', { skip: !corpus && 'set KUNENA_BBCODE_CORPUS' }, () => {
	const failures = [];

	for (const { source, bbcode } of JSON.parse(readFileSync(corpus, 'utf8'))) {
		const expected = visible(bbcode);
		const actual = visible(roundTrip(bbcode));

		if (actual !== expected) {
			failures.push(`${source}\n--- written\n${expected}\n--- from the editor\n${actual}`);
		}
	}

	assert.deepEqual(failures, []);
});
