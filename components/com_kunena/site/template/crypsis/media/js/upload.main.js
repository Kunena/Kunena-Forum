jQuery(function($) {
	'use strict';

	// Insert bbcode in message
	function insertInMessage(attachid,filename) {	
		var value = jQuery('#kbbcode-message').val();

		jQuery('#kbbcode-message').val(value+' [attachment='+attachid+']'+filename+'[/attachment]');
	}

	var uploadButton = $('<button/>')
		.addClass('btn btn-primary')
		.attr('type', 'button')
		.prop('disabled', true)
		.text(Joomla.JText._('COM_KUNENA_UPLOADED_LABEL_PROCESSING_BUTTON'))
		.on('click', function () {
			var $this = $(this),
				data = $this.data();
			$this
				.off('click')
				.text(Joomla.JText._('COM_KUNENA_UPLOADED_LABEL_ABORT_BUTTON'))
				.on('click', function () {
					$this.remove();
					data.abort();
				});

				data.submit().always(function () {
					$this.remove();
				});
		});

	var removeButton = $('<button/>')
		.addClass('btn btn-danger')
		.attr('type', 'button')
		.text(Joomla.JText._('COM_KUNENA_GEN_REMOVE_FILE'))
		.on('click', function () {
			var $this = $(this),
				data = $this.data(); 

			// Check if file exist on server
			if(data.onserver!==false)
			{
				var file_id = 0;
				if (data.result!=false){
					file_id = data.result.data.id;
				} else {
					file_id = data.file_id;
				}

				if ( jQuery('#kattachs-'+file_id).length > 0 ) {
					jQuery('#kattachs-'+file_id).remove();
				}

				if ( jQuery('#kattach-'+file_id).length > 0 ) {
					jQuery('#kattach-'+file_id).remove();
				}

				// Ajax Request to delete the file from filesystem
				jQuery.ajax({
					url: kunena_upload_files_rem+'&fil_id='+file_id,
					type: 'DELETE',
					success: function(result) {
						$this.parent().remove();
					}
				});
			}
			else
			{
				$this.parent().parent().remove();
			} 
	});

	function getNumberOfFiles()
	{
		return $('#files').children().not('.processing').length;
	}

	var maxFiles = kunena_upload_files_maxfiles;

	$('#fileupload').fileupload({
		url: jQuery('#kunena_upload_files_url').val(),
		dataType: 'json',
		autoUpload: false,
		singleFileUploads: false,
		// Enable image resizing, except for Android and Opera,
		// which actually support image resizing, but fail to
		// send Blob objects via XHR requests:
		disableImageResize: /Android(?!.*Chrome)|Opera/
			.test(window.navigator.userAgent),
		previewMaxWidth: 100,
		previewMaxHeight: 100,
		previewCrop: true
	}).bind('fileuploadsubmit', function (e, data) {
		var params = {};
		$.each(data.files, function (index, file) {
			params = { 'catid': jQuery('#kunena_upload').val(), 'filename': file.name, 'size': file.size, 'mime': file.type }; 
		});

		data.formData = params;
	})
	.bind('fileuploadchange', function (e, data) {
		var fileCount = getNumberOfFiles()+1; 

		if (fileCount > maxFiles) {
			$('<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert">×</button>'+Joomla.JText._('COM_KUNENA_UPLOADED_LABEL_ERROR_REACHED_MAX_NUMBER_FILES')+'</div>').insertBefore( $('#progress') );

			return false; 
		}
	})  
	.on('fileuploadadd', function (e, data) {
		$('#progress .bar').css(
			'width',
			'0%'
		);

		data.context = $('<div/>').appendTo('#files');
		
		$.each(data.files, function (index, file) {
			var node = $('<p/>')
				.append($('<span/>').text(file.name));
			if (!index) {
				node
					.append('<br>')
					.append(uploadButton.clone(true).data(data));
				data.onserver = false;    
				node.append(removeButton.clone(true).data(data));
			}
			node.appendTo(data.context);
		});
	}).on('fileuploadprocessalways', function (e, data) {
		var index = data.index,
			file = data.files[index],
			node = $(data.context.children()[index]);
		if (file.preview) {
			node
				.prepend('<br>')
				.prepend(file.preview);
		}
		if (file.error) {
			node
				.append('<br>')
				.append($('<span class="text-danger"/>').text(file.error));
		}
		if (index + 1 === data.files.length) {
			data.context.find('button.btn-primary')
				.text(Joomla.JText._('COM_KUNENA_UPLOADED_LABEL_UPLOAD_BUTTON'))
				.prop('disabled', !!data.files.error);
		}
	}).on('fileuploadprogressall', function (e, data) {
		var progress = parseInt(data.loaded / data.total * 100, 10);
		$('#progress .bar').css(
			'width',
			progress + '%'
		);
	}).on('fileuploaddone', function (e, data) {
		// $.each(data.result.data, function (index, file) 
		//console.log(data.result.data);
		if (data.result.success==true) {
			// The attachment has been right uploaded, so now we need to put into input hidden to added to message
			jQuery('#kattach-list').append('<input id="kattachs-'+data.result.data.id+'" type="hidden" name="attachments['+data.result.data.id+']" value="1" />');
			jQuery('#kattach-list').append('<input id="kattach-'+data.result.data.id+'" type="hidden" name="attachment['+data.result.data.id+']" value="1" />');

			var link = $('<a>')
				.attr('target', '_blank')
				.prop('href', data.result.location);

			var insertButton = $('<button>').addClass("btn btn-primary").text(Joomla.JText._('COM_KUNENA_EDITOR_INSERT'));

			insertButton.click(function(e) {
				// Make sure the button click doesn't submit the form:
				e.preventDefault();
				e.stopPropagation();

				insertInMessage(data.result.data.id,data.result.data.filename);
			});

			data.context.find('span')
				.wrap(link);
			data.context.append(insertButton); 
			if (data.context.find('button').hasClass('btn-danger'))
			{
				data.context.find('button.btn-danger').remove();
			}
			data.context.append(removeButton.clone(true).data(data));
		} else if (data.result.message) {
			var error = $('<div class="alert alert-error"/>').text(data.result.message);
			data.context.find('span')
				.append('<br>')
				.append(error);
		} 
	}).on('fileuploadfail', function (e, data) {
		$.each(data.files, function (index, file) {
			// TODO: replace text with error message from server fi possible
			var error = $('<span class="text-danger"/>').text('File upload failed.');
			$(data.context.children()[index])
				.append('<br>')
				.append(error);
			});
	}).prop('disabled', !$.support.fileInput)
		.parent().addClass($.support.fileInput ? undefined : 'disabled');

	// Load attachments when the message is edited
	if ( jQuery('#kmessageid').val() > 0 ) {      
		jQuery.ajax({
			type: 'POST',
			url: kunena_upload_files_preload,
			async: false,
			dataType: 'json',
			data: {mes_id : jQuery('#kmessageid').val() },
			success: function(data){
				$( data.files ).each(function( index, file ) {
					var object = $( '<div><p><img src="'+file.path+'" width="100" height="100" /><br /><span>'+file.name+'</span><br /></p></div>' );
					data.onserver = true;
					data.result= false;
					data.file_id = file.id;
					object.append(removeButton.clone(true).data(data));
					object.appendTo( "#files" );
				});
			}
		});
	}
});