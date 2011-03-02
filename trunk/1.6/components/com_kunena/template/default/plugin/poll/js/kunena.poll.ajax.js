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
	function insert_text(textString,nb)
	{	
		var polltexthelp = document.id('kpoll-text-help');
		//Remove the content of the html tag <p></p> before to add something else
		//i don't know if it's possible to use something else than innerHTML() function for do this purpose
		polltexthelp.innerHTML='';
		var newinfo = document.createElement("p");
		newinfo.setAttribute('id','kpoll-text-infos');
		var image = document.createElement("img");	
		if(nb == '1'){				
			image.setAttribute('src',KUNENA_ICON_ERROR);		
			texte = document.createTextNode(' '+textString); 
		}else {		
			image.setAttribute('src',KUNENA_ICON_INFO);		
			texte = document.createTextNode(' '+textString); 
		}	
		polltexthelp.appendChild(newinfo);
		newinfo.appendChild(image);
		newinfo.appendChild(texte);
	}
	
	if(document.id('kpoll-form-vote') != undefined) {
		document.id('kpoll-form-vote').addEvent('submit', function(e) {
			var jsonurl = document.id('kpollvotejsonurl').getProperty('value');
			document.id('kpoll-form-vote').setProperty('action',jsonurl);
			//Prevents the default submit event from loading a new page.
			e.stop();
			var datano = '0';
			$$('.kpoll-boxvote').each(function(el){
				if(el.checked==true){
					datano = "1";
				}
			});
			
			if(datano == "0") {
				var nbimages = '1';
  	  			insert_text(KUNENA_POLL_SAVE_ALERT_ERROR_NOT_CHECK,nbimages);
			} else { 
				//Set the options of the form's Request handler.
				//("this" refers to the document.id('myForm') element).
				this.set('send', {onComplete: function(response) {
					var json = JSON.decode(response);
					var nb = '0';
					if(json.results == '1'){
						insert_text(KUNENA_POLL_SAVE_ALERT_OK,nb);
					} else if(json.results == '2') {
						nb = '1';
						insert_text(KUNENA_POLL_CANNOT_VOTE_NEW_TIME,nb);
					} else if(json.results == '3') {
						nb = '1';
						insert_text(KUNENA_POLL_WAIT_BEFORE_VOTE,nb);
					}
				}});
				//Send the form.
				this.send();
			}
		});
	}	
});
