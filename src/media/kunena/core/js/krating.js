/**
 * Kunena Component
 * @package     Kunena.Media
 *
 * @copyright   Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

/**
 * The rating of a topic: five stars on the page of the topic (#krating), which show the rating and set the rating of the user.
 *
 *   KunenaRating.create(list, {value, max, onRate})   makes the stars in a <ul>, returns {get(), set(value)}
 *
 * The stars are a group of radio buttons for a screen reader and the keyboard: the arrows move between them (and show the rating
 * they would give), Enter or Space gives it, as a click does. The mouse shows the rating under it and puts the rating back when it
 * leaves. The look is the one of rating.css (.c-rating, .c-rating__item, .is-active).
 */
(function () {
	'use strict';

	const text = (key, fallback) => {
		const value = window.Joomla && window.Joomla.Text ? window.Joomla.Text._(key, '') : '';

		return value && value !== key ? value : fallback;
	};

	const create = (list, options = {}) => {
		const max = Math.max(1, parseInt(options.max, 10) || 5);
		const clamp = (value) => Math.min(max, Math.max(0, Math.round(Number(value) || 0)));
		let value = clamp(options.value);
		const stars = [];

		list.classList.add('c-rating');
		list.setAttribute('role', 'radiogroup');
		list.setAttribute('aria-label', text('COM_KUNENA_RATING_LABEL', 'Rate this topic'));

		// The stars up to a number are lit: the rating, or the one under the mouse or the keyboard.
		const show = (count) => stars.forEach((star, index) => star.classList.toggle('is-active', index < count));

		// The star of the rating is the one the keyboard reaches (the first one without a rating), as in a group of radio buttons.
		const mark = () => stars.forEach((star, index) => {
			const checked = index + 1 === value;

			star.setAttribute('aria-checked', String(checked));
			star.tabIndex = checked || (value === 0 && index === 0) ? 0 : -1;
		});

		const set = (next) => {
			value = clamp(next);
			show(value);
			mark();
		};

		const choose = (count) => {
			set(count);

			if (typeof options.onRate === 'function') {
				options.onRate(count);
			}
		};

		for (let index = 0; index < max; index++) {
			const star = document.createElement('li');

			star.className = 'c-rating__item';
			star.dataset.index = String(index);
			star.setAttribute('role', 'radio');
			star.setAttribute('aria-label', text('COM_KUNENA_RATING_STAR', '{star} of {total}').replace('{star}', String(index + 1)).replace('{total}', String(max)));
			star.addEventListener('mouseenter', () => show(index + 1));
			star.addEventListener('click', (event) => {
				event.preventDefault();
				choose(index + 1);
			});
			stars.push(star);
			list.append(star);
		}

		list.addEventListener('mouseleave', () => show(value));
		list.addEventListener('focusout', (event) => {
			if (!list.contains(event.relatedTarget)) {
				show(value);
			}
		});
		list.addEventListener('keydown', (event) => {
			const current = stars.indexOf(document.activeElement);

			if (current === -1) {
				return;
			}

			const rtl = getComputedStyle(list).direction === 'rtl';
			const moves = { ArrowRight: rtl ? -1 : 1, ArrowUp: 1, ArrowLeft: rtl ? 1 : -1, ArrowDown: -1 };
			let target = null;

			if (event.key in moves) {
				target = Math.min(max - 1, Math.max(0, current + moves[event.key]));
			} else if (event.key === 'Home') {
				target = 0;
			} else if (event.key === 'End') {
				target = max - 1;
			} else if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				choose(current + 1);

				return;
			} else {
				return;
			}

			event.preventDefault();
			stars[target].focus();
			show(target + 1);
		});

		set(value);

		return { get: () => value, set };
	};

	// A message above the forum (the messages of Joomla), as text.
	const notify = (success, message, title) => {
		const container = document.getElementById('system-message-container');

		if (!container) {
			return;
		}

		const alert = document.createElement('div');
		const close = document.createElement('button');

		alert.className = `alert alert-${success ? 'success' : 'danger'} alert-dismissible fade show`;
		alert.setAttribute('role', 'alert');

		if (title) {
			const heading = document.createElement('h4');

			heading.textContent = title;
			alert.append(heading);
		}

		alert.append(document.createTextNode(message));
		close.type = 'button';
		close.className = 'btn-close';
		close.setAttribute('data-bs-dismiss', 'alert');
		close.setAttribute('aria-label', text('JCLOSE', 'Close'));
		alert.append(close);
		container.append(alert);
	};

	// The answers of Kunena are a JsonResponse of Joomla: {success, message, data}.
	const getJson = (url) => fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } }).then((response) => {
		if (!response.ok) {
			throw new Error(response.statusText || String(response.status));
		}

		return response.json();
	});

	const init = () => {
		const box = document.getElementById('krating');
		const loadUrl = document.getElementById('krating_url');
		const submitUrl = document.getElementById('krating_submit_url');

		if (!box || !loadUrl || !submitUrl) {
			return;
		}

		const list = document.createElement('ul');
		const holder = document.createElement('div');
		const catid = (document.getElementById('krating-catid') || {}).value || '';

		holder.append(list);
		box.append(holder);

		// The rating which the server has: the stars go back to it when a rating is refused (a guest, the own topic...).
		let saved = 0;
		const rating = create(list, {
			value: 0,
			max: 5,
			onRate: (stars) => {
				getJson(`${submitUrl.value}&starid=${stars}&catid=${encodeURIComponent(catid)}`)
					.then((answer) => {
						const success = Boolean(answer && answer.success);
						// A guest gets an empty answer (nothing is saved for a guest): the message says to log in.
						const message = answer && answer.message ? answer.message : 'COM_KUNENA_RATE_LOGIN';

						if (success) {
							saved = stars;
						} else {
							rating.set(saved);
						}

						notify(success, text(message, message === 'COM_KUNENA_RATE_LOGIN' ? 'You need to be logged in to rate topic' : message));
					})
					.catch((error) => {
						rating.set(saved);
						notify(false, error.message, text('COM_KUNENA_RATING_WARNING_LABEL', 'Warning!'));
					});
			},
		});

		// The rating which the topic shows: the one of the user, or the average of the topic. The address of the layout does not
		// name the topic: it is in the field topic_id.
		const topicId = (document.getElementById('topic_id') || {}).value || '';

		getJson(`${loadUrl.value}${loadUrl.value.includes('?') ? '&' : '?'}topic_id=${encodeURIComponent(topicId)}`)
			.then((answer) => {
				saved = typeof answer === 'number' ? answer : answer && answer.success ? Number(answer.data) || 0 : 0;
				rating.set(saved);
			})
			.catch(() => {
				// The stars stay empty: the rating could not be read.
			});
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	window.KunenaRating = { create };
})();
