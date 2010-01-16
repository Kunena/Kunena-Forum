<?php
/**
 * @version $Id: kunena.button.php 1210 2009-11-23 06:51:41Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
global $kunena_icons;
$catid = JRequest::getInt ( 'catid', 0 );
?>
<div class="kmessage_buttons_cover">
<div class="kmessage_buttons_row">
		<?php if (! isset ( $msg_html->closed )) {
			if (isset ( $msg_html->quickreply )) echo " " . $msg_html->quickreply;
			echo " " . $msg_html->reply;
			echo " " . $msg_html->quote;
			if (CKunenaTools::isModerator ( $kunena_my->id, $catid ))
				//echo ' </div><div class="kmessage_buttons_row">';
			if (isset ( $msg_html->merge )) { echo " " . $msg_html->merge; }
			if (isset ( $msg_html->split )) { echo " " . $msg_html->split; }
			if (isset ( $msg_html->delete )) { echo " " . $msg_html->delete; }
			if (isset ( $msg_html->edit )) { echo " " . $msg_html->edit; }
		} else { echo $msg_html->closed; }
		?>
</div>
</div>