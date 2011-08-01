window.addEvent("domready",function(){
	var tabs = [];
	var options = [];
	var opt_iterator = -1;
	var base_table = $ES('.adminform .admintable',$$('#element-box .m')[0])[2];

	$$('.paramlist_value').each(function(el){
		if(!$E('input', el) && !$E('select', el) && !$E('textarea', el)){
			opt_iterator++;
			var div_gen = new Element('div',{"class":"tk_opt"});
			div_gen.innerHTML = '<span class="tk_text">'+el.innerHTML+'</span><span class="tk_btn">Toggle</span>';
			div_gen.injectBefore(base_table);
			tabs.push(div_gen);
			options[opt_iterator] = [];
		}else options[opt_iterator].push(el.getParent());
	});

	options.each(function(el,i){
			var div = new Element('div',{"class":"tk_opts"});
			div.innerHTML = '<td colspan="2"><div class="tk_desc"></div><table cellspacing="1" width="100%" class="paramlist admintable"><tbody></tbody></table></td>';
			div.injectAfter(tabs[i]);
			div_body = div.getElementsBySelector('tbody')[0];
			options[i].each(function(elm,j){ elm.injectInside(div_body); });
	});

	base_table.remove();
	
	$$('.tk_switch').each(function(el){
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

	new Accordion($$('.tk_opt'),$$('.tk_opts'),{
		onActive:function(toggler){ toggler.setProperty("class","tk_opt active"); },
		onBackground:function(toggler){ toggler.setProperty("class","tk_opt"); },
		alwaysHide: true
	});
});