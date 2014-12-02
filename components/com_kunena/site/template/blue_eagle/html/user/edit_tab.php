<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage User
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="kuserform" class="form-validate" enctype="multipart/form-data">
	<input type="hidden" name="view" value="user" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="userid" value="<?php echo $this->user->id ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>

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
	<div class="kbutton-container">
		<button class="kbutton ks validate" type="submit"><?php echo JText::_('COM_KUNENA_SAVE'); ?></button>
		<input type="button" name="cancel" class="kbutton" value="<?php echo (' ' . JText::_('COM_KUNENA_CANCEL') . ' ');?>"
			onclick="window.history.back();"
			title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" />
	</div>
</div>
</form>
