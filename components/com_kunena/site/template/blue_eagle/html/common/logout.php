<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kblock kpbox">
	<div class="kcontainer" id="kprofilebox">
		<div class="kbody">
<table class="kprofilebox">
	<tbody>
		<tr class="krow1">
			<?php if ($this->me->getAvatarImage('welcome')) : ?>
			<td class="kprofilebox-left">
				<?php echo $this->me->getAvatarImage('kavatar', 'welcome'); ?>
			</td>
			<?php endif; ?>
			<td class="kprofileboxcnt">
				<ul class="kprofilebox-link">
					<?php if (!empty($this->privateMessagesLink)) : ?><li><?php echo $this->privateMessagesLink ?></li><?php endif ?>
					<?php if (!empty($this->editProfileLink)) : ?><li><?php echo $this->editProfileLink ?></li><?php endif ?>
					<?php if (!empty($this->announcementsLink)) : ?><li><?php echo $this->announcementsLink ?></li><?php endif ?>
				</ul>
				<ul class="kprofilebox-welcome">
					<li><?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME'); ?>, <strong><?php echo $this->me->getLink() ?></strong></li>
					<li class="kms"><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LASTLOGIN'); ?>:</strong> <span title="<?php echo KunenaDate::getInstance($this->me->lastvisitDate)->toKunena('ago'); ?>"><?php echo KunenaDate::getInstance($this->me->lastvisitDate)->toKunena('config_post_dateformat'); ?></span></li>
					<?php if ($this->logout->enabled()) : ?>
					<li>
					<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="login">
						<input type="hidden" name="view" value="user" />
						<input type="hidden" name="task" value="logout" />
						[K=TOKEN]

						<input type="submit" name="submit" class="kbutton" value="<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGOUT'); ?>" />
					</form>
					</li>
					<?php endif; ?>
				</ul>
			</td>
			<!-- Module position -->
			<?php if ($this->moduleHtml) : ?>
			<td class = "kprofilebox-right">
				<div class="kprofilebox-modul">
					<?php echo $this->moduleHtml; ?>
				</div>
			</td>
			<?php endif; ?>
		</tr>
	</tbody>
</table>
		</div>
	</div>
</div>
