/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

/* Function used to ordering the data by clicking on column title */
function kunenatableOrdering( order, dir, task, form ) {
	var form=document.getElementById(form);
	form.filter_order.value=order;
	form.filter_order_Dir.value=dir;
	form.submit( task );
}

jQuery(document).ready(function($) {
	/* To check or uncheck boxes to select items */
	$('input.kcheckall').click(function() {
		$('.kcheck').prop('checked', $(this).prop("checked"));
	});

	/* Allow to make working drop-down choose destination */
	$('#kchecktask').change(function() {
		var task = $("select#kchecktask").val();
		if (task=='move') {
			$("#kchecktarget").attr('disabled', false).trigger("liszt:updated");
		} else {
			$("#kchecktarget").attr('disabled', true);
		}
	});

	$("input.kcatcheckall").click(function(){
		$("input.kcatcheckall:checkbox").not(this).prop('checked', this.checked);
	});

	$("input.kcheckallcategories").click(function(){
		$("input.kcheckallcategory:checkbox").not(this).prop('checked', this.checked);
	});

	$(document).ready(function() {
		$('[rel=popover]').popover();
	});

	$('#avatar_gallery_select').change(function() {
		var gallery_selected = $("select#avatar_gallery_select").val();

		var gallery_list = $('#gallery_list');

		// We remove avatar which exist in td tag to allow us to put new one items
		gallery_list.empty();

		// Get the list of images from the gallery selected drop-down above
	 $.ajax({
			 dataType: "json",
			 url: $('#kunena_url_avatargallery').val(),
			 data: 'gallery_name=' + gallery_selected
		}).done(function(response) {
       $.each(response, function( key, value ) {
				  gallery_list.append('<li class="span2"><input id="radio'+gallery_selected+'/'+value.filename+'" type="radio" value="gallery/'+gallery_selected+'/'+value.filename+'" name="avatar"><label class=" radio thumbnail" for="radio'+gallery_selected+'/'+value.filename+'"><img alt="" src="'+value.url+'"></label></li>');
			  });
		}).fail(function(response) {

		});
	});

	if ($.fn.datepicker != undefined) {
		// Load datepicker for announcement
		$('#ann-date .input-group.date').datepicker({
			orientation: "top auto",
			format: "yyyy-mm-dd"
		});

		$('#ann-date2 .input-group.date').datepicker({
			orientation: "top auto",
			format: "yyyy-mm-dd"
		});

		$('#ann-date3 .input-group.date').datepicker({
			orientation: "top auto",
			format: "yyyy-mm-dd"
		});
	}

	$('#clearcache').on('click', function (e) {
		e.preventDefault();
		$('#clearcache').addClass('btn-success');
		$('#clearcache').html('<span class="glyphicon glyphicon-ok-sign"></span> ' + Joomla.JText._('COM_KUNENA_CLEARED'));
	});
});

