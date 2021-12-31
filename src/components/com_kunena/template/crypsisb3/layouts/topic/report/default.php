<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>

<h4>
	<?php echo Text::_('COM_KUNENA_REPORT_TO_MODERATOR'); ?>
</h4>

<form method="post" action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" class="form-horizontal">
	<input type="hidden" name="view" value="topic"/>
	<input type="hidden" name="task" value="report"/>
	<input type="hidden" name="catid" value="<?php echo (int) $this->category->id; ?>"/>
	<input type="hidden" name="id" value="<?php echo (int) $this->topic->id; ?>"/>

	<?php if ($this->message)
		:
		?>
		<input type="hidden" name="mesid" value="<?php echo (int) $this->message->id; ?>"/>
	<?php endif; ?>

	<?php echo HTMLHelper::_('form.token'); ?>

	<div class="well well-small">
		<div class="control-group">
			<label class="control-label" for="kreport-reason">
				<?php echo Text::_('COM_KUNENA_REPORT_REASON'); ?>
			</label>
			<div class="controls">
				<input class="input-xxlarge form-control" type="text" name="reason" size="30" id="kreport-reason"/>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="kreport-msg">
				<?php echo Text::_('COM_KUNENA_REPORT_MESSAGE'); ?>
			</label>
			<div class="controls">
				<textarea class="input-xxlarge form-control" id="kreport-msg" name="text" cols="40"
				          rows="10"></textarea>
			</div>
		</div>
		<br>
		<div class="control-group">
			<div class="controls">
				<input class="btn btn-primary" type="submit" name="Submit"
				       value="<?php echo Text::_('COM_KUNENA_REPORT_SEND'); ?>"/>
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
					<?php echo Text::_('COM_KUNENA_REPORT_CLOSEMODAL_LABEL'); ?></button>
			</div>
		</div>
	</div>
</form>

