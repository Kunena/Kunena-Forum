<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<!--
<div class="well well-small">
	<div class="row-fluid column-row">
		<div class="span12 column-item">

				<div class="k_guest">
					<?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME'); ?>,
					<b><?php echo JText::_('COM_KUNENA_PROFILEBOX_GUEST'); ?></b>
				</div>
				<?php if ($this->login->enabled()) : ?>
				<form class="form-inline" action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="login" style="margin:0;">
					<input type="hidden" name="view" value="user" />
					<input type="hidden" name="task" value="login" />
					[K=TOKEN]

					<div id="form-login-username" class="control-group">
			<div class="controls">
				<div class="input-prepend input-append">
					<span class="add-on"><i class="icon-user tip" title="<?php echo JText::_('COM_KUNENA_LOGIN_USERNAME') ?>"></i><label for="modlgn-username" class="element-invisible"><?php echo JText::_('COM_KUNENA_LOGIN_USERNAME'); ?></label></span><input id="modlgn-username" type="text" name="username" class="input-small" tabindex="1" size="25" placeholder="<?php echo JText::_('COM_KUNENA_LOGIN_USERNAME') ?>" /><a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>" class="btn hasTooltip" title="<?php echo JText::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME'); ?>"><i class="icon-question-sign"></i></a></span>
					<span class="add-on"><i class="icon-lock tip" title="<?php echo JText::_('JGLOBAL_PASSWORD') ?>"></i><label for="modlgn-passwd" class="element-invisible"><?php echo JText::_('COM_KUNENA_LOGIN_PASSWORD'); ?></label></span><input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="2" size="25" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" /><a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>" class="btn hasTooltip" title="<?php echo JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD'); ?>"><i class="icon-question-sign"></i></a></span>
				<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/><label for="modlgn-remember" class="control-label">  <?php echo JText::_('COM_KUNENA_LOGIN_REMEMBER_ME') ?></label>
				<button type="submit" tabindex="3" name="Submit" class="btn btn-primary"><?php echo JText::_('JLOGIN') ?></button>

		</div>
		</div>
		</div>
		<?php
			$usersConfig = JComponentHelper::getParams('com_users');
			if ($usersConfig->get('allowUserRegistration')) : ?>
			<ul class="unstyled">
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
					<?php echo JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT'); ?> <i class="icon-arrow-right"></i></a>
				</li>

			</ul>
		<?php endif; ?>
				</form>
				<?php endif; ?>
			</td>
			<?php if ($this->moduleHtml) : ?>
			<td class = "kprofilebox-right">
				<div class="kprofilebox-modul">
					<?php echo $this->moduleHtml; ?>
				</div>
			</td>
			<?php endif; ?>
		</div>
	</div>
</div>
-->

