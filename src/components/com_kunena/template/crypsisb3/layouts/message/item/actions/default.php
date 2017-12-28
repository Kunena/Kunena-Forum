<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Message
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$config = KunenaConfig::getInstance();
$this->ktemplate = KunenaFactory::getTemplate();
$fullactions = $this->ktemplate->params->get('fullactions');
$quick = $this->ktemplate->params->get('quick');
?>

<?php if (!$fullactions) : ?>
	<?php if (empty($this->message_closed)) : ?>
		<div class="kmessagepadding">
			<?php if ($this->quickreply && $quick == 0) : ?>
				<a id="btn_qreply" data-related="kreply<?php echo $this->message->displayField('id'); ?>_form" role="button" class="btn btn-default Kreplyclick"
					  rel="nofollow">
						<?php echo KunenaIcons::undo() . ' ' . JText::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_QUICK_REPLY'); ?>
				</a>
			<?php endif; ?>
			<?php if ($this->quickreply && $quick == 1) : ?>
				<a href="#kreply<?php echo $this->message->displayField('id'); ?>_form" role="button" class="btn btn-default openmodal"
					data-toggle="modal" data-target="#kreply<?php echo $this->message->displayField('id'); ?>_form" rel="nofollow">
					<?php echo KunenaIcons::undo() . ' ' . JText::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_QUICK_REPLY'); ?>
				</a>
			<?php endif; ?>

			<?php echo $this->messageButtons->get('reply'); ?>
			<?php echo $this->messageButtons->get('quote'); ?>
			<?php echo $this->messageButtons->get('edit'); ?>
			<?php if ($this->message->isAuthorised('delete')) : ?>
				<?php echo $this->messageButtons->get('delete'); ?>
			<?php endif; ?>
			<?php echo $this->messageButtons->get('thankyou'); ?>
			<?php echo $this->messageButtons->get('unthankyou'); ?>

			<?php if ($this->messageButtons->get('moderate')) : ?>
				<br />
				<br />
				<?php echo $this->messageButtons->get('moderate'); ?>
				<?php echo $this->messageButtons->get('undelete'); ?>
				<?php echo $this->messageButtons->get('permdelete'); ?>
				<?php echo $this->messageButtons->get('publish'); ?>
				<?php echo $this->messageButtons->get('spam'); ?>
			<?php endif; ?>
		</div>

	<?php else : ?>

		<div class="kreplymessage">
			<?php echo $this->message_closed; ?>
		</div>
	<?php endif;
endif; ?>

<?php if ($fullactions) : ?>

	<?php if (empty($this->message_closed)) : ?>
		<div class="btn-toolbar btn-marging kmessagepadding">
			<?php if ($this->quickreply && $quick == 0) : ?>
				<a id="btn_qreply" data-related="kreply<?php echo $this->message->displayField('id'); ?>_form" role="button" class="btn btn-default Kreplyclick"
					rel="nofollow">
					<?php echo KunenaIcons::undo() . ' ' . JText::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_QUICK_REPLY'); ?>
				</a>
			<?php endif; ?>
			<?php if ($this->quickreply && $quick == 1) : ?>
				<a href="#kreply<?php echo $this->message->displayField('id'); ?>_form" role="button" class="btn btn-default openmodal"
					data-toggle="modal" data-target="#kreply<?php echo $this->message->displayField('id'); ?>_form" rel="nofollow">
					<?php echo KunenaIcons::undo() . ' ' . JText::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_QUICK_REPLY'); ?>
				</a>
			<?php endif; ?>
			<div class="btn-group">
				<a class="btn btn-default" data-toggle="dropdown">
					<?php echo KunenaIcons::edit() . ' ' . JText::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_ACTION'); ?>
				</a>
				<a class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
					<li><?php echo $this->messageButtons->get('reply'); ?></li>
					<li><?php echo $this->messageButtons->get('quote'); ?></li>
					<li><?php echo $this->messageButtons->get('edit'); ?></li>
					<?php if ($config->userdeletetmessage > 0) : ?>
						<li><?php echo $this->messageButtons->get('delete'); ?></li>
					<?php endif; ?>
				</ul>
			</div>

			<?php if ($this->messageButtons->get('moderate')) : ?>
				<div class="btn-group">
					<a class="btn btn-default" data-toggle="dropdown">
						<?php echo KunenaIcons::shuffle() . ' ' . JText::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_MODERATE'); ?>
					</a>
					<a class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><?php echo $this->messageButtons->get('moderate'); ?></li>
						<li><?php echo $this->messageButtons->get('delete'); ?></li>
						<li><?php echo $this->messageButtons->get('undelete'); ?></li>
						<li><?php echo $this->messageButtons->get('permdelete'); ?></li>
						<li><?php echo $this->messageButtons->get('publish'); ?></li>
						<li><?php echo $this->messageButtons->get('spam'); ?></li>
					</ul>
				</div>
			<?php endif; ?>

			<?php echo $this->messageButtons->get('thankyou'); ?>
			<?php echo $this->messageButtons->get('unthankyou'); ?>
		</div>

	<?php else : ?>

		<div class="kreplymessage">
			<?php echo $this->message_closed; ?>
		</div>
	<?php endif;
endif; ?>
