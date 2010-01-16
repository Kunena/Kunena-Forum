<?php
/**
 * @version $Id: vertical.php 1210 2009-11-23 06:51:41Z mahagr $
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
$kunena_config = & CKunenaConfig::getInstance ();

if ($kunena_config->avposition == 'left' || $kunena_config->avposition == 'right') {
?>
<div class="kunena-profile">
	<span class="view-username">
		<?php if ($userinfo->userid) { echo CKunenaLink::GetProfileLink ( $kunena_config, $this->kunena_message->userid, $msg_html->username );
			} else { echo $msg_html->username; } ?>
	</span>
		<?php if ($kunena_config->userlist_usertype)
			echo '<span class = "msgusertype">(' . $msg_html->usertype . ')</span>'; ?>
	<br />
		<?php if ($this->kunena_message->userid > 0) {
			echo CKunenaLink::GetProfileLink ( $kunena_config, $this->kunena_message->userid, $msg_html->avatar );
			} else { echo $msg_html->avatar; } ?>
	<div class="viewcover">
		<?php if (isset ( $msg_html->userrank )) { echo $msg_html->userrank; } ?>
	</div>
	<div class="viewcover">
		<?php if (isset ( $msg_html->userrankimg )) { echo $msg_html->userrankimg; } ?>
	</div>
	<div class="onlineimg">
		<?php if (isset ( $msg_html->posts )) { echo $msg_html->posts; }
			if (isset ( $msg_html->points )) { echo $msg_html->points; }
			if (isset ( $msg_html->online )) { echo $msg_html->online; } ?>
	</div>
		<?php if (isset ( $msg_html->pms )) { echo $msg_html->pms; } ?>
	<div class="smallicons">
		<?php 	if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'profile' . DS . 'socialbuttons.php')) {
		include (KUNENA_ABSTMPLTPATH . DS . 'profile' . DS . 'socialbuttons.php');
	} else {
		include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'profile' . DS . 'socialbuttons.php');
	} ?>
	<div class="viewcover">
		<?php if (isset ( $msg_html->personal )) { echo $msg_html->personal; } ?>
	</div>
	</div>
</div>

<?php } else { ?>

<div class="kunena-topbottom-avatar">
	<?php if ($this->kunena_message->userid > 0) {
	echo CKunenaLink::GetProfileLink ( $kunena_config, $this->kunena_message->userid, $msg_html->avatar );
	} else { echo $msg_html->avatar; } ?>
</div>
<div class="kunena-profile-mid">
	<?php if ($userinfo->userid) {
	echo CKunenaLink::GetProfileLink ( $kunena_config, $this->kunena_message->userid, $msg_html->username );
	} else { echo $msg_html->username; } ?>
	<?php if ($kunena_config->userlist_usertype)
	echo '<span class = "msgusertype">(' . $msg_html->usertype . ')</span>'; ?>
	<br />
	<?php if (isset ( $msg_html->userrank )) {
	echo $msg_html->userrank; } ?>
	<br />
	<?php if (isset ( $msg_html->userrankimg )) {
	echo $msg_html->userrankimg; } ?>
</div>
<div class="kunena-personal">
	<?php if (isset ( $msg_html->personal )) { 
	echo $msg_html->personal; } ?>
</div>
<div class="kunena-profile-right">
	<?php if (isset ( $msg_html->posts )) { echo $msg_html->posts; }
		if (isset ( $msg_html->points )) { echo $msg_html->points; } ?>
	<div class="viewcover"><div class="onlineimg">
		<?php if (isset ( $msg_html->online )) { echo $msg_html->online; } ?></div><?php
		 if (isset ( $msg_html->pms )) { echo $msg_html->pms; } ?>
	</div>
</div>
<div class="kunena-profile-right">
	<div>
		<?php 	if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'profile' . DS . 'socialbuttons.php')) {
		include (KUNENA_ABSTMPLTPATH . DS . 'profile' . DS . 'socialbuttons.php');
	} else {
		include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'profile' . DS . 'socialbuttons.php');
	} ?>
	</div>
</div>

<?php } ?>