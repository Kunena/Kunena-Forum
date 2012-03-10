<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage User
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule">
	<div class="box-wrapper">
		<div id="kprofile-tabs" class="kbox">
			<dl class="ktabs">
				<?php if ($this->me->isModerator()): ?>
				<dt class="closed" title="<?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION') ?>"><a class="link-tab" href="#tab-administrator"><?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayUnapprovedPosts(); ?>
				</dd>
				<?php endif; ?>
				<dt class="open" title="<?php echo JText::_('COM_KUNENA_USERPOSTS') ?>"><a class="link-tab" href="#tab-userposts"><?php echo JText::_('COM_KUNENA_USERPOSTS') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayUserPosts(); ?>
				</dd>
				<?php if($this->config->showthankyou && $this->me->userid != 0) : ?>
				<dt class="closed" title="<?php echo JText::_('COM_KUNENA_THANK_YOU') ?>"><a class="link-tab" href="#tab-thank_you"><?php echo JText::_('COM_KUNENA_THANK_YOU') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayGotThankyou(); ?>
					<?php $this->displaySaidThankyou(); ?>
				</dd>
				<?php endif; ?>
				<?php if ($this->me->userid == $this->user->id): ?>
				<?php if ($this->config->allowsubscriptions) :?>
				<dt class="closed" title="<?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?>"><a class="link-tab" href="#tab-subscriptions"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?></a></dt>
				<dd style="display: none;">
					<?php //$this->displayCategoriesSubscriptions(); ?>
					<?php $this->displaySubscriptions(); ?>
				</dd>
				<?php endif; ?>
				<?php if ($this->config->allowfavorites) : ?>
				<dt class="closed" title="<?php echo JText::_('COM_KUNENA_FAVORITES') ?>"><a class="link-tab" href="#tab-favorites"><?php echo JText::_('COM_KUNENA_FAVORITES') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayFavorites(); ?>
				</dd>
				<?php endif; ?>
				<?php endif;?>
				<?php if ($this->me->isModerator() && $this->me->userid == $this->profile->userid ): ?>
				<dt class="closed" title="<?php echo JText::_('COM_KUNENA_BAN_BANMANAGER') ?>"><a class="link-tab" href="#tab-banmanager"><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayBanManager(); ?>
				</dd>
				<?php endif;?>
				<?php if ($this->me->isModerator() && $this->me->userid != $this->user->id):?>
				<dt class="closed" title="<?php echo JText::_('COM_KUNENA_BAN_BANHISTORY') ?>"><a class="link-tab" href="#tab-banhistory"><?php echo JText::_('COM_KUNENA_BAN_BANHISTORY') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayBanHistory(); ?>
				</dd>
				<?php endif;?>
				<?php if ($this->me->isModerator() || $this->me->userid == $this->profile->userid ): ?>
				<dt class="closed" title="<?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?>"><a class="link-tab" href="#tab-attachments"><?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayAttachments(); ?>
				</dd>
				<?php endif;?>
				<?php if ($this->canBan) : ?>
				<dt class="closed" title="<?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ) ?>"><a class="link-tab" href="#tab-ban_edit"><?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ) ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayBanUser(); ?>
				</dd>
				<?php endif; ?>
			</dl>
		</div>
	</div>
</div>