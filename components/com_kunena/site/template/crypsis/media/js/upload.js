/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

Dropzone.autoDiscover = false;

jQuery(function() {
	// Now that the DOM is fully loaded, create the dropzone, and setup the 
	// event listeners

	var myDropzone = new Dropzone("#kunena-upload", { url:kunena_upload_files_url});
	
	var index =0;
	
	var attach_id = 0;
	
	// Limit the total number of files allowed to upload to follow Kunena configuration
	if ( config_attachment_limit != 0 ) {
		myDropzone.options.maxFiles = config_attachment_limit;
	}
	
	// Load attachments when the message is edited
	if ( jQuery('#kmessageid').val() > 0 ) {
		jQuery.ajax({
			type: 'POST',
			url: kunena_upload_files_preload,
			async: false,
			dataType: 'json',
			data: {mes_id : jQuery('#kmessageid').val() },
			success: function(data){
				jQuery.each(data, function(index, value) {
					var item = jQuery('<div class="dz-preview dz-processing dz-image-preview dz-success"></div>');
					jQuery('#kunena-upload').append(item);
					var details = jQuery('<div class="dz-details"></div>').appendTo(item);
					var filename = jQuery('<div class="dz-filename"></div>').appendTo(details);
					jQuery('<span data-dz-name="">'+this['filename']+'</span>').appendTo(filename);
					var size = jQuery('<div class="dz-size" data-dz-size=""></div>').appendTo(details);
					jQuery('<strong>'+this['size']+' kB</strong>').appendTo(size);
					jQuery('<img data-dz-thumbnail="" alt="'+this['filename']+'" src="'+this['url']+'">').appendTo(details);
				})
			}
		});	
	}
	
	myDropzone.on("success", function(file, response) {
		attach_id = response['data']['id'];

		// The attachment has been right uploaded, so now we need to put into input hidden to added to message 
		jQuery('#kattach-list').append('<input type="hidden" name="attachments['+response['data']['id']+']" value="1" />');
		jQuery('#kattach-list').append('<input type="hidden" name="attachment['+response['data']['id']+']" value="1" />');
	});
	
	myDropzone.on("maxfilesreached", function(file, response) {
		jQuery('#alert_upload_box').empty();
		var alert_maxfiles = jQuery('<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert">×</button>You have reached the maximum number of files allowed</div>');
		jQuery('#alert_upload_box').append(alert_maxfiles);
	});
	
	myDropzone.on("sending", function(file, xhr, formData) {
		// Add extra parameters here to pass to ajax query
		formData.append('catid', jQuery('#kunena_upload').val());
		formData.append('filename', file["name"]);
		formData.append('size', file["size"]);
		formData.append('mime', file["type"]);
	});
	
	myDropzone.on("addedfile", function(file) {
		index = index+1;
		
		// Create the remove button
		var insertButton = Dropzone.createElement('<button class="btn btn-primary">'+Joomla.JText._('COM_KUNENA_EDITOR_INSERT')+'</button>');
		
		insertButton.addEventListener("click", function(e) {
			// Make sure the button click doesn't submit the form:
			e.preventDefault();
			e.stopPropagation();
			
			var value = jQuery('#kbbcode-message').val();
		
			jQuery('#kbbcode-message').val(value+' [attachment:'+index+']'+file.name+'[/attachment]');
		});

		// Add the button to the file preview element.
		file.previewElement.appendChild(insertButton);
		
		// Create the remove button
		var removeButton = Dropzone.createElement('<button class="btn btn-danger delete">'+Joomla.JText._('COM_KUNENA_GEN_REMOVE_FILE')+'</button>');

		// Capture the Dropzone instance as closure.
		var _this = this;

		// Listen to the click event
		removeButton.addEventListener("click", function(e) {
			// Make sure the button click doesn't submit the form:
			e.preventDefault();
			e.stopPropagation();

			jQuery('#kattach-list').append('<input type="hidden" name="attachments['+attach_id+']" value="1" />');

			// Remove the file preview.
			_this.removeFile(file);
			
			// Ajax Request to delete the file from filesystem
			jQuery.ajax({
				url: kunena_upload_files_rem+'&fil_id='+attach_id,
				type: 'DELETE',
				success: function(result) {
					// Do something with the result
				}
			});
		});

		// Add the button to the file preview element.
		file.previewElement.appendChild(removeButton);
	});

})