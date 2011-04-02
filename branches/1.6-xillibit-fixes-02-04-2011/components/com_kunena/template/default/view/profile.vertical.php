<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

if (!isset($this->showUnusedSocial)) $this->showUnusedSocial = false;
?>
	<ul class="kpost-profile">
		<li class="kpost-username">
			<?php echo CKunenaLink::GetProfileLink ( intval($this->profile->userid), $this->escape($this->username) ); ?>
		</li>
		<?php if ($this->usertype) : ?>
		<li class="kpost-usertype">
			<span class = "kmsgusertype">( <?php echo $this->escape($this->usertype) ?> )</span>
		</li>
		<?php endif ?>
		<?php if ($this->avatar) : ?>
		<li class="kpost-avatar">
		<?php echo CKunenaLink::GetProfileLink ( intval($this->profile->userid), $this->avatar ); ?>
		</li>
		<?php endif; ?>
		<?php if ($this->profile->userid): ?>

		<li><span class="kicon-button kbuttononline-<?php echo $this->profile->isOnline(true) ?>"><span class="online-<?php echo $this->profile->isOnline(true) ?>"><span><?php echo $this->profile->isOnline() ? JText::_('COM_KUNENA_ONLINE') : JText::_('COM_KUNENA_OFFLINE'); ?></span></span></span></li>

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

		<?php if ($this->personaltext) : ?>
		<li class="kpost-personal">
			<?php echo KunenaParser::parseText($this->personaltext) ?>
		</li>
		<?php endif ?>
		<?php if ($this->userposts) : ?>
		<li class="kpost-userposts"><?php echo JText::_('COM_KUNENA_POSTS') .' '. intval($this->userposts); ?></li>
		<?php endif ?>
		<?php if ($this->userpoints) : ?>
		<li class="kpost-userposts"><?php echo JText::_('COM_KUNENA_AUP_POINTS') .' '. intval($this->userpoints); ?></li>
		<?php endif ?>
		<?php if ( $this->userkarma ) : ?>
		<li class="kpost-karma">
			<span class="kmsgkarma">
				<?php echo $this->userkarma ?>
			</span>
		</li>
		<?php endif ?>
		<?php if ( $this->usermedals ) : ?>
			<li class="kpost-usermedals">
			<?php foreach ( $this->usermedals as $medal ) : ?>
				<?php echo $medal; ?>
			<?php endforeach ?>
			</li>
		<?php endif ?>

		<li class="kpost-smallicons">
			<?php echo $this->profile->profileIcon('gender'); ?>
			<?php echo $this->profile->profileIcon('birthdate'); ?>
			<?php echo $this->profile->profileIcon('location'); ?>
			<?php echo $this->profile->profileIcon('website'); ?>
			<?php echo $this->profile->profileIcon('private'); ?>
			<?php echo $this->profile->profileIcon('email'); ?>
		</li>
		<?php endif ?>
</ul>