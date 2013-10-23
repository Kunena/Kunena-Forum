<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$avatar = $this->profile->getAvatarImage ('kavatar', 'post');
?>
<ul>
	<li>
		<?php echo $this->profile->getLink() ?>
	</li>

	<?php if (!empty($this->usertype)) : ?>
	<li>
		( <?php echo JText::_($this->usertype) ?> )
	</li>
	<?php endif; ?>

	<?php if ($avatar) : ?>
	<li>
		<?php echo $this->profile->getLink($avatar); ?>
	</li>
	<?php endif; ?>

	<?php if ($this->profile->exists()): ?>

	<li>
		<span class="kicon-button kbuttononline-<?php echo $this->profile->isOnline('yes', 'no') ?>">
			<span class="online-<?php echo $this->profile->isOnline('yes', 'no') ?>">
				<span>
					<?php echo $this->profile->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')); ?>
				</span>
			</span>
		</span>
	</li>

	<?php if (!empty($this->userranktitle)) : ?>
	<li>
		<?php echo $this->escape($this->userranktitle) ?>
	</li>
	<?php endif; ?>

	<?php if (!empty($this->userrankimage)) : ?>
	<li>
		<?php echo $this->userrankimage ?>
	</li>
	<?php endif; ?>

	<?php if (!empty($this->personalText)) : ?>
	<li>
		<?php echo $this->personalText ?>
	</li>
	<?php endif; ?>

	<?php if ($this->userposts) : ?>
	<li>
		<?php echo JText::_('COM_KUNENA_POSTS') .' '. intval($this->userposts); ?>
	</li>
	<?php endif; ?>

	<?php if ($this->userthankyou) : ?>
	<li>
		<?php echo JText::_('COM_KUNENA_MYPROFILE_THANKYOU_RECEIVED') .' '. intval($this->userthankyou); ?>
	</li>
	<?php endif; ?>

	<?php if ($this->userpoints) : ?>
	<li>
		<?php echo JText::_('COM_KUNENA_AUP_POINTS') .' '. intval($this->userpoints); ?>
	</li>
	<?php endif; ?>

	<?php if ($this->userkarma) : ?>
	<li>
		<?php echo $this->userkarma ?>
	</li>
	<?php endif; ?>

	<?php if (!empty($this->usermedals)) : ?>
	<li>
		<?php foreach ($this->usermedals as $medal) : ?>
		<?php echo $medal; ?>
		<?php endforeach; ?>
	</li>
	<?php endif; ?>

	<li>
		<?php echo $this->profile->profileIcon('gender'); ?>
		<?php echo $this->profile->profileIcon('birthdate'); ?>
		<?php echo $this->profile->profileIcon('location'); ?>
		<?php echo $this->profile->profileIcon('website'); ?>
		<?php echo $this->profile->profileIcon('private'); ?>
		<?php echo $this->profile->profileIcon('email'); ?>
	</li>

	<?php endif ?>
</ul>
