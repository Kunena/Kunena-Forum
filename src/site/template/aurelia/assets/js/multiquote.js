/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

/*
 * Multi-quote: the button Multi-quote of each message adds it to (or takes it out of) the quotes of the reply; a bar shows how many
 * messages are chosen, with a button which opens the reply form with all of them quoted (the parameter quotes) and a button which
 * clears the choice. The choice is kept per topic in the session of the browser tab, also on the other pages of the topic.
 */
document.addEventListener('DOMContentLoaded', () => {
	const root = document.querySelector('[data-kmultiquote-url]');

	if (!root) {
		return;
	}

	const url = root.dataset.kmultiquoteUrl;
	const key = 'kunena.multiquote.' + root.dataset.kmultiquoteTopic;
	const max = parseInt(root.dataset.kmultiquoteMax || '10', 10);
	const text = (name) => (window.Joomla && Joomla.Text ? Joomla.Text._(name) : name);

	const read = () => {
		try {
			const value = JSON.parse(window.sessionStorage.getItem(key) || '[]');

			return Array.isArray(value) ? value.map(Number).filter((id) => id > 0).slice(0, max) : [];
		} catch (e) {
			return [];
		}
	};

	const write = (ids) => {
		try {
			if (ids.length) {
				window.sessionStorage.setItem(key, JSON.stringify(ids));
			} else {
				window.sessionStorage.removeItem(key);
			}
		} catch (e) {
			// No storage (a private window): the choice lasts as long as the page.
		}
	};

	let ids = read();

	// The bar: the reply with the quotes, and the button which clears them. It is announced when the number changes.
	const bar = document.createElement('div');
	bar.className = 'kmultiquote-bar';
	bar.hidden = true;
	bar.setAttribute('role', 'region');
	bar.setAttribute('aria-label', text('COM_KUNENA_MULTIQUOTE'));

	const status = document.createElement('span');
	status.className = 'visually-hidden';
	status.setAttribute('aria-live', 'polite');

	const reply = document.createElement('a');
	reply.className = 'btn btn-primary kmultiquote-reply';
	reply.rel = 'nofollow';

	const clear = document.createElement('button');
	clear.type = 'button';
	clear.className = 'btn btn-outline-primary border kmultiquote-clear';
	clear.textContent = text('COM_KUNENA_MULTIQUOTE_CLEAR');

	bar.append(status, reply, clear);
	root.append(bar);

	const render = (announce) => {
		document.querySelectorAll('.kmultiquote[data-kmultiquote]').forEach((button) => {
			const chosen = ids.includes(Number(button.dataset.kmultiquote));

			button.setAttribute('aria-pressed', chosen ? 'true' : 'false');
			button.classList.toggle('active', chosen);
		});

		const label = text(ids.length === 1 ? 'COM_KUNENA_MULTIQUOTE_REPLY_1' : 'COM_KUNENA_MULTIQUOTE_REPLY').replace('%d', String(ids.length));

		bar.hidden = ids.length === 0;
		reply.textContent = label;
		reply.href = url + (url.includes('?') ? '&' : '?') + 'quotes=' + ids.join(',');

		if (announce) {
			status.textContent = ids.length ? label : text('COM_KUNENA_MULTIQUOTE_CLEARED');
		}
	};

	document.addEventListener('click', (event) => {
		const button = event.target.closest('.kmultiquote[data-kmultiquote]');

		if (!button) {
			return;
		}

		event.preventDefault();

		const id = Number(button.dataset.kmultiquote);

		if (ids.includes(id)) {
			ids = ids.filter((other) => other !== id);
		} else if (ids.length < max) {
			ids = ids.concat(id);
		}

		write(ids);
		render(true);
	});

	clear.addEventListener('click', () => {
		ids = [];
		write(ids);
		render(true);
	});

	// The reply form gets the quotes: the choice starts again.
	reply.addEventListener('click', () => write([]));

	render(false);
});
