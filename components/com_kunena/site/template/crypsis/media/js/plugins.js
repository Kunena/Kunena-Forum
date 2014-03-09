/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

/* Function used to ordering the data by clicking on column title */
function kunenatableOrdering( order, dir, task, form ) {
	var form=document.getElementById(form);
	form.filter_order.value=order;
	form.filter_order_Dir.value=dir;
	form.submit( task );
}

/**
 *  Helper function for to perform JSON request for preview
 */
function kPreviewHelper() {
	if (_previewActive == true){
		if ( jQuery('#kbbcode-message').val() != null ) {
			jQuery.ajax({
				type: 'POST',
				url: jQuery('#kpreview_url').val(),
				async: false,
				dataType: 'json',
				data: {body : jQuery('#kbbcode-message').val() },
				success: function(data){
					jQuery('#kbbcode-preview').html(data.preview);
				}
			});
		}
	}
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
	
	/* To hide or open spoiler on click */
	jQuery('.kspoiler').each(function() {
		jQuery('.kspoiler').click(function() {
			if ( !jQuery('.kspoiler-content').is(':visible') ) {
				jQuery(this).find('.kspoiler-content').show();
				jQuery(this).find('.kspoiler-expand').hide();
				jQuery(this).find('.kspoiler-hide').show();
			} else {
				jQuery(this).find('.kspoiler-content').hide();
				jQuery(this).find('.kspoiler-expand').show();
				jQuery(this).find('.kspoiler-hide').hide();
			}
		});
	});
	
	/* To check or uncheck boxes to select items */
	jQuery('input.kcheckall').click(function() {
		jQuery( '.kcheck' ).each(function( ) {			
			jQuery(this).prop('checked',!jQuery(this).prop('checked'));
		});
	});
		
	/* To allow to close or open the quick-reply modal box */
	jQuery('.openmodal').click(function() {
		var boxToOpen = jQuery(this).attr('href');
		jQuery(boxToOpen ).css('visibility', 'visible');
	});
	
	/* Change avatar in gallery */
	if ( jQuery('#avatar_category_select') != undefined ) { 
		jQuery('#avatar_category_select').change(function() {
			// we getting the name of gallery selected in drop-down by user 
			var avatar_selected =	jQuery('#avatar_category_select').chosen().val();
			var td_avatar = jQuery('#kgallery_avatar_list');
			
			// we remove avatar which exist in td tag to allow us to put new one items
			jQuery('#kgallery_avatar_list').empty(); 
			// we getting from hidden input the url of kunena image gallery
			var url_gallery_main = jQuery('#Kunena_Image_Gallery_URL').val();
			var id_to_select = jQuery('#Kunena_'+avatar_selected);      
			var name_to_select = id_to_select.attr('name');
			// Convert JSON to object
			var image_object = JSON.decode(id_to_select.val());
						
			// Re-create all HTML items with avatars images from gallery selected by user
			for(var i = 0, len = image_object.length; i < len; ++i) {
				if ( name_to_select != 'default' ) {
					var name_img = name_to_select+'/'+image_object[i];
				} else {
					var name_img = image_object[i];
				}
				
				jQuery('<span></span>', {
				'id': 'kspan_gallery'+i}).appendTo(td_avatar);
				jQuery('<label></label>', {
				'id': 'klabel_gallery'+i,'for':'kavatar'+i}).appendTo('#kspan_gallery'+i);
				jQuery('<img>', {
				'src': url_gallery_main+'/'+name_img,'alt':image_object[i],'title':image_object[i]}).appendTo('#klabel_gallery'+i);
				jQuery('<input>', {
				'id': 'kavatar'+i,'type':'radio', 'value':'gallery/'+name_img, 'name':'avatar'}).appendTo('#kspan_gallery'+i);
			}
		});
	}
	
	/* Allow to make working drop-down choose destination */
	jQuery('#kchecktask').change(function() {
		var task = jQuery("select#kchecktask").val();
		if (task=='move') {
			jQuery("#kchecktarget").attr('disabled', false).trigger("liszt:updated");
		} else {
			jQuery("#kchecktarget").attr('disabled', true);
		}
	});
	
	/* Hide search form when there are search results found */
	if ( jQuery('#kunena_search_results').is(':visible') ) {
		jQuery('#search').hide();
	}
	
	/* Provide autocomplete user list in search form and in user list */
	if (  jQuery( '#kurl_users' ).length > 0 ) {
		var users_url = jQuery( '#kurl_users' ).val();
		
		jQuery('#kusersearch').atwho({
			at: "", 
			tpl: '<li data-value="${username}"><i class="icon-user"></i>${name} <small>${username}</small></li>',
			limit: 7, 
			callbacks: {
				remote_filter: function(query, callback)  {
					jQuery.ajax({
						url: users_url,
						data: {
							search : query
						},
						success: function(data) {
							callback(data.names);
						}
					});
				}
			}
		});
	}

	/* On moderate page display subject or field to enter manually the topic ID */
	jQuery('#kmod_topics').change(function() {
		var id_item_selected = jQuery(this).val();
				
		if (id_item_selected != 0) {
			jQuery('#kmod_subject').hide();
		} else {
			jQuery('#kmod_subject').show();
		}
		
		if (id_item_selected == -1) {
			jQuery('#kmod_targetid').show();
		} else {
			jQuery('#kmod_targetid').hide();
		}
	});
	
	jQuery('#kmod_categories').change(function() {
		jQuery.getJSON(
			kunena_url_ajax, { catid: jQuery(this).val() }
		).done(function( json ) {
			var first_item = jQuery('#kmod_topics option:nth(0)').clone();
			var second_item = jQuery('#kmod_topics option:nth(1)').clone();      
			
			jQuery('#kmod_topics').empty();
			first_item.appendTo('#kmod_topics');
			second_item.appendTo('#kmod_topics');
			
			jQuery.each(json,function(index, object) {  
				jQuery.each(object, function(key, element) {
					jQuery('#kmod_topics').append('<option value="'+element['id']+'">'+element['subject']+'</option>');
				});
			});
		});
	});
	
	/* Button to show more info on profilebox */
	jQuery(".heading").click(function() {
            if ( !jQuery(this).hasClass('heading-less') ) {
                    jQuery(this).prev(".heading").show();
                    jQuery(this).hide();
                    jQuery(this).next(".content").slideToggle(500);
            } else {
                    var content = jQuery(this).next(".heading").show();
                    jQuery(this).hide();
                    content.next(".content").slideToggle(500);
            }
    });
	
	/* To enabled emojis in kunena textera feature like on github */
	if ( jQuery('#kemojis_allowed').val() ) {
		jQuery('#kbbcode-message').atwho({
			at: ":",
			tpl:"<li data-value='${key}'>${name} <img src='${url}' height='20' width='20' /></li>",
			callbacks: {
				remote_filter: function(query, callback) {
					if(query.length > 0) {
						jQuery.ajax({
							url: jQuery( "#kurl_emojis" ).val(),
							data: {
								search : query
							},
							success: function(data) {
								callback(data.emojis);
							}
						});
					}
				}
			}
		});
	}
});

