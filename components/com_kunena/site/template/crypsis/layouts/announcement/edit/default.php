<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.formvalidation');
?>
<h2>
	<?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?>: <?php echo $this->announcement->exists() ? JText::_('COM_KUNENA_ANN_EDIT') : JText::_('COM_KUNENA_ANN_ADD'); ?>
</h2>

<div class="well well-small">
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement'); ?>" method="post" name="editform" class="form-validate form-horizontal" id="editform" onsubmit="return kunenaValidate(this);">
		<input type="hidden" name="task" value="save" />
		<?php echo $this->displayInput('id'); ?>
		<?php echo JHtml::_('form.token'); ?>

		<div class="control-group">
			<label class="control-label" for="ann-title">
				<?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?>
			</label>
			<div class="controls" id="ann-title">
				<?php echo $this->displayInput('title', 'class="input-xxlarge required"'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="ann-short">
				<?php echo JText::_('COM_KUNENA_ANN_SORTTEXT'); ?>
			</label>
			<div class="controls" id="ann-short">
				<?php echo $this->displayInput('sdescription', 'class="input-xxlarge required"'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="ann-long">
				<?php echo JText::_('COM_KUNENA_ANN_LONGTEXT'); ?>
			</label>
			<div class="controls" id="ann-long">
				<?php echo $this->displayInput('description', 'rows="5" class="input-xxlarge"'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="ann-date">
				<?php echo JText::_('COM_KUNENA_ANN_DATE'); ?>
			</label>
			<div class="controls" id="ann-date">
				<?php echo $this->displayInput('created', 'addcreated'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="ann-showdate">
				<?php echo JText::_('COM_KUNENA_ANN_SHOWDATE'); ?>
			</label>
			<div class="controls" id="ann-showdate">
				<?php echo $this->displayInput('showdate'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="ann-publish">
				<?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?>
			</label>
			<div class="controls" id="ann-publish">
				<?php echo $this->displayInput('published'); ?>
			</div>
		</div>

		<div class="control-group">
			<div class="controls" id="ann-publish">
				<input name="submit" class="btn btn-primary" type="submit" value="<?php echo JText::_('COM_KUNENA_SAVE'); ?>"/>
				<input onclick="javascript:window.history.back();" name="cancel" class="btn" type="button" value="<?php echo JText::_('COM_KUNENA_CANCEL'); ?>"/>
			</div>
		</div>
	</form>
</div>
