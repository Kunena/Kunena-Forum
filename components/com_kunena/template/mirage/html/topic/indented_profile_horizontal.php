<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div class="fl innerspacer">
	<?php $avatar = $this->profile->getAvatarImage ('kavatar kavatar-128', 'post'); if ($avatar) : ?>
		<?php echo $this->profile->getLink( $avatar ); ?>
	<?php endif; ?>
	<ul class="list-unstyled kcontent-128">
		<li class="kpost-username">
			<?php echo $this->profile->getLink() ?>
		</li>
		<?php if (!empty($this->usertype)) : ?>
			<li class="kpost-user-type">
				<span class="kmsgusertype">( <?php echo JText::_($this->usertype) ?> )</span>
			</li>
		<?php endif ?>
		<?php if ($this->profile->exists()): ?>
			<?php if (!empty($this->userranktitle)) : ?>
				<li class="kpost-user-rank">
					<?php echo $this->escape($this->userranktitle) ?>
				</li>
			<?php endif ?>
			<?php if (!empty($this->userrankimage)) : ?>
				<li class="kpost-user-rank-img">
					<?php echo $this->userrankimage ?>
				</li>
			<?php endif ?>
			<li><span class="kicon-button kbuttononline-<?php echo $this->profile->isOnline('yes', 'no') ?>"><span class="online-<?php echo $this->profile->isOnline('yes', 'no') ?>"><span><?php echo $this->profile->isOnline() ? JText::_('COM_KUNENA_ONLINE') : JText::_('COM_KUNENA_OFFLINE'); ?></span></span></span></li>
		<?php endif ?>
	</ul>
</div>
<div class="fr innerspacer">
	<ul class="list-unstyled">
		<?php if ($this->profile->exists()): ?>
			<li class="kpost-user-icons">
				<?php echo $this->profile->profileIcon('gender'); ?>
				<?php echo $this->profile->profileIcon('birthdate'); ?>
				<?php echo $this->profile->profileIcon('location'); ?>
				<?php echo $this->profile->profileIcon('website'); ?>
				<?php echo $this->profile->profileIcon('private'); ?>
				<?php echo $this->profile->profileIcon('email'); ?>
				<br />
				<div>
				<?php if ($this->userposts) : ?>
					<span class="kpost-user-posts"><?php echo JText::_('COM_KUNENA_POSTS') . intval($this->userposts); ?></span>
				<?php endif ?>
				<?php if ($this->userthankyou) : ?>
					<span class="kpost-user-thankyou"><?php echo JText::_('COM_KUNENA_MYPROFILE_THANKYOU_RECEIVED') .' '. intval($this->userthankyou); ?></span>
				<?php endif ?>
				<?php if ($this->userpoints) : ?>
					<span class="kpost-user-points"><?php echo JText::_('COM_KUNENA_AUP_POINTS') . intval($this->userpoints); ?></span>
				<?php endif ?>
				</div>
			</li>
			<?php if (!empty($this->personaltext)) : ?>
				<li class="kpost-personal">
					<?php echo $this->personaltext ?>
				</li>
			<?php endif ?>
		<?php endif; ?>
		<?php if ($this->profile->exists()): ?>
			<?php if ( !empty($this->userkarma) ) : ?>
				<li class="kpost-user-karma">
					<span class="kmsgkarma">
						<?php echo $this->userkarma ?>
					</span>
				</li>
			<?php endif ?>
			<?php if ( !empty($this->usermedals) ) : ?>
				<li class="kpost-user-medals">
				<?php foreach ( $this->usermedals as $medal ) : ?>
					<?php echo $medal; ?>
				<?php endforeach ?>
				</li>
			<?php endif ?>
		<?php endif; ?>
	</ul>
</div>