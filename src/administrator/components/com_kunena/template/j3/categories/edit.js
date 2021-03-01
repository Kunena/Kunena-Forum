jQuery(document).ready(function ($) {
	var input_alias = $("#jform_aliases");
	var box = $("#aliascheck");
	var info = $("#aliasinfo");
	var apply = $(".button-apply");
	var save = $(".button-save");
	var savenew = $(".button-save-new");
	var savecopy = $(".button-save-copy");

	input_alias.on('input', function () {
		$.ajax({
			dataType: "json",
			url: "index.php?option=com_kunena&view=categories&format=raw&layout=chkAliases&alias=" + input_alias.val()
		}).done(function (response) {
			if (!response.msg) {
				input_alias.addClass("inputbox invalid-border");

				if (box.length) {
					apply.prop('disabled', true);
					save.prop('disabled', true);
					savenew.prop('disabled', true);
					savecopy.prop('disabled', true);
				}

				box.removeClass("valid icon-ok");
				box.addClass("invalid icon icon-remove");
				box.html(Joomla.JText._('COM_KUNENA_CATEGORIES_ERROR_CHOOSE_ANOTHER_ALIAS'));
			}
			else {
				input_alias.addClass("inputbox");
				input_alias.removeClass("invalid-border");

				if (box.length) {
					apply.prop('disabled', false);
					save.prop('disabled', false);
					savenew.prop('disabled', false);
					savecopy.prop('disabled', false);
				}

				if (box.length) {
					info.addClass("valid icon icon-ok");
					info.html("");
				}
			}
		});
	});
});
