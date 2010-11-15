<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kblock kpbox">
	<div class="kcontainer" id="kprofilebox">
		<div class="kbody">
<table class="kprofilebox">
	<tbody>
		<tr class="krow1">
			<?php if ($this->me->getAvatarLink('welcome')) : ?>
			<td class="kprofilebox-left">
				<?php echo $this->me->getAvatarLink('kavatar', 'welcome'); ?>
			</td>
			<?php endif; ?>
			<td class="kprofileboxcnt">
				<ul class="kprofilebox-link">
					<?php if (!empty($this->privateMessages)) : ?>
						<li><?php echo $this->privateMessages; ?></li>
					<?php endif ?>
					<?php if (!empty($this->announcements)) : ?>
						<li><?php echo $this->announcements; ?></li>
					<?php endif; ?>
				</ul>
				<ul class="kprofilebox-welcome">
					<li><?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME'); ?>, <strong><?php echo CKunenaLink::GetProfileLink ( intval($this->me->userid) ); ;?></strong></li>
					<li class="kms"><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->me->lastvisitDate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->me->lastvisitDate, 'date_today', 'utc'); ?></span></li>
					<?php if ($this->logout) : ?>
					<li>
					<form action="<?php echo KUNENA_LIVEURLREL ?>" method="post" name="login">
						<input type="submit" name="submit" class="kbutton" value="<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGOUT'); ?>" />
						<input type="hidden" name="option" value="<?php echo $this->logout['option']; ?>" />
						<?php if (!empty($this->logout['view'])) : ?>
						<input type="hidden" name="view" value="<?php echo $this->logout['view']; ?>" />
						<?php endif; ?>
						<input type="hidden" name="task" value="<?php echo $this->logout['task']; ?>" />
						<input type="hidden" name="<?php echo $this->logout['field_return']; ?>" value="<?php echo $this->return; ?>" />
						<?php echo JHTML::_ ( 'form.token' ); ?>
					</form>
					</li>
					<?php endif; ?>
				</ul>
			</td>
			<?php if ($this->moduleHtml) : ?>
			<td class = "kprofilebox-right">
				<div class="kprofilebox-modul">
					<?php $this->moduleHtml; ?>
				</div>
			</td>
			<?php endif; ?>
		</tr>
	</tbody>
</table>
		</div>
	</div>
</div>