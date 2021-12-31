<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright   (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<div class="kloginform center">
	<h1><?php echo Text::_('COM_KUNENA_LOGIN_FORUM'); ?></h1>
	<form action="<?php echo Route::_('index.php?option=com_kunena'); ?>" method="post" class="form-inline">
		<input type="hidden" name="view" value="user"/>
		<input type="hidden" name="task" value="login"/>
		<?php echo HTMLHelper::_('form.token'); ?>

		<div id="kform-login-username" class="control-group center">
			<div class="controls">
				<div class="input-prepend input-append">
					<span class="add-on">
						<?php echo KunenaIcons::user(); ?>
						<label for="klogin-username" class="element-invisible">
							<?php echo Text::_('JGLOBAL_USERNAME'); ?>
						</label>
					</span>
					<input id="klogin-username-full" type="text" name="username" class="input-small" tabindex="1"
					       size="18" autocomplete="username" placeholder="<?php echo Text::_('JGLOBAL_USERNAME'); ?>"/>
				</div>
			</div>
		</div>

		<div id="kform-login-password" class="control-group center">
			<div class="controls">
				<div class="input-prepend input-append">
					<span class="add-on">
						<?php echo KunenaIcons::lock(); ?>
						<label for="klogin-passwd" class="element-invisible">
							<?php echo Text::_('JGLOBAL_PASSWORD'); ?>
						</label>
					</span>
					<input id="klogin-passwd-full" type="password" name="password" class="input-small" tabindex="2"
					       size="18" autocomplete="current-password" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>"/>
				</div>
			</div>
		</div>

		<?php $login = KunenaLogin::getInstance(); ?>
		<?php
		if ($login->getTwoFactorMethods() > 1)
			:
			?>
			<div id="kform-login-tfa" class="control-group center">
				<div class="controls">
					<div class="input-prepend input-append">
					<span class="add-on">
						<?php echo KunenaIcons::star(); ?>
						<label for="kk-lgn-secretkey" class="element-invisible">
							<?php echo Text::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>
						</label>
				  </span>
						<input id="kk-lgn-secretkey" type="text" name="secretkey" class="input-small" tabindex="3"
						       size="18" placeholder="<?php echo Text::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>"/>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div id="kform-login-submit" class="control-group center">
			<p>
				<button type="submit" tabindex="3" name="submit" class="btn btn-primary btn">
					<?php echo Text::_('JLOGIN'); ?>
				</button>
			</p>

			<p>
				<?php if ($login->getResetUrl())
					:
					?>
					<a href="<?php echo $login->getResetUrl(); ?>" rel="nofollow">
						<?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD'); ?>
					</a>
					<br/>
				<?php endif ?>

				<?php if ($login->getRemindUrl())
					:
					?>
					<a href="<?php echo $login->getRemindUrl(); ?>" rel="nofollow">
						<?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME'); ?>
					</a>
					<br/>
				<?php endif ?>

				<?php if ($login->getRegistrationUrl())
					:
					?>
					<a href="<?php echo $login->getRegistrationUrl(); ?>" rel="nofollow">
						<?php echo Text::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT'); ?>
					</a>
				<?php endif ?>

			</p>
		</div>
	</form>
</div>
