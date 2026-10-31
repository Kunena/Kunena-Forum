/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

/*
 * Hand-over of the text of the editor between the quick reply and the full editor.
 *
 * The text used to be kept under one key for the whole site ("copyKunenaeditor"), without end: on a shared computer the next
 * member who opened an editor got the text of the previous one, even after a logout. The text is now kept under a key of the
 * member who wrote it, it is only handed back to the same member, it is removed when it is taken, and it expires.
 * A guest has no identity to bind to, so the text of a guest only lives as long as the tab (sessionStorage).
 */
(function (root) {
	'use strict';

	const PREFIX = 'copyKunenaeditor';
	const MAX_AGE = 15 * 60 * 1000;

	function owner() {
		const id = parseInt(root.Joomla && root.Joomla.getOptions ? root.Joomla.getOptions('com_kunena.draftowner') : 0, 10);

		return id > 0 ? id : 0;
	}

	function store(id) {
		try {
			return id > 0 ? root.localStorage : root.sessionStorage;
		} catch (e) {
			return null;
		}
	}

	function key(id) {
		return PREFIX + ':' + id;
	}

	function purge(area, keep) {
		// The key of the old versions has no owner: whoever wrote it is unknown, so it is never handed to anybody.
		try {
			area.removeItem(PREFIX);

			for (let i = area.length - 1; i >= 0; i--) {
				const name = area.key(i);

				if (name && name.indexOf(PREFIX + ':') === 0 && name !== keep) {
					const raw = area.getItem(name);
					let old = true;

					try {
						old = (Date.now() - JSON.parse(raw).t) > MAX_AGE;
					} catch (e) {
						old = true;
					}

					if (old) {
						area.removeItem(name);
					}
				}
			}
		} catch (e) {
			// Storage is not available.
		}
	}

	const KunenaDraft = {
		save: function (text) {
			const id = owner();
			const area = store(id);

			if (!area) {
				return;
			}

			purge(area, key(id));

			try {
				if (text) {
					area.setItem(key(id), JSON.stringify({t: Date.now(), v: String(text)}));
				} else {
					area.removeItem(key(id));
				}
			} catch (e) {
				// Quota or private mode: the hand-over is a convenience only.
			}
		},

		// Gives back the text of the current member once, or null.
		take: function () {
			const id = owner();
			const area = store(id);

			if (!area) {
				return null;
			}

			purge(area, key(id));

			let text = null;

			try {
				const entry = JSON.parse(area.getItem(key(id)));

				if (entry && typeof entry.v === 'string' && (Date.now() - entry.t) <= MAX_AGE) {
					text = entry.v;
				}
			} catch (e) {
				text = null;
			}

			this.clear();

			return text;
		},

		clear: function () {
			const id = owner();
			const area = store(id);

			if (area) {
				try {
					area.removeItem(key(id));
					area.removeItem(PREFIX);
				} catch (e) {
					// Storage is not available.
				}
			}
		}
	};

	root.KunenaDraft = KunenaDraft;
})(typeof window !== 'undefined' ? window : globalThis);
