/**
 * Kunena Component
 *
 * Build the editor of Kunena (CKEditor 5) into media/kunena/core/js/ckeditor5/: one script (an IIFE which sets window.KunenaEditor),
 * its style sheet, and the translations of CKEditor which Kunena ships.
 *
 * Run: npm run build:editor
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 */

import { build } from 'esbuild';
import { copyFileSync, mkdirSync, readdirSync, existsSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '../..');
const out = join(root, 'src/media/kunena/core/js/ckeditor5');
const ckeditor = join(root, 'node_modules/ckeditor5/dist');

mkdirSync(out, { recursive: true });

await build({
	entryPoints: [join(root, 'editor/ckeditor5/src/kunena-editor.js')],
	bundle: true,
	format: 'iife',
	minify: true,
	sourcemap: false,
	target: ['es2020'],
	outfile: join(out, 'kunena-editor.js'),
	legalComments: 'inline',
	logLevel: 'info',
});

// The style sheet of the editor (the content of the messages is styled by the templates).
const styleSheet = join(out, 'ckeditor5.css');
copyFileSync(join(ckeditor, 'ckeditor5.css'), styleSheet);

const css = readFileSync(styleSheet, 'utf8');
const focusShadowGeometry = '  --ck-focus-shadow-geometry: 0 0 0 var(--ck-focus-shadow-spread);';
const showBlocksBorderColor = '  --ck-show-blocks-border-color: var(--ck-color-base-text-light);';

if (!css.includes(focusShadowGeometry)) {
	throw new Error('Could not find CKEditor focus shadow geometry in its style sheet.');
}

if (!css.includes(showBlocksBorderColor)) {
	throw new Error('Could not find CKEditor show-blocks styles in its style sheet.');
}

const showBlocksElements = [
	'address', 'aside', 'blockquote', 'details', 'div', 'footer', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
	'header', 'main', 'nav', 'pre', 'ol', 'ul', 'p', 'section', 'figcaption',
];
const showBlocksLabels = showBlocksElements
	.flatMap((element) => ['ltr', 'rtl'].map((direction) => `  --ck-show-blocks-label-${element}-${direction}: none;`))
	.join('\n');

let fixedCss = css
	.replace(
		focusShadowGeometry,
		`${focusShadowGeometry}\n  --ck-focus-outer-shadow-geometry: var(--ck-focus-shadow-geometry);`
	)
	.replace(showBlocksBorderColor, `${showBlocksBorderColor}\n${showBlocksLabels}`);

const emptyUnfocusedMaxWidths = /^(\s*--ck-labeled-field-empty-unfocused-max-width:\s*)(100%\s*-.+);$/gm;
const matches = [...fixedCss.matchAll(emptyUnfocusedMaxWidths)];

if (matches.length !== 2) {
	throw new Error(`Expected two CKEditor search max-width values, found ${matches.length}.`);
}

fixedCss = fixedCss.replace(emptyUnfocusedMaxWidths, '$1calc($2);');

const searchHorizontalSpacing =
	'calc(var(--ck-icon-size) + var(--ck-spacing-medium, var(--ck-spacing-base)))';
const searchHorizontalSpacingFallback = `var(--ck-search-field-view-horizontal-spacing, ${searchHorizontalSpacing})`;

if (fixedCss.split(searchHorizontalSpacingFallback).length - 1 !== 7) {
	throw new Error('Expected seven CKEditor search spacing fallbacks.');
}

