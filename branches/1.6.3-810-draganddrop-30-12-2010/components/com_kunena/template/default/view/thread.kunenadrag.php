<?php
/**
 * @version $Id: message.attachments.php 3864 2010-11-05 16:23:40Z fxstein $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
$kunena_drag_delete = 'index.php?option=com_kunena&amp;func=post&amp;do=deletethread&amp;catid='. "$this->catid" .'&amp;id='. "$this->id" . '&amp;' .JUtility::getToken() .'=1&amp;Itemid='. KunenaRoute::getItemid() ;

 if (CKunenaTools::isModerator($this->my->id)) : ?>
<style>
#Kunena .kblock div.kheader {
	border-bottom:2px solid;
	padding:0 0 0 0;
}

</style>
<script type="text/javascript">
//look for the functions within the external javascript file
window.addEvent('domready', function() {
	var dragElement = $('drag_me');
	var dragContainer = $('drag_cont');
	var dragHandle = $('drag_me_handle');
	var dropElement = $$('.draggable');
	var dropDrop = $('drop_in_droppable');

	var myDrag = new Drag.Move(dragElement, {
	    // Drag.Move options
		droppables: dropElement,
		container: dragContainer,
		// Drag options
		handle: dragHandle,
		// Drag.Move Events
		onDrop: function(el, dr) {
			if (!dr) { }
        	else {
				el.highlight('#fff'); //flashes white
				dr.highlight('#FF0000');
				window.open(' <?php echo $kunena_drag_delete ?>', '_self');
			};
		},
		onEnter: function(el, drop_here){
			drop_here.highlight('#FF0000');

		},

	});
});
</script>

<?php endif; ?>
