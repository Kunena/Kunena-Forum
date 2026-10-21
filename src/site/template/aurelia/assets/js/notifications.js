/**
 * Kunena Component
 *
 * Shows a popup (a toast) when somebody mentions the user, replies in his topic or in a topic he follows, or starts a topic
 * in a category he follows. The server is asked now and then (only when the page is visible), after the last message the
 * browser has seen. That message is kept in the local storage, so the tabs of a browser do not show the same popup twice.
 *
 * @package         Kunena.Template.Aurelia
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

(function () {
	'use strict';

	const start = () => {
		const options = (window.Joomla && Joomla.getOptions) ? Joomla.getOptions('com_kunena.notifications') : null;

		if (!options || !options.url) {
			return;
		}

		const interval = Math.max(10, parseInt(options.interval, 10) || 30) * 1000;
		const labels = options.labels || {};
		const key = 'kunena.notify.' + (options.userid || 0);
		const symbols = {mention: '@', reply: '↩', subscribed: '↩', topic: '+'};
		const shown = new Set();
		let busy = false;
		let container = null;

		const getCursor = () => {
			try {
				return parseInt(window.localStorage.getItem(key), 10) || 0;
			} catch (e) {
				return 0;
			}
		};

		const setCursor = (value) => {
			try {
				window.localStorage.setItem(key, String(value));
			} catch (e) {
				// The popups still work, a second tab may show the same one.
			}
		};

		// The look is made here, so the template does not need a rebuilt stylesheet.
		const addStyle = () => {
			if (document.getElementById('kunena-notify-style')) {
				return;
			}

			const style = document.createElement('style');
			style.id = 'kunena-notify-style';
			style.textContent = '#kunena-notify{position:fixed;right:1rem;bottom:1rem;z-index:1090;display:flex;flex-direction:column;gap:.6rem;width:min(23rem,calc(100vw - 2rem));pointer-events:none}'
				+ '.kunena-notify-toast{--kn-accent:#0d6efd;pointer-events:auto;position:relative;display:flex;gap:.75rem;align-items:flex-start;padding:.8rem 2.2rem .8rem .8rem;background:var(--bs-body-bg,#fff);color:var(--bs-body-color,#212529);border:1px solid var(--bs-border-color,#dee2e6);border-left:4px solid var(--kn-accent);border-radius:.8rem;box-shadow:0 .6rem 1.6rem rgba(0,0,0,.22);animation:kunena-notify-in .3s ease-out;transition:opacity .3s,transform .3s}'
				+ '.kunena-notify-toast.kunena-notify-out{opacity:0;transform:translateX(1.5rem)}'
				+ '.kunena-notify-toast[data-type=mention]{--kn-accent:#6f42c1}'
				+ '.kunena-notify-toast[data-type=subscribed]{--kn-accent:#0dcaf0}'
				+ '.kunena-notify-toast[data-type=topic]{--kn-accent:#198754}'
				+ '.kunena-notify-badge{flex:0 0 auto;width:2.2rem;height:2.2rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;color:#fff;background:var(--kn-accent)}'
				+ '.kunena-notify-body{min-width:0;flex:1 1 auto}'
				+ '.kunena-notify-title{font-weight:600;line-height:1.25}'
				+ '.kunena-notify-topic{font-size:.85rem;opacity:.75;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}'
				+ '.kunena-notify-excerpt{font-size:.85rem;margin-top:.25rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}'
				+ '.kunena-notify-open{display:inline-block;margin-top:.5rem;padding:.2rem .7rem;font-size:.85rem;border-radius:2rem;background:var(--kn-accent);color:#fff!important;text-decoration:none}'
				+ '.kunena-notify-close{position:absolute;top:.35rem;right:.5rem;border:0;background:transparent;color:inherit;opacity:.55;font-size:1.3rem;line-height:1;cursor:pointer}'
				+ '.kunena-notify-close:hover{opacity:1}'
				+ '.kunena-notify-more{align-self:flex-end;pointer-events:auto;font-size:.8rem;padding:.15rem .6rem;border-radius:2rem;background:var(--bs-body-bg,#fff);color:var(--bs-body-color,#212529);border:1px solid var(--bs-border-color,#dee2e6)}'
				+ '@keyframes kunena-notify-in{from{opacity:0;transform:translateX(1.5rem)}to{opacity:1;transform:none}}'
				+ '@media (prefers-reduced-motion:reduce){.kunena-notify-toast{animation:none;transition:none}}';
			document.head.appendChild(style);
		};

		const getContainer = () => {
			if (!container) {
				container = document.createElement('div');
				container.id = 'kunena-notify';
				container.setAttribute('role', 'status');
				container.setAttribute('aria-live', 'polite');
				document.body.appendChild(container);
			}

			return container;
		};

		const remove = (element) => {
			element.classList.add('kunena-notify-out');
			window.setTimeout(() => element.remove(), 300);
		};

		const show = (item) => {
			const toast = document.createElement('div');
			const badge = document.createElement('div');
			const body = document.createElement('div');
			const title = document.createElement('div');
			const topic = document.createElement('div');
			const open = document.createElement('a');
			const close = document.createElement('button');
			let timer = null;

			toast.className = 'kunena-notify-toast';
			toast.dataset.type = item.type;

			badge.className = 'kunena-notify-badge';
			badge.setAttribute('aria-hidden', 'true');
			badge.textContent = symbols[item.type] || '•';

			body.className = 'kunena-notify-body';
			title.className = 'kunena-notify-title';
			title.textContent = item.title;
			topic.className = 'kunena-notify-topic';
			topic.textContent = item.topic;
			body.append(title, topic);

			if (item.excerpt) {
				const excerpt = document.createElement('div');

				excerpt.className = 'kunena-notify-excerpt';
				excerpt.textContent = item.excerpt;
				body.append(excerpt);
			}

			open.className = 'kunena-notify-open';
			open.href = item.url;
			open.textContent = labels.open || 'Open';
			body.append(open);

			close.type = 'button';
			close.className = 'kunena-notify-close';
			close.setAttribute('aria-label', labels.close || 'Close');
			close.textContent = '×';
			close.addEventListener('click', () => remove(toast));

			toast.append(badge, body, close);

			// It goes by itself after some seconds, and stays while the pointer is on it.
			const arm = () => {
				timer = window.setTimeout(() => remove(toast), 10000);
			};

			toast.addEventListener('mouseenter', () => window.clearTimeout(timer));
			toast.addEventListener('mouseleave', arm);
			arm();

			getContainer().append(toast);
		};

		const poll = async () => {
			if (busy || document.hidden) {
				return;
			}

			busy = true;

			try {
				const response = await fetch(options.url + '&after=' + getCursor(), {
					credentials: 'same-origin',
					headers: {Accept: 'application/json'}
				});

				if (!response.ok) {
					return;
				}

				const data = await response.json();

				if (!data || !data.enabled) {
					return;
				}

				// Another tab can have shown some of them already.
				const seen = getCursor();
				const fresh = (data.items || []).filter((item) => item.id > seen && !shown.has(item.id));

				setCursor(Math.max(seen, parseInt(data.cursor, 10) || 0));

				if (!fresh.length) {
					return;
				}

				addStyle();
				fresh.forEach((item) => {
					shown.add(item.id);
					show(item);
				});

				if (data.more > 0) {
					const more = document.createElement('div');

					more.className = 'kunena-notify-more';
					more.textContent = (labels.more || '+%d').replace('%d', data.more);
					getContainer().append(more);
					window.setTimeout(() => more.remove(), 10000);
				}
			} catch (e) {
				// The next time may work: nothing to tell the visitor.
			} finally {
				busy = false;
			}
		};

		window.setInterval(poll, interval);
		document.addEventListener('visibilitychange', () => {
			if (!document.hidden) {
				poll();
			}
		});

		// The first time (no cursor yet) only the cursor is taken: no popups for what happened before.
		poll();
	};

	// The script is loaded in the head, the page is in the body.
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
