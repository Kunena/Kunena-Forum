/**
 * Kunena Component
 * @package     Kunena.Media
 *
 * @copyright   Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

/**
 * The names of the users while a name is typed in a field (the user field of the search, the search of the user list): the list
 * of the server (task user.getusersmentionssearch: up to 10 objects {name}), under the field.
 *
 *   KunenaUserAutocomplete.attach(input, {url, minLength})
 *
 * The field is a combobox for a screen reader: the arrows go through the names, Enter takes the name which is chosen, Escape closes
 * the list, a click takes a name. The list is a dropdown of Bootstrap (.dropdown-menu), which the templates already show.
 */
(function () {
	'use strict';

	let count = 0;

	const attach = (input, options = {}) => {
		if (!(input instanceof HTMLInputElement) || input.dataset.kunenaAutocomplete || !options.url) {
			return;
		}

		const minLength = options.minLength || 2;
		const list = document.createElement('ul');
		let names = [];
		let active = -1;
		let timer = 0;
		let request = null;

		count += 1;
		list.id = `kunena-autocomplete-${count}`;
		list.className = 'dropdown-menu kunena-autocomplete';
		list.setAttribute('role', 'listbox');
		list.hidden = true;
		list.style.position = 'absolute';
		list.style.zIndex = '1080';
		list.style.maxHeight = '16rem';
		list.style.overflowY = 'auto';

		input.dataset.kunenaAutocomplete = '1';
		input.setAttribute('role', 'combobox');
		input.setAttribute('autocomplete', 'off');
		input.setAttribute('aria-autocomplete', 'list');
		input.setAttribute('aria-controls', list.id);
		input.setAttribute('aria-expanded', 'false');
		input.after(list);

		if (getComputedStyle(input.parentElement).position === 'static') {
			input.parentElement.style.position = 'relative';
		}

		const close = () => {
			list.hidden = true;
			list.classList.remove('show');
			input.setAttribute('aria-expanded', 'false');
			input.removeAttribute('aria-activedescendant');
			active = -1;
		};

		const highlight = (index) => {
			const items = [...list.children];

			active = index;
			items.forEach((item, position) => {
				const on = position === index;

				item.firstElementChild.classList.toggle('active', on);
				item.setAttribute('aria-selected', String(on));
			});

			if (index >= 0) {
				input.setAttribute('aria-activedescendant', items[index].id);
				items[index].scrollIntoView({ block: 'nearest' });
			} else {
				input.removeAttribute('aria-activedescendant');
			}
		};

		const choose = (index) => {
			if (!names[index]) {
				return;
			}

			input.value = names[index];
			close();
			input.dispatchEvent(new Event('change', { bubbles: true }));
			input.focus();
		};

		const show = (found) => {
			names = found;
			list.replaceChildren(...found.map((name, index) => {
				const item = document.createElement('li');
				const button = document.createElement('button');

				item.id = `${list.id}-${index}`;
				item.setAttribute('role', 'option');
				item.setAttribute('aria-selected', 'false');
				button.type = 'button';
				button.className = 'dropdown-item';
				button.tabIndex = -1;
				button.textContent = name;
				// A press of the mouse would take the focus from the field (and close the list) before the click.
				button.addEventListener('mousedown', (event) => event.preventDefault());
				button.addEventListener('click', () => choose(index));
				item.append(button);

				return item;
			}));

			if (!found.length) {
				close();

				return;
			}

			list.style.top = `${input.offsetTop + input.offsetHeight}px`;
			list.style.left = `${input.offsetLeft}px`;
			list.style.minWidth = `${input.offsetWidth}px`;
			list.hidden = false;
			list.classList.add('show');
			input.setAttribute('aria-expanded', 'true');
			active = -1;
		};

		const search = () => {
			const text = input.value.trim();

			if (request) {
				request.abort();
			}

			if (text.length < minLength) {
				close();

				return;
			}

			// Only the answer of the last search is shown: an older one which comes later is dropped.
			request = new AbortController();
			fetch(`${options.url}${options.url.includes('?') ? '&' : '?'}usersearch=${encodeURIComponent(text)}`, {
				credentials: 'same-origin',
				headers: { Accept: 'application/json' },
				signal: request.signal,
			})
				.then((response) => (response.ok ? response.json() : []))
				.then((users) => {
					const found = (Array.isArray(users) ? users : [])
						.map((user) => (user && typeof user.name === 'string' ? user.name : ''))
						.filter((name, index, all) => name !== '' && all.indexOf(name) === index);

					if (document.activeElement === input) {
						show(found);
					}
				})
				.catch((error) => {
					if (error.name !== 'AbortError') {
						close();
					}
				});
		};

		input.addEventListener('input', () => {
			window.clearTimeout(timer);
			timer = window.setTimeout(search, 250);
		});

		input.addEventListener('keydown', (event) => {
			const open = !list.hidden && names.length > 0;

			if (event.key === 'ArrowDown' && open) {
				event.preventDefault();
				highlight(active < names.length - 1 ? active + 1 : 0);
			} else if (event.key === 'ArrowUp' && open) {
				event.preventDefault();
				highlight(active > 0 ? active - 1 : names.length - 1);
			} else if (event.key === 'Enter' && open && active >= 0) {
				// The name is taken; the form is sent with the next Enter.
				event.preventDefault();
				choose(active);
			} else if (event.key === 'Escape' && open) {
				event.preventDefault();
				close();
			}
		});

		input.addEventListener('blur', close);
	};

	window.KunenaUserAutocomplete = { attach };
})();
