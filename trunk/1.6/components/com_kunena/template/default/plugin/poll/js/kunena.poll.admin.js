/**
* @version $Id: kunena.poll.admin.js 1395 2009-12-30 14:40:22Z xillibit $ 
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

//fill the input filed in configuration panel with items selected in the multiple select list
function onGetCategoriesAllowed()
{
	var getValues = new Array();
	var select = document.getElementById('pollallowedcats');
	for (var i = 0; i < select.options.length; i++) {
		if(select.options[i].selected == true) {
			getValues.push(select.options[i].value);
		}
	}
	//we concats the values selected to be inserted into an input field
	var string_numbers = getValues.join(",");						
	var input = document.getElementById('cfg_pollallowedcategories');
	input.value = string_numbers;
}