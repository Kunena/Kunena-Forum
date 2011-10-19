var Kunena = new Class({

	initialize: function() {
	}

});

Kunena.Uploader = new Class({

	options: {
		runtimes : 'gears,html5,flash,silverlight,browserplus',

		browse_button_hover: 'ui-state-hover',
		browse_button_active: 'ui-state-active',

		// widget specific
		dragdrop : true,
		multiple_queues: true, // re-use widget by default

		buttons: {
			browse: true,
			start: true,
			stop: true
		},
		autostart: true,
		max_file_count: 0 // unlimited
	},

	contents_bak: '',
	FILE_COUNT_ERROR: -9001,

	initialize: function(name, options) {
		var self = this, id, uploader;

		this.options = Object.merge(options, this.options);
		if (this.options.autostart === true) this.options.buttons.start = false;

		this.element = document.id(name);

		id = this.element.get('id');
		if (!id) {
			id = plupload.guid();
			this.element.set('id', id);
		}
		this.id = id;

		this.contents_bak = this.element.get('html');
		this._render(name);

		// container, just in case
		this.container = this.element.getElement('.plupload_container').set('id', id + '_container');

		// list of files, may become sortable
		this.filelist = this.container.getElement('.plupload_filelist_content').set({
			id: id + '_filelist',
			unselectable: 'on'
		});

		// buttons
		this.browse_button = this.container.getElement('.plupload_add').set('id', id + '_browse');
		this.start_button = this.container.getElement('.plupload_start').set('id', id + '_start');
		this.stop_button = this.container.getElement('.plupload_stop').set('id', id + '_stop');

		if (this.options.buttons.start === false) this.start_button.addClass('plupload_hidden');

		// counter
		this.counter = this.element.getElement('.plupload_count').set({
			id: id + '_count',
			name: id + '_count'
		});

		// initialize uploader instance
		uploader = this.uploader = new plupload.Uploader(Object.merge({
			container: id,
			browse_button: id + '_browse'
		}, this.options));

		uploader.bind('Init', function(up, res) {
			if (self.uploader.features.dragdrop && self.options.dragdrop) {
				self._enableDragAndDrop();
			}

			self.container.set('title', 'Using runtime: ' + (self.runtime = res.runtime));

			self.start_button.addEvent('click', function(e) {
				//if (!$(this).button('option', 'disabled')) {
					self.start();
				//}
				//e.preventDefault();
			});

			self.stop_button.addEvent('click', function(e) {
				self.stop();
				//e.preventDefault();
			});
		});

		uploader.init();

		// check if file count doesn't exceed the limit
		if (this.options.max_file_count) {
			uploader.bind('FilesAdded', function(up, selectedFiles) {
				var removed = [], selectedCount = selectedFiles.length;
				var extraCount = up.files.length + selectedCount - self.options.max_file_count;

				if (extraCount > 0) {
					removed = selectedFiles.splice(selectedCount - extraCount, extraCount);
					//self.uploader.splice(selectedCount - extraCount, extraCount);

					up.trigger('Error', {
						code : self.FILE_COUNT_ERROR,
						message : 'File count error.',
						file : removed
					});
				}
			});
		}

		uploader.bind('FilesAdded', function(up, files) {
			//self._trigger('selected', null, { up: up, files: files } );

			if (self.options.autostart) {
				self.start();
			}
		});

		uploader.bind('FilesRemoved', function(up, files) {
			//self._trigger('removed', null, { up: up, files: files } );
		});

		uploader.bind('QueueChanged', function() {
			self._updateFileList();
		});

		uploader.bind('StateChanged', function() {
			self._handleState();
		});

		uploader.bind('UploadFile', function(up, file) {
			self._handleFileStatus(file);
		});

		uploader.bind('FileUploaded', function(up, file) {
			self._handleFileStatus(file);

			//self._trigger('uploaded', null, { up: up, file: file } );
		});

		uploader.bind('UploadProgress', function(up, file) {
			// Set file specific progress
			self.element.getElement('#' + file.id + ' .plupload_file_status').set('html', file.percent + '%');

			self._handleFileStatus(file);
			self._updateTotalProgress();

			//self._trigger('progress', null, { up: up, file: file } );
		});

		uploader.bind('UploadComplete', function(up, files) {
			//self._trigger('complete', null, { up: up, files: files } );
		});

		uploader.bind('Error', function(up, err) {
			var file = err.file, message, details;

			if (file) {
				message = '<strong>' + err.message + '</strong>';
				details = err.details;

				if (details) {
					message += " <br /><i>" + err.details + "</i>";
				} else {
					switch (err.code) {
						case plupload.FILE_EXTENSION_ERROR:
							details = "File: %s".replace('%s', file.name);
							break;
						
						case plupload.FILE_SIZE_ERROR:
							details = "File: %f, size: %s, max file size: %m".replace(/%([fsm])/g, function($0, $1) {
								switch ($1) {
									case 'f': return file.name;
									case 's': return file.size;
									case 'm': return plupload.parseSize(self.options.max_file_size);
								}
							});
							break;

						case self.FILE_COUNT_ERROR:
							details = "Upload element accepts only %d file(s) at a time. Extra files were stripped."
								.replace('%d', self.options.max_file_count);
							break;

						case plupload.IMAGE_FORMAT_ERROR :
							details = plupload.translate('Image format either wrong or not supported.');
							break;

						case plupload.IMAGE_MEMORY_ERROR :
							details = plupload.translate('Runtime ran out of available memory.');
							break;

						case plupload.IMAGE_DIMENSIONS_ERROR :
							details = plupload.translate('Resoultion out of boundaries! <b>%s</b> runtime supports images only up to %wx%hpx.').replace(/%([swh])/g, function($0, $1) {
								switch ($1) {
									case 's': return up.runtime;
									case 'w': return up.features.maxWidth;	
									case 'h': return up.features.maxHeight;
								}
							});
							break;

						case plupload.HTTP_ERROR:
							details = "Upload URL might be wrong or doesn't exist";
							break;
					}
					message += " <br /><i>" + details + "</i>";
				}

				self.notify('error', message);
				//self._trigger('error', null, { up: up, file: file, error: message } );
			}
		});
	},

	start: function() {
		this.uploader.start();
		//this._trigger('start', null);
	},

	stop: function() {
		this.uploader.stop();
		//this._trigger('stop', null);
	},

	getFile: function(id) {
		var file;

		if (typeof id === 'number') {
			file = this.uploader.files[id];
		} else {
			file = this.uploader.getFile(id);
		}
		return file;
	},

	removeFile: function(id) {
		var file = this.getFile(id);
		if (file) {
			this.uploader.removeFile(file);
		}
	},

	clearQueue: function() {
		this.uploader.splice();
	},

	getUploader: function() {
		return this.uploader;
	},

	refresh: function() {
		this.uploader.refresh();
	},

	notify: function(type, message) {
		var popup = new Element('div', {
			'class': 'plupload_message', 
			'html': '<div class="plupload_message"><span class="plupload_message_close ui-icon ui-icon-circle-close" title="'+'Close'+'"></span>'+
					'<p><span class="ui-icon"></span>' + message + '</p></div>'
		});

		popup.addClass('ui-state-' + (type === 'error' ? 'error' : 'highlight'));
		popup.getElements('p .ui-icon').addClass('ui-icon-' + (type === 'error' ? 'alert' : 'info'));
		popup.getElements('.plupload_message_close').addEvent('click', (function() {
			popup.destroy();
		}));

		this.container.getElement('.plupload_header_content').grab(popup);
	},

	_render: function(name) {
		document.id(name).set('html',
				'<div class="plupload_wrapper">' +
				'<div class="ui-widget-content plupload_container">' +
				'<div class="plupload">' +
				'<div class="ui-state-default ui-widget-header plupload_header">' +
				'<div class="plupload_header_content">' +
				'<div class="plupload_header_title">' + 'Select files' + '</div>' +
				'<div class="plupload_header_text">' + 'Add files to the upload queue and click the start button.' + '</div>' +
				'</div>' +
				'</div>' +

				'<div class="plupload_content">' +
				'<table class="plupload_filelist">' +
				'<tr class="ui-widget-header plupload_filelist_header">' +
				'<td class="plupload_cell plupload_file_name">' + 'Filename' + '</td>' +
				'<td class="plupload_cell plupload_file_status">' + 'Status' + '</td>' +
				'<td class="plupload_cell plupload_file_size">' + 'Size' + '</td>' +
				'<td class="plupload_cell plupload_file_action">&nbsp;</td>' +
				'</tr>' +
				'</table>' +

				'<div class="plupload_scroll">' +
				'<table class="plupload_filelist_content"></table>' +
				'</div>' +

				'<table class="plupload_filelist">' +
				'<tr class="ui-widget-header ui-widget-content plupload_filelist_footer">' +
				'<td class="plupload_cell plupload_file_name">' +

				'<div class="plupload_buttons"><!-- Visible -->' +
				'<a class="plupload_button plupload_add">' + 'Add Files' + '</a>&nbsp;' +
				'<a class="plupload_button plupload_start">' + 'Start Upload' + '</a>&nbsp;' +
				'<a class="plupload_button plupload_stop plupload_hidden">'+ 'Stop Upload' + '</a>&nbsp;' +
				'</div>' +

				'<div class="plupload_cell plupload_upload_status"></div>' +

				'<div class="plupload_clearer">&nbsp;</div>' +

				'</div>' +
				'</td>' +
				'<td class="plupload_file_status"><span class="plupload_total_status">0%</span></td>' +
				'<td class="plupload_file_size"><span class="plupload_total_file_size">0 kb</span></td>' +
				'<td class="plupload_file_action"></td>' +
				'</tr>' +
				'</table>' +
				'</div>' +
				'</div>' +
				'</div>' +
				'<input class="plupload_count" value="0" type="hidden">' +
				'</div>'
				);
	},

	_handleState: function() {
		var self = this, uploader = this.uploader;

		if (uploader.state === plupload.STARTED) {

			self.start_button.addClass('plupload_hidden');
			if (this.options.buttons.stop === true) self.stop_button.removeClass('plupload_hidden');

			self.element.getElement('.plupload_upload_status').set('text',
				'Uploaded %d/%d files'.replace('%d/%d', uploader.total.uploaded+'/'+uploader.files.length)
			);

			self.element.getElement('.plupload_header_content').addClass('plupload_header_content_bw');

		} else {

			self.stop_button.addClass('plupload_hidden');
			if (this.options.buttons.start === true) self.start_button.removeClass('plupload_hidden');

			if (self.options.multiple_queues) {
				self.element.getElement('.plupload_header_content').removeClass('plupload_header_content_bw');
			}

			self._updateFileList();
		}
	},
	
	_handleFileStatus: function(file) {
		var actionClass, iconClass;

		switch (file.status) {
			case plupload.DONE: 
				actionClass = 'plupload_done';
				iconClass = 'ui-icon ui-icon-circle-check';
				break;
			
			case plupload.FAILED:
				actionClass = 'ui-state-error plupload_failed';
				iconClass = 'ui-icon ui-icon-alert';
				break;

			case plupload.QUEUED:
				actionClass = 'plupload_delete';
				iconClass = 'ui-icon ui-icon-circle-minus';
				break;

			case plupload.UPLOADING:
				actionClass = 'ui-state-highlight plupload_uploading';
				iconClass = 'ui-icon ui-icon-circle-arrow-w';
				break;
		}
		actionClass += ' ui-state-default plupload_file';

		var entry = document.id(file.id);
		if (entry) entry.set('class', actionClass).getElements('.ui-icon').set('class', iconClass);
	},

	_updateTotalProgress: function() {
		var uploader = this.uploader;

		this.element.getElement('.plupload_total_status').set('html', uploader.total.percent + '%');

		this.element.getElement('.plupload_upload_status').set('text',
			'Uploaded %d/%d files'.replace('%d/%d', uploader.total.uploaded+'/'+uploader.files.length)
		);
	},

	_updateFileList: function() {
		var self = this, uploader = this.uploader, filelist = this.filelist, 
			count = 0, 
			id, prefix = this.id + '_',
			fields;
			
		filelist.empty();

		Array.each(uploader.files, function(file) {
			fields = '';
			id = prefix + count;

			if (file.status === plupload.DONE) {
				if (file.target_name) {
					fields += '<input type="hidden" name="' + id + '_tmpname" value="'+plupload.xmlEncode(file.target_name)+'" />';
				}
				fields += '<input type="hidden" name="' + id + '_name" value="'+plupload.xmlEncode(file.name)+'" />';
				fields += '<input type="hidden" name="' + id + '_status" value="' + (file.status === plupload.DONE ? 'done' : 'failed') + '" />';

				count++;
				self.counter.set('value', count);
			}

			var html = new Element('tr', {
				class: 'ui-state-default plupload_file', 
				id: file.id, 
				html: '<td class="plupload_cell plupload_file_name"><span>' + file.name + '</span></td>' +
				'<td class="plupload_cell plupload_file_status">' + file.percent + '%</td>' +
				'<td class="plupload_cell plupload_file_size">' + plupload.formatSize(file.size) + '</td>' +
				'<td class="plupload_cell plupload_file_action"><div class="ui-icon"></div>' + fields + '</td>'
				}
			);
			filelist.grab(html);

			self._handleFileStatus(file);

			$$('#' + file.id + ' .ui-icon').addEvent('click', function(e) {
				document.id(file.id).destroy();
				uploader.removeFile(file);

				//e.preventDefault();
			});

			//self._trigger('updatelist', null, filelist);
		});

		self.element.getElement('.plupload_total_file_size').set('html',plupload.formatSize(uploader.total.size));

		if (uploader.total.queued === 0) {
			self.browse_button.set('text', 'Add Files');
		} else {
			self.browse_button.set('text', '%d files queued'.replace('%d', uploader.total.queued));
		}

		if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
			//self.start_button.button('disable');
		} else {
			//self.start_button.button('enable');
		}

		self._updateTotalProgress();

		if (!uploader.files.length && uploader.features.dragdrop && uploader.settings.dragdrop) {
			// Re-add drag message if there are no files
			this.filelist.set('html','<tr><td class="plupload_droptext">' + "Drag files here." + '</td></tr>');
		}
	},
	
	_enableDragAndDrop: function() {
		this.filelist.set('html','<tr><td class="plupload_droptext">' + "Drag files here." + '</td></tr>');
		this.filelist.getParent().set('id', this.id + '_dropbox');
		this.uploader.settings.drop_element = this.options.drop_element = this.id + '_dropbox';
	}

});

Kunena.Uploader.File = new Class({

	initialize: function() {
	}

});
