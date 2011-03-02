/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/

window.addEvent('domready', function() {	
	function valuetotaloptions(number){		
		var optiontotal = document.id('numbertotal');
		if(optiontotal != null) {
			optiontotal.set('value',number);
		}				
	}
	function regleCSS(number_field) {
		document.id('opt'+number_field).set('style', {
		    'fontWeight': 'bold'		    
		});
	}	
	
	function create_new_field_now(){		
		  var numfield = number_field-1;		  
		  var polldiv = document.id('kbbcode-poll-options');
		  var hide_input = document.id('nb_options_allowed');
		  valuetotaloptions(number_field);		  
		  var mydiv = new Element('div', {
			  id:'option'+number_field,
			  text:KUNENA_POLL_OPTION_NAME+" "+number_field
		  });		  
		  document.id('helpbox').set('value',KUNENA_EDITOR_HELPLINE_OPTION );
		  var input = new Element('input', {
			  name:'polloptionsID[newoption'+numfield+']',
			  id:'field_option'+numfield,
			  maxlength:'50',
			  size:'30',
			  onmouseover: 'document.id("helpbox").set("value", "'+KUNENA_EDITOR_HELPLINE_OPTION+'")'
		  });		  
		  mydiv.injectInside(polldiv).injectBefore(hide_input);
		  input.inject(mydiv);
		  //regleCSS(number_field); //need to test this on IE
		  number_field++;
		}

		//this function insert a text by modifing the DOM, for show infos given by ajax result
		function insert_text_write(textString)
		{				
			var polldiv = document.id('kbbcode-poll-options');
			var hide_input = document.id('nb_options_allowed');
			var mydiv = new Element('div');
			
			var span = new Element('span');
			
			var myimg = new Element('img', {
				'src':KUNENA_ICON_ERROR					
			});				
			mydiv.injectInside(polldiv).injectBefore(hide_input);
			mydiv.set('id','option_error');
			myimg.injectInside(mydiv);				

			span.injectInside(mydiv);
			span.set('text', textString);
		}		
			
	if(document.id('kbutton-poll-add') != undefined) {
		document.id('kbutton-poll-add').onclick = function () {
			var nboptionsmax = document.id('nb_options_allowed').get('value');			
			if(nboptionsmax == "0") {				
				if(number_field == '1') {
					create_new_field_now();
					create_new_field_now();
				} else {
					create_new_field_now();
				}
			}else {
				if(number_field <= nboptionsmax){
					if(number_field == '1') {
						create_new_field_now();
						create_new_field_now();
					} else {
						create_new_field_now();
					}
				} else {
					if(document.id('option_error')== undefined){
						insert_text_write(KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW);
					}	
				}
			}
		};
	}
	if(document.id('kbutton-poll-rem') != undefined) {
		document.id('kbutton-poll-rem').onclick = function () {
			if(document.id('option_error')){
				document.id('option_error').dispose();
			}
			var matable = document.id('kbbcode-poll-options');		
			if(number_field > 1) {
				number_field = number_field - 1 ;
				var row = document.id('option'+number_field);
				matable.removeChild(row);
				var value = number_field - 1;
				valuetotaloptions(value);
			}
		};
	}

});	