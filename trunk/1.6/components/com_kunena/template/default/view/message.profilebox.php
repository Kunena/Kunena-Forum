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
$kunena_config = & CKunenaConfig::getInstance ();

if ($kunena_config->avposition == 'left' || $kunena_config->avposition == 'right') :
?>
	<ul id="kpost-profile">
		<li class="kpost-username">
			<?php if ($this->profile->userid) { echo CKunenaLink::GetProfileLink ( $kunena_config, $this->kunena_message->userid, $this->msg_html->username );
				} else { echo $this->msg_html->username; } ?>
		</li>
		<li class="kpost-usertype">
			<?php if ($kunena_config->userlist_usertype)
				echo '<span class = "msgusertype">(' . $this->msg_html->usertype . ')</span>'; ?>
		</li>
		<li class="kpost-avatar">
		<?php if ($this->profile->userid > 0) {
			echo CKunenaLink::GetProfileLink ( $kunena_config, $this->kunena_message->userid, $this->msg_html->avatar );
			} else { echo $this->msg_html->avatar; } ?>
		</li>
		<?php if ($this->profile->userid): ?>
		<li class="kpost-userrank">
			<?php if (isset ( $this->msg_html->userrank )) { echo $this->msg_html->userrank; } ?>
		</li>
		<li class="kpost-userrank-img">
			<?php if (isset ( $this->msg_html->userrankimg )) { echo $this->msg_html->userrankimg; } ?>
		</li>

		<!--  The markup needs to be removed from these tags and each enclosed as a list item. -->

		<?php
			if (isset ( $this->msg_html->posts )) { ?>
		<li class="kpost-userposts"><?php echo $this->msg_html->posts; ?></li>
		<?php   }
			if (isset ( $this->msg_html->points )) { ?>
		<li class="kpost-userposts"><?php echo $this->msg_html->points; ?></li>
		<?php }	 ?>

		<li class="kpost-online-status-<?php echo $this->msg_html->online ? 'yes':'no'; ?>"> </li>
		<li class="kpost-smallicons">
			<div class="iconrow">
				<?php echo $this->profile->profileIcon('gender'); ?>
				<?php echo $this->profile->profileIcon('birthdate'); ?>
				<?php echo $this->profile->profileIcon('location'); ?>
				<?php echo $this->profile->profileIcon('website');
				if (isset ( $this->msg_html->pms )) {
			 		echo $this->msg_html->pms;
				}?>
			</div>
			<?php
			CKunenaTools::loadTemplate('/profile/socialbuttons.php');
			?>
		</li>
		<li class="kpost-personal">
			<?php if (isset ( $this->msg_html->personal )) { echo $this->msg_html->personal; } ?>
		</li>
		<?php endif; ?>
</ul>
<?php else : ?>
	<ul id="kpost-profiletop">
		<?php if ($this->profile->userid): ?>
		<li class="kpost-smallicons">
			<div class="iconrow">
			<?php echo $this->profile->profileIcon('gender'); ?>
			<?php echo $this->profile->profileIcon('birthdate'); ?>
			<?php echo $this->profile->profileIcon('location'); ?>
			<?php echo $this->profile->profileIcon('website');
			if (isset ( $this->msg_html->pms )) {
				echo $this->msg_html->pms;
			} ?>
			</div><br />
			<?php
			CKunenaTools::loadTemplate('/profile/socialbuttons.php');
			?>
		</li>
		<li class="kpost-personal">
			<?php if (isset ( $this->msg_html->personal )) { echo $this->msg_html->personal; } ?>
		</li>
		<?php endif; ?>
		<li class="kpost-avatar">
		<?php if ($this->profile->userid > 0) {
			echo CKunenaLink::GetProfileLink ( $kunena_config, $this->kunena_message->userid, $this->msg_html->avatar );
			} else { echo $this->msg_html->avatar; } ?>
		</li>
		<li class="kpost-username">
			<?php if ($this->profile->userid) { echo CKunenaLink::GetProfileLink ( $kunena_config, $this->kunena_message->userid, $this->msg_html->username );
				} else { echo $this->msg_html->username; } ?>
		</li>
		<li class="kpost-usertype">
			<?php if ($kunena_config->userlist_usertype)
				echo '<span class = "msgusertype">(' . $this->msg_html->usertype . ')</span>'; ?>
		</li>

		<?php if ($this->profile->userid): ?>
		<li class="kpost-userrank">
			<?php if (isset ( $this->msg_html->userrank )) { echo $this->msg_html->userrank; } ?>
		</li>
		<li class="kpost-userrank-img">
			<?php if (isset ( $this->msg_html->userrankimg )) { echo $this->msg_html->userrankimg; } ?>
		</li>
		<li class="kpost-online-status-top-<?php echo $this->msg_html->online ? 'yes':'no'; ?>"> </li>

		<!--  The markup needs to be removed from these tags and each enclosed as a list item. -->

		<?php
			if (isset ( $this->msg_html->posts )) { ?>
		<li class="kpost-userposts"><?php echo $this->msg_html->posts; ?></li>
		<?php   }
			if (isset ( $this->msg_html->points )) { ?>
		<li class="kpost-userposts"><?php echo $this->msg_html->points; ?></li>
		<?php }	 ?>
		<?php endif; ?>
	</ul>
<?php endif; ?>

