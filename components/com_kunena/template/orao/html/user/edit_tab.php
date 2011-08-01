<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php /*?>
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="kuserform" class="form-validate" enctype="multipart/form-data">
				<input type="hidden" name="view" value="user" />
				<input type="hidden" name="task" value="save" />
				<?php echo JHTML::_( 'form.token' ); ?>
				<div id="kprofile-edit">
					<dl class="tabs">
						<dt class="open"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER') ?></dt>
						<dd style="display: none;">
							<?php $this->displayEditUser() ?>
						</dd>
						<dt class="closed"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE') ?></dt>
						<dd style="display: none;">
							<?php $this->displayEditProfile() ?>
						</dd>
						<?php if ($this->editavatar) : ?>
						<dt class="closed"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR') ?></dt>
						<dd style="display: none;">
							<?php $this->displayEditAvatar() ?>
						</dd>
						<?php endif ?>
						<dt class="closed"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS') ?></dt>
						<dd style="display: none;">
							<?php $this->displayEditSettings() ?>
						</dd>
					</dl>
					<div class="kpost-buttons">
						<button title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SAVE') ?>" type="submit" class="kbutton"><?php echo JText::_('COM_KUNENA_GEN_SAVE') ?></button>
						<button onclick="javascript:window.history.back();" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL') ?>" type="button" class="kbutton"><?php echo (JText::_('COM_KUNENA_GEN_CANCEL')) ?></button>
					</div>
				</div>
			</form>
<?php */?>


<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="kuserform" class="form-validate" enctype="multipart/form-data">
				<input type="hidden" name="view" value="user" />
				<input type="hidden" name="task" value="save" />
				<?php echo JHTML::_( 'form.token' ); ?>

<ul id="profile-edit" class="shadetabs">
<li class="tk-profiletab-edit-user tk-tip" title=" ::<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER'); ?>">
	<a href="#" rel="tcontent-edit-user" class="selected"><?php echo JText::_('Account'); ?><br /><span></span></a>
</li>

<li class="tk-profiletab-edit-profile tk-tip" title=" ::<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE'); ?>">
	<a href="#" rel="tcontent-edit-profile"><?php echo JText::_('Information'); ?><br /><span></span></a>
</li>

<li class="tk-profiletab-edit-avatar tk-tip" title=" ::<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR'); ?>">
	<a href="#" rel="tcontent-edit-avatar"><?php echo JText::_('Avatar'); ?><br /><span></span></a>
</li>

<li class="tk-profiletab-edit-settings tk-tip" title=" ::<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS'); ?>">
	<a href="#" rel="tcontent-edit-settings"><?php echo JText::_('Settings'); ?><br /><span></span></a>
</li>

</ul>
<div style="border:0px solid gray; padding: 20px 0 10px;clear:both;">
<div id="tcontent-edit-user" class="tabcontent">
<?php $this->displayEditUser(); ?>
</div>

<div id="tcontent-edit-profile" class="tabcontent">
<?php $this->displayEditProfile(); ?>
</div>

<div id="tcontent-edit-avatar" class="tabcontent">
<?php $this->displayEditAvatar(); ?>
</div>

<div id="tcontent-edit-settings" class="tabcontent">
<?php $this->displayEditSettings(); ?>
</div>
</div>

<div class="forumlist tk-clear">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist forums">
				<li class="rowfull" style="text-align:center;padding:5px;">

							<input class="tk-submit-button validate" type="submit" value="<?php echo JText::_('COM_KUNENA_GEN_SAVE'); ?>"></input>
							<input onclick="javascript:window.history.back();" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL') ?>" class="tk-cancel-button" type="button" value="<?php echo JText::_('COM_KUNENA_GEN_CANCEL'); ?>"></input>

				</li>
			</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>
</form>


<script type="text/javascript">
//<![CDATA[
var profileedit=new ddtabcontent("profile-edit")
profileedit.setpersist(true)
profileedit.setselectedClassTarget("link")
profileedit.init()
// ]]>
</script>