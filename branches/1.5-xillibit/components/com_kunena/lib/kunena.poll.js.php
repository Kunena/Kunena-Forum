<?php
/**
* @version $Id: kunena.poll.js.php $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

defined( '_JEXEC' ) or die('Restricted access');
?>

<script language = "JavaScript" type = "text/javascript">
<?php
echo "var jliveurl =\"".KUNENA_JLIVEURL."\";";
?>
var number_field = "1";

//This function fill the field hidden with the options number for a poll
function valuetotaloptions(value){
  var number = value;
  var optiontotal = document.getElementById('numbertotal');
  if(optiontotal != null) {
    optiontotal.setAttribute('value',number);
  }
  var optiontotalet = document.getElementById('numbertotalr');
  if(optiontotalet != null) {
     optiontotalet.setAttribute('value',number);
  }
}

//Function only for IE
function regleCSS(number_field) {
   document.getElementById('opt'+number_field).style.fontWeight = "bold";
}

function create_new_field_now(){
  valuetotaloptions(number_field);
  var tablebody =document.getElementById('fb_post_message');
  var row = document.createElement("tr");
  row.className="<?php echo $boardclass; ?>sectiontableentry2";
  row.setAttribute('id','option'+number_field);
  cell = document.createElement("td");
  cell.setAttribute('id','opt'+number_field);
  cell.className ="fb_leftcolumn";
  texte = document.createTextNode("<?php echo _KUNENA_POLL_OPTION_NAME; ?> "+number_field);
  cell.appendChild(texte);
  row.appendChild(cell);
  cell = document.createElement("td");
  var field_option = document.createElement("input");
  cell.appendChild(field_option);
  //field_option.setAttribute('type','text');
  field_option.setAttribute('name','field_option'+number_field);
  row.appendChild(cell);
  tablebody.appendChild(row);
  document.getElementById("fb_post_message").insertBefore(row,document.getElementById("fb_post_buttons_tr"));
  regleCSS(number_field);
  number_field++;
}

//Create only a new poll options, the function valueoptions get the number 1 and check the options maximu for number
function new_field(nboptionsmax){
  if(nboptionsmax == "0") {
    create_new_field_now();
  }else {
    if(number_field <= nboptionsmax){
      create_new_field_now();
    } else {
      alert("<?php echo _KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW; ?>");
    }
  }
}

//Delete a poll option
function delete_field(){
  var matable = document.getElementById('fb_post_message');
  if(number_field > 1) {
      number_field = number_field - 1 ;
      var row = document.getElementById('option'+number_field);
      matable.removeChild(row);
      var value = number_field - 1;
      valuetotaloptions(value);
  }
}

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
            alert(<?php echo "\""._KUNENA_POLL_SAVE_ALERT_OK."\"";?>);
          }
          if(datasendfromserver.match("infoserver=\"0\"")){
            alert(<?php echo "\""._KUNENA_POLL_SAVE_ALERT_ERROR."\"";?>);
          }
          if(datasendfromserver.match("infoserver=\"2\"")){
            alert(<?php echo "\""._KUNENA_POLL_WAIT_BEFORE_VOTE."\"";?>);
          }
        }

        };
        xhr.open("GET", jliveurl+"/index.php?option=com_kunena&func=poll&do=results&radio="+data+"&id="+id, true);
        xhr.send(null);
      }

    }
    if(datano == "0") {
      alert(<?php echo "\""._KUNENA_POLL_SAVE_ALERT_ERROR_NOT_CHECK."\"";?>);
    }
}

</script>
