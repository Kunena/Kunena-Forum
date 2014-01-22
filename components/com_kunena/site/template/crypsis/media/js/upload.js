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
	
	// Limit the total number of files allowed to upload to follow Kunena configuration
	if ( config_attachment_limit != 0 ) {
		myDropzone.options.maxFiles = config_attachment_limit;
	}
	
	myDropzone.on("complete", function(file) {
		/* Maybe display some more file information on your page */
		jQuery('#kunena_tmp_dir').val(response.tmp_dir);
	});
	
	myDropzone.on("sending", function(file, xhr, formData) {
		// Add extra parameters here to pass to ajax query
		formData.append('catid', jQuery('#kunena_upload').val());
	});
	
	myDropzone.on("addedfile", function(file) {
		index = index+1;
		
		// Create the remove button
		var insertButton = Dropzone.createElement('<button class="btn btn-primary">Insert</button>');
		
		insertButton.addEventListener("click", function(e) {
			// Make sure the button click doesn't submit the form:
			e.preventDefault();
			e.stopPropagation();
			
			jQuery('#kbbcode-message').append('[attachment:'+index+']'+file.name+'[/attachment]');
		});

		// Add the button to the file preview element.
		file.previewElement.appendChild(insertButton);
		
		// Create the remove button
		var removeButton = Dropzone.createElement('<button class="btn btn-danger delete">Remove file</button>');

		// Capture the Dropzone instance as closure.
		var _this = this;

		// Listen to the click event
		removeButton.addEventListener("click", function(e) {
			// Make sure the button click doesn't submit the form:
			e.preventDefault();
			e.stopPropagation();

			// Remove the file preview.
			_this.removeFile(file);
			// If you want to the delete the file on the server as well,
			// you can do the AJAX request here.
		});

		// Add the button to the file preview element.
		file.previewElement.appendChild(removeButton);
	});

})