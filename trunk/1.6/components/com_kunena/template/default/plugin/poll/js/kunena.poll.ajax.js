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
	function insert_text(textString,nb)
	{	
		var polltexthelp = $('poll_text_help');
		//Remove the content of the html tag <p></p> before to add something else
		//i don't know if it's possible to use something else than innerHTML() function for do this purpose
		polltexthelp.innerHTML='';
		var newinfo = document.createElement("p");
		newinfo.setAttribute('id','poll_text_infos');
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
	
	$('k_poll_button_vote').onclick = function () {		
		var nb = $('k_poll_nb_options').get('value');
		var id = $('k_poll_id').get('value');
		var funcdo = $('k_poll_do').get('value');
		var datano = '0';
		for(var j = 0; j < nb; j++){
		      if((document.getElementById("radio_name"+j).checked==true)) {
		        var data = $('radio_name'+j).get('value');               
		        datano = "1"; 
		      }
		}
		if(datano == "0") {
	    	var nbimages = '1';
	  	  	insert_text(KUNENA_POLL_SAVE_ALERT_ERROR_NOT_CHECK,nbimages);
	    } else {    	
	    	var url = jliveurl+"index.php?option=com_kunena&func="+funcdo+"&radio="+data+"&id="+id;	    	
	    	var request = new Request.JSON({
				url: url,
				onComplete: function(jsonObj) {	    			
	    			var nb = '0';
					if(jsonObj.results == '1'){						
						insert_text(KUNENA_POLL_SAVE_ALERT_OK,nb);
					} else if(jsonObj.results == '2') {
						nb = '1';
						insert_text(KUNENA_POLL_CANNOT_VOTE_NEW_TIME,nb);
					} else if(jsonObj.results == '3') {
						nb = '1';
						insert_text(KUNENA_POLL_WAIT_BEFORE_VOTE,nb);
					}
				}
			}).send();

	    }
	};
});
