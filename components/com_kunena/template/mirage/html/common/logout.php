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
				<li class="login-avatar"><span><?php echo $this->me->getLink($this->me->getAvatarImage('', 'welcome'), JText::_('COM_KUNENA_VIEW_COMMON_LOGOUT_OWN_LINK_TITLE')) ?></span></li>
				<li class="login-welcome"><?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_LOGOUT_WELCOME', $this->me->getLink(null, JText::_('COM_KUNENA_VIEW_COMMON_LOGOUT_OWN_LINK_TITLE'))) ?></li>
				<li class="login-lastvisit"><?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_LOGOUT_LASTVISIT', $this->lastvisitDate->toSpan('date_today', 'ago')) ?></li>
			</ul>
			<ul class="login-divider"><li class="divider"></li></ul>
			<ul class="login-userlinks">
				<?php if (!empty($this->privateMessagesLink)) : ?><li class="kpm"><?php echo $this->privateMessagesLink ?></li><?php endif ?>
				<?php if (!empty($this->editProfileLink)) : ?><li class="keditprofile"><?php echo $this->editProfileLink ?></li><?php endif ?>
				<?php if (!empty($this->announcementsLink)) : ?><li class="kannouncements"><?php echo $this->announcementsLink ?></li><?php endif ?>
			</ul>
			<ul class="login-divider"><li class="divider"></li></ul>
			<ul class="login-buttons">
				<?php if ($this->logout->enabled()) : ?>
				<li class="logout-form">
					<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" class="kform klogout">
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
