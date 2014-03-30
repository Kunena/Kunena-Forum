<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
	<ul class="kpost-profile">
		<li class="kpost-username">
			<?php echo $this->profile->getLink() ?>
		</li>
		<?php if (!empty($this->usertype)) : ?>
		<li class="kpost-usertype">
			<span class = "kmsgusertype">( <?php echo JText::_($this->usertype) ?> )</span>
		</li>
		<?php endif ?>
		<?php $avatar = $this->profile->getAvatarImage ('kavatar', 'post'); if ($avatar) : ?>
		<li class="kpost-avatar">
			<span class="kavatar"><?php echo $this->profile->getLink( $avatar ); ?></span>
		</li>
		<?php endif; ?>

		<?php if ($this->profile->exists()): ?>

		<li>
			<span class="kicon-button kbuttononline-<?php echo $this->profile->isOnline('yes', 'no') ?>">
				<span class="online-<?php echo $this->profile->isOnline('yes', 'no') ?>">
					<span><?php echo $this->profile->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')); ?></span>
				</span>
			</span>
		</li>

		<?php if (!empty($this->userranktitle)) : ?>
		<li class="kpost-userrank">
			<?php echo $this->escape($this->userranktitle) ?>
		</li>
		<?php endif ?>
		<?php if (!empty($this->userrankimage)) : ?>
		<li class="kpost-userrank-img">
			<?php echo $this->userrankimage ?>
		</li>
		<?php endif ?>

		<?php if (!empty($this->personalText)) : ?>
		<li class="kpost-personal">
			<?php echo $this->personalText ?>
		</li>
		<?php endif ?>
		<?php if ($this->userposts) : ?>
		<li class="kpost-userposts"><?php echo JText::_('COM_KUNENA_POSTS') .' '. intval($this->userposts); ?></li>
		<?php endif ?>
		<?php if ($this->userthankyou) : ?>
			<li class="kpost-usertyr"><?php echo JText::_('COM_KUNENA_MYPROFILE_THANKYOU_RECEIVED') .' '. intval($this->userthankyou); ?></li>
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
		<?php if ( !empty($this->usermedals) ) : ?>
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
