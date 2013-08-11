/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

function showMessage() {
	div = document.getElementById('tow', 'test1', 'k', 'row');
	div.style.display = "block";
}

function hideMessage() {
	div = document.getElementById('tow','test2', 'k', 'row');
	div.style.display = "none";
}

window.addEvent('domready', function(){	
	$$('.kspoiler').each(function(el){
		var contentElement = el.getElement('.kspoiler-content');
		var expandElement = el.getElement('.kspoiler-expand');
		var hideElement = el.getElement('.kspoiler-hide');
		el.getElement('.kspoiler-header').addEvent('click', function(e){
			if (contentElement.style.display == "none") {
				contentElement.setStyle('display');
				expandElement.setStyle('display', 'none');
				hideElement.setStyle('display');
			} else {
				contentElement.setStyle('display', 'none');
				expandElement.setStyle('display');
				hideElement.setStyle('display', 'none');
			}
		});
	});
	
  /* To check or uncheck boxes to select items */
	$$('input.kcheckall').addEvent('click', function(e){
		this.getParent('form').getElements('input.kcheck').each(function(el){
			if(el.get('checked')==false){
				el.set('checked',true);
				el.set('value','1');
			} else {
				el.set('value','0');
				el.set('checked',false);
			}
		}); 
	});
  
  /* To close quick-reply form on hit on cancel button */
  $$('.kreply-cancel').addEvent('click', function(e){
		$$('.kreply-form').setStyle('display', 'none');
	});
});

jQuery(document).ready(function() { 
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
});  
