jQuery(function()
{
	jQuery(".ListToggleCube").click(function() {
                $(".boxwrap1").removeClass("bmax").addClass("bmin");
				$(".fbboxchildlist").removeClass("bmaxchild").addClass("bminchild");
				$.cookie('fbcatlisting', 'min', { expires: 700 });
				 
    });	
	jQuery(".ListToggleList").click(function() {
                $(".boxwrap1").removeClass("bmin").addClass("bmax");
				$(".fbboxchildlist").removeClass("bminchild").addClass("bmaxchild");
				$.cookie('fbcatlisting', 'max', { expires: 700 });
				
    });
	
	jQuery(".ListThreadToggleCube").click(function() {
                $(".boxwrap1").removeClass("boxthreadmax").addClass("boxthreadmin");
				$.cookie('fbthreadlisting', 'boxthreadmin', { expires: 700 });
    });	
	jQuery(".ListThreadToggleList").click(function() {
                $(".boxwrap1").removeClass("boxthreadmin").addClass("boxthreadmax");
				$.cookie('fbthreadlisting', 'boxthreadmax', { expires: 700 });
    });
	
  });