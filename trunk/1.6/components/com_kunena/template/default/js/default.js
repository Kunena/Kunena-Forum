
/* Javascript file for default Kunena BlueEagle template */

/* Tabs class */
var JTabs = new Class({
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
		descriptionSelector: 'dd',
	},

    initialize: function(dlist, options){
		this.setOptions(options);
        this.dlist = document.id(dlist);
        this.titles = this.dlist.getElements(this.options.titleSelector);
        this.descriptions = this.dlist.getElements(this.options.descriptionSelector);
        this.content = new Element('div').inject(this.dlist, 'after').addClass('current');

        for (var i = 0, l = this.titles.length; i < l; i++){
            var title = this.titles[i];
            var description = this.descriptions[i];
            title.setStyle('cursor', 'pointer');
            title.addEvent('click', this.display.bind(this, i));
            description.inject(this.content);
        }

        if ($chk(this.options.display)) this.display(this.options.display);

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

/* Slider functions */

/* Top profile box 
window.addEvent('domready', function() {
	var status = {
		'true': '<span class="close"></span>',
		'false': '<span class="open"></span>'
	};
	var myVerticalSlide = new Fx.Slide('kprofilebox');
	$('kprofilebox_toggle').addEvent('click', function(e){
		e.stop();
		myVerticalSlide.toggle();
	});
	myVerticalSlide.addEvent('complete', function() {
		$('kprofilebox_status').set('html', status[myVerticalSlide.open]);
	});

});
*/
/* Main forum list 
window.addEvent('domready', function() {
	var status = {
		'true': '<span class="close"></span>',
		'false': '<span class="open"></span>'
	};
	var myVerticalSlide = new Fx.Slide('kmainforum');
	$('kmainforum_toggle').addEvent('click', function(e){
		e.stop();
		myVerticalSlide.toggle();
	});
	myVerticalSlide.addEvent('complete', function() {
		$('kmainforum_status').set('html', status[myVerticalSlide.open]);
	});

});
*/
/* Who is online 
window.addEvent('domready', function() {
	var status = {
		'true': '<span class="close"></span>',
		'false': '<span class="open"></span>'
	};
	var myVerticalSlide = new Fx.Slide('whoisonline_tbody');
	$('kwhoisonline_toggle').addEvent('click', function(e){
		e.stop();
		myVerticalSlide.toggle();
	});
	myVerticalSlide.addEvent('complete', function() {
		$('kwhoisonline_status').set('html', status[myVerticalSlide.open]);
	});

});

/* Member stats 
window.addEvent('domready', function() {
	var status = {
		'true': '<span class="close"></span>',
		'false': '<span class="open"></span>'
	};
	var myVerticalSlide = new Fx.Slide('frontstats_tbody');
	$('kstats_toggle').addEvent('click', function(e){
		e.stop();
		myVerticalSlide.toggle();
	});
	myVerticalSlide.addEvent('complete', function() {
		$('kstats_status').set('html', status[myVerticalSlide.open]);
	});

});
*/

//----------------- New Mootools based behaviors ----------------------

window.addEvent('domready', function(){	
	// Get the kunena settings cookie data.
	KCookie = new Hash.Cookie('kunena_settings', {duration: 3600});

	// Setup the behavior for all kunena toggler elements.
	$$('a.ktoggler').each(function(link){
		// Auto-hide if the cookie is set.
		if (KCookie.get('hide_'+link.getProperty('rel'))) {
			link.removeClass('close').addClass('open');
			document.id(link.getProperty('rel')).setStyle('display', 'none');
		}
		
		// Add the onclick event.
		link.addEvent('click', function(){
			if (this.hasClass('close')) {
				this.removeClass('close').addClass('open');
				document.id(this.getProperty('rel')).setStyle('display', 'none');
				KCookie.set('hide_'+this.getProperty('rel'), true);
			}
			else {
				this.removeClass('open').addClass('close');
				document.id(this.getProperty('rel')).setStyle('display', '');
				KCookie.set('hide_'+this.getProperty('rel'), false);
			}
		});
	});
});

