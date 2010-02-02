/**
 * Uploader
 *
 * @version		1.0
 *
 * @license		MIT License
 *
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @copyright	Authors
 */

var Uploader = new Class({

	Implements: [Options, Events],

	options: {
		container: null,

		queued: true,
		verbose: false,

		url: null,
		method: null,
		data: null,
		mergeData: true,
		fieldName: null,

		allowDuplicates: false,
		fileListMax: 0,

		instantStart: false,
		appendCookieData: false,

		fileClass: null
	},

	initialize: function(options) {
		this.setOptions(options);

		this.target = $(this.options.target);

		this.box = new Element('div').setStyles({
			position: 'absolute',
			opacity: 0.01,
			zIndex: 9999,
			overflow: 'hidden'
		});

		this.reposition();
		window.addEvent('resize', this.reposition.bind(this));

		this.box.inject(this.options.container || document.body);

		this.addEvents({
			buttonEnter: this.targetRelay.bind(this, ['mouseenter']),
			buttonLeave: this.targetRelay.bind(this, ['mouseleave']),
			buttonDown: this.targetRelay.bind(this, ['mousedown']),
			buttonDisable: this.targetRelay.bind(this, ['disable'])
		});

		this.uploading = 0;
		this.fileList = [];

		this.createIFrame();

		return this;
	},

	targetRelay: function(name) {
		if (this.target) this.target.fireEvent(name);
	},

	createIFrame: function() {
		this.iframe = new Element('iframe', {
			src: "javascript:'<html></html>'",
			frameborder: 'no',
			border: 0,
			styles: {
				width: '100%',
				height: '100%'
			}
		}).inject(this.box);
		this.runner = this.createIBody.periodical(50, this);
	},

	createIBody: function() {
		var doc = this.iframe.contentWindow.document;
		
		if (!doc || !doc.body) return;
		$clear(this.runner);
				
		var align = (Browser.Engine.trident) ? 'left' : 'right';
		doc.body.innerHTML = '<form method="post" enctype="multipart/form-data" id="form">' +
			'<input type="file" id="file" style="position:absolute;' + align + ':0;top:0" />' +
			'<input type="submit" /><div id="data"></div></form>' + 
			'<style type="text/css">*{margin:0;padding:0;border:0;overflow:hidden;cursor:pointer;}</style>';
		
		this.doc = doc;
		
		this.processIBody.delay(50, this);
	},
	
	processIBody: function() {
		this.doc;
		
		if (!(this.file = this.doc.getElementById('file')) || !this.file.offsetHeight) {
			this.createIBody(); // WTF: FF forgot to update the HTML?!
			return;
		}
		
		$extend(this.file, {
			onmousedown: function() {
				if (Browser.Engine.presto) return true;
				(function() {
					this.file.click();
					this.fireEvent('buttonDown');
				}).delay(10, this);
				return false;
			}.bind(this),
			onfocus: function() {
				return false;
			},
			onchange: this.select.bind(this),
			onmouseover: this.fireEvent.bind(this, 'buttonEnter'),
			onmouseout: this.fireEvent.bind(this, 'buttonLeave')
		});
	},

	select: function() {
		this.file.onchange = this.file.onmousedown = this.file.onfocus = null;

		var name = this.file.value.replace(/^.*[\\\/]/, '');

		var cls = this.options.fileClass || Uploader.File;
		var ret = new cls(this, name, this.iframe.setStyle('display', 'none'));
		if (!ret.validate()) {
			ret.invalidate().render();
			this.fireEvent('onSelectFailed', [[ret]]);
			return;
		}

		this.fileList.push(ret);
		ret.render();
		this.fireEvent('onSelectSuccess', [[ret]]);

		if (this.options.instantStart) this.start();
		
		this.file = null;

		this.createIFrame();
	},

	reposition: function() {
		var pos = this.target.getCoordinates(this.box.getOffsetParent());
		this.box.setStyles(pos);
	},

	start: function() {
		this.fireEvent('beforeStart');
		var queued = this.options.queued;
		queued = (queued) ? ((queued > 1) ? queued : 1) : 0;

		for (var i = 0, file; file = this.fileList[i]; i++) {
			if (this.fileList[i].status != Uploader.STATUS_QUEUED) continue;
			this.fileList[i].start();
			if (queued && this.uploading >= queued) break;
		}
		return this;
	},

	stop: function() {
		this.fireEvent('beforeStop');
		for (var i = this.fileList.length; i--;) this.fileList[i].stop();
	},

	remove: function() {
		this.fireEvent('beforeRemove');
		for (var i = this.fileList.length; i--;) this.fileList[i].remove();
	},

	setEnabled: function(status) {
		this.file.disabled = !!(status);
		if (status) this.fireEvent('buttonDisable');
	}

});

