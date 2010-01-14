/**
* @version $Id: kunena.poll.js 1395 2010-12-30 14:40:22Z xillibit $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

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
  var numfield = number_field-1;
  valuetotaloptions(number_field);
  var tablebody =document.getElementById('kpost_message');
  var row = document.createElement("tr");
  row.className=boardclass+"sectiontableentry2";
  row.setAttribute('id','option'+number_field);
  cell = document.createElement("td");
  cell.setAttribute('id','opt'+number_field);
  cell.className ="kleftcolumn";
  texte = document.createTextNode(KUNENA_POLL_OPTION_NAME+" "+number_field);
  cell.appendChild(texte);
  row.appendChild(cell);
  cell = document.createElement("td");
  var field_option = document.createElement("input"); 
  field_option.setAttribute('name','field_option'+numfield);
  field_option.setAttribute('id','field_option'+numfield);  
  cell.appendChild(field_option);
  //field_option.setAttribute('type','text');    
  row.appendChild(cell);
  tablebody.appendChild(row);
  document.getElementById("kpost_message").insertBefore(row,document.getElementById("kpost_buttons_tr"));
  regleCSS(number_field);
  number_field++;
}

//this function insert a text by modifing the DOM, for show infos given by ajax result
function insert_text_write(textString)
{	
	if(document.getElementById('option_error') == undefined){
		var tablebody = document.getElementById('kpost_message');
		var row = document.createElement("tr");
		row.className=boardclass+"sectiontableentry2";
		row.setAttribute('id','option_error');
		cell = document.createElement("td");
		cell.setAttribute('id','error');
		cell.className ="kleftcolumn";
		var image = document.createElement("img");			
		image.setAttribute('src',KUNENA_ICON_ERROR);
		cell.appendChild(image);
		row.appendChild(cell);
		cell = document.createElement("td");
		texte = document.createTextNode(textString);
		//field_option.setAttribute('type','text');    
		row.appendChild(cell);
		cell.appendChild(texte);
		tablebody.appendChild(row);
		document.getElementById("kpost_message").insertBefore(row,document.getElementById("kpost_buttons_tr"));	
	}
}

//Create only a new poll options, the function valueoptions get the number 1 and check the options maximu for number
function new_field(nboptionsmax){
  if(nboptionsmax == "0") {
    create_new_field_now();
  }else {
    if(number_field <= nboptionsmax){
      create_new_field_now();
    } else {
    	insert_text_write(KUNENA_POLL_NUMBER_OPTIONS_MAX_NOW);
    }
  }
}

//Delete a poll option
function delete_field(){
  var matable = document.getElementById('kpost_message');
  if(number_field > 1) {
      number_field = number_field - 1 ;
      var row = document.getElementById('option'+number_field);
      matable.removeChild(row);
      var value = number_field - 1;
      valuetotaloptions(value);
  }
}