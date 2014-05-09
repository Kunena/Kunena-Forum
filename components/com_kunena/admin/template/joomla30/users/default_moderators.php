<?php
/**
* Kunena Component
* @package Kunena.Administrator.Users
* @subpackage Users
*
* @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/

// no direct access
defined('_JEXEC') or die;
?>
<div class="modal hide fade" id="moderateModal">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3><?php echo JText::_('COM_KUNENA_BATCH_USERS_OPTIONS');?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo JText::_('COM_KUNENA_BATCH_USERS_TIP'); ?></p>
		<div class="control-group">
			<div class="controls">
				<?php echo $this->modcatlist; ?>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('catid').value='';" data-dismiss="modal">
		<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('batch_moderators');">
		<?php echo JText::_('JSUBMIT'); ?>
		</button>
	</div>
</div>
