/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

/* Function used to ordering the data by clicking on column title */
function kunenatableOrdering( order, dir, task, form ) {
	var form=document.getElementById(form);
	form.filter_order.value=order;
	form.filter_order_Dir.value=dir;
	form.submit( task );
}

jQuery(document).ready(function() {
	/* To hide or open collapse localStorage */
	jQuery('.collapse').on('hidden', function() {
				if (this.id) {
						localStorage[this.id] = 'true';
				}
		}).on('shown', function() {
				if (this.id) {
						localStorage.removeItem(this.id);
				}
		}).each(function() {
				if (this.id && localStorage[this.id] === 'true' ) {
						jQuery(this).collapse('hide');
				}
	});

	/* To check or uncheck boxes to select items */
	jQuery('input.kcheckall').click(function() {
		jQuery( '.kcheck' ).each(function( ) {
			jQuery(this).prop('checked',!jQuery(this).prop('checked'));
		});
	});

	/* Allow to make working drop-down choose destination */
	jQuery('#kchecktask').change(function() {
		var task = jQuery("select#kchecktask").val();
		if (task=='move') {
			jQuery("#kchecktarget").attr('disabled', false).trigger("liszt:updated");
		} else {
			jQuery("#kchecktarget").attr('disabled', true);
		}
	});

	jQuery("input.kcatcheckall").click(function(){
		jQuery("input.kcatcheckall:checkbox").not(this).prop('checked', this.checked);
	});

	jQuery("input.kcheckallcategories").click(function(){
		jQuery("input.kcheckallcategory:checkbox").not(this).prop('checked', this.checked);
	});

	jQuery(document).ready(function() {
		jQuery('[rel=popover]').popover();
	});
	
	jQuery('#avatar_gallery_select').change(function() {
		var gallery_selected = jQuery("select#avatar_gallery_select").val();

		var gallery_list = jQuery('#gallery_list');
		
		// We remove avatar which exist in td tag to allow us to put new one items
		gallery_list.empty();
		
		// Get the list of images from the gallery selected drop-down above
	 jQuery.ajax({
			 dataType: "json",
			 url: 'index.php?option=com_kunena&view=user&layout=galleryimages&format=raw',
			 data: 'gallery_name=' + gallery_selected 
		}).done(function(response) {
       jQuery.each(response, function( key, value ) {
				  gallery_list.append('<li class="span2"><input id="radio'+gallery_selected+'/'+value.filename+'" type="radio" value="gallery/'+gallery_selected+'/'+value.filename+'" name="avatar"><label class=" radio thumbnail" for="radio'+gallery_selected+'/'+value.filename+'"><img alt="" src="'+value.url+'"></label></li>');
			  });
		}).fail(function(response) {
			
		});
	});	
});

