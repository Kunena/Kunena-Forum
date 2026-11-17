/**
 * Kunena Component
 * @package     Kunena.Media
 *
 * @copyright   Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

/**
 * The uploads of the forum (the attachments of a message, the avatar), Kunena's own code in place of jQuery File Upload.
 *
 *   KunenaUploader.attach(input, options)
 *
 * The files come from the file field, or are dropped on the page. Each file is sent alone, as soon as it is chosen, in the request
 * which the server reads (KunenaUpload::ajaxUpload()): a POST of multipart/form-data with the file in the field of the name of the
 * input ("file") and the fields of options.params(file) (filename, size, mime...). The answer is the JSON of Kunena.
 *
 * Options:
 *   url             the address of the upload (a text, or a function which gives it)
 *   params(file)    the other fields of the request
 *   accept(files)   called with the chosen files before anything else: false keeps none of them (too many files...), a list keeps
 *                   those files only
 *   add(file)       a file is taken: gives the item of the page which shows it (passed to the other callbacks)
 *   preview(canvas, file, item)   the picture of an image (previewWidth × previewHeight, cropped)
 *   progress(loaded, total)       of all the files which are sent
 *   done(answer, item, file)      the answer of the server
 *   fail(message, item, file)     no answer (the network, an error page of the server)
 *   resize          {maxWidth, maxHeight}: a JPEG, PNG or WebP image which is larger is made smaller before it is sent (not a
 *                   GIF, which would lose its animation, nor an SVG, which is cleaned on the server and never drawn)
 *   dropZone        where files may be dropped (the document by default; null: nowhere)
 */
(function () {
	'use strict';

	const RESIZED = ['image/jpeg', 'image/png', 'image/webp'];
	const PREVIEWED = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

	// The image of a file, turned as its EXIF says (a photo of a phone).
	const bitmapOf = (file) => createImageBitmap(file, { imageOrientation: 'from-image' });

	const preview = async (file, width, height) => {
		const bitmap = await bitmapOf(file);
		const canvas = document.createElement('canvas');
		// The picture fills the square: the image is cut at its sides or at its top and bottom.
		const scale = Math.max(width / bitmap.width, height / bitmap.height);
		const drawn = { width: bitmap.width * scale, height: bitmap.height * scale };

		canvas.width = width;
		canvas.height = height;
		canvas.getContext('2d').drawImage(bitmap, (width - drawn.width) / 2, (height - drawn.height) / 2, drawn.width, drawn.height);
		bitmap.close();

		return canvas;
	};

	const resized = async (file, limits) => {
		if (!limits || !RESIZED.includes(file.type)) {
			return file;
		}

		const bitmap = await bitmapOf(file);
		// The limits may be texts of the options ("800"); none (0, empty) is no limit.
		const limit = (value) => Number(value) || Infinity;
		const scale = Math.min(1, limit(limits.maxWidth) / bitmap.width, limit(limits.maxHeight) / bitmap.height);

		if (scale >= 1) {
			bitmap.close();

			return file;
		}

		const canvas = document.createElement('canvas');

		canvas.width = Math.round(bitmap.width * scale);
		canvas.height = Math.round(bitmap.height * scale);
		canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
		bitmap.close();

		const blob = await new Promise((resolve) => canvas.toBlob(resolve, file.type, 0.92));

		return blob ? new File([blob], file.name, { type: blob.type, lastModified: file.lastModified }) : file;
	};

	const send = (url, data, onProgress) => new Promise((resolve, reject) => {
		const request = new XMLHttpRequest();

		request.open('POST', url);
		request.setRequestHeader('Accept', 'application/json');
		request.upload.addEventListener('progress', (event) => onProgress(event.loaded, event.total));
		request.addEventListener('load', () => {
			let answer = null;

			try {
				answer = JSON.parse(request.responseText);
			} catch (error) {
				// An answer which is no JSON: an error page of the server.
			}

			// An error of Kunena is JSON too (with its message), whatever the status of the answer.
			if (answer !== null && typeof answer === 'object') {
				resolve(answer);
			} else {
				reject(new Error(request.statusText || `HTTP ${request.status}`));
			}
		});
		request.addEventListener('error', () => reject(new Error('Network error')));
		request.addEventListener('abort', () => reject(new Error('Aborted')));
		request.send(data);
	});

	const attach = (input, options = {}) => {
		if (!(input instanceof HTMLInputElement) || input.dataset.kunenaUploader) {
			return null;
		}

		input.dataset.kunenaUploader = '1';

		const field = input.name || 'file';
		const sizes = new Map();
		const call = (name, ...args) => (typeof options[name] === 'function' ? options[name](...args) : undefined);

		// The progress of all the files which are sent now.
		const report = () => {
			let loaded = 0;
			let total = 0;

			sizes.forEach((size) => {
				loaded += size.loaded;
				total += size.total;
			});

			call('progress', loaded, total);
		};

		const upload = async (original) => {
			const item = call('add', original);
			let file = original;

			try {
				if (PREVIEWED.includes(original.type) && options.previewWidth !== 0) {
					call('preview', await preview(original, options.previewWidth || 100, options.previewHeight || 100), original, item);
				}
			} catch (error) {
				// No picture of an image which the browser cannot read: the file is sent all the same.
			}

			try {
				file = await resized(original, options.resize);
			} catch (error) {
				file = original;
			}

			const data = new FormData();
			const params = call('params', file) || {};

			Object.keys(params).forEach((name) => data.append(name, params[name]));
			data.append(field, file, file.name);
			sizes.set(file, { loaded: 0, total: file.size });

			try {
				const url = typeof options.url === 'function' ? options.url() : options.url;
				const answer = await send(url, data, (loaded, total) => {
					sizes.set(file, { loaded, total });
					report();
				});

				sizes.set(file, { loaded: file.size, total: file.size });
				report();
				call('done', answer, item, file);
			} catch (error) {
				call('fail', error.message, item, file);
			} finally {
				sizes.delete(file);
			}
		};

		const take = (list) => {
			const files = [...(list || [])].filter((file) => file instanceof File);

			if (!files.length) {
				return;
			}

			// accept() keeps none of the files (false), some of them (a list) or all of them.
			const kept = call('accept', files);

			if (kept === false) {
				return;
			}

			(Array.isArray(kept) ? kept : files).forEach((file) => upload(file));
		};

		input.addEventListener('change', () => {
			take(input.files);
			// The same file can be chosen again.
			input.value = '';
		});

		const zone = options.dropZone === undefined ? document : options.dropZone;
		const hasFiles = (event) => event.dataTransfer && [...event.dataTransfer.types].includes('Files');

		if (zone) {
			zone.addEventListener('dragover', (event) => {
				if (hasFiles(event) && !input.disabled) {
					event.preventDefault();
				}
			});
			zone.addEventListener('drop', (event) => {
				// An editor which takes the drop itself (it stops it) keeps it.
				if (!hasFiles(event) || event.defaultPrevented || input.disabled) {
					return;
				}

				event.preventDefault();
				take(event.dataTransfer.files);
			});
		}

		return { add: take };
	};

	window.KunenaUploader = { attach };
})();
