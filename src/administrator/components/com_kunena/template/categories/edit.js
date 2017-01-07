jQuery(document).ready(function($) {
	var input_alias = $("#jform_aliases");
	var box = $("#aliascheck");

	input_alias.on('input', function() {
	$.ajax({
		dataType: "json",
		url: "index.php?option=com_kunena&view=categories&format=raw&layout=chkAliases&alias="+input_alias.val(),
		}).done(function(response) {
			if (!response.msg){
				input_alias.addClass("inputbox invalid-border");

				if (box.length)
				{
					box.removeClass("valid icon-ok");			
					box.addClass("invalid icon icon-remove");
					box.html(Joomla.JText._('COM_KUNENA_CATEGORIES_ERROR_CHOOSE_ANOTHER_ALIAS'));
				}
			}
			else 
			{
				input_alias.addClass("inputbox"); 
				input_alias.removeClass("invalid-border");

				if (box.length)
				{
					box.addClass("valid icon icon-ok");
					box.html("");
				}
			} 
		});
	});
  
  $('.category_aliases').change(function() {
    if(!$(this).is(':checked')){
      var alias_name = $(this).attr('value');
      
      $.ajax({
		    dataType: "json",
		    url: "index.php?option=com_kunena&view=categories&format=raw&layout=DeleteAlias&alias="+alias_name,
		  }).done(function(response) {
	       $('#checklist_aliases_'+alias_name).parent().remove(); 
		  });

    }     
  });
});