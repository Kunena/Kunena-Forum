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

	const max_avatar = 1;

	const userId = () => document.getElementById('kunena_userid')?.value || '';

	// A POST of form fields to the server.
	const post = (url, fields = {}) => fetch(url, {
		method: 'POST',
		credentials: 'same-origin',
		headers: { Accept: 'application/json' },
		body: new URLSearchParams(fields)
	});

	// The button which removes the avatar of the user (the server takes the user of the session), in the item of the avatar.
	const removeButton = (userid, filename) => {
		const button = document.createElement('button');

		button.type = 'button';
		button.className = 'btn btn-danger';
		// The icon is markup of the forum (KunenaIcons::delete()), the text a language string.
		button.innerHTML = Joomla.getOptions('com_kunena.avatar_delete') + ' ';
		button.append(Joomla.Text._('COM_KUNENA_GEN_REMOVE_AVATAR'));

		button.addEventListener('click', async () => {
			const url = Joomla.getOptions('com_kunena.avatar_remove_url') + '&userid=' + encodeURIComponent(userid) + '&avatar=' + encodeURIComponent(filename);

			try {
				const response = await post(url);

				if (response.ok) {
					button.parentElement?.remove();
				}
			} catch {
				// The avatar stays: the network or the server failed.
			}
		});

		return button;
	};

	// The item of an avatar: <div><p>[picture]<br><span>name</span><br></p></div>
	const item = (name) => {
		const context = document.createElement('div');
		const paragraph = document.createElement('p');
		const span = document.createElement('span');

		span.textContent = name;
		paragraph.append(span, document.createElement('br'));
		context.append(paragraph);

		return context;
	};

	const setProgress = (percent) => {
		document.querySelectorAll('#progress .bar').forEach((bar) => {
			bar.style.width = percent + '%';
		});
	};

	const enableSubmit = () => {
		const submit = document.getElementById('form_submit_button');

		if (submit) {
			submit.disabled = false;
		}
	};

	const init = () => {
		const files = document.getElementById('files');
		const input = document.getElementById('fileupload');

		if (!files || !input) {
			return;
		}

		// The upload of the avatar (uploader.js): the file is sent as soon as it is chosen or dropped; one avatar, so one file.
		KunenaUploader.attach(input, {
			url: Joomla.getOptions('com_kunena.avatar_upload_url'),
			params: (file) => ({
				userid: userId(),
				filename: file.name,
				size: file.size,
				mime: file.type
			}),
			// A photo larger than this is made smaller before it is sent (the forum makes the sizes of the avatars itself).
			resize: { maxWidth: 1920, maxHeight: 1080 },
			previewWidth: 100,
			previewHeight: 100,
			accept: (chosen) => chosen.slice(0, max_avatar),
			add: (file) => {
				setProgress(0);
				files.replaceChildren();

				const context = item(file.name);

				files.append(context);

				return context;
			},
			preview: (canvas, file, context) => {
				context.firstElementChild.prepend(canvas, document.createElement('br'));
			},
			progress: (loaded, total) => {
				setProgress(total ? Math.floor(loaded / total * 100) : 0);
			},
			done: (result, context, file) => {
				const span = context.querySelector('span');

				if (result.location && span) {
					const link = document.createElement('a');

					link.target = '_blank';
					link.href = result.location;
					span.replaceWith(link);
					link.append(span);
				}

				context.querySelectorAll('button.btn-danger').forEach((button) => button.remove());

				// "Remove avatar" for an avatar which was saved only: the task removes the avatar of the user, whatever its name,
				// so the button of a refused upload (a file too large) deleted his current avatar, which the list no longer showed.
				if (result.success === true) {
					context.append(removeButton(userId(), file.name));
				}

				if (result.success === true) {
					enableSubmit();
				} else if (result.message) {
					enableSubmit();

					const error = document.createElement('div');

					error.className = 'alert alert-danger';
					error.setAttribute('role', 'alert');
					error.textContent = result.message;
					span?.append(document.createElement('br'), error);
				}
			},
			fail: (message, context) => {
				const error = document.createElement('span');

				error.className = 'text-danger';
				error.textContent = message;
				context.firstElementChild.append(document.createElement('br'), error);
			}
		});

		// The avatar which the user has now.
		if (parseInt(userId(), 10) > 0) {
			(async () => {
				let data;

				try {
					const response = await post(Joomla.getOptions('com_kunena.avatar_preload_url'), { userid: userId() });
					data = await response.json();
				} catch {
					return;
				}

				if (!data || !data.path) {
					return;
				}

				const context = item(data.name ?? '');
				const img = document.createElement('img');

				img.alt = '';
				img.src = data.path;
				img.width = 100;
				img.height = 100;
				context.firstElementChild.prepend(img, document.createElement('br'));
				context.append(removeButton(userId(), data.name ?? ''));
				files.append(context);
			})();
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
