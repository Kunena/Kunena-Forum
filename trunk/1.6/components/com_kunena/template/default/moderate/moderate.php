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
defined( '_JEXEC' ) or die();

// Get request varibales
$catid = JRequest::getInt ( 'catid', 0 );
$action = JRequest::getVar( 'action', 'view' );

// Get singletons
$kunena_db = &JFactory::getDBO();
$kunena_app =& JFactory::getApplication();
$kunena_my = &JFactory::getUser();

// perform admin and moderator check
$kunena_is_admin = CKunenaTools::isAdmin ();
$kunena_is_moderator = CKunenaTools::isModerator($kunena_my->id, $catid);

// make sure only admins and valid moderators can proceed
if (!$kunena_is_admin && !$kunena_is_moderator){
	// Sorry - but you have nothing to do here.
	// This module is for moderators and admins only.

	$kunena_app->redirect ( htmlspecialchars_decode ( JRoute::_ ( KUNENA_LIVEURLREL ) ), _POST_NOT_MODERATOR );
}
else {
	// Here comes the moderator functionality

	switch ($action)
	{
    case 'xxx':

        break;

    case 'yyy':

        break;

    default:
    case 'view':




    	?>
<div class="kbt_cvr1">
<div class="kbt_cvr2">
<div class="kbt_cvr3">
<div class="kbt_cvr4">
<div class="kbt_cvr5">
<table
	class="kblocktable<?php
				echo isset ( $cat->class_sfx ) ? ' kblocktable' . $cat->class_sfx : '';
				?>"
	width="100%" id="kcat<?php
				echo $cat->id;
				?>" border="0"
	cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th colspan="5">
			<div class="ktitle_cover kl">
				Forum Moderation
			</div>
			<div class="ktitle_desc km">
				The moderation central for Kunena. Perform various moderation tasks from a central interface.
			</div>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan = "5">
				<div>
					This is where the text goes...
				</div>
			</td>
		</tr>
	</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
    	<?php

        break;
	}
}
