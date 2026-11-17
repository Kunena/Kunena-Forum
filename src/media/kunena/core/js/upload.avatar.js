/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(function ($) {
	'use strict';

	let fileCount = null;
	const max_avatar = 1;

	const removeButton = $('<button/>')
		.addClass('btn btn-danger')
		.attr('type', 'button')
		.html(Joomla.getOptions('com_kunena.avatar_delete') + ' ' + Joomla.Text._('COM_KUNENA_GEN_REMOVE_AVATAR'))
		.on('click', function () {
			const $this = $(this),
				data = $this.data();

			let userid;
			let avatar;

			userid = data.userid;
			avatar = data.filename;

			fileCount = fileCount - 1;

			// Ajax Request to delete the file from filesystem
			$.ajax({
				url: Joomla.getOptions('com_kunena.avatar_remove_url') + '&userid=' + encodeURIComponent(userid) + '&avatar=' + encodeURIComponent(avatar),
				type: 'POST'
			})
				.done(function (data) {
					$this.parent().remove();
				})
				.fail(function () {
					//TODO: handle the error of ajax request
				});
		});

	// The upload of the avatar (uploader.js): the file is sent as soon as it is chosen or dropped; one avatar, so one file.
	KunenaUploader.attach(document.getElementById('fileupload'), {
		url: Joomla.getOptions('com_kunena.avatar_upload_url'),
		params: function (file) {
			return {
				'userid': $('#kunena_userid').val(),
				'filename': file.name,
				'size': file.size,
				'mime': file.type
			};
		},
		// A photo larger than this is made smaller before it is sent (the forum makes the sizes of the avatars itself).
		resize: {maxWidth: 1920, maxHeight: 1080},
		previewWidth: 100,
		previewHeight: 100,
		accept: function (files) {
			fileCount = 1;

			return files.slice(0, max_avatar);
		},
		add: function (file) {
			$('#progress .bar').css('width', '0%');
			$('#files').empty();

			const context = $('<div/>').appendTo('#files');

			$('<p/>').append($('<span/>').text(file.name)).append('<br>').appendTo(context);

			return context;
		},
		preview: function (canvas, file, context) {
			$(context.children()[0]).prepend('<br>').prepend(canvas);
		},
		progress: function (loaded, total) {
			$('#progress .bar').css('width', (total ? parseInt(loaded / total * 100, 10) : 0) + '%');
		},
		done: function (result, context, file) {
			if (result.location) {
				context.find('span').wrap($('<a>').attr('target', '_blank').prop('href', result.location));
			}

			context.find('button.btn-danger').remove();
			// The button removes the avatar of the user (the server takes the user of the session).
			context.append(removeButton.clone(true).data({
				userid: $('#kunena_userid').val(),
				filename: file.name
			}));

			if (result.success === true) {
				$('#form_submit_button').prop('disabled', false);
			} else if (result.message) {
				$('#form_submit_button').prop('disabled', false);

				const error = $('<div class="alert alert-danger" role="alert" />').text(result.message);

				context.find('span').append('<br>').append(error);
			}
		},
		fail: function (message, context) {
			$(context.children()[0]).append('<br>').append($('<span class="text-danger"/>').text(message));
		}
	});

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

					const name = data.name !== undefined ? data.name : '';

					const object = $('<div><p><img alt="" src="' + data.path + '" width="100" height="100" /><br /><span>' + name + '</span><br /></p></div>');
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