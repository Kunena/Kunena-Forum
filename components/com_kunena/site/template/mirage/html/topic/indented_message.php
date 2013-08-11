<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<li class="kpost-body">
	<?php echo $this->displayMessageField('message') ?>
</li>
<li class="kclear"></li>
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
