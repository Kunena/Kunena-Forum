<?php

/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

// Get request varibales
$catid = JRequest::getInt ( 'catid', 0 );
$action = JRequest::getVar ( 'action', 'view' );

// Get singletons
$kunena_db = &JFactory::getDBO ();
$kunena_app = & JFactory::getApplication ();
$kunena_my = &JFactory::getUser ();

// perform admin and moderator check
$kunena_is_moderator = CKunenaTools::isModerator ( $kunena_my->id, $catid );

// make sure only admins and valid moderators can proceed
if (! CKunenaTools::isAdmin () && ! $kunena_is_moderator) {
	// Sorry - but you have nothing to do here.
	// This module is for moderators and admins only.

	$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_POST_NOT_MODERATOR') );
} else {
	// Here comes the moderator functionality

	switch ($action) {
		case 'xxx' :

			break;

		case 'yyy' :

			break;

		default :
		case 'view' :

			?>
<script type="text/javascript">
	document.addEvent('domready', function() {

		// Attach auto completer to the following ids:
		new Autocompleter.Request.JSON('ksrc-cat', '<?php echo CKunenaLink::GetJsonURL('autocomplete', 'getcat');?>', { });
		new Autocompleter.Request.JSON('ktrgt-cat', '<?php echo CKunenaLink::GetJsonURL('autocomplete', 'getcat');?>', { });
		new Autocompleter.Request.JSON('ksrc-topic', '<?php echo CKunenaLink::GetJsonURL('autocomplete', 'gettopic');?>', { });
		new Autocompleter.Request.JSON('ktrgt-topic', '<?php echo CKunenaLink::GetJsonURL('autocomplete', 'gettopic');?>', { });
});
</script>
<div class="kbt_cvr1">
<div class="kbt_cvr2">
<div class="kbt_cvr3">
<div class="kbt_cvr4">
<div class="kbt_cvr5">

<h1>Forum Moderation</h1>
<div id="kmod-container">
		<div id="kmod-leftcol">
			<fieldset><legend>Source:</legend>
				<label>
					<span>Category:</span>
					<input type="text" name="ksource-category" class="text" id="ksrc-cat" />
				</label>
				<label>
					<span>Topic:</span>
					<input type="text" name="ksource-topic" class="text" id="ksrc-topic" />
				</label>
			</fieldset>
		</div>
		<div id="kmod-rightcol">
			<form id="ktarget">
				<fieldset><legend>Target:</legend>
					<label>
					<span>Category:</span>
					<input type="text" name="ktarget-category" class="text" id="ktrgt-cat" />
					</label>
					<label>
					<span>Topic:</span>
					<input type="text" name="ktarget-topic" class="text" id="ktrgt-topic" />
					</label>
				</fieldset>
			</form>

			<div class="clr"></div>
		</div>
</div>







</div>
</div>
</div>
</div>
</div>
<?php

			break;
	}
}
