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
import { copyFileSync, mkdirSync, readdirSync, existsSync } from 'node:fs';
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
copyFileSync(join(ckeditor, 'ckeditor5.css'), join(out, 'ckeditor5.css'));

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
