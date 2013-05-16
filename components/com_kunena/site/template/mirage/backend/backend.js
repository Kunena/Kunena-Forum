window.addEvent("domready",function() {
	var tbody = null;
	var base_table = $$('.kparameters')[0];

	$$('.paramlist tr').each(function(el) {
		if(!el.getElement('input') && !el.getElement('select') && !el.getElement('textarea')){
			var div = new Element('div',{"class":"ksection"});
			div.set('html', '<span class="ktext">'+el.getElement('.paramlist_value').get('html')+'</span><span class="kbutton">Toggle</span>');
			div.inject(base_table,'before');
			var div = new Element('div',{"class":"koptions"});
			div.set('html', '<table cellspacing="1" width="100%" class="paramlist admintable"><tbody></tbody></table>');
			tbody = div.getElement('tbody');
			div.inject(base_table,'before');
		} else if (tbody) {
			el.clone().inject(tbody);
		}
	});
	base_table.dispose();

	new Accordion($$('.ksection'),$$('.koptions'),{
		onActive:function(toggler){ toggler.setProperty("class","ksection active"); },
		onBackground:function(toggler){ toggler.setProperty("class","ksection"); },
		alwaysHide: false
	});
});
