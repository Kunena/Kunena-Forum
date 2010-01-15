/**
* @version $Id$ 
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

//this function insert a text by modifing the DOM, for show infos given by ajax result
function insert_text(textString,nb)
{	
	var polltexthelp = document.getElementById('poll_text_help');
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

//Send the user vote by ajax for save it in the database
function ajax(nb,id,funcdo)
{     
    var datano = "0";
    //Get the element that has selectionned by the user
    for(var j = 0; j < nb; j++){
      if((document.getElementById("radio_name"+j).checked==true)) {
        var data = document.getElementById("radio_name"+j).value;               
        datano = "1";              
        var xhr=null;
        if (window.XMLHttpRequest) {
          xhr = new XMLHttpRequest();
        }
        else if (window.ActiveXObject)
        {
          xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
          var datasendfromserver = xhr.responseText;          
          if(datasendfromserver.match("infoserver=\"1\"")){
        	  var nbimages = '0';
        	  insert_text(KUNENA_POLL_SAVE_ALERT_OK,nbimages);
          }
          if(datasendfromserver.match("infoserver=\"0\"")){
        	  var nbimages = '1';
        	  insert_text(KUNENA_POLL_SAVE_ALERT_ERROR,nbimages);
          }
          if(datasendfromserver.match("infoserver=\"2\"")){
        	  var nbimages = '1';
        	  insert_text(KUNENA_POLL_WAIT_BEFORE_VOTE,nbimages);
          }
          if(datasendfromserver.match("infoserver=\"3\"")){
        	  var nbimages = '1';
        	  insert_text(KUNENA_POLL_CANNOT_VOTE_NEW_TIME,nbimages);
            }
        }

        };
        xhr.open("GET", jliveurl+"index.php?option=com_kunena&func=poll&do="+funcdo+"&radio="+data+"&id="+id, true);
        xhr.send(null);
      }

    }
    if(datano == "0") {
    	var nbimages = '1';
  	  	insert_text(KUNENA_POLL_SAVE_ALERT_ERROR_NOT_CHECK,nbimages);
    }
}