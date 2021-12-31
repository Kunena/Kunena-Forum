/**
 * Kunena Component
 * @package Kunena.Template.Crypsisb4
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(function ($) {
	'use strict';

	var fileCount = null;
	var filesedit = null;
	var max_avatar = 1;

	var removeButton = $('<button/>')
		.addClass('btn btn-danger')
		.attr('type', 'button')
		.html(Joomla.getOptions('com_kunena.avatar_delete') + ' ' + Joomla.JText._('COM_KUNENA_GEN_REMOVE_AVATAR'))
		.on('click', function () {
			var $this = $(this),
				data = $this.data();

			if (data['files'] !== undefined) {
				var userid = data['files'].userid;
				var avatar = data['files'].filename;
			} else {
				var userid = data.userid;
				var avatar = data.filename;
			}

			fileCount = fileCount - 1;

			// Ajax Request to delete the file from filesystem
			$.ajax({
				url: Joomla.getOptions('com_kunena.avatar_remove_url') + '&userid=' + userid + '&avatar=' + avatar,
				type: 'POST',
			})
				.done(function (data) {
					$this.parent().remove();
				})
				.fail(function () {
					//TODO: handle the error of ajax request
				});
		});

	$('#fileupload').fileupload({
		url: Joomla.getOptions('com_kunena.avatar_upload_url'),
		dataType: 'json',
		autoUpload: true,
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
			params = {
				'userid': $('#kunena_userid').val(),
				'filename': file.name,
				'size': file.size,
				'mime': file.type
			};
		});

		data.formData = params;
	})
		.bind('fileuploaddrop', function (e, data) {
			var filecoutntmp = Object.keys(data['files']).length + fileCount;

			fileCount = Object.keys(data['files']).length + fileCount;
		})
		.bind('fileuploadchange', function (e, data) {
			var filecoutntmp = Object.keys(data['files']).length + fileCount;

			fileCount = Object.keys(data['files']).length + fileCount;
		})
		.on('fileuploadadd', function (e, data) {
			$('#progress .bar').css(
				'width',
				'0%'
			);

			$('#files').empty();

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
				.text('COM_KUNENA_UPLOADED_LABEL_UPLOAD_BUTTON')
				.prop('disabled', !!data.files.error);
		}
	}).on('fileuploaddone', function (e, data) {
		// $.each(data.result.data, function (index, file)

		var link = $('<a>')
			.attr('target', '_blank')
			.prop('href', data.result.location);

		data.context.find('span')
			.wrap(link);

		if (data.result.success === true) {
			$('#form_submit_button').prop('disabled', false);

			// The attachment has been right uploaded, so now we need to put into input hidden to added to message
			$('#kattach-list').append('<input id="kattachs-' + data.result.data.id + '" type="hidden" name="attachments[' + data.result.data.id + ']" value="1" />');
			$('#kattach-list').append('<input id="kattach-' + data.result.data.id + '" placeholder="' + data.result.data.filename + '" type="hidden" name="attachment[' + data.result.data.id + ']" value="1" />');

			data.uploaded = true;

			if (data.context.find('button').hasClass('btn-danger')) {
				data.context.find('button.btn-danger').remove();
			}
			data.context.append(removeButton.clone(true).data(data));
		} else if (data.result.message) {
			$('#form_submit_button').prop('disabled', false);

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

	if ($('#kunena_userid').val() > 0) {
		$.ajax({
			type: 'POST',
			url: Joomla.getOptions('com_kunena.avatar_preload_url'),
			async: true,
			dataType: 'json',
			data: {userid: $('#kunena_userid').val()}
		})
			.done(function (data) {
				if ($.isEmptyObject(data) === false) {
					fileCount = 1;

					if (data.name != undefined) {
						var name = data.name;
					}
					else {
						var name = '';
					}

					var object = $('<div><p><img src="' + data.path + '" width="100" height="100" /><br /><span>' + name + '</span><br /></p></div>');
					data.uploaded = true;
					data.result = false;

					data.userid = $('#kunena_userid').val();
					data.filename = data.name;

					object.append(removeButton.clone(true).data(data));

					object.appendTo("#files");
				}
			})
			.fail(function () {
				//TODO: handle the error of ajax request
			});
	}
});
