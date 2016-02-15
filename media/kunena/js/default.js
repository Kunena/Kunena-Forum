/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

/* Javascript file for default Kunena BlueEagle template */

/* Tabs class */
var KunenaTabs = new Class({
	Implements: [Options, Events],

	options: {
		display            : 0,
		onActive           : function(title, description) {
			description.setStyle('display', 'block');
			title.addClass('open').removeClass('closed');
		},
		onBackground       : function(title, description) {
			description.setStyle('display', 'none');
			title.addClass('closed').removeClass('open');
		},
		titleSelector      : 'dt',
		descriptionSelector: 'dd'
	},

	initialize: function(dlist, options) {
		this.setOptions(options);
		this.dlist = document.id(dlist);
		this.titles = this.dlist.getChildren(this.options.titleSelector);
		this.descriptions = this.dlist.getChildren(this.options.descriptionSelector);
		this.content = new Element('div').inject(this.dlist, 'after').addClass('current');

		for (var i = 0, l = this.titles.length; i < l; i++) {
			var title = this.titles[i];
			var description = this.descriptions[i];
			title.setStyle('cursor', 'pointer');
			title.addEvent('click', this.display.bind(this, i));
			description.inject(this.content);
		}

		if (this.options.display != null) this.display(this.options.display);

		if (this.options.initialize) this.options.initialize.call(this);
	},

	hideAllBut: function(but) {
		for (var i = 0, l = this.titles.length; i < l; i++) {
			if (i != but) this.fireEvent('onBackground', [this.titles[i], this.descriptions[i]]);
		}
	},

	display: function(i) {
		this.hideAllBut(i);
		this.fireEvent('onActive', [this.titles[i], this.descriptions[i]]);
	}
});

//----------------- New Mootools extensions ---------------------------

/**
 * Autocompleter
 *
 * http://digitarald.de/project/autocompleter/
 *
 * @version        1.1.2
 *
 * @license        MIT-style license
 * @author        Harald Kirschner <mail [at] digitarald.de>
 * @copyright    Author
 */

