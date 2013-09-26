<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$userConfig = JComponentHelper::getParams('com_users');
?>
<ul class="nav pull-right">
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<i class="icon-large icon-user"></i> <b class="caret"></b>
		</a>
		<div class="dropdown-menu well well-small">
			<form action="<?php echo JRoute::_('index.php?option=com_kunena'); ?>" method="post" class="form-inline">
				<div id="form-login-username" class="control-group">
					<div class="controls">
						<div class="input-prepend input-append">
							<span class="add-on">
								<i class="icon-user tip" title="<?php echo JText::_('JGLOBAL_USERNAME') ?>"></i>
								<label for="modlgn-username" class="element-invisible">
									<?php echo JText::_('JGLOBAL_USERNAME'); ?>
								</label>
							</span>
							<input id="modlgn-username" type="text" name="username" class="input-small" tabindex="1" size="18" placeholder="<?php echo JText::_('JGLOBAL_USERNAME') ?>" />
						</div>
					</div>
				</div>
				<div id="form-login-password" class="control-group">
					<div class="controls">
						<div class="input-prepend input-append">
							<span class="add-on">
								<i class="icon-lock tip" title="<?php echo JText::_('JGLOBAL_PASSWORD') ?>"></i>
								<label for="modlgn-passwd" class="element-invisible">
									<?php echo JText::_('JGLOBAL_PASSWORD'); ?>
								</label>
							</span>
							<input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="2" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
						</div>
					</div>
				</div>
				<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
					<div id="form-login-remember" class="control-group checkbox">
						<label for="modlgn-remember" class="control-label">
							<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>
						</label>
						<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
					</div>
				<?php endif; ?>
				<div id="form-login-submit" class="control-group center">
					<p>
						<button type="submit" tabindex="3" name="Submit" class="btn btn-primary btn"><?php echo JText::_('JLOGIN') ?></button>
					</p>

					<?php if ($userConfig->get('allowUserRegistration')) : ?>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
							<?php echo JText::_('REGISTER'); ?> <i class="icon-arrow-right"></i>
						</a>
					</p>
					<?php endif; ?>
				</div>

				<input type="hidden" name="view" value="user" />
				<input type="hidden" name="task" value="login" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>
	</li>
</ul>
