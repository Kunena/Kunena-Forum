/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

var Kunena = new Class({

	initialize: function() {
	}

});

Kunena.Uploader = new Class({

	options: {
		runtimes : 'gears,html5,flash,silverlight,browserplus',
		url: '',

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

		//this.options = Object.merge(this.options, options); // MooTools 1.3
		this.options = $merge(this.options, options);

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
		this.container = this.element.getElement('.upload-container').set('id', id + '_container');

		// list of files, may become sortable
		this.filelist = this.container.getElement('.upload-filelist-files').set({
			id: id + '_filelist',
			unselectable: 'on'
		});

		// buttons
		this.browse_button = this.container.getElement('.upload-button-add').set('id', id + '_browse');
		this.start_button = this.container.getElement('.upload-button-start').set('id', id + '_start').set('style', 'display: none');
		this.stop_button = this.container.getElement('.upload-button-stop').set('id', id + '_stop').set('style', 'display: none');

		// counter
		this.counter = this.element.getElement('.upload-count').set({
			id: id + '_count',
			name: id + '_count'
		});

		// initialize uploader instance
//		uploader = this.uploader = new plupload.Uploader(Object.merge({ // MooTools 1.3
		uploader = this.uploader = new plupload.Uploader($merge({
			container: id,
			browse_button: id + '_browse'
		}, this.options));

		uploader.bind('Init', function(up, res) {
			if (self.uploader.features.dragdrop && self.options.dragdrop) {
				self._enableDragAndDrop();
			}

			self.container.set('title', 'Using runtime: ' + (self.runtime = res.runtime));

			self.start_button.addEvent('click', function(e) {
				self.start();
				e.preventDefault();
			});

			self.stop_button.addEvent('click', function(e) {
				self.stop();
				e.preventDefault();
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
			if (self.options.autostart) {
				self.start();
			}
		});

		uploader.bind('FilesRemoved', function(up, files) {
		});

		uploader.bind('QueueChanged', function() {
			self._updateFileList();
			self._handleState();
		});

		uploader.bind('StateChanged', function() {
			self._handleState();
		});

		uploader.bind('UploadFile', function(up, file) {
			self._handleFileStatus(file);
		});

		uploader.bind('ChunkUploaded', function(up, file, result) {
			self._handleErrors(file, result);
		});

		uploader.bind('FileUploaded', function(up, file, result) {
			self._handleErrors(file, result);
			self._handleFileStatus(file);
		});

		uploader.bind('UploadProgress', function(up, file) {
			// Set file specific progress
			/*if(file.percent != 100) {
				self.element.getElement('#' + file.id + ' .upload-file-status').set('html', '<div class="progress progress-striped active"><div class="bar" style="width: ' + file.percent + '%"><span class="bold">' + file.percent + '%</span></div></div>');
			} else {
				self.element.getElement('#' + file.id + ' .upload-file-status').set('html', '<div class="progress progress-striped active"><div class="bar" style="width: ' + file.percent + '%"><span class="bold">' + "Completed " + file.percent + '%</span></div></div>');
			}*/
			
			self.element.getElement('#' + file.id + ' .upload-file-status').getElement('.bar').set('style','width: ' + file.percent + '%');
			if(file.percent < 100) {
				self.element.getElement('#' + file.id + ' .upload-file-status').getElement('.bar-label').set('text', file.percent + '%');
			} else {
				self.element.getElement('#' + file.id + ' .upload-file-status').getElement('.bar-label').set('text', 'Completed ' + file.percent + '%');
			}

			self._handleFileStatus(file);
			self._updateTotalProgress();
		});

		uploader.bind('UploadComplete', function(up, files) {
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
			}
		});
	},

	start: function() {
		this.uploader.start();
	},

	stop: function() {
		this.uploader.stop();
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
			'html': '<div class="upload-icon upload-close" title="'+'Close'+'"></div>' +
					'<p><span class="upload-icon"></span>'+message+'</p>'
		});

		popup.addClass('upload-' + (type === 'error' ? 'error' : 'highlight'));
		popup.getElements('p .upload-icon').addClass('upload-' + (type === 'error' ? 'alert' : 'info'));
		popup.getElements('.upload-close').addEvent('click', (function(e) {
			popup.destroy();
			e.preventDefault();
		}));

		this.container.grab(popup, 'top');
	},

	_render: function(name) {
		document.id(name).set('html',
			'<div class="innerspacer kbox-full">' +
			'<div class="upload-container">' +
			'<div class="upload-filelist-container">' +
			'<table class="upload-filelist kbox-full kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">' +
			'<thead class="upload-filelist header">' +
			'<tr class="upload-filelist-header kbox-hover_header-row">' +
			'<th class="upload-file-name"><span class="bold">'+'File Name'+'</span></th>' +
			'<th class="upload-file-status"><span class="bold">'+'Status'+'</span></th>' +
			'<th class="upload-file-size"><span class="bold">'+'Size'+'</span></th>' +
			'<th class="upload-file-action"></th>' +
			'</tr>' +
			'</thead>' +
			'<tbody class="upload-filelist-files"></tbody>' +
			'<tfoot class="upload-filelist-bottom">' +
			'<tr class="upload-filelist-footer kbox-hover_header-row">' +
			'<td class="upload-file-name upload-upload-status"><span class="upload-status-label bold"></td>' +
			'<td class="upload-file-status upload-total-status"><div class="progress progress-striped active"><div class="bar"><span class="bar-label bold">'+'0%'+'</span></div></div></td>' +
			'<td class="upload-file-size upload-total-file-size"><span class="total-file-size-label bold">'+'0kb'+'</span></td>' +
			'<td class="upload-file-action"></td>' +
			'</tr>' +
			'</tfoot>' +
			'</table>' +
			'</div>' +
			'<div class="upload-buttons innerspacer">' +
			'<ul class="buttonbar buttons-category">' +
			'<li class="item-button">' +
			'<a class="kbutton button-type-standard upload-button-add">'+'Add Files'+'</a>' +
			'</li>' +
			'<li class="item-button">' +
			'<a class="kbutton button-type-standard upload-button-start"><span>'+'Start Upload'+'</span></a>' +
			'</li>' +
			'<li class="item-button">' +
			'<a class="kbutton button-type-standard upload-button-stop"><span>'+'Stop Upload'+'</span></a>' +
			'</li>' +
			'</ul>' +
			'</div>' +
			'<input class="upload-count" value="0" type="hidden" />' +
			'</div>' +
			'</div>');
	},

	_handleErrors: function(file, result) {
		var self = this, uploader = this.uploader;
		var response = JSON.decode(result.response, true);
		if (!response || !response.success) {
			file.status = plupload.FAILED;

			self.notify('error', (response && response.error ? response.error : 'Unknown response error!') + '<br />File: '+ file.name);
		}
	},

	_handleState: function() {
		var self = this, uploader = this.uploader;

		if (uploader.state === plupload.STARTED) {
			
			self.start_button.set('style', 'display: none');
			if (this.options.buttons.stop === true) self.stop_button.set('style', 'display: block');
			
		} else {
			
			self.stop_button.set('style', 'display: none');
			if (this.options.buttons.start === true && uploader.total.queued > 0) self.start_button.set('style', 'display: block');
			self._updateFileList();
		}
	},
	
	_handleFileStatus: function(file) {
		var actionClass, iconClass;

		switch (file.status) {
			case plupload.DONE: 
				actionClass = 'upload-file upload-done';
				iconClass = 'upload-icon upload-check';
				break;
			
			case plupload.FAILED:
				actionClass = 'upload-file upload-failed';
				iconClass = 'upload-icon upload-alert';
				break;

			case plupload.QUEUED:
				actionClass = 'upload-file upload-delete';
				iconClass = 'upload-icon upload-minus';
				break;

			case plupload.UPLOADING:
				actionClass = 'upload-file upload-uploading';
				iconClass = 'upload-icon upload-arrow';
				break;
		}

		var entry = document.id(file.id);
		if (entry) entry.set('class', actionClass).getElements('.upload-icon').set('class', iconClass);
	},

	_updateTotalProgress: function() {
		var uploader = this.uploader;

		/*if(uploader.total.percent < 100) {
			this.element.getElement('.upload-total-status').set('html', '<div class="progress progress-striped active"><div class="bar" style="width: ' + uploader.total.percent + '%"><span class="bold">' + uploader.total.percent + '%</span></div></div>');
		} else {
			this.element.getElement('.upload-total-status').set('html', '<div class="progress progress-striped active"><div class="bar" style="width: ' + uploader.total.percent + '%"><span class="bold">' + "Completed " + uploader.total.percent + '%</span></div></div>');
		}*/
		
		this.element.getElement('.upload-total-status').getElement('.bar').set('style', 'width: ' + uploader.total.percent + '%');
		if(uploader.total.percent < 100) {
				this.element.getElement('.upload-total-status').getElement('.bar-label').set('text', uploader.total.percent + '%');
		} else {
				this.element.getElement('.upload-total-status').getElement('.bar-label').set('text', 'Completed ' + uploader.total.percent + '%');
		}

		this.element.getElement('.upload-upload-status').getElement('.upload-status-label').set('text', 'Uploaded ' + uploader.total.uploaded + '/' + uploader.files.length + ' files');
		if (uploader.total.queued === 0) {
			this.browse_button.set('html', '<span>'+'Add Files'+'</span>');
		} else {
			this.browse_button.set('html', '<span>'+'%d files queued'.replace('%d', uploader.total.queued)+'</span>');
		}
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
				'class': 'upload_file',
				'id': file.id, 
				'html': '<td class="upload-file-name"><span>' + file.name + '</span></td>' +
				'<td class="upload-file-status"><div class="progress progress-striped active"><div class="bar"><span class="bar-label bold">' + file.percent + '%</span></div></div></td>' +
				'<td class="upload-file-size">' + plupload.formatSize(file.size) + '</td>' +
				'<td class="upload-file-action"><div class="upload-icon" title="'+'Remove File'+'"></div>' + fields + '</td>'
				}
			);
			filelist.grab(html);

			self._handleFileStatus(file);

			$$('#' + file.id + ' .upload-icon').addEvent('click', function(e) {
				document.id(file.id).destroy();
				uploader.removeFile(file);
				self._handleState();
				if (file.status == plupload.UPLOADING) {
					self.stop();
					self.start();
				}
				e.preventDefault();
			});
		});

		self.element.getElement('.upload-total-file-size').getElement('.total-file-size-label').set('text',plupload.formatSize(uploader.total.size));

		if (uploader.features.dragdrop && uploader.settings.dragdrop) {
			// Re-add drag message if needed
			var drag = new Element('tr', {
				'class' : 'kbox-hover_list-row',
				'html': '<td colspan="4" class="upload-droptext">' + "Drag files here." + '</td>'
				}
			);
			this.filelist.grab(drag);
		}
		self._updateTotalProgress();
	},
	
	_enableDragAndDrop: function() {
		this._updateFileList();
		this.filelist.getParent().set('id', this.id + '_dropbox');
		this.uploader.settings.drop_element = this.options.drop_element = this.id + '_dropbox';
	}

});