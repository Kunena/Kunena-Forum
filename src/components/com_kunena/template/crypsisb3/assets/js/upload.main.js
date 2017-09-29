jQuery(function ($) {
	'use strict';

	$.widget('blueimp.fileupload', $.blueimp.fileupload, {
		options: {
			// The maximum width of resized images:
			imageMaxWidth: imagewidth,
			// The maximum height of resized images:
			imageMaxHeight: imageheight
		}
	});

	// Insert bbcode in message
	function insertInMessage(attachid, filename, button) {
		var value = $('#kbbcode-message').val();

		$('#kbbcode-message').insertAtCaret(' [attachment=' + attachid + ']' + filename + '[/attachment]');

		if (button != undefined) {
			button.removeClass('btn-primary');
			button.addClass('btn-success');
			button.html('<i class="glyphicon glyphicon-upload"></i> ' + Joomla.JText._('COM_KUNENA_EDITOR_IN_MESSAGE'));
		}
	}

	jQuery.fn.extend({
		insertAtCaret: function(myValue){
			return this.each(function(i) {
				if (document.selection) {
					//For browsers like Internet Explorer
					this.focus();
					//noinspection JSUnresolvedVariable
					sel = document.selection.createRange();
					sel.text = myValue;
					this.focus();
				}
				else if (this.selectionStart || this.selectionStart == '0') {
					//For browsers like Firefox and Webkit based
					var startPos = this.selectionStart;
					var endPos = this.selectionEnd;
					var scrollTop = this.scrollTop;
					this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
					this.focus();
					this.selectionStart = startPos + myValue.length;
					this.selectionEnd = startPos + myValue.length;
					this.scrollTop = scrollTop;
				} else {
					this.value += myValue;
					this.focus();
				}
			})
		}
	});

	var fileCount = null;
	var filesedit = null;

	$('#remove-all').on('click', function (e) {
		e.preventDefault();

		$('#insert-all').removeClass('btn-success');
		$('#insert-all').addClass('btn-primary');
		$('#insert-all').html('<i class="glyphicon glyphicon-upload"></i>' + Joomla.JText._('COM_KUNENA_UPLOADED_LABEL_INSERT_ALL_BUTTON'));

		$('#remove-all').hide();
		$('#insert-all').hide();

		// Removing items in edit if they are present
		if ($.isEmptyObject(filesedit) == false) {
			$(filesedit).each(function (index, file) {
				if ($('#kattachs-' + file.id).length == 0) {
					$('#kattach-list').append('<input id="kattachs-' + file.id + '" type="hidden" name="attachments[' + file.id + ']" value="1" />');
				}

				if ($('#kattach-' + file.id).length > 0) {
					$('#kattach-' + file.id).remove();
				}

				$.ajax({
					url    : kunena_upload_files_rem + '&fil_id=' + file.id,
					type   : 'POST',
					success: function (result) {
						$('#files').empty();
					}
				});
			});

			filesedit = null;
		}

		var child = $('#kattach-list').find('input');

		child.each(function (i, el) {
			var elem = $(el);

			if (!elem.attr('id').match("[a-z]{8}")) {
				var fileid = elem.attr('id').match("[0-9]{2}");

				if ($('#kattachs-' + fileid).length == 0) {
					$('#kattach-list').append('<input id="kattachs-' + fileid + '" type="hidden" name="attachments[' + fileid + ']" value="1" />');
				}

				if ($('#kattach-' + fileid).length > 0) {
					$('#kattach-' + fileid).remove();
				}

				$.ajax({
					url    : kunena_upload_files_rem + '&fil_id=' + fileid,
					type   : 'POST',
					success: function (result) {
						$('#files').empty();
					}
				});
			}
		});

		$('#alert_max_file').remove();

		fileCount = 0;
	});

	$('#insert-all').on('click', function (e) {
		e.preventDefault();

		// Inserting items from edit if they are present
		if ($.isEmptyObject(filesedit) == false) {
			$(filesedit).each(function (index, file) {
				insertInMessage(file.id, file.name);
			});
		}
		filesedit = null;

		var child = $('#kattach-list').find('input');

		child.each(function (i, el) {
			var elem = $(el);

			if (!elem.attr('id').match("[a-z]{8}")) {
				var attachid = elem.attr('id').match("[0-9]{1,8}");
				var filename = elem.attr('placeholder');

				insertInMessage(attachid, filename);

				$('#insert-all').removeClass('btn-primary');
				$('#insert-all').addClass('btn-success');
				$('#insert-all').html('<i class="glyphicon glyphicon-upload"></i> ' + Joomla.JText._('COM_KUNENA_EDITOR_IN_MESSAGE'));
			}
		});

		$('#files .btn.btn-primary').each(function () {
			$('#files .btn.btn-primary').addClass('btn-success');
			$('#files .btn.btn-success').removeClass('btn-primary');
			$('#files .btn.btn-success').html('<i class="glyphicon glyphicon-upload"></i> ' + Joomla.JText._('COM_KUNENA_EDITOR_IN_MESSAGE'));
		});
	});

	var insertButton = $('<button>')
		.addClass("btn btn-primary")
		.html('<i class="glyphicon glyphicon-upload"></i> ' + Joomla.JText._('COM_KUNENA_EDITOR_INSERT'))
		.on('click', function (e) {
			// Make sure the button click doesn't submit the form:
			e.preventDefault();
			e.stopPropagation();

			var $this = $(this),
				data = $this.data();

			var file_id = 0;
			var filename = null;
			if (data.result != undefined) {
				file_id = data.result.data.id;
				filename = data.result.data.filename;
			} else {
				file_id = data.id;
				filename = data.name;
			}

			insertInMessage(file_id, filename, $this);
		});

	var removeButton = $('<button/>')
		.addClass('btn btn-danger')
		.attr('type', 'button')
		.html('<i class="glyphicon glyphicon-trash"></i> ' + Joomla.JText._('COM_KUNENA_GEN_REMOVE_FILE'))
		.on('click', function () {
			var $this = $(this),
				data = $this.data();

			$('#klabel_info_drop_browse').show();

			var file_id = 0;
			if (data.uploaded == true) {
				if (data.result != false) {
					file_id = data.result.data.id;
				} else {
					file_id = data.file_id;
				}
			}

			if ($('#kattachs-' + file_id).length == 0) {
				$('#kattach-list').append('<input id="kattachs-' + file_id + '" type="hidden" name="attachments[' + file_id + ']" value="1" />');
			}

			if ($('#kattach-' + file_id).length > 0) {
				$('#kattach-' + file_id).remove();
			}

			fileCount = fileCount - 1;

			$('#alert_max_file').remove();

			// Ajax Request to delete the file from filesystem
			$.ajax({
				url    : kunena_upload_files_rem + '&fil_id=' + file_id,
				type: 'POST',
				success: function (result) {
					$this.parent().remove();
				}
			});
		});

	$('#fileupload').fileupload({
		url               : $('#kunena_upload_files_url').val(),
		dataType          : 'json',
		autoUpload        : true,
		// Enable image resizing, except for Android and Opera,
		// which actually support image resizing, but fail to
		// send Blob objects via XHR requests:
		disableImageResize: /Android(?!.*Chrome)|Opera/
			.test(window.navigator.userAgent),
		previewMaxWidth   : 100,
		previewMaxHeight  : 100,
		previewCrop       : true
	}).bind('fileuploadsubmit', function (e, data) {
			var params = {};
			$.each(data.files, function (index, file) {
				params = {
					'catid'   : $('#kunena_upload').val(),
					'filename': file.name,
					'size'    : file.size,
					'mime'    : file.type
				};
			});

			data.formData = params;
		})
		.bind('fileuploaddrop', function (e, data) {
			$('#form_submit_button').prop('disabled', true);

			$('#insert-all').show();
			$('#remove-all').show();

			var filecoutntmp = Object.keys(data['files']).length + fileCount;

			if (filecoutntmp > kunena_upload_files_maxfiles) {
				$('<div class="alert alert-danger" id="alert_max_file"><button class="close" type="button" data-dismiss="alert">×</button>' + Joomla.JText._('COM_KUNENA_UPLOADED_LABEL_ERROR_REACHED_MAX_NUMBER_FILES') + '</div>').insertBefore($('#files'));

				$('#form_submit_button').prop('disabled', false);

				return false;
			}
			else {
				fileCount = Object.keys(data['files']).length + fileCount;
			}
		})
		.bind('fileuploadchange', function (e, data) {
			$('#form_submit_button').prop('disabled', true);

			$('#insert-all').show();
			$('#remove-all').show();

			var filecoutntmp = Object.keys(data['files']).length + fileCount;

			if (filecoutntmp > kunena_upload_files_maxfiles) {
				$('<div class="alert alert-danger" id="alert_max_file"><button class="close" type="button" data-dismiss="alert">×</button>' + Joomla.JText._('COM_KUNENA_UPLOADED_LABEL_ERROR_REACHED_MAX_NUMBER_FILES') + '</div>').insertBefore($('#files'));

				$('#form_submit_button').prop('disabled', false);

				return false;
			}
			else {
				fileCount = Object.keys(data['files']).length + fileCount;
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
						.append('<br>');
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
	}).on('fileuploaddone', function (e, data) {
		// $.each(data.result.data, function (index, file)

		var link = $('<a>')
			.attr('target', '_blank')
			.prop('href', data.result.location);

		data.context.find('span')
			.wrap(link);

		if (data.result.success == true) {
			$('#form_submit_button').prop('disabled', false);

			// The attachment has been right uploaded, so now we need to put into input hidden to added to message
			$('#kattach-list').append('<input id="kattachs-' + data.result.data.id + '" type="hidden" name="attachments[' + data.result.data.id + ']" value="1" />');
			$('#kattach-list').append('<input id="kattach-' + data.result.data.id + '" placeholder="' + data.result.data.filename + '" type="hidden" name="attachment[' + data.result.data.id + ']" value="1" />');

			data.uploaded = true;

			data.context.append(insertButton.clone(true).data(data));
			if (data.context.find('button').hasClass('btn-danger')) {
				data.context.find('button.btn-danger').remove();
			}
			data.context.append(removeButton.clone(true).data(data));
		} else if (data.result.message) {
			data.uploaded = false;
			data.context.append(removeButton.clone(true).data(data));

			var error = null;
			$.each(data.result.data.exceptions, function (index, error) {
				error = $('<div class="alert alert-error"/>').text(error.message);
				data.context.find('span')
					.append('<br>')
					.append(error);
			});
		}
	}).on('fileuploadfail', function (e, data) {
		$.each(data.files, function (index, file) {
			var error = $('<span class="text-danger"/>').text(file.error);
			$(data.context.children()[index])
				.append('<br>')
				.append(error);
		});
	}).prop('disabled', !$.support.fileInput)
		.parent().addClass($.support.fileInput ? undefined : 'disabled');

	// Load attachments when the message is edited
	if ($('#kmessageid').val() > 0) {
		$.ajax({
			type    : 'POST',
			url     : kunena_upload_files_preload,
			async   : false,
			dataType: 'json',
			data    : {mes_id: $('#kmessageid').val()},
			success : function (data) {
				if ($.isEmptyObject(data.files) == false) {
					fileCount = Object.keys(data.files).length;

					filesedit = data.files;

					$(data.files).each(function (index, file) {
						var image = '';
						if (file.image === true) {
							image = '<img src="' + file.path + '" width="100" height="100" /><br />';
						} else {
							image = '<i class="glyphicon glyphicon-paperclip glyphicon-big"></i><br />';
						}

						var object = $('<div><p>' + image + '<span>' + file.name + '</span><br /></p></div>');
						data.uploaded = true;
						data.result = false;
						data.file_id = file.id;

						object.append(insertButton.clone(true).data(file));
						object.append(removeButton.clone(true).data(data));

						object.appendTo("#files");
					});
				}
			}
		});
	}
});
