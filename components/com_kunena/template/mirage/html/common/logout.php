<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
// TODO: add missing module position
?>
<div class="login dropdown-menu">
	<ul class="login-profile">
		<li class="login-avatar link-dropdown">
			<a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user') ?>">
				<span class="login-avatar"><?php echo $this->me->getAvatarImage('', 'welcome'), JText::_('COM_KUNENA_VIEW_COMMON_LOGOUT_OWN_LINK_TITLE') ?></span>
				<span class="login-welcome"><?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_LOGOUT_WELCOME', $this->me->getName()) ?></span>
			</a>
		</li>
		<li class="divider"></li>
		<?php if (!empty($this->privateMessagesLink)) : ?><li class="pm link-dropdown"><?php echo $this->privateMessagesLink ?></li><?php endif ?>
		<?php if (!empty($this->editProfileLink)) : ?><li class="editprofile link-dropdown"><?php echo $this->editProfileLink ?></li><?php endif ?>
		<?php if (!empty($this->announcementsLink)) : ?><li class="announcements link-dropdown"><?php echo $this->announcementsLink ?></li><?php endif ?>
		<li class="divider"></li>
		<?php if ($this->logout) : ?>
			<li class="login-form">
				<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" class="form logout">
					<input type="hidden" name="view" value="user" />
					<input type="hidden" name="task" value="logout" />
					[K=TOKEN]
	
					<fieldset>
						<legend class="klegend klogout"><?php echo JText::_('COM_KUNENA_VIEW_COMMON_LOGOUT_LONG') ?></legend>
						<button type="submit" value="Log out" class="kbutton"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGOUT') ?></button>
					</fieldset>
				</form>
			</li>
		<?php endif ?>
	</ul>
</div>