fixedCss = fixedCss
	.replace(
		'& > .ck-icon {\n      margin-right: var(--ck-spacing-control-icon-gap);\n      width: var(--ck-collapsible-arrow-size, calc(.5 * var(--ck-icon-size)));',
		'& > .ck-icon {\n      --ck-collapsible-arrow-size: calc(.5 * var(--ck-icon-size));\n      margin-right: var(--ck-spacing-control-icon-gap);\n      width: var(--ck-collapsible-arrow-size);'
	)
	.replace(
		'.ck.ck-labeled-field-view {\n  border-radius:',
		'.ck.ck-labeled-field-view {\n  --ck-labeled-field-empty-unfocused-max-width: calc(100% - var(--ck-labeled-field-label-start-gap) - var(--ck-labeled-field-label-start-gap));\n  border-radius:'
	)
	.replace(
		'max-width: var(--ck-labeled-field-empty-unfocused-max-width, calc(100% - 2 * var(--ck-labeled-field-label-start-gap)));',
		'max-width: var(--ck-labeled-field-empty-unfocused-max-width);'
	)
	.replace(
		'&.ck-labeled-field-view_empty:not(.ck-labeled-field-view_focused):not(.ck-labeled-field-view_placeholder) > .ck.ck-labeled-field-view__input-wrapper > .ck-dropdown + .ck-label {\n    max-width: var(--ck-labeled-field-empty-unfocused-max-width, calc(100% - 2 * var(--ck-labeled-field-label-start-gap) - var(--ck-dropdown-arrow-size) - var(--ck-spacing-control-padding-inline)));',
		'&.ck-labeled-field-view_empty:not(.ck-labeled-field-view_focused):not(.ck-labeled-field-view_placeholder) > .ck.ck-labeled-field-view__input-wrapper > .ck-dropdown + .ck-label {\n    --ck-labeled-field-empty-unfocused-max-width: calc(100% - var(--ck-labeled-field-label-start-gap) - var(--ck-labeled-field-label-start-gap) - var(--ck-dropdown-arrow-size) - var(--ck-spacing-control-padding-inline));\n    max-width: var(--ck-labeled-field-empty-unfocused-max-width);'
	)
	.replace(
		'.ck.ck-search {\n',
		`.ck.ck-search {\n  --ck-search-field-view-horizontal-spacing: ${searchHorizontalSpacing};\n`
	)
	.replaceAll(searchHorizontalSpacingFallback, 'var(--ck-search-field-view-horizontal-spacing)')
	.replace('  & .ck-widget:-webkit-drag {', '  /*noinspection CssInvalidPseudoSelector*/\n  & .ck-widget:-webkit-drag {')
	.replace('.ck .ck-widget.media:-webkit-drag > .ck-widget__resizer {', '/*noinspection CssInvalidPseudoSelector*/\n.ck .ck-widget.media:-webkit-drag > .ck-widget__resizer {');

if (
	fixedCss.includes('var(--ck-collapsible-arrow-size,') ||
	fixedCss.includes('var(--ck-labeled-field-empty-unfocused-max-width,') ||
	fixedCss.includes('var(--ck-search-field-view-horizontal-spacing,') ||
	fixedCss.split('noinspection CssInvalidPseudoSelector').length - 1 !== 2
) {
	throw new Error('Could not apply all CKEditor CSS editor-compatibility fixes.');
}

const declaredProperties = new Set(
	[...fixedCss.matchAll(/(--[\w-]+)\s*:/g)].map(([, property]) => property)
);
const missingProperties = new Set();
const variableReferences = /var\(\s*(--[\w-]+)/g;

for (const match of fixedCss.matchAll(variableReferences)) {
	const property = match[1];

	if (declaredProperties.has(property)) {
		continue;
	}

	let depth = 1;
	let hasFallback = false;
	let index = match.index + match[0].length;

	while (index < fixedCss.length && depth > 0) {
		if (fixedCss[index] === '(') {
			depth++;
		} else if (fixedCss[index] === ')') {
			depth--;
		} else if (fixedCss[index] === ',' && depth === 1) {
			hasFallback = true;
		}

		index++;
	}

	if (!hasFallback) {
		throw new Error(`CKEditor custom property ${property} has no declaration or fallback.`);
	}

	missingProperties.add(property);
}

const missingPropertyDefaults = [...missingProperties]
	.map((property) => `  ${property}: initial;`)
	.join('\n');

if (missingPropertyDefaults) {
	const rootSelector = ':root, :host {\n';

	if (!fixedCss.includes(rootSelector)) {
		throw new Error('Could not find CKEditor root styles for custom-property defaults.');
	}

	fixedCss = fixedCss.replace(rootSelector, `${rootSelector}${missingPropertyDefaults}\n`);
}

writeFileSync(styleSheet, fixedCss);

// The translations of CKEditor: one script for each language of CKEditor, loaded with the editor for the language of the page.
const translations = join(ckeditor, 'translations');
const target = join(out, 'translations');

mkdirSync(target, { recursive: true });

for (const file of readdirSync(translations)) {
	if (file.endsWith('.umd.js')) {
		copyFileSync(join(translations, file), join(target, file.replace('.umd.js', '.js')));
	}
}

console.log(`Built into ${out}${existsSync(join(out, 'kunena-editor.js')) ? '' : ' (no script!)'}`);
