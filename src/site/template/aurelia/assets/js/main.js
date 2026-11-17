/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

/* Function used to ordering the data by clicking on column title */
function kunenatableOrdering(order, dir, task, form) {
	const formElement = document.getElementById(form);
	formElement.filter_order.value = order;
	formElement.filter_order_Dir.value = dir;
	formElement.submit(task);
}

(function () {
	'use strict';

	// The boxes and the lists of the same list of items: its form, or the page when there is no form.
	const scopeOf = (element) => element.closest('form') || document;

	/* Allow to make working drop-down choose destination. Each list has its own actions: their ids get a number after the
	 * first list (kchecktask-2, kchecktarget-2), so they are looked for in the form of the list. */
	const bulkOptions = (select) => {
		const scope = scopeOf(select);
		const task = select.value;

		/* The destination and the status (topic icon) are only shown for their action */
		const toggle = (selector, shown) => scope.querySelectorAll(selector).forEach((field) => {
			field.disabled = !shown;
			field.classList.toggle('d-none', !shown);
		});

		toggle('[id^="kchecktarget"]', task === 'move');
		toggle('[id^="kcheckstatus"]', task === 'status');
	};

	// The avatars of a gallery (the profile edit): the names come from the server, so they are set as attributes, never written
	// into the markup.
	const showGallery = async (select) => {
		const list = document.getElementById('gallery_list');

		if (!list) {
			return;
		}

		list.replaceChildren();

		const url = new URL(Joomla.getOptions('com_kunena.avatar_gallery_url'), window.location.href);
		url.searchParams.set('gallery_name', select.value);

		try {
			const response = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
			const images = await response.json();

			if (!Array.isArray(images)) {
				return;
			}

			images.forEach((item, key) => {
				const id = 'kavatar-gallery-' + key;
				const li = document.createElement('li');
				const input = document.createElement('input');
				const label = document.createElement('label');
				const img = document.createElement('img');

				li.className = 'col-md-2';
				input.type = 'radio';
				input.name = 'avatar_gallery';
				input.id = id;
				input.value = item.value;
				label.className = 'radio thumbnail';
				label.htmlFor = id;
				img.loading = 'lazy';
				img.alt = 'avatar';
				img.src = item.url;

				label.append(img);
				li.append(input, label);
				list.append(li);
			});
		} catch {
			list.replaceChildren();
		}
	};

	const init = () => {
		document.querySelectorAll('select.kchecktask, select[id^="kchecktask"]').forEach((select) => {
			select.addEventListener('change', () => bulkOptions(select));
			bulkOptions(select);
		});

		if (window.bootstrap?.Popover) {
			document.querySelectorAll('[data-bs-toggle="popover"]').forEach((trigger) => {
				bootstrap.Popover.getOrCreateInstance(trigger);
			});
		}

		document.getElementById('avatar_gallery_select')?.addEventListener('change', (e) => showGallery(e.target));

		document.getElementById('modaldeteleallsubmit')?.addEventListener('click', () => {
			document.getElementById('adminForm')?.submit();
		});
	};

	document.addEventListener('click', (e) => {
		const box = e.target.closest('input[type="checkbox"]');

		if (!box) {
			return;
		}

		/* To check or uncheck boxes to select items: the boxes of the same list (a page can show several lists, as the tabs
		 * of a profile), or of the page when the box is not in a form. */
		if (box.matches('input.kcheckall')) {
			scopeOf(box).querySelectorAll('.kcheck').forEach((item) => {
				item.checked = box.checked;
			});
		}

		// All the categories of the list of categories.
		if (box.matches('input.kcheckallcategories')) {
			document.querySelectorAll('input.kcheckallcategory[type="checkbox"]').forEach((item) => {
				item.checked = box.checked;
			});
		}
	});

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
