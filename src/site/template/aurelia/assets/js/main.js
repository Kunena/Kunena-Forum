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
	/* To check or uncheck boxes to select items */
	$('input.kcheckall').click(function () {
		$('.kcheck').prop('checked', $(this).prop("checked"));
	});

	/* Allow to make working drop-down choose destination */
	const bulkOptions = function () {
		const task = $("#kchecktask").val();
		const move = task === 'move';

		/* The destination and the status (topic icon) are only shown for their action */
		$("#kchecktarget").attr('disabled', !move).toggleClass('d-none', !move);

		if (move) {
			$("#kchecktarget").trigger("list:updated");
		}

		$("#kcheckstatus").attr('disabled', task !== 'status').toggleClass('d-none', task !== 'status');
	};

	$('#kchecktask').change(bulkOptions);
	bulkOptions();

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

	if ($.fn.datepicker !== undefined) {
		$('.date').datepicker({
			orientation: "top auto",
			format: "yyyy-mm-dd",
			language: "kunena"
		});
	}

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
