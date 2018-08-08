/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 *
 * @copyright     Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
**/

jQuery(document).ready(function() {
	jQuery(".fancybox-button").fancybox({
		type: 'image',
		prevEffect		: 'none',
		nextEffect		: 'none',
		closeBtn		:  true,
		helpers		: {
			title	: { type : 'inside' },
			buttons	: {}
		}
	});
});