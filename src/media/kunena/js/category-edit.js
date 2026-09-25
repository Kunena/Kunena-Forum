/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 *
 * Frontend copy of administrator/components/com_kunena/template/categories/edit.js,
 * kept separate so the frontend category-manage form (site/template/aurelia/
 * layouts/category/manage/default.php) never depends on an administrator/
 * asset path. Some sites block administrator/ entirely at the webserver
 * level for anyone not logged into the backend, which would silently break
 * the alias check below if it were loaded from there.
 **/

jQuery(document).ready(function ($) {
	const input_alias = $("#jform_aliases");
	const box = $("#aliascheck");
	const info = $("#aliasinfo");
	const submitButton = $('input[name="submit"]');

	input_alias.on('input', function () {
		$.ajax({
			dataType: "json",
			url: "index.php?option=com_kunena&view=category&task=category.chkaliases&alias=" + input_alias.val() + "&format=json"
		}).done(function (response) {
			if (!response.msg) {
				input_alias.addClass("inputbox invalid-border");
				submitButton.prop('disabled', true);

				box.removeClass("valid icon-ok");
				box.addClass("invalid icon icon-remove");
				box.html(Joomla.Text._('COM_KUNENA_CATEGORIES_ERROR_CHOOSE_ANOTHER_ALIAS'));
			}
			else {
				input_alias.addClass("inputbox");
				input_alias.removeClass("invalid-border");
				submitButton.prop('disabled', false);

				if (box.length) {
					info.addClass("valid icon icon-ok");
					info.html("");
				}
			}
		});
	});
});
