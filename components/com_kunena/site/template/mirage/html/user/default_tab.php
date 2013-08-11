<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule">
	<div class="kbox-wrapper box-full">
		<div id="kprofile-tabs" class="kbox tabbable">
			<dl class="list-unstyled ktabs">
				<?php if ($this->showUserPosts) : ?>
				<dt class="open"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_USERPOSTS') ?>" href="#tab-userposts"><?php echo JText::_('COM_KUNENA_USERPOSTS') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayUserPosts() ?>
				</dd>
				<?php endif ?>
				<?php if ($this->showSubscriptions) : ?>
				<dt class="closed"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?>" href="#tab-subscriptions"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayCategoriesSubscriptions() ?>
					<?php $this->displaySubscriptions() ?>
				</dd>
				<?php endif ?>
				<?php if ($this->showFavorites) : ?>
				<dt class="closed"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_FAVORITES') ?>" href="#tab-favorites"><?php echo JText::_('COM_KUNENA_FAVORITES') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayFavorites() ?>
				</dd>
				<?php endif ?>
				<?php if($this->showThankyou) : ?>
				<dt class="closed"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_THANK_YOU') ?>" href="#tab-thank_you"><?php echo JText::_('COM_KUNENA_THANK_YOU') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayGotThankyou() ?>
					<?php $this->displaySaidThankyou() ?>
				</dd>
				<?php endif ?>
				<?php if ($this->showUnapprovedPosts) : ?>
				<dt class="closed"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION') ?>" href="#tab-administrator"><?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayUnapprovedPosts() ?>
				</dd>
				<?php endif ?>
				<?php if ($this->showAttachments): ?>
				<dt class="closed"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?>" href="#tab-attachments"><?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayAttachments() ?>
				</dd>
				<?php endif;?>
				<?php if ($this->showBanManager): ?>
				<dt class="closed"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_BAN_BANMANAGER') ?>" href="#tab-banmanager"><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayBanManager() ?>
				</dd>
				<?php endif;?>
				<?php if ($this->showBanHistory):?>
				<dt class="closed"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_BAN_BANHISTORY') ?>" href="#tab-banhistory"><?php echo JText::_('COM_KUNENA_BAN_BANHISTORY') ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayBanHistory() ?>
				</dd>
				<?php endif;?>
				<?php if ($this->showBanUser) : ?>
				<dt class="closed"><a class="link-tab" title="<?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ) ?>" href="#tab-ban_edit"><?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ) ?></a></dt>
				<dd style="display: none;">
					<?php $this->displayBanUser() ?>
				</dd>
				<?php endif; ?>
			</dl>
		</div>
	</div>
</div>
