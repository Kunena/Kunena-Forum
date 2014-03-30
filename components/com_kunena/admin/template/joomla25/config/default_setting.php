<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Users
 * @subpackage Categories
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// no direct access
defined('_JEXEC') or die;
?>
<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=config') ?>" method="post" name="settingFormModal" id="settingFormModal">
	<div class="modal hide fade" id="settingModal">
		<input type="hidden" name="task" value="setdefault" />
		<?php echo JHTML::_( 'form.token' ) ?>

		<div class="modal-header">
			<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
			<h3><?php echo JText::_('COM_KUNENA_CONFIG_MODAL_TITLE');?></h3>
		</div>
		<div class="modal-body">
			<p><?php echo JText::_('COM_KUNENA_CONFIG_MODAL_DESCRIPTION'); ?></p>
		</div>
		<div class="modal-footer">
			<button class="btn" type="button" data-dismiss="modal">
				<?php echo JText::_('JCANCEL'); ?>
			</button>
			<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('setDefaultValue');">
				<?php echo JText::_('JSUBMIT'); ?>
			</button>
		</div>
	</div>
</form>
