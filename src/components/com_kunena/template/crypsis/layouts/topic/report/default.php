<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>

<h2>
	<?php echo JText::_('COM_KUNENA_REPORT_TO_MODERATOR'); ?>
</h2>

<form method="post" action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" class="form-horizontal">
	<input type="hidden" name="view" value="topic" />
	<input type="hidden" name="task" value="report" />
	<input type="hidden" name="catid" value="<?php echo (int) $this->category->id; ?>" />
	<input type="hidden" name="id" value="<?php echo (int) $this->topic->id; ?>" />

	<?php if ($this->message) : ?>
	<input type="hidden" name="mesid" value="<?php echo (int) $this->message->id; ?>" />
	<?php endif; ?>

	<?php echo JHtml::_('form.token'); ?>

	<div class="well well-small">
		<div class="control-group">
			<label class="control-label" for="kreport-reason">
				<?php echo JText::_('COM_KUNENA_REPORT_REASON'); ?>
			</label>
			<div class="controls">
				<input class="input-xxlarge" type="text" name="reason" size="30" id="kreport-reason"/>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="kreport-msg">
				<?php echo JText::_('COM_KUNENA_REPORT_MESSAGE'); ?>
			</label>
			<div class="controls">
				<textarea class="input-xxlarge" id="kreport-msg" name="text" cols="40" rows="10"></textarea>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input class="btn btn-primary" type="submit" name="Submit"
				       value="<?php echo JText::_('COM_KUNENA_REPORT_SEND'); ?>"/>
				<button class="btn" data-dismiss="modal" aria-hidden="true">
						<?php echo JText::_('COM_KUNENA_REPORT_CLOSEMODAL_LABEL'); ?></button>
			</div>
		</div>
	</div>
</form>