$extend(Uploader, {

	STATUS_QUEUED: 0,
	STATUS_RUNNING: 1,
	STATUS_ERROR: 2,
	STATUS_COMPLETE: 3,
	STATUS_STOPPED: 4,

	id: 0,

	log: function() {
		if (window.console && console.info) console.info.apply(console, arguments);
	}

});

Uploader.File = new Class({

	Extends: Events,

	Implements: Options,
	
	options: {
		url: null,
		method: null,
		data: null,
		mergeData: true,
		fieldName: null
	},

	initialize: function(base, name, iframe) {
		this.base = base;

		this.id = Uploader.id++;
		this.name = name;
		this.extension = name.replace(/^.*\./, '').toLowerCase();
		this.status = Uploader.STATUS_QUEUED;
		this.dates = {};

		this.dates.add = new Date();

		this.iframe = iframe.addEvents({
			abort: this.stop.bind(this),
			load: this.onLoad.bind(this)
		});
	},

	fireEvent: function(name) {
		this.base.fireEvent('file' + name.capitalize(), [this]);
		Uploader.log('File::' + name, this);
		return this.parent(name, [this]);
	},

	validate: function() {
		var base = this.base.options;

		if (!base.allowDuplicates) {
			var name = this.name;
			var dup = this.base.fileList.some(function(file) {
				return (file.name == name);
			});
			if (dup) {
				this.validationError = 'duplicate';
				return false;
			}
		}

		if (base.fileListMax && this.base.fileList.length >= base.fileListMax) {
			this.validationError = 'fileListMax';
			return false;
		}

		return true;
	},

	invalidate: function() {
		this.invalid = true;
		return this.fireEvent('invalid');
	},

	render: function() {
		return this;
	},

	onLoad: function() {
		if (this.status != Uploader.STATUS_RUNNING) return;

		this.status = Uploader.STATUS_COMPLETE;

		var win = new Window(this.iframe.contentWindow);
		var doc = new Document(win.document);
		
		this.response = {
			window: win,
			document: doc,
			text: doc.innerHTML || ''
		};

		this.base.uploading--;
		this.dates.complete = new Date();

		this.fireEvent('complete');

		this.base.start();
	},

	start: function() {
		if (this.status != Uploader.STATUS_QUEUED) return this;

		var base = this.base.options, options = this.options;
		
		var merged = {};
		for (var key in base) {
			merged[key] = (this.options[key] != null) ? this.options[key] : base[key];
		}
		
		merged.url = merged.url || location.href;
		merged.method = (merged.method) ? (merged.method.toLowerCase()) : 'post';
		
		var doc = this.iframe.contentWindow.document;

		var more = doc.getElementById('data');
		more.innerHTML = '';
		if (merged.data) {
			if (merged.mergeData && base.data && options.data) {
				if ($type(base.data) == 'string') merged.data = base.data + '&' + options.data;
				else merged.data = $merge(base.data, options.data);
			}
			
			var query = ($type(merged.data) == 'string') ? merged.data : Hash.toQueryString(merged.data);
			
			if (query.length) {
				if (merged.method == 'get') {
					if (data.length) merged.url += ((merged.url.contains('?')) ? '&' : '?') + query;
				} else {
					query.split('&').map(function(value) {
						value = value.split('=');
						var input = doc.createElement('input');
						input.type = 'hidden';
						input.name = decodeURIComponent(value[0]);
						input.value = decodeURIComponent(value[1] || '');
						more.appendChild(input);
					}).join('');
				}
			}
			
		}

		var form = doc.forms[0];
		form.action = merged.url;

		var input = form.elements[0];
		input.name = merged.fieldName || 'Filedata';

		this.status = Uploader.STATUS_RUNNING;
		this.base.uploading++;

		form.submit();

		this.dates.start = new Date();

		this.fireEvent('start');

		return this;
	},

	requeue: function() {
		this.stop();
		this.status = Uploader.STATUS_QUEUED;
		this.fireEvent('requeue');
	},

	stop: function(soft) {
		if (this.status == Uploader.STATUS_RUNNING) {
			this.status = Uploader.STATUS_STOPPED;
			this.base.uploading--;
			this.base.start();
			if (!soft) {
				this.iframe.contentWindow.history.back();
				this.fireEvent('stop');
			}
		}
		return this;
	},

	remove: function() {
		this.stop(true);
		this.iframe = this.iframe.destroy();
		this.base.fileList.erase(this);
		this.fireEvent('remove');
		return this;
	}

});
