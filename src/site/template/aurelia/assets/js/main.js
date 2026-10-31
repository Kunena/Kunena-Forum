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
	var form = document.getElementById(form);
	form.filter_order.value = order;
	form.filter_order_Dir.value = dir;
	form.submit(task);
}

jQuery(document).ready(function ($) {
	/* To check or uncheck boxes to select items */
	$('input.kcheckall').click(function () {
		$('.kcheck').prop('checked', $(this).prop("checked"));
	});

	/* Allow to make working drop-down choose destination */
	$('#kchecktask').change(function () {
		const task = $("select#kchecktask").val();
		if (task === 'move') {
			$("#kchecktarget").attr('disabled', false).trigger("list:updated");
		}
		else {
			$("#kchecktarget").attr('disabled', true);
		}
	});

	$("input.kcatcheckall").click(function () {
		$("input.kcatcheckall:checkbox").not(this).prop('checked', this.checked);
	});

	$("input.kcheckallcategories").click(function () {
		$("input.kcheckallcategory:checkbox").not(this).prop('checked', this.checked);
	});

	var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
		var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  		return new bootstrap.Popover(popoverTriggerEl)
	})

	$('#avatar_gallery_select').change(function () {
		const gallery_selected = $("select#avatar_gallery_select").val();

		const gallery_list = $('#gallery_list');

		// We remove avatar which exist in td tag to allow us to put new one items
		gallery_list.empty();

		// Get the list of images from the gallery selected drop-down above
		$.ajax({
			dataType: "json",
			url: $('#kunena_url_avatargallery').val(),
			data: 'gallery_name=' + gallery_selected
		}).done(function (response) {
			$.each(response, function (key, value) {
				// File names and addresses are set as attributes, never written into a string of html.
				const radio_id = 'radio' + gallery_selected + '/' + value.filename;
				const radio = $('<input type="radio" name="avatar_gallery">')
					.attr('id', radio_id)
					.val('gallery/' + gallery_selected + '/' + value.filename);
				const label = $('<label class="radio thumbnail">')
					.attr('for', radio_id)
					.append($('<img alt="">').attr('src', value.url));

				gallery_list.append($('<li class="span2">').append(radio).append(label));
			});
		}).fail(function (response) {

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
