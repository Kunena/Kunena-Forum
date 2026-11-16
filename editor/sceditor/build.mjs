/**
 * The minified SCEditor of Kunena (media/kunena/core/js/sceditor/*.min.js), built from the unminified files in
 * media/kunena/core/js/dev/sceditor: SCEditor with Kunena's own changes (the image dialog with alt text, size and float, the image
 * data URIs). The debug mode of the site loads the unminified files, every other page the minified ones: both must be the same code.
 *
 * Run with: npm run build:sceditor
 */
import { readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { transformSync } from 'esbuild';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const js = resolve(root, 'src/media/kunena/core/js');
const version = '3.2.3';
const header = `/* SCEditor v${version} | (C) 2017, Sam Clarke | sceditor.com/license | with changes of Kunena (dev/sceditor) */\n`;

for (const [source, target] of [['dev/sceditor/sceditor.js', 'sceditor/sceditor.min.js'], ['dev/sceditor/bbcode.js', 'sceditor/bbcode.min.js']]) {
	const { code } = transformSync(readFileSync(resolve(js, source), 'utf8'), {
		minify: true,
		legalComments: 'inline',
		target: 'es2019',
		loader: 'js',
	});

	writeFileSync(resolve(js, target), header + code);
	console.log(`${target}: ${(code.length / 1024).toFixed(0)} kB`);
}
