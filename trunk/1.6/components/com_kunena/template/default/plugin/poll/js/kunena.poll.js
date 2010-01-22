/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

window.addEvent('domready', function() {	
	function valuetotaloptions(number){		
		var optiontotal = $('numbertotal');
		if(optiontotal != null) {
			optiontotal.set('value',number);
		}
		var optiontotalet = $('numbertotalr');
		if(optiontotalet != null) {
			optiontotalet.set('value',number);
		}		
	}
	function regleCSS(number_field) {
		$('opt'+number_field).set('style', {
		    'fontWeight': 'bold'		    
		});
	}
	
	function create_new_field_now(){			
		  var numfield = number_field-1;
		  var tablebody =document.getElementById('kpost_message');
		  var buttons = $("kpost_buttons_tr");
		  valuetotaloptions(number_field);
		  var mytr = new Element('tr', {
				'class':'ksectiontableentry2'				
			});
		  var mytd = new Element('td', {
				'class':'kleftcolumn'
			});
		  var mystrong = new Element('strong');
		  var input = new Element('input');
		  var mytd2 = new Element('td');
		  mytr.injectInside(tablebody).injectBefore(buttons);
		  mytr.set('id','option'+number_field);
		  mytd.injectInside(mytr);		    
		  mytd.set('id','opt'+number_field);
		  mystrong.injectInside(mytd);
		  mystrong.set('text',KUNENA_POLL_OPTION_NAME+" "+number_field);		  
		  mytd2.injectInside(mytr);
		  mytd2.set('name','field_option'+numfield);
		  mytd2.set('id','field_option'+numfield);
		  input.inject(mytd2);
		  input.set('name','field_option'+numfield);
		  input.set('id','field_option'+numfield);		 
		  regleCSS(number_field);
		  number_field++;
		}

		//this function insert a text by modifing the DOM, for show infos given by ajax result
		function insert_text_write(textString)
		{				
				var tablebody = $('kpost_message');
				var buttons = $("kpost_buttons_tr");
				var mytr = new Element('tr', {
					'class':'ksectiontableentry2',
					'id':'option_error'
				});
				
				var mytd = new Element('td', {
					'class':'kleftcolumn',
					'id':'error'
				});
				
				var mytd2 = new Element('td', {					
					'id':'error'
				});
				
				var myimg = new Element('img', {
					'src':KUNENA_ICON_ERROR					
				});
				
				mytr.injectInside(tablebody).injectBefore(buttons);				
				mytd.injectInside(mytr);
				myimg.injectInside(mytd);
				mytd2.injectInside(mytr);
				mytd2.set('text', textString);						
		}

	
	$('kbutton_poll_add').onclick = function () {
		var nboptionsmax = $('nb_options_allowed').get('value');
		if(nboptionsmax == "0") {
		    create_new_field_now();
		}else {
			if(number_field <= nboptionsmax){
		      create_new_field_now();
		    } else {
		    	if($('option_error')== undefined){
		    		insert_text_write(KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW);
		    	}	
		    }
		}
	};
	$('kbutton_poll_rem').onclick = function () {
		if($('option_error')){
			$('option_error').dispose();
		}
		var matable = $('kpost_message');		
		if(number_field > 1) {
	      number_field = number_field - 1 ;
	      var row = $('option'+number_field);
	      matable.removeChild(row);
	      var value = number_field - 1;
	      valuetotaloptions(value);
		}
	};

});	