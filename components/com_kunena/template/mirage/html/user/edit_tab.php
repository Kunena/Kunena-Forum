<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage User
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
$this->document->addScriptDeclaration ( "// <![CDATA[
	window.addEvent('domready', function(){ $$('dl.ktabs').each(function(tabs){ new KunenaTabs(tabs); }); });
// ]]>" );
?>
<div class="kmodule user-edit_tab">
	<div class="kbox-wrapper kbox-full">
		<div id="kprofile-edit" class="user-edit_tab-kbox kbox tabbable">
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="kuserform" class="form-validate" enctype="multipart/form-data">
				<div class="detailsbox-wrapper kbox-full">
					<div class="detailsbox kbox-full">
						<input type="hidden" name="view" value="user" />
						<input type="hidden" name="task" value="save" />
						<input type="hidden" name="userid" value="<?php echo $this->user->id ?>" />
						<?php echo JHtml::_( 'form.token' ); ?>

						<dl class="ktabs">
							<dt class="open"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER') ?>" href="#tab-edit_user"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_USER') ?></a></dt>
							<dd style="display: none;">
								<?php $this->displayEditUser() ?>
							</dd>
							<dt class="closed"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE') ?>" href="#tab-profile"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_PROFILE') ?></a></dt>
							<dd style="display: none;">
								<?php $this->displayEditProfile() ?>
							</dd>
							<?php if ($this->editavatar) : ?>
							<dt class="closed"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR') ?>" href="#tab-avatar"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR') ?></a></dt>
							<dd style="display: none;">
								<?php $this->displayEditAvatar() ?>
							</dd>
							<?php endif ?>
							<dt class="closed"><a class="link-tab" title="<?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS') ?>" href="#tab-settings"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS') ?></a></dt>
							<dd style="display: none;">
								<?php $this->displayEditSettings() ?>
							</dd>
						</dl>
					</div>
				</div>
				<div class="footerbox-wrapper kbox-full">
					<div class="footerbox innerspacer kbox-full kbox-color kbox-border kbox-border_radius kbox-shadow">
						<div class="buttonbar">
							<div class="form-label">
							</div>
							<div class="form-field">
								<ul class="button-list button-user-form">
									<li class="item-button">
										<button title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SAVE') ?>" type="submit" class="kbutton button-type-standard"><span><?php echo JText::_('COM_KUNENA_SAVE') ?></span></button>
									</li>
									<li class="item-button">
										<button onclick="javascript:window.history.back();" title="<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL') ?>" type="button" class="kbutton button-type-standard"><span><?php echo (JText::_('COM_KUNENA_CANCEL')) ?></span></button>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>