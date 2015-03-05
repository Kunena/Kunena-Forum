/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

/* Javascript file for debugging Mootools conflicts */

var kmt_domready = false;

if (typeof window.addEvent != 'undefined') {
	window.addEvent('domready', function(){
		kmt_domready = true;
	});
}

window.onload=function(){
	// MooTools is not loaded
	if (typeof MooTools == 'undefined') {
		alert('Kunena: MooTools JavaScript library is not loaded!');
		return;
	}
	// Using deprecated MooTools version
	var kmt_version = MooTools.version.split('.');
	if (kmt_version[0] == 1 && kmt_version[1] >= 10) {
		alert('Kunena: Deprecated MooTools ' + MooTools.version+ ' JavaScript library loaded!');
		return;
	}
	// MooTools addEvent function does not exist
	if (typeof window.addEvent == 'undefined') {
		alert('Kunena: MooTools window.addEvent() is not a function!');
		return;
	}
	// MooTools domready event not fired
	if (kmt_domready != true) {
		alert('Kunena: MooTools domready event was never fired!');
		return;
	}
};
