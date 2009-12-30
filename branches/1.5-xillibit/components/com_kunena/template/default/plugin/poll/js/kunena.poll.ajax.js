/**
* @version $Id: 
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

//Send the user vote by ajax for save it in the database
function ajax(nb,id)
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
            alert(KUNENA_POLL_SAVE_ALERT_OK);
          }
          if(datasendfromserver.match("infoserver=\"0\"")){
            alert(KUNENA_POLL_SAVE_ALERT_ERROR);
          }
          if(datasendfromserver.match("infoserver=\"2\"")){
            alert(KUNENA_POLL_WAIT_BEFORE_VOTE);
          }
          if(datasendfromserver.match("infoserver=\"3\"")){
              alert(KUNENA_POLL_CANNOT_VOTE_NEW_TIME);
            }
        }

        };
        xhr.open("GET", jliveurl+"index.php?option=com_kunena&func=poll&do=results&radio="+data+"&id="+id, true);
        xhr.send(null);
      }

    }
    if(datano == "0") {
      alert(KUNENA_POLL_SAVE_ALERT_ERROR_NOT_CHECK);
    }
}