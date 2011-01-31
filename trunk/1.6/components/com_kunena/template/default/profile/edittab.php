<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined( '_JEXEC' ) or die();
?>
<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" name="kuserform" class="form-validate" enctype="multipart/form-data">
<div id="kprofile-edit">
	<dl class="tabs">
		<dt class="open"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayEditUser(); ?>
		</dd>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayEditProfile(); ?>
		</dd>
		<?php if ($this->editavatar) : ?>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayEditAvatar(); ?>
		</dd>
		<?php endif; ?>
		<dt class="closed"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS'); ?></dt>
		<dd style="display: none;">
			<?php $this->displayEditSettings(); ?>
		</dd>
	</dl>
	<input type="hidden" name="option" value="com_kunena" />
	<input type="hidden" name="func" value="profile" />
	<input type="hidden" name="do" value="save" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<div class="kbutton-container">
		<button class="kbutton ks validate" type="submit"><?php echo JText::_('COM_KUNENA_GEN_SAVE'); ?></button>
		<input type="button" name="cancel" class="kbutton" value="<?php echo (' ' . JText::_('COM_KUNENA_GEN_CANCEL') . ' ');?>"
			onclick="javascript:window.history.back();"
			title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" />
	</div>
</div>
</form>