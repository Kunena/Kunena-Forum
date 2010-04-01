<?php
/**
 * @version $Id$
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
			<?php if (! isset ( $this->msg_html->closed )) {
				if (isset ( $this->msg_html->quickreply )) echo " " . $this->msg_html->quickreply;
				if (isset ( $this->msg_html->reply )) { echo " " . $this->msg_html->reply; }
				if (isset ( $this->msg_html->quote )) { echo " " . $this->msg_html->quote; }
				if (isset ( $this->msg_html->edit )) { echo " " . $this->msg_html->edit; }
				if (isset ( $this->msg_html->merge )) { echo " " . $this->msg_html->merge; }
				if (isset ( $this->msg_html->split )) { echo " " . $this->msg_html->split; }
				if (isset ( $this->msg_html->delete )) { echo " " . $this->msg_html->delete; }
				if (isset ( $this->msg_html->move )) { echo " " . $this->msg_html->move; }
				if (isset ( $this->msg_html->publish )) { echo " " . $this->msg_html->publish; }
			} else { echo $this->msg_html->closed; }
			?>
	</div>
</div>