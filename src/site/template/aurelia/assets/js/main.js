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

jQuery(document).ready(function ($) {
	/* To check or uncheck boxes to select items: the boxes of the same list (a page can show several lists, as the tabs of a
	 * profile), or of the page when the box is not in a form. */
	$('input.kcheckall').click(function () {
		const form = $(this).closest('form');

		(form.length ? form : $(document)).find('.kcheck').prop('checked', $(this).prop("checked"));
	});

	/* Allow to make working drop-down choose destination. Each list has its own actions: their ids get a number after the
	 * first list (kchecktask-2, kchecktarget-2), so they are looked for in the form of the list. */
	const bulkOptions = function (select) {
		const form = $(select).closest('form');
		const scope = form.length ? form : $(document);
		const task = $(select).val();
		const move = task === 'move';
		const target = scope.find('[id^="kchecktarget"]');
		const status = scope.find('[id^="kcheckstatus"]');

		/* The destination and the status (topic icon) are only shown for their action */
		target.attr('disabled', !move).toggleClass('d-none', !move);

		if (move) {
			target.trigger("list:updated");
		}

		status.attr('disabled', task !== 'status').toggleClass('d-none', task !== 'status');
	};

	$('select.kchecktask, select[id^="kchecktask"]').each(function () {
		$(this).change(() => bulkOptions(this));
		bulkOptions(this);
	});

	$("input.kcatcheckall").click(function () {
		$("input.kcatcheckall:checkbox").not(this).prop('checked', this.checked);
	});

	$("input.kcheckallcategories").click(function () {
		$("input.kcheckallcategory:checkbox").not(this).prop('checked', this.checked);
	});

	const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
	const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  		return new bootstrap.Popover(popoverTriggerEl)
	})

	$('#avatar_gallery_select').change(function () {
		const gallery_selected = $("#avatar_gallery_select").val();

		const gallery_list = $('#gallery_list');

		// We remove avatar which exist in td tag to allow us to put new one items
		gallery_list.empty();

		// Get the list of images from the gallery selected drop-down above
		$.ajax({
			dataType: "json",
			url: Joomla.getOptions('com_kunena.avatar_gallery_url'),
			data: {gallery_name: gallery_selected}
		}).done(function (response) {
			if (!Array.isArray(response)) {
				return;
			}

			// The names come from the server: they are set as attributes, never written into the markup.
			$.each(response, function (key, item) {
				const id = 'kavatar-gallery-' + key;

				$('<li>', {'class': 'col-md-2'})
					.append($('<input>', {type: 'radio', name: 'avatar_gallery', id: id}).val(item.value))
					.append($('<label>', {'class': 'radio thumbnail', 'for': id})
						.append($('<img>', {loading: 'lazy', alt: 'avatar'}).attr('src', item.url)))
					.appendTo(gallery_list);
			});
		}).fail(function () {
			gallery_list.empty();
		});
	});

	const clearcache = $('#clearcache');
	clearcache.on('click', function (e) {
		e.preventDefault();
		clearcache.addClass('btn-success');
		clearcache.html('<span class="glyphicon glyphicon-ok-sign"></span> ' + Joomla.Text._('COM_KUNENA_CLEARED'));
	});

	$('#modaldeteleallsubmit').click(function() {
		$('#adminForm').submit();
	});
});
