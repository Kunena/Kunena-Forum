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
<li class="topic-row">
	<dl class="list-unstyled">
		<dd class="kbox-border kbox-border_radius kbox-shadow">
			<ul class="list-unstyled message-list">
				<li class="header">
					<a id="<?php echo $this->displayMessageField('id') ?>"></a>
					<dl class="list-unstyled">
						<dd class="kposthead-replytitle"><h3>RE: <?php echo $this->displayMessageField('subject') ?></h3></dd>
						<dd class="kposthead-postid" ><?php echo $this->numLink ?></dd>
						<?php if (!empty($this->ipLink)) : ?><dd class="kposthead-postip"><?php echo $this->ipLink ?></dd><?php endif ?>
						<dd class="kposthead-posttime">Posted [K=DATE:<?php echo $this->message->time ?>]</dd>
					</dl>
				</li>
			</ul>
			[K=MESSAGE_PROFILE]
			<div class="kpost-container kbox-hover">
				<ul class="list-unstyled kpost-post-body">
					<li class="kpost-body">
						<?php echo $this->displayMessageField('message') ?>
					</li>
					<li class="clr"></li>
					<?php if (!empty($this->attachments)) : ?>
						<li class="kpost-body-attach">
							<span class="kattach-title"><?php echo JText::_('COM_KUNENA_ATTACHMENTS') ?></span>
							<ul class="list-unstyled">
								<?php foreach($this->attachments as $attachment) : ?>
								<li class="kattach-details">
									<?php echo $attachment->getThumbnailLink(); ?> <span><?php echo $attachment->getTextLink(); ?></span>
								</li>
								<?php endforeach; ?>
							</ul>
							<div class="clr"></div>
						</li>
						<?php endif; ?>

						<?php $this->displayTemplateFile('topic', 'default', 'quickreply') ?>

						<?php if ($this->config->editmarkup && $this->message->modified_by) : ?>
							<li class="kpost-body-lastedit">
								<?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ": [K=DATE:{$this->message->modified_time}] " . JText::_('COM_KUNENA_BY') . ' ' . $this->message->getModifier()->getLink() . '.'; ?>
							</li>
						<?php if ($this->message->modified_reason) : ?>
							<li class="kpost-body-editreason">
								<?php echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape ( $this->message->modified_reason ); ?>
							</li>
						<?php endif ?>
					<?php endif ?>
					<?php if (!empty($this->signatureHtml)) : ?>
						<li class="kpost-body-sig"><?php echo $this->signatureHtml ?></li>
					<?php endif ?>
				</ul>
				<?php if(!empty($this->thankyou)): ?>
				<div class="buttonbar">
					<ul class="list-unstyled buttons-message innerblock">
					<?php
					echo JText::_('COM_KUNENA_THANKYOU').': ';
					echo implode(', ', $this->thankyou);
					if (count($this->thankyou) > 9) echo '...';
					?>
					</ul>
				</div>
				<?php endif ?>
				[K=MESSAGE_ACTIONS]
			</div>
			<div class="clr"></div>
		</dd>
	</dl>
</li>
<?php if ($this->isModulePosition('kunena_msg_' . $this->mmm)) : ?><li class="kmodules"><?php $this->getModulePosition('kunena_msg_' . $this->mmm) ?></li><?php endif ?>