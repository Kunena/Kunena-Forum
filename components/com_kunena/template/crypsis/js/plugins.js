/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

function showMessage() {
	div = document.getElementById('tow', 'test1', 'k', 'row');
	div.style.display = "block";
}

function hideMessage() {
	div = document.getElementById('tow','test2', 'k', 'row');
	div.style.display = "none";
}

window.addEvent('domready', function(){	
	$$('.kspoiler').each(function(el){
		var contentElement = el.getElement('.kspoiler-content');
		var expandElement = el.getElement('.kspoiler-expand');
		var hideElement = el.getElement('.kspoiler-hide');
		el.getElement('.kspoiler-header').addEvent('click', function(e){
			if (contentElement.style.display == "none") {
				contentElement.setStyle('display');
				expandElement.setStyle('display', 'none');
				hideElement.setStyle('display');
			} else {
				contentElement.setStyle('display', 'none');
				expandElement.setStyle('display');
				hideElement.setStyle('display', 'none');
			}
		});
	});
});