var Autocompleter = new Class({

	Implements: [Options, Events],

	options: {
		/*
		 onOver: $empty,
		 onSelect: $empty,
		 onSelection: $empty,
		 onShow: $empty,
		 onHide: $empty,
		 onBlur: $empty,
		 onFocus: $empty,*/
		minLength      : 1,
		markQuery      : true,
		width          : 'inherit',
		maxChoices     : 10,
		injectChoice   : null,
		customChoices  : null,
		emptyChoices   : null,
		visibleChoices : true,
		className      : 'autocompleter-choices',
		zIndex         : 42,
		delay          : 400,
		observerOptions: {},
		fxOptions      : {},

		autoSubmit    : false,
		overflow      : false,
		overflowMargin: 25,
		selectFirst   : false,
		filter        : null,
		filterCase    : false,
		filterSubset  : false,
		forceSelect   : false,
		selectMode    : true,
		choicesMatch  : null,

		multiple      : false,
		separator     : ', ',
		separatorSplit: /\s*[,;]\s*/,
		autoTrim      : false,
		allowDupes    : false,

		cache   : true,
		relative: false
	},

	initialize: function(element, options) {
		this.element = document.id(element);
		this.setOptions(options);
		this.build();
		this.observer = new Observer(this.element, this.prefetch.bind(this), Object.merge({
			'delay': this.options.delay
		}, this.options.observerOptions));
		this.queryValue = null;
		if (this.options.filter) this.filter = this.options.filter.bind(this);
		var mode = this.options.selectMode;
		this.typeAhead = (mode == 'type-ahead');
		this.selectMode = (mode === true) ? 'selection' : mode;
		this.cached = [];
	},

	/**
	 * build - Initialize DOM
	 *
	 * Builds the html structure for choices and appends the events to the element.
	 * Override this function to modify the html generation.
	 */
	build: function() {
		if (document.id(this.options.customChoices)) {
			this.choices = this.options.customChoices;
		} else {
			this.choices = new Element('ul', {
				'class' : this.options.className,
				'styles': {
					'zIndex': this.options.zIndex
				}
			}).inject(document.body);
			this.relative = false;
			if (this.options.relative) {
				this.choices.inject(this.element, 'after');
				this.relative = this.element.getOffsetParent();
			}
			this.fix = new OverlayFix(this.choices);
		}
		if (!this.options.separator.test(this.options.separatorSplit)) {
			this.options.separatorSplit = this.options.separator;
		}
		this.fx = (!this.options.fxOptions) ? null : new Fx.Tween(this.choices, Object.merge({
			'property': 'opacity',
			'link'    : 'cancel',
			'duration': 200
		}, this.options.fxOptions)).addEvent('onStart', Chain.prototype.clearChain).set(0);
		this.element.setProperty('autocomplete', 'off')
			.addEvent((Browser.ie || Browser.safari || Browser.chrome) ? 'keydown' : 'keypress', this.onCommand.bind(this))
			.addEvent('click', this.onCommand.bind(this, [false]))
			.addEvent('focus', this.toggleFocus.pass({bind: this, arguments: true, delay: 100}))
			.addEvent('blur', this.toggleFocus.pass({bind: this, arguments: false, delay: 100}));
	},

	destroy: function() {
		if (this.fix) this.fix.destroy();
		this.choices = this.selected = this.choices.destroy();
	},

	toggleFocus: function(state) {
		this.focussed = state;
		if (!state) this.hideChoices(true);
		this.fireEvent((state) ? 'onFocus' : 'onBlur', [this.element]);
	},

	onCommand: function(e) {
		if (!e && this.focussed) return this.prefetch();
		if (e && e.key && !e.shift) {
			switch (e.key) {
				case 'enter':
					if (this.element.value != this.opted) return true;
					if (this.selected && this.visible) {
						this.choiceSelect(this.selected);
						return !!(this.options.autoSubmit);
					}
					break;
				case 'up':
				case 'down':
					if (!this.prefetch() && this.queryValue !== null) {
						var up = (e.key == 'up');
						this.choiceOver((this.selected || this.choices)[
							(this.selected) ? ((up) ? 'getPrevious' : 'getNext') : ((up) ? 'getLast' : 'getFirst')
							](this.options.choicesMatch), true);
					}
					return false;
				case 'esc':
				case 'tab':
					this.hideChoices(true);
					break;
			}
		}
		return true;
	},

	setSelection: function(finish) {
		var input = this.selected.inputValue, value = input;
		var start = this.queryValue.length, end = input.length;
		if (input.substr(0, start).toLowerCase() != this.queryValue.toLowerCase()) start = 0;
		if (this.options.multiple) {
			var split = this.options.separatorSplit;
			value = this.element.value;
			start += this.queryIndex;
			end += this.queryIndex;
			var old = value.substr(this.queryIndex).split(split, 1)[0];
			value = value.substr(0, this.queryIndex) + input + value.substr(this.queryIndex + old.length);
			if (finish) {
				var tokens = value.split(this.options.separatorSplit).filter(function(entry) {
					return this.test(entry);
				}, /[^\s,]+/);
				if (!this.options.allowDupes) tokens = [].combine(tokens);
				var sep = this.options.separator;
				value = tokens.join(sep) + sep;
				end = value.length;
			}
		}
		this.observer.setValue(value);
		this.opted = value;
		if (finish || this.selectMode == 'pick') start = end;
		this.element.selectRange(start, end);
		this.fireEvent('onSelection', [this.element, this.selected, value, input]);
	},

	showChoices: function() {
		var match = this.options.choicesMatch, first = this.choices.getFirst(match);
		this.selected = this.selectedValue = null;
		if (this.fix) {
			var pos = this.element.getCoordinates(this.relative), width = this.options.width || 'auto';
			this.choices.setStyles({
				'left' : pos.left,
				'top'  : pos.bottom,
				'width': (width === true || width == 'inherit') ? pos.width : width
			});
		}
		if (!first) return;
		if (!this.visible) {
			this.visible = true;
			this.choices.setStyle('display', '');
			if (this.fx) this.fx.start(1);
			this.fireEvent('onShow', [this.element, this.choices]);
		}
		if (this.options.selectFirst || this.typeAhead || first.inputValue == this.queryValue) this.choiceOver(first, this.typeAhead);
		var items = this.choices.getChildren(match), max = this.options.maxChoices;
		var styles = {'overflowY': 'hidden', 'height': ''};
		this.overflown = false;
		if (items.length > max) {
			var item = items[max - 1];
			styles.overflowY = 'scroll';
			styles.height = item.getCoordinates(this.choices).bottom;
			this.overflown = true;
		}
		;
		this.choices.setStyles(styles);
		this.fix.show();
		if (this.options.visibleChoices) {
			var scroll = document.getScroll(),
				size = document.getSize(),
				coords = this.choices.getCoordinates();
			if (coords.right > scroll.x + size.x) scroll.x = coords.right - size.x;
			if (coords.bottom > scroll.y + size.y) scroll.y = coords.bottom - size.y;
			window.scrollTo(Math.min(scroll.x, coords.left), Math.min(scroll.y, coords.top));
		}
	},

	hideChoices: function(clear) {
		if (clear) {
			var value = this.element.value;
			if (this.options.forceSelect) value = this.opted;
			if (this.options.autoTrim) {
				value = value.split(this.options.separatorSplit).filter($arguments(0)).join(this.options.separator);
			}
			this.observer.setValue(value);
		}
		if (!this.visible) return;
		this.visible = false;
		if (this.selected) this.selected.removeClass('autocompleter-selected');
		this.observer.clear();
		var hide = function() {
			this.choices.setStyle('display', 'none');
			this.fix.hide();
		}.bind(this);
		if (this.fx) this.fx.start(0).chain(hide);
		else hide();
		this.fireEvent('onHide', [this.element, this.choices]);
	},

	prefetch: function() {
		var value = this.element.value, query = value;
		if (this.options.multiple) {
			var split = this.options.separatorSplit;
			var values = value.split(split);
			var index = this.element.getSelectedRange().start;
			var toIndex = value.substr(0, index).split(split);
			var last = toIndex.length - 1;
			index -= toIndex[last].length;
			query = values[last];
		}
		if (query.length < this.options.minLength) {
			this.hideChoices();
		} else {
			if (query === this.queryValue || (this.visible && query == this.selectedValue)) {
				if (this.visible) return false;
				this.showChoices();
			} else {
				this.queryValue = query;
				this.queryIndex = index;
				if (!this.fetchCached()) this.query();
			}
		}
		return true;
	},

	fetchCached: function() {
		if (!this.options.cache
			|| !this.cached
			|| !this.cached.length
			|| this.cached.length >= this.options.maxChoices
			|| this.queryValue) {
			return false;
		}
		this.update(this.filter(this.cached));
		return true;
	},

	update: function(tokens) {
		this.choices.empty();
		this.cached = tokens;
		var type = tokens && typeOf(tokens);
		if (!type || (type == 'array' && !tokens.length) || (type == 'hash' && !tokens.getLength())) {
			(this.options.emptyChoices || this.hideChoices).call(this);
		} else {
			if (this.options.maxChoices < tokens.length && !this.options.overflow) tokens.length = this.options.maxChoices;
			tokens.each(this.options.injectChoice || function(token) {
					var choice = new Element('li', {'html': this.markQueryValue(token)});
					choice.inputValue = token;
					this.addChoiceEvents(choice).inject(this.choices);
				}, this);
			this.showChoices();
		}
	},

	choiceOver: function(choice, selection) {
		if (!choice || choice == this.selected) return;
		if (this.selected) this.selected.removeClass('autocompleter-selected');
		this.selected = choice.addClass('autocompleter-selected');
		this.fireEvent('onSelect', [this.element, this.selected, selection]);
		if (!this.selectMode) this.opted = this.element.value;
		if (!selection) return;
		this.selectedValue = this.selected.inputValue;
		if (this.overflown) {
			var coords = this.selected.getCoordinates(this.choices), margin = this.options.overflowMargin,
				top = this.choices.scrollTop, height = this.choices.offsetHeight, bottom = top + height;
			if (coords.top - margin < top && top) this.choices.scrollTop = Math.max(coords.top - margin, 0);
			else if (coords.bottom + margin > bottom) this.choices.scrollTop = Math.min(coords.bottom - height + margin, bottom);
		}
		if (this.selectMode) this.setSelection();
	},

	choiceSelect: function(choice) {
		if (choice) this.choiceOver(choice);
		this.setSelection(true);
		this.queryValue = false;
		this.hideChoices();
	},

	filter: function(tokens) {
		return (tokens || this.tokens).filter(function(token) {
			return this.test(token);
		}, new RegExp(((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp(), (this.options.filterCase) ? '' : 'i'));
	},

	/**
	 * markQueryValue
	 *
	 * Marks the queried word in the given string with <span class="autocompleter-queried">*</span>
	 * Call this i.e. from your custom parseChoices, same for addChoiceEvents
	 *
	 * @param        {String} Text
	 * @return        {String} Text
	 */
	markQueryValue: function(str) {
		return (!this.options.markQuery || !this.queryValue) ? str
			: str.replace(new RegExp('(' + ((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp() + ')', (this.options.filterCase) ? '' : 'i'), '<span class="autocompleter-queried">$1</span>');
	},

	/**
	 * addChoiceEvents
	 *
	 * Appends the needed event handlers for a choice-entry to the given element.
	 *
	 * @param        {Element} Choice entry
	 * @return        {Element} Choice entry
	 */
	addChoiceEvents: function(el) {
		return el.addEvents({
			'mouseover': this.choiceOver.bind(this, el),
			'click'    : this.choiceSelect.bind(this, el)
		});
	}
});

var OverlayFix = new Class({

	initialize: function(el) {
		if (Browser.ie) {
			this.element = document.id(el);
			this.relative = this.element.getOffsetParent();
			this.fix = new Element('iframe', {
				'frameborder': '0',
				'scrolling'  : 'no',
				'src'        : 'javascript:false;',
				'styles'     : {
					'position': 'absolute',
					'border'  : 'none',
					'display' : 'none',
					'filter'  : 'progid:DXImageTransform.Microsoft.Alpha(opacity=0)'
				}
			}).inject(this.element, 'after');
		}
	},

	show: function() {
		if (this.fix) {
			var coords = this.element.getCoordinates(this.relative);
			delete coords.right;
			delete coords.bottom;
			this.fix.setStyles(Object.append(coords, {
				'display': '',
				'zIndex' : (this.element.getStyle('zIndex') || 1) - 1
			}));
		}
		return this;
	},

	hide: function() {
		if (this.fix) this.fix.setStyle('display', 'none');
		return this;
	},

	destroy: function() {
		if (this.fix) this.fix = this.fix.destroy();
	}

});

Element.implement({

	getSelectedRange: function() {
		if (!Browser.ie) return {start: this.selectionStart, end: this.selectionEnd};
		var pos = {start: 0, end: 0};
		var range = this.getDocument().selection.createRange();
		if (!range || range.parentElement() != this) return pos;
		var dup = range.duplicate();
		if (this.type == 'text') {
			pos.start = 0 - dup.moveStart('character', -100000);
			pos.end = pos.start + range.text.length;
		} else {
			var value = this.value;
			var offset = value.length - value.match(/[\n\r]*$/)[0].length;
			dup.moveToElementText(this);
			dup.setEndPoint('StartToEnd', range);
			pos.end = offset - dup.text.length;
			dup.setEndPoint('StartToStart', range);
			pos.start = offset - dup.text.length;
		}
		return pos;
	},

	selectRange: function(start, end) {
		if (Browser.ie) {
			var diff = this.value.substr(start, end - start).replace(/\r/g, '').length;
			start = this.value.substr(0, start).replace(/\r/g, '').length;
			var range = this.createTextRange();
			range.collapse(true);
			range.moveEnd('character', start + diff);
			range.moveStart('character', start);
			range.select();
		} else {
			this.focus();
			this.setSelectionRange(start, end);
		}
		return this;
	}

});

/* compatibility */

Autocompleter.Base = Autocompleter;

/**
 * Autocompleter.Local
 *
 * http://digitarald.de/project/autocompleter/
 *
 * @version        1.1.2
 *
 * @license        MIT-style license
 * @author        Harald Kirschner <mail [at] digitarald.de>
 * @copyright    Author
 */

Autocompleter.Local = new Class({

	Extends: Autocompleter,

	options: {
		minLength: 0,
		delay    : 200
	},

	initialize: function(element, tokens, options) {
		this.parent(element, options);
		this.tokens = tokens;
	},

	query: function() {
		this.update(this.filter());
	}

});

/**
 * Autocompleter.Request
 *
 * http://digitarald.de/project/autocompleter/
 *
 * @version        1.1.2
 *
 * @license        MIT-style license
 * @author        Harald Kirschner <mail [at] digitarald.de>
 * @copyright    Author
 */

Autocompleter.Request = new Class({

	Extends: Autocompleter,

	options: {
		/*
		 indicator: null,
		 indicatorClass: null,
		 onRequest: $empty,
		 onComplete: $empty,*/
		postData   : {},
		ajaxOptions: {},
		postVar    : 'value'

	},

	query: function() {
		var data = Object.clone(this.options.postData) || {};
		data[this.options.postVar] = this.queryValue;
		var indicator = document.id(this.options.indicator);
		if (indicator) indicator.setStyle('display', '');
		var cls = this.options.indicatorClass;
		if (cls) this.element.addClass(cls);
		this.fireEvent('onRequest', [this.element, this.request, data, this.queryValue]);
		this.request.send({'data': data});
	},

	/**
	 * queryResponse - abstract
	 *
	 * Inherated classes have to extend this function and use this.parent()
	 */
	queryResponse: function() {
		var indicator = document.id(this.options.indicator);
		if (indicator) indicator.setStyle('display', 'none');
		var cls = this.options.indicatorClass;
		if (cls) this.element.removeClass(cls);
		return this.fireEvent('onComplete', [this.element, this.request]);
	}

});

Autocompleter.Request.JSON = new Class({
	Extends: Autocompleter.Request,

	secure    : false,
	initialize: function(el, url, options) {
		this.parent(el, options);
		this.request = new Request.JSON(Object.merge({
			'secure': false,
			'url'   : url,
			'link'  : 'cancel'
		}, this.options.ajaxOptions)).addEvent('onComplete', this.queryResponse.bind(this));
	},

	queryResponse: function(response) {
		this.parent();
		this.update(response);
	}

});

/* compatibility */

Autocompleter.Ajax = {
	Base : Autocompleter.Request,
	Json : Autocompleter.Request.JSON,
	Xhtml: Autocompleter.Request.HTML
};

/**
 * Observer - Observe formelements for changes
 *
 * - Additional code from clientside.cnet.com
 *
 * @version        1.1
 *
 * @license        MIT-style license
 * @author        Harald Kirschner <mail [at] digitarald.de>
 * @copyright    Author
 */
var Observer = new Class({

	Implements: [Options, Events],

	options: {
		periodical: false,
		delay     : 1000
	},

	initialize: function(el, onFired, options) {
		this.element = document.id(el) || $$(el);
		this.addEvent('onFired', onFired);
		this.setOptions(options);
		this.bound = this.changed.bind(this);
		this.resume();
	},

	changed: function() {
		var value = this.element.get('value');
		if ($equals(this.value, value)) return;
		this.clear();
		this.value = value;
		this.timeout = this.onFired.delay(this.options.delay, this);
	},

	setValue: function(value) {
		this.value = value;
		this.element.set('value', value);
		return this.clear();
	},

	onFired: function() {
		this.fireEvent('onFired', [this.value, this.element]);
	},

	clear: function() {
		clearInterval(this.timeout || null);
		return this;
	},

	pause: function() {
		if (this.timer) clearInterval(this.timer);
		else this.element.removeEvent('keyup', this.bound);
		return this.clear();
	},

	resume: function() {
		this.value = this.element.get('value');
		if (this.options.periodical) this.timer = this.changed.periodical(this.options.periodical, this);
		else this.element.addEvent('keyup', this.bound);
		return this;
	}

});

var $equals = function(obj1, obj2) {
	return (obj1 == obj2 || JSON.encode(obj1) == JSON.encode(obj2));
};

function kRequestShowTopics(catid, select, list) {
	select.set('value', 0).fireEvent('change', select);
	var first = select.getFirst().clone();
	select.empty().grab(first);
	list.each(function(item) {
		var option = new Element('option', {'value': item.id, 'html': item.subject});
		select.grab(option);
	});
}

function kRequestGetTopics(el) {
	var catid = el.get("value");
	var select = document.id('kmod_topics');
	request = new Request.JSON({
		secure   : false, url: kunena_url_ajax,
		onSuccess: function(response) {
			kRequestShowTopics(catid, select, response.topiclist);
		}
	}).post({'catid': catid});
}

function kunenaSelectUsernameView(kobj, kuser) {
	var kform = kobj.getParent('form');
	if (kobj.get('checked')) {
		kform.getElement('input[name=authorname]').removeProperty('disabled').setStyle('display', 'inline').set('value', kunena_anonymous_name);
	} else {
		kform.getElement('input[name=authorname]').set('disabled', 'disabled').setStyle('display', 'none').set('value', kuser);
	}
}

function kunenatableOrdering(order, dir, task, form) {
	var form = document.getElementById(form);
	form.filter_order.value = order;
	form.filter_order_Dir.value = dir;
	form.submit(task);
}

//----------------- New Mootools based behaviors ----------------------

window.addEvent('domready', function() {
	/* Quick reply */
	$$('.kqreply').each(function(el) {
		el.addEvent('click', function(e) {
			//prevent to load the page when click is detected on a button
			e.stop();
			var kreply = this.get('id');
			var kstate = document.id(kreply + '_form').getStyle('display');
			$$('.kreply-form').setStyle('display', 'none');
			document.id(kreply + '_form').setStyle('display', 'block');
			if (document.id(kreply + '_form').getElement('input[name=anonymous]')) {
				var kuser = document.id(kreply + '_form').getElement('input[name=authorname]').get('value');
				kunenaSelectUsernameView(document.id(kreply + '_form').getElement('input[name=anonymous]'), kuser);
				document.id(kreply + '_form').getElement('input[name=anonymous]').addEvent('click', function(e) {
					kunenaSelectUsernameView(this, kuser);
				});
			}
		});
	});

	$$('.kreply-cancel').addEvent('click', function(e) {
		$$('.kreply-form').setStyle('display', 'none');
	});

	/* Logic for bulkactions */
	$$('input.kcheckall').addEvent('click', function(e) {
		this.getParent('form').getElements('input.kcheck').each(function(el) {
			if (el.get('checked') == false) {
				el.set('checked', true);
				el.set('value', '1');
			} else {
				el.set('value', '0');
				el.set('checked', false);
			}
		});
	});

	$$('select.kchecktask').addEvent('change', function(e) {
		ktarget = this.getSiblings('select[name=target]');
		if (this.get('value') == 'move') {
			ktarget.removeProperty('disabled');
		} else {
			ktarget.setProperty('disabled', 'disabled');
		}
	});

	if (document.id('kmod_categories') != undefined) {
		document.id('kmod_categories').addEvent('change', function(e) {
			kRequestGetTopics(this);
		});
	}
	if (document.id('kmod_topics') != undefined) {
		document.id('kmod_topics').addEvent('change', function(e) {
			id = this.get('value');
			if (id != 0) {
				targetid = this.get('value');
				document.id('kmod_subject').setStyle('display', 'none');
			} else {
				targetid = '';
				document.id('kmod_subject').setStyle('display', 'block');
			}
			if (id == -1) {
				targetid = '';
				document.id('kmod_targetid').setStyle('display', 'inline');
			} else {
				document.id('kmod_targetid').setStyle('display', 'none');
			}
			document.id('kmod_targetid').set('value', targetid);
		});
	}

	// Get the kunena settings cookie data.
	var KCookie = new Hash.Cookie('kunena_toggler', {path: '/', duration: 0});

	// Setup the behavior for all kunena toggler elements.
	$$('a.ktoggler').each(function(link) {
		// Auto-hide if the cookie is set.
		if (KCookie.get(link.getProperty('rel'))) {
			link.removeClass('close').addClass('open');
			link.set('title', kunena_toggler_open);
			document.id(link.getProperty('rel')).setStyle('display', 'none');
		}

		// Add the onclick event.
		link.addEvent('click', function() {
			if (this.hasClass('close')) {
				this.removeClass('close').addClass('open');
				link.set('title', kunena_toggler_open);
				document.id(this.getProperty('rel')).setStyle('display', 'none');
				KCookie.set(this.getProperty('rel'), 1);
			}
			else {
				this.removeClass('open').addClass('close');
				link.set('title', kunena_toggler_close);
				document.id(this.getProperty('rel')).setStyle('display', '');
				KCookie.erase(this.getProperty('rel'));
			}
		});
	});

	// Set autocompleter to off
	$$('.kautocomplete-off').each(function() {
		this.setProperty('autocompleter', 'off');
	});
	if (document.id('kpassword') != undefined && document.id('kpassword2') != undefined) {
		document.id('kpassword').setProperty('autocompleter', 'off');
		document.id('kpassword2').setProperty('autocompleter', 'off');
	}

	if (document.id('kpoll-moreusers') != undefined) {
		document.id('kpoll-moreusers').addEvent('click', function() {
			var displaytype = document.id('kpoll-moreusers-div').getStyle('display');
			if (displaytype == 'none') document.id('kpoll-moreusers-div').setStyle('display');
			else document.id('kpoll-moreusers-div').setStyle('display', 'none');
		});
	}

	if (document.id('kchecbox-all') != undefined) {
		document.id('kchecbox-all').addEvent('click', function() {
			if (document.id('kchecbox-all').getProperty('checked') == false) {
				$$('.kmoderate-topic-checkbox').each(function(box) {
					box.removeProperty('checked');
				});
			} else {
				$$('.kmoderate-topic-checkbox').each(function(box) {
					box.setProperty('checked', 'checked');
				});
			}
		});
	}

	if (document.id('kmoderate-select') != undefined) {
		document.id('kmoderate-select').addEvent('click', function() {
			if (document.id('kmoderate-select').getSelected().get('value') == 'move') {
				document.id('kcategorytarget').setStyle('display');
			}
		});
	}

	if (document.id('avatar_category_select') != undefined) {
		document.id('avatar_category_select').addEvent('change', function(e) {
			// we getting the name of gallery selected in drop-down by user
			var avatar_selected = document.id('avatar_category_select').getSelected();

			var td_avatar = document.id('kgallery_avatar_list');

			// we remove avatar which exist in td tag to allow us to put new one items
			document.id('kgallery_avatar_list').empty();
			// we getting from hidden input the url of kunena image gallery
			var url_gallery_main = document.id('Kunena_Image_Gallery_URL').get('value');
			var id_to_select = document.id('Kunena_' + avatar_selected.get('value'));
			var name_to_select = id_to_select.getProperty('name');
			// Convert JSON to object
			var image_object = JSON.decode(id_to_select.get('value'));

			// Re-create all HTML items with avatars images from gallery selected by user
			for (var i = 0, len = image_object.length; i < len; ++i) {
				var SpanElement = new Element('span');
				var LabelElement = new Element('label');
				LabelElement.setProperty('for', 'kavatar' + i);
				if (name_to_select != 'default') {
					var ImageElement = new Element('img', {
						src: url_gallery_main + '/' + name_to_select + '/' + image_object[i],
						alt: ''
					});
					var InputElement = new Element('input', {
						id   : 'kavatar' + i,
						type : 'radio',
						name : 'avatar',
						value: 'gallery/' + name_to_select + '/' + image_object[i]
					});
				} else {
					var ImageElement = new Element('img', {src: url_gallery_main + '/' + image_object[i], alt: ''});
					var InputElement = new Element('input', {
						id   : 'kavatar' + i,
						type : 'radio',
						name : 'avatar',
						value: 'gallery/' + image_object[i]
					});
				}
				SpanElement.inject(td_avatar);
				LabelElement.inject(SpanElement);
				ImageElement.inject(LabelElement);
				InputElement.inject(SpanElement);
			}
		});
	}

	$$('.kspoiler').each(function(el) {
		var contentElement = el.getElement('.kspoiler-content');
		var expandElement = el.getElement('.kspoiler-expand');
		var hideElement = el.getElement('.kspoiler-hide');
		el.getElement('.kspoiler-header').addEvent('click', function(e) {
			if (contentElement.style.display == "none") {
				contentElement.setStyle('display');
				expandElement.setStyle('display', 'none');
				hideElement.setStyle('display');
			} else {
				contentElement.setStyle('display', 'none');
				expandElement.setStyle('display');
				hideElement.setStyle('display', 'none');
			}
		});
	});

	/* For profile tabs */
	$$('dl.tabs').each(function(tabs) {
		new KunenaTabs(tabs);
	});
});
