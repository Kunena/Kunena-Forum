/**
* Kunena Component
* @package Kunena.Template.Blue_Eagle
*
* @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/

window.addEvent('domready', function() {
	function create_new_field_now(optionid) {
		var polldiv = document.id('kbbcode-poll-options');
		var hide_input = document.id('nb_options_allowed');
		var mydiv = new Element('div', {
			'class':'polloption',
			text:KUNENA_POLL_OPTION_NAME+' '+optionid+' '
		});
		document.id('helpbox').set('value',KUNENA_EDITOR_HELPLINE_OPTION );
		var input = new Element('input', {
			name:'polloptionsID[new'+optionid+']',
			id:'field_option'+optionid,
			type: 'text',
			maxlength:'35',
			onmouseover: 'document.id("helpbox").set("value", "'+KUNENA_EDITOR_HELPLINE_OPTION+'")'
		});
		mydiv.inject(polldiv);
		mydiv.inject(hide_input, 'before');
		input.inject(mydiv);
	}

	//this function insert a text by modifing the DOM, for show infos given by ajax result
	function insert_text_write(textString) {
		var polldiv = document.id('kbbcode-poll-options');
		var hide_input = document.id('nb_options_allowed');
		var mydiv = new Element('div');

		var span = new Element('span');

		var myimg = new Element('img', {
			'src':KUNENA_ICON_ERROR
		});
		mydiv.inject(polldiv);
		mydiv.inject(hide_input, 'before');
		mydiv.set('id','option_error');
		myimg.inject(mydiv);

		span.inject(mydiv);
		span.set('text', textString);
	}

	if(document.id('kbutton-poll-add') != undefined) {
		document.id('kbutton-poll-add').onclick = function () {
			var nboptionsmax = document.id('nb_options_allowed').get('value');
			var koptions = document.id('kbbcode-poll-options').getChildren('div.polloption');
			if(!nboptionsmax || (koptions.length < nboptionsmax && koptions.length > 1 ) ){
				create_new_field_now(koptions.length+1);
			} else if ( !nboptionsmax || koptions.length < 1 ) {
				create_new_field_now(koptions.length+1);
				create_new_field_now(koptions.length+2);
			} else {
				if(document.id('option_error')== undefined){
					insert_text_write(KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW);
				}
			}
		};
	}
	if(document.id('kbutton-poll-rem') != undefined) {
		document.id('kbutton-poll-rem').onclick = function () {
			var koption = document.id('kbbcode-poll-options').getLast('div.polloption');
			if(koption) {
				var koption_error = document.id('option_error');
				if(koption_error){
					document.id('option_error').dispose();
				}
				koption.dispose();
			}
		};
	}

});
