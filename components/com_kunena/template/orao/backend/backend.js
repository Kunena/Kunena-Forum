window.addEvent("domready",function(){
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
	
	$$('.kswitch').each(function(el){
		el.setStyle('display','none');
		var style = (el.value == 1) ? 'on' : 'off';
		var switcher = new Element('div',{'class' : 'switcher-'+style});
		switcher.injectAfter(el);
		switcher.addEvent("click", function(){
			if(el.value == 1){
				switcher.setProperty('class','switcher-off');
				el.value = 0;
			}else{
				switcher.setProperty('class','switcher-on');
				el.value = 1;
			}
		});
	});
	$$('.group-end').each(function(el){
		var new_tr = new Element('tr');
		var elm = el.getParent().getParent();
		new_tr.injectAfter(elm);
		new_tr.innerHTML = '<td width="" style="height:5px;background:#eee !important;padding:0 !important;" class="paramlist_key"></td><td class="paramlist_value" style="height:5px;background:#eee;padding:0 !important;"></td>';
	});

	new Accordion($$('.ksection'),$$('.koptions'),{
		onActive:function(toggler){ toggler.setProperty("class","ksection active"); },
		onBackground:function(toggler){ toggler.setProperty("class","ksection"); },
		alwaysHide: true
	});
});