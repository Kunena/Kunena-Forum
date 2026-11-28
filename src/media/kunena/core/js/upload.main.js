/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

(function () {
	'use strict';

	// The files of the message: how many there are, and how many of the files of the edited message are in the text.
	let fileCount = 0;
	// The uploads which are still running: the form is sent only when none is (the first finished one of several enabled the
	// button, and the files still on their way were not in the post).
	let pending = 0;
	const finished = () => {
		pending = Math.max(0, pending - 1);

		return pending === 0;
	};
	let fileeditinline = 0;

	// The data of each button of a file (the answer of the upload, or the file of the edited message).
	const buttonData = new WeakMap();

	const byId = (id) => document.getElementById(id);
	const text = (key) => Joomla.Text._(key);
	const isCkeditor = () => Joomla.getOptions('com_kunena.ckeditor_config') !== undefined;
	const privateMessages = () => Joomla.getOptions('com_kunena.privateMessage') == 1;

	const show = (element, shown = true) => {
		if (element) {
			element.style.display = shown ? '' : 'none';
		}
	};

	// The label of a button: the icon (markup of the forum, an option of the page) and a language string.
	const setLabel = (button, icon, key) => {
		button.innerHTML = (Joomla.getOptions(icon) || '') + ' ';
		button.append(text(key));
	};

	// A button which is done: green and, when asked, disabled.
	const markDone = (button, icon, key, disabled = false) => {
		button.classList.remove('btn-primary', 'btn-outline-primary');
		button.classList.add('btn-success');
		button.disabled = disabled;
		setLabel(button, icon, key);
	};

	const fileIdOf = (data) => data?.file_id || data?.result?.data?.id || data?.id;

	// The buttons of a file (in the same group), of one kind or all.
	const siblingsOf = (button, kind) => [...button.parentElement.children]
		.filter((other) => other !== button && other.tagName === 'BUTTON' && (!kind || other.dataset.kunenaAttachment === kind));

	const fileButtons = (kind) => [...document.querySelectorAll('#files button')]
		.filter((button) => !kind || button.dataset.kunenaAttachment === kind);

	const post = (url, fields) => fetch(url, {
		method: 'POST',
		credentials: 'same-origin',
		headers: { Accept: 'application/json' },
		body: fields ? new URLSearchParams(fields) : undefined
	});

	const postJson = async (url, fields) => (await post(url, fields)).json();

	const editorInstance = () => (isCkeditor() ? window.KunenaEditor?.get('message') : null);

	const insertInEditor = (content) => {
		if (isCkeditor()) {
			editorInstance()?.insertText(content);
		} else if (window.sceditor) {
			sceditor.instance(byId('message')).insert(content);
		}
	};

	const editorText = () => {
		if (isCkeditor()) {
			return editorInstance()?.getData() ?? '';
		}

		return window.sceditor ? sceditor.instance(byId('message')).val() : '';
	};

	// The hidden fields of a file, which the form sends: attachments[id] and attachment[id] (its name in the placeholder).
	const addHiddenFields = (id, name) => {
		const list = byId('kattach-list');

		if (!list) {
			return;
		}

		const kept = document.createElement('input');
		const named = document.createElement('input');

		kept.type = 'hidden';
		kept.id = 'kattachs-' + id;
		kept.name = 'attachments[' + id + ']';
		kept.value = '1';
		named.type = 'hidden';
		named.id = 'kattach-' + id;
		named.placeholder = name;
		named.name = 'attachment[' + id + ']';
		named.value = '1';
		list.append(kept, named);
	};

	// The ids of the private files, in the hidden field attachment_private ("1,2,3").
	const addPrivateIds = (ids) => {
		let field = byId('attachment_private');

		if (!field) {
			field = document.createElement('input');
			field.type = 'hidden';
			field.id = 'attachment_private';
			field.name = 'attachment_private';
			byId('kattach-list')?.append(field);
		}

		field.value = [field.value, ...ids].filter((id) => id !== '' && id !== undefined && id !== null).join(',');
	};

	// The files of the list (the hidden fields kattach-<id>): their ids and names.
	const listedFiles = () => [...document.querySelectorAll('#kattach-list input')]
		.filter((input) => !(input.id || '').match('[a-z]{8}'))
		.map((input) => ({ id: ((input.id || '').match('[0-9]{1,8}') || [])[0], name: input.placeholder }))
		.filter((file) => file.id);

	const deleteAttachment = async (fileId) => {
		const response = await post(Joomla.getOptions('com_kunena.kunena_upload_files_rem'), { files_id_delete: fileId });
		let answer = null;

		try {
			answer = await response.json();
		} catch {
			// No JSON: the error below.
		}

		if (!response.ok) {
			const error = new Error('The server refused the removal of the attachment.');

			error.serverMessage = answer?.message;
			throw error;
		}

		return answer;
	};

	// Takes the [attachment=ID] tags of the file out of the editor and leaves every other character of the text as it is.
	const removeAttachmentFromEditor = (fileId) => {
		const tag = new RegExp('\\[attachment=' + fileId + '\\][^[\\]]+\\[/attachment\\]', 'g');

		// CKEditor 5: the tag is taken out of the texts of the editor, the paragraphs and the formatting stay as they are.
		if (isCkeditor()) {
			editorInstance()?.removeText(tag);

			return;
		}

		if (!window.sceditor) {
			return;
		}

		const editor = sceditor.instance(byId('message'));
		const value = editor.val();

		// The tag is not in the text: the editor is not touched.
		if (value.search(tag) === -1) {
			return;
		}

		// The source view holds the BBCode as it is, so the text is written back without a conversion.
		if (editor.sourceMode()) {
			editor.val(value.replace(tag, ''));

			return;
		}

		// Writing the text back to the WYSIWYG view converts it to HTML again, which loses line breaks: the tag is taken out of
		// the text of the document instead.
		const body = editor.getBody();

		body.normalize();

		const walker = body.ownerDocument.createTreeWalker(body, NodeFilter.SHOW_TEXT);

		while (walker.nextNode()) {
			if (walker.currentNode.data.search(tag) !== -1) {
				walker.currentNode.data = walker.currentNode.data.replace(tag, '');
			}
		}
	};

	const removeAttachmentFromList = (fileId) => {
		byId('kattachs-' + fileId)?.remove();
		byId('kattach-' + fileId)?.remove();
		document.querySelectorAll('#kattachments-message-container .attachment-removal-error').forEach((error) => error.remove());

		document.querySelectorAll('#files > div').forEach((item) => {
			const containsAttachment = [...item.querySelectorAll('button')]
				.some((button) => String(fileIdOf(buttonData.get(button))) === String(fileId));

			if (containsAttachment) {
				item.remove();
			}
		});

		fileCount = Math.max(0, fileCount - 1);

		if (fileCount === 0) {
			['insert-all', 'remove-all', 'set-secure-all'].forEach((id) => show(byId(id), false));
		}
	};

	const reportRemovalFailure = (error) => {
		console.error('Failed to remove attachment', error);

		const container = byId('kattachments-message-container');
		const alert = document.createElement('div');

		container?.querySelectorAll('.attachment-removal-error').forEach((old) => old.remove());
		alert.className = 'alert alert-danger attachment-removal-error';
		alert.setAttribute('role', 'alert');
		alert.textContent = error?.serverMessage || text('COM_KUNENA_UPLOADED_LABEL_ERROR_REMOVE_FILE');
		container?.append(alert);

		if (fileCount > 0) {
			show(byId('remove-all'));
		}
	};

	// Removes a file on the server, then from the list and from the text; false when the server did not remove it.
	const removeFile = async (fileId) => {
		try {
			const answer = await deleteAttachment(fileId);

			if (answer?.result !== true) {
				reportRemovalFailure(new Error('The server did not confirm attachment removal.'));

				return false;
			}
		} catch (error) {
			reportRemovalFailure(error);

			return false;
		}

		removeAttachmentFromList(fileId);
		removeAttachmentFromEditor(fileId);

		return true;
	};

	const setInline = (ids) => {
		post(Joomla.getOptions('com_kunena.kunena_upload_files_set_inline') + '&files_id=' + encodeURIComponent(JSON.stringify(ids)))
			.catch(() => {});
	};

	const setPrivate = (ids) => post(Joomla.getOptions('com_kunena.kunena_upload_files_set_private') + '&files_id=' + encodeURIComponent(JSON.stringify(ids)));

	const makeButton = (kind, data, className, icon, key) => {
		const button = document.createElement('button');

		button.type = 'button';
		button.className = className;
		button.dataset.kunenaAttachment = kind;
		setLabel(button, icon, key);
		buttonData.set(button, data);

		return button;
	};

	// "Insert": the tag of the file into the text.
	const insertButton = (data) => {
		const button = makeButton('insert', data, 'btn btn-primary', 'com_kunena.icons.upload', 'COM_KUNENA_EDITOR_INSERT');

		button.addEventListener('click', (e) => {
			e.preventDefault();
			e.stopPropagation();

			if (data.private) {
				return;
			}

			const fileId = fileIdOf(data);
			const filename = data.protected ? (data.hash || data.name) : (data.result?.data?.filename || data.name);

			if (fileId && filename) {
				insertInEditor(' [attachment=' + fileId + ']' + filename + '[/attachment]');
				markDone(button, 'com_kunena.icons.upload', 'COM_KUNENA_EDITOR_IN_MESSAGE');
			}

			// A file in the text is not private.
			siblingsOf(button, 'private').forEach((other) => show(other, false));

			if (fileId) {
				setInline([fileId]);
			}
		});

		return button;
	};

	// "Private": the file is only shown to the users who may see the private message.
	const privateButton = (data) => {
		const button = makeButton('private', data, 'btn btn-primary', 'com_kunena.icons.secure', 'COM_KUNENA_EDITOR_INSERT_PRIVATE_ATTACHMENT');

		button.addEventListener('click', async (e) => {
			e.preventDefault();
			e.stopPropagation();

			const fileId = data.result !== undefined ? data.result.data.id : data.id;

			try {
				const response = await setPrivate([fileId]);

				if (!response.ok) {
					return;
				}
			} catch {
				return;
			}

			markDone(button, 'com_kunena.icons.secure', 'COM_KUNENA_EDITOR_ATTACHMENT_IS_SECURED', true);
			// A private file is not in the text.
			siblingsOf(button, 'insert').forEach((other) => show(other, false));
			addPrivateIds([fileId]);

			// All the files are private: the buttons for all of them are done.
			const allPrivate = fileButtons('private').every((other) => other.disabled);

			show(byId('insert-all'), false);

			if (allPrivate) {
				const secureAll = byId('set-secure-all');

				if (secureAll) {
					markDone(secureAll, 'com_kunena.icons.secure', 'COM_KUNENA_EDITOR_ATTACHMENTS_ARE_SECURED', true);
				}
			} else {
				show(byId('set-secure-all'), false);
			}
		});

		return button;
	};

	// "Remove File": the file is removed on the server, from the list and from the text.
	const removeButton = (data) => {
		const button = makeButton('remove', data, 'btn btn-danger', 'com_kunena.icons.trash', 'COM_KUNENA_GEN_REMOVE_FILE');

		button.addEventListener('click', async (e) => {
			e.preventDefault();
			e.stopPropagation();
			show(byId('klabel_info_drop_browse'));

			const fileId = fileIdOf(data) || 0;

			if (!fileId) {
				console.error('Could not determine file ID');

				return;
			}

			if (await removeFile(fileId)) {
				byId('alert_max_file')?.remove();

				const submit = byId('form_submit_button');

				if (submit) {
					submit.disabled = false;
				}
			}
		});

		return button;
	};

	// The alert of too many files, above the list of the files.
	const tooManyFiles = () => {
		const files = byId('files');
		const alert = document.createElement('div');
		const close = document.createElement('button');

		alert.className = 'alert alert-danger alert-dismissible fade show';
		alert.id = 'alert_max_file';
		alert.setAttribute('role', 'alert');
		alert.textContent = text('COM_KUNENA_UPLOADED_LABEL_ERROR_REACHED_MAX_NUMBER_FILES');
		close.type = 'button';
		close.className = 'btn-close';
		close.dataset.bsDismiss = 'alert';
		close.setAttribute('aria-label', 'Close');
		alert.append(close);
		files?.before(alert);
	};

	const setSubmit = (enabled) => {
		const submit = byId('form_submit_button');

		if (submit) {
			submit.disabled = !enabled;
		}
	};

	// The item of a file in the list: <div><p>[picture]<br><span>name</span><br></p></div>
	const fileItem = (name) => {
		const item = document.createElement('div');
		const paragraph = document.createElement('p');
		const span = document.createElement('span');

		span.textContent = name;
		paragraph.append(span, document.createElement('br'));
		item.append(paragraph);

		return item;
	};

	const removeAll = async (e) => {
		e.preventDefault();

		show(byId('progress'), false);

		const insertAll = byId('insert-all');

		if (insertAll) {
			insertAll.classList.remove('btn-success');
			insertAll.classList.add('btn-outline-primary');
			setLabel(insertAll, 'com_kunena.icons.upload', 'COM_KUNENA_UPLOADED_LABEL_INSERT_ALL_BUTTON');
		}

		['remove-all', 'insert-all', 'set-secure-all'].forEach((id) => show(byId(id), false));

		// The files of the text, then the files of the list which are not in the text
		const attachmentRegex = /\[attachment=([0-9]+)\]([^[\]]+)\[\/attachment\]/g;
		const attachments = [];
		const content = editorText();
		let match;

		while ((match = attachmentRegex.exec(content)) !== null) {
			attachments.push({ id: parseInt(match[1], 10), filename: match[2] });
		}

		fileButtons().forEach((button) => {
			const data = buttonData.get(button);
			const fileId = Number(data?.result?.data?.id || data?.file_id);

			if (Number.isInteger(fileId) && fileId > 0 && !attachments.some((a) => Number(a.id) === fileId)) {
				attachments.push({ id: fileId, filename: data.result?.data?.filename || data.name });
			}
		});

		byId('alert_max_file')?.remove();

		// One after the other: a refused removal stops the others.
		for (const attachment of attachments) {
			if (!await removeFile(attachment.id)) {
				return;
			}
		}

		if (attachments.length > 0) {
			byId('files')?.replaceChildren();
			fileCount = 0;
			fileeditinline = 0;
		}
	};

	const setSecureAll = async (e) => {
		e.preventDefault();

		const secureAll = e.currentTarget;
		const ids = listedFiles().map((file) => file.id);

		if (ids.length === 0) {
			return;
		}

		try {
			const response = await setPrivate(ids);

			if (!response.ok) {
				return;
			}
		} catch {
			return;
		}

		fileButtons('private').forEach((button) => {
			markDone(button, 'com_kunena.icons.secure', 'COM_KUNENA_EDITOR_ATTACHMENT_IS_SECURED', true);
		});
		markDone(secureAll, 'com_kunena.icons.secure', 'COM_KUNENA_EDITOR_ATTACHMENTS_ARE_SECURED', true);

		// Private files are not in the text.
		fileButtons('insert').forEach((button) => show(button, false));
		show(byId('insert-all'), false);
		addPrivateIds(ids);
	};

	const insertAll = (e) => {
		e.preventDefault();

		const filesId = [];
		let content = '';
		// The list of the form has the files of the edited message too (preload()), so it gives every file once; a file which is
		// already in the text is not put in again. The files of the edited message were added a second time after this list, so
		// each of them came twice into the text.
		const text = editorText();

		listedFiles().forEach((file) => {
			if (new RegExp('\\[attachment=' + file.id + '\\]').test(text)) {
				return;
			}

			content += '[attachment=' + file.id + ']' + file.name + '[/attachment]';

			fileButtons('insert')
				.filter((button) => button.classList.contains('btn-primary') && String(fileIdOf(buttonData.get(button))) === String(file.id))
				.forEach((button) => {
					markDone(button, 'com_kunena.icons.upload', 'COM_KUNENA_EDITOR_IN_MESSAGE');
					siblingsOf(button, 'private').forEach((other) => show(other, false));
				});

			filesId.push(file.id);
		});

		markDone(e.currentTarget, 'com_kunena.icons.upload', 'COM_KUNENA_EDITOR_IN_MESSAGE');
		// The files are in the text, so they are not private.
		show(byId('set-secure-all'), false);

		if (byId('message')) {
			insertInEditor(content);
		}

		setInline(filesId);
	};

	// The files of the message which is edited, with their state (in the text, private).
	const preload = async () => {
		const messageId = parseInt(byId('kmessageid')?.value, 10);

		if (!(messageId > 0)) {
			return;
		}

		let answer;

		try {
			answer = await postJson(Joomla.getOptions('com_kunena.kunena_upload_files_preload'), { mes_id: messageId });
		} catch {
			return;
		}

		const files = answer?.files ? Object.values(answer.files) : [];

		if (files.length === 0) {
			return;
		}

		fileCount = files.length;

		let allPrivate = true;
		let hasInlineAttachments = false;

		files.forEach((file) => {
			const isPrivate = Boolean(file.private) || file.protected === 32;
			const isProtected = file.protected === 1 || file.protected === 32;
			const item = fileItem(file.name);
			const paragraph = item.firstElementChild;

			// The picture of an image, or the icon of a file
			if (file.image) {
				const img = document.createElement('img');

				img.alt = '';
				img.width = 100;
				img.height = 100;
				img.src = file.path;
				paragraph.prepend(img, document.createElement('br'));
			} else {
				const icon = document.createElement('span');

				icon.innerHTML = Joomla.getOptions('com_kunena.icons.attach') || '';
				paragraph.prepend(...icon.childNodes, document.createElement('br'));
			}

			const data = {
				file_id: file.id,
				uploaded: true,
				name: file.name,
				hash: file.hash,
				inline: Boolean(file.inline),
				private: isPrivate,
				protected: isProtected,
				result: { data: { id: file.id, filename: isProtected ? file.hash : file.name } }
			};

			const group = document.createElement('div');
			const insert = insertButton(data);

			group.className = 'btn-group mt-2';

			if (isPrivate) {
				show(insert, false);
			} else if (file.inline) {
				markDone(insert, 'com_kunena.icons.upload', 'COM_KUNENA_EDITOR_IN_MESSAGE');
				hasInlineAttachments = true;
			}

			group.append(insert);

			if (privateMessages()) {
				const privateBtn = privateButton(data);

				if (isPrivate) {
					markDone(privateBtn, 'com_kunena.icons.secure', 'COM_KUNENA_EDITOR_ATTACHMENT_IS_SECURED', true);
				} else if (file.inline) {
					show(privateBtn, false);
				}

				group.append(privateBtn);
			}

			group.append(removeButton(data));
			item.append(group);
			byId('files')?.append(item);

			// The hash for a protected file, the name otherwise
			addHiddenFields(file.id, isProtected ? file.hash : file.name);

			if (file.inline) {
				fileeditinline++;
			}

			if (!isPrivate) {
				allPrivate = false;
			}
		});

		show(byId('remove-all'));
		// Insert all: only when no file is private nor in the text yet
		show(byId('insert-all'), !allPrivate && !hasInlineAttachments);

		if (privateMessages()) {
			const secureAll = byId('set-secure-all');

			if (allPrivate && secureAll) {
				markDone(secureAll, 'com_kunena.icons.secure', 'COM_KUNENA_EDITOR_ATTACHMENTS_ARE_SECURED', true);
				show(secureAll);
			} else if (!hasInlineAttachments) {
				show(secureAll);
			}
		}
	};

	const init = () => {
		byId('remove-all')?.addEventListener('click', removeAll);
		byId('set-secure-all')?.addEventListener('click', setSecureAll);
		byId('insert-all')?.addEventListener('click', insertAll);

		const input = byId('fileupload');

		if (!input || !window.KunenaUploader) {
			return;
		}

		// The uploads (uploader.js): a file which is chosen or dropped is sent at once, the list shows it with its buttons.
		KunenaUploader.attach(input, {
			url: () => byId('kunena_upload_files_url')?.value,
			params: (file) => ({
				// The category chosen in the form (a new topic), else the one of the message: the rights of the upload were those of
				// the category in which the form opened, and a file was refused in a category which allows it.
				catid: byId('postcatid')?.value || byId('kunena_upload')?.value || '',
				filename: file.name,
				size: file.size,
				mime: file.type
			}),
			// An image larger than the images of the forum is made smaller before it is sent.
			resize: {
				maxWidth: Joomla.getOptions('com_kunena.imageWidth'),
				maxHeight: Joomla.getOptions('com_kunena.imageHeight')
			},
			previewWidth: 100,
			previewHeight: 100,
			accept: (files) => {
				setSubmit(false);
				show(byId('remove-all'));
				show(byId('insert-all'));

				if (privateMessages()) {
					show(byId('set-secure-all'));
				}

				show(byId('kattach_form'));

				const fileCountTotal = files.length + fileCount;
				// "Limit Attachments" 0 is no limit, as on the server: every file was "too many".
				const maxFiles = Number(Joomla.getOptions('com_kunena.kunena_upload_files_maxfiles')) || 0;

				if (maxFiles > 0 && fileCountTotal > maxFiles) {
					tooManyFiles();
					setSubmit(true);

					return false;
				}

				fileCount = fileCountTotal;
				pending += files.length;
			},
			add: (file) => {
				const bar = byId('progress-bar');

				if (bar) {
					bar.style.width = '0%';
				}

				show(byId('progress'));

				const item = fileItem(file.name);

				byId('files')?.append(item);

				return item;
			},
			preview: (canvas, file, item) => {
				item.firstElementChild.prepend(canvas, document.createElement('br'));
			},
			progress: (loaded, total) => {
				const progress = total ? Math.floor(loaded / total * 100) : 0;

				document.querySelectorAll('.progress-bar').forEach((bar) => {
					bar.style.width = progress + '%';
					bar.setAttribute('aria-valuenow', progress);
				});
			},
			done: (result, item) => {
				const span = item.querySelector('span');

				const last = finished();

				if (result.success === true) {
					setSubmit(last);

					// The name links to the file which was sent (an answer without its address: no link to "null").
					if (span && result.location) {
						const link = document.createElement('a');

						link.target = '_blank';
						link.href = result.location;
						span.replaceWith(link);
						link.append(span);
					}

					addHiddenFields(result.data.id, result.data.filename);

					const group = document.createElement('div');
					const data = { result };

					group.className = 'btn-group mt-2';
					group.append(insertButton(data));

					if (privateMessages()) {
						group.append(privateButton(data));
					}

					group.append(removeButton(data));
					item.append(group);
				} else {
					// A file which the server refused is no file of the message: it does not count against the limit (with a
					// limit of one file, the file after a refused one was "too many").
					fileCount = Math.max(0, fileCount - 1);

					// A refusal without a message enables the form too (it stayed disabled for good).
					setSubmit(last);

					if (result.message) {
						const error = document.createElement('div');

						error.className = 'alert alert-danger';
						error.setAttribute('role', 'alert');
						error.textContent = result.message;
						span?.append(document.createElement('br'), error);
					}
				}
			},
			fail: (message, item) => {
				const error = document.createElement('span');

				// No answer: the file is not on the server, so it is no file of the message either.
				fileCount = Math.max(0, fileCount - 1);
				setSubmit(finished());
				error.className = 'text-danger';
				error.textContent = message || 'Upload failed';
				item.firstElementChild.append(document.createElement('br'), error);
			}
		});

		preload();
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
