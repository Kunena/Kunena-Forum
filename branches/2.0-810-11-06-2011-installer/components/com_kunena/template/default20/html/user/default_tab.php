<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
				<div id="kprofile-tabs">
					<dl class="tabs">
						<?php if ($this->me->isModerator()): ?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION') ?>"><?php echo JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION') ?></dt>
						<dd style="display: none;">
							<?php $this->displayUnapprovedPosts(); ?>
						</dd>
						<?php endif; ?>
						<dt class="open" title="<?php echo JText::_('COM_KUNENA_USERPOSTS') ?>"><?php echo JText::_('COM_KUNENA_USERPOSTS') ?></dt>
						<dd style="display: none;">
							<?php $this->displayUserPosts(); ?>
						</dd>
						<?php if($this->config->showthankyou && $this->my->id != 0) : ?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_THANK_YOU') ?>"><?php echo JText::_('COM_KUNENA_THANK_YOU') ?></dt>
						<dd style="display: none;">
							<?php $this->displayGotThankYou(); ?>
							<?php $this->displaySaidThankYou(); ?>
						</dd>
						<?php endif; ?>
						<?php if ($this->my->id == $this->user->id): ?>
						<?php if ($this->config->allowsubscriptions) :?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?>"><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?></dt>
						<dd style="display: none;">
							<?php //$this->displayCategoriesSubscriptions(); ?>
							<?php $this->displaySubscriptions(); ?>
						</dd>
						<?php endif; ?>
						<?php if ($this->config->allowfavorites) : ?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_FAVORITES') ?>"><?php echo JText::_('COM_KUNENA_FAVORITES') ?></dt>
						<dd style="display: none;">
							<?php $this->displayFavorites(); ?>
						</dd>
						<?php endif; ?>
						<?php endif;?>
						<?php if ($this->me->isModerator() && $this->my->id == $this->profile->userid ): ?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_BAN_BANMANAGER') ?>"><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER') ?></dt>
						<dd style="display: none;">
							<?php $this->displayBanManager(); ?>
						</dd>
						<?php endif;?>
						<?php if ($this->me->isModerator() && $this->my->id != $this->user->id):?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_BAN_BANHISTORY') ?>"><?php echo JText::_('COM_KUNENA_BAN_BANHISTORY') ?></dt>
						<dd style="display: none;">
							<?php $this->displayBanHistory(); ?>
						</dd>
						<?php endif;?>
						<?php if ($this->me->isModerator() || $this->my->id == $this->profile->userid ): ?>
						<dt class="closed" title="<?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?>"><?php echo JText::_('COM_KUNENA_MANAGE_ATTACHMENTS'); ?></dt>
						<dd style="display: none;">
							<?php $this->displayAttachments(); ?>
						</dd>
						<?php endif;?>
						<?php if ($this->canBan) : ?>
						<dt class="closed" title="<?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ) ?>"><?php echo $this->banInfo->id ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW' ) ?></dt>
						<dd style="display: none;">
							<?php $this->displayBanUser(); ?>
						</dd>
						<?php endif; ?>
					</dl>
				</div>