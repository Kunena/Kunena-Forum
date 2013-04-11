/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

/* Javascript file for default Kunena BlueEagle template */

/* Tabs class */
var KunenaTabs = new Class({
	Implements: [Options, Events],

	options : {
		display: 0,
		onActive: function(title, description) {
			description.setStyle('display', 'block');
			title.addClass('open').removeClass('closed');
		},
		onBackground: function(title, description){
			description.setStyle('display', 'none');
			title.addClass('closed').removeClass('open');
		},
		titleSelector: 'dt',
		descriptionSelector: 'dd'
	},

	initialize: function(dlist, options){
		this.setOptions(options);
		this.dlist = document.id(dlist);
		this.titles = this.dlist.getChildren(this.options.titleSelector);
		this.descriptions = this.dlist.getChildren(this.options.descriptionSelector);
		this.content = new Element('div').inject(this.dlist, 'after').addClass('current');

		for (var i = 0, l = this.titles.length; i < l; i++){
			var title = this.titles[i];
			var description = this.descriptions[i];
			title.setStyle('cursor', 'pointer');
			title.addEvent('click', this.display.bind(this, i));
			description.inject(this.content);
		}

		if (this.options.display!=null) this.display(this.options.display);

		if (this.options.initialize) this.options.initialize.call(this);
	},

	hideAllBut: function(but) {
		for (var i = 0, l = this.titles.length; i < l; i++){
			if (i != but) this.fireEvent('onBackground', [this.titles[i], this.descriptions[i]]);
		}
	},

	display: function(i) {
		this.hideAllBut(i);
		this.fireEvent('onActive', [this.titles[i], this.descriptions[i]]);
	}
});

function kRequestShowTopics(catid, select, list)
{
	select.set('value', 0).fireEvent('change', select);
	var first = select.getFirst().clone();
	select.empty().grab(first);
	list.each(function(item) {
		var option = new Element('option', {'value':item.id, 'html':item.subject});
		select.grab(option);
	});	
}

function kRequestGetTopics(el)
{
	var catid = el.get("value");
	var select = document.id('kmod_topics');
	request = new Request.JSON({secure: false, url: kunena_url_ajax,
	onSuccess: function(response){
		kRequestShowTopics(catid, select, response.topiclist);
		}}).post({'catid': catid});
}

function kunenaSelectUsernameView(kobj, kuser) {
	var kform = kobj.getParent('form');
	if (kobj.get('checked')) {
		kform.getElement('input[name=authorname]').removeProperty('disabled').setStyle('display','inline').set('value',kunena_anonymous_name);
	} else {
		kform.getElement('input[name=authorname]').set('disabled', 'disabled').setStyle('display','none').set('value',kuser);
	}
}

function kunenatableOrdering( order, dir, task, form ) {
	var form=document.getElementById(form);
	form.filter_order.value=order;
	form.filter_order_Dir.value=dir;
	form.submit( task );
}

//----------------- New Mootools based behaviors ----------------------

