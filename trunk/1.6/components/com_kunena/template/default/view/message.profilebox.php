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
if ($this->params->get('avatarPosition') == 'left' || $this->params->get('avatarPosition') == 'right') :
?>
	<ul id="kpost-profile">
		<li class="kpost-username">
			<?php echo CKunenaLink::GetProfileLink ( intval($this->profile->userid), $this->escape($this->username) ); ?>
		</li>
		<?php if ($this->usertype) : ?>
		<li class="kpost-usertype">
			<span class = "kmsgusertype">( <?php echo $this->escape($this->usertype) ?> )</span>
		</li>
		<?php endif ?>
		<li class="kpost-avatar">
		<?php echo CKunenaLink::GetProfileLink ( intval($this->profile->userid), $this->avatar ); ?>
		</li>
		<?php if ($this->profile->userid): ?>

		<?php if ($this->userranktitle) : ?>
		<li class="kpost-userrank">
			<?php echo $this->escape($this->userranktitle) ?>
		</li>
		<?php endif ?>
		<?php if ($this->userrankimage) : ?>
		<li class="kpost-userrank-img">
			<?php echo $this->userrankimage ?>
		</li>
		<?php endif ?>

		<?php if ($this->userposts) : ?>
		<li class="kpost-userposts"><?php echo JText::_('COM_KUNENA_POSTS') . intval($this->userposts); ?></li>
		<?php endif ?>
		<?php if ($this->userpoints) : ?>
		<li class="kpost-userposts"><?php echo JText::_('COM_KUNENA_AUP_POINTS') . intval($this->userpoints); ?></li>
		<?php endif ?>
		<?php if ( $this->aupmedals ) : ?>
		<li class="kiconrow">
			<?php foreach ( $this->aupmedals as $medal ) : ?>
			<?php echo '<img src="' . _AUP_MEDALS_LIVE_PATH.$this->escape($medal->icon). '" alt="'.$this->escape($medal->rank).'" title="'.$this->escape($medal->rank).'" />'; ?>
			<?php endforeach ?>
		</li>
		<?php endif ?>

		<li class="kpost-online-status-<?php echo $this->profile->isOnline(true); ?>"> </li>
		<li class="kpost-smallicons">
			<div class="kiconrow">
				<?php echo $this->profile->profileIcon('gender'); ?>
				<?php echo $this->profile->profileIcon('birthdate'); ?>
				<?php echo $this->profile->profileIcon('location'); ?>
				<?php echo $this->profile->profileIcon('website'); ?>
				<?php echo $this->profile->profileIcon('private'); ?>
				<?php echo $this->profile->profileIcon('email'); ?>
			</div>
			<?php CKunenaTools::loadTemplate('/profile/socialbuttons.php') ?>
		</li>
		<?php if ($this->personaltext) : ?>
		<li class="kpost-personal">
			<?php echo KunenaParser::parseText($this->personaltext) ?>
		</li>
		<?php endif ?>
		<?php endif ?>
</ul>
<?php else : ?>
	<ul id="kpost-profiletop">
		<?php if ($this->profile->userid): ?>
		<li class="kpost-smallicons">
			<div class="kiconrow">
			<?php echo $this->profile->profileIcon('gender'); ?>
			<?php echo $this->profile->profileIcon('birthdate'); ?>
			<?php echo $this->profile->profileIcon('location'); ?>
			<?php echo $this->profile->profileIcon('website'); ?>
			<?php echo $this->profile->profileIcon('private'); ?>
			<?php echo $this->profile->profileIcon('email'); ?>
			</div><br />
			<?php CKunenaTools::loadTemplate('/profile/socialbuttons.php') ?>
		</li>
		<?php if ($this->personaltext) : ?>
		<li class="kpost-personal">
			<?php echo KunenaParser::parseText($this->personaltext) ?>
		</li>
		<?php endif ?>
		<?php endif; ?>
		<li class="kpost-avatar">
		<?php echo CKunenaLink::GetProfileLink ( intval($this->profile->userid), $this->avatar ); ?>
		</li>
		<li class="kpost-username">
			<?php echo CKunenaLink::GetProfileLink ( intval($this->profile->userid), $this->escape($this->username) ); ?>
		</li>
		<?php if ($this->usertype) : ?>
		<li class="kpost-usertype">
			<span class = "kmsgusertype">( <?php echo $this->escape($this->usertype) ?> )</span>
		</li>
		<?php endif ?>

		<?php if ($this->profile->userid): ?>

		<?php if ($this->userranktitle) : ?>
		<li class="kpost-userrank">
			<?php echo $this->escape($this->userranktitle) ?>
		</li>
		<?php endif ?>
		<?php if ($this->userrankimage) : ?>
		<li class="kpost-userrank-img">
			<?php echo $this->userrankimage ?>
		</li>
		<?php endif ?>

		<li class="kpost-online-status-top-<?php echo $this->profile->isOnline(true); ?>"> </li>

		<?php if ($this->userposts) : ?>
		<li class="kpost-userposts"><?php echo JText::_('COM_KUNENA_POSTS') . intval($this->userposts); ?></li>
		<?php endif ?>
		<?php if ($this->userpoints) : ?>
		<li class="kpost-userposts"><?php echo JText::_('COM_KUNENA_AUP_POINTS') . intval($this->userpoints); ?></li>
		<?php endif ?>
		<?php if ( $this->aupmedals ) : ?>
			<li class="kpost-userposts">
			<?php foreach ( $this->aupmedals as $medal ) : ?>
			<?php echo '<img src="' . _AUP_MEDALS_LIVE_PATH.$this->escape($medal->icon). '" alt="'.$this->escape($medal->rank).'" title="'.$this->escape($medal->rank).'" />'; ?>
			<?php endforeach ?>
			</li>
		<?php endif ?>

		<?php endif; ?>
	</ul>
<?php endif; ?>

