/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

var KunenaTabs = new Class({

	Implements: [Options, Events],

	options : {
		display: 0,
		useStorage: true,
		onActive: function(title, description) {
			description.addClass('active');
			title.addClass('active');
		},
		onBackground: function(title, description){
			console.debug(title);
			console.debug(description);
			description.removeClass('active');
			title.removeClass('active');
		},
		titleSelector: 'a[data-toggle="tab"]',
		descriptionSelector: '.tab-pane'
	},

	/*initialize: function(list, content, options) {*/
	initialize: function(options) {
		this.setOptions(options);
		this.list = document.getElements(this.options.titleSelector);
		console.debug(this.list);
		this.content = document.getElements(this.options.descriptionSelector);
		console.debug(this.content);
		//this.titles = this.options.title.getChildren(this.options.titleSelector);
		//this.descriptions =  this.description.getChildren(this.options.descriptionSelector);
		this.storageName = 'ktabs_'+this.list.id;

		if (this.options.useStorage) {
			if (Browser.Features.localstorage) {
				this.options.display = localStorage[this.storageName];
			} else {
				this.options.display = Cookie.read(this.storageName);
			}
		}

		if (this.options.display === null || this.options.display === undefined) {
			this.options.display = 0;
		}
		this.options.display = this.options.display.toInt().limit(0, this.list.length-1);

		for (var i = 0, l = this.list.length; i < l; i++)
		{
			var itemList = this.list[i];
			var itemContent = this.content[i];
			itemList.setStyle('cursor', 'pointer');
			itemList.addEventListener('click', function(event) {
			    event.preventDefault();
			});
			itemList.addEvent('click', this.display.bind(this, i));
		}

		this.display(this.options.display);

		if (this.options.initialize) this.options.initialize.call(this);
	},

	hideAllBut: function(but) {
		for (var i = 0, l = this.list.length; i < l; i++)
		{
			if (i != but) this.fireEvent('onBackground', [this.list[i].getParent(), this.content[i]]);
		}
	},

	display: function(i) {
		this.hideAllBut(i);
		this.fireEvent('onActive', [this.list[i].getParent(), this.content[i]]);
		if (this.options.useStorage) {
			if (Browser.Features.localstorage) {
				localStorage[this.storageName] = i;
			} else {
				Cookie.write(this.storageName, i);
			}
		}
	}
});