window.addEvent('domready', function(){	
	/* Quick reply */
	$$('.kqreply').each(function(el){
		el.addEvent('click', function(e){
			//prevent to load the page when click is detected on a button
			e.stop();
			var kreply = this.get('id');
			var kstate = document.id(kreply+'_form').getStyle('display');
			$$('.kreply-form').setStyle('display', 'none');
			document.id(kreply+'_form').setStyle('display', 'block');
			if (document.id(kreply+'_form').getElement('input[name=anonymous]')) {
				var kuser = document.id(kreply+'_form').getElement('input[name=authorname]').get('value');
				kunenaSelectUsernameView(document.id(kreply+'_form').getElement('input[name=anonymous]'), kuser);
				document.id(kreply+'_form').getElement('input[name=anonymous]').addEvent('click', function(e) {
					kunenaSelectUsernameView(this, kuser);
				});
			}
		});
	});
	
	$$('.kreply-cancel').addEvent('click', function(e){
		$$('.kreply-form').setStyle('display', 'none');
	});
	
	/* Logic for bulkactions */
	$$('input.kcheckall').addEvent('click', function(e){
		this.getParent('form').getElements('input.kcheck').each(function(el){
			if(el.get('checked')==false){
				el.set('checked',true);
				el.set('value','1');
			} else {
				el.set('value','0');
				el.set('checked',false);
			}
		}); 
	});

	$$('select.kchecktask').addEvent('change', function(e){
		ktarget = this.getSiblings('select[name=target]');
		if(this.get('value') == 'move'){
			ktarget.removeProperty('disabled');
		} else {
			ktarget.setProperty('disabled','disabled');
		}
	});
	
	if(document.id('kmod_categories') != undefined){
		document.id('kmod_categories').addEvent('change', function(e){
			kRequestGetTopics(this);
		});
	}
	if(document.id('kmod_topics') != undefined){
		document.id('kmod_topics').addEvent('change', function(e){
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
	$$('a.ktoggler').each(function(link){
		// Auto-hide if the cookie is set.
		if (KCookie.get(link.getProperty('rel'))) {
			link.removeClass('close').addClass('open');
			link.set('title',kunena_toggler_open);
			document.id(link.getProperty('rel')).setStyle('display', 'none');
		}
		
		// Add the onclick event.
		link.addEvent('click', function(){
			if (this.hasClass('close')) {
				this.removeClass('close').addClass('open');
				link.set('title',kunena_toggler_open);
				document.id(this.getProperty('rel')).setStyle('display', 'none');
				KCookie.set(this.getProperty('rel'), 1);
			}
			else {
				this.removeClass('open').addClass('close');
				link.set('title',kunena_toggler_close);
				document.id(this.getProperty('rel')).setStyle('display', '');
				KCookie.erase(this.getProperty('rel'));
			}
		});
	});
	
	// Set autocompleter to off
	$$('.kautocomplete-off').each(function(){
		this.setProperty('autocompleter', 'off');
	});
	if(document.id('kpassword') != undefined && document.id('kpassword2') != undefined){
		document.id('kpassword').setProperty('autocompleter', 'off');
		document.id('kpassword2').setProperty('autocompleter', 'off');
	}
	
	if(document.id('kpoll-moreusers') != undefined){
		document.id('kpoll-moreusers').addEvent('click', function(){
			var displaytype = document.id('kpoll-moreusers-div').getStyle('display');
			if ( displaytype == 'none' ) document.id('kpoll-moreusers-div').setStyle('display');
			else document.id('kpoll-moreusers-div').setStyle('display', 'none');
		});
	}
	
	if ( document.id('kchecbox-all') != undefined ) {
		document.id('kchecbox-all').addEvent('click', function(){
			if ( document.id('kchecbox-all').getProperty('checked') == false ) {
				$$('.kmoderate-topic-checkbox').each(function(box){
					box.removeProperty('checked');
				});
			} else {
				$$('.kmoderate-topic-checkbox').each(function(box){
					box.setProperty('checked', 'checked');
				});
			}
		});	
	}
	
	if ( document.id('kmoderate-select') != undefined ) {
		document.id('kmoderate-select').addEvent('click', function(){
			if ( document.id('kmoderate-select').getSelected().get('value') == 'move' ) {
				document.id('kcategorytarget').setStyle('display');
			}
		});
	}
	
	if ( document.id('avatar_category_select') != undefined ) { 
		document.id('avatar_category_select').addEvent('change', function(e){
			// we getting the name of gallery selected in drop-down by user 
			var avatar_selected= document.id('avatar_category_select').getSelected();
			
			var td_avatar = document.id('kgallery_avatar_list');
			
			// we remove avatar which exist in td tag to allow us to put new one items
			document.id('kgallery_avatar_list').empty(); 
			// we getting from hidden input the url of kunena image gallery
			var url_gallery_main = document.id('Kunena_Image_Gallery_URL').get('value');
			var id_to_select = document.id('Kunena_'+avatar_selected.get('value'));
			var name_to_select = id_to_select.getProperty('name');
			// Convert JSON to object
			var image_object = JSON.decode(id_to_select.get('value'));
			
			// Re-create all HTML items with avatars images from gallery selected by user
			for(var i = 0, len = image_object.length; i < len; ++i) {
				var SpanElement  = new Element('span');
				var LabelElement = new Element('label');
				LabelElement.setProperty('for','kavatar'+i);
				if ( name_to_select != 'default' ) {
					var ImageElement = new Element('img', {src: url_gallery_main+'/'+name_to_select+'/'+image_object[i], alt: ''});
					var InputElement  = new Element('input', {id: 'kavatar'+i, type: 'radio', name: 'avatar', value: 'gallery/'+name_to_select+'/'+image_object[i]});
				} else {
					var ImageElement = new Element('img', {src: url_gallery_main+'/'+image_object[i], alt: ''});
					var InputElement  = new Element('input', {id: 'kavatar'+i, type: 'radio', name: 'avatar', value: 'gallery/'+image_object[i]});
				}
				SpanElement.inject(td_avatar);
				LabelElement.inject(SpanElement);
				ImageElement.inject(LabelElement);
				InputElement.inject(SpanElement);
			}
		});
	}
	
	$$('.kspoiler').each(function(el){
		var contentElement = el.getElement('.kspoiler-content');
		var expandElement = el.getElement('.kspoiler-expand');
		var hideElement = el.getElement('.kspoiler-hide');
		el.getElement('.kspoiler-header').addEvent('click', function(e){
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
	$$('dl.tabs').each(function(tabs){ new KunenaTabs(tabs); });
});
