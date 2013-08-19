<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php if (empty($this->message_closed)) : ?>

<div class="btn-toolbar btn-marging kmessagepadding">
	<a href="#kreply<?php echo $this->message->displayField('id') ?>_form" role="button" class="btn" data-toggle="modal"><?php echo JText::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_QUICK_REPLY') ?></a>
	<div class="btn-group">
		<button class="btn" data-toggle="dropdown"><?php echo JText::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_ACTION') ?></button>
		<button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
		<ul class="dropdown-menu">
			<li><?php echo $this->messageButtons->get('reply'); ?></li>
			<li><?php echo $this->messageButtons->get('quote'); ?></li>
			<li><?php echo $this->messageButtons->get('edit'); ?></li>
		</ul>
	</div>
	<?php if ($this->messageButtons->get('moderate')) : ?>
	<div class="btn-group">
		<button class="btn" data-toggle="dropdown"><?php echo JText::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_MODERATE') ?></button>
		<button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
		<ul class="dropdown-menu">
			<li><?php echo $this->messageButtons->get('moderate'); ?></li>
			<li><?php echo $this->messageButtons->get('delete'); ?></li>
			<li><?php echo $this->messageButtons->get('undelete'); ?></li>
			<li><?php echo $this->messageButtons->get('permdelete'); ?></li>
			<li><?php echo $this->messageButtons->get('publish'); ?></li>
			<li><?php echo $this->messageButtons->get('spam'); ?></li>
		</ul>
	</div>
	<?php endif?>
	<?php echo $this->messageButtons->get('thankyou'); ?>
</div>

<?php else : ?>
<div>
	<?php echo $this->message_closed; ?>
</div>
<?php endif ?>

