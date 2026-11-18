/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

(function () {
	'use strict';

	const byId = (id) => document.getElementById(id);

	// An option of the page which holds JSON (a text), or null.
	const jsonOption = (name) => {
		const value = Joomla.getOptions(name);

		if (value === undefined || value === null) {
			return null;
		}

		try {
			return typeof value === 'string' ? JSON.parse(value) : value;
		} catch {
			return null;
		}
	};

	const hasCkeditor = () => Joomla.getOptions('com_kunena.ckeditor_config') !== undefined;
	const messageEditor = () => window.KunenaEditor?.get('message');

	// A POST of form fields, answered with JSON.
	const postJson = async (url, fields) => {
		const response = await fetch(url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { Accept: 'application/json' },
			body: new URLSearchParams(fields)
		});

		return response.json();
	};

	// The users which can be mentioned: a promise of the list (the editor asks for it while @name is typed).
	const dataFeed = async (query) => {
		let url = Joomla.getOptions('com_kunena.kunena_url_topic_get_mentions');

		// A new topic has no users yet: the users are searched with the typed text, for the category.
		if (Joomla.getOptions('com_kunena.mentions_search')) {
			const catid = byId('postcatid')?.value || byId('poll_catid')?.value || '';

			url += '&catid=' + encodeURIComponent(catid) + '&q=' + encodeURIComponent(query || '');
		}

		try {
			const response = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
			let users = await response.json();

			// The server sends the list as a JSON text inside the JSON.
			if (typeof users === 'string') {
				users = JSON.parse(users);
			}

			const text = String(query || '').toLowerCase();

			return (Array.isArray(users) ? users : [])
				.filter((user) => String(user.username || '').toLowerCase().includes(text))
				.slice(0, 10);
		} catch (error) {
			console.error('Request Failed: ' + error);

			return [];
		}
	};

	// The topic icons of a category, the same markup as the layout of the form. The chosen icon stays chosen when the icons of
	// the new category have it.
	const showTopicIcons = (icons) => {
		const checked = document.querySelector('input[name="topic_emoticon"]:checked')?.value;
		const icontype = Joomla.getOptions('com_kunena.kunena_topicicontype');
		const iconLabels = Joomla.getOptions('com_kunena.topicIconLabels');
		const list = document.createElement('div');

		list.id = 'iconset_topicList';
		byId('iconset_topicList')?.remove();
		byId('iconset_inject')?.append(list);

		Object.values(icons || {}).forEach((value, index) => {
			const id = 'radio' + value.id;
			const input = document.createElement('input');
			const label = document.createElement('label');
			const empty = document.createElement('label');

			input.type = 'radio';
			input.id = id;
			input.name = 'topic_emoticon';
			input.value = value.id;
			label.className = 'radio inline';
			label.htmlFor = id;
			empty.htmlFor = id;

			if (icontype === 'svg') {
				// The SVG of the icon set of the forum.
				const svg = document.createElement('template');

				svg.innerHTML = String(value.svg || '').trim();
				label.append(svg.content);
			} else if (icontype === 'fa') {
				const i = document.createElement('i');

				i.className = 'fa fa-' + value.fa + ' glyphicon-topic fa-2x';
				label.append(i);
			} else {
				const img = document.createElement('img');

				img.loading = 'lazy';
				img.src = value.path;
				img.alt = value.name;
				img.style.border = '0';
				label.append(img);
			}

			if (iconLabels) {
				const span = document.createElement('span');

				span.className = 'd-block small text-center';
				span.textContent = value.label;
				label.append(span);
			}

			// A space between the icons, as between the lines of the layout
			if (index > 0) {
				list.append(document.createTextNode(' '));
			}

			list.append(empty, input, label);
		});

		const radios = [...list.querySelectorAll('input[name="topic_emoticon"]')];
		const chosen = radios.find((radio) => radio.value === checked) || radios[0];

		if (chosen) {
			chosen.checked = true;
		}
	};

	const init = () => {
		const submitButton = byId('form_submit_button');
		const pollcategoriesid = jsonOption('com_kunena.pollcategoriesid');
		const pollexist = byId('poll_exist_edit');
		const pollcatid = byId('poll_catid')?.value;
		// The poll of the edited topic may not be edited: the button only shows it, the save checks it again
		const pollEditRefused = Joomla.getOptions('com_kunena.pollEditRefused');
		// The categories in which a message is anonymous by default
		const arrayanynomousbox = jsonOption('com_kunena.arrayanynomousbox');
		const templateModal = byId('modal_confirm_template_category');
		// The template text of the category which was chosen last
		let categoryTemplate = '';

		if (arrayanynomousbox && arrayanynomousbox[byId('postcatid')?.value] == 1 && byId('kanonymous')) {
			byId('kanonymous').checked = true;
		}

		/* "Recover the form" of the template: what is written in the form is kept in the browser until it is sent (form-recover.js) */
		if (window.KunenaFormRecover !== undefined) {
			window.KunenaFormRecover.protect(byId('postform'), { timeout: 5 });
		}

		submitButton?.addEventListener('click', () => {
			byId('subject')?.setAttribute('required', 'required');
			byId('editor')?.setAttribute('required', 'required');
			localStorage.removeItem('copyKunenaeditor');
		});

		// The question about the template text of the category: a native <dialog> (dialog.js).
		const showTemplateModal = (shown) => {
			if (!(templateModal instanceof HTMLDialogElement)) {
				return;
			}

			if (shown && !templateModal.open) {
				templateModal.showModal();
			} else if (!shown && templateModal.open) {
				templateModal.close();
			}
		};

		byId('postcatid')?.addEventListener('change', async (e) => {
			const catid = e.target.value;

			// The poll button: only in a category with polls, and not when the poll cannot be changed any more.
			if (hasCkeditor()) {
				let polls = false;

				if (pollEditRefused !== undefined) {
					polls = false;
				} else if (pollcategoriesid && !pollexist) {
					polls = pollcategoriesid[catid] !== undefined;
				} else {
					polls = !!pollexist;
				}

				messageEditor()?.setCommandEnabled('polls', polls);
			}

			const anonymous = byId('kanynomous-check');

			if (anonymous && arrayanynomousbox) {
				const shown = arrayanynomousbox[catid] !== undefined;

				anonymous.style.display = shown ? '' : 'none';

				if (byId('kanonymous')) {
					byId('kanonymous').checked = shown;
				}
			}

			// Load topic icons by ajax request
			const iconsUrl = byId('kurl_topicons_request')?.value;

			if (iconsUrl) {
				postJson(iconsUrl, { catid }).then(showTopicIcons).catch(() => {});
			}

			// The template text of the category: into an empty editor, or after the question when the editor has a text
			const templateUrl = byId('kurl_category_template_text')?.value;

			if (!templateUrl || !hasCkeditor()) {
				return;
			}

			try {
				const answer = await postJson(templateUrl, { catid });

				categoryTemplate = typeof answer?.data === 'string' ? answer.data : '';
			} catch {
				return;
			}

			if (categoryTemplate === '') {
				return;
			}

			if ((messageEditor()?.getData() ?? '').length > 1) {
				showTemplateModal(true);
			} else {
				messageEditor()?.setData(categoryTemplate);
			}
		});

		byId('modal_confirm_erase')?.addEventListener('click', () => {
			showTemplateModal(false);
			messageEditor()?.setData(categoryTemplate);
		});

		byId('modal_confirm_erase_keep_old')?.addEventListener('click', () => {
			showTemplateModal(false);

			const existing = messageEditor()?.getData() ?? '';

			messageEditor()?.setData(categoryTemplate + ' ' + existing);
		});

		byId('gotoeditor')?.addEventListener('click', () => {
			localStorage.setItem('copyKunenaeditor', byId('editor')?.value ?? '');
		});

		if (!hasCkeditor() || !window.KunenaEditor) {
			return;
		}

		// The submit button: enabled when one of the editors has a text.
		const updateSubmit = () => {
			const length = ['message', 'message_private'].reduce((total, id) => {
				const instance = window.KunenaEditor.get(id);

				return total + (instance ? instance.getData().length : 0);
			}, 0);

			if (submitButton) {
				submitButton.disabled = length === 0;
			}
		};

		// The poll button: only in a category with polls, and not when the poll cannot be changed any more.
		const updatePollButton = (instance) => {
			if (pollEditRefused !== undefined) {
				instance.setCommandEnabled('polls', false);
				instance.editor.ui.view.element.querySelectorAll('[data-kunena-button="polls"]').forEach((button) => {
					button.setAttribute('title', pollEditRefused);
					button.setAttribute('aria-describedby', 'poll-edit-refused');
				});
			} else if (pollcatid !== undefined && pollcategoriesid && !pollexist) {
				instance.setCommandEnabled('polls', pollcategoriesid[byId('kcategory_poll')?.value] !== undefined);
			} else {
				instance.setCommandEnabled('polls', !!pollexist);
			}
		};

		// A text which was copied from the quick reply to the full editor.
		const restoreCopy = (instance) => {
			const copy = localStorage.getItem('copyKunenaeditor');

			if (copy) {
				instance.setData(copy);
				localStorage.removeItem('copyKunenaeditor');
			}
		};

		window.KunenaEditor.create('message', { mentions: dataFeed }).then((instance) => {
			restoreCopy(instance);
			updatePollButton(instance);
			updateSubmit();
			instance.on('change', updateSubmit);
		});

		if (byId('message_private')) {
			// The text of the quick reply goes into the message only: the private part (its own tab) took it when it was ready first.
			window.KunenaEditor.create('message_private').then((instance) => {
				instance.setCommandEnabled('polls', false);
				updateSubmit();
				instance.on('change', updateSubmit);
			});
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
