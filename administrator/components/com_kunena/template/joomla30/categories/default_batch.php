<?php
/**
* Kunena Component
* @package Kunena.Administrator.Template
* @subpackage Categories
*
* @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/

// no direct access
defined('_JEXEC') or die;
?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_KUNENA_BATCH_OPTIONS');?></legend>
	<p><?php echo JText::_('COM_KUNENA_BATCH_TIP'); ?></p>
	<label id="batch-choose-action-lbl" for="batch-category-id">
		<?php echo JText::_('COM_KUNENA_BATCH_CATEGORY_LABEL'); ?>
	</label>
	<fieldset id="batch-choose-action" class="combo">
		<?php echo $this->batch_categories; ?><input type="radio" name="move_copy" value="copy" /><label><?php echo JText::_('COM_KUNENA_BATCH_CATEGORY_COPY') ?></label><input type="radio" name="move_copy" value="move" /><label><?php echo JText::_('COM_KUNENA_BATCH_CATEGORY_MOVE') ?></label>
	</fieldset>

	<button type="submit" onclick="Joomla.submitbutton('batch_categories');">
		<?php echo JText::_('COM_KUNENA_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-language-id').value=''">
		<?php echo JText::_('COM_KUNENA_FILTER_CLEAR'); ?>
	</button>
</fieldset>
