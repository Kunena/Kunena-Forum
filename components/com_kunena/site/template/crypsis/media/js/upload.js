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
	
	// Limit the total number of files allowed to upload to follow Kunena configuration
	if ( config_attachment_limit != 0 ) {
		myDropzone.options.maxFiles = config_attachment_limit;
	}
	
	// Insert bbcode in message
	function insertInMessage(id,filename) {
		var value = jQuery('#kbbcode-message').val();
		
		jQuery('#kbbcode-message').val(value+' [attachment='+id+']'+filename+'[/attachment]');
	}

	// Load attachments when the message is edited
	if ( jQuery('#kmessageid').val() > 0 ) {
		// Load attachments when the message is edited
		jQuery.ajax({
			type: 'POST',
			url: kunena_upload_files_preload,
			async: false,
			dataType: 'json',
			data: {mes_id : jQuery('#kmessageid').val() },
			success: function(data){
				if ( jQuery('#kunena-upload > div').hasClass('dz-default') ) {
					jQuery('#kunena-upload > div').css( 'background-image', 'none' );
				}
				
				jQuery.each(data, function(index, value) {
					var myattach = this;
					
					// Create the mock file:
					var mockFile = { name: this['filename'], size: this['size'] };
			
					var insertButton = Dropzone.createElement('<button class="btn btn-primary">'+Joomla.JText._('COM_KUNENA_EDITOR_INSERT')+'</button>');
					var removeButton = Dropzone.createElement('<button class="btn btn-danger delete">'+Joomla.JText._('COM_KUNENA_GEN_REMOVE_FILE')+'</button>');
					
					// Call the default addedfile event handler
					myDropzone.emit("addedfile", mockFile);
					
					// And optionally show the thumbnail of the file:
					myDropzone.emit("thumbnail", mockFile, this['url']);
					
					insertButton.addEventListener("click", function(e) {
						// Make sure the button click doesn't submit the form:
						e.preventDefault();
						e.stopPropagation();
						
						insertInMessage(myattach['id'],mockFile.name);
					});
			
					mockFile.previewElement.adopt(insertButton);
			
					removeButton.addEventListener("click", function(e) {
						// Make sure the button click doesn't submit the form:
						e.preventDefault();
						e.stopPropagation();
							
						jQuery('#kattach-list').append('<input id="kattachs-'+myattach['id']+'" type="hidden" name="attachments['+myattach['id']+']" value="1" />');
							
						// Remove the input added
						if ( jQuery('#kattach-'+myattach['id']).length > 0 ) {
							jQuery('#kattach-'+myattach['id']).remove();
						}
						
						myDropzone.options.maxFiles = myDropzone.options.maxFiles+1;

						// Remove the file preview.
						mockFile.previewElement.destroy();
							
						// Ajax Request to delete the file from filesystem
						jQuery.ajax({
							url: kunena_upload_files_rem+'&fil_id='+myattach['id'],
							type: 'DELETE',
							success: function(result) {
								// Do something with the result
							}
						});
					});
			
					mockFile.previewElement.adopt(removeButton);
				});
				// Adjust it to the correct amount for the maxfiles option
				var existingFileCount = data.length; // The number of files already uploaded
				myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;
			}
		});	
	}
	
	myDropzone.on("success", function(file, response) {
		var attach_id = response['data']['id'];
		
		// Create the insert button
		var insertButton = Dropzone.createElement('<button class="btn btn-primary">'+Joomla.JText._('COM_KUNENA_EDITOR_INSERT')+'</button>');
		
		insertButton.addEventListener("click", function(e) {
			// Make sure the button click doesn't submit the form:
			e.preventDefault();
			e.stopPropagation();
			
			insertInMessage(attach_id,file.name);
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

			// Remove the input added previously on insertion of attachment
			if ( jQuery('#kattachs-'+attach_id).length > 0 ) {
				jQuery('#kattachs-'+attach_id).remove();
			}

			if ( jQuery('#kattach-'+attach_id).length > 0 ) {
				jQuery('#kattach-'+attach_id).remove();
			}

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

		// The attachment has been right uploaded, so now we need to put into input hidden to added to message 
		jQuery('#kattach-list').append('<input id="kattachs-'+response['data']['id']+'" type="hidden" name="attachments['+response['data']['id']+']" value="1" />');
		jQuery('#kattach-list').append('<input id="kattach-'+response['data']['id']+'" type="hidden" name="attachment['+response['data']['id']+']" value="1" />');
	});
	
	myDropzone.on("maxfilesexceeded", function(file, response) {
		jQuery('#alert_upload_box').empty();
		var alert_maxfiles = jQuery('<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert">×</button>'+Joomla.JText._('COM_KUNENA_UPLOADED_LABEL_ERROR_REACHED_MAX_NUNBER_FILES')+'</div>');
		jQuery('#alert_upload_box').append(alert_maxfiles);
	});
	
	myDropzone.on("sending", function(file, xhr, formData) {
		// Add extra parameters here to pass to ajax query
		formData.append('catid', jQuery('#kunena_upload').val());
		formData.append('filename', file["name"]);
		formData.append('size', file["size"]);
		formData.append('mime', file["type"]);
	});
	
	myDropzone.on("error", function(file, message) { 
		// Create the remove button
		var removeButton = Dropzone.createElement('<button class="btn btn-danger delete">'+Joomla.JText._('COM_KUNENA_GEN_REMOVE_FILE')+'</button>');
	
		// Capture the Dropzone instance as closure.
		var _this = this;

		// Listen to the click event
		removeButton.addEventListener("click", function(e) {
			// Make sure the button click doesn't submit the form:
			e.preventDefault();
			e.stopPropagation();
	
			// Remove the file preview.
			_this.removeFile(file);
		}); 
	
		// Add the button to the file preview element.
		file.previewElement.appendChild(removeButton); 
	});
